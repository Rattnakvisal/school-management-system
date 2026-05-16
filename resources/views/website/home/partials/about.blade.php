{{-- =========================
    About Section
========================= --}}
<section id="about" class="mx-auto max-w-7xl px-4 py-14 sm:px-6">
    @php
        $aboutIconSvg = function ($icon, $index = 0) {
            $rawIcon = strtolower(trim((string) $icon));

            if (str_starts_with(trim((string) $icon), '<')) {
                $html = strip_tags((string) $icon, '<i><span><svg><path><circle><rect><line><polyline><polygon><g>');
                $html = preg_replace('/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);

                return $html ?: null;
            }

            return match (true) {
                str_contains($rawIcon, 'vision') || str_contains($rawIcon, 'target')
                    => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9" /><circle cx="12" cy="12" r="3" /><path d="M12 3v3M12 18v3M3 12h3M18 12h3" /></svg>',
                str_contains($rawIcon, 'star') || str_contains($rawIcon, 'culture')
                    => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.5 7 7.5 1-5.5 5 1.5 8-7-4-7 4 1.5-8-5.5-5 7.5-1Z" /></svg>',
                str_contains($rawIcon, 'leader') || str_contains($rawIcon, 'user')
                    => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="m17 11 2 2 4-4" /></svg>',
                str_contains($rawIcon, 'shield') || str_contains($rawIcon, 'trust')
                    => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l8 4v6c0 5-3.5 9.5-8 10-4.5-.5-8-5-8-10V6l8-4Z" /><path d="m9 12 2 2 4-4" /></svg>',
                default => match ($index % 4) {
                    1 => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.5 7 7.5 1-5.5 5 1.5 8-7-4-7 4 1.5-8-5.5-5 7.5-1Z" /></svg>',
                    2 => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" /><path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15Z" /></svg>',
                    3 => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12h18M12 3v18" /></svg>',
                    default => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12l5 5L20 7" /></svg>',
                },
            };
        };

        $aboutToneStyle = function ($color, string $fallback = '#10b981') {
            $color = trim((string) $color);
            $color = preg_match('/^#[0-9A-Fa-f]{6}$/', $color) ? $color : $fallback;

            return "color: {$color}; background-color: {$color}14; border-color: {$color}24;";
        };

        $aboutFeatureCards = array_slice($aboutCards ?? [], 0, 4);
        $aboutStats = array_slice($aboutHighlights ?? [], 0, 2);
        $aboutStrip = array_slice($aboutCards ?? [], 0, 4);
    @endphp

    <div class="relative overflow-hidden bg-[#effff9]">
        <div class="grid items-center gap-8 lg:grid-cols-[minmax(0,0.92fr)_minmax(0,1fr)]">
            <div data-reveal class="relative min-h-[24rem] overflow-hidden">
                <div class="absolute bottom-0 left-0 right-0 h-24 bg-emerald-400/15"></div>
                <div class="absolute left-8 top-12 h-44 w-44 rounded-full border border-emerald-300/50"></div>
                <div class="absolute left-20 top-20 h-64 w-64 rounded-full border border-emerald-200/70"></div>

                <img src="{{ $aboutImage ?? asset('images/8865364.png') }}" alt="{{ $aboutTitle }}"
                    class="relative z-10 mx-auto h-[27rem] w-full max-w-[32rem] object-contain object-bottom drop-shadow-[0_28px_38px_rgba(15,118,110,0.16)]" />

                <div class="absolute right-8 top-8 hidden grid-cols-6 gap-1.5 text-emerald-300 sm:grid">
                    @for ($i = 0; $i < 30; $i++)
                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    @endfor
                </div>
            </div>

            <div data-reveal class="px-5 pb-10 pt-2 sm:px-8 lg:px-10 lg:py-12">
                <span class="text-[10px] font-extrabold uppercase tracking-[0.22em] text-emerald-600">
                    {{ $aboutBadge }}
                </span>

                <h2
                    class="[font-family:Outfit,_sans-serif] mt-3 max-w-2xl text-3xl font-extrabold leading-tight tracking-tight text-[#10221e] sm:text-4xl">
                    {{ $aboutTitle }}
                </h2>

                <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-600">
                    {{ $aboutDescription }}
                </p>

                @if (!empty($aboutFeatureCards))
                    <div class="mt-5 grid gap-2 sm:grid-cols-2">
                        @foreach (array_slice($aboutFeatureCards, 0, 2) as $card)
                            <div class="flex items-center gap-2 text-sm font-bold text-[#10221e]">
                                <span
                                    class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full border text-emerald-600 [&_i]:text-xs [&_svg]:h-3.5 [&_svg]:w-3.5"
                                    style="{{ $aboutToneStyle($card['color'] ?? null, ['#10b981', '#0ea5e9'][$loop->index % 2]) }}">
                                    {!! $aboutIconSvg($card['icon'] ?? null, $loop->index) !!}
                                </span>
                                {{ $card['title'] ?? '' }}
                            </div>
                        @endforeach
                    </div>
                @endif

                @if (!empty($aboutStats))
                    <div class="mt-8 grid max-w-2xl gap-4 sm:grid-cols-2">
                        @foreach ($aboutStats as $stat)
                            <article
                                class="group border border-emerald-900/5 bg-white/75 p-4 shadow-[0_16px_36px_-32px_rgba(15,118,110,0.55)] backdrop-blur transition hover:-translate-y-1 hover:bg-white">
                                <div class="flex items-start gap-3">
                                    <span
                                        class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full border text-emerald-600 [&_i]:text-sm [&_svg]:h-4 [&_svg]:w-4"
                                        style="{{ $aboutToneStyle($stat['color'] ?? null, ['#10b981', '#0ea5e9'][$loop->index % 2]) }}">
                                        {!! $aboutIconSvg($stat['icon'] ?? null, $loop->index) !!}
                                    </span>
                                    <span class="min-w-0">
                                        <span class="block text-2xl font-extrabold tracking-tight text-emerald-500">
                                            {{ $stat['title'] ?? '' }}
                                        </span>
                                        <span class="mt-1 block text-[12px] font-bold leading-5 text-slate-500">
                                            {{ $stat['description'] ?? '' }}
                                        </span>
                                    </span>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif

                <div class="mt-8">
                    <a href="#programs"
                        class="inline-flex items-center justify-center rounded-full bg-emerald-500 px-5 py-3 text-xs font-extrabold uppercase tracking-wide text-white shadow-[0_16px_30px_-18px_rgba(16,185,129,0.95)] transition hover:-translate-y-0.5 hover:bg-emerald-600">
                        More About
                    </a>
                </div>
            </div>
        </div>

        @if (!empty($aboutStrip))
            <div
                class="grid gap-px bg-emerald-500 text-white sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($aboutStrip as $item)
                    <div class="flex items-center justify-center gap-2 bg-emerald-500 px-4 py-3 text-center text-[11px] font-extrabold uppercase tracking-[0.16em]">
                        <span class="[&_i]:text-sm [&_svg]:h-4 [&_svg]:w-4">
                            {!! $aboutIconSvg($item['icon'] ?? null, $loop->index) !!}
                        </span>
                        {{ $item['title'] ?? '' }}
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
