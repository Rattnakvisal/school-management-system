@extends('layout.admin.navbar.navbar')

@section('page')
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
        <x-admin.page-header reveal-class="student-reveal" delay="1" icon="flag" title="Mission Events"
            subtitle="Create mission events and send them to staff, teachers, or both.">
            <x-slot:stats>
                <span class="admin-page-stat">Total: {{ $stats['total'] }}</span>
                <span class="admin-page-stat admin-page-stat--emerald">Active: {{ $stats['active'] }}</span>
                <span class="admin-page-stat admin-page-stat--amber">Teachers: {{ $stats['teacher'] }}</span>
                <span class="admin-page-stat">Staff: {{ $stats['staff'] }}</span>
            </x-slot:stats>
        </x-admin.page-header>

        @if (session('success'))
            <div
                class="student-reveal rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"
                style="--sd: 2;">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="student-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
                style="--sd: 2;">
                <div class="font-semibold">Please check the form fields and try again.</div>
            </div>
        @endif

        <section class="grid gap-6 xl:grid-cols-12">
            <form method="POST" action="{{ route('admin.mission.store') }}"
                class="js-create-form student-reveal student-float rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-4"
                style="--sd: 3;">
                @csrf

                <div class="mb-5">
                    <h2 class="text-lg font-black text-slate-900">Create Mission</h2>
                    <p class="mt-1 text-xs text-slate-500">Send a new event alert to the selected audience.</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label for="mission_title" class="mb-1 block text-xs font-semibold text-slate-600">Title</label>
                        <input id="mission_title" name="title" value="{{ old('title') }}" required
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                            placeholder="Mission title">
                        @error('title')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="mission_description"
                            class="mb-1 block text-xs font-semibold text-slate-600">Description</label>
                        <textarea id="mission_description" name="description" rows="5" required
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                            placeholder="What should staff or teachers know?">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="mission_starts_at"
                                class="mb-1 block text-xs font-semibold text-slate-600">Start</label>
                            <input id="mission_starts_at" name="starts_at" type="datetime-local"
                                value="{{ old('starts_at') }}"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            @error('starts_at')
                                <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="mission_ends_at"
                                class="mb-1 block text-xs font-semibold text-slate-600">End</label>
                            <input id="mission_ends_at" name="ends_at" type="datetime-local"
                                value="{{ old('ends_at') }}"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            @error('ends_at')
                                <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="mission_location"
                            class="mb-1 block text-xs font-semibold text-slate-600">Location</label>
                        <input id="mission_location" name="location" value="{{ old('location') }}"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                            placeholder="Optional">
                        @error('location')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="mission_audience"
                                class="mb-1 block text-xs font-semibold text-slate-600">Audience</label>
                            <select id="mission_audience" name="audience"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                @foreach ($audienceLabels as $value => $label)
                                    <option value="{{ $value }}" @selected(old('audience', 'all') === $value)>{{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('audience')
                                <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="mission_priority"
                                class="mb-1 block text-xs font-semibold text-slate-600">Priority</label>
                            <select id="mission_priority" name="priority"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                @foreach (['low', 'normal', 'high', 'urgent'] as $priority)
                                    <option value="{{ $priority }}" @selected(old('priority', 'normal') === $priority)>
                                        {{ ucfirst($priority) }}</option>
                                @endforeach
                            </select>
                            @error('priority')
                                <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <button type="submit"
                        class="inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-3 text-sm font-bold text-white hover:bg-indigo-500">
                        Send Mission Event
                    </button>
                </div>
            </form>

            <section
                class="student-reveal student-float rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-8"
                x-data="{ filterOpen: false }"
                style="--sd: 4;">
                <div class="mb-5 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-black text-slate-900">Mission List</h2>
                        <p class="mt-1 text-xs text-slate-500">Manage active and archived events.</p>
                    </div>
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
                    @click="filterOpen = false"></div>

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
                                    class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:bg-slate-100 hover:text-slate-800">
                                    Clear All
                                </a>
                                <button type="button" @click="filterOpen = false"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-2xl font-bold leading-none text-slate-600 shadow-sm transition hover:bg-slate-100 hover:text-slate-900"
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
                                    <input id="mission_q" name="q" type="text" value="{{ $search ?? '' }}"
                                        placeholder="Search by title, description, or location"
                                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                </section>

                                <section class="space-y-2">
                                    <h4 class="text-xl font-bold text-slate-900">Audience</h4>
                                    <select name="audience"
                                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                        <option value="all" @selected(($audience ?? 'all') === 'all')>All audiences</option>
                                        <option value="teacher" @selected(($audience ?? 'all') === 'teacher')>Teachers</option>
                                        <option value="staff" @selected(($audience ?? 'all') === 'staff')>Staff</option>
                                    </select>
                                </section>

                                <section class="space-y-2">
                                    <h4 class="text-xl font-bold text-slate-900">Priority</h4>
                                    <select name="priority"
                                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                        <option value="all" @selected(($priority ?? 'all') === 'all')>All priorities</option>
                                        @foreach (['low', 'normal', 'high', 'urgent'] as $priorityOption)
                                            <option value="{{ $priorityOption }}" @selected(($priority ?? 'all') === $priorityOption)>
                                                {{ ucfirst($priorityOption) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </section>

                                <section class="space-y-2">
                                    <h4 class="text-xl font-bold text-slate-900">Status</h4>
                                    <select name="status"
                                        class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                        <option value="all" @selected(($status ?? 'all') === 'all')>All</option>
                                        <option value="active" @selected(($status ?? 'all') === 'active')>Active</option>
                                        <option value="inactive" @selected(($status ?? 'all') === 'inactive')>Inactive</option>
                                    </select>
                                </section>
                            </div>

                            <div class="border-t border-slate-200 px-5 py-4">
                                <button type="submit"
                                    class="inline-flex w-full items-center justify-center rounded-2xl bg-slate-950 px-4 py-3 text-base font-bold text-white shadow-sm transition hover:bg-slate-800">
                                    Apply Filters
                                </button>
                            </div>
                        </form>
                    </div>
                </aside>

                <div class="mt-1 overflow-hidden rounded-2xl border border-slate-200">
                    <div class="max-h-[1200px] overflow-auto">
                        <table class="student-table w-full min-w-[1180px] text-left text-sm">
                            <thead
                                class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="whitespace-nowrap px-3 py-3 font-semibold">Mission</th>
                                    <th class="whitespace-nowrap px-3 py-3 font-semibold">Audience</th>
                                    <th class="whitespace-nowrap px-3 py-3 font-semibold">Priority</th>
                                    <th class="whitespace-nowrap px-3 py-3 font-semibold">Start</th>
                                    <th class="whitespace-nowrap px-3 py-3 font-semibold">End</th>
                                    <th class="whitespace-nowrap px-3 py-3 font-semibold">Status</th>
                                    <th class="whitespace-nowrap px-3 py-3 font-semibold">Created By</th>
                                    <th class="whitespace-nowrap px-3 py-3 text-right font-semibold">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse ($missions as $mission)
                                    <tr x-data="{ editOpen: false }" class="align-top hover:bg-slate-50/80">
                                        <td class="whitespace-nowrap px-3 py-3">
                                            <div class="flex max-w-[320px] items-start gap-3">
                                                <span
                                                    class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-indigo-50 text-xs font-black text-indigo-600 ring-1 ring-indigo-100">
                                                    {{ strtoupper(substr($mission->title, 0, 2)) }}
                                                </span>
                                                <div class="min-w-0">
                                                    <div class="truncate font-semibold text-slate-800">
                                                        {{ $mission->title }}
                                                    </div>
                                                    <div class="text-xs text-slate-400">
                                                        ID #{{ str_pad((string) $mission->id, 7, '0', STR_PAD_LEFT) }}
                                                    </div>
                                                    <div class="mt-1 line-clamp-2 text-xs leading-5 text-slate-500">
                                                        {{ \Illuminate\Support\Str::limit($mission->description, 110) }}
                                                    </div>
                                                    @if ($mission->location)
                                                        <div class="mt-1 text-xs font-semibold text-slate-400">
                                                            {{ $mission->location }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-3">
                                            <span
                                                class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                                {{ $audienceLabels[$mission->audience] ?? ucfirst($mission->audience) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3">
                                            <span
                                                class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $priorityClasses[$mission->priority] ?? $priorityClasses['normal'] }}">
                                                {{ ucfirst($mission->priority) }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-3 text-xs font-semibold text-slate-600">
                                            {{ $mission->starts_at?->format('M d, Y h:i A') ?? 'Not scheduled' }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-3 text-xs font-semibold text-slate-600">
                                            {{ $mission->ends_at?->format('M d, Y h:i A') ?? 'Open' }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-3">
                                            @if ($mission->is_active)
                                                <span
                                                    class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>Active
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                                    <span class="h-2 w-2 rounded-full bg-slate-400"></span>Inactive
                                                </span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-3 text-slate-500">
                                            <div class="font-semibold text-slate-700">
                                                {{ $mission->creator?->name ?? 'Admin' }}
                                            </div>
                                            <div class="mt-1 text-xs text-slate-400">
                                                {{ $mission->created_at?->format('M d, Y h:i A') }}
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-3">
                                            <div class="flex flex-wrap items-center justify-end gap-2">
                                                <button type="button" @click="editOpen = true"
                                                    class="rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                                    Edit
                                                </button>
                                                <form method="POST"
                                                    action="{{ route('admin.mission.status', $mission) }}"
                                                    class="js-status-form" data-mission="{{ $mission->title }}"
                                                    data-action="{{ $mission->is_active ? 'deactivate' : 'activate' }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button
                                                        class="whitespace-nowrap rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                                        {{ $mission->is_active ? 'Deactivate' : 'Activate' }}
                                                    </button>
                                                </form>
                                                <form method="POST"
                                                    action="{{ route('admin.mission.destroy', $mission) }}"
                                                    class="js-delete-form" data-mission="{{ $mission->title }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        class="whitespace-nowrap rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">
                                                        Remove
                                                    </button>
                                                </form>
                                            </div>

                                            <div x-show="editOpen" x-cloak x-transition.opacity
                                                class="fixed inset-0 z-[80] bg-slate-900/40" @click="editOpen = false">
                                            </div>
                                            <div x-show="editOpen" x-cloak x-transition
                                                class="fixed inset-x-3 top-6 z-[81] mx-auto max-h-[calc(100vh-3rem)] w-full max-w-2xl overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl sm:top-10">
                                                <form method="POST"
                                                    action="{{ route('admin.mission.update', $mission) }}"
                                                    class="js-edit-form flex max-h-[calc(100vh-3rem)] flex-col"
                                                    data-mission="{{ $mission->title }}">
                                                    @csrf
                                                    @method('PUT')

                                                    <div
                                                        class="flex items-center justify-between gap-4 border-b border-slate-100 px-5 py-4">
                                                        <div>
                                                            <h3 class="text-lg font-black text-slate-900">Edit Mission
                                                            </h3>
                                                            <p class="mt-1 text-xs text-slate-500">Update the mission
                                                                event details.</p>
                                                        </div>
                                                        <button type="button" @click="editOpen = false"
                                                            class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50"
                                                            aria-label="Close edit mission">
                                                            &times;
                                                        </button>
                                                    </div>

                                                    <div class="min-h-0 flex-1 space-y-4 overflow-y-auto px-5 py-4">
                                                        <div>
                                                            <label for="edit_mission_title_{{ $mission->id }}"
                                                                class="mb-1 block text-xs font-semibold text-slate-600">Title</label>
                                                            <input id="edit_mission_title_{{ $mission->id }}"
                                                                name="title" value="{{ old('title', $mission->title) }}"
                                                                required
                                                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                        </div>

                                                        <div>
                                                            <label for="edit_mission_description_{{ $mission->id }}"
                                                                class="mb-1 block text-xs font-semibold text-slate-600">Description</label>
                                                            <textarea id="edit_mission_description_{{ $mission->id }}" name="description" rows="5" required
                                                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">{{ old('description', $mission->description) }}</textarea>
                                                        </div>

                                                        <div class="grid gap-4 sm:grid-cols-2">
                                                            <div>
                                                                <label for="edit_mission_starts_at_{{ $mission->id }}"
                                                                    class="mb-1 block text-xs font-semibold text-slate-600">Start</label>
                                                                <input id="edit_mission_starts_at_{{ $mission->id }}"
                                                                    name="starts_at" type="datetime-local"
                                                                    value="{{ old('starts_at', $mission->starts_at?->format('Y-m-d\TH:i')) }}"
                                                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                            </div>

                                                            <div>
                                                                <label for="edit_mission_ends_at_{{ $mission->id }}"
                                                                    class="mb-1 block text-xs font-semibold text-slate-600">End</label>
                                                                <input id="edit_mission_ends_at_{{ $mission->id }}"
                                                                    name="ends_at" type="datetime-local"
                                                                    value="{{ old('ends_at', $mission->ends_at?->format('Y-m-d\TH:i')) }}"
                                                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                            </div>
                                                        </div>

                                                        <div>
                                                            <label for="edit_mission_location_{{ $mission->id }}"
                                                                class="mb-1 block text-xs font-semibold text-slate-600">Location</label>
                                                            <input id="edit_mission_location_{{ $mission->id }}"
                                                                name="location"
                                                                value="{{ old('location', $mission->location) }}"
                                                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                        </div>

                                                        <div class="grid gap-4 sm:grid-cols-2">
                                                            <div>
                                                                <label for="edit_mission_audience_{{ $mission->id }}"
                                                                    class="mb-1 block text-xs font-semibold text-slate-600">Audience</label>
                                                                <select id="edit_mission_audience_{{ $mission->id }}"
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
                                                                <label for="edit_mission_priority_{{ $mission->id }}"
                                                                    class="mb-1 block text-xs font-semibold text-slate-600">Priority</label>
                                                                <select id="edit_mission_priority_{{ $mission->id }}"
                                                                    name="priority"
                                                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                    @foreach (['low', 'normal', 'high', 'urgent'] as $priority)
                                                                        <option value="{{ $priority }}"
                                                                            @selected(old('priority', $mission->priority) === $priority)>
                                                                            {{ ucfirst($priority) }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="flex justify-end gap-2 border-t border-slate-100 px-5 py-4">
                                                        <button type="button" @click="editOpen = false"
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
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-3 py-10 text-center text-sm text-slate-500">
                                            No mission events yet.
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
            </section>
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
    <script id="admin-mission-data" type="application/json">{!! json_encode($missionPageData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
    @vite(['resources/js/admin/mission.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
