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
        $defaultCourseItems = self::configArray('course.items') ?: self::configArray('admission.steps');
        $courseCards = array_values(array_map(
            fn($item, $index) => self::courseCardData($item, $index),
            array_values($defaultCourseItems),
            array_keys(array_values($defaultCourseItems))
        ));
        $aboutCards = self::configArray('about.cards');
        $aboutHighlights = self::configArray('about.highlights');
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

        $links = array_values(array_filter(
            $links,
            fn($link) => ($link['href'] ?? null) !== '#facilities'
        ));
        $links = array_map(fn($link) => array_merge($link, [
            'href' => ($link['href'] ?? null) === '#admission' ? '#course' : ($link['href'] ?? '#'),
        ]), $links);
        $links = array_slice($links, 0, 6);

        $homeHeroItem = $homePageItems->get('hero', collect())->firstWhere('key', 'main');
        $heroBadge = $homeHeroItem?->subtitle ?: HomePageContent::text('hero.badge');
        $heroTitle = $homeHeroItem?->title ?: HomePageContent::text('hero.title');
        $heroDescription = $homeHeroItem?->description ?: HomePageContent::text('hero.description', ['schoolName' => $schoolName]);
        $heroImage = self::imageUrl($homeHeroItem?->image_path, 'images/3D.png');

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
        $aboutImage = self::imageUrl($aboutItem?->image_path, 'images/8865364.png');

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

        $courseItem = $homePageItems->get('course', collect())->firstWhere('key', 'main')
            ?: $homePageItems->get('admission', collect())->firstWhere('key', 'main');
        $courseBadge = $courseItem?->subtitle ?: HomePageContent::text('course.badge', [], HomePageContent::text('admission.badge'));
        $courseTitle = $courseItem?->title ?: HomePageContent::text('course.title', [], HomePageContent::text('admission.title'));
        $courseDescription = $courseItem?->description ?: HomePageContent::text('course.description', [], HomePageContent::text('admission.description'));

        $courseFeaturedItem = $homePageItems->get('course_featured', collect())->firstWhere('key', 'main')
            ?: $homePageItems->get('admission_intake', collect())->firstWhere('key', 'main');
        $courseFeaturedLabel = $courseFeaturedItem?->subtitle ?: HomePageContent::text('course.featured_label', [], HomePageContent::text('admission.open_intake_label'));
        $courseFeaturedTitle = $courseFeaturedItem?->title ?: HomePageContent::text('course.featured_title', [], HomePageContent::text('admission.open_intake_title'));
        $courseFeaturedDescription = $courseFeaturedItem?->description ?: HomePageContent::text('course.featured_description', [], HomePageContent::text('admission.open_intake_description'));

        $dynamicCourseCards = ($homePageItems->get('course_cards', collect())->isNotEmpty()
                ? $homePageItems->get('course_cards', collect())
                : $homePageItems->get('admission_steps', collect()))
            ->values()
            ->map(function ($item, $index) use ($defaultCourseItems) {
                $fallback = array_values($defaultCourseItems)[$index] ?? [];
                $meta = is_array($item->meta) ? $item->meta : [];

                return self::courseCardData([
                    'category' => $meta['category'] ?? ($item->subtitle ?: ($fallback['category'] ?? 'Course')),
                    'title' => $item->title,
                    'description' => $item->description,
                    'image_url' => self::imageUrl($item->image_path, $fallback['image'] ?? 'images/study.jpg'),
                    'instructor_name' => $meta['instructor_name'] ?? ($fallback['instructor_name'] ?? ''),
                    'instructor_role' => $meta['instructor_role'] ?? ($fallback['instructor_role'] ?? ''),
                    'lessons' => $meta['lessons'] ?? ($fallback['lessons'] ?? ''),
                    'duration' => $meta['duration'] ?? ($fallback['duration'] ?? ''),
                    'rating' => $meta['rating'] ?? ($fallback['rating'] ?? ''),
                    'review_count' => $meta['review_count'] ?? ($fallback['review_count'] ?? ''),
                    'price' => $meta['price'] ?? ($fallback['price'] ?? ''),
                    'button_label' => $meta['button_label'] ?? ($fallback['button_label'] ?? ''),
                    'color' => $item->color ?: ($fallback['color'] ?? '#10b981'),
                ], $index);
            })
            ->filter(fn($item) => filled($item['title']) && filled($item['description']))
            ->values()
            ->all();

        if (count($dynamicCourseCards) > 0) {
            $courseCards = $dynamicCourseCards;
        }

        $steps = $courseCards;
        $admissionBadge = $courseBadge;
        $admissionTitle = $courseTitle;
        $admissionDescription = $courseDescription;
        $admissionIntakeLabel = $courseFeaturedLabel;
        $admissionIntakeTitle = $courseFeaturedTitle;
        $admissionIntakeDescription = $courseFeaturedDescription;

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
        $footerTagline = $brandTagline;
        $footerDescription = $footerItem?->description ?: HomePageContent::text('footer.description');
        $footerExploreLabel = $footerItem?->subtitle ?: HomePageContent::text('footer.explore');
        $footerContactLabel = $footerItem?->value ?: HomePageContent::text('footer.contact');
        $footerCopyright = $footerItem?->meta['copyright'] ?? HomePageContent::text('footer.copyright');
        $footerLogo = $brandLogo;

        $dynamicFooterLinks = $homePageItems
            ->get('footer_links', collect())
            ->map(fn($item) => ['label' => $item->title, 'href' => $item->value])
            ->filter(fn($item) => filled($item['label']) && filled($item['href']))
            ->values()
            ->all();

        if (count($dynamicFooterLinks) > 0) {
            $footerLinks = $dynamicFooterLinks;
        }

        $footerLinks = array_values(array_filter(
            $footerLinks,
            fn($link) => ($link['href'] ?? null) !== '#facilities'
        ));
        $footerLinks = array_map(fn($link) => array_merge($link, [
            'href' => ($link['href'] ?? null) === '#admission' ? '#course' : ($link['href'] ?? '#'),
        ]), $footerLinks);

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
            'courseCards',
            'steps',
            'heroPoints',
            'aboutCards',
            'aboutHighlights',
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
            'aboutImage',
            'featureBadge',
            'featureTitle',
            'featureTag',
            'programBadge',
            'programTitle',
            'programDescription',
            'courseBadge',
            'courseTitle',
            'courseDescription',
            'courseFeaturedLabel',
            'courseFeaturedTitle',
            'courseFeaturedDescription',
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

    private static function courseCardData(array $item, int $index): array
    {
        $imageUrl = $item['image_url'] ?? null;
        $image = $item['image'] ?? null;

        return [
            'category' => $item['category'] ?? $item['level'] ?? 'Course',
            'title' => $item['title'] ?? '',
            'description' => $item['description'] ?? '',
            'image_url' => $imageUrl ?: asset($image ?: ['images/study.jpg', 'images/AI.png', 'images/school.jpg'][$index % 3]),
            'instructor_name' => $item['instructor_name'] ?? '',
            'instructor_role' => $item['instructor_role'] ?? '',
            'lessons' => $item['lessons'] ?? '',
            'duration' => $item['duration'] ?? '',
            'rating' => $item['rating'] ?? '',
            'review_count' => $item['review_count'] ?? '',
            'price' => $item['price'] ?? '',
            'button_label' => $item['button_label'] ?? '',
            'color' => $item['color'] ?? '#10b981',
        ];
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
