                    <section data-settings-panel="footer-page" class="hidden space-y-6">
                        @if ($homePageReady ?? false)
                            <form method="POST" action="{{ route('admin.settings.footer-page.update') }}"
                                enctype="multipart/form-data" class="space-y-5">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="_settings_tab" value="footer-page">

                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <span
                                        class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-slate-700">Footer</span>
                                    <h3 class="mt-3 text-base font-black text-slate-900">Footer Content</h3>
                                    <div class="mt-5 grid gap-5 lg:grid-cols-[12rem_minmax(0,1fr)]">
                                        <div>
                                            <img id="footer_logo_preview" src="{{ $footerLogo }}"
                                                alt="Footer logo preview"
                                                class="h-36 w-full rounded-2xl border border-slate-200 bg-slate-950 object-contain p-4 shadow-sm">
                                            <input id="footer_logo_input" name="footer[logo]" type="file"
                                                accept="image/*" class="hidden">
                                            <input id="remove_footer_logo" name="footer[remove_logo]" type="hidden"
                                                value="0">
                                            <div class="mt-3 flex flex-wrap gap-2">
                                                <button id="upload_footer_logo_btn" type="button"
                                                    class="rounded-full bg-slate-900 px-3 py-1.5 text-xs font-bold text-white transition hover:bg-slate-700">
                                                    Upload Logo
                                                </button>
                                                <button id="delete_footer_logo_btn" type="button"
                                                    class="rounded-full border border-red-200 bg-white px-3 py-1.5 text-xs font-bold text-red-600 transition hover:bg-red-50">
                                                    Remove
                                                </button>
                                            </div>
                                        </div>
                                        <div class="grid gap-4 md:grid-cols-2">
                                            <input name="footer[tagline]" type="text"
                                                value="{{ old('footer.tagline', $footerSection?->title ?? \App\Support\HomePageContent::text('footer.tagline')) }}"
                                                placeholder="Tagline"
                                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                            <input name="footer[explore]" type="text"
                                                value="{{ old('footer.explore', $footerSection?->subtitle ?? \App\Support\HomePageContent::text('footer.explore')) }}"
                                                placeholder="Explore label"
                                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                            <input name="footer[contact]" type="text"
                                                value="{{ old('footer.contact', $footerSection?->value ?? \App\Support\HomePageContent::text('footer.contact')) }}"
                                                placeholder="Contact label"
                                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                            <input name="footer[copyright]" type="text"
                                                value="{{ old('footer.copyright', $footerSection?->meta['copyright'] ?? \App\Support\HomePageContent::text('footer.copyright')) }}"
                                                placeholder="Copyright text"
                                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                            <textarea name="footer[description]" rows="3" placeholder="Description"
                                                class="md:col-span-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ old('footer.description', $footerSection?->description ?? \App\Support\HomePageContent::text('footer.description')) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <div class="flex items-center justify-between gap-3">
                                        <h3 class="text-base font-black text-slate-900">Footer Links</h3>
                                        <button type="button" data-add-home-card="footer-links"
                                            class="rounded-full bg-slate-900 px-3 py-1 text-xs font-bold text-white">Add
                                            Link</button>
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
                                                    value="{{ $link?->id }}">
                                                <input type="hidden" name="footer_links[{{ $i }}][delete]"
                                                    data-home-delete-field value="0">
                                                <div class="mb-2 flex items-center justify-between gap-2">
                                                    <span class="text-xs font-bold text-slate-500">Footer link
                                                        {{ $i + 1 }}</span>
                                                    <div data-home-card-actions class="flex gap-2">
                                                        <label
                                                            class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                                                            <input type="hidden"
                                                                name="footer_links[{{ $i }}][active]"
                                                                value="0">
                                                            <input type="checkbox"
                                                                name="footer_links[{{ $i }}][active]"
                                                                data-home-active-toggle value="1"
                                                                {{ old('footer_links.' . $i . '.active', $link?->is_active ?? true) ? 'checked' : '' }}>
                                                            Active
                                                        </label>
                                                        <button type="button" data-home-remove-card
                                                            class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600">Remove</button>
                                                    </div>
                                                </div>
                                                <input name="footer_links[{{ $i }}][label]" type="text"
                                                    value="{{ $label }}" placeholder="Label"
                                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold">
                                                <input name="footer_links[{{ $i }}][href]" type="text"
                                                    value="{{ $href }}" placeholder="#about"
                                                    class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                            </div>
                                        @endfor
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <div class="flex items-center justify-between gap-3">
                                        <h3 class="text-base font-black text-slate-900">Footer Contact Lines</h3>
                                        <button type="button" data-add-home-card="footer-contacts"
                                            class="rounded-full bg-slate-900 px-3 py-1 text-xs font-bold text-white">Add
                                            Contact</button>
                                    </div>
                                    <div class="mt-4 grid gap-3 xl:grid-cols-2">
                                        @for ($i = 0; $i < $footerContactLimit; $i++)
                                            @php
                                                $contact = $dynamicFooterContacts->get($i);
                                                $label = old(
                                                    'footer_contacts.' . $i . '.label',
                                                    $contact?->title ?? '',
                                                );
                                                $value = old(
                                                    'footer_contacts.' . $i . '.value',
                                                    $contact?->value ?? '',
                                                );
                                                $showContact = filled($label) || filled($value);
                                            @endphp
                                            <div data-home-editor-card data-addable-card="footer-contacts"
                                                class="{{ $showContact ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                <input type="hidden" name="footer_contacts[{{ $i }}][id]"
                                                    value="{{ $contact?->id }}">
                                                <input type="hidden"
                                                    name="footer_contacts[{{ $i }}][delete]"
                                                    data-home-delete-field value="0">
                                                <div class="mb-2 flex items-center justify-between gap-2">
                                                    <span class="text-xs font-bold text-slate-500">Footer contact
                                                        {{ $i + 1 }}</span>
                                                    <div data-home-card-actions class="flex gap-2">
                                                        <label
                                                            class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                                                            <input type="hidden"
                                                                name="footer_contacts[{{ $i }}][active]"
                                                                value="0">
                                                            <input type="checkbox"
                                                                name="footer_contacts[{{ $i }}][active]"
                                                                data-home-active-toggle value="1"
                                                                {{ old('footer_contacts.' . $i . '.active', $contact?->is_active ?? true) ? 'checked' : '' }}>
                                                            Active
                                                        </label>
                                                        <button type="button" data-home-remove-card
                                                            class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600">Remove</button>
                                                    </div>
                                                </div>
                                                <input name="footer_contacts[{{ $i }}][label]"
                                                    type="text" value="{{ $label }}" placeholder="Label"
                                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold">
                                                <input name="footer_contacts[{{ $i }}][value]"
                                                    type="text" value="{{ $value }}" placeholder="Value"
                                                    class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                            </div>
                                        @endfor
                                    </div>
                                </div>

                                <div class="sticky bottom-4 z-10 flex justify-end">
                                    <div class="rounded-2xl border border-slate-200 bg-white/95 p-2 shadow-xl">
                                        <button type="submit"
                                            class="rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white">Save
                                            Footer Page</button>
                                    </div>
                                </div>
                            </form>
                        @endif
                    </section>
