<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $schoolName ?? 'Schooli' }} | {{ __('home.meta.title_suffix') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="website-home {{ app()->getLocale() === 'km' ? 'khmer-ui' : '[font-family:Manrope,_sans-serif]' }} bg-slate-100 text-slate-900 antialiased"
    x-data="{
        open: false,
        langDesktopOpen: false,
        langMobileOpen: false,
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
    x-effect="document.body.classList.toggle('overflow-hidden', showBanner)"
    @keydown.escape.window="if (showBanner) closeBanner(); langDesktopOpen = false; langMobileOpen = false">
    @php
        $schoolName = $schoolName ?? 'Schooli';
        $studentsTotal = $studentsTotal ?? 0;
        $teachersTotal = $teachersTotal ?? 0;
        $dashboardRoute = $dashboardRoute ?? 'home';

        $currentLocale = app()->getLocale();
        $supportedLocales = config('app.supported_locales', ['en', 'km']);
        $localeLabels = trans('home.locales');
        $localeCodeLabels = [
            'en' => 'EN',
            'km' => 'KH',
        ];
        $currentLocaleLabel = $localeLabels[$currentLocale] ?? strtoupper($currentLocale);
        $currentLocaleCode = $localeCodeLabels[$currentLocale] ?? strtoupper($currentLocale);
        $isKhmerLocale = $currentLocale === 'km';
        $links = trans('home.links');
        $features = trans('home.features.items');
        $programs = trans('home.programs.items');
        $steps = trans('home.admission.steps');
        $heroPoints = trans('home.hero.points');
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
