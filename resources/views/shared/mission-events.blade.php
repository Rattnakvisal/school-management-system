@php
    $audienceLabels = ['all' => 'Staff and Teachers', 'teacher' => 'Teachers', 'staff' => 'Staff'];
    $priorityClasses = [
        'low' => 'bg-slate-50 text-slate-600 ring-slate-200',
        'normal' => 'bg-indigo-50 text-indigo-700 ring-indigo-100',
        'high' => 'bg-amber-50 text-amber-700 ring-amber-100',
        'urgent' => 'bg-red-50 text-red-700 ring-red-100',
    ];
@endphp

<div class="student-stage space-y-6">
    <section class="student-reveal admin-page-header admin-page-header--iconic overflow-hidden" style="--sd: 1;">
        <div class="admin-page-header__main flex flex-wrap items-start justify-between gap-5">
            <div class="admin-page-header__intro min-w-0 flex-1">
                <div class="admin-page-header__title-row flex items-center gap-3">
                    <span class="admin-page-header__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 4v16" />
                            <path d="M7 5h9.5l-1 2 1 2H7" />
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <div class="admin-page-header__eyebrow">Mission Center</div>
                        <h1 class="admin-page-title text-2xl font-black tracking-tight sm:text-3xl">{{ $title }}</h1>
                    </div>
                </div>

                <p class="admin-page-subtitle mt-2 text-sm">{{ $subtitle }}</p>
            </div>

            <div class="admin-page-header__stats flex flex-wrap items-center gap-2 text-xs font-semibold sm:gap-3">
                <span class="admin-page-stat admin-page-stat--emerald">Active: {{ $missions->total() }}</span>
            </div>
        </div>
    </section>

    <section class="student-reveal student-float rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200"
        style="--sd: 2;">
        <div class="mb-5 flex items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-black text-slate-900">Mission List</h2>
                <p class="mt-1 text-xs text-slate-500">Review active mission events from admin.</p>
            </div>
        </div>

        <div class="grid gap-3 lg:grid-cols-2">
        @forelse ($missions as $mission)
            <article class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                <div class="mb-4 flex flex-wrap items-center gap-2">
                    <span class="rounded-full px-2.5 py-1 text-xs font-bold ring-1 {{ $priorityClasses[$mission->priority] ?? $priorityClasses['normal'] }}">
                        {{ ucfirst($mission->priority) }}
                    </span>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">
                        {{ $audienceLabels[$mission->audience] ?? ucfirst($mission->audience) }}
                    </span>
                </div>

                <h3 class="text-base font-black text-slate-900">{{ $mission->title }}</h3>
                <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">{{ $mission->description }}</p>

                <div class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                    <div class="rounded-2xl bg-white p-4 ring-1 ring-slate-100">
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-400">Starts</div>
                        <div class="mt-1 font-bold text-slate-800">
                            {{ $mission->starts_at?->format('M d, Y h:i A') ?? 'Not scheduled' }}
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white p-4 ring-1 ring-slate-100">
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-400">Ends</div>
                        <div class="mt-1 font-bold text-slate-800">
                            {{ $mission->ends_at?->format('M d, Y h:i A') ?? 'Open' }}
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-center justify-between gap-3 text-xs font-semibold text-slate-500">
                    <span>{{ $mission->location ?: 'No location set' }}</span>
                    <span>Posted {{ $mission->created_at?->diffForHumans() }}</span>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center lg:col-span-2">
                <h2 class="text-lg font-black text-slate-900">No mission events yet</h2>
                <p class="mt-2 text-sm text-slate-500">New mission events from admin will appear here.</p>
                <a href="{{ $emptyRoute }}"
                    class="mt-5 inline-flex rounded-xl bg-slate-950 px-4 py-2 text-sm font-bold text-white hover:bg-slate-800">
                    Back to dashboard
                </a>
            </div>
        @endforelse
        </div>

        <div class="mt-5">
            {{ $missions->links() }}
        </div>
    </section>
</div>
