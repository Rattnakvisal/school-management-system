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

    @include('admin.settings._styles')

    <div id="admin-settings-page" class="stage space-y-6" data-settings-default-tab="{{ $settingsDefaultTab }}">
        @include('admin.settings._header')
        @include('admin.settings._alerts')

        <section class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm ring-1 ring-slate-100 sm:p-6">
            <div class="grid gap-6 xl:grid-cols-[260px_minmax(0,1fr)]">
                @include('admin.settings._sidebar')

                <div class="min-w-0">
                    @include('admin.settings.panels.profile')
                    @include('admin.settings.panels.password')
                    @include('admin.settings.panels.notifications')
                    @include('admin.settings.panels.navbar-page')
                    @include('admin.settings.panels.home-page')
                    @include('admin.settings.panels.about-page')
                    @include('admin.settings.panels.feature-page')
                    @include('admin.settings.panels.program-page')
                    @include('admin.settings.panels.facility-page')
                    @include('admin.settings.panels.admission-page')
                    @include('admin.settings.panels.faq-page')
                    @include('admin.settings.panels.contact-page')
                    @include('admin.settings.panels.footer-page')
                    @include('admin.settings.panels.verification')
                </div>
            </div>
        </section>
    </div>

    @vite(['resources/js/admin/settings.js'])
@endsection
