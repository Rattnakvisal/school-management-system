const parseSearchItems = (searchItems) => {
    if (Array.isArray(searchItems)) {
        return searchItems;
    }

    try {
        return JSON.parse(searchItems || "[]");
    } catch {
        return [];
    }
};

window.adminShell = (searchItems = window.adminNavbarSearchItems || []) => ({
    mobileOpen: false,
    collapsed: false,
    darkMode: false,

    notifOpen: false,
    messageOpen: false,
    profileOpen: false,
    searchOpen: false,
    searchTerm: "",
    searchItems: parseSearchItems(searchItems),

    init() {
        const savedTheme = localStorage.getItem("admin_theme");
        const prefersDark = window.matchMedia?.("(prefers-color-scheme: dark)")?.matches ?? false;
        this.darkMode = savedTheme ? savedTheme === "dark" : prefersDark;
        this.applyTheme();

        const mq = window.matchMedia("(min-width: 1024px)");
        const handler = () => {
            if (mq.matches) this.mobileOpen = false;
        };
        handler();
        mq.addEventListener?.("change", handler);

        const saved = localStorage.getItem("admin_sidebar_collapsed");
        if (saved !== null) this.collapsed = saved === "1";
    },

    closeAll() {
        this.searchOpen = false;
        this.closeMenus();
    },

    closeMenus() {
        this.notifOpen = false;
        this.messageOpen = false;
        this.profileOpen = false;
    },

    filteredSearchItems() {
        const term = this.searchTerm.trim().toLowerCase();

        if (!term) {
            return this.searchItems.slice(0, 8);
        }

        const matches = this.searchItems.filter((item) => {
            return `${item.label} ${item.description} ${item.keywords}`.toLowerCase().includes(term);
        });

        if (matches.length > 0) {
            return matches.slice(0, 8);
        }

        const currentSearchable = this.searchItems.filter((item) => item.active && item.searchable);
        const searchable = this.searchItems.filter((item) => item.searchable);

        return [...currentSearchable, ...searchable]
            .filter((item, index, items) => items.findIndex((candidate) => candidate.label === item.label) === index)
            .slice(0, 8);
    },

    searchUrl(item) {
        const term = this.searchTerm.trim();
        const target = new URL(item.url, window.location.origin);

        if (term && item.searchable && !this.isPageSearch(item, term)) {
            target.searchParams.set("q", term);
        }

        return target.toString();
    },

    isPageSearch(item, term) {
        const normalizedTerm = term.toLowerCase();
        const normalizedLabel = String(item.label || "").toLowerCase();

        return normalizedLabel.includes(normalizedTerm) || normalizedTerm.includes(normalizedLabel);
    },

    goSearch(item = null) {
        const term = this.searchTerm.trim();
        const exactPageTarget = term
            ? this.filteredSearchItems().find((searchItem) => this.isPageSearch(searchItem, term))
            : null;
        const target =
            item ||
            exactPageTarget ||
            (term ? this.searchItems.find((searchItem) => searchItem.active && searchItem.searchable) : null) ||
            this.filteredSearchItems()[0] ||
            this.searchItems.find((searchItem) => searchItem.searchable);

        if (!target) return;

        window.location.href = this.searchUrl(target);
    },

    toggleSidebar() {
        this.collapsed = !this.collapsed;
        localStorage.setItem("admin_sidebar_collapsed", this.collapsed ? "1" : "0");
    },

    applyTheme() {
        document.documentElement.classList.toggle("dark", this.darkMode);
        document.documentElement.style.colorScheme = this.darkMode ? "dark" : "light";
    },

    toggleTheme() {
        this.darkMode = !this.darkMode;
        localStorage.setItem("admin_theme", this.darkMode ? "dark" : "light");
        this.applyTheme();
    },
});
