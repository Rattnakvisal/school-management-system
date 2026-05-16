{{-- Hero Section --}}
@php
    $heroFeatureStrip = array_slice($heroStripFeatures ?? ($features ?? []), 0, 4);

    $normalizeHeroIcon = function ($icon, string $fallback = 'check') {
        $rawIcon = strtolower(trim((string) $icon));

        return match (true) {
            str_contains($rawIcon, 'graduation') || str_contains($rawIcon, 'student') => 'graduation',
            str_contains($rawIcon, 'chart') || str_contains($rawIcon, 'report') || str_contains($rawIcon, 'growth')
            => 'chart',
            str_contains($rawIcon, 'user') || str_contains($rawIcon, 'people') || str_contains($rawIcon, 'community')
            => 'users',
            str_contains($rawIcon, 'clock') || str_contains($rawIcon, 'time') || str_contains($rawIcon, 'access')
            => 'clock',
            str_contains($rawIcon, 'shield') ||
            str_contains($rawIcon, 'safe') ||
            str_contains($rawIcon, 'security') ||
            str_contains($rawIcon, 'trust')
            => 'shield',
            str_contains($rawIcon, 'calendar') ||
            str_contains($rawIcon, 'schedule') ||
            str_contains($rawIcon, 'planning')
            => 'calendar',
            str_contains($rawIcon, 'check') => 'check',
            default => trim((string) preg_replace('/[^a-z0-9]+/', '-', $rawIcon), '-') ?: $fallback,
        };
    };

    $sanitizeIconHtml = function ($html) {
        $html = trim((string) $html);

        if ($html === '' || !str_starts_with($html, '<')) {
            return null;
        }

        $html = strip_tags(
            $html,
            '<i><span><svg><path><circle><rect><line><polyline><polygon><g><defs><clipPath><ellipse>',
        );

        $html = (string) preg_replace('/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
        $html = (string) preg_replace(
            '/\s+(href|xlink:href)\s*=\s*("[^"]*javascript:[^"]*"|\'[^\']*javascript:[^\']*\'|[^\s>]*javascript:[^\s>]*)/i',
            '',
            $html,
        );

        return $html !== '' ? $html : null;
    };

    $heroIconSvg = function ($icon, string $fallback = 'check') use ($normalizeHeroIcon, $sanitizeIconHtml) {
        $customIcon = $sanitizeIconHtml($icon);

        if ($customIcon) {
            return $customIcon;
        }

        return match ($normalizeHeroIcon($icon, $fallback)) {
            'graduation'
            => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 3 1 9l11 6 9-4.91V17h2V9L12 3Zm-7 9.75V17l7 4 7-4v-4.25l-7 3.82-7-3.82Z" /></svg>',
            'chart'
            => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M4 13h4v7H4v-7Zm6-5h4v12h-4V8Zm6-4h4v16h-4V4Z" /></svg>',
            'clock'
            => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" /></svg>',
            'shield'
            => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z" /><path d="m9 12 2 2 4-4" /></svg>',
            'calendar'
            => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="4" y="5" width="16" height="15" rx="2" /><path d="M16 3v4M8 3v4M4 11h16" /></svg>',
            'check'
            => '<svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="m5 12 5 5L20 7" /></svg>',
            default
            => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M16 11c1.66 0 2.99-1.79 2.99-4S17.66 3 16 3s-3 1.79-3 4 1.34 4 3 4Zm-8 0c1.66 0 2.99-1.79 2.99-4S9.66 3 8 3 5 4.79 5 7s1.34 4 3 4Zm0 2c-2.33 0-7 1.17-7 3.5V21h14v-4.5C15 14.17 10.33 13 8 13Zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.96 1.97 3.45V21h6v-4.5c0-2.33-4.67-3.5-7-3.5Z" /></svg>',
        };
    };

    $heroToneStyle = function ($color, string $fallback = '#10b981') {
        $color = trim((string) $color);
        $color = preg_match('/^#[0-9A-Fa-f]{6}$/', $color) ? $color : $fallback;

        return "color: {$color}; background-color: {$color}18; border-color: {$color}26;";
    };

    $heroPointList = array_slice($heroPoints ?? [], 0, 3);
    $heroVisualStats = array_slice($heroHighlights ?? [], 0, 3);
    $heroFeatureList = array_slice($heroFeatureStrip, 0, 2);
@endphp

<section class="mx-auto max-w-7xl px-4 pb-14 pt-5 sm:px-6 lg:pb-20">
    <div
        class="relative isolate overflow-hidden bg-[#eafff7] px-5 py-10 shadow-[0_26px_80px_-58px_rgba(15,118,110,0.55)] sm:px-8 lg:px-12 lg:py-14">
        <div class="pointer-events-none absolute inset-0 opacity-70"
            style="background-image: linear-gradient(135deg, rgba(20,184,166,.1) 0 1px, transparent 1px), linear-gradient(135deg, transparent 0 92%, rgba(16,185,129,.13) 92%); background-size: 22px 22px, 100% 100%;">
        </div>

        <svg class="pointer-events-none absolute left-[48%] top-16 hidden h-12 w-12 text-emerald-500 lg:block"
            viewBox="0 0 48 48" fill="none" aria-hidden="true">
            <path d="M24 8c6 5.5 9 10.5 9 15.2A9 9 0 1 1 15 23.2C15 18.5 18 13.5 24 8Z" stroke="currentColor"
                stroke-width="3" />
            <path d="M24 14v23M16 24c4 1.5 7 4 8 8M32 24c-4 1.5-7 4-8 8" stroke="currentColor" stroke-width="3"
                stroke-linecap="round" />
        </svg>

        <div class="relative grid gap-10 lg:grid-cols-[minmax(0,0.93fr)_minmax(25rem,0.82fr)] lg:items-center">
            <div class="max-w-2xl">
                <span data-reveal data-first style="--d:.06s"
                    class="inline-flex items-center gap-2 bg-emerald-50 px-3 py-1.5 text-[10px] font-extrabold uppercase tracking-[0.18em] text-emerald-700 ring-1 ring-emerald-100">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    {{ $heroBadge ?? \App\Support\HomePageContent::text('hero.badge') }}
                </span>

                <h1 data-reveal data-first style="--d:.12s"
                    class="[font-family:Outfit,_sans-serif] mt-5 max-w-xl text-4xl font-extrabold leading-[1.02] tracking-tight text-[#10221e] sm:text-5xl lg:text-[4rem]">
                    {{ $heroTitle ?? \App\Support\HomePageContent::text('hero.title') }}
                </h1>

                <p data-reveal data-first style="--d:.18s" class="mt-5 max-w-xl text-base leading-7 text-slate-600">
                    {{ $heroDescription ?? \App\Support\HomePageContent::text('hero.description', ['schoolName' => $schoolName]) }}
                </p>

                <div data-reveal data-first style="--d:.24s" class="mt-7 flex flex-wrap items-center gap-3">
                    @auth
                        <a href="{{ route($dashboardRoute) }}"
                            class="inline-flex items-center justify-center gap-2 rounded-full bg-emerald-500 px-5 py-3 text-xs font-extrabold uppercase tracking-wide text-white shadow-[0_16px_30px_-18px_rgba(16,185,129,0.95)] transition hover:-translate-y-0.5 hover:bg-emerald-600">
                            {{ \App\Support\HomePageContent::text('actions.open_dashboard') }}
                            <i class="fa-solid fa-arrow-right text-[11px]"></i>
                        </a>
                    @else
                        <a href="#contact"
                            class="inline-flex items-center justify-center gap-2 rounded-full bg-emerald-500 px-5 py-3 text-xs font-extrabold uppercase tracking-wide text-white shadow-[0_16px_30px_-18px_rgba(16,185,129,0.95)] transition hover:-translate-y-0.5 hover:bg-emerald-600">
                            {{ \App\Support\HomePageContent::text('actions.contact_admissions') }}
                            <i class="fa-solid fa-arrow-right text-[11px]"></i>
                        </a>

                        <a href="#programs"
                            class="inline-flex items-center justify-center gap-2 rounded-full px-4 py-3 text-xs font-extrabold uppercase tracking-wide text-[#10221e] transition hover:text-emerald-600">
                            {{ \App\Support\HomePageContent::text('actions.explore_programs') }}
                            <i class="fa-solid fa-arrow-right-long text-[11px]"></i>
                        </a>
                    @endauth
                </div>

                <div data-reveal data-first style="--d:.30s" class="mt-8 flex flex-wrap items-center gap-5">
                    <a href="#about"
                        class="group inline-flex items-center gap-3 text-left text-sm font-bold text-[#10221e] transition hover:text-emerald-600">
                        <span
                            class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-[#071714] text-white shadow-[0_18px_28px_-20px_rgba(7,23,20,0.9)] transition group-hover:bg-emerald-500">
                            <i class="fa-solid fa-play text-xs"></i>
                        </span>
                        <span>
                            <span class="block">Watch school story</span>
                            <span class="block text-xs font-semibold text-slate-500">Discover our learning
                                culture</span>
                        </span>
                    </a>

                    @if (!empty($heroPointList))
                        <div class="grid w-full gap-2 sm:max-w-xl">
                            @foreach ($heroPointList as $point)
                                @php
                                    $pointIcon = is_array($point) ? $point['icon'] ?? 'check' : 'check';
                                    $pointText = is_array($point) ? $point['description'] ?? '' : $point;
                                @endphp
                                @if (filled($pointText))
                                    <div class="flex items-center gap-3 text-sm font-semibold text-slate-600">
                                        <span
                                            class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-white text-emerald-600 shadow-sm ring-1 ring-emerald-100 [&_i]:text-xs [&_svg]:h-3.5 [&_svg]:w-3.5">
                                            {!! $heroIconSvg($pointIcon, 'check') !!}
                                        </span>
                                        <span>{{ $pointText }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div data-reveal data-first style="--d:.16s" class="relative min-h-[26rem] lg:min-h-[31rem]">
                <div
                    class="absolute right-0 top-1/2 h-[22rem] w-[22rem] -translate-y-1/2 rounded-[3rem] bg-emerald-400 sm:h-[25rem] sm:w-[25rem] lg:right-4 lg:h-[28rem] lg:w-[28rem]">
                </div>

                <div class="absolute right-5 top-9 hidden grid-cols-5 gap-2 text-emerald-300 sm:grid">
                    @for ($i = 0; $i < 20; $i++)
                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    @endfor
                </div>

                <img src="{{ $heroImage ?? asset('images/3D.png') }}"
                    alt="{{ \App\Support\HomePageContent::text('hero.image_alt') }}"
                    class="relative z-10 mx-auto h-[25rem] w-full max-w-[30rem] object-contain drop-shadow-[0_34px_42px_rgba(15,118,110,0.24)] sm:h-[30rem] lg:ml-auto" />

                @if (!empty($heroVisualStats))
                    @php $primaryHighlight = $heroVisualStats[0]; @endphp
                    <article
                        class="absolute bottom-10 left-0 z-20 w-48 bg-white/95 p-4 shadow-[0_22px_46px_-28px_rgba(15,23,42,0.48)] ring-1 ring-emerald-100 backdrop-blur sm:left-5">
                        <div class="flex -space-x-2">
                            @foreach ($heroVisualStats as $stat)
                                <span
                                    class="flex h-8 w-8 items-center justify-center rounded-full border-2 border-white bg-white text-emerald-600 shadow-sm [&_i]:text-xs [&_svg]:h-3.5 [&_svg]:w-3.5"
                                    style="{{ $heroToneStyle($stat['color'] ?? null) }}">
                                    {!! $heroIconSvg($stat['icon'] ?? 'users', 'users') !!}
                                </span>
                            @endforeach
                        </div>
                        <p class="mt-3 text-3xl font-extrabold tracking-tight text-[#10221e]">
                            {{ $primaryHighlight['value'] }}
                        </p>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                            {{ $primaryHighlight['label'] }}
                        </p>
                        @if (!empty($primaryHighlight['trend']))
                            <p class="mt-1 text-xs font-bold text-emerald-500">{{ $primaryHighlight['trend'] }}</p>
                        @endif
                    </article>

                    @if (count($heroVisualStats) > 1)
                        <div class="absolute right-0 top-20 z-20 hidden w-44 space-y-2 lg:block">
                            @foreach (array_slice($heroVisualStats, 1) as $stat)
                                <article
                                    class="flex items-center gap-3 bg-white/95 p-3 shadow-[0_18px_38px_-28px_rgba(15,23,42,0.45)] ring-1 ring-emerald-100 backdrop-blur">
                                    <span
                                        class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full border text-emerald-600 [&_i]:text-sm [&_svg]:h-4 [&_svg]:w-4"
                                        style="{{ $heroToneStyle($stat['color'] ?? null) }}">
                                        {!! $heroIconSvg($stat['icon'] ?? 'chart', 'chart') !!}
                                    </span>
                                    <span class="min-w-0">
                                        <span class="block truncate text-lg font-extrabold leading-none text-[#10221e]">
                                            {{ $stat['value'] }}
                                        </span>
                                        <span
                                            class="mt-1 block truncate text-[10px] font-bold uppercase tracking-wide text-slate-500">
                                            {{ $stat['label'] }}
                                        </span>
                                    </span>
                                </article>
                            @endforeach
                        </div>
                    @endif
                @endif

                <div
                    class="absolute left-7 top-12 z-20 hidden h-12 w-12 items-center justify-center rounded-full bg-white text-emerald-500 shadow-lg ring-1 ring-emerald-100 sm:flex">
                    <i class="fa-solid fa-arrow-trend-up"></i>
                </div>
            </div>
        </div>
    </div>
</section>
