@extends('layout.teacher.navbar')

@section('page')
    @php
        $statusStyles = [
            'pending' => 'border-amber-200 bg-amber-50 text-amber-700',
            'approved' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
            'rejected' => 'border-red-200 bg-red-50 text-red-700',
        ];

        $formValues = $formDefaults ?? [];
        $isEditing = isset($editingRequest) && $editingRequest;
        $selectedSubjectId = (string) ($formValues['subject_id'] ?? 'all');
        $selectedTimeKey = (string) ($formValues['subject_time_key'] ?? 'all:all');
        $subjectTimeMap = $subjectTimeOptionsBySubject ?? [];
        $initialTimeOptions = $subjectTimeMap[$selectedSubjectId] ?? [];
    @endphp

    <div class="teacher-time-stage space-y-6">
        <section class="admin-page-header teacher-page-header">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="min-w-0">
                    <h1 class="admin-page-title text-3xl font-black tracking-tight">Teacher Law Requests</h1>
                    <p class="admin-page-subtitle mt-1 text-sm">
                        Submit a law request and track your request status.
                    </p>
                </div>

                <div class="teacher-page-header__stats flex flex-wrap items-center gap-2 text-xs font-semibold">
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
            <section class="min-w-0 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-5">
                <h2 class="text-lg font-black text-slate-900">{{ $isEditing ? 'Edit Request' : 'Create Request' }}</h2>
                <p class="mt-1 text-xs font-semibold text-slate-500">
                    {{ $isEditing ? 'Update your law request and save changes.' : 'Fill in the details and submit your request.' }}
                </p>

                <form method="POST"
                    action="{{ $isEditing ? route('teacher.law-requests.update', $editingRequest) : route('teacher.law-requests.store') }}"
                    class="mt-5 space-y-4">
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
                            <option value="all" {{ $selectedSubjectId === 'all' ? 'selected' : '' }}>All Subjects</option>
                            @foreach ($subjectOptions as $subjectOption)
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
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="subject_time_key" class="text-sm font-semibold text-slate-700">Subject Time</label>
                        <select id="subject_time_key" name="subject_time_key"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            @forelse ($initialTimeOptions as $timeOption)
                                <option value="{{ $timeOption['key'] ?? '' }}"
                                    {{ $selectedTimeKey === (string) ($timeOption['key'] ?? '') ? 'selected' : '' }}>
                                    {{ $timeOption['label'] ?? '' }}
                                </option>
                            @empty
                                <option value="">No time available</option>
                            @endforelse
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="requested_for" class="text-sm font-semibold text-slate-700">Requested For Date</label>
                        <input id="requested_for" type="date" name="requested_for"
                            value="{{ (string) ($formValues['requested_for'] ?? '') }}"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                    </div>

                    <div class="space-y-2">
                        <label for="reason" class="text-sm font-semibold text-slate-700">Reason</label>
                        <textarea id="reason" name="reason" rows="6" maxlength="5000"
                            placeholder="Write full details of the law request..."
                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">{{ (string) ($formValues['reason'] ?? '') }}</textarea>
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                        <button type="submit"
                            class="inline-flex w-full flex-1 items-center justify-center rounded-xl bg-indigo-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-indigo-500">
                            {{ $isEditing ? 'Update Law Request' : 'Submit Law Request' }}
                        </button>
                        @if ($isEditing)
                            <a href="{{ route('teacher.law-requests.index') }}"
                                class="inline-flex w-full items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50 sm:w-auto">
                                Cancel
                            </a>
                        @endif
                    </div>
                </form>
            </section>

            <section class="min-w-0 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-7">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-lg font-black text-slate-900">Request History</h2>
                    <span class="text-xs font-semibold text-slate-500">Latest first</span>
                </div>

                <div class="overflow-hidden rounded-2xl border border-slate-200">
                    <div class="max-h-[620px] overflow-auto">
                        <table class="w-full min-w-[860px] lg:min-w-[980px] text-left text-sm">
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
                                        $subjectText = trim((string) ($lawRequest->subject ?? ''));
                                        $subjectTimeText = trim((string) ($lawRequest->subject_time ?? ''));
                                        if ($subjectTimeText === '' && str_contains($subjectText, '|')) {
                                            [$parsedSubject, $parsedTime] = array_pad(array_map('trim', explode('|', $subjectText, 2)), 2, '');
                                            $subjectText = $parsedSubject;
                                            $subjectTimeText = $parsedTime;
                                        }
                                    @endphp
                                    <tr
                                        class="align-top hover:bg-slate-50/80 {{ $isEditing && (int) ($editingRequest->id ?? 0) === (int) $lawRequest->id ? 'bg-indigo-50/40' : '' }}">
                                        <td class="px-3 py-3 font-semibold text-slate-800">{{ $typeLabel }}</td>
                                        <td class="px-3 py-3">
                                            <div class="font-semibold text-slate-900">{{ $subjectText }}</div>
                                            @if ($subjectTimeText !== '')
                                                <div class="mt-1 text-xs font-semibold text-indigo-600">{{ $subjectTimeText }}</div>
                                            @endif
                                            <div class="mt-1 text-xs text-slate-500 line-clamp-2">{{ $lawRequest->reason }}</div>
                                        </td>
                                        <td class="px-3 py-3 text-slate-700">
                                            {{ $lawRequest->requested_for ? $lawRequest->requested_for->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="px-3 py-3">
                                            <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                                {{ ucfirst($statusKey) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 text-slate-700">{{ $lawRequest->created_at?->format('M d, Y h:i A') }}</td>
                                        <td class="px-3 py-3">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <a href="{{ route('teacher.law-requests.index', ['edit' => $lawRequest->id]) }}"
                                                    class="inline-flex items-center rounded-lg border border-indigo-200 bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                                    Edit
                                                </a>
                                                <form method="POST"
                                                    action="{{ route('teacher.law-requests.destroy', $lawRequest) }}"
                                                    class="js-law-request-delete-form"
                                                    data-request-label="{{ $subjectText !== '' ? $subjectText : ('Request #' . $lawRequest->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 hover:bg-red-100">
                                                        Remove
                                                    </button>
                                                </form>
                                            </div>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const subjectSelect = document.getElementById('subject');
            const timeSelect = document.getElementById('subject_time_key');
            if (!subjectSelect || !timeSelect) return;

            const subjectTimeMap = @json($subjectTimeOptionsBySubject ?? []);
            const defaultTimeKey = @json((string) ($formValues['subject_time_key'] ?? ''));

            const renderTimeOptions = (preferredKey = '') => {
                const subjectId = String(subjectSelect.value || 'all');
                const options = Array.isArray(subjectTimeMap[subjectId]) ? subjectTimeMap[subjectId] : [];
                timeSelect.innerHTML = '';

                if (options.length === 0) {
                    const emptyOption = document.createElement('option');
                    emptyOption.value = '';
                    emptyOption.textContent = 'No time available';
                    timeSelect.appendChild(emptyOption);
                    timeSelect.disabled = true;
                    return;
                }

                options.forEach((item) => {
                    const option = document.createElement('option');
                    option.value = String(item?.key || '');
                    option.textContent = String(item?.label || '');
                    timeSelect.appendChild(option);
                });

                timeSelect.disabled = false;
                const allKey = subjectId === 'all' ? 'all:all' : `all:${subjectId}`;
                const hasPreferred = preferredKey !== '' && options.some((item) => String(item?.key || '') === preferredKey);
                const hasAllKey = options.some((item) => String(item?.key || '') === allKey);

                if (hasPreferred) {
                    timeSelect.value = preferredKey;
                } else if (hasAllKey) {
                    timeSelect.value = allKey;
                } else {
                    timeSelect.value = String(options[0]?.key || '');
                }
            };

            renderTimeOptions(defaultTimeKey);
            subjectSelect.addEventListener('change', () => {
                renderTimeOptions('');
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hasSwal = typeof Swal !== 'undefined';

            const alerts = [];
            @foreach (($approvalAlerts ?? []) as $approvalAlert)
                alerts.push({
                    icon: 'success',
                    title: @json((string) ($approvalAlert['title'] ?? 'Law request approved')),
                    text: @json((string) ($approvalAlert['text'] ?? 'Your law request has been approved.')),
                });
            @endforeach
            @if (session('success'))
                alerts.push({ icon: 'success', title: 'Success', text: @json(session('success')) });
            @endif
            @if (session('warning'))
                alerts.push({ icon: 'warning', title: 'Warning', text: @json(session('warning')) });
            @endif
            @if (session('error'))
                alerts.push({ icon: 'error', title: 'Error', text: @json(session('error')) });
            @endif
            @if ($errors->any())
                alerts.push({ icon: 'error', title: 'Validation Error', text: @json($errors->first()) });
            @endif

            if (hasSwal && alerts.length > 0) {
                document.querySelectorAll('.js-inline-flash').forEach((element) => {
                    element.classList.add('hidden');
                });

                alerts.reduce((chain, config) => {
                    return chain.then(() => Swal.fire({
                        ...config,
                        confirmButtonColor: '#4f46e5',
                    }));
                }, Promise.resolve());
            }

            document.querySelectorAll('.js-law-request-delete-form').forEach((form) => {
                form.addEventListener('submit', function(event) {
                    if (this.dataset.confirmed === '1') {
                        return;
                    }

                    event.preventDefault();
                    const label = String(this.dataset.requestLabel || 'this request');
                    const confirmText = `Are you sure you want to remove ${label}?`;

                    if (!hasSwal) {
                        if (window.confirm(confirmText)) {
                            this.dataset.confirmed = '1';
                            this.submit();
                        }
                        return;
                    }

                    Swal.fire({
                        icon: 'warning',
                        title: 'Remove Request?',
                        text: confirmText,
                        showCancelButton: true,
                        confirmButtonText: 'Yes, remove',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#dc2626',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.dataset.confirmed = '1';
                            this.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection
