<?php

use App\Models\HomePageItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest homepage renders default content and public actions', function () {
    User::factory()->count(2)->create(['role' => 'student']);
    User::factory()->create(['role' => 'teacher']);

    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertViewIs('website.home')
        ->assertViewHas('studentsTotal', 2)
        ->assertViewHas('teachersTotal', 1)
        ->assertSee('TechBridge', false)
        ->assertSee(\App\Support\HomePageContent::text('actions.contact_admissions'), false)
        ->assertSee(\App\Support\HomePageContent::text('actions.explore_programs'), false)
        ->assertSee(\App\Support\HomePageContent::text('token.prompt_title'), false);
});

test('authenticated users get dashboard calls to action on the homepage', function () {
    $teacher = User::factory()->create([
        'role' => 'teacher',
    ]);

    $response = $this
        ->actingAs($teacher)
        ->get(route('home'));

    $response
        ->assertOk()
        ->assertViewHas('dashboardRoute', 'teacher.dashboard')
        ->assertSee(route('teacher.dashboard'), false)
        ->assertSee(\App\Support\HomePageContent::text('actions.open_dashboard'), false)
        ->assertDontSee(\App\Support\HomePageContent::text('actions.login'), false);
});

test('homepage uses active editable content and ignores inactive items', function () {
    HomePageItem::query()->create([
        'section' => 'brand',
        'key' => 'main',
        'title' => 'Northstar Academy',
        'description' => 'A calm school portal',
        'is_active' => true,
    ]);
    HomePageItem::query()->create([
        'section' => 'hero',
        'key' => 'main',
        'subtitle' => 'Built for families',
        'title' => 'A clearer school experience',
        'description' => 'Admissions, learning, and communication in one place.',
        'is_active' => true,
    ]);
    HomePageItem::query()->create([
        'section' => 'navbar_links',
        'key' => 'scholarships',
        'title' => 'Scholarships',
        'value' => '#scholarships',
        'sort_order' => 1,
        'is_active' => true,
    ]);
    HomePageItem::query()->create([
        'section' => 'navbar_links',
        'key' => 'hidden-link',
        'title' => 'Hidden Link',
        'value' => '#hidden',
        'sort_order' => 2,
        'is_active' => false,
    ]);

    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertSee('Northstar Academy', false)
        ->assertSee('Built for families', false)
        ->assertSee('A clearer school experience', false)
        ->assertSee('Scholarships', false)
        ->assertDontSee('Hidden Link', false);
});

test('homepage footer branding follows navbar branding', function () {
    HomePageItem::query()->create([
        'section' => 'brand',
        'key' => 'main',
        'title' => 'Synced Academy',
        'description' => 'Navbar brand description',
        'image_path' => 'home-page/navbar-logo.png',
        'is_active' => true,
    ]);

    HomePageItem::query()->create([
        'section' => 'footer',
        'key' => 'main',
        'title' => 'Old footer tagline',
        'description' => 'Footer long description',
        'image_path' => 'home-page/footer-logo.png',
        'is_active' => true,
    ]);

    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertViewHas('schoolName', 'Synced Academy')
        ->assertViewHas('brandTagline', 'Navbar brand description')
        ->assertViewHas('footerTagline', 'Navbar brand description');

    expect($response->viewData('footerLogo'))->toBe($response->viewData('brandLogo'));
});

test('navbar settings update syncs footer brand fields', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    HomePageItem::query()->create([
        'section' => 'brand',
        'key' => 'main',
        'title' => 'Before Academy',
        'description' => 'Before description',
        'image_path' => 'home-page/before-logo.png',
        'is_active' => true,
    ]);

    HomePageItem::query()->create([
        'section' => 'footer',
        'key' => 'main',
        'title' => 'Old footer tagline',
        'description' => 'Footer long description',
        'image_path' => 'home-page/footer-logo.png',
        'subtitle' => 'Explore',
        'value' => 'Contact',
        'meta' => ['copyright' => 'All rights reserved.'],
        'is_active' => true,
    ]);

    $this
        ->actingAs($admin)
        ->put(route('admin.settings.navbar-page.update'), [
            'brand' => [
                'name' => 'After Academy',
                'description' => 'After brand description',
                'remove_logo' => 0,
            ],
            'navbar_links' => [],
        ])
        ->assertRedirect(route('admin.homepage.index'));

    $footer = HomePageItem::query()
        ->where('section', 'footer')
        ->where('key', 'main')
        ->first();

    expect($footer?->title)->toBe('After brand description');
    expect($footer?->image_path)->toBe('home-page/before-logo.png');
});

test('navbar settings can add a homepage course link from selected page', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $this
        ->actingAs($admin)
        ->put(route('admin.settings.navbar-page.update'), [
            'brand' => [
                'name' => 'Course Link Academy',
                'description' => 'Course link description',
                'remove_logo' => 0,
            ],
            'navbar_links' => [
                [
                    'label' => '',
                    'href' => '#course',
                    'active' => 1,
                    'delete' => 0,
                ],
            ],
        ])
        ->assertRedirect(route('admin.homepage.index'));

    $link = HomePageItem::query()
        ->where('section', 'navbar_links')
        ->where('key', 'link_1')
        ->first();

    expect($link?->title)->toBe('Courses');
    expect($link?->value)->toBe('#course');

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Courses', false)
        ->assertSee('#course', false);
});

test('navbar settings can save six homepage links', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $links = collect(range(1, 6))
        ->map(fn($number) => [
            'label' => 'Link ' . $number,
            'href' => '#section-' . $number,
            'active' => 1,
            'delete' => 0,
        ])
        ->all();

    $this
        ->actingAs($admin)
        ->put(route('admin.settings.navbar-page.update'), [
            'brand' => [
                'name' => 'Six Link Academy',
                'description' => 'Six link description',
                'remove_logo' => 0,
            ],
            'navbar_links' => $links,
        ])
        ->assertRedirect(route('admin.homepage.index'));

    expect(HomePageItem::query()->where('section', 'navbar_links')->count())->toBe(6);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Link 1', false)
        ->assertSee('Link 6', false)
        ->assertSee('#section-6', false);
});

test('admin course page updates dynamic homepage courses', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $this
        ->actingAs($admin)
        ->put(route('admin.settings.course-page.update'), [
            'course' => [
                'badge' => 'Learn. Grow. Succeed',
                'title' => 'Our Courses',
                'description' => 'Course cards managed from Homepage UI.',
            ],
            'featured' => [
                'label' => 'Featured',
                'title' => 'Start today',
                'description' => 'Choose a course and begin learning.',
            ],
            'course_cards' => [
                [
                    'title' => 'Laravel for School Platforms',
                    'description' => 'Build practical school management features.',
                    'category' => 'Development',
                    'instructor_name' => 'Nora Kim',
                    'instructor_role' => 'Senior Engineer',
                    'lessons' => '18 Lessons',
                    'duration' => '9h 30m',
                    'rating' => '4.9',
                    'review_count' => '320',
                    'price' => '$49',
                    'button_label' => '',
                    'color' => '#10b981',
                    'active' => 1,
                    'delete' => 0,
                    'remove_image' => 0,
                ],
            ],
        ])
        ->assertRedirect(route('admin.homepage.index'));

    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertSee('Our Courses', false)
        ->assertSee('Laravel for School Platforms', false)
        ->assertSee('Development', false)
        ->assertSee('Nora Kim', false)
        ->assertSee('$49', false);
});
