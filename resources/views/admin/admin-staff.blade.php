@extends('layout.admin.navbar')

@section('page')
    @php
        $accountTotal = max(0, (int) ($stats['total'] ?? 0));

        $adminStaffStatCards = [
            [
                'label' => 'Accounts',
                'activeLabel' => 'Total',
                'active' => $accountTotal,
                'total' => $accountTotal,
                'icon' => 'staff',
                'tone' =>
                    'from-indigo-100 to-white text-indigo-600 dark:from-indigo-500/20 dark:to-slate-900 dark:text-indigo-300',
            ],
            [
                'label' => 'Admins',
                'activeLabel' => 'Admins',
                'active' => (int) ($stats['admins'] ?? 0),
                'total' => $accountTotal,
                'icon' => 'staff',
                'tone' => 'from-sky-100 to-white text-sky-600 dark:from-sky-500/20 dark:to-slate-900 dark:text-sky-300',
            ],
            [
                'label' => 'Staff',
                'activeLabel' => 'Staff',
                'active' => (int) ($stats['staff'] ?? 0),
                'total' => $accountTotal,
                'icon' => 'teachers',
                'tone' =>
                    'from-amber-100 to-white text-amber-600 dark:from-amber-500/20 dark:to-slate-900 dark:text-amber-300',
            ],
            [
                'label' => 'Active',
                'activeLabel' => 'Active',
                'active' => (int) ($stats['active'] ?? 0),
                'total' => $accountTotal,
                'icon' => 'active',
                'tone' =>
                    'from-emerald-100 to-white text-emerald-600 dark:from-emerald-500/20 dark:to-slate-900 dark:text-emerald-300',
            ],
            [
                'label' => 'Inactive',
                'activeLabel' => 'Inactive',
                'active' => (int) ($stats['inactive'] ?? 0),
                'total' => $accountTotal,
                'icon' => 'inactive',
                'tone' =>
                    'from-rose-100 to-white text-rose-600 dark:from-rose-500/20 dark:to-slate-900 dark:text-rose-300',
            ],
        ];

        $panelClass =
            'teacher-reveal teacher-float rounded-[28px] border border-slate-200/80 bg-white/95 p-5 shadow-[0_24px_55px_-42px_rgba(15,23,42,0.75)] ring-1 ring-slate-200/70 dark:border-slate-700/80 dark:bg-slate-900/95 dark:ring-slate-700/80 dark:shadow-[0_24px_70px_-42px_rgba(0,0,0,0.9)]';

        $labelClass = 'mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300';

        $inputClass =
            'w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-500/20';

        $fileInputClass =
            'w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 outline-none file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100 focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:file:bg-indigo-500/15 dark:file:text-indigo-300 dark:hover:file:bg-indigo-500/25 dark:focus:border-indigo-400 dark:focus:ring-indigo-500/20';

        $smallMutedClass = 'text-xs font-semibold text-slate-500 dark:text-slate-400';
    @endphp

    <div class="teacher-stage admin-management-page admin-staff-page mx-auto max-w-[1500px] space-y-6 pb-8 text-slate-900 dark:text-slate-100">

        <x-admin.page-header reveal-class="teacher-reveal" delay="1" icon="staff" title="Admin / Staff Management"
            subtitle="Create and manage school administrator and staff accounts." />

        <x-admin.stat-cards :cards="$adminStaffStatCards" reveal-class="teacher-reveal" float-class="teacher-float" />

        @if (session('success'))
            <div class="teacher-reveal rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/15 dark:text-emerald-300"
                style="--sd: 2;">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="teacher-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 dark:border-red-500/30 dark:bg-red-500/15 dark:text-red-300"
                style="--sd: 2;">
                {{ session('error') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="teacher-reveal rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/15 dark:text-amber-300"
                style="--sd: 2;">
                {{ session('warning') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="teacher-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-500/30 dark:bg-red-500/15 dark:text-red-300"
                style="--sd: 2;">
                <div class="font-semibold">Please check the form fields and try again.</div>
            </div>
        @endif

        @php
            $showCreateFormOnLoad = old('_form') === 'create_admin_staff';
        @endphp

        <div x-data="{ createOpen: @js($showCreateFormOnLoad) }"
            @open-admin-staff-create.window="
                createOpen = true;
                $nextTick(() => document.getElementById('name')?.focus());
            "
            class="grid gap-6 xl:grid-cols-12">

            {{-- CREATE ACCOUNT --}}
            <section x-show="createOpen" x-cloak x-transition.opacity.duration.150ms
                @keydown.escape.window="createOpen = false" @click.self="createOpen = false"
                role="dialog" aria-modal="true" aria-labelledby="create-admin-staff-title"
                class="fixed inset-0 z-[90] flex items-start justify-center overflow-y-auto bg-slate-950/65 px-4 py-6 backdrop-blur-sm sm:py-10"
                style="--sd: 3;">
                <div
                    class="w-full max-w-5xl overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl ring-1 ring-slate-950/5 dark:border-slate-700 dark:bg-slate-900 dark:ring-white/10">

                    <div
                        class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-5 dark:border-slate-700">
                        <div>
                            <h2 id="create-admin-staff-title" class="text-2xl font-black text-slate-950 dark:text-white">
                                Create account
                            </h2>
                            <p class="mt-1 text-sm font-semibold text-slate-500 dark:text-slate-400">
                                Add a new admin or staff profile with account access.
                            </p>
                        </div>

                        <button type="button" @click="createOpen = false" :aria-expanded="createOpen.toString()"
                            aria-controls="create-admin-staff-form-panel"
                            class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-red-600 text-lg font-black leading-none text-white shadow-sm transition hover:bg-red-500 focus:outline-none focus:ring-4 focus:ring-red-200 dark:focus:ring-red-500/25"
                            aria-label="Close create account form">
                            &times;
                        </button>
                    </div>

                <form id="create-admin-staff-form-panel" method="POST" action="{{ route('admin.admin-staff.store') }}"
                    enctype="multipart/form-data" class="js-create-form">
                    @csrf

                    <input type="hidden" name="_form" value="create_admin_staff">

                    <div class="max-h-[calc(100vh-15rem)] overflow-y-auto px-6 py-5">
                        <div class="grid gap-5 lg:grid-cols-2">
                    <div>
                        <label for="role" class="{{ $labelClass }}">Role</label>
                        <select id="role" name="role" class="{{ $inputClass }} capitalize">
                            @foreach ($assignableRoles as $roleOption)
                                <option value="{{ $roleOption }}"
                                    {{ old('role', 'staff') === $roleOption ? 'selected' : '' }}>
                                    {{ ucfirst($roleOption) }}
                                </option>
                            @endforeach
                        </select>

                        @error('role')
                            <p class="mt-1 text-xs font-semibold text-red-600 dark:text-red-300">{{ $message }}</p>
                        @enderror
                    </div>

                    @if ($hasStaffPermissionsColumn ?? false)
                        @php
                            $selectedCreateStaffPermissions = old('staff_permissions', []);
                            $selectedCreateStaffPermissions = is_array($selectedCreateStaffPermissions)
                                ? $selectedCreateStaffPermissions
                                : [];
                        @endphp

                        <div x-data="{ staffAccessOpen: false }"
                            class="rounded-2xl border border-slate-200 bg-slate-50/80 p-3 dark:border-slate-700 dark:bg-slate-800/70 lg:col-span-2">
                            <button type="button" @click="staffAccessOpen = !staffAccessOpen"
                                :aria-expanded="staffAccessOpen.toString()"
                                class="flex w-full items-center justify-between gap-3 text-left">
                                <span class="min-w-0">
                                    <span
                                        class="block text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                        Staff page access
                                    </span>
                                    <span class="mt-1 block text-[11px] leading-5 text-slate-500 dark:text-slate-400">
                                        Used only when role is Staff. Leave all unchecked for no admin pages.
                                    </span>
                                </span>

                                <span class="flex shrink-0 items-center gap-2">
                                    <span
                                        class="rounded-full bg-white px-2.5 py-1 text-[11px] font-bold text-slate-500 ring-1 ring-slate-200 dark:bg-slate-900 dark:text-slate-300 dark:ring-slate-700">
                                        {{ count($selectedCreateStaffPermissions) }} selected
                                    </span>

                                    <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200"
                                        :class="staffAccessOpen ? 'rotate-180' : ''"></i>
                                </span>
                            </button>

                            <div x-show="staffAccessOpen" x-cloak x-transition class="mt-3 max-h-72 overflow-y-auto pr-1">
                                <div class="grid gap-2">
                                    @foreach ($staffPermissionOptions as $permissionKey => $permissionOption)
                                        <label
                                            class="flex gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-900">
                                            <input type="checkbox" name="staff_permissions[]" value="{{ $permissionKey }}"
                                                @checked(in_array($permissionKey, $selectedCreateStaffPermissions, true))
                                                class="mt-0.5 h-4 w-4 shrink-0 rounded border-slate-300 text-indigo-600 dark:border-slate-600 dark:bg-slate-800">

                                            <span class="min-w-0">
                                                <span class="block text-xs font-bold text-slate-700 dark:text-slate-200">
                                                    {{ $permissionOption['label'] }}
                                                </span>

                                                <span
                                                    class="block text-[11px] leading-4 text-slate-500 dark:text-slate-400">
                                                    {{ $permissionOption['description'] }}
                                                </span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            @error('staff_permissions')
                                <p class="mt-2 text-xs font-semibold text-red-600 dark:text-red-300">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <div>
                        <label for="name" class="{{ $labelClass }}">Full Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}"
                            class="{{ $inputClass }}" placeholder="Full name">

                        @error('name')
                            <p class="mt-1 text-xs font-semibold text-red-600 dark:text-red-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="{{ $labelClass }}">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}"
                            class="{{ $inputClass }}" placeholder="account@example.com">

                        @error('email')
                            <p class="mt-1 text-xs font-semibold text-red-600 dark:text-red-300">{{ $message }}</p>
                        @enderror
                    </div>

                    @if ($hasPhoneColumn ?? false)
                        <div>
                            <label for="phone_number" class="{{ $labelClass }}">Phone Number</label>
                            <input id="phone_number" name="phone_number" type="text"
                                value="{{ old('phone_number') }}" class="{{ $inputClass }}"
                                placeholder="+855 12 345 678">

                            @error('phone_number')
                                <p class="mt-1 text-xs font-semibold text-red-600 dark:text-red-300">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="password" class="{{ $labelClass }}">Password</label>
                            <input id="password" name="password" type="password" class="{{ $inputClass }}"
                                placeholder="Minimum 8 characters">
                        </div>

                        <div>
                            <label for="password_confirmation" class="{{ $labelClass }}">Confirm</label>
                            <input id="password_confirmation" name="password_confirmation" type="password"
                                class="{{ $inputClass }}" placeholder="Re-enter password">
                        </div>
                    </div>

                    @error('password')
                        <p class="-mt-2 text-xs font-semibold text-red-600 dark:text-red-300">{{ $message }}</p>
                    @enderror

                    <div>
                        <label for="avatar_image" class="{{ $labelClass }}">Avatar Image (Optional)</label>
                        <input id="avatar_image" name="avatar_image" type="file" accept="image/*"
                            class="{{ $fileInputClass }}">

                        <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                            Allowed: JPG, PNG, WEBP max 5MB.
                        </p>

                        @error('avatar_image')
                            <p class="mt-1 text-xs font-semibold text-red-600 dark:text-red-300">{{ $message }}</p>
                        @enderror
                    </div>

                    @if ($hasStatusColumn)
                        <label
                            class="flex items-center justify-between rounded-xl border border-slate-200 bg-white px-3 py-2.5 dark:border-slate-700 dark:bg-slate-800">
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">Initial Status</span>

                            <span
                                class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                                <input type="checkbox" name="is_active" value="1"
                                    class="h-4 w-4 rounded border-slate-300 text-indigo-600 dark:border-slate-600 dark:bg-slate-900"
                                    {{ old('is_active', '1') ? 'checked' : '' }}>
                                Active
                            </span>
                        </label>
                    @endif
                        </div>
                    </div>

                    <div
                        class="flex flex-col-reverse gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4 sm:flex-row sm:justify-end dark:border-slate-700 dark:bg-slate-950/40">
                        <button type="button" @click="createOpen = false"
                            class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                            Cancel
                        </button>

                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-sm shadow-indigo-500/20 transition hover:bg-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-200 dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:focus:ring-indigo-500/25">
                            Create account
                        </button>
                    </div>
                </form>
                </div>
            </section>

            {{-- LIST --}}
            <section x-data="{ filterOpen: false, editing: null }" @open-filter-panel.window="filterOpen = true"
                class="{{ $panelClass }} min-w-0 xl:col-span-12"
                style="--sd: 4;">

                <div class="space-y-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <h2 class="text-lg font-black text-slate-950 dark:text-white">Admin / Staff List</h2>

                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                            <button type="button" id="admin-staff-create-alert-button"
                                @click="window.dispatchEvent(new CustomEvent('open-admin-staff-create'))"
                                class="inline-flex min-w-[150px] items-center justify-center gap-2 rounded-2xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-indigo-500/20 transition duration-200 hover:-translate-y-0.5 hover:bg-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-200 dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:focus:ring-indigo-500/25">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M12 5v14M5 12h14"></path>
                                </svg>
                                Create
                            </button>

                            <button type="button" @click="filterOpen = true"
                                class="inline-flex min-w-[150px] items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M3 5h18l-7 8v5l-4 2v-7L3 5z"></path>
                                </svg>
                                Filters
                            </button>
                        </div>
                    </div>

                    {{-- FILTER OVERLAY --}}
                    <div x-show="filterOpen" x-cloak x-transition.opacity
                        class="fixed inset-0 z-[80] bg-slate-900/50 backdrop-blur-sm" @click="filterOpen = false"></div>

                    <div class="grid gap-4">
                        {{-- FILTER DRAWER --}}
                        <aside x-show="filterOpen" x-cloak x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0"
                            x-transition:leave-end="translate-x-full"
                            class="fixed inset-y-0 right-0 z-[81] w-full max-w-md transform border-l border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900">

                            <div class="flex h-full flex-col">
                                <div
                                    class="flex items-center justify-between border-b border-slate-200 px-5 py-4 dark:border-slate-700">
                                    <h3 class="text-3xl font-black text-slate-950 dark:text-white">Filters</h3>

                                    <div class="flex items-center gap-4">
                                        <a href="{{ route('admin.admin-staff.index') }}"
                                            class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:bg-slate-100 hover:text-slate-800 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                                            Clear All
                                        </a>

                                        <button type="button" @click="filterOpen = false"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-2xl font-bold leading-none text-slate-600 shadow-sm transition hover:bg-slate-100 hover:text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                                            aria-label="Close filters">
                                            &times;
                                        </button>
                                    </div>
                                </div>

                                <form method="GET" action="{{ route('admin.admin-staff.index') }}"
                                    class="flex min-h-0 flex-1 flex-col" @submit="filterOpen = false">

                                    <div class="flex-1 space-y-5 overflow-y-auto px-5 py-4">
                                        <section class="space-y-2">
                                            <h4 class="text-xl font-bold text-slate-950 dark:text-white">Search</h4>
                                            <input id="q" name="q" type="text"
                                                value="{{ $search }}" placeholder="Search by name, email, or phone"
                                                class="{{ $inputClass }}">
                                        </section>

                                        <section class="space-y-2">
                                            <h4 class="text-xl font-bold text-slate-950 dark:text-white">Role</h4>
                                            <select name="role" class="{{ $inputClass }}">
                                                <option value="all" {{ $role === 'all' ? 'selected' : '' }}>
                                                    All roles
                                                </option>

                                                @foreach ($roles as $roleOption)
                                                    <option value="{{ $roleOption }}"
                                                        {{ $role === $roleOption ? 'selected' : '' }}>
                                                        {{ ucfirst($roleOption) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </section>

                                        @if ($hasStatusColumn)
                                            <section class="space-y-2">
                                                <h4 class="text-xl font-bold text-slate-950 dark:text-white">Status</h4>
                                                <select name="status" class="{{ $inputClass }}">
                                                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>
                                                        All status
                                                    </option>

                                                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>
                                                        Active
                                                    </option>

                                                    <option value="inactive"
                                                        {{ $status === 'inactive' ? 'selected' : '' }}>
                                                        Inactive
                                                    </option>
                                                </select>
                                            </section>
                                        @else
                                            <input type="hidden" name="status" value="all">
                                        @endif
                                    </div>

                                    <div class="border-t border-slate-200 px-5 py-4 dark:border-slate-700">
                                        <button type="submit"
                                            class="inline-flex w-full items-center justify-center rounded-2xl bg-slate-950 px-4 py-3 text-base font-bold text-white shadow-sm transition hover:bg-slate-800 dark:bg-indigo-600 dark:hover:bg-indigo-500">
                                            Apply Filters
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </aside>

                        {{-- ACTIVE FILTERS --}}
                        @if ($search !== '' || $role !== 'all' || $status !== 'all')
                            <div
                                class="flex flex-wrap items-center gap-2 rounded-2xl bg-indigo-50/70 px-3 py-2 text-xs font-semibold text-slate-600 dark:bg-indigo-500/10 dark:text-slate-300">
                                <span class="text-indigo-700 dark:text-indigo-300">Active filters:</span>

                                @if ($search !== '')
                                    <span
                                        class="rounded-full bg-white px-2.5 py-1 text-slate-700 ring-1 ring-indigo-100 dark:bg-slate-800 dark:text-slate-300 dark:ring-indigo-400/20">
                                        Search: {{ $search }}
                                    </span>
                                @endif

                                @if ($role !== 'all')
                                    <span
                                        class="rounded-full bg-white px-2.5 py-1 text-slate-700 ring-1 ring-indigo-100 dark:bg-slate-800 dark:text-slate-300 dark:ring-indigo-400/20">
                                        Role: {{ ucfirst($role) }}
                                    </span>
                                @endif

                                @if ($status !== 'all')
                                    <span
                                        class="rounded-full bg-white px-2.5 py-1 text-slate-700 ring-1 ring-indigo-100 dark:bg-slate-800 dark:text-slate-300 dark:ring-indigo-400/20">
                                        Status: {{ ucfirst($status) }}
                                    </span>
                                @endif

                                <a href="{{ route('admin.admin-staff.index') }}"
                                    class="ml-auto rounded-full bg-white px-2.5 py-1 text-indigo-700 ring-1 ring-indigo-100 hover:bg-indigo-100 dark:bg-slate-800 dark:text-indigo-300 dark:ring-indigo-400/20 dark:hover:bg-slate-700">
                                    Clear
                                </a>
                            </div>
                        @endif

                        {{-- TABLE --}}
                        <div class="min-w-0">
                            <div class="mt-1 overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-700">
                                <div class="max-h-[700px] overflow-auto">
                                    <table class="admin-table w-full min-w-[1280px] text-left text-sm">
                                        <thead
                                            class="admin-table-head sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">
                                            <tr>
                                                <th class="px-3 py-3 font-semibold">Account</th>
                                                <th class="px-3 py-3 font-semibold">Email</th>

                                                @if ($hasPhoneColumn ?? false)
                                                    <th class="whitespace-nowrap px-3 py-3 font-semibold">Phone Number</th>
                                                @endif

                                                <th class="px-3 py-3 font-semibold">Role</th>
                                                <th class="px-3 py-3 font-semibold">Status</th>
                                                <th class="whitespace-nowrap px-3 py-3 font-semibold">Created</th>
                                                <th class="px-3 py-3 text-right font-semibold">Actions</th>
                                            </tr>
                                        </thead>

                                        <tbody
                                            class="divide-y divide-slate-100 bg-white dark:divide-slate-700 dark:bg-slate-900">
                                            @forelse ($accounts as $account)
                                                @php
                                                    $isSelf = (int) auth()->id() === (int) $account->id;

                                                    $currentUserIsStaff =
                                                        strtolower(trim((string) (auth()->user()?->role ?? ''))) ===
                                                        'staff';

                                                    $deleteDisabled =
                                                        $isSelf || ($currentUserIsStaff && $account->role === 'admin');

                                                    $roleBadgeClass =
                                                        $account->role === 'admin'
                                                            ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-300'
                                                            : 'bg-sky-50 text-sky-700 dark:bg-sky-500/15 dark:text-sky-300';
                                                @endphp

                                                <tr
                                                    class="align-top transition hover:bg-slate-50/80 dark:hover:bg-slate-800/70">
                                                    <td class="px-3 py-3">
                                                        <div class="flex items-center gap-3">
                                                            <img src="{{ $account->avatar_url }}"
                                                                alt="{{ $account->name }}"
                                                                class="h-9 w-9 rounded-full object-cover ring-1 ring-slate-200 dark:ring-slate-700">

                                                            <div class="min-w-0">
                                                                <div
                                                                    class="student-name font-semibold text-slate-800 dark:text-slate-100">
                                                                    {{ $account->name }}

                                                                    @if ($isSelf)
                                                                        <span
                                                                            class="ml-1 text-xs font-bold text-indigo-600 dark:text-indigo-300">
                                                                            (You)
                                                                        </span>
                                                                    @endif
                                                                </div>

                                                                <div class="text-xs text-slate-400 dark:text-slate-500">
                                                                    ID #{{ $account->formatted_id }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td class="px-3 py-3 text-slate-600 dark:text-slate-300">
                                                        <div class="student-email text-slate-600 dark:text-slate-300">
                                                            {{ $account->email }}
                                                        </div>
                                                    </td>

                                                    @if ($hasPhoneColumn ?? false)
                                                        <td
                                                            class="whitespace-nowrap px-3 py-3 tabular-nums text-slate-600 dark:text-slate-300">
                                                            {{ $account->phone_number ?: '-' }}
                                                        </td>
                                                    @endif

                                                    <td class="px-3 py-3">
                                                        <span
                                                            class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold capitalize {{ $roleBadgeClass }}">
                                                            {{ $account->role }}
                                                        </span>
                                                    </td>

                                                    <td class="px-3 py-3">
                                                        @if ($hasStatusColumn && $account->is_active)
                                                            <span
                                                                class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300">
                                                                <span
                                                                    class="status-dot h-2 w-2 rounded-full bg-emerald-500"></span>
                                                                Active
                                                            </span>
                                                        @elseif($hasStatusColumn)
                                                            <span
                                                                class="inline-flex items-center gap-2 rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700 dark:bg-rose-500/15 dark:text-rose-300">
                                                                <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                                                                Inactive
                                                            </span>
                                                        @else
                                                            <span
                                                                class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                                                N/A
                                                            </span>
                                                        @endif
                                                    </td>

                                                    <td
                                                        class="whitespace-nowrap px-3 py-3 text-slate-500 dark:text-slate-400">
                                                        {{ $account->created_at->format('M d, Y') }}
                                                    </td>

                                                    <td class="whitespace-nowrap px-3 py-3">
                                                        <div
                                                            class="flex flex-nowrap items-center justify-end gap-2 whitespace-nowrap">
                                                            <button @click="editing = {{ $account->id }}" type="button"
                                                                class="whitespace-nowrap rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                                                                Edit
                                                            </button>

                                                            @if ($hasStatusColumn)
                                                                <form method="POST"
                                                                    action="{{ route('admin.admin-staff.status', $account) }}"
                                                                    class="js-status-form"
                                                                    data-account="{{ $account->name }}"
                                                                    data-action="{{ $account->is_active ? 'set inactive' : 'set active' }}">
                                                                    @csrf
                                                                    @method('PATCH')

                                                                    <button type="submit" {{ $isSelf ? 'disabled' : '' }}
                                                                        class="whitespace-nowrap rounded-lg border px-3 py-1.5 text-xs font-semibold disabled:cursor-not-allowed disabled:opacity-50
                                                                        {{ $account->is_active
                                                                            ? 'border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100 dark:border-amber-500/30 dark:bg-amber-500/15 dark:text-amber-300 dark:hover:bg-amber-500/25'
                                                                            : 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:border-emerald-500/30 dark:bg-emerald-500/15 dark:text-emerald-300 dark:hover:bg-emerald-500/25' }}">
                                                                        {{ $account->is_active ? 'Set Inactive' : 'Set Active' }}
                                                                    </button>
                                                                </form>
                                                            @endif

                                                            <form method="POST"
                                                                action="{{ route('admin.admin-staff.destroy', $account) }}"
                                                                class="js-delete-form"
                                                                data-account="{{ $account->name }}">
                                                                @csrf
                                                                @method('DELETE')

                                                                <button type="submit"
                                                                    {{ $deleteDisabled ? 'disabled' : '' }}
                                                                    class="whitespace-nowrap rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 transition hover:bg-red-100 disabled:cursor-not-allowed disabled:opacity-50 dark:border-red-500/30 dark:bg-red-500/15 dark:text-red-300 dark:hover:bg-red-500/25">
                                                                    Delete
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="{{ $hasPhoneColumn ?? false ? 7 : 6 }}"
                                                        class="px-3 py-10 text-center text-sm text-slate-500 dark:text-slate-400">
                                                        No admin or staff accounts found.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- EDIT MODALS --}}
                    @foreach ($accounts as $account)
                        @php
                            $isSelf = (int) auth()->id() === (int) $account->id;
                        @endphp

                        <div x-show="editing === {{ $account->id }}" x-cloak
                            class="fixed inset-0 z-[90] grid place-items-center p-4" aria-modal="true" role="dialog">

                            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="editing = null"></div>

                            <div
                                class="relative z-10 flex max-h-[calc(100vh-2rem)] w-full max-w-xl flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900">

                                <div
                                    class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-slate-700">
                                    <h3 class="text-lg font-black text-slate-950 dark:text-white">Edit Account</h3>

                                    <button type="button" @click="editing = null"
                                        class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-slate-200">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M18.3 5.71 12 12l6.3 6.29-1.41 1.42L10.59 13.4 4.3 19.7 2.89 18.3 9.17 12 2.9 5.71 4.3 4.29l6.29 6.3 6.3-6.3 1.41 1.42Z" />
                                        </svg>
                                    </button>
                                </div>

                                <form method="POST" action="{{ route('admin.admin-staff.update', $account) }}"
                                    enctype="multipart/form-data" class="js-edit-form flex min-h-0 flex-1 flex-col"
                                    data-account="{{ $account->name }}">
                                    @csrf
                                    @method('PUT')

                                    <div class="flex-1 space-y-4 overflow-y-auto overscroll-contain px-5 pb-4 pt-4">
                                        <div>
                                            <label for="edit_role_{{ $account->id }}"
                                                class="{{ $labelClass }}">Role</label>
                                            <select id="edit_role_{{ $account->id }}" name="role"
                                                class="{{ $inputClass }} capitalize">
                                                @foreach ($assignableRoles as $roleOption)
                                                    <option value="{{ $roleOption }}"
                                                        {{ $account->role === $roleOption ? 'selected' : '' }}>
                                                        {{ ucfirst($roleOption) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        @if ($hasStaffPermissionsColumn ?? false)
                                            @php
                                                $accountStaffPermissions = \App\Support\StaffPermissions::permissionsFor(
                                                    $account,
                                                );
                                                $accountPermissionsWereSaved =
                                                    $account->getAttribute('staff_permissions') !== null;

                                                if (!$accountPermissionsWereSaved && $account->role === 'staff') {
                                                    $accountStaffPermissions = [];
                                                }
                                            @endphp

                                            <div x-data="{ staffAccessOpen: false }"
                                                class="rounded-2xl border border-slate-200 bg-slate-50/80 p-3 dark:border-slate-700 dark:bg-slate-800/70">
                                                <button type="button" @click="staffAccessOpen = !staffAccessOpen"
                                                    :aria-expanded="staffAccessOpen.toString()"
                                                    class="flex w-full items-center justify-between gap-3 text-left">
                                                    <span class="min-w-0">
                                                        <span
                                                            class="block text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                                            Staff page access
                                                        </span>

                                                        <span
                                                            class="mt-1 block text-[11px] leading-5 text-slate-500 dark:text-slate-400">
                                                            Used only when this account role is Staff.
                                                        </span>
                                                    </span>

                                                    <span class="flex shrink-0 items-center gap-2">
                                                        <span
                                                            class="rounded-full bg-white px-2.5 py-1 text-[11px] font-bold text-slate-500 ring-1 ring-slate-200 dark:bg-slate-900 dark:text-slate-300 dark:ring-slate-700">
                                                            {{ count($accountStaffPermissions) }} selected
                                                        </span>

                                                        <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200"
                                                            :class="staffAccessOpen ? 'rotate-180' : ''"></i>
                                                    </span>
                                                </button>

                                                <div x-show="staffAccessOpen" x-cloak x-transition
                                                    class="mt-3 max-h-72 overflow-y-auto pr-1">
                                                    <div class="grid gap-2 sm:grid-cols-2">
                                                        @foreach ($staffPermissionOptions as $permissionKey => $permissionOption)
                                                            <label
                                                                class="flex gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-900">
                                                                <input type="checkbox" name="staff_permissions[]"
                                                                    value="{{ $permissionKey }}"
                                                                    @checked(in_array($permissionKey, $accountStaffPermissions, true))
                                                                    class="mt-0.5 h-4 w-4 shrink-0 rounded border-slate-300 text-indigo-600 dark:border-slate-600 dark:bg-slate-800">

                                                                <span class="min-w-0">
                                                                    <span
                                                                        class="block text-xs font-bold text-slate-700 dark:text-slate-200">
                                                                        {{ $permissionOption['label'] }}
                                                                    </span>

                                                                    <span
                                                                        class="block text-[11px] leading-4 text-slate-500 dark:text-slate-400">
                                                                        {{ $permissionOption['description'] }}
                                                                    </span>
                                                                </span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <div>
                                            <label for="edit_name_{{ $account->id }}" class="{{ $labelClass }}">Full
                                                Name</label>
                                            <input id="edit_name_{{ $account->id }}" name="name" type="text"
                                                value="{{ $account->name }}" class="{{ $inputClass }}">
                                        </div>

                                        <div>
                                            <label for="edit_email_{{ $account->id }}"
                                                class="{{ $labelClass }}">Email</label>
                                            <input id="edit_email_{{ $account->id }}" name="email" type="email"
                                                value="{{ $account->email }}" class="{{ $inputClass }}">
                                        </div>

                                        @if ($hasPhoneColumn ?? false)
                                            <div>
                                                <label for="edit_phone_number_{{ $account->id }}"
                                                    class="{{ $labelClass }}">
                                                    Phone Number
                                                </label>

                                                <input id="edit_phone_number_{{ $account->id }}" name="phone_number"
                                                    type="text" value="{{ $account->phone_number }}"
                                                    class="{{ $inputClass }}" placeholder="+855 12 345 678">
                                            </div>
                                        @endif

                                        <div>
                                            <label for="edit_avatar_image_{{ $account->id }}"
                                                class="{{ $labelClass }}">
                                                Avatar Image
                                            </label>

                                            <input id="edit_avatar_image_{{ $account->id }}" name="avatar_image"
                                                type="file" accept="image/*" class="{{ $fileInputClass }}">

                                            <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                                                Leave empty to keep current avatar.
                                            </p>
                                        </div>

                                        <div class="grid gap-4 sm:grid-cols-2">
                                            <div>
                                                <label for="edit_password_{{ $account->id }}"
                                                    class="{{ $labelClass }}">
                                                    New Password
                                                </label>

                                                <div class="relative">
                                                    <input id="edit_password_{{ $account->id }}" name="password"
                                                        type="password" class="{{ $inputClass }} pr-10"
                                                        placeholder="Leave blank to keep current">

                                                    <button type="button"
                                                        onclick="toggleAdminStaffPassword('edit_password_{{ $account->id }}', this)"
                                                        class="absolute inset-y-0 right-3 flex items-center text-slate-400 transition hover:text-slate-700 dark:hover:text-slate-200"
                                                        aria-label="Show new password">
                                                        <svg class="admin-staff-eye-icon h-4 w-4" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                            <path stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0" />
                                                            <path stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>

                                                        <svg class="admin-staff-eye-off-icon hidden h-4 w-4"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                            aria-hidden="true">
                                                            <path stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                d="M3 3l18 18M10.584 10.587A2 2 0 0012 14a2 2 0 001.414-.586M9.88 4.24A9.77 9.77 0 0112 4c5 0 9.27 3.11 11 7.5a11.72 11.72 0 01-3.13 4.44M6.61 6.61A11.72 11.72 0 001 11.5C2.73 15.89 7 19 12 19a9.8 9.8 0 004.39-1.03" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>

                                            <div>
                                                <label for="edit_password_confirmation_{{ $account->id }}"
                                                    class="{{ $labelClass }}">
                                                    Confirm Password
                                                </label>

                                                <div class="relative">
                                                    <input id="edit_password_confirmation_{{ $account->id }}"
                                                        name="password_confirmation" type="password"
                                                        class="{{ $inputClass }} pr-10">

                                                    <button type="button"
                                                        onclick="toggleAdminStaffPassword('edit_password_confirmation_{{ $account->id }}', this)"
                                                        class="absolute inset-y-0 right-3 flex items-center text-slate-400 transition hover:text-slate-700 dark:hover:text-slate-200"
                                                        aria-label="Show password confirmation">
                                                        <svg class="admin-staff-eye-icon h-4 w-4" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                            <path stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0" />
                                                            <path stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>

                                                        <svg class="admin-staff-eye-off-icon hidden h-4 w-4"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                            aria-hidden="true">
                                                            <path stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                d="M3 3l18 18M10.584 10.587A2 2 0 0012 14a2 2 0 001.414-.586M9.88 4.24A9.77 9.77 0 0112 4c5 0 9.27 3.11 11 7.5a11.72 11.72 0 01-3.13 4.44M6.61 6.61A11.72 11.72 0 001 11.5C2.73 15.89 7 19 12 19a9.8 9.8 0 004.39-1.03" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        @if ($hasStatusColumn)
                                            <label
                                                class="flex items-center justify-between rounded-xl border border-slate-200 bg-white px-3 py-2.5 dark:border-slate-700 dark:bg-slate-800">
                                                <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                                                    Status
                                                </span>

                                                <span
                                                    class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                                                    <input type="checkbox" name="is_active" value="1"
                                                        class="h-4 w-4 rounded border-slate-300 text-indigo-600 dark:border-slate-600 dark:bg-slate-900"
                                                        {{ $account->is_active ? 'checked' : '' }}
                                                        {{ $isSelf ? 'disabled' : '' }}>
                                                    Active
                                                </span>
                                            </label>
                                        @endif
                                    </div>

                                    <div
                                        class="flex justify-end gap-2 border-t border-slate-100 px-5 py-4 dark:border-slate-700">
                                        <button type="button" @click="editing = null"
                                            class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                                            Cancel
                                        </button>

                                        <button type="submit"
                                            class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                                            Save Changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach

                    <div class="admin-staff-pagination text-slate-700 dark:text-slate-300">
                        {{ $accounts->links() }}
                    </div>
                </div>
            </section>
        </div>
    </div>

    @php
        $adminStaffPageData = [
            'validationErrors' => $errors->all(),
            'flash' => [
                'success' => session('success'),
                'warning' => session('warning'),
                'error' => session('error'),
            ],
        ];
    @endphp

    <script id="admin-staff-data" type="application/json">{!! json_encode($adminStaffPageData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>

    <script>
        function toggleAdminStaffPassword(id, button) {
            const input = document.getElementById(id);
            const eye = button.querySelector('.admin-staff-eye-icon');
            const eyeOff = button.querySelector('.admin-staff-eye-off-icon');

            if (!input) {
                return;
            }

            const isHidden = input.type === 'password';

            input.type = isHidden ? 'text' : 'password';
            button.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');

            eye?.classList.toggle('hidden', isHidden);
            eyeOff?.classList.toggle('hidden', !isHidden);
        }
    </script>

    @vite(['resources/js/admin/admin-staff.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
