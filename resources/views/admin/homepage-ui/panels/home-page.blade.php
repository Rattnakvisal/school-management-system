<section data-settings-panel="home-page" class="hidden space-y-7">
    @if ($homePageReady ?? false)
        <form method="POST" action="{{ route('admin.settings.home-page.update') }}" enctype="multipart/form-data"
            class="space-y-7">
            @csrf
            @method('PUT')
            <input type="hidden" name="_settings_tab" value="home-page">
            <input type="hidden" id="remove_home_hero_image" name="hero[remove_image]" value="0">

            {{-- ── Hero Page Builder ── --}}
            <div
                class="relative overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm ring-1 ring-black/[0.03]">
                <div
                    class="absolute inset-x-0 top-0 h-[3px] bg-gradient-to-r from-blue-400 via-indigo-500 to-violet-500 rounded-t-3xl">
                </div>

                <div class="grid gap-0 xl:grid-cols-[minmax(0,1fr)_400px]">
                    {{-- Left: Form fields --}}
                    <div class="px-6 pb-7 pt-5 sm:px-8">
                        {{-- Header --}}
                        <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-100 pb-6">
                            <div class="space-y-2">
                                <div
                                    class="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-3.5 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-blue-700">
                                    <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                    Website Content
                                </div>
                                <h2 class="text-2xl font-black tracking-tight text-slate-950">Hero Page Builder</h2>
                                <p class="max-w-lg text-[13px] leading-relaxed text-slate-500">
                                    Edit the homepage hero content, image, stats, and feature strip from one screen.
                                </p>
                            </div>
                        </div>

                        {{-- Fields --}}
                        <div class="mt-6 grid gap-5 md:grid-cols-2">
                            <div class="space-y-1.5 md:col-span-2">
                                <label for="home_hero_title"
                                    class="block text-[10px] font-black uppercase tracking-[0.18em] text-slate-400">
                                    Main Headline
                                </label>
                                <input id="home_hero_title" name="hero[title]" type="text"
                                    data-home-preview-target="home_preview_title"
                                    value="{{ old('hero.title', $homeHero?->title ?? 'A Smarter school experience for Every Students, Every Day.') }}"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition-all duration-200 placeholder:text-slate-300 hover:border-slate-300 focus:border-indigo-400 focus:bg-white focus:ring-4 focus:ring-indigo-50">
                            </div>

                            <div class="space-y-1.5">
                                <label for="home_hero_badge"
                                    class="block text-[10px] font-black uppercase tracking-[0.18em] text-slate-400">
                                    Top Badge
                                </label>
                                <input id="home_hero_badge" name="hero[badge]" type="text"
                                    data-home-preview-target="home_preview_badge"
                                    value="{{ old('hero.badge', $homeHero?->subtitle ?? 'Future-ready school platform') }}"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-800 outline-none transition-all duration-200 placeholder:text-slate-300 hover:border-slate-300 focus:border-indigo-400 focus:bg-white focus:ring-4 focus:ring-indigo-50">
                            </div>

                            {{-- Hero Image Upload --}}
                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-black uppercase tracking-[0.18em] text-slate-400">
                                    Hero Image
                                </label>
                                <input id="home_hero_image_input" type="file" name="hero[image]" accept="image/*"
                                    class="hidden">
                                <div class="flex gap-2">
                                    <button type="button" id="upload_home_hero_image_btn"
                                        class="flex-1 rounded-xl bg-slate-900 px-4 py-3 text-[12px] font-bold text-white transition-all duration-150 hover:bg-slate-800 active:scale-95">
                                        Upload
                                    </button>
                                    <button type="button" id="delete_home_hero_image_btn"
                                        class="flex-1 rounded-xl border border-slate-200 bg-white px-4 py-3 text-[12px] font-bold text-slate-700 transition-all duration-150 hover:bg-slate-50 active:scale-95">
                                        Use Default
                                    </button>
                                </div>
                            </div>

                            <div class="space-y-1.5 md:col-span-2">
                                <label for="home_hero_description"
                                    class="block text-[10px] font-black uppercase tracking-[0.18em] text-slate-400">
                                    Intro Paragraph
                                </label>
                                <textarea id="home_hero_description" name="hero[description]" rows="4"
                                    data-home-preview-target="home_preview_description"
                                    class="w-full resize-none rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm leading-relaxed text-slate-700 outline-none transition-all duration-200 placeholder:text-slate-300 hover:border-slate-300 focus:border-indigo-400 focus:bg-white focus:ring-4 focus:ring-indigo-50">{{ old('hero.description', $homeHero?->description ?? 'TechBridge brings academics, admissions, attendance, communication, and daily school operations together in one seamless platform for students, parents, teachers, and school leaders.') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Right: Live Preview --}}
                    <aside class="border-t border-slate-100 bg-slate-950 p-5 xl:border-l xl:border-t-0">
                        <div class="sticky top-24">
                            <p class="mb-3 text-[10px] font-black uppercase tracking-[0.24em] text-slate-500">Live
                                Preview</p>
                            <div class="overflow-hidden rounded-2xl bg-[#eafff7] shadow-2xl shadow-black/30">
                                <div class="relative min-h-52 overflow-hidden">
                                    <div class="absolute right-5 top-8 h-36 w-36 rounded-[2rem] bg-emerald-400"></div>
                                    <div class="absolute right-4 top-4 grid grid-cols-4 gap-1 text-emerald-300">
                                        @for ($i = 0; $i < 12; $i++)
                                            <span class="h-1 w-1 rounded-full bg-current"></span>
                                        @endfor
                                    </div>
                                    <img id="home_hero_image_preview" src="{{ $homePageImage }}"
                                        class="relative z-10 mx-auto h-56 w-full object-contain p-4 drop-shadow-xl"
                                        alt="home hero preview">
                                </div>
                                <div class="p-5">
                                    <p id="home_preview_badge"
                                        class="inline-flex items-center gap-1.5 bg-emerald-50 px-2.5 py-1 text-[9px] font-black uppercase tracking-[0.18em] text-emerald-700 ring-1 ring-emerald-100">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        {{ old('hero.badge', $homeHero?->subtitle ?? 'Future-ready school platform') }}
                                    </p>
                                    <h3 id="home_preview_title"
                                        class="mt-3 text-xl font-black leading-tight tracking-tight text-[#10221e]">
                                        {{ old('hero.title', $homeHero?->title ?? 'A Smarter school experience for Every Students, Every Day.') }}
                                    </h3>
                                    <p id="home_preview_description"
                                        class="mt-3 line-clamp-4 text-[12px] leading-5 text-slate-600">
                                        {{ old('hero.description', $homeHero?->description ?? 'TechBridge brings academics, admissions, attendance, communication, and daily school operations together in one seamless platform for students, parents, teachers, and school leaders.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>

            {{-- ── Check Points + Floating Stats ── --}}
            <div class="grid gap-6 xl:grid-cols-2">

                {{-- Check Points --}}
                <div
                    class="relative overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm ring-1 ring-black/[0.03]">
                    <div
                        class="absolute inset-x-0 top-0 h-[3px] bg-gradient-to-r from-blue-400 to-indigo-500 rounded-t-3xl">
                    </div>

                    <div class="px-6 pb-6 pt-7 sm:px-7">
                        <div class="flex items-start justify-between gap-3">
                            <div class="space-y-1">
                                <h3 class="text-base font-black tracking-tight text-slate-900">Check Points</h3>
                                <p class="text-[12px] leading-relaxed text-slate-500">Three short benefits under the
                                    hero button.</p>
                            </div>
                            <button type="button" data-add-home-card="hero-points"
                                class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-blue-600 px-3.5 py-1.5 text-[11px] font-bold text-white shadow-sm shadow-blue-200 transition-all duration-150 hover:bg-blue-500 hover:shadow-blue-300 active:scale-95">
                                <svg class="h-3 w-3" viewBox="0 0 12 12" fill="none" stroke="currentColor"
                                    stroke-width="2.5" stroke-linecap="round">
                                    <path d="M6 1v10M1 6h10" />
                                </svg>
                                Add Point
                            </button>
                        </div>

                        <div class="mt-5 space-y-3">
                            @for ($i = 0; $i < $pointLimit; $i++)
                                @php
                                    $pointItem = $homePoints->get($i);
                                    $pointDescription = old(
                                        'points.' . $i . '.description',
                                        $pointItem?->description ?? '',
                                    );
                                    $pointIcon = old('points.' . $i . '.icon', $pointItem?->icon ?? '');
                                    $showPoint = filled($pointDescription);
                                @endphp

                                <div data-home-editor-card data-addable-card="hero-points"
                                    class="{{ $showPoint ? '' : 'hidden' }} rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-4 transition-shadow duration-200 hover:shadow-md">
                                    <input type="hidden" name="points[{{ $i }}][id]"
                                        value="{{ $pointItem?->id }}">
                                    <input type="hidden" name="points[{{ $i }}][delete]"
                                        data-home-delete-field value="0">

                                    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                                        <span
                                            class="flex items-center gap-2 text-[11px] font-black uppercase tracking-wide text-slate-400">
                                            <span
                                                class="grid h-5 w-5 place-items-center rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 text-[10px] font-black text-white shadow-sm">
                                                {{ $i + 1 }}
                                            </span>
                                            Point {{ $i + 1 }}
                                        </span>
                                        <div data-home-card-actions class="flex flex-wrap items-center gap-1.5">
                                            <button type="button" data-home-edit-card
                                                class="rounded-full border border-blue-200 bg-white px-3 py-1 text-[11px] font-bold text-blue-600 transition-all duration-150 hover:border-blue-300 hover:bg-blue-50 active:scale-95">
                                                Edit
                                            </button>
                                            <label
                                                class="inline-flex cursor-pointer select-none items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-bold text-slate-600 transition-colors hover:bg-slate-50">
                                                <input type="hidden" name="points[{{ $i }}][active]"
                                                    value="0">
                                                <input type="checkbox" name="points[{{ $i }}][active]"
                                                    data-home-active-toggle value="1"
                                                    {{ old('points.' . $i . '.active', $pointItem?->is_active ?? true) ? 'checked' : '' }}
                                                    class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                Active
                                            </label>
                                            <button type="button" data-home-remove-card
                                                class="rounded-full border border-red-200 bg-white px-3 py-1 text-[11px] font-bold text-red-500 transition-all duration-150 hover:border-red-300 hover:bg-red-50 active:scale-95">
                                                Remove
                                            </button>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <textarea name="points[{{ $i }}][description]" rows="2"
                                            class="w-full resize-none rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm leading-relaxed text-slate-700 outline-none transition-all placeholder:text-slate-300 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-50">{{ $pointDescription }}</textarea>
                                        <input name="points[{{ $i }}][icon]" type="text"
                                            data-random-icon-input
                                            placeholder='Icon name, <i class="fa-solid fa-school"></i>, or SVG'
                                            value="{{ $pointIcon }}"
                                            class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition-all placeholder:text-slate-300 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-50">
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>

                {{-- Floating Stats --}}
                <div
                    class="relative overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm ring-1 ring-black/[0.03]">
                    <div
                        class="absolute inset-x-0 top-0 h-[3px] bg-gradient-to-r from-emerald-400 to-teal-500 rounded-t-3xl">
                    </div>

                    <div class="px-6 pb-6 pt-7 sm:px-7">
                        <div class="flex items-start justify-between gap-3">
                            <div class="space-y-1">
                                <h3 class="text-base font-black tracking-tight text-slate-900">Floating Stats</h3>
                                <p class="text-[12px] leading-relaxed text-slate-500">Cards shown over the hero image.
                                </p>
                            </div>
                            <button type="button" data-add-home-card="hero-stats"
                                class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-emerald-600 px-3.5 py-1.5 text-[11px] font-bold text-white shadow-sm shadow-emerald-200 transition-all duration-150 hover:bg-emerald-500 hover:shadow-emerald-300 active:scale-95">
                                <svg class="h-3 w-3" viewBox="0 0 12 12" fill="none" stroke="currentColor"
                                    stroke-width="2.5" stroke-linecap="round">
                                    <path d="M6 1v10M1 6h10" />
                                </svg>
                                Add Stat
                            </button>
                        </div>

                        <div class="mt-5 space-y-3">
                            @for ($i = 0; $i < $statLimit; $i++)
                                @php
                                    $statItem = $homeStats->get($i);
                                    $statLabel = old('stats.' . $i . '.label', $statItem?->title ?? '');
                                    $statValue = old('stats.' . $i . '.value', $statItem?->value ?? '');
                                    $statTrend = old('stats.' . $i . '.trend', $statItem?->subtitle ?? '');
                                    $statIcon = old(
                                        'stats.' . $i . '.icon',
                                        $statItem?->icon ??
                                            match ($i) {
                                                0 => 'graduation',
                                                1 => 'chart',
                                                2 => 'users',
                                                default => 'clock',
                                            },
                                    );
                                    $statColor = old(
                                        'stats.' . $i . '.color',
                                        $iconColorValue(
                                            $statItem?->color,
                                            $heroStatColorDefaults[$i % count($heroStatColorDefaults)],
                                        ),
                                    );
                                    $showStat = filled($statLabel) || filled($statValue) || filled($statTrend);
                                @endphp

                                <div data-home-editor-card data-addable-card="hero-stats"
                                    class="{{ $showStat ? '' : 'hidden' }} rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-4 transition-shadow duration-200 hover:shadow-md">
                                    <input type="hidden" name="stats[{{ $i }}][id]"
                                        value="{{ $statItem?->id }}">
                                    <input type="hidden" name="stats[{{ $i }}][delete]"
                                        data-home-delete-field value="0">

                                    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                                        <span
                                            class="flex items-center gap-2 text-[11px] font-black uppercase tracking-wide text-slate-400">
                                            <span
                                                class="grid h-5 w-5 place-items-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-[10px] font-black text-white shadow-sm">
                                                {{ $i + 1 }}
                                            </span>
                                            Stat card {{ $i + 1 }}
                                        </span>
                                        <div data-home-card-actions class="flex flex-wrap items-center gap-1.5">
                                            <button type="button" data-home-edit-card
                                                class="rounded-full border border-blue-200 bg-white px-3 py-1 text-[11px] font-bold text-blue-600 transition-all duration-150 hover:border-blue-300 hover:bg-blue-50 active:scale-95">
                                                Edit
                                            </button>
                                            <label
                                                class="inline-flex cursor-pointer select-none items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-bold text-slate-600 transition-colors hover:bg-slate-50">
                                                <input type="hidden" name="stats[{{ $i }}][active]"
                                                    value="0">
                                                <input type="checkbox" name="stats[{{ $i }}][active]"
                                                    data-home-active-toggle value="1"
                                                    {{ old('stats.' . $i . '.active', $statItem?->is_active ?? true) ? 'checked' : '' }}
                                                    class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                Active
                                            </label>
                                            <button type="button" data-home-remove-card
                                                class="rounded-full border border-red-200 bg-white px-3 py-1 text-[11px] font-bold text-red-500 transition-all duration-150 hover:border-red-300 hover:bg-red-50 active:scale-95">
                                                Remove
                                            </button>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        {{-- Label / Value / Trend row --}}
                                        <div class="grid gap-2 sm:grid-cols-[minmax(0,1fr)_88px_120px]">
                                            <input name="stats[{{ $i }}][label]" type="text"
                                                placeholder="Label" value="{{ $statLabel }}"
                                                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 outline-none transition-all placeholder:text-slate-300 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-50">
                                            <input name="stats[{{ $i }}][value]" type="text"
                                                placeholder="Value" value="{{ $statValue }}"
                                                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm font-semibold text-slate-900 outline-none transition-all placeholder:text-slate-300 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-50">
                                            <input name="stats[{{ $i }}][trend]" type="text"
                                                placeholder="+6% this year" value="{{ $statTrend }}"
                                                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 outline-none transition-all placeholder:text-slate-300 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-50">
                                        </div>
                                        {{-- Icon / Color row --}}
                                        <div class="grid gap-2 sm:grid-cols-[minmax(0,1fr)_140px]">
                                            <div class="space-y-1">
                                                <label
                                                    class="block text-[10px] font-black uppercase tracking-[0.15em] text-slate-400">Icon</label>
                                                <input name="stats[{{ $i }}][icon]" type="text"
                                                    data-stat-icon-select data-random-icon-input
                                                    placeholder='Icon name, <i class="fa-solid fa-school"></i>, or SVG'
                                                    value="{{ $statIcon }}"
                                                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition-all placeholder:text-slate-300 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-50">
                                            </div>
                                            <label
                                                class="flex items-center gap-3 self-end rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-[11px] font-bold text-slate-500">
                                                <span class="flex-1">Icon color</span>
                                                <input name="stats[{{ $i }}][color]" data-stat-color-input
                                                    type="color" value="{{ $statColor }}"
                                                    class="h-8 w-10 cursor-pointer rounded-lg border border-slate-200 bg-white p-0.5">
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Sticky Save Bar ── --}}
            <div class="sticky bottom-5 z-10 flex justify-end">
                <div
                    class="flex items-center gap-3 rounded-2xl border border-slate-200/80 bg-white/90 px-3 py-2.5 shadow-xl shadow-slate-200/60 backdrop-blur-md">
                    <p class="hidden text-[11px] font-semibold text-slate-400 sm:block">Unsaved changes</p>
                    <div class="hidden h-4 w-px bg-slate-200 sm:block"></div>
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm shadow-indigo-200 transition-all duration-150 hover:bg-indigo-500 hover:shadow-indigo-300 active:scale-[0.97]">
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M13.5 4.5L6 12 2.5 8.5" />
                        </svg>
                        Save Hero Page
                    </button>
                </div>
            </div>
        </form>
    @else
        {{-- Migration notice --}}
        <div class="flex items-start gap-4 rounded-2xl border border-amber-200 bg-amber-50 dark:border-amber-900/50 dark:bg-amber-900/20 dark:border-amber-900/50 dark:bg-amber-900/20 p-5 text-sm text-amber-800">
            <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-full bg-amber-100 text-amber-600">
                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round">
                    <path d="M8 1v6m0 3v.5" />
                    <circle cx="8" cy="8" r="7" />
                </svg>
            </span>
            <div>
                <p class="font-bold text-amber-900">Migrations required</p>
                <p class="mt-0.5 text-amber-700 dark:text-amber-300">Run migrations to enable dynamic homepage editing.</p>
            </div>
        </div>
    @endif
</section>
