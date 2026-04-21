@extends('layout.students.navbar')

@section('page')
    <div class="space-y-6">
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="max-w-2xl">
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-indigo-500">Assignments</p>
                    <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-900">Your Assignment Board</h1>
                    <p class="mt-3 text-sm leading-6 text-slate-600">
                        Review the work your teachers posted for you, check deadlines, and stay ahead of upcoming tasks.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                    <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-slate-700">
                        Total: {{ number_format($stats['total'] ?? 0) }}
                    </span>
                    <span class="rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-amber-700">
                        Due Soon: {{ number_format($stats['dueSoon'] ?? 0) }}
                    </span>
                    <span class="rounded-full border border-red-200 bg-red-50 px-3 py-1 text-red-700">
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
