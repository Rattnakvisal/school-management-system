                    <section data-settings-panel="navbar-page" class="hidden space-y-6">
                        @if ($homePageReady ?? false)
                            <form method="POST" action="{{ route('admin.settings.navbar-page.update') }}"
                                enctype="multipart/form-data" class="space-y-5">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="_settings_tab" value="navbar-page">
                                <input type="hidden" id="remove_home_brand_logo" name="brand[remove_logo]"
                                    value="0">

                                <div
                                    class="overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white shadow-sm ring-1 ring-slate-100">
                                    <div class="grid gap-0 xl:grid-cols-[minmax(0,1fr)_420px]">
                                        <div class="p-5 sm:p-7">
                                            <div
                                                class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-200 pb-6">
                                                <div>
                                                    <span
                                                        class="inline-flex rounded-full bg-indigo-50 px-3 py-1 text-[11px] font-black uppercase tracking-[0.2em] text-indigo-700 ring-1 ring-indigo-100">
                                                        Website navigation
                                                    </span>
                                                    <h2 class="mt-3 text-2xl font-black tracking-tight text-slate-950">
                                                        Navbar Page Builder</h2>
                                                    <p class="mt-2 max-w-2xl text-sm leading-7 text-slate-500">
                                                        Update the public logo, school name, tagline, and home navigation
                                                        links from one screen.
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
                                                <div>
                                                    <label for="home_brand_name"
                                                        class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">School
                                                        name</label>
                                                    <input id="home_brand_name" name="brand[name]" type="text"
                                                        data-home-preview-target="navbar_preview_name"
                                                        value="{{ old('brand.name', $homeBrand?->title ?? 'TechBridge') }}"
                                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                </div>
                                                <div>
                                                    <label for="home_brand_description"
                                                        class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Brand
                                                        description</label>
                                                    <input id="home_brand_description" name="brand[description]"
                                                        type="text" data-home-preview-target="navbar_preview_tagline"
                                                        value="{{ old('brand.description', $homeBrand?->description ?? \App\Support\HomePageContent::text('brand.tagline')) }}"
                                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                </div>
                                                <div
                                                    class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 md:col-span-2">
                                                    <label
                                                        class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Brand
                                                        logo</label>
                                                    <input id="home_brand_logo_input" type="file" name="brand[logo]"
                                                        accept="image/*" class="hidden">
                                                    <div
                                                        class="mt-2 grid gap-2 sm:grid-cols-2 md:grid-cols-1 lg:grid-cols-2">
                                                        <button type="button" id="upload_home_brand_logo_btn"
                                                            class="rounded-xl bg-slate-900 px-4 py-3 text-xs font-bold text-white transition hover:bg-slate-800">
                                                            Upload Logo
                                                        </button>
                                                        <button type="button" id="delete_home_brand_logo_btn"
                                                            class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs font-bold text-slate-700 transition hover:bg-slate-100">
                                                            Use Default
                                                        </button>
                                                    </div>
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
                                                <div
                                                    class="rounded-2xl border border-slate-200 bg-slate-50 p-4 shadow-sm">
                                                    <div class="flex items-center gap-3">
                                                        <img id="home_brand_logo_preview" src="{{ $homeBrandLogo }}"
                                                            alt="Brand logo preview"
                                                            class="h-14 w-14 rounded-2xl bg-slate-950 object-contain p-2 shadow-sm">
                                                        <div class="min-w-0">
                                                            <h3 id="navbar_preview_name"
                                                                class="truncate text-xl font-black tracking-tight text-slate-950">
                                                                {{ old('brand.name', $homeBrand?->title ?? 'TechBridge') }}
                                                            </h3>
                                                            <p id="navbar_preview_tagline"
                                                                class="mt-1 line-clamp-2 text-xs leading-5 text-slate-500">
                                                                {{ old('brand.description', $homeBrand?->description ?? \App\Support\HomePageContent::text('brand.tagline')) }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="mt-5 flex flex-wrap gap-2">
                                                        @foreach ($navbarPageOptions as $pageLabel)
                                                            <span
                                                                class="rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-bold text-slate-600">
                                                                {{ $pageLabel }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </aside>
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <div>
                                            <h3 class="text-base font-black text-slate-900">Navbar Links</h3>
                                            <p class="mt-1 text-xs text-slate-500">Links shown in the home page header
                                                dropdown.</p>
                                        </div>
                                        <button type="button" data-add-home-card="navbar-links"
                                            class="rounded-full bg-indigo-600 px-3 py-1 text-xs font-bold text-white transition hover:bg-indigo-500">
                                            Add Link
                                        </button>
                                    </div>
                                    <div class="mt-4 grid gap-3 xl:grid-cols-2">
                                        @for ($i = 0; $i < $navbarLinkLimit; $i++)
                                            @php
                                                $navbarLink = $navbarLinks->get($i);
                                                $navbarLabel = old(
                                                    'navbar_links.' . $i . '.label',
                                                    $navbarLink?->title ?? '',
                                                );
                                                $navbarHref = old(
                                                    'navbar_links.' . $i . '.href',
                                                    $navbarLink?->value ?? '',
                                                );
                                                $showNavbarLink =
                                                    filled($navbarLabel) || filled($navbarHref) || $i < 4;
                                            @endphp
                                            <div data-home-editor-card data-addable-card="navbar-links"
                                                class="{{ $showNavbarLink ? '' : 'hidden' }} rounded-2xl border border-slate-200 bg-white p-3">
                                                <input type="hidden" name="navbar_links[{{ $i }}][id]"
                                                    value="{{ old('navbar_links.' . $i . '.id', $navbarLink?->id) }}">
                                                <input type="hidden" data-home-delete-field
                                                    name="navbar_links[{{ $i }}][delete]" value="0">
                                                <div class="mb-3 flex items-center justify-between gap-2">
                                                    <p class="text-xs font-black text-slate-500">Navbar link
                                                        {{ $i + 1 }}</p>
                                                    <div class="flex items-center gap-2">
                                                        <label
                                                            class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white px-2.5 py-1 text-xs font-bold text-slate-600">
                                                            <input type="checkbox" data-home-active-toggle
                                                                name="navbar_links[{{ $i }}][active]"
                                                                value="1" @checked(old('navbar_links.' . $i . '.active', $navbarLink?->is_active ?? true))
                                                                class="rounded border-slate-300 text-indigo-600">
                                                            Active
                                                        </label>
                                                        <button type="button" data-home-remove-card
                                                            class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600 transition hover:bg-red-50">
                                                            Remove
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="grid gap-2">
                                                    <input data-navbar-label-input
                                                        name="navbar_links[{{ $i }}][label]" type="text"
                                                        value="{{ $navbarLabel }}" placeholder="Label"
                                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                    <select data-navbar-page-select
                                                        name="navbar_links[{{ $i }}][href]"
                                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                        <option value="">Select page</option>
                                                        @foreach ($navbarPageOptions as $pageHref => $pageLabel)
                                                            <option value="{{ $pageHref }}"
                                                                data-label="{{ $pageLabel }}"
                                                                @selected($navbarHref === $pageHref)>
                                                                {{ $pageLabel }}
                                                            </option>
                                                        @endforeach
                                                        @if (filled($navbarHref) && !array_key_exists($navbarHref, $navbarPageOptions))
                                                            <option value="{{ $navbarHref }}" selected>
                                                                {{ $navbarHref }}</option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                </div>

                                <div class="sticky bottom-4 z-10 flex justify-end">
                                    <div
                                        class="rounded-2xl border border-slate-200 bg-white/95 p-2 shadow-xl backdrop-blur">
                                        <button type="submit"
                                            class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-indigo-500">
                                            Save Navbar Page
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-800">
                                Home page tables are not available yet. Run migrations, then refresh this page.
                            </div>
                        @endif
                    </section>
