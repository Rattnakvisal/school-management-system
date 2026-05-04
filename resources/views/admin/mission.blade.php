@extends('layout.admin.navbar.navbar')

@section('page')
    @php
        use Illuminate\Support\Carbon;
        use Illuminate\Support\Facades\Storage;
        use Illuminate\Support\Str;

        $showCreateFormOnLoad = $errors->any() && old('_form') === 'create_mission';

        $audienceLabels = [
            'all' => 'Staff and Teachers',
            'teacher' => 'Teachers',
            'staff' => 'Staff',
        ];

        $priorityClasses = [
            'low' => 'bg-slate-50 text-slate-600 ring-slate-200',
            'normal' => 'bg-indigo-50 text-indigo-700 ring-indigo-100',
            'high' => 'bg-amber-50 text-amber-700 ring-amber-100',
            'urgent' => 'bg-red-50 text-red-700 ring-red-100',
        ];

        $priorityOptions = ['low', 'normal', 'high', 'urgent'];
        $missionTotal = max(0, (int) ($stats['total'] ?? 0));

        $missionStatCards = [
            [
                'label' => 'Missions',
                'activeLabel' => 'Total',
                'active' => $missionTotal,
                'total' => max(1, $missionTotal),
                'icon' => 'mission',
                'tone' => 'from-indigo-100 to-white text-indigo-600',
            ],
            [
                'label' => 'Active',
                'activeLabel' => 'Active',
                'active' => (int) ($stats['active'] ?? 0),
                'total' => max(1, $missionTotal),
                'icon' => 'active',
                'tone' => 'from-emerald-100 to-white text-emerald-600',
            ],
            [
                'label' => 'Teachers',
                'activeLabel' => 'Audience',
                'active' => (int) ($stats['teacher'] ?? 0),
                'total' => max(1, $missionTotal),
                'icon' => 'teachers',
                'tone' => 'from-amber-100 to-white text-amber-600',
            ],
            [
                'label' => 'Staff',
                'activeLabel' => 'Audience',
                'active' => (int) ($stats['staff'] ?? 0),
                'total' => max(1, $missionTotal),
                'icon' => 'staff',
                'tone' => 'from-sky-100 to-white text-sky-600',
            ],
            [
                'label' => 'Submitted',
                'activeLabel' => 'Files',
                'active' => (int) ($stats['submissions'] ?? 0),
                'total' => max(1, (int) ($stats['submissions'] ?? 0)),
                'icon' => 'active',
                'tone' => 'from-violet-100 to-white text-violet-600',
            ],
        ];

        $currentSearch = $search ?? request('q', '');
        $currentAudience = $audience ?? request('audience', 'all');
        $currentPriority = $priority ?? request('priority', 'all');
        $currentStatus = $status ?? request('status', 'all');
    @endphp

    <div class="student-stage space-y-6 overflow-x-hidden" x-data="{ createOpen: false, filterOpen: false }">
        <x-admin.page-header reveal-class="student-reveal" delay="1" icon="flag" title="Mission Events"
            subtitle="Create mission events and send them to staff, teachers, or both." />

        <x-admin.stat-cards :cards="$missionStatCards" reveal-class="student-reveal" float-class="student-float"
            grid-class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-5" />

        @if (session('success'))
            <div class="student-reveal rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"
                style="--sd: 2;">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="student-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700"
                style="--sd: 2;">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="student-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
                style="--sd: 2;">
                <div class="font-bold">Please check the form fields and try again.</div>

                <ul class="mt-2 list-inside list-disc space-y-1 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid gap-6 xl:grid-cols-12">
            {{-- Create Mission --}}
            <section x-data="{
                createOpen: @js($showCreateFormOnLoad),
                isDesktop: false,
                init() {
                    const media = window.matchMedia('(min-width: 1280px)');

                    const update = () => {
                        this.isDesktop = media.matches;

                        if (this.isDesktop) {
                            this.createOpen = true;
                        } else if (!@js($showCreateFormOnLoad)) {
                            this.createOpen = false;
                        }
                    };

                    update();

                    if (typeof media.addEventListener === 'function') {
                        media.addEventListener('change', update);
                    } else if (typeof media.addListener === 'function') {
                        media.addListener(update);
                    }
                }
            }" x-init="init()"
                class="student-reveal student-float rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-4"
                style="--sd: 3;">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-black text-slate-900">Create Mission</h2>
                        <p class="mt-1 text-xs text-slate-500">
                            New mission event will be sent to selected audience.
                        </p>
                    </div>

                    <button type="button" @click="createOpen = !createOpen"
                        :aria-expanded="(createOpen || isDesktop).toString()" aria-controls="create-mission-form-panel"
                        class="inline-flex items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 shadow-sm hover:bg-indigo-100 xl:hidden">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 5v14M5 12h14" x-show="!(createOpen || isDesktop)"></path>
                            <path d="M5 12h14" x-show="createOpen || isDesktop"></path>
                        </svg>

                        <span x-show="!(createOpen || isDesktop)">Create Mission</span>
                        <span x-show="createOpen || isDesktop">Hide Form</span>
                    </button>
                </div>

                <form id="create-mission-form-panel" method="POST" action="{{ route('admin.mission.store') }}"
                    class="js-create-form mt-5 space-y-4" x-show="createOpen || isDesktop" x-cloak
                    x-transition.opacity.duration.150ms>
                    @csrf
                    <input type="hidden" name="_form" value="create_mission">

                    <div>
                        <label for="mission_title" class="mb-1 block text-xs font-semibold text-slate-600">
                            Title
                        </label>

                        <input id="mission_title" name="title" type="text" value="{{ old('title') }}" required
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                            placeholder="mission title">

                        @error('title')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="mission_description" class="mb-1 block text-xs font-semibold text-slate-600">
                            Description
                        </label>

                        <textarea id="mission_description" name="description" rows="5" required
                            class="w-full resize-none rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                            placeholder="What should staff or teachers know?">{{ old('description') }}</textarea>

                        @error('description')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="mission_starts_at" class="mb-1 block text-xs font-semibold text-slate-600">
                                Start
                            </label>

                            <input id="mission_starts_at" name="starts_at" type="datetime-local"
                                value="{{ old('starts_at') }}"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">

                            @error('starts_at')
                                <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="mission_ends_at" class="mb-1 block text-xs font-semibold text-slate-600">
                                End
                            </label>

                            <input id="mission_ends_at" name="ends_at" type="datetime-local" value="{{ old('ends_at') }}"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">

                            @error('ends_at')
                                <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="mission_audience" class="mb-1 block text-xs font-semibold text-slate-600">
                            Audience
                        </label>

                        <select id="mission_audience" name="audience"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            @foreach ($audienceLabels as $value => $label)
                                <option value="{{ $value }}" @selected(old('audience', 'all') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>

                        @error('audience')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="mission_priority" class="mb-1 block text-xs font-semibold text-slate-600">
                            Priority
                        </label>

                        <select id="mission_priority" name="priority"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            @foreach ($priorityOptions as $priorityOption)
                                <option value="{{ $priorityOption }}" @selected(old('priority', 'normal') === $priorityOption)>
                                    {{ ucfirst($priorityOption) }}
                                </option>
                            @endforeach
                        </select>

                        @error('priority')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">
                        Create Mission
                    </button>
                </form>
            </section>

            {{-- Mission List --}}
            <section
                class="student-reveal student-float rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-8"
                style="--sd: 4;">
                <div x-data="{ filterOpen: false }" @open-filter-panel.window="filterOpen = true" class="space-y-4">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-lg font-black text-slate-900">Mission List</h2>
                        <button type="button" @click="filterOpen = true"
                            class="inline-flex min-w-[150px] items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:bg-slate-50">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M3 5h18l-7 8v5l-4 2v-7L3 5z"></path>
                            </svg>
                            Filters
                        </button>
                    </div>

                    <div x-show="filterOpen" x-cloak x-transition.opacity class="fixed inset-0 z-[80] bg-slate-900/40"
                        @click="filterOpen = false">
                    </div>

                    <div class="grid gap-4">
                        <aside x-show="filterOpen" x-cloak x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0"
                            x-transition:leave-end="translate-x-full"
                            class="fixed inset-y-0 right-0 z-[81] w-full max-w-md transform border-l border-slate-200 bg-white shadow-2xl">

                            <div class="flex h-full flex-col">
                                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                                    <h3 class="text-3xl font-black text-slate-900">Filters</h3>

                                    <div class="flex items-center gap-4">
                                        <a href="{{ route('admin.mission.index') }}"
                                            class="text-sm font-semibold text-slate-500 hover:text-slate-700">
                                            Clear All
                                        </a>

                                        <button type="button" @click="filterOpen = false"
                                            class="text-2xl font-bold leading-none text-slate-700 hover:text-slate-900"
                                            aria-label="Close filters">
                                            &times;
                                        </button>
                                    </div>
                                </div>

                                <form method="GET" action="{{ route('admin.mission.index') }}"
                                    class="flex min-h-0 flex-1 flex-col" @submit="filterOpen = false">

                                    <div class="flex-1 space-y-5 overflow-y-auto px-5 py-4">
                                        <section class="space-y-2">
                                            <h4 class="text-xl font-bold text-slate-900">Search</h4>

                                            <input id="mission_q" name="q" type="text"
                                                value="{{ $currentSearch }}"
                                                placeholder="Search mission title, description, audience, or priority"
                                                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                        </section>

                                        <section class="space-y-2">
                                            <h4 class="text-xl font-bold text-slate-900">Audience</h4>

                                            <select name="audience"
                                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                <option value="all" @selected($currentAudience === 'all')>
                                                    All audiences
                                                </option>

                                                <option value="teacher" @selected($currentAudience === 'teacher')>
                                                    Teachers
                                                </option>

                                                <option value="staff" @selected($currentAudience === 'staff')>
                                                    Staff
                                                </option>
                                            </select>
                                        </section>

                                        <section class="space-y-2">
                                            <h4 class="text-xl font-bold text-slate-900">Priority</h4>

                                            <select name="priority"
                                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                <option value="all" @selected($currentPriority === 'all')>
                                                    All priorities
                                                </option>

                                                @foreach ($priorityOptions as $priorityOption)
                                                    <option value="{{ $priorityOption }}" @selected($currentPriority === $priorityOption)>
                                                        {{ ucfirst($priorityOption) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </section>

                                        <section class="space-y-2">
                                            <h4 class="text-xl font-bold text-slate-900">Status</h4>

                                            <select name="status"
                                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                <option value="all" @selected($currentStatus === 'all')>
                                                    All
                                                </option>

                                                <option value="active" @selected($currentStatus === 'active')>
                                                    Active
                                                </option>

                                                <option value="inactive" @selected($currentStatus === 'inactive')>
                                                    Inactive
                                                </option>
                                            </select>
                                        </section>
                                    </div>

                                    <div class="border-t border-slate-200 px-5 py-4">
                                        <button type="submit"
                                            class="inline-flex w-full items-center justify-center rounded-lg bg-slate-950 px-4 py-3 text-lg font-bold text-white transition hover:bg-slate-800">
                                            Apply Filters
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </aside>


                        <div class="min-w-0">
                            <div class="mt-1 overflow-hidden rounded-2xl border border-slate-200">
                                <div class="student-table-scroller max-h-[720px] overflow-auto">
                                    <table class="admin-table w-full min-w-[1280px] whitespace-nowrap text-left text-sm">
                                        <thead
                                            class="admin-table-head sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                            <tr>
                                                <th class="mission-col-mission px-3 py-3 font-semibold">Mission</th>
                                                <th class="mission-col-audience px-3 py-3 font-semibold">Audience</th>
                                                <th class="mission-col-priority px-3 py-3 font-semibold">Priority</th>
                                                <th class="mission-col-start whitespace-nowrap px-3 py-3 font-semibold">
                                                    Start</th>
                                                <th class="mission-col-end whitespace-nowrap px-3 py-3 font-semibold">End
                                                </th>
                                                <th class="mission-col-status px-3 py-3 font-semibold">Status</th>
                                                <th
                                                    class="mission-col-submissions whitespace-nowrap px-3 py-3 font-semibold">
                                                    Submissions</th>
                                                <th class="mission-col-created whitespace-nowrap px-3 py-3 font-semibold">
                                                    Created By</th>
                                                <th class="mission-col-actions px-3 py-3 font-semibold text-right">Actions
                                                </th>
                                            </tr>
                                        </thead>

                                        <tbody class="divide-y divide-slate-100 bg-white">
                                            @forelse ($missions as $mission)
                                                @php
                                                    $submissionDetails = collect($mission->submission_details ?? []);
                                                    $missionTitle = $mission->title ?: 'Untitled Mission';
                                                    $missionInitial = strtoupper(Str::substr($missionTitle, 0, 2));
                                                @endphp

                                                <tr class="align-top hover:bg-slate-50/80" x-data="{ open: false, detailOpen: false }">
                                                    <td class="mission-col-mission px-3 py-3 align-top">
                                                        <div class="flex items-start gap-3">
                                                            <span
                                                                class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-indigo-50 text-xs font-black text-indigo-600 ring-1 ring-indigo-100">
                                                                {{ $missionInitial }}
                                                            </span>

                                                            <div class="min-w-0">
                                                                <div
                                                                    class="max-w-[250px] truncate font-semibold text-slate-800">
                                                                    {{ $missionTitle }}
                                                                </div>

                                                                <div class="text-xs text-slate-400">
                                                                    ID
                                                                    #{{ str_pad((string) $mission->id, 7, '0', STR_PAD_LEFT) }}
                                                                </div>

                                                                <div
                                                                    class="mt-1 max-w-[260px] truncate text-xs text-slate-500">
                                                                    {{ Str::limit($mission->description ?? 'No description', 80) }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td
                                                        class="mission-col-audience whitespace-nowrap px-3 py-3 text-slate-600">
                                                        <span
                                                            class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                                            {{ $audienceLabels[$mission->audience] ?? ucfirst($mission->audience ?? 'all') }}
                                                        </span>
                                                    </td>

                                                    <td
                                                        class="mission-col-priority whitespace-nowrap px-3 py-3 text-slate-600">
                                                        <span
                                                            class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $priorityClasses[$mission->priority] ?? $priorityClasses['normal'] }}">
                                                            {{ ucfirst($mission->priority ?? 'normal') }}
                                                        </span>
                                                    </td>

                                                    <td
                                                        class="mission-col-start whitespace-nowrap px-3 py-3 text-slate-500">
                                                        {{ $mission->starts_at?->format('M d, Y h:i A') ?? 'Not scheduled' }}
                                                    </td>

                                                    <td class="mission-col-end whitespace-nowrap px-3 py-3 text-slate-500">
                                                        {{ $mission->ends_at?->format('M d, Y h:i A') ?? 'Open' }}
                                                    </td>

                                                    <td class="mission-col-status px-3 py-3">
                                                        @if ($mission->is_active)
                                                            <span
                                                                class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                                                <span
                                                                    class="status-dot h-2 w-2 rounded-full bg-emerald-500"></span>
                                                                Active
                                                            </span>
                                                        @else
                                                            <span
                                                                class="inline-flex items-center gap-2 rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700">
                                                                <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                                                                Inactive
                                                            </span>
                                                        @endif
                                                    </td>

                                                    <td class="mission-col-submissions whitespace-nowrap px-3 py-3">
                                                        <button type="button" @click="detailOpen = true"
                                                            class="inline-flex items-center gap-2 rounded-lg border border-emerald-100 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-100">
                                                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                                            {{ number_format($submissionDetails->count()) }} submitted
                                                        </button>
                                                    </td>

                                                    <td
                                                        class="mission-col-created whitespace-nowrap px-3 py-3 text-slate-500">
                                                        <div class="font-semibold text-slate-700">
                                                            {{ $mission->creator?->name ?? 'Admin' }}
                                                        </div>

                                                        <div class="mt-1 text-xs text-slate-400">
                                                            {{ $mission->created_at?->format('M d, Y') ?? '-' }}
                                                        </div>
                                                    </td>

                                                    <td class="mission-col-actions whitespace-nowrap px-3 py-3">
                                                        <div
                                                            class="flex flex-nowrap items-center justify-end gap-2 whitespace-nowrap">
                                                            <button @click="open = true" type="button"
                                                                class="whitespace-nowrap rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                                                Edit
                                                            </button>

                                                            <form method="POST"
                                                                action="{{ route('admin.mission.status', $mission) }}"
                                                                class="js-status-form" data-mission="{{ $missionTitle }}"
                                                                data-action="{{ $mission->is_active ? 'set inactive' : 'set active' }}">
                                                                @csrf
                                                                @method('PATCH')

                                                                <button type="submit"
                                                                    class="whitespace-nowrap rounded-lg border px-3 py-1.5 text-xs font-semibold {{ $mission->is_active ? 'border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100' : 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                                                    {{ $mission->is_active ? 'Set Inactive' : 'Set Active' }}
                                                                </button>
                                                            </form>

                                                            <form method="POST"
                                                                action="{{ route('admin.mission.destroy', $mission) }}"
                                                                class="js-delete-form"
                                                                data-mission="{{ $missionTitle }}">
                                                                @csrf
                                                                @method('DELETE')

                                                                <button type="submit"
                                                                    class="whitespace-nowrap rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">
                                                                    Delete
                                                                </button>
                                                            </form>
                                                        </div>

                                                        {{-- Edit Mission Modal --}}
                                                        <div x-show="open" x-cloak
                                                            class="fixed inset-0 z-[70] grid place-items-center p-4"
                                                            aria-modal="true" role="dialog">
                                                            <div class="absolute inset-0 bg-slate-900/50"
                                                                @click="open = false"></div>

                                                            <div
                                                                class="relative z-10 w-full max-w-xl rounded-3xl bg-white p-5 shadow-2xl">
                                                                <div class="mb-4 flex items-center justify-between">
                                                                    <h3 class="text-lg font-black text-slate-900">
                                                                        Edit Mission
                                                                    </h3>

                                                                    <button type="button" @click="open = false"
                                                                        class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                                                                        <svg class="h-5 w-5" viewBox="0 0 24 24"
                                                                            fill="currentColor">
                                                                            <path
                                                                                d="M18.3 5.71 12 12l6.3 6.29-1.41 1.42L10.59 13.4 4.3 19.7 2.89 18.3 9.17 12 2.9 5.71 4.3 4.29l6.29 6.3 6.3-6.3 1.41 1.42Z" />
                                                                        </svg>
                                                                    </button>
                                                                </div>

                                                                <form method="POST"
                                                                    action="{{ route('admin.mission.update', $mission) }}"
                                                                    class="js-edit-form space-y-4"
                                                                    data-mission="{{ $missionTitle }}">
                                                                    @csrf
                                                                    @method('PUT')

                                                                    <div>
                                                                        <label
                                                                            for="edit_mission_title_{{ $mission->id }}"
                                                                            class="mb-1 block text-xs font-semibold text-slate-600">
                                                                            Title
                                                                        </label>

                                                                        <input id="edit_mission_title_{{ $mission->id }}"
                                                                            name="title" type="text"
                                                                            value="{{ old('title', $mission->title) }}"
                                                                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                    </div>

                                                                    <div>
                                                                        <label
                                                                            for="edit_mission_description_{{ $mission->id }}"
                                                                            class="mb-1 block text-xs font-semibold text-slate-600">
                                                                            Description
                                                                        </label>

                                                                        <textarea id="edit_mission_description_{{ $mission->id }}" name="description" rows="4"
                                                                            class="w-full resize-none rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">{{ old('description', $mission->description) }}</textarea>
                                                                    </div>

                                                                    <div class="grid gap-4 sm:grid-cols-2">
                                                                        <div>
                                                                            <label
                                                                                for="edit_mission_starts_at_{{ $mission->id }}"
                                                                                class="mb-1 block text-xs font-semibold text-slate-600">
                                                                                Start
                                                                            </label>

                                                                            <input
                                                                                id="edit_mission_starts_at_{{ $mission->id }}"
                                                                                name="starts_at" type="datetime-local"
                                                                                value="{{ old('starts_at', $mission->starts_at?->format('Y-m-d\TH:i')) }}"
                                                                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                        </div>

                                                                        <div>
                                                                            <label
                                                                                for="edit_mission_ends_at_{{ $mission->id }}"
                                                                                class="mb-1 block text-xs font-semibold text-slate-600">
                                                                                End
                                                                            </label>

                                                                            <input
                                                                                id="edit_mission_ends_at_{{ $mission->id }}"
                                                                                name="ends_at" type="datetime-local"
                                                                                value="{{ old('ends_at', $mission->ends_at?->format('Y-m-d\TH:i')) }}"
                                                                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                        </div>
                                                                    </div>

                                                                    <div class="grid gap-4 sm:grid-cols-2">
                                                                        <div>
                                                                            <label
                                                                                for="edit_mission_audience_{{ $mission->id }}"
                                                                                class="mb-1 block text-xs font-semibold text-slate-600">
                                                                                Audience
                                                                            </label>

                                                                            <select
                                                                                id="edit_mission_audience_{{ $mission->id }}"
                                                                                name="audience"
                                                                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                                @foreach ($audienceLabels as $value => $label)
                                                                                    <option value="{{ $value }}"
                                                                                        @selected(old('audience', $mission->audience) === $value)>
                                                                                        {{ $label }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        <div>
                                                                            <label
                                                                                for="edit_mission_priority_{{ $mission->id }}"
                                                                                class="mb-1 block text-xs font-semibold text-slate-600">
                                                                                Priority
                                                                            </label>

                                                                            <select
                                                                                id="edit_mission_priority_{{ $mission->id }}"
                                                                                name="priority"
                                                                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                                @foreach ($priorityOptions as $priorityOption)
                                                                                    <option value="{{ $priorityOption }}"
                                                                                        @selected(old('priority', $mission->priority) === $priorityOption)>
                                                                                        {{ ucfirst($priorityOption) }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    <div class="flex justify-end gap-2 pt-2">
                                                                        <button type="button" @click="open = false"
                                                                            class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                                                            Cancel
                                                                        </button>

                                                                        <button type="submit"
                                                                            class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                                                                            Save Changes
                                                                        </button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>

                                                        {{-- Submission Modal --}}
                                                        <div x-show="detailOpen" x-cloak
                                                            class="fixed inset-0 z-[70] grid place-items-center p-4"
                                                            aria-modal="true" role="dialog">
                                                            <div class="absolute inset-0 bg-slate-900/50"
                                                                @click="detailOpen = false"></div>

                                                            <div
                                                                class="relative z-10 w-full max-w-2xl rounded-3xl bg-white p-5 shadow-2xl">
                                                                <div class="mb-4 flex items-center justify-between">
                                                                    <div>
                                                                        <h3 class="text-lg font-black text-slate-900">
                                                                            Submission Details
                                                                        </h3>

                                                                        <p class="mt-1 text-xs text-slate-500">
                                                                            {{ $missionTitle }} -
                                                                            {{ number_format($submissionDetails->count()) }}
                                                                            file{{ $submissionDetails->count() === 1 ? '' : 's' }}
                                                                            received.
                                                                        </p>
                                                                    </div>

                                                                    <button type="button" @click="detailOpen = false"
                                                                        class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                                                                        <svg class="h-5 w-5" viewBox="0 0 24 24"
                                                                            fill="currentColor">
                                                                            <path
                                                                                d="M18.3 5.71 12 12l6.3 6.29-1.41 1.42L10.59 13.4 4.3 19.7 2.89 18.3 9.17 12 2.9 5.71 4.3 4.29l6.29 6.3 6.3-6.3 1.41 1.42Z" />
                                                                        </svg>
                                                                    </button>
                                                                </div>

                                                                <div class="max-h-[65vh] overflow-y-auto">
                                                                    @if ($submissionDetails->isNotEmpty())
                                                                        <div class="space-y-3">
                                                                            @foreach ($submissionDetails as $submission)
                                                                                @php
                                                                                    $fileSize =
                                                                                        (int) ($submission->submission_file_size ??
                                                                                            0);
                                                                                    $fileSizeLabel = 'Unknown size';

                                                                                    if ($fileSize > 0) {
                                                                                        $units = [
                                                                                            'B',
                                                                                            'KB',
                                                                                            'MB',
                                                                                            'GB',
                                                                                        ];
                                                                                        $unitIndex = 0;
                                                                                        $displaySize = (float) $fileSize;

                                                                                        while (
                                                                                            $displaySize >= 1024 &&
                                                                                            $unitIndex <
                                                                                                count($units) - 1
                                                                                        ) {
                                                                                            $displaySize /= 1024;
                                                                                            $unitIndex++;
                                                                                        }

                                                                                        $fileSizeLabel =
                                                                                            rtrim(
                                                                                                rtrim(
                                                                                                    number_format(
                                                                                                        $displaySize,
                                                                                                        1,
                                                                                                    ),
                                                                                                    '0',
                                                                                                ),
                                                                                                '.',
                                                                                            ) .
                                                                                            ' ' .
                                                                                            $units[$unitIndex];
                                                                                    }

                                                                                    $filePath =
                                                                                        $submission->submission_file_path ??
                                                                                        null;
                                                                                    $fileUrl = $filePath
                                                                                        ? Storage::url($filePath)
                                                                                        : null;
                                                                                @endphp

                                                                                <article
                                                                                    class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                                                                                    <div
                                                                                        class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                                                                        <div class="min-w-0">
                                                                                            <div
                                                                                                class="flex flex-wrap items-center gap-2">
                                                                                                <span
                                                                                                    class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700 ring-1 ring-indigo-100">
                                                                                                    {{ ucfirst($submission->submitter_role ?? 'User') }}
                                                                                                </span>

                                                                                                <span
                                                                                                    class="text-sm font-black text-slate-900">
                                                                                                    {{ $submission->submitter_name ?? 'Unknown user' }}
                                                                                                </span>
                                                                                            </div>

                                                                                            <div
                                                                                                class="mt-1 text-xs text-slate-400">
                                                                                                {{ $submission->submitter_email ?? 'No email' }}
                                                                                            </div>

                                                                                            <div class="mt-3">
                                                                                                @if ($fileUrl)
                                                                                                    <a href="{{ $fileUrl }}"
                                                                                                        target="_blank"
                                                                                                        rel="noopener noreferrer"
                                                                                                        class="inline-flex max-w-full items-center gap-2 rounded-xl border border-emerald-100 bg-white px-3 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-50">
                                                                                                        <svg class="h-4 w-4 shrink-0"
                                                                                                            viewBox="0 0 24 24"
                                                                                                            fill="none"
                                                                                                            stroke="currentColor"
                                                                                                            stroke-width="2">
                                                                                                            <path
                                                                                                                d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" />
                                                                                                            <path
                                                                                                                d="M14 2v6h6" />
                                                                                                        </svg>

                                                                                                        <span
                                                                                                            class="truncate">
                                                                                                            {{ $submission->submission_file_name ?: 'Submitted file' }}
                                                                                                        </span>
                                                                                                    </a>
                                                                                                @else
                                                                                                    <span
                                                                                                        class="inline-flex rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-500">
                                                                                                        File path not
                                                                                                        available
                                                                                                    </span>
                                                                                                @endif
                                                                                            </div>
                                                                                        </div>

                                                                                        <div
                                                                                            class="shrink-0 rounded-2xl bg-white px-3 py-2 text-xs font-semibold text-slate-500 ring-1 ring-slate-100">
                                                                                            <div>{{ $fileSizeLabel }}</div>

                                                                                            <div class="mt-1">
                                                                                                {{ $submission->submitted_at ? Carbon::parse($submission->submitted_at)->format('M d, Y h:i A') : 'No date' }}
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </article>
                                                                            @endforeach
                                                                        </div>
                                                                    @else
                                                                        <div
                                                                            class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center">
                                                                            <h4
                                                                                class="text-base font-black text-slate-900">
                                                                                No submitted files yet
                                                                            </h4>

                                                                            <p class="mt-2 text-sm text-slate-500">
                                                                                Teacher and staff files will appear here
                                                                                after they submit the mission.
                                                                            </p>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="9"
                                                        class="px-3 py-10 text-center text-sm text-slate-500">
                                                        No mission events found.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="mt-5">
                                {{ $missions->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        @php
            $missionPageData = [
                'validationErrors' => $errors->all(),
                'flash' => [
                    'success' => session('success'),
                    'error' => session('error'),
                    'warning' => session('warning'),
                ],
            ];
        @endphp

        <script id="admin-mission-data" type="application/json">
        {!! json_encode($missionPageData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}
    </script>

        @vite(['resources/js/admin/mission.js'])
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endsection
