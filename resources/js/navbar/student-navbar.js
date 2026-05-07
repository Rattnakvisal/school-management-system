window.studentShell = () => ({
    mobileOpen: false,
    collapsed: false,
    isDesktop: window.innerWidth >= 1024,
    notifOpen: false,
    profileOpen: false,

    init() {
        const saved = localStorage.getItem("student_sidebar_collapsed");
        this.collapsed = saved === "1";
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
});
