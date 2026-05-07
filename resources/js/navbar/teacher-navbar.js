window.teacherShell = () => ({
    mobileOpen: false,
    collapsed: false,
    isDesktop: false,

    notifOpen: false,
    profileOpen: false,

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
});
