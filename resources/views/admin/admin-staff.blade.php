@extends('layout.admin.navbar.navbar')

@section('page')
    <div class="stage admin-stage space-y-6">
        <x-admin.page-header reveal-class="reveal" delay="1" icon="staff" title="Admin / Staff Management"
            subtitle="Create and manage school administrator and staff accounts.">
            <x-slot:stats>
                <span class="admin-page-stat">Total: {{ $stats['total'] }}</span>
                <span class="admin-page-stat admin-page-stat--sky">Admins: {{ $stats['admins'] }}</span>
                <span class="admin-page-stat admin-page-stat--amber">Staff: {{ $stats['staff'] }}</span>
                <span class="admin-page-stat admin-page-stat--emerald">Active: {{ $stats['active'] }}</span>
                <span class="admin-page-stat admin-page-stat--rose">Inactive: {{ $stats['inactive'] }}</span>
            </x-slot:stats>
        </x-admin.page-header>

        @if (session('success'))
            <div class="reveal rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"
                style="--sd: 2;">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700"
                style="--sd: 2;">
                {{ session('error') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="reveal rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700"
                style="--sd: 2;">
                {{ session('warning') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" style="--sd: 2;">
                <div class="font-semibold">Please check the form fields and try again.</div>
            </div>
        @endif

        @php
            $showCreateFormOnLoad = old('_form') === 'create_admin_staff';
        @endphp

        <div class="grid gap-6 xl:grid-cols-12">
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
                class="reveal float-card rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-4"
                style="--sd: 3;">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-black text-slate-900">Create account</h2>
                        <p class="mt-1 text-xs text-slate-500">Choose admin or staff as the account role.</p>
                    </div>
                    <button type="button" @click="createOpen = !createOpen"
                        :aria-expanded="(createOpen || isDesktop).toString()" aria-controls="create-admin-staff-form-panel"
                        class="inline-flex items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 shadow-sm hover:bg-indigo-100 xl:hidden">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 5v14M5 12h14" x-show="!(createOpen || isDesktop)"></path>
                            <path d="M5 12h14" x-show="createOpen || isDesktop"></path>
                        </svg>
                        <span x-show="!(createOpen || isDesktop)">Create</span>
                        <span x-show="createOpen || isDesktop">Hide Form</span>
                    </button>
                </div>

                <form id="create-admin-staff-form-panel" method="POST" action="{{ route('admin.admin-staff.store') }}"
                    enctype="multipart/form-data" class="js-create-form mt-5 space-y-4" x-show="createOpen || isDesktop"
                    x-cloak x-transition.opacity.duration.150ms>
                    @csrf
                    <input type="hidden" name="_form" value="create_admin_staff">

                    <div>
                        <label for="role" class="mb-1 block text-xs font-semibold text-slate-600">Role</label>
                        <select id="role" name="role"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm capitalize outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            @foreach ($roles as $roleOption)
                                <option value="{{ $roleOption }}"
                                    {{ old('role', 'staff') === $roleOption ? 'selected' : '' }}>
                                    {{ ucfirst($roleOption) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="name" class="mb-1 block text-xs font-semibold text-slate-600">Full Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                            placeholder="Full name">
                        @error('name')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-1 block text-xs font-semibold text-slate-600">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                            placeholder="account@example.com">
                        @error('email')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    @if ($hasPhoneColumn ?? false)
                        <div>
                            <label for="phone_number" class="mb-1 block text-xs font-semibold text-slate-600">Phone
                                Number</label>
                            <input id="phone_number" name="phone_number" type="text" value="{{ old('phone_number') }}"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                                placeholder="+855 12 345 678">
                            @error('phone_number')
                                <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="password" class="mb-1 block text-xs font-semibold text-slate-600">Password</label>
                            <input id="password" name="password" type="password"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                                placeholder="Minimum 8 characters">
                        </div>
                        <div>
                            <label for="password_confirmation"
                                class="mb-1 block text-xs font-semibold text-slate-600">Confirm</label>
                            <input id="password_confirmation" name="password_confirmation" type="password"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                                placeholder="Re-enter password">
                        </div>
                    </div>
                    @error('password')
                        <p class="-mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror

                    <div>
                        <label for="avatar_image" class="mb-1 block text-xs font-semibold text-slate-600">Avatar Image
                            (Optional)</label>
                        <input id="avatar_image" name="avatar_image" type="file" accept="image/*"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100 focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                        <p class="mt-1 text-[11px] text-slate-500">Allowed: JPG, PNG, WEBP (max 5MB)</p>
                        @error('avatar_image')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    @if ($hasStatusColumn)
                        <label class="flex items-center justify-between rounded-xl border border-slate-200 px-3 py-2.5">
                            <span class="text-sm font-semibold text-slate-700">Initial Status</span>
                            <span class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500">
                                <input type="checkbox" name="is_active" value="1"
                                    class="h-4 w-4 rounded border-slate-300" {{ old('is_active', '1') ? 'checked' : '' }}>
                                Active
                            </span>
                        </label>
                    @endif

                    <button type="submit"
                        class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">
                        Create account
                    </button>
                </form>
            </section>

            <section x-data="{ filterOpen: false, editing: null }" @open-filter-panel.window="filterOpen = true"
                class="reveal float-card min-w-0 rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-8"
                style="--sd: 4;">
                <div class="space-y-4">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <h2 class="text-lg font-black text-slate-900">Admin / Staff List</h2>
                            <p class="mt-1 text-xs font-medium text-slate-500">
                                Search, filter, edit, activate, deactivate, or remove privileged accounts.
                            </p>
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
                                    <a href="{{ route('admin.admin-staff.index') }}"
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

                            <form method="GET" action="{{ route('admin.admin-staff.index') }}"
                                class="flex min-h-0 flex-1 flex-col" @submit="filterOpen = false">
                                <div class="flex-1 space-y-5 overflow-y-auto px-5 py-4">
                                    <section class="space-y-2">
                                        <h4 class="text-xl font-bold text-slate-900">Search</h4>
                                        <input id="q" name="q" type="text" value="{{ $search }}"
                                            placeholder="Search by name, email, or phone"
                                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                    </section>

                                    <section class="space-y-2">
                                        <h4 class="text-xl font-bold text-slate-900">Role</h4>
                                        <select name="role"
                                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                            <option value="all" {{ $role === 'all' ? 'selected' : '' }}>All roles
                                            </option>
                                            @foreach ($roles as $roleOption)
                                                <option value="{{ $roleOption }}"
                                                    {{ $role === $roleOption ? 'selected' : '' }}>
                                                    {{ ucfirst($roleOption) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </section>

                                    <section class="space-y-2">
                                        <h4 class="text-xl font-bold text-slate-900">Status</h4>
                                        <select name="status"
                                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All status
                                            </option>
                                            <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>
                                                Inactive</option>
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

                    @if ($search !== '' || $role !== 'all' || $status !== 'all')
                        <div
                            class="flex flex-wrap items-center gap-2 rounded-2xl border border-indigo-100 bg-indigo-50/60 px-3 py-2 text-xs font-semibold text-slate-600">
                            <span class="text-indigo-700">Active filters:</span>
                            @if ($search !== '')
                                <span
                                    class="rounded-full bg-white px-2.5 py-1 text-slate-700 ring-1 ring-indigo-100">Search:
                                    {{ $search }}</span>
                            @endif
                            @if ($role !== 'all')
                                <span class="rounded-full bg-white px-2.5 py-1 text-slate-700 ring-1 ring-indigo-100">Role:
                                    {{ ucfirst($role) }}</span>
                            @endif
                            @if ($status !== 'all')
                                <span
                                    class="rounded-full bg-white px-2.5 py-1 text-slate-700 ring-1 ring-indigo-100">Status:
                                    {{ ucfirst($status) }}</span>
                            @endif
                            <a href="{{ route('admin.admin-staff.index') }}"
                                class="ml-auto rounded-full bg-white px-2.5 py-1 text-indigo-700 ring-1 ring-indigo-100 hover:bg-indigo-100">
                                Clear
                            </a>
                        </div>
                    @endif

                    <div class="mt-1 overflow-hidden rounded-2xl border border-slate-200">
                        <div class="max-h-[1200px] overflow-auto">
                            <table class="student-table admin-staff-table w-full min-w-[1180px] text-left text-sm">
                                <thead class="sticky top-0 z-10 text-xs uppercase">
                                    <tr>
                                        <th class="student-col-student px-3 py-3 font-semibold ">Account
                                        </th>
                                        <th class="student-col-email px-3 py-3 font-semibold whitespace-nowrap">Email</th>
                                        @if ($hasPhoneColumn ?? false)
                                            <th class="student-col-phone whitespace-nowrap px-3 py-3 font-semibold">Phone
                                                Number</th>
                                        @endif
                                        <th class="student-col-class px-3 py-3 font-semibold">Role</th>
                                        <th class="student-col-status px-3 py-3 font-semibold">Status</th>
                                        <th class="student-col-created whitespace-nowrap px-3 py-3 font-semibold">Created
                                        </th>
                                        <th class="student-col-actions whitespace-nowrap px-3 py-3 font-semibold">Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    @forelse ($accounts as $account)
                                        @php
                                            $isSelf = (int) auth()->id() === (int) $account->id;
                                            $roleBadgeClass =
                                                $account->role === 'admin'
                                                    ? 'bg-indigo-50 text-indigo-700'
                                                    : 'bg-sky-50 text-sky-700';
                                        @endphp
                                        <tr class="align-top">
                                            <td class="student-col-accounts px-3 py-3 align-top">
                                                <div class="flex items-center gap-3">
                                                    <img src="{{ $account->avatar_url }}" alt="{{ $account->name }}"
                                                        class="h-9 w-9 rounded-full object-cover ring-1 ring-slate-200">
                                                    <div class="min-w-0">
                                                        <div class="student-name font-semibold text-slate-800">
                                                            {{ $account->name }}
                                                            @if ($isSelf)
                                                                <span
                                                                    class="ml-1 text-xs font-bold text-indigo-600">(You)</span>
                                                            @endif
                                                        </div>
                                                        <div class="text-xs text-slate-400">ID
                                                            #{{ $account->formatted_id }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="student-col-email px-3 py-3 align-top text-slate-600">
                                                <div class="student-email text-slate-600">{{ $account->email }}</div>
                                            </td>
                                            @if ($hasPhoneColumn ?? false)
                                                <td
                                                    class="student-col-phone whitespace-nowrap px-3 py-3 align-top tabular-nums text-slate-600">
                                                    {{ $account->phone_number ?: '-' }}
                                                </td>
                                            @endif
                                            <td class="student-col-class px-3 py-3 align-top">
                                                <span
                                                    class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold capitalize {{ $roleBadgeClass }}">
                                                    {{ $account->role }}
                                                </span>
                                            </td>
                                            <td class="student-col-status px-3 py-3 align-top">
                                                @if ($hasStatusColumn && $account->is_active)
                                                    <span
                                                        class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                                        <span
                                                            class="status-dot h-2 w-2 rounded-full bg-emerald-500"></span>Active
                                                    </span>
                                                @elseif($hasStatusColumn)
                                                    <span
                                                        class="inline-flex items-center gap-2 rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700">
                                                        <span class="h-2 w-2 rounded-full bg-rose-500"></span>Inactive
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">N/A</span>
                                                @endif
                                            </td>
                                            <td class="student-col-created px-3 py-3 align-top text-slate-500">
                                                <span
                                                    class="whitespace-nowrap">{{ $account->created_at->format('M d, Y') }}</span>
                                            </td>
                                            <td class="student-col-actions whitespace-nowrap px-3 py-3 align-top">
                                                <div
                                                    class="flex flex-nowrap items-center justify-end gap-2 whitespace-nowrap">
                                                    <button @click="editing = {{ $account->id }}" type="button"
                                                        class="whitespace-nowrap rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                                        Edit
                                                    </button>

                                                    @if ($hasStatusColumn)
                                                        <form method="POST"
                                                            action="{{ route('admin.admin-staff.status', $account) }}"
                                                            class="js-status-form" data-account="{{ $account->name }}"
                                                            data-action="{{ $account->is_active ? 'set inactive' : 'set active' }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" {{ $isSelf ? 'disabled' : '' }}
                                                                class="whitespace-nowrap rounded-lg border px-3 py-1.5 text-xs font-semibold disabled:cursor-not-allowed disabled:opacity-50 {{ $account->is_active ? 'border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100' : 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                                                {{ $account->is_active ? 'Set Inactive' : 'Set Active' }}
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <form method="POST"
                                                        action="{{ route('admin.admin-staff.destroy', $account) }}"
                                                        class="js-delete-form" data-account="{{ $account->name }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" {{ $isSelf ? 'disabled' : '' }}
                                                            class="whitespace-nowrap rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100 disabled:cursor-not-allowed disabled:opacity-50">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>

                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ $hasPhoneColumn ?? false ? 7 : 6 }}"
                                                class="px-3 py-10 text-center text-sm text-slate-500">
                                                No admin or staff accounts found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @foreach ($accounts as $account)
                        @php
                            $isSelf = (int) auth()->id() === (int) $account->id;
                        @endphp
                        <div x-show="editing === {{ $account->id }}" x-cloak
                            class="fixed inset-0 z-[90] grid place-items-center p-4" aria-modal="true" role="dialog">
                            <div class="absolute inset-0 bg-slate-900/50" @click="editing = null"></div>

                            <div
                                class="relative z-10 flex w-full max-w-xl max-h-[calc(100vh-2rem)] flex-col overflow-hidden rounded-3xl bg-white shadow-2xl">
                                <div class="mb-4 flex items-center justify-between">
                                    <h3 class="ml-4 mt-1 text-lg font-black text-slate-900">Edit Account</h3>
                                    <button type="button" @click="editing = null"
                                        class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
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

                                    <div class="flex-1 space-y-4 overflow-y-auto overscroll-contain px-5 pb-4 pt-1">
                                        <div>
                                            <label for="edit_role_{{ $account->id }}"
                                                class="mb-1 block text-xs font-semibold text-slate-600">Role</label>
                                            <select id="edit_role_{{ $account->id }}" name="role"
                                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm capitalize outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                @foreach ($editRoles as $roleOption)
                                                    <option value="{{ $roleOption }}"
                                                        {{ $account->role === $roleOption ? 'selected' : '' }}>
                                                        {{ ucfirst($roleOption) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <label for="edit_name_{{ $account->id }}"
                                                class="mb-1 block text-xs font-semibold text-slate-600">Full Name</label>
                                            <input id="edit_name_{{ $account->id }}" name="name" type="text"
                                                value="{{ $account->name }}"
                                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                        </div>

                                        <div>
                                            <label for="edit_email_{{ $account->id }}"
                                                class="mb-1 block text-xs font-semibold text-slate-600">Email</label>
                                            <input id="edit_email_{{ $account->id }}" name="email" type="email"
                                                value="{{ $account->email }}"
                                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                        </div>

                                        @if ($hasPhoneColumn ?? false)
                                            <div>
                                                <label for="edit_phone_number_{{ $account->id }}"
                                                    class="mb-1 block text-xs font-semibold text-slate-600">Phone
                                                    Number</label>
                                                <input id="edit_phone_number_{{ $account->id }}" name="phone_number"
                                                    type="text" value="{{ $account->phone_number }}"
                                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                                                    placeholder="+855 12 345 678">
                                            </div>
                                        @endif

                                        <div>
                                            <label for="edit_avatar_image_{{ $account->id }}"
                                                class="mb-1 block text-xs font-semibold text-slate-600">Avatar
                                                Image</label>
                                            <input id="edit_avatar_image_{{ $account->id }}" name="avatar_image"
                                                type="file" accept="image/*"
                                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100 focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                            <p class="mt-1 text-[11px] text-slate-500">Leave empty to keep current avatar.
                                            </p>
                                        </div>

                                        <div class="grid gap-4 sm:grid-cols-2">
                                            <div>
                                                <label for="edit_password_{{ $account->id }}"
                                                    class="mb-1 block text-xs font-semibold text-slate-600">New
                                                    Password</label>
                                                <div class="relative">
                                                    <input id="edit_password_{{ $account->id }}" name="password"
                                                        type="password"
                                                        class="w-full rounded-xl border border-slate-200 px-3 py-2.5 pr-10 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                                                        placeholder="Leave blank to keep current">
                                                    <button type="button"
                                                        onclick="toggleAdminStaffPassword('edit_password_{{ $account->id }}', this)"
                                                        class="absolute inset-y-0 right-3 flex items-center text-slate-400 transition hover:text-slate-700"
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
                                                    class="mb-1 block text-xs font-semibold text-slate-600">Confirm
                                                    Password</label>
                                                <div class="relative">
                                                    <input id="edit_password_confirmation_{{ $account->id }}"
                                                        name="password_confirmation" type="password"
                                                        class="w-full rounded-xl border border-slate-200 px-3 py-2.5 pr-10 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                    <button type="button"
                                                        onclick="toggleAdminStaffPassword('edit_password_confirmation_{{ $account->id }}', this)"
                                                        class="absolute inset-y-0 right-3 flex items-center text-slate-400 transition hover:text-slate-700"
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
                                                class="flex items-center justify-between rounded-xl border border-slate-200 px-3 py-2.5">
                                                <span class="text-sm font-semibold text-slate-700">Status</span>
                                                <span
                                                    class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500">
                                                    <input type="checkbox" name="is_active" value="1"
                                                        class="h-4 w-4 rounded border-slate-300"
                                                        {{ $account->is_active ? 'checked' : '' }}
                                                        {{ $isSelf ? 'disabled' : '' }}>
                                                    Active
                                                </span>
                                            </label>
                                        @endif
                                    </div>

                                    <div class="flex justify-end gap-2 border-t border-slate-100 px-5 py-4">
                                        <button type="button" @click="editing = null"
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
                    @endforeach

                    <div>
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
