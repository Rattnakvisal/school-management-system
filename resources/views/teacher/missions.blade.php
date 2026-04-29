@extends('layout.teacher.navbar')

@section('page')
    @php
        $teacherStatPanelClass =
            'rounded-[26px] border border-white/80 bg-white/90 shadow-[0_24px_55px_-36px_rgba(78,85,135,0.55)] ring-1 ring-slate-200 backdrop-blur';
        $missionTotal = (int) ($stats['total'] ?? 0);
        $missionDueSoon = (int) ($stats['dueSoon'] ?? 0);
        $missionUrgent = (int) ($stats['urgent'] ?? 0);
        $missionSubmitted = (int) ($stats['submitted'] ?? 0);
        $missionPending = (int) ($stats['pending'] ?? 0);
        $audienceLabels = ['all' => 'Staff and Teachers', 'teacher' => 'Teachers', 'staff' => 'Staff'];
        $priorityClasses = [
            'low' => 'border-slate-200 bg-slate-50 text-slate-700',
            'normal' => 'border-indigo-200 bg-indigo-50 text-indigo-700',
            'high' => 'border-amber-200 bg-amber-50 text-amber-700',
            'urgent' => 'border-red-200 bg-red-50 text-red-700',
        ];
    @endphp

    <div class="student-stage space-y-6">
        <section class="student-reveal student-float admin-page-header" style="--sd: 1;">
            <div class="admin-page-header__main flex flex-wrap items-start gap-4">
                <div class="admin-page-header__intro space-y-2">
                    <div class="admin-page-header__title-row flex items-start gap-3">
                        <span class="admin-page-header__icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M6 4v16" />
                                <path d="M7 5h9.5l-1 2 1 2H7" />
                            </svg>
                        </span>
                        <div>
                            <div class="admin-page-header__eyebrow">Mission Center</div>
                            <h1 class="admin-page-title text-3xl font-black tracking-tight sm:text-4xl">Mission Events</h1>
                        </div>
                    </div>
                    <p class="admin-page-subtitle text-sm">
                        Review active mission events and send your completed file to admin / staff.
                    </p>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-5">
            @foreach ([
                ['label' => 'Mission Events', 'meta' => 'Active', 'value' => $missionTotal, 'tone' => 'from-indigo-500 to-cyan-400', 'iconTone' => 'from-indigo-100 to-white text-indigo-600', 'icon' => 'flag'],
                ['label' => 'Due Soon', 'meta' => 'Next 7 days', 'value' => $missionDueSoon, 'tone' => 'from-amber-500 to-cyan-400', 'iconTone' => 'from-amber-100 to-white text-amber-600', 'icon' => 'clock'],
                ['label' => 'Urgent', 'meta' => 'Priority', 'value' => $missionUrgent, 'tone' => 'from-red-500 to-cyan-400', 'iconTone' => 'from-red-100 to-white text-red-600', 'icon' => 'alert'],
                ['label' => 'Submitted', 'meta' => 'Sent', 'value' => $missionSubmitted, 'tone' => 'from-emerald-500 to-cyan-400', 'iconTone' => 'from-emerald-100 to-white text-emerald-600', 'icon' => 'check'],
                ['label' => 'To Send', 'meta' => 'Waiting', 'value' => $missionPending, 'tone' => 'from-sky-500 to-cyan-400', 'iconTone' => 'from-sky-100 to-white text-sky-600', 'icon' => 'upload'],
            ] as $index => $card)
                <article class="student-reveal student-float {{ $teacherStatPanelClass }} min-h-[132px] p-5"
                    style="--sd: {{ $index + 2 }};">
                    <div class="flex items-start justify-between gap-4">
                        <span class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br {{ $card['iconTone'] }}">
                            @switch($card['icon'])
                                @case('clock')
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <circle cx="12" cy="12" r="9" />
                                        <path d="M12 7v5l3 3" />
                                    </svg>
                                @break

                                @case('alert')
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M12 9v4" />
                                        <path d="M12 17h.01" />
                                        <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" />
                                    </svg>
                                @break

                                @case('check')
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M20 6 9 17l-5-5" />
                                    </svg>
                                @break

                                @case('upload')
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                        <path d="m17 8-5-5-5 5" />
                                        <path d="M12 3v12" />
                                    </svg>
                                @break

                                @default
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M6 4v16" />
                                        <path d="M7 5h9.5l-1 2 1 2H7" />
                                    </svg>
                            @endswitch
                        </span>
                        <span class="text-slate-300">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M12 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z" />
                            </svg>
                        </span>
                    </div>
                    <div class="mt-5 flex items-end gap-1 text-2xl font-black tracking-[-0.04em] text-slate-950">
                        <span>{{ number_format($card['value']) }}</span>
                        <span class="pb-0.5 text-base font-extrabold text-slate-300">/
                            {{ number_format(max(1, $missionTotal)) }}</span>
                    </div>
                    <div class="mt-1 text-sm font-bold text-slate-600">{{ $card['label'] }}</div>
                    <div class="mt-1 text-[11px] font-semibold text-slate-400">{{ $card['meta'] }}:
                        {{ number_format($card['value']) }}</div>
                    <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-slate-100">
                        <span class="block h-full rounded-full bg-gradient-to-r {{ $card['tone'] }}"
                            style="width: {{ min(100, max(0, (int) round(($card['value'] / max(1, $missionTotal)) * 100))) }}%"></span>
                    </div>
                </article>
            @endforeach
        </section>

        @if (session('success'))
            <div class="js-inline-flash student-reveal rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"
                style="--sd: 7;">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="js-inline-flash student-reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700"
                style="--sd: 7;">
                {{ $errors->first() }}
            </div>
        @endif

        @if (($missions ?? null) && $missions->count() > 0)
            <section class="student-reveal student-float rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200"
                style="--sd: 8;">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-lg font-black text-slate-900">Mission Table</h2>
                        <p class="mt-1 text-xs font-medium text-slate-500">Choose a file from the row, then send it to admin / staff.</p>
                    </div>
                    <span class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm">
                        {{ number_format($missionTotal) }} total
                    </span>
                </div>

                <div class="min-w-0">
                    <div class="mt-4 overflow-hidden rounded-2xl border border-slate-200">
                        <div class="student-table-scroller max-h-[720px] overflow-auto">
                            <table class="student-table w-full whitespace-nowrap text-left text-sm">
                                <thead class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                    <tr>
                                        <th class="px-3 py-3 font-semibold">Mission</th>
                                        <th class="px-3 py-3 font-semibold">Audience</th>
                                        <th class="px-3 py-3 font-semibold">Priority</th>
                                        <th class="px-3 py-3 font-semibold">Start</th>
                                        <th class="px-3 py-3 font-semibold">End</th>
                                        <th class="px-3 py-3 font-semibold">Submission</th>
                                        <th class="whitespace-nowrap px-3 py-3 text-right font-semibold">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @foreach ($missions as $mission)
                                        @php
                                            $submission = $missionSubmissions->get($mission->id);
                                            $isSubmitted = filled($submission?->submission_file_path);
                                            $priority = strtolower((string) ($mission->priority ?? 'normal'));
                                        @endphp
                                        <tr id="mission-{{ $mission->id }}" x-data="{ detailOpen: false }" class="align-top hover:bg-slate-50/80">
                                            <td class="px-3 py-3 align-top">
                                                <div class="flex items-start gap-3">
                                                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-indigo-50 text-indigo-600 ring-1 ring-indigo-100">
                                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                            <path d="M6 4v16" />
                                                            <path d="M7 5h9.5l-1 2 1 2H7" />
                                                        </svg>
                                                    </span>
                                                    <div class="min-w-0">
                                                        <div class="max-w-[280px] whitespace-normal font-semibold leading-5 text-slate-800">
                                                            {{ $mission->title }}
                                                        </div>
                                                        <div class="mt-1 max-w-[280px] truncate text-xs text-slate-400">
                                                            ID #{{ str_pad((string) $mission->id, 6, '0', STR_PAD_LEFT) }}
                                                            - Posted {{ $mission->created_at?->format('M d, Y') }}
                                                        </div>
                                                        <div class="mt-1 max-w-[280px] truncate text-xs text-slate-500">
                                                            {{ $mission->description }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-3 py-3 align-top text-slate-600">
                                                <span class="inline-flex rounded-lg border border-indigo-100 bg-indigo-50/90 px-2 py-1 text-[11px] font-semibold text-indigo-700">
                                                    {{ $audienceLabels[$mission->audience] ?? ucfirst((string) $mission->audience) }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-3 align-top">
                                                <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $priorityClasses[$priority] ?? $priorityClasses['normal'] }}">
                                                    {{ ucfirst($priority) }}
                                                </span>
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-3 align-top text-slate-500">
                                                {{ $mission->starts_at?->format('M d, Y') ?? 'Not scheduled' }}
                                                @if ($mission->starts_at)
                                                    <div class="mt-1 text-xs text-slate-400">{{ $mission->starts_at->format('h:i A') }}</div>
                                                @endif
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-3 align-top text-slate-500">
                                                {{ $mission->ends_at?->format('M d, Y') ?? 'Open' }}
                                                @if ($mission->ends_at)
                                                    <div class="mt-1 text-xs text-slate-400">{{ $mission->ends_at->diffForHumans() }}</div>
                                                @endif
                                            </td>
                                            <td class="px-3 py-3 align-top text-slate-600">
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
                                                        class="inline-flex max-w-[220px] items-center rounded-lg border border-emerald-100 bg-emerald-50/90 px-2 py-1 text-[11px] hover:bg-emerald-100">
                                                        <span class="truncate font-semibold text-emerald-700">{{ $submission->submission_file_name ?: 'Submitted file' }}</span>
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
                                                                                <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700 ring-1 ring-indigo-100">Teacher</span>
                                                                                <span class="text-sm font-black text-slate-900">{{ auth()->user()?->name ?? 'Teacher' }}</span>
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
                                                    <span class="text-slate-400">-</span>
                                                @endif
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-3 align-top">
                                                <form method="POST" action="{{ route('teacher.missions.submit', $mission) }}"
                                                    enctype="multipart/form-data"
                                                    class="js-teacher-mission-submit-form flex flex-nowrap items-center justify-end gap-2"
                                                    data-mission="{{ $mission->title }}"
                                                    novalidate
                                                    x-data="{ fileName: '' }">
                                                    @csrf
                                                    <input id="mission_submission_{{ $mission->id }}" name="submission_file" type="file"
                                                        class="sr-only" required @change="fileName = $event.target.files[0]?.name || ''">

                                                    <label for="mission_submission_{{ $mission->id }}"
                                                        class="inline-flex cursor-pointer items-center justify-center gap-1.5 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                                        <svg class="h-4 w-4 text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" />
                                                            <path d="M14 2v6h6" />
                                                        </svg>
                                                        {{ $isSubmitted ? 'Edit' : 'Choose' }}
                                                    </label>
                                                    <span class="max-w-[150px] truncate text-xs font-medium text-slate-400"
                                                        x-text="fileName || 'No file chosen'"></span>

                                                    <button type="submit"
                                                        class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                            <path d="m22 2-7 20-4-9-9-4Z" />
                                                            <path d="M22 2 11 13" />
                                                        </svg>
                                                        Send to admin / staff
                                                    </button>
                                                </form>
                                                @if ($isSubmitted)
                                                    <form method="POST"
                                                        action="{{ route('teacher.missions.submission.destroy', $mission) }}"
                                                        class="js-teacher-mission-delete-form mt-2 flex justify-end"
                                                        data-mission="{{ $mission->title }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">
                                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                                <path d="M3 6h18" />
                                                                <path d="M8 6V4h8v2" />
                                                                <path d="M19 6l-1 14H6L5 6" />
                                                                <path d="M10 11v5" />
                                                                <path d="M14 11v5" />
                                                            </svg>
                                                            Remove
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <div>
                {{ $missions->links() }}
            </div>
        @else
            <section class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-6 py-14 text-center shadow-sm">
                <div class="text-xl font-black text-slate-900">No mission events yet</div>
                <p class="mt-2 text-sm text-slate-500">New mission events from admin will appear here.</p>
            </section>
        @endif
    </div>

    @php
        $teacherMissionPageData = [
            'validationErrors' => $errors->all(),
            'flash' => [
                'success' => session('success'),
                'successTitle' => session('success_title', 'Sent to admin / staff'),
                'error' => $errors->any() ? $errors->first() : '',
            ],
        ];
    @endphp
    <script id="teacher-mission-data" type="application/json">{!! json_encode($teacherMissionPageData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const hasSwal = typeof window.Swal !== 'undefined';
            const dataNode = document.getElementById('teacher-mission-data');
            const pageData = {
                validationErrors: [],
                flash: {
                    success: '',
                    successTitle: 'Sent to admin / staff',
                    error: '',
                },
            };

            if (dataNode) {
                try {
                    const parsed = JSON.parse(dataNode.textContent || '{}');
                    Object.assign(pageData, parsed || {});
                    pageData.flash = {
                        ...pageData.flash,
                        ...((parsed && parsed.flash) || {}),
                    };
                } catch (error) {
                    console.error('Unable to parse teacher mission data.', error);
                }
            }

            const showAlert = (config) => {
                const options = {
                    icon: config.icon || 'info',
                    title: config.title || 'Notice',
                    text: config.text || '',
                    confirmButtonText: config.confirmButtonText || 'OK',
                    confirmButtonColor: config.confirmButtonColor || '#4f46e5',
                };

                if (hasSwal) {
                    return window.Swal.fire(options);
                }

                window.alert(`${options.title}\n\n${options.text}`);
                return Promise.resolve({ isConfirmed: true });
            };

            const confirmAction = (options) => {
                if (hasSwal) {
                    return window.Swal.fire(options).then((result) => Boolean(result && result.isConfirmed));
                }

                return Promise.resolve(window.confirm(`${options.title}\n\n${options.text}`));
            };

            if ((pageData.validationErrors || []).length > 0) {
                document.querySelectorAll('.js-inline-flash').forEach((element) => element.classList.add('hidden'));
                showAlert({
                    icon: 'error',
                    title: 'Upload failed',
                    text: String(pageData.validationErrors[0] || 'Please choose a valid file and try again.'),
                });
            } else if (String(pageData.flash.success || '').trim() !== '') {
                document.querySelectorAll('.js-inline-flash').forEach((element) => element.classList.add('hidden'));
                showAlert({
                    icon: 'success',
                    title: String(pageData.flash.successTitle || 'Done'),
                    text: String(pageData.flash.success),
                    confirmButtonText: 'Great',
                });
            }

            document.querySelectorAll('.js-teacher-mission-submit-form').forEach((form) => {
                form.addEventListener('submit', (event) => {
                    if (form.dataset.confirmed === '1') {
                        form.dataset.confirmed = '0';
                        return;
                    }

                    event.preventDefault();

                    const input = form.querySelector('input[type="file"]');
                    const file = input && input.files ? input.files[0] : null;
                    const missionTitle = String(form.dataset.mission || 'this mission');

                    if (!file) {
                        showAlert({
                            icon: 'warning',
                            title: 'Choose a file first',
                            text: 'Please choose your completed mission file before sending it to admin / staff.',
                        });
                        return;
                    }

                    confirmAction({
                        icon: 'question',
                        title: 'Send mission file?',
                        text: `Send "${file.name}" to admin / staff for "${missionTitle}"?`,
                        showCancelButton: true,
                        confirmButtonText: 'Yes, send it',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#4f46e5',
                        cancelButtonColor: '#94a3b8',
                    }).then((confirmed) => {
                        if (!confirmed) {
                            return;
                        }

                        form.dataset.confirmed = '1';
                        form.submit();
                    });
                });
            });

            document.querySelectorAll('.js-teacher-mission-delete-form').forEach((form) => {
                form.addEventListener('submit', (event) => {
                    if (form.dataset.confirmed === '1') {
                        form.dataset.confirmed = '0';
                        return;
                    }

                    event.preventDefault();

                    const missionTitle = String(form.dataset.mission || 'this mission');

                    confirmAction({
                        icon: 'warning',
                        title: 'Remove submitted file?',
                        text: `Remove your submitted file for "${missionTitle}"?`,
                        showCancelButton: true,
                        confirmButtonText: 'Yes, remove it',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#94a3b8',
                    }).then((confirmed) => {
                        if (!confirmed) {
                            return;
                        }

                        form.dataset.confirmed = '1';
                        form.submit();
                    });
                });
            });
        });
    </script>
@endsection
