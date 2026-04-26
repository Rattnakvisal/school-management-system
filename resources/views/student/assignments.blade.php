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
                                <path d="M8 3.5h8A2.5 2.5 0 0 1 18.5 6v14l-3-1.75L12.5 20l-3-1.75L6.5 20V6A2.5 2.5 0 0 1 9 3.5Z" />
                                <path d="M9.5 8h5" />
                                <path d="M9.5 11.5h5" />
                                <path d="M9.5 15h3" />
                            </svg>
                        </span>
                        <div>
                            <div class="admin-page-header__eyebrow">Assignments</div>
                            <h1 class="admin-page-title text-3xl font-black tracking-tight sm:text-4xl">Your Assignment Board</h1>
                        </div>
                    </div>
                    <p class="admin-page-subtitle text-sm">
                        Review the work your teachers posted for you, check deadlines, and stay ahead of upcoming tasks.
                    </p>
                </div>

                <div class="admin-page-header__stats flex flex-wrap items-center gap-2 text-xs font-semibold">
                    <span class="admin-page-stat">
                        Total: {{ number_format($stats['total'] ?? 0) }}
                    </span>
                    <span class="admin-page-stat admin-page-stat--amber">
                        Due Soon: {{ number_format($stats['dueSoon'] ?? 0) }}
                    </span>
                    <span class="admin-page-stat admin-page-stat--rose">
                        Overdue: {{ number_format($stats['overdue'] ?? 0) }}
                    </span>
                </div>
            </div>
        </section>

        @if (($assignments ?? null) && $assignments->count() > 0)
            <section class="grid gap-4 xl:grid-cols-2">
                @foreach ($assignments as $assignment)
                    @php
                        $dueAt = $assignment->due_at;
                        $statusLabel = 'No due date';
                        $statusClass = 'border-slate-200 bg-slate-50 text-slate-700';

                        if ($dueAt) {
                            if ($dueAt->isPast()) {
                                $statusLabel = 'Overdue';
                                $statusClass = 'border-red-200 bg-red-50 text-red-700';
                            } elseif ($dueAt->isBefore(now()->addDays(7))) {
                                $statusLabel = 'Due soon';
                                $statusClass = 'border-amber-200 bg-amber-50 text-amber-700';
                            } else {
                                $statusLabel = 'Upcoming';
                                $statusClass = 'border-emerald-200 bg-emerald-50 text-emerald-700';
                            }
                        }

                        $subjectLabel = trim((string) ($assignment->subject?->name ?? 'General assignment'));
                        $classLabel = trim((string) ($assignment->subject?->schoolClass?->display_name ?? ''));
                    @endphp

                    <article id="assignment-{{ $assignment->id }}"
                        class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full border px-2.5 py-1 text-[11px] font-semibold {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                    <span
                                        class="rounded-full border border-indigo-100 bg-indigo-50 px-2.5 py-1 text-[11px] font-semibold text-indigo-700">
                                        {{ $subjectLabel }}
                                    </span>
                                </div>
                                <h2 class="mt-3 text-xl font-black tracking-tight text-slate-900">
                                    {{ $assignment->title }}
                                </h2>
                            </div>

                            <div class="text-right text-xs font-medium text-slate-400">
                                <div>{{ $assignment->created_at?->format('M d, Y') }}</div>
                                <div class="mt-1">{{ $assignment->created_at?->diffForHumans() }}</div>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-3 sm:grid-cols-3">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-3">
                                <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Teacher</div>
                                <div class="mt-1 text-sm font-semibold text-slate-900">
                                    {{ $assignment->teacher?->name ?? 'Teacher' }}
                                </div>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-3">
                                <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Class</div>
                                <div class="mt-1 text-sm font-semibold text-slate-900">
                                    {{ $classLabel !== '' ? $classLabel : 'Shared assignment' }}
                                </div>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-3">
                                <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Due</div>
                                <div class="mt-1 text-sm font-semibold text-slate-900">
                                    {{ $dueAt ? $dueAt->format('M d, Y') : 'No deadline' }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 rounded-2xl border border-slate-200 bg-white px-4 py-4">
                            <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Instructions</div>
                            <div class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">
                                {{ $assignment->description }}
                            </div>
                        </div>
                    </article>
                @endforeach
            </section>

            <div>
                {{ $assignments->links() }}
            </div>
        @else
            <section class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-6 py-14 text-center shadow-sm">
                <div class="text-xl font-black text-slate-900">No assignments yet</div>
                <p class="mt-2 text-sm text-slate-500">
                    When a teacher posts a new assignment for you, it will show up here and an alert notification will be sent.
                </p>
            </section>
        @endif
    </div>
@endsection
