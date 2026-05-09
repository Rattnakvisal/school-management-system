window.teacherShell = () => ({
    mobileOpen: false,
    collapsed: false,
    isDesktop: false,

    notifOpen: false,
    profileOpen: false,
    sidebarSections: {
        main: true,
        learning: true,
        requests: true,
        system: true,
    },

    get sidebarCollapsed() {
        return this.isDesktop && this.collapsed;
    },

    init() {
        const mq = window.matchMedia("(min-width: 1024px)");
        const handler = () => {
            this.isDesktop = mq.matches;
            if (mq.matches) this.mobileOpen = false;
        };
        handler();
        mq.addEventListener?.("change", handler);

        const saved = localStorage.getItem("teacher_sidebar_collapsed");
        if (saved !== null) this.collapsed = saved === "1";

        try {
            const savedSections = JSON.parse(localStorage.getItem("teacher_sidebar_sections") || "{}");
            this.sidebarSections = { ...this.sidebarSections, ...savedSections };
        } catch {
            localStorage.removeItem("teacher_sidebar_sections");
        }
    },

    closeAll() {
        this.notifOpen = false;
        this.profileOpen = false;
    },

    toggleSidebar() {
        if (!this.isDesktop) return;
        this.collapsed = !this.collapsed;
        localStorage.setItem("teacher_sidebar_collapsed", this.collapsed ? "1" : "0");
    },

    isSidebarSectionOpen(section) {
        return this.sidebarSections[section] !== false;
    },

    openSidebarSection(section) {
        this.sidebarSections[section] = true;
    },

    toggleSidebarSection(section) {
        this.sidebarSections[section] = !this.isSidebarSectionOpen(section);
        localStorage.setItem("teacher_sidebar_sections", JSON.stringify(this.sidebarSections));
    },
});
