{{-- =========================
    Programs Section
========================= --}}
<section id="programs" class="mx-auto max-w-7xl px-4 py-16 sm:px-6">
    @php
        /*
        |--------------------------------------------------------------------------
        | Program Icon Generator
        |--------------------------------------------------------------------------
        | Clean custom icon HTML and return default SVG icons.
        */
        $programIconSvg = function ($icon) {
            $rawIcon = strtolower(trim((string) $icon));

            if (str_starts_with(trim((string) $icon), '<')) {
                $html = strip_tags((string) $icon, '<i><span><svg><path><circle><rect><line><polyline><polygon><g>');

                $html = (string) preg_replace('/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);

                return $html ?: null;
            }

            return match (true) {
                str_contains($rawIcon, 'book')
                    => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" /><path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15Z" /></svg>',

                str_contains($rawIcon, 'users') || str_contains($rawIcon, 'team')
                    => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.66 0 3-1.79 3-4s-1.34-4-3-4-3 1.79-3 4 1.34 4 3 4ZM8 11c1.66 0 3-1.79 3-4S9.66 3 8 3 5 4.79 5 7s1.34 4 3 4Zm0 2c-2.33 0-7 1.17-7 3.5V21h14v-4.5C15 14.17 10.33 13 8 13Zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.96 1.97 3.45V21h6v-4.5c0-2.33-4.67-3.5-7-3.5Z" /></svg>',

                str_contains($rawIcon, 'trophy') || str_contains($rawIcon, 'career')
                    => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 21h8M12 17v4M7 4h10v4a5 5 0 0 1-10 0V4Z" /><path d="M5 5H3v2a4 4 0 0 0 4 4M19 5h2v2a4 4 0 0 1-4 4" /></svg>',

                default
                    => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3 2 8l10 5 10-5-10-5z" /><path d="m2 8 10 5 10-5" /><path d="M2 13l10 5 10-5" /></svg>',
            };
        };

        $programIconTones = [
            'bg-blue-50 text-blue-600 ring-1 ring-blue-100',
            'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100',
            'bg-amber-50 text-amber-600 ring-1 ring-amber-100',
            'bg-fuchsia-50 text-fuchsia-600 ring-1 ring-fuchsia-100',
        ];

        $programIconToneStyle = function ($color) {
            $color = trim((string) $color);

            return preg_match('/^#[0-9A-Fa-f]{6}$/', $color)
                ? "color: {$color}; background-color: {$color}1A; border: 1px solid {$color}33;"
                : null;
        };
    @endphp

    {{-- Section Header --}}
    <div data-reveal class="flex flex-wrap items-end justify-between gap-6">
        <div>
            <span
                class="inline-flex items-center gap-2 rounded-full bg-indigo-50 px-4 py-2 text-[11px] font-extrabold uppercase tracking-[0.28em] text-indigo-700 ring-1 ring-indigo-100">
                <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                {{ $programBadge ?? \App\Support\HomePageContent::text('programs.badge') }}
            </span>

            <h2 class="[font-family:Outfit,_sans-serif] mt-4 text-3xl font-semibold text-slate-950 sm:text-4xl">
                {{ $programTitle ?? \App\Support\HomePageContent::text('programs.title') }}
            </h2>
        </div>

        <p class="max-w-xl text-sm leading-7 text-slate-500">
            {{ $programDescription ?? \App\Support\HomePageContent::text('programs.description') }}
        </p>
    </div>

    {{-- Programs Grid --}}
    <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($programs as $item)
            @php
                $iconStyle = $programIconToneStyle($item['color'] ?? null);
                $iconTone = $iconStyle
                    ? ''
                    : (($item['color'] ?? null) ?: $programIconTones[$loop->index % count($programIconTones)]);
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
                        {!! $programIconSvg($item['icon'] ?? null) !!}
                    </div>

                    <span
                        class="rounded-full bg-indigo-50 px-3 py-1 text-[11px] font-extrabold uppercase tracking-[0.18em] text-indigo-700 ring-1 ring-indigo-100">
                        {{ $item['level'] }}
                    </span>
                </div>

                {{-- Content --}}
                <h3 class="relative mt-6 text-xl font-semibold text-slate-900 transition group-hover:text-indigo-700">
                    {{ $item['title'] }}
                </h3>

                <p class="relative mt-3 text-sm leading-7 text-slate-600">
                    {{ $item['description'] }}
                </p>

                {{-- Bottom Line --}}
                <div
                    class="relative mt-6 h-1.5 w-12 rounded-full bg-gradient-to-r from-indigo-500 to-blue-500 transition-all duration-300 group-hover:w-20">
                </div>
            </article>
        @endforeach
    </div>
</section>
