@php
    $heroFeatureStrip = array_slice($heroStripFeatures ?? $features, 0, 4);
    $normalizeHeroIcon = function ($icon, string $fallback = 'check') {
        $rawIcon = strtolower(trim((string) $icon));

        if (str_contains($rawIcon, 'graduation') || str_contains($rawIcon, 'student')) {
            return 'graduation';
        }

        if (str_contains($rawIcon, 'chart') || str_contains($rawIcon, 'report') || str_contains($rawIcon, 'growth')) {
            return 'chart';
        }

        if (str_contains($rawIcon, 'user') || str_contains($rawIcon, 'people') || str_contains($rawIcon, 'community')) {
            return 'users';
        }

        if (str_contains($rawIcon, 'clock') || str_contains($rawIcon, 'time') || str_contains($rawIcon, 'access')) {
            return 'clock';
        }

        if (str_contains($rawIcon, 'shield') || str_contains($rawIcon, 'safe') || str_contains($rawIcon, 'security') || str_contains($rawIcon, 'trust')) {
            return 'shield';
        }

        if (str_contains($rawIcon, 'calendar') || str_contains($rawIcon, 'schedule') || str_contains($rawIcon, 'planning')) {
            return 'calendar';
        }

        if (str_contains($rawIcon, 'check')) {
            return 'check';
        }

        $icon = $rawIcon;
        $icon = trim((string) preg_replace('/[^a-z0-9]+/', '-', $icon), '-');

        return $icon !== '' ? $icon : $fallback;
    };
    $sanitizeIconHtml = function ($html) {
        $html = trim((string) $html);

        if ($html === '' || !str_starts_with($html, '<')) {
            return null;
        }

        $html = strip_tags($html, '<i><span><svg><path><circle><rect><line><polyline><polygon><g><defs><clipPath><ellipse>');
        $html = (string) preg_replace('/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
        $html = (string) preg_replace('/\s+(href|xlink:href)\s*=\s*("[^"]*javascript:[^"]*"|\'[^\']*javascript:[^\']*\'|[^\s>]*javascript:[^\s>]*)/i', '', $html);

        return $html !== '' ? $html : null;
    };
    $heroIconSvg = function ($icon, string $fallback = 'check') use ($normalizeHeroIcon, $sanitizeIconHtml) {
        $customIcon = $sanitizeIconHtml($icon);

        if ($customIcon) {
            return $customIcon;
        }

        return match ($normalizeHeroIcon($icon, $fallback)) {
            'graduation' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 3 1 9l11 6 9-4.91V17h2V9L12 3Zm-7 9.75V17l7 4 7-4v-4.25l-7 3.82-7-3.82Z" /></svg>',
            'chart' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M4 13h4v7H4v-7Zm6-5h4v12h-4V8Zm6-4h4v16h-4V4Z" /></svg>',
            'clock' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" /></svg>',
            'shield' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z" /><path d="m9 12 2 2 4-4" /></svg>',
            'calendar' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="4" y="5" width="16" height="15" rx="2" /><path d="M16 3v4M8 3v4M4 11h16" /></svg>',
            'check' => '<svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m5 12 5 5L20 7" /></svg>',
            default => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M16 11c1.66 0 2.99-1.79 2.99-4S17.66 3 16 3s-3 1.79-3 4 1.34 4 3 4Zm-8 0c1.66 0 2.99-1.79 2.99-4S9.66 3 8 3 5 4.79 5 7s1.34 4 3 4Zm0 2c-2.33 0-7 1.17-7 3.5V21h14v-4.5C15 14.17 10.33 13 8 13Zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.96 1.97 3.45V21h6v-4.5c0-2.33-4.67-3.5-7-3.5Z" /></svg>',
        };
    };
@endphp

<section class="mx-auto max-w-7xl px-4 pb-12 pt-6 sm:px-6 lg:pb-16 lg:pt-8">
    <div class="home-hero-panel relative overflow-hidden p-6 sm:p-8 lg:p-10">
        <div
            class="home-hero-halo pointer-events-none absolute inset-y-0 right-0 w-[46%] bg-[radial-gradient(circle_at_center,rgba(191,219,254,0.38),transparent_68%)]">
        </div>
        <div
            class="home-hero-halo pointer-events-none absolute -left-16 top-12 h-64 w-64 rounded-full bg-cyan-200/45 blur-3xl">
        </div>
        <div
            class="home-hero-halo home-hero-halo--slow pointer-events-none absolute right-10 top-16 h-72 w-72 rounded-full bg-blue-300/30 blur-3xl">
        </div>
        <div
            class="home-hero-halo home-hero-halo--slow pointer-events-none absolute bottom-0 right-0 h-80 w-80 rounded-full bg-indigo-200/35 blur-3xl">
        </div>

        <div class="relative grid gap-10 lg:grid-cols-[minmax(0,1.05fr)_minmax(0,0.95fr)] lg:items-center">
            <div class="max-w-2xl">
                <span data-reveal data-first style="--d:.06s"
                    class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-4 py-2 text-[11px] font-extrabold uppercase tracking-[0.24em] text-blue-700 ring-1 ring-blue-100">
                    <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                    {{ $heroBadge ?? __('home.hero.badge') }}
                </span>

                <h1 data-reveal data-first style="--d:.12s"
                    class="[font-family:Outfit,_sans-serif] mt-7 max-w-3xl text-4xl font-semibold leading-[0.98] tracking-[-0.04em] text-slate-950 sm:text-5xl lg:text-[4.15rem]">
                    {{ $heroTitle ?? __('home.hero.title') }}
                </h1>

                <p data-reveal data-first style="--d:.18s"
                    class="mt-6 max-w-xl text-lg leading-8 text-slate-500">
                    {{ $heroDescription ?? __('home.hero.description', ['schoolName' => $schoolName]) }}
                </p>

                <div data-reveal data-first style="--d:.24s" class="mt-8 flex flex-wrap items-center gap-3">
                    @auth
                        <a href="{{ route($dashboardRoute) }}"
                            class="home-hero-button home-hero-button--primary home-cta inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-blue-500 px-6 py-3.5 text-sm font-bold text-white shadow-[0_20px_34px_-22px_rgba(37,99,235,0.8)] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_24px_40px_-22px_rgba(37,99,235,0.8)]">
                            {{ __('home.actions.open_dashboard') }}
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                aria-hidden="true">
                                <path d="M5 12h14M13 5l7 7-7 7" />
                            </svg>
                        </a>
                    @else
                        <a href="#contact"
                            class="home-hero-button home-hero-button--primary home-cta inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-blue-500 px-6 py-3.5 text-sm font-bold text-white shadow-[0_20px_34px_-22px_rgba(37,99,235,0.8)] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_24px_40px_-22px_rgba(37,99,235,0.8)]">
                            {{ __('home.actions.contact_admissions') }}
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                aria-hidden="true">
                                <path d="M5 12h14M13 5l7 7-7 7" />
                            </svg>
                        </a>
                        <a href="#programs"
                            class="home-hero-button home-hero-button--secondary inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white/90 px-6 py-3.5 text-sm font-semibold text-slate-700 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-blue-200 hover:text-slate-950">
                            <span
                                class="inline-flex h-5 w-5 items-center justify-center rounded-full border border-blue-200 text-blue-600">
                                <svg class="h-2.5 w-2.5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path d="M8 5v14l11-7z" />
                                </svg>
                            </span>
                            {{ __('home.actions.explore_programs') }}
                        </a>
                    @endauth
                </div>

                <div data-reveal data-first style="--d:.30s" class="mt-8 grid gap-3 sm:max-w-xl">
                        @foreach ($heroPoints as $point)
                            @php
                                $pointText = is_array($point) ? $point['description'] ?? '' : $point;
                                $pointIcon = is_array($point) ? $point['icon'] ?? 'check' : 'check';
                            @endphp
                            <div class="flex items-start gap-3 text-slate-600">
                                <span
                                    class="mt-1 inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600 ring-1 ring-blue-100 [&_i]:text-[0.95rem] [&_svg]:h-4 [&_svg]:w-4">
                                    {!! $heroIconSvg($pointIcon, 'check') !!}
                                </span>
                            <p class="text-base leading-7">
                                {{ $pointText }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div data-reveal data-first style="--d:.16s" class="relative">
                <div class="relative mx-auto max-w-[42rem]">
                    <div
                        class="home-hero-halo pointer-events-none absolute inset-x-8 top-5 h-[88%] rounded-[42%] border border-blue-200/80 bg-gradient-to-br from-blue-100/70 to-transparent">
                    </div>
                    <div
                        class="home-hero-halo home-hero-halo--slow pointer-events-none absolute -left-4 top-20 h-[72%] w-[78%] rounded-[45%] border border-blue-300/40 bg-blue-200/30">
                    </div>
                    <div
                        class="home-hero-halo pointer-events-none absolute -right-4 top-14 h-56 w-56 rounded-full bg-white/60 blur-3xl">
                    </div>
                    <div
                        class="home-hero-media-shell relative overflow-hidden rounded-[3rem] border border-white/80 bg-white/80 p-3 shadow-[0_30px_60px_-30px_rgba(37,99,235,0.35)] backdrop-blur lg:rounded-[40%]">
                        <img src="{{ $heroImage ?? asset('images/school.jpg') }}" alt="{{ __('home.hero.image_alt') }}"
                            class="h-[24rem] w-full rounded-[2.4rem] object-cover sm:h-[28rem] lg:h-[34rem] lg:rounded-[38%]" />
                        <div
                            class="absolute inset-3 rounded-[2.4rem] bg-gradient-to-tr from-slate-950/25 via-transparent to-transparent lg:rounded-[38%]">
                        </div>
                    </div>

                    <div class="home-hero-stats absolute right-0 top-7 hidden w-52 gap-4 lg:grid">
                        @foreach ($heroHighlights as $item)
                            @php
                                    $statIcon = $item['icon'] ?? match ($loop->index) {
                                        0 => 'graduation',
                                        1 => 'chart',
                                        default => 'users',
                                    };
                                $statColor = $item['color'] ?? match ($loop->index) {
                                    0 => 'emerald',
                                    1 => 'blue',
                                    default => 'violet',
                                };
                                $statColorClass = [
                                    'emerald' => 'bg-gradient-to-br from-emerald-500 to-teal-500',
                                    'blue' => 'bg-gradient-to-br from-blue-600 to-indigo-500',
                                    'violet' => 'bg-gradient-to-br from-violet-600 to-fuchsia-500',
                                    'amber' => 'bg-gradient-to-br from-amber-500 to-orange-500',
                                    'rose' => 'bg-gradient-to-br from-rose-500 to-pink-500',
                                    'cyan' => 'bg-gradient-to-br from-cyan-500 to-sky-500',
                                ][$statColor] ?? 'bg-gradient-to-br from-blue-600 to-indigo-500';
                            @endphp
                            <article data-reveal style="--d:{{ number_format(0.08 * $loop->iteration, 2) }}s"
                                class="home-hero-stat rounded-[1.75rem] border border-white/80 bg-white/95 p-4 shadow-[0_24px_50px_-34px_rgba(15,23,42,0.42)] backdrop-blur">
                                <div class="flex items-start gap-3">
                                        <span
                                            class="inline-flex h-12 w-12 items-center justify-center rounded-2xl text-white shadow-lg {{ $statColorClass }} [&_i]:text-xl [&_svg]:h-5 [&_svg]:w-5">
                                            {!! $heroIconSvg($statIcon, 'users') !!}
                                        </span>
                                    <div>
                                        <p class="text-xs font-medium text-slate-500">{{ $item['label'] }}</p>
                                        <p class="mt-1 text-3xl font-bold tracking-[-0.03em] text-slate-950">
                                            {{ $item['value'] }}
                                        </p>
                                        <p class="mt-1 text-xs font-semibold text-emerald-500">
                                            {{ $item['trend'] ?? '+' . (4 + $loop->iteration * 2) . '% ' . __('home.hero.trend_suffix') }}
                                        </p>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div data-reveal style="--d:.34s"
            class="home-hero-strip relative mt-10 overflow-hidden rounded-[2rem] bg-gradient-to-br from-slate-950 via-blue-950 to-slate-900 p-4 text-white shadow-[0_28px_70px_-36px_rgba(15,23,42,0.75)] sm:p-5 lg:p-6">
            <div
                class="pointer-events-none absolute inset-y-0 right-0 w-72 bg-[radial-gradient(circle_at_center,rgba(59,130,246,0.3),transparent_70%)]">
            </div>
            <div class="relative grid gap-4" style="grid-template-columns: repeat(auto-fit, minmax(min(100%, 15rem), 1fr));">
                @foreach ($heroFeatureStrip as $item)
                    @php
                            $stripIcon = $item['icon'] ?? match ($loop->index) {
                                0 => 'graduation',
                                1 => 'users',
                                2 => 'calendar',
                                default => 'shield',
                            };
                    @endphp
                    <article
                        class="group min-h-[15rem] rounded-[1.5rem] border border-white/10 bg-white/[0.06] p-5 shadow-[inset_0_1px_0_rgba(255,255,255,0.08)] backdrop-blur transition duration-200 hover:-translate-y-1 hover:border-blue-300/40 hover:bg-white/[0.09]">
                            <span
                                class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-600/90 text-white shadow-[0_16px_30px_-20px_rgba(59,130,246,0.9)] transition group-hover:scale-105 [&_i]:text-xl [&_svg]:h-5 [&_svg]:w-5">
                                {!! $heroIconSvg($stripIcon, 'shield') !!}
                            </span>
                        <div class="mt-5">
                            <p class="text-lg font-bold leading-7 text-white">{{ $item['title'] }}</p>
                            <p class="mt-3 text-sm leading-7 text-blue-100/85">
                                {{ $item['description'] }}
                            </p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</section>
