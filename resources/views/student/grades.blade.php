@extends('layout.students.navbar')

@section('page')
    @php
        $gradeRows = collect(
            ($grades ?? null) && is_object($grades) && method_exists($grades, 'items') ? $grades->items() : [],
        );
        $subjectOptions = collect($subjectOptions ?? []);
        $termOptions = collect($termOptions ?? []);
        $selectedSubjectId = (int) ($selectedSubjectId ?? 0);
        $selectedTerm = (string) ($selectedTerm ?? '');
        $gradeTotal = (int) ($stats['total'] ?? 0);
        $gradeSubjectTotal = (int) ($stats['subjects'] ?? 0);
        $gradeAverage = isset($stats['average']) ? (float) $stats['average'] : null;
        $transcriptStats = $transcriptStats ?? [];
        $selectedSubjectLabel = 'All Programs';
        $selectedTermLabel = 'All posted grades';
        $gradeStatPanelClass =
            'rounded-[26px] border border-white/80 bg-white/90 shadow-[0_24px_55px_-36px_rgba(78,85,135,0.55)] ring-1 ring-slate-200 backdrop-blur';

        foreach ($subjectOptions as $option) {
            if ((int) ($option['id'] ?? 0) === $selectedSubjectId) {
                $selectedSubjectLabel = (string) ($option['label'] ?? 'Selected program');
                break;
            }
        }

        foreach ($termOptions as $option) {
            if ((string) ($option['value'] ?? '') === $selectedTerm) {
                $selectedTermLabel = (string) ($option['label'] ?? 'Selected term');
                break;
            }
        }

        $gradePointFor = function (float $score, float $maxScore): float {
            $percentage = $maxScore > 0 ? ($score / $maxScore) * 100 : 0;

            return round(
                match (true) {
                    $percentage >= 90 => 4.0,
                    $percentage >= 80 => 3.0 + ($percentage - 80) / 10,
                    $percentage >= 70 => 2.0 + ($percentage - 70) / 10,
                    $percentage >= 60 => 1.0 + ($percentage - 60) / 10,
                    default => 0.0,
                },
                2,
            );
        };

        $remarkFor = function (float $point, ?string $remarks): string {
            $cleanRemarks = trim((string) $remarks);
            if ($cleanRemarks !== '') {
                return $cleanRemarks;
            }

            if ($point >= 3.5) {
                return 'Perfect';
            }

            if ($point >= 3) {
                return 'Very Good';
            }

            if ($point >= 2) {
                return 'Good';
            }

            if ($point >= 1) {
                return 'Needs Improvement';
            }

            return 'Not Good';
        };

        $creditFor = fn($grade): int => 3;
        $hasActiveFilters = $selectedSubjectId > 0 || $selectedTerm !== '';
        $gradeFilterSummary = trim($selectedSubjectLabel . ' | ' . $selectedTermLabel);
        $creditsTransferred = (int) ($transcriptStats['credits_transferred'] ?? 0);
        $summaryCreditsAttempted = (int) ($transcriptStats['credits_attempted'] ?? 0);
        $summaryCreditsEarned = (int) ($transcriptStats['credits_earned'] ?? 0);
        $summaryTotalPoints = (float) ($transcriptStats['total_points'] ?? 0);
        $summaryCumulativeGpa = $transcriptStats['cumulative_gpa'] ?? null;
    @endphp

    <div class="student-stage space-y-6">
        <section class="student-reveal student-float admin-page-header" style="--sd: 1;">
            <div class="admin-page-header__main flex flex-wrap items-start gap-4">
                <div class="admin-page-header__intro space-y-2">
                    <div class="admin-page-header__title-row flex items-start gap-3">
                        <span class="admin-page-header__icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M9 4.5h6A1.5 1.5 0 0 1 16.5 6v1H19v13H5V7h2.5V6A1.5 1.5 0 0 1 9 4.5Z" />
                                <path d="M9 4.5h6v3H9v-3Z" />
                                <path d="M8.5 13h7" />
                                <path d="M8.5 16.5h4" />
                            </svg>
                        </span>
                        <div>
                            <div class="admin-page-header__eyebrow">Student Portal</div>
                            <h1 class="admin-page-title text-3xl font-black tracking-tight sm:text-4xl">My Grades</h1>
                        </div>
                    </div>
                    <p class="admin-page-subtitle text-sm">
                        Review your assigned grades, GPA progress, teacher feedback, and score history in one page.
                    </p>
                    <p class="text-xs font-semibold text-slate-600">
                        Home Class: <span class="text-slate-900">{{ $classLabel }}</span>
                    </p>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
            <article class="student-reveal student-float {{ $gradeStatPanelClass }} min-h-[132px] p-5" style="--sd: 2;">
                <div class="flex items-start justify-between gap-4">
                    <span
                        class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br from-indigo-100 to-white text-indigo-600">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M9 4.5h6A1.5 1.5 0 0 1 16.5 6v1H19v13H5V7h2.5V6A1.5 1.5 0 0 1 9 4.5Z" />
                            <path d="M8.5 13h7" />
                        </svg>
                    </span>
                    <span class="text-slate-300">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path
                                d="M12 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-5 flex items-end gap-1 text-2xl font-black tracking-[-0.04em] text-slate-950">
                    <span>{{ number_format($gradeTotal) }}</span>
                    <span class="pb-0.5 text-base font-extrabold text-slate-300">/
                        {{ number_format(max(1, $gradeTotal)) }}</span>
                </div>
                <div class="mt-1 text-sm font-bold text-slate-600">Grades</div>
                <div class="mt-1 text-[11px] font-semibold text-slate-400">Posted:
                    {{ number_format($gradeTotal) }}</div>
                <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-slate-100">
                    <span class="block h-full rounded-full bg-gradient-to-r from-indigo-500 to-cyan-400"
                        style="width: {{ $gradeTotal > 0 ? 100 : 0 }}%"></span>
                </div>
            </article>

            <article class="student-reveal student-float {{ $gradeStatPanelClass }} min-h-[132px] p-5" style="--sd: 3;">
                <div class="flex items-start justify-between gap-4">
                    <span
                        class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br from-emerald-100 to-white text-emerald-600">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M4 19.5V5a2 2 0 0 1 2-2h11a3 3 0 0 1 3 3v14a2 2 0 0 0-2-2H6a2 2 0 0 0-2 1.5Z" />
                            <path d="M8 7h7" />
                        </svg>
                    </span>
                    <span class="text-slate-300">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path
                                d="M12 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-5 flex items-end gap-1 text-2xl font-black tracking-[-0.04em] text-slate-950">
                    <span>{{ number_format($gradeSubjectTotal) }}</span>
                    <span class="pb-0.5 text-base font-extrabold text-slate-300">/
                        {{ number_format(max(1, $gradeTotal)) }}</span>
                </div>
                <div class="mt-1 text-sm font-bold text-slate-600">Subjects</div>
                <div class="mt-1 text-[11px] font-semibold text-slate-400">With grades:
                    {{ number_format($gradeSubjectTotal) }}</div>
                <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-slate-100">
                    <span class="block h-full rounded-full bg-gradient-to-r from-emerald-500 to-cyan-400"
                        style="width: {{ min(100, max(0, (int) round(($gradeSubjectTotal / max(1, $gradeTotal)) * 100))) }}%"></span>
                </div>
            </article>

            <article class="student-reveal student-float {{ $gradeStatPanelClass }} min-h-[132px] p-5" style="--sd: 4;">
                <div class="flex items-start justify-between gap-4">
                    <span
                        class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br from-sky-100 to-white text-sky-600">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 3v18" />
                            <path d="m17 8-5-5-5 5" />
                            <path d="M5 21h14" />
                        </svg>
                    </span>
                    <span class="text-slate-300">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path
                                d="M12 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-5 flex items-end gap-1 text-2xl font-black tracking-[-0.04em] text-slate-950">
                    <span>{{ $gradeAverage !== null ? number_format($gradeAverage, 1) : 'N/A' }}</span>
                    @if ($gradeAverage !== null)
                        <span class="pb-0.5 text-base font-extrabold text-slate-300">%</span>
                    @endif
                </div>
                <div class="mt-1 text-sm font-bold text-slate-600">Average</div>
                <div class="mt-1 text-[11px] font-semibold text-slate-400">Overall:
                    {{ $gradeAverage !== null ? number_format($gradeAverage, 1) . '%' : 'No score yet' }}</div>
                <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-slate-100">
                    <span class="block h-full rounded-full bg-gradient-to-r from-sky-500 to-cyan-400"
                        style="width: {{ $gradeAverage !== null ? min(100, max(0, (int) round($gradeAverage))) : 0 }}%"></span>
                </div>
            </article>

            <article class="student-reveal student-float {{ $gradeStatPanelClass }} min-h-[132px] p-5" style="--sd: 5;">
                <div class="flex items-start justify-between gap-4">
                    <span
                        class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br from-amber-100 to-white text-amber-600">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M8 12h8" />
                            <path d="M12 8v8" />
                        </svg>
                    </span>
                    <span class="text-slate-300">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path
                                d="M12 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-5 flex items-end gap-1 text-2xl font-black tracking-[-0.04em] text-slate-950">
                    <span>{{ $summaryCumulativeGpa !== null ? number_format((float) $summaryCumulativeGpa, 2) : 'N/A' }}</span>
                    @if ($summaryCumulativeGpa !== null)
                        <span class="pb-0.5 text-base font-extrabold text-slate-300">/ 4.00</span>
                    @endif
                </div>
                <div class="mt-1 text-sm font-bold text-slate-600">GPA</div>
                <div class="mt-1 text-[11px] font-semibold text-slate-400">Cumulative grade point</div>
                <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-slate-100">
                    <span class="block h-full rounded-full bg-gradient-to-r from-amber-500 to-cyan-400"
                        style="width: {{ $summaryCumulativeGpa !== null ? min(100, max(0, (int) round(((float) $summaryCumulativeGpa / 4) * 100))) : 0 }}%"></span>
                </div>
            </article>
        </section>

        <section x-data="{ gradeFilterOpen: false }"
            class="student-reveal student-float rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200"
            style="--sd: 7;">
            <div class="mb-4 flex items-center justify-between gap-3">
                <h2 class="text-lg font-bold text-slate-900">Grade Records</h2>
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" @click="gradeFilterOpen = true"
                        class="inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-white px-3 py-1.5 text-xs font-bold uppercase tracking-wide text-indigo-700 shadow-sm transition hover:bg-indigo-50">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M3 5h18l-7 8v5l-4 2v-7L3 5z"></path>
                        </svg>
                        Filters
                    </button>
                    <span
                        class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">
                        {{ $gradeFilterSummary }}
                    </span>
                    @if ($hasActiveFilters)
                        <a href="{{ route('student.grades.index') }}"
                            class="rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700 transition hover:bg-amber-100">
                            Clear filters
                        </a>
                    @else
                        <span
                            class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                            Latest first
                        </span>
                    @endif
                </div>
            </div>

            <div x-show="gradeFilterOpen" x-cloak x-transition.opacity class="fixed inset-0 z-[80] bg-slate-900/40"
                @click="gradeFilterOpen = false"></div>

            <aside x-show="gradeFilterOpen" x-cloak x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="fixed inset-y-0 right-0 z-[81] flex w-full max-w-xl transform flex-col border-l border-slate-200 bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
                    <h3 class="text-3xl font-black text-slate-900">Filters</h3>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('student.grades.index') }}"
                            class="rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:bg-slate-100 hover:text-slate-800">
                            Clear All
                        </a>
                        <button type="button" @click="gradeFilterOpen = false"
                            class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-slate-200 bg-white text-3xl font-bold leading-none text-slate-600 shadow-sm transition hover:bg-slate-100 hover:text-slate-900"
                            aria-label="Close filters">
                            &times;
                        </button>
                    </div>
                </div>

                <form method="GET" action="{{ route('student.grades.index') }}" class="flex min-h-0 flex-1 flex-col"
                    @submit="gradeFilterOpen = false">
                    <div class="nav-scrollbar flex-1 space-y-7 overflow-y-auto px-6 py-6">
                        <section class="space-y-3">
                            <h4 class="text-2xl font-black text-slate-900">Program / Subject</h4>
                            <select id="grade-subject" name="subject_id"
                                class="h-12 w-full rounded-3xl border border-slate-300 bg-white px-5 text-base text-slate-800 outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                <option value="0">All Programs</option>
                                @foreach ($subjectOptions as $option)
                                    <option value="{{ (int) $option['id'] }}"
                                        {{ $selectedSubjectId === (int) $option['id'] ? 'selected' : '' }}>
                                        {{ $option['label'] }}{{ trim((string) ($option['code'] ?? '')) !== '' ? ' | ' . $option['code'] : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </section>

                        <section class="space-y-3">
                            <h4 class="text-2xl font-black text-slate-900">Term</h4>
                            <select id="grade-term" name="term"
                                class="h-12 w-full rounded-3xl border border-slate-300 bg-white px-5 text-base text-slate-800 outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                <option value="">All posted grades</option>
                                @foreach ($termOptions as $option)
                                    <option value="{{ $option['value'] }}"
                                        {{ $selectedTerm === (string) $option['value'] ? 'selected' : '' }}>
                                        {{ $option['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </section>

                        @if ($termOptions->isNotEmpty())
                            <section class="space-y-3">
                                <h4 class="text-2xl font-black text-slate-900">Quick Terms</h4>
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('student.grades.index', ['subject_id' => $selectedSubjectId]) }}"
                                        class="inline-flex items-center rounded-full border px-3 py-1.5 text-xs font-semibold transition {{ $selectedTerm === ''
                                            ? 'border-indigo-200 bg-indigo-50 text-indigo-700 shadow-sm'
                                            : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300 hover:bg-slate-50' }}">
                                        All posted grades
                                    </a>
                                    @foreach ($termOptions as $option)
                                        <a href="{{ route('student.grades.index', ['subject_id' => $selectedSubjectId, 'term' => $option['value']]) }}"
                                            class="inline-flex items-center rounded-full border px-3 py-1.5 text-xs font-semibold transition {{ $selectedTerm === (string) $option['value']
                                                ? 'border-indigo-200 bg-indigo-50 text-indigo-700 shadow-sm'
                                                : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300 hover:bg-slate-50' }}">
                                            {{ $option['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </section>
                        @endif
                    </div>

                    <div class="border-t border-slate-200 px-6 py-5">
                        <button type="submit"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-slate-950 px-4 py-3 text-base font-bold text-white shadow-sm transition hover:bg-slate-800">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M3 5h18l-7 8v5l-4 2v-7L3 5z"></path>
                            </svg>
                            Apply Filters
                        </button>
                    </div>
                </form>
            </aside>

            @if ($gradeRows->isNotEmpty())
                <div class="overflow-hidden rounded-2xl border border-slate-200">
                    <div class="student-table-scroller max-h-[720px] overflow-auto">
                        <table class="student-table student-schedule-table w-full whitespace-nowrap text-left text-sm">
                            <thead
                                class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-3 py-3 font-semibold">CourseCode</th>
                                    <th class="px-3 py-3 font-semibold">Course Title</th>
                                    <th class="px-3 py-3 font-semibold">Alpha. Grade</th>
                                    <th class="px-3 py-3 font-semibold">Credits Attempt.</th>
                                    <th class="px-3 py-3 font-semibold">Credits Earned</th>
                                    <th class="px-3 py-3 font-semibold">GradePoint</th>
                                    <th class="px-3 py-3 font-semibold">TotalPoint</th>
                                    <th class="px-3 py-3 font-semibold">Remarks</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                <tr class="bg-white">
                                    <td class="px-3 py-3"></td>
                                    <td colspan="7" class="px-3 py-3 text-base font-black text-slate-950 underline">
                                        {{ $gradeFilterSummary }}
                                    </td>
                                </tr>

                                @foreach ($gradeRows as $grade)
                                    @php
                                        $score = (float) ($grade->score ?? 0);
                                        $maxScore = (float) ($grade->max_score ?? 0);
                                        $percentage = $maxScore > 0 ? round(($score / $maxScore) * 100, 1) : 0;
                                        $letter = trim((string) ($grade->grade_letter ?? ''));
                                        $point = $gradePointFor($score, $maxScore);
                                        $credit = $creditFor($grade);
                                        $earnedCredit = $point > 0 ? $credit : 0;
                                        $totalPoint = $credit * $point;
                                        $subjectCode = trim((string) ($grade->subject?->code ?? ''));
                                        $subjectName = trim((string) ($grade->subject?->name ?? 'General result'));
                                        $letterClass = 'border-red-200 bg-red-50 text-red-700';
                                        if (in_array($letter, ['A+', 'A', 'A-', 'B+', 'B'], true)) {
                                            $letterClass = 'border-emerald-200 bg-emerald-50 text-emerald-700';
                                        } elseif (str_starts_with($letter, 'C')) {
                                            $letterClass = 'border-amber-200 bg-amber-50 text-amber-700';
                                        }
                                    @endphp

                                    <tr id="grade-{{ $grade->id }}" class="hover:bg-slate-50/80">
                                        <td class="px-3 py-3 align-top text-center font-semibold text-slate-700">
                                            {{ $subjectCode !== '' ? $subjectCode : 'GEN ' . $grade->id }}
                                        </td>
                                        <td class="px-3 py-3 align-top">
                                            <div class="student-name font-semibold text-slate-900 whitespace-normal">
                                                {{ $subjectName !== '' ? $subjectName : $grade->title }}
                                            </div>
                                            <div class="text-xs text-slate-500">
                                                {{ $grade->title }} |
                                                {{ $grade->graded_at?->format('M d, Y') ?? 'No date' }}
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 align-top">
                                            <span
                                                class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold {{ $letterClass }}">
                                                {{ $letter !== '' ? $letter : 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 align-top text-center font-semibold text-slate-700">
                                            {{ $credit }}
                                        </td>
                                        <td class="px-3 py-3 align-top text-center font-semibold text-slate-700">
                                            {{ $earnedCredit }}
                                        </td>
                                        <td class="px-3 py-3 align-top text-center font-semibold text-slate-700">
                                            {{ number_format($point, 2) }}
                                        </td>
                                        <td class="px-3 py-3 align-top text-center font-semibold text-slate-700">
                                            {{ number_format($totalPoint, $totalPoint == (int) $totalPoint ? 0 : 1) }}
                                        </td>
                                        <td class="px-3 py-3 align-top">
                                            <div class="max-w-xs whitespace-normal text-xs leading-relaxed text-slate-600">
                                                {{ $remarkFor($point, $grade->remarks) }}
                                            </div>
                                            <div class="mt-1 text-[11px] text-slate-400">
                                                {{ number_format($score, 2) }}/{{ number_format($maxScore, 2) }}
                                                ({{ number_format($percentage, 1) }}%)
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                                <tr class="bg-white">
                                    <td colspan="4" class="px-3 py-4 text-center text-sm font-black text-slate-900">
                                        Total Credits Transferred
                                    </td>
                                    <td class="px-3 py-4 text-center text-sm font-black text-slate-900">
                                        {{ number_format($creditsTransferred) }}
                                    </td>
                                    <td colspan="3"></td>
                                </tr>
                                <tr class="bg-white">
                                    <td colspan="3" class="px-3 py-4 text-center text-sm font-black text-slate-900">
                                        Total Credits Attained
                                    </td>
                                    <td class="px-3 py-4 text-center text-sm font-black text-slate-900">
                                        {{ number_format($summaryCreditsAttempted) }}
                                    </td>
                                    <td class="px-3 py-4 text-center text-sm font-black text-slate-900">
                                        {{ number_format($summaryCreditsEarned) }}
                                    </td>
                                    <td></td>
                                    <td class="px-3 py-4 text-center text-sm font-black text-slate-900">
                                        {{ number_format($summaryTotalPoints, 2) }}
                                    </td>
                                    <td></td>
                                </tr>
                                <tr class="border-t border-slate-200 bg-slate-50/80">
                                    <td colspan="6" class="px-3 py-4 text-center text-sm font-black text-slate-900">
                                        Cumulative GPA
                                    </td>
                                    <td class="px-3 py-4 text-center text-sm font-black text-slate-900">
                                        {{ $summaryCumulativeGpa !== null ? number_format((float) $summaryCumulativeGpa, 2) : 'N/A' }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                @if (!empty($grades) && $grades->hasPages())
                    <div class="mt-4">
                        {{ $grades->links() }}
                    </div>
                @endif
            @else
                <div
                    class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-10 text-center text-sm text-slate-500">
                    No grades found for the selected filters.
                </div>
            @endif
        </section>
    </div>
@endsection
