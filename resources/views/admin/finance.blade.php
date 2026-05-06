@extends('layout.admin.navbar.navbar')

@section('page')
    @php
        $paymentTotal = (int) ($stats['payments'] ?? 0);
        $collectedTotal = (float) ($stats['collected'] ?? 0);
        $outstandingTotal = (float) ($stats['outstanding'] ?? 0);
        $discountTotal = (float) ($stats['discounts'] ?? 0);
        $studentsPaid = (int) ($stats['students_paid'] ?? 0);
        $financeCards = [
            [
                'label' => 'Collected',
                'activeLabel' => 'Income',
                'active' => (int) round($collectedTotal),
                'total' => max(1, (int) round($collectedTotal + $outstandingTotal)),
                'displayActive' => '$' . number_format($collectedTotal, 2),
                'displayTotal' => '$' . number_format(max(0, $collectedTotal + $outstandingTotal), 2),
                'icon' => 'active',
                'tone' => 'from-emerald-100 to-white text-emerald-600',
                'barTone' => 'from-emerald-500 to-teal-400',
            ],
            [
                'label' => 'Outstanding',
                'activeLabel' => 'Due',
                'active' => (int) round($outstandingTotal),
                'total' => max(1, (int) round($collectedTotal + $outstandingTotal)),
                'displayActive' => '$' . number_format($outstandingTotal, 2),
                'displayTotal' => '$' . number_format(max(0, $collectedTotal + $outstandingTotal), 2),
                'icon' => 'pending',
                'tone' => 'from-amber-100 to-white text-amber-600',
                'barTone' => 'from-amber-500 to-orange-400',
            ],
            [
                'label' => 'Discounts',
                'activeLabel' => 'Given',
                'active' => (int) round($discountTotal),
                'total' => max(1, (int) round($discountTotal + $collectedTotal)),
                'displayActive' => '$' . number_format($discountTotal, 2),
                'displayTotal' => '$' . number_format(max(0, $discountTotal + $collectedTotal), 2),
                'icon' => 'records',
                'tone' => 'from-sky-100 to-white text-sky-600',
                'barTone' => 'from-sky-500 to-indigo-400',
            ],
            [
                'label' => 'Payment Records',
                'activeLabel' => 'Records',
                'active' => $paymentTotal,
                'total' => max(1, $paymentTotal),
                'displayActive' => number_format($paymentTotal),
                'displayTotal' => number_format($studentsPaid) . ' students',
                'icon' => 'assignment',
                'tone' => 'from-indigo-100 to-white text-indigo-600',
            ],
        ];
        $statusTone = [
            'paid' => 'bg-emerald-50 text-emerald-700 ring-emerald-100',
            'partial' => 'bg-sky-50 text-sky-700 ring-sky-100',
            'pending' => 'bg-amber-50 text-amber-700 ring-amber-100',
            'overdue' => 'bg-rose-50 text-rose-700 ring-rose-100',
            'waived' => 'bg-slate-100 text-slate-700 ring-slate-200',
        ];
    @endphp

    <style>
        .student-stage .finance-payment-table {
            min-width: 920px;
        }

        .student-stage .finance-payment-table .student-col-student {
            min-width: 170px;
        }

        .student-stage .finance-payment-table .student-col-email {
            min-width: 190px;
        }

        .student-stage .finance-payment-table .student-col-class {
            min-width: 130px;
        }

        .student-stage .finance-payment-table .student-col-status {
            min-width: 112px;
        }

        .student-stage .finance-payment-table .student-col-actions {
            min-width: 132px;
        }
    </style>

    <div class="student-stage space-y-6">
        <x-admin.page-header reveal-class="student-reveal" delay="1" icon="finance" title="School Finance"
            subtitle="Record student payments, track outstanding balances, and export finance details for reports.">
        </x-admin.page-header>

        <x-admin.stat-cards :cards="$financeCards" reveal-class="student-reveal" float-class="student-float"
            grid-class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4" />

        @if (!$tableReady)
            <div class="student-reveal rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-800"
                style="--sd: 2;">
                Finance is ready, but the database table is not installed yet. Run <span class="font-black">php artisan
                    migrate</span> to enable payment records.
            </div>
        @endif

        @if (session('success'))
            <div class="student-reveal hidden rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"
                style="--sd: 2;">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="student-reveal hidden rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700"
                style="--sd: 2;">
                {{ session('error') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="student-reveal hidden rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700"
                style="--sd: 2;">
                {{ session('warning') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="student-reveal hidden rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700"
                style="--sd: 2;">
                Please check the finance form and try again.
            </div>
        @endif

        <div class="grid gap-6 xl:grid-cols-12">
            <section
                class="student-reveal student-float rounded-3xl border border-slate-100 bg-white/95 p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-4"
                style="--sd: 3;">
                <div>
                    <h2 class="text-lg font-black text-slate-900">Record Payment</h2>
                    <p class="mt-1 text-xs text-slate-500">Add a student payment, due item, discount, or scholarship waiver.</p>
                </div>

                <form method="POST" action="{{ route('admin.finance.store') }}" class="mt-5 space-y-4">
                    @csrf
                    <div>
                        <label for="student_id" class="mb-1 block text-xs font-semibold text-slate-600">Student</label>
                        <select id="student_id" name="student_id" @disabled(!$tableReady)
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            <option value="">Select student</option>
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}" {{ (string) old('student_id') === (string) $student->id ? 'selected' : '' }}>
                                    {{ $student->name }} - {{ $student->email }}
                                </option>
                            @endforeach
                        </select>
                        @error('student_id')
                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="amount" class="mb-1 block text-xs font-semibold text-slate-600">Amount</label>
                            <input id="amount" name="amount" type="number" step="0.01" min="0"
                                value="{{ old('amount') }}" @disabled(!$tableReady)
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                                placeholder="0.00">
                            @error('amount')
                                <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="discount_amount" class="mb-1 block text-xs font-semibold text-slate-600">Discount</label>
                            <input id="discount_amount" name="discount_amount" type="number" step="0.01" min="0"
                                value="{{ old('discount_amount') }}" @disabled(!$tableReady)
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                                placeholder="0.00">
                            @error('discount_amount')
                                <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="payment_date" class="mb-1 block text-xs font-semibold text-slate-600">Payment Date</label>
                            <input id="payment_date" name="payment_date" type="date"
                                value="{{ old('payment_date', now()->toDateString()) }}" @disabled(!$tableReady)
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                        </div>
                        <div>
                            <label for="due_date" class="mb-1 block text-xs font-semibold text-slate-600">Due Date</label>
                            <input id="due_date" name="due_date" type="date" value="{{ old('due_date') }}"
                                @disabled(!$tableReady)
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="status" class="mb-1 block text-xs font-semibold text-slate-600">Status</label>
                            <select id="status" name="status" @disabled(!$tableReady)
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                @foreach ($statuses as $statusOption)
                                    <option value="{{ $statusOption }}" {{ old('status', 'paid') === $statusOption ? 'selected' : '' }}>
                                        {{ ucfirst($statusOption) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="payment_method" class="mb-1 block text-xs font-semibold text-slate-600">Method</label>
                            <select id="payment_method" name="payment_method" @disabled(!$tableReady)
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                <option value="">Select method</option>
                                @foreach ($paymentMethods as $methodOption)
                                    <option value="{{ $methodOption }}" {{ old('payment_method') === $methodOption ? 'selected' : '' }}>
                                        {{ \App\Models\StudentPayment::methodLabel($methodOption) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="note" class="mb-1 block text-xs font-semibold text-slate-600">Note</label>
                        <textarea id="note" name="note" rows="3" @disabled(!$tableReady)
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100"
                            placeholder="Optional payment detail">{{ old('note') }}</textarea>
                    </div>

                    <button type="submit" @disabled(!$tableReady)
                        class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:bg-slate-300">
                        Save Payment
                    </button>
                </form>
            </section>

            <section class="xl:col-span-8">
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
                <div class="student-reveal rounded-[28px] border border-slate-100 bg-white/95 p-5 shadow-[0_24px_60px_-42px_rgba(15,23,42,0.45)] ring-1 ring-slate-200"
                    style="--sd: 4;" x-data="{ filterOpen: false, exportOpen: false }">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-black tracking-[-0.03em] text-slate-950">Payment Details</h2>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <div class="relative" @keydown.escape.window="exportOpen = false">
                                <button type="button" @click="exportOpen = !exportOpen"
                                    :aria-expanded="exportOpen.toString()" aria-haspopup="menu"
                                    class="inline-flex min-w-[150px] items-center justify-center gap-2 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm font-semibold text-rose-700 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-rose-300 hover:bg-rose-100">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        aria-hidden="true">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <path d="M14 2v6h6"></path>
                                        <path d="M9 15h6"></path>
                                        <path d="M9 11h2"></path>
                                        <path d="M9 19h6"></path>
                                    </svg>
                                    Export
                                    <svg class="h-4 w-4 transition-transform duration-200"
                                        :class="{ 'rotate-180': exportOpen }" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" aria-hidden="true">
                                        <path d="m6 9 6 6 6-6"></path>
                                    </svg>
                                </button>

                                <div x-show="exportOpen" x-cloak x-transition.opacity.scale.origin.top.right
                                    @click.outside="exportOpen = false"
                                    class="absolute right-0 z-40 mt-3 w-64 overflow-hidden rounded-2xl border border-slate-200 bg-white p-2 shadow-xl ring-1 ring-slate-900/5">
                                    <a href="{{ route('admin.finance.export.pdf', $financeExportQuery) }}"
                                        class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold text-rose-700 transition hover:bg-rose-50">
                                        <span
                                            class="flex h-10 w-10 items-center justify-center rounded-full bg-rose-50 text-rose-700">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" aria-hidden="true">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                <path d="M14 2v6h6"></path>
                                                <path d="M9 15h6"></path>
                                                <path d="M9 11h2"></path>
                                                <path d="M9 19h6"></path>
                                            </svg>
                                        </span>
                                        <span>
                                            <span class="block">PDF Report</span>
                                            <span class="block text-xs font-medium text-slate-500">Download as PDF</span>
                                        </span>
                                    </a>
                                    <a href="{{ route('admin.finance.export.excel', $financeExportQuery) }}"
                                        class="mt-1 flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50">
                                        <span
                                            class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-50 text-emerald-700">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" aria-hidden="true">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                <path d="M14 2v6h6"></path>
                                                <path d="m9 15 6-6"></path>
                                                <path d="m15 15-6-6"></path>
                                            </svg>
                                        </span>
                                        <span>
                                            <span class="block">Excel Report</span>
                                            <span class="block text-xs font-medium text-slate-500">Download as workbook</span>
                                        </span>
                                    </a>
                                </div>
                            </div>

                            <button type="button" @click="filterOpen = true"
                                class="inline-flex min-w-[150px] items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:bg-slate-50">
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

                    <aside x-show="filterOpen" x-cloak x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0"
                        x-transition:leave-end="translate-x-full"
                        class="fixed inset-y-0 right-0 z-[81] w-full max-w-md transform border-l border-slate-200 bg-white shadow-2xl">
                        <div class="flex h-full flex-col">
                            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
                                <h3 class="text-3xl font-black text-slate-900">Filters</h3>
                                <div class="flex items-center gap-4">
                                    <a href="{{ route('admin.finance.index') }}"
                                        class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:bg-slate-100 hover:text-slate-800">
                                        Clear All
                                    </a>
                                    <button type="button" @click="filterOpen = false"
                                        class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-2xl font-bold leading-none text-slate-600 shadow-sm transition hover:bg-slate-100 hover:text-slate-900"
                                        aria-label="Close filters">
                                        &times;
                                    </button>
                                </div>
                            </div>

                            <form method="GET" action="{{ route('admin.finance.index') }}"
                                class="flex min-h-0 flex-1 flex-col" @submit="filterOpen = false">
                                <div class="flex-1 space-y-6 overflow-y-auto px-6 py-5">
                                    <section class="space-y-2">
                                        <h4 class="text-xl font-bold text-slate-900">Search</h4>
                                        <input name="q" value="{{ $search }}"
                                            placeholder="Search by name, email, or note"
                                            class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                    </section>

                                    <section class="space-y-2">
                                        <h4 class="text-xl font-bold text-slate-900">Student</h4>
                                        <select name="student_id"
                                            class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                            <option value="all">All Students</option>
                                            @foreach ($students as $student)
                                                <option value="{{ $student->id }}" {{ $studentId === (string) $student->id ? 'selected' : '' }}>
                                                    {{ $student->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </section>

                                    <section class="space-y-2">
                                        <h4 class="text-xl font-bold text-slate-900">Class</h4>
                                        <select name="class_id"
                                            class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                            <option value="all">All Classes</option>
                                            @foreach ($classes as $classOption)
                                                <option value="{{ $classOption->id }}" {{ $classId === (string) $classOption->id ? 'selected' : '' }}>
                                                    {{ $classOption->display_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </section>

                                    <section class="space-y-2">
                                        <h4 class="text-xl font-bold text-slate-900">Status</h4>
                                        <select name="status"
                                            class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                            <option value="all">All</option>
                                            @foreach ($statuses as $statusOption)
                                                <option value="{{ $statusOption }}" {{ $status === $statusOption ? 'selected' : '' }}>
                                                    {{ ucfirst($statusOption) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </section>

                                    <section class="space-y-2">
                                        <h4 class="text-xl font-bold text-slate-900">Date Range</h4>
                                        <div class="grid gap-3 sm:grid-cols-2">
                                            <input name="from" type="date" value="{{ $from }}"
                                                class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                            <input name="to" type="date" value="{{ $to }}"
                                                class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                        </div>
                                    </section>
                                </div>

                                <div class="border-t border-slate-200 px-6 py-5">
                                    <button type="submit"
                                        class="inline-flex w-full items-center justify-center rounded-2xl bg-slate-950 px-4 py-3 text-base font-bold text-white shadow-sm transition hover:bg-slate-800">
                                        Apply Filters
                                    </button>
                                </div>
                            </form>
                        </div>
                    </aside>

                    <div class="mt-5 min-w-0">
                        <div class="mt-1 overflow-hidden rounded-2xl border border-slate-200">
                            <div class="student-table-scroller max-h-[620px] overflow-auto" style="scrollbar-gutter: stable;">
                                <table class="admin-table student-table finance-payment-table w-full text-left text-sm">
                                    <thead
                                        class="admin-table-head sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                        <tr>
                                            <th class="student-col-student px-3 py-3 font-semibold">Student</th>
                                            <th class="student-col-email px-3 py-3 font-semibold">Email</th>
                                            <th class="student-col-class px-3 py-3 font-semibold">Class</th>
                                            <th class="whitespace-nowrap px-3 py-3 font-semibold">Amount</th>
                                            <th class="student-col-status px-3 py-3 font-semibold">Status</th>
                                            <th class="whitespace-nowrap px-3 py-3 font-semibold">Payment Date</th>
                                            <th class="student-col-actions whitespace-nowrap px-3 py-3 font-semibold">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse ($payments as $payment)
                                    @php
                                        $paymentStudent = $payment->student;
                                        $studentName = trim((string) ($payment->student?->name ?? 'Unknown Student'));
                                        $studentParts = preg_split('/\s+/', $studentName, -1, PREG_SPLIT_NO_EMPTY) ?: [];
                                        $studentInitials = '';
                                        foreach (array_slice($studentParts, 0, 2) as $part) {
                                            $studentInitials .= strtoupper(substr($part, 0, 1));
                                        }
                                        $studentInitials = $studentInitials !== '' ? $studentInitials : 'US';
                                    @endphp
                                    <tr class="align-top hover:bg-slate-50/80">
                                        <td class="student-col-student px-3 py-3 align-top">
                                            <div class="flex items-center gap-3">
                                                @if ($paymentStudent)
                                                    <img src="{{ $paymentStudent->avatar_url }}"
                                                        alt="{{ $studentName }}"
                                                        class="h-9 w-9 rounded-full object-cover ring-1 ring-slate-200">
                                                @else
                                                    <span
                                                        class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-indigo-100 text-xs font-black text-indigo-700 ring-1 ring-indigo-200">
                                                        {{ $studentInitials }}
                                                    </span>
                                                @endif
                                                <div class="min-w-0">
                                                    <div class="student-name font-semibold text-slate-800">{{ $studentName }}</div>
                                                    <div class="text-xs truncate text-slate-400">ID #{{ $payment->student?->formatted_id ?? str_pad((string) $payment->student_id, 7, '0', STR_PAD_LEFT) }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="student-col-email px-3 py-3 align-top text-slate-600">
                                            <div class="student-email max-w-[360px] truncate text-slate-600">
                                                {{ $paymentStudent?->email ?? '-' }}
                                            </div>
                                            <div class="mt-1 max-w-[360px] truncate text-xs text-slate-400">
                                                {{ \Illuminate\Support\Str::limit((string) ($payment->note ?: 'No note'), 52) }}
                                            </div>
                                        </td>
                                        <td class="student-col-class px-3 py-3 align-top text-slate-600">
                                            @if ($paymentStudent?->schoolClass)
                                                <span
                                                    class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                                    {{ $paymentStudent->schoolClass->display_name }}
                                                </span>
                                            @else
                                                <span class="text-slate-400">-</span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-3 align-top tabular-nums text-slate-700">
                                            <div class="font-semibold text-slate-900">${{ number_format((float) $payment->amount, 2) }}</div>
                                            @if ((float) $payment->discount_amount > 0)
                                                <div class="mt-1 text-xs font-semibold text-sky-600">
                                                    Discount ${{ number_format((float) $payment->discount_amount, 2) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="student-col-status px-3 py-3 align-top">
                                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $statusTone[$payment->status] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">
                                                {{ $payment->status_label }}
                                            </span>
                                            <div
                                                class="mt-1 inline-flex max-w-[12rem] items-center rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-[11px] font-semibold text-slate-600">
                                                {{ $payment->method_label }}
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-3 align-top text-slate-600">
                                            <div>{{ optional($payment->payment_date)->format('M d, Y') ?? '-' }}</div>
                                            @if ($payment->due_date)
                                                <div class="mt-1 text-xs text-slate-400">Due {{ $payment->due_date->format('M d, Y') }}</div>
                                            @endif
                                        </td>
                                        <td class="student-col-actions px-3 py-3 align-top">
                                            <div class="flex justify-end gap-2" x-data="{ editOpen: false }">
                                                <button type="button" @click="editOpen = true"
                                                    class=" rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                                                    Edit
                                                </button>
                                                <form method="POST" action="{{ route('admin.finance.destroy', $payment) }}"
                                                    class="js-finance-delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        class=" rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100 disabled:cursor-not-allowed disabled:opacity-50">
                                                            Delete
                                                    </button>
                                                </form>

                                                <div x-show="editOpen" x-cloak x-transition.opacity
                                                    class="fixed inset-0 z-[90] grid place-items-center bg-slate-950/45 p-4"
                                                    @click.self="editOpen = false">
                                                    <form method="POST" action="{{ route('admin.finance.update', $payment) }}"
                                                        class="w-full max-w-2xl rounded-3xl bg-white text-left shadow-2xl">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="border-b border-slate-100 p-5">
                                                            <h3 class="text-lg font-black text-slate-900">Edit Payment</h3>
                                                            <p class="mt-1 text-xs text-slate-500">{{ $payment->student?->name ?? 'Student payment' }}</p>
                                                        </div>
                                                        <div class="grid gap-4 p-5 sm:grid-cols-2">
                                                            <div class="sm:col-span-2">
                                                                <label class="mb-1 block text-xs font-semibold text-slate-600">Student</label>
                                                                <select name="student_id"
                                                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                    @foreach ($students as $student)
                                                                        <option value="{{ $student->id }}" {{ (int) $payment->student_id === (int) $student->id ? 'selected' : '' }}>
                                                                            {{ $student->name }} - {{ $student->email }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label class="mb-1 block text-xs font-semibold text-slate-600">Amount</label>
                                                                <input name="amount" type="number" step="0.01" min="0" value="{{ $payment->amount }}"
                                                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                            </div>
                                                            <div>
                                                                <label class="mb-1 block text-xs font-semibold text-slate-600">Discount</label>
                                                                <input name="discount_amount" type="number" step="0.01" min="0" value="{{ $payment->discount_amount }}"
                                                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                            </div>
                                                            <div>
                                                                <label class="mb-1 block text-xs font-semibold text-slate-600">Payment Date</label>
                                                                <input name="payment_date" type="date" value="{{ optional($payment->payment_date)->toDateString() }}"
                                                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                            </div>
                                                            <div>
                                                                <label class="mb-1 block text-xs font-semibold text-slate-600">Due Date</label>
                                                                <input name="due_date" type="date" value="{{ optional($payment->due_date)->toDateString() }}"
                                                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                            </div>
                                                            <div>
                                                                <label class="mb-1 block text-xs font-semibold text-slate-600">Status</label>
                                                                <select name="status"
                                                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                    @foreach ($statuses as $statusOption)
                                                                        <option value="{{ $statusOption }}" {{ $payment->status === $statusOption ? 'selected' : '' }}>
                                                                            {{ ucfirst($statusOption) }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label class="mb-1 block text-xs font-semibold text-slate-600">Method</label>
                                                                <select name="payment_method"
                                                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                                                                    <option value="">Select method</option>
                                                                    @foreach ($paymentMethods as $methodOption)
                                                                        <option value="{{ $methodOption }}" {{ $payment->payment_method === $methodOption ? 'selected' : '' }}>
                                                                            {{ \App\Models\StudentPayment::methodLabel($methodOption) }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="sm:col-span-2">
                                                                <label class="mb-1 block text-xs font-semibold text-slate-600">Note</label>
                                                                <textarea name="note" rows="3"
                                                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">{{ $payment->note }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="flex justify-end gap-2 border-t border-slate-100 p-5">
                                                            <button type="button" @click="editOpen = false"
                                                                class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50">
                                                                Cancel
                                                            </button>
                                                            <button
                                                                class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-bold text-white hover:bg-indigo-700">
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
                                        <td colspan="7" class="px-5 py-10 text-center text-sm text-slate-500">
                                            No student payments found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($payments instanceof \Illuminate\Contracts\Pagination\Paginator)
                        <div class="p-5">
                            {{ $payments->links() }}
                        </div>
                    @endif
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

            if (typeof window.Swal !== 'undefined') {
                if (flash.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved',
                        text: flash.success,
                        confirmButtonColor: '#4f46e5',
                    });
                } else if (flash.error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Unable to save',
                        text: flash.error,
                        confirmButtonColor: '#dc2626',
                    });
                } else if (flash.warning) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Heads up',
                        text: flash.warning,
                        confirmButtonColor: '#d97706',
                    });
                } else if (flash.validation) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Check the form',
                        text: flash.validation,
                        confirmButtonColor: '#dc2626',
                    });
                }
            }

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
                        confirmButtonText: 'Delete',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#64748b',
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
