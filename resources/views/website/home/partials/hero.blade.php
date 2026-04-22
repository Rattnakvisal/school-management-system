@php
    $heroFeatureStrip = array_slice($features, 0, 4);
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
                    class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-4 py-2 text-blue-700 ring-1 ring-blue-100 {{ $isKhmerLocale ? 'khmer-meta text-xs font-semibold tracking-[0.04em]' : 'text-[11px] font-extrabold uppercase tracking-[0.24em]' }}">
                    <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                    {{ __('home.hero.badge') }}
                </span>

                <h1 data-reveal data-first style="--d:.12s"
                    class="{{ $isKhmerLocale ? 'khmer-display mt-7 max-w-3xl text-4xl font-semibold tracking-[-0.02em] text-slate-950 sm:text-5xl lg:text-[4rem] leading-[1.16] sm:leading-[1.12] lg:leading-[1.08]' : '[font-family:Outfit,_sans-serif] mt-7 max-w-3xl text-4xl font-semibold tracking-[-0.04em] text-slate-950 sm:text-5xl lg:text-[4.15rem] leading-[0.98]' }}">
                    {{ __('home.hero.title') }}
                </h1>

                <p data-reveal data-first style="--d:.18s"
                    class="mt-6 max-w-xl text-slate-500 {{ $isKhmerLocale ? 'text-[15px] leading-9 sm:text-[1.05rem]' : 'text-lg leading-8' }}">
                    {{ __('home.hero.description', ['schoolName' => $schoolName]) }}
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
                        <div class="flex items-start gap-3 text-slate-600">
                            <span
                                class="mt-1 inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600 ring-1 ring-blue-100">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="m5 12 5 5L20 7" />
                                </svg>
                            </span>
                            <p class="{{ $isKhmerLocale ? 'text-sm leading-8' : 'text-base leading-7' }}">
                                {{ $point }}</p>
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
                        <img src="{{ asset('images/school.jpg') }}" alt="{{ __('home.hero.image_alt') }}"
                            class="h-[24rem] w-full rounded-[2.4rem] object-cover sm:h-[28rem] lg:h-[34rem] lg:rounded-[38%]" />
                        <div
                            class="absolute inset-3 rounded-[2.4rem] bg-gradient-to-tr from-slate-950/25 via-transparent to-transparent lg:rounded-[38%]">
                        </div>
                    </div>

                    <div class="home-hero-stats absolute right-0 top-7 hidden w-52 gap-4 lg:grid">
                        @foreach ($heroHighlights as $item)
                            <article data-reveal style="--d:{{ number_format(0.08 * $loop->iteration, 2) }}s"
                                class="home-hero-stat rounded-[1.75rem] border border-white/80 bg-white/95 p-4 shadow-[0_24px_50px_-34px_rgba(15,23,42,0.42)] backdrop-blur">
                                <div class="flex items-start gap-3">
                                    <span
                                        class="inline-flex h-12 w-12 items-center justify-center rounded-2xl text-white shadow-lg {{ $loop->first ? 'bg-gradient-to-br from-emerald-500 to-teal-500' : ($loop->index === 1 ? 'bg-gradient-to-br from-blue-600 to-indigo-500' : 'bg-gradient-to-br from-violet-600 to-fuchsia-500') }}">
                                        @if ($loop->first)
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"
                                                aria-hidden="true">
                                                <path
                                                    d="M12 3 1 9l11 6 9-4.91V17h2V9L12 3Zm-7 9.75V17l7 4 7-4v-4.25l-7 3.82-7-3.82Z" />
                                            </svg>
                                        @elseif ($loop->index === 1)
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"
                                                aria-hidden="true">
                                                <path d="M4 13h4v7H4v-7Zm6-5h4v12h-4V8Zm6-4h4v16h-4V4Z" />
                                            </svg>
                                        @else
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"
                                                aria-hidden="true">
                                                <path
                                                    d="M16 11c1.66 0 2.99-1.79 2.99-4S17.66 3 16 3s-3 1.79-3 4 1.34 4 3 4Zm-8 0c1.66 0 2.99-1.79 2.99-4S9.66 3 8 3 5 4.79 5 7s1.34 4 3 4Zm0 2c-2.33 0-7 1.17-7 3.5V21h14v-4.5C15 14.17 10.33 13 8 13Zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.96 1.97 3.45V21h6v-4.5c0-2.33-4.67-3.5-7-3.5Z" />
                                            </svg>
                                        @endif
                                    </span>
                                    <div>
                                        <p class="text-xs font-medium text-slate-500">{{ $item['label'] }}</p>
                                        <p class="mt-1 text-3xl font-bold tracking-[-0.03em] text-slate-950">
                                            {{ $item['value'] }}
                                        </p>
                                        <p class="mt-1 text-xs font-semibold text-emerald-500">
                                            +{{ 4 + $loop->iteration * 2 }}% {{ __('home.hero.trend_suffix') }}
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
            class="home-hero-strip relative mt-10 overflow-hidden rounded-[2rem] bg-gradient-to-r from-slate-950 via-blue-950 to-slate-900 px-5 py-6 text-white shadow-[0_28px_70px_-36px_rgba(15,23,42,0.75)] sm:px-6 lg:px-8">
            <div
                class="pointer-events-none absolute inset-y-0 right-0 w-72 bg-[radial-gradient(circle_at_center,rgba(59,130,246,0.3),transparent_70%)]">
            </div>
            <div class="relative grid gap-4 lg:grid-cols-4">
                @foreach ($heroFeatureStrip as $item)
                    <article
                        class="flex gap-4 border-white/10 lg:border-r {{ $loop->last ? 'lg:border-r-0' : '' }} lg:pr-5">
                        <span
                            class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-600/90 text-white shadow-[0_16px_30px_-20px_rgba(59,130,246,0.9)]">
                            @if ($loop->first)
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path
                                        d="M12 3 1 9l11 6 9-4.91V17h2V9L12 3Zm-7 9.75V17l7 4 7-4v-4.25l-7 3.82-7-3.82Z" />
                                </svg>
                            @elseif ($loop->index === 1)
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path
                                        d="M16 11c1.66 0 2.99-1.79 2.99-4S17.66 3 16 3s-3 1.79-3 4 1.34 4 3 4Zm-8 0c1.66 0 2.99-1.79 2.99-4S9.66 3 8 3 5 4.79 5 7s1.34 4 3 4Zm0 2c-2.33 0-7 1.17-7 3.5V21h14v-4.5C15 14.17 10.33 13 8 13Zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.96 1.97 3.45V21h6v-4.5c0-2.33-4.67-3.5-7-3.5Z" />
                                </svg>
                            @elseif ($loop->index === 2)
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" aria-hidden="true">
                                    <rect x="4" y="5" width="16" height="15" rx="2" />
                                    <path d="M16 3v4M8 3v4M4 11h16" />
                                </svg>
                            @else
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" aria-hidden="true">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z" />
                                    <path d="m9 12 2 2 4-4" />
                                </svg>
                            @endif
                        </span>
                        <div>
                            <p class="text-base font-semibold text-white">{{ $item['title'] }}</p>
                            <p class="mt-1 text-sm leading-7 text-blue-100/85">
                                {{ $item['description'] }}
                            </p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</section>
