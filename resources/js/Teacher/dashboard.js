document.addEventListener('DOMContentLoaded', () => {
    const dataNode = document.getElementById('teacher-dashboard-data');
    const workloadCanvas = document.getElementById('teacherWorkloadChart');
    const calendarRoot = document.getElementById('teacher-dashboard-calendar');
    const calendarLabel = document.getElementById('teacher-dashboard-calendar-label');
    const slots = Array.from(document.querySelectorAll('.js-dash-slot'));
    const numberNodes = Array.from(document.querySelectorAll('.teacher-animate-number'));
    const progressFills = Array.from(document.querySelectorAll('.teacher-progress-fill'));
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    let dashboardData = {};
    try {
        dashboardData = JSON.parse(dataNode?.textContent || '{}');
    } catch (error) {
        console.error('Unable to parse teacher dashboard data.', error);
        dashboardData = {};
    }

    const easeOutCubic = (value) => 1 - Math.pow(1 - value, 3);

    const animateNumber = (element) => {
        if (!element || !element.dataset.value) {
            return;
        }

        const end = Number.parseFloat(element.dataset.value || '0');
        const decimals = Number.parseInt(element.dataset.decimals || (Number.isInteger(end) ? '0' : '1'), 10);
        const suffix = element.dataset.suffix || '';

        if (Number.isNaN(end)) {
            return;
        }

        if (prefersReducedMotion) {
            const staticValue = decimals > 0 ? end.toFixed(decimals) : Math.round(end).toString();
            element.textContent = `${staticValue}${suffix}`;
            return;
        }

        const startedAt = performance.now();
        const duration = 1100;

        const step = (now) => {
            const progress = Math.min((now - startedAt) / duration, 1);
            const eased = easeOutCubic(progress);
            const current = end * eased;
            const output = decimals > 0 ? current.toFixed(decimals) : Math.round(current).toString();

            element.textContent = `${output}${suffix}`;

            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };

        window.requestAnimationFrame(step);
    };

    const animateProgressFill = (fill, index) => {
        if (!fill) {
            return;
        }

        const finalWidth = Math.max(0, Math.min(100, Number.parseFloat(fill.dataset.width || '0')));

        if (prefersReducedMotion) {
            fill.style.width = `${finalWidth}%`;
            return;
        }

        fill.style.width = '0%';
        fill.style.transition = 'width 900ms cubic-bezier(0.22, 1, 0.36, 1)';
        fill.style.transitionDelay = `${index * 110}ms`;

        window.requestAnimationFrame(() => {
            window.setTimeout(() => {
                fill.style.width = `${finalWidth}%`;
            }, 180);
        });
    };

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

        if (!prefersReducedMotion) {
            Array.from(calendarRoot.children).forEach((cell, index) => {
                cell.style.opacity = '0';
                cell.style.transform = 'translateY(8px) scale(0.96)';
                cell.style.transition = 'opacity 320ms ease, transform 320ms ease';
                cell.style.transitionDelay = `${index * 18}ms`;

                window.requestAnimationFrame(() => {
                    cell.style.opacity = '1';
                    cell.style.transform = 'translateY(0) scale(1)';
                });
            });
        }
    };

    if (workloadCanvas && typeof window.Chart !== 'undefined') {
        const progressValue = Math.max(0, Math.min(100, Number(dashboardData.workloadPercent) || 0));
        let animatedProgressValue = prefersReducedMotion ? progressValue : 0;
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
                ctx.font = '700 34px "Sora", sans-serif';
                ctx.fillText(`${Math.round(animatedProgressValue)}%`, point.x, point.y - 6);
                ctx.fillStyle = '#94a3b8';
                ctx.font = '600 11px "Plus Jakarta Sans", sans-serif';
                ctx.fillText('booked today', point.x, point.y + 22);
                ctx.restore();
            },
        };

        const chart = new Chart(workloadCanvas, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [animatedProgressValue, Math.max(0, 100 - animatedProgressValue)],
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

        if (!prefersReducedMotion) {
            const startedAt = performance.now();
            const duration = 1150;

            const step = (now) => {
                const progress = Math.min((now - startedAt) / duration, 1);
                const eased = easeOutCubic(progress);

                animatedProgressValue = progressValue * eased;
                chart.data.datasets[0].data = [
                    animatedProgressValue,
                    Math.max(0, 100 - animatedProgressValue),
                ];
                chart.update('none');

                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };

            window.requestAnimationFrame(step);
        }
    }

    renderCalendar();
    updateRealtimeSchedule();
    numberNodes.forEach((node) => animateNumber(node));
    progressFills.forEach((fill, index) => animateProgressFill(fill, index));
    window.setInterval(updateRealtimeSchedule, 30000);
});
