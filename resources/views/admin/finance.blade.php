@extends('layout.admin.navbar')

@section('page')
    @php
        $paymentTotal = (int) ($stats['payments'] ?? 0);
        $collectedTotal = (float) ($stats['collected'] ?? 0);
        $outstandingTotal = (float) ($stats['outstanding'] ?? 0);
        $discountTotal = (float) ($stats['discounts'] ?? 0);
        $studentsPaid = (int) ($stats['students_paid'] ?? 0);

        $billableTotal = max((float) ($stats['billable'] ?? 0), $collectedTotal + $outstandingTotal + $discountTotal);
        $financeExportQuery = array_filter(
            [
                'q' => $search,
                'student_id' => $studentId !== 'all' ? $studentId : null,
                'status' => $status !== 'all' ? $status : null,
                'class_id' => $classId !== 'all' ? $classId : null,
                'from' => $from !== '' ? $from : null,
                'to' => $to !== '' ? $to : null,
            ],
            fn($value) => $value !== null && $value !== '',
        );

        $studentPaidTotalSet = collect($studentPaidTotals ?? [])->mapWithKeys(
            fn($value, $id) => [(string) $id => (float) $value],
        );

        $studentTuitionPayload = collect($students ?? [])->mapWithKeys(function ($student) use ($studentPaidTotalSet) {
            $tuitionSubjects = collect($student->tuition_subjects ?? [])
                ->map(
                    fn($subject) => [
                        'id' => (int) $subject->id,
                        'name' => (string) $subject->name,
                        'fee' => (float) ($subject->tuition_fee ?? 0),
                    ],
                )
                ->values();

            $tuitionTotal = (float) ($student->tuition_total ?? 0);
            $paidTotal = (float) ($studentPaidTotalSet[(string) $student->id] ?? 0);
            $remainingTotal = max($tuitionTotal - $paidTotal, 0);

            return [
                (string) $student->id => [
                    'amount' => $remainingTotal > 0 ? $remainingTotal : $tuitionTotal,
                    'paid' => $paidTotal,
                    'remaining' => $remainingTotal,
                    'total' => $tuitionTotal,
                    'subjects' => $tuitionSubjects,
                ],
            ];
        });

        $statusTone = [
            'paid' =>
                'bg-emerald-50 text-emerald-700 ring-emerald-100 dark:bg-emerald-500/15 dark:text-white-300 dark:ring-emerald-400/20',
            'partial' =>
                'bg-sky-50 text-sky-700 ring-sky-100 dark:bg-sky-500/15 dark:text-sky-300 dark:ring-sky-400/20',
            'pending' =>
                'bg-amber-50 text-amber-700 ring-amber-100 dark:bg-amber-500/15 dark:text-amber-300 dark:ring-amber-400/20',
            'overdue' =>
                'bg-rose-50 text-rose-700 ring-rose-100 dark:bg-rose-500/15 dark:text-rose-300 dark:ring-rose-400/20',
            'waived' =>
                'bg-slate-100 text-slate-700 ring-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-700',
        ];

        $panelClass =
            'student-reveal rounded-2xl border border-slate-200 bg-white shadow-sm ring-1 ring-slate-100 dark:border-slate-700 dark:bg-slate-900 dark:ring-slate-800';

        $labelClass = 'mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300';

        $inputClass =
            'w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-500/20 dark:disabled:bg-slate-800/60 dark:disabled:text-slate-500';

        $textareaClass =
            'w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-500/20 dark:disabled:bg-slate-800/60 dark:disabled:text-slate-500';

        $summaryBoxClass =
            'mt-2 rounded-xl border border-indigo-100 bg-indigo-50/60 px-3 py-2 text-xs text-slate-600 dark:border-indigo-400/20 dark:bg-indigo-500/10 dark:text-slate-300';

        $modalPanelClass =
            'relative z-10 flex max-h-[calc(100vh-2rem)] w-full max-w-xl flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900';

        $tableHeadClass =
            'admin-table-head sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400';

        $tableBodyClass = 'divide-y divide-slate-100 bg-white dark:divide-slate-700 dark:bg-slate-900';
    @endphp

    <style>
        .finance-page .finance-payment-table .student-col-student {
            min-width: 170px;
        }

        .finance-page .finance-payment-table .student-col-email {
            min-width: 190px;
        }

        .finance-page .finance-payment-table .student-col-class {
            min-width: 130px;
        }

        .finance-page .finance-payment-table .student-col-subjects {
            min-width: 180px;
        }

        .finance-page .finance-payment-table .student-col-status {
            min-width: 112px;
        }

        .finance-page .finance-payment-table .student-col-actions {
            min-width: 132px;
        }
    </style>

    <div
        class="student-stage admin-management-page finance-page mx-auto max-w-[1500px] space-y-5 pb-8 text-slate-900 dark:text-slate-100">
        <section class="student-reveal flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between" style="--sd: 1;">
            <div>
                <h1 class="text-3xl font-black tracking-tight text-slate-950 dark:text-white">School Finance</h1>
                <p class="mt-1 flex items-center gap-1.5 text-sm font-semibold text-slate-500 dark:text-slate-400">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 2v20" />
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6" />
                    </svg>
                    Total: {{ number_format($paymentTotal) }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.finance.export.excel', $financeExportQuery) }}"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                        <path d="M7 10l5 5 5-5" />
                        <path d="M12 15V3" />
                    </svg>
                    Export data
                </a>

                <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-finance-create'))"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm shadow-indigo-500/20 transition hover:bg-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-200 dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:focus:ring-indigo-500/25">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 5v14M5 12h14" />
                    </svg>
                    Add payment
                </button>
            </div>
        </section>

        @if (!$tableReady)
            <div class="student-reveal rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/15 dark:text-amber-300"
                style="--sd: 2;">
                Finance is ready, but the database table is not installed yet. Run
                <span class="font-black">php artisan migrate</span>
                to enable payment records.
            </div>
        @endif

        <div x-data="{ createOpen: @js($errors->any()) }"
            @open-finance-create.window="
                createOpen = true;
                $nextTick(() => document.getElementById('student_id')?.focus());
            "
            class="grid gap-6 xl:grid-cols-12">
            {{-- RECORD PAYMENT --}}
            <section x-show="createOpen" x-cloak x-transition.opacity.duration.150ms
                @keydown.escape.window="createOpen = false" @click.self="createOpen = false" role="dialog"
                aria-modal="true" aria-labelledby="create-finance-title"
                class="fixed inset-0 z-[90] flex items-start justify-center overflow-y-auto bg-slate-950/65 px-4 py-6 backdrop-blur-sm sm:py-10"
                style="--sd: 3;">
                <div
                    class="w-full max-w-5xl overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl ring-1 ring-slate-950/5 dark:border-slate-700 dark:bg-slate-900 dark:ring-white/10">

                    <div
                        class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-5 dark:border-slate-700">
                        <div>
                            <h2 id="create-finance-title" class="text-2xl font-black text-slate-950 dark:text-white">
                                Record Payment
                            </h2>

                            <p class="mt-1 text-sm font-semibold text-slate-500 dark:text-slate-400">
                                Add a student payment, due item, discount, or scholarship waiver.
                            </p>
                        </div>

                        <button type="button" @click="createOpen = false" aria-controls="create-finance-form-panel"
                            class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-red-600 text-lg font-black leading-none text-white shadow-sm transition hover:bg-red-500 focus:outline-none focus:ring-4 focus:ring-red-200 dark:focus:ring-red-500/25"
                            aria-label="Close record payment form">
                            &times;
                        </button>
                    </div>

                    <form id="create-finance-form-panel" method="POST" action="{{ route('admin.finance.store') }}"
                        enctype="multipart/form-data" data-finance-payment-form data-current-net="0">
                        @csrf

                        <div class="max-h-[calc(100vh-15rem)] overflow-y-auto px-6 py-5">
                            <div class="grid gap-5 lg:grid-cols-2">
                                <div class="lg:col-span-2">
                                    <label for="student_id" class="{{ $labelClass }}">Student</label>

                                    <select id="student_id" name="student_id" @disabled(!$tableReady)
                                        class="js-finance-student-select {{ $inputClass }}">
                                        <option value="">Select student</option>

                                        @foreach ($students as $student)
                                            @php
                                                $studentTuitionTotal = (float) ($student->tuition_total ?? 0);
                                                $studentPaidTotal =
                                                    (float) ($studentPaidTotalSet[(string) $student->id] ?? 0);
                                                $studentRemainingTotal = max(
                                                    $studentTuitionTotal - $studentPaidTotal,
                                                    0,
                                                );

                                                $studentPaidInFull =
                                                    $studentPaidTotal > 0 &&
                                                    ($studentTuitionTotal <= 0 || $studentRemainingTotal <= 0.009);
                                            @endphp

                                            <option value="{{ $student->id }}" @disabled($studentPaidInFull)
                                                {{ (string) old('student_id') === (string) $student->id ? 'selected' : '' }}>
                                                {{ $student->name }} - {{ $student->email }}

                                                @if ($studentTuitionTotal > 0)
                                                    - Due ${{ number_format($studentRemainingTotal, 2) }}
                                                @endif

                                                {{ $studentPaidInFull ? ' (Paid in full)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('student_id')
                                        <p class="mt-1 text-xs font-semibold text-red-600 dark:text-red-300">
                                            {{ $message }}
                                        </p>
                                    @enderror

                                    <p class="mt-1 text-[11px] font-semibold text-slate-400 dark:text-slate-500">
                                        Students can pay again while they still have tuition due.
                                    </p>

                                    <div class="{{ $summaryBoxClass }}">
                                        <div class="flex items-center justify-between gap-3">
                                            <span class="font-semibold text-slate-600 dark:text-slate-300">Tuition from
                                                subjects</span>
                                            <span class="font-black text-indigo-700 dark:text-indigo-300"
                                                data-finance-tuition-total>$0.00</span>
                                        </div>

                                        <div class="mt-1 flex items-center justify-between gap-3">
                                            <span class="font-semibold text-slate-500 dark:text-slate-400">Already
                                                covered</span>
                                            <span class="font-black text-emerald-700 dark:text-emerald-300"
                                                data-finance-paid-total>$0.00</span>
                                        </div>

                                        <div class="mt-1 flex items-center justify-between gap-3">
                                            <span class="font-semibold text-slate-500 dark:text-slate-400">Tuition
                                                discount</span>
                                            <span class="font-black text-sky-700 dark:text-sky-300"
                                                data-finance-discount-total>$0.00</span>
                                        </div>

                                        <div class="mt-1 flex items-center justify-between gap-3">
                                            <span class="font-semibold text-slate-500 dark:text-slate-400">Cash to
                                                collect</span>
                                            <span class="font-black text-slate-700 dark:text-slate-100"
                                                data-finance-net-total>$0.00</span>
                                        </div>

                                        <div class="mt-1 flex items-center justify-between gap-3">
                                            <span class="font-semibold text-slate-500 dark:text-slate-400">Additional
                                                due</span>
                                            <span class="font-black text-amber-700 dark:text-amber-300"
                                                data-finance-remaining-total>$0.00</span>
                                        </div>

                                        <p class="mt-1 text-[11px] font-semibold text-slate-500 dark:text-slate-400"
                                            data-finance-tuition-subjects>
                                            Select a student to load tuition fee.
                                        </p>
                                    </div>
                                </div>

                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label for="amount" class="{{ $labelClass }}">Amount</label>

                                        <input id="amount" name="amount" type="number" step="0.01" min="0"
                                            value="{{ old('amount') }}"
                                            data-auto-filled="{{ old('amount') ? '0' : '1' }}"
                                            @disabled(!$tableReady) class="js-finance-amount {{ $inputClass }}"
                                            placeholder="0.00">

                                        @error('amount')
                                            <p class="mt-1 text-xs font-semibold text-red-600 dark:text-red-300">
                                                {{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="discount_amount" class="{{ $labelClass }}">Discount</label>

                                        <input id="discount_amount" name="discount_amount" type="number" step="0.01"
                                            min="0" value="{{ old('discount_amount') }}"
                                            @disabled(!$tableReady) class="js-finance-discount {{ $inputClass }}"
                                            placeholder="0.00">

                                        @error('discount_amount')
                                            <p class="mt-1 text-xs font-semibold text-red-600 dark:text-red-300">
                                                {{ $message }}</p>
                                        @enderror

                                        <p class="mt-1 text-[11px] font-semibold text-slate-400 dark:text-slate-500">
                                            Discount must be less than or equal to the amount.
                                        </p>
                                    </div>
                                </div>

                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label for="payment_date" class="{{ $labelClass }}">Payment Date</label>

                                        <input id="payment_date" name="payment_date" type="date"
                                            value="{{ old('payment_date', now()->toDateString()) }}"
                                            @disabled(!$tableReady) class="{{ $inputClass }}">
                                    </div>

                                    <div>
                                        <label for="due_date" class="{{ $labelClass }}">Due Date</label>

                                        <input id="due_date" name="due_date" type="date"
                                            value="{{ old('due_date') }}" @disabled(!$tableReady)
                                            class="{{ $inputClass }}">
                                    </div>
                                </div>

                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label for="status" class="{{ $labelClass }}">Status</label>

                                        <div
                                            class="flex min-h-[43px] items-center rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                            Automatic after save
                                        </div>
                                    </div>

                                    <div>
                                        <label for="payment_method" class="{{ $labelClass }}">Method</label>

                                        <select id="payment_method" name="payment_method" @disabled(!$tableReady)
                                            class="{{ $inputClass }}">
                                            <option value="">Select method</option>

                                            @foreach ($paymentMethods as $methodOption)
                                                <option value="{{ $methodOption }}"
                                                    {{ old('payment_method') === $methodOption ? 'selected' : '' }}>
                                                    {{ \App\Models\StudentPayment::methodLabel($methodOption) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label for="note" class="{{ $labelClass }}">Note</label>

                                    <textarea id="note" name="note" rows="3" @disabled(!$tableReady) class="{{ $textareaClass }}"
                                        placeholder="Optional payment detail">{{ old('note') }}</textarea>
                                </div>

                            </div>
                        </div>

                        <div
                            class="flex flex-col-reverse gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4 sm:flex-row sm:justify-end dark:border-slate-700 dark:bg-slate-950/40">
                            <button type="button" @click="createOpen = false"
                                class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                                Cancel
                            </button>

                            <button type="submit" @disabled(!$tableReady)
                                class="inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-sm shadow-indigo-500/20 transition hover:bg-indigo-500 disabled:cursor-not-allowed disabled:bg-slate-300 focus:outline-none focus:ring-4 focus:ring-indigo-200 dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:disabled:bg-slate-700 dark:focus:ring-indigo-500/25">
                                Save Payment
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            {{-- PAYMENT DETAILS --}}
            <section class="xl:col-span-12">
                @php
                    $financeExportQuery = array_filter(
                        [
                            'q' => $search,
                            'student_id' => $studentId !== 'all' ? $studentId : null,
                            'status' => $status !== 'all' ? $status : null,
                            'class_id' => $classId !== 'all' ? $classId : null,
                            'from' => $from !== '' ? $from : null,
                            'to' => $to !== '' ? $to : null,
                        ],
                        fn($value) => $value !== null && $value !== '',
                    );
                @endphp

                <div class="{{ $panelClass }} p-5" style="--sd: 4;" x-data="{ filterOpen: false, exportOpen: false }">

                    <form method="GET" action="{{ route('admin.finance.index') }}"
                        class="flex flex-wrap items-center gap-3">
                        <select name="class_id"
                            class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 shadow-sm outline-none transition hover:bg-slate-50 focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                            <option value="all" {{ $classId === 'all' ? 'selected' : '' }}>Classes</option>
                            @foreach ($classes as $classOption)
                                <option value="{{ $classOption->id }}"
                                    {{ $classId === (string) $classOption->id ? 'selected' : '' }}>
                                    {{ $classOption->display_name }}
                                </option>
                            @endforeach
                        </select>

                        <select name="status"
                            class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 shadow-sm outline-none transition hover:bg-slate-50 focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Status</option>
                            @foreach ($statuses as $statusOption)
                                <option value="{{ $statusOption }}" {{ $status === $statusOption ? 'selected' : '' }}>
                                    {{ ucfirst($statusOption) }}
                                </option>
                            @endforeach
                        </select>

                        <input name="q" type="search" value="{{ $search }}" placeholder="Search finance"
                            class="h-10 min-w-0 flex-1 rounded-lg border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 shadow-sm outline-none transition placeholder:text-slate-400 hover:bg-slate-50 focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100 sm:max-w-xs dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:placeholder:text-slate-500">

                        <button type="submit"
                            class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-200 bg-white px-4 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                            Apply
                        </button>

                        <button type="button" @click="filterOpen = true"
                            class="inline-flex h-10 items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M4 6h16M7 12h10M10 18h4" />
                            </svg>
                            All filters
                        </button>
                    </form>

                    {{-- FILTER OVERLAY --}}
                    <div x-show="filterOpen" x-cloak x-transition.opacity
                        class="fixed inset-0 z-[80] bg-slate-900/50 backdrop-blur-sm" @click="filterOpen = false"></div>

                    {{-- FILTER DRAWER --}}
                    <aside x-show="filterOpen" x-cloak x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0"
                        x-transition:leave-end="translate-x-full"
                        class="fixed inset-y-0 right-0 z-[81] w-full max-w-md transform border-l border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900">

                        <div class="flex h-full flex-col">
                            <div
                                class="flex items-center justify-between border-b border-slate-200 px-6 py-5 dark:border-slate-700">
                                <h3 class="text-3xl font-black text-slate-950 dark:text-white">Filters</h3>

                                <div class="flex items-center gap-4">
                                    <a href="{{ route('admin.finance.index') }}"
                                        class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:bg-slate-100 hover:text-slate-800 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                                        Clear All
                                    </a>

                                    <button type="button" @click="filterOpen = false"
                                        class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-2xl font-bold leading-none text-slate-600 shadow-sm transition hover:bg-slate-100 hover:text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                                        aria-label="Close filters">
                                        &times;
                                    </button>
                                </div>
                            </div>

                            <form method="GET" action="{{ route('admin.finance.index') }}"
                                class="flex min-h-0 flex-1 flex-col" @submit="filterOpen = false">

                                <div class="flex-1 space-y-6 overflow-y-auto px-6 py-5">
                                    <section class="space-y-2">
                                        <h4 class="text-xl font-bold text-slate-950 dark:text-white">Search</h4>
                                        <input name="q" value="{{ $search }}"
                                            placeholder="Search by name, email, or note" class="{{ $inputClass }}">
                                    </section>

                                    <section class="space-y-2">
                                        <h4 class="text-xl font-bold text-slate-950 dark:text-white">Student</h4>
                                        <select name="student_id" class="{{ $inputClass }}">
                                            <option value="all">All Students</option>

                                            @foreach ($students as $student)
                                                <option value="{{ $student->id }}"
                                                    {{ $studentId === (string) $student->id ? 'selected' : '' }}>
                                                    {{ $student->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </section>

                                    <section class="space-y-2">
                                        <h4 class="text-xl font-bold text-slate-950 dark:text-white">Class</h4>
                                        <select name="class_id" class="{{ $inputClass }}">
                                            <option value="all">All Classes</option>

                                            @foreach ($classes as $classOption)
                                                <option value="{{ $classOption->id }}"
                                                    {{ $classId === (string) $classOption->id ? 'selected' : '' }}>
                                                    {{ $classOption->display_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </section>

                                    <section class="space-y-2">
                                        <h4 class="text-xl font-bold text-slate-950 dark:text-white">Status</h4>
                                        <select name="status" class="{{ $inputClass }}">
                                            <option value="all">All</option>

                                            @foreach ($statuses as $statusOption)
                                                <option value="{{ $statusOption }}"
                                                    {{ $status === $statusOption ? 'selected' : '' }}>
                                                    {{ ucfirst($statusOption) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </section>

                                    <section class="space-y-2">
                                        <h4 class="text-xl font-bold text-slate-950 dark:text-white">Date Range</h4>

                                        <div class="grid gap-3 sm:grid-cols-2">
                                            <input name="from" type="date" value="{{ $from }}"
                                                class="{{ $inputClass }}">

                                            <input name="to" type="date" value="{{ $to }}"
                                                class="{{ $inputClass }}">
                                        </div>
                                    </section>
                                </div>

                                <div class="border-t border-slate-200 px-6 py-5 dark:border-slate-700">
                                    <button type="submit"
                                        class="inline-flex w-full items-center justify-center rounded-2xl bg-slate-950 px-4 py-3 text-base font-bold text-white shadow-sm transition hover:bg-slate-800 dark:bg-indigo-600 dark:hover:bg-indigo-500">
                                        Apply Filters
                                    </button>
                                </div>
                            </form>
                        </div>
                    </aside>

                    {{-- TABLE --}}
                    <div class="mt-5">
                        <div class="mt-1 overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-700">
                            <div class="finance-table-scroller min-h-[520px] max-h-[720px] overflow-auto"
                                style="scrollbar-gutter: stable;">
                                <table class="admin-table finance-payment-table w-full min-w-[1280px] text-left text-sm">
                                    <thead class="{{ $tableHeadClass }}">
                                        <tr>
                                            <th class="student-col-student px-3 py-4 font-semibold">Student</th>
                                            <th class="student-col-email px-3 py-4 font-semibold">Email</th>
                                            <th class="student-col-class px-3 py-4 font-semibold">Class</th>
                                            <th class="student-col-subjects px-3 py-4 font-semibold">Tuition Subjects</th>
                                            <th class="whitespace-nowrap px-3 py-4 font-semibold">Amount</th>
                                            <th class="student-col-status px-3 py-4 font-semibold">Status</th>
                                            <th class="whitespace-nowrap px-3 py-4 font-semibold">Payment Date</th>
                                            <th
                                                class="student-col-actions whitespace-nowrap px-3 py-4 text-right font-semibold">
                                                Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody class="{{ $tableBodyClass }}">
                                        @forelse ($payments as $paymentRow)
                                            @php
                                                $paymentStudent = $paymentRow['student'] ?? null;
                                                $latestPayment = $paymentRow['latest_payment'] ?? null;

                                                $studentName = trim(
                                                    (string) ($paymentStudent?->name ?? 'Unknown Student'),
                                                );
                                                $studentParts =
                                                    preg_split('/\s+/', $studentName, -1, PREG_SPLIT_NO_EMPTY) ?: [];

                                                $studentInitials = '';

                                                foreach (array_slice($studentParts, 0, 2) as $part) {
                                                    $studentInitials .= strtoupper(substr($part, 0, 1));
                                                }

                                                $studentInitials = $studentInitials !== '' ? $studentInitials : 'US';

                                                $paymentAmount = (float) ($paymentRow['paid_total'] ?? 0);
                                                $paymentDiscount = (float) ($paymentRow['discount_total'] ?? 0);
                                                $paymentNetAmount = (float) ($paymentRow['cash_collected'] ?? 0);
                                                $paymentRemainingDue = (float) ($paymentRow['remaining_due'] ?? 0);
                                                $paymentStatus = (string) ($paymentRow['status'] ?? 'pending');
                                                $paymentStatusLabel =
                                                    (string) ($paymentRow['status_label'] ?? ucfirst($paymentStatus));
                                                $paymentTuitionTotal = (float) ($paymentRow['tuition_total'] ?? 0);
                                                $paymentTuitionSubjects =
                                                    (string) ($paymentStudent?->tuition_subject_names ?:
                                                    'No subjects assigned');
                                                $latestPaymentAmount = (float) ($latestPayment?->amount ?? 0);
                                            @endphp

                                            <tr
                                                class="align-top transition hover:bg-slate-50/80 dark:hover:bg-slate-800/70">
                                                <td class="student-col-student px-3 py-3 align-top">
                                                    <div class="flex items-center gap-3">
                                                        @if ($paymentStudent)
                                                            <img src="{{ $paymentStudent->avatar_url }}"
                                                                alt="{{ $studentName }}"
                                                                class="h-9 w-9 rounded-full object-cover ring-1 ring-slate-200 dark:ring-slate-700">
                                                        @else
                                                            <span
                                                                class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-indigo-100 text-xs font-black text-indigo-700 ring-1 ring-indigo-200 dark:bg-indigo-500/15 dark:text-indigo-300 dark:ring-indigo-400/20">
                                                                {{ $studentInitials }}
                                                            </span>
                                                        @endif

                                                        <div class="min-w-0">
                                                            <div
                                                                class="student-name font-semibold text-slate-800 dark:text-slate-100">
                                                                {{ $studentName }}
                                                            </div>

                                                            <div
                                                                class="truncate text-xs text-slate-400 dark:text-slate-500">
                                                                ID #{{ $paymentStudent?->formatted_id ?? '0000000' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td
                                                    class="student-col-email px-3 py-3 align-top text-slate-600 dark:text-slate-300">
                                                    <div
                                                        class="student-email max-w-[360px] truncate text-slate-600 dark:text-slate-300">
                                                        {{ $paymentStudent?->email ?? '-' }}
                                                    </div>

                                                    <div
                                                        class="mt-1 max-w-[360px] truncate text-xs text-slate-400 dark:text-slate-500">
                                                        {{ \Illuminate\Support\Str::limit((string) ($latestPayment?->note ?: 'No payment recorded'), 52) }}
                                                    </div>
                                                </td>

                                                <td
                                                    class="student-col-class px-3 py-3 align-top text-slate-600 dark:text-slate-300">
                                                    @if ($paymentStudent?->schoolClass)
                                                        <span
                                                            class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-semibold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                                            {{ $paymentStudent->schoolClass->display_name }}
                                                        </span>
                                                    @else
                                                        <span class="text-slate-400 dark:text-slate-500">-</span>
                                                    @endif
                                                </td>

                                                <td
                                                    class="student-col-subjects px-3 py-3 align-top text-slate-600 dark:text-slate-300">
                                                    <div
                                                        class="max-w-[260px] truncate text-xs font-semibold text-slate-700 dark:text-slate-300">
                                                        {{ $paymentTuitionSubjects }}
                                                    </div>

                                                    <div
                                                        class="mt-1 text-xs font-semibold text-indigo-600 dark:text-indigo-300">
                                                        Tuition ${{ number_format($paymentTuitionTotal, 2) }}
                                                    </div>
                                                </td>

                                                <td
                                                    class="whitespace-nowrap px-3 py-3 align-top tabular-nums text-slate-700 dark:text-slate-300">
                                                    <div class="font-semibold text-slate-900 dark:text-white">
                                                        ${{ number_format($paymentNetAmount, 2) }}
                                                    </div>

                                                    <div
                                                        class="mt-1 text-xs font-semibold text-slate-400 dark:text-slate-500">
                                                        Paid ${{ number_format($paymentAmount, 2) }}
                                                    </div>

                                                    @if ($paymentDiscount > 0)
                                                        <div
                                                            class="mt-1 text-xs font-semibold text-sky-600 dark:text-sky-300">
                                                            Discount ${{ number_format($paymentDiscount, 2) }}
                                                        </div>
                                                    @endif

                                                    @if ($paymentRemainingDue > 0.009)
                                                        <div
                                                            class="mt-1 text-xs font-black text-amber-600 dark:text-amber-300">
                                                            Missing ${{ number_format($paymentRemainingDue, 2) }}
                                                        </div>
                                                    @else
                                                        <div
                                                            class="mt-1 text-xs font-semibold text-emerald-600 dark:text-emerald-300">
                                                            No missing amount
                                                        </div>
                                                    @endif
                                                </td>

                                                <td class="student-col-status px-3 py-3 align-top">
                                                    <span
                                                        class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $statusTone[$paymentStatus] ?? 'bg-slate-100 text-slate-700 ring-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-700' }}">
                                                        {{ $paymentStatusLabel }}
                                                    </span>

                                                    <div
                                                        class="mt-1 inline-flex max-w-[12rem] items-center rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-[11px] font-semibold text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                                        {{ $latestPayment?->method_label ?? 'No payment yet' }}
                                                    </div>
                                                </td>

                                                <td
                                                    class="whitespace-nowrap px-3 py-3 align-top text-slate-600 dark:text-slate-300">
                                                    <div>
                                                        {{ optional($latestPayment?->payment_date)->format('M d, Y') ?? '-' }}
                                                    </div>

                                                    @if ($latestPayment?->due_date)
                                                        <div class="mt-1 text-xs text-slate-400 dark:text-slate-500">
                                                            Due {{ $latestPayment->due_date->format('M d, Y') }}
                                                        </div>
                                                    @endif
                                                </td>

                                                <td class="student-col-actions px-3 py-3 align-top">
                                                    <div class="relative flex items-center justify-end gap-2"
                                                        x-data="{ editOpen: false, menuOpen: false }" @click.outside="menuOpen = false">
                                                        @if ($latestPayment)
                                                            <button type="button" @click="menuOpen = !menuOpen"
                                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-indigo-300"
                                                                aria-label="Open payment actions">
                                                                <svg class="h-5 w-5" viewBox="0 0 24 24"
                                                                    fill="currentColor" aria-hidden="true">
                                                                    <path
                                                                        d="M12 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z" />
                                                                </svg>
                                                            </button>

                                                            <div x-show="menuOpen" x-cloak
                                                                x-transition.opacity.scale.origin.top.right
                                                                class="absolute right-0 top-9 z-30 w-52 overflow-hidden rounded-xl border border-slate-200 bg-white py-2 text-left shadow-xl ring-1 ring-slate-900/5 dark:border-slate-700 dark:bg-slate-900 dark:ring-white/10">
                                                                <button type="button"
                                                                    @click="editOpen = true; menuOpen = false"
                                                                    class="flex w-full items-center gap-3 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-indigo-50 hover:text-indigo-700 dark:text-slate-200 dark:hover:bg-indigo-500/15 dark:hover:text-indigo-300">
                                                                    <svg class="h-4 w-4" viewBox="0 0 24 24"
                                                                        fill="none" stroke="currentColor"
                                                                        stroke-width="2" stroke-linecap="round"
                                                                        stroke-linejoin="round" aria-hidden="true">
                                                                        <path d="M12 20h9" />
                                                                        <path
                                                                            d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z" />
                                                                    </svg>
                                                                    Edit
                                                                </button>

                                                                <form method="POST"
                                                                    action="{{ route('admin.finance.destroy', $latestPayment) }}"
                                                                    class="js-finance-delete-form">
                                                                    @csrf
                                                                    @method('DELETE')

                                                                    <button type="submit"
                                                                        class="flex w-full items-center gap-3 px-4 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-50 dark:text-red-300 dark:hover:bg-red-500/15">
                                                                        Delete
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        @else
                                                            <span
                                                                class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-400 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-500">
                                                                No payment
                                                            </span>
                                                        @endif

                                                        @if ($latestPayment)
                                                            {{-- EDIT MODAL --}}
                                                            <template x-teleport="body">
                                                                <div x-show="editOpen" x-cloak x-transition.opacity
                                                                    class="fixed inset-0 z-[70] grid place-items-center p-4"
                                                                    aria-modal="true" role="dialog">

                                                                    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
                                                                        @click="editOpen = false"></div>

                                                                    <div class="{{ $modalPanelClass }}">
                                                                        <div
                                                                            class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-slate-700">
                                                                            <h3
                                                                                class="text-lg font-black text-slate-950 dark:text-white">
                                                                                Edit Payment
                                                                            </h3>

                                                                            <button type="button"
                                                                                @click="editOpen = false"
                                                                                class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-slate-200">
                                                                                <svg class="h-5 w-5" viewBox="0 0 24 24"
                                                                                    fill="currentColor">
                                                                                    <path
                                                                                        d="M18.3 5.71 12 12l6.3 6.29-1.41 1.42L10.59 13.4 4.3 19.7 2.89 18.3 9.17 12 2.9 5.71 4.3 4.29l6.29 6.3 6.3-6.3 1.41 1.42Z" />
                                                                                </svg>
                                                                            </button>
                                                                        </div>

                                                                        <form method="POST"
                                                                            action="{{ route('admin.finance.update', $latestPayment) }}"
                                                                            enctype="multipart/form-data"
                                                                            class="js-edit-form flex min-h-0 flex-1 flex-col"
                                                                            data-finance-payment-form
                                                                            data-current-net="{{ $latestPaymentAmount }}">
                                                                            @csrf
                                                                            @method('PUT')

                                                                            <div
                                                                                class="flex-1 space-y-4 overflow-y-auto overscroll-contain px-5 pb-4 pt-4">
                                                                                <div class="grid gap-4 sm:grid-cols-2">
                                                                                    <div class="sm:col-span-2">
                                                                                        <label
                                                                                            class="{{ $labelClass }}">Student</label>

                                                                                        <select name="student_id"
                                                                                            class="js-finance-student-select {{ $inputClass }}">
                                                                                            @foreach ($students as $student)
                                                                                                @php
                                                                                                    $isCurrentPaymentStudent =
                                                                                                        (int) $latestPayment->student_id ===
                                                                                                        (int) $student->id;
                                                                                                    $studentTuitionTotal =
                                                                                                        (float) ($student->tuition_total ??
                                                                                                            0);
                                                                                                    $studentPaidTotal =
                                                                                                        (float) ($studentPaidTotalSet[
                                                                                                            (string) $student->id
                                                                                                        ] ?? 0);
                                                                                                    $studentRemainingTotal = max(
                                                                                                        $studentTuitionTotal -
                                                                                                            $studentPaidTotal,
                                                                                                        0,
                                                                                                    );

                                                                                                    $studentPaidInFull =
                                                                                                        $studentPaidTotal >
                                                                                                            0 &&
                                                                                                        ($studentTuitionTotal <=
                                                                                                            0 ||
                                                                                                            $studentRemainingTotal <=
                                                                                                                0.009);
                                                                                                @endphp

                                                                                                <option
                                                                                                    value="{{ $student->id }}"
                                                                                                    @disabled($studentPaidInFull && !$isCurrentPaymentStudent)
                                                                                                    {{ $isCurrentPaymentStudent ? 'selected' : '' }}>
                                                                                                    {{ $student->name }} -
                                                                                                    {{ $student->email }}

                                                                                                    @if ($studentTuitionTotal > 0)
                                                                                                        - Due
                                                                                                        ${{ number_format($studentRemainingTotal, 2) }}
                                                                                                    @endif

                                                                                                    {{ $studentPaidInFull && !$isCurrentPaymentStudent ? ' (Paid in full)' : '' }}
                                                                                                </option>
                                                                                            @endforeach
                                                                                        </select>

                                                                                        <div
                                                                                            class="{{ $summaryBoxClass }}">
                                                                                            <div
                                                                                                class="flex items-center justify-between gap-3">
                                                                                                <span
                                                                                                    class="font-semibold text-slate-600 dark:text-slate-300">Tuition
                                                                                                    from subjects</span>
                                                                                                <span
                                                                                                    class="font-black text-indigo-700 dark:text-indigo-300"
                                                                                                    data-finance-tuition-total>$0.00</span>
                                                                                            </div>

                                                                                            <div
                                                                                                class="mt-1 flex items-center justify-between gap-3">
                                                                                                <span
                                                                                                    class="font-semibold text-slate-500 dark:text-slate-400">Already
                                                                                                    covered</span>
                                                                                                <span
                                                                                                    class="font-black text-emerald-700 dark:text-emerald-300"
                                                                                                    data-finance-paid-total>$0.00</span>
                                                                                            </div>

                                                                                            <div
                                                                                                class="mt-1 flex items-center justify-between gap-3">
                                                                                                <span
                                                                                                    class="font-semibold text-slate-500 dark:text-slate-400">Tuition
                                                                                                    discount</span>
                                                                                                <span
                                                                                                    class="font-black text-sky-700 dark:text-sky-300"
                                                                                                    data-finance-discount-total>$0.00</span>
                                                                                            </div>

                                                                                            <div
                                                                                                class="mt-1 flex items-center justify-between gap-3">
                                                                                                <span
                                                                                                    class="font-semibold text-slate-500 dark:text-slate-400">Cash
                                                                                                    to collect</span>
                                                                                                <span
                                                                                                    class="font-black text-slate-700 dark:text-slate-100"
                                                                                                    data-finance-net-total>$0.00</span>
                                                                                            </div>

                                                                                            <div
                                                                                                class="mt-1 flex items-center justify-between gap-3">
                                                                                                <span
                                                                                                    class="font-semibold text-slate-500 dark:text-slate-400">Additional
                                                                                                    due</span>
                                                                                                <span
                                                                                                    class="font-black text-amber-700 dark:text-amber-300"
                                                                                                    data-finance-remaining-total>$0.00</span>
                                                                                            </div>

                                                                                            <p class="mt-1 text-[11px] font-semibold text-slate-500 dark:text-slate-400"
                                                                                                data-finance-tuition-subjects>
                                                                                                Select a student to load
                                                                                                tuition
                                                                                                fee.
                                                                                            </p>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div>
                                                                                        <label
                                                                                            class="{{ $labelClass }}">Amount</label>

                                                                                        <input name="amount"
                                                                                            type="number" step="0.01"
                                                                                            min="0"
                                                                                            value="{{ $latestPayment->amount }}"
                                                                                            data-auto-filled="0"
                                                                                            class="js-finance-amount {{ $inputClass }}">
                                                                                    </div>

                                                                                    <div>
                                                                                        <label
                                                                                            class="{{ $labelClass }}">Discount</label>

                                                                                        <input name="discount_amount"
                                                                                            type="number" step="0.01"
                                                                                            min="0"
                                                                                            value="{{ $latestPayment->discount_amount }}"
                                                                                            class="js-finance-discount {{ $inputClass }}">

                                                                                        <p
                                                                                            class="mt-1 text-[11px] font-semibold text-slate-400 dark:text-slate-500">
                                                                                            Cannot be greater than amount.
                                                                                        </p>
                                                                                    </div>

                                                                                    <div>
                                                                                        <label
                                                                                            class="{{ $labelClass }}">Payment
                                                                                            Date</label>

                                                                                        <input name="payment_date"
                                                                                            type="date"
                                                                                            value="{{ optional($latestPayment->payment_date)->toDateString() }}"
                                                                                            class="{{ $inputClass }}">
                                                                                    </div>

                                                                                    <div>
                                                                                        <label
                                                                                            class="{{ $labelClass }}">Due
                                                                                            Date</label>

                                                                                        <input name="due_date"
                                                                                            type="date"
                                                                                            value="{{ optional($latestPayment->due_date)->toDateString() }}"
                                                                                            class="{{ $inputClass }}">
                                                                                    </div>

                                                                                    <div>
                                                                                        <label
                                                                                            class="{{ $labelClass }}">Status</label>

                                                                                        <div
                                                                                            class="flex min-h-[43px] items-center rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                                                                            Automatic after save
                                                                                        </div>
                                                                                    </div>

                                                                                    <div>
                                                                                        <label
                                                                                            class="{{ $labelClass }}">Method</label>

                                                                                        <select name="payment_method"
                                                                                            class="{{ $inputClass }}">
                                                                                            <option value="">Select
                                                                                                method</option>

                                                                                            @foreach ($paymentMethods as $methodOption)
                                                                                                <option
                                                                                                    value="{{ $methodOption }}"
                                                                                                    {{ $latestPayment->payment_method === $methodOption ? 'selected' : '' }}>
                                                                                                    {{ \App\Models\StudentPayment::methodLabel($methodOption) }}
                                                                                                </option>
                                                                                            @endforeach
                                                                                        </select>
                                                                                    </div>

                                                                                    <div class="sm:col-span-2">
                                                                                        <label
                                                                                            class="{{ $labelClass }}">Note</label>

                                                                                        <textarea name="note" rows="3" class="{{ $textareaClass }}">{{ $latestPayment->note }}</textarea>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div
                                                                                class="flex justify-end gap-2 border-t border-slate-100 px-5 py-4 dark:border-slate-700">
                                                                                <button type="button"
                                                                                    @click="editOpen = false"
                                                                                    class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                                                                                    Cancel
                                                                                </button>

                                                                                <button type="submit"
                                                                                    class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                                                                                    Save Changes
                                                                                </button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8"
                                                    class="px-5 py-10 text-center text-sm text-slate-500 dark:text-slate-400">
                                                    No student balances found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if ($payments instanceof \Illuminate\Contracts\Pagination\Paginator)
                                <div
                                    class="finance-pagination border-t border-slate-100 bg-slate-50/70 px-4 py-3 text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                                    {{ $payments->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const flash = {
                success: @json(session('success')),
                error: @json(session('error')),
                warning: @json(session('warning')),
                validation: @json($errors->any() ? 'Please check the finance form and try again.' : null),
            };

            const showFlash = (kind, title, text, options = {}) => {
                if (!text) {
                    return;
                }

                if (typeof window.Swal !== 'undefined') {
                    Swal.fire({
                        icon: kind,
                        title,
                        text,
                        ...options,
                    });

                    return;
                }

                window.alert(`${title}\n\n${text}`);
            };

            if (flash.success) {
                showFlash('success', 'Success', flash.success, {
                    timer: 2200,
                    showConfirmButton: false,
                });
            } else if (flash.error) {
                showFlash('error', 'Error', flash.error, {
                    timer: 3000,
                    showConfirmButton: false,
                });
            } else if (flash.warning) {
                showFlash('warning', 'Warning', flash.warning, {
                    timer: 3000,
                    showConfirmButton: false,
                });
            } else if (flash.validation) {
                showFlash('error', 'Validation Error', flash.validation, {
                    timer: 3500,
                    showConfirmButton: false,
                });
            }

            const studentTuition = @json($studentTuitionPayload);

            const formatCurrency = (amount) => `$${Number(amount || 0).toFixed(2)}`;

            const formatSubjectList = (subjects) => {
                if (!Array.isArray(subjects) || subjects.length === 0) {
                    return 'No tuition subjects assigned to this student.';
                }

                return subjects
                    .map((subject) => `${subject.name} (${formatCurrency(subject.fee)})`)
                    .join(', ');
            };

            document.querySelectorAll('[data-finance-payment-form]').forEach((form) => {
                const studentSelect = form.querySelector('.js-finance-student-select');
                const amountInput = form.querySelector('.js-finance-amount');
                const discountInput = form.querySelector('.js-finance-discount');
                const tuitionTotal = form.querySelector('[data-finance-tuition-total]');
                const paidTotal = form.querySelector('[data-finance-paid-total]');
                const discountTotal = form.querySelector('[data-finance-discount-total]');
                const netTotal = form.querySelector('[data-finance-net-total]');
                const remainingTotal = form.querySelector('[data-finance-remaining-total]');
                const tuitionSubjects = form.querySelector('[data-finance-tuition-subjects]');

                if (!studentSelect || !amountInput || !tuitionTotal || !tuitionSubjects) {
                    return;
                }

                const syncRemaining = () => {
                    const data = studentTuition[String(studentSelect.value)] || {
                        paid: 0,
                        total: 0,
                    };

                    const currentNet = Number(form.dataset.currentNet || 0);
                    const basePaid = Math.max(Number(data.paid || 0) - currentNet, 0);
                    const enteredAmount = Number(amountInput.value || 0);
                    const discountAmount = Math.min(Math.max(Number(discountInput?.value || 0), 0),
                        enteredAmount);
                    const netAmount = Math.max(enteredAmount - discountAmount, 0);
                    const paidAfterThis = basePaid + enteredAmount;

                    if (discountTotal) {
                        discountTotal.textContent = formatCurrency(discountAmount);
                    }

                    if (netTotal) {
                        netTotal.textContent = formatCurrency(netAmount);
                    }

                    if (remainingTotal) {
                        remainingTotal.textContent = formatCurrency(Math.max(Number(data.total || 0) -
                            paidAfterThis, 0));
                    }
                };

                const syncTuition = () => {
                    const data = studentTuition[String(studentSelect.value)] || {
                        amount: 0,
                        paid: 0,
                        remaining: 0,
                        subjects: [],
                        total: 0,
                    };

                    const currentNet = Number(form.dataset.currentNet || 0);
                    const basePaid = Math.max(Number(data.paid || 0) - currentNet, 0);
                    const amount = Math.max(Number(data.total || 0) - basePaid, 0) || Number(data
                        .amount || 0);

                    tuitionTotal.textContent = formatCurrency(data.total || amount);

                    if (paidTotal) {
                        paidTotal.textContent = formatCurrency(basePaid);
                    }

                    if (remainingTotal) {
                        remainingTotal.textContent = formatCurrency(data.remaining || 0);
                    }

                    tuitionSubjects.textContent = formatSubjectList(data.subjects);

                    if ((amountInput.value.trim() === '' || amountInput.dataset.autoFilled === '1') &&
                        amount > 0) {
                        amountInput.value = amount.toFixed(2);
                        amountInput.dataset.autoFilled = '1';
                    }

                    syncRemaining();
                };

                amountInput.addEventListener('input', () => {
                    amountInput.dataset.autoFilled = '0';
                    syncRemaining();
                });

                discountInput?.addEventListener('input', syncRemaining);
                studentSelect.addEventListener('change', syncTuition);

                syncTuition();
            });

            document.querySelectorAll('.js-finance-delete-form').forEach((form) => {
                form.addEventListener('submit', (event) => {
                    if (form.dataset.confirmed === '1') {
                        return;
                    }

                    event.preventDefault();

                    if (typeof window.Swal === 'undefined') {
                        if (window.confirm('Delete this student payment?')) {
                            form.dataset.confirmed = '1';
                            form.submit();
                        }

                        return;
                    }

                    Swal.fire({
                        icon: 'warning',
                        title: 'Delete payment?',
                        text: 'This student payment record will be removed.',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#dc2626',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.dataset.confirmed = '1';
                            form.submit();
                        }
                    });
                });
            });

            document.querySelectorAll('.js-edit-form').forEach((form) => {
                form.addEventListener('submit', (event) => {
                    if (form.dataset.confirmed === '1') {
                        return;
                    }

                    event.preventDefault();

                    const studentLabel = form
                        .querySelector('.js-finance-student-select option:checked')
                        ?.textContent
                        ?.trim() || 'this payment';

                    const title = 'Save changes?';
                    const text = `Update payment for ${studentLabel}.`;

                    if (typeof window.Swal === 'undefined') {
                        if (window.confirm(`${title}\n\n${text}`)) {
                            form.dataset.confirmed = '1';
                            form.submit();
                        }

                        return;
                    }

                    Swal.fire({
                        icon: 'question',
                        title,
                        text,
                        showCancelButton: true,
                        confirmButtonText: 'Yes, save',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#4f46e5',
                        cancelButtonColor: '#6b7280',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.dataset.confirmed = '1';
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection
