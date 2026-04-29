<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $schoolName ?? 'TechBridge Academy' }} | {{ \App\Support\HomePageContent::text('meta.title_suffix') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="website-home [font-family:Manrope,_sans-serif] min-h-screen overflow-x-hidden overflow-y-auto bg-slate-100 text-slate-900 antialiased"
    x-data="websiteHomePage()" @keydown.escape.window="if (showBanner) closeBanner()">
    @php
        $schoolName = $schoolName ?? 'TechBridge';
        $studentsTotal = $studentsTotal ?? 0;
        $teachersTotal = $teachersTotal ?? 0;
        $dashboardRoute = $dashboardRoute ?? 'home';

        $links = \App\Support\HomePageContent::get('links');
        $features = \App\Support\HomePageContent::get('features.items');
        $programs = \App\Support\HomePageContent::get('programs.items');
        $steps = \App\Support\HomePageContent::get('admission.steps');
        $heroPoints = array_map(
            fn($point) => [
                'description' => $point,
                'icon' => 'check',
            ],
            \App\Support\HomePageContent::get('hero.points'),
        );
        $aboutCards = \App\Support\HomePageContent::get('about.cards');
        $aboutHighlights = \App\Support\HomePageContent::get('about.highlights');
        $facilityCards = \App\Support\HomePageContent::get('facilities.items');
        $faqItems = \App\Support\HomePageContent::get('faq.items');
        $footerLinks = \App\Support\HomePageContent::get('footer_links');
        $contactCards = \App\Support\HomePageContent::get('contact.cards');
        $chatbotAnswers = array_map(function (array $item) use ($schoolName) {
            $item['answer'] = str_replace(':schoolName', $schoolName, $item['answer']);

            return $item;
        }, \App\Support\HomePageContent::get('chatbot.answers'));

        $heroHighlights = array_map(function (array $item) use ($studentsTotal, $teachersTotal) {
            $item['value'] = match ($item['value']) {
                'students_total' => number_format($studentsTotal),
                'teachers_total' => number_format($teachersTotal),
                default => $item['value'],
            };

            return $item;
        }, \App\Support\HomePageContent::get('hero.highlights'));

        $homePageItems = $homePageItems ?? collect();
        $brandItem = $homePageItems->get('brand', collect())->firstWhere('key', 'main');
        $schoolName = $brandItem?->title ?: $schoolName;
        $brandTagline = $brandItem?->description ?: \App\Support\HomePageContent::text('brand.tagline');
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
            $heroBadge = $homeHeroItem->subtitle ?: \App\Support\HomePageContent::text('hero.badge');
            $heroTitle = $homeHeroItem->title ?: \App\Support\HomePageContent::text('hero.title');
            $heroDescription = $homeHeroItem->description ?: \App\Support\HomePageContent::text('hero.description', ['schoolName' => $schoolName]);
            $heroImage = $homeHeroItem->image_path
                ? route('public.storage', ['path' => $homeHeroItem->image_path])
                : asset('images/school.jpg');
        } else {
            $heroBadge = \App\Support\HomePageContent::text('hero.badge');
            $heroTitle = \App\Support\HomePageContent::text('hero.title');
            $heroDescription = \App\Support\HomePageContent::text('hero.description', ['schoolName' => $schoolName]);
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
                    'color' => $item->color,
                ],
            )
            ->filter(fn($item) => filled($item['title']) && filled($item['description']))
            ->values()
            ->all();
        $heroStripFeatures = count($dynamicHeroFeatures) > 0 ? $dynamicHeroFeatures : $features;

        $aboutItem = $homePageItems->get('about', collect())->firstWhere('key', 'main');
        $aboutBadge = $aboutItem?->subtitle ?: \App\Support\HomePageContent::text('about.badge', ['schoolName' => $schoolName]);
        $aboutTitle = $aboutItem?->title ?: \App\Support\HomePageContent::text('about.title');
        $aboutDescription = $aboutItem?->description ?: \App\Support\HomePageContent::text('about.description');

        $dynamicAboutHighlights = $homePageItems
            ->get('about_highlights', collect())
            ->map(
                fn($item) => [
                    'title' => $item->title,
                    'description' => $item->description,
                    'icon' => $item->icon,
                    'color' => $item->color,
                ],
            )
            ->filter(fn($item) => filled($item['title']) && filled($item['description']))
            ->values()
            ->all();
        if (count($dynamicAboutHighlights) > 0) {
            $aboutHighlights = $dynamicAboutHighlights;
        }

        $defaultAboutTones = array_column(\App\Support\HomePageContent::get('about.cards'), 'tone');
        $dynamicAboutCards = $homePageItems
            ->get('about_cards', collect())
            ->values()
            ->map(
                fn($item, $index) => [
                    'title' => $item->title,
                    'description' => $item->description,
                    'icon' => $item->icon,
                    'tone' => $item->color ?: $defaultAboutTones[$index] ?? 'bg-cyan-100 text-cyan-700',
                    'color' => $item->color,
                ],
            )
            ->filter(fn($item) => filled($item['title']) && filled($item['description']))
            ->values()
            ->all();
        if (count($dynamicAboutCards) > 0) {
            $aboutCards = $dynamicAboutCards;
        }

        $featureItem = $homePageItems->get('platform_features', collect())->firstWhere('key', 'main');
        $featureBadge = $featureItem?->subtitle ?: \App\Support\HomePageContent::text('features.badge');
        $featureTitle = $featureItem?->title ?: \App\Support\HomePageContent::text('features.title');
        $featureTag = $featureItem?->value ?: \App\Support\HomePageContent::text('features.tag');

        $dynamicFeatures = $homePageItems
            ->get('platform_feature_cards', collect())
            ->map(
                fn($item) => [
                    'title' => $item->title,
                    'description' => $item->description,
                    'icon' => $item->icon,
                    'color' => $item->color,
                ],
            )
            ->filter(fn($item) => filled($item['title']) && filled($item['description']))
            ->values()
            ->all();
        if (count($dynamicFeatures) > 0) {
            $features = $dynamicFeatures;
        }

        $programItem = $homePageItems->get('programs', collect())->firstWhere('key', 'main');
        $programBadge = $programItem?->subtitle ?: \App\Support\HomePageContent::text('programs.badge');
        $programTitle = $programItem?->title ?: \App\Support\HomePageContent::text('programs.title');
        $programDescription = $programItem?->description ?: \App\Support\HomePageContent::text('programs.description');

        $dynamicPrograms = $homePageItems
            ->get('program_cards', collect())
            ->map(
                fn($item) => [
                    'level' => $item->subtitle,
                    'title' => $item->title,
                    'description' => $item->description,
                    'icon' => $item->icon,
                    'color' => $item->color,
                ],
            )
            ->filter(fn($item) => filled($item['level']) && filled($item['title']) && filled($item['description']))
            ->values()
            ->all();
        if (count($dynamicPrograms) > 0) {
            $programs = $dynamicPrograms;
        }

        $facilityItem = $homePageItems->get('facilities', collect())->firstWhere('key', 'main');
        $facilityBadge = $facilityItem?->subtitle ?: \App\Support\HomePageContent::text('facilities.badge');
        $facilityTitle = $facilityItem?->title ?: \App\Support\HomePageContent::text('facilities.title');
        $facilityDescription = $facilityItem?->description ?: \App\Support\HomePageContent::text('facilities.description');
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
        $admissionBadge = $admissionItem?->subtitle ?: \App\Support\HomePageContent::text('admission.badge');
        $admissionTitle = $admissionItem?->title ?: \App\Support\HomePageContent::text('admission.title');
        $admissionDescription = $admissionItem?->description ?: \App\Support\HomePageContent::text('admission.description');

        $admissionIntakeItem = $homePageItems->get('admission_intake', collect())->firstWhere('key', 'main');
        $admissionIntakeLabel = $admissionIntakeItem?->subtitle ?: \App\Support\HomePageContent::text('admission.open_intake_label');
        $admissionIntakeTitle = $admissionIntakeItem?->title ?: \App\Support\HomePageContent::text('admission.open_intake_title');
        $admissionIntakeDescription =
            $admissionIntakeItem?->description ?: \App\Support\HomePageContent::text('admission.open_intake_description');

        $dynamicSteps = $homePageItems
            ->get('admission_steps', collect())
            ->map(fn($item) => ['title' => $item->title, 'description' => $item->description, 'color' => $item->color])
            ->filter(fn($item) => filled($item['title']) && filled($item['description']))
            ->values()
            ->all();
        if (count($dynamicSteps) > 0) {
            $steps = $dynamicSteps;
        }

        $faqItem = $homePageItems->get('faq', collect())->firstWhere('key', 'main');
        $faqBadge = $faqItem?->subtitle ?: \App\Support\HomePageContent::text('faq.badge');
        $faqTitle = $faqItem?->title ?: \App\Support\HomePageContent::text('faq.title');

        $faqHelpItem = $homePageItems->get('faq_help', collect())->firstWhere('key', 'main');
        $faqHelpLabel = $faqHelpItem?->subtitle ?: \App\Support\HomePageContent::text('faq.more_help_label');
        $faqHelpTitle = $faqHelpItem?->title ?: \App\Support\HomePageContent::text('faq.more_help_title');
        $faqHelpText = $faqHelpItem?->description ?: \App\Support\HomePageContent::text('faq.more_help_text');

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
        $contactBadge = $contactItem?->subtitle ?: \App\Support\HomePageContent::text('contact.badge');
        $contactTitle = $contactItem?->title ?: \App\Support\HomePageContent::text('contact.title');
        $contactDescription = $contactItem?->description ?: \App\Support\HomePageContent::text('contact.description');

        $contactCampusItem = $homePageItems->get('contact_campus', collect())->firstWhere('key', 'main');
        $contactCampusLabel = $contactCampusItem?->subtitle ?: \App\Support\HomePageContent::text('contact.campus_label');
        $contactCampusTitle = $contactCampusItem?->title ?: \App\Support\HomePageContent::text('contact.campus_title');
        $contactCampusText = $contactCampusItem?->description ?: \App\Support\HomePageContent::text('contact.campus_text');

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
        $footerTagline = $footerItem?->title ?: \App\Support\HomePageContent::text('footer.tagline');
        $footerDescription = $footerItem?->description ?: \App\Support\HomePageContent::text('footer.description');
        $footerExploreLabel = $footerItem?->subtitle ?: \App\Support\HomePageContent::text('footer.explore');
        $footerContactLabel = $footerItem?->value ?: \App\Support\HomePageContent::text('footer.contact');
        $footerCopyright = $footerItem?->meta['copyright'] ?? \App\Support\HomePageContent::text('footer.copyright');
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

    @vite(['resources/js/website/home/chat-bot.js'])
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>

</html>
