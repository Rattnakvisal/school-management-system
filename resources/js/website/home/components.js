const activeSectionIds = [
    "#home",
    "#about",
    "#features",
    "#programs",
    "#facilities",
    "#admission",
    "#faq",
    "#contact",
];

const bannerStorageKey = "website_home_banner_hidden_until";

const scrollToSection = (selector) => {
    document
        .querySelector(selector)
        ?.scrollIntoView({ behavior: "smooth", block: "start" });
};

const getCookie = (name) => {
    const match = document.cookie
        .split("; ")
        .find((row) => row.startsWith(`${encodeURIComponent(name)}=`));

    return match ? decodeURIComponent(match.split("=").slice(1).join("=")) : "";
};

const setCookie = (name, value, maxAgeSeconds) => {
    const secure = window.location.protocol === "https:" ? "; Secure" : "";

    document.cookie = [
        `${encodeURIComponent(name)}=${encodeURIComponent(value)}`,
        `Max-Age=${maxAgeSeconds}`,
        "Path=/",
        "SameSite=Lax",
        secure,
    ].join("; ");
};

export const registerHomeComponents = () => {
    window.websiteHomePage = () => ({
        open: false,
        top: false,
        active: "#home",
        showBanner: false,
        bannerStorageKey,
        tokenPromptVisible: false,
        tokenAlertVisible: false,
        tokenAlertTimer: null,
        tokenCookieName: "website_home_cookie_preference",

        init() {
            this.setActive();
            this.initBanner();
            this.initTokenPrompt();

            window.addEventListener("scroll", () => {
                this.top = window.scrollY > 560;
                this.setActive();
            });
        },

        setActive() {
            const y = window.scrollY + 140;

            for (let i = activeSectionIds.length - 1; i >= 0; i -= 1) {
                const section = document.querySelector(activeSectionIds[i]);
                if (section && section.offsetTop <= y) {
                    this.active = activeSectionIds[i];
                    break;
                }
            }
        },

        initBanner() {
            const forceBanner =
                new URLSearchParams(window.location.search).get("banner") ===
                "1";

            try {
                if (forceBanner) {
                    window.localStorage.removeItem(this.bannerStorageKey);
                }

                const raw = window.localStorage.getItem(this.bannerStorageKey);
                const hiddenUntil = raw ? Number(raw) : 0;

                if (!Number.isNaN(hiddenUntil) && hiddenUntil > Date.now()) {
                    this.showBanner = false;
                    return;
                }
            } catch (_) {
                // Storage can be blocked in private or restricted contexts.
            }

            window.setTimeout(() => {
                this.showBanner = true;
            }, 280);
        },

        closeBanner(remember = false) {
            this.showBanner = false;

            if (!remember) {
                return;
            }

            try {
                window.localStorage.setItem(
                    this.bannerStorageKey,
                    String(Date.now() + 24 * 60 * 60 * 1000),
                );
            } catch (_) {
                // Closing still works even when storage is unavailable.
            }
        },

        openProgramsFromBanner() {
            this.closeBanner();
            scrollToSection("#programs");
        },

        initTokenPrompt() {
            if (getCookie(this.tokenCookieName)) {
                return;
            }

            window.setTimeout(() => {
                this.tokenPromptVisible = true;
            }, 760);
        },

        storePageToken() {
            this.tokenPromptVisible = false;
            this.tokenAlertVisible = true;

            window.clearTimeout(this.tokenAlertTimer);

            try {
                setCookie(this.tokenCookieName, "remembered", 60 * 60 * 24 * 7);
            } catch (_) {
                // The alert still confirms the preference for this page load.
            }

            this.tokenAlertTimer = window.setTimeout(() => {
                this.closeTokenAlert();
            }, 5200);
        },

        skipTokenPrompt() {
            this.tokenPromptVisible = false;

            try {
                setCookie(this.tokenCookieName, "dismissed", 60 * 60 * 24 * 7);
            } catch (_) {
                // Dismissal still works for this page load.
            }
        },

        closeTokenAlert() {
            this.tokenAlertVisible = false;
            window.clearTimeout(this.tokenAlertTimer);
        },
    });

    window.websiteHomeHeader = () => ({
        open: false,
        active: window.location.hash || "#home",
        scrolled: false,
        showNavbar: true,
        lastScrollY: window.scrollY,

        handleScroll() {
            this.scrolled = window.scrollY > 20;

            if (window.scrollY > this.lastScrollY && window.scrollY > 120) {
                this.showNavbar = false;
                this.open = false;
            } else {
                this.showNavbar = true;
            }

            this.lastScrollY = window.scrollY;

            document.querySelectorAll("section[id]").forEach((section) => {
                const top = section.offsetTop - 140;
                const bottom = top + section.offsetHeight;

                if (window.scrollY >= top && window.scrollY < bottom) {
                    this.active = `#${section.id}`;
                }
            });
        },
    });
};
