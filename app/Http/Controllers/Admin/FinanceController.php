<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AdminUserReportExport;
use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\StudentPayment;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
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
        $payments = $this->filteredPaymentQuery($filters)
            ->latest('payment_date')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.finance', [
            ...$this->sharedPayload($filters),
            'payments' => $payments,
            'stats' => $this->stats(),
            'latestPayments' => StudentPayment::query()
                ->with('student.schoolClass')
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

        StudentPayment::create($this->validatedPayment($request));

        return redirect()
            ->route('admin.finance.index')
            ->with('success', 'Student payment recorded successfully.');
    }

    public function update(Request $request, StudentPayment $payment)
    {
        $payment->update($this->validatedPayment($request));

        return redirect()
            ->route('admin.finance.index', $request->query())
            ->with('success', 'Student payment updated successfully.');
    }

    public function destroy(StudentPayment $payment)
    {
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

    private function validatedPayment(Request $request): array
    {
        $validated = $request->validate([
            'student_id' => ['required', 'integer', 'exists:users,id'],
            'amount' => ['required', 'numeric', 'min:0', 'max:999999999.99'],
            'discount_amount' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'payment_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(StudentPayment::STATUSES)],
            'payment_method' => ['nullable', Rule::in(StudentPayment::PAYMENT_METHODS)],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $studentIsValid = User::query()
            ->whereKey($validated['student_id'])
            ->where('role', 'student')
            ->exists();

        if (!$studentIsValid) {
            throw ValidationException::withMessages([
                'student_id' => 'Selected user must be a student.',
            ]);
        }

        $validated['discount_amount'] = $validated['discount_amount'] ?? 0;
        $validated['payment_method'] = $validated['payment_method'] ?? null;
        $validated['reference'] = null;
        $validated['note'] = $validated['note'] ?? null;

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
                'headings' => ['Payment', 'Student', 'Class', 'Status', 'Amount', 'Discount', 'Method', 'Date'],
                'rows' => [['No finance records found. Please run migrations first.', '', '', '', '', '', '', '']],
                'cards' => [],
                'filters' => [],
            ];
        }

        $filters = $this->filters($request);
        $payments = $this->filteredPaymentQuery($filters)
            ->latest('payment_date')
            ->latest()
            ->get();

        $headings = ['Payment', 'Student', 'Class', 'Status', 'Amount', 'Discount', 'Method', 'Date'];
        $rows = $payments->map(function (StudentPayment $payment): array {
            return [
                '#' . $payment->id,
                (string) ($payment->student?->name ?? 'Unknown Student'),
                (string) ($payment->student?->schoolClass?->display_name ?? 'Unassigned'),
                $payment->status_label,
                '$' . number_format((float) $payment->amount, 2),
                '$' . number_format((float) $payment->discount_amount, 2),
                $payment->method_label,
                optional($payment->payment_date)->format('M d, Y') ?? '-',
            ];
        })->values()->all();

        if ($rows === []) {
            $rows = [['No finance records found for the selected filters.', '', '', '', '', '', '', '']];
        }

        $collected = $payments
            ->whereIn('status', ['paid', 'partial'])
            ->sum(fn(StudentPayment $payment): float => (float) $payment->amount);
        $outstanding = $payments
            ->whereIn('status', ['pending', 'overdue'])
            ->sum(fn(StudentPayment $payment): float => (float) $payment->amount);

        return [
            'title' => 'Student Payments Report',
            'subtitle' => 'Detailed finance report for student payments, balances, methods, and payment dates.',
            'sheet_name' => 'Payments',
            'accent' => '0F766E',
            'headings' => $headings,
            'rows' => $rows,
            'cards' => [
                ['label' => 'Payments', 'value' => (string) $payments->count()],
                ['label' => 'Collected', 'value' => '$' . number_format($collected, 2)],
                ['label' => 'Outstanding', 'value' => '$' . number_format($outstanding, 2)],
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
                'students_paid' => 0,
            ],
            'latestPayments' => collect(),
            'tableReady' => false,
        ];
    }

    private function sharedPayload(array $filters): array
    {
        return [
            'students' => User::query()
                ->where('role', 'student')
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'school_class_id']),
            'classes' => Schema::hasTable('school_classes')
                ? SchoolClass::query()->orderBy('name')->orderBy('section')->get()
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
        $payments = StudentPayment::query();

        return [
            'payments' => (clone $payments)->count(),
            'collected' => (float) (clone $payments)->whereIn('status', ['paid', 'partial'])->sum('amount'),
            'outstanding' => (float) (clone $payments)->whereIn('status', ['pending', 'overdue'])->sum('amount'),
            'discounts' => (float) (clone $payments)->sum('discount_amount'),
            'students_paid' => (clone $payments)->distinct('student_id')->count('student_id'),
        ];
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

    private function filteredPaymentQuery(array $filters): Builder
    {
        return StudentPayment::query()
            ->with('student.schoolClass')
            ->when($filters['search'] !== '', function (Builder $query) use ($filters) {
                $search = $filters['search'];

                $query->where(function (Builder $inner) use ($search) {
                    $inner->where('note', 'like', '%' . $search . '%')
                        ->orWhereHas('student', function (Builder $studentQuery) use ($search) {
                            $studentQuery->where('name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($filters['status'] !== 'all', fn(Builder $query) => $query->where('status', $filters['status']))
            ->when($filters['studentId'] !== null, fn(Builder $query) => $query->where('student_id', $filters['studentId']))
            ->when($filters['classId'] !== null, function (Builder $query) use ($filters) {
                $query->whereHas('student', fn(Builder $studentQuery) => $studentQuery->where('school_class_id', $filters['classId']));
            })
            ->when($filters['from'] !== '', fn(Builder $query) => $query->whereDate('payment_date', '>=', $filters['from']))
            ->when($filters['to'] !== '', fn(Builder $query) => $query->whereDate('payment_date', '<=', $filters['to']));
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
