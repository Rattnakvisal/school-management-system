import "./bootstrap";
import "./admin/dashboard";
import "./Teacher/Notifications";
import "./Student/Notifications";

const prefersReducedMotion = window.matchMedia(
    "(prefers-reduced-motion: reduce)",
);
let homeRevealObserver = null;

const isAnimatableChild = (node) => {
    return (
        node instanceof HTMLElement &&
        !node.matches("script, style, template, link, meta") &&
        !node.hasAttribute("data-no-reveal") &&
        !node.hasAttribute("x-cloak")
    );
};

const animatePageBlocks = (force = false) => {
    if (prefersReducedMotion.matches) {
        return;
    }

    document.querySelectorAll("[data-page-animate]").forEach((container) => {
        if (
            !(container instanceof HTMLElement) ||
            (!force && container.dataset.pageAnimated === "1")
        ) {
            return;
        }

        const children = Array.from(container.children).filter(
            isAnimatableChild,
        );
        children.forEach((child, index) => {
            const delay = Math.min(index, 14) * 75 + 40;

            child.animate(
                [
                    { opacity: 0, transform: "translateY(16px) scale(0.992)" },
                    { opacity: 1, transform: "translateY(0) scale(1)" },
                ],
                {
                    duration: 620,
                    delay,
                    easing: "cubic-bezier(0.22, 1, 0.36, 1)",
                    fill: "both",
                },
            );
        });

        container.dataset.pageAnimated = "1";
    });
};

const setupHomeReveal = (replay = false) => {
    if (
        !(document.body instanceof HTMLElement) ||
        !document.body.classList.contains("website-home")
    ) {
        return;
    }

    document.body.classList.add("page-ready");

    if (homeRevealObserver) {
        homeRevealObserver.disconnect();
        homeRevealObserver = null;
    }

    const items = Array.from(document.querySelectorAll("[data-reveal]")).filter(
        (node) => node instanceof HTMLElement,
    );

    if (!items.length) {
        return;
    }

    items.forEach((el) => el.classList.remove("is-visible"));
    document.body.classList.remove("reveal-ready");

    const activateReveal = () => {
        document.body.classList.add("reveal-ready");

        const firstFold = document.querySelectorAll("[data-first][data-reveal]");
        firstFold.forEach((el) => el.classList.add("is-visible"));

        if (
            prefersReducedMotion.matches ||
            !("IntersectionObserver" in window)
        ) {
            items.forEach((el) => el.classList.add("is-visible"));
            return;
        }

        homeRevealObserver = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("is-visible");
                        homeRevealObserver?.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.14, rootMargin: "0px 0px -6% 0px" },
        );

        items.forEach((el) => homeRevealObserver?.observe(el));
    };

    if (replay) {
        requestAnimationFrame(() => {
            requestAnimationFrame(activateReveal);
        });
        return;
    }

    requestAnimationFrame(activateReveal);
};

const bootHomeAnimations = ({
    replayPageBlocks = false,
    replayReveal = false,
} = {}) => {
    if (replayPageBlocks) {
        document
            .querySelectorAll("[data-page-animate]")
            .forEach((container) =>
                container.removeAttribute("data-page-animated"),
            );
    }

    setupHomeReveal(replayReveal);
    animatePageBlocks(replayPageBlocks);
};

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => bootHomeAnimations());
} else {
    bootHomeAnimations();
}

window.addEventListener("pageshow", (event) => {
    if (event.persisted) {
        bootHomeAnimations({ replayPageBlocks: true, replayReveal: true });
    }
});
