<?php

namespace App\Support\Reports;

use App\Models\ClassStudyTime;
use App\Models\User;
use Illuminate\Support\Collection;

class AdminUserReportFactory
{
    public function makeStudents(Collection $students, array $options = []): array
    {
        $hasPhoneColumn = (bool) ($options['hasPhoneColumn'] ?? false);
        $hasStatusColumn = (bool) ($options['hasStatusColumn'] ?? false);
        $hasClassColumn = (bool) ($options['hasClassColumn'] ?? false);
        $hasMajorSubjectColumn = (bool) ($options['hasMajorSubjectColumn'] ?? false);
        $hasClassStudyTimeColumn = (bool) ($options['hasClassStudyTimeColumn'] ?? false);

        $headings = array_values(array_filter([
            'Student ID',
            'Name',
            'Email',
            $hasPhoneColumn ? 'Phone Number' : null,
            $hasStatusColumn ? 'Status' : null,
            $hasClassColumn ? 'Home Class' : null,
            $hasMajorSubjectColumn ? 'Major Subjects' : null,
            $hasClassStudyTimeColumn ? 'Study Time' : null,
            'Joined',
        ]));

        $rows = $students->map(function (User $student) use (
            $hasPhoneColumn,
            $hasStatusColumn,
            $hasClassColumn,
            $hasMajorSubjectColumn,
            $hasClassStudyTimeColumn
        ) {
            return array_values(array_filter([
                $student->formatted_id,
                $student->name,
                $student->email,
                $hasPhoneColumn ? $this->formatPhone($student->phone_number) : null,
                $hasStatusColumn ? $this->formatStatus($student->is_active) : null,
                $hasClassColumn ? $this->formatClassName($student) : null,
                $hasMajorSubjectColumn ? $this->formatMajorSubjects($student) : null,
                $hasClassStudyTimeColumn ? $this->formatStudyTimes($student) : null,
                $this->formatDate($student->created_at),
            ]));
        })->values()->all();

        return [
            'title' => 'Student Report',
            'subtitle' => 'Filtered student roster with enrollment, status, and assignment details.',
            'sheet_name' => 'Students',
            'accent' => '4F46E5',
            'headings' => $headings,
            'rows' => $this->normalizeRows($rows, count($headings)),
            'cards' => array_values(array_filter([
                $this->card('Exported', (string) $students->count()),
                $hasStatusColumn ? $this->card('Active', (string) $students->where('is_active', true)->count()) : null,
                $hasStatusColumn ? $this->card('Inactive', (string) $students->where('is_active', false)->count()) : null,
                $hasClassColumn ? $this->card('Assigned', (string) $students->filter(fn(User $student) => !empty($student->school_class_id))->count()) : null,
            ])),
            'filters' => $this->normalizeFilters($options['filters'] ?? []),
        ];
    }

    public function makeTeachers(Collection $teachers, array $options = []): array
    {
        $hasPhoneColumn = (bool) ($options['hasPhoneColumn'] ?? false);
        $hasStatusColumn = (bool) ($options['hasStatusColumn'] ?? false);

        $headings = array_values(array_filter([
            'Teacher ID',
            'Name',
            'Email',
            $hasPhoneColumn ? 'Phone Number' : null,
            $hasStatusColumn ? 'Status' : null,
            'Joined',
        ]));

        $rows = $teachers->map(function (User $teacher) use ($hasPhoneColumn, $hasStatusColumn) {
            return array_values(array_filter([
                $teacher->formatted_id,
                $teacher->name,
                $teacher->email,
                $hasPhoneColumn ? $this->formatPhone($teacher->phone_number) : null,
                $hasStatusColumn ? $this->formatStatus($teacher->is_active) : null,
                $this->formatDate($teacher->created_at),
            ]));
        })->values()->all();

        return [
            'title' => 'Teacher Report',
            'subtitle' => 'Filtered teacher roster with contact, availability, and account status details.',
            'sheet_name' => 'Teachers',
            'accent' => '059669',
            'headings' => $headings,
            'rows' => $this->normalizeRows($rows, count($headings)),
            'cards' => array_values(array_filter([
                $this->card('Exported', (string) $teachers->count()),
                $hasStatusColumn ? $this->card('Active', (string) $teachers->where('is_active', true)->count()) : null,
                $hasStatusColumn ? $this->card('Inactive', (string) $teachers->where('is_active', false)->count()) : null,
            ])),
            'filters' => $this->normalizeFilters($options['filters'] ?? []),
        ];
    }

    private function normalizeRows(array $rows, int $columnCount): array
    {
        if (!empty($rows)) {
            return $rows;
        }

        return [[
            'No records found for the selected filters.',
            ...array_fill(0, max(0, $columnCount - 1), ''),
        ]];
    }

    private function normalizeFilters(array $filters): array
    {
        return collect($filters)
            ->map(function ($filter, $label) {
                if (is_array($filter)) {
                    $name = trim((string) ($filter['label'] ?? ''));
                    $value = trim((string) ($filter['value'] ?? ''));

                    return $name !== '' && $value !== '' ? ['label' => $name, 'value' => $value] : null;
                }

                $value = trim((string) $filter);

                return trim((string) $label) !== '' && $value !== ''
                    ? ['label' => (string) $label, 'value' => $value]
                    : null;
            })
            ->filter()
            ->values()
            ->all();
    }

    private function card(string $label, string $value): array
    {
        return [
            'label' => $label,
            'value' => $value,
        ];
    }

    private function formatPhone(?string $phoneNumber): string
    {
        $phoneNumber = trim((string) $phoneNumber);

        return $phoneNumber !== '' ? $phoneNumber : '-';
    }

    private function formatStatus(bool $isActive): string
    {
        return $isActive ? 'Active' : 'Inactive';
    }

    private function formatClassName(User $student): string
    {
        return trim((string) optional($student->schoolClass)->display_name) ?: 'Unassigned';
    }

    private function formatMajorSubjects(User $student): string
    {
        $subjects = $student->relationLoaded('majorSubjects')
            ? $student->majorSubjects
            : collect();

        $names = $subjects
            ->pluck('name')
            ->map(fn($name) => trim((string) $name))
            ->filter()
            ->unique()
            ->values();

        if ($names->isNotEmpty()) {
            return $names->implode(', ');
        }

        return trim((string) optional($student->majorSubject)->name) ?: 'Not assigned';
    }

    private function formatStudyTimes(User $student): string
    {
        $studyTimes = $student->relationLoaded('studyTimes')
            ? $student->studyTimes
            : collect();

        $labels = $studyTimes
            ->map(fn(ClassStudyTime $studyTime) => $this->formatStudyTimeSlot($studyTime))
            ->filter()
            ->unique()
            ->values();

        if ($labels->isNotEmpty()) {
            return $labels->implode("\n");
        }

        if ($student->relationLoaded('classStudyTime') && $student->classStudyTime) {
            return $this->formatStudyTimeSlot($student->classStudyTime);
        }

        return 'Not assigned';
    }

    private function formatStudyTimeSlot(ClassStudyTime $studyTime): string
    {
        $day = match (strtolower(trim((string) ($studyTime->day_of_week ?? '')))) {
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
            default => 'All Days',
        };

        $period = trim((string) $studyTime->period);
        $period = $period !== '' ? ucfirst($period) : 'Class';

        $start = substr((string) $studyTime->start_time, 0, 5);
        $end = substr((string) $studyTime->end_time, 0, 5);

        return sprintf('%s | %s | %s-%s', $day, $period, $start, $end);
    }

    private function formatDate(mixed $date): string
    {
        if (!method_exists($date, 'format')) {
            return '-';
        }

        return $date->format('M d, Y');
    }
}
