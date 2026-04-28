import "./bootstrap";
import "./admin/dashboard";
import "./Teacher/Notifications";
import "./Student/Notifications";

const prefersReducedMotion = window.matchMedia(
    "(prefers-reduced-motion: reduce)",
);
let homeRevealObserver = null;
let shellLoaderEventsBound = false;

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

const isLoadingShell = () =>
    document.body instanceof HTMLElement &&
    document.body.hasAttribute("data-loading-shell");

const getShellLoader = () => document.getElementById("admin-swirling-loader");

const hideShellLoader = ({ minVisible = true } = {}) => {
    if (!isLoadingShell()) {
        return;
    }

    const loader = getShellLoader();
    if (!loader) {
        return;
    }

    const startedAt = Number(loader.dataset.startedAt || performance.now());
    const elapsed = performance.now() - startedAt;
    const delay = minVisible ? Math.max(0, 420 - elapsed) : 0;

    window.setTimeout(() => {
        document.body.classList.add("admin-loader-hidden");
    }, delay);
};

const showShellLoader = () => {
    if (!isLoadingShell()) {
        return;
    }

    const loader = getShellLoader();
    if (!loader) {
        return;
    }

    loader.dataset.startedAt = String(performance.now());
    document.body.classList.remove("admin-loader-hidden");
};

const shouldShowShellLoaderForLink = (event) => {
    if (
        event.defaultPrevented ||
        event.button !== 0 ||
        event.metaKey ||
        event.ctrlKey ||
        event.shiftKey ||
        event.altKey ||
        !(event.target instanceof Element)
    ) {
        return false;
    }

    const link = event.target.closest("a[href]");
    if (!(link instanceof HTMLAnchorElement)) {
        return false;
    }

    const target = (link.getAttribute("target") || "").toLowerCase();
    const href = link.getAttribute("href") || "";

    if (
        link.hasAttribute("download") ||
        (target && target !== "_self") ||
        href.startsWith("#") ||
        href.startsWith("mailto:") ||
        href.startsWith("tel:") ||
        href.startsWith("javascript:")
    ) {
        return false;
    }

    const url = new URL(link.href, window.location.href);
    const currentUrl = new URL(window.location.href);

    return (
        url.origin === currentUrl.origin &&
        (url.pathname !== currentUrl.pathname ||
            url.search !== currentUrl.search ||
            !url.hash)
    );
};

const bindShellLoaderEvents = () => {
    if (shellLoaderEventsBound || !isLoadingShell()) {
        return;
    }

    shellLoaderEventsBound = true;

    document.addEventListener("click", (event) => {
        if (shouldShowShellLoaderForLink(event)) {
            showShellLoader();
        }
    });

    document.addEventListener("submit", (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        const target = (form.getAttribute("target") || "").toLowerCase();
        if (!target || target === "_self") {
            showShellLoader();
        }
    });

    window.addEventListener("beforeunload", showShellLoader);
};

const bootShellLoader = () => {
    if (!isLoadingShell()) {
        return;
    }

    const loader = getShellLoader();
    if (loader && !loader.dataset.startedAt) {
        loader.dataset.startedAt = String(performance.now());
    }

    bindShellLoaderEvents();
    hideShellLoader();
};

const bootApp = (options = {}) => {
    bootShellLoader();
    bootHomeAnimations(options);
};

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => bootApp());
} else {
    bootApp();
}

window.addEventListener("pageshow", (event) => {
    if (event.persisted) {
        hideShellLoader({ minVisible: false });
        bootHomeAnimations({ replayPageBlocks: true, replayReveal: true });
    }
});
