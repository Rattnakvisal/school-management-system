@extends('layout.students.navbar')

@section('page')
    @php
        $student = auth()->user();
        $nameParts = preg_split('/\s+/', trim((string) $student->name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $firstName = $nameParts[0] ?? 'Student';
        $lastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';
        $classLabel = $student->schoolClass?->display_name ?? 'No class assigned';
        $majorSubjectLabel = $student->majorSubject?->name ?? 'No major subject';
    @endphp

    <div class="student-stage mx-auto max-w-7xl space-y-6">
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
                        View your profile details and keep your account security in good shape.
                    </p>
                    <p class="text-xs font-semibold text-slate-600">
                        Role: <span class="text-slate-900">{{ ucfirst((string) $student->role) }}</span>
                    </p>
                </div>

                <div class="admin-page-header__stats flex flex-wrap items-center gap-2 text-xs font-semibold sm:ml-auto sm:justify-end">
                    <span class="admin-page-stat">Class: {{ $classLabel }}</span>
                    <span class="admin-page-stat admin-page-stat--emerald">Major:
                        {{ $majorSubjectLabel }}</span>
                    <span class="admin-page-stat admin-page-stat--sky">Email:
                        {{ $student->email }}</span>
                    <span class="admin-page-stat admin-page-stat--amber">ID:
                        {{ $student->formatted_id ?? $student->id }}</span>
                </div>
            </div>
        </section>

        <section class="student-reveal rounded-[32px] border border-slate-200 bg-white/90 p-4 shadow-[0_20px_60px_-40px_rgba(15,23,42,0.45)] ring-1 ring-slate-100 sm:p-6"
            style="--sd: 2;">
            <div class="grid gap-6 xl:grid-cols-[260px_minmax(0,1fr)]">
                <aside class="rounded-[28px] border border-slate-200 bg-slate-50/80 p-4 shadow-sm">
                    <div class="space-y-2">
                        <button type="button"
                            class="group flex w-full items-center gap-3 rounded-2xl border border-indigo-100 bg-indigo-600 px-4 py-3.5 text-left text-sm font-semibold text-white shadow-[0_14px_32px_-22px_rgba(79,70,229,0.85)] transition hover:-translate-y-px">
                            <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white/15 text-white">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 12c2.21 0 4-1.79 4-4S14.21 4 12 4 8 5.79 8 8s1.79 4 4 4Zm0 2c-3.33 0-8 1.67-8 5v1h16v-1c0-3.33-4.67-5-8-5Z" />
                                </svg>
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block text-sm font-semibold">Profile</span>
                                <span class="block text-[11px] font-medium text-white/75">Personal information</span>
                            </span>
                        </button>

                        <button type="button"
                            class="group flex w-full items-center gap-3 rounded-2xl border border-transparent px-4 py-3.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-100 hover:bg-white hover:text-indigo-700">
                            <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-hover:bg-indigo-50 group-hover:text-indigo-600">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17 9h-1V7a4 4 0 1 0-8 0v2H7a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-8a2 2 0 0 0-2-2Zm-5 6a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z" />
                                </svg>
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block text-sm font-semibold">Security</span>
                                <span class="block text-[11px] font-medium text-slate-400">Password and access</span>
                            </span>
                        </button>

                        <a href="{{ route('student.notices.index') }}"
                            class="group flex w-full items-center gap-3 rounded-2xl border border-transparent px-4 py-3.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-100 hover:bg-white hover:text-indigo-700">
                            <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500 transition group-hover:bg-indigo-50 group-hover:text-indigo-600">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 2a6 6 0 0 0-6 6v3.59L4.71 13.3A1 1 0 0 0 5.41 15h13.18a1 1 0 0 0 .7-1.7L18 11.59V8a6 6 0 0 0-6-6Z" />
                                </svg>
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block text-sm font-semibold">Notifications</span>
                                <span class="block text-[11px] font-medium text-slate-400">Alerts and updates</span>
                            </span>
                        </a>
                    </div>

                    <div class="mt-6 rounded-2xl bg-gradient-to-br from-indigo-600 via-indigo-600 to-violet-600 p-4 text-white shadow-[0_18px_40px_-24px_rgba(79,70,229,0.9)]">
                        <div class="text-[11px] font-black uppercase tracking-[0.22em] text-white/70">Student Portal</div>
                        <div class="mt-2 text-sm font-semibold leading-6 text-white/95">
                            Manage your account safely and keep school updates close.
                        </div>
                    </div>
                </aside>

                <div class="min-w-0 space-y-6">
                    <section class="rounded-[28px] border border-slate-200 bg-white p-4 sm:p-6">
                        <div class="flex flex-wrap items-center gap-4 border-b border-slate-100 pb-5">
                            <div class="relative">
                                <div
                                    class="absolute inset-0 rounded-full bg-gradient-to-br from-indigo-200/60 via-white to-sky-200/50 blur-xl">
                                </div>
                                <img id="student_avatar_preview"
                                    class="relative h-24 w-24 rounded-full border-4 border-white object-cover shadow-[0_12px_28px_-18px_rgba(15,23,42,0.6)]"
                                    src="{{ $student->avatar_url }}"
                                    data-fallback="{{ $student->fallback_avatar_url }}"
                                    onerror="this.onerror=null;this.src='{{ $student->fallback_avatar_url }}';"
                                    alt="student avatar">
                            </div>

                            <div class="space-y-1">
                                <div class="text-xs font-bold uppercase tracking-[0.18em] text-indigo-600">
                                    Student Account
                                </div>
                                <h2 class="text-2xl font-black tracking-tight text-slate-900 sm:text-3xl">
                                    {{ $student->name }}
                                </h2>
                                <p class="max-w-2xl text-sm text-slate-500">
                                    Keep your profile information up to date with the school office.
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">First Name</label>
                                <input type="text" value="{{ $firstName }}" readonly
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-indigo-200 focus:bg-white">
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Last Name</label>
                                <input type="text" value="{{ $lastName ?: '-' }}" readonly
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-indigo-200 focus:bg-white">
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Email</label>
                                <input type="email" value="{{ $student->email }}" readonly
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-indigo-200 focus:bg-white">
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Phone Number</label>
                                <input type="text" value="{{ $student->phone_number ?: '-' }}" readonly
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-indigo-200 focus:bg-white">
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Home Class</label>
                                <input type="text" value="{{ $classLabel }}" readonly
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-indigo-200 focus:bg-white">
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Major Subject</label>
                                <input type="text" value="{{ $majorSubjectLabel }}" readonly
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-indigo-200 focus:bg-white">
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Role</label>
                                <input type="text" value="{{ ucfirst((string) $student->role) }}" readonly
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-sm text-slate-600">
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Student ID</label>
                                <input type="text" value="ID #{{ $student->formatted_id ?? $student->id }}" readonly
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-sm text-slate-600">
                            </div>
                        </div>
                    </section>

                    <section class="grid gap-6 xl:grid-cols-2">
                        <article class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                            <h2 class="text-lg font-semibold text-slate-900">Security</h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Student password changes are handled by the school office or administrator.
                            </p>

                            <div class="mt-5 space-y-3">
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Password</div>
                                    <div class="mt-1 text-sm font-semibold text-slate-900">Managed by administrator</div>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Tip</div>
                                    <div class="mt-1 text-sm text-slate-600">
                                        Keep your account private and sign out on shared devices.
                                    </div>
                                </div>
                            </div>
                        </article>

                        <article class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                            <h2 class="text-lg font-semibold text-slate-900">Account Notes</h2>
                            <p class="mt-1 text-sm text-slate-500">
                                This page keeps the clean admin-style layout, but student edits remain controlled by staff.
                            </p>

                            <div class="mt-5 flex flex-wrap gap-2">
                                <span class="rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                                    Profile view only
                                </span>
                                <span class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                    School verified
                                </span>
                                <span class="rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700">
                                    Student portal
                                </span>
                            </div>
                        </article>
                    </section>
                </div>
            </div>
        </section>
    </div>
@endsection
