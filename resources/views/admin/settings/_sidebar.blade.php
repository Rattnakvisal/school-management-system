                <aside
                    class="settings-sidebar-shell sticky top-16 z-20 self-start rounded-2xl border border-slate-200 bg-slate-50/95 p-3 shadow-sm backdrop-blur xl:top-20">
                    <div class="grid gap-2 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto] xl:block xl:space-y-2">
                        <details data-settings-nav-group open class="group min-w-0 p-2">
                            <summary
                                class="flex cursor-pointer list-none items-center justify-between rounded-xl px-3 py-2 text-xs font-black uppercase tracking-[0.18em] text-slate-500 transition hover:bg-slate-50">
                                Account
                                <svg class="h-4 w-4 transition group-open:rotate-180" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="m6 9 6 6 6-6" />
                                </svg>
                            </summary>
                            <div class="mt-2 space-y-1">
                                <button type="button" data-settings-nav="profile"
                                    class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                                    <span class="inline-flex items-center gap-2">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M12 12c2.21 0 4-1.79 4-4S14.21 4 12 4 8 5.79 8 8s1.79 4 4 4Zm0 2c-3.33 0-8 1.67-8 5v1h16v-1c0-3.33-4.67-5-8-5Z" />
                                        </svg>
                                        Profile Settings
                                    </span>
                                </button>
                                <button type="button" data-settings-nav="password"
                                    class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                                    <span class="inline-flex items-center gap-2">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M17 9h-1V7a4 4 0 1 0-8 0v2H7a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-8a2 2 0 0 0-2-2Zm-7-2a2 2 0 1 1 4 0v2h-4V7Zm2 10a2 2 0 1 1 0-4 2 2 0 0 1 0 4Z" />
                                        </svg>
                                        Password
                                    </span>
                                </button>
                                <button type="button" data-settings-nav="notifications"
                                    class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                                    <span class="inline-flex items-center gap-2">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M12 2a6 6 0 0 0-6 6v3.59L4.71 13.3A1 1 0 0 0 5.41 15h13.18a1 1 0 0 0 .7-1.7L18 11.59V8a6 6 0 0 0-6-6Zm0 20a3 3 0 0 0 2.82-2H9.18A3 3 0 0 0 12 22Z" />
                                        </svg>
                                        Notifications
                                    </span>
                                </button>
                            </div>
                        </details>
                        <details data-settings-nav-group open class="group min-w-0 rounded-2xl p-2">
                            <summary
                                class="flex cursor-pointer list-none items-center justify-between rounded-xl px-3 py-2 text-xs font-black uppercase tracking-[0.18em] text-slate-500 transition hover:bg-slate-50">
                                Home UI
                                <svg class="h-4 w-4 transition group-open:rotate-180" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="m6 9 6 6 6-6" />
                                </svg>
                            </summary>
                            <div
                                class="settings-sidebar-scrollbar mt-2 max-h-[calc(100vh-19rem)] space-y-1 overflow-y-auto pr-1">
                                <button type="button" data-settings-nav="navbar-page"
                                    class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                                    <span class="inline-flex items-center gap-2">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M4 5h16v4H4V5Zm0 6h16v8H4v-8Zm3 3v2h10v-2H7Z" />
                                        </svg>
                                        Navbar Page
                                    </span>
                                </button>
                                <button type="button" data-settings-nav="home-page"
                                    class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                                    <span class="inline-flex items-center gap-2">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M4 5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v14a1 1 0 0 1-1.45.89L12 16.62l-6.55 3.27A1 1 0 0 1 4 19V5Zm2 0v10.76l5.11-2.55a2 2 0 0 1 1.78 0L18 15.76V5H6Z" />
                                        </svg>
                                        Hero Page
                                    </span>
                                </button>
                                <button type="button" data-settings-nav="about-page"
                                    class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                                    <span class="inline-flex items-center gap-2">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M4 4h16v2H4V4Zm0 4h10v2H4V8Zm0 4h16v8H4v-8Zm2 2v4h12v-4H6Z" />
                                        </svg>
                                        About Page
                                    </span>
                                </button>
                                <button type="button" data-settings-nav="feature-page"
                                    class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                                    <span class="inline-flex items-center gap-2">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm2 4v4h4V7H7Zm6 0v4h4V7h-4ZM7 13v4h4v-4H7Zm6 0v4h4v-4h-4Z" />
                                        </svg>
                                        Feature Page
                                    </span>
                                </button>
                                <button type="button" data-settings-nav="program-page"
                                    class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                                    <span class="inline-flex items-center gap-2">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M12 3 2 8l10 5 10-5-10-5Zm-6 9.2V16c0 2.21 2.69 4 6 4s6-1.79 6-4v-3.8l-6 3.27-6-3.27Z" />
                                        </svg>
                                        Program Page
                                    </span>
                                </button>
                                <button type="button" data-settings-nav="facility-page"
                                    class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                                    <span class="inline-flex items-center gap-2">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M4 21V7l8-4 8 4v14h-5v-6H9v6H4Zm5-11h2V8H9v2Zm4 0h2V8h-2v2Z" />
                                        </svg>
                                        Facility Page
                                    </span>
                                </button>
                                <button type="button" data-settings-nav="admission-page"
                                    class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                                    <span class="inline-flex items-center gap-2">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M4 4h16v4H4V4Zm0 6h16v10H4V10Zm3 3v2h7v-2H7Zm0 4h10v-2H7v2Z" />
                                        </svg>
                                        Admission Page
                                    </span>
                                </button>
                                <button type="button" data-settings-nav="faq-page"
                                    class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                                    <span class="inline-flex items-center gap-2">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M11 18h2v-2h-2v2Zm1-16a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm0 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16Zm0-14a4 4 0 0 0-4 4h2a2 2 0 1 1 2 2 1 1 0 0 0-1 1v2h2v-1.17A4 4 0 0 0 12 6Z" />
                                        </svg>
                                        FAQ Page
                                    </span>
                                </button>
                                <button type="button" data-settings-nav="contact-page"
                                    class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                                    <span class="inline-flex items-center gap-2">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Zm8 9 8-5V6l-8 5-8-5v2l8 5Z" />
                                        </svg>
                                        Contact Page
                                    </span>
                                </button>
                                <button type="button" data-settings-nav="footer-page"
                                    class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                                    <span class="inline-flex items-center gap-2">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M4 4h16v16H4V4Zm2 2v8h12V6H6Zm0 10v2h12v-2H6Z" />
                                        </svg>
                                        Footer Page
                                    </span>
                                </button>
                            </div>
                        </details>
                        <button type="button" data-settings-nav="verification"
                            class="settings-nav-item w-full rounded-xl border border-transparent px-3 py-2.5 text-left text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700 lg:h-full xl:h-auto">
                            <span class="inline-flex items-center gap-2 lg:justify-center xl:justify-start">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="m10.56 17.36 7.78-7.78-1.42-1.42-6.36 6.36-2.84-2.83-1.41 1.41 4.25 4.26Zm1.44-15.3 7 3v5c0 5.25-3.5 10.17-7 11.94-3.5-1.77-7-6.69-7-11.94v-5l7-3Z" />
                                </svg>
                                Verification
                            </span>
                        </button>
                    </div>
                </aside>
