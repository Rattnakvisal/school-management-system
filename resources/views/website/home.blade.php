<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $schoolName ?? 'TechBridge Academy' }} | {{ __('home.meta.title_suffix') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="website-home [font-family:Manrope,_sans-serif] min-h-screen overflow-x-hidden overflow-y-auto bg-slate-100 text-slate-900 antialiased"
    x-data="{
        open: false,
        top: false,
        active: '#home',
        showBanner: false,
        bannerStorageKey: 'website_home_banner_hidden_until',
        setActive() {
            const ids = ['#home', '#about', '#features', '#programs', '#facilities', '#admission', '#faq', '#contact'];
            const y = window.scrollY + 140;
    
            for (let i = ids.length - 1; i >= 0; i--) {
                const el = document.querySelector(ids[i]);
                if (el && el.offsetTop <= y) {
                    this.active = ids[i];
                    break;
                }
            }
        },
        initBanner() {
            const forceBanner = new URLSearchParams(window.location.search).get('banner') === '1';
    
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
            } catch (_) {}
    
            setTimeout(() => {
                this.showBanner = true;
            }, 280);
        },
        closeBanner(remember = false) {
            this.showBanner = false;
    
            if (remember) {
                try {
                    window.localStorage.setItem(this.bannerStorageKey, String(Date.now() + (24 * 60 * 60 * 1000)));
                } catch (_) {}
            }
        },
        openProgramsFromBanner() {
            this.closeBanner();
            const target = document.querySelector('#programs');
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    }" x-init="setActive();
    initBanner();
    window.addEventListener('scroll', () => {
        top = window.scrollY > 560;
        setActive();
    });"
    @keydown.escape.window="if (showBanner) closeBanner()">
    @php
        $schoolName = $schoolName ?? 'TechBridge';
        $studentsTotal = $studentsTotal ?? 0;
        $teachersTotal = $teachersTotal ?? 0;
        $dashboardRoute = $dashboardRoute ?? 'home';

        $links = trans('home.links');
        $features = trans('home.features.items');
        $programs = trans('home.programs.items');
        $steps = trans('home.admission.steps');
        $heroPoints = array_map(
            fn($point) => [
                'description' => $point,
                'icon' => 'check',
            ],
            trans('home.hero.points'),
        );
        $aboutCards = trans('home.about.cards');
        $aboutHighlights = trans('home.about.highlights');
        $facilityCards = trans('home.facilities.items');
        $faqItems = trans('home.faq.items');
        $footerLinks = trans('home.footer_links');
        $contactCards = trans('home.contact.cards');
        $chatbotAnswers = array_map(function (array $item) use ($schoolName) {
            $item['answer'] = str_replace(':schoolName', $schoolName, $item['answer']);

            return $item;
        }, trans('home.chatbot.answers'));

        $heroHighlights = array_map(function (array $item) use ($studentsTotal, $teachersTotal) {
            $item['value'] = match ($item['value']) {
                'students_total' => number_format($studentsTotal),
                'teachers_total' => number_format($teachersTotal),
                default => $item['value'],
            };

            return $item;
        }, trans('home.hero.highlights'));

        $homePageItems = $homePageItems ?? collect();
        $brandItem = $homePageItems->get('brand', collect())->firstWhere('key', 'main');
        $schoolName = $brandItem?->title ?: $schoolName;
        $brandTagline = $brandItem?->description ?: __('home.brand.tagline');
        $brandLogo = $brandItem?->image_path
            ? route('public.storage', ['path' => $brandItem->image_path])
            : asset('images/techbridge-logo-mark.svg');

        $dynamicNavbarLinks = $homePageItems
            ->get('navbar_links', collect())
            ->map(fn($item) => ['label' => $item->title, 'href' => $item->value])
            ->filter(fn($item) => filled($item['label']) && filled($item['href']))
            ->values()
            ->all();
        if (count($dynamicNavbarLinks) > 0) {
            $links = $dynamicNavbarLinks;
        }

        $homeHeroItem = $homePageItems->get('hero', collect())->firstWhere('key', 'main');
        if ($homeHeroItem) {
            $heroBadge = $homeHeroItem->subtitle ?: __('home.hero.badge');
            $heroTitle = $homeHeroItem->title ?: __('home.hero.title');
            $heroDescription = $homeHeroItem->description ?: __('home.hero.description', ['schoolName' => $schoolName]);
            $heroImage = $homeHeroItem->image_path
                ? route('public.storage', ['path' => $homeHeroItem->image_path])
                : asset('images/school.jpg');
        } else {
            $heroBadge = __('home.hero.badge');
            $heroTitle = __('home.hero.title');
            $heroDescription = __('home.hero.description', ['schoolName' => $schoolName]);
            $heroImage = asset('images/school.jpg');
        }

        $dynamicHeroPoints = $homePageItems
            ->get('hero_points', collect())
            ->map(
                fn($item) => [
                    'description' => $item->description,
                    'icon' => $item->icon ?: 'check',
                ],
            )
            ->filter(fn($item) => filled($item['description']))
            ->values()
            ->all();
        if (count($dynamicHeroPoints) > 0) {
            $heroPoints = $dynamicHeroPoints;
        }

        $dynamicHeroHighlights = $homePageItems
            ->get('hero_stats', collect())
            ->map(function ($item) use ($studentsTotal, $teachersTotal) {
                $value = match ($item->value) {
                    'students_total' => number_format($studentsTotal),
                    'teachers_total' => number_format($teachersTotal),
                    default => $item->value,
                };

                return [
                    'label' => $item->title,
                    'value' => $value,
                    'trend' => $item->subtitle,
                    'icon' => $item->icon,
                    'color' => $item->color,
                ];
            })
            ->filter(fn($item) => filled($item['label']) && filled($item['value']))
            ->values()
            ->all();
        if (count($dynamicHeroHighlights) > 0) {
            $heroHighlights = $dynamicHeroHighlights;
        }

        $dynamicHeroFeatures = $homePageItems
            ->get('hero_features', collect())
            ->map(
                fn($item) => [
                    'title' => $item->title,
                    'description' => $item->description,
                    'icon' => $item->icon,
                ],
            )
            ->filter(fn($item) => filled($item['title']) && filled($item['description']))
            ->values()
            ->all();
        $heroStripFeatures = count($dynamicHeroFeatures) > 0 ? $dynamicHeroFeatures : $features;

        $aboutItem = $homePageItems->get('about', collect())->firstWhere('key', 'main');
        $aboutBadge = $aboutItem?->subtitle ?: __('home.about.badge', ['schoolName' => $schoolName]);
        $aboutTitle = $aboutItem?->title ?: __('home.about.title');
        $aboutDescription = $aboutItem?->description ?: __('home.about.description');

        $dynamicAboutHighlights = $homePageItems
            ->get('about_highlights', collect())
            ->map(
                fn($item) => [
                    'title' => $item->title,
                    'description' => $item->description,
                    'icon' => $item->icon,
                ],
            )
            ->filter(fn($item) => filled($item['title']) && filled($item['description']))
            ->values()
            ->all();
        if (count($dynamicAboutHighlights) > 0) {
            $aboutHighlights = $dynamicAboutHighlights;
        }

        $defaultAboutTones = array_column(trans('home.about.cards'), 'tone');
        $dynamicAboutCards = $homePageItems
            ->get('about_cards', collect())
            ->values()
            ->map(
                fn($item, $index) => [
                    'title' => $item->title,
                    'description' => $item->description,
                    'icon' => $item->icon,
                    'tone' => $item->color ?: ($defaultAboutTones[$index] ?? 'bg-cyan-100 text-cyan-700'),
                ],
            )
            ->filter(fn($item) => filled($item['title']) && filled($item['description']))
            ->values()
            ->all();
        if (count($dynamicAboutCards) > 0) {
            $aboutCards = $dynamicAboutCards;
        }

        $featureItem = $homePageItems->get('platform_features', collect())->firstWhere('key', 'main');
        $featureBadge = $featureItem?->subtitle ?: __('home.features.badge');
        $featureTitle = $featureItem?->title ?: __('home.features.title');
        $featureTag = $featureItem?->value ?: __('home.features.tag');

        $dynamicFeatures = $homePageItems
            ->get('platform_feature_cards', collect())
            ->map(
                fn($item) => [
                    'title' => $item->title,
                    'description' => $item->description,
                    'icon' => $item->icon,
                ],
            )
            ->filter(fn($item) => filled($item['title']) && filled($item['description']))
            ->values()
            ->all();
        if (count($dynamicFeatures) > 0) {
            $features = $dynamicFeatures;
        }

        $programItem = $homePageItems->get('programs', collect())->firstWhere('key', 'main');
        $programBadge = $programItem?->subtitle ?: __('home.programs.badge');
        $programTitle = $programItem?->title ?: __('home.programs.title');
        $programDescription = $programItem?->description ?: __('home.programs.description');

        $dynamicPrograms = $homePageItems
            ->get('program_cards', collect())
            ->map(
                fn($item) => [
                    'level' => $item->subtitle,
                    'title' => $item->title,
                    'description' => $item->description,
                    'icon' => $item->icon,
                ],
            )
            ->filter(fn($item) => filled($item['level']) && filled($item['title']) && filled($item['description']))
            ->values()
            ->all();
        if (count($dynamicPrograms) > 0) {
            $programs = $dynamicPrograms;
        }

        $facilityItem = $homePageItems->get('facilities', collect())->firstWhere('key', 'main');
        $facilityBadge = $facilityItem?->subtitle ?: __('home.facilities.badge');
        $facilityTitle = $facilityItem?->title ?: __('home.facilities.title');
        $facilityDescription = $facilityItem?->description ?: __('home.facilities.description');
        $facilityImage = $facilityItem?->image_path
            ? route('public.storage', ['path' => $facilityItem->image_path])
            : asset('images/study.jpg');

        $dynamicFacilityCards = $homePageItems
            ->get('facility_cards', collect())
            ->map(
                fn($item) => [
                    'title' => $item->title,
                    'description' => $item->description,
                    'icon' => $item->icon,
                ],
            )
            ->filter(fn($item) => filled($item['title']) && filled($item['description']))
            ->values()
            ->all();
        if (count($dynamicFacilityCards) > 0) {
            $facilityCards = $dynamicFacilityCards;
        }

        $admissionItem = $homePageItems->get('admission', collect())->firstWhere('key', 'main');
        $admissionBadge = $admissionItem?->subtitle ?: __('home.admission.badge');
        $admissionTitle = $admissionItem?->title ?: __('home.admission.title');
        $admissionDescription = $admissionItem?->description ?: __('home.admission.description');

        $admissionIntakeItem = $homePageItems->get('admission_intake', collect())->firstWhere('key', 'main');
        $admissionIntakeLabel = $admissionIntakeItem?->subtitle ?: __('home.admission.open_intake_label');
        $admissionIntakeTitle = $admissionIntakeItem?->title ?: __('home.admission.open_intake_title');
        $admissionIntakeDescription = $admissionIntakeItem?->description ?: __('home.admission.open_intake_description');

        $dynamicSteps = $homePageItems
            ->get('admission_steps', collect())
            ->map(fn($item) => ['title' => $item->title, 'description' => $item->description])
            ->filter(fn($item) => filled($item['title']) && filled($item['description']))
            ->values()
            ->all();
        if (count($dynamicSteps) > 0) {
            $steps = $dynamicSteps;
        }

        $faqItem = $homePageItems->get('faq', collect())->firstWhere('key', 'main');
        $faqBadge = $faqItem?->subtitle ?: __('home.faq.badge');
        $faqTitle = $faqItem?->title ?: __('home.faq.title');

        $faqHelpItem = $homePageItems->get('faq_help', collect())->firstWhere('key', 'main');
        $faqHelpLabel = $faqHelpItem?->subtitle ?: __('home.faq.more_help_label');
        $faqHelpTitle = $faqHelpItem?->title ?: __('home.faq.more_help_title');
        $faqHelpText = $faqHelpItem?->description ?: __('home.faq.more_help_text');

        $dynamicFaqItems = $homePageItems
            ->get('faq_items', collect())
            ->map(fn($item) => ['question' => $item->title, 'answer' => $item->description])
            ->filter(fn($item) => filled($item['question']) && filled($item['answer']))
            ->values()
            ->all();
        if (count($dynamicFaqItems) > 0) {
            $faqItems = $dynamicFaqItems;
        }

        $contactItem = $homePageItems->get('contact', collect())->firstWhere('key', 'main');
        $contactBadge = $contactItem?->subtitle ?: __('home.contact.badge');
        $contactTitle = $contactItem?->title ?: __('home.contact.title');
        $contactDescription = $contactItem?->description ?: __('home.contact.description');

        $contactCampusItem = $homePageItems->get('contact_campus', collect())->firstWhere('key', 'main');
        $contactCampusLabel = $contactCampusItem?->subtitle ?: __('home.contact.campus_label');
        $contactCampusTitle = $contactCampusItem?->title ?: __('home.contact.campus_title');
        $contactCampusText = $contactCampusItem?->description ?: __('home.contact.campus_text');

        $dynamicContactCards = $homePageItems
            ->get('contact_cards', collect())
            ->map(fn($item) => ['label' => $item->title, 'value' => $item->value])
            ->filter(fn($item) => filled($item['label']) && filled($item['value']))
            ->values()
            ->all();
        if (count($dynamicContactCards) > 0) {
            $contactCards = $dynamicContactCards;
        }

        $footerItem = $homePageItems->get('footer', collect())->firstWhere('key', 'main');
        $footerTagline = $footerItem?->title ?: __('home.footer.tagline');
        $footerDescription = $footerItem?->description ?: __('home.footer.description');
        $footerExploreLabel = $footerItem?->subtitle ?: __('home.footer.explore');
        $footerContactLabel = $footerItem?->value ?: __('home.footer.contact');
        $footerCopyright = $footerItem?->meta['copyright'] ?? __('home.footer.copyright');
        $footerLogo = $footerItem?->image_path
            ? route('public.storage', ['path' => $footerItem->image_path])
            : asset('images/techbridge-logo-mark.svg');

        $dynamicFooterLinks = $homePageItems
            ->get('footer_links', collect())
            ->map(fn($item) => ['label' => $item->title, 'href' => $item->value])
            ->filter(fn($item) => filled($item['label']) && filled($item['href']))
            ->values()
            ->all();
        if (count($dynamicFooterLinks) > 0) {
            $footerLinks = $dynamicFooterLinks;
        }

        $footerContacts = $homePageItems
            ->get('footer_contacts', collect())
            ->map(fn($item) => ['label' => $item->title, 'value' => $item->value])
            ->filter(fn($item) => filled($item['value']))
            ->values()
            ->all();
        if (count($footerContacts) === 0) {
            $footerContacts = $contactCards;
        }
    @endphp

    <div class="relative isolate overflow-x-hidden">
        <div class="pointer-events-none absolute inset-x-0 top-0 -z-20 h-[42rem] ">
        </div>
        <div
            class="home-orb pointer-events-none absolute -left-28 top-12 -z-10 h-72 w-72 rounded-full bg-cyan-300/35 blur-3xl">
        </div>
        <div
            class="home-orb home-orb--b pointer-events-none absolute right-0 top-24 -z-10 h-80 w-80 rounded-full bg-indigo-300/25 blur-3xl">
        </div>
        <div
            class="home-orb home-orb--c pointer-events-none absolute bottom-[22rem] left-1/3 -z-10 h-72 w-72 rounded-full bg-amber-200/25 blur-3xl">
        </div>

        @include('website.home.partials.header')

        <main id="home" class="relative z-10" data-page-animate>
            @include('website.home.partials.hero')
            @include('website.home.partials.about')
            @include('website.home.partials.features')
            @include('website.home.partials.programs')
            @include('website.home.partials.facilities')
            @include('website.home.partials.admission')
            @include('website.home.partials.faq')
            @include('website.home.partials.contact')
        </main>

        @include('website.home.partials.footer')

        @include('website.home.partials.chatbot')
    </div>

    @vite(['resources/js/chat-bot.js'])
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>

</html>
