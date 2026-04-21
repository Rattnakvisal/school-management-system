@extends('layout.students.navbar')

@section('page')
    @php
        $fullName = trim((string) ($student->name ?? auth()->user()->name ?? 'Student'));
        $firstName = explode(' ', $fullName)[0] ?? 'Student';
        $studentClass = $student->schoolClass?->display_name ?? 'No class assigned';
        $classRoom = trim((string) ($student->schoolClass?->room ?? ''));
        $studentPhone = trim((string) ($student->phone_number ?? ''));
        $studentEmail = trim((string) ($student->email ?? ''));
        $studentAddress = $classRoom !== '' ? 'Room ' . $classRoom : $studentClass;
        $todayLabel = $dayLabels[$todayKey] ?? ucfirst((string) $todayKey);
        $majorSubjects = collect($majorSubjects ?? []);
        $classSubjects = collect($classSubjects ?? []);
        $latestAttendance = collect($latestAttendance ?? []);
        $monthlyAttendanceTrend = collect($monthlyAttendanceTrend ?? []);
        $todayTimeline = collect($todayTimeline ?? []);
        $displayStudentId = 'ID: ' . ($student->formatted_id ?? str_pad((string) ($student->id ?? 0), 7, '0', STR_PAD_LEFT));
        $studentSubtitle = $majorSubjects->first()?->name ?? $classSubjects->first()?->name ?? 'Student overview';

        $statusCards = collect([
            [
                'value' => (int) ($attendanceByStatus['present'] ?? 0),
                'label' => 'Total Attendance',
                'short_label' => 'Attendance',
                'tone' => 'blue',
                'icon' => 'attendance',
            ],
            [
                'value' => (int) ($attendanceByStatus['late'] ?? 0),
                'label' => 'Late Attendance',
                'short_label' => 'Late',
                'tone' => 'lime',
                'icon' => 'late',
            ],
            [
                'value' => (int) ($attendanceByStatus['excused'] ?? 0),
                'label' => 'Excused Attendance',
                'short_label' => 'Excused',
                'tone' => 'amber',
                'icon' => 'excused',
            ],
            [
                'value' => (int) ($attendanceByStatus['absent'] ?? 0),
                'label' => 'Total Absent',
                'short_label' => 'Absent',
                'tone' => 'rose',
                'icon' => 'absent',
            ],
        ]);

        $statusMax = max(1, (int) $statusCards->max('value'));
        $summaryBars = $statusCards->map(function (array $card) use ($statusMax): array {
            $value = (int) ($card['value'] ?? 0);

            return $card + [
                'height' => max(26, (int) round(($value / $statusMax) * 100)),
                'display_value' => str_pad((string) $value, 2, '0', STR_PAD_LEFT),
            ];
        });
    @endphp

    <div class="student-dashboard-shell dashboard-stage space-y-6">
        <section class="dash-reveal student-profile-card" style="--d: 1;">
            <div class="student-profile-card__top">
                <div>
                    <p class="student-profile-card__eyebrow">Dashboard</p>
                    <h1 class="student-profile-card__title">Student Details</h1>
                </div>

                <span class="student-profile-card__chip">Monthly</span>
            </div>

            <div class="student-profile-card__body">
                <div class="student-profile-card__identity">
                    <img src="{{ $student->avatar_url }}" alt="{{ $fullName }}"
                        onerror="this.onerror=null;this.src='{{ $student->fallback_avatar_url }}';"
                        class="student-profile-card__avatar">

                    <div class="min-w-0">
                        <h2 class="student-profile-card__name">{{ $fullName }}</h2>
                        <p class="student-profile-card__subtitle">
                            {{ $studentClass }}
                            @if ($classRoom !== '')
                                | Room {{ $classRoom }}
                            @endif
                        </p>
                        <p class="student-profile-card__focus">{{ $studentSubtitle }}</p>
                    </div>
                </div>

                <div class="student-profile-card__meta">
                    <article class="student-profile-card__meta-item">
                        <span>ID</span>
                        <strong>{{ $displayStudentId }}</strong>
                    </article>
                    <article class="student-profile-card__meta-item">
                        <span>Number</span>
                        <strong>{{ $studentPhone !== '' ? $studentPhone : 'Not set' }}</strong>
                    </article>
                    <article class="student-profile-card__meta-item">
                        <span>Email</span>
                        <strong>{{ $studentEmail !== '' ? $studentEmail : 'Not set' }}</strong>
                    </article>
                    <article class="student-profile-card__meta-item">
                        <span>Address</span>
                        <strong>{{ $studentAddress }}</strong>
                    </article>
                </div>
            </div>

            <div class="student-profile-card__stats">
                @foreach ($statusCards as $card)
                    <article class="student-stat-card student-stat-card--{{ $card['tone'] }}">
                        <span class="student-stat-card__icon">
                            @switch($card['icon'])
                                @case('attendance')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M12 12a3 3 0 1 0-3-3 3 3 0 0 0 3 3Z" />
                                        <path d="M6.5 18.5a5.5 5.5 0 0 1 11 0" stroke-linecap="round" />
                                    </svg>
                                @break

                                @case('late')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <circle cx="12" cy="12" r="7" />
                                        <path d="M12 8v4l2.5 1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                @break

                                @case('excused')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <circle cx="12" cy="12" r="7" />
                                        <path d="M12 9.5v3.5" stroke-linecap="round" />
                                        <path d="M12 15.5h.01" stroke-linecap="round" />
                                    </svg>
                                @break

                                @default
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M8 8l8 8" stroke-linecap="round" />
                                        <path d="M16 8l-8 8" stroke-linecap="round" />
                                        <circle cx="12" cy="12" r="8" />
                                    </svg>
                            @endswitch
                        </span>

                        <div>
                            <strong>{{ number_format($card['value']) }} Days</strong>
                            <span>{{ $card['label'] }}</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="dash-reveal student-dashboard-main" style="--d: 2;">
            <div class="student-dashboard-main__left">
                <article class="student-insight-card">
                    <div>
                        <h2>Class Days</h2>
                        <p>Class days for Monthly</p>
                    </div>
                    <div class="student-insight-card__value">{{ number_format($attendanceThisMonth ?? 0) }} Days</div>
                </article>

                <article class="student-insight-card student-insight-card--stacked">
                    <div class="student-insight-card__topline">
                        <div>
                            <h2>Attendance Rate</h2>
                            <p>This year</p>
                        </div>
                        <div class="student-insight-card__percentage">
                            {{ number_format((float) ($attendanceRate ?? 0), 1) }}%
                        </div>
                    </div>

                    <div class="student-rate-trend">
                        @forelse ($monthlyAttendanceTrend as $trend)
                            <article class="student-rate-trend__item">
                                <span class="student-rate-trend__month">{{ $trend['label'] }}</span>
                                <strong>{{ number_format((float) ($trend['value'] ?? 0), 1) }}%</strong>
                                <span class="student-rate-trend__track">
                                    <span class="student-rate-trend__fill"
                                        style="width: {{ max(0, min(100, (float) ($trend['value'] ?? 0))) }}%"></span>
                                </span>
                                <small>{{ number_format((int) ($trend['total'] ?? 0)) }} checks</small>
                            </article>
                        @empty
                            <div class="student-empty-state">
                                No monthly attendance data yet.
                            </div>
                        @endforelse
                    </div>
                </article>
            </div>

            <article class="student-summary-card">
                <div class="student-summary-card__head">
                    <div>
                        <p class="student-summary-card__eyebrow">Summary</p>
                        <h2>Summary - {{ $firstName }}</h2>
                    </div>
                    <span class="student-summary-card__chip">{{ $todayLabel }}</span>
                </div>

                <div class="student-summary-bars">
                    @foreach ($summaryBars as $card)
                        <article class="student-summary-bar student-summary-bar--{{ $card['tone'] }}">
                            <div class="student-summary-bar__value">{{ $card['display_value'] }}</div>
                            <div class="student-summary-bar__track">
                                <div class="student-summary-bar__fill" style="height: {{ $card['height'] }}%"></div>
                            </div>
                            <div class="student-summary-bar__label">{{ $card['short_label'] }}</div>
                        </article>
                    @endforeach
                </div>
            </article>
        </section>

        <section class="dash-reveal student-dashboard-lower student-dashboard-lower--single" style="--d: 3;">
            <div class="student-dashboard-lower__side">
                <article class="student-detail-card">
                    <div class="student-detail-card__head">
                        <div>
                            <p class="student-detail-card__eyebrow">Recent</p>
                            <h2>Attendance Checks</h2>
                        </div>
                    </div>

                    @if ($latestAttendance->isNotEmpty())
                        <div class="student-check-list">
                            @foreach ($latestAttendance as $record)
                                <article class="student-check-item">
                                    <div>
                                        <strong>{{ optional($record->attendance_date)->format('d M Y') ?? '-' }}</strong>
                                        <span>{{ $record->teacher?->name ?: 'Teacher update' }}</span>
                                    </div>
                                    <span
                                        class="student-check-item__badge student-check-item__badge--{{ strtolower((string) $record->status) }}">
                                        {{ ucfirst((string) $record->status) }}
                                    </span>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="student-empty-state">
                            No attendance records yet.
                        </div>
                    @endif
                </article>

                <article class="student-detail-card">
                    <div class="student-detail-card__head">
                        <div>
                            <p class="student-detail-card__eyebrow">Subjects</p>
                            <h2>Study Focus</h2>
                        </div>
                    </div>

                    <div class="student-subject-stack">
                        @forelse ($majorSubjects->take(3) as $subject)
                            <article class="student-subject-pill student-subject-pill--emerald">
                                <strong>{{ $subject->name }}</strong>
                                <span>{{ $subject->code ?: 'Major subject' }}</span>
                            </article>
                        @empty
                            @forelse ($classSubjects->take(3) as $subject)
                                <article class="student-subject-pill student-subject-pill--sky">
                                    <strong>{{ $subject->name }}</strong>
                                    <span>
                                        {{ $subject->code ?: 'Class subject' }}
                                        @if ($subject->teacher?->name)
                                            | {{ $subject->teacher->name }}
                                        @endif
                                    </span>
                                </article>
                            @empty
                                <div class="student-empty-state">
                                    No subjects assigned yet.
                                </div>
                            @endforelse
                        @endforelse
                    </div>
                </article>
            </div>
        </section>
    </div>
@endsection
