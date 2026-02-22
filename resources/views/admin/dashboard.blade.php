@extends('layout.admin.navbar.navbar')

@section('page')
    <div class="dashboard-stage space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="dash-reveal dash-hover rounded-3xl border border-violet-100 bg-violet-100/70 p-5"
                style="--d: 1;">
                <div class="text-xs font-semibold uppercase tracking-wide text-violet-500">Students</div>
                <div class="mt-2 text-3xl font-black text-slate-900">{{ number_format($studentsTotal ?? 0) }}</div>
                <div class="mt-1 text-sm text-slate-500">Total active</div>
            </article>

            <article class="dash-reveal dash-hover rounded-3xl border border-amber-100 bg-amber-100/70 p-5" style="--d: 2;">
                <div class="text-xs font-semibold uppercase tracking-wide text-amber-600">Teachers</div>
                <div class="mt-2 text-3xl font-black text-slate-900">{{ number_format($teachersTotal ?? 0) }}</div>
                <div class="mt-1 text-sm text-slate-500">Team members</div>
            </article>

            <article class="dash-reveal dash-hover rounded-3xl border border-indigo-100 bg-indigo-100/70 p-5"
                style="--d: 3;">
                <div class="text-xs font-semibold uppercase tracking-wide text-indigo-500">Tests</div>
                <div class="mt-2 text-3xl font-black text-slate-900">29,300</div>
                <div class="mt-1 text-sm text-slate-500">This semester</div>
            </article>

            <article class="dash-reveal dash-hover rounded-3xl border border-yellow-100 bg-yellow-100/80 p-5"
                style="--d: 4;">
                <div class="text-xs font-semibold uppercase tracking-wide text-yellow-600">Parents</div>
                <div class="mt-2 text-3xl font-black text-slate-900">95,800</div>
                <div class="mt-1 text-sm text-slate-500">Accounts linked</div>
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-12">
            <div class="space-y-6 xl:col-span-8">
                <div class="grid gap-6 lg:grid-cols-3">
                    <article
                        class="dash-reveal dash-hover rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 lg:col-span-1"
                        style="--d: 5;">
                        <div class="flex items-center justify-between">
                            <h2 class="text-base font-bold text-slate-900">Students</h2>
                            <a href="#" class="text-xs font-semibold text-slate-400">View</a>
                        </div>

                        <div class="mt-5 flex justify-center">
                            <div class="dash-ring grid h-44 w-44 place-items-center rounded-full bg-[conic-gradient(#7dd3fc_0_225deg,#facc15_225deg_325deg,#e2e8f0_325deg_360deg)]"
                                style="--d: 6;">
                                <div class="grid h-32 w-32 place-items-center rounded-full bg-white">
                                    <svg class="h-10 w-10 text-slate-500" viewBox="0 0 24 24" fill="currentColor"
                                        aria-hidden="true">
                                        <path
                                            d="M12 12c2.2 0 4-1.8 4-4s-1.8-4-4-4-4 1.8-4 4 1.8 4 4 4Zm0 2c-3.3 0-6 2.7-6 6h2a4 4 0 1 1 8 0h2c0-3.3-2.7-6-6-6Z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-2 gap-4 text-center">
                            <div>
                                <div class="text-2xl font-black text-slate-900">46,414</div>
                                <div class="text-xs text-slate-500">New this month</div>
                            </div>
                            <div>
                                <div class="text-2xl font-black text-slate-900">40.2%</div>
                                <div class="text-xs text-slate-500">Growth rate</div>
                            </div>
                        </div>
                    </article>

                    <article
                        class="dash-reveal dash-hover rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 lg:col-span-2"
                        style="--d: 6;">
                        <div class="flex items-center justify-between">
                            <h2 class="text-base font-bold text-slate-900">Attendance</h2>
                            <div class="flex items-center gap-3 text-xs">
                                <span class="inline-flex items-center gap-1 text-slate-500">
                                    <span class="h-2.5 w-2.5 rounded-full bg-yellow-300"></span> Total Present
                                </span>
                                <span class="inline-flex items-center gap-1 text-slate-500">
                                    <span class="h-2.5 w-2.5 rounded-full bg-sky-300"></span> Total Absent
                                </span>
                            </div>
                        </div>

                        <div class="mt-6 flex h-56 items-end justify-between gap-3">
                            <div class="flex w-full flex-col items-center gap-2">
                                <div class="flex h-44 items-end gap-2">
                                    <div class="dash-bar w-4 rounded-t-full bg-yellow-300" style="--h: 78%; --d: 7;"></div>
                                    <div class="dash-bar w-4 rounded-t-full bg-sky-300" style="--h: 62%; --d: 8;"></div>
                                </div>
                                <span class="text-xs text-slate-400">Mon</span>
                            </div>
                            <div class="flex w-full flex-col items-center gap-2">
                                <div class="flex h-44 items-end gap-2">
                                    <div class="dash-bar w-4 rounded-t-full bg-yellow-300" style="--h: 68%; --d: 9;"></div>
                                    <div class="dash-bar w-4 rounded-t-full bg-sky-300" style="--h: 52%; --d: 10;"></div>
                                </div>
                                <span class="text-xs text-slate-400">Tue</span>
                            </div>
                            <div class="flex w-full flex-col items-center gap-2">
                                <div class="flex h-44 items-end gap-2">
                                    <div class="dash-bar w-4 rounded-t-full bg-yellow-300" style="--h: 82%; --d: 11;"></div>
                                    <div class="dash-bar w-4 rounded-t-full bg-sky-300" style="--h: 70%; --d: 12;"></div>
                                </div>
                                <span class="text-xs text-slate-400">Wed</span>
                            </div>
                            <div class="flex w-full flex-col items-center gap-2">
                                <div class="flex h-44 items-end gap-2">
                                    <div class="dash-bar w-4 rounded-t-full bg-yellow-300" style="--h: 74%; --d: 13;"></div>
                                    <div class="dash-bar w-4 rounded-t-full bg-sky-300" style="--h: 56%; --d: 14;"></div>
                                </div>
                                <span class="text-xs text-slate-400">Thu</span>
                            </div>
                            <div class="flex w-full flex-col items-center gap-2">
                                <div class="flex h-44 items-end gap-2">
                                    <div class="dash-bar w-4 rounded-t-full bg-yellow-300" style="--h: 86%; --d: 15;"></div>
                                    <div class="dash-bar w-4 rounded-t-full bg-sky-300" style="--h: 72%; --d: 16;"></div>
                                </div>
                                <span class="text-xs text-slate-400">Fri</span>
                            </div>
                            <div class="flex w-full flex-col items-center gap-2">
                                <div class="flex h-44 items-end gap-2">
                                    <div class="dash-bar w-4 rounded-t-full bg-yellow-300" style="--h: 70%; --d: 17;"></div>
                                    <div class="dash-bar w-4 rounded-t-full bg-sky-300" style="--h: 58%; --d: 18;"></div>
                                </div>
                                <span class="text-xs text-slate-400">Sat</span>
                            </div>
                        </div>
                    </article>
                </div>

                <article class="dash-reveal dash-hover rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
                    style="--d: 7;">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-bold text-slate-900">Earnings</h2>
                        <div class="text-xs text-slate-500">Income and expenses</div>
                    </div>

                    <div class="mt-5 grid gap-4 md:grid-cols-3">
                        <div class="md:col-span-2">
                            <svg viewBox="0 0 600 220" class="h-44 w-full">
                                <path class="dash-line-path" style="--d: 8;"
                                    d="M0 170 C 70 120, 120 180, 200 130 S 340 80, 420 130 S 540 185, 600 95" fill="none"
                                    stroke="#7dd3fc" stroke-width="5" stroke-linecap="round" />
                                <path class="dash-line-path" style="--d: 10;"
                                    d="M0 150 C 70 180, 120 110, 200 140 S 340 200, 420 120 S 540 70, 600 130" fill="none"
                                    stroke="#c4b5fd" stroke-width="5" stroke-linecap="round" />
                            </svg>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-4">
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Net Balance</div>
                            <div class="mt-2 text-3xl font-black text-slate-900">24,680</div>
                            <div class="mt-1 text-sm text-emerald-600">+18% from last month</div>
                            <div class="mt-4 h-2 rounded-full bg-slate-200">
                                <div class="h-2 w-[78%] rounded-full bg-emerald-500"></div>
                            </div>
                        </div>
                    </div>
                </article>
            </div>

            <div class="space-y-6 xl:col-span-4">
                <article class="dash-reveal dash-hover rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
                    style="--d: 8;">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-bold text-slate-900">September 2030</h2>
                        <button class="rounded-lg p-1 text-slate-400 hover:bg-slate-100" aria-label="Open calendar">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M19 4h-1V2h-2v2H8V2H6v2H5a2 2 0 0 0-2 2v14c0 1.1.9 2 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 16H5V10h14v10Z" />
                            </svg>
                        </button>
                    </div>

                    <div class="mt-4 grid grid-cols-7 gap-2 text-center text-xs font-semibold text-slate-500">
                        <span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span><span>Sun</span>
                    </div>

                    <div class="mt-3 grid grid-cols-7 gap-2 text-center text-sm">
                        <span class="rounded-lg py-1 text-slate-400">19</span>
                        <span class="rounded-lg py-1 text-slate-400">20</span>
                        <span class="rounded-lg py-1 bg-sky-100 font-bold text-sky-600">21</span>
                        <span class="rounded-lg py-1 bg-violet-100 font-bold text-violet-600">22</span>
                        <span class="rounded-lg py-1 text-slate-500">23</span>
                        <span class="rounded-lg py-1 text-slate-500">24</span>
                        <span class="rounded-lg py-1 text-slate-500">25</span>
                    </div>

                    <h3 class="mt-6 text-sm font-bold text-slate-900">Agenda</h3>
                    <div class="mt-3 space-y-3">
                        <div class="dash-reveal rounded-2xl bg-indigo-50 p-3" style="--d: 9;">
                            <div class="text-xs font-semibold text-indigo-500">09:00 AM</div>
                            <div class="mt-1 text-sm font-semibold text-slate-800">Welcome & Announcement</div>
                        </div>
                        <div class="dash-reveal rounded-2xl bg-yellow-50 p-3" style="--d: 10;">
                            <div class="text-xs font-semibold text-yellow-600">02:00 PM</div>
                            <div class="mt-1 text-sm font-semibold text-slate-800">Math Review & Practice</div>
                        </div>
                        <div class="dash-reveal rounded-2xl bg-sky-50 p-3" style="--d: 11;">
                            <div class="text-xs font-semibold text-sky-600">03:30 PM</div>
                            <div class="mt-1 text-sm font-semibold text-slate-800">Science Experiment Discussion</div>
                        </div>
                    </div>
                </article>

                <article class="dash-reveal dash-hover rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
                    style="--d: 9;">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-bold text-slate-900">Messages</h2>
                        <a href="{{ route('admin.contacts.index') }}" class="text-xs font-semibold text-slate-400">View
                            all</a>
                    </div>

                    <div class="mt-4 space-y-4">
                        @forelse (($latestContactMessages ?? []) as $contactMessage)
                            @php
                                $name = trim((string) ($contactMessage->name ?? 'Unknown Sender'));
                                $parts = preg_split('/\s+/', $name, -1, PREG_SPLIT_NO_EMPTY);
                                $initials = '';
                                foreach (array_slice($parts ?: [], 0, 2) as $part) {
                                    $initials .= strtoupper(substr($part, 0, 1));
                                }
                                if ($initials === '') {
                                    $initials = 'UN';
                                }
                            @endphp

                            <a href="{{ route('admin.contacts.index') }}" class="dash-reveal block"
                                style="--d: {{ 10 + $loop->index }};">
                                <div class="flex items-start gap-3 rounded-2xl p-2 transition hover:bg-slate-50">
                                    <div
                                        class="grid h-10 w-10 place-items-center rounded-full font-bold {{ $contactMessage->is_read ? 'bg-slate-100 text-slate-500' : 'bg-violet-100 text-violet-600' }}">
                                        {{ $initials }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="truncate text-sm font-semibold text-slate-900">{{ $name }}</div>
                                            <div class="text-[11px] text-slate-400">
                                                {{ $contactMessage->created_at->diffForHumans() }}</div>
                                        </div>
                                        <div class="truncate text-xs font-semibold text-slate-600">
                                            {{ $contactMessage->subject ?: 'No subject' }}
                                        </div>
                                        <div class="truncate text-xs text-slate-500">
                                            {{ \Illuminate\Support\Str::limit($contactMessage->message, 60) }}
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="rounded-2xl bg-slate-50 px-3 py-4 text-center text-xs text-slate-500">
                                No contact messages yet.
                            </div>
                        @endforelse
                    </div>
                </article>
            </div>
        </section>
    </div>
@endsection