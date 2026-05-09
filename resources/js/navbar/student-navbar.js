window.studentShell = () => ({
    mobileOpen: false,
    collapsed: false,
    isDesktop: window.innerWidth >= 1024,
    notifOpen: false,
    profileOpen: false,
    sidebarSections: {
        main: true,
        management: true,
        requests: true,
        system: true,
    },

    get sidebarCollapsed() {
        return this.isDesktop && this.collapsed;
    },

    init() {
        const saved = localStorage.getItem("student_sidebar_collapsed");
        this.collapsed = saved === "1";

        try {
            const savedSections = JSON.parse(localStorage.getItem("student_sidebar_sections") || "{}");
            this.sidebarSections = { ...this.sidebarSections, ...savedSections };
        } catch {
            localStorage.removeItem("student_sidebar_sections");
        }

        this.syncViewport();
        window.addEventListener("resize", () => this.syncViewport());
    },

    syncViewport() {
        this.isDesktop = window.innerWidth >= 1024;
        if (this.isDesktop) {
            this.mobileOpen = false;
        }
    },

    toggleSidebar() {
        this.collapsed = !this.collapsed;
        localStorage.setItem("student_sidebar_collapsed", this.collapsed ? "1" : "0");
    },

    closeAll() {
        this.notifOpen = false;
        this.profileOpen = false;
        this.mobileOpen = false;
    },

    isSidebarSectionOpen(section) {
        return this.sidebarSections[section] !== false;
    },

    openSidebarSection(section) {
        this.sidebarSections[section] = true;
    },

    toggleSidebarSection(section) {
        this.sidebarSections[section] = !this.isSidebarSectionOpen(section);
        localStorage.setItem("student_sidebar_sections", JSON.stringify(this.sidebarSections));
    },
});
