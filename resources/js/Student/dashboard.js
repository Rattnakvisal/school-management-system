document.addEventListener("DOMContentLoaded", () => {
    setupRevealAnimation();
    animateCountNumbers();
    animateSummaryFills();
    animateTrendFills();
    setupHoverCards();
});

function setupRevealAnimation() {
    const items = document.querySelectorAll(".dash-reveal");

    if (!items.length) return;
    if (document.querySelector(".dashboard-stage")) return;

    items.forEach((item) => {
        const delayStep = Number.parseFloat(
            getComputedStyle(item).getPropertyValue("--d") || "0",
        );

        item.style.opacity = "0";
        item.style.transform = "translateY(24px)";
        item.style.transition =
            "opacity 700ms ease, transform 700ms cubic-bezier(0.22, 1, 0.36, 1)";
        item.style.transitionDelay = `${delayStep * 120}ms`;
    });

    requestAnimationFrame(() => {
        items.forEach((item) => {
            item.style.opacity = "1";
            item.style.transform = "translateY(0)";
        });
    });
}

function animateCountNumbers() {
    const elements = document.querySelectorAll(".student-animate-number");

    elements.forEach((el) => {
        const end = Number.parseFloat(el.dataset.value || "0");
        const suffix = el.dataset.suffix || "";
        const decimals = Number.parseInt(el.dataset.decimals || "0", 10);

        if (Number.isNaN(end)) return;

        animateNumber(0, end, 1100, (value) => {
            const output =
                decimals > 0 ? value.toFixed(decimals) : Math.round(value);
            el.textContent = `${output}${suffix}`;
        });
    });
}

function animateSummaryFills() {
    const fills = document.querySelectorAll(".student-summary-fill");

    fills.forEach((fill, index) => {
        const finalHeight = Number.parseFloat(fill.dataset.height || "0");

        fill.style.height = "0%";
        fill.style.transition = "height 900ms cubic-bezier(0.22, 1, 0.36, 1)";
        fill.style.transitionDelay = `${index * 100}ms`;

        requestAnimationFrame(() => {
            setTimeout(() => {
                fill.style.height = `${finalHeight}%`;
            }, 220);
        });
    });
}

function animateTrendFills() {
    const fills = document.querySelectorAll(".student-trend-fill");

    fills.forEach((fill, index) => {
        const finalWidth = Number.parseFloat(fill.dataset.width || "0");

        fill.style.width = "0%";
        fill.style.transition = "width 1000ms cubic-bezier(0.22, 1, 0.36, 1)";
        fill.style.transitionDelay = `${index * 120}ms`;

        requestAnimationFrame(() => {
            setTimeout(() => {
                fill.style.width = `${finalWidth}%`;
            }, 260);
        });
    });
}

function setupHoverCards() {
    const cards = document.querySelectorAll(".student-hover-card");

    cards.forEach((card) => {
        card.style.transition =
            "transform 220ms ease, box-shadow 220ms ease, background-color 220ms ease";

        card.addEventListener("mouseenter", () => {
            card.style.transform = "translateY(-4px)";
        });

        card.addEventListener("mouseleave", () => {
            card.style.transform = "translateY(0)";
        });
    });
}

function animateNumber(start, end, duration, onUpdate) {
    const startTime = performance.now();

    function step(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        const value = start + (end - start) * eased;

        onUpdate(value);

        if (progress < 1) {
            requestAnimationFrame(step);
        }
    }

    requestAnimationFrame(step);
}
