<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AdminUserReportExport;
use App\Http\Controllers\Controller;
use App\Support\AdminDashboardViewData;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

class ReportsController extends Controller
{
    public function __invoke(AdminDashboardViewData $viewData)
    {
        if (strtolower(trim((string) (auth()->user()?->role ?? ''))) === 'staff') {
            return redirect()->route('staff.dashboard');
        }

        return view('admin.reports', $viewData->make());
    }

    public function exportPdf(AdminDashboardViewData $viewData)
    {
        $report = $this->reportPayload($viewData);

        return Pdf::loadView('admin.reports.users-pdf', [
            ...$report,
            'generatedAt' => now()->format('F j, Y g:i A'),
        ])
            ->setPaper('a4', 'portrait')
            ->download('admin-reports-overview-' . now()->format('Ymd-His') . '.pdf');
    }

    public function exportExcel(AdminDashboardViewData $viewData)
    {
        $report = $this->reportPayload($viewData);

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
            'admin-reports-overview-' . now()->format('Ymd-His') . '.xlsx'
        );
    }

    private function reportPayload(AdminDashboardViewData $viewData): array
    {
        $data = $viewData->make();

        $activeStudents = (int) ($data['studentsActive'] ?? 0);
        $totalStudents = (int) ($data['studentsTotal'] ?? 0);
        $activeTeachers = (int) ($data['teachersActive'] ?? 0);
        $totalTeachers = (int) ($data['teachersTotal'] ?? 0);
        $activeClasses = (int) ($data['classesActive'] ?? 0);
        $totalClasses = (int) ($data['classesTotal'] ?? 0);
        $activeSubjects = (int) ($data['subjectsActive'] ?? 0);
        $totalSubjects = (int) ($data['subjectsTotal'] ?? 0);
        $unreadMessages = (int) ($data['messagesUnread'] ?? 0);
        $totalMessages = (int) ($data['messagesTotal'] ?? 0);
        $studentsWithMajor = (int) ($data['studentsWithMajor'] ?? 0);
        $studentsWithStudyTime = (int) ($data['studentsWithStudyTime'] ?? 0);
        $financeStats = $data['financeStats'] ?? [];
        $financeCollected = (float) ($financeStats['collected'] ?? 0);
        $financeOutstanding = (float) ($financeStats['outstanding'] ?? 0);
        $financePayments = (int) ($financeStats['payments'] ?? 0);

        return [
            'title' => 'Admin Reports Overview',
            'subtitle' => 'School-wide performance, enrollment, assignment, payment, and communication summary.',
            'sheet_name' => 'Reports Overview',
            'accent' => '4F46E5',
            'headings' => ['Area', 'Active / Current', 'Total', 'Detail'],
            'rows' => [
                ['Students', (string) $activeStudents, (string) $totalStudents, $studentsWithMajor . ' with major subjects, ' . $studentsWithStudyTime . ' with study time'],
                ['Teachers', (string) $activeTeachers, (string) $totalTeachers, 'Teacher account availability and status'],
                ['Classes', (string) $activeClasses, (string) $totalClasses, 'Active class records'],
                ['Subjects', (string) $activeSubjects, (string) $totalSubjects, 'Active subject records'],
                ['Finance', '$' . number_format($financeCollected, 2), (string) $financePayments, '$' . number_format($financeOutstanding, 2) . ' outstanding student payments'],
                ['Messages', (string) $unreadMessages, (string) $totalMessages, 'Unread contact messages'],
            ],
            'cards' => [
                ['label' => 'Students', 'value' => number_format($activeStudents) . ' / ' . number_format($totalStudents)],
                ['label' => 'Teachers', 'value' => number_format($activeTeachers) . ' / ' . number_format($totalTeachers)],
                ['label' => 'Classes', 'value' => number_format($activeClasses) . ' / ' . number_format($totalClasses)],
                ['label' => 'Collected', 'value' => '$' . number_format($financeCollected, 2)],
            ],
            'filters' => [
                ['label' => 'Scope', 'value' => 'All admin dashboard data'],
                ['label' => 'Finance Table', 'value' => Schema::hasTable('student_payments') ? 'Available' : 'Migration pending'],
            ],
        ];
    }
}
