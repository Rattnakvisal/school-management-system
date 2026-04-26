@extends('layout.students.navbar')

@section('page')
    @php
        $student = auth()->user();
        $passwordErrors = $errors->passwordUpdate;
        $nameParts = preg_split('/\s+/', trim((string) $student->name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $firstName = $nameParts[0] ?? 'Student';
        $lastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';
        $majorSubjects = $student->relationLoaded('majorSubjects') && $student->majorSubjects->isNotEmpty()
            ? $student->majorSubjects
            : ($student->majorSubject ? collect([$student->majorSubject]) : collect());
        $majorSubjectLabel = $majorSubjects->pluck('name')->filter()->join(', ') ?: 'No major subject';
    @endphp

    <div id="student-settings-page" class="student-stage space-y-6"
        data-settings-default-tab="{{ $passwordErrors->any() ? 'password' : 'profile' }}">
        <section
            class="student-reveal student-float admin-page-header overflow-hidden rounded-[32px] border border-indigo-100/80 bg-[radial-gradient(circle_at_top_left,rgba(99,102,241,0.12),transparent_30%),linear-gradient(135deg,rgba(255,255,255,0.97),rgba(243,247,255,0.92))] p-5 shadow-[0_24px_80px_-48px_rgba(15,23,42,0.45)] sm:p-6"
            style="--sd: 1;">
            <div class="admin-page-header__main flex flex-wrap items-start gap-5">
                <div class="admin-page-header__intro space-y-2">
                    <div class="admin-page-header__title-row flex items-start gap-3">
                        <span class="admin-page-header__icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path
                                    d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06-2 3.46-.08-.03a1.65 1.65 0 0 0-2.11.73l-.03.08h-4l-.03-.08a1.65 1.65 0 0 0-2.11-.73l-.08.03-2-3.46.06-.06A1.65 1.65 0 0 0 4.6 15l-.03-.08V9l.03-.08A1.65 1.65 0 0 0 4.27 7.1l-.06-.06 2-3.46.08.03a1.65 1.65 0 0 0 2.11-.73l.03-.08h4l.03.08a1.65 1.65 0 0 0 2.11.73l.08-.03 2 3.46-.06.06A1.65 1.65 0 0 0 19.4 9l.03.08v5.84Z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </span>
                        <div>
                            <div class="admin-page-header__eyebrow">Student Portal</div>
                            <h1 class="admin-page-title text-3xl font-black tracking-tight sm:text-4xl">
                                Student Settings
                            </h1>
                        </div>
                    </div>
                    <p class="admin-page-subtitle max-w-2xl text-sm">
                        Manage your profile details, account security, and student portal notifications.
                    </p>
                </div>

                <div
                    class="admin-page-header__stats flex flex-wrap items-center gap-2 text-xs font-semibold sm:ml-auto sm:justify-end">
                    <span class="admin-page-stat admin-page-stat--emerald">Major:
                        {{ \Illuminate\Support\Str::limit($majorSubjectLabel, 48) }}</span>
                    <span class="admin-page-stat admin-page-stat--sky">Role: {{ ucfirst((string) $student->role) }}</span>
                    <span class="admin-page-stat admin-page-stat--amber">ID:
                        {{ $student->formatted_id ?? $student->id }}</span>
                </div>
            </div>
        </section>

        @if (session('success'))
            <div class="student-reveal rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"
                style="--sd: 2;">
                {{ session('success') }}
            </div>
        @endif

        @if ($passwordErrors->any())
            <div class="student-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
                style="--sd: 2;">
                {{ $passwordErrors->first() }}
            </div>
        @endif

        <section
            class="student-reveal rounded-3xl border border-slate-200 bg-white p-4 shadow-sm ring-1 ring-slate-100 sm:p-6"
            style="--sd: 3;">
            <div class="grid gap-6 xl:grid-cols-[220px_minmax(0,1fr)]">
                <aside class="rounded-2xl border border-slate-200 bg-slate-50 p-3">
                    <div class="space-y-2">
                        <button type="button" data-settings-nav="profile"
                            class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                            <span class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path
                                        d="M12 12c2.21 0 4-1.79 4-4S14.21 4 12 4 8 5.79 8 8s1.79 4 4 4Zm0 2c-3.33 0-8 1.67-8 5v1h16v-1c0-3.33-4.67-5-8-5Z" />
                                </svg>
                                Profile Settings
                            </span>
                        </button>

                        <button type="button" data-settings-nav="password"
                            class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                            <span class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path
                                        d="M17 9h-1V7a4 4 0 1 0-8 0v2H7a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-8a2 2 0 0 0-2-2Zm-7-2a2 2 0 1 1 4 0v2h-4V7Zm2 10a2 2 0 1 1 0-4 2 2 0 0 1 0 4Z" />
                                </svg>
                                Password
                            </span>
                        </button>

                        <button type="button" data-settings-nav="notifications"
                            class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                            <span class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path
                                        d="M12 2a6 6 0 0 0-6 6v3.59L4.71 13.3A1 1 0 0 0 5.41 15h13.18a1 1 0 0 0 .7-1.7L18 11.59V8a6 6 0 0 0-6-6Zm0 20a3 3 0 0 0 2.82-2H9.18A3 3 0 0 0 12 22Z" />
                                </svg>
                                Notifications
                            </span>
                        </button>

                        <button type="button" data-settings-nav="verification"
                            class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                            <span class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
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
                        <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 sm:p-6">
                            <div class="flex flex-wrap items-center gap-4 border-b border-slate-200 pb-5">
                                <div class="relative">
                                    <img id="student_avatar_preview"
                                        class="h-24 w-24 rounded-full border-4 border-white object-cover shadow-sm"
                                        src="{{ $student->avatar_url }}"
                                        data-fallback="{{ $student->fallback_avatar_url }}"
                                        onerror="this.onerror=null;this.src='{{ $student->fallback_avatar_url }}';"
                                        alt="student avatar">
                                </div>

                                <div>
                                    <div class="text-xs font-bold uppercase tracking-[0.18em] text-indigo-600">
                                        Student Account
                                    </div>
                                    <h2 class="mt-1 text-2xl font-black tracking-tight text-slate-900 sm:text-3xl">
                                        {{ $student->name }}
                                    </h2>
                                    <p class="mt-1 max-w-2xl text-sm text-slate-500">
                                        Profile details are shown here for review. Contact the school office for profile
                                        changes.
                                    </p>
                                </div>
                            </div>

                            <div class="mt-5 grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-600">First Name</label>
                                    <input type="text" value="{{ $firstName }}" readonly
                                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none">
                                </div>

                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-600">Last Name</label>
                                    <input type="text" value="{{ $lastName ?: '-' }}" readonly
                                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none">
                                </div>

                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-600">Email</label>
                                    <input type="email" value="{{ $student->email }}" readonly
                                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none">
                                </div>

                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-600">Mobile Number</label>
                                    <input type="text" value="{{ $student->phone_number ?: '-' }}" readonly
                                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="mb-1 block text-xs font-semibold text-slate-600">Major Subjects</label>
                                    <div
                                        class="min-h-[46px] rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700">
                                        @if ($majorSubjects->isNotEmpty())
                                            <div class="flex flex-wrap gap-2">
                                                @foreach ($majorSubjects as $majorSubject)
                                                    <span
                                                        class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                                        {{ $majorSubject->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-slate-400">No major subject</span>
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-600">Role</label>
                                    <input type="text" value="{{ ucfirst((string) $student->role) }}" readonly
                                        class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-600">
                                </div>

                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-600">Student ID</label>
                                    <input type="text" value="ID #{{ $student->formatted_id ?? $student->id }}"
                                        readonly
                                        class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-600">
                                </div>
                            </div>
                        </div>
                    </section>

                    <section data-settings-panel="password" class="hidden space-y-6">
                        <form method="POST" action="{{ route('student.settings.password.update') }}"
                            class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 sm:p-6">
                            @csrf
                            @method('PUT')

                            <h2 class="text-lg font-semibold text-slate-900">Change Password</h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Students can update their own password here. Use at least 8 characters.
                            </p>

                            <div class="mt-5 space-y-4">
                                <div>
                                    <label for="current_password"
                                        class="mb-1 block text-xs font-semibold text-slate-600">Current Password</label>
                                    <input id="current_password" type="password" name="current_password"
                                        autocomplete="current-password" required
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                    @error('current_password', 'passwordUpdate')
                                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label for="password"
                                            class="mb-1 block text-xs font-semibold text-slate-600">New Password</label>
                                        <input id="password" type="password" name="password" autocomplete="new-password"
                                            required
                                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                        @error('password', 'passwordUpdate')
                                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="password_confirmation"
                                            class="mb-1 block text-xs font-semibold text-slate-600">Confirm Password</label>
                                        <input id="password_confirmation" type="password" name="password_confirmation"
                                            autocomplete="new-password" required
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
                            <p class="mt-2 text-sm text-slate-600">
                                Check school notices and updates from the student portal.
                            </p>
                            <a href="{{ route('student.notices.index') }}"
                                class="mt-5 inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                                Open Notifications
                            </a>
                        </div>
                    </section>

                    <section data-settings-panel="verification" class="hidden">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-6">
                            <h2 class="text-lg font-semibold text-slate-900">Verification</h2>
                            <p class="mt-2 text-sm text-slate-500">
                                Your major subjects, student ID, and role are verified by the school office.
                            </p>
                            <div class="mt-5 flex flex-wrap gap-2">
                                <span
                                    class="rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                                    Profile view only
                                </span>
                                <span
                                    class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                    School verified
                                </span>
                                <span
                                    class="rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700">
                                    Student portal
                                </span>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </section>
    </div>

    @vite(['resources/js/admin/settings.js'])
@endsection
