@extends('layout.students.navbar')

@section('page')
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
                            <div class="admin-page-header__eyebrow">Grades</div>
                            <h1 class="admin-page-title text-3xl font-black tracking-tight sm:text-4xl">Your Grade Report</h1>
                        </div>
                    </div>
                    <p class="admin-page-subtitle text-sm">
                        Review the grades your teachers have assigned, track your average, and check feedback for each assessment.
                    </p>
                </div>

                <div class="admin-page-header__stats flex flex-wrap items-center gap-2 text-xs font-semibold">
                    <span class="admin-page-stat">
                        Total: {{ number_format($stats['total'] ?? 0) }}
                    </span>
                    <span class="admin-page-stat admin-page-stat--sky">
                        Subjects: {{ number_format($stats['subjects'] ?? 0) }}
                    </span>
                    <span class="admin-page-stat admin-page-stat--emerald">
                        Average: {{ isset($stats['average']) ? number_format((float) $stats['average'], 1) . '%' : 'N/A' }}
                    </span>
                </div>
            </div>
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
