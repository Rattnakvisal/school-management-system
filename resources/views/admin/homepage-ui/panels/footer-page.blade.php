<section data-settings-panel="footer-page" class="hidden space-y-6">
    @if ($homePageReady ?? false)
        <form method="POST" action="{{ route('admin.settings.footer-page.update') }}" enctype="multipart/form-data"
            class="space-y-6">
            @csrf
            @method('PUT')

            <input type="hidden" name="_settings_tab" value="footer-page">

            {{-- Footer Content --}}
            <div
                class="relative overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm ring-1 ring-black/[0.03]">
                <div
                    class="absolute inset-x-0 top-0 h-[3px] rounded-t-3xl bg-gradient-to-r from-blue-400 to-indigo-500">
                </div>

                <div class="px-6 pb-6 pt-7 sm:px-7">
                    <span
                        class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-slate-700">
                        Footer
                    </span>

                    <h3 class="mt-3 text-base font-black text-slate-900">
                        Footer Content
                    </h3>

                    <div class="mt-5 grid gap-5 lg:grid-cols-[12rem_minmax(0,1fr)]">
                        <div>
                            <img id="footer_logo_preview" src="{{ $footerLogo }}" alt="Footer logo preview"
                                class="h-36 w-full rounded-2xl border border-slate-200 bg-slate-950 object-contain p-4 shadow-sm">

                            <div
                                class="mt-3 rounded-2xl border border-indigo-100 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700">
                                Logo, school name, and brand description are synced from Navbar Page.
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <input name="footer[tagline]" type="hidden"
                                value="{{ old('footer.tagline', $homeBrand?->description ?? \App\Support\HomePageContent::text('brand.tagline')) }}">

                            <input type="text" value="{{ old('brand.name', $homeBrand?->title ?? 'TechBridge') }}"
                                aria-label="Synced school name" readonly
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700">

                            <input name="footer[explore]" type="text"
                                value="{{ old('footer.explore', $footerSection?->subtitle ?? \App\Support\HomePageContent::text('footer.explore')) }}"
                                placeholder="Explore label"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">

                            <input name="footer[contact]" type="text"
                                value="{{ old('footer.contact', $footerSection?->value ?? \App\Support\HomePageContent::text('footer.contact')) }}"
                                placeholder="Contact label"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">

                            <input name="footer[copyright]" type="text"
                                value="{{ old('footer.copyright', $footerSection?->meta['copyright'] ?? \App\Support\HomePageContent::text('footer.copyright')) }}"
                                placeholder="Copyright text"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">

                            <input type="text"
                                value="{{ old('brand.description', $homeBrand?->description ?? \App\Support\HomePageContent::text('brand.tagline')) }}"
                                aria-label="Synced brand description" readonly
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 md:col-span-2">

                            <textarea name="footer[description]" rows="3" placeholder="Description"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 md:col-span-2">{{ old('footer.description', $footerSection?->description ?? \App\Support\HomePageContent::text('footer.description')) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Links --}}
            <div
                class="relative overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm ring-1 ring-black/[0.03]">
                <div
                    class="absolute inset-x-0 top-0 h-[3px] rounded-t-3xl bg-gradient-to-r from-blue-400 to-indigo-500">
                </div>

                <div class="px-6 pb-6 pt-7 sm:px-7">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h3 class="text-base font-black text-slate-900">
                                Footer Links
                            </h3>

                            <p class="mt-1 text-xs text-slate-500">
                                Manage links displayed in the footer explore area.
                            </p>
                        </div>

                        <button type="button" data-add-home-card="footer-links"
                            class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-emerald-600 px-3.5 py-1.5 text-[11px] font-bold text-white shadow-sm shadow-emerald-200 transition-all duration-150 hover:bg-emerald-500 hover:shadow-emerald-300 active:scale-95">
                            <svg class="h-3 w-3" viewBox="0 0 12 12" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round">
                                <path d="M6 1v10M1 6h10" />
                            </svg>
                            Add Link
                        </button>
                    </div>

                    <div class="mt-4 grid gap-3 xl:grid-cols-2">
                        @for ($i = 0; $i < $footerLinkLimit; $i++)
                            @php
                                $link = $dynamicFooterLinks->get($i);

                                $label = old('footer_links.' . $i . '.label', $link?->title ?? '');

                                $href = old('footer_links.' . $i . '.href', $link?->value ?? '');

                                $showLink = filled($label) || filled($href);
                            @endphp

                            <div data-home-editor-card data-addable-card="footer-links"
                                class="{{ $showLink ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                                <input type="hidden" name="footer_links[{{ $i }}][id]"
                                    value="{{ old('footer_links.' . $i . '.id', $link?->id) }}">

                                <input type="hidden" name="footer_links[{{ $i }}][delete]"
                                    data-home-delete-field value="0">

                                <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                                    <span class="flex items-center gap-2 text-xs font-bold text-slate-500">
                                        <span
                                            class="grid h-6 w-6 place-items-center rounded-full bg-slate-900 text-[11px] text-white">
                                            {{ $i + 1 }}
                                        </span>

                                        Footer link {{ $i + 1 }}
                                    </span>

                                    <div data-home-card-actions class="flex flex-wrap items-center gap-2">
                                        <label
                                            class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                                            <input type="hidden" name="footer_links[{{ $i }}][active]"
                                                value="0">

                                            <input type="checkbox" name="footer_links[{{ $i }}][active]"
                                                data-home-active-toggle value="1"
                                                {{ old('footer_links.' . $i . '.active', $link?->is_active ?? true) ? 'checked' : '' }}
                                                class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">

                                            Active
                                        </label>

                                        <button type="button" data-home-remove-card
                                            class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600 transition hover:bg-red-50">
                                            Remove
                                        </button>
                                    </div>
                                </div>

                                <input data-navbar-label-input name="footer_links[{{ $i }}][label]"
                                    type="text" value="{{ $label }}" placeholder="Label"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">

                                <select data-navbar-page-select name="footer_links[{{ $i }}][href]"
                                    class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                    <option value="">Select page</option>

                                    @foreach ($navbarPageOptions as $pageHref => $pageLabel)
                                        <option value="{{ $pageHref }}" data-label="{{ $pageLabel }}"
                                            @selected($href === $pageHref)>
                                            {{ $pageLabel }}
                                        </option>
                                    @endforeach

                                    @if (filled($href) && !array_key_exists($href, $navbarPageOptions))
                                        <option value="{{ $href }}" selected>
                                            {{ $href }}
                                        </option>
                                    @endif
                                </select>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            {{-- Footer Contact Lines --}}
            <div
                class="relative overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm ring-1 ring-black/[0.03]">
                <div
                    class="absolute inset-x-0 top-0 h-[3px] rounded-t-3xl bg-gradient-to-r from-blue-400 to-indigo-500">
                </div>

                <div class="px-6 pb-6 pt-7 sm:px-7">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h3 class="text-base font-black text-slate-900">
                                Footer Contact Lines
                            </h3>

                            <p class="mt-1 text-xs text-slate-500">
                                Manage email, phone, address, or other contact information.
                            </p>
                        </div>

                        <button type="button" data-add-home-card="footer-contacts"
                            class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-emerald-600 px-3.5 py-1.5 text-[11px] font-bold text-white shadow-sm shadow-emerald-200 transition-all duration-150 hover:bg-emerald-500 hover:shadow-emerald-300 active:scale-95">
                            <svg class="h-3 w-3" viewBox="0 0 12 12" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round">
                                <path d="M6 1v10M1 6h10" />
                            </svg>
                            Add Contact
                        </button>
                    </div>

                    <div class="mt-4 grid gap-3 xl:grid-cols-2">
                        @for ($i = 0; $i < $footerContactLimit; $i++)
                            @php
                                $contact = $dynamicFooterContacts->get($i);

                                $label = old('footer_contacts.' . $i . '.label', $contact?->title ?? '');

                                $value = old('footer_contacts.' . $i . '.value', $contact?->value ?? '');

                                $showContact = filled($label) || filled($value);
                            @endphp

                            <div data-home-editor-card data-addable-card="footer-contacts"
                                class="{{ $showContact ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                                <input type="hidden" name="footer_contacts[{{ $i }}][id]"
                                    value="{{ $contact?->id }}">

                                <input type="hidden" name="footer_contacts[{{ $i }}][delete]"
                                    data-home-delete-field value="0">

                                <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                                    <span class="flex items-center gap-2 text-xs font-bold text-slate-500">
                                        <span
                                            class="grid h-6 w-6 place-items-center rounded-full bg-slate-900 text-[11px] text-white">
                                            {{ $i + 1 }}
                                        </span>

                                        Footer contact {{ $i + 1 }}
                                    </span>

                                    <div data-home-card-actions class="flex flex-wrap items-center gap-2">
                                        <label
                                            class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                                            <input type="hidden" name="footer_contacts[{{ $i }}][active]"
                                                value="0">

                                            <input type="checkbox"
                                                name="footer_contacts[{{ $i }}][active]"
                                                data-home-active-toggle value="1"
                                                {{ old('footer_contacts.' . $i . '.active', $contact?->is_active ?? true) ? 'checked' : '' }}
                                                class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">

                                            Active
                                        </label>

                                        <button type="button" data-home-remove-card
                                            class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600 transition hover:bg-red-50">
                                            Remove
                                        </button>
                                    </div>
                                </div>

                                <input name="footer_contacts[{{ $i }}][label]" type="text"
                                    value="{{ $label }}" placeholder="Label"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">

                                <input name="footer_contacts[{{ $i }}][value]" type="text"
                                    value="{{ $value }}" placeholder="Value"
                                    class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            {{-- Save Button --}}
            <div class="sticky bottom-4 z-10 flex justify-end">
                <div
                    class="flex items-center gap-3 rounded-2xl border border-slate-200/80 bg-white/90 px-3 py-2.5 shadow-xl shadow-slate-200/60 backdrop-blur-md">
                    <p class="hidden text-[11px] font-semibold text-slate-400 sm:block">
                        Unsaved changes
                    </p>

                    <div class="hidden h-4 w-px bg-slate-200 sm:block"></div>

                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm shadow-indigo-200 transition-all duration-150 hover:bg-indigo-500 hover:shadow-indigo-300 active:scale-[0.97]">
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M13.5 4.5L6 12 2.5 8.5" />
                        </svg>

                        Save Footer Page
                    </button>
                </div>
            </div>
        </form>
    @else
        <div class="rounded-2xl border border-amber-200 bg-amber-50 dark:border-amber-900/50 dark:bg-amber-900/20 dark:border-amber-900/50 dark:bg-amber-900/20 p-6 text-sm text-amber-800">
            Run migrations to enable dynamic footer page editing.
        </div>
    @endif
</section>
