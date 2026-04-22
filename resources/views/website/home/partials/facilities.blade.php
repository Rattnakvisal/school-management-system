<section id="facilities" class="mx-auto max-w-7xl px-4 pb-12 sm:px-6">
    <div class="grid gap-6 lg:grid-cols-12">
        <div data-reveal class="lg:col-span-7">
            <div class="overflow-hidden rounded-[2rem] border border-white/70 bg-white/70 p-3 shadow-lg backdrop-blur">
                <img src="{{ asset('images/study.jpg') }}" alt="{{ __('home.facilities.image_alt') }}"
                    class="home-hero-image h-80 w-full rounded-[1.6rem] object-cover lg:h-[32rem]" />
            </div>
        </div>

        <div data-reveal style="--d:.08s" class="lg:col-span-5">
            <div class="rounded-[2rem] border border-slate-200 bg-white p-7 shadow-sm">
                <span
                    class="inline-flex items-center gap-2 rounded-full bg-cyan-50 px-4 py-2 text-[11px] font-extrabold uppercase tracking-[0.28em] text-cyan-800 ring-1 ring-cyan-100">
                    <span class="h-2 w-2 rounded-full bg-cyan-500"></span>
                    {{ __('home.facilities.badge') }}
                </span>

                <h2 class="[font-family:Outfit,_sans-serif] mt-4 text-3xl font-semibold text-slate-950">
                    {{ __('home.facilities.title') }}
                </h2>

                <p class="mt-4 text-slate-600 {{ $isKhmerLocale ? 'text-[15px] leading-8' : 'text-sm leading-7' }}">
                    {{ __('home.facilities.description') }}
                </p>

                <div class="mt-7 space-y-3">
                    @foreach ($facilityCards as $facility)
                        <article data-reveal style="--d:{{ number_format(0.05 * $loop->iteration, 2) }}s"
                            class="home-lift-card home-hover-soft rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm font-bold text-slate-900">{{ $facility['title'] }}</p>
                            <p class="mt-1 text-sm text-slate-600 {{ $isKhmerLocale ? 'leading-8' : 'leading-7' }}">{{ $facility['description'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
