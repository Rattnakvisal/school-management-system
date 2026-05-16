<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassStudyTime;
use App\Models\ContactMessage;
use App\Models\HomePageItem;
use App\Models\Notification;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\SubjectStudyTime;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $admin = $request->user();
        $homePageItems = collect();

        if (Schema::hasTable('home_page_items')) {
            $this->ensureDefaultHomePageItems();
            $homePageItems = HomePageItem::query()
                ->orderBy('section')
                ->orderBy('sort_order')
                ->get()
                ->groupBy('section');
        }

        $stats = [
            'students' => (int) User::query()->where('role', 'student')->count(),
            'teachers' => (int) User::query()->where('role', 'teacher')->count(),
            'classes' => (int) SchoolClass::query()->count(),
            'subjects' => (int) Subject::query()->count(),
            'classSlots' => (int) ClassStudyTime::query()->count(),
            'subjectSlots' => (int) SubjectStudyTime::query()->count(),
            'unreadMessages' => (int) ContactMessage::query()->where('is_read', false)->count(),
            'unreadNotifications' => (int) Notification::query()->where('is_read', false)->count(),
        ];

        return view('admin.settings', [
            'admin' => $admin,
            'stats' => $stats,
            'homePageItems' => $homePageItems,
            'homePageReady' => Schema::hasTable('home_page_items'),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $admin = $request->user();

        $validated = $request->validateWithBag('profileUpdate', [
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['nullable', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($admin->id)],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'avatar' => ['nullable', 'file', 'image', 'max:4096'],
            'remove_avatar' => ['nullable', 'boolean'],
        ]);

        $firstName = trim((string) ($validated['first_name'] ?? ''));
        $lastName = trim((string) ($validated['last_name'] ?? ''));

        $admin->name = trim($firstName . ' ' . $lastName);
        $admin->email = (string) $validated['email'];
        $admin->phone_number = trim((string) ($validated['phone_number'] ?? '')) ?: null;

        $currentAvatar = $admin->avatar;

        if ($request->boolean('remove_avatar')) {
            $this->deleteStoredAvatar($currentAvatar);
            $admin->avatar = null;
            $currentAvatar = null;
        }

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars/admins', 'public');
            if ($avatarPath !== false) {
                if ($currentAvatar && $currentAvatar !== $avatarPath) {
                    $this->deleteStoredAvatar($currentAvatar);
                }
                $admin->avatar = $avatarPath;
            }
        }

        $admin->save();

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Profile updated successfully.');
    }

    private function deleteStoredAvatar(?string $avatar): void
    {
        if (!$this->isLocalStorageAvatar($avatar)) {
            return;
        }

        $path = $this->normalizePublicPath((string) $avatar);
        if ($path !== '' && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function isLocalStorageAvatar(?string $avatar): bool
    {
        if (!$avatar) {
            return false;
        }

        return !Str::startsWith($avatar, ['http://', 'https://', '//', 'data:image/']);
    }

    private function normalizePublicPath(string $path): string
    {
        $normalized = ltrim($path, '/');

        if (Str::startsWith($normalized, 'storage/')) {
            return Str::after($normalized, 'storage/');
        }

        if (Str::startsWith($normalized, 'public/')) {
            return Str::after($normalized, 'public/');
        }

        return $normalized;
    }

    public function updatePassword(Request $request)
    {
        $admin = $request->user();

        $validated = $request->validateWithBag('passwordUpdate', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $admin->password = (string) $validated['password'];
        $admin->save();

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Password updated successfully.');
    }

    public function updateNavbarPage(Request $request)
    {
        if (!Schema::hasTable('home_page_items')) {
            return redirect()
                ->route('admin.homepage.index')
                ->with('error', 'Please run migrations before editing the navbar page.');
        }

        $validated = $request->validateWithBag('navbarPageUpdate', [
            '_settings_tab' => ['nullable', 'string'],
            'brand.name' => ['required', 'string', 'max:160'],
            'brand.description' => ['required', 'string', 'max:220'],
            'brand.logo' => ['nullable', 'file', 'image', 'max:4096'],
            'brand.remove_logo' => ['nullable', 'boolean'],
            'navbar_links' => ['nullable', 'array', 'max:8'],
            'navbar_links.*.id' => ['nullable', 'integer'],
            'navbar_links.*.label' => ['nullable', 'string', 'max:120'],
            'navbar_links.*.href' => ['nullable', 'string', 'max:180'],
            'navbar_links.*.active' => ['nullable', 'boolean'],
            'navbar_links.*.delete' => ['nullable', 'boolean'],
        ]);

        $this->ensureDefaultHomePageItems();

        $brand = HomePageItem::query()->where('section', 'brand')->where('key', 'main')->firstOrFail();
        $currentLogo = $brand->image_path;

        if ($request->boolean('brand.remove_logo')) {
            $this->deleteStoredHomeImage($currentLogo);
            $brand->image_path = null;
            $currentLogo = null;
        }

        if ($request->hasFile('brand.logo')) {
            $path = $request->file('brand.logo')->store('home-page', 'public');

            if ($path !== false) {
                if ($currentLogo && $currentLogo !== $path) {
                    $this->deleteStoredHomeImage($currentLogo);
                }

                $brand->image_path = $path;
            }
        }

        $brand->fill([
            'type' => 'content',
            'title' => $validated['brand']['name'],
            'description' => $validated['brand']['description'],
            'sort_order' => 1,
            'is_active' => true,
        ])->save();

        $this->syncFooterBrandFromBrand($brand);

        $navbarPageLabels = [
            '#home' => 'Home',
            '#about' => 'About',
            '#programs' => 'Academic Programs',
            '#course' => 'Courses',
            '#faq' => 'FAQ',
            '#contact' => 'Contact',
        ];

        foreach (($validated['navbar_links'] ?? []) as $index => $link) {
            $key = 'link_' . ($index + 1);
            $label = trim((string) ($link['label'] ?? ''));
            $href = trim((string) ($link['href'] ?? ''));
            $href = $href === '#admission' ? '#course' : $href;
            $label = $label !== '' ? $label : ($navbarPageLabels[$href] ?? '');

            if (!empty($link['delete']) || ($label === '' && $href === '')) {
                $this->deleteHomePageItem('navbar_links', $link['id'] ?? null, $key);
                continue;
            }

            $this->updateOrCreateHomePageItem('navbar_links', $link['id'] ?? null, $key, [
                'type' => 'link',
                'title' => $label,
                'value' => $href,
                'sort_order' => $index + 1,
                'is_active' => !empty($link['active']),
            ]);
        }

        return redirect()
            ->route('admin.homepage.index')
            ->with('success', 'Navbar page content updated successfully.')
            ->with('settings_tab', 'navbar-page');
    }

    public function updateHomePage(Request $request)
    {
        if (!Schema::hasTable('home_page_items')) {
            return redirect()
                ->route('admin.homepage.index')
                ->with('error', 'Please run migrations before editing the home page.');
        }

        $validated = $request->validateWithBag('homePageUpdate', [
            '_settings_tab' => ['nullable', 'string'],
            'hero.badge' => ['required', 'string', 'max:160'],
            'hero.title' => ['required', 'string', 'max:220'],
            'hero.description' => ['required', 'string', 'max:700'],
            'hero.image' => ['nullable', 'file', 'image', 'max:4096'],
            'hero.remove_image' => ['nullable', 'boolean'],
            'points' => ['nullable', 'array', 'max:6'],
            'points.*.id' => ['nullable', 'integer'],
            'points.*.description' => ['nullable', 'string', 'max:220'],
            'points.*.icon' => ['nullable', 'string', 'max:4000'],
            'points.*.active' => ['nullable', 'boolean'],
            'points.*.delete' => ['nullable', 'boolean'],
            'stats' => ['nullable', 'array', 'max:6'],
            'stats.*.id' => ['nullable', 'integer'],
            'stats.*.label' => ['nullable', 'string', 'max:120'],
            'stats.*.value' => ['nullable', 'string', 'max:40'],
            'stats.*.trend' => ['nullable', 'string', 'max:80'],
            'stats.*.icon' => ['nullable', 'string', 'max:4000'],
            'stats.*.color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'stats.*.active' => ['nullable', 'boolean'],
            'stats.*.delete' => ['nullable', 'boolean'],
            'features' => ['nullable', 'array', 'max:8'],
            'features.*.id' => ['nullable', 'integer'],
            'features.*.title' => ['nullable', 'string', 'max:140'],
            'features.*.description' => ['nullable', 'string', 'max:260'],
            'features.*.icon' => ['nullable', 'string', 'max:4000'],
            'features.*.color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'features.*.active' => ['nullable', 'boolean'],
            'features.*.delete' => ['nullable', 'boolean'],
        ]);

        $this->ensureDefaultHomePageItems();

        $hero = HomePageItem::query()->where('section', 'hero')->where('key', 'main')->firstOrFail();
        $currentImage = $hero->image_path;

        if ($request->boolean('hero.remove_image')) {
            $this->deleteStoredHomeImage($currentImage);
            $hero->image_path = null;
            $currentImage = null;
        }

        if ($request->hasFile('hero.image')) {
            $path = $request->file('hero.image')->store('home-page', 'public');

            if ($path !== false) {
                if ($currentImage && $currentImage !== $path) {
                    $this->deleteStoredHomeImage($currentImage);
                }

                $hero->image_path = $path;
            }
        }

        $hero->fill([
            'title' => $validated['hero']['title'],
            'subtitle' => $validated['hero']['badge'],
            'description' => $validated['hero']['description'],
        ])->save();

        foreach (($validated['points'] ?? []) as $index => $point) {
            $key = 'point_' . ($index + 1);
            $description = trim((string) ($point['description'] ?? ''));

            if (!empty($point['delete']) || $description === '') {
                $this->deleteHomePageItem('hero_points', $point['id'] ?? null, $key);
                continue;
            }

            $this->updateOrCreateHomePageItem(
                'hero_points',
                $point['id'] ?? null,
                $key,
                [
                    'type' => 'point',
                    'description' => $description,
                    'icon' => $point['icon'] ?? null,
                    'sort_order' => $index + 1,
                    'is_active' => !empty($point['active']),
                ]
            );
        }

        foreach (($validated['stats'] ?? []) as $index => $stat) {
            $key = 'stat_' . ($index + 1);
            $label = trim((string) ($stat['label'] ?? ''));
            $value = trim((string) ($stat['value'] ?? ''));
            $trend = trim((string) ($stat['trend'] ?? ''));

            if (!empty($stat['delete']) || ($label === '' && $value === '' && $trend === '')) {
                $this->deleteHomePageItem('hero_stats', $stat['id'] ?? null, $key);
                continue;
            }

            $this->updateOrCreateHomePageItem(
                'hero_stats',
                $stat['id'] ?? null,
                $key,
                [
                    'type' => 'stat',
                    'title' => $label,
                    'value' => $value,
                    'subtitle' => $trend,
                    'icon' => $stat['icon'] ?? null,
                    'color' => $stat['color'] ?? null,
                    'sort_order' => $index + 1,
                    'is_active' => !empty($stat['active']),
                ]
            );
        }

        foreach (($validated['features'] ?? []) as $index => $feature) {
            $key = 'feature_' . ($index + 1);
            $title = trim((string) ($feature['title'] ?? ''));
            $description = trim((string) ($feature['description'] ?? ''));

            if (!empty($feature['delete']) || ($title === '' && $description === '')) {
                $this->deleteHomePageItem('hero_features', $feature['id'] ?? null, $key);
                continue;
            }

            $this->updateOrCreateHomePageItem(
                'hero_features',
                $feature['id'] ?? null,
                $key,
                [
                    'type' => 'feature',
                    'title' => $title,
                    'description' => $description,
                    'icon' => $feature['icon'] ?? null,
                    'color' => $feature['color'] ?? null,
                    'sort_order' => $index + 1,
                    'is_active' => !empty($feature['active']),
                ]
            );
        }

        return redirect()
            ->route('admin.homepage.index')
            ->with('success', 'Hero page content updated successfully.')
            ->with('settings_tab', 'home-page');
    }

    public function updateAboutPage(Request $request)
    {
        if (!Schema::hasTable('home_page_items')) {
            return redirect()
                ->route('admin.homepage.index')
                ->with('error', 'Please run migrations before editing the about page.');
        }

        $validated = $request->validateWithBag('aboutPageUpdate', [
            '_settings_tab' => ['nullable', 'string'],
            'about.badge' => ['required', 'string', 'max:160'],
            'about.title' => ['required', 'string', 'max:240'],
            'about.description' => ['required', 'string', 'max:900'],
            'about.image' => ['nullable', 'file', 'image', 'max:4096'],
            'about.remove_image' => ['nullable', 'boolean'],
            'about_highlights' => ['nullable', 'array', 'max:4'],
            'about_highlights.*.id' => ['nullable', 'integer'],
            'about_highlights.*.title' => ['nullable', 'string', 'max:120'],
            'about_highlights.*.description' => ['nullable', 'string', 'max:300'],
            'about_highlights.*.icon' => ['nullable', 'string', 'max:4000'],
            'about_highlights.*.color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'about_highlights.*.active' => ['nullable', 'boolean'],
            'about_highlights.*.delete' => ['nullable', 'boolean'],
            'about_cards' => ['nullable', 'array', 'max:8'],
            'about_cards.*.id' => ['nullable', 'integer'],
            'about_cards.*.title' => ['nullable', 'string', 'max:120'],
            'about_cards.*.description' => ['nullable', 'string', 'max:320'],
            'about_cards.*.icon' => ['nullable', 'string', 'max:4000'],
            'about_cards.*.color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'about_cards.*.active' => ['nullable', 'boolean'],
            'about_cards.*.delete' => ['nullable', 'boolean'],
        ]);

        $this->ensureDefaultHomePageItems();

        $about = HomePageItem::query()->where('section', 'about')->where('key', 'main')->firstOrFail();
        $currentImage = $about->image_path;

        if ($request->boolean('about.remove_image')) {
            $this->deleteStoredHomeImage($currentImage);
            $about->image_path = null;
            $currentImage = null;
        }

        if ($request->hasFile('about.image')) {
            $path = $request->file('about.image')->store('home-page', 'public');

            if ($path !== false) {
                if ($currentImage && $currentImage !== $path) {
                    $this->deleteStoredHomeImage($currentImage);
                }

                $about->image_path = $path;
            }
        }

        $about->fill([
            'title' => $validated['about']['title'],
            'subtitle' => $validated['about']['badge'],
            'description' => $validated['about']['description'],
        ])->save();

        foreach (($validated['about_highlights'] ?? []) as $index => $highlight) {
            $key = 'highlight_' . ($index + 1);
            $title = trim((string) ($highlight['title'] ?? ''));
            $description = trim((string) ($highlight['description'] ?? ''));

            if (!empty($highlight['delete']) || ($title === '' && $description === '')) {
                $this->deleteHomePageItem('about_highlights', $highlight['id'] ?? null, $key);
                continue;
            }

            $this->updateOrCreateHomePageItem(
                'about_highlights',
                $highlight['id'] ?? null,
                $key,
                [
                    'type' => 'highlight',
                    'title' => $title,
                    'description' => $description,
                    'icon' => $highlight['icon'] ?? null,
                    'color' => $highlight['color'] ?? null,
                    'sort_order' => $index + 1,
                    'is_active' => !empty($highlight['active']),
                ]
            );
        }

        foreach (($validated['about_cards'] ?? []) as $index => $card) {
            $key = 'card_' . ($index + 1);
            $title = trim((string) ($card['title'] ?? ''));
            $description = trim((string) ($card['description'] ?? ''));

            if (!empty($card['delete']) || ($title === '' && $description === '')) {
                $this->deleteHomePageItem('about_cards', $card['id'] ?? null, $key);
                continue;
            }

            $this->updateOrCreateHomePageItem(
                'about_cards',
                $card['id'] ?? null,
                $key,
                [
                    'type' => 'card',
                    'title' => $title,
                    'description' => $description,
                    'icon' => $card['icon'] ?? null,
                    'color' => $card['color'] ?? null,
                    'sort_order' => $index + 1,
                    'is_active' => !empty($card['active']),
                ]
            );
        }

        return redirect()
            ->route('admin.homepage.index')
            ->with('success', 'About page content updated successfully.')
            ->with('settings_tab', 'about-page');
    }

    public function updateProgramPage(Request $request)
    {
        if (!Schema::hasTable('home_page_items')) {
            return redirect()
                ->route('admin.homepage.index')
                ->with('error', 'Please run migrations before editing the program page.');
        }

        $validated = $request->validateWithBag('programPageUpdate', [
            '_settings_tab' => ['nullable', 'string'],
            'program.badge' => ['required', 'string', 'max:160'],
            'program.title' => ['required', 'string', 'max:240'],
            'program.description' => ['required', 'string', 'max:900'],
            'program_cards' => ['nullable', 'array', 'max:8'],
            'program_cards.*.id' => ['nullable', 'integer'],
            'program_cards.*.level' => ['nullable', 'string', 'max:120'],
            'program_cards.*.title' => ['nullable', 'string', 'max:140'],
            'program_cards.*.description' => ['nullable', 'string', 'max:320'],
            'program_cards.*.icon' => ['nullable', 'string', 'max:4000'],
            'program_cards.*.color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'program_cards.*.active' => ['nullable', 'boolean'],
            'program_cards.*.delete' => ['nullable', 'boolean'],
        ]);

        $this->ensureDefaultHomePageItems();

        HomePageItem::query()->updateOrCreate(
            ['section' => 'programs', 'key' => 'main'],
            [
                'type' => 'content',
                'title' => $validated['program']['title'],
                'subtitle' => $validated['program']['badge'],
                'description' => $validated['program']['description'],
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        foreach (($validated['program_cards'] ?? []) as $index => $card) {
            $key = 'card_' . ($index + 1);
            $level = trim((string) ($card['level'] ?? ''));
            $title = trim((string) ($card['title'] ?? ''));
            $description = trim((string) ($card['description'] ?? ''));

            if (!empty($card['delete']) || ($level === '' && $title === '' && $description === '')) {
                $this->deleteHomePageItem('program_cards', $card['id'] ?? null, $key);
                continue;
            }

            $this->updateOrCreateHomePageItem(
                'program_cards',
                $card['id'] ?? null,
                $key,
                [
                    'type' => 'card',
                    'title' => $title,
                    'subtitle' => $level,
                    'description' => $description,
                    'icon' => $card['icon'] ?? null,
                    'color' => $card['color'] ?? null,
                    'sort_order' => $index + 1,
                    'is_active' => !empty($card['active']),
                ]
            );
        }

        return redirect()
            ->route('admin.homepage.index')
            ->with('success', 'Program page content updated successfully.')
            ->with('settings_tab', 'program-page');
    }

    public function updateCoursePage(Request $request)
    {
        if (!Schema::hasTable('home_page_items')) {
            return redirect()->route('admin.homepage.index')->with('error', 'Please run migrations before editing the course page.');
        }

        $validated = $request->validateWithBag('coursePageUpdate', [
            '_settings_tab' => ['nullable', 'string'],
            'course.badge' => ['required', 'string', 'max:160'],
            'course.title' => ['required', 'string', 'max:240'],
            'course.description' => ['required', 'string', 'max:900'],
            'featured.label' => ['required', 'string', 'max:160'],
            'featured.title' => ['required', 'string', 'max:180'],
            'featured.description' => ['required', 'string', 'max:500'],
            'course_cards' => ['nullable', 'array', 'max:8'],
            'course_cards.*.id' => ['nullable', 'integer'],
            'course_cards.*.category' => ['nullable', 'string', 'max:120'],
            'course_cards.*.title' => ['nullable', 'string', 'max:140'],
            'course_cards.*.description' => ['nullable', 'string', 'max:320'],
            'course_cards.*.image' => ['nullable', 'file', 'image', 'max:4096'],
            'course_cards.*.remove_image' => ['nullable', 'boolean'],
            'course_cards.*.instructor_name' => ['nullable', 'string', 'max:120'],
            'course_cards.*.instructor_role' => ['nullable', 'string', 'max:160'],
            'course_cards.*.lessons' => ['nullable', 'string', 'max:60'],
            'course_cards.*.duration' => ['nullable', 'string', 'max:60'],
            'course_cards.*.rating' => ['nullable', 'string', 'max:20'],
            'course_cards.*.review_count' => ['nullable', 'string', 'max:40'],
            'course_cards.*.price' => ['nullable', 'string', 'max:40'],
            'course_cards.*.button_label' => ['nullable', 'string', 'max:80'],
            'course_cards.*.color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'course_cards.*.active' => ['nullable', 'boolean'],
            'course_cards.*.delete' => ['nullable', 'boolean'],
        ]);

        $this->ensureDefaultHomePageItems();

        HomePageItem::query()->updateOrCreate(
            ['section' => 'course', 'key' => 'main'],
            [
                'type' => 'content',
                'title' => $validated['course']['title'],
                'subtitle' => $validated['course']['badge'],
                'description' => $validated['course']['description'],
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        HomePageItem::query()->updateOrCreate(
            ['section' => 'course_featured', 'key' => 'main'],
            [
                'type' => 'content',
                'title' => $validated['featured']['title'],
                'subtitle' => $validated['featured']['label'],
                'description' => $validated['featured']['description'],
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        foreach (($validated['course_cards'] ?? []) as $index => $step) {
            $key = 'card_' . ($index + 1);
            $category = trim((string) ($step['category'] ?? ''));
            $title = trim((string) ($step['title'] ?? ''));
            $description = trim((string) ($step['description'] ?? ''));
            $existingStep = $this->findHomePageItem('course_cards', $step['id'] ?? null, $key)
                ?: $this->findHomePageItem('admission_steps', $step['id'] ?? null, 'step_' . ($index + 1));

            if (!empty($step['delete']) || ($title === '' && $description === '')) {
                $this->deleteStoredHomeImage($existingStep?->image_path);
                $this->deleteHomePageItem('course_cards', $step['id'] ?? null, $key);
                $this->deleteHomePageItem('admission_steps', $step['id'] ?? null, 'step_' . ($index + 1));
                continue;
            }

            $imagePath = $existingStep?->image_path;

            if (!empty($step['remove_image'])) {
                $this->deleteStoredHomeImage($imagePath);
                $imagePath = null;
            }

            if ($request->hasFile("course_cards.{$index}.image")) {
                $path = $request->file("course_cards.{$index}.image")->store('home-page/courses', 'public');

                if ($path !== false) {
                    if ($imagePath && $imagePath !== $path) {
                        $this->deleteStoredHomeImage($imagePath);
                    }

                    $imagePath = $path;
                }
            }

            $this->updateOrCreateHomePageItem('course_cards', $step['id'] ?? null, $key, [
                'type' => 'course',
                'title' => $title,
                'subtitle' => $category,
                'description' => $description,
                'color' => $step['color'] ?? null,
                'image_path' => $imagePath,
                'meta' => [
                    'category' => $category,
                    'instructor_name' => trim((string) ($step['instructor_name'] ?? '')),
                    'instructor_role' => trim((string) ($step['instructor_role'] ?? '')),
                    'lessons' => trim((string) ($step['lessons'] ?? '')),
                    'duration' => trim((string) ($step['duration'] ?? '')),
                    'rating' => trim((string) ($step['rating'] ?? '')),
                    'review_count' => trim((string) ($step['review_count'] ?? '')),
                    'price' => trim((string) ($step['price'] ?? '')),
                    'button_label' => trim((string) ($step['button_label'] ?? '')),
                ],
                'sort_order' => $index + 1,
                'is_active' => !empty($step['active']),
            ]);
        }

        return redirect()->route('admin.homepage.index')->with('success', 'Course page content updated successfully.')->with('settings_tab', 'course-page');
    }

    public function updateFaqPage(Request $request)
    {
        if (!Schema::hasTable('home_page_items')) {
            return redirect()->route('admin.homepage.index')->with('error', 'Please run migrations before editing the FAQ page.');
        }

        $validated = $request->validateWithBag('faqPageUpdate', [
            '_settings_tab' => ['nullable', 'string'],
            'faq.badge' => ['required', 'string', 'max:160'],
            'faq.title' => ['required', 'string', 'max:240'],
            'faq_help.label' => ['required', 'string', 'max:160'],
            'faq_help.title' => ['required', 'string', 'max:180'],
            'faq_help.description' => ['required', 'string', 'max:500'],
            'faq_items' => ['nullable', 'array', 'max:10'],
            'faq_items.*.id' => ['nullable', 'integer'],
            'faq_items.*.question' => ['nullable', 'string', 'max:220'],
            'faq_items.*.answer' => ['nullable', 'string', 'max:700'],
            'faq_items.*.active' => ['nullable', 'boolean'],
            'faq_items.*.delete' => ['nullable', 'boolean'],
        ]);

        $this->ensureDefaultHomePageItems();

        HomePageItem::query()->updateOrCreate(
            ['section' => 'faq', 'key' => 'main'],
            [
                'type' => 'content',
                'title' => $validated['faq']['title'],
                'subtitle' => $validated['faq']['badge'],
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        HomePageItem::query()->updateOrCreate(
            ['section' => 'faq_help', 'key' => 'main'],
            [
                'type' => 'content',
                'title' => $validated['faq_help']['title'],
                'subtitle' => $validated['faq_help']['label'],
                'description' => $validated['faq_help']['description'],
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        foreach (($validated['faq_items'] ?? []) as $index => $faq) {
            $key = 'item_' . ($index + 1);
            $question = trim((string) ($faq['question'] ?? ''));
            $answer = trim((string) ($faq['answer'] ?? ''));

            if (!empty($faq['delete']) || ($question === '' && $answer === '')) {
                $this->deleteHomePageItem('faq_items', $faq['id'] ?? null, $key);
                continue;
            }

            $this->updateOrCreateHomePageItem('faq_items', $faq['id'] ?? null, $key, [
                'type' => 'question',
                'title' => $question,
                'description' => $answer,
                'sort_order' => $index + 1,
                'is_active' => !empty($faq['active']),
            ]);
        }

        return redirect()->route('admin.homepage.index')->with('success', 'FAQ page content updated successfully.')->with('settings_tab', 'faq-page');
    }

    public function updateContactPage(Request $request)
    {
        if (!Schema::hasTable('home_page_items')) {
            return redirect()->route('admin.homepage.index')->with('error', 'Please run migrations before editing the contact page.');
        }

        $validated = $request->validateWithBag('contactPageUpdate', [
            '_settings_tab' => ['nullable', 'string'],
            'contact.badge' => ['required', 'string', 'max:160'],
            'contact.title' => ['required', 'string', 'max:240'],
            'contact.description' => ['required', 'string', 'max:900'],
            'campus.label' => ['required', 'string', 'max:160'],
            'campus.title' => ['required', 'string', 'max:180'],
            'campus.description' => ['required', 'string', 'max:500'],
            'contact_cards' => ['nullable', 'array', 'max:8'],
            'contact_cards.*.id' => ['nullable', 'integer'],
            'contact_cards.*.label' => ['nullable', 'string', 'max:120'],
            'contact_cards.*.value' => ['nullable', 'string', 'max:220'],
            'contact_cards.*.active' => ['nullable', 'boolean'],
            'contact_cards.*.delete' => ['nullable', 'boolean'],
        ]);

        $this->ensureDefaultHomePageItems();

        HomePageItem::query()->updateOrCreate(
            ['section' => 'contact', 'key' => 'main'],
            [
                'type' => 'content',
                'title' => $validated['contact']['title'],
                'subtitle' => $validated['contact']['badge'],
                'description' => $validated['contact']['description'],
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        HomePageItem::query()->updateOrCreate(
            ['section' => 'contact_campus', 'key' => 'main'],
            [
                'type' => 'content',
                'title' => $validated['campus']['title'],
                'subtitle' => $validated['campus']['label'],
                'description' => $validated['campus']['description'],
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        foreach (($validated['contact_cards'] ?? []) as $index => $card) {
            $key = 'card_' . ($index + 1);
            $label = trim((string) ($card['label'] ?? ''));
            $value = trim((string) ($card['value'] ?? ''));

            if (!empty($card['delete']) || ($label === '' && $value === '')) {
                $this->deleteHomePageItem('contact_cards', $card['id'] ?? null, $key);
                continue;
            }

            $this->updateOrCreateHomePageItem('contact_cards', $card['id'] ?? null, $key, [
                'type' => 'card',
                'title' => $label,
                'value' => $value,
                'sort_order' => $index + 1,
                'is_active' => !empty($card['active']),
            ]);
        }

        return redirect()->route('admin.homepage.index')->with('success', 'Contact page content updated successfully.')->with('settings_tab', 'contact-page');
    }

    public function updateFooterPage(Request $request)
    {
        if (!Schema::hasTable('home_page_items')) {
            return redirect()->route('admin.homepage.index')->with('error', 'Please run migrations before editing the footer page.');
        }

        $validated = $request->validateWithBag('footerPageUpdate', [
            '_settings_tab' => ['nullable', 'string'],
            'footer.tagline' => ['required', 'string', 'max:180'],
            'footer.description' => ['required', 'string', 'max:700'],
            'footer.explore' => ['required', 'string', 'max:80'],
            'footer.contact' => ['required', 'string', 'max:80'],
            'footer.copyright' => ['required', 'string', 'max:160'],
            'footer.logo' => ['nullable', 'file', 'image', 'max:4096'],
            'footer.remove_logo' => ['nullable', 'boolean'],
            'footer_links' => ['nullable', 'array', 'max:8'],
            'footer_links.*.id' => ['nullable', 'integer'],
            'footer_links.*.label' => ['nullable', 'string', 'max:120'],
            'footer_links.*.href' => ['nullable', 'string', 'max:180'],
            'footer_links.*.active' => ['nullable', 'boolean'],
            'footer_links.*.delete' => ['nullable', 'boolean'],
            'footer_contacts' => ['nullable', 'array', 'max:8'],
            'footer_contacts.*.id' => ['nullable', 'integer'],
            'footer_contacts.*.label' => ['nullable', 'string', 'max:120'],
            'footer_contacts.*.value' => ['nullable', 'string', 'max:220'],
            'footer_contacts.*.active' => ['nullable', 'boolean'],
            'footer_contacts.*.delete' => ['nullable', 'boolean'],
        ]);

        $this->ensureDefaultHomePageItems();

        $footer = HomePageItem::query()->where('section', 'footer')->where('key', 'main')->firstOrFail();
        $brand = HomePageItem::query()->where('section', 'brand')->where('key', 'main')->first();
        $currentLogo = $footer->image_path;

        if ($request->boolean('footer.remove_logo')) {
            $this->deleteStoredHomeImage($currentLogo);
            $footer->image_path = null;
            $currentLogo = null;
        }

        if ($request->hasFile('footer.logo')) {
            $path = $request->file('footer.logo')->store('home-page', 'public');

            if ($path !== false) {
                if ($currentLogo && $currentLogo !== $path) {
                    $this->deleteStoredHomeImage($currentLogo);
                }

                $footer->image_path = $path;
            }
        }

        $footer->fill([
            'type' => 'content',
            'title' => $brand?->description ?: $validated['footer']['tagline'],
            'description' => $validated['footer']['description'],
            'subtitle' => $validated['footer']['explore'],
            'value' => $validated['footer']['contact'],
            'meta' => ['copyright' => $validated['footer']['copyright']],
            'image_path' => $brand?->image_path,
            'sort_order' => 1,
            'is_active' => true,
        ])->save();

        foreach (($validated['footer_links'] ?? []) as $index => $link) {
            $key = 'link_' . ($index + 1);
            $label = trim((string) ($link['label'] ?? ''));
            $href = trim((string) ($link['href'] ?? ''));

            if (!empty($link['delete']) || ($label === '' && $href === '')) {
                $this->deleteHomePageItem('footer_links', $link['id'] ?? null, $key);
                continue;
            }

            $this->updateOrCreateHomePageItem('footer_links', $link['id'] ?? null, $key, [
                'type' => 'link',
                'title' => $label,
                'value' => $href,
                'sort_order' => $index + 1,
                'is_active' => !empty($link['active']),
            ]);
        }

        foreach (($validated['footer_contacts'] ?? []) as $index => $contact) {
            $key = 'contact_' . ($index + 1);
            $label = trim((string) ($contact['label'] ?? ''));
            $value = trim((string) ($contact['value'] ?? ''));

            if (!empty($contact['delete']) || ($label === '' && $value === '')) {
                $this->deleteHomePageItem('footer_contacts', $contact['id'] ?? null, $key);
                continue;
            }

            $this->updateOrCreateHomePageItem('footer_contacts', $contact['id'] ?? null, $key, [
                'type' => 'contact',
                'title' => $label,
                'value' => $value,
                'sort_order' => $index + 1,
                'is_active' => !empty($contact['active']),
            ]);
        }

        return redirect()->route('admin.homepage.index')->with('success', 'Footer page content updated successfully.')->with('settings_tab', 'footer-page');
    }

    private function syncFooterBrandFromBrand(HomePageItem $brand): void
    {
        $footer = HomePageItem::query()
            ->where('section', 'footer')
            ->where('key', 'main')
            ->first();

        if (!$footer) {
            return;
        }

        $footer->fill([
            'title' => $brand->description,
            'image_path' => $brand->image_path,
        ])->save();
    }

    private function ensureDefaultHomePageItems(): void
    {
        $hasExistingItems = HomePageItem::query()->exists();
        $featureItems = $this->homeContentArray('features.items');
        $programItems = $this->homeContentArray('programs.items');
        $courseItems = $this->homeContentArray('course.items') ?: $this->homeContentArray('admission.steps');
        $faqItems = $this->homeContentArray('faq.items');
        $contactCards = $this->homeContentArray('contact.cards');
        $footerLinks = $this->homeContentArray('footer_links');
        $navbarLinks = $this->homeContentArray('links');
        $defaults = [
            ['brand', 'main', 'content', 'TechBridge', null, \App\Support\HomePageContent::text('brand.tagline'), null, 1],
            ['hero', 'main', 'content', \App\Support\HomePageContent::text('hero.title'), \App\Support\HomePageContent::text('hero.badge'), \App\Support\HomePageContent::text('hero.description', ['schoolName' => 'TechBridge']), null, 1],
            ['hero_points', 'point_1', 'point', null, null, 'One connected platform for academics, attendance, and communication.', null, 1],
            ['hero_points', 'point_2', 'point', null, null, 'Dedicated dashboards for school leaders, teachers, students, and parents.', null, 2],
            ['hero_points', 'point_3', 'point', null, null, 'A more efficient admissions and operations process with reduced paperwork and better visibility.', null, 3],
            ['hero_stats', 'stat_1', 'stat', 'Enrolled Students', '+6% this year', null, 'students_total', 1],
            ['hero_stats', 'stat_2', 'stat', 'Qualified Teachers', '+8% this year', null, 'teachers_total', 2],
            ['hero_stats', 'stat_3', 'stat', 'Portal Access', '+10% this year', null, '24/7', 3],
            ['hero_features', 'feature_1', 'feature', 'Student and Staff Management', null, 'Manage student records, class assignments, subjects, and staff roles in one secure and organized system.', null, 1],
            ['hero_features', 'feature_2', 'feature', 'Attendance and Reporting', null, 'Record daily attendance with accurate history and reporting tools ready for review or export.', null, 2],
            ['hero_features', 'feature_3', 'feature', 'Grades and Academic Progress', null, 'Capture assessment results, monitor student progress, and generate clear academic summaries.', null, 3],
            ['hero_features', 'feature_4', 'feature', 'Announcements and Communication', null, 'Share important notices and school updates quickly with students, teachers, and parents.', null, 4],
            ['about', 'main', 'content', \App\Support\HomePageContent::text('about.title'), \App\Support\HomePageContent::text('about.badge', ['schoolName' => 'TechBridge']), \App\Support\HomePageContent::text('about.description'), null, 1],
            ['about_highlights', 'highlight_1', 'highlight', 'Leadership', null, 'We prepare students to lead with confidence, character, knowledge, and purpose.', null, 1],
            ['about_highlights', 'highlight_2', 'highlight', 'Transparency', null, 'Progress, updates, and next steps remain visible and easy to understand across the school community.', null, 2],
            ['about_cards', 'card_1', 'card', 'Mission', null, 'To empower every learner through quality education, discipline, confidence, and care.', 'bg-cyan-100 text-cyan-700', 1],
            ['about_cards', 'card_2', 'card', 'Vision', null, 'To build a modern learning environment where technology and personal guidance work hand in hand.', 'bg-indigo-100 text-indigo-700', 2],
            ['about_cards', 'card_3', 'card', 'Culture', null, 'Respect, curiosity, responsibility, and integrity shape both learning and leadership.', 'bg-amber-100 text-amber-700', 3],
            ['about_cards', 'card_4', 'card', 'Operations', null, 'Well-structured systems help families and staff receive timely support and clear information.', 'bg-emerald-100 text-emerald-700', 4],
            ['platform_features', 'main', 'content', \App\Support\HomePageContent::text('features.title'), \App\Support\HomePageContent::text('features.badge'), null, \App\Support\HomePageContent::text('features.tag'), 1],
            ['programs', 'main', 'content', \App\Support\HomePageContent::text('programs.title'), \App\Support\HomePageContent::text('programs.badge'), \App\Support\HomePageContent::text('programs.description'), null, 1],
            ['course', 'main', 'content', \App\Support\HomePageContent::text('course.title'), \App\Support\HomePageContent::text('course.badge'), \App\Support\HomePageContent::text('course.description'), null, 1],
            ['course_featured', 'main', 'content', \App\Support\HomePageContent::text('course.featured_title'), \App\Support\HomePageContent::text('course.featured_label'), \App\Support\HomePageContent::text('course.featured_description'), null, 1],
            ['faq', 'main', 'content', \App\Support\HomePageContent::text('faq.title'), \App\Support\HomePageContent::text('faq.badge'), null, null, 1],
            ['faq_help', 'main', 'content', \App\Support\HomePageContent::text('faq.more_help_title'), \App\Support\HomePageContent::text('faq.more_help_label'), \App\Support\HomePageContent::text('faq.more_help_text'), null, 1],
            ['contact', 'main', 'content', \App\Support\HomePageContent::text('contact.title'), \App\Support\HomePageContent::text('contact.badge'), \App\Support\HomePageContent::text('contact.description'), null, 1],
            ['contact_campus', 'main', 'content', \App\Support\HomePageContent::text('contact.campus_title'), \App\Support\HomePageContent::text('contact.campus_label'), \App\Support\HomePageContent::text('contact.campus_text'), null, 1],
            ['footer', 'main', 'content', \App\Support\HomePageContent::text('footer.tagline'), \App\Support\HomePageContent::text('footer.explore'), \App\Support\HomePageContent::text('footer.description'), \App\Support\HomePageContent::text('footer.contact'), 1],
        ];

        foreach ($featureItems as $index => $item) {
            $defaults[] = [
                'platform_feature_cards',
                'card_' . ($index + 1),
                'card',
                $item['title'] ?? '',
                null,
                $item['description'] ?? '',
                null,
                $index + 1,
            ];
        }

        foreach ($navbarLinks as $index => $item) {
            $defaults[] = [
                'navbar_links',
                'link_' . ($index + 1),
                'link',
                $item['label'] ?? '',
                null,
                null,
                $item['href'] ?? '',
                $index + 1,
            ];
        }

        foreach ($programItems as $index => $item) {
            $defaults[] = [
                'program_cards',
                'card_' . ($index + 1),
                'card',
                $item['title'] ?? '',
                $item['level'] ?? '',
                $item['description'] ?? '',
                null,
                $index + 1,
            ];
        }

        foreach ($courseItems as $index => $item) {
            $defaults[] = [
                'course_cards',
                'card_' . ($index + 1),
                'course',
                $item['title'] ?? '',
                $item['category'] ?? null,
                $item['description'] ?? '',
                null,
                $index + 1,
            ];
        }

        foreach ($faqItems as $index => $item) {
            $defaults[] = [
                'faq_items',
                'item_' . ($index + 1),
                'question',
                $item['question'] ?? '',
                null,
                $item['answer'] ?? '',
                null,
                $index + 1,
            ];
        }

        foreach ($contactCards as $index => $item) {
            $defaults[] = [
                'contact_cards',
                'card_' . ($index + 1),
                'card',
                $item['label'] ?? '',
                null,
                null,
                $item['value'] ?? '',
                $index + 1,
            ];
        }

        foreach ($footerLinks as $index => $item) {
            $defaults[] = [
                'footer_links',
                'link_' . ($index + 1),
                'link',
                $item['label'] ?? '',
                null,
                null,
                $item['href'] ?? '',
                $index + 1,
            ];
        }

        foreach ($contactCards as $index => $item) {
            $defaults[] = [
                'footer_contacts',
                'contact_' . ($index + 1),
                'contact',
                $item['label'] ?? '',
                null,
                null,
                $item['value'] ?? '',
                $index + 1,
            ];
        }

        foreach ($defaults as [$section, $key, $type, $title, $subtitle, $description, $value, $sortOrder]) {
            if ($hasExistingItems && $key !== 'main' && HomePageItem::query()->where('section', $section)->exists()) {
                continue;
            }

            $attributes = [
                'type' => $type,
                'title' => $title,
                'subtitle' => $subtitle,
                'description' => $description,
                'sort_order' => $sortOrder,
                'is_active' => true,
            ];

            if ($section === 'about_cards') {
                $attributes['color'] = $value;
            } elseif ($section === 'footer' && $key === 'main') {
                $attributes['value'] = $value;
                $attributes['meta'] = ['copyright' => \App\Support\HomePageContent::text('footer.copyright')];
            } else {
                $attributes['value'] = $value;
            }

            HomePageItem::query()->firstOrCreate(
                ['section' => $section, 'key' => $key],
                $attributes
            );
        }
    }

    private function homeContentArray(string $key): array
    {
        $value = \App\Support\HomePageContent::get($key, []);

        return is_array($value) ? $value : [];
    }

    private function deleteHomePageItem(string $section, mixed $id, string $fallbackKey): void
    {
        $query = HomePageItem::query()->where('section', $section);

        if ($id) {
            $query->whereKey((int) $id);
        } else {
            $query->where('key', $fallbackKey);
        }

        $query->delete();
    }

    private function findHomePageItem(string $section, mixed $id, string $fallbackKey): ?HomePageItem
    {
        $query = HomePageItem::query()->where('section', $section);

        if ($id) {
            return $query->whereKey((int) $id)->first();
        }

        return $query->where('key', $fallbackKey)->first();
    }

    private function updateOrCreateHomePageItem(string $section, mixed $id, string $fallbackKey, array $attributes): void
    {
        $item = null;

        if ($id) {
            $item = HomePageItem::query()
                ->where('section', $section)
                ->whereKey((int) $id)
                ->first();
        }

        if ($item) {
            $item->fill($attributes)->save();
            return;
        }

        HomePageItem::query()->updateOrCreate(
            ['section' => $section, 'key' => $fallbackKey],
            $attributes
        );
    }

    private function deleteStoredHomeImage(?string $path): void
    {
        if (!$path || Str::startsWith($path, ['http://', 'https://', '//', 'data:image/'])) {
            return;
        }

        $normalized = $this->normalizePublicPath($path);

        if ($normalized !== '' && Storage::disk('public')->exists($normalized)) {
            Storage::disk('public')->delete($normalized);
        }
    }
}
