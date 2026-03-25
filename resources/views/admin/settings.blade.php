@extends('layout.admin.navbar.navbar')

@section('page')
    @php
        $nameParts = preg_split('/\s+/', trim((string) $admin->name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $defaultFirstName = $nameParts[0] ?? '';
        $defaultLastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';
        $profileErrors = $errors->profileUpdate;
        $passwordErrors = $errors->passwordUpdate;
    @endphp

    <div class="stage space-y-6">
        <x-admin.page-header reveal-class="reveal" delay="1" icon="settings" title="Admin Settings"
            subtitle="Manage profile details, avatar, and account security.">
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
            </x-slot:actions>
        </x-admin.page-header>

        @if (session('success'))
            <div class="reveal rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"
                style="--sd: 2;">
                {{ session('success') }}
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
                        <form method="POST" action="{{ route('admin.settings.profile.update') }}" enctype="multipart/form-data"
                            class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 sm:p-6">
                            @csrf
                            @method('PUT')

                            <input type="hidden" id="remove_admin_avatar" name="remove_avatar" value="0">

                            <div class="flex flex-wrap items-center gap-4 border-b border-slate-200 pb-5">
                                <div class="relative">
                                    <img id="admin_avatar_preview"
                                        class="h-24 w-24 rounded-full border-4 border-white object-cover shadow-sm"
                                        src="{{ $admin->avatar_url }}"
                                        data-fallback="{{ $admin->fallback_avatar_url }}"
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
                                    <label for="first_name" class="mb-1 block text-xs font-semibold text-slate-600">First Name
                                        <span class="text-red-500">*</span></label>
                                    <input id="first_name" name="first_name" type="text"
                                        value="{{ old('first_name', $defaultFirstName) }}" required
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                    @error('first_name', 'profileUpdate')
                                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="last_name" class="mb-1 block text-xs font-semibold text-slate-600">Last Name</label>
                                    <input id="last_name" name="last_name" type="text"
                                        value="{{ old('last_name', $defaultLastName) }}"
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                    @error('last_name', 'profileUpdate')
                                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="mb-1 block text-xs font-semibold text-slate-600">Email</label>
                                    <input id="email" name="email" type="email" value="{{ old('email', $admin->email) }}"
                                        required
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                    @error('email', 'profileUpdate')
                                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone_number" class="mb-1 block text-xs font-semibold text-slate-600">Mobile
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
                                    <label for="current_password" class="mb-1 block text-xs font-semibold text-slate-600">Current
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
                                            class="mb-1 block text-xs font-semibold text-slate-600">Confirm Password</label>
                                        <input id="password_confirmation" type="password" name="password_confirmation" required
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
                                <span class="font-semibold text-indigo-700">{{ number_format($stats['unreadNotifications'] ?? 0) }}</span>
                            </p>
                            <p class="mt-1 text-sm text-slate-500">This section is ready for notification preferences.</p>
                        </div>
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

    <script>
        (function() {
            const navItems = Array.from(document.querySelectorAll('[data-settings-nav]'));
            const panels = Array.from(document.querySelectorAll('[data-settings-panel]'));

            if (navItems.length && panels.length) {
                const activateTab = (tab) => {
                    navItems.forEach((item) => {
                        const active = item.dataset.settingsNav === tab;
                        item.classList.toggle('border-indigo-200', active);
                        item.classList.toggle('bg-indigo-50', active);
                        item.classList.toggle('text-indigo-700', active);
                        item.classList.toggle('text-slate-600', !active);
                    });

                    panels.forEach((panel) => {
                        panel.classList.toggle('hidden', panel.dataset.settingsPanel !== tab);
                    });
                };

                const defaultTab = '{{ $passwordErrors->any() ? 'password' : 'profile' }}';
                activateTab(defaultTab);

                navItems.forEach((item) => {
                    item.addEventListener('click', () => activateTab(item.dataset.settingsNav));
                });
            }

            const avatarInput = document.getElementById('admin_avatar_input');
            const avatarPreview = document.getElementById('admin_avatar_preview');
            const uploadAvatarBtn = document.getElementById('upload_avatar_btn');
            const triggerUploadBtn = document.getElementById('trigger_avatar_upload');
            const deleteAvatarBtn = document.getElementById('delete_avatar_btn');
            const removeAvatarField = document.getElementById('remove_admin_avatar');

            if (!avatarInput || !avatarPreview || !uploadAvatarBtn || !triggerUploadBtn || !deleteAvatarBtn || !removeAvatarField) {
                return;
            }

            let currentObjectUrl = null;

            const openFilePicker = () => avatarInput.click();
            uploadAvatarBtn.addEventListener('click', openFilePicker);
            triggerUploadBtn.addEventListener('click', openFilePicker);

            avatarInput.addEventListener('change', () => {
                const file = avatarInput.files && avatarInput.files[0] ? avatarInput.files[0] : null;
                if (!file) {
                    return;
                }

                if (currentObjectUrl) {
                    URL.revokeObjectURL(currentObjectUrl);
                }

                currentObjectUrl = URL.createObjectURL(file);
                avatarPreview.src = currentObjectUrl;
                removeAvatarField.value = '0';
            });

            deleteAvatarBtn.addEventListener('click', () => {
                if (currentObjectUrl) {
                    URL.revokeObjectURL(currentObjectUrl);
                    currentObjectUrl = null;
                }

                avatarInput.value = '';
                avatarPreview.src = avatarPreview.dataset.fallback || avatarPreview.src;
                removeAvatarField.value = '1';
            });
        })();
    </script>
@endsection
