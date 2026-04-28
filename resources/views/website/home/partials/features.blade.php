{{-- =========================
    Features Section
========================= --}}
@php
    $sanitizeFeatureIconHtml = function ($html) {
        $html = trim((string) $html);

        if ($html === '' || !str_starts_with($html, '<')) {
            return null;
        }

        $html = strip_tags($html, '<i><span><svg><path><circle><rect><line><polyline><polygon><g>');
        $html = (string) preg_replace('/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);

        return $html ?: null;
    };

    $featureIconSvg = function ($icon, $index) use ($sanitizeFeatureIconHtml) {
        if ($custom = $sanitizeFeatureIconHtml($icon)) {
            return $custom;
        }

        return match ($index) {
            1
                => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 7V3m8 4V3" /><rect x="3" y="5" width="18" height="16" rx="2" /><path d="M3 11h18" /></svg>',
            2
                => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18" /><path d="M7 14l4-4 3 3 4-5" /></svg>',
            3
                => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" /></svg>',
            4
                => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l8 4v6c0 5-3.5 9.5-8 10-4.5-.5-8-5-8-10V6l8-4z" /><path d="m9 12 2 2 4-4" /></svg>',
            default
                => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12l5 5L20 7" /></svg>',
        };
    };

    $featureIconTones = [
        'bg-amber-50 text-amber-600 ring-1 ring-amber-100',
        'bg-teal-50 text-teal-600 ring-1 ring-teal-100',
        'bg-cyan-50 text-cyan-600 ring-1 ring-cyan-100',
        'bg-rose-50 text-rose-600 ring-1 ring-rose-100',
        'bg-violet-50 text-violet-600 ring-1 ring-violet-100',
        'bg-lime-50 text-lime-600 ring-1 ring-lime-100',
    ];

    $featureIconToneStyle = function ($color) {
        $color = trim((string) $color);

        return preg_match('/^#[0-9A-Fa-f]{6}$/', $color)
            ? "color: {$color}; background-color: {$color}1A; border: 1px solid {$color}33;"
            : null;
    };
@endphp

@if (count($features))
    <section id="features" class="mx-auto max-w-7xl px-4 py-16 sm:px-6">

        {{-- Section Header --}}
        <div data-reveal class="flex flex-wrap items-end justify-between gap-6">
            <div>
                <span
                    class="inline-flex items-center gap-2 rounded-full bg-indigo-50 px-4 py-2 text-[11px] font-extrabold uppercase tracking-[0.28em] text-indigo-700 ring-1 ring-indigo-100">
                    <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                    {{ $featureBadge }}
                </span>

                <h2 class="mt-4 text-3xl font-bold text-slate-950 sm:text-4xl">
                    {{ $featureTitle }}
                </h2>
            </div>

            <span
                class="rounded-full bg-indigo-50 px-4 py-2 text-[11px] font-extrabold uppercase tracking-[0.18em] text-indigo-700 ring-1 ring-indigo-100">
                {{ $featureTag }}
            </span>
        </div>

        {{-- Feature Grid --}}
        <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($features as $f)
                @php
                    $iconStyle = $featureIconToneStyle($f['color'] ?? null);
                    $iconTone = $iconStyle
                        ? ''
                        : (($f['color'] ?? null) ?: $featureIconTones[$loop->index % count($featureIconTones)]);
                @endphp

                <article data-reveal style="--d:{{ number_format(0.05 * $loop->iteration, 2) }}s"
                    class="group relative overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-2 hover:border-indigo-200 hover:shadow-xl">
                    {{-- Card Glow --}}
                    <div
                        class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-indigo-400/20 blur-3xl opacity-0 transition duration-300 group-hover:opacity-100">
                    </div>

                    {{-- Top Row --}}
                    <div class="relative flex items-center justify-between gap-3">
                        <div
                            class="inline-flex h-12 w-12 items-center justify-center rounded-2xl {{ $iconTone }} shadow-md transition duration-300 group-hover:scale-110 [&_i]:text-xl [&_svg]:h-5 [&_svg]:w-5"
                            @if ($iconStyle) style="{{ $iconStyle }}" @endif>
                            {!! $featureIconSvg($f['icon'] ?? null, $loop->index) !!}
                        </div>

                        <span
                            class="rounded-full bg-indigo-50 px-3 py-1 text-[11px] font-extrabold uppercase tracking-[0.18em] text-indigo-700 ring-1 ring-indigo-100">
                            0{{ $loop->iteration }}
                        </span>
                    </div>

                    {{-- Content --}}
                    <h3
                        class="relative mt-6 text-lg font-semibold text-slate-900 transition group-hover:text-indigo-700">
                        {{ $f['title'] }}
                    </h3>

                    <p class="relative mt-3 text-sm leading-7 text-slate-600">
                        {{ $f['description'] }}
                    </p>

                    {{-- Bottom Line --}}
                    <div
                        class="relative mt-6 h-1.5 w-12 rounded-full bg-gradient-to-r from-indigo-500 to-blue-500 transition-all duration-300 group-hover:w-20">
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endif
