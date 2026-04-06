document.addEventListener('DOMContentLoaded', () => {
    const timeEl = document.getElementById('dashboard-live-time');
    const hintEl = document.getElementById('dashboard-live-hint');
    const dayEl = document.getElementById('dashboard-live-day');
    const dateEl = document.getElementById('dashboard-live-date');
    const openTodayLink = document.getElementById('dashboard-open-today-link');
    const dataNode = document.getElementById('teacher-dashboard-data');
    const slots = Array.from(document.querySelectorAll('.js-dash-slot'));

    if (!dataNode || typeof window.Chart === 'undefined') {
        return;
    }

    let dashboardData = {};
    try {
        dashboardData = JSON.parse(dataNode.textContent || '{}');
    } catch (error) {
        console.error('Unable to parse teacher dashboard data.', error);
        dashboardData = {};
    }

    const dayLabels = dashboardData.dayLabels || {};
    const chartData = dashboardData.chartData || {
        weekly: {
            labels: [],
            classes: [],
            subjects: [],
        },
        todayMix: {
            labels: [],
            values: [],
        },
        todayPeriods: {
            labels: [],
            values: [],
        },
    };
    const weeklyData = chartData.weekly || {
        labels: [],
        classes: [],
        subjects: [],
    };
    const todayMixData = chartData.todayMix || {
        labels: [],
        values: [],
    };
    const todayPeriodsData = chartData.todayPeriods || {
        labels: [],
        values: [],
    };
    const todayKey = String(dashboardData.todayKey || '').trim();
    const dayKeys = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

    Chart.defaults.responsive = true;
    Chart.defaults.maintainAspectRatio = false;

    const toMinutes = (value) => {
        const parts = String(value || '').split(':');
        if (parts.length < 2) {
            return null;
        }

        const hour = Number(parts[0]);
        const minute = Number(parts[1]);

        if (Number.isNaN(hour) || Number.isNaN(minute)) {
            return null;
        }

        return (hour * 60) + minute;
    };

    const setStatusClass = (element, mode) => {
        if (!element) {
            return;
        }

        element.className = 'js-dash-slot-status rounded-full border px-2.5 py-1 text-[11px] font-semibold';

        if (mode === 'live') {
            element.classList.add('border-emerald-200', 'bg-emerald-50', 'text-emerald-700');
            return;
        }

        if (mode === 'upcoming') {
            element.classList.add('border-amber-200', 'bg-amber-50', 'text-amber-700');
            return;
        }

        element.classList.add('border-slate-200', 'bg-slate-50', 'text-slate-600');
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

        if (!hintEl) {
            return;
        }

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
        if (!timeEl) {
            return;
        }

        const now = new Date();
        timeEl.textContent = now.toLocaleTimeString([], {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
        });
    };

    const updateLiveDateMeta = () => {
        const now = new Date();
        const fallbackDayKey = dayKeys[now.getDay()] || '';
        const activeDayKey = todayKey || fallbackDayKey;

        if (dayEl) {
            dayEl.textContent = dayLabels[activeDayKey] || (activeDayKey ? activeDayKey.charAt(0).toUpperCase() + activeDayKey.slice(1) : '');
        }

        if (dateEl) {
            dateEl.textContent = now.toLocaleDateString([], {
                month: 'short',
                day: '2-digit',
                year: 'numeric',
            });
        }

        if (openTodayLink && activeDayKey) {
            const url = new URL(openTodayLink.href, window.location.origin);
            url.searchParams.set('day', activeDayKey);
            openTodayLink.href = url.toString();
        }
    };

    const weeklyCanvas = document.getElementById('teacherWeeklyChart');
    if (weeklyCanvas) {
        new Chart(weeklyCanvas, {
            type: 'bar',
            data: {
                labels: weeklyData.labels || [],
                datasets: [{
                        label: 'Class Slots',
                        data: weeklyData.classes || [],
                        borderRadius: 8,
                        backgroundColor: 'rgba(14, 165, 233, 0.75)',
                    },
                    {
                        label: 'Subject Slots',
                        data: weeklyData.subjects || [],
                        borderRadius: 8,
                        backgroundColor: 'rgba(99, 102, 241, 0.75)',
                    },
                ],
            },
            options: {
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 8,
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            stepSize: 1,
                        },
                        grid: {
                            color: 'rgba(148, 163, 184, 0.2)',
                        },
                    },
                    x: {
                        grid: {
                            display: false,
                        },
                    },
                },
            },
        });
    }

    const mixCanvas = document.getElementById('teacherTodayMixChart');
    if (mixCanvas) {
        new Chart(mixCanvas, {
            type: 'doughnut',
            data: {
                labels: todayMixData.labels || [],
                datasets: [{
                    data: todayMixData.values || [],
                    borderWidth: 0,
                    backgroundColor: ['rgba(56, 189, 248, 0.9)', 'rgba(99, 102, 241, 0.9)'],
                    hoverOffset: 6,
                }],
            },
            options: {
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 8,
                        },
                    },
                },
            },
        });
    }

    const periodCanvas = document.getElementById('teacherPeriodChart');
    if (periodCanvas) {
        new Chart(periodCanvas, {
            type: 'bar',
                data: {
                    labels: todayPeriodsData.labels || [],
                    datasets: [{
                        label: 'Slots',
                        data: todayPeriodsData.values || [],
                        borderRadius: 10,
                        backgroundColor: [
                            'rgba(16, 185, 129, 0.82)',
                            'rgba(59, 130, 246, 0.82)',
                            'rgba(99, 102, 241, 0.82)',
                            'rgba(245, 158, 11, 0.82)',
                            'rgba(148, 163, 184, 0.82)',
                        ],
                    }],
                },
            options: {
                plugins: {
                    legend: {
                        display: false,
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            stepSize: 1,
                        },
                        grid: {
                            color: 'rgba(148, 163, 184, 0.18)',
                        },
                    },
                    x: {
                        grid: {
                            display: false,
                        },
                    },
                },
            },
        });
    }

    updateLiveDateMeta();
    updateLiveClock();
    updateRealtimeSchedule();
    window.setInterval(updateLiveDateMeta, 30000);
    window.setInterval(updateLiveClock, 1000);
    window.setInterval(updateRealtimeSchedule, 30000);
});
