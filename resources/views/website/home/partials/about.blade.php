@php
    $sanitizeAboutIconHtml = function ($html) {
        $html = trim((string) $html);

        if ($html === '' || !str_starts_with($html, '<')) {
            return null;
        }

        $html = strip_tags($html, '<i><span><svg><path><circle><rect><line><polyline><polygon><g><defs><clipPath><ellipse>');
        $html = (string) preg_replace('/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
        $html = (string) preg_replace('/\s+(href|xlink:href)\s*=\s*("[^"]*javascript:[^"]*"|\'[^\']*javascript:[^\']*\'|[^\s>]*javascript:[^\s>]*)/i', '', $html);

        return $html !== '' ? $html : null;
    };
    $aboutIconSvg = function ($icon, int $index) use ($sanitizeAboutIconHtml) {
        $customIcon = $sanitizeAboutIconHtml($icon);

        if ($customIcon) {
            return $customIcon;
        }

        $rawIcon = strtolower(trim((string) $icon));

        if (str_contains($rawIcon, 'plus') || str_contains($rawIcon, 'vision')) {
            return '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12h18" /><path d="M12 3v18" /></svg>';
        }

        if (str_contains($rawIcon, 'star') || str_contains($rawIcon, 'culture')) {
            return '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.5 7.09L23 10.18l-5.5 5.36L18.82 23 12 19.27 5.18 23l1.32-7.46L1 10.18l7.5-1.09L12 2z" /></svg>';
        }

        if (str_contains($rawIcon, 'card') || str_contains($rawIcon, 'operation')) {
            return '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="3" /><path d="M8 9h8M8 13h5" /></svg>';
        }

        return match ($index) {
            1 => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12h18" /><path d="M12 3v18" /></svg>',
            2 => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.5 7.09L23 10.18l-5.5 5.36L18.82 23 12 19.27 5.18 23l1.32-7.46L1 10.18l7.5-1.09L12 2z" /></svg>',
            3 => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="3" /><path d="M8 9h8M8 13h5" /></svg>',
            default => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 5 5L20 7" /></svg>',
        };
    };
@endphp

<section id="about" class="mx-auto max-w-7xl px-4 pb-12 sm:px-6">
    <div class="grid gap-6 lg:grid-cols-12">
        <div data-reveal
            class="overflow-hidden rounded-[2rem] border border-slate-900/10 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-900 p-7 text-white shadow-lg lg:col-span-5 lg:p-8">
            <span
                class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-4 py-2 text-[11px] font-extrabold uppercase tracking-[0.28em] text-cyan-100">
                <span class="h-2 w-2 rounded-full bg-cyan-300"></span>
                {{ $aboutBadge }}
            </span>

            <h2 class="[font-family:Outfit,_sans-serif] mt-5 text-3xl font-semibold sm:text-4xl">
                {{ $aboutTitle }}
            </h2>

            <p class="mt-4 max-w-xl text-slate-100 text-sm leading-8 sm:text-base">
                {{ $aboutDescription }}
            </p>

            @if (count($aboutHighlights) > 0)
                <div class="mt-8 grid gap-3 sm:grid-cols-2">
                    @foreach ($aboutHighlights as $highlight)
                        <div class="home-lift-card rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                            <span
                                class="mb-3 inline-flex h-9 w-9 items-center justify-center rounded-xl bg-white/10 text-cyan-100 [&_i]:text-lg [&_svg]:h-5 [&_svg]:w-5">
                                {!! $aboutIconSvg($highlight['icon'] ?? null, $loop->index) !!}
                            </span>
                            <p class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-cyan-200">
                                {{ $highlight['title'] }}</p>
                            <p class="mt-2 text-sm text-slate-100 leading-7">
                                {{ $highlight['description'] }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        @if (count($aboutCards) > 0)
            <div class="grid gap-4 sm:grid-cols-2 lg:col-span-7">
                @foreach ($aboutCards as $card)
                    <article data-reveal style="--d:{{ number_format(0.06 * $loop->iteration, 2) }}s"
                        class="home-lift-card rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                        <div
                            class="inline-flex h-12 w-12 items-center justify-center rounded-2xl {{ $card['tone'] }} [&_i]:text-xl [&_svg]:h-5 [&_svg]:w-5">
                            {!! $aboutIconSvg($card['icon'] ?? null, $loop->index) !!}
                        </div>
                        <p class="mt-5 text-[11px] font-extrabold uppercase tracking-[0.24em] text-slate-500">
                            {{ $card['title'] }}</p>
                        <p class="mt-3 text-base font-semibold text-slate-950 leading-7">
                            {{ $card['description'] }}
                        </p>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
