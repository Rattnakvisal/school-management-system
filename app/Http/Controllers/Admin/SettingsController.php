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
            ->route('admin.settings')
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
            ->route('admin.settings')
            ->with('success', 'Password updated successfully.');
    }

    public function updateHomePage(Request $request)
    {
        if (!Schema::hasTable('home_page_items')) {
            return redirect()
                ->route('admin.settings')
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
            'stats.*.color' => ['nullable', 'string', 'max:40'],
            'stats.*.active' => ['nullable', 'boolean'],
            'stats.*.delete' => ['nullable', 'boolean'],
            'features' => ['nullable', 'array', 'max:8'],
            'features.*.id' => ['nullable', 'integer'],
            'features.*.title' => ['nullable', 'string', 'max:140'],
            'features.*.description' => ['nullable', 'string', 'max:260'],
            'features.*.icon' => ['nullable', 'string', 'max:4000'],
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
                    'sort_order' => $index + 1,
                    'is_active' => !empty($feature['active']),
                ]
            );
        }

        return redirect()
            ->route('admin.settings')
            ->with('success', 'Hero page content updated successfully.')
            ->with('settings_tab', 'home-page');
    }

    public function updateAboutPage(Request $request)
    {
        if (!Schema::hasTable('home_page_items')) {
            return redirect()
                ->route('admin.settings')
                ->with('error', 'Please run migrations before editing the about page.');
        }

        $validated = $request->validateWithBag('aboutPageUpdate', [
            '_settings_tab' => ['nullable', 'string'],
            'about.badge' => ['required', 'string', 'max:160'],
            'about.title' => ['required', 'string', 'max:240'],
            'about.description' => ['required', 'string', 'max:900'],
            'about_highlights' => ['nullable', 'array', 'max:4'],
            'about_highlights.*.id' => ['nullable', 'integer'],
            'about_highlights.*.title' => ['nullable', 'string', 'max:120'],
            'about_highlights.*.description' => ['nullable', 'string', 'max:300'],
            'about_highlights.*.icon' => ['nullable', 'string', 'max:4000'],
            'about_highlights.*.active' => ['nullable', 'boolean'],
            'about_highlights.*.delete' => ['nullable', 'boolean'],
            'about_cards' => ['nullable', 'array', 'max:8'],
            'about_cards.*.id' => ['nullable', 'integer'],
            'about_cards.*.title' => ['nullable', 'string', 'max:120'],
            'about_cards.*.description' => ['nullable', 'string', 'max:320'],
            'about_cards.*.icon' => ['nullable', 'string', 'max:4000'],
            'about_cards.*.active' => ['nullable', 'boolean'],
            'about_cards.*.delete' => ['nullable', 'boolean'],
        ]);

        $this->ensureDefaultHomePageItems();

        $about = HomePageItem::query()->where('section', 'about')->where('key', 'main')->firstOrFail();
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
                    'sort_order' => $index + 1,
                    'is_active' => !empty($highlight['active']),
                ]
            );
        }

        $defaultAboutCardTones = [
            'bg-cyan-100 text-cyan-700',
            'bg-indigo-100 text-indigo-700',
            'bg-amber-100 text-amber-700',
            'bg-emerald-100 text-emerald-700',
        ];

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
                    'color' => $defaultAboutCardTones[$index] ?? $defaultAboutCardTones[0],
                    'sort_order' => $index + 1,
                    'is_active' => !empty($card['active']),
                ]
            );
        }

        return redirect()
            ->route('admin.settings')
            ->with('success', 'About page content updated successfully.')
            ->with('settings_tab', 'about-page');
    }

    public function updateFeaturePage(Request $request)
    {
        if (!Schema::hasTable('home_page_items')) {
            return redirect()
                ->route('admin.settings')
                ->with('error', 'Please run migrations before editing the feature page.');
        }

        $validated = $request->validateWithBag('featurePageUpdate', [
            '_settings_tab' => ['nullable', 'string'],
            'feature.badge' => ['required', 'string', 'max:160'],
            'feature.title' => ['required', 'string', 'max:240'],
            'feature.tag' => ['required', 'string', 'max:160'],
            'feature_cards' => ['nullable', 'array', 'max:8'],
            'feature_cards.*.id' => ['nullable', 'integer'],
            'feature_cards.*.title' => ['nullable', 'string', 'max:140'],
            'feature_cards.*.description' => ['nullable', 'string', 'max:320'],
            'feature_cards.*.icon' => ['nullable', 'string', 'max:4000'],
            'feature_cards.*.active' => ['nullable', 'boolean'],
            'feature_cards.*.delete' => ['nullable', 'boolean'],
        ]);

        $this->ensureDefaultHomePageItems();

        HomePageItem::query()->updateOrCreate(
            ['section' => 'platform_features', 'key' => 'main'],
            [
                'type' => 'content',
                'title' => $validated['feature']['title'],
                'subtitle' => $validated['feature']['badge'],
                'value' => $validated['feature']['tag'],
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        foreach (($validated['feature_cards'] ?? []) as $index => $card) {
            $key = 'card_' . ($index + 1);
            $title = trim((string) ($card['title'] ?? ''));
            $description = trim((string) ($card['description'] ?? ''));

            if (!empty($card['delete']) || ($title === '' && $description === '')) {
                $this->deleteHomePageItem('platform_feature_cards', $card['id'] ?? null, $key);
                continue;
            }

            $this->updateOrCreateHomePageItem(
                'platform_feature_cards',
                $card['id'] ?? null,
                $key,
                [
                    'type' => 'card',
                    'title' => $title,
                    'description' => $description,
                    'icon' => $card['icon'] ?? null,
                    'sort_order' => $index + 1,
                    'is_active' => !empty($card['active']),
                ]
            );
        }

        return redirect()
            ->route('admin.settings')
            ->with('success', 'Feature page content updated successfully.')
            ->with('settings_tab', 'feature-page');
    }

    private function ensureDefaultHomePageItems(): void
    {
        $hasExistingItems = HomePageItem::query()->exists();
        $featureItems = trans('home.features.items');
        $defaults = [
            ['hero', 'main', 'content', __('home.hero.title'), __('home.hero.badge'), __('home.hero.description', ['schoolName' => 'TechBridge']), null, 1],
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
            ['about', 'main', 'content', __('home.about.title'), __('home.about.badge', ['schoolName' => 'TechBridge']), __('home.about.description'), null, 1],
            ['about_highlights', 'highlight_1', 'highlight', 'Leadership', null, 'We prepare students to lead with confidence, character, knowledge, and purpose.', null, 1],
            ['about_highlights', 'highlight_2', 'highlight', 'Transparency', null, 'Progress, updates, and next steps remain visible and easy to understand across the school community.', null, 2],
            ['about_cards', 'card_1', 'card', 'Mission', null, 'To empower every learner through quality education, discipline, confidence, and care.', 'bg-cyan-100 text-cyan-700', 1],
            ['about_cards', 'card_2', 'card', 'Vision', null, 'To build a modern learning environment where technology and personal guidance work hand in hand.', 'bg-indigo-100 text-indigo-700', 2],
            ['about_cards', 'card_3', 'card', 'Culture', null, 'Respect, curiosity, responsibility, and integrity shape both learning and leadership.', 'bg-amber-100 text-amber-700', 3],
            ['about_cards', 'card_4', 'card', 'Operations', null, 'Well-structured systems help families and staff receive timely support and clear information.', 'bg-emerald-100 text-emerald-700', 4],
            ['platform_features', 'main', 'content', __('home.features.title'), __('home.features.badge'), null, __('home.features.tag'), 1],
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

        foreach ($defaults as [$section, $key, $type, $title, $subtitle, $description, $value, $sortOrder]) {
            if ($hasExistingItems && $key !== 'main') {
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
            } else {
                $attributes['value'] = $value;
            }

            HomePageItem::query()->firstOrCreate(
                ['section' => $section, 'key' => $key],
                $attributes
            );
        }
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
