{{-- =========================
    Programs Section
========================= --}}
<section id="programs" class="mx-auto max-w-7xl px-4 py-14 sm:px-6">
    @php
        $programIconSvg = function ($icon) {
            $rawIcon = strtolower(trim((string) $icon));

            if (str_starts_with(trim((string) $icon), '<')) {
                $html = strip_tags((string) $icon, '<i><span><svg><path><circle><rect><line><polyline><polygon><g>');
                $html = (string) preg_replace('/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);

                return $html ?: null;
            }

            return match (true) {
                str_contains($rawIcon, 'book') || str_contains($rawIcon, 'learn')
                    => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" /><path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15Z" /></svg>',
                str_contains($rawIcon, 'teacher') || str_contains($rawIcon, 'user') || str_contains($rawIcon, 'team')
                    => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M22 21v-2a4 4 0 0 0-3-3.87" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /></svg>',
                str_contains($rawIcon, 'message') || str_contains($rawIcon, 'support') || str_contains($rawIcon, 'chat')
                    => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v8Z" /><path d="M8 9h8M8 13h5" /></svg>',
                str_contains($rawIcon, 'trophy') || str_contains($rawIcon, 'career')
                    => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 21h8M12 17v4M7 4h10v4a5 5 0 0 1-10 0V4Z" /><path d="M5 5H3v2a4 4 0 0 0 4 4M19 5h2v2a4 4 0 0 1-4 4" /></svg>',
                default
                    => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3 2 8l10 5 10-5-10-5Z" /><path d="m2 13 10 5 10-5" /><path d="m2 8 10 5 10-5" /></svg>',
            };
        };

        $programToneStyle = function ($color, string $fallback = '#10b981') {
            $color = trim((string) $color);
            $color = preg_match('/^#[0-9A-Fa-f]{6}$/', $color) ? $color : $fallback;

            return "color: {$color}; background-color: {$color}12; border-color: {$color}24;";
        };

        $programItems = array_slice($programs ?? [], 0, 4);
    @endphp

    <div class="relative overflow-hidden bg-white px-5 py-10 sm:px-8 lg:px-12">
        <div class="pointer-events-none absolute left-4 top-4 grid grid-cols-2 gap-1.5">
            <span class="h-2 w-2 rounded-sm bg-rose-300"></span>
            <span class="h-2 w-2 rounded-sm bg-sky-300"></span>
            <span class="h-2 w-2 rounded-sm bg-amber-300"></span>
            <span class="h-2 w-2 rounded-sm bg-emerald-300"></span>
        </div>

        <div data-reveal class="mx-auto max-w-2xl text-center">
            <span class="text-[10px] font-extrabold uppercase tracking-[0.22em] text-emerald-600">
                {{ $programBadge ?? \App\Support\HomePageContent::text('programs.badge') }}
            </span>

            <h2
                class="[font-family:Outfit,_sans-serif] mt-3 text-3xl font-extrabold leading-tight tracking-tight text-[#10221e] sm:text-4xl">
                {{ $programTitle ?? \App\Support\HomePageContent::text('programs.title') }}
            </h2>

            <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-slate-500">
                {{ $programDescription ?? \App\Support\HomePageContent::text('programs.description') }}
            </p>
        </div>

        @if (!empty($programItems))
            <div class="mt-9 grid gap-4 md:grid-cols-3 {{ count($programItems) > 3 ? 'xl:grid-cols-4' : '' }}">
                @foreach ($programItems as $item)
                    <article data-reveal style="--d:{{ number_format(0.05 * $loop->iteration, 2) }}s"
                        class="group border border-emerald-900/5 bg-[#f6fffb] p-6 transition duration-300 hover:-translate-y-1 hover:bg-white hover:shadow-[0_18px_45px_-34px_rgba(15,118,110,0.45)]">
                        <span
                            class="inline-flex h-11 w-11 items-center justify-center rounded-full border text-emerald-600 transition group-hover:scale-105 [&_i]:text-lg [&_svg]:h-5 [&_svg]:w-5"
                            style="{{ $programToneStyle($item['color'] ?? null, ['#10b981', '#f59e0b', '#0ea5e9', '#a855f7'][$loop->index % 4]) }}">
                            {!! $programIconSvg($item['icon'] ?? null) !!}
                        </span>

                        <p class="mt-5 text-[11px] font-extrabold uppercase tracking-[0.18em] text-emerald-600">
                            {{ $item['level'] ?? '' }}
                        </p>

                        <h3 class="mt-2 text-lg font-extrabold tracking-tight text-[#10221e]">
                            {{ $item['title'] ?? '' }}
                        </h3>

                        <p class="mt-3 text-sm leading-6 text-slate-500">
                            {{ $item['description'] ?? '' }}
                        </p>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
