@extends('layout.teacher.navbar')

@section('page')
    @php
        $todayLabel = $dayLabels[$todayKey] ?? ucfirst($todayKey);
    @endphp

    <div class="dashboard-stage space-y-6">
        <section class="dash-reveal admin-page-header teacher-page-header" style="--d: 1;">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="admin-page-title text-3xl font-black tracking-tight">Teacher Dashboard</h1>
                    <p class="admin-page-subtitle mt-1 text-sm">
                        Dynamic schedule overview for <span id="dashboard-live-day">{{ $todayLabel }}</span>
                        (<span id="dashboard-live-date">{{ now()->format('M d, Y') }}</span>).
                    </p>
                </div>

                <div class="teacher-page-header__stats flex flex-wrap items-center gap-2 text-xs font-semibold">
                    <span class="admin-page-stat">Classes: {{ number_format($stats['classes']) }}</span>
                    <span class="admin-page-stat admin-page-stat--sky">Students:
                        {{ number_format($stats['students']) }}</span>
                    <span class="admin-page-stat admin-page-stat--indigo">Subjects:
                        {{ number_format($stats['subjects']) }}</span>
                    <span class="admin-page-stat admin-page-stat--emerald">Today Slots:
                        {{ number_format($stats['todaySchedules']) }}</span>
                </div>
            </div>

            <div class="mt-5">
                <a id="dashboard-open-today-link" href="{{ route('teacher.schedule.index', ['day' => $todayKey]) }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100">
                    Open Today Schedule
                </a>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-12">
            <section class="dash-reveal dash-hover rounded-3xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-7"
                style="--d: 2;">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-black text-slate-900">Today's Schedule</h2>
                    <div class="flex items-center gap-2">
                        <span id="dashboard-live-time"
                            class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                            --:--:--
                        </span>
                        <span
                            class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700">
                            {{ $todayLabel }}
                        </span>
                    </div>
                </div>
                <p id="dashboard-live-hint" class="mb-4 text-xs font-semibold text-slate-500">
                    Checking live schedule...
                </p>

                <div class="space-y-3">
                    @forelse ($todayTimeline as $item)
                        <div class="js-dash-slot rounded-2xl border border-slate-200 bg-slate-50/70 px-4 py-3"
                            data-start="{{ $item['start_24'] ?? '' }}" data-end="{{ $item['end_24'] ?? '' }}">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <div>
                                    <div class="text-sm font-bold text-slate-900">{{ $item['title'] }}</div>
                                    <div class="text-xs text-slate-500">{{ $item['subtitle'] }}</div>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span
                                        class="rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $item['type'] === 'subject' ? 'border border-indigo-200 bg-indigo-50 text-indigo-700' : 'border border-sky-200 bg-sky-50 text-sky-700' }}">
                                        {{ ucfirst($item['type']) }}
                                    </span>
                                    <span
                                        class="js-dash-slot-status rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-[11px] font-semibold text-slate-600">
                                        Scheduled
                                    </span>
                                </div>
                            </div>
                            <div class="mt-2 flex flex-wrap items-center gap-2 text-xs font-semibold text-slate-700">
                                <span class="rounded-lg border border-slate-200 bg-white px-2 py-1">{{ $item['period'] }}</span>
                                <span class="rounded-lg border border-slate-200 bg-white px-2 py-1">{{ $item['start'] }} ->
                                    {{ $item['end'] }}</span>
                            </div>
                        </div>
                    @empty
                        <div
                            class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                            No schedule configured for today.
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="dash-reveal dash-hover rounded-3xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-5"
                style="--d: 3;">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-black text-slate-900">Weekly Schedule</h2>
                    <a href="{{ route('teacher.schedule.index') }}"
                        class="text-xs font-semibold text-indigo-600 hover:text-indigo-500">
                        Manage
                    </a>
                </div>

                <div class="space-y-3">
                    @foreach ($weeklySummary as $dayItem)
                        @php
                            $barWidth = (int) round(($dayItem['total'] / $maxWeeklyTotal) * 100);
                        @endphp
                        <div>
                            <div class="mb-1 flex items-center justify-between text-xs font-semibold">
                                <span class="text-slate-700">{{ $dayItem['label'] }}</span>
                                <span class="text-slate-500">{{ $dayItem['total'] }} slots</span>
                            </div>
                            <div class="h-2 rounded-full bg-slate-100">
                                <div class="h-2 rounded-full bg-indigo-500" style="width: {{ $barWidth }}%"></div>
                            </div>
                            <div class="mt-1 text-[11px] text-slate-500">
                                Class: {{ $dayItem['class_count'] }} | Subject: {{ $dayItem['subject_count'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const timeEl = document.getElementById('dashboard-live-time');
            const hintEl = document.getElementById('dashboard-live-hint');
            const dayEl = document.getElementById('dashboard-live-day');
            const dateEl = document.getElementById('dashboard-live-date');
            const openTodayLink = document.getElementById('dashboard-open-today-link');
            const slots = Array.from(document.querySelectorAll('.js-dash-slot'));
            const dayLabels = @json($dayLabels ?? []);
            const dayKeys = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

            const toMinutes = (value) => {
                const parts = String(value || '').split(':');
                if (parts.length < 2) return null;
                const hour = Number(parts[0]);
                const minute = Number(parts[1]);
                if (Number.isNaN(hour) || Number.isNaN(minute)) return null;
                return (hour * 60) + minute;
            };

            const setStatusClass = (el, mode) => {
                if (!el) return;
                el.className =
                    'js-dash-slot-status rounded-full border px-2.5 py-1 text-[11px] font-semibold';

                if (mode === 'live') {
                    el.classList.add('border-emerald-200', 'bg-emerald-50', 'text-emerald-700');
                    return;
                }

                if (mode === 'upcoming') {
                    el.classList.add('border-amber-200', 'bg-amber-50', 'text-amber-700');
                    return;
                }

                el.classList.add('border-slate-200', 'bg-slate-50', 'text-slate-600');
            };

            const updateRealtimeSchedule = () => {
                const now = new Date();
                const nowMinutes = (now.getHours() * 60) + now.getMinutes();

                let liveCount = 0;
                let nearestUpcoming = null;

                slots.forEach((slot) => {
                    const start = toMinutes(slot.dataset.start);
                    const end = toMinutes(slot.dataset.end);
                    const statusEl = slot.querySelector('.js-dash-slot-status');
                    slot.classList.remove('ring-1', 'ring-emerald-200');

                    if (start === null || end === null || !statusEl) {
                        return;
                    }

                    if (nowMinutes >= start && nowMinutes < end) {
                        setStatusClass(statusEl, 'live');
                        statusEl.textContent = 'Live Now';
                        slot.classList.add('ring-1', 'ring-emerald-200');
                        liveCount += 1;
                        return;
                    }

                    if (nowMinutes < start) {
                        const delta = start - nowMinutes;
                        setStatusClass(statusEl, 'upcoming');
                        statusEl.textContent = `Starts in ${delta}m`;
                        nearestUpcoming = nearestUpcoming === null ? delta : Math.min(nearestUpcoming, delta);
                        return;
                    }

                    setStatusClass(statusEl, 'default');
                    statusEl.textContent = 'Finished';
                });

                if (!hintEl) return;

                if (liveCount > 0) {
                    hintEl.textContent = `${liveCount} slot(s) live now`;
                    return;
                }

                if (nearestUpcoming !== null) {
                    hintEl.textContent = `Next slot starts in ${nearestUpcoming} minute(s)`;
                    return;
                }

                hintEl.textContent = 'No more slots for today';
            };

            const updateLiveClock = () => {
                if (!timeEl) return;
                const now = new Date();
                timeEl.textContent = now.toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                });
            };

            const updateLiveDateMeta = () => {
                const now = new Date();
                const dayKey = dayKeys[now.getDay()] || '';

                if (dayEl) {
                    dayEl.textContent = dayLabels[dayKey] || (dayKey ? (dayKey.charAt(0).toUpperCase() + dayKey.slice(1)) : '');
                }

                if (dateEl) {
                    dateEl.textContent = now.toLocaleDateString([], {
                        month: 'short',
                        day: '2-digit',
                        year: 'numeric',
                    });
                }

                if (openTodayLink && dayKey) {
                    const url = new URL(openTodayLink.href, window.location.origin);
                    url.searchParams.set('day', dayKey);
                    openTodayLink.href = url.toString();
                }
            };

            updateLiveDateMeta();
            updateLiveClock();
            updateRealtimeSchedule();
            setInterval(updateLiveDateMeta, 30000);
            setInterval(updateLiveClock, 1000);
            setInterval(updateRealtimeSchedule, 30000);
        });
    </script>
@endsection
