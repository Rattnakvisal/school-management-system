@extends('layout.admin.navbar.navbar')

@section('page')
    @php
        $nameParts = preg_split('/\s+/', trim((string) $admin->name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $defaultFirstName = $nameParts[0] ?? '';
        $defaultLastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';
        $profileErrors = $errors->profileUpdate;
        $passwordErrors = $errors->passwordUpdate;
        $homePageErrors = $errors->homePageUpdate;
        $navbarPageErrors = $errors->navbarPageUpdate;
        $aboutPageErrors = $errors->aboutPageUpdate;
        $featurePageErrors = $errors->featurePageUpdate;
        $programPageErrors = $errors->programPageUpdate;
        $facilityPageErrors = $errors->facilityPageUpdate;
        $admissionPageErrors = $errors->admissionPageUpdate;
        $faqPageErrors = $errors->faqPageUpdate;
        $contactPageErrors = $errors->contactPageUpdate;
        $footerPageErrors = $errors->footerPageUpdate;
        $fallbackSettingsTab = 'profile';
        foreach (
            [
            'footer-page' => $footerPageErrors,
            'contact-page' => $contactPageErrors,
            'faq-page' => $faqPageErrors,
            'admission-page' => $admissionPageErrors,
            'facility-page' => $facilityPageErrors,
            'program-page' => $programPageErrors,
            'navbar-page' => $navbarPageErrors,
            'feature-page' => $featurePageErrors,
                'about-page' => $aboutPageErrors,
                'home-page' => $homePageErrors,
                'password' => $passwordErrors,
            ]
            as $tab => $bag
        ) {
            if ($bag->any()) {
                $fallbackSettingsTab = $tab;
                break;
            }
        }
        $settingsDefaultTab = session('settings_tab', old('_settings_tab', $fallbackSettingsTab));
        $homePageItems = $homePageItems ?? collect();
        $homeHero = $homePageItems->get('hero', collect())->firstWhere('key', 'main');
        $homeBrand = $homePageItems->get('brand', collect())->firstWhere('key', 'main');
        $navbarLinks = $homePageItems->get('navbar_links', collect())->values();
        $homePoints = $homePageItems->get('hero_points', collect())->values();
        $homeStats = $homePageItems->get('hero_stats', collect())->values();
        $homeFeatures = $homePageItems->get('hero_features', collect())->values();
        $homeAbout = $homePageItems->get('about', collect())->firstWhere('key', 'main');
        $homeAboutHighlights = $homePageItems->get('about_highlights', collect())->values();
        $homeAboutCards = $homePageItems->get('about_cards', collect())->values();
        $platformFeature = $homePageItems->get('platform_features', collect())->firstWhere('key', 'main');
        $platformFeatureCards = $homePageItems->get('platform_feature_cards', collect())->values();
        $programSection = $homePageItems->get('programs', collect())->firstWhere('key', 'main');
        $programCards = $homePageItems->get('program_cards', collect())->values();
        $facilitySection = $homePageItems->get('facilities', collect())->firstWhere('key', 'main');
        $facilityCards = $homePageItems->get('facility_cards', collect())->values();
        $admissionSection = $homePageItems->get('admission', collect())->firstWhere('key', 'main');
        $admissionIntake = $homePageItems->get('admission_intake', collect())->firstWhere('key', 'main');
        $admissionSteps = $homePageItems->get('admission_steps', collect())->values();
        $faqSection = $homePageItems->get('faq', collect())->firstWhere('key', 'main');
        $faqHelp = $homePageItems->get('faq_help', collect())->firstWhere('key', 'main');
        $dynamicFaqItems = $homePageItems->get('faq_items', collect())->values();
        $contactSection = $homePageItems->get('contact', collect())->firstWhere('key', 'main');
        $contactCampus = $homePageItems->get('contact_campus', collect())->firstWhere('key', 'main');
        $dynamicContactCards = $homePageItems->get('contact_cards', collect())->values();
        $footerSection = $homePageItems->get('footer', collect())->firstWhere('key', 'main');
        $dynamicFooterLinks = $homePageItems->get('footer_links', collect())->values();
        $dynamicFooterContacts = $homePageItems->get('footer_contacts', collect())->values();
        $pointLimit = 6;
        $statLimit = 6;
        $heroFeatureLimit = 8;
        $aboutHighlightLimit = 4;
        $aboutCardLimit = 8;
        $platformFeatureLimit = 8;
        $programLimit = 8;
        $facilityLimit = 8;
        $admissionStepLimit = 8;
        $faqLimit = 10;
        $contactCardLimit = 8;
        $footerLinkLimit = 8;
        $footerContactLimit = 8;
        $navbarLinkLimit = 10;
        $iconColorValue = function ($value, string $fallback): string {
            $value = trim((string) $value);

            return preg_match('/^#[0-9A-Fa-f]{6}$/', $value) ? $value : $fallback;
        };
        $aboutHighlightColorDefaults = ['#fbbf24', '#34d399', '#38bdf8', '#e879f9'];
        $aboutCardColorDefaults = ['#e11d48', '#059669', '#0284c7', '#7c3aed'];
        $heroStatColorDefaults = ['#10b981', '#2563eb', '#7c3aed', '#d97706', '#e11d48', '#0891b2'];
        $heroFeatureColorDefaults = ['#2563eb', '#0d9488', '#7c3aed', '#d97706'];
        $featureCardColorDefaults = ['#d97706', '#0d9488', '#0891b2', '#e11d48', '#7c3aed', '#65a30d'];
        $programCardColorDefaults = ['#2563eb', '#059669', '#d97706', '#c026d3'];
        $admissionStepColorDefaults = ['#22d3ee', '#f59e0b', '#34d399', '#fb7185'];
        $navbarPageOptions = [
            '#home' => 'Home',
            '#about' => 'About',
            '#features' => 'Features',
            '#programs' => 'Academic Programs',
            '#facilities' => 'Campus and Facilities',
            '#admission' => 'Admissions',
            '#faq' => 'FAQ',
            '#contact' => 'Contact',
        ];
        $homePageImage = $homeHero?->image_path
            ? route('public.storage', ['path' => $homeHero->image_path])
            : asset('images/school.jpg');
        $homeBrandLogo = $homeBrand?->image_path
            ? route('public.storage', ['path' => $homeBrand->image_path])
            : asset('images/techbridge-logo-mark.svg');
        $facilityImage = $facilitySection?->image_path
            ? route('public.storage', ['path' => $facilitySection->image_path])
            : asset('images/study.jpg');
        $footerLogo = $footerSection?->image_path
            ? route('public.storage', ['path' => $footerSection->image_path])
            : asset('images/techbridge-logo-mark.svg');
    @endphp

    <style>
        #admin-settings-page .settings-sidebar-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: rgba(99, 102, 241, 0.35) transparent;
            overscroll-behavior: contain;
        }

        #admin-settings-page .settings-sidebar-scrollbar::-webkit-scrollbar {
            width: 5px;
        }

        #admin-settings-page .settings-sidebar-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        #admin-settings-page .settings-sidebar-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(99, 102, 241, 0.32);
            border-radius: 999px;
        }

        #admin-settings-page .settings-sidebar-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(79, 70, 229, 0.55);
        }

        #admin-settings-page input[type="color"] {
            height: 2.5rem;
            min-width: 3rem;
            cursor: pointer;
            border-color: #bfdbfe;
            background: #eff6ff;
            padding: 0.25rem;
        }

        #admin-settings-page .settings-sidebar-shell {
            scrollbar-width: none;
        }

        #admin-settings-page .settings-sidebar-shell::-webkit-scrollbar {
            display: none;
        }

        @media (max-width: 1279px) {
            #admin-settings-page .settings-sidebar-shell {
                max-height: calc(100vh - 4.75rem);
                overflow-y: auto;
            }

            #admin-settings-page .settings-sidebar-scrollbar {
                max-height: 16rem;
            }
        }
    </style>

    <div id="admin-settings-page" class="stage space-y-6" data-settings-default-tab="{{ $settingsDefaultTab }}">
        <x-admin.page-header reveal-class="reveal" delay="1" icon="settings" title="Admin Settings"
            subtitle="Manage profile details, avatar, and account security.">
            <x-slot:actions>
                <a href="{{ route('admin.dashboard') }}"
                    class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                    Open Dashboard
                </a>
            </x-slot:actions>
        </x-admin.page-header>

        @if (session('success'))
            <div class="reveal rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700"
                style="--sd: 2;">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700"
                style="--sd: 2;">
                {{ session('error') }}
            </div>
        @endif

        @if ($profileErrors->any())
            <div class="reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" style="--sd: 2;">
                {{ $profileErrors->first() }}
            </div>
        @endif

        @if ($passwordErrors->any())
            <div class="reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" style="--sd: 2;">
                {{ $passwordErrors->first() }}
            </div>
        @endif

        @if ($homePageErrors->any())
            <div class="reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" style="--sd: 2;">
                {{ $homePageErrors->first() }}
            </div>
        @endif

        @if ($navbarPageErrors->any())
            <div class="reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" style="--sd: 2;">
                {{ $navbarPageErrors->first() }}
            </div>
        @endif

        @if ($aboutPageErrors->any())
            <div class="reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" style="--sd: 2;">
                {{ $aboutPageErrors->first() }}
            </div>
        @endif

        @if ($featurePageErrors->any())
            <div class="reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" style="--sd: 2;">
                {{ $featurePageErrors->first() }}
            </div>
        @endif

        @if ($programPageErrors->any())
            <div class="reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" style="--sd: 2;">
                {{ $programPageErrors->first() }}
            </div>
        @endif

        @if ($facilityPageErrors->any())
            <div class="reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" style="--sd: 2;">
                {{ $facilityPageErrors->first() }}
            </div>
        @endif

        @if ($admissionPageErrors->any())
            <div class="reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" style="--sd: 2;">
                {{ $admissionPageErrors->first() }}
            </div>
        @endif

        @if ($faqPageErrors->any())
            <div class="reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" style="--sd: 2;">
                {{ $faqPageErrors->first() }}
            </div>
        @endif

        @if ($contactPageErrors->any())
            <div class="reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" style="--sd: 2;">
                {{ $contactPageErrors->first() }}
            </div>
        @endif

        @if ($footerPageErrors->any())
            <div class="reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" style="--sd: 2;">
                {{ $footerPageErrors->first() }}
            </div>
        @endif

        <section class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm ring-1 ring-slate-100 sm:p-6">
            <div class="grid gap-6 xl:grid-cols-[260px_minmax(0,1fr)]">
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

                <div class="min-w-0">
                    <section data-settings-panel="profile" class="space-y-6">
                        <form method="POST" action="{{ route('admin.settings.profile.update') }}"
                            enctype="multipart/form-data"
                            class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 sm:p-6">
                            @csrf
                            @method('PUT')

                            <input type="hidden" id="remove_admin_avatar" name="remove_avatar" value="0">

                            <div class="flex flex-wrap items-center gap-4 border-b border-slate-200 pb-5">
                                <div class="relative">
                                    <img id="admin_avatar_preview"
                                        class="h-24 w-24 rounded-full border-4 border-white object-cover shadow-sm"
                                        src="{{ $admin->avatar_url }}" data-fallback="{{ $admin->fallback_avatar_url }}"
                                        onerror="this.onerror=null;this.src='{{ $admin->fallback_avatar_url }}';"
                                        alt="admin avatar">
                                    <button type="button" id="trigger_avatar_upload"
                                        class="absolute -bottom-1 -right-1 grid h-8 w-8 place-items-center rounded-full border-2 border-white bg-indigo-600 text-white shadow transition hover:bg-indigo-500"
                                        aria-label="Upload avatar">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6Z" />
                                            <path
                                                d="M20 4h-3.17L15.6 2.35A2 2 0 0 0 14.06 2H9.94A2 2 0 0 0 8.4 2.35L7.17 4H4a2 2 0 0 0-2 2v10a4 4 0 0 0 4 4h12a4 4 0 0 0 4-4V6a2 2 0 0 0-2-2Zm-8 13a5 5 0 1 1 0-10 5 5 0 0 1 0 10Z" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="flex flex-wrap items-center gap-2">
                                    <input id="admin_avatar_input" type="file" name="avatar" accept="image/*"
                                        class="hidden">
                                    <button type="button" id="upload_avatar_btn"
                                        class="rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-indigo-500">
                                        Upload New
                                    </button>
                                    <button type="button" id="delete_avatar_btn"
                                        class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">
                                        Delete avatar
                                    </button>
                                    <span class="text-xs text-slate-500">JPG, PNG, WEBP up to 4MB.</span>
                                </div>
                            </div>

                            <div class="mt-5 grid gap-4 md:grid-cols-2">
                                <div>
                                    <label for="first_name" class="mb-1 block text-xs font-semibold text-slate-600">First
                                        Name
                                        <span class="text-red-500">*</span></label>
                                    <input id="first_name" name="first_name" type="text"
                                        value="{{ old('first_name', $defaultFirstName) }}" required
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                    @error('first_name', 'profileUpdate')
                                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="last_name" class="mb-1 block text-xs font-semibold text-slate-600">Last
                                        Name</label>
                                    <input id="last_name" name="last_name" type="text"
                                        value="{{ old('last_name', $defaultLastName) }}"
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                    @error('last_name', 'profileUpdate')
                                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email"
                                        class="mb-1 block text-xs font-semibold text-slate-600">Email</label>
                                    <input id="email" name="email" type="email"
                                        value="{{ old('email', $admin->email) }}" required
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                    @error('email', 'profileUpdate')
                                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone_number"
                                        class="mb-1 block text-xs font-semibold text-slate-600">Mobile
                                        Number</label>
                                    <input id="phone_number" name="phone_number" type="text"
                                        value="{{ old('phone_number', $admin->phone_number) }}"
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                                        placeholder="+855 ...">
                                    @error('phone_number', 'profileUpdate')
                                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-600">Role</label>
                                    <input type="text" readonly value="{{ ucfirst((string) $admin->role) }}"
                                        class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-600">
                                </div>

                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-600">ID</label>
                                    <input type="text" readonly value="ID #{{ $admin->formatted_id }}"
                                        class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-600">
                                </div>
                            </div>

                            <div class="mt-6">
                                <button type="submit"
                                    class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </section>

                    <section data-settings-panel="password" class="hidden space-y-6">
                        <form method="POST" action="{{ route('admin.settings.password.update') }}"
                            class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 sm:p-6">
                            @csrf
                            @method('PUT')

                            <h2 class="text-lg font-semibold text-slate-900">Change Password</h2>
                            <p class="mt-1 text-sm text-slate-500">Use a strong password with at least 8 characters.</p>

                            <div class="mt-5 space-y-4">
                                <div>
                                    <label for="current_password"
                                        class="mb-1 block text-xs font-semibold text-slate-600">Current
                                        Password</label>
                                    <input id="current_password" type="password" name="current_password" required
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                    @error('current_password', 'passwordUpdate')
                                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label for="password" class="mb-1 block text-xs font-semibold text-slate-600">New
                                            Password</label>
                                        <input id="password" type="password" name="password" required
                                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                        @error('password', 'passwordUpdate')
                                            <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="password_confirmation"
                                            class="mb-1 block text-xs font-semibold text-slate-600">Confirm
                                            Password</label>
                                        <input id="password_confirmation" type="password" name="password_confirmation"
                                            required
                                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6">
                                <button type="submit"
                                    class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </section>

                    <section data-settings-panel="notifications" class="hidden">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-6">
                            <h2 class="text-lg font-semibold text-slate-900">Notifications</h2>
                            <p class="mt-2 text-sm text-slate-600">Unread notifications:
                                <span
                                    class="font-semibold text-indigo-700">{{ number_format($stats['unreadNotifications'] ?? 0) }}</span>
                            </p>
                            <p class="mt-1 text-sm text-slate-500">This section is ready for notification preferences.</p>
                        </div>
                    </section>

                    <section data-settings-panel="navbar-page" class="hidden space-y-6">
                        @if ($homePageReady ?? false)
                            <form method="POST" action="{{ route('admin.settings.navbar-page.update') }}"
                                enctype="multipart/form-data" class="space-y-5">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="_settings_tab" value="navbar-page">
                                <input type="hidden" id="remove_home_brand_logo" name="brand[remove_logo]"
                                    value="0">

                                <div
                                    class="overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white shadow-sm ring-1 ring-slate-100">
                                    <div class="border-b border-slate-200 p-5 sm:p-7">
                                        <div class="flex flex-wrap items-start justify-between gap-4">
                                            <div>
                                                <span
                                                    class="inline-flex rounded-full bg-indigo-50 px-3 py-1 text-[11px] font-black uppercase tracking-[0.2em] text-indigo-700 ring-1 ring-indigo-100">
                                                    Navbar
                                                </span>
                                                <h2 class="mt-3 text-2xl font-black tracking-tight text-slate-950">Navbar Page Builder</h2>
                                                <p class="mt-2 max-w-2xl text-sm leading-7 text-slate-500">
                                                    Update the public logo, school name, tagline, and home navigation links from one place.
                                                </p>
                                            </div>
                                            <button type="submit"
                                                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-indigo-500">
                                                Save Navbar
                                            </button>
                                        </div>
                                    </div>

                                    <div class="grid gap-6 p-5 sm:p-7 xl:grid-cols-[18rem_minmax(0,1fr)]">
                                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50/80 p-4 shadow-sm">
                                            <p class="mb-3 text-xs font-black uppercase tracking-[0.18em] text-slate-500">Logo</p>
                                            <img id="home_brand_logo_preview" src="{{ $homeBrandLogo }}"
                                                alt="Brand logo preview"
                                                class="h-48 w-full rounded-2xl bg-slate-950 object-contain p-6 shadow-sm">
                                            <input id="home_brand_logo_input" type="file" name="brand[logo]"
                                                accept="image/*" class="hidden">
                                            <div class="mt-4 grid gap-2">
                                                <button type="button" id="upload_home_brand_logo_btn"
                                                    class="rounded-xl bg-slate-900 px-4 py-3 text-xs font-bold text-white transition hover:bg-slate-800">
                                                    Upload Logo
                                                </button>
                                                <button type="button" id="delete_home_brand_logo_btn"
                                                    class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs font-bold text-slate-700 transition hover:bg-slate-100">
                                                    Use Default
                                                </button>
                                            </div>
                                        </div>

                                        <div class="grid content-start gap-5">
                                            <div class="grid gap-5 md:grid-cols-2">
                                                <div>
                                                    <label for="home_brand_name"
                                                        class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">School
                                                        name</label>
                                                    <input id="home_brand_name" name="brand[name]" type="text"
                                                        value="{{ old('brand.name', $homeBrand?->title ?? 'TechBridge') }}"
                                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                </div>
                                                <div>
                                                    <label for="home_brand_description"
                                                        class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Brand
                                                        description</label>
                                                    <input id="home_brand_description" name="brand[description]" type="text"
                                                        value="{{ old('brand.description', $homeBrand?->description ?? __('home.brand.tagline')) }}"
                                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                </div>
                                            </div>

                                            <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50/70 p-4">
                                                <div class="flex flex-wrap items-center justify-between gap-3">
                                                    <div>
                                                        <h3 class="text-base font-black text-slate-900">Navbar Links</h3>
                                                        <p class="mt-1 text-xs text-slate-500">Links shown in the home page header dropdown.</p>
                                                    </div>
                                                    <button type="button" data-add-home-card="navbar-links"
                                                        class="rounded-full bg-indigo-600 px-3 py-1 text-xs font-bold text-white transition hover:bg-indigo-500">
                                                        Add Link
                                                    </button>
                                                </div>
                                                <div class="mt-4 grid gap-3 md:grid-cols-2">
                                                    @for ($i = 0; $i < $navbarLinkLimit; $i++)
                                                        @php
                                                            $navbarLink = $navbarLinks->get($i);
                                                            $navbarLabel = old('navbar_links.' . $i . '.label', $navbarLink?->title ?? '');
                                                            $navbarHref = old('navbar_links.' . $i . '.href', $navbarLink?->value ?? '');
                                                            $showNavbarLink = filled($navbarLabel) || filled($navbarHref) || $i < 4;
                                                        @endphp
                                                        <div data-home-editor-card data-addable-card="navbar-links"
                                                            class="{{ $showNavbarLink ? '' : 'hidden' }} rounded-2xl border border-slate-200 bg-white p-3">
                                                            <input type="hidden" name="navbar_links[{{ $i }}][id]"
                                                                value="{{ old('navbar_links.' . $i . '.id', $navbarLink?->id) }}">
                                                            <input type="hidden" data-home-delete-field
                                                                name="navbar_links[{{ $i }}][delete]" value="0">
                                                            <div class="mb-3 flex items-center justify-between gap-2">
                                                                <p class="text-xs font-black text-slate-500">Navbar link {{ $i + 1 }}</p>
                                                                <div class="flex items-center gap-2">
                                                                    <label
                                                                        class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white px-2.5 py-1 text-xs font-bold text-slate-600">
                                                                        <input type="checkbox" data-home-active-toggle name="navbar_links[{{ $i }}][active]"
                                                                            value="1"
                                                                            @checked(old('navbar_links.' . $i . '.active', $navbarLink?->is_active ?? true))
                                                                            class="rounded border-slate-300 text-indigo-600">
                                                                        Active
                                                                    </label>
                                                                    <button type="button" data-home-remove-card
                                                                        class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600 transition hover:bg-red-50">
                                                                        Remove
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="grid gap-2">
                                                                <input data-navbar-label-input name="navbar_links[{{ $i }}][label]" type="text"
                                                                    value="{{ $navbarLabel }}" placeholder="Label"
                                                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                                <select data-navbar-page-select name="navbar_links[{{ $i }}][href]"
                                                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                                    <option value="">Select page</option>
                                                                    @foreach ($navbarPageOptions as $pageHref => $pageLabel)
                                                                        <option value="{{ $pageHref }}" data-label="{{ $pageLabel }}"
                                                                            @selected($navbarHref === $pageHref)>
                                                                            {{ $pageLabel }}
                                                                        </option>
                                                                    @endforeach
                                                                    @if (filled($navbarHref) && !array_key_exists($navbarHref, $navbarPageOptions))
                                                                        <option value="{{ $navbarHref }}" selected>{{ $navbarHref }}</option>
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        </div>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @else
                            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-800">
                                Home page tables are not available yet. Run migrations, then refresh this page.
                            </div>
                        @endif
                    </section>

                    <section data-settings-panel="home-page" class="hidden space-y-6">
                        @if ($homePageReady ?? false)
                            <form method="POST" action="{{ route('admin.settings.home-page.update') }}"
                                enctype="multipart/form-data" class="space-y-5">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="_settings_tab" value="home-page">
                                <input type="hidden" id="remove_home_hero_image" name="hero[remove_image]"
                                    value="0">

                                <div
                                    class="overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white shadow-sm ring-1 ring-slate-100">
                                    <div class="grid gap-0 xl:grid-cols-[minmax(0,1fr)_420px]">
                                        <div class="p-5 sm:p-7">
                                            <div
                                                class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-200 pb-6">
                                                <div>
                                                    <span
                                                        class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-[11px] font-black uppercase tracking-[0.2em] text-blue-700 ring-1 ring-blue-100">
                                                        Website content
                                                    </span>
                                                    <h2 class="mt-3 text-2xl font-black tracking-tight text-slate-950">Hero Page Builder
                                                    </h2>
                                                    <p class="mt-2 max-w-2xl text-sm leading-7 text-slate-500">
                                                        Edit the homepage hero content, image, stats, and feature strip from
                                                        one screen.
                                                    </p>
                                                </div>
                                                <div class="flex flex-wrap gap-2">
                                                    <a href="{{ route('home') }}" target="_blank"
                                                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50">
                                                        View Home
                                                    </a>
                                                    <button type="submit"
                                                        class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-indigo-500">
                                                        Save Changes
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="mt-6 grid gap-5 md:grid-cols-2">
                                                <div class="md:col-span-2">
                                                    <label for="home_hero_title"
                                                        class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Main
                                                        headline</label>
                                                    <input id="home_hero_title" name="hero[title]" type="text"
                                                        data-home-preview-target="home_preview_title"
                                                        value="{{ old('hero.title', $homeHero?->title ?? 'A Smarter school experience for Every Students, Every Day.') }}"
                                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                </div>
                                                <div>
                                                    <label for="home_hero_badge"
                                                        class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Top
                                                        badge</label>
                                                    <input id="home_hero_badge" name="hero[badge]" type="text"
                                                        data-home-preview-target="home_preview_badge"
                                                        value="{{ old('hero.badge', $homeHero?->subtitle ?? 'Future-ready school platform') }}"
                                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                </div>
                                                <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                                                    <label
                                                        class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Hero
                                                        image</label>
                                                    <input id="home_hero_image_input" type="file" name="hero[image]"
                                                        accept="image/*" class="hidden">
                                                    <div class="mt-2 grid gap-2 sm:grid-cols-2 md:grid-cols-1 lg:grid-cols-2">
                                                        <button type="button" id="upload_home_hero_image_btn"
                                                            class="rounded-xl bg-slate-900 px-4 py-3 text-xs font-bold text-white transition hover:bg-slate-800">
                                                            Upload Image
                                                        </button>
                                                        <button type="button" id="delete_home_hero_image_btn"
                                                            class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs font-bold text-slate-700 transition hover:bg-slate-100">
                                                            Use Default
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="md:col-span-2">
                                                    <label for="home_hero_description"
                                                        class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Intro
                                                        paragraph</label>
                                                    <textarea id="home_hero_description" name="hero[description]" rows="4"
                                                        data-home-preview-target="home_preview_description"
                                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm leading-6 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ old('hero.description', $homeHero?->description ?? 'TechBridge brings academics, admissions, attendance, communication, and daily school operations together in one seamless platform for students, parents, teachers, and school leaders.') }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <aside
                                            class="border-t border-slate-200 bg-slate-950 p-5 text-white xl:border-l xl:border-t-0">
                                            <div class="sticky top-24 rounded-[1.5rem] bg-white p-4 text-slate-950 shadow-lg">
                                                <p class="mb-3 text-[10px] font-black uppercase tracking-[0.24em] text-slate-400">Live preview</p>
                                                <img id="home_hero_image_preview" src="{{ $homePageImage }}"
                                                    class="h-56 w-full rounded-2xl object-cover" alt="home hero preview">
                                                <div class="mt-5">
                                                    <p id="home_preview_badge"
                                                        class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-600">
                                                        {{ old('hero.badge', $homeHero?->subtitle ?? 'Future-ready school platform') }}
                                                    </p>
                                                    <h3 id="home_preview_title"
                                                        class="mt-2 text-2xl font-black leading-tight tracking-tight text-slate-950">
                                                        {{ old('hero.title', $homeHero?->title ?? 'A Smarter school experience for Every Students, Every Day.') }}
                                                    </h3>
                                                    <p id="home_preview_description"
                                                        class="mt-3 line-clamp-4 text-xs leading-5 text-slate-500">
                                                        {{ old('hero.description', $homeHero?->description ?? 'TechBridge brings academics, admissions, attendance, communication, and daily school operations together in one seamless platform for students, parents, teachers, and school leaders.') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </aside>
                                    </div>
                                </div>

                                <div class="grid gap-5 xl:grid-cols-2">
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                        <div class="flex items-center justify-between gap-3">
                                            <div>
                                                <h3 class="text-base font-black text-slate-900">Check Points</h3>
                                                <p class="mt-1 text-xs text-slate-500">Three short benefits under the hero
                                                    button.</p>
                                            </div>
                                            <button type="button" data-add-home-card="hero-points"
                                                class="rounded-full bg-blue-600 px-3 py-1 text-xs font-bold text-white transition hover:bg-blue-500">
                                                Add Point
                                            </button>
                                        </div>
                                        <div class="mt-4 space-y-3">
                                            @for ($i = 0; $i < $pointLimit; $i++)
                                                @php
                                                    $pointItem = $homePoints->get($i);
                                                    $pointDescription = old(
                                                        'points.' . $i . '.description',
                                                        $pointItem?->description ?? '',
                                                    );
                                                    $pointIcon = old('points.' . $i . '.icon', $pointItem?->icon ?? '');
                                                    $showPoint = filled($pointDescription);
                                                @endphp
                                                <div data-home-editor-card data-addable-card="hero-points"
                                                    class="{{ $showPoint ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                    <input type="hidden" name="points[{{ $i }}][id]"
                                                        value="{{ $pointItem?->id }}">
                                                    <input type="hidden" name="points[{{ $i }}][delete]"
                                                        data-home-delete-field value="0">
                                                    <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                                                        <span
                                                            class="flex items-center gap-2 text-xs font-bold text-slate-500">
                                                            <span
                                                                class="grid h-6 w-6 place-items-center rounded-full bg-blue-600 text-[11px] text-white">{{ $i + 1 }}</span>
                                                            Point {{ $i + 1 }}
                                                        </span>
                                                        <div data-home-card-actions
                                                            class="flex flex-wrap items-center gap-2">
                                                            <button type="button" data-home-edit-card
                                                                class="rounded-full border border-blue-200 bg-white px-3 py-1 text-xs font-bold text-blue-600 transition hover:bg-blue-50">
                                                                Edit
                                                            </button>
                                                            <label
                                                                class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                                                                <input type="hidden"
                                                                    name="points[{{ $i }}][active]"
                                                                    value="0">
                                                                <input type="checkbox"
                                                                    name="points[{{ $i }}][active]"
                                                                    data-home-active-toggle value="1"
                                                                    {{ old('points.' . $i . '.active', $pointItem?->is_active ?? true) ? 'checked' : '' }}
                                                                    class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                                Active
                                                            </label>
                                                            <button type="button" data-home-remove-card
                                                                class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600 transition hover:bg-red-50">
                                                                Remove
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <textarea name="points[{{ $i }}][description]" rows="2"
                                                        class="w-full resize-none rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm leading-6 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ $pointDescription }}</textarea>
                                                    <input name="points[{{ $i }}][icon]" type="text"
                                                        data-random-icon-input
                                                        placeholder='Icon name, <i class="fa-solid fa-school"></i>, or SVG'
                                                        value="{{ $pointIcon }}"
                                                        class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                </div>
                                            @endfor
                                        </div>
                                    </div>

                                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                        <div class="flex items-center justify-between gap-3">
                                            <div>
                                                <h3 class="text-base font-black text-slate-900">Floating Stats</h3>
                                                <p class="mt-1 text-xs text-slate-500">Cards shown over the hero image.</p>
                                            </div>
                                            <button type="button" data-add-home-card="hero-stats"
                                                class="rounded-full bg-emerald-600 px-3 py-1 text-xs font-bold text-white transition hover:bg-emerald-500">
                                                Add Stat
                                            </button>
                                        </div>
                                        <div class="mt-4 grid gap-3">
                                            @for ($i = 0; $i < $statLimit; $i++)
                                                @php
                                                    $statItem = $homeStats->get($i);
                                                    $statLabel = old('stats.' . $i . '.label', $statItem?->title ?? '');
                                                    $statValue = old('stats.' . $i . '.value', $statItem?->value ?? '');
                                                    $statTrend = old(
                                                        'stats.' . $i . '.trend',
                                                        $statItem?->subtitle ?? '',
                                                    );
                                                    $statIcon = old(
                                                        'stats.' . $i . '.icon',
                                                        $statItem?->icon ??
                                                            match ($i) {
                                                                0 => 'graduation',
                                                                1 => 'chart',
                                                                2 => 'users',
                                                                default => 'clock',
                                                            },
                                                    );
                                                    $statColor = old(
                                                        'stats.' . $i . '.color',
                                                        $iconColorValue(
                                                            $statItem?->color,
                                                            $heroStatColorDefaults[$i % count($heroStatColorDefaults)],
                                                        ),
                                                    );
                                                    $showStat =
                                                        filled($statLabel) || filled($statValue) || filled($statTrend);
                                                @endphp
                                                <div data-home-editor-card data-addable-card="hero-stats"
                                                    class="{{ $showStat ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                    <input type="hidden" name="stats[{{ $i }}][id]"
                                                        value="{{ $statItem?->id }}">
                                                    <input type="hidden" name="stats[{{ $i }}][delete]"
                                                        data-home-delete-field value="0">
                                                    <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                                                        <span
                                                            class="flex items-center gap-2 text-xs font-bold text-slate-500">
                                                            <span
                                                                class="grid h-6 w-6 place-items-center rounded-full bg-emerald-500 text-[11px] text-white">{{ $i + 1 }}</span>
                                                            Stat card {{ $i + 1 }}
                                                        </span>
                                                        <div data-home-card-actions
                                                            class="flex flex-wrap items-center gap-2">
                                                            <button type="button" data-home-edit-card
                                                                class="rounded-full border border-blue-200 bg-white px-3 py-1 text-xs font-bold text-blue-600 transition hover:bg-blue-50">
                                                                Edit
                                                            </button>
                                                            <label
                                                                class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                                                                <input type="hidden"
                                                                    name="stats[{{ $i }}][active]"
                                                                    value="0">
                                                                <input type="checkbox"
                                                                    name="stats[{{ $i }}][active]"
                                                                    data-home-active-toggle value="1"
                                                                    {{ old('stats.' . $i . '.active', $statItem?->is_active ?? true) ? 'checked' : '' }}
                                                                    class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                                Active
                                                            </label>
                                                            <button type="button" data-home-remove-card
                                                                class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600 transition hover:bg-red-50">
                                                                Remove
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="grid gap-2 sm:grid-cols-[minmax(0,1fr)_96px_130px]">
                                                        <input name="stats[{{ $i }}][label]" type="text"
                                                            placeholder="Label" value="{{ $statLabel }}"
                                                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                        <input name="stats[{{ $i }}][value]" type="text"
                                                            placeholder="Value" value="{{ $statValue }}"
                                                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                        <input name="stats[{{ $i }}][trend]" type="text"
                                                            placeholder="+6% this year" value="{{ $statTrend }}"
                                                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                    </div>
                                                    <div class="mt-2 grid gap-2 sm:grid-cols-[minmax(0,1fr)_150px]">
                                                        <div>
                                                            <label
                                                                class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">
                                                                Icon
                                                            </label>
                                                            <input name="stats[{{ $i }}][icon]" type="text"
                                                                data-stat-icon-select data-random-icon-input
                                                                placeholder='Icon name, <i class="fa-solid fa-school"></i>, or SVG'
                                                                value="{{ $statIcon }}"
                                                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                        </div>
                                                        <label
                                                            class="flex items-center gap-3 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600">
                                                            <span class="min-w-0 flex-1">Icon color</span>
                                                            <input name="stats[{{ $i }}][color]"
                                                                data-stat-color-input
                                                                type="color" value="{{ $statColor }}"
                                                                class="h-10 w-12 rounded-md border border-slate-300 bg-white p-1">
                                                        </label>
                                                    </div>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <div>
                                            <h3 class="text-base font-black text-slate-900">Bottom Feature Strip</h3>
                                            <p class="mt-1 text-xs text-slate-500">Four feature cards displayed below the
                                                hero header.</p>
                                        </div>
                                        <button type="button" data-add-home-card="hero-features"
                                            class="rounded-full bg-violet-600 px-3 py-1 text-xs font-bold text-white transition hover:bg-violet-500">
                                            Add Feature
                                        </button>
                                    </div>
                                    <div class="mt-4 grid gap-3 xl:grid-cols-2">
                                        @for ($i = 0; $i < $heroFeatureLimit; $i++)
                                            @php
                                                $heroFeatureItem = $homeFeatures->get($i);
                                                $heroFeatureTitle = old(
                                                    'features.' . $i . '.title',
                                                    $heroFeatureItem?->title ?? '',
                                                );
                                                $heroFeatureDescription = old(
                                                    'features.' . $i . '.description',
                                                    $heroFeatureItem?->description ?? '',
                                                );
                                                $heroFeatureIcon = old(
                                                    'features.' . $i . '.icon',
                                                    $heroFeatureItem?->icon ?? '',
                                                );
                                                $heroFeatureColor = old(
                                                    'features.' . $i . '.color',
                                                    $iconColorValue(
                                                        $heroFeatureItem?->color,
                                                        $heroFeatureColorDefaults[$i % count($heroFeatureColorDefaults)],
                                                    ),
                                                );
                                                $showHeroFeature =
                                                    filled($heroFeatureTitle) || filled($heroFeatureDescription);
                                            @endphp
                                            <div data-home-editor-card data-addable-card="hero-features"
                                                class="{{ $showHeroFeature ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                <input type="hidden" name="features[{{ $i }}][id]"
                                                    value="{{ $heroFeatureItem?->id }}">
                                                <input type="hidden" name="features[{{ $i }}][delete]"
                                                    data-home-delete-field value="0">
                                                <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                                                    <span class="flex items-center gap-2 text-xs font-bold text-slate-500">
                                                        <span
                                                            class="grid h-6 w-6 place-items-center rounded-full bg-violet-600 text-[11px] text-white">{{ $i + 1 }}</span>
                                                        Feature {{ $i + 1 }}
                                                    </span>
                                                    <div data-home-card-actions class="flex flex-wrap items-center gap-2">
                                                        <button type="button" data-home-edit-card
                                                            class="rounded-full border border-blue-200 bg-white px-3 py-1 text-xs font-bold text-blue-600 transition hover:bg-blue-50">
                                                            Edit
                                                        </button>
                                                        <label
                                                            class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                                                            <input type="hidden"
                                                                name="features[{{ $i }}][active]"
                                                                value="0">
                                                            <input type="checkbox"
                                                                name="features[{{ $i }}][active]"
                                                                data-home-active-toggle value="1"
                                                                {{ old('features.' . $i . '.active', $heroFeatureItem?->is_active ?? true) ? 'checked' : '' }}
                                                                class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                            Active
                                                        </label>
                                                        <button type="button" data-home-remove-card
                                                            class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600 transition hover:bg-red-50">
                                                            Remove
                                                        </button>
                                                    </div>
                                                </div>
                                                <input name="features[{{ $i }}][title]" type="text"
                                                    placeholder="Feature title" value="{{ $heroFeatureTitle }}"
                                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                <textarea name="features[{{ $i }}][description]" rows="3" placeholder="Feature description"
                                                    class="mt-2 w-full resize-none rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm leading-6 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ $heroFeatureDescription }}</textarea>
                                                <input name="features[{{ $i }}][icon]" type="text"
                                                    data-random-icon-input
                                                    placeholder='Icon name, <i class="fa-solid fa-school"></i>, or SVG'
                                                    value="{{ $heroFeatureIcon }}"
                                                    class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                <label
                                                    class="mt-2 flex items-center gap-3 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600">
                                                    <span class="min-w-0 flex-1">Icon color</span>
                                                    <input name="features[{{ $i }}][color]"
                                                        type="color" value="{{ $heroFeatureColor }}"
                                                        class="h-10 w-12 rounded-md border border-slate-300 bg-white p-1">
                                                </label>
                                            </div>
                                        @endfor
                                    </div>
                                </div>

                                <div class="sticky bottom-4 z-10 flex justify-end">
                                    <div
                                        class="rounded-2xl border border-slate-200 bg-white/95 p-2 shadow-xl backdrop-blur">
                                        <button type="submit"
                                            class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-indigo-500">
                                            Save Hero Page
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-800">
                                Run migrations to enable dynamic homepage editing.
                            </div>
                        @endif
                    </section>

                    <section data-settings-panel="about-page" class="hidden space-y-6">
                        @if ($homePageReady ?? false)
                            <form method="POST" action="{{ route('admin.settings.about-page.update') }}"
                                class="space-y-5">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="_settings_tab" value="about-page">

                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div>
                                            <span
                                                class="inline-flex rounded-full bg-cyan-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-cyan-700">
                                                About section
                                            </span>
                                            <h3 class="mt-3 text-base font-black text-slate-900">About Content</h3>
                                            <p class="mt-1 text-xs text-slate-500">Main About text, highlights, mission,
                                                vision, culture, and operations cards.</p>
                                        </div>
                                        <span
                                            class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">Visible
                                            on homepage</span>
                                    </div>

                                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                                        <div>
                                            <label for="home_about_badge"
                                                class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Badge</label>
                                            <input id="home_about_badge" name="about[badge]" type="text"
                                                value="{{ old('about.badge', $homeAbout?->subtitle ?? 'About TechBridge') }}"
                                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                        </div>
                                        <div>
                                            <label for="home_about_title"
                                                class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Headline</label>
                                            <input id="home_about_title" name="about[title]" type="text"
                                                value="{{ old('about.title', $homeAbout?->title ?? __('home.about.title')) }}"
                                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label for="home_about_description"
                                                class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Description</label>
                                            <textarea id="home_about_description" name="about[description]" rows="4"
                                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm leading-6 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ old('about.description', $homeAbout?->description ?? __('home.about.description')) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid gap-5 xl:grid-cols-2">
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                        <div class="flex items-center justify-between gap-3">
                                            <div>
                                                <h3 class="text-base font-black text-slate-900">About Highlights</h3>
                                                <p class="mt-1 text-xs text-slate-500">Small cards inside the dark
                                                    About panel.</p>
                                            </div>
                                            <button type="button" data-add-home-card="about-highlights"
                                                class="rounded-full bg-cyan-600 px-3 py-1 text-xs font-bold text-white transition hover:bg-cyan-500">
                                                Add Highlight
                                            </button>
                                        </div>
                                        <div class="mt-4 space-y-3">
                                            @for ($i = 0; $i < $aboutHighlightLimit; $i++)
                                                @php
                                                    $aboutHighlightItem = $homeAboutHighlights->get($i);
                                                    $aboutHighlightTitle = old(
                                                        'about_highlights.' . $i . '.title',
                                                        $aboutHighlightItem?->title ?? '',
                                                    );
                                                    $aboutHighlightDescription = old(
                                                        'about_highlights.' . $i . '.description',
                                                        $aboutHighlightItem?->description ?? '',
                                                    );
                                                    $aboutHighlightIcon = old(
                                                        'about_highlights.' . $i . '.icon',
                                                        $aboutHighlightItem?->icon ?? '',
                                                    );
                                                    $aboutHighlightColor = old(
                                                        'about_highlights.' . $i . '.color',
                                                        $iconColorValue(
                                                            $aboutHighlightItem?->color,
                                                            $aboutHighlightColorDefaults[$i % count($aboutHighlightColorDefaults)],
                                                        ),
                                                    );
                                                    $showAboutHighlight =
                                                        filled($aboutHighlightTitle) ||
                                                        filled($aboutHighlightDescription);
                                                @endphp
                                                <div data-home-editor-card data-addable-card="about-highlights"
                                                    class="{{ $showAboutHighlight ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                    <input type="hidden"
                                                        name="about_highlights[{{ $i }}][id]"
                                                        value="{{ $aboutHighlightItem?->id }}">
                                                    <input type="hidden"
                                                        name="about_highlights[{{ $i }}][delete]"
                                                        data-home-delete-field value="0">
                                                    <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                                                        <span
                                                            class="flex items-center gap-2 text-xs font-bold text-slate-500">
                                                            <span
                                                                class="grid h-6 w-6 place-items-center rounded-full bg-cyan-600 text-[11px] text-white">{{ $i + 1 }}</span>
                                                            Highlight {{ $i + 1 }}
                                                        </span>
                                                        <div data-home-card-actions
                                                            class="flex flex-wrap items-center gap-2">
                                                            <button type="button" data-home-edit-card
                                                                class="rounded-full border border-blue-200 bg-white px-3 py-1 text-xs font-bold text-blue-600 transition hover:bg-blue-50">
                                                                Edit
                                                            </button>
                                                            <label
                                                                class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                                                                <input type="hidden"
                                                                    name="about_highlights[{{ $i }}][active]"
                                                                    value="0">
                                                                <input type="checkbox"
                                                                    name="about_highlights[{{ $i }}][active]"
                                                                    data-home-active-toggle value="1"
                                                                    {{ old('about_highlights.' . $i . '.active', $aboutHighlightItem?->is_active ?? true) ? 'checked' : '' }}
                                                                    class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                                Active
                                                            </label>
                                                            <button type="button" data-home-remove-card
                                                                class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600 transition hover:bg-red-50">
                                                                Remove
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <input name="about_highlights[{{ $i }}][title]"
                                                        type="text" placeholder="Highlight title"
                                                        value="{{ $aboutHighlightTitle }}"
                                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                    <textarea name="about_highlights[{{ $i }}][description]" rows="3"
                                                        placeholder="Highlight description"
                                                        class="mt-2 w-full resize-none rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm leading-6 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ $aboutHighlightDescription }}</textarea>
                                                    <input name="about_highlights[{{ $i }}][icon]"
                                                        type="text" data-random-icon-input
                                                        placeholder='Icon name, <i class="fa-solid fa-school"></i>, or SVG'
                                                        value="{{ $aboutHighlightIcon }}"
                                                        class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                    <label
                                                        class="mt-2 flex items-center gap-3 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600">
                                                        <span class="min-w-0 flex-1">Icon color</span>
                                                        <input name="about_highlights[{{ $i }}][color]"
                                                            type="color" value="{{ $aboutHighlightColor }}"
                                                            class="h-10 w-12 rounded-md border border-slate-300 bg-white p-1">
                                                    </label>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>

                                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                        <div class="flex items-center justify-between gap-3">
                                            <div>
                                                <h3 class="text-base font-black text-slate-900">About Cards</h3>
                                                <p class="mt-1 text-xs text-slate-500">Cards shown on the right side of the
                                                    About section.</p>
                                            </div>
                                            <button type="button" data-add-home-card="about-cards"
                                                class="rounded-full bg-emerald-600 px-3 py-1 text-xs font-bold text-white transition hover:bg-emerald-500">
                                                Add Card
                                            </button>
                                        </div>
                                        <div class="mt-4 grid gap-3">
                                            @for ($i = 0; $i < $aboutCardLimit; $i++)
                                                @php
                                                    $aboutCardItem = $homeAboutCards->get($i);
                                                    $aboutCardTitle = old(
                                                        'about_cards.' . $i . '.title',
                                                        $aboutCardItem?->title ?? '',
                                                    );
                                                    $aboutCardDescription = old(
                                                        'about_cards.' . $i . '.description',
                                                        $aboutCardItem?->description ?? '',
                                                    );
                                                    $aboutCardIcon = old(
                                                        'about_cards.' . $i . '.icon',
                                                        $aboutCardItem?->icon ?? '',
                                                    );
                                                    $aboutCardColor = old(
                                                        'about_cards.' . $i . '.color',
                                                        $iconColorValue(
                                                            $aboutCardItem?->color,
                                                            $aboutCardColorDefaults[$i % count($aboutCardColorDefaults)],
                                                        ),
                                                    );
                                                    $showAboutCard =
                                                        filled($aboutCardTitle) || filled($aboutCardDescription);
                                                @endphp
                                                <div data-home-editor-card data-addable-card="about-cards"
                                                    class="{{ $showAboutCard ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                    <input type="hidden" name="about_cards[{{ $i }}][id]"
                                                        value="{{ $aboutCardItem?->id }}">
                                                    <input type="hidden"
                                                        name="about_cards[{{ $i }}][delete]"
                                                        data-home-delete-field value="0">
                                                    <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                                                        <span
                                                            class="flex items-center gap-2 text-xs font-bold text-slate-500">
                                                            <span
                                                                class="grid h-6 w-6 place-items-center rounded-full bg-emerald-500 text-[11px] text-white">{{ $i + 1 }}</span>
                                                            About card {{ $i + 1 }}
                                                        </span>
                                                        <div data-home-card-actions
                                                            class="flex flex-wrap items-center gap-2">
                                                            <button type="button" data-home-edit-card
                                                                class="rounded-full border border-blue-200 bg-white px-3 py-1 text-xs font-bold text-blue-600 transition hover:bg-blue-50">
                                                                Edit
                                                            </button>
                                                            <label
                                                                class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                                                                <input type="hidden"
                                                                    name="about_cards[{{ $i }}][active]"
                                                                    value="0">
                                                                <input type="checkbox"
                                                                    name="about_cards[{{ $i }}][active]"
                                                                    data-home-active-toggle value="1"
                                                                    {{ old('about_cards.' . $i . '.active', $aboutCardItem?->is_active ?? true) ? 'checked' : '' }}
                                                                    class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                                Active
                                                            </label>
                                                            <button type="button" data-home-remove-card
                                                                class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600 transition hover:bg-red-50">
                                                                Remove
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <input name="about_cards[{{ $i }}][title]" type="text"
                                                        placeholder="Card title" value="{{ $aboutCardTitle }}"
                                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                    <textarea name="about_cards[{{ $i }}][description]" rows="3" placeholder="Card description"
                                                        class="mt-2 w-full resize-none rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm leading-6 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ $aboutCardDescription }}</textarea>
                                                    <input name="about_cards[{{ $i }}][icon]" type="text"
                                                        data-random-icon-input
                                                        placeholder='Icon name, <i class="fa-solid fa-school"></i>, or SVG'
                                                        value="{{ $aboutCardIcon }}"
                                                        class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                    <label
                                                        class="mt-2 flex items-center gap-3 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600">
                                                        <span class="min-w-0 flex-1">Icon color</span>
                                                        <input name="about_cards[{{ $i }}][color]"
                                                            type="color" value="{{ $aboutCardColor }}"
                                                            class="h-10 w-12 rounded-md border border-slate-300 bg-white p-1">
                                                    </label>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                </div>

                                <div class="sticky bottom-4 z-10 flex justify-end">
                                    <div
                                        class="rounded-2xl border border-slate-200 bg-white/95 p-2 shadow-xl backdrop-blur">
                                        <button type="submit"
                                            class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-indigo-500">
                                            Save About Page
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-800">
                                Run migrations to enable dynamic about page editing.
                            </div>
                        @endif
                    </section>

                    <section data-settings-panel="feature-page" class="hidden space-y-6">
                        @if ($homePageReady ?? false)
                            <form method="POST" action="{{ route('admin.settings.feature-page.update') }}"
                                class="space-y-5">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="_settings_tab" value="feature-page">

                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div>
                                            <span
                                                class="inline-flex rounded-full bg-sky-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-sky-700">
                                                Platform features
                                            </span>
                                            <h3 class="mt-3 text-base font-black text-slate-900">Feature Section Header
                                            </h3>
                                            <p class="mt-1 text-xs text-slate-500">Badge, headline, and tag shown above the
                                                feature cards.</p>
                                        </div>
                                        <span
                                            class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">Visible
                                            on homepage</span>
                                    </div>

                                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                                        <div>
                                            <label for="platform_feature_badge"
                                                class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Badge</label>
                                            <input id="platform_feature_badge" name="feature[badge]" type="text"
                                                value="{{ old('feature.badge', $platformFeature?->subtitle ?? __('home.features.badge')) }}"
                                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                        </div>
                                        <div>
                                            <label for="platform_feature_tag"
                                                class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Tag</label>
                                            <input id="platform_feature_tag" name="feature[tag]" type="text"
                                                value="{{ old('feature.tag', $platformFeature?->value ?? __('home.features.tag')) }}"
                                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label for="platform_feature_title"
                                                class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Headline</label>
                                            <textarea id="platform_feature_title" name="feature[title]" rows="3"
                                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold leading-6 text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ old('feature.title', $platformFeature?->title ?? __('home.features.title')) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <div>
                                            <h3 class="text-base font-black text-slate-900">Feature Cards</h3>
                                            <p class="mt-1 text-xs text-slate-500">Cards displayed in the Platform
                                                Features section.</p>
                                        </div>
                                        <button type="button" data-add-home-card="platform-features"
                                            class="rounded-full bg-sky-600 px-3 py-1 text-xs font-bold text-white transition hover:bg-sky-500">
                                            Add Feature
                                        </button>
                                    </div>

                                    <div class="mt-4 grid gap-3 xl:grid-cols-2">
                                        @for ($i = 0; $i < $platformFeatureLimit; $i++)
                                            @php
                                                $platformFeatureItem = $platformFeatureCards->get($i);
                                                $platformFeatureTitle = old(
                                                    'feature_cards.' . $i . '.title',
                                                    $platformFeatureItem?->title ?? '',
                                                );
                                                $platformFeatureDescription = old(
                                                    'feature_cards.' . $i . '.description',
                                                    $platformFeatureItem?->description ?? '',
                                                );
                                                $platformFeatureIcon = old(
                                                    'feature_cards.' . $i . '.icon',
                                                    $platformFeatureItem?->icon ?? '',
                                                );
                                                $platformFeatureColor = old(
                                                    'feature_cards.' . $i . '.color',
                                                    $iconColorValue(
                                                        $platformFeatureItem?->color,
                                                        $featureCardColorDefaults[$i % count($featureCardColorDefaults)],
                                                    ),
                                                );
                                                $showPlatformFeature =
                                                    filled($platformFeatureTitle) ||
                                                    filled($platformFeatureDescription);
                                            @endphp
                                            <div data-home-editor-card data-addable-card="platform-features"
                                                class="{{ $showPlatformFeature ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                <input type="hidden" name="feature_cards[{{ $i }}][id]"
                                                    value="{{ $platformFeatureItem?->id }}">
                                                <input type="hidden" name="feature_cards[{{ $i }}][delete]"
                                                    data-home-delete-field value="0">
                                                <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                                                    <span class="flex items-center gap-2 text-xs font-bold text-slate-500">
                                                        <span
                                                            class="grid h-6 w-6 place-items-center rounded-full bg-sky-600 text-[11px] text-white">{{ $i + 1 }}</span>
                                                        Feature card {{ $i + 1 }}
                                                    </span>
                                                    <div data-home-card-actions class="flex flex-wrap items-center gap-2">
                                                        <button type="button" data-home-edit-card
                                                            class="rounded-full border border-blue-200 bg-white px-3 py-1 text-xs font-bold text-blue-600 transition hover:bg-blue-50">
                                                            Edit
                                                        </button>
                                                        <label
                                                            class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                                                            <input type="hidden"
                                                                name="feature_cards[{{ $i }}][active]"
                                                                value="0">
                                                            <input type="checkbox"
                                                                name="feature_cards[{{ $i }}][active]"
                                                                data-home-active-toggle value="1"
                                                                {{ old('feature_cards.' . $i . '.active', $platformFeatureItem?->is_active ?? true) ? 'checked' : '' }}
                                                                class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                            Active
                                                        </label>
                                                        <button type="button" data-home-remove-card
                                                            class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600 transition hover:bg-red-50">
                                                            Remove
                                                        </button>
                                                    </div>
                                                </div>
                                                <input name="feature_cards[{{ $i }}][title]" type="text"
                                                    placeholder="Feature title" value="{{ $platformFeatureTitle }}"
                                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                <textarea name="feature_cards[{{ $i }}][description]" rows="3" placeholder="Feature description"
                                                    class="mt-2 w-full resize-none rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm leading-6 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ $platformFeatureDescription }}</textarea>
                                                <input name="feature_cards[{{ $i }}][icon]" type="text"
                                                    data-random-icon-input
                                                    placeholder='Icon name, <i class="fa-solid fa-school"></i>, or SVG'
                                                    value="{{ $platformFeatureIcon }}"
                                                    class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                <label
                                                    class="mt-2 flex items-center gap-3 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600">
                                                    <span class="min-w-0 flex-1">Icon color</span>
                                                    <input name="feature_cards[{{ $i }}][color]"
                                                        type="color" value="{{ $platformFeatureColor }}"
                                                        class="h-10 w-12 rounded-md border border-slate-300 bg-white p-1">
                                                </label>
                                            </div>
                                        @endfor
                                    </div>
                                </div>

                                <div class="sticky bottom-4 z-10 flex justify-end">
                                    <div
                                        class="rounded-2xl border border-slate-200 bg-white/95 p-2 shadow-xl backdrop-blur">
                                        <button type="submit"
                                            class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-indigo-500">
                                            Save Feature Page
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-800">
                                Run migrations to enable dynamic feature page editing.
                            </div>
                        @endif
                    </section>

                    <section data-settings-panel="program-page" class="hidden space-y-6">
                        @if ($homePageReady ?? false)
                            <form method="POST" action="{{ route('admin.settings.program-page.update') }}"
                                class="space-y-5">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="_settings_tab" value="program-page">

                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div>
                                            <span
                                                class="inline-flex rounded-full bg-indigo-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-indigo-700">
                                                Academic programs
                                            </span>
                                            <h3 class="mt-3 text-base font-black text-slate-900">Program Section Header
                                            </h3>
                                            <p class="mt-1 text-xs text-slate-500">Badge, headline, and description shown
                                                above the program cards.</p>
                                        </div>
                                        <span
                                            class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">Visible
                                            on homepage</span>
                                    </div>

                                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                                        <div>
                                            <label for="program_badge"
                                                class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Badge</label>
                                            <input id="program_badge" name="program[badge]" type="text"
                                                value="{{ old('program.badge', $programSection?->subtitle ?? __('home.programs.badge')) }}"
                                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label for="program_title"
                                                class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Headline</label>
                                            <textarea id="program_title" name="program[title]" rows="2"
                                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold leading-6 text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ old('program.title', $programSection?->title ?? __('home.programs.title')) }}</textarea>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label for="program_description"
                                                class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Description</label>
                                            <textarea id="program_description" name="program[description]" rows="3"
                                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm leading-6 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ old('program.description', $programSection?->description ?? __('home.programs.description')) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <div>
                                            <h3 class="text-base font-black text-slate-900">Program Cards</h3>
                                            <p class="mt-1 text-xs text-slate-500">Academic programs displayed in the
                                                homepage Academic Programs section.</p>
                                        </div>
                                        <button type="button" data-add-home-card="program-cards"
                                            class="rounded-full bg-indigo-600 px-3 py-1 text-xs font-bold text-white transition hover:bg-indigo-500">
                                            Add Program
                                        </button>
                                    </div>

                                    <div class="mt-4 grid gap-3 xl:grid-cols-2">
                                        @for ($i = 0; $i < $programLimit; $i++)
                                            @php
                                                $programItem = $programCards->get($i);
                                                $programLevel = old(
                                                    'program_cards.' . $i . '.level',
                                                    $programItem?->subtitle ?? '',
                                                );
                                                $programTitle = old(
                                                    'program_cards.' . $i . '.title',
                                                    $programItem?->title ?? '',
                                                );
                                                $programDescription = old(
                                                    'program_cards.' . $i . '.description',
                                                    $programItem?->description ?? '',
                                                );
                                                $programIcon = old(
                                                    'program_cards.' . $i . '.icon',
                                                    $programItem?->icon ?? '',
                                                );
                                                $programColor = old(
                                                    'program_cards.' . $i . '.color',
                                                    $iconColorValue(
                                                        $programItem?->color,
                                                        $programCardColorDefaults[$i % count($programCardColorDefaults)],
                                                    ),
                                                );
                                                $showProgram =
                                                    filled($programLevel) ||
                                                    filled($programTitle) ||
                                                    filled($programDescription);
                                            @endphp
                                            <div data-home-editor-card data-addable-card="program-cards"
                                                class="{{ $showProgram ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                <input type="hidden" name="program_cards[{{ $i }}][id]"
                                                    value="{{ $programItem?->id }}">
                                                <input type="hidden" name="program_cards[{{ $i }}][delete]"
                                                    data-home-delete-field value="0">
                                                <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                                                    <span class="flex items-center gap-2 text-xs font-bold text-slate-500">
                                                        <span
                                                            class="grid h-6 w-6 place-items-center rounded-full bg-indigo-600 text-[11px] text-white">{{ $i + 1 }}</span>
                                                        Program card {{ $i + 1 }}
                                                    </span>
                                                    <div data-home-card-actions class="flex flex-wrap items-center gap-2">
                                                        <button type="button" data-home-edit-card
                                                            class="rounded-full border border-blue-200 bg-white px-3 py-1 text-xs font-bold text-blue-600 transition hover:bg-blue-50">
                                                            Edit
                                                        </button>
                                                        <label
                                                            class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                                                            <input type="hidden"
                                                                name="program_cards[{{ $i }}][active]"
                                                                value="0">
                                                            <input type="checkbox"
                                                                name="program_cards[{{ $i }}][active]"
                                                                data-home-active-toggle value="1"
                                                                {{ old('program_cards.' . $i . '.active', $programItem?->is_active ?? true) ? 'checked' : '' }}
                                                                class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                            Active
                                                        </label>
                                                        <button type="button" data-home-remove-card
                                                            class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600 transition hover:bg-red-50">
                                                            Remove
                                                        </button>
                                                    </div>
                                                </div>
                                                <input name="program_cards[{{ $i }}][level]" type="text"
                                                    placeholder="Level, example: Primary" value="{{ $programLevel }}"
                                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                <input name="program_cards[{{ $i }}][title]" type="text"
                                                    placeholder="Program title" value="{{ $programTitle }}"
                                                    class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                <textarea name="program_cards[{{ $i }}][description]" rows="3" placeholder="Program description"
                                                    class="mt-2 w-full resize-none rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm leading-6 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ $programDescription }}</textarea>
                                                <input name="program_cards[{{ $i }}][icon]" type="text"
                                                    data-random-icon-input
                                                    placeholder='Icon name, <i class="fa-solid fa-book-open"></i>, or SVG'
                                                    value="{{ $programIcon }}"
                                                    class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                <label
                                                    class="mt-2 flex items-center gap-3 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600">
                                                    <span class="min-w-0 flex-1">Icon color</span>
                                                    <input name="program_cards[{{ $i }}][color]"
                                                        type="color" value="{{ $programColor }}"
                                                        class="h-10 w-12 rounded-md border border-slate-300 bg-white p-1">
                                                </label>
                                            </div>
                                        @endfor
                                    </div>
                                </div>

                                <div class="sticky bottom-4 z-10 flex justify-end">
                                    <div
                                        class="rounded-2xl border border-slate-200 bg-white/95 p-2 shadow-xl backdrop-blur">
                                        <button type="submit"
                                            class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-indigo-500">
                                            Save Program Page
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-800">
                                Run migrations to enable dynamic program page editing.
                            </div>
                        @endif
                    </section>

                    <section data-settings-panel="facility-page" class="hidden space-y-6">
                        @if ($homePageReady ?? false)
                            <form method="POST" action="{{ route('admin.settings.facility-page.update') }}"
                                enctype="multipart/form-data" class="space-y-5">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="_settings_tab" value="facility-page">

                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div>
                                            <span
                                                class="inline-flex rounded-full bg-cyan-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-cyan-700">
                                                Campus and facilities
                                            </span>
                                            <h3 class="mt-3 text-base font-black text-slate-900">Facility Section Header
                                            </h3>
                                            <p class="mt-1 text-xs text-slate-500">Image, badge, headline, and description
                                                shown in the Campus and Facilities section.</p>
                                        </div>
                                        <span
                                            class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">Visible
                                            on homepage</span>
                                    </div>

                                    <div class="mt-5 grid gap-5 lg:grid-cols-[18rem_minmax(0,1fr)]">
                                        <div>
                                            <img id="facility_image_preview" src="{{ $facilityImage }}"
                                                alt="Facility section preview"
                                                class="h-52 w-full rounded-2xl border border-slate-200 object-cover shadow-sm">
                                            <input id="facility_image_input" name="facility[image]" type="file"
                                                accept="image/*" class="hidden">
                                            <input id="remove_facility_image" name="facility[remove_image]"
                                                type="hidden" value="0">
                                            <div class="mt-3 flex flex-wrap gap-2">
                                                <button id="upload_facility_image_btn" type="button"
                                                    class="rounded-full bg-cyan-600 px-3 py-1.5 text-xs font-bold text-white transition hover:bg-cyan-500">
                                                    Upload Image
                                                </button>
                                                <button id="delete_facility_image_btn" type="button"
                                                    class="rounded-full border border-red-200 bg-white px-3 py-1.5 text-xs font-bold text-red-600 transition hover:bg-red-50">
                                                    Remove
                                                </button>
                                            </div>
                                        </div>

                                        <div class="grid gap-4 md:grid-cols-2">
                                            <div>
                                                <label for="facility_badge"
                                                    class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Badge</label>
                                                <input id="facility_badge" name="facility[badge]" type="text"
                                                    value="{{ old('facility.badge', $facilitySection?->subtitle ?? __('home.facilities.badge')) }}"
                                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label for="facility_title"
                                                    class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Headline</label>
                                                <textarea id="facility_title" name="facility[title]" rows="2"
                                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold leading-6 text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ old('facility.title', $facilitySection?->title ?? __('home.facilities.title')) }}</textarea>
                                            </div>
                                            <div class="md:col-span-2">
                                                <label for="facility_description"
                                                    class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Description</label>
                                                <textarea id="facility_description" name="facility[description]" rows="3"
                                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm leading-6 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ old('facility.description', $facilitySection?->description ?? __('home.facilities.description')) }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <div>
                                            <h3 class="text-base font-black text-slate-900">Facility Cards</h3>
                                            <p class="mt-1 text-xs text-slate-500">Cards displayed inside the Campus and
                                                Facilities section.</p>
                                        </div>
                                        <button type="button" data-add-home-card="facility-cards"
                                            class="rounded-full bg-cyan-600 px-3 py-1 text-xs font-bold text-white transition hover:bg-cyan-500">
                                            Add Facility
                                        </button>
                                    </div>

                                    <div class="mt-4 grid gap-3 xl:grid-cols-2">
                                        @for ($i = 0; $i < $facilityLimit; $i++)
                                            @php
                                                $facilityItem = $facilityCards->get($i);
                                                $facilityTitle = old(
                                                    'facility_cards.' . $i . '.title',
                                                    $facilityItem?->title ?? '',
                                                );
                                                $facilityDescription = old(
                                                    'facility_cards.' . $i . '.description',
                                                    $facilityItem?->description ?? '',
                                                );
                                                $facilityIcon = old(
                                                    'facility_cards.' . $i . '.icon',
                                                    $facilityItem?->icon ?? '',
                                                );
                                                $showFacility = filled($facilityTitle) || filled($facilityDescription);
                                            @endphp
                                            <div data-home-editor-card data-addable-card="facility-cards"
                                                class="{{ $showFacility ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                <input type="hidden" name="facility_cards[{{ $i }}][id]"
                                                    value="{{ $facilityItem?->id }}">
                                                <input type="hidden" name="facility_cards[{{ $i }}][delete]"
                                                    data-home-delete-field value="0">
                                                <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                                                    <span class="flex items-center gap-2 text-xs font-bold text-slate-500">
                                                        <span
                                                            class="grid h-6 w-6 place-items-center rounded-full bg-cyan-600 text-[11px] text-white">{{ $i + 1 }}</span>
                                                        Facility card {{ $i + 1 }}
                                                    </span>
                                                    <div data-home-card-actions class="flex flex-wrap items-center gap-2">
                                                        <button type="button" data-home-edit-card
                                                            class="rounded-full border border-blue-200 bg-white px-3 py-1 text-xs font-bold text-blue-600 transition hover:bg-blue-50">
                                                            Edit
                                                        </button>
                                                        <label
                                                            class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                                                            <input type="hidden"
                                                                name="facility_cards[{{ $i }}][active]"
                                                                value="0">
                                                            <input type="checkbox"
                                                                name="facility_cards[{{ $i }}][active]"
                                                                data-home-active-toggle value="1"
                                                                {{ old('facility_cards.' . $i . '.active', $facilityItem?->is_active ?? true) ? 'checked' : '' }}
                                                                class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                            Active
                                                        </label>
                                                        <button type="button" data-home-remove-card
                                                            class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600 transition hover:bg-red-50">
                                                            Remove
                                                        </button>
                                                    </div>
                                                </div>
                                                <input name="facility_cards[{{ $i }}][title]" type="text"
                                                    placeholder="Facility title" value="{{ $facilityTitle }}"
                                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                <textarea name="facility_cards[{{ $i }}][description]" rows="3" placeholder="Facility description"
                                                    class="mt-2 w-full resize-none rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm leading-6 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ $facilityDescription }}</textarea>
                                                <input name="facility_cards[{{ $i }}][icon]" type="text"
                                                    data-random-icon-input
                                                    placeholder='Icon name, <i class="fa-solid fa-building-columns"></i>, or SVG'
                                                    value="{{ $facilityIcon }}"
                                                    class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                            </div>
                                        @endfor
                                    </div>
                                </div>

                                <div class="sticky bottom-4 z-10 flex justify-end">
                                    <div
                                        class="rounded-2xl border border-slate-200 bg-white/95 p-2 shadow-xl backdrop-blur">
                                        <button type="submit"
                                            class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-indigo-500">
                                            Save Facility Page
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-800">
                                Run migrations to enable dynamic facility page editing.
                            </div>
                        @endif
                    </section>

                    <section data-settings-panel="admission-page" class="hidden space-y-6">
                        @if ($homePageReady ?? false)
                            <form method="POST" action="{{ route('admin.settings.admission-page.update') }}"
                                class="space-y-5">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="_settings_tab" value="admission-page">
                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <span
                                        class="inline-flex rounded-full bg-cyan-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-cyan-700">Admissions</span>
                                    <h3 class="mt-3 text-base font-black text-slate-900">Admission Section</h3>
                                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                                        <input name="admission[badge]" type="text"
                                            value="{{ old('admission.badge', $admissionSection?->subtitle ?? __('home.admission.badge')) }}"
                                            placeholder="Badge"
                                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                        <textarea name="admission[title]" rows="2" placeholder="Title"
                                            class="md:col-span-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ old('admission.title', $admissionSection?->title ?? __('home.admission.title')) }}</textarea>
                                        <textarea name="admission[description]" rows="3" placeholder="Description"
                                            class="md:col-span-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ old('admission.description', $admissionSection?->description ?? __('home.admission.description')) }}</textarea>
                                    </div>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <h3 class="text-base font-black text-slate-900">Open Intake Card</h3>
                                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                                        <input name="intake[label]" type="text"
                                            value="{{ old('intake.label', $admissionIntake?->subtitle ?? __('home.admission.open_intake_label')) }}"
                                            placeholder="Label"
                                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                        <input name="intake[title]" type="text"
                                            value="{{ old('intake.title', $admissionIntake?->title ?? __('home.admission.open_intake_title')) }}"
                                            placeholder="Title"
                                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                        <textarea name="intake[description]" rows="3" placeholder="Description"
                                            class="md:col-span-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ old('intake.description', $admissionIntake?->description ?? __('home.admission.open_intake_description')) }}</textarea>
                                    </div>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <div class="flex items-center justify-between gap-3">
                                        <h3 class="text-base font-black text-slate-900">Admission Steps</h3>
                                        <button type="button" data-add-home-card="admission-steps"
                                            class="rounded-full bg-cyan-600 px-3 py-1 text-xs font-bold text-white">Add
                                            Step</button>
                                    </div>
                                    <div class="mt-4 grid gap-3 xl:grid-cols-2">
                                        @for ($i = 0; $i < $admissionStepLimit; $i++)
                                            @php
                                                $step = $admissionSteps->get($i);
                                                $stepTitle = old(
                                                    'admission_steps.' . $i . '.title',
                                                    $step?->title ?? '',
                                                );
                                                $stepDescription = old(
                                                    'admission_steps.' . $i . '.description',
                                                    $step?->description ?? '',
                                                );
                                                $stepColor = old(
                                                    'admission_steps.' . $i . '.color',
                                                    $iconColorValue(
                                                        $step?->color,
                                                        $admissionStepColorDefaults[$i % count($admissionStepColorDefaults)],
                                                    ),
                                                );
                                                $showStep = filled($stepTitle) || filled($stepDescription);
                                            @endphp
                                            <div data-home-editor-card data-addable-card="admission-steps"
                                                class="{{ $showStep ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                <input type="hidden" name="admission_steps[{{ $i }}][id]"
                                                    value="{{ $step?->id }}">
                                                <input type="hidden"
                                                    name="admission_steps[{{ $i }}][delete]"
                                                    data-home-delete-field value="0">
                                                <div class="mb-2 flex items-center justify-between gap-2">
                                                    <span class="text-xs font-bold text-slate-500">Step
                                                        {{ $i + 1 }}</span>
                                                    <div data-home-card-actions class="flex gap-2">
                                                        <label
                                                            class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200"><input
                                                                type="hidden"
                                                                name="admission_steps[{{ $i }}][active]"
                                                                value="0"><input type="checkbox"
                                                                name="admission_steps[{{ $i }}][active]"
                                                                data-home-active-toggle value="1"
                                                                {{ old('admission_steps.' . $i . '.active', $step?->is_active ?? true) ? 'checked' : '' }}>
                                                            Active</label>
                                                        <button type="button" data-home-remove-card
                                                            class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600">Remove</button>
                                                    </div>
                                                </div>
                                                <input name="admission_steps[{{ $i }}][title]"
                                                    type="text" value="{{ $stepTitle }}"
                                                    placeholder="Step title"
                                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold">
                                                <textarea name="admission_steps[{{ $i }}][description]" rows="3" placeholder="Step description"
                                                    class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ $stepDescription }}</textarea>
                                                <label
                                                    class="mt-2 flex items-center gap-3 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600">
                                                    <span class="min-w-0 flex-1">Icon color</span>
                                                    <input name="admission_steps[{{ $i }}][color]"
                                                        type="color" value="{{ $stepColor }}"
                                                        class="h-10 w-12 rounded-md border border-slate-300 bg-white p-1">
                                                </label>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                                <div class="sticky bottom-4 z-10 flex justify-end">
                                    <div class="rounded-2xl border border-slate-200 bg-white/95 p-2 shadow-xl"><button
                                            type="submit"
                                            class="rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white">Save
                                            Admission Page</button></div>
                                </div>
                            </form>
                        @endif
                    </section>

                    <section data-settings-panel="faq-page" class="hidden space-y-6">
                        @if ($homePageReady ?? false)
                            <form method="POST" action="{{ route('admin.settings.faq-page.update') }}"
                                class="space-y-5">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="_settings_tab" value="faq-page">
                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <span
                                        class="inline-flex rounded-full bg-cyan-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-cyan-700">FAQ</span>
                                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                                        <input name="faq[badge]" type="text"
                                            value="{{ old('faq.badge', $faqSection?->subtitle ?? __('home.faq.badge')) }}"
                                            placeholder="Badge"
                                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                                        <textarea name="faq[title]" rows="2" placeholder="Title"
                                            class="md:col-span-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold">{{ old('faq.title', $faqSection?->title ?? __('home.faq.title')) }}</textarea>
                                    </div>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <h3 class="text-base font-black text-slate-900">Help Card</h3>
                                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                                        <input name="faq_help[label]" type="text"
                                            value="{{ old('faq_help.label', $faqHelp?->subtitle ?? __('home.faq.more_help_label')) }}"
                                            placeholder="Label"
                                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                                        <input name="faq_help[title]" type="text"
                                            value="{{ old('faq_help.title', $faqHelp?->title ?? __('home.faq.more_help_title')) }}"
                                            placeholder="Title"
                                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                                        <textarea name="faq_help[description]" rows="3" placeholder="Description"
                                            class="md:col-span-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">{{ old('faq_help.description', $faqHelp?->description ?? __('home.faq.more_help_text')) }}</textarea>
                                    </div>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <div class="flex items-center justify-between gap-3">
                                        <h3 class="text-base font-black text-slate-900">FAQ Items</h3><button
                                            type="button" data-add-home-card="faq-items"
                                            class="rounded-full bg-cyan-600 px-3 py-1 text-xs font-bold text-white">Add
                                            FAQ</button>
                                    </div>
                                    <div class="mt-4 grid gap-3 xl:grid-cols-2">
                                        @for ($i = 0; $i < $faqLimit; $i++)
                                            @php
                                                $faq = $dynamicFaqItems->get($i);
                                                $question = old('faq_items.' . $i . '.question', $faq?->title ?? '');
                                                $answer = old('faq_items.' . $i . '.answer', $faq?->description ?? '');
                                                $showFaq = filled($question) || filled($answer);
                                            @endphp
                                            <div data-home-editor-card data-addable-card="faq-items"
                                                class="{{ $showFaq ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                <input type="hidden" name="faq_items[{{ $i }}][id]"
                                                    value="{{ $faq?->id }}"><input type="hidden"
                                                    name="faq_items[{{ $i }}][delete]" data-home-delete-field
                                                    value="0">
                                                <div class="mb-2 flex items-center justify-between gap-2"><span
                                                        class="text-xs font-bold text-slate-500">Question
                                                        {{ $i + 1 }}</span>
                                                    <div data-home-card-actions class="flex gap-2"><label
                                                            class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200"><input
                                                                type="hidden"
                                                                name="faq_items[{{ $i }}][active]"
                                                                value="0"><input type="checkbox"
                                                                name="faq_items[{{ $i }}][active]"
                                                                data-home-active-toggle value="1"
                                                                {{ old('faq_items.' . $i . '.active', $faq?->is_active ?? true) ? 'checked' : '' }}>
                                                            Active</label><button type="button" data-home-remove-card
                                                            class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600">Remove</button>
                                                    </div>
                                                </div>
                                                <input name="faq_items[{{ $i }}][question]" type="text"
                                                    value="{{ $question }}" placeholder="Question"
                                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold">
                                                <textarea name="faq_items[{{ $i }}][answer]" rows="3" placeholder="Answer"
                                                    class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ $answer }}</textarea>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                                <div class="sticky bottom-4 z-10 flex justify-end">
                                    <div class="rounded-2xl border border-slate-200 bg-white/95 p-2 shadow-xl"><button
                                            type="submit"
                                            class="rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white">Save
                                            FAQ Page</button></div>
                                </div>
                            </form>
                        @endif
                    </section>

                    <section data-settings-panel="contact-page" class="hidden space-y-6">
                        @if ($homePageReady ?? false)
                            <form method="POST" action="{{ route('admin.settings.contact-page.update') }}"
                                class="space-y-5">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="_settings_tab" value="contact-page">
                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <span
                                        class="inline-flex rounded-full bg-cyan-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-cyan-700">Contact
                                        Admissions</span>
                                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                                        <input name="contact[badge]" type="text"
                                            value="{{ old('contact.badge', $contactSection?->subtitle ?? __('home.contact.badge')) }}"
                                            placeholder="Badge"
                                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                                        <textarea name="contact[title]" rows="2" placeholder="Title"
                                            class="md:col-span-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm font-semibold">{{ old('contact.title', $contactSection?->title ?? __('home.contact.title')) }}</textarea>
                                        <textarea name="contact[description]" rows="3" placeholder="Description"
                                            class="md:col-span-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">{{ old('contact.description', $contactSection?->description ?? __('home.contact.description')) }}</textarea>
                                    </div>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <h3 class="text-base font-black text-slate-900">Campus Information Card</h3>
                                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                                        <input name="campus[label]" type="text"
                                            value="{{ old('campus.label', $contactCampus?->subtitle ?? __('home.contact.campus_label')) }}"
                                            placeholder="Label"
                                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                                        <input name="campus[title]" type="text"
                                            value="{{ old('campus.title', $contactCampus?->title ?? __('home.contact.campus_title')) }}"
                                            placeholder="Title"
                                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                                        <textarea name="campus[description]" rows="3" placeholder="Description"
                                            class="md:col-span-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">{{ old('campus.description', $contactCampus?->description ?? __('home.contact.campus_text')) }}</textarea>
                                    </div>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <div class="flex items-center justify-between gap-3">
                                        <h3 class="text-base font-black text-slate-900">Contact Cards</h3><button
                                            type="button" data-add-home-card="contact-cards"
                                            class="rounded-full bg-cyan-600 px-3 py-1 text-xs font-bold text-white">Add
                                            Contact</button>
                                    </div>
                                    <div class="mt-4 grid gap-3 xl:grid-cols-2">
                                        @for ($i = 0; $i < $contactCardLimit; $i++)
                                            @php
                                                $card = $dynamicContactCards->get($i);
                                                $label = old('contact_cards.' . $i . '.label', $card?->title ?? '');
                                                $value = old('contact_cards.' . $i . '.value', $card?->value ?? '');
                                                $showCard = filled($label) || filled($value);
                                            @endphp
                                            <div data-home-editor-card data-addable-card="contact-cards"
                                                class="{{ $showCard ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                <input type="hidden" name="contact_cards[{{ $i }}][id]"
                                                    value="{{ $card?->id }}"><input type="hidden"
                                                    name="contact_cards[{{ $i }}][delete]"
                                                    data-home-delete-field value="0">
                                                <div class="mb-2 flex items-center justify-between gap-2"><span
                                                        class="text-xs font-bold text-slate-500">Contact card
                                                        {{ $i + 1 }}</span>
                                                    <div data-home-card-actions class="flex gap-2"><label
                                                            class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200"><input
                                                                type="hidden"
                                                                name="contact_cards[{{ $i }}][active]"
                                                                value="0"><input type="checkbox"
                                                                name="contact_cards[{{ $i }}][active]"
                                                                data-home-active-toggle value="1"
                                                                {{ old('contact_cards.' . $i . '.active', $card?->is_active ?? true) ? 'checked' : '' }}>
                                                            Active</label><button type="button" data-home-remove-card
                                                            class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600">Remove</button>
                                                    </div>
                                                </div>
                                                <input name="contact_cards[{{ $i }}][label]" type="text"
                                                    value="{{ $label }}" placeholder="Label"
                                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold">
                                                <input name="contact_cards[{{ $i }}][value]" type="text"
                                                    value="{{ $value }}" placeholder="Value"
                                                    class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                                <div class="sticky bottom-4 z-10 flex justify-end">
                                    <div class="rounded-2xl border border-slate-200 bg-white/95 p-2 shadow-xl"><button
                                            type="submit"
                                            class="rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white">Save
                                            Contact Page</button></div>
                                </div>
                            </form>
                        @endif
                    </section>

                    <section data-settings-panel="footer-page" class="hidden space-y-6">
                        @if ($homePageReady ?? false)
                            <form method="POST" action="{{ route('admin.settings.footer-page.update') }}"
                                enctype="multipart/form-data" class="space-y-5">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="_settings_tab" value="footer-page">

                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <span
                                        class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-slate-700">Footer</span>
                                    <h3 class="mt-3 text-base font-black text-slate-900">Footer Content</h3>
                                    <div class="mt-5 grid gap-5 lg:grid-cols-[12rem_minmax(0,1fr)]">
                                        <div>
                                            <img id="footer_logo_preview" src="{{ $footerLogo }}"
                                                alt="Footer logo preview"
                                                class="h-36 w-full rounded-2xl border border-slate-200 bg-slate-950 object-contain p-4 shadow-sm">
                                            <input id="footer_logo_input" name="footer[logo]" type="file"
                                                accept="image/*" class="hidden">
                                            <input id="remove_footer_logo" name="footer[remove_logo]" type="hidden"
                                                value="0">
                                            <div class="mt-3 flex flex-wrap gap-2">
                                                <button id="upload_footer_logo_btn" type="button"
                                                    class="rounded-full bg-slate-900 px-3 py-1.5 text-xs font-bold text-white transition hover:bg-slate-700">
                                                    Upload Logo
                                                </button>
                                                <button id="delete_footer_logo_btn" type="button"
                                                    class="rounded-full border border-red-200 bg-white px-3 py-1.5 text-xs font-bold text-red-600 transition hover:bg-red-50">
                                                    Remove
                                                </button>
                                            </div>
                                        </div>
                                        <div class="grid gap-4 md:grid-cols-2">
                                            <input name="footer[tagline]" type="text"
                                                value="{{ old('footer.tagline', $footerSection?->title ?? __('home.footer.tagline')) }}"
                                                placeholder="Tagline"
                                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                            <input name="footer[explore]" type="text"
                                                value="{{ old('footer.explore', $footerSection?->subtitle ?? __('home.footer.explore')) }}"
                                                placeholder="Explore label"
                                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                            <input name="footer[contact]" type="text"
                                                value="{{ old('footer.contact', $footerSection?->value ?? __('home.footer.contact')) }}"
                                                placeholder="Contact label"
                                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                            <input name="footer[copyright]" type="text"
                                                value="{{ old('footer.copyright', $footerSection?->meta['copyright'] ?? __('home.footer.copyright')) }}"
                                                placeholder="Copyright text"
                                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                            <textarea name="footer[description]" rows="3" placeholder="Description"
                                                class="md:col-span-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ old('footer.description', $footerSection?->description ?? __('home.footer.description')) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <div class="flex items-center justify-between gap-3">
                                        <h3 class="text-base font-black text-slate-900">Footer Links</h3>
                                        <button type="button" data-add-home-card="footer-links"
                                            class="rounded-full bg-slate-900 px-3 py-1 text-xs font-bold text-white">Add
                                            Link</button>
                                    </div>
                                    <div class="mt-4 grid gap-3 xl:grid-cols-2">
                                        @for ($i = 0; $i < $footerLinkLimit; $i++)
                                            @php
                                                $link = $dynamicFooterLinks->get($i);
                                                $label = old('footer_links.' . $i . '.label', $link?->title ?? '');
                                                $href = old('footer_links.' . $i . '.href', $link?->value ?? '');
                                                $showLink = filled($label) || filled($href);
                                            @endphp
                                            <div data-home-editor-card data-addable-card="footer-links"
                                                class="{{ $showLink ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                <input type="hidden" name="footer_links[{{ $i }}][id]"
                                                    value="{{ $link?->id }}">
                                                <input type="hidden" name="footer_links[{{ $i }}][delete]"
                                                    data-home-delete-field value="0">
                                                <div class="mb-2 flex items-center justify-between gap-2">
                                                    <span class="text-xs font-bold text-slate-500">Footer link
                                                        {{ $i + 1 }}</span>
                                                    <div data-home-card-actions class="flex gap-2">
                                                        <label
                                                            class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                                                            <input type="hidden"
                                                                name="footer_links[{{ $i }}][active]"
                                                                value="0">
                                                            <input type="checkbox"
                                                                name="footer_links[{{ $i }}][active]"
                                                                data-home-active-toggle value="1"
                                                                {{ old('footer_links.' . $i . '.active', $link?->is_active ?? true) ? 'checked' : '' }}>
                                                            Active
                                                        </label>
                                                        <button type="button" data-home-remove-card
                                                            class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600">Remove</button>
                                                    </div>
                                                </div>
                                                <input name="footer_links[{{ $i }}][label]" type="text"
                                                    value="{{ $label }}" placeholder="Label"
                                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold">
                                                <input name="footer_links[{{ $i }}][href]" type="text"
                                                    value="{{ $href }}" placeholder="#about"
                                                    class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                            </div>
                                        @endfor
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <div class="flex items-center justify-between gap-3">
                                        <h3 class="text-base font-black text-slate-900">Footer Contact Lines</h3>
                                        <button type="button" data-add-home-card="footer-contacts"
                                            class="rounded-full bg-slate-900 px-3 py-1 text-xs font-bold text-white">Add
                                            Contact</button>
                                    </div>
                                    <div class="mt-4 grid gap-3 xl:grid-cols-2">
                                        @for ($i = 0; $i < $footerContactLimit; $i++)
                                            @php
                                                $contact = $dynamicFooterContacts->get($i);
                                                $label = old(
                                                    'footer_contacts.' . $i . '.label',
                                                    $contact?->title ?? '',
                                                );
                                                $value = old(
                                                    'footer_contacts.' . $i . '.value',
                                                    $contact?->value ?? '',
                                                );
                                                $showContact = filled($label) || filled($value);
                                            @endphp
                                            <div data-home-editor-card data-addable-card="footer-contacts"
                                                class="{{ $showContact ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                <input type="hidden" name="footer_contacts[{{ $i }}][id]"
                                                    value="{{ $contact?->id }}">
                                                <input type="hidden"
                                                    name="footer_contacts[{{ $i }}][delete]"
                                                    data-home-delete-field value="0">
                                                <div class="mb-2 flex items-center justify-between gap-2">
                                                    <span class="text-xs font-bold text-slate-500">Footer contact
                                                        {{ $i + 1 }}</span>
                                                    <div data-home-card-actions class="flex gap-2">
                                                        <label
                                                            class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                                                            <input type="hidden"
                                                                name="footer_contacts[{{ $i }}][active]"
                                                                value="0">
                                                            <input type="checkbox"
                                                                name="footer_contacts[{{ $i }}][active]"
                                                                data-home-active-toggle value="1"
                                                                {{ old('footer_contacts.' . $i . '.active', $contact?->is_active ?? true) ? 'checked' : '' }}>
                                                            Active
                                                        </label>
                                                        <button type="button" data-home-remove-card
                                                            class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600">Remove</button>
                                                    </div>
                                                </div>
                                                <input name="footer_contacts[{{ $i }}][label]"
                                                    type="text" value="{{ $label }}" placeholder="Label"
                                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold">
                                                <input name="footer_contacts[{{ $i }}][value]"
                                                    type="text" value="{{ $value }}" placeholder="Value"
                                                    class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                            </div>
                                        @endfor
                                    </div>
                                </div>

                                <div class="sticky bottom-4 z-10 flex justify-end">
                                    <div class="rounded-2xl border border-slate-200 bg-white/95 p-2 shadow-xl">
                                        <button type="submit"
                                            class="rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white">Save
                                            Footer Page</button>
                                    </div>
                                </div>
                            </form>
                        @endif
                    </section>

                    <section data-settings-panel="verification" class="hidden">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-6">
                            <h2 class="text-lg font-semibold text-slate-900">Verification</h2>
                            <p class="mt-2 text-sm text-slate-500">This section is reserved for account verification and
                                security checks.</p>
                        </div>
                    </section>
                </div>
            </div>
        </section>
    </div>

    @vite(['resources/js/admin/settings.js'])
@endsection
