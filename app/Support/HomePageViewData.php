<?php

namespace App\Support;

use Illuminate\Support\Collection;

class HomePageViewData
{
    public static function make(
        Collection $homePageItems,
        int $studentsTotal,
        int $teachersTotal,
        ?string $dashboardRoute
    ): array {
        $schoolName = 'TechBridge';
        $dashboardRoute ??= 'home';

        $links = self::configArray('links');
        $features = self::configArray('features.items');
        $programs = self::configArray('programs.items');
        $steps = self::configArray('admission.steps');
        $aboutCards = self::configArray('about.cards');
        $aboutHighlights = self::configArray('about.highlights');
        $facilityCards = self::configArray('facilities.items');
        $faqItems = self::configArray('faq.items');
        $footerLinks = self::configArray('footer_links');
        $contactCards = self::configArray('contact.cards');

        $heroPoints = array_map(
            fn($point) => [
                'description' => is_array($point) ? ($point['description'] ?? '') : $point,
                'icon' => is_array($point) ? ($point['icon'] ?? 'check') : 'check',
            ],
            self::configArray('hero.points'),
        );

        $heroHighlights = array_map(function (array $item) use ($studentsTotal, $teachersTotal) {
            $item['value'] = self::resolveStatValue($item['value'] ?? '', $studentsTotal, $teachersTotal);

            return $item;
        }, self::configArray('hero.highlights'));

        $brandItem = $homePageItems->get('brand', collect())->firstWhere('key', 'main');
        $schoolName = $brandItem?->title ?: $schoolName;
        $brandTagline = $brandItem?->description ?: HomePageContent::text('brand.tagline');
        $brandLogo = self::imageUrl($brandItem?->image_path, 'images/techbridge-logo-mark.svg');

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
        $heroBadge = $homeHeroItem?->subtitle ?: HomePageContent::text('hero.badge');
        $heroTitle = $homeHeroItem?->title ?: HomePageContent::text('hero.title');
        $heroDescription = $homeHeroItem?->description ?: HomePageContent::text('hero.description', ['schoolName' => $schoolName]);
        $heroImage = self::imageUrl($homeHeroItem?->image_path, 'images/school.jpg');

        $dynamicHeroPoints = $homePageItems
            ->get('hero_points', collect())
            ->map(fn($item) => [
                'description' => $item->description,
                'icon' => $item->icon ?: 'check',
            ])
            ->filter(fn($item) => filled($item['description']))
            ->values()
            ->all();

        if (count($dynamicHeroPoints) > 0) {
            $heroPoints = $dynamicHeroPoints;
        }

        $dynamicHeroHighlights = $homePageItems
            ->get('hero_stats', collect())
            ->map(fn($item) => [
                'label' => $item->title,
                'value' => self::resolveStatValue($item->value, $studentsTotal, $teachersTotal),
                'trend' => $item->subtitle,
                'icon' => $item->icon,
                'color' => $item->color,
            ])
            ->filter(fn($item) => filled($item['label']) && filled($item['value']))
            ->values()
            ->all();

        if (count($dynamicHeroHighlights) > 0) {
            $heroHighlights = $dynamicHeroHighlights;
        }

        $dynamicHeroFeatures = $homePageItems
            ->get('hero_features', collect())
            ->map(fn($item) => [
                'title' => $item->title,
                'description' => $item->description,
                'icon' => $item->icon,
                'color' => $item->color,
            ])
            ->filter(fn($item) => filled($item['title']) && filled($item['description']))
            ->values()
            ->all();

        $heroStripFeatures = count($dynamicHeroFeatures) > 0 ? $dynamicHeroFeatures : $features;

        $aboutItem = $homePageItems->get('about', collect())->firstWhere('key', 'main');
        $aboutBadge = $aboutItem?->subtitle ?: HomePageContent::text('about.badge', ['schoolName' => $schoolName]);
        $aboutTitle = $aboutItem?->title ?: HomePageContent::text('about.title');
        $aboutDescription = $aboutItem?->description ?: HomePageContent::text('about.description');

        $dynamicAboutHighlights = $homePageItems
            ->get('about_highlights', collect())
            ->map(fn($item) => [
                'title' => $item->title,
                'description' => $item->description,
                'icon' => $item->icon,
                'color' => $item->color,
            ])
            ->filter(fn($item) => filled($item['title']) && filled($item['description']))
            ->values()
            ->all();

        if (count($dynamicAboutHighlights) > 0) {
            $aboutHighlights = $dynamicAboutHighlights;
        }

        $defaultAboutTones = array_column($aboutCards, 'tone');
        $dynamicAboutCards = $homePageItems
            ->get('about_cards', collect())
            ->values()
            ->map(fn($item, $index) => [
                'title' => $item->title,
                'description' => $item->description,
                'icon' => $item->icon,
                'tone' => $item->color ?: $defaultAboutTones[$index] ?? 'bg-cyan-100 text-cyan-700',
                'color' => $item->color,
            ])
            ->filter(fn($item) => filled($item['title']) && filled($item['description']))
            ->values()
            ->all();

        if (count($dynamicAboutCards) > 0) {
            $aboutCards = $dynamicAboutCards;
        }

        $featureItem = $homePageItems->get('platform_features', collect())->firstWhere('key', 'main');
        $featureBadge = $featureItem?->subtitle ?: HomePageContent::text('features.badge');
        $featureTitle = $featureItem?->title ?: HomePageContent::text('features.title');
        $featureTag = $featureItem?->value ?: HomePageContent::text('features.tag');

        $dynamicFeatures = $homePageItems
            ->get('platform_feature_cards', collect())
            ->map(fn($item) => [
                'title' => $item->title,
                'description' => $item->description,
                'icon' => $item->icon,
                'color' => $item->color,
            ])
            ->filter(fn($item) => filled($item['title']) && filled($item['description']))
            ->values()
            ->all();

        if (count($dynamicFeatures) > 0) {
            $features = $dynamicFeatures;
        }

        $programItem = $homePageItems->get('programs', collect())->firstWhere('key', 'main');
        $programBadge = $programItem?->subtitle ?: HomePageContent::text('programs.badge');
        $programTitle = $programItem?->title ?: HomePageContent::text('programs.title');
        $programDescription = $programItem?->description ?: HomePageContent::text('programs.description');

        $dynamicPrograms = $homePageItems
            ->get('program_cards', collect())
            ->map(fn($item) => [
                'level' => $item->subtitle,
                'title' => $item->title,
                'description' => $item->description,
                'icon' => $item->icon,
                'color' => $item->color,
            ])
            ->filter(fn($item) => filled($item['level']) && filled($item['title']) && filled($item['description']))
            ->values()
            ->all();

        if (count($dynamicPrograms) > 0) {
            $programs = $dynamicPrograms;
        }

        $facilityItem = $homePageItems->get('facilities', collect())->firstWhere('key', 'main');
        $facilityBadge = $facilityItem?->subtitle ?: HomePageContent::text('facilities.badge');
        $facilityTitle = $facilityItem?->title ?: HomePageContent::text('facilities.title');
        $facilityDescription = $facilityItem?->description ?: HomePageContent::text('facilities.description');
        $facilityImage = self::imageUrl($facilityItem?->image_path, 'images/study.jpg');

        $dynamicFacilityCards = $homePageItems
            ->get('facility_cards', collect())
            ->map(fn($item) => [
                'title' => $item->title,
                'description' => $item->description,
                'icon' => $item->icon,
            ])
            ->filter(fn($item) => filled($item['title']) && filled($item['description']))
            ->values()
            ->all();

        if (count($dynamicFacilityCards) > 0) {
            $facilityCards = $dynamicFacilityCards;
        }

        $admissionItem = $homePageItems->get('admission', collect())->firstWhere('key', 'main');
        $admissionBadge = $admissionItem?->subtitle ?: HomePageContent::text('admission.badge');
        $admissionTitle = $admissionItem?->title ?: HomePageContent::text('admission.title');
        $admissionDescription = $admissionItem?->description ?: HomePageContent::text('admission.description');

        $admissionIntakeItem = $homePageItems->get('admission_intake', collect())->firstWhere('key', 'main');
        $admissionIntakeLabel = $admissionIntakeItem?->subtitle ?: HomePageContent::text('admission.open_intake_label');
        $admissionIntakeTitle = $admissionIntakeItem?->title ?: HomePageContent::text('admission.open_intake_title');
        $admissionIntakeDescription = $admissionIntakeItem?->description ?: HomePageContent::text('admission.open_intake_description');

        $dynamicSteps = $homePageItems
            ->get('admission_steps', collect())
            ->map(fn($item) => [
                'title' => $item->title,
                'description' => $item->description,
                'color' => $item->color,
            ])
            ->filter(fn($item) => filled($item['title']) && filled($item['description']))
            ->values()
            ->all();

        if (count($dynamicSteps) > 0) {
            $steps = $dynamicSteps;
        }

        $faqItem = $homePageItems->get('faq', collect())->firstWhere('key', 'main');
        $faqBadge = $faqItem?->subtitle ?: HomePageContent::text('faq.badge');
        $faqTitle = $faqItem?->title ?: HomePageContent::text('faq.title');

        $faqHelpItem = $homePageItems->get('faq_help', collect())->firstWhere('key', 'main');
        $faqHelpLabel = $faqHelpItem?->subtitle ?: HomePageContent::text('faq.more_help_label');
        $faqHelpTitle = $faqHelpItem?->title ?: HomePageContent::text('faq.more_help_title');
        $faqHelpText = $faqHelpItem?->description ?: HomePageContent::text('faq.more_help_text');

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
        $contactBadge = $contactItem?->subtitle ?: HomePageContent::text('contact.badge');
        $contactTitle = $contactItem?->title ?: HomePageContent::text('contact.title');
        $contactDescription = $contactItem?->description ?: HomePageContent::text('contact.description');

        $contactCampusItem = $homePageItems->get('contact_campus', collect())->firstWhere('key', 'main');
        $contactCampusLabel = $contactCampusItem?->subtitle ?: HomePageContent::text('contact.campus_label');
        $contactCampusTitle = $contactCampusItem?->title ?: HomePageContent::text('contact.campus_title');
        $contactCampusText = $contactCampusItem?->description ?: HomePageContent::text('contact.campus_text');

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
        $footerTagline = $footerItem?->title ?: HomePageContent::text('footer.tagline');
        $footerDescription = $footerItem?->description ?: HomePageContent::text('footer.description');
        $footerExploreLabel = $footerItem?->subtitle ?: HomePageContent::text('footer.explore');
        $footerContactLabel = $footerItem?->value ?: HomePageContent::text('footer.contact');
        $footerCopyright = $footerItem?->meta['copyright'] ?? HomePageContent::text('footer.copyright');
        $footerLogo = self::imageUrl($footerItem?->image_path, 'images/techbridge-logo-mark.svg');

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

        $chatbotAnswers = array_map(function (array $item) use ($schoolName) {
            $item['answer'] = str_replace(':schoolName', $schoolName, $item['answer'] ?? '');

            return $item;
        }, self::configArray('chatbot.answers'));

        return compact(
            'schoolName',
            'dashboardRoute',
            'links',
            'features',
            'programs',
            'steps',
            'heroPoints',
            'aboutCards',
            'aboutHighlights',
            'facilityCards',
            'faqItems',
            'footerLinks',
            'contactCards',
            'chatbotAnswers',
            'heroHighlights',
            'brandTagline',
            'brandLogo',
            'heroBadge',
            'heroTitle',
            'heroDescription',
            'heroImage',
            'heroStripFeatures',
            'aboutBadge',
            'aboutTitle',
            'aboutDescription',
            'featureBadge',
            'featureTitle',
            'featureTag',
            'programBadge',
            'programTitle',
            'programDescription',
            'facilityBadge',
            'facilityTitle',
            'facilityDescription',
            'facilityImage',
            'admissionBadge',
            'admissionTitle',
            'admissionDescription',
            'admissionIntakeLabel',
            'admissionIntakeTitle',
            'admissionIntakeDescription',
            'faqBadge',
            'faqTitle',
            'faqHelpLabel',
            'faqHelpTitle',
            'faqHelpText',
            'contactBadge',
            'contactTitle',
            'contactDescription',
            'contactCampusLabel',
            'contactCampusTitle',
            'contactCampusText',
            'footerTagline',
            'footerDescription',
            'footerExploreLabel',
            'footerContactLabel',
            'footerCopyright',
            'footerLogo',
            'footerContacts',
        );
    }

    private static function configArray(string $key): array
    {
        $value = HomePageContent::get($key, []);

        return is_array($value) ? $value : [];
    }

    private static function imageUrl(?string $path, string $fallback): string
    {
        return $path ? route('public.storage', ['path' => $path]) : asset($fallback);
    }

    private static function resolveStatValue(mixed $value, int $studentsTotal, int $teachersTotal): string
    {
        return match ((string) $value) {
            'students_total' => number_format($studentsTotal),
            'teachers_total' => number_format($teachersTotal),
            default => (string) $value,
        };
    }
}
