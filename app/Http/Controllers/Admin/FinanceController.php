<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AdminUserReportExport;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\SchoolClass;
use App\Models\StudentPayment;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        if (!Schema::hasTable('student_payments')) {
            return view('admin.finance', $this->emptyPayload($request));
        }

        $filters = $this->filters($request);
        $payments = $this->paginatedPaymentRows($filters, 8);

        return view('admin.finance', [
            ...$this->sharedPayload($filters),
            'payments' => $payments,
            'stats' => $this->stats(),
            'latestPayments' => StudentPayment::query()
                ->with(['student.schoolClass', 'student.majorSubject', 'student.majorSubjects'])
                ->latest('payment_date')
                ->take(5)
                ->get(),
            'tableReady' => true,
        ]);
    }

    public function store(Request $request)
    {
        if (!Schema::hasTable('student_payments')) {
            return redirect()
                ->route('admin.finance.index')
                ->with('error', 'Finance table is missing. Please run migrations first.');
        }

        $payment = StudentPayment::create($this->validatedPayment($request));
        $this->sendPaymentNotification($payment, 'recorded');

        return redirect()
            ->route('admin.finance.index')
            ->with('success', 'Student payment recorded successfully.');
    }

    public function update(Request $request, StudentPayment $payment)
    {
        $payment->update($this->validatedPayment($request, $payment));
        $payment->refresh();
        $this->sendPaymentNotification($payment, 'updated');

        return redirect()
            ->route('admin.finance.index', $request->query())
            ->with('success', 'Student payment updated successfully.');
    }

    public function destroy(StudentPayment $payment)
    {
        $this->deleteProofFile($payment);
        $payment->delete();

        return redirect()
            ->route('admin.finance.index')
            ->with('success', 'Student payment deleted successfully.');
    }

    public function exportPdf(Request $request)
    {
        $report = $this->reportPayload($request);

        return Pdf::loadView('admin.reports.users-pdf', [
            ...$report,
            'generatedAt' => now()->format('F j, Y g:i A'),
        ])
            ->setPaper('a4', 'landscape')
            ->download('student-payments-report-' . now()->format('Ymd-His') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $report = $this->reportPayload($request);

        return Excel::download(
            new AdminUserReportExport(
                $report['title'],
                $report['subtitle'],
                $report['sheet_name'],
                $report['accent'],
                $report['headings'],
                $report['rows'],
                $report['cards'],
                $report['filters'],
            ),
            'student-payments-report-' . now()->format('Ymd-His') . '.xlsx'
        );
    }

    private function validatedPayment(Request $request, ?StudentPayment $currentPayment = null): array
    {
        $validated = $request->validate([
            'student_id' => ['required', 'integer', 'exists:users,id'],
            'amount' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'discount_amount' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'payment_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date'],
            'status' => ['nullable', Rule::in(StudentPayment::STATUSES)],
            'payment_method' => ['nullable', Rule::in(StudentPayment::PAYMENT_METHODS)],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $student = User::query()
            ->with(['majorSubject', 'majorSubjects'])
            ->whereKey($validated['student_id'])
            ->where('role', 'student')
            ->first();

        if (!$student) {
            throw ValidationException::withMessages([
                'student_id' => 'Selected user must be a student.',
            ]);
        }

        $alreadyPaid = $this->studentPaidTotal((int) $student->id, $currentPayment);
        $tuitionTotal = (float) $student->tuition_total;
        $remainingBeforePayment = $tuitionTotal > 0
            ? max($tuitionTotal - $alreadyPaid, 0)
            : null;
        $validated['amount'] = $request->filled('amount')
            ? (float) $validated['amount']
            : ($remainingBeforePayment !== null && $remainingBeforePayment > 0 ? $remainingBeforePayment : $tuitionTotal);
        $validated['discount_amount'] = $validated['discount_amount'] ?? 0;
        $tuitionCoveredAmount = (float) $validated['amount'];

        if ((float) $validated['discount_amount'] > (float) $validated['amount']) {
            throw ValidationException::withMessages([
                'discount_amount' => 'The discount cannot be greater than the amount.',
            ]);
        }

        if ($remainingBeforePayment !== null && $tuitionCoveredAmount > $remainingBeforePayment + 0.009) {
            throw ValidationException::withMessages([
                'amount' => 'The payment amount cannot be greater than the remaining tuition due.',
            ]);
        }

        // Proof files are no longer required or handled by the controller.

        if ($remainingBeforePayment !== null && $remainingBeforePayment <= 0.009 && !$currentPayment) {
            throw ValidationException::withMessages([
                'student_id' => 'This student has already paid the full tuition amount.',
            ]);
        }

        $validated['payment_method'] = $validated['payment_method'] ?? null;
        $validated['reference'] = null;
        $validated['note'] = $validated['note'] ?? null;
        $validated['status'] = $this->paymentStatusAfterAmount($tuitionTotal, $alreadyPaid, $tuitionCoveredAmount);

        // No file handling for proof files anymore.

        return $validated;
    }

    private function reportPayload(Request $request): array
    {
        if (!Schema::hasTable('student_payments')) {
            return [
                'title' => 'Student Payments Report',
                'subtitle' => 'Student payment records.',
                'sheet_name' => 'Payments',
                'accent' => '0F766E',
                'headings' => ['Payment', 'Student', 'Class', 'Subjects', 'Status', 'Amount', 'Discount', 'Net', 'Method', 'Date'],
                'rows' => [['No finance records found. Please run migrations first.', '', '', '', '', '', '', '', '', '']],
                'cards' => [],
                'filters' => [],
            ];
        }

        $filters = $this->filters($request);
        $payments = $this->paymentRows($filters);

        $headings = ['Student', 'Class', 'Subjects', 'Status', 'Tuition', 'Paid', 'Discount', 'Missing', 'Method', 'Latest Payment'];
        $rows = $payments->map(function (array $payment): array {
            $latestPayment = $payment['latest_payment'];

            return [
                (string) ($payment['student']?->name ?? 'Unknown Student'),
                (string) ($payment['student']?->schoolClass?->display_name ?? 'Unassigned'),
                (string) ($payment['student']?->tuition_subject_names ?: 'No subjects assigned'),
                $payment['status_label'],
                '$' . number_format((float) $payment['tuition_total'], 2),
                '$' . number_format((float) $payment['paid_total'], 2),
                '$' . number_format((float) $payment['discount_total'], 2),
                '$' . number_format((float) $payment['remaining_due'], 2),
                $latestPayment?->method_label ?? 'Not specified',
                optional($latestPayment?->payment_date)->format('M d, Y') ?? '-',
            ];
        })->values()->all();

        if ($rows === []) {
            $rows = [['No finance records found for the selected filters.', '', '', '', '', '', '', '', '', '']];
        }

        $collected = (float) $payments->sum(fn(array $payment): float => (float) $payment['cash_collected']);
        $discounts = (float) $payments->sum(fn(array $payment): float => (float) $payment['discount_total']);
        $tuitionTotal = (float) $payments->sum(fn(array $payment): float => (float) $payment['tuition_total']);
        $outstanding = (float) $payments->sum(fn(array $payment): float => (float) $payment['remaining_due']);

        return [
            'title' => 'Student Payments Report',
            'subtitle' => 'Detailed finance report for student payments, balances, methods, and payment dates.',
            'sheet_name' => 'Payments',
            'accent' => '0F766E',
            'headings' => $headings,
            'rows' => $rows,
            'cards' => [
                ['label' => 'Students', 'value' => (string) $payments->count()],
                ['label' => 'Collected', 'value' => '$' . number_format($collected, 2)],
                ['label' => 'Outstanding', 'value' => '$' . number_format($outstanding, 2)],
                ['label' => 'Discounts', 'value' => '$' . number_format($discounts, 2)],
            ],
            'filters' => [
                ['label' => 'Search', 'value' => $filters['search'] !== '' ? $filters['search'] : 'Any payment'],
                ['label' => 'Student', 'value' => $this->studentFilterLabel($filters['studentId'])],
                ['label' => 'Status', 'value' => $filters['status'] !== 'all' ? ucfirst($filters['status']) : 'All'],
                ['label' => 'Date Range', 'value' => trim(($filters['from'] ?: 'Any') . ' to ' . ($filters['to'] ?: 'Any'))],
            ],
        ];
    }

    private function emptyPayload(Request $request): array
    {
        $filters = $this->filters($request);

        return [
            ...$this->sharedPayload($filters),
            'payments' => collect(),
            'stats' => [
                'payments' => 0,
                'collected' => 0,
                'outstanding' => 0,
                'discounts' => 0,
                'billable' => 0,
                'students_paid' => 0,
            ],
            'latestPayments' => collect(),
            'tableReady' => false,
        ];
    }

    private function sharedPayload(array $filters): array
    {
        return [
            'students' => $this->studentsWithTuition(),
            'studentPaidTotals' => $this->studentPaidTotals(),
            'paidStudentIds' => Schema::hasTable('student_payments')
                ? StudentPayment::query()->where('status', 'paid')->pluck('student_id')->map(fn($id) => (int) $id)->all()
                : [],
            'classes' => Schema::hasTable('school_classes')
                ? SchoolClass::query()->orderBy('name')->get()
                : collect(),
            'statuses' => StudentPayment::STATUSES,
            'paymentMethods' => StudentPayment::PAYMENT_METHODS,
            'search' => $filters['search'],
            'status' => $filters['status'],
            'studentId' => $filters['studentId'] !== null ? (string) $filters['studentId'] : 'all',
            'classId' => $filters['classId'] !== null ? (string) $filters['classId'] : 'all',
            'from' => $filters['from'],
            'to' => $filters['to'],
        ];
    }

    private function stats(): array
    {
        $payments = StudentPayment::query()
            ->get(['student_id', 'amount', 'discount_amount', 'status']);
        $paymentRows = $this->paymentRows();
        $totalTuition = (float) $paymentRows->sum(fn(array $payment): float => (float) $payment['tuition_total']);
        $collected = (float) $paymentRows->sum(fn(array $payment): float => (float) $payment['cash_collected']);
        $discounts = (float) $paymentRows->sum(fn(array $payment): float => (float) $payment['discount_total']);

        return [
            'payments' => $payments->count(),
            'collected' => $collected,
            'outstanding' => (float) $paymentRows->sum(fn(array $payment): float => (float) $payment['remaining_due']),
            'discounts' => $discounts,
            'billable' => $totalTuition,
            'students_paid' => $paymentRows->where('status', 'paid')->count(),
        ];
    }

    private function studentPaidTotal(int $studentId, ?StudentPayment $currentPayment = null): float
    {
        $query = StudentPayment::query()
            ->where('student_id', $studentId)
            ->whereIn('status', ['paid', 'partial']);

        if ($currentPayment) {
            $query->where('id', '!=', (int) $currentPayment->id);
        }

        return (float) $query
            ->get(['amount'])
            ->sum(fn(StudentPayment $payment): float => (float) $payment->amount);
    }

    private function paymentStatusAfterAmount(float $tuitionTotal, float $alreadyPaid, float $paymentAmount): string
    {
        if ($tuitionTotal <= 0.009) {
            return 'waived';
        }

        $paidAfterPayment = max($alreadyPaid + $paymentAmount, 0);
        $remainingAfterPayment = max($tuitionTotal - $paidAfterPayment, 0);

        if ($remainingAfterPayment <= 0.009) {
            return 'paid';
        }

        return $paidAfterPayment > 0.009 ? 'partial' : 'pending';
    }

    private function studentPaidTotals(): array
    {
        if (!Schema::hasTable('student_payments')) {
            return [];
        }

        return StudentPayment::query()
            ->whereIn('status', ['paid', 'partial'])
            ->get(['student_id', 'amount'])
            ->groupBy(fn(StudentPayment $payment): string => (string) $payment->student_id)
            ->map(fn($payments): float => (float) $payments->sum(fn(StudentPayment $payment): float => (float) $payment->amount))
            ->all();
    }

    private function deleteProofFile(StudentPayment $payment): void
    {
        $path = trim((string) $payment->proof_file_path);
        if ($path !== '' && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function sendPaymentNotification(StudentPayment $payment, string $action): void
    {
        $studentId = (int) $payment->student_id;
        if ($studentId <= 0) {
            return;
        }

        $title = $action === 'updated' ? 'Payment updated' : 'Payment recorded';
        $student = $payment->student()
            ->with(['majorSubject', 'majorSubjects'])
            ->first();
        $message = '[student_id:' . $studentId . '] '
            . 'Your school payment was ' . $action
            . ' for $' . number_format($payment->net_amount, 2);

        $subjects = (string) ($student?->tuition_subject_names ?? '');
        if ($subjects !== '') {
            $message .= ' for ' . $subjects;
        }

        if ((float) $payment->discount_amount > 0) {
            $message .= ' after a $' . number_format((float) $payment->discount_amount, 2) . ' discount';
        }

        $message .= '. Status: ' . $payment->status_label . '.';

        if ($student) {
            $remaining = max((float) $student->tuition_total - $this->studentPaidTotal($studentId), 0);
            if ($remaining > 0.009) {
                $message .= ' Additional amount due: $' . number_format($remaining, 2) . '.';
            }
        }

        Notification::query()->create([
            'type' => 'student_payment_saved',
            'title' => $title,
            'message' => $message,
            'url' => route('student.notices.index'),
            'is_read' => false,
        ]);
    }

    private function filters(Request $request): array
    {
        $status = (string) $request->query('status', 'all');
        $studentIdRaw = (string) $request->query('student_id', 'all');
        $classIdRaw = (string) $request->query('class_id', 'all');

        return [
            'search' => trim((string) $request->query('q', '')),
            'status' => in_array($status, StudentPayment::STATUSES, true) ? $status : 'all',
            'studentId' => ctype_digit($studentIdRaw) ? (int) $studentIdRaw : null,
            'classId' => ctype_digit($classIdRaw) ? (int) $classIdRaw : null,
            'from' => trim((string) $request->query('from', '')),
            'to' => trim((string) $request->query('to', '')),
        ];
    }

    private function paginatedPaymentRows(array $filters, int $perPage): LengthAwarePaginator
    {
        $rows = $this->paymentRows($filters)->values();
        $page = LengthAwarePaginator::resolveCurrentPage();

        return new LengthAwarePaginator(
            $rows->forPage($page, $perPage)->values(),
            $rows->count(),
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ],
        );
    }

    private function paymentRows(array $filters = [])
    {
        $students = $this->studentBalanceQuery($filters)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'avatar', 'school_class_id', 'major_subject_id']);

        $paymentsByStudent = StudentPayment::query()
            ->whereIn('student_id', $students->pluck('id')->all())
            ->orderByDesc('payment_date')
            ->latest()
            ->get()
            ->groupBy(fn(StudentPayment $payment): string => (string) $payment->student_id);

        return $students
            ->map(fn(User $student): array => $this->studentPaymentRow(
                $student,
                $paymentsByStudent->get((string) $student->id, collect()),
            ))
            ->when(($filters['status'] ?? 'all') !== 'all', function ($rows) use ($filters) {
                return $rows->filter(fn(array $row): bool => $row['status'] === $filters['status']);
            })
            ->values();
    }

    private function studentBalanceQuery(array $filters = []): Builder
    {
        return User::query()
            ->with(['schoolClass', 'majorSubject', 'majorSubjects'])
            ->where('role', 'student')
            ->when(($filters['studentId'] ?? null) !== null, fn(Builder $query) => $query->whereKey($filters['studentId']))
            ->when(($filters['classId'] ?? null) !== null, fn(Builder $query) => $query->where('school_class_id', $filters['classId']))
            ->when(($filters['from'] ?? '') !== '', function (Builder $query) use ($filters) {
                $query->whereHas('payments', fn(Builder $paymentQuery) => $paymentQuery->whereDate('payment_date', '>=', $filters['from']));
            })
            ->when(($filters['to'] ?? '') !== '', function (Builder $query) use ($filters) {
                $query->whereHas('payments', fn(Builder $paymentQuery) => $paymentQuery->whereDate('payment_date', '<=', $filters['to']));
            })
            ->when(($filters['search'] ?? '') !== '', function (Builder $query) use ($filters) {
                $search = $filters['search'];

                $query->where(function (Builder $inner) use ($search) {
                    $inner->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhereHas('majorSubject', function (Builder $subjectQuery) use ($search) {
                            $subjectQuery->where('name', 'like', '%' . $search . '%')
                                ->orWhere('tuition_fee', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('majorSubjects', function (Builder $subjectQuery) use ($search) {
                            $subjectQuery->where('name', 'like', '%' . $search . '%')
                                ->orWhere('tuition_fee', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('payments', function (Builder $paymentQuery) use ($search) {
                            $paymentQuery->where('note', 'like', '%' . $search . '%')
                                ->orWhere('amount', 'like', '%' . $search . '%')
                                ->orWhere('discount_amount', 'like', '%' . $search . '%');
                        });
                });
            });
    }

    private function studentPaymentRow(User $student, $payments): array
    {
        $latestPayment = $payments->first();
        $paidPayments = $payments->whereIn('status', ['paid', 'partial']);
        $tuitionTotal = (float) $student->tuition_total;
        $paidTotal = (float) $paidPayments->sum(fn(StudentPayment $payment): float => (float) $payment->amount);
        $discountTotal = (float) $payments->sum(fn(StudentPayment $payment): float => (float) $payment->discount_amount);
        $remainingDue = max($tuitionTotal - $paidTotal, 0);
        $status = $this->studentBalanceStatus($tuitionTotal, $paidTotal);

        return [
            'student' => $student,
            'latest_payment' => $latestPayment,
            'tuition_total' => $tuitionTotal,
            'paid_total' => $paidTotal,
            'discount_total' => $discountTotal,
            'cash_collected' => max($paidTotal - $discountTotal, 0),
            'remaining_due' => $remainingDue,
            'status' => $status,
            'status_label' => ucfirst($status),
        ];
    }

    private function studentBalanceStatus(float $tuitionTotal, float $paidTotal): string
    {
        if ($tuitionTotal <= 0.009) {
            return 'waived';
        }

        $remainingDue = max($tuitionTotal - $paidTotal, 0);

        if ($remainingDue <= 0.009) {
            return 'paid';
        }

        return $paidTotal > 0.009 ? 'partial' : 'pending';
    }

    private function studentsWithTuition(array $filters = [])
    {
        return User::query()
            ->with(['schoolClass', 'majorSubject', 'majorSubjects'])
            ->where('role', 'student')
            ->when(($filters['studentId'] ?? null) !== null, fn(Builder $query) => $query->whereKey($filters['studentId']))
            ->when(($filters['classId'] ?? null) !== null, fn(Builder $query) => $query->where('school_class_id', $filters['classId']))
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'avatar', 'school_class_id', 'major_subject_id']);
    }

    private function studentFilterLabel(?int $studentId): string
    {
        if ($studentId === null) {
            return 'All students';
        }

        $student = User::query()
            ->where('role', 'student')
            ->find($studentId, ['id', 'name', 'email']);

        return $student
            ? trim($student->name . ' - ' . $student->email)
            : 'Selected student';
    }
}
