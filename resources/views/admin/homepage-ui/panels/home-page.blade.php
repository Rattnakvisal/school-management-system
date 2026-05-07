                    <section data-settings-panel="home-page" class="hidden space-y-6">
                        @if ($homePageReady ?? false)
                            <form method="POST" action="{{ route('admin.settings.home-page.update') }}"
                                enctype="multipart/form-data" class="space-y-5">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="_settings_tab" value="home-page">
                                <input type="hidden" id="remove_home_hero_image" name="hero[remove_image]"
                                    value="0">

                                <div
                                    class="overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white shadow-sm ring-1 ring-slate-100">
                                    <div class="grid gap-0 xl:grid-cols-[minmax(0,1fr)_420px]">
                                        <div class="p-5 sm:p-7">
                                            <div
                                                class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-200 pb-6">
                                                <div>
                                                    <span
                                                        class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-[11px] font-black uppercase tracking-[0.2em] text-blue-700 ring-1 ring-blue-100">
                                                        Website content
                                                    </span>
                                                    <h2 class="mt-3 text-2xl font-black tracking-tight text-slate-950">Hero
                                                        Page Builder
                                                    </h2>
                                                    <p class="mt-2 max-w-2xl text-sm leading-7 text-slate-500">
                                                        Edit the homepage hero content, image, stats, and feature strip from
                                                        one screen.
                                                    </p>
                                                </div>
                                                <div class="flex flex-wrap gap-2">
                                                    <a href="{{ route('home') }}" target="_blank"
                                                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50">
                                                        View Home
                                                    </a>
                                                    <button type="submit"
                                                        class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-indigo-500">
                                                        Save Changes
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="mt-6 grid gap-5 md:grid-cols-2">
                                                <div class="md:col-span-2">
                                                    <label for="home_hero_title"
                                                        class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Main
                                                        headline</label>
                                                    <input id="home_hero_title" name="hero[title]" type="text"
                                                        data-home-preview-target="home_preview_title"
                                                        value="{{ old('hero.title', $homeHero?->title ?? 'A Smarter school experience for Every Students, Every Day.') }}"
                                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                </div>
                                                <div>
                                                    <label for="home_hero_badge"
                                                        class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Top
                                                        badge</label>
                                                    <input id="home_hero_badge" name="hero[badge]" type="text"
                                                        data-home-preview-target="home_preview_badge"
                                                        value="{{ old('hero.badge', $homeHero?->subtitle ?? 'Future-ready school platform') }}"
                                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                </div>
                                                <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                                                    <label
                                                        class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Hero
                                                        image</label>
                                                    <input id="home_hero_image_input" type="file" name="hero[image]"
                                                        accept="image/*" class="hidden">
                                                    <div
                                                        class="mt-2 grid gap-2 sm:grid-cols-2 md:grid-cols-1 lg:grid-cols-2">
                                                        <button type="button" id="upload_home_hero_image_btn"
                                                            class="rounded-xl bg-slate-900 px-4 py-3 text-xs font-bold text-white transition hover:bg-slate-800">
                                                            Upload Image
                                                        </button>
                                                        <button type="button" id="delete_home_hero_image_btn"
                                                            class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs font-bold text-slate-700 transition hover:bg-slate-100">
                                                            Use Default
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="md:col-span-2">
                                                    <label for="home_hero_description"
                                                        class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Intro
                                                        paragraph</label>
                                                    <textarea id="home_hero_description" name="hero[description]" rows="4"
                                                        data-home-preview-target="home_preview_description"
                                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm leading-6 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ old('hero.description', $homeHero?->description ?? 'TechBridge brings academics, admissions, attendance, communication, and daily school operations together in one seamless platform for students, parents, teachers, and school leaders.') }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <aside
                                            class="border-t border-slate-200 bg-slate-950 p-5 text-white xl:border-l xl:border-t-0">
                                            <div
                                                class="sticky top-24 rounded-[1.5rem] bg-white p-4 text-slate-950 shadow-lg">
                                                <p
                                                    class="mb-3 text-[10px] font-black uppercase tracking-[0.24em] text-slate-400">
                                                    Live preview</p>
                                                <img id="home_hero_image_preview" src="{{ $homePageImage }}"
                                                    class="h-56 w-full rounded-2xl object-cover" alt="home hero preview">
                                                <div class="mt-5">
                                                    <p id="home_preview_badge"
                                                        class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-600">
                                                        {{ old('hero.badge', $homeHero?->subtitle ?? 'Future-ready school platform') }}
                                                    </p>
                                                    <h3 id="home_preview_title"
                                                        class="mt-2 text-2xl font-black leading-tight tracking-tight text-slate-950">
                                                        {{ old('hero.title', $homeHero?->title ?? 'A Smarter school experience for Every Students, Every Day.') }}
                                                    </h3>
                                                    <p id="home_preview_description"
                                                        class="mt-3 line-clamp-4 text-xs leading-5 text-slate-500">
                                                        {{ old('hero.description', $homeHero?->description ?? 'TechBridge brings academics, admissions, attendance, communication, and daily school operations together in one seamless platform for students, parents, teachers, and school leaders.') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </aside>
                                    </div>
                                </div>

                                <div class="grid gap-5 xl:grid-cols-2">
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                        <div class="flex items-center justify-between gap-3">
                                            <div>
                                                <h3 class="text-base font-black text-slate-900">Check Points</h3>
                                                <p class="mt-1 text-xs text-slate-500">Three short benefits under the hero
                                                    button.</p>
                                            </div>
                                            <button type="button" data-add-home-card="hero-points"
                                                class="rounded-full bg-blue-600 px-3 py-1 text-xs font-bold text-white transition hover:bg-blue-500">
                                                Add Point
                                            </button>
                                        </div>
                                        <div class="mt-4 space-y-3">
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
                                                    class="{{ $showPoint ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                    <input type="hidden" name="points[{{ $i }}][id]"
                                                        value="{{ $pointItem?->id }}">
                                                    <input type="hidden" name="points[{{ $i }}][delete]"
                                                        data-home-delete-field value="0">
                                                    <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                                                        <span
                                                            class="flex items-center gap-2 text-xs font-bold text-slate-500">
                                                            <span
                                                                class="grid h-6 w-6 place-items-center rounded-full bg-blue-600 text-[11px] text-white">{{ $i + 1 }}</span>
                                                            Point {{ $i + 1 }}
                                                        </span>
                                                        <div data-home-card-actions
                                                            class="flex flex-wrap items-center gap-2">
                                                            <button type="button" data-home-edit-card
                                                                class="rounded-full border border-blue-200 bg-white px-3 py-1 text-xs font-bold text-blue-600 transition hover:bg-blue-50">
                                                                Edit
                                                            </button>
                                                            <label
                                                                class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                                                                <input type="hidden"
                                                                    name="points[{{ $i }}][active]"
                                                                    value="0">
                                                                <input type="checkbox"
                                                                    name="points[{{ $i }}][active]"
                                                                    data-home-active-toggle value="1"
                                                                    {{ old('points.' . $i . '.active', $pointItem?->is_active ?? true) ? 'checked' : '' }}
                                                                    class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                                Active
                                                            </label>
                                                            <button type="button" data-home-remove-card
                                                                class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600 transition hover:bg-red-50">
                                                                Remove
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <textarea name="points[{{ $i }}][description]" rows="2"
                                                        class="w-full resize-none rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm leading-6 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ $pointDescription }}</textarea>
                                                    <input name="points[{{ $i }}][icon]" type="text"
                                                        data-random-icon-input
                                                        placeholder='Icon name, <i class="fa-solid fa-school"></i>, or SVG'
                                                        value="{{ $pointIcon }}"
                                                        class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                </div>
                                            @endfor
                                        </div>
                                    </div>

                                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                        <div class="flex items-center justify-between gap-3">
                                            <div>
                                                <h3 class="text-base font-black text-slate-900">Floating Stats</h3>
                                                <p class="mt-1 text-xs text-slate-500">Cards shown over the hero image.</p>
                                            </div>
                                            <button type="button" data-add-home-card="hero-stats"
                                                class="rounded-full bg-emerald-600 px-3 py-1 text-xs font-bold text-white transition hover:bg-emerald-500">
                                                Add Stat
                                            </button>
                                        </div>
                                        <div class="mt-4 grid gap-3">
                                            @for ($i = 0; $i < $statLimit; $i++)
                                                @php
                                                    $statItem = $homeStats->get($i);
                                                    $statLabel = old('stats.' . $i . '.label', $statItem?->title ?? '');
                                                    $statValue = old('stats.' . $i . '.value', $statItem?->value ?? '');
                                                    $statTrend = old(
                                                        'stats.' . $i . '.trend',
                                                        $statItem?->subtitle ?? '',
                                                    );
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
                                                    $showStat =
                                                        filled($statLabel) || filled($statValue) || filled($statTrend);
                                                @endphp
                                                <div data-home-editor-card data-addable-card="hero-stats"
                                                    class="{{ $showStat ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                    <input type="hidden" name="stats[{{ $i }}][id]"
                                                        value="{{ $statItem?->id }}">
                                                    <input type="hidden" name="stats[{{ $i }}][delete]"
                                                        data-home-delete-field value="0">
                                                    <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                                                        <span
                                                            class="flex items-center gap-2 text-xs font-bold text-slate-500">
                                                            <span
                                                                class="grid h-6 w-6 place-items-center rounded-full bg-emerald-500 text-[11px] text-white">{{ $i + 1 }}</span>
                                                            Stat card {{ $i + 1 }}
                                                        </span>
                                                        <div data-home-card-actions
                                                            class="flex flex-wrap items-center gap-2">
                                                            <button type="button" data-home-edit-card
                                                                class="rounded-full border border-blue-200 bg-white px-3 py-1 text-xs font-bold text-blue-600 transition hover:bg-blue-50">
                                                                Edit
                                                            </button>
                                                            <label
                                                                class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                                                                <input type="hidden"
                                                                    name="stats[{{ $i }}][active]"
                                                                    value="0">
                                                                <input type="checkbox"
                                                                    name="stats[{{ $i }}][active]"
                                                                    data-home-active-toggle value="1"
                                                                    {{ old('stats.' . $i . '.active', $statItem?->is_active ?? true) ? 'checked' : '' }}
                                                                    class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                                Active
                                                            </label>
                                                            <button type="button" data-home-remove-card
                                                                class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600 transition hover:bg-red-50">
                                                                Remove
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="grid gap-2 sm:grid-cols-[minmax(0,1fr)_96px_130px]">
                                                        <input name="stats[{{ $i }}][label]" type="text"
                                                            placeholder="Label" value="{{ $statLabel }}"
                                                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                        <input name="stats[{{ $i }}][value]" type="text"
                                                            placeholder="Value" value="{{ $statValue }}"
                                                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                        <input name="stats[{{ $i }}][trend]" type="text"
                                                            placeholder="+6% this year" value="{{ $statTrend }}"
                                                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                    </div>
                                                    <div class="mt-2 grid gap-2 sm:grid-cols-[minmax(0,1fr)_150px]">
                                                        <div>
                                                            <label
                                                                class="mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-500">
                                                                Icon
                                                            </label>
                                                            <input name="stats[{{ $i }}][icon]" type="text"
                                                                data-stat-icon-select data-random-icon-input
                                                                placeholder='Icon name, <i class="fa-solid fa-school"></i>, or SVG'
                                                                value="{{ $statIcon }}"
                                                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                        </div>
                                                        <label
                                                            class="flex items-center gap-3 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600">
                                                            <span class="min-w-0 flex-1">Icon color</span>
                                                            <input name="stats[{{ $i }}][color]"
                                                                data-stat-color-input type="color"
                                                                value="{{ $statColor }}"
                                                                class="h-10 w-12 rounded-md border border-slate-300 bg-white p-1">
                                                        </label>
                                                    </div>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <div>
                                            <h3 class="text-base font-black text-slate-900">Bottom Feature Strip</h3>
                                            <p class="mt-1 text-xs text-slate-500">Four feature cards displayed below the
                                                hero header.</p>
                                        </div>
                                        <button type="button" data-add-home-card="hero-features"
                                            class="rounded-full bg-violet-600 px-3 py-1 text-xs font-bold text-white transition hover:bg-violet-500">
                                            Add Feature
                                        </button>
                                    </div>
                                    <div class="mt-4 grid gap-3 xl:grid-cols-2">
                                        @for ($i = 0; $i < $heroFeatureLimit; $i++)
                                            @php
                                                $heroFeatureItem = $homeFeatures->get($i);
                                                $heroFeatureTitle = old(
                                                    'features.' . $i . '.title',
                                                    $heroFeatureItem?->title ?? '',
                                                );
                                                $heroFeatureDescription = old(
                                                    'features.' . $i . '.description',
                                                    $heroFeatureItem?->description ?? '',
                                                );
                                                $heroFeatureIcon = old(
                                                    'features.' . $i . '.icon',
                                                    $heroFeatureItem?->icon ?? '',
                                                );
                                                $heroFeatureColor = old(
                                                    'features.' . $i . '.color',
                                                    $iconColorValue(
                                                        $heroFeatureItem?->color,
                                                        $heroFeatureColorDefaults[
                                                            $i % count($heroFeatureColorDefaults)
                                                        ],
                                                    ),
                                                );
                                                $showHeroFeature =
                                                    filled($heroFeatureTitle) || filled($heroFeatureDescription);
                                            @endphp
                                            <div data-home-editor-card data-addable-card="hero-features"
                                                class="{{ $showHeroFeature ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                <input type="hidden" name="features[{{ $i }}][id]"
                                                    value="{{ $heroFeatureItem?->id }}">
                                                <input type="hidden" name="features[{{ $i }}][delete]"
                                                    data-home-delete-field value="0">
                                                <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                                                    <span class="flex items-center gap-2 text-xs font-bold text-slate-500">
                                                        <span
                                                            class="grid h-6 w-6 place-items-center rounded-full bg-violet-600 text-[11px] text-white">{{ $i + 1 }}</span>
                                                        Feature {{ $i + 1 }}
                                                    </span>
                                                    <div data-home-card-actions class="flex flex-wrap items-center gap-2">
                                                        <button type="button" data-home-edit-card
                                                            class="rounded-full border border-blue-200 bg-white px-3 py-1 text-xs font-bold text-blue-600 transition hover:bg-blue-50">
                                                            Edit
                                                        </button>
                                                        <label
                                                            class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                                                            <input type="hidden"
                                                                name="features[{{ $i }}][active]"
                                                                value="0">
                                                            <input type="checkbox"
                                                                name="features[{{ $i }}][active]"
                                                                data-home-active-toggle value="1"
                                                                {{ old('features.' . $i . '.active', $heroFeatureItem?->is_active ?? true) ? 'checked' : '' }}
                                                                class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                            Active
                                                        </label>
                                                        <button type="button" data-home-remove-card
                                                            class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600 transition hover:bg-red-50">
                                                            Remove
                                                        </button>
                                                    </div>
                                                </div>
                                                <input name="features[{{ $i }}][title]" type="text"
                                                    placeholder="Feature title" value="{{ $heroFeatureTitle }}"
                                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                <textarea name="features[{{ $i }}][description]" rows="3" placeholder="Feature description"
                                                    class="mt-2 w-full resize-none rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm leading-6 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ $heroFeatureDescription }}</textarea>
                                                <input name="features[{{ $i }}][icon]" type="text"
                                                    data-random-icon-input
                                                    placeholder='Icon name, <i class="fa-solid fa-school"></i>, or SVG'
                                                    value="{{ $heroFeatureIcon }}"
                                                    class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                <label
                                                    class="mt-2 flex items-center gap-3 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600">
                                                    <span class="min-w-0 flex-1">Icon color</span>
                                                    <input name="features[{{ $i }}][color]" type="color"
                                                        value="{{ $heroFeatureColor }}"
                                                        class="h-10 w-12 rounded-md border border-slate-300 bg-white p-1">
                                                </label>
                                            </div>
                                        @endfor
                                    </div>
                                </div>

                                <div class="sticky bottom-4 z-10 flex justify-end">
                                    <div
                                        class="rounded-2xl border border-slate-200 bg-white/95 p-2 shadow-xl backdrop-blur">
                                        <button type="submit"
                                            class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-indigo-500">
                                            Save Hero Page
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-800">
                                Run migrations to enable dynamic homepage editing.
                            </div>
                        @endif
                    </section>
