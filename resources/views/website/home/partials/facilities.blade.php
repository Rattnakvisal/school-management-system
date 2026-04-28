<section id="facilities" class="mx-auto max-w-7xl px-4 pb-12 sm:px-6">
    @php
        $facilityIconSvg = function ($icon) {
            $rawIcon = strtolower(trim((string) $icon));

            if (str_starts_with(trim((string) $icon), '<')) {
                $html = strip_tags((string) $icon, '<i><span><svg><path><circle><rect><line><polyline><polygon><g>');
                $html = (string) preg_replace('/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);

                return $html ?: null;
            }

            return match (true) {
                str_contains($rawIcon, 'computer') || str_contains($rawIcon, 'lab') => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="12" rx="2" /><path d="M8 20h8M12 16v4" /></svg>',
                str_contains($rawIcon, 'book') || str_contains($rawIcon, 'library') => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" /><path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15Z" /></svg>',
                str_contains($rawIcon, 'sport') || str_contains($rawIcon, 'activity') => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9" /><path d="M5.6 5.6 18.4 18.4M3.2 13.5c4-1.2 7.1-4.3 8.3-8.3M12.5 18.8c1.2-4 4.3-7.1 8.3-8.3" /></svg>',
                default => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 21V7l8-4 8 4v14" /><path d="M9 21v-7h6v7M8 10h.01M12 10h.01M16 10h.01" /></svg>',
            };
        };
    @endphp

    <div class="grid gap-6 lg:grid-cols-12">
        <div data-reveal class="lg:col-span-7">
            <div class="overflow-hidden rounded-[2rem] border border-white/70 bg-white/70 p-3 shadow-lg backdrop-blur">
                <img src="{{ $facilityImage ?? asset('images/study.jpg') }}" alt="{{ __('home.facilities.image_alt') }}"
                    class="home-hero-image h-80 w-full rounded-[1.6rem] object-cover lg:h-[32rem]" />
            </div>
        </div>

        <div data-reveal style="--d:.08s" class="lg:col-span-5">
            <div class="rounded-[2rem] border border-slate-200 bg-white p-7 shadow-sm">
                <span
                    class="inline-flex items-center gap-2 rounded-full bg-cyan-50 px-4 py-2 text-[11px] font-extrabold uppercase tracking-[0.28em] text-cyan-800 ring-1 ring-cyan-100">
                    <span class="h-2 w-2 rounded-full bg-cyan-500"></span>
                    {{ $facilityBadge ?? __('home.facilities.badge') }}
                </span>

                <h2 class="[font-family:Outfit,_sans-serif] mt-4 text-3xl font-semibold text-slate-950">
                    {{ $facilityTitle ?? __('home.facilities.title') }}
                </h2>

                <p class="mt-4 text-slate-600 text-sm leading-7">
                    {{ $facilityDescription ?? __('home.facilities.description') }}
                </p>

                <div class="mt-7 space-y-3">
                    @foreach ($facilityCards as $facility)
                        <article data-reveal style="--d:{{ number_format(0.05 * $loop->iteration, 2) }}s"
                            class="home-lift-card home-hover-soft rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-cyan-50 text-cyan-700 ring-1 ring-cyan-100 [&_i]:text-lg [&_svg]:h-5 [&_svg]:w-5">
                                    {!! $facilityIconSvg($facility['icon'] ?? null) !!}
                                </span>
                                <div>
                                    <p class="text-sm font-bold text-slate-900">{{ $facility['title'] }}</p>
                                    <p class="mt-1 text-sm text-slate-600 leading-7">{{ $facility['description'] }}</p>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
