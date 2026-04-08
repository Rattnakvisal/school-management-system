@extends('layout.students.navbar')

@section('page')
    @php
        $statusStyles = [
            'pending' => 'border-amber-200 bg-amber-50 text-amber-700',
            'approved' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
            'rejected' => 'border-red-200 bg-red-50 text-red-700',
        ];

        $formValues = $formDefaults ?? [];
        $isEditing = isset($editingRequest) && $editingRequest;
        $teacherRecipients = collect($teacherRecipients ?? []);
        $selectedSubjectId = (string) ($formValues['subject_id'] ?? '');
        $selectedTimeKeys = array_values(array_unique(array_filter(array_map('strval', (array) ($formValues['subject_time_keys'] ?? [])))));
        $subjectTimeMap = $subjectTimeOptionsBySubject ?? [];
        $initialTimeOptions = $selectedSubjectId !== '' ? ($subjectTimeMap[$selectedSubjectId] ?? []) : [];
    @endphp

    <div class="student-stage space-y-6">
        <section class="student-reveal student-float admin-page-header" style="--sd: 1;">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="min-w-0">
                    <div class="admin-page-header__eyebrow">Student Portal</div>
                    <h1 class="admin-page-title text-3xl font-black tracking-tight sm:text-4xl">Law Requests</h1>
                    <p class="admin-page-subtitle mt-1 text-sm">
                        Connect your request to a real subject schedule so your teacher sees the exact class and time.
                    </p>
                    <p class="mt-2 text-xs font-semibold text-slate-600">
                        Class: <span class="text-slate-900">{{ $classLabel }}</span>
                    </p>
                </div>

                <div class="admin-page-header__stats flex flex-wrap items-center gap-2 text-xs font-semibold">
                    <span class="admin-page-stat">Total: {{ number_format(($lawRequests ?? collect())->count()) }}</span>
                    <span class="admin-page-stat admin-page-stat--amber">Pending:
                        {{ number_format(($lawRequests ?? collect())->where('status', 'pending')->count()) }}</span>
                    <span class="admin-page-stat admin-page-stat--emerald">Approved:
                        {{ number_format(($lawRequests ?? collect())->where('status', 'approved')->count()) }}</span>
                </div>
            </div>
        </section>

        @if (session('success'))
            <div class="js-inline-flash rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="js-inline-flash rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700">
                {{ session('warning') }}
            </div>
        @endif

        @if (session('error'))
            <div class="js-inline-flash rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="js-inline-flash rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid gap-6 xl:grid-cols-12">
            <section class="student-reveal student-float rounded-3xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-5"
                style="--sd: 2;">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-black text-slate-900">{{ $isEditing ? 'Edit Request' : 'Create Request' }}</h2>
                        <p class="mt-1 text-xs font-semibold text-slate-500">
                            {{ $isEditing ? 'Update your pending request and save changes.' : 'Choose subject, class time, and let the date fill automatically from the schedule.' }}
                        </p>
                    </div>

                    <span class="rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-indigo-700">
                        Teacher Alert Enabled
                    </span>
                </div>

                <form method="POST"
                    action="{{ $isEditing ? route('student.law-requests.update', $editingRequest) : route('student.law-requests.store') }}"
                    class="js-student-law-request-submit-form mt-5 space-y-4">
                    @csrf
                    @if ($isEditing)
                        @method('PUT')
                    @endif

                    <div class="space-y-2">
                        <label for="law_type" class="text-sm font-semibold text-slate-700">Law Type</label>
                        <select id="law_type" name="law_type"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            @foreach ($lawTypes as $typeKey => $typeLabel)
                                <option value="{{ $typeKey }}"
                                    {{ (string) ($formValues['law_type'] ?? '') === (string) $typeKey ? 'selected' : '' }}>
                                    {{ $typeLabel }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="subject" class="text-sm font-semibold text-slate-700">Subject</label>
                        <select id="subject" name="subject_id"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            @forelse ($subjectOptions ?? [] as $subjectOption)
                                @php
                                    $className = trim((string) ($subjectOption->class_name ?? ''));
                                    $classSection = trim((string) ($subjectOption->class_section ?? ''));
                                    $classDisplay = $className !== '' ? ($className . ($classSection !== '' ? ' - ' . $classSection : '')) : '';
                                @endphp
                                <option value="{{ $subjectOption->id }}"
                                    {{ $selectedSubjectId === (string) $subjectOption->id ? 'selected' : '' }}>
                                    {{ $subjectOption->name }}
                                    {{ !empty($subjectOption->code) ? ' (' . $subjectOption->code . ')' : '' }}
                                    {{ $classDisplay !== '' ? ' | ' . $classDisplay : '' }}
                                </option>
                            @empty
                                <option value="">No subject available</option>
                            @endforelse
                        </select>
                    </div>

                    <div class="space-y-2">
                        <div class="text-sm font-semibold text-slate-700">Subject Time</div>
                        <div id="subject_time_options" class="grid gap-2 sm:grid-cols-2">
                            @forelse ($initialTimeOptions as $timeOption)
                                @php
                                    $timeKey = (string) ($timeOption['key'] ?? '');
                                    $timeLabel = (string) ($timeOption['label'] ?? '');
                                    $isChecked = in_array($timeKey, $selectedTimeKeys, true);
                                @endphp
                                <label
                                    class="js-time-card flex cursor-pointer items-start gap-3 rounded-xl border px-3 py-3 text-sm transition hover:border-indigo-300 hover:bg-indigo-50/40 {{ $isChecked ? 'border-indigo-300 bg-indigo-50' : 'border-slate-200 bg-white' }}"
                                    data-day-of-week="{{ (string) ($timeOption['day_of_week'] ?? '') }}"
                                    data-time-key="{{ $timeKey }}">
                                    <input type="checkbox" name="subject_time_keys[]"
                                        value="{{ $timeKey }}"
                                        {{ $isChecked ? 'checked' : '' }}
                                        class="js-time-checkbox mt-1 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-300">
                                    <div class="min-w-0">
                                        <div class="font-semibold text-slate-800">{{ $timeLabel }}</div>
                                        <div class="mt-1 text-xs font-semibold text-slate-500">
                                            Teacher:
                                            {{ !empty($timeOption['teacher_name']) ? $timeOption['teacher_name'] : 'Not assigned yet' }}
                                        </div>
                                    </div>
                                </label>
                            @empty
                                <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-500">
                                    No time available
                                </div>
                            @endforelse
                        </div>
                        <p class="text-xs font-medium text-slate-400">Choose one class time so the correct teacher receives the notification.</p>
                    </div>

                    <div class="space-y-2">
                        <label for="requested_for" class="text-sm font-semibold text-slate-700">Requested For Date</label>
                        <input id="requested_for" type="date" name="requested_for"
                            value="{{ (string) ($formValues['requested_for'] ?? '') }}"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                        <p class="text-xs font-medium text-slate-400">
                            The date can auto-fill from the selected class day. You can still change it manually.
                        </p>
                    </div>

                    <div class="space-y-2">
                        <label for="reason" class="text-sm font-semibold text-slate-700">Reason</label>
                        <textarea id="reason" name="reason" rows="6" maxlength="5000"
                            placeholder="Explain why you need an attendance law request..."
                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">{{ (string) ($formValues['reason'] ?? '') }}</textarea>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <div class="text-xs font-bold uppercase tracking-wide text-slate-500">Teachers Who Will Be Notified</div>
                        @if ($teacherRecipients->isNotEmpty())
                            <div id="teacher_recipient_list" class="mt-3 flex flex-wrap gap-2">
                                @foreach ($teacherRecipients as $teacherRecipient)
                                    <span class="inline-flex items-center rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700">
                                        {{ $teacherRecipient->name }}
                                    </span>
                                @endforeach
                            </div>
                            <div id="teacher_recipient_empty" class="mt-2 hidden text-sm text-slate-500">
                                No teacher is assigned to this subject time yet. Your request will still be saved.
                            </div>
                        @else
                            <div id="teacher_recipient_list" class="mt-3 hidden flex-wrap gap-2"></div>
                            <div id="teacher_recipient_empty" class="mt-2 text-sm text-slate-500">
                                No teacher is assigned to this subject time yet. Your request will still be saved.
                            </div>
                        @endif
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                        <button type="submit"
                            class="inline-flex w-full flex-1 items-center justify-center rounded-xl bg-gradient-to-r from-indigo-600 to-slate-900 px-4 py-3 text-sm font-bold text-white transition hover:from-indigo-500 hover:to-slate-800">
                            {{ $isEditing ? 'Update Law Request' : 'Submit Law Request' }}
                        </button>
                        @if ($isEditing)
                            <a href="{{ route('student.law-requests.index') }}"
                                class="inline-flex w-full items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50 sm:w-auto">
                                Cancel
                            </a>
                        @endif
                    </div>
                </form>
            </section>

            <section class="student-reveal student-float rounded-3xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-7"
                style="--sd: 3;">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-lg font-black text-slate-900">Request History</h2>
                    <span class="text-xs font-semibold text-slate-500">Latest first</span>
                </div>

                <div class="overflow-hidden rounded-2xl border border-slate-200">
                    <div class="max-h-[620px] overflow-auto">
                        <table class="w-full min-w-[980px] text-left text-sm">
                            <thead class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-3 py-3 font-semibold">Type</th>
                                    <th class="px-3 py-3 font-semibold">Subject</th>
                                    <th class="px-3 py-3 font-semibold">Requested For</th>
                                    <th class="px-3 py-3 font-semibold">Status</th>
                                    <th class="px-3 py-3 font-semibold">Submitted</th>
                                    <th class="px-3 py-3 font-semibold">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse ($lawRequests as $lawRequest)
                                    @php
                                        $statusKey = strtolower((string) ($lawRequest->status ?? 'pending'));
                                        $statusClass = $statusStyles[$statusKey] ?? 'border-slate-200 bg-slate-50 text-slate-700';
                                        $typeLabel = $lawTypes[$lawRequest->law_type] ?? ucfirst(str_replace('_', ' ', (string) $lawRequest->law_type));
                                        $canManage = $statusKey === 'pending';
                                        $subjectTimeParts = array_values(array_filter(array_map('trim', explode(',', (string) ($lawRequest->subject_time ?? ''))), fn($value) => $value !== ''));
                                    @endphp
                                    <tr class="{{ $isEditing && (int) ($editingRequest->id ?? 0) === (int) $lawRequest->id ? 'bg-indigo-50/40' : 'hover:bg-slate-50/80' }}">
                                        <td class="px-3 py-3 font-semibold text-slate-900">{{ $typeLabel }}</td>
                                        <td class="px-3 py-3">
                                            <div class="font-semibold text-slate-900">{{ $lawRequest->subject ?: 'N/A' }}</div>
                                            <div class="mt-1 text-xs font-semibold text-sky-600">
                                                Teacher: {{ $lawRequest->teacher?->name ?: 'Not assigned' }}
                                            </div>
                                            <div class="mt-1 text-xs text-slate-500 line-clamp-2">{{ $lawRequest->reason }}</div>
                                        </td>
                                        <td class="px-3 py-3 text-slate-700">
                                            <div class="font-semibold text-slate-900">
                                                {{ $lawRequest->requested_for?->format('M d, Y') ?? 'N/A' }}
                                            </div>
                                            @if ($subjectTimeParts !== [])
                                                <div class="mt-2 flex flex-wrap gap-1.5">
                                                    @foreach ($subjectTimeParts as $subjectTimePart)
                                                        <span class="inline-flex rounded-full border border-indigo-200 bg-indigo-50 px-2 py-0.5 text-[11px] font-semibold text-indigo-700">
                                                            {{ $subjectTimePart }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3">
                                            <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                                {{ ucfirst($statusKey) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 text-slate-700">{{ $lawRequest->created_at?->format('M d, Y h:i A') }}</td>
                                        <td class="px-3 py-3">
                                            @if ($canManage)
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <a href="{{ route('student.law-requests.index', ['edit' => $lawRequest->id]) }}"
                                                        class="inline-flex items-center rounded-lg border border-indigo-200 bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                                        Edit
                                                    </a>
                                                    <form method="POST"
                                                        action="{{ route('student.law-requests.destroy', $lawRequest) }}"
                                                        class="js-student-law-request-delete-form"
                                                        data-request-label="{{ $typeLabel }} for {{ $lawRequest->requested_for?->format('M d, Y') ?? 'selected date' }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 hover:bg-red-100">
                                                            Remove
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                <span class="text-xs font-semibold text-slate-400">Locked after review</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-3 py-10 text-center text-sm text-slate-500">
                                            No law requests submitted yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>

    @php
        $lawRequestPageData = [
            'validationErrors' => $errors->all(),
            'subjectTimeOptionsBySubject' => $subjectTimeMap ?? [],
            'teacherRecipientsByTime' => $teacherRecipientsByTime ?? [],
            'formDefaults' => [
                'subject_id' => $selectedSubjectId,
                'subject_time_keys' => $selectedTimeKeys,
                'requested_for' => (string) ($formValues['requested_for'] ?? ''),
            ],
            'flash' => [
                'success' => session('success'),
                'warning' => session('warning'),
                'error' => session('error'),
            ],
        ];
    @endphp
    <script id="student-law-request-data" type="application/json">{!! json_encode($lawRequestPageData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/Student/LawRequests.js'])
@endsection
