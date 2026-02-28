@extends('layout.teacher.navbar')

@section('page')
    @php
        $statusStyles = [
            'pending' => 'border-amber-200 bg-amber-50 text-amber-700',
            'approved' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
            'rejected' => 'border-red-200 bg-red-50 text-red-700',
        ];
    @endphp

    <div class="space-y-6">
        <section class="admin-page-header teacher-page-header">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
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
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid gap-6 xl:grid-cols-12">
            <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-5">
                <h2 class="text-lg font-black text-slate-900">Create Request</h2>
                <p class="mt-1 text-xs font-semibold text-slate-500">Fill in the details and submit your request.</p>

                <form method="POST" action="{{ route('teacher.law-requests.store') }}" class="mt-5 space-y-4">
                    @csrf

                    <div class="space-y-2">
                        <label for="law_type" class="text-sm font-semibold text-slate-700">Law Type</label>
                        <select id="law_type" name="law_type"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                            @foreach ($lawTypes as $typeKey => $typeLabel)
                                <option value="{{ $typeKey }}" {{ old('law_type') === $typeKey ? 'selected' : '' }}>
                                    {{ $typeLabel }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="subject" class="text-sm font-semibold text-slate-700">Subject</label>
                        <input id="subject" type="text" name="subject" value="{{ old('subject') }}" maxlength="150"
                            placeholder="Example: New classroom phone usage rule"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                    </div>

                    <div class="space-y-2">
                        <label for="requested_for" class="text-sm font-semibold text-slate-700">Requested For Date</label>
                        <input id="requested_for" type="date" name="requested_for" value="{{ old('requested_for') }}"
                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                    </div>

                    <div class="space-y-2">
                        <label for="reason" class="text-sm font-semibold text-slate-700">Reason</label>
                        <textarea id="reason" name="reason" rows="6" maxlength="5000"
                            placeholder="Write full details of the law request..."
                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">{{ old('reason') }}</textarea>
                    </div>

                    <button type="submit"
                        class="inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-indigo-500">
                        Submit Law Request
                    </button>
                </form>
            </section>

            <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-7">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-black text-slate-900">Request History</h2>
                    <span class="text-xs font-semibold text-slate-500">Latest first</span>
                </div>

                <div class="overflow-hidden rounded-2xl border border-slate-200">
                    <div class="max-h-[620px] overflow-auto">
                        <table class="w-full min-w-[760px] text-left text-sm">
                            <thead class="sticky top-0 z-10 border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-3 py-3 font-semibold">Type</th>
                                    <th class="px-3 py-3 font-semibold">Subject</th>
                                    <th class="px-3 py-3 font-semibold">Requested For</th>
                                    <th class="px-3 py-3 font-semibold">Status</th>
                                    <th class="px-3 py-3 font-semibold">Submitted</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse ($lawRequests as $lawRequest)
                                    @php
                                        $statusKey = strtolower((string) ($lawRequest->status ?? 'pending'));
                                        $statusClass = $statusStyles[$statusKey] ?? 'border-slate-200 bg-slate-50 text-slate-700';
                                        $typeLabel = $lawTypes[$lawRequest->law_type] ?? ucfirst(str_replace('_', ' ', (string) $lawRequest->law_type));
                                    @endphp
                                    <tr class="align-top hover:bg-slate-50/80">
                                        <td class="px-3 py-3 font-semibold text-slate-800">{{ $typeLabel }}</td>
                                        <td class="px-3 py-3">
                                            <div class="font-semibold text-slate-900">{{ $lawRequest->subject }}</div>
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
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-10 text-center text-sm text-slate-500">
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
@endsection
