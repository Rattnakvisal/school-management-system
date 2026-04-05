document.addEventListener("DOMContentLoaded", () => {
    const liveTimeEl = document.getElementById("schedule-live-time");
    const liveDayEl = document.getElementById("schedule-live-day");
    const liveHintEl = document.getElementById("schedule-live-hint");
    const liveCurrentTitleEl = document.getElementById("schedule-live-current-title");
    const liveCurrentMetaEl = document.getElementById("schedule-live-current-meta");
    const liveNextTitleEl = document.getElementById("schedule-live-next-title");
    const liveNextMetaEl = document.getElementById("schedule-live-next-meta");
    const liveProgressEl = document.getElementById("schedule-live-progress");
    const selectedDayPillEl = document.getElementById("schedule-selected-day-pill");
    const dataNode = document.getElementById("teacher-schedule-data");

    if (!dataNode) {
        return;
    }

    let pageData = {};
    try {
        pageData = JSON.parse(dataNode.textContent || "{}");
    } catch (error) {
        console.error("Unable to parse teacher schedule data.", error);
        pageData = {};
    }

    const dayLabels = pageData.dayLabels || {};
    const selectedDay = String(pageData.selectedDay || "all").toLowerCase();
    const weekDays = ["sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday"];
    const validDayKeys = new Set(weekDays);

    const toSeconds = (value) => {
        const parts = String(value || "").split(":");
        if (parts.length < 2) {
            return null;
        }

        const hour = Number(parts[0]);
        const minute = Number(parts[1]);
        const second = Number(parts[2] || "0");

        if (Number.isNaN(hour) || Number.isNaN(minute) || Number.isNaN(second)) {
            return null;
        }

        return (hour * 3600) + (minute * 60) + second;
    };

    const formatDuration = (totalSeconds) => {
        const safeSeconds = Math.max(0, Math.floor(totalSeconds));
        const hours = Math.floor(safeSeconds / 3600);
        const minutes = Math.floor((safeSeconds % 3600) / 60);
        const seconds = safeSeconds % 60;

        if (hours > 0) {
            return `${hours}h ${minutes}m`;
        }

        if (minutes > 0) {
            return `${minutes}m ${seconds}s`;
        }

        return `${seconds}s`;
    };

    const formatClock = (totalSeconds) => {
        const date = new Date();
        date.setHours(0, 0, 0, 0);
        date.setSeconds(totalSeconds);

        return date.toLocaleTimeString([], {
            hour: "2-digit",
            minute: "2-digit",
            hour12: true,
        });
    };

    const setStatusStyle = (statusEl, variant) => {
        statusEl.className =
            "js-live-status inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold";

        if (variant === "active") {
            statusEl.classList.add("border-emerald-200", "bg-emerald-50", "text-emerald-700");
            return;
        }

        if (variant === "upcoming") {
            statusEl.classList.add("border-amber-200", "bg-amber-50", "text-amber-700");
            return;
        }

        if (variant === "selected") {
            statusEl.classList.add("border-indigo-200", "bg-indigo-50", "text-indigo-700");
            return;
        }

        statusEl.classList.add("border-slate-200", "bg-slate-50", "text-slate-600");
    };

    const updateLiveSchedule = () => {
        const now = new Date();
        const dayKey = weekDays[now.getDay()];
        const nowSeconds = (now.getHours() * 3600) + (now.getMinutes() * 60) + now.getSeconds();
        const dayLabel = dayLabels[dayKey] || dayKey;
        const selectedDayIsSpecific = validDayKeys.has(selectedDay);
        const selectedDayLabel = dayLabels[selectedDay] || selectedDay;

        if (liveTimeEl) {
            liveTimeEl.textContent = now.toLocaleTimeString([], {
                hour: "2-digit",
                minute: "2-digit",
                second: "2-digit",
            });
        }

        if (liveDayEl) {
            liveDayEl.textContent = `${dayLabel} | ${now.toLocaleDateString([], {
                month: "short",
                day: "2-digit",
                year: "numeric",
            })}`;
        }

        if (selectedDayPillEl) {
            if (selectedDayIsSpecific) {
                selectedDayPillEl.textContent = `Day: ${selectedDayLabel}`;
            } else {
                selectedDayPillEl.textContent = `Day: All Days (Now ${dayLabel})`;
            }
        }

        let activeSlots = [];
        let upcomingSlots = [];
        let finishedCount = 0;
        let todayRelevantCount = 0;

        document.querySelectorAll(".js-schedule-row").forEach((row) => {
            const statusEl = row.querySelector(".js-live-status");
            if (!statusEl) {
                return;
            }

            const slotDay = String(row.dataset.day || "all").toLowerCase();
            const label = String(row.dataset.label || "Schedule");
            const type = String(row.dataset.type || "slot");
            const startSecondsRaw = toSeconds(row.dataset.start);
            const endSecondsRaw = toSeconds(row.dataset.end);
            const dayMatchToday = slotDay === "all" || slotDay === dayKey;
            const dayMatchSelected = selectedDayIsSpecific ?
                (slotDay === "all" || slotDay === selectedDay) :
                dayMatchToday;

            row.classList.remove("ring-1", "ring-emerald-200", "ring-sky-200");

            if (!dayMatchSelected || startSecondsRaw === null || endSecondsRaw === null) {
                setStatusStyle(statusEl, "default");
                statusEl.textContent = "Scheduled";
                return;
            }

            let startSeconds = startSecondsRaw;
            let endSeconds = endSecondsRaw;
            if (endSeconds <= startSeconds) {
                endSeconds += 24 * 3600;
            }

            if (!dayMatchToday) {
                setStatusStyle(statusEl, "selected");
                statusEl.textContent = "Selected Day";
                return;
            }

            todayRelevantCount += 1;

            if (nowSeconds >= startSeconds && nowSeconds < endSeconds) {
                const remainingSeconds = endSeconds - nowSeconds;
                setStatusStyle(statusEl, "active");
                statusEl.textContent = `Live ${formatDuration(remainingSeconds)} left`;
                row.classList.add("ring-1", "ring-emerald-200");
                activeSlots.push({
                    row,
                    label,
                    type,
                    startSeconds,
                    endSeconds,
                    remainingSeconds,
                });
                return;
            }

            if (nowSeconds < startSeconds) {
                const secondsToStart = startSeconds - nowSeconds;
                setStatusStyle(statusEl, "upcoming");
                statusEl.textContent = `Starts in ${formatDuration(secondsToStart)}`;
                upcomingSlots.push({
                    row,
                    label,
                    type,
                    startSeconds,
                    endSeconds,
                    secondsToStart,
                });
                return;
            }

            setStatusStyle(statusEl, "default");
            statusEl.textContent = "Finished";
            finishedCount += 1;
        });

        activeSlots = activeSlots.sort((a, b) => a.remainingSeconds - b.remainingSeconds);
        upcomingSlots = upcomingSlots.sort((a, b) => a.secondsToStart - b.secondsToStart);

        if (activeSlots.length > 0) {
            const current = activeSlots[0];
            const span = Math.max(1, current.endSeconds - current.startSeconds);
            const elapsed = Math.max(0, nowSeconds - current.startSeconds);
            const progress = Math.max(0, Math.min(100, Math.round((elapsed / span) * 100)));

            if (liveCurrentTitleEl) {
                liveCurrentTitleEl.textContent =
                    `${current.type === "subject" ? "Subject" : "Class"}: ${current.label}`;
            }

            if (liveCurrentMetaEl) {
                liveCurrentMetaEl.textContent =
                    `${formatClock(current.startSeconds)} - ${formatClock(current.endSeconds)} | ${formatDuration(current.remainingSeconds)} left`;
            }

            if (liveProgressEl) {
                liveProgressEl.style.width = `${progress}%`;
            }
        } else {
            if (liveCurrentTitleEl) {
                liveCurrentTitleEl.textContent = "No live slot now";
            }

            if (liveCurrentMetaEl) {
                liveCurrentMetaEl.textContent = "Waiting for next slot...";
            }

            if (liveProgressEl) {
                liveProgressEl.style.width = "0%";
            }
        }

        if (upcomingSlots.length > 0) {
            const next = upcomingSlots[0];
            next.row.classList.add("ring-1", "ring-sky-200");

            if (liveNextTitleEl) {
                liveNextTitleEl.textContent =
                    `${next.type === "subject" ? "Subject" : "Class"}: ${next.label}`;
            }

            if (liveNextMetaEl) {
                liveNextMetaEl.textContent =
                    `Starts in ${formatDuration(next.secondsToStart)} | ${formatClock(next.startSeconds)} - ${formatClock(next.endSeconds)}`;
            }
        } else {
            if (liveNextTitleEl) {
                liveNextTitleEl.textContent = "No upcoming slot";
            }

            if (liveNextMetaEl) {
                liveNextMetaEl.textContent = "All slots finished for selected day";
            }
        }

        if (liveHintEl) {
            if (selectedDayIsSpecific && selectedDay !== dayKey) {
                liveHintEl.textContent =
                    `Viewing ${selectedDayLabel} schedule. Today is ${dayLabel}. Real-time status is for today only.`;
                return;
            }

            if (activeSlots.length > 0) {
                liveHintEl.textContent = `${activeSlots.length} slot(s) live now`;
                return;
            }

            if (upcomingSlots.length > 0) {
                liveHintEl.textContent = `${upcomingSlots.length} upcoming slot(s) today`;
                return;
            }

            if (todayRelevantCount > 0 && finishedCount >= todayRelevantCount) {
                liveHintEl.textContent = `All ${todayRelevantCount} slot(s) finished for ${dayLabel}`;
                return;
            }

            liveHintEl.textContent = `No schedule matched for ${dayLabel}`;
        }
    };

    updateLiveSchedule();
    window.setInterval(updateLiveSchedule, 1000);
});
