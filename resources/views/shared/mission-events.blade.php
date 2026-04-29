@php
    $audienceLabels = ['all' => 'Staff and Teachers', 'teacher' => 'Teachers', 'staff' => 'Staff'];
    $priorityClasses = [
        'low' => 'bg-slate-50 text-slate-600 ring-slate-200',
        'normal' => 'bg-indigo-50 text-indigo-700 ring-indigo-100',
        'high' => 'bg-amber-50 text-amber-700 ring-amber-100',
        'urgent' => 'bg-red-50 text-red-700 ring-red-100',
    ];
    $missionTotal = (int) (is_object($missions) && method_exists($missions, 'total')
        ? $missions->total()
        : collect($missions ?? [])->count());
    $missionStatCards = [
        [
            'label' => 'Mission Events',
            'activeLabel' => 'Active',
            'active' => $missionTotal,
            'total' => $missionTotal,
            'icon' => 'mission',
            'tone' => 'from-indigo-100 to-white text-indigo-600',
        ],
    ];
    $canSubmitMission = isset($missionSubmissions, $submitRouteName, $deleteRouteName);
@endphp

<div class="student-stage space-y-6">
    <section class="student-reveal admin-page-header admin-page-header--iconic overflow-hidden" style="--sd: 1;">
        <div class="admin-page-header__main flex flex-wrap items-start justify-between gap-5">
            <div class="admin-page-header__intro min-w-0 flex-1">
                <div class="admin-page-header__title-row flex items-center gap-3">
                    <span class="admin-page-header__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 4v16" />
                            <path d="M7 5h9.5l-1 2 1 2H7" />
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <div class="admin-page-header__eyebrow">Mission Center</div>
                        <h1 class="admin-page-title text-2xl font-black tracking-tight sm:text-3xl">{{ $title }}</h1>
                    </div>
                </div>

                <p class="admin-page-subtitle mt-2 text-sm">{{ $subtitle }}</p>
            </div>

        </div>
    </section>

    <x-admin.stat-cards :cards="$missionStatCards" reveal-class="student-reveal" float-class="student-float"
        grid-class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4" />

    @if (session('success'))
        <div class="js-inline-flash student-reveal rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"
            style="--sd: 2;">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="js-inline-flash student-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700"
            style="--sd: 2;">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="student-reveal student-float rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200"
        style="--sd: 2;">
        <div class="mb-5 flex items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-black text-slate-900">Mission List</h2>
                <p class="mt-1 text-xs text-slate-500">Review active mission events from admin.</p>
            </div>
        </div>

        <div class="grid gap-3 lg:grid-cols-2">
        @forelse ($missions as $mission)
            @php
                $submission = $canSubmitMission ? $missionSubmissions->get($mission->id) : null;
                $isSubmitted = filled($submission?->submission_file_path);
            @endphp
            <article class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4" x-data="{ detailOpen: false }">
                <div class="mb-4 flex flex-wrap items-center gap-2">
                    <span class="rounded-full px-2.5 py-1 text-xs font-bold ring-1 {{ $priorityClasses[$mission->priority] ?? $priorityClasses['normal'] }}">
                        {{ ucfirst($mission->priority) }}
                    </span>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">
                        {{ $audienceLabels[$mission->audience] ?? ucfirst($mission->audience) }}
                    </span>
                </div>

                <h3 class="text-base font-black text-slate-900">{{ $mission->title }}</h3>
                <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">{{ $mission->description }}</p>

                <div class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                    <div class="rounded-2xl bg-white p-4 ring-1 ring-slate-100">
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-400">Starts</div>
                        <div class="mt-1 font-bold text-slate-800">
                            {{ $mission->starts_at?->format('M d, Y h:i A') ?? 'Not scheduled' }}
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white p-4 ring-1 ring-slate-100">
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-400">Ends</div>
                        <div class="mt-1 font-bold text-slate-800">
                            {{ $mission->ends_at?->format('M d, Y h:i A') ?? 'Open' }}
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-center justify-end gap-3 text-xs font-semibold text-slate-500">
                    <span>Posted {{ $mission->created_at?->diffForHumans() }}</span>
                </div>

                @if ($canSubmitMission)
                    <div class="mt-4 rounded-2xl bg-white p-4 ring-1 ring-slate-100">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <div class="text-xs font-bold uppercase tracking-wide text-slate-400">Submission</div>
                                @if ($isSubmitted)
                                    @php
                                        $fileSize = (int) ($submission->submission_file_size ?? 0);
                                        $fileSizeLabel = 'Unknown size';

                                        if ($fileSize > 0) {
                                            $units = ['B', 'KB', 'MB', 'GB'];
                                            $unitIndex = 0;
                                            $displaySize = (float) $fileSize;

                                            while ($displaySize >= 1024 && $unitIndex < count($units) - 1) {
                                                $displaySize /= 1024;
                                                $unitIndex++;
                                            }

                                            $fileSizeLabel = rtrim(rtrim(number_format($displaySize, 1), '0'), '.') . ' ' . $units[$unitIndex];
                                        }
                                    @endphp
                                    <button type="button" @click="detailOpen = true"
                                        class="mt-2 inline-flex max-w-full rounded-lg border border-emerald-100 bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-700 hover:bg-emerald-100">
                                        <span class="truncate">{{ $submission->submission_file_name ?: 'Submitted file' }}</span>
                                    </button>
                                    @if ($submission?->submitted_at)
                                        <div class="mt-1 text-xs text-slate-400">
                                            {{ \Illuminate\Support\Carbon::parse($submission->submitted_at)->diffForHumans() }}
                                        </div>
                                    @endif
                                    <div x-show="detailOpen" x-cloak x-transition.opacity
                                        class="fixed inset-0 z-[80] bg-slate-900/40" @click="detailOpen = false"></div>
                                    <div x-show="detailOpen" x-cloak x-transition
                                        class="fixed inset-x-3 top-6 z-[81] mx-auto max-h-[calc(100vh-3rem)] w-full max-w-3xl overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl sm:top-10">
                                        <div class="flex max-h-[calc(100vh-3rem)] flex-col">
                                            <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-4">
                                                <div class="min-w-0">
                                                    <h3 class="truncate text-lg font-black text-slate-900">Submission Details</h3>
                                                    <p class="mt-1 text-xs text-slate-500">{{ $mission->title }} - 1 file received.</p>
                                                </div>
                                                <button type="button" @click="detailOpen = false"
                                                    class="grid h-11 w-11 place-items-center rounded-full border border-slate-200 text-xl font-bold text-slate-500 hover:bg-slate-50"
                                                    aria-label="Close submission details">&times;</button>
                                            </div>
                                            <div class="min-h-0 flex-1 overflow-y-auto px-5 py-4">
                                                <article class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                                        <div class="min-w-0">
                                                            <div class="flex flex-wrap items-center gap-2">
                                                                <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700 ring-1 ring-indigo-100">
                                                                    {{ ucfirst((string) (auth()->user()?->role ?? 'User')) }}
                                                                </span>
                                                                <span class="text-sm font-black text-slate-900">{{ auth()->user()?->name ?? 'User' }}</span>
                                                            </div>
                                                            <div class="mt-1 text-xs text-slate-400">{{ auth()->user()?->email ?? 'No email' }}</div>
                                                            <div class="mt-3">
                                                                <a href="{{ \Illuminate\Support\Facades\Storage::url($submission->submission_file_path) }}"
                                                                    target="_blank"
                                                                    class="inline-flex max-w-full items-center gap-2 rounded-xl border border-emerald-100 bg-white px-3 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-50">
                                                                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" />
                                                                        <path d="M14 2v6h6" />
                                                                    </svg>
                                                                    <span class="truncate">{{ $submission->submission_file_name ?: 'Submitted file' }}</span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="shrink-0 rounded-2xl bg-white px-3 py-2 text-xs font-semibold text-slate-500 ring-1 ring-slate-100">
                                                            <div>{{ $fileSizeLabel }}</div>
                                                            <div class="mt-1">
                                                                {{ $submission?->submitted_at ? \Illuminate\Support\Carbon::parse($submission->submitted_at)->format('M d, Y h:i A') : 'No date' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </article>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-2 text-sm font-semibold text-slate-400">No file sent yet</div>
                                @endif
                            </div>

                            <form method="POST" action="{{ route($submitRouteName, $mission) }}"
                                enctype="multipart/form-data"
                                class="js-mission-submit-form flex flex-wrap items-center justify-end gap-2"
                                data-mission="{{ $mission->title }}"
                                x-data="{ fileName: '' }"
                                novalidate>
                                @csrf
                                <input id="mission_submission_{{ $mission->id }}" name="submission_file" type="file"
                                    class="sr-only" required @change="fileName = $event.target.files[0]?.name || ''">
                                <label for="mission_submission_{{ $mission->id }}"
                                    class="inline-flex cursor-pointer items-center justify-center rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                    {{ $isSubmitted ? 'Edit file' : 'Choose file' }}
                                </label>
                                <span class="max-w-[140px] truncate text-xs font-medium text-slate-400"
                                    x-text="fileName || 'No file chosen'"></span>
                                <button type="submit"
                                    class="rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                    Send
                                </button>
                            </form>
                        </div>

                        @if ($isSubmitted)
                            <form method="POST" action="{{ route($deleteRouteName, $mission) }}"
                                class="js-mission-delete-form mt-3 flex justify-end"
                                data-mission="{{ $mission->title }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">
                                    Remove submitted file
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center lg:col-span-2">
                <h2 class="text-lg font-black text-slate-900">No mission events yet</h2>
                <p class="mt-2 text-sm text-slate-500">New mission events from admin will appear here.</p>
                <a href="{{ $emptyRoute }}"
                    class="mt-5 inline-flex rounded-xl bg-slate-950 px-4 py-2 text-sm font-bold text-white hover:bg-slate-800">
                    Back to dashboard
                </a>
            </div>
        @endforelse
        </div>

        <div class="mt-5">
            {{ $missions->links() }}
        </div>
    </section>
</div>

@if ($canSubmitMission)
    @php
        $missionPageData = [
            'validationErrors' => $errors->all(),
            'flash' => [
                'success' => session('success'),
                'successTitle' => session('success_title', 'Mission file sent'),
                'error' => $errors->any() ? $errors->first() : '',
            ],
        ];
    @endphp
    <script id="shared-mission-data" type="application/json">{!! json_encode($missionPageData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const hasSwal = typeof window.Swal !== 'undefined';
            const dataNode = document.getElementById('shared-mission-data');
            let pageData = { validationErrors: [], flash: { success: '', successTitle: 'Mission file sent', error: '' } };

            if (dataNode) {
                try {
                    pageData = { ...pageData, ...JSON.parse(dataNode.textContent || '{}') };
                } catch (error) {
                    console.error('Unable to parse mission data.', error);
                }
            }

            const alertMessage = (icon, title, text) => {
                if (!text) return;
                document.querySelectorAll('.js-inline-flash').forEach((element) => element.classList.add('hidden'));

                if (hasSwal) {
                    window.Swal.fire({ icon, title, text, confirmButtonColor: '#4f46e5' });
                    return;
                }

                window.alert(`${title}\n\n${text}`);
            };

            const confirmAction = (options) => {
                if (hasSwal) {
                    return window.Swal.fire(options).then((result) => Boolean(result && result.isConfirmed));
                }

                return Promise.resolve(window.confirm(`${options.title}\n\n${options.text}`));
            };

            if ((pageData.validationErrors || []).length > 0) {
                alertMessage('error', 'Upload failed', String(pageData.validationErrors[0] || 'Please choose a valid file.'));
            } else if (pageData.flash && pageData.flash.success) {
                alertMessage('success', String(pageData.flash.successTitle || 'Done'), String(pageData.flash.success));
            }

            document.querySelectorAll('.js-mission-submit-form').forEach((form) => {
                form.addEventListener('submit', (event) => {
                    if (form.dataset.confirmed === '1') {
                        form.dataset.confirmed = '0';
                        return;
                    }

                    event.preventDefault();

                    const input = form.querySelector('input[type="file"]');
                    const file = input && input.files ? input.files[0] : null;
                    if (!file) {
                        alertMessage('warning', 'Choose a file first', 'Please choose your completed mission file before sending it.');
                        return;
                    }

                    confirmAction({
                        icon: 'question',
                        title: 'Send mission file?',
                        text: `Send "${file.name}" for "${form.dataset.mission || 'this mission'}"?`,
                        showCancelButton: true,
                        confirmButtonText: 'Yes, send it',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#4f46e5',
                        cancelButtonColor: '#94a3b8',
                    }).then((confirmed) => {
                        if (!confirmed) return;
                        form.dataset.confirmed = '1';
                        form.submit();
                    });
                });
            });

            document.querySelectorAll('.js-mission-delete-form').forEach((form) => {
                form.addEventListener('submit', (event) => {
                    if (form.dataset.confirmed === '1') {
                        form.dataset.confirmed = '0';
                        return;
                    }

                    event.preventDefault();
                    confirmAction({
                        icon: 'warning',
                        title: 'Remove submitted file?',
                        text: `Remove your submitted file for "${form.dataset.mission || 'this mission'}"?`,
                        showCancelButton: true,
                        confirmButtonText: 'Yes, remove it',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#94a3b8',
                    }).then((confirmed) => {
                        if (!confirmed) return;
                        form.dataset.confirmed = '1';
                        form.submit();
                    });
                });
            });
        });
    </script>
@endif
