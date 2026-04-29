let shellLoaderEventsBound = false;

const isLoadingShell = () =>
    document.body instanceof HTMLElement &&
    document.body.hasAttribute("data-loading-shell");

const getShellLoader = () => document.getElementById("admin-swirling-loader");

export const hideShellLoader = ({ minVisible = true } = {}) => {
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
        if (event.defaultPrevented) {
            return;
        }

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

export const bootShellLoader = () => {
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
