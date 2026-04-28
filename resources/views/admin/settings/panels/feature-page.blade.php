                    <section data-settings-panel="feature-page" class="hidden space-y-6">
                        @if ($homePageReady ?? false)
                            <form method="POST" action="{{ route('admin.settings.feature-page.update') }}"
                                class="space-y-5">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="_settings_tab" value="feature-page">

                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div>
                                            <span
                                                class="inline-flex rounded-full bg-sky-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-sky-700">
                                                Platform features
                                            </span>
                                            <h3 class="mt-3 text-base font-black text-slate-900">Feature Section Header
                                            </h3>
                                            <p class="mt-1 text-xs text-slate-500">Badge, headline, and tag shown above the
                                                feature cards.</p>
                                        </div>
                                        <span
                                            class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">Visible
                                            on homepage</span>
                                    </div>

                                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                                        <div>
                                            <label for="platform_feature_badge"
                                                class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Badge</label>
                                            <input id="platform_feature_badge" name="feature[badge]" type="text"
                                                value="{{ old('feature.badge', $platformFeature?->subtitle ?? __('home.features.badge')) }}"
                                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                        </div>
                                        <div>
                                            <label for="platform_feature_tag"
                                                class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Tag</label>
                                            <input id="platform_feature_tag" name="feature[tag]" type="text"
                                                value="{{ old('feature.tag', $platformFeature?->value ?? __('home.features.tag')) }}"
                                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label for="platform_feature_title"
                                                class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Headline</label>
                                            <textarea id="platform_feature_title" name="feature[title]" rows="3"
                                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold leading-6 text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ old('feature.title', $platformFeature?->title ?? __('home.features.title')) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <div>
                                            <h3 class="text-base font-black text-slate-900">Feature Cards</h3>
                                            <p class="mt-1 text-xs text-slate-500">Cards displayed in the Platform
                                                Features section.</p>
                                        </div>
                                        <button type="button" data-add-home-card="platform-features"
                                            class="rounded-full bg-sky-600 px-3 py-1 text-xs font-bold text-white transition hover:bg-sky-500">
                                            Add Feature
                                        </button>
                                    </div>

                                    <div class="mt-4 grid gap-3 xl:grid-cols-2">
                                        @for ($i = 0; $i < $platformFeatureLimit; $i++)
                                            @php
                                                $platformFeatureItem = $platformFeatureCards->get($i);
                                                $platformFeatureTitle = old(
                                                    'feature_cards.' . $i . '.title',
                                                    $platformFeatureItem?->title ?? '',
                                                );
                                                $platformFeatureDescription = old(
                                                    'feature_cards.' . $i . '.description',
                                                    $platformFeatureItem?->description ?? '',
                                                );
                                                $platformFeatureIcon = old(
                                                    'feature_cards.' . $i . '.icon',
                                                    $platformFeatureItem?->icon ?? '',
                                                );
                                                $platformFeatureColor = old(
                                                    'feature_cards.' . $i . '.color',
                                                    $iconColorValue(
                                                        $platformFeatureItem?->color,
                                                        $featureCardColorDefaults[
                                                            $i % count($featureCardColorDefaults)
                                                        ],
                                                    ),
                                                );
                                                $showPlatformFeature =
                                                    filled($platformFeatureTitle) ||
                                                    filled($platformFeatureDescription);
                                            @endphp
                                            <div data-home-editor-card data-addable-card="platform-features"
                                                class="{{ $showPlatformFeature ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                <input type="hidden" name="feature_cards[{{ $i }}][id]"
                                                    value="{{ $platformFeatureItem?->id }}">
                                                <input type="hidden" name="feature_cards[{{ $i }}][delete]"
                                                    data-home-delete-field value="0">
                                                <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                                                    <span class="flex items-center gap-2 text-xs font-bold text-slate-500">
                                                        <span
                                                            class="grid h-6 w-6 place-items-center rounded-full bg-sky-600 text-[11px] text-white">{{ $i + 1 }}</span>
                                                        Feature card {{ $i + 1 }}
                                                    </span>
                                                    <div data-home-card-actions class="flex flex-wrap items-center gap-2">
                                                        <button type="button" data-home-edit-card
                                                            class="rounded-full border border-blue-200 bg-white px-3 py-1 text-xs font-bold text-blue-600 transition hover:bg-blue-50">
                                                            Edit
                                                        </button>
                                                        <label
                                                            class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                                                            <input type="hidden"
                                                                name="feature_cards[{{ $i }}][active]"
                                                                value="0">
                                                            <input type="checkbox"
                                                                name="feature_cards[{{ $i }}][active]"
                                                                data-home-active-toggle value="1"
                                                                {{ old('feature_cards.' . $i . '.active', $platformFeatureItem?->is_active ?? true) ? 'checked' : '' }}
                                                                class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                            Active
                                                        </label>
                                                        <button type="button" data-home-remove-card
                                                            class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600 transition hover:bg-red-50">
                                                            Remove
                                                        </button>
                                                    </div>
                                                </div>
                                                <input name="feature_cards[{{ $i }}][title]" type="text"
                                                    placeholder="Feature title" value="{{ $platformFeatureTitle }}"
                                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                <textarea name="feature_cards[{{ $i }}][description]" rows="3" placeholder="Feature description"
                                                    class="mt-2 w-full resize-none rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm leading-6 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ $platformFeatureDescription }}</textarea>
                                                <input name="feature_cards[{{ $i }}][icon]" type="text"
                                                    data-random-icon-input
                                                    placeholder='Icon name, <i class="fa-solid fa-school"></i>, or SVG'
                                                    value="{{ $platformFeatureIcon }}"
                                                    class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                <label
                                                    class="mt-2 flex items-center gap-3 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600">
                                                    <span class="min-w-0 flex-1">Icon color</span>
                                                    <input name="feature_cards[{{ $i }}][color]"
                                                        type="color" value="{{ $platformFeatureColor }}"
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
                                            Save Feature Page
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-800">
                                Run migrations to enable dynamic feature page editing.
                            </div>
                        @endif
                    </section>
