<aside
    class="settings-sidebar-shell sticky top-16 z-20 self-start rounded-2xl border border-slate-200 bg-slate-50/95 p-3 shadow-sm backdrop-blur dark:border-slate-700 dark:bg-slate-800/50 xl:top-0">
    <div class="grid gap-2 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto] xl:block xl:space-y-2">
        <div data-settings-nav-group
            class="settings-sidebar-scrollbar max-h-[calc(100vh-13rem)] space-y-1 overflow-y-auto p-2 pr-1">

            <!-- Navbar Section -->
            <button type="button" data-settings-nav="navbar-page"
                class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                <span class="inline-flex items-center gap-2">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M4 5h16v4H4V5Zm0 6h16v8H4v-8Zm3 3v2h10v-2H7Z" />
                    </svg>
                    Navbar
                </span>
            </button>

            <!-- Home Section -->
            <button type="button" data-settings-nav="home-page"
                class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                <span class="inline-flex items-center gap-2">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M4 5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v14a1 1 0 0 1-1.45.89L12 16.62l-6.55 3.27A1 1 0 0 1 4 19V5Zm2 0v10.76l5.11-2.55a2 2 0 0 1 1.78 0L18 15.76V5H6Z" />
                    </svg>
                    Hero
                </span>
            </button>

            <!-- About Section -->
            <button type="button" data-settings-nav="about-page"
                class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                <span class="inline-flex items-center gap-2">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M4 4h16v2H4V4Zm0 4h10v2H4V8Zm0 4h16v8H4v-8Zm2 2v4h12v-4H6Z" />
                    </svg>
                    About
                </span>
            </button>

            <!-- Program Section -->
            <button type="button" data-settings-nav="program-page"
                class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                <span class="inline-flex items-center gap-2">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12 3 2 8l10 5 10-5-10-5Zm-6 9.2V16c0 2.21 2.69 4 6 4s6-1.79 6-4v-3.8l-6 3.27-6-3.27Z" />
                    </svg>
                    Program
                </span>
            </button>

            <!-- Course Section -->
            <button type="button" data-settings-nav="course-page"
                class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                <span class="inline-flex items-center gap-2">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M4 5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v13a1 1 0 0 1-1.45.89L12 15.62l-6.55 3.27A1 1 0 0 1 4 18V5Zm4 3v2h8V8H8Zm0 4v2h5v-2H8Z" />
                    </svg>
                    Courses
                </span>
            </button>

            <!-- FAQ Section -->
            <button type="button" data-settings-nav="faq-page"
                class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                <span class="inline-flex items-center gap-2">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M11 18h2v-2h-2v2Zm1-16a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm0 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16Zm0-14a4 4 0 0 0-4 4h2a2 2 0 1 1 2 2 1 1 0 0 0-1 1v2h2v-1.17A4 4 0 0 0 12 6Z" />
                    </svg>
                    FAQ
                </span>
            </button>

            <!-- Contact Section -->
            <button type="button" data-settings-nav="contact-page"
                class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                <span class="inline-flex items-center gap-2">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Zm8 9 8-5V6l-8 5-8-5v2l8 5Z" />
                    </svg>
                    Contact
                </span>
            </button>

            <!-- Footer Section -->
            <button type="button" data-settings-nav="footer-page"
                class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                <span class="inline-flex items-center gap-2">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M4 4h16v16H4V4Zm2 2v8h12V6H6Zm0 10v2h12v-2H6Z" />
                    </svg>
                    Footer
                </span>
            </button>

        </div>
    </div>
</aside>
