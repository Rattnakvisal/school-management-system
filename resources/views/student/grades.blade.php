@extends('layout.students.navbar')

@section('page')
    @php
        $studentStatPanelClass =
            'rounded-[26px] border border-white/80 bg-white/90 shadow-[0_24px_55px_-36px_rgba(78,85,135,0.55)] ring-1 ring-slate-200 backdrop-blur';
        $gradeTotal = (int) ($stats['total'] ?? 0);
        $gradeSubjectTotal = (int) ($stats['subjects'] ?? 0);
        $gradeAverage = isset($stats['average']) ? (float) $stats['average'] : null;
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
                            <h1 class="admin-page-title text-3xl font-black tracking-tight sm:text-4xl">Your Grade Report</h1>
                        </div>
                    </div>
                    <p class="admin-page-subtitle text-sm">
                        Review the grades your teachers have assigned, track your average, and check feedback for each assessment.
                    </p>
                    <p class="text-xs font-semibold text-slate-600">
                        Home Class: <span class="text-slate-900">{{ $classLabel }}</span>
                    </p>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
            <article class="student-reveal student-float {{ $studentStatPanelClass }} min-h-[132px] p-5" style="--sd: 2;">
                <div class="flex items-start justify-between gap-4">
                    <span
                        class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br from-indigo-100 to-white text-indigo-600">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M9 4.5h6A1.5 1.5 0 0 1 16.5 6v1H19v13H5V7h2.5V6A1.5 1.5 0 0 1 9 4.5Z" />
                            <path d="M9 4.5h6v3H9v-3Z" />
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

            <article class="student-reveal student-float {{ $studentStatPanelClass }} min-h-[132px] p-5" style="--sd: 3;">
                <div class="flex items-start justify-between gap-4">
                    <span
                        class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br from-sky-100 to-white text-sky-600">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M4 19.5V5a2 2 0 0 1 2-2h11a3 3 0 0 1 3 3v14a2 2 0 0 0-2-2H6a2 2 0 0 0-2 1.5Z" />
                            <path d="M8 7h7" />
                            <path d="M8 11h5" />
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
                    <span class="block h-full rounded-full bg-gradient-to-r from-sky-500 to-cyan-400"
                        style="width: {{ min(100, max(0, (int) round(($gradeSubjectTotal / max(1, $gradeTotal)) * 100))) }}%"></span>
                </div>
            </article>

            <article class="student-reveal student-float {{ $studentStatPanelClass }} min-h-[132px] p-5" style="--sd: 4;">
                <div class="flex items-start justify-between gap-4">
                    <span
                        class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br from-emerald-100 to-white text-emerald-600">
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
                    <span class="block h-full rounded-full bg-gradient-to-r from-emerald-500 to-cyan-400"
                        style="width: {{ $gradeAverage !== null ? min(100, max(0, (int) round($gradeAverage))) : 0 }}%"></span>
                </div>
            </article>
        </section>

        @if (($grades ?? null) && $grades->count() > 0)
            <section class="grid gap-4 xl:grid-cols-2">
                @foreach ($grades as $grade)
                    @php
                        $score = (float) ($grade->score ?? 0);
                        $maxScore = (float) ($grade->max_score ?? 0);
                        $percentage = $maxScore > 0 ? round(($score / $maxScore) * 100, 1) : 0;
                        $letter = trim((string) ($grade->grade_letter ?? ''));
                        $letterClass = 'border-red-200 bg-red-50 text-red-700';
                        if (in_array($letter, ['A', 'B'], true)) {
                            $letterClass = 'border-emerald-200 bg-emerald-50 text-emerald-700';
                        } elseif ($letter === 'C') {
                            $letterClass = 'border-amber-200 bg-amber-50 text-amber-700';
                        }
                    @endphp

                    <article id="grade-{{ $grade->id }}"
                        class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full border px-2.5 py-1 text-[11px] font-semibold {{ $letterClass }}">
                                        Grade {{ $letter !== '' ? $letter : 'N/A' }}
                                    </span>
                                    <span
                                        class="rounded-full border border-indigo-100 bg-indigo-50 px-2.5 py-1 text-[11px] font-semibold text-indigo-700">
                                        {{ $grade->subject?->name ?? 'General result' }}
                                    </span>
                                </div>
                                <h2 class="mt-3 text-xl font-black tracking-tight text-slate-900">
                                    {{ $grade->title }}
                                </h2>
                            </div>

                            <div class="text-right text-xs font-medium text-slate-400">
                                <div>{{ $grade->graded_at?->format('M d, Y') }}</div>
                                <div class="mt-1">{{ $grade->created_at?->diffForHumans() }}</div>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-3 sm:grid-cols-3">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-3">
                                <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Teacher</div>
                                <div class="mt-1 text-sm font-semibold text-slate-900">
                                    {{ $grade->teacher?->name ?? 'Teacher' }}
                                </div>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-3">
                                <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Score</div>
                                <div class="mt-1 text-sm font-semibold text-slate-900">
                                    {{ number_format($score, 2) }}/{{ number_format($maxScore, 2) }}
                                </div>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-3">
                                <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Percentage</div>
                                <div class="mt-1 text-sm font-semibold text-slate-900">
                                    {{ number_format($percentage, 1) }}%
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 rounded-2xl border border-slate-200 bg-white px-4 py-4">
                            <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Teacher Feedback</div>
                            <div class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">
                                {{ trim((string) ($grade->remarks ?? '')) !== '' ? $grade->remarks : 'No additional feedback was added for this grade.' }}
                            </div>
                        </div>
                    </article>
                @endforeach
            </section>

            <div>
                {{ $grades->links() }}
            </div>
        @else
            <section class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-6 py-14 text-center shadow-sm">
                <div class="text-xl font-black text-slate-900">No grades yet</div>
                <p class="mt-2 text-sm text-slate-500">
                    When a teacher assigns your first grade, it will appear here and you will receive a notification alert.
                </p>
            </section>
        @endif
    </div>
@endsection
