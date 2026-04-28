{{-- =========================
    Facilities Section
========================= --}}
<section id="facilities" class="mx-auto max-w-7xl px-4 py-16 sm:px-6">

    @php
        /*
        |--------------------------------------------------------------------------
        | Facility Icon Generator (Improved)
        |--------------------------------------------------------------------------
        */
        $facilityIconSvg = function ($icon) {
            $rawIcon = strtolower(trim((string) $icon));

            if (str_starts_with(trim((string) $icon), '<')) {
                $html = strip_tags((string) $icon, '<i><span><svg><path><circle><rect><line><polyline><polygon><g>');
                $html = preg_replace('/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);

                return $html ?: null;
            }

            return match (true) {
                str_contains($rawIcon, 'computer') || str_contains($rawIcon, 'lab')
                    => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="4" y="4" width="16" height="12" rx="2" />
                        <path d="M8 20h8M12 16v4" />
                    </svg>',

                str_contains($rawIcon, 'book') || str_contains($rawIcon, 'library')
                    => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                        <path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15Z" />
                    </svg>',

                str_contains($rawIcon, 'sport') || str_contains($rawIcon, 'activity')
                    => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="9" />
                        <path d="M5.6 5.6 18.4 18.4" />
                    </svg>',

                default => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 21V7l8-4 8 4v14" />
                        <path d="M9 21v-7h6v7" />
                    </svg>',
            };
        };
    @endphp

    <div class="grid gap-8 lg:grid-cols-12 lg:items-center">

        {{-- =========================
            LEFT IMAGE
        ========================= --}}
        <div data-reveal class="lg:col-span-7">
            <div
                class="group relative overflow-hidden rounded-[2rem] border border-white/70 bg-white/70 p-3 shadow-xl backdrop-blur">

                <img src="{{ $facilityImage ?? asset('images/study.jpg') }}" alt="{{ __('home.facilities.image_alt') }}"
                    class="h-80 w-full rounded-[1.6rem] object-cover transition duration-500 group-hover:scale-105 lg:h-[32rem]" />

                {{-- Overlay --}}
                <div class="absolute inset-3 rounded-[1.6rem] bg-gradient-to-tr from-slate-950/40 via-transparent"></div>

                {{-- Floating Label --}}
                <div class="absolute bottom-8 left-8 rounded-2xl bg-white/90 px-5 py-4 shadow-lg backdrop-blur">
                    <p class="text-xs font-extrabold uppercase tracking-[0.22em] text-indigo-700">
                        {{ $facilityBadge ?? __('home.facilities.badge') }}
                    </p>

                    <p class="mt-1 text-lg font-bold text-slate-950">
                        {{ $facilityTitle ?? __('home.facilities.title') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- =========================
            RIGHT CONTENT
        ========================= --}}
        <div data-reveal style="--d:.08s" class="lg:col-span-5">
            <div class="relative overflow-hidden rounded-[2rem] border border-slate-200 bg-white p-7 shadow-sm">

                {{-- Glow --}}
                <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-indigo-400/20 blur-3xl"></div>

                {{-- Badge --}}
                <span
                    class="relative inline-flex items-center gap-2 rounded-full bg-indigo-50 px-4 py-2 text-[11px] font-extrabold uppercase tracking-[0.28em] text-indigo-700 ring-1 ring-indigo-100">
                    <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                    {{ $facilityBadge ?? __('home.facilities.badge') }}
                </span>

                {{-- Title --}}
                <h2 class="mt-4 text-3xl font-semibold text-slate-950">
                    {{ $facilityTitle ?? __('home.facilities.title') }}
                </h2>

                {{-- Description --}}
                <p class="mt-4 text-sm leading-7 text-slate-600">
                    {{ $facilityDescription ?? __('home.facilities.description') }}
                </p>

                {{-- =========================
                    FACILITY CARDS (UPDATED STYLE)
                ========================= --}}
                <div class="mt-7 space-y-4">
                    @foreach ($facilityCards as $facility)
                        <article data-reveal style="--d:{{ number_format(0.05 * $loop->iteration, 2) }}s"
                            class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-indigo-200 hover:shadow-md">
                            {{-- Glow --}}
                            <div
                                class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-indigo-400/20 blur-2xl opacity-0 transition group-hover:opacity-100">
                            </div>

                            <div class="relative flex items-start gap-3">

                                {{-- ICON (PROGRAM STYLE) --}}
                                <span
                                    class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 to-blue-600 text-white shadow-md transition group-hover:scale-110 [&_svg]:h-5 [&_svg]:w-5">
                                    {!! $facilityIconSvg($facility['icon'] ?? null) !!}
                                </span>

                                {{-- TEXT --}}
                                <div>
                                    <p class="text-sm font-bold text-slate-900 group-hover:text-indigo-700 transition">
                                        {{ $facility['title'] }}
                                    </p>

                                    <p class="mt-1 text-sm leading-7 text-slate-600">
                                        {{ $facility['description'] }}
                                    </p>

                                    {{-- Bottom Line --}}
                                    <div
                                        class="mt-3 h-1 w-10 rounded-full bg-gradient-to-r from-indigo-500 to-blue-500 transition-all duration-300 group-hover:w-16">
                                    </div>
                                </div>

                            </div>
                        </article>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</section>
