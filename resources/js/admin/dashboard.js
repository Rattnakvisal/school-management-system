document.addEventListener("DOMContentLoaded", () => {
    const clock = document.getElementById("dash_agenda_clock");

    if (clock) {
        const tick = () => {
            const now = new Date();
            const hh = String(((now.getHours() + 11) % 12) + 1).padStart(
                2,
                "0",
            );
            const mm = String(now.getMinutes()).padStart(2, "0");
            const ss = String(now.getSeconds()).padStart(2, "0");
            const ampm = now.getHours() >= 12 ? "PM" : "AM";
            clock.textContent = `${hh}:${mm}:${ss} ${ampm}`;
        };

        tick();
        window.setInterval(tick, 1000);
    }

    const chartDataNode = document.getElementById("admin-dashboard-data");
    if (!chartDataNode || typeof window.Chart === "undefined") {
        return;
    }

    let charts = {};

    try {
        charts = JSON.parse(chartDataNode.textContent || "{}");
    } catch (error) {
        console.error("Unable to parse admin dashboard data.", error);
        return;
    }

    const trendData = charts.trend ?? {
        labels: [],
        students: [],
        teachers: [],
    };
    const studentProfileData = charts.studentProfile ?? {
        labels: [],
        values: [],
    };
    const compositionData = charts.composition ?? { labels: [], values: [] };
    const classLoadData = charts.classLoad ?? { labels: [], values: [] };
    const subjectHealthData = charts.subjectHealth ?? {
        labels: [],
        values: [],
    };
    const periodData = charts.periods ?? { labels: [], values: [] };
    const attendanceData = charts.attendance ?? {
        labels: [],
        present: [],
        absent: [],
    };

    Chart.defaults.responsive = true;
    Chart.defaults.maintainAspectRatio = false;

    const reduceMotion = window.matchMedia(
        "(prefers-reduced-motion: reduce)",
    ).matches;
    const compact = window.matchMedia("(max-width: 640px)").matches;
    const padding = compact
        ? { left: 4, right: 4, top: 2, bottom: 0 }
        : { left: 0, right: 0, top: 0, bottom: 0 };

    const legend = (desktop = "top", mobile = "bottom") => ({
        position: compact ? mobile : desktop,
        labels: {
            boxWidth: compact ? 10 : 12,
            usePointStyle: true,
            padding: compact ? 10 : 14,
            font: { size: compact ? 11 : 12 },
        },
    });

    const delay = (ctx, base = 0, step = 75) => {
        if (ctx.type !== "data" || ctx.mode !== "default") {
            return 0;
        }

        return base + ctx.dataIndex * step + ctx.datasetIndex * 90;
    };

    const animation = (base = 0, step = 75, duration = 1050) => {
        if (reduceMotion) {
            return false;
        }

        return {
            duration,
            easing: "easeOutCubic",
            delay: (ctx) => delay(ctx, base, step),
        };
    };

    const axisAnimations = (base = 0) => {
        if (reduceMotion) {
            return {};
        }

        return {
            x: {
                duration: 760,
                easing: "easeOutQuart",
                delay: (ctx) => delay(ctx, base, 55),
            },
            y: {
                from: 0,
                duration: 920,
                easing: "easeOutQuart",
                delay: (ctx) => delay(ctx, base, 70),
            },
        };
    };

    const yScale = {
        beginAtZero: true,
        ticks: { precision: 0 },
        grid: { color: "rgba(148,163,184,0.2)" },
    };

    const xGridless = { grid: { display: false } };

    const gradient = (ctx, start, end) => {
        const fill = ctx.createLinearGradient(0, 0, 0, 320);
        fill.addColorStop(0, start);
        fill.addColorStop(1, end);
        return fill;
    };

    const trendCanvas = document.getElementById("enrollmentTrendChart");
    if (trendCanvas) {
        const ctx = trendCanvas.getContext("2d");
        new Chart(ctx, {
            type: "line",
            data: {
                labels: trendData.labels,
                datasets: [
                    {
                        label: "Students",
                        data: trendData.students,
                        borderWidth: 3,
                        borderColor: "#4f46e5",
                        backgroundColor: gradient(
                            ctx,
                            "rgba(79,70,229,0.35)",
                            "rgba(79,70,229,0.02)",
                        ),
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3,
                        pointBackgroundColor: "#4f46e5",
                    },
                    {
                        label: "Teachers",
                        data: trendData.teachers,
                        borderWidth: 3,
                        borderColor: "#0ea5e9",
                        backgroundColor: gradient(
                            ctx,
                            "rgba(14,165,233,0.28)",
                            "rgba(14,165,233,0.02)",
                        ),
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3,
                        pointBackgroundColor: "#0ea5e9",
                    },
                ],
            },
            options: {
                animation: animation(120, 60, 1100),
                animations: axisAnimations(120),
                interaction: { mode: "index", intersect: false },
                layout: { padding },
                plugins: { legend: legend("top", "bottom") },
                scales: { y: yScale, x: xGridless },
            },
        });
    }

    const studentsCanvas = document.getElementById("studentsSnapshotChart");
    if (studentsCanvas) {
        new Chart(studentsCanvas, {
            type: "doughnut",
            data: {
                labels: studentProfileData.labels,
                datasets: [
                    {
                        data: studentProfileData.values,
                        backgroundColor: ["#38bdf8", "#facc15"],
                        borderWidth: 0,
                    },
                ],
            },
            options: {
                animation: animation(150, 70, 980),
                cutout: "68%",
                layout: { padding },
                plugins: { legend: legend("top", "bottom") },
            },
        });
    }

    const attendanceCanvas = document.getElementById("attendanceOverviewChart");
    if (attendanceCanvas) {
        new Chart(attendanceCanvas, {
            type: "bar",
            data: {
                labels: attendanceData.labels?.length
                    ? attendanceData.labels
                    : ["Mon", "Tue", "Wed", "Thu", "Fri"],
                datasets: [
                    {
                        label: "Total Present",
                        data: attendanceData.present?.length
                            ? attendanceData.present
                            : [0, 0, 0, 0, 0],
                        backgroundColor: "#facc15",
                        borderRadius: 8,
                        borderSkipped: false,
                        maxBarThickness: 28,
                    },
                    {
                        label: "Total Absent",
                        data: attendanceData.absent?.length
                            ? attendanceData.absent
                            : [0, 0, 0, 0, 0],
                        backgroundColor: "#7dd3fc",
                        borderRadius: 8,
                        borderSkipped: false,
                        maxBarThickness: 28,
                    },
                ],
            },
            options: {
                animation: animation(170, 65, 980),
                animations: axisAnimations(170),
                layout: { padding },
                plugins: { legend: legend("top", "bottom") },
                scales: { y: yScale, x: xGridless },
            },
        });
    }

    const compositionCanvas = document.getElementById("compositionChart");
    if (compositionCanvas) {
        new Chart(compositionCanvas, {
            type: "doughnut",
            data: {
                labels: compositionData.labels,
                datasets: [
                    {
                        data: compositionData.values,
                        backgroundColor: [
                            "#4f46e5",
                            "#0ea5e9",
                            "#10b981",
                            "#f59e0b",
                        ],
                        borderWidth: 0,
                    },
                ],
            },
            options: {
                animation: animation(180, 80, 1020),
                cutout: "62%",
                layout: { padding },
                plugins: { legend: legend("bottom", "bottom") },
            },
        });
    }

    const periodCanvas = document.getElementById("periodChart");
    if (periodCanvas) {
        new Chart(periodCanvas, {
            type: "polarArea",
            data: {
                labels: periodData.labels,
                datasets: [
                    {
                        data: periodData.values,
                        backgroundColor: [
                            "rgba(99,102,241,0.8)",
                            "rgba(14,165,233,0.8)",
                            "rgba(16,185,129,0.8)",
                            "rgba(245,158,11,0.8)",
                            "rgba(148,163,184,0.8)",
                        ],
                        borderWidth: 0,
                    },
                ],
            },
            options: {
                animation: animation(220, 70, 1000),
                layout: { padding },
                plugins: { legend: legend("bottom", "bottom") },
                scales: { r: { beginAtZero: true, ticks: { precision: 0 } } },
            },
        });
    }

    const classLoadCanvas = document.getElementById("classLoadChart");
    if (classLoadCanvas) {
        new Chart(classLoadCanvas, {
            type: "bar",
            data: {
                labels: classLoadData.labels?.length
                    ? classLoadData.labels
                    : ["No Data"],
                datasets: [
                    {
                        label: "Students",
                        data: classLoadData.values?.length
                            ? classLoadData.values
                            : [0],
                        backgroundColor: "#6366f1",
                        borderRadius: 10,
                        borderSkipped: false,
                        maxBarThickness: 34,
                    },
                ],
            },
            options: {
                animation: animation(250, 85, 940),
                animations: axisAnimations(250),
                plugins: { legend: { display: false } },
                scales: { y: yScale, x: xGridless },
            },
        });
    }

    const subjectCanvas = document.getElementById("subjectHealthChart");
    if (subjectCanvas) {
        new Chart(subjectCanvas, {
            type: "bar",
            data: {
                labels: subjectHealthData.labels,
                datasets: [
                    {
                        label: "Subjects",
                        data: subjectHealthData.values,
                        backgroundColor: [
                            "#10b981",
                            "#f43f5e",
                            "#2563eb",
                            "#94a3b8",
                        ],
                        borderRadius: 10,
                        borderSkipped: false,
                        maxBarThickness: 36,
                    },
                ],
            },
            options: {
                animation: animation(290, 85, 940),
                animations: axisAnimations(290),
                indexAxis: "y",
                plugins: { legend: { display: false } },
                scales: {
                    x: yScale,
                    y: { grid: { display: false } },
                },
            },
        });
    }
});
