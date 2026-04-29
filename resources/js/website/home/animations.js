const prefersReducedMotion = window.matchMedia(
    "(prefers-reduced-motion: reduce)",
);

let revealObserver = null;

const isHomePage = () =>
    document.body instanceof HTMLElement &&
    document.body.classList.contains("website-home");

const isAnimatableChild = (node) => {
    return (
        node instanceof HTMLElement &&
        !node.matches("script, style, template, link, meta") &&
        !node.hasAttribute("data-no-reveal") &&
        !node.hasAttribute("x-cloak")
    );
};

const revealFirstFold = () => {
    document
        .querySelectorAll("[data-first][data-reveal]")
        .forEach((element) => element.classList.add("is-visible"));
};

const animatePageBlocks = (force = false) => {
    if (!isHomePage() || prefersReducedMotion.matches) {
        return;
    }

    document.querySelectorAll("[data-page-animate]").forEach((container) => {
        if (
            !(container instanceof HTMLElement) ||
            (!force && container.dataset.pageAnimated === "1")
        ) {
            return;
        }

        Array.from(container.children)
            .filter(isAnimatableChild)
            .forEach((child, index) => {
                child.animate(
                    [
                        {
                            opacity: 0,
                            transform: "translateY(16px) scale(0.992)",
                        },
                        { opacity: 1, transform: "translateY(0) scale(1)" },
                    ],
                    {
                        duration: 620,
                        delay: Math.min(index, 14) * 75 + 40,
                        easing: "cubic-bezier(0.22, 1, 0.36, 1)",
                        fill: "both",
                    },
                );
            });

        container.dataset.pageAnimated = "1";
    });
};

const setupRevealObserver = (replay = false) => {
    if (!isHomePage()) {
        return;
    }

    document.body.classList.add("page-ready");

    if (revealObserver) {
        revealObserver.disconnect();
        revealObserver = null;
    }

    document.addEventListener("DOMContentLoaded", () => {
        const elements = document.querySelectorAll("[data-reveal]");

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = 1;
                    entry.target.style.transform = "translateY(0)";
                }
            });
        });

        elements.forEach((el) => {
            el.style.opacity = 0;
            el.style.transform = "translateY(20px)";
            el.style.transition = "all 0.6s ease";
            observer.observe(el);
        });
    });
};

export const bootHomeAnimations = ({
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

    setupRevealObserver(replayReveal);
    animatePageBlocks(replayPageBlocks);
};
