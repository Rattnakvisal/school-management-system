{{-- =========================
    About Section
========================= --}}
<section id="about" class="mx-auto max-w-7xl px-4 py-16 sm:px-6">
    @php
        /*
        |--------------------------------------------------------------------------
        | About Icon Generator
        |--------------------------------------------------------------------------
        */
        $aboutIconSvg = function ($icon, $index) {
            $rawIcon = strtolower(trim((string) $icon));

            if (str_starts_with(trim((string) $icon), '<')) {
                $html = strip_tags((string) $icon, '<i><span><svg><path><circle><rect><line><polyline><polygon><g>');
                $html = preg_replace('/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);

                return $html ?: null;
            }

            return match (true) {
                str_contains($rawIcon, 'vision') || str_contains($rawIcon, 'target')
                    => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="9" />
                        <circle cx="12" cy="12" r="3" />
                        <path d="M12 3v3M12 18v3M3 12h3M18 12h3" />
                    </svg>',

                str_contains($rawIcon, 'star') || str_contains($rawIcon, 'culture')
                    => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2l3.5 7 7.5 1-5.5 5 1.5 8-7-4-7 4 1.5-8-5.5-5 7.5-1z" />
                    </svg>',

                str_contains($rawIcon, 'card') || str_contains($rawIcon, 'operation')
                    => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="16" rx="3" />
                        <path d="M8 9h8M8 13h5" />
                    </svg>',

                str_contains($rawIcon, 'shield') || str_contains($rawIcon, 'trust')
                    => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2l8 4v6c0 5-3.5 9.5-8 10-4.5-.5-8-5-8-10V6l8-4z" />
                        <path d="m9 12 2 2 4-4" />
                    </svg>',

                default => match ($index) {
                    1 => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M12 8v4l3 2" />
                        </svg>',

                    2 => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2l3.5 7 7.5 1-5.5 5 1.5 8-7-4-7 4 1.5-8-5.5-5 7.5-1z" />
                        </svg>',

                    3 => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 12h18M12 3v18" />
                        </svg>',

                    default
                        => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12l5 5L20 7" />
                        </svg>',
                },
            };
        };

        $aboutHighlightIconTones = [
            'bg-amber-300/15 text-amber-200 ring-1 ring-amber-200/20',
            'bg-emerald-300/15 text-emerald-200 ring-1 ring-emerald-200/20',
            'bg-sky-300/15 text-sky-200 ring-1 ring-sky-200/20',
            'bg-fuchsia-300/15 text-fuchsia-200 ring-1 ring-fuchsia-200/20',
        ];

        $aboutCardIconTones = [
            'bg-rose-50 text-rose-600 ring-1 ring-rose-100',
            'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100',
            'bg-sky-50 text-sky-600 ring-1 ring-sky-100',
            'bg-violet-50 text-violet-600 ring-1 ring-violet-100',
        ];

        $aboutIconToneStyle = function ($color) {
            $color = trim((string) $color);

            return preg_match('/^#[0-9A-Fa-f]{6}$/', $color)
                ? "color: {$color}; background-color: {$color}1A; border: 1px solid {$color}33;"
                : null;
        };
    @endphp

    <div class="grid gap-8 lg:grid-cols-12">

        {{-- =========================
            LEFT SIDE
        ========================= --}}
        <div data-reveal
            class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-slate-950 via-slate-900 to-blue-900 p-8 text-white shadow-xl lg:col-span-5">
            {{-- Glow Effects --}}
            <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-indigo-500/30 blur-3xl"></div>
            <div class="absolute -bottom-16 -left-16 h-48 w-48 rounded-full bg-cyan-400/20 blur-3xl"></div>

            {{-- Badge --}}
            <span
                class="relative inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-xs font-bold uppercase tracking-widest text-blue-200">
                <span class="h-2 w-2 rounded-full bg-blue-300"></span>
                {{ $aboutBadge }}
            </span>

            {{-- Title --}}
            <h2 class="relative mt-5 text-3xl font-bold sm:text-4xl">
                {{ $aboutTitle }}
            </h2>

            {{-- Description --}}
            <p class="relative mt-4 text-sm leading-7 text-slate-200 sm:text-base">
                {{ $aboutDescription }}
            </p>

            {{-- Highlights --}}
            @if (count($aboutHighlights))
                <div class="relative mt-8 grid gap-4 sm:grid-cols-2">
                    @foreach ($aboutHighlights as $highlight)
                        @php
                            $iconStyle = $aboutIconToneStyle($highlight['color'] ?? null);
                            $iconTone = $iconStyle
                                ? ''
                                : (($highlight['color'] ?? null) ?: $aboutHighlightIconTones[$loop->index % count($aboutHighlightIconTones)]);
                        @endphp

                        <div
                            class="group relative overflow-hidden rounded-2xl bg-white/10 p-4 backdrop-blur transition duration-300 hover:-translate-y-1 hover:bg-white/20">
                            {{-- Card Glow --}}
                            <div
                                class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-indigo-400/20 blur-2xl opacity-0 transition duration-300 group-hover:opacity-100">
                            </div>

                            {{-- Icon same style as Academic Programs --}}
                            <div
                                class="relative mb-3 flex h-12 w-12 items-center justify-center rounded-2xl {{ $iconTone }} shadow-md transition duration-300 group-hover:scale-110 [&_svg]:h-5 [&_svg]:w-5"
                                @if ($iconStyle) style="{{ $iconStyle }}" @endif>
                                {!! $aboutIconSvg($highlight['icon'] ?? null, $loop->index) !!}
                            </div>

                            <p class="relative text-xs font-bold uppercase tracking-wider text-blue-300">
                                {{ $highlight['title'] }}
                            </p>

                            <p class="relative mt-2 text-sm leading-6 text-slate-200">
                                {{ $highlight['description'] }}
                            </p>

                            <div
                                class="relative mt-3 h-1 w-10 rounded-full bg-gradient-to-r from-indigo-500 to-blue-500 transition-all duration-300 group-hover:w-16">
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- =========================
            RIGHT CARDS
        ========================= --}}
        @if (count($aboutCards))
            <div class="grid gap-5 sm:grid-cols-2 lg:col-span-7">
                @foreach ($aboutCards as $card)
                    @php
                        $iconStyle = $aboutIconToneStyle($card['color'] ?? null);
                        $legacyTone = $card['color'] ?? ($card['tone'] ?? null);
                        $iconTone = $iconStyle
                            ? ''
                            : ($legacyTone ?: $aboutCardIconTones[$loop->index % count($aboutCardIconTones)]);
                    @endphp

                    <article data-reveal style="--d:{{ number_format(0.06 * $loop->iteration, 2) }}s"
                        class="group relative overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-2 hover:border-indigo-200 hover:shadow-xl">
                        {{-- Glow --}}
                        <div
                            class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-indigo-400/20 blur-3xl opacity-0 transition duration-300 group-hover:opacity-100">
                        </div>

                        {{-- Icon same style as Academic Programs --}}
                        <div
                            class="relative flex h-12 w-12 items-center justify-center rounded-2xl {{ $iconTone }} shadow-md transition duration-300 group-hover:scale-110 [&_svg]:h-5 [&_svg]:w-5"
                            @if ($iconStyle) style="{{ $iconStyle }}" @endif>
                            {!! $aboutIconSvg($card['icon'] ?? null, $loop->index) !!}
                        </div>

                        <p class="relative mt-5 text-xs font-bold uppercase tracking-widest text-slate-400">
                            {{ $card['title'] }}
                        </p>

                        <p class="relative mt-2 text-base font-semibold leading-7 text-slate-900">
                            {{ $card['description'] }}
                        </p>

                        <div
                            class="relative mt-6 h-1.5 w-12 rounded-full bg-gradient-to-r from-indigo-500 to-blue-500 transition-all duration-300 group-hover:w-20">
                        </div>
                    </article>
                @endforeach
            </div>
        @endif

    </div>
</section>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const elements = document.querySelectorAll("[data-reveal]");

        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = 1;
                    entry.target.style.transform = "translateY(0)";
                }
            });
        });

        elements.forEach(el => {
            el.style.opacity = 0;
            el.style.transform = "translateY(20px)";
            el.style.transition = "all 0.6s ease";
            observer.observe(el);
        });
    });
</script>
