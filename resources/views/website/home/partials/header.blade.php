@php
    $localeFlags = [
        'en' => asset('images/united-kingdom.png'),
        'km' => asset('images/flag.png'),
    ];
@endphp

<header class="sticky top-0 z-50 px-4 pt-4 sm:px-6 lg:pt-5">
    <div data-reveal data-first style="--d:.02s" class="mx-auto max-w-7xl">
        <div
            class="home-header-shell flex items-center justify-between gap-4 rounded-[2rem] border border-white/80 bg-white/92 px-5 py-4 shadow-[0_24px_70px_-38px_rgba(37,99,235,0.35)] ring-1 ring-slate-200/70 backdrop-blur-2xl">
            <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3 transition hover:opacity-95">
                <span
                    class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-slate-950 via-blue-950 to-slate-900 text-white shadow-[0_18px_30px_-20px_rgba(15,23,42,0.9)]">
                    <svg class="h-5 w-5 text-cyan-200" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M12 2 2 7l10 5 10-5-10-5Zm0 7L2 4v9l10 5 10-5V4l-10 5Z" />
                    </svg>
                </span>

                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <p
                            class="{{ $isKhmerLocale ? 'khmer-display truncate text-xl font-semibold leading-tight tracking-[-0.01em] text-slate-950 sm:text-2xl' : '[font-family:Outfit,_sans-serif] truncate text-xl font-semibold leading-none tracking-tight text-slate-950 sm:text-2xl' }}">
                            {{ $schoolName }}
                        </p>
                        <span
                            class="hidden rounded-full bg-blue-50 px-2 py-1 text-[10px] font-extrabold uppercase tracking-[0.18em] text-blue-700 ring-1 ring-blue-100 sm:inline-flex">
                            {{ $currentLocaleCode }}
                        </span>
                    </div>
                    <p
                        class="mt-1 truncate {{ $isKhmerLocale ? 'khmer-meta text-[12px] font-semibold text-slate-500' : 'text-[11px] font-extrabold uppercase tracking-[0.26em] text-slate-500' }}">
                        {{ __('home.brand.tagline') }}
                    </p>
                </div>
            </a>

            <nav class="hidden items-center gap-7 lg:flex">
                @foreach ($links as $link)
                    <a href="{{ $link['href'] }}"
                        class="home-nav-link relative text-sm font-semibold tracking-[-0.01em] transition duration-200"
                        :class="active === '{{ $link['href'] }}'
                            ? 'text-blue-700'
                            : 'text-slate-600 hover:text-slate-950'">
                        {{ $link['label'] }}
                        <span class="absolute inset-x-0 -bottom-2 mx-auto h-0.5 w-7 rounded-full bg-blue-600 transition"
                            :class="active === '{{ $link['href'] }}' ? 'opacity-100' : 'opacity-0'"></span>
                    </a>
                @endforeach
            </nav>

            <div class="flex items-center gap-2 sm:gap-3">
                <div class="relative hidden lg:block" @click.outside="langDesktopOpen = false">
                    <button type="button" @click="langDesktopOpen = !langDesktopOpen"
                        class="inline-flex items-center gap-2.5 rounded-full border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-blue-200 hover:text-slate-950"
                        :class="langDesktopOpen ? 'border-blue-300 ring-4 ring-blue-100' : ''"
                        aria-haspopup="true" :aria-expanded="langDesktopOpen.toString()">
                        <img src="{{ $localeFlags[$currentLocale] ?? null }}" alt="{{ $currentLocaleLabel }}"
                            class="h-6 w-9 rounded-md object-cover ring-1 ring-slate-200">
                        <span
                            class="rounded-full bg-blue-50 px-2 py-1 text-[10px] font-extrabold uppercase tracking-[0.18em] text-blue-700 ring-1 ring-blue-100">
                            {{ $currentLocaleCode }}
                        </span>
                        <svg class="h-4 w-4 text-slate-400 transition" :class="langDesktopOpen ? 'rotate-180' : ''"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            aria-hidden="true">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </button>

                    <div x-show="langDesktopOpen" x-cloak x-transition.origin.top.right
                        class="absolute right-0 top-[calc(100%+0.75rem)] z-50 w-64 overflow-hidden rounded-[1.5rem] border border-slate-200 bg-white p-2 shadow-[0_28px_60px_-30px_rgba(15,23,42,0.32)]">
                        <div class="mb-2 flex items-center gap-3 rounded-[1.2rem] bg-slate-50 px-3 py-3">
                            <img src="{{ $localeFlags[$currentLocale] ?? null }}" alt="{{ $currentLocaleLabel }}"
                                class="h-10 w-10 rounded-2xl object-cover ring-1 ring-slate-200">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $currentLocaleLabel }}</p>
                                <p class="text-[10px] font-extrabold uppercase tracking-[0.18em] text-slate-400">
                                    {{ $currentLocaleCode }}</p>
                            </div>
                        </div>

                        @foreach ($supportedLocales as $locale)
                            <a href="{{ route('language.switch', $locale) }}"
                                class="flex items-center justify-between rounded-2xl px-3 py-3 text-sm transition {{ $currentLocale === $locale ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-950' }}">
                                <span class="flex items-center gap-3">
                                    <img src="{{ $localeFlags[$locale] ?? null }}"
                                        alt="{{ $localeLabels[$locale] ?? strtoupper($locale) }}"
                                        class="h-8 w-8 rounded-xl object-cover ring-1 {{ $currentLocale === $locale ? 'ring-white/20' : 'ring-slate-200' }}">
                                    <span class="font-semibold">{{ $localeLabels[$locale] ?? strtoupper($locale) }}</span>
                                </span>
                                <span
                                    class="rounded-full px-2 py-1 text-[10px] font-extrabold uppercase tracking-[0.18em] {{ $currentLocale === $locale ? 'bg-white/15 text-blue-100' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $localeCodeLabels[$locale] ?? strtoupper($locale) }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>

                @auth
                    <a href="{{ route($dashboardRoute) }}"
                        class="home-header-cta hidden items-center justify-center gap-2 rounded-full bg-gradient-to-r from-blue-600 to-blue-500 px-5 py-3 text-sm font-bold text-white shadow-[0_18px_34px_-24px_rgba(37,99,235,0.8)] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_22px_38px_-24px_rgba(37,99,235,0.85)] lg:inline-flex">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" aria-hidden="true">
                            <path d="M3 12h7V3H3v9Zm11 9h7v-7h-7v7Zm0-18v7h7V3h-7ZM3 21h7v-5H3v5Z" />
                        </svg>
                        {{ __('home.actions.dashboard') }}
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="home-header-cta hidden items-center justify-center gap-2 rounded-full bg-gradient-to-r from-blue-600 to-blue-500 px-5 py-3 text-sm font-bold text-white shadow-[0_18px_34px_-24px_rgba(37,99,235,0.8)] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_22px_38px_-24px_rgba(37,99,235,0.85)] lg:inline-flex">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" aria-hidden="true">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                            <path d="M10 17l5-5-5-5" />
                            <path d="M15 12H3" />
                        </svg>
                        {{ __('home.actions.login') }}
                    </a>
                @endauth

                <button type="button" @click="open = !open; langDesktopOpen = false; langMobileOpen = false"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-700 shadow-sm lg:hidden"
                    aria-label="{{ __('home.actions.toggle_menu') }}">
                    <svg x-show="!open" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="open" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <path d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div x-show="open" x-cloak x-transition class="px-4 pb-3 pt-2 sm:px-6 lg:hidden">
        <div class="mx-auto max-w-7xl">
            <div
                class="rounded-[1.75rem] border border-slate-200 bg-white/95 p-4 shadow-[0_20px_50px_-35px_rgba(15,23,42,0.32)] ring-1 ring-slate-200/60 backdrop-blur-xl">
                <div class="grid gap-2 text-sm font-semibold text-slate-700">
                    @foreach ($links as $link)
                        <a href="{{ $link['href'] }}" @click="open = false; langMobileOpen = false"
                            class="rounded-2xl px-4 py-3 transition hover:bg-slate-100">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </div>

                <div class="mt-4 border-t border-slate-200 pt-4" @click.outside="langMobileOpen = false">
                    <button type="button" @click="langMobileOpen = !langMobileOpen"
                        class="flex w-full items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-left text-sm font-semibold text-slate-700 transition hover:bg-white">
                        <span class="flex items-center gap-3">
                            <img src="{{ $localeFlags[$currentLocale] ?? null }}" alt="{{ $currentLocaleLabel }}"
                                class="h-8 w-12 rounded-lg object-cover ring-1 ring-slate-200">
                            <span>
                                <span class="block text-sm font-semibold text-slate-700">{{ $currentLocaleLabel }}</span>
                                <span
                                    class="mt-1 inline-flex rounded-full bg-white px-2 py-1 text-[10px] font-extrabold uppercase tracking-[0.18em] text-blue-700 ring-1 ring-blue-100">
                                    {{ $currentLocaleCode }}
                                </span>
                            </span>
                        </span>
                        <svg class="h-4 w-4 text-slate-400 transition" :class="langMobileOpen ? 'rotate-180' : ''"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            aria-hidden="true">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </button>

                    <div x-show="langMobileOpen" x-cloak x-transition class="mt-3 grid gap-2">
                        @foreach ($supportedLocales as $locale)
                            <a href="{{ route('language.switch', $locale) }}"
                                class="flex items-center justify-between rounded-2xl border px-4 py-3 text-sm font-semibold transition {{ $currentLocale === $locale ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}">
                                <span class="flex items-center gap-3">
                                    <img src="{{ $localeFlags[$locale] ?? null }}"
                                        alt="{{ $localeLabels[$locale] ?? strtoupper($locale) }}"
                                        class="h-8 w-12 rounded-lg object-cover ring-1 {{ $currentLocale === $locale ? 'ring-white/20' : 'ring-slate-200' }}">
                                    <span>{{ $localeLabels[$locale] ?? strtoupper($locale) }}</span>
                                </span>
                                <span
                                    class="rounded-full px-2 py-1 text-[10px] font-extrabold uppercase tracking-[0.18em] {{ $currentLocale === $locale ? 'bg-white/15 text-blue-100' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $localeCodeLabels[$locale] ?? strtoupper($locale) }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="mt-4 grid gap-2">
                    @auth
                        <a href="{{ route($dashboardRoute) }}"
                            class="home-header-cta inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-blue-500 px-4 py-3 text-center text-sm font-bold text-white">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" aria-hidden="true">
                                <path d="M3 12h7V3H3v9Zm11 9h7v-7h-7v7Zm0-18v7h7V3h-7ZM3 21h7v-5H3v5Z" />
                            </svg>
                            {{ __('home.actions.dashboard') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="home-header-cta inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 to-blue-500 px-4 py-3 text-center text-sm font-bold text-white">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" aria-hidden="true">
                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                                <path d="M10 17l5-5-5-5" />
                                <path d="M15 12H3" />
                            </svg>
                            {{ __('home.actions.login') }}
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</header>
