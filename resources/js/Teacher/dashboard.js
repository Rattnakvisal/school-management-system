document.addEventListener('DOMContentLoaded', () => {
    const dataNode = document.getElementById('teacher-dashboard-data');
    const workloadCanvas = document.getElementById('teacherWorkloadChart');
    const calendarRoot = document.getElementById('teacher-dashboard-calendar');
    const calendarLabel = document.getElementById('teacher-dashboard-calendar-label');
    const slots = Array.from(document.querySelectorAll('.js-dash-slot'));

    let dashboardData = {};
    try {
        dashboardData = JSON.parse(dataNode?.textContent || '{}');
    } catch (error) {
        console.error('Unable to parse teacher dashboard data.', error);
        dashboardData = {};
    }

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

        element.className = 'js-dash-slot-status teacher-lesson__status';

        if (mode === 'live') {
            element.classList.add('teacher-lesson__status--live');
            return;
        }

        if (mode === 'upcoming') {
            element.classList.add('teacher-lesson__status--upcoming');
            return;
        }

        if (mode === 'done') {
            element.classList.add('teacher-lesson__status--done');
            return;
        }

        element.classList.add('teacher-lesson__status--default');
    };

    const updateRealtimeSchedule = () => {
        const now = new Date();
        const nowMinutes = (now.getHours() * 60) + now.getMinutes();

        slots.forEach((slot) => {
            const start = toMinutes(slot.dataset.start);
            const end = toMinutes(slot.dataset.end);
            const statusEl = slot.querySelector('.js-dash-slot-status');

            slot.classList.remove('teacher-lesson--live');

            if (start === null || end === null || !statusEl) {
                return;
            }

            if (nowMinutes >= start && nowMinutes < end) {
                setStatusClass(statusEl, 'live');
                statusEl.textContent = 'Live now';
                slot.classList.add('teacher-lesson--live');
                return;
            }

            if (nowMinutes < start) {
                setStatusClass(statusEl, 'upcoming');
                statusEl.textContent = `Starts in ${start - nowMinutes}m`;
                return;
            }

            setStatusClass(statusEl, 'done');
            statusEl.textContent = 'Finished';
        });
    };

    const renderCalendar = () => {
        if (!calendarRoot) {
            return;
        }

        const today = dashboardData.calendar || {};
        const year = Number(today.year) || new Date().getFullYear();
        const monthIndex = Math.max(0, (Number(today.month) || (new Date().getMonth() + 1)) - 1);
        const activeDay = Number(today.day) || new Date().getDate();
        const monthDate = new Date(year, monthIndex, 1);
        const monthLabel = monthDate.toLocaleDateString([], {
            month: 'long',
            year: 'numeric',
        });
        const daysInMonth = new Date(year, monthIndex + 1, 0).getDate();
        const startOffset = (monthDate.getDay() + 6) % 7;
        const cells = [];

        if (calendarLabel) {
            calendarLabel.textContent = monthLabel;
        }

        for (let index = 0; index < startOffset; index += 1) {
            cells.push('<span class="teacher-calendar__day teacher-calendar__day--muted"></span>');
        }

        for (let day = 1; day <= daysInMonth; day += 1) {
            const activeClass = day === activeDay ? ' teacher-calendar__day--active' : '';
            cells.push(`<span class="teacher-calendar__day${activeClass}">${day}</span>`);
        }

        calendarRoot.innerHTML = cells.join('');
    };

    if (workloadCanvas && typeof window.Chart !== 'undefined') {
        const progressValue = Math.max(0, Math.min(100, Number(dashboardData.workloadPercent) || 0));
        const centerText = {
            id: 'teacherWorkloadCenterText',
            afterDraw(chart) {
                const {ctx} = chart;
                const point = chart.getDatasetMeta(0)?.data?.[0];

                if (!point) {
                    return;
                }

                ctx.save();
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillStyle = '#111827';
                ctx.font = '700 34px sans-serif';
                ctx.fillText(`${progressValue}%`, point.x, point.y - 6);
                ctx.fillStyle = '#94a3b8';
                ctx.font = '600 11px sans-serif';
                ctx.fillText('booked today', point.x, point.y + 22);
                ctx.restore();
            },
        };

        new Chart(workloadCanvas, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [progressValue, Math.max(0, 100 - progressValue)],
                    borderWidth: 0,
                    backgroundColor: ['#4f46e5', '#fbbf24'],
                    hoverOffset: 0,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '78%',
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        enabled: false,
                    },
                },
            },
            plugins: [centerText],
        });
    }

    renderCalendar();
    updateRealtimeSchedule();
    window.setInterval(updateRealtimeSchedule, 30000);
});
