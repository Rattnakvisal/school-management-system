@extends('layout.admin.navbar.navbar')

@section('page')
    <div class="student-stage space-y-6">
        <x-admin.page-header reveal-class="student-reveal" delay="1" icon="students" title="Student Management"
            subtitle="Create, edit, activate, deactivate, and remove student accounts.">
            <x-slot:stats>
                <span class="admin-page-stat">Total: {{ $stats['total'] }}</span>
                <span class="admin-page-stat admin-page-stat--emerald">Active:
                    {{ $stats['active'] }}</span>
                <span class="admin-page-stat admin-page-stat--rose">Inactive:
                    {{ $stats['inactive'] }}</span>
                @if ($hasClassColumn)
                    <span class="admin-page-stat admin-page-stat--sky">Assigned:
                        {{ $stats['assigned'] }}</span>
                @endif
            </x-slot:stats>
        </x-admin.page-header>

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

        @if (session('warning'))
            <div class="student-reveal rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700"
                style="--sd: 2;">
                {{ session('warning') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="student-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
                style="--sd: 2;">
                <div class="font-semibold">Please check the form fields and try again.</div>
            </div>
        @endif

        @php
            $showCreateFormOnLoad = old('_form') === 'create_student';
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
                class="student-reveal student-float rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-4"
                style="--sd: 3;">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-black text-slate-900">Create Student</h2>
                        <p class="mt-1 text-xs text-slate-500">Create a new account and assign the role.</p>
                    </div>
                    <button type="button" @click="createOpen = !createOpen"
                        :aria-expanded="(createOpen || isDesktop).toString()" aria-controls="create-student-form-panel"
                        class="inline-flex items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 shadow-sm hover:bg-indigo-100 xl:hidden">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 5v14M5 12h14" x-show="!(createOpen || isDesktop)"></path>
                            <path d="M5 12h14" x-show="createOpen || isDesktop"></path>
                        </svg>
                        <span x-show="!(createOpen || isDesktop)">Create Student</span>
                        <span x-show="createOpen || isDesktop">Hide Form</span>
                    </button>
                </div>
                <form id="create-student-form-panel" method="POST" action="{{ route('admin.students.store') }}"
                    enctype="multipart/form-data" class="js-create-form mt-5 space-y-4"
                    x-show="createOpen || isDesktop" x-cloak x-transition.opacity.duration.150ms>
                    @csrf
                    <input type="hidden" name="_form" value="create_student">

                    <div>
                        <label for="name" class="mb-1 block text-xs font-semibold text-slate-600">Full Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                            placeholder="Student full name">
                        @error('name')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-1 block text-xs font-semibold text-slate-600">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                            placeholder="student@example.com">
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

                    <div>
                        <label for="role" class="mb-1 block text-xs font-semibold text-slate-600">Role</label>
                        <select id="role" name="role"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            <option value="student" {{ old('role', 'student') === 'student' ? 'selected' : '' }}>Student
                            </option>
                            <option value="teacher" {{ old('role') === 'teacher' ? 'selected' : '' }}>Teacher</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        @error('role')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    @if ($hasClassColumn)
                        <div>
                            <label for="school_class_id" class="mb-1 block text-xs font-semibold text-slate-600">Home Class
                                (Optional)</label>
                            <select id="school_class_id" name="school_class_id"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                <option value="">Unassigned</option>
                                @foreach ($classes as $classOption)
                                    <option value="{{ $classOption->id }}"
                                        {{ (string) old('school_class_id') === (string) $classOption->id ? 'selected' : '' }}>
                                        {{ $classOption->display_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('school_class_id')
                                <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        @if ($hasMajorSubjectColumn)
                            <div>
                                @php
                                    $createSelectedMajorSubjectIds = old('major_subject_ids');
                                    if (
                                        !is_array($createSelectedMajorSubjectIds) ||
                                        count($createSelectedMajorSubjectIds) === 0
                                    ) {
                                        $legacyMajorId = old('major_subject_id');
                                        $createSelectedMajorSubjectIds = $legacyMajorId ? [$legacyMajorId] : [];
                                    }
                                    $createSelectedMajorSubjectIds = collect($createSelectedMajorSubjectIds)
                                        ->map(fn($value) => (string) $value)
                                        ->filter(fn($value) => $value !== '')
                                        ->values()
                                        ->all();
                                @endphp
                                <label for="major_subject_id" class="mb-1 block text-xs font-semibold text-slate-600">Major
                                    Subjects</label>
                                <select id="major_subject_id" name="major_subject_ids[]"
                                    data-selected-list='@json($createSelectedMajorSubjectIds)'
                                    data-checkbox-target="major_subject_checkbox_list" multiple size="5"
                                    class="hidden">
                                    <option value="">Select major subjects</option>
                                </select>
                                <div id="major_subject_checkbox_list"
                                    class="min-h-[132px] space-y-2 rounded-xl border border-slate-200 bg-slate-50/50 p-3">
                                </div>
                                <p class="mt-1 text-[11px] text-slate-500">You can select multiple major subjects.</p>
                                @error('major_subject_ids')
                                    <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                                @error('major_subject_ids.*')
                                    <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                                @error('major_subject_id')
                                    <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        @if ($hasClassStudyTimeColumn)
                            <div>
                                @php
                                    $createSelectedStudyTimeIds = old('class_study_time_ids');
                                    if (
                                        !is_array($createSelectedStudyTimeIds) ||
                                        count($createSelectedStudyTimeIds) === 0
                                    ) {
                                        $legacySelectedId = old('class_study_time_id');
                                        $createSelectedStudyTimeIds = $legacySelectedId ? [$legacySelectedId] : [];
                                    }
                                    $createSelectedStudyTimeIds = collect($createSelectedStudyTimeIds)
                                        ->map(fn($value) => (string) $value)
                                        ->filter(fn($value) => $value !== '')
                                        ->values()
                                        ->all();
                                @endphp
                                <div class="mb-1 flex items-center justify-between gap-2">
                                    <label for="class_study_time_id"
                                        class="block text-xs font-semibold text-slate-600">Study
                                        Time</label>
                                    <a id="manage_study_time_inline"
                                        href="{{ route('admin.time-studies.index', ['tab' => 'class']) }}"
                                        data-base-url="{{ route('admin.time-studies.index') }}" target="_blank"
                                        rel="noopener"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-indigo-200 bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                        + Add More
                                    </a>
                                </div>
                                <select id="class_study_time_id" name="class_study_time_ids[]"
                                    data-selected-list='@json($createSelectedStudyTimeIds)'
                                    data-checkbox-target="study_time_checkbox_list" multiple size="4" class="hidden">
                                    <option value="">Select class first</option>
                                </select>
                                <div id="study_time_checkbox_list"
                                    class="min-h-[132px] space-y-2 rounded-xl border border-slate-200 bg-slate-50/50 p-3">
                                </div>
                                <p class="mt-1 text-[11px] text-slate-500">You can select multiple study times.</p>
                                @error('class_study_time_ids')
                                    <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                                @error('class_study_time_ids.*')
                                    <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
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
                        <p class="mt-1 text-[11px] text-slate-500">Allowed: JPG, PNG, WEBP (max 2MB)</p>
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
                        Create Student
                    </button>
                </form>
            </section>

            <section
                class="student-reveal student-float rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-8"
                style="--sd: 4;">
                @php
                    $studentExportQuery = array_filter(
                        [
                            'q' => $search,
                            'status' => $status !== 'all' ? $status : null,
                            'class_id' => $classId !== 'all' ? $classId : null,
                        ],
                        fn($value) => $value !== null && $value !== ''
                    );
                @endphp
                <div x-data="{ filterOpen: false }" @open-filter-panel.window="filterOpen = true" class="space-y-4">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <h2 class="text-lg font-black text-slate-900">Student List</h2>
                            <p class="mt-1 text-xs font-medium text-slate-500">Export the current roster as a polished PDF
                                or a styled Excel workbook.</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('admin.students.export.pdf', $studentExportQuery) }}"
                                class="inline-flex items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-3.5 py-2 text-sm font-semibold text-rose-700 shadow-sm transition hover:-translate-y-0.5 hover:bg-rose-100">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <path d="M14 2v6h6"></path>
                                    <path d="M9 15h6"></path>
                                    <path d="M9 11h2"></path>
                                    <path d="M9 19h6"></path>
                                </svg>
                                PDF Report
                            </a>
                            <a href="{{ route('admin.students.export.excel', $studentExportQuery) }}"
                                class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-3.5 py-2 text-sm font-semibold text-emerald-700 shadow-sm transition hover:-translate-y-0.5 hover:bg-emerald-100">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <path d="M14 2v6h6"></path>
                                    <path d="m9 15 6-6"></path>
                                    <path d="m15 15-6-6"></path>
                                </svg>
                                Excel Report
                            </a>
                            <button type="button" @click="filterOpen = true"
                                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M3 5h18l-7 8v5l-4 2v-7L3 5z"></path>
                                </svg>
                                Filters
                            </button>
                        </div>
                    </div>

                    <div x-show="filterOpen" x-cloak x-transition.opacity class="fixed inset-0 z-[80] bg-slate-900/40"
                        @click="filterOpen = false"></div>

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
                                        <a href="{{ route('admin.students.index') }}"
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

                                <form method="GET" action="{{ route('admin.students.index') }}"
                                    class="flex min-h-0 flex-1 flex-col" @submit="filterOpen = false">
                                    <div class="flex-1 space-y-5 overflow-y-auto px-5 py-4">
                                        <section class="space-y-2">
                                            <h4 class="text-xl font-bold text-slate-900">Search</h4>
                                            <input id="q" name="q" type="text"
                                                value="{{ $search }}" placeholder="Search by name, email, phone, or class"
                                                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                        </section>
                                        @if ($hasClassColumn)
                                            <section class="space-y-2">
                                                <h4 class="text-xl font-bold text-slate-900">Class</h4>
                                                <select name="class_id"
                                                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                    <option value="all" {{ $classId === 'all' ? 'selected' : '' }}>All
                                                        Classes
                                                    </option>
                                                    @foreach ($classes as $classOption)
                                                        <option value="{{ $classOption->id }}"
                                                            {{ $classId === (string) $classOption->id ? 'selected' : '' }}>
                                                            {{ $classOption->display_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </section>
                                        @else
                                            <input type="hidden" name="class_id" value="all">
                                        @endif
                                        @if ($hasStatusColumn)
                                            <section class="space-y-2">
                                                <h4 class="text-xl font-bold text-slate-900">Status</h4>
                                                <select name="status"
                                                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All
                                                    </option>
                                                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>
                                                        Active
                                                    </option>
                                                    <option value="inactive"
                                                        {{ $status === 'inactive' ? 'selected' : '' }}>
                                                        Inactive</option>
                                                </select>
                                            </section>
                                        @else
                                            <input type="hidden" name="status" value="all">
                                        @endif
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
                                <div class="max-h-[700px] overflow-auto">
                                    <table class="student-table w-full min-w-[1300px] text-left text-sm">
                                        <thead
                                            class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                            <tr>
                                                <th class="student-col-student px-3 py-3 font-semibold">Student</th>
                                                <th class="student-col-email px-3 py-3 font-semibold">Email</th>
                                                @if ($hasPhoneColumn ?? false)
                                                    <th class="student-col-phone px-3 py-3 font-semibold">Phone Number
                                                    </th>
                                                @endif
                                                <th class="student-col-class px-3 py-3 font-semibold">Class</th>
                                                @if ($hasMajorSubjectColumn)
                                                    <th class="student-col-major px-3 py-3 font-semibold">Major Subjects
                                                    </th>
                                                @endif
                                                @if ($hasClassStudyTimeColumn)
                                                    <th class="student-col-study px-3 py-3 font-semibold">Study Time</th>
                                                @endif
                                                <th class="student-col-status px-3 py-3 font-semibold">Status</th>
                                                <th class="student-col-created px-3 py-3 font-semibold">Created</th>
                                                <th class="student-col-actions px-3 py-3 font-semibold text-right">Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 bg-white">
                                            @forelse ($students as $student)
                                                <tr class="align-top hover:bg-slate-50/80" x-data="{ open: false }">
                                                    <td class="student-col-student px-3 py-3 align-top">
                                                        <div class="flex items-center gap-3">
                                                            <img src="{{ $student->avatar_url }}"
                                                                alt="{{ $student->name }}"
                                                                class="h-9 w-9 rounded-full object-cover ring-1 ring-slate-200">
                                                            <div class="min-w-0">
                                                                <div class="student-name font-semibold text-slate-800">
                                                                    {{ $student->name }}
                                                                </div>
                                                                <div class="text-xs text-slate-400">ID
                                                                    #{{ $student->formatted_id }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="student-col-email px-3 py-3 align-top text-slate-600">
                                                        <div class="student-email text-slate-600">{{ $student->email }}
                                                        </div>
                                                    </td>
                                                    @if ($hasPhoneColumn ?? false)
                                                        <td class="student-col-phone px-3 py-3 align-top text-slate-600">
                                                            {{ $student->phone_number ?: '-' }}
                                                        </td>
                                                    @endif
                                                    <td class="student-col-class px-3 py-3 align-top text-slate-600">
                                                        @if ($hasClassColumn)
                                                            @if ($student->schoolClass)
                                                                <span
                                                                    class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                                                    {{ $student->schoolClass->display_name }}
                                                                </span>
                                                            @else
                                                                <span class="text-slate-400">-</span>
                                                            @endif
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    @if ($hasMajorSubjectColumn)
                                                        <td class="student-col-major px-3 py-3 align-top text-slate-600">
                                                            @php
                                                                $majorSubjects = collect();
                                                                if (
                                                                    ($hasStudentMajorSubjectsTable ?? false) &&
                                                                    $student->relationLoaded('majorSubjects') &&
                                                                    $student->majorSubjects->isNotEmpty()
                                                                ) {
                                                                    $majorSubjects = $student->majorSubjects;
                                                                } elseif ($student->majorSubject) {
                                                                    $majorSubjects = collect([$student->majorSubject]);
                                                                }
                                                            @endphp
                                                            @if ($majorSubjects->isNotEmpty())
                                                                <div class="flex max-w-full flex-col gap-1">
                                                                    @foreach ($majorSubjects->take(3) as $majorSubject)
                                                                        <div
                                                                            class="inline-flex max-w-full items-center gap-1.5 rounded-lg border border-indigo-100 bg-indigo-50/90 px-2 py-1 text-[11px]">
                                                                            <span class="font-semibold text-indigo-700">
                                                                                {{ $majorSubject->name }}
                                                                            </span>
                                                                        </div>
                                                                    @endforeach

                                                                    @if ($majorSubjects->count() > 3)
                                                                        <span
                                                                            class="text-[11px] font-semibold text-slate-500">
                                                                            +{{ $majorSubjects->count() - 3 }} more majors
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            @else
                                                                <span class="text-slate-400">-</span>
                                                            @endif
                                                        </td>
                                                    @endif
                                                    @if ($hasClassStudyTimeColumn)
                                                        <td class="student-col-study px-3 py-3 align-top text-slate-600">
                                                            @php
                                                                $studySlots = collect();
                                                                if (
                                                                    $student->relationLoaded('studyTimes') &&
                                                                    $student->studyTimes->isNotEmpty()
                                                                ) {
                                                                    $studySlots = $student->studyTimes;
                                                                } elseif ($student->classStudyTime) {
                                                                    $studySlots = collect([$student->classStudyTime]);
                                                                }
                                                            @endphp
                                                            @if ($studySlots->isNotEmpty())
                                                                <div class="flex max-w-full flex-col gap-1">
                                                                    @foreach ($studySlots->take(3) as $slot)
                                                                        @php
                                                                            $periodKey = strtolower(
                                                                                (string) $slot->period,
                                                                            );
                                                                            $periodLabel =
                                                                                $periodLabels[$periodKey] ??
                                                                                ucfirst($periodKey);
                                                                            $dayKey = strtolower(
                                                                                (string) ($slot->day_of_week ?? 'all'),
                                                                            );
                                                                            $dayLabel = match ($dayKey) {
                                                                                'monday' => 'Monday',
                                                                                'tuesday' => 'Tuesday',
                                                                                'wednesday' => 'Wednesday',
                                                                                'thursday' => 'Thursday',
                                                                                'friday' => 'Friday',
                                                                                'saturday' => 'Saturday',
                                                                                'sunday' => 'Sunday',
                                                                                default => 'All Days',
                                                                            };
                                                                        @endphp
                                                                        <div
                                                                            class="inline-flex max-w-full items-center gap-1.5 rounded-lg border border-indigo-100 bg-indigo-50/90 px-2 py-1 text-[11px]">
                                                                            <span
                                                                                class="font-bold uppercase tracking-wide text-indigo-700">
                                                                                {{ $dayLabel }} | {{ $periodLabel }}
                                                                            </span>
                                                                            <span class="text-indigo-300">|</span>
                                                                            <span
                                                                                class="study-time-label whitespace-nowrap font-semibold text-slate-700">
                                                                                {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}
                                                                                ->
                                                                                {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                                                            </span>
                                                                        </div>
                                                                    @endforeach

                                                                    @if ($studySlots->count() > 3)
                                                                        <span
                                                                            class="text-[11px] font-semibold text-slate-500">
                                                                            +{{ $studySlots->count() - 3 }} more times
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            @else
                                                                <span class="text-slate-400">-</span>
                                                            @endif
                                                        </td>
                                                    @endif
                                                    <td class="student-col-status px-3 py-3 align-top">
                                                        @if ($hasStatusColumn && $student->is_active)
                                                            <span
                                                                class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                                                <span
                                                                    class="status-dot h-2 w-2 rounded-full bg-emerald-500"></span>Active
                                                            </span>
                                                        @elseif($hasStatusColumn)
                                                            <span
                                                                class="inline-flex items-center gap-2 rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700">
                                                                <span
                                                                    class="h-2 w-2 rounded-full bg-rose-500"></span>Inactive
                                                            </span>
                                                        @else
                                                            <span
                                                                class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td class="student-col-created px-3 py-3 align-top text-slate-500">
                                                        <span
                                                            class="whitespace-nowrap">{{ $student->created_at->format('M d, Y') }}</span>
                                                    </td>
                                                    <td class="student-col-actions px-3 py-3 align-top">
                                                        <div class="flex flex-wrap items-center justify-end gap-2">
                                                            <button @click="open = true" type="button"
                                                                class="whitespace-nowrap rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                                                Edit
                                                            </button>

                                                            @if ($hasStatusColumn)
                                                                <form method="POST"
                                                                    action="{{ route('admin.students.status', $student) }}"
                                                                    class="js-status-form"
                                                                    data-student="{{ $student->name }}"
                                                                    data-action="{{ $student->is_active ? 'set inactive' : 'set active' }}">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="submit"
                                                                        class="whitespace-nowrap rounded-lg border px-3 py-1.5 text-xs font-semibold {{ $student->is_active ? 'border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100' : 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                                                        {{ $student->is_active ? 'Set Inactive' : 'Set Active' }}
                                                                    </button>
                                                                </form>
                                                            @endif

                                                            <form method="POST"
                                                                action="{{ route('admin.students.destroy', $student) }}"
                                                                class="js-delete-form"
                                                                data-student="{{ $student->name }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="whitespace-nowrap rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">
                                                                    Delete
                                                                </button>
                                                            </form>
                                                        </div>

                                                        <div x-show="open" x-cloak
                                                            class="fixed inset-0 z-[70] grid place-items-center p-4"
                                                            aria-modal="true" role="dialog">
                                                            <div class="absolute inset-0 bg-slate-900/50"
                                                                @click="open = false">
                                                            </div>

                                                            <div
                                                                class="relative z-10 w-full max-w-xl rounded-3xl bg-white p-5 shadow-2xl">
                                                                <div class="mb-4 flex items-center justify-between">
                                                                    <h3 class="text-lg font-black text-slate-900">Edit
                                                                        Student
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
                                                                    action="{{ route('admin.students.update', $student) }}"
                                                                    enctype="multipart/form-data"
                                                                    class="js-edit-form space-y-4"
                                                                    data-student="{{ $student->name }}">
                                                                    @csrf
                                                                    @method('PUT')

                                                                    <div>
                                                                        <label for="edit_name_{{ $student->id }}"
                                                                            class="mb-1 block text-xs font-semibold text-slate-600">Full
                                                                            Name</label>
                                                                        <input id="edit_name_{{ $student->id }}"
                                                                            name="name" type="text"
                                                                            value="{{ $student->name }}"
                                                                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                    </div>

                                                                    <div>
                                                                        <label for="edit_email_{{ $student->id }}"
                                                                            class="mb-1 block text-xs font-semibold text-slate-600">Email</label>
                                                                        <input id="edit_email_{{ $student->id }}"
                                                                            name="email" type="email"
                                                                            value="{{ $student->email }}"
                                                                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                    </div>

                                                                    @if ($hasPhoneColumn ?? false)
                                                                        <div>
                                                                            <label
                                                                                for="edit_phone_number_{{ $student->id }}"
                                                                                class="mb-1 block text-xs font-semibold text-slate-600">Phone
                                                                                Number</label>
                                                                            <input
                                                                                id="edit_phone_number_{{ $student->id }}"
                                                                                name="phone_number" type="text"
                                                                                value="{{ $student->phone_number }}"
                                                                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                                                                                placeholder="+855 12 345 678">
                                                                        </div>
                                                                    @endif

                                                                    <div>
                                                                        <label for="edit_role_{{ $student->id }}"
                                                                            class="mb-1 block text-xs font-semibold text-slate-600">Role</label>
                                                                        <select id="edit_role_{{ $student->id }}"
                                                                            name="role"
                                                                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                            <option value="student"
                                                                                {{ $student->role === 'student' ? 'selected' : '' }}>
                                                                                Student</option>
                                                                            <option value="teacher"
                                                                                {{ $student->role === 'teacher' ? 'selected' : '' }}>
                                                                                Teacher</option>
                                                                            <option value="admin"
                                                                                {{ $student->role === 'admin' ? 'selected' : '' }}>
                                                                                Admin</option>
                                                                        </select>
                                                                        <p class="mt-1 text-[11px] text-slate-500">
                                                                            Changing role from student will remove this user
                                                                            from Student List.
                                                                        </p>
                                                                    </div>

                                                                    @if ($hasClassColumn)
                                                                        <div>
                                                                            <label
                                                                                for="edit_school_class_id_{{ $student->id }}"
                                                                                class="mb-1 block text-xs font-semibold text-slate-600">Home
                                                                                Class (Optional)</label>
                                                                            <select
                                                                                id="edit_school_class_id_{{ $student->id }}"
                                                                                name="school_class_id"
                                                                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                                <option value="">Unassigned</option>
                                                                                @foreach ($classes as $classOption)
                                                                                    <option value="{{ $classOption->id }}"
                                                                                        {{ (string) $student->school_class_id === (string) $classOption->id ? 'selected' : '' }}>
                                                                                        {{ $classOption->display_name }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>

                                                                        @if ($hasMajorSubjectColumn)
                                                                            <div>
                                                                                @php
                                                                                    $editSelectedMajorSubjectIds = [];
                                                                                    if (
                                                                                        ($hasStudentMajorSubjectsTable ??
                                                                                            false) &&
                                                                                        $student->relationLoaded(
                                                                                            'majorSubjects',
                                                                                        ) &&
                                                                                        $student->majorSubjects->isNotEmpty()
                                                                                    ) {
                                                                                        $editSelectedMajorSubjectIds = $student->majorSubjects
                                                                                            ->pluck('id')
                                                                                            ->map(
                                                                                                fn(
                                                                                                    $value,
                                                                                                ) => (string) $value,
                                                                                            )
                                                                                            ->values()
                                                                                            ->all();
                                                                                    } elseif (
                                                                                        $student->major_subject_id
                                                                                    ) {
                                                                                        $editSelectedMajorSubjectIds = [
                                                                                            (string) $student->major_subject_id,
                                                                                        ];
                                                                                    }
                                                                                @endphp
                                                                                <label
                                                                                    for="edit_major_subject_id_{{ $student->id }}"
                                                                                    class="mb-1 block text-xs font-semibold text-slate-600">Major
                                                                                    Subjects</label>
                                                                                <select
                                                                                    id="edit_major_subject_id_{{ $student->id }}"
                                                                                    name="major_subject_ids[]"
                                                                                    data-selected-list='@json($editSelectedMajorSubjectIds)'
                                                                                    data-checkbox-target="edit_major_subject_checkbox_list_{{ $student->id }}"
                                                                                    multiple size="5"
                                                                                    class="hidden">
                                                                                    <option value="">Select major
                                                                                        subjects</option>
                                                                                </select>
                                                                                <div id="edit_major_subject_checkbox_list_{{ $student->id }}"
                                                                                    class="min-h-[132px] space-y-2 rounded-xl border border-slate-200 bg-slate-50/50 p-3">
                                                                                </div>
                                                                                <p class="mt-1 text-[11px] text-slate-500">
                                                                                    You can select multiple major subjects.
                                                                                </p>
                                                                            </div>
                                                                        @endif

                                                                        @if ($hasClassStudyTimeColumn)
                                                                            <div>
                                                                                @php
                                                                                    $editSelectedStudyTimeIds = [];
                                                                                    if (
                                                                                        $student->relationLoaded(
                                                                                            'studyTimes',
                                                                                        ) &&
                                                                                        $student->studyTimes->isNotEmpty()
                                                                                    ) {
                                                                                        $editSelectedStudyTimeIds = $student->studyTimes
                                                                                            ->pluck('id')
                                                                                            ->map(
                                                                                                fn(
                                                                                                    $value,
                                                                                                ) => (string) $value,
                                                                                            )
                                                                                            ->values()
                                                                                            ->all();
                                                                                    } elseif (
                                                                                        $student->class_study_time_id
                                                                                    ) {
                                                                                        $editSelectedStudyTimeIds = [
                                                                                            (string) $student->class_study_time_id,
                                                                                        ];
                                                                                    }
                                                                                @endphp
                                                                                <label
                                                                                    for="edit_class_study_time_id_{{ $student->id }}"
                                                                                    class="mb-1 block text-xs font-semibold text-slate-600">Study
                                                                                    Time</label>
                                                                                <select
                                                                                    id="edit_class_study_time_id_{{ $student->id }}"
                                                                                    name="class_study_time_ids[]"
                                                                                    data-selected-list='@json($editSelectedStudyTimeIds)'
                                                                                    data-checkbox-target="edit_study_time_checkbox_list_{{ $student->id }}"
                                                                                    multiple size="4"
                                                                                    class="hidden">
                                                                                    <option value="">Select class
                                                                                        first</option>
                                                                                </select>
                                                                                <div id="edit_study_time_checkbox_list_{{ $student->id }}"
                                                                                    class="min-h-[132px] space-y-2 rounded-xl border border-slate-200 bg-slate-50/50 p-3">
                                                                                </div>
                                                                                <p class="mt-1 text-[11px] text-slate-500">
                                                                                    You can select multiple study times.</p>
                                                                            </div>
                                                                        @endif
                                                                    @endif

                                                                    <div>
                                                                        <label for="edit_avatar_image_{{ $student->id }}"
                                                                            class="mb-1 block text-xs font-semibold text-slate-600">Avatar
                                                                            Image</label>
                                                                        <input id="edit_avatar_image_{{ $student->id }}"
                                                                            name="avatar_image" type="file"
                                                                            accept="image/*"
                                                                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100 focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                        <p class="mt-1 text-[11px] text-slate-500">Leave
                                                                            empty
                                                                            to keep
                                                                            current avatar.</p>
                                                                    </div>

                                                                    <div class="grid gap-4 sm:grid-cols-2">
                                                                        <div>
                                                                            <label for="edit_password_{{ $student->id }}"
                                                                                class="mb-1 block text-xs font-semibold text-slate-600">New
                                                                                Password</label>
                                                                            <input id="edit_password_{{ $student->id }}"
                                                                                name="password" type="password"
                                                                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                                                                                placeholder="Leave blank to keep current">
                                                                        </div>
                                                                        <div>
                                                                            <label
                                                                                for="edit_password_confirmation_{{ $student->id }}"
                                                                                class="mb-1 block text-xs font-semibold text-slate-600">Confirm
                                                                                Password</label>
                                                                            <input
                                                                                id="edit_password_confirmation_{{ $student->id }}"
                                                                                name="password_confirmation"
                                                                                type="password"
                                                                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                        </div>
                                                                    </div>

                                                                    @if ($hasStatusColumn)
                                                                        <label
                                                                            class="flex items-center justify-between rounded-xl border border-slate-200 px-3 py-2.5">
                                                                            <span
                                                                                class="text-sm font-semibold text-slate-700">Status</span>
                                                                            <span
                                                                                class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500">
                                                                                <input type="checkbox" name="is_active"
                                                                                    value="1"
                                                                                    class="h-4 w-4 rounded border-slate-300"
                                                                                    {{ $student->is_active ? 'checked' : '' }}>
                                                                                Active
                                                                            </span>
                                                                        </label>
                                                                    @endif

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
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="{{ 6 + (($hasPhoneColumn ?? false) ? 1 : 0) + ($hasMajorSubjectColumn ? 1 : 0) + ($hasClassStudyTimeColumn ? 1 : 0) }}"
                                                        class="px-3 py-10 text-center text-sm text-slate-500">
                                                        No students found.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="mt-5">
                                {{ $students->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hasSwal = typeof Swal !== 'undefined';
            const subjectsByClass = @json($subjectsByClass ?? []);
            const studyTimesByClass = @json($studyTimesByClass ?? []);
            const subjectStudySlotsByClassSubject = @json($subjectStudySlotsByClassSubject ?? []);
            const classStudyTimeIdsByClassSubject = @json($classStudyTimeIdsByClassSubject ?? []);
            const classStudyTimeIdsBySubjectAll = @json($classStudyTimeIdsBySubjectAll ?? []);
            const classLabelById = @json(($classes ?? collect())->mapWithKeys(fn($classOption) => [(string) $classOption->id => (string) $classOption->display_name])->toArray());
            const periodLabels = @json($periodLabels ?? []);

            const to12Hour = (value) => {
                if (!value) {
                    return '';
                }
                const [hourRaw, minuteRaw] = String(value).split(':');
                const hour = Number(hourRaw || 0);
                const minute = String(minuteRaw || '00').padStart(2, '0');
                const suffix = hour >= 12 ? 'PM' : 'AM';
                const hour12 = hour % 12 === 0 ? 12 : hour % 12;
                return `${hour12.toString().padStart(2, '0')}:${minute} ${suffix}`;
            };

            const resetSelect = (select, placeholder) => {
                if (!select) {
                    return;
                }

                select.innerHTML = '';
                const option = document.createElement('option');
                option.value = '';
                option.textContent = placeholder;
                select.appendChild(option);
            };

            const dayLabels = {
                all: 'All Days',
                monday: 'Monday',
                tuesday: 'Tuesday',
                wednesday: 'Wednesday',
                thursday: 'Thursday',
                friday: 'Friday',
                saturday: 'Saturday',
                sunday: 'Sunday'
            };

            const parseSelectedIds = (rawValue) => {
                if (Array.isArray(rawValue)) {
                    return rawValue.map((item) => String(item)).filter((item) => item !== '');
                }

                const text = String(rawValue || '').trim();
                if (text === '') {
                    return [];
                }

                try {
                    const parsed = JSON.parse(text);
                    if (Array.isArray(parsed)) {
                        return parsed.map((item) => String(item)).filter((item) => item !== '');
                    }
                } catch (error) {
                    // Fallback below handles legacy comma-separated values.
                }

                return text.split(',').map((item) => item.trim()).filter((item) => item !== '');
            };

            const selectedIdsFromSelect = (select) => {
                if (!select) {
                    return [];
                }

                return Array.from(select.selectedOptions || [])
                    .map((option) => String(option.value || ''))
                    .filter((value) => value !== '');
            };

            const renderSelectAsCheckboxes = (select) => {
                if (!select) {
                    return;
                }

                const targetId = String(select.dataset.checkboxTarget || '').trim();
                if (targetId === '') {
                    return;
                }

                const container = document.getElementById(targetId);
                if (!container) {
                    return;
                }

                container.innerHTML = '';
                const options = Array.from(select.options || []).filter((option) => String(option.value ||
                    '') !== '');
                if (options.length === 0) {
                    const placeholder = document.createElement('p');
                    placeholder.className = 'text-xs font-medium text-slate-500';
                    placeholder.textContent = String(select.options?.[0]?.textContent ||
                    'No options available');
                    container.appendChild(placeholder);
                    return;
                }

                options.forEach((option, index) => {
                    const label = document.createElement('label');
                    label.className =
                        'flex items-start gap-2 rounded-lg border border-slate-200 bg-white px-2.5 py-2 text-sm text-slate-700 transition hover:bg-slate-50';

                    const input = document.createElement('input');
                    input.type = 'checkbox';
                    input.value = String(option.value || '');
                    input.checked = option.selected;
                    input.disabled = !!select.disabled;
                    input.className =
                        'mt-0.5 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500';
                    input.addEventListener('change', () => {
                        option.selected = input.checked;
                        select.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
                    });

                    const text = document.createElement('span');
                    text.className = 'leading-5';
                    text.textContent = String(option.textContent || `Option ${index + 1}`);

                    label.appendChild(input);
                    label.appendChild(text);
                    container.appendChild(label);
                });
            };

            const normalizeDay = (dayOfWeek) => {
                const day = String(dayOfWeek || '').toLowerCase().trim();
                return day !== '' ? day : 'all';
            };

            const normalizeTime = (value) => String(value || '').slice(0, 5);

            const slotsShareBaseTime = (classSlot, subjectSlot) => {
                return String(classSlot.period || '').toLowerCase() === String(subjectSlot.period || '')
                    .toLowerCase() &&
                    normalizeTime(classSlot.start_time) === normalizeTime(subjectSlot.start_time) &&
                    normalizeTime(classSlot.end_time) === normalizeTime(subjectSlot.end_time);
            };

            const slotDaysAreCompatible = (classSlot, subjectSlot) => {
                const classDay = normalizeDay(classSlot.day_of_week);
                const subjectDay = normalizeDay(subjectSlot.day_of_week);
                return classDay === subjectDay || classDay === 'all' || subjectDay === 'all';
            };

            const classSlotMatchesSubjectSchedule = (classSlot, subjectSlots) => {
                return subjectSlots.some((subjectSlot) => {
                    return slotsShareBaseTime(classSlot, subjectSlot) &&
                        slotDaysAreCompatible(classSlot, subjectSlot);
                });
            };

            const allSubjects = (() => {
                const seen = new Set();
                const rows = [];

                Object.values(subjectsByClass || {}).forEach((group) => {
                    if (!Array.isArray(group)) {
                        return;
                    }

                    group.forEach((item) => {
                        const id = String(item?.id || '');
                        if (id === '' || seen.has(id)) {
                            return;
                        }
                        seen.add(id);
                        rows.push(item);
                    });
                });

                return rows.sort((a, b) => String(a?.name || '').localeCompare(String(b?.name || '')));
            })();

            const allStudyTimes = (() => {
                const rows = [];
                Object.entries(studyTimesByClass || {}).forEach(([classId, group]) => {
                    if (!Array.isArray(group)) {
                        return;
                    }

                    group.forEach((slot) => {
                        rows.push({
                            ...slot,
                            school_class_id: Number(slot?.school_class_id ||
                                classId || 0)
                        });
                    });
                });

                return rows;
            })();

            const renderMajorSubjects = (select, classId, selectedIds = []) => {
                if (!select) {
                    return;
                }

                const key = String(classId || '');
                const subjects = key !== '' && Array.isArray(subjectsByClass?.[key]) ? subjectsByClass[key] :
            [];
                let placeholder = 'Select class first';
                if (subjects.length > 0 && key !== '') {
                    placeholder = 'Select major subjects';
                } else if (subjects.length === 0 && key !== '') {
                    placeholder = 'No subjects in selected class';
                }
                resetSelect(select, placeholder);
                const selectedSet = new Set(parseSelectedIds(selectedIds));

                subjects.forEach((item) => {
                    const option = document.createElement('option');
                    option.value = String(item.id);
                    const classLabel = classLabelById[String(item.school_class_id || '')] || '';
                    option.textContent = classLabel ? `${item.name} (${classLabel})` : `${item.name}`;
                    if (selectedSet.has(String(item.id))) {
                        option.selected = true;
                    }
                    select.appendChild(option);
                });

                select.disabled = key === '' || subjects.length === 0;
                renderSelectAsCheckboxes(select);
            };

            const renderStudyTimes = (select, classId, selectedIds = [], subjectIds = [], requireSubjectMatch =
                false, autoSelectAll = false) => {
                if (!select) {
                    return;
                }

                const key = String(classId || '');
                let slots = allStudyTimes;
                const selectedSubjectIds = parseSelectedIds(subjectIds);

                if (requireSubjectMatch && selectedSubjectIds.length === 0) {
                    slots = key ? allStudyTimes.filter((item) => String(item.school_class_id || '') === key) :
                    [];
                } else if (selectedSubjectIds.length > 0) {
                    const allowedStudyTimeIds = new Set();
                    selectedSubjectIds.forEach((subjectKey) => {
                        const idsForSubject = key !== '' ?
                            classStudyTimeIdsByClassSubject?.[key]?.[String(subjectKey)] :
                            classStudyTimeIdsBySubjectAll[String(subjectKey)];
                        if (Array.isArray(idsForSubject)) {
                            idsForSubject.forEach((id) => allowedStudyTimeIds.add(String(id)));
                        }
                    });

                    if (allowedStudyTimeIds.size > 0) {
                        slots = allStudyTimes.filter((item) => {
                            const matchesAllowedStudyTime = allowedStudyTimeIds.has(String(item.id));
                            const matchesClass = key === '' || String(item.school_class_id || '') ===
                                key;
                            return matchesAllowedStudyTime && matchesClass;
                        });
                    } else {
                        const subjectSlots = selectedSubjectIds.flatMap((subjectKey) => {
                            if (key !== '') {
                                const slotsForSubject = subjectStudySlotsByClassSubject?.[key]?.[String(
                                    subjectKey)];
                                return Array.isArray(slotsForSubject) ? slotsForSubject : [];
                            }

                            return Object.values(subjectStudySlotsByClassSubject || {}).flatMap((
                                subjectMap) => {
                                const slotsForSubject = subjectMap?.[String(subjectKey)];
                                return Array.isArray(slotsForSubject) ? slotsForSubject : [];
                            });
                        });
                        slots = allStudyTimes.filter((item) => {
                            if (key !== '' && String(item.school_class_id || '') !== key) {
                                return false;
                            }
                            return classSlotMatchesSubjectSchedule(item, subjectSlots);
                        });
                    }
                } else if (key !== '') {
                    slots = allStudyTimes.filter((item) => String(item.school_class_id || '') === key);
                } else {
                    slots = [];
                }

                let placeholder = 'Select major subjects first';
                if (requireSubjectMatch && selectedSubjectIds.length === 0 && key === '') {
                    placeholder = 'Select class first';
                } else if (slots.length > 0) {
                    placeholder = 'Select study time';
                } else if (selectedSubjectIds.length > 0) {
                    placeholder = 'No study time for selected subjects';
                } else if (key !== '') {
                    placeholder = 'No study times in selected class';
                }
                resetSelect(select, placeholder);
                const selectedSet = new Set(parseSelectedIds(selectedIds));

                slots.forEach((item) => {
                    const option = document.createElement('option');
                    option.value = String(item.id);
                    const periodKey = String(item.period || '').toLowerCase();
                    const period = periodLabels[periodKey] || (periodKey ? (periodKey.charAt(0)
                        .toUpperCase() + periodKey.slice(1)) : 'Custom');
                    const dayKey = String(item.day_of_week || 'all').toLowerCase();
                    const dayLabel = dayLabels[dayKey] || (dayKey ? (dayKey.charAt(0).toUpperCase() +
                        dayKey.slice(1)) : 'All Days');
                    const slotClassLabel = classLabelById[String(item.school_class_id || '')] ||
                    'Class';
                    option.textContent =
                        `${slotClassLabel} | ${dayLabel} | ${period}: ${to12Hour(item.start_time)} -> ${to12Hour(item.end_time)}`;
                    if (selectedSet.has(String(item.id))) {
                        option.selected = true;
                    }
                    select.appendChild(option);
                });

                if (autoSelectAll && slots.length > 0 && selectedSet.size === 0) {
                    Array.from(select.options).forEach((option) => {
                        if (String(option.value || '') !== '') {
                            option.selected = true;
                        }
                    });
                }

                select.disabled = slots.length === 0;
                renderSelectAsCheckboxes(select);
            };

            const wireClassDependentFields = (classSelect, majorSelect, studyTimeSelect) => {
                if (!classSelect) {
                    return;
                }

                const apply = (useCurrentSelection = true) => {
                    const classId = classSelect.value || '';
                    const majorSelected = useCurrentSelection ?
                        (() => {
                            const fromCurrentSelection = selectedIdsFromSelect(majorSelect);
                            if (fromCurrentSelection.length > 0) {
                                return fromCurrentSelection;
                            }
                            return parseSelectedIds(majorSelect?.dataset.selectedList || majorSelect
                                ?.dataset.selected || '');
                        })() :
                        [];
                    const studySelected = useCurrentSelection ?
                        (() => {
                            const fromCurrentSelection = selectedIdsFromSelect(studyTimeSelect);
                            if (fromCurrentSelection.length > 0) {
                                return fromCurrentSelection;
                            }
                            return parseSelectedIds(studyTimeSelect?.dataset.selectedList ||
                                studyTimeSelect?.dataset.selected || '');
                        })() :
                        [];

                    if (majorSelect) {
                        renderMajorSubjects(majorSelect, classId, majorSelected);
                        majorSelect.dataset.selectedList = '';
                        majorSelect.dataset.selected = '';
                    }

                    if (studyTimeSelect) {
                        const requireSubjectMatch = !!majorSelect;
                        const selectedSubjectIds = majorSelect ?
                            (() => {
                                const selectedValues = selectedIdsFromSelect(majorSelect);
                                return selectedValues.length > 0 ? selectedValues : parseSelectedIds(
                                    majorSelected);
                            })() :
                            [];
                        renderStudyTimes(
                            studyTimeSelect,
                            classId,
                            studySelected,
                            selectedSubjectIds,
                            requireSubjectMatch,
                            !useCurrentSelection
                        );
                        studyTimeSelect.dataset.selectedList = '';
                        studyTimeSelect.dataset.selected = '';
                    }
                };

                apply(true);
                classSelect.addEventListener('change', () => apply(false));

                if (majorSelect && studyTimeSelect) {
                    majorSelect.addEventListener('change', () => {
                        const selectedMajorIds = selectedIdsFromSelect(majorSelect);
                        renderStudyTimes(studyTimeSelect, classSelect.value || '', [], selectedMajorIds,
                            true, true);
                    });
                }
            };

            const manageStudyTimeTop = document.getElementById('manage_study_time_top');
            const manageStudyTimeInline = document.getElementById('manage_study_time_inline');

            const buildTimeStudiesUrl = (classId = '', subjectId = '') => {
                const baseUrl = manageStudyTimeTop?.dataset.baseUrl || manageStudyTimeInline?.dataset.baseUrl;
                if (!baseUrl) {
                    return '#';
                }

                const url = new URL(baseUrl, window.location.origin);
                const selectedClassId = String(classId || '').trim();
                const selectedSubjectId = String(subjectId || '').trim();

                url.searchParams.set('tab', selectedSubjectId ? 'subject' : 'class');
                if (selectedClassId) {
                    url.searchParams.set('class_id', selectedClassId);
                }
                if (selectedSubjectId) {
                    url.searchParams.set('subject_id', selectedSubjectId);
                }

                return url.toString();
            };

            const syncManageStudyTimeLinks = () => {
                const createClassSelect = document.getElementById('school_class_id');
                const createMajorSelect = document.getElementById('major_subject_id');
                const selectedMajorIds = selectedIdsFromSelect(createMajorSelect);
                const href = buildTimeStudiesUrl(createClassSelect?.value || '', selectedMajorIds[0] || '');

                if (manageStudyTimeTop) {
                    manageStudyTimeTop.href = href;
                }
                if (manageStudyTimeInline) {
                    manageStudyTimeInline.href = href;
                }
            };

            wireClassDependentFields(
                document.getElementById('school_class_id'),
                document.getElementById('major_subject_id'),
                document.getElementById('class_study_time_id')
            );

            syncManageStudyTimeLinks();
            document.getElementById('school_class_id')?.addEventListener('change', syncManageStudyTimeLinks);
            document.getElementById('major_subject_id')?.addEventListener('change', syncManageStudyTimeLinks);

            document.querySelectorAll('[id^="edit_school_class_id_"]').forEach((classSelect) => {
                const suffix = classSelect.id.replace('edit_school_class_id_', '');
                wireClassDependentFields(
                    classSelect,
                    document.getElementById(`edit_major_subject_id_${suffix}`),
                    document.getElementById(`edit_class_study_time_id_${suffix}`)
                );
            });

            const selectedValuesByName = (form, inputName) => {
                const select = form?.querySelector(`[name="${inputName}"]`);
                return selectedIdsFromSelect(select);
            };

            const validateStudentSelections = (form) => {
                if (!form) {
                    return null;
                }

                const role = String(form.querySelector('[name="role"]')?.value || 'student').toLowerCase();
                if (role !== 'student') {
                    return null;
                }

                const classId = String(form.querySelector('[name="school_class_id"]')?.value || '').trim();
                if (classId === '') {
                    return null;
                }

                const selectedMajorIds = selectedValuesByName(form, 'major_subject_ids[]');
                if (selectedMajorIds.length === 0) {
                    return 'Select at least one major subject for the selected class.';
                }

                const selectedStudyTimeIds = selectedValuesByName(form, 'class_study_time_ids[]');
                if (selectedStudyTimeIds.length === 0) {
                    return 'Select at least one study time for the selected class.';
                }

                return null;
            };

            const confirmSubmit = (selector, buildConfig) => {
                if (!hasSwal) {
                    return;
                }

                document.querySelectorAll(selector).forEach((form) => {
                    form.addEventListener('submit', function(event) {
                        if (form.dataset.confirmed === '1') {
                            return;
                        }

                        event.preventDefault();
                        const selectionMessage = validateStudentSelections(form);
                        if (selectionMessage) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Selection Required',
                                text: selectionMessage
                            });
                            return;
                        }

                        const config = buildConfig(form);

                        Swal.fire(config).then((result) => {
                            if (result.isConfirmed) {
                                form.dataset.confirmed = '1';
                                form.submit();
                            }
                        });
                    });
                });
            };

            confirmSubmit('.js-create-form', () => ({
                title: 'Create student account?',
                text: 'A new student user will be saved.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, create',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#4f46e5'
            }));

            confirmSubmit('.js-edit-form', (form) => ({
                title: 'Save changes?',
                text: `Update profile for ${form.dataset.student || 'this student'}.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, save',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#4f46e5'
            }));

            confirmSubmit('.js-status-form', (form) => ({
                title: 'Change student status?',
                text: `This will ${form.dataset.action || 'change status'} for ${form.dataset.student || 'the student'}.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, continue',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#f59e0b'
            }));

            confirmSubmit('.js-delete-form', (form) => ({
                title: 'Delete student?',
                text: `Delete ${form.dataset.student || 'this student'} permanently. This cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc2626'
            }));

            const validationErrors = @json($errors->all());
            if (hasSwal && validationErrors.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: validationErrors[0]
                });
                return;
            }

            if (hasSwal) {
                @if (session('error'))
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: @json(session('error'))
                    });
                @elseif (session('warning'))
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: @json(session('warning'))
                    });
                @elseif (session('success'))
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: @json(session('success')),
                        timer: 2200,
                        showConfirmButton: false
                    });
                @endif
            }
        });
    </script>
@endsection
