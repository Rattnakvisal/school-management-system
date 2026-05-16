<section data-settings-panel="course-page" class="hidden space-y-6">
    @if ($homePageReady ?? false)
        <form method="POST" action="{{ route('admin.settings.course-page.update') }}" enctype="multipart/form-data"
            class="space-y-6">
            @csrf
            @method('PUT')

            <input type="hidden" name="_settings_tab" value="course-page">

            {{-- Course Section Header --}}
            <div
                class="relative overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm ring-1 ring-black/[0.03]">
                <div class="absolute inset-x-0 top-0 h-[3px] rounded-t-3xl bg-gradient-to-r from-blue-400 to-indigo-500">
                </div>

                <div class="px-6 pb-6 pt-7 sm:px-7">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <span
                                class="inline-flex rounded-full bg-cyan-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-cyan-700">
                                Courses
                            </span>

                            <h3 class="mt-3 text-base font-black text-slate-900">
                                Course Section
                            </h3>

                            <p class="mt-1 text-xs text-slate-500">
                                Badge, headline, and description shown above the course section.
                            </p>
                        </div>

                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                            Visible on homepage
                        </span>
                    </div>

                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">
                                Badge
                            </label>

                            <input name="course[badge]" type="text"
                                value="{{ old('course.badge', $courseSection?->subtitle ?: \App\Support\HomePageContent::text('course.badge')) }}"
                                placeholder="Badge"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                        </div>

                        <div class="md:col-span-2">
                            <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">
                                Title
                            </label>

                            <textarea name="course[title]" rows="2" placeholder="Title"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold leading-6 text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ old('course.title', $courseSection?->title ?: \App\Support\HomePageContent::text('course.title')) }}</textarea>
                        </div>

                        <div class="md:col-span-2">
                            <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">
                                Description
                            </label>

                            <textarea name="course[description]" rows="3" placeholder="Description"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm leading-6 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ old('course.description', $courseSection?->description ?: \App\Support\HomePageContent::text('course.description')) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Course Info Card --}}
            <div
                class="relative overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm ring-1 ring-black/[0.03]">
                <div
                    class="absolute inset-x-0 top-0 h-[3px] rounded-t-3xl bg-gradient-to-r from-blue-400 to-indigo-500">
                </div>

                <div class="px-6 pb-6 pt-7 sm:px-7">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h3 class="text-base font-black text-slate-900">
                                Course Info Card
                            </h3>

                            <p class="mt-1 text-xs text-slate-500">
                                Extra course message stored with this homepage section.
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">
                                Label
                            </label>

                            <input name="featured[label]" type="text"
                                value="{{ old('featured.label', $courseFeatured?->subtitle ?: \App\Support\HomePageContent::text('course.featured_label')) }}"
                                placeholder="Label"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">
                                Title
                            </label>

                            <input name="featured[title]" type="text"
                                value="{{ old('featured.title', $courseFeatured?->title ?: \App\Support\HomePageContent::text('course.featured_title')) }}"
                                placeholder="Title"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                        </div>

                        <div class="md:col-span-2">
                            <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">
                                Description
                            </label>

                            <textarea name="featured[description]" rows="3" placeholder="Description"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm leading-6 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ old('featured.description', $courseFeatured?->description ?: \App\Support\HomePageContent::text('course.featured_description')) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Course Cards --}}
            <div
                class="relative overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm ring-1 ring-black/[0.03]">
                <div
                    class="absolute inset-x-0 top-0 h-[3px] rounded-t-3xl bg-gradient-to-r from-blue-400 to-indigo-500">
                </div>

                <div class="px-6 pb-6 pt-7 sm:px-7">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h3 class="text-base font-black text-slate-900">
                                Course Cards
                            </h3>

                            <p class="mt-1 text-xs text-slate-500">
                                Cards shown in the homepage course section.
                            </p>
                        </div>

                        <button type="button" data-add-home-card="course-cards"
                            class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-emerald-600 px-3.5 py-1.5 text-[11px] font-bold text-white shadow-sm shadow-emerald-200 transition-all duration-150 hover:bg-emerald-500 hover:shadow-emerald-300 active:scale-95">
                            <svg class="h-3 w-3" viewBox="0 0 12 12" fill="none" stroke="currentColor"
                                stroke-width="2.5" stroke-linecap="round">
                                <path d="M6 1v10M1 6h10" />
                            </svg>

                            Add Course
                        </button>
                    </div>

                    <div class="mt-4 grid gap-3 xl:grid-cols-2">
                        @for ($i = 0; $i < $courseCardLimit; $i++)
                            @php
                                $step = $courseCards->get($i);

                                $stepTitle = old('course_cards.' . $i . '.title', $step?->title ?? '');

                                $stepDescription = old(
                                    'course_cards.' . $i . '.description',
                                    $step?->description ?? '',
                                );

                                $stepMeta = is_array($step?->meta) ? $step->meta : [];
                                $defaultCourse = \App\Support\HomePageContent::get('course.items.' . $i, []);
                                $stepCategory = old(
                                    'course_cards.' . $i . '.category',
                                    $stepMeta['category'] ?? data_get($defaultCourse, 'category', ''),
                                );
                                $stepInstructorName = old(
                                    'course_cards.' . $i . '.instructor_name',
                                    $stepMeta['instructor_name'] ?? data_get($defaultCourse, 'instructor_name', ''),
                                );
                                $stepInstructorRole = old(
                                    'course_cards.' . $i . '.instructor_role',
                                    $stepMeta['instructor_role'] ?? data_get($defaultCourse, 'instructor_role', ''),
                                );
                                $stepLessons = old(
                                    'course_cards.' . $i . '.lessons',
                                    $stepMeta['lessons'] ?? data_get($defaultCourse, 'lessons', ''),
                                );
                                $stepDuration = old(
                                    'course_cards.' . $i . '.duration',
                                    $stepMeta['duration'] ?? data_get($defaultCourse, 'duration', ''),
                                );
                                $stepRating = old(
                                    'course_cards.' . $i . '.rating',
                                    $stepMeta['rating'] ?? data_get($defaultCourse, 'rating', ''),
                                );
                                $stepReviewCount = old(
                                    'course_cards.' . $i . '.review_count',
                                    $stepMeta['review_count'] ?? data_get($defaultCourse, 'review_count', ''),
                                );
                                $stepPrice = old(
                                    'course_cards.' . $i . '.price',
                                    $stepMeta['price'] ?? data_get($defaultCourse, 'price', ''),
                                );
                                $stepButtonLabel = old(
                                    'course_cards.' . $i . '.button_label',
                                    $stepMeta['button_label'] ?? data_get($defaultCourse, 'button_label', ''),
                                );
                                $stepImage = $step?->image_path
                                    ? route('public.storage', ['path' => $step->image_path])
                                    : asset(data_get($defaultCourse, 'image', 'images/study.jpg'));

                                $stepColor = old(
                                    'course_cards.' . $i . '.color',
                                    $iconColorValue(
                                        $step?->color ?: data_get($defaultCourse, 'color'),
                                        $courseCardColorDefaults[$i % count($courseCardColorDefaults)],
                                    ),
                                );

                                $showStep = filled($stepTitle) || filled($stepDescription) || filled($stepCategory);
                            @endphp

                            <div data-home-editor-card data-addable-card="course-cards"
                                class="{{ $showStep ? '' : 'hidden' }} rounded-xl border border-slate-200 bg-slate-50 p-3">
                                <input type="hidden" name="course_cards[{{ $i }}][id]"
                                    value="{{ $step?->id }}">

                                <input type="hidden" name="course_cards[{{ $i }}][delete]"
                                    data-home-delete-field value="0">

                                <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                                    <span class="flex items-center gap-2 text-xs font-bold text-slate-500">
                                        <span
                                            class="grid h-6 w-6 place-items-center rounded-full bg-cyan-600 text-[11px] text-white">
                                            {{ $i + 1 }}
                                        </span>

                                        Step {{ $i + 1 }}
                                    </span>

                                    <div data-home-card-actions class="flex flex-wrap items-center gap-2">
                                        <label
                                            class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                                            <input type="hidden" name="course_cards[{{ $i }}][active]"
                                                value="0">

                                            <input type="checkbox" name="course_cards[{{ $i }}][active]"
                                                data-home-active-toggle value="1"
                                                {{ old('course_cards.' . $i . '.active', $step?->is_active ?? true) ? 'checked' : '' }}
                                                class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">

                                            Active
                                        </label>

                                        <button type="button" data-home-remove-card
                                            class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-bold text-red-600 transition hover:bg-red-50">
                                            Remove
                                        </button>
                                    </div>
                                </div>

                                <input name="course_cards[{{ $i }}][title]" type="text"
                                    value="{{ $stepTitle }}" placeholder="Step title"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">

                                <textarea name="course_cards[{{ $i }}][description]" rows="3" placeholder="Course description"
                                    class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm leading-6 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ $stepDescription }}</textarea>

                                <div class="mt-2 grid gap-2 md:grid-cols-2">
                                    <input name="course_cards[{{ $i }}][category]" type="text"
                                        value="{{ $stepCategory }}" placeholder="Category, example: Design"
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">

                                    <input name="course_cards[{{ $i }}][lessons]" type="text"
                                        value="{{ $stepLessons }}" placeholder="Lessons, example: 28 Lessons"
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">

                                    <input name="course_cards[{{ $i }}][duration]" type="text"
                                        value="{{ $stepDuration }}" placeholder="Duration, example: 12h 45m"
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">

                                    <input name="course_cards[{{ $i }}][rating]" type="text"
                                        value="{{ $stepRating }}" placeholder="Rating, example: 4.8"
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">

                                    <input name="course_cards[{{ $i }}][review_count]" type="text"
                                        value="{{ $stepReviewCount }}" placeholder="Reviews, example: 1,248"
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">

                                    <input name="course_cards[{{ $i }}][price]" type="text"
                                        value="{{ $stepPrice }}" placeholder="Price, example: $79"
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">

                                    <input name="course_cards[{{ $i }}][instructor_name]" type="text"
                                        value="{{ $stepInstructorName }}" placeholder="Instructor name"
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">

                                    <input name="course_cards[{{ $i }}][instructor_role]" type="text"
                                        value="{{ $stepInstructorRole }}" placeholder="Instructor role"
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">

                                    <input name="course_cards[{{ $i }}][button_label]" type="text"
                                        value="{{ $stepButtonLabel }}" placeholder="Button label, example: Enroll Now"
                                        class="md:col-span-2 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                </div>

                                <div class="mt-2 rounded-lg border border-slate-200 bg-white p-3">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <img src="{{ $stepImage }}" alt="{{ $stepTitle ?: 'Course image' }}"
                                            class="h-20 w-28 rounded-lg object-cover">

                                        <div class="min-w-0 flex-1">
                                            <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">
                                                Course image
                                            </label>

                                            <input name="course_cards[{{ $i }}][image]" type="file"
                                                accept="image/*"
                                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs outline-none transition file:mr-3 file:rounded-md file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-indigo-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                        </div>

                                        <label
                                            class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-slate-50 px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                                            <input type="hidden" name="course_cards[{{ $i }}][remove_image]"
                                                value="0">
                                            <input type="checkbox" name="course_cards[{{ $i }}][remove_image]"
                                                value="1"
                                                class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                            Remove image
                                        </label>
                                    </div>
                                </div>

                                <label
                                    class="mt-2 flex items-center gap-3 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600">
                                    <span class="min-w-0 flex-1">
                                        Icon color
                                    </span>

                                    <input name="course_cards[{{ $i }}][color]" type="color"
                                        value="{{ $stepColor }}"
                                        class="h-10 w-12 rounded-md border border-slate-300 bg-white p-1">
                                </label>
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

                        Save Course Page
                    </button>
                </div>
            </div>
        </form>
    @else
        <div class="rounded-2xl border border-amber-200 bg-amber-50 dark:border-amber-900/50 dark:bg-amber-900/20 dark:border-amber-900/50 dark:bg-amber-900/20 p-6 text-sm text-amber-800">
            Run migrations to enable dynamic course page editing.
        </div>
    @endif
</section>
