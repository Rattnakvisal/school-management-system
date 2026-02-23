@extends('layout.admin.navbar.navbar')

@section('page')
    <div class="teacher-stage space-y-6">
        <section
            class="teacher-reveal admin-page-header overflow-hidden"
            style="--sd: 1;">
            <div class="flex flex-wrap items-center justify-between gap-5">
                <div>
                    <h1 class="admin-page-title text-3xl font-black tracking-tight">Teacher Management</h1>
                    <p class="admin-page-subtitle mt-1 text-sm">Create, edit, activate, deactivate, and remove teacher accounts.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-xs font-semibold">
                    <span class="admin-page-stat">Total: {{ $stats['total'] }}</span>
                    <span class="admin-page-stat admin-page-stat--emerald">Active:
                        {{ $stats['active'] }}</span>
                    <span class="admin-page-stat admin-page-stat--rose">Inactive:
                        {{ $stats['inactive'] }}</span>
                </div>
            </div>
        </section>

        @if (session('success'))
            <div class="teacher-reveal rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"
                style="--sd: 2;">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="teacher-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700"
                style="--sd: 2;">
                {{ session('error') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="teacher-reveal rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700"
                style="--sd: 2;">
                {{ session('warning') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="teacher-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
                style="--sd: 2;">
                <div class="font-semibold">Please check the form fields and try again.</div>
            </div>
        @endif

        <div class="grid gap-6 xl:grid-cols-12">
            <section
                class="teacher-reveal teacher-float rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-4"
                style="--sd: 3;">
                <h2 class="text-lg font-black text-slate-900">Create teacher</h2>
                <p class="mt-1 text-xs text-slate-500">New account will be saved with role `teacher`.</p>

                <form method="POST" action="{{ route('admin.teachers.store') }}" enctype="multipart/form-data"
                    class="js-create-form mt-5 space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="mb-1 block text-xs font-semibold text-slate-600">Full Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                            placeholder="teacher full name">
                        @error('name')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-1 block text-xs font-semibold text-slate-600">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                            placeholder="teacher@example.com">
                        @error('email')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

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
                                <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300" {{ old('is_active', '1') ? 'checked' : '' }}>
                                Active
                            </span>
                        </label>
                    @endif

                    <button type="submit"
                        class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">
                        Create teacher
                    </button>
                </form>
            </section>

            <section
                class="teacher-reveal teacher-float rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-8"
                style="--sd: 4;">
                <div x-data="{ filterOpen: false }" @open-filter-panel.window="filterOpen = true" class="space-y-4">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-lg font-black text-slate-900">teacher List</h2>
                        <button type="button" @click="filterOpen = true"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M3 5h18l-7 8v5l-4 2v-7L3 5z"></path>
                            </svg>
                            Filters
                        </button>
                    </div>

                    <div x-show="filterOpen" x-cloak x-transition.opacity
                        class="fixed inset-0 z-[80] bg-slate-900/40" @click="filterOpen = false"></div>

                    <div class="grid gap-4">
                        <aside x-show="filterOpen" x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="translate-x-full"
                            x-transition:enter-end="translate-x-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="translate-x-0"
                            x-transition:leave-end="translate-x-full"
                            class="fixed inset-y-0 right-0 z-[81] w-full max-w-md transform border-l border-slate-200 bg-white shadow-2xl">
                            <div class="flex h-full flex-col">
                                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                                    <h3 class="text-3xl font-black text-slate-900">Filters</h3>
                                    <div class="flex items-center gap-4">
                                        <a href="{{ route('admin.teachers.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">
                                            Clear All
                                        </a>
                                        <button type="button" @click="filterOpen = false"
                                            class="text-2xl font-bold leading-none text-slate-700 hover:text-slate-900" aria-label="Close filters">
                                            &times;
                                        </button>
                                    </div>
                                </div>

                                <form method="GET" action="{{ route('admin.teachers.index') }}" class="flex min-h-0 flex-1 flex-col"
                                    @submit="filterOpen = false">
                                    <div class="flex-1 space-y-5 overflow-y-auto px-5 py-4">
                                        <section class="space-y-2">
                                            <h4 class="text-xl font-bold text-slate-900">Search</h4>
                                            <input id="q" name="q" type="text" value="{{ $search }}"
                                                placeholder="Search by name or email"
                                                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                        </section>
                                        @if ($hasStatusColumn)
                                            <section class="space-y-2">
                                                <h4 class="text-xl font-bold text-slate-900">Status</h4>
                                                <select name="status"
                                                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
                                                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                                                    <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                    <div class="max-h-[560px] overflow-auto">
                    <table class="w-full min-w-[920px] text-left text-sm">
                        <thead
                            class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-3 py-3 font-semibold">teacher</th>
                                <th class="px-3 py-3 font-semibold">Email</th>
                                <th class="px-3 py-3 font-semibold">Status</th>
                                <th class="px-3 py-3 font-semibold">Created</th>
                                <th class="px-3 py-3 font-semibold text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($teachers as $teacher)
                                <tr class="align-top hover:bg-slate-50/80" x-data="{ open: false }">
                                    <td class="px-3 py-3">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $teacher->avatar_url }}" alt="{{ $teacher->name }}"
                                                class="h-9 w-9 rounded-full object-cover ring-1 ring-slate-200">
                                            <div>
                                                <div class="font-semibold text-slate-800">{{ $teacher->name }}</div>
                                                <div class="text-xs text-slate-400">ID #{{ $teacher->formatted_id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-slate-600">{{ $teacher->email }}</td>
                                    <td class="px-3 py-3">
                                        @if ($hasStatusColumn && $teacher->is_active)
                                            <span
                                                class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                                <span class="status-dot h-2 w-2 rounded-full bg-emerald-500"></span>Active
                                            </span>
                                        @elseif($hasStatusColumn)
                                            <span
                                                class="inline-flex items-center gap-2 rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700">
                                                <span class="h-2 w-2 rounded-full bg-rose-500"></span>Inactive
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-slate-500">{{ $teacher->created_at->format('M d, Y') }}</td>
                                    <td class="px-3 py-3">
                                        <div class="flex flex-wrap items-center justify-end gap-2">
                                            <button @click="open = true" type="button"
                                                class="whitespace-nowrap rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                                Edit
                                            </button>

                                            @if ($hasStatusColumn)
                                                <form method="POST" action="{{ route('admin.teachers.status', $teacher) }}"
                                                    class="js-status-form" data-teacher="{{ $teacher->name }}"
                                                    data-action="{{ $teacher->is_active ? 'set inactive' : 'set active' }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="whitespace-nowrap rounded-lg border px-3 py-1.5 text-xs font-semibold {{ $teacher->is_active ? 'border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100' : 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                                        {{ $teacher->is_active ? 'Set Inactive' : 'Set Active' }}
                                                    </button>
                                                </form>
                                            @endif

                                            <form method="POST" action="{{ route('admin.teachers.destroy', $teacher) }}"
                                                class="js-delete-form" data-teacher="{{ $teacher->name }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="whitespace-nowrap rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>

                                        <div x-show="open" x-cloak class="fixed inset-0 z-[70] grid place-items-center p-4"
                                            aria-modal="true" role="dialog">
                                            <div class="absolute inset-0 bg-slate-900/50" @click="open = false"></div>

                                            <div class="relative z-10 w-full max-w-xl rounded-3xl bg-white p-5 shadow-2xl">
                                                <div class="mb-4 flex items-center justify-between">
                                                    <h3 class="text-lg font-black text-slate-900">Edit teacher</h3>
                                                    <button type="button" @click="open = false"
                                                        class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                                            <path
                                                                d="M18.3 5.71 12 12l6.3 6.29-1.41 1.42L10.59 13.4 4.3 19.7 2.89 18.3 9.17 12 2.9 5.71 4.3 4.29l6.29 6.3 6.3-6.3 1.41 1.42Z" />
                                                        </svg>
                                                    </button>
                                                </div>

                                                <form method="POST" action="{{ route('admin.teachers.update', $teacher) }}"
                                                    enctype="multipart/form-data" class="js-edit-form space-y-4"
                                                    data-teacher="{{ $teacher->name }}">
                                                    @csrf
                                                    @method('PUT')

                                                    <div>
                                                        <label for="edit_name_{{ $teacher->id }}"
                                                            class="mb-1 block text-xs font-semibold text-slate-600">Full
                                                            Name</label>
                                                        <input id="edit_name_{{ $teacher->id }}" name="name" type="text"
                                                            value="{{ $teacher->name }}"
                                                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                    </div>

                                                    <div>
                                                        <label for="edit_email_{{ $teacher->id }}"
                                                            class="mb-1 block text-xs font-semibold text-slate-600">Email</label>
                                                        <input id="edit_email_{{ $teacher->id }}" name="email" type="email"
                                                            value="{{ $teacher->email }}"
                                                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                    </div>

                                                    <div>
                                                        <label for="edit_avatar_image_{{ $teacher->id }}"
                                                            class="mb-1 block text-xs font-semibold text-slate-600">Avatar
                                                            Image</label>
                                                        <input id="edit_avatar_image_{{ $teacher->id }}" name="avatar_image"
                                                            type="file" accept="image/*"
                                                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100 focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                        <p class="mt-1 text-[11px] text-slate-500">Leave empty to keep
                                                            current avatar.</p>
                                                    </div>

                                                    <div class="grid gap-4 sm:grid-cols-2">
                                                        <div>
                                                            <label for="edit_password_{{ $teacher->id }}"
                                                                class="mb-1 block text-xs font-semibold text-slate-600">New
                                                                Password</label>
                                                            <input id="edit_password_{{ $teacher->id }}" name="password"
                                                                type="password"
                                                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                                                                placeholder="Leave blank to keep current">
                                                        </div>
                                                        <div>
                                                            <label for="edit_password_confirmation_{{ $teacher->id }}"
                                                                class="mb-1 block text-xs font-semibold text-slate-600">Confirm
                                                                Password</label>
                                                            <input id="edit_password_confirmation_{{ $teacher->id }}"
                                                                name="password_confirmation" type="password"
                                                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                        </div>
                                                    </div>

                                                    @if ($hasStatusColumn)
                                                        <label
                                                            class="flex items-center justify-between rounded-xl border border-slate-200 px-3 py-2.5">
                                                            <span class="text-sm font-semibold text-slate-700">Status</span>
                                                            <span
                                                                class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500">
                                                                <input type="checkbox" name="is_active" value="1"
                                                                    class="h-4 w-4 rounded border-slate-300" {{ $teacher->is_active ? 'checked' : '' }}>
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
                                    <td colspan="5" class="px-3 py-10 text-center text-sm text-slate-500">
                                        No teachers found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                </div>

                <div class="mt-5">
                    {{ $teachers->links() }}
                </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Swal === 'undefined') {
                return;
            }

            const confirmSubmit = (selector, buildConfig) => {
                document.querySelectorAll(selector).forEach((form) => {
                    form.addEventListener('submit', function (event) {
                        if (form.dataset.confirmed === '1') {
                            return;
                        }

                        event.preventDefault();
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
                title: 'Create teacher account?',
                text: 'A new teacher user will be saved.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, create',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#4f46e5'
            }));

            confirmSubmit('.js-edit-form', (form) => ({
                title: 'Save changes?',
                text: `Update profile for ${form.dataset.teacher || 'this teacher'}.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, save',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#4f46e5'
            }));

            confirmSubmit('.js-status-form', (form) => ({
                title: 'Change teacher status?',
                text: `This will ${form.dataset.action || 'change status'} for ${form.dataset.teacher || 'the teacher'}.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, continue',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#f59e0b'
            }));

            confirmSubmit('.js-delete-form', (form) => ({
                title: 'Delete teacher?',
                text: `Delete ${form.dataset.teacher || 'this teacher'} permanently. This cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc2626'
            }));

            const validationErrors = @json($errors->all());
            if (validationErrors.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: validationErrors[0]
                });
                return;
            }

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
            });
    </script>
@endsection
