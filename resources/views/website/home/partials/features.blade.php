@php
    $sanitizeFeatureIconHtml = function ($html) {
        $html = trim((string) $html);

        if ($html === '' || !str_starts_with($html, '<')) {
            return null;
        }

        $html = strip_tags($html, '<i><span><svg><path><circle><rect><line><polyline><polygon><g><defs><clipPath><ellipse>');
        $html = (string) preg_replace('/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
        $html = (string) preg_replace('/\s+(href|xlink:href)\s*=\s*("[^"]*javascript:[^"]*"|\'[^\']*javascript:[^\']*\'|[^\s>]*javascript:[^\s>]*)/i', '', $html);

        return $html !== '' ? $html : null;
    };
    $featureIconSvg = function ($icon, int $index) use ($sanitizeFeatureIconHtml) {
        $customIcon = $sanitizeFeatureIconHtml($icon);

        if ($customIcon) {
            return $customIcon;
        }

        $rawIcon = strtolower(trim((string) $icon));

        if (str_contains($rawIcon, 'calendar') || str_contains($rawIcon, 'attendance')) {
            return '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 7V3m8 4V3" /><rect x="3" y="5" width="18" height="16" rx="2" /><path d="M3 11h18" /></svg>';
        }

        if (str_contains($rawIcon, 'chart') || str_contains($rawIcon, 'grade')) {
            return '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18" /><path d="M7 14l4-4 3 3 4-5" /></svg>';
        }

        if (str_contains($rawIcon, 'message') || str_contains($rawIcon, 'chat')) {
            return '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" /></svg>';
        }

        if (str_contains($rawIcon, 'shield') || str_contains($rawIcon, 'role')) {
            return '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l8 4v6c0 5-3.5 9.5-8 10-4.5-.5-8-5-8-10V6l8-4z" /><path d="m9 12 2 2 4-4" /></svg>';
        }

        return match ($index) {
            1 => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 7V3m8 4V3" /><rect x="3" y="5" width="18" height="16" rx="2" /><path d="M3 11h18" /></svg>',
            2 => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18" /><path d="M7 14l4-4 3 3 4-5" /></svg>',
            3 => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" /></svg>',
            4 => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l8 4v6c0 5-3.5 9.5-8 10-4.5-.5-8-5-8-10V6l8-4z" /><path d="m9 12 2 2 4-4" /></svg>',
            5 => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" /><path d="M12 6v6l4 2" /></svg>',
            default => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18" /><path d="M5 21V7l7-4 7 4v14" /><path d="M9 10h.01M15 10h.01M9 14h.01M15 14h.01" /></svg>',
        };
    };
@endphp

@if (count($features) > 0)
<section id="features" class="mx-auto max-w-7xl px-4 pb-12 sm:px-6">
    <div data-reveal class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <span
                class="inline-flex items-center gap-2 rounded-full bg-cyan-50 px-4 py-2 text-[11px] font-extrabold uppercase tracking-[0.28em] text-cyan-800 ring-1 ring-cyan-100">
                <span class="h-2 w-2 rounded-full bg-cyan-500"></span>
                {{ $featureBadge }}
            </span>
            <h2 class="[font-family:Outfit,_sans-serif] mt-4 text-3xl font-semibold text-slate-950 sm:text-4xl">
                {{ $featureTitle }}
            </h2>
        </div>

        <span
            class="rounded-full border border-cyan-200 bg-cyan-50 px-4 py-2 text-[11px] font-extrabold uppercase tracking-[0.18em] text-cyan-700">
            {{ $featureTag }}
        </span>
    </div>

    <div class="mt-7 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($features as $f)
            <article data-reveal style="--d:{{ number_format(0.05 * $loop->iteration, 2) }}s"
                class="home-lift-card rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div
                        class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan-100 text-cyan-700 [&_i]:text-xl [&_svg]:h-5 [&_svg]:w-5">
                        {!! $featureIconSvg($f['icon'] ?? null, $loop->index) !!}
                    </div>

                    <span class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-slate-400">
                        0{{ $loop->iteration }}
                    </span>
                </div>

                <h3 class="mt-6 text-lg font-semibold text-slate-900">{{ $f['title'] }}</h3>
                <p class="mt-3 text-sm leading-7 text-slate-600">{{ $f['description'] }}</p>
                <div class="mt-6 h-1.5 w-16 rounded-full bg-gradient-to-r from-cyan-400 to-slate-900"></div>
            </article>
        @endforeach
    </div>
</section>
@endif
