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

export const registerHomeComponents = () => {
    window.websiteHomePage = () => ({
        open: false,
        top: false,
        active: "#home",
        showBanner: false,
        bannerStorageKey,

        init() {
            this.setActive();
            this.initBanner();

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
