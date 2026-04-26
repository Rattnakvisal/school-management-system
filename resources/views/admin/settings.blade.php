@extends('layout.admin.navbar.navbar')

@section('page')
    @php
        $nameParts = preg_split('/\s+/', trim((string) $admin->name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $defaultFirstName = $nameParts[0] ?? '';
        $defaultLastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';
        $profileErrors = $errors->profileUpdate;
        $passwordErrors = $errors->passwordUpdate;
        $homePageErrors = $errors->homePageUpdate;
        $aboutPageErrors = $errors->aboutPageUpdate;
        $featurePageErrors = $errors->featurePageUpdate;
        $settingsDefaultTab = session(
            'settings_tab',
            old(
                '_settings_tab',
                $featurePageErrors->any()
                    ? 'feature-page'
                    : ($aboutPageErrors->any()
                        ? 'about-page'
                        : ($homePageErrors->any()
                            ? 'home-page'
                            : ($passwordErrors->any()
                                ? 'password'
                                : 'profile'))),
            ),
        );
        $homePageItems = $homePageItems ?? collect();
        $homeHero = $homePageItems->get('hero', collect())->firstWhere('key', 'main');
        $homePoints = $homePageItems->get('hero_points', collect())->values();
        $homeStats = $homePageItems->get('hero_stats', collect())->values();
        $homeFeatures = $homePageItems->get('hero_features', collect())->values();
        $homeAbout = $homePageItems->get('about', collect())->firstWhere('key', 'main');
        $homeAboutHighlights = $homePageItems->get('about_highlights', collect())->values();
        $homeAboutCards = $homePageItems->get('about_cards', collect())->values();
        $platformFeature = $homePageItems->get('platform_features', collect())->firstWhere('key', 'main');
        $platformFeatureCards = $homePageItems->get('platform_feature_cards', collect())->values();
        $pointLimit = 6;
        $statLimit = 6;
        $heroFeatureLimit = 8;
        $aboutHighlightLimit = 4;
        $aboutCardLimit = 8;
        $platformFeatureLimit = 8;
        $homePageImage = $homeHero?->image_path
            ? route('public.storage', ['path' => $homeHero->image_path])
            : asset('images/school.jpg');
    @endphp

    <div id="admin-settings-page" class="stage space-y-6" data-settings-default-tab="{{ $settingsDefaultTab }}">
        <x-admin.page-header reveal-class="reveal" delay="1" icon="settings" title="Admin Settings"
            subtitle="Manage profile details, avatar, and account security.">
            <x-slot:actions>
                <a href="{{ route('admin.dashboard') }}"
                    class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                    Open Dashboard
                </a>
            </x-slot:actions>
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

        @if ($profileErrors->any())
            <div class="reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" style="--sd: 2;">
                {{ $profileErrors->first() }}
            </div>
        @endif

        @if ($passwordErrors->any())
            <div class="reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" style="--sd: 2;">
                {{ $passwordErrors->first() }}
            </div>
        @endif

        @if ($homePageErrors->any())
            <div class="reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" style="--sd: 2;">
                {{ $homePageErrors->first() }}
            </div>
        @endif

        @if ($aboutPageErrors->any())
            <div class="reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" style="--sd: 2;">
                {{ $aboutPageErrors->first() }}
            </div>
        @endif

        @if ($featurePageErrors->any())
            <div class="reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" style="--sd: 2;">
                {{ $featurePageErrors->first() }}
            </div>
        @endif

        <section class="reveal rounded-3xl border border-slate-200 bg-white p-4 shadow-sm ring-1 ring-slate-100 sm:p-6"
            style="--sd: 3;">
            <div class="grid gap-6 xl:grid-cols-[220px_minmax(0,1fr)]">
                <aside class="rounded-2xl border border-slate-200 bg-slate-50 p-3">
                    <div class="space-y-2">
                        <button type="button" data-settings-nav="profile"
                            class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                            <span class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M12 12c2.21 0 4-1.79 4-4S14.21 4 12 4 8 5.79 8 8s1.79 4 4 4Zm0 2c-3.33 0-8 1.67-8 5v1h16v-1c0-3.33-4.67-5-8-5Z" />
                                </svg>
                                Profile Settings
                            </span>
                        </button>
                        <button type="button" data-settings-nav="password"
                            class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                            <span class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M17 9h-1V7a4 4 0 1 0-8 0v2H7a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-8a2 2 0 0 0-2-2Zm-7-2a2 2 0 1 1 4 0v2h-4V7Zm2 10a2 2 0 1 1 0-4 2 2 0 0 1 0 4Z" />
                                </svg>
                                Password
                            </span>
                        </button>
                        <button type="button" data-settings-nav="notifications"
                            class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                            <span class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M12 2a6 6 0 0 0-6 6v3.59L4.71 13.3A1 1 0 0 0 5.41 15h13.18a1 1 0 0 0 .7-1.7L18 11.59V8a6 6 0 0 0-6-6Zm0 20a3 3 0 0 0 2.82-2H9.18A3 3 0 0 0 12 22Z" />
                                </svg>
                                Notifications
                            </span>
                        </button>
                        <button type="button" data-settings-nav="home-page"
                            class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                            <span class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M4 5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v14a1 1 0 0 1-1.45.89L12 16.62l-6.55 3.27A1 1 0 0 1 4 19V5Zm2 0v10.76l5.11-2.55a2 2 0 0 1 1.78 0L18 15.76V5H6Z" />
                                </svg>
                                Hero Page
                            </span>
                        </button>
                        <button type="button" data-settings-nav="about-page"
                            class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                            <span class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M4 4h16v2H4V4Zm0 4h10v2H4V8Zm0 4h16v8H4v-8Zm2 2v4h12v-4H6Z" />
                                </svg>
                                About Page
                            </span>
                        </button>
                        <button type="button" data-settings-nav="feature-page"
                            class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                            <span class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm2 4v4h4V7H7Zm6 0v4h4V7h-4ZM7 13v4h4v-4H7Zm6 0v4h4v-4h-4Z" />
                                </svg>
                                Feature Page
                            </span>
                        </button>
                        <button type="button" data-settings-nav="verification"
                            class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                            <span class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="m10.56 17.36 7.78-7.78-1.42-1.42-6.36 6.36-2.84-2.83-1.41 1.41 4.25 4.26Zm1.44-15.3 7 3v5c0 5.25-3.5 10.17-7 11.94-3.5-1.77-7-6.69-7-11.94v-5l7-3Z" />
                                </svg>
                                Verification
                            </span>
                        </button>
                    </div>
                </aside>

                <div class="min-w-0">
                    <section data-settings-panel="profile" class="space-y-6">
                        <form method="POST" action="{{ route('admin.settings.profile.update') }}"
                            enctype="multipart/form-data"
                            class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 sm:p-6">
                            @csrf
                            @method('PUT')

                            <input type="hidden" id="remove_admin_avatar" name="remove_avatar" value="0">

                            <div class="flex flex-wrap items-center gap-4 border-b border-slate-200 pb-5">
                                <div class="relative">
                                    <img id="admin_avatar_preview"
                                        class="h-24 w-24 rounded-full border-4 border-white object-cover shadow-sm"
                                        src="{{ $admin->avatar_url }}" data-fallback="{{ $admin->fallback_avatar_url }}"
                                        onerror="this.onerror=null;this.src='{{ $admin->fallback_avatar_url }}';"
                                        alt="admin avatar">
                                    <button type="button" id="trigger_avatar_upload"
                                        class="absolute -bottom-1 -right-1 grid h-8 w-8 place-items-center rounded-full border-2 border-white bg-indigo-600 text-white shadow transition hover:bg-indigo-500"
                                        aria-label="Upload avatar">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6Z" />
                                            <path
                                                d="M20 4h-3.17L15.6 2.35A2 2 0 0 0 14.06 2H9.94A2 2 0 0 0 8.4 2.35L7.17 4H4a2 2 0 0 0-2 2v10a4 4 0 0 0 4 4h12a4 4 0 0 0 4-4V6a2 2 0 0 0-2-2Zm-8 13a5 5 0 1 1 0-10 5 5 0 0 1 0 10Z" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="flex flex-wrap items-center gap-2">
                                    <input id="admin_avatar_input" type="file" name="avatar" accept="image/*"
                                        class="hidden">
                                    <button type="button" id="upload_avatar_btn"
                                        class="rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-indigo-500">
                                        Upload New
                                    </button>
                                    <button type="button" id="delete_avatar_btn"
                                        class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">
                                        Delete avatar
                                    </button>
                                    <span class="text-xs text-slate-500">JPG, PNG, WEBP up to 4MB.</span>
                                </div>
                            </div>

                            <div class="mt-5 grid gap-4 md:grid-cols-2">
                                <div>
                                    <label for="first_name" class="mb-1 block text-xs font-semibold text-slate-600">First
                                        Name
                                        <span class="text-red-500">*</span></label>
                                    <input id="first_name" name="first_name" type="text"
                                        value="{{ old('first_name', $defaultFirstName) }}" required
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                    @error('first_name', 'profileUpdate')
                                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="last_name" class="mb-1 block text-xs font-semibold text-slate-600">Last
                                        Name</label>
                                    <input id="last_name" name="last_name" type="text"
                                        value="{{ old('last_name', $defaultLastName) }}"
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                    @error('last_name', 'profileUpdate')
                                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email"
                                        class="mb-1 block text-xs font-semibold text-slate-600">Email</label>
                                    <input id="email" name="email" type="email"
                                        value="{{ old('email', $admin->email) }}" required
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                    @error('email', 'profileUpdate')
                                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone_number"
                                        class="mb-1 block text-xs font-semibold text-slate-600">Mobile
                                        Number</label>
                                    <input id="phone_number" name="phone_number" type="text"
                                        value="{{ old('phone_number', $admin->phone_number) }}"
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                                        placeholder="+855 ...">
                                    @error('phone_number', 'profileUpdate')
                                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-600">Role</label>
                                    <input type="text" readonly value="{{ ucfirst((string) $admin->role) }}"
                                        class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-600">
                                </div>

                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-600">ID</label>
                                    <input type="text" readonly value="ID #{{ $admin->formatted_id }}"
                                        class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-600">
                                </div>
                            </div>

                            <div class="mt-6">
                                <button type="submit"
                                    class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </section>

                    <section data-settings-panel="password" class="hidden space-y-6">
                        <form method="POST" action="{{ route('admin.settings.password.update') }}"
                            class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 sm:p-6">
                            @csrf
                            @method('PUT')

                            <h2 class="text-lg font-semibold text-slate-900">Change Password</h2>
                            <p class="mt-1 text-sm text-slate-500">Use a strong password with at least 8 characters.</p>

                            <div class="mt-5 space-y-4">
                                <div>
                                    <label for="current_password"
                                        class="mb-1 block text-xs font-semibold text-slate-600">Current
                                        Password</label>
                                    <input id="current_password" type="password" name="current_password" required
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                    @error('current_password', 'passwordUpdate')
                                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label for="password" class="mb-1 block text-xs font-semibold text-slate-600">New
                                            Password</label>
                                        <input id="password" type="password" name="password" required
                                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                        @error('password', 'passwordUpdate')
                                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="password_confirmation"
                                            class="mb-1 block text-xs font-semibold text-slate-600">Confirm
                                            Password</label>
                                        <input id="password_confirmation" type="password" name="password_confirmation"
                                            required
                                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6">
                                <button type="submit"
                                    class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </section>

                    <section data-settings-panel="notifications" class="hidden">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-6">
                            <h2 class="text-lg font-semibold text-slate-900">Notifications</h2>
                            <p class="mt-2 text-sm text-slate-600">Unread notifications:
                                <span
                                    class="font-semibold text-indigo-700">{{ number_format($stats['unreadNotifications'] ?? 0) }}</span>
                            </p>
                            <p class="mt-1 text-sm text-slate-500">This section is ready for notification preferences.</p>
                        </div>
                    </section>

                    <section data-settings-panel="home-page" class="hidden space-y-6">
                        @if ($homePageReady ?? false)
                            <form method="POST" action="{{ route('admin.settings.home-page.update') }}"
                                enctype="multipart/form-data" class="space-y-5">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="_settings_tab" value="home-page">
                                <input type="hidden" id="remove_home_hero_image" name="hero[remove_image]"
                                    value="0">

                                <div
                                    class="overflow-hidden rounded-2xl border border-slate-200 bg-gradient-to-br from-white via-blue-50/60 to-slate-50 shadow-sm">
                                    <div class="grid gap-0 xl:grid-cols-[minmax(0,1fr)_360px]">
                                        <div class="p-4 sm:p-6">
                                            <div class="flex flex-wrap items-start justify-between gap-4">
                                                <div>
                                                    <span
                                                        class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-blue-700">
                                                        Website content
                                                    </span>
                                                    <h2 class="mt-3 text-xl font-black text-slate-950">Hero Page Builder
                                                    </h2>
                                                    <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-500">
                                                        Edit the homepage hero content, image, stats, and feature strip from
                                                        one screen.
                                                    </p>
                                                </div>
                                                <div class="flex flex-wrap gap-2">
                                                    <a href="{{ route('home') }}" target="_blank"
                                                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50">
                                                        View Home
                                                    </a>
                                                    <button type="submit"
                                                        class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-indigo-500">
                                                        Save Changes
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="mt-6 grid gap-4 md:grid-cols-2">
                                                <div class="md:col-span-2">
                                                    <label for="home_hero_title"
                                                        class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Main
                                                        headline</label>
                                                    <input id="home_hero_title" name="hero[title]" type="text"
                                                        data-home-preview-target="home_preview_title"
                                                        value="{{ old('hero.title', $homeHero?->title ?? 'A Smarter school experience for Every Students, Every Day.') }}"
                                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                </div>
                                                <div>
                                                    <label for="home_hero_badge"
                                                        class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Top
                                                        badge</label>
                                                    <input id="home_hero_badge" name="hero[badge]" type="text"
                                                        data-home-preview-target="home_preview_badge"
                                                        value="{{ old('hero.badge', $homeHero?->subtitle ?? 'Future-ready school platform') }}"
                                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                </div>
                                                <div>
                                                    <label
                                                        class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Hero
                                                        image</label>
                                                    <input id="home_hero_image_input" type="file" name="hero[image]"
                                                        accept="image/*" class="hidden">
                                                    <div class="flex flex-wrap gap-2">
                                                        <button type="button" id="upload_home_hero_image_btn"
                                                            class="rounded-xl bg-slate-900 px-4 py-3 text-xs font-bold text-white transition hover:bg-slate-800">
                                                            Upload Image
                                                        </button>
                                                        <button type="button" id="delete_home_hero_image_btn"
                                                            class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs font-bold text-slate-700 transition hover:bg-slate-100">
                                                            Use Default
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="md:col-span-2">
                                                    <label for="home_hero_description"
                                                        class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Intro
                                                        paragraph</label>
                                                    <textarea id="home_hero_description" name="hero[description]" rows="4"
                                                        data-home-preview-target="home_preview_description"
                                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm leading-6 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ old('hero.description', $homeHero?->description ?? 'TechBridge brings academics, admissions, attendance, communication, and daily school operations together in one seamless platform for students, parents, teachers, and school leaders.') }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <aside
                                            class="border-t border-slate-200 bg-slate-950 p-4 text-white xl:border-l xl:border-t-0">
                                            <div class="rounded-2xl bg-white p-3 text-slate-950 shadow-lg">
                                                <img id="home_hero_image_preview" src="{{ $homePageImage }}"
                                                    class="h-44 w-full rounded-xl object-cover" alt="home hero preview">
                                                <div class="mt-4">
                                                    <p id="home_preview_badge"
                                                        class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-600">
                                                        {{ old('hero.badge', $homeHero?->subtitle ?? 'Future-ready school platform') }}
                                                    </p>
                                                    <h3 id="home_preview_title"
                                                        class="mt-2 text-2xl font-black leading-tight tracking-tight text-slate-950">
                                                        {{ old('hero.title', $homeHero?->title ?? 'A Smarter school experience for Every Students, Every Day.') }}
                                                    </h3>
                                                    <p id="home_preview_description"
                                                        class="mt-3 line-clamp-4 text-xs leading-5 text-slate-500">
                                                        {{ old('hero.description', $homeHero?->description ?? 'TechBridge brings academics, admissions, attendance, communication, and daily school operations together in one seamless platform for students, parents, teachers, and school leaders.') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </aside>
                                    </div>
                                </div>

                                <div class="grid gap-5 xl:grid-cols-2">
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                        <div class="flex items-center justify-between gap-3">
                                            <div>
                                                <h3 class="text-base font-black text-slate-900">Check Points</h3>
                                                <p class="mt-1 text-xs text-slate-500">Three short benefits under the hero
                                                    button.</p>
                                            </div>
                                            <button type="button" data-add-home-card="hero-points"
                                                class="rounded-full bg-blue-600 px-3 py-1 text-xs font-bold text-white transition hover:bg-blue-500">
                                                Add Point
                                            </button>
                                        </div>
                                        <div class="mt-4 space-y-3">
                                            @for ($i = 0; $i < $pointLimit; $i++)
                                                @php
                                                    $pointItem = $homePoints->get($i);
                                                    $pointDescription = old(
                                                        'points.' . $i . '.description',
                                                        $pointItem?->description ?? '',
                                                    );
                                                    $pointIcon = old(
                                                        'points.' . $i . '.icon',
                                                        $pointItem?->icon ?? '',
                                                    );
                                                    $showPoint = filled($pointDescription);
                                                @endphp
                                                <div data-home-editor-card data-addable-card="hero-points"
                                                    class="{{ $showPoint ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                    <input type="hidden" name="points[{{ $i }}][id]"
                                                        value="{{ $pointItem?->id }}">
                                                    <input type="hidden" name="points[{{ $i }}][delete]"
                                                        data-home-delete-field value="0">
                                                    <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                                                        <span
                                                            class="flex items-center gap-2 text-xs font-bold text-slate-500">
                                                            <span
                                                                class="grid h-6 w-6 place-items-center rounded-full bg-blue-600 text-[11px] text-white">{{ $i + 1 }}</span>
                                                            Point {{ $i + 1 }}
                                                        </span>
                                                        <div data-home-card-actions
                                                            class="flex flex-wrap items-center gap-2">
                                                            <button type="button" data-home-edit-card
                                                                class="rounded-full border border-blue-200 bg-white px-3 py-1 text-xs font-bold text-blue-600 transition hover:bg-blue-50">
                                                                Edit
                                                            </button>
                                                            <label
                                                                class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                                                                <input type="hidden"
                                                                    name="points[{{ $i }}][active]"
                                                                    value="0">
                                                                <input type="checkbox"
                                                                    name="points[{{ $i }}][active]"
                                                                    data-home-active-toggle value="1"
                                                                    {{ old('points.' . $i . '.active', $pointItem?->is_active ?? true) ? 'checked' : '' }}
                                                                    class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                                Active
                                                            </label>
                                                            <button type="button" data-home-remove-card
                                                                class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600 transition hover:bg-red-50">
                                                                Remove
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <textarea name="points[{{ $i }}][description]" rows="2"
                                                        class="w-full resize-none rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm leading-6 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ $pointDescription }}</textarea>
                                                    <input name="points[{{ $i }}][icon]" type="text"
                                                        data-random-icon-input
                                                        placeholder='Icon name, <i class="fa-solid fa-school"></i>, or SVG'
                                                        value="{{ $pointIcon }}"
                                                        class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                </div>
                                            @endfor
                                        </div>
                                    </div>

                                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                        <div class="flex items-center justify-between gap-3">
                                            <div>
                                                <h3 class="text-base font-black text-slate-900">Floating Stats</h3>
                                                <p class="mt-1 text-xs text-slate-500">Cards shown over the hero image.</p>
                                            </div>
                                            <button type="button" data-add-home-card="hero-stats"
                                                class="rounded-full bg-emerald-600 px-3 py-1 text-xs font-bold text-white transition hover:bg-emerald-500">
                                                Add Stat
                                            </button>
                                        </div>
                                        <div class="mt-4 grid gap-3">
                                            @for ($i = 0; $i < $statLimit; $i++)
                                                @php
                                                    $statItem = $homeStats->get($i);
                                                    $statLabel = old(
                                                        'stats.' . $i . '.label',
                                                        $statItem?->title ?? '',
                                                    );
                                                    $statValue = old(
                                                        'stats.' . $i . '.value',
                                                        $statItem?->value ?? '',
                                                    );
                                                    $statTrend = old(
                                                        'stats.' . $i . '.trend',
                                                        $statItem?->subtitle ?? '',
                                                    );
                                                    $statIcon = old(
                                                        'stats.' . $i . '.icon',
                                                        $statItem?->icon ??
                                                            match ($i) {
                                                                0 => 'graduation',
                                                                1 => 'chart',
                                                                2 => 'users',
                                                                default => 'clock',
                                                            },
                                                    );
                                                    $statColor = old(
                                                        'stats.' . $i . '.color',
                                                        $statItem?->color ??
                                                            match ($i) {
                                                                0 => 'emerald',
                                                                1 => 'blue',
                                                                2 => 'violet',
                                                                3 => 'amber',
                                                                4 => 'rose',
                                                                default => 'cyan',
                                                            },
                                                    );
                                                    $showStat =
                                                        filled($statLabel) ||
                                                        filled($statValue) ||
                                                        filled($statTrend);
                                                @endphp
                                                <div data-home-editor-card data-addable-card="hero-stats"
                                                    class="{{ $showStat ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                    <input type="hidden" name="stats[{{ $i }}][id]"
                                                        value="{{ $statItem?->id }}">
                                                    <input type="hidden" name="stats[{{ $i }}][delete]"
                                                        data-home-delete-field value="0">
                                                    <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                                                        <span
                                                            class="flex items-center gap-2 text-xs font-bold text-slate-500">
                                                            <span
                                                                class="grid h-6 w-6 place-items-center rounded-full bg-emerald-500 text-[11px] text-white">{{ $i + 1 }}</span>
                                                            Stat card {{ $i + 1 }}
                                                        </span>
                                                        <div data-home-card-actions
                                                            class="flex flex-wrap items-center gap-2">
                                                            <button type="button" data-home-edit-card
                                                                class="rounded-full border border-blue-200 bg-white px-3 py-1 text-xs font-bold text-blue-600 transition hover:bg-blue-50">
                                                                Edit
                                                            </button>
                                                            <label
                                                                class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                                                                <input type="hidden"
                                                                    name="stats[{{ $i }}][active]"
                                                                    value="0">
                                                                <input type="checkbox"
                                                                    name="stats[{{ $i }}][active]"
                                                                    data-home-active-toggle value="1"
                                                                    {{ old('stats.' . $i . '.active', $statItem?->is_active ?? true) ? 'checked' : '' }}
                                                                    class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                                Active
                                                            </label>
                                                            <button type="button" data-home-remove-card
                                                                class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600 transition hover:bg-red-50">
                                                                Remove
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="grid gap-2 sm:grid-cols-[minmax(0,1fr)_96px_130px]">
                                                        <input name="stats[{{ $i }}][label]" type="text"
                                                            placeholder="Label" value="{{ $statLabel }}"
                                                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                        <input name="stats[{{ $i }}][value]" type="text"
                                                            placeholder="Value" value="{{ $statValue }}"
                                                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                        <input name="stats[{{ $i }}][trend]" type="text"
                                                            placeholder="+6% this year" value="{{ $statTrend }}"
                                                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                    </div>
                                                    <div class="mt-2 grid gap-2 sm:grid-cols-[minmax(0,1fr)_150px]">
                                                        <div>
                                                            <label
                                                                class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">
                                                                Icon
                                                            </label>
                                                            <input name="stats[{{ $i }}][icon]" type="text"
                                                                data-stat-icon-select data-random-icon-input
                                                                placeholder='Icon name, <i class="fa-solid fa-school"></i>, or SVG'
                                                                value="{{ $statIcon }}"
                                                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                        </div>
                                                        <div>
                                                            <label
                                                                class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">
                                                                Color
                                                            </label>
                                                            <select name="stats[{{ $i }}][color]"
                                                                data-stat-color-select
                                                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                                @foreach ([
            'emerald' => 'Emerald',
            'blue' => 'Blue',
            'violet' => 'Violet',
            'amber' => 'Amber',
            'rose' => 'Rose',
            'cyan' => 'Cyan',
        ] as $colorValue => $colorLabel)
                                                                    <option value="{{ $colorValue }}"
                                                                        @selected($statColor === $colorValue)>
                                                                        {{ $colorLabel }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <div>
                                            <h3 class="text-base font-black text-slate-900">Bottom Feature Strip</h3>
                                            <p class="mt-1 text-xs text-slate-500">Four feature cards displayed below the
                                                hero header.</p>
                                        </div>
                                        <button type="button" data-add-home-card="hero-features"
                                            class="rounded-full bg-violet-600 px-3 py-1 text-xs font-bold text-white transition hover:bg-violet-500">
                                            Add Feature
                                        </button>
                                    </div>
                                    <div class="mt-4 grid gap-3 xl:grid-cols-2">
                                        @for ($i = 0; $i < $heroFeatureLimit; $i++)
                                            @php
                                                $heroFeatureItem = $homeFeatures->get($i);
                                                $heroFeatureTitle = old(
                                                    'features.' . $i . '.title',
                                                    $heroFeatureItem?->title ?? '',
                                                );
                                                $heroFeatureDescription = old(
                                                    'features.' . $i . '.description',
                                                    $heroFeatureItem?->description ?? '',
                                                );
                                                $heroFeatureIcon = old(
                                                    'features.' . $i . '.icon',
                                                    $heroFeatureItem?->icon ?? '',
                                                );
                                                $showHeroFeature =
                                                    filled($heroFeatureTitle) ||
                                                    filled($heroFeatureDescription);
                                            @endphp
                                            <div data-home-editor-card data-addable-card="hero-features"
                                                class="{{ $showHeroFeature ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                <input type="hidden" name="features[{{ $i }}][id]"
                                                    value="{{ $heroFeatureItem?->id }}">
                                                <input type="hidden" name="features[{{ $i }}][delete]"
                                                    data-home-delete-field value="0">
                                                <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                                                    <span class="flex items-center gap-2 text-xs font-bold text-slate-500">
                                                        <span
                                                            class="grid h-6 w-6 place-items-center rounded-full bg-violet-600 text-[11px] text-white">{{ $i + 1 }}</span>
                                                        Feature {{ $i + 1 }}
                                                    </span>
                                                    <div data-home-card-actions class="flex flex-wrap items-center gap-2">
                                                        <button type="button" data-home-edit-card
                                                            class="rounded-full border border-blue-200 bg-white px-3 py-1 text-xs font-bold text-blue-600 transition hover:bg-blue-50">
                                                            Edit
                                                        </button>
                                                        <label
                                                            class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                                                            <input type="hidden"
                                                                name="features[{{ $i }}][active]"
                                                                value="0">
                                                            <input type="checkbox"
                                                                name="features[{{ $i }}][active]"
                                                                data-home-active-toggle value="1"
                                                                {{ old('features.' . $i . '.active', $heroFeatureItem?->is_active ?? true) ? 'checked' : '' }}
                                                                class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                            Active
                                                        </label>
                                                        <button type="button" data-home-remove-card
                                                            class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600 transition hover:bg-red-50">
                                                            Remove
                                                        </button>
                                                    </div>
                                                </div>
                                                <input name="features[{{ $i }}][title]" type="text"
                                                    placeholder="Feature title" value="{{ $heroFeatureTitle }}"
                                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                <textarea name="features[{{ $i }}][description]" rows="3" placeholder="Feature description"
                                                    class="mt-2 w-full resize-none rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm leading-6 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ $heroFeatureDescription }}</textarea>
                                                <input name="features[{{ $i }}][icon]" type="text"
                                                    data-random-icon-input
                                                    placeholder='Icon name, <i class="fa-solid fa-school"></i>, or SVG'
                                                    value="{{ $heroFeatureIcon }}"
                                                    class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                            </div>
                                        @endfor
                                    </div>
                                </div>

                                <div class="sticky bottom-4 z-10 flex justify-end">
                                    <div
                                        class="rounded-2xl border border-slate-200 bg-white/95 p-2 shadow-xl backdrop-blur">
                                        <button type="submit"
                                            class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-indigo-500">
                                            Save Hero Page
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-800">
                                Run migrations to enable dynamic homepage editing.
                            </div>
                        @endif
                    </section>

                    <section data-settings-panel="about-page" class="hidden space-y-6">
                        @if ($homePageReady ?? false)
                            <form method="POST" action="{{ route('admin.settings.about-page.update') }}"
                                class="space-y-5">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="_settings_tab" value="about-page">

                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div>
                                            <span
                                                class="inline-flex rounded-full bg-cyan-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-cyan-700">
                                                About section
                                            </span>
                                            <h3 class="mt-3 text-base font-black text-slate-900">About Content</h3>
                                            <p class="mt-1 text-xs text-slate-500">Main About text, highlights, mission,
                                                vision, culture, and operations cards.</p>
                                        </div>
                                        <span
                                            class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">Visible
                                            on homepage</span>
                                    </div>

                                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                                        <div>
                                            <label for="home_about_badge"
                                                class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Badge</label>
                                            <input id="home_about_badge" name="about[badge]" type="text"
                                                value="{{ old('about.badge', $homeAbout?->subtitle ?? 'About TechBridge') }}"
                                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                        </div>
                                        <div>
                                            <label for="home_about_title"
                                                class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Headline</label>
                                            <input id="home_about_title" name="about[title]" type="text"
                                                value="{{ old('about.title', $homeAbout?->title ?? __('home.about.title')) }}"
                                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label for="home_about_description"
                                                class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Description</label>
                                            <textarea id="home_about_description" name="about[description]" rows="4"
                                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm leading-6 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ old('about.description', $homeAbout?->description ?? __('home.about.description')) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid gap-5 xl:grid-cols-2">
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                        <div class="flex items-center justify-between gap-3">
                                            <div>
                                                <h3 class="text-base font-black text-slate-900">About Highlights</h3>
                                                <p class="mt-1 text-xs text-slate-500">Small cards inside the dark
                                                    About panel.</p>
                                            </div>
                                            <button type="button" data-add-home-card="about-highlights"
                                                class="rounded-full bg-cyan-600 px-3 py-1 text-xs font-bold text-white transition hover:bg-cyan-500">
                                                Add Highlight
                                            </button>
                                        </div>
                                        <div class="mt-4 space-y-3">
                                            @for ($i = 0; $i < $aboutHighlightLimit; $i++)
                                                @php
                                                    $aboutHighlightItem = $homeAboutHighlights->get($i);
                                                    $aboutHighlightTitle = old('about_highlights.' . $i . '.title', $aboutHighlightItem?->title ?? '');
                                                    $aboutHighlightDescription = old('about_highlights.' . $i . '.description', $aboutHighlightItem?->description ?? '');
                                                    $aboutHighlightIcon = old('about_highlights.' . $i . '.icon', $aboutHighlightItem?->icon ?? '');
                                                    $showAboutHighlight = filled($aboutHighlightTitle) || filled($aboutHighlightDescription);
                                                @endphp
                                                <div data-home-editor-card data-addable-card="about-highlights"
                                                    class="{{ $showAboutHighlight ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                    <input type="hidden" name="about_highlights[{{ $i }}][id]"
                                                        value="{{ $aboutHighlightItem?->id }}">
                                                    <input type="hidden"
                                                        name="about_highlights[{{ $i }}][delete]"
                                                        data-home-delete-field value="0">
                                                    <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                                                        <span
                                                            class="flex items-center gap-2 text-xs font-bold text-slate-500">
                                                            <span
                                                                class="grid h-6 w-6 place-items-center rounded-full bg-cyan-600 text-[11px] text-white">{{ $i + 1 }}</span>
                                                            Highlight {{ $i + 1 }}
                                                        </span>
                                                        <div data-home-card-actions
                                                            class="flex flex-wrap items-center gap-2">
                                                            <button type="button" data-home-edit-card
                                                                class="rounded-full border border-blue-200 bg-white px-3 py-1 text-xs font-bold text-blue-600 transition hover:bg-blue-50">
                                                                Edit
                                                            </button>
                                                            <label
                                                                class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                                                                <input type="hidden"
                                                                    name="about_highlights[{{ $i }}][active]"
                                                                    value="0">
                                                                <input type="checkbox"
                                                                    name="about_highlights[{{ $i }}][active]"
                                                                    data-home-active-toggle value="1"
                                                                    {{ old('about_highlights.' . $i . '.active', $aboutHighlightItem?->is_active ?? true) ? 'checked' : '' }}
                                                                    class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                                Active
                                                            </label>
                                                            <button type="button" data-home-remove-card
                                                                class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600 transition hover:bg-red-50">
                                                                Remove
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <input name="about_highlights[{{ $i }}][title]"
                                                        type="text" placeholder="Highlight title"
                                                        value="{{ $aboutHighlightTitle }}"
                                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                    <textarea name="about_highlights[{{ $i }}][description]" rows="3"
                                                        placeholder="Highlight description"
                                                        class="mt-2 w-full resize-none rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm leading-6 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ $aboutHighlightDescription }}</textarea>
                                                    <input name="about_highlights[{{ $i }}][icon]" type="text"
                                                        data-random-icon-input
                                                        placeholder='Icon name, <i class="fa-solid fa-school"></i>, or SVG'
                                                        value="{{ $aboutHighlightIcon }}"
                                                        class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                </div>
                                            @endfor
                                        </div>
                                    </div>

                                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                        <div class="flex items-center justify-between gap-3">
                                            <div>
                                                <h3 class="text-base font-black text-slate-900">About Cards</h3>
                                                <p class="mt-1 text-xs text-slate-500">Cards shown on the right side of the
                                                    About section.</p>
                                            </div>
                                            <button type="button" data-add-home-card="about-cards"
                                                class="rounded-full bg-emerald-600 px-3 py-1 text-xs font-bold text-white transition hover:bg-emerald-500">
                                                Add Card
                                            </button>
                                        </div>
                                        <div class="mt-4 grid gap-3">
                                            @for ($i = 0; $i < $aboutCardLimit; $i++)
                                                @php
                                                    $aboutCardItem = $homeAboutCards->get($i);
                                                    $aboutCardTitle = old('about_cards.' . $i . '.title', $aboutCardItem?->title ?? '');
                                                    $aboutCardDescription = old('about_cards.' . $i . '.description', $aboutCardItem?->description ?? '');
                                                    $aboutCardIcon = old('about_cards.' . $i . '.icon', $aboutCardItem?->icon ?? '');
                                                    $showAboutCard = filled($aboutCardTitle) || filled($aboutCardDescription);
                                                @endphp
                                                <div data-home-editor-card data-addable-card="about-cards"
                                                    class="{{ $showAboutCard ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                    <input type="hidden" name="about_cards[{{ $i }}][id]"
                                                        value="{{ $aboutCardItem?->id }}">
                                                    <input type="hidden" name="about_cards[{{ $i }}][delete]"
                                                        data-home-delete-field value="0">
                                                    <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                                                        <span
                                                            class="flex items-center gap-2 text-xs font-bold text-slate-500">
                                                            <span
                                                                class="grid h-6 w-6 place-items-center rounded-full bg-emerald-500 text-[11px] text-white">{{ $i + 1 }}</span>
                                                            About card {{ $i + 1 }}
                                                        </span>
                                                        <div data-home-card-actions
                                                            class="flex flex-wrap items-center gap-2">
                                                            <button type="button" data-home-edit-card
                                                                class="rounded-full border border-blue-200 bg-white px-3 py-1 text-xs font-bold text-blue-600 transition hover:bg-blue-50">
                                                                Edit
                                                            </button>
                                                            <label
                                                                class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                                                                <input type="hidden"
                                                                    name="about_cards[{{ $i }}][active]"
                                                                    value="0">
                                                                <input type="checkbox"
                                                                    name="about_cards[{{ $i }}][active]"
                                                                    data-home-active-toggle value="1"
                                                                    {{ old('about_cards.' . $i . '.active', $aboutCardItem?->is_active ?? true) ? 'checked' : '' }}
                                                                    class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                                Active
                                                            </label>
                                                            <button type="button" data-home-remove-card
                                                                class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600 transition hover:bg-red-50">
                                                                Remove
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <input name="about_cards[{{ $i }}][title]" type="text"
                                                        placeholder="Card title"
                                                        value="{{ $aboutCardTitle }}"
                                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                    <textarea name="about_cards[{{ $i }}][description]" rows="3" placeholder="Card description"
                                                        class="mt-2 w-full resize-none rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm leading-6 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ $aboutCardDescription }}</textarea>
                                                    <input name="about_cards[{{ $i }}][icon]" type="text"
                                                        data-random-icon-input
                                                        placeholder='Icon name, <i class="fa-solid fa-school"></i>, or SVG'
                                                        value="{{ $aboutCardIcon }}"
                                                        class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                </div>

                                <div class="sticky bottom-4 z-10 flex justify-end">
                                    <div
                                        class="rounded-2xl border border-slate-200 bg-white/95 p-2 shadow-xl backdrop-blur">
                                        <button type="submit"
                                            class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-indigo-500">
                                            Save About Page
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-800">
                                Run migrations to enable dynamic about page editing.
                            </div>
                        @endif
                    </section>

                    <section data-settings-panel="feature-page" class="hidden space-y-6">
                        @if ($homePageReady ?? false)
                            <form method="POST" action="{{ route('admin.settings.feature-page.update') }}"
                                class="space-y-5">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="_settings_tab" value="feature-page">

                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div>
                                            <span
                                                class="inline-flex rounded-full bg-sky-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-sky-700">
                                                Platform features
                                            </span>
                                            <h3 class="mt-3 text-base font-black text-slate-900">Feature Section Header
                                            </h3>
                                            <p class="mt-1 text-xs text-slate-500">Badge, headline, and tag shown above the
                                                feature cards.</p>
                                        </div>
                                        <span
                                            class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">Visible
                                            on homepage</span>
                                    </div>

                                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                                        <div>
                                            <label for="platform_feature_badge"
                                                class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Badge</label>
                                            <input id="platform_feature_badge" name="feature[badge]" type="text"
                                                value="{{ old('feature.badge', $platformFeature?->subtitle ?? __('home.features.badge')) }}"
                                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                        </div>
                                        <div>
                                            <label for="platform_feature_tag"
                                                class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Tag</label>
                                            <input id="platform_feature_tag" name="feature[tag]" type="text"
                                                value="{{ old('feature.tag', $platformFeature?->value ?? __('home.features.tag')) }}"
                                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label for="platform_feature_title"
                                                class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Headline</label>
                                            <textarea id="platform_feature_title" name="feature[title]" rows="3"
                                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold leading-6 text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ old('feature.title', $platformFeature?->title ?? __('home.features.title')) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                            <div>
                                                <h3 class="text-base font-black text-slate-900">Feature Cards</h3>
                                                <p class="mt-1 text-xs text-slate-500">Cards displayed in the Platform
                                                    Features section.</p>
                                            </div>
                                            <button type="button" data-add-home-card="platform-features"
                                                class="rounded-full bg-sky-600 px-3 py-1 text-xs font-bold text-white transition hover:bg-sky-500">
                                                Add Feature
                                            </button>
                                        </div>

                                    <div class="mt-4 grid gap-3 xl:grid-cols-2">
                                        @for ($i = 0; $i < $platformFeatureLimit; $i++)
                                            @php
                                                $platformFeatureItem = $platformFeatureCards->get($i);
                                                $platformFeatureTitle = old('feature_cards.' . $i . '.title', $platformFeatureItem?->title ?? '');
                                                $platformFeatureDescription = old('feature_cards.' . $i . '.description', $platformFeatureItem?->description ?? '');
                                                $platformFeatureIcon = old('feature_cards.' . $i . '.icon', $platformFeatureItem?->icon ?? '');
                                                $showPlatformFeature = filled($platformFeatureTitle) || filled($platformFeatureDescription);
                                            @endphp
                                            <div data-home-editor-card data-addable-card="platform-features"
                                                class="{{ $showPlatformFeature ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                <input type="hidden" name="feature_cards[{{ $i }}][id]"
                                                    value="{{ $platformFeatureItem?->id }}">
                                                <input type="hidden" name="feature_cards[{{ $i }}][delete]"
                                                    data-home-delete-field value="0">
                                                <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                                                    <span class="flex items-center gap-2 text-xs font-bold text-slate-500">
                                                        <span
                                                            class="grid h-6 w-6 place-items-center rounded-full bg-sky-600 text-[11px] text-white">{{ $i + 1 }}</span>
                                                        Feature card {{ $i + 1 }}
                                                    </span>
                                                    <div data-home-card-actions class="flex flex-wrap items-center gap-2">
                                                        <button type="button" data-home-edit-card
                                                            class="rounded-full border border-blue-200 bg-white px-3 py-1 text-xs font-bold text-blue-600 transition hover:bg-blue-50">
                                                            Edit
                                                        </button>
                                                        <label
                                                            class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                                                            <input type="hidden"
                                                                name="feature_cards[{{ $i }}][active]"
                                                                value="0">
                                                            <input type="checkbox"
                                                                name="feature_cards[{{ $i }}][active]"
                                                                data-home-active-toggle value="1"
                                                                {{ old('feature_cards.' . $i . '.active', $platformFeatureItem?->is_active ?? true) ? 'checked' : '' }}
                                                                class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                            Active
                                                        </label>
                                                        <button type="button" data-home-remove-card
                                                            class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600 transition hover:bg-red-50">
                                                            Remove
                                                        </button>
                                                    </div>
                                                </div>
                                                <input name="feature_cards[{{ $i }}][title]" type="text"
                                                    placeholder="Feature title"
                                                    value="{{ $platformFeatureTitle }}"
                                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                <textarea name="feature_cards[{{ $i }}][description]" rows="3" placeholder="Feature description"
                                                    class="mt-2 w-full resize-none rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm leading-6 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ $platformFeatureDescription }}</textarea>
                                                <input name="feature_cards[{{ $i }}][icon]" type="text"
                                                    data-random-icon-input
                                                    placeholder='Icon name, <i class="fa-solid fa-school"></i>, or SVG'
                                                    value="{{ $platformFeatureIcon }}"
                                                    class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                            </div>
                                        @endfor
                                    </div>
                                </div>

                                <div class="sticky bottom-4 z-10 flex justify-end">
                                    <div
                                        class="rounded-2xl border border-slate-200 bg-white/95 p-2 shadow-xl backdrop-blur">
                                        <button type="submit"
                                            class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-indigo-500">
                                            Save Feature Page
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-800">
                                Run migrations to enable dynamic feature page editing.
                            </div>
                        @endif
                    </section>

                    <section data-settings-panel="verification" class="hidden">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-6">
                            <h2 class="text-lg font-semibold text-slate-900">Verification</h2>
                            <p class="mt-2 text-sm text-slate-500">This section is reserved for account verification and
                                security checks.</p>
                        </div>
                    </section>
                </div>
            </div>
        </section>
    </div>

    @vite(['resources/js/admin/settings.js'])
@endsection
