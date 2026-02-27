@extends('layout.admin.navbar.navbar')

@section('page')
    <div class="stage space-y-6">
        <x-admin.page-header reveal-class="reveal" delay="1" icon="settings" title="Admin Settings"
            subtitle="Manage your account profile and security settings.">
            <x-slot:stats>
                <span class="admin-page-stat">Students: {{ number_format($stats['students'] ?? 0) }}</span>
                <span class="admin-page-stat admin-page-stat--sky">Teachers:
                    {{ number_format($stats['teachers'] ?? 0) }}</span>
                <span class="admin-page-stat admin-page-stat--emerald">Classes:
                    {{ number_format($stats['classes'] ?? 0) }}</span>
                <span class="admin-page-stat admin-page-stat--amber">Subjects:
                    {{ number_format($stats['subjects'] ?? 0) }}</span>
            </x-slot:stats>
            <x-slot:actions>
                <a href="{{ route('admin.dashboard') }}"
                    class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                    Open Dashboard
                </a>
                <a href="{{ route('admin.contacts.index') }}"
                    class="rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100">
                    Unread Messages: {{ number_format($stats['unreadMessages'] ?? 0) }}
                </a>
            </x-slot:actions>
        </x-admin.page-header>

        @if (session('success'))
            <div class="reveal rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"
                style="--sd: 2;">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->profileUpdate->any())
            <div class="reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" style="--sd: 2;">
                {{ $errors->profileUpdate->first() }}
            </div>
        @endif

        @if ($errors->passwordUpdate->any())
            <div class="reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" style="--sd: 2;">
                {{ $errors->passwordUpdate->first() }}
            </div>
        @endif

        <div class="grid gap-6 xl:grid-cols-12">
            <section
                class="reveal float-card rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-5"
                style="--sd: 3;">
                <div class="mb-5 flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-3">
                    <img class="h-20 w-20 rounded-2xl border border-white/60 object-cover shadow-sm"
                        src="{{ $admin->avatar_url }}"
                        onerror="this.onerror=null;this.src='{{ $admin->fallback_avatar_url }}';" alt="avatar">
                    <div class="min-w-0">
                        <div class="truncate text-xl font-black text-slate-900">{{ $admin->name }}</div>
                        <div class="truncate text-sm text-slate-600">{{ $admin->email }}</div>
                        <div
                            class="mt-1 inline-flex rounded-full border border-indigo-200 bg-indigo-50 px-2.5 py-1 text-[11px] font-bold uppercase text-indigo-700">
                            {{ $admin->role }}
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.settings.profile.update') }}" enctype="multipart/form-data"
                    class="space-y-4 rounded-2xl border border-slate-200 bg-slate-50/80 p-4">
                    @csrf
                    @method('PUT')

                    <h2 class="text-sm font-black text-slate-900">Profile Details</h2>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">Full Name</label>
                        <input type="text" name="name" value="{{ old('name', $admin->name) }}" required
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">Email</label>
                        <input type="email" name="email" value="{{ old('email', $admin->email) }}" required
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">Avatar</label>
                        <input type="file" name="avatar" accept="image/*"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                        <p class="mt-1 text-[11px] text-slate-500">JPG, PNG, WEBP up to 4MB.</p>
                    </div>

                    <button type="submit"
                        class="inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-indigo-500">
                        Save Profile
                    </button>
                </form>
            </section>

            <section
                class="reveal float-card rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-7"
                style="--sd: 4;">
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    <article class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-3">
                        <div class="text-xs font-semibold text-slate-500">Class Slots</div>
                        <div class="mt-1 text-lg font-black text-slate-900">{{ number_format($stats['classSlots'] ?? 0) }}
                        </div>
                    </article>
                    <article class="rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-3">
                        <div class="text-xs font-semibold text-indigo-600">Subject Slots</div>
                        <div class="mt-1 text-lg font-black text-indigo-800">
                            {{ number_format($stats['subjectSlots'] ?? 0) }}</div>
                    </article>
                    <article class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-3">
                        <div class="text-xs font-semibold text-amber-600">Unread Messages</div>
                        <div class="mt-1 text-lg font-black text-amber-800">
                            {{ number_format($stats['unreadMessages'] ?? 0) }}</div>
                    </article>
                    <article class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-3">
                        <div class="text-xs font-semibold text-emerald-600">Unread Notifications</div>
                        <div class="mt-1 text-lg font-black text-emerald-800">
                            {{ number_format($stats['unreadNotifications'] ?? 0) }}</div>
                    </article>
                </div>

                <form method="POST" action="{{ route('admin.settings.password.update') }}"
                    class="mt-5 space-y-4 rounded-2xl border border-slate-200 bg-slate-50/80 p-4">
                    @csrf
                    @method('PUT')

                    <h2 class="text-sm font-black text-slate-900">Change Password</h2>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">Current Password</label>
                        <input type="password" name="current_password" required
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-600">New Password</label>
                            <input type="password" name="password" required
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-600">Confirm Password</label>
                            <input type="password" name="password_confirmation" required
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                        </div>
                    </div>

                    <button type="submit"
                        class="inline-flex w-full items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-slate-800 sm:w-auto">
                        Update Password
                    </button>
                </form>
            </section>
        </div>
    </div>
@endsection
