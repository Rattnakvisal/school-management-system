<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $schoolName ?? 'Schooli' }} | School Management System</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="website-home [font-family:Manrope,_sans-serif] bg-slate-100 text-slate-900 antialiased"
    x-data="{
        open: false,
        top: false,
        active: '#home',
        showBanner: false,
        bannerStorageKey: 'website_home_banner_hidden_until',
        setActive() {
            const ids = ['#home', '#about', '#features', '#programs', '#facilities', '#admission', '#faq', '#contact'];
            const y = window.scrollY + 140;
    
            for (let i = ids.length - 1; i >= 0; i--) {
                const el = document.querySelector(ids[i]);
                if (el && el.offsetTop <= y) {
                    this.active = ids[i];
                    break;
                }
            }
        },
        initBanner() {
            const forceBanner = new URLSearchParams(window.location.search).get('banner') === '1';
    
            try {
                if (forceBanner) {
                    window.localStorage.removeItem(this.bannerStorageKey);
                }
    
                const raw = window.localStorage.getItem(this.bannerStorageKey);
                const hiddenUntil = raw ? Number(raw) : 0;
    
                if (!Number.isNaN(hiddenUntil) && hiddenUntil > Date.now()) {
                    this.showBanner = false;
                    return;
                }
            } catch (_) {}
    
            setTimeout(() => {
                this.showBanner = true;
            }, 280);
        },
        closeBanner(remember = false) {
            this.showBanner = false;
    
            if (remember) {
                try {
                    window.localStorage.setItem(this.bannerStorageKey, String(Date.now() + (24 * 60 * 60 * 1000)));
                } catch (_) {}
            }
        },
        openProgramsFromBanner() {
            this.closeBanner();
            const target = document.querySelector('#programs');
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    }" x-init="setActive();
    initBanner();
    window.addEventListener('scroll', () => {
        top = window.scrollY > 560;
        setActive();
    });"
    x-effect="document.body.classList.toggle('overflow-hidden', showBanner)"
    @keydown.escape.window="if (showBanner) closeBanner()">
    @php
        $schoolName = $schoolName ?? 'Schooli';
        $studentsTotal = $studentsTotal ?? 0;
        $teachersTotal = $teachersTotal ?? 0;
        $ratio = $teachersTotal > 0 ? number_format($studentsTotal / $teachersTotal, 1) : '0.0';
        $dashboardRoute = $dashboardRoute ?? 'home';

        $links = [
            ['#about', 'About'],
            ['#features', 'Features'],
            ['#programs', 'Programs'],
            ['#facilities', 'Facilities'],
            ['#admission', 'Admission'],
            ['#faq', 'FAQ'],
            ['#contact', 'Contact'],
        ];

        $features = [
            [
                'title' => 'Student & Teacher Management',
                'desc' => 'Manage profiles, classes, subjects, and roles in one secure system.',
            ],
            [
                'title' => 'Attendance & Reports',
                'desc' => 'Daily attendance with exportable reports and clear history tracking.',
            ],
            [
                'title' => 'Grades & Performance',
                'desc' => 'Record scores, track progress, and generate academic summaries.',
            ],
            [
                'title' => 'Announcements & Messaging',
                'desc' => 'Send notices and updates quickly to students, teachers, and parents.',
            ],
            [
                'title' => 'Secure Role-Based Access',
                'desc' => 'Admin, teacher, and student dashboards with proper permissions.',
            ],
            [
                'title' => 'Fast Admissions & Requests',
                'desc' => 'Online registration, inquiries, and admin review workflow.',
            ],
        ];

        $programs = [
            [
                'level' => 'Primary',
                'title' => 'Foundation Learning',
                'desc' => 'Literacy, numeracy, language, and social growth.',
            ],
            [
                'level' => 'Lower Secondary',
                'title' => 'Core Expansion',
                'desc' => 'Stronger science, math, collaboration, and projects.',
            ],
            [
                'level' => 'Upper Secondary',
                'title' => 'Exam & Career Focus',
                'desc' => 'Academic depth, mentorship, and university readiness.',
            ],
            [
                'level' => 'Co-Curricular',
                'title' => 'Clubs, Arts & Sports',
                'desc' => 'Leadership, creativity, confidence, and teamwork.',
            ],
        ];

        $steps = [
            ['t' => 'Get access account', 'd' => 'Request an account from the school administrator.'],
            ['t' => 'Submit information', 'd' => 'Complete profile details and required documents.'],
            ['t' => 'Review & placement', 'd' => 'Academic coordinator confirms grade placement.'],
            ['t' => 'Confirm enrollment', 'd' => 'Receive activation and class schedule in the portal.'],
        ];

        $heroHighlights = [
            ['value' => number_format($studentsTotal), 'label' => 'active students'],
            ['value' => number_format($teachersTotal), 'label' => 'teaching specialists'],
            ['value' => '24/7', 'label' => 'portal visibility'],
        ];

        $heroPoints = [
            'One connected portal for academics, attendance, and communication.',
            'Clear dashboards for school leaders, teachers, and students.',
            'Smoother admissions and daily operations with less paperwork.',
        ];

        $aboutCards = [
            [
                'title' => 'Mission',
                'desc' => 'Empower each learner with quality teaching, discipline, and confidence.',
                'tone' => 'bg-cyan-100 text-cyan-700',
            ],
            [
                'title' => 'Vision',
                'desc' => 'Build a modern school where technology and human guidance work together.',
                'tone' => 'bg-indigo-100 text-indigo-700',
            ],
            [
                'title' => 'Culture',
                'desc' => 'Respect, curiosity, and responsibility shape both learning and leadership.',
                'tone' => 'bg-amber-100 text-amber-700',
            ],
            [
                'title' => 'Operations',
                'desc' => 'Structured workflows give families and staff faster answers and better visibility.',
                'tone' => 'bg-emerald-100 text-emerald-700',
            ],
        ];

        $facilityCards = [
            [
                'title' => 'Smart classrooms',
                'desc' => 'Interactive teaching spaces designed for focused and visual instruction.',
            ],
            [
                'title' => 'Science and computer labs',
                'desc' => 'Hands-on practice for experiments, digital literacy, and problem solving.',
            ],
            [
                'title' => 'Library and activity zones',
                'desc' => 'Quiet reading areas and collaboration spaces that support student growth.',
            ],
        ];

        $faqItems = [
            [
                'q' => 'Can parents track student progress online?',
                'a' => 'Yes. Parents can view notices, attendance updates, and academic reports.',
            ],
            [
                'q' => 'Do teachers manage attendance in the system?',
                'a' => 'Yes. Attendance can be recorded daily with clear history and reports.',
            ],
            [
                'q' => 'Is the platform role-based?',
                'a' => 'Yes. Admin, teacher, and student roles have different permissions and dashboards.',
            ],
            [
                'q' => 'How do we contact admissions?',
                'a' => 'Use the contact form below or reach out via phone or email.',
            ],
        ];

        $footerLinks = [
            ['href' => '#about', 'label' => 'About'],
            ['href' => '#features', 'label' => 'Features'],
            ['href' => '#programs', 'label' => 'Programs'],
            ['href' => '#admission', 'label' => 'Admissions'],
            ['href' => '#contact', 'label' => 'Contact'],
        ];

        $contactCards = [
            ['label' => 'Address', 'value' => 'Phnom Penh, Cambodia'],
            ['label' => 'Phone', 'value' => '+855 XXX XXX XXX'],
            ['label' => 'Email', 'value' => 'admissions@schooli.edu'],
            ['label' => 'Office hours', 'value' => 'Mon-Fri, 8:00 AM - 5:00 PM'],
        ];
    @endphp

    <div class="relative isolate overflow-x-hidden">
        <div
            class="pointer-events-none absolute inset-x-0 top-0 -z-20 h-[42rem] bg-[radial-gradient(circle_at_top_left,_rgba(34,211,238,0.18),_transparent_34%),radial-gradient(circle_at_top_right,_rgba(59,130,246,0.16),_transparent_26%),linear-gradient(180deg,_#f8fbff_0%,_#eff6ff_55%,_#f8fafc_100%)]">
        </div>
        <div
            class="home-orb pointer-events-none absolute -left-28 top-12 -z-10 h-72 w-72 rounded-full bg-cyan-300/35 blur-3xl">
        </div>
        <div
            class="home-orb home-orb--b pointer-events-none absolute right-0 top-24 -z-10 h-80 w-80 rounded-full bg-indigo-300/25 blur-3xl">
        </div>
        <div
            class="home-orb home-orb--c pointer-events-none absolute bottom-[22rem] left-1/3 -z-10 h-72 w-72 rounded-full bg-amber-200/25 blur-3xl">
        </div>

        <div x-show="showBanner" x-cloak x-transition.opacity class="fixed inset-0 z-[90] px-4 py-6 sm:px-6">
            <div class="absolute inset-0 bg-slate-950/75 backdrop-blur-sm" @click="closeBanner()"></div>

            <div class="relative mx-auto flex min-h-full max-w-6xl items-center">
                <section @click.stop
                    class="relative w-full overflow-hidden rounded-[2rem] border border-white/15 bg-gradient-to-br from-slate-900 via-cyan-900 to-sky-700 text-white shadow-2xl shadow-slate-950/40">
                    <button type="button" @click="closeBanner()"
                        class="absolute right-4 top-4 inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/20 bg-white/10 text-white transition hover:bg-white/20"
                        aria-label="Close banner">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 6l12 12M18 6 6 18" />
                        </svg>
                    </button>

                    <div
                        class="pointer-events-none absolute -right-20 top-0 h-64 w-64 rounded-full bg-cyan-200/20 blur-3xl">
                    </div>
                    <div
                        class="pointer-events-none absolute -bottom-16 left-12 h-60 w-60 rounded-full bg-sky-300/20 blur-3xl">
                    </div>

                    <div class="grid gap-8 lg:grid-cols-12">
                        <div class="relative p-6 sm:p-8 lg:col-span-7 lg:p-10">
                            <span data-reveal data-first style="--d:.06s"
                                class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-2 text-[11px] font-extrabold uppercase tracking-[0.28em] text-cyan-100">
                                <span class="h-2 w-2 rounded-full bg-cyan-300"></span>
                                New semester 2026
                            </span>

                            <h2 data-reveal data-first style="--d:.12s"
                                class="[font-family:Outfit,_sans-serif] mt-6 max-w-2xl text-3xl font-semibold leading-tight sm:text-4xl lg:text-5xl">
                                Welcome to {{ $schoolName }} with a cleaner, faster first impression.
                            </h2>

                            <p data-reveal data-first style="--d:.18s"
                                class="mt-4 max-w-2xl text-sm leading-7 text-cyan-50 sm:text-base">
                                Explore the school, review programs, and reach admissions from one homepage that feels
                                organized and easier to trust.
                            </p>

                            <div data-reveal data-first style="--d:.22s" class="mt-6 grid gap-3 sm:grid-cols-2">
                                @foreach ($programs as $item)
                                    <div class="rounded-2xl border border-white/15 bg-white/10 px-4 py-3 backdrop-blur">
                                        <p class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-cyan-100">
                                            {{ $item['level'] }}
                                        </p>
                                        <p class="mt-2 text-sm font-semibold text-white">{{ $item['title'] }}</p>
                                    </div>
                                @endforeach
                            </div>

                            <div data-reveal data-first style="--d:.26s" class="mt-7 flex flex-wrap gap-3">
                                <button type="button" @click="openProgramsFromBanner()"
                                    class="inline-flex items-center justify-center rounded-2xl bg-white px-5 py-3 text-sm font-bold text-slate-900 transition hover:bg-cyan-50">
                                    Explore programs
                                </button>
                                @guest
                                    <a href="{{ route('login') }}"
                                        class="inline-flex items-center justify-center rounded-2xl border border-white/25 bg-white/10 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/20">
                                        Login to portal
                                    </a>
                                @else
                                    <a href="{{ route($dashboardRoute) }}"
                                        class="inline-flex items-center justify-center rounded-2xl border border-white/25 bg-white/10 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/20">
                                        Open dashboard
                                    </a>
                                @endguest
                                <button type="button" @click="closeBanner(true)"
                                    class="inline-flex items-center justify-center rounded-2xl border border-white/20 px-5 py-3 text-sm font-semibold text-cyan-50 transition hover:bg-white/10">
                                    Don't show today
                                </button>
                            </div>
                        </div>

                        <div data-reveal data-first style="--d:.18s"
                            class="relative hidden overflow-hidden lg:col-span-5 lg:block">
                            <img src="{{ asset('images/study.jpg') }}" alt="Students in class"
                                class="home-hero-image h-full min-h-[460px] w-full object-cover" />
                            <div
                                class="absolute inset-0 bg-gradient-to-l from-slate-950/70 via-slate-950/15 to-transparent">
                            </div>
                            <div
                                class="home-float-card absolute bottom-6 left-6 right-6 rounded-[1.5rem] border border-white/15 bg-slate-950/55 p-5 backdrop-blur-xl">
                                <p class="text-[11px] font-extrabold uppercase tracking-[0.24em] text-cyan-200">Quick
                                    info</p>
                                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                    <div class="rounded-2xl bg-white/10 px-4 py-3">
                                        <p class="text-xs text-slate-200">Students</p>
                                        <p class="mt-1 text-2xl font-bold text-white">
                                            {{ number_format($studentsTotal) }}</p>
                                    </div>
                                    <div class="rounded-2xl bg-white/10 px-4 py-3">
                                        <p class="text-xs text-slate-200">Teachers</p>
                                        <p class="mt-1 text-2xl font-bold text-white">
                                            {{ number_format($teachersTotal) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <header class="sticky top-0 z-50 border-b border-white/60 bg-white/80 backdrop-blur-xl">
            <div data-reveal data-first style="--d:.02s"
                class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6">
                <a href="{{ route('home') }}" class="flex items-center gap-3 transition hover:opacity-90">
                    <span
                        class="grid h-12 w-12 place-items-center rounded-2xl bg-slate-950 text-cyan-300 shadow-lg shadow-slate-900/15">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M12 2 2 7l10 5 10-5-10-5Zm0 7L2 4v9l10 5 10-5V4l-10 5Z" />
                        </svg>
                    </span>
                    <div>
                        <p class="[font-family:Outfit,_sans-serif] text-2xl font-semibold leading-none tracking-tight">
                            {{ $schoolName }}
                        </p>
                        <p class="mt-1 text-[11px] font-extrabold uppercase tracking-[0.26em] text-slate-500">
                            Smart school management
                        </p>
                    </div>
                </a>

                <nav
                    class="hidden items-center gap-1 rounded-full border border-slate-200 bg-white/90 p-1 text-sm font-semibold text-slate-600 shadow-sm lg:flex">
                    @foreach ($links as [$href, $label])
                        <a href="{{ $href }}" class="rounded-full px-4 py-2 transition"
                            :class="active === '{{ $href }}' ? 'bg-slate-950 text-white shadow-sm' :
                                'hover:bg-slate-100 hover:text-slate-950'">
                            {{ $label }}
                        </a>
                    @endforeach
                </nav>

                <div class="flex items-center gap-2">
                    <div class="hidden items-center gap-2 md:flex">
                        @auth
                            <a href="{{ route($dashboardRoute) }}"
                                class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-slate-800">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                                Login
                            </a>
                            <a href="#contact"
                                class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-slate-800">
                                Contact admissions
                            </a>
                        @endauth
                    </div>

                    <button type="button" @click="open = !open"
                        class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-300 bg-white text-slate-700 lg:hidden"
                        aria-label="Toggle menu">
                        <svg x-show="!open" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="open" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div x-show="open" x-cloak x-transition class="border-t border-slate-200 bg-white/95 lg:hidden">
                <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6">
                    <div class="grid gap-2 text-sm font-semibold text-slate-700">
                        @foreach ($links as [$href, $label])
                            <a href="{{ $href }}" @click="open = false"
                                class="rounded-2xl px-4 py-3 transition hover:bg-slate-100">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>

                    <div class="mt-4 grid gap-2">
                        @auth
                            <a href="{{ route($dashboardRoute) }}"
                                class="rounded-2xl bg-slate-950 px-4 py-3 text-center text-sm font-bold text-white">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="rounded-2xl border border-slate-300 bg-white px-4 py-3 text-center text-sm font-semibold text-slate-700">
                                Login
                            </a>
                            <a href="#contact" @click="open = false"
                                class="rounded-2xl bg-slate-950 px-4 py-3 text-center text-sm font-bold text-white">
                                Contact admissions
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <main id="home" class="relative z-10" data-page-animate>
            <section class="mx-auto max-w-7xl px-4 pb-12 pt-8 sm:px-6 lg:pb-16 lg:pt-10">
                <div
                    class="home-hero-shell relative overflow-hidden rounded-[2rem] border border-white/70 bg-white/80 p-5 shadow-[0_28px_80px_rgba(15,23,42,0.08)] backdrop-blur-xl sm:p-7 lg:p-10">
                    <div
                        class="pointer-events-none absolute inset-0 bg-[linear-gradient(rgba(148,163,184,0.10)_1px,transparent_1px),linear-gradient(90deg,rgba(148,163,184,0.10)_1px,transparent_1px)] bg-[size:32px_32px] [mask-image:linear-gradient(180deg,rgba(15,23,42,0.16),transparent_70%)]">
                    </div>
                    <div
                        class="pointer-events-none absolute -left-16 top-0 h-56 w-56 rounded-full bg-cyan-300/25 blur-3xl">
                    </div>
                    <div
                        class="pointer-events-none absolute right-0 top-8 h-60 w-60 rounded-full bg-indigo-300/20 blur-3xl">
                    </div>

                    <div class="relative grid items-center gap-10 lg:grid-cols-12">
                        <div class="lg:col-span-5">
                            <span data-reveal data-first style="--d:.08s"
                                class="inline-flex items-center gap-2 rounded-full bg-cyan-50 px-4 py-2 text-[11px] font-extrabold uppercase tracking-[0.28em] text-cyan-800 ring-1 ring-cyan-100">
                                <span class="h-2 w-2 rounded-full bg-cyan-500"></span>
                                Future-ready school platform
                            </span>

                            <h1 data-reveal data-first style="--d:.14s"
                                class="[font-family:Outfit,_sans-serif] mt-6 text-4xl font-semibold leading-[1.02] text-slate-950 sm:text-5xl lg:text-6xl">
                                A homepage that feels modern, clear, and ready for families.
                            </h1>

                            <p data-reveal data-first style="--d:.20s"
                                class="mt-5 max-w-2xl text-base leading-8 text-slate-600 sm:text-lg">
                                {{ $schoolName }} connects academics, admissions, attendance, communication, and
                                school
                                operations in one cleaner experience built with Tailwind-first UI.
                            </p>

                            <div data-reveal data-first style="--d:.24s" class="mt-7 grid gap-3 sm:grid-cols-3">
                                @foreach ($heroHighlights as $item)
                                    <div
                                        class="home-lift-card rounded-[1.5rem] border border-slate-200 bg-white px-4 py-4 shadow-sm">
                                        <p class="text-2xl font-extrabold text-slate-950">{{ $item['value'] }}</p>
                                        <p
                                            class="mt-1 text-[11px] font-extrabold uppercase tracking-[0.24em] text-slate-500">
                                            {{ $item['label'] }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>

                            <div data-reveal data-first style="--d:.28s"
                                class="mt-7 flex flex-wrap items-center gap-3">
                                @auth
                                    <a href="{{ route($dashboardRoute) }}"
                                        class="home-cta inline-flex items-center justify-center rounded-2xl bg-slate-950 px-6 py-3.5 text-sm font-bold text-white transition hover:bg-slate-800">
                                        Open dashboard
                                    </a>
                                @else
                                    <a href="#contact"
                                        class="home-cta inline-flex items-center justify-center rounded-2xl bg-slate-950 px-6 py-3.5 text-sm font-bold text-white transition hover:bg-slate-800">
                                        Contact admissions
                                    </a>
                                    <a href="{{ route('login') }}"
                                        class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-6 py-3.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                                        Login to portal
                                    </a>
                                @endauth
                            </div>

                            <div data-reveal data-first style="--d:.32s" class="mt-8 space-y-3">
                                @foreach ($heroPoints as $point)
                                    <div class="flex items-start gap-3 text-sm text-slate-600">
                                        <span
                                            class="mt-0.5 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-cyan-100 text-cyan-700">
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2">
                                                <path d="m5 12 5 5L20 7" />
                                            </svg>
                                        </span>
                                        <p class="leading-6">{{ $point }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div data-reveal data-first style="--d:.18s" class="relative lg:col-span-7">
                            <div
                                class="relative overflow-hidden rounded-[2rem] border border-white/70 bg-white/70 p-3 shadow-xl backdrop-blur">
                                <img src="{{ asset('images/school.jpg') }}" alt="School campus"
                                    class="home-hero-image h-[21rem] w-full rounded-[1.5rem] object-cover sm:h-[26rem] lg:h-[34rem]" />
                                <div
                                    class="absolute inset-3 rounded-[1.5rem] bg-gradient-to-tr from-slate-950/80 via-slate-950/20 to-transparent">
                                </div>

                                <div
                                    class="home-float-card absolute left-6 top-6 max-w-[15rem] rounded-[1.5rem] bg-white/90 p-5 shadow-xl ring-1 ring-white/80 backdrop-blur">
                                    <p class="text-[11px] font-extrabold uppercase tracking-[0.24em] text-slate-500">
                                        Student-teacher ratio</p>
                                    <p
                                        class="[font-family:Outfit,_sans-serif] mt-2 text-3xl font-semibold text-slate-950">
                                        {{ $ratio }} : 1
                                    </p>
                                    <p class="mt-1 text-sm text-slate-500">Balanced support across classes</p>
                                </div>

                                <div
                                    class="home-float-card home-float-card--slow absolute bottom-6 left-6 right-6 rounded-[1.75rem] bg-slate-950/80 p-5 text-white ring-1 ring-white/10 backdrop-blur-xl">
                                    <div class="flex flex-wrap items-start justify-between gap-4">
                                        <div class="max-w-xl">
                                            <p
                                                class="text-[11px] font-extrabold uppercase tracking-[0.24em] text-cyan-200">
                                                Operational excellence
                                            </p>
                                            <p class="mt-2 text-base font-semibold sm:text-lg">
                                                Admissions, attendance, classes, and communication stay aligned in one
                                                system.
                                            </p>
                                        </div>
                                        <div class="grid gap-2 text-sm text-slate-200 sm:text-right">
                                            <span class="rounded-full bg-white/10 px-3 py-1.5">Role-based
                                                dashboards</span>
                                            <span class="rounded-full bg-white/10 px-3 py-1.5">Live administrative
                                                visibility</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div data-reveal style="--d:.24s"
                                class="home-float-card mt-4 rounded-[1.75rem] border border-slate-200 bg-white px-5 py-5 shadow-sm lg:absolute lg:-bottom-5 lg:-left-3 lg:mt-0 lg:max-w-xs">
                                <p class="text-[11px] font-extrabold uppercase tracking-[0.24em] text-cyan-700">Daily
                                    coordination</p>
                                <p class="mt-2 text-sm leading-7 text-slate-600">
                                    Timetables, records, and announcements stay aligned so the whole school day feels
                                    smoother.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 lg:grid-cols-[1.2fr_.8fr]">
                    <div data-reveal style="--d:.08s"
                        class="rounded-[1.75rem] border border-slate-200 bg-white px-5 py-5 shadow-sm sm:px-6">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <p class="text-[11px] font-extrabold uppercase tracking-[0.24em] text-cyan-700">
                                    Tailwind-first refresh
                                </p>
                                <h2
                                    class="[font-family:Outfit,_sans-serif] mt-2 text-2xl font-semibold text-slate-950">
                                    Better spacing, stronger hierarchy, and a cleaner story from the first screen
                                </h2>
                            </div>
                            <a href="#about"
                                class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white px-4 py-2 text-xs font-extrabold uppercase tracking-[0.16em] text-slate-600 transition hover:bg-slate-100">
                                Scroll to explore
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="m6 9 6 6 6-6" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div data-reveal style="--d:.14s"
                        class="rounded-[1.75rem] border border-slate-900/10 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-900 px-5 py-5 text-white shadow-lg">
                        <p class="text-[11px] font-extrabold uppercase tracking-[0.24em] text-cyan-200">Connected
                            experience</p>
                        <p class="mt-2 text-sm leading-7 text-slate-100">
                            Every section now feels like part of one school system instead of isolated content blocks.
                        </p>
                    </div>
                </div>
            </section>

            <section id="about" class="mx-auto max-w-7xl px-4 pb-12 sm:px-6">
                <div class="grid gap-6 lg:grid-cols-12">
                    <div data-reveal
                        class="overflow-hidden rounded-[2rem] border border-slate-900/10 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-900 p-7 text-white shadow-lg lg:col-span-5 lg:p-8">
                        <span
                            class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-4 py-2 text-[11px] font-extrabold uppercase tracking-[0.28em] text-cyan-100">
                            <span class="h-2 w-2 rounded-full bg-cyan-300"></span>
                            About {{ $schoolName }}
                        </span>

                        <h2 class="[font-family:Outfit,_sans-serif] mt-5 text-3xl font-semibold sm:text-4xl">
                            A complete learning ecosystem with clearer communication.
                        </h2>

                        <p class="mt-4 max-w-xl text-sm leading-8 text-slate-100 sm:text-base">
                            Balanced education means strong academics, healthy routines, digital readiness, and better
                            visibility for families, teachers, and school leaders.
                        </p>

                        <div class="mt-8 grid gap-3 sm:grid-cols-2">
                            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                                <p class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-cyan-200">
                                    Leadership</p>
                                <p class="mt-2 text-sm leading-7 text-slate-100">
                                    We prepare students to lead with knowledge, character, and purpose.
                                </p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                                <p class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-cyan-200">
                                    Visibility</p>
                                <p class="mt-2 text-sm leading-7 text-slate-100">
                                    Progress, updates, and next steps stay easier to see across the community.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 lg:col-span-7">
                        @foreach ($aboutCards as $card)
                            <article data-reveal style="--d:{{ number_format(0.06 * $loop->iteration, 2) }}s"
                                class="home-lift-card rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                                <div
                                    class="inline-flex h-12 w-12 items-center justify-center rounded-2xl {{ $card['tone'] }}">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        @if ($loop->first)
                                            <path d="m5 12 5 5L20 7" />
                                        @elseif ($loop->index === 1)
                                            <path d="M3 12h18" />
                                            <path d="M12 3v18" />
                                        @elseif ($loop->index === 2)
                                            <path
                                                d="M12 2l3.5 7.09L23 10.18l-5.5 5.36L18.82 23 12 19.27 5.18 23l1.32-7.46L1 10.18l7.5-1.09L12 2z" />
                                        @else
                                            <rect x="3" y="4" width="18" height="16" rx="3" />
                                            <path d="M8 9h8M8 13h5" />
                                        @endif
                                    </svg>
                                </div>
                                <p class="mt-5 text-[11px] font-extrabold uppercase tracking-[0.24em] text-slate-500">
                                    {{ $card['title'] }}</p>
                                <p class="mt-3 text-base font-semibold leading-7 text-slate-950">{{ $card['desc'] }}
                                </p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="features" class="mx-auto max-w-7xl px-4 pb-12 sm:px-6">
                <div data-reveal class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <span
                            class="inline-flex items-center gap-2 rounded-full bg-cyan-50 px-4 py-2 text-[11px] font-extrabold uppercase tracking-[0.28em] text-cyan-800 ring-1 ring-cyan-100">
                            <span class="h-2 w-2 rounded-full bg-cyan-500"></span>
                            Platform features
                        </span>
                        <h2
                            class="[font-family:Outfit,_sans-serif] mt-4 text-3xl font-semibold text-slate-950 sm:text-4xl">
                            Everything needed to run a modern school system.
                        </h2>
                    </div>

                    <span
                        class="rounded-full border border-cyan-200 bg-cyan-50 px-4 py-2 text-[11px] font-extrabold uppercase tracking-[0.18em] text-cyan-700">
                        Secure / Fast / Simple
                    </span>
                </div>

                <div class="mt-7 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($features as $f)
                        <article data-reveal style="--d:{{ number_format(0.05 * $loop->iteration, 2) }}s"
                            class="home-lift-card rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div
                                    class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan-100 text-cyan-700">
                                    @if ($loop->index === 0)
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <path d="M3 21h18" />
                                            <path d="M5 21V7l7-4 7 4v14" />
                                            <path d="M9 10h.01M15 10h.01M9 14h.01M15 14h.01" />
                                        </svg>
                                    @elseif ($loop->index === 1)
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <path d="M8 7V3m8 4V3" />
                                            <rect x="3" y="5" width="18" height="16" rx="2" />
                                            <path d="M3 11h18" />
                                        </svg>
                                    @elseif ($loop->index === 2)
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <path d="M3 3v18h18" />
                                            <path d="M7 14l4-4 3 3 4-5" />
                                        </svg>
                                    @elseif ($loop->index === 3)
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                                        </svg>
                                    @elseif ($loop->index === 4)
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <path d="M12 2l8 4v6c0 5-3.5 9.5-8 10-4.5-.5-8-5-8-10V6l8-4z" />
                                            <path d="m9 12 2 2 4-4" />
                                        </svg>
                                    @else
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10" />
                                            <path d="M12 6v6l4 2" />
                                        </svg>
                                    @endif
                                </div>

                                <span class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-slate-400">
                                    0{{ $loop->iteration }}
                                </span>
                            </div>

                            <h3 class="mt-6 text-lg font-semibold text-slate-900">{{ $f['title'] }}</h3>
                            <p class="mt-3 text-sm leading-7 text-slate-600">{{ $f['desc'] }}</p>
                            <div class="mt-6 h-1.5 w-16 rounded-full bg-gradient-to-r from-cyan-400 to-slate-900">
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <section id="programs" class="mx-auto max-w-7xl px-4 pb-12 sm:px-6">
                <div data-reveal class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <span
                            class="inline-flex items-center gap-2 rounded-full bg-cyan-50 px-4 py-2 text-[11px] font-extrabold uppercase tracking-[0.28em] text-cyan-800 ring-1 ring-cyan-100">
                            <span class="h-2 w-2 rounded-full bg-cyan-500"></span>
                            Academic programs
                        </span>
                        <h2
                            class="[font-family:Outfit,_sans-serif] mt-4 text-3xl font-semibold text-slate-950 sm:text-4xl">
                            A structured path from foundation to graduation.
                        </h2>
                    </div>

                    <p class="max-w-xl text-sm leading-7 text-slate-500">
                        Each stage is easier to scan so visitors understand the learning journey quickly.
                    </p>
                </div>

                <div class="mt-7 grid gap-4 lg:grid-cols-4">
                    @foreach ($programs as $item)
                        <article data-reveal style="--d:{{ number_format(0.05 * $loop->iteration, 2) }}s"
                            class="home-lift-card rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="flex items-center justify-between gap-3">
                                <div
                                    class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-700">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M12 3 2 8l10 5 10-5-10-5z" />
                                        <path d="m2 8 10 5 10-5" />
                                        <path d="M2 13l10 5 10-5" />
                                    </svg>
                                </div>

                                <span
                                    class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-extrabold uppercase tracking-[0.18em] text-slate-500">
                                    {{ $item['level'] }}
                                </span>
                            </div>

                            <h3 class="mt-6 text-xl font-semibold text-slate-900">{{ $item['title'] }}</h3>
                            <p class="mt-3 text-sm leading-7 text-slate-600">{{ $item['desc'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <section id="facilities" class="mx-auto max-w-7xl px-4 pb-12 sm:px-6">
                <div class="grid gap-6 lg:grid-cols-12">
                    <div data-reveal class="lg:col-span-7">
                        <div
                            class="overflow-hidden rounded-[2rem] border border-white/70 bg-white/70 p-3 shadow-lg backdrop-blur">
                            <img src="{{ asset('images/study.jpg') }}" alt="Student learning experience"
                                class="home-hero-image h-80 w-full rounded-[1.6rem] object-cover lg:h-[32rem]" />
                        </div>
                    </div>

                    <div data-reveal style="--d:.08s" class="lg:col-span-5">
                        <div class="rounded-[2rem] border border-slate-200 bg-white p-7 shadow-sm">
                            <span
                                class="inline-flex items-center gap-2 rounded-full bg-cyan-50 px-4 py-2 text-[11px] font-extrabold uppercase tracking-[0.28em] text-cyan-800 ring-1 ring-cyan-100">
                                <span class="h-2 w-2 rounded-full bg-cyan-500"></span>
                                Campus and facilities
                            </span>

                            <h2 class="[font-family:Outfit,_sans-serif] mt-4 text-3xl font-semibold text-slate-950">
                                Spaces that support focused, practical learning.
                            </h2>

                            <p class="mt-4 text-sm leading-7 text-slate-600">
                                A strong homepage should show more than features. It should also show the environment
                                where students study, collaborate, and grow.
                            </p>

                            <div class="mt-7 space-y-3">
                                @foreach ($facilityCards as $facility)
                                    <article data-reveal
                                        style="--d:{{ number_format(0.05 * $loop->iteration, 2) }}s"
                                        class="home-lift-card home-hover-soft rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                        <p class="text-sm font-bold text-slate-900">{{ $facility['title'] }}</p>
                                        <p class="mt-1 text-sm leading-7 text-slate-600">{{ $facility['desc'] }}</p>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="admission" class="mx-auto max-w-7xl px-4 pb-12 sm:px-6">
                <div data-reveal
                    class="overflow-hidden rounded-[2rem] border border-slate-900/10 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-900 p-7 text-white shadow-lg sm:p-8 lg:p-10">
                    <div class="grid gap-8 lg:grid-cols-12">
                        <div class="lg:col-span-5">
                            <span
                                class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-4 py-2 text-[11px] font-extrabold uppercase tracking-[0.28em] text-cyan-100">
                                <span class="h-2 w-2 rounded-full bg-cyan-300"></span>
                                Admissions
                            </span>

                            <h2 class="[font-family:Outfit,_sans-serif] mt-4 text-3xl font-semibold sm:text-4xl">
                                Simple enrollment with a clear next step.
                            </h2>

                            <p class="mt-4 max-w-xl text-sm leading-8 text-slate-100 sm:text-base">
                                Prospective families should understand the process quickly, without digging through an
                                overloaded page.
                            </p>

                            <div class="mt-6 rounded-[1.75rem] border border-white/10 bg-white/10 p-5 backdrop-blur">
                                <p class="text-[11px] font-extrabold uppercase tracking-[0.24em] text-cyan-200">Open
                                    intake</p>
                                <p class="mt-2 text-2xl font-semibold">2026 enrollment is available now</p>
                                <p class="mt-2 text-sm leading-7 text-slate-200">
                                    Reach out through the contact form to start placement, guidance, and account setup.
                                </p>
                            </div>

                            <div class="mt-6">
                                @guest
                                    <a href="{{ route('login') }}"
                                        class="inline-flex items-center justify-center rounded-2xl bg-cyan-300 px-5 py-3 text-sm font-bold text-slate-950 transition hover:bg-cyan-200">
                                        Apply now
                                    </a>
                                @else
                                    <a href="{{ route($dashboardRoute) }}"
                                        class="inline-flex items-center justify-center rounded-2xl bg-cyan-300 px-5 py-3 text-sm font-bold text-slate-950 transition hover:bg-cyan-200">
                                        Continue to dashboard
                                    </a>
                                @endguest
                            </div>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2 lg:col-span-7">
                            @foreach ($steps as $i => $s)
                                <article data-reveal style="--d:{{ number_format(0.06 * $loop->iteration, 2) }}s"
                                    class="home-lift-card rounded-[1.6rem] border border-white/10 bg-white/10 p-5 backdrop-blur">
                                    <div
                                        class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-white/15 text-cyan-100">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <path d="m5 12 5 5L20 7" />
                                        </svg>
                                    </div>
                                    <p
                                        class="mt-4 text-[11px] font-extrabold uppercase tracking-[0.24em] text-cyan-200">
                                        Step {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                                    </p>
                                    <p class="mt-2 text-base font-semibold">{{ $s['t'] }}</p>
                                    <p class="mt-2 text-sm leading-7 text-slate-200">{{ $s['d'] }}</p>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <section id="faq" class="mx-auto max-w-7xl px-4 pb-12 sm:px-6">
                <div class="grid gap-6 lg:grid-cols-12">
                    <div data-reveal
                        class="rounded-[2rem] border border-slate-200 bg-white p-7 shadow-sm lg:col-span-8">
                        <span
                            class="inline-flex items-center gap-2 rounded-full bg-cyan-50 px-4 py-2 text-[11px] font-extrabold uppercase tracking-[0.28em] text-cyan-800 ring-1 ring-cyan-100">
                            <span class="h-2 w-2 rounded-full bg-cyan-500"></span>
                            FAQ
                        </span>

                        <h2 class="[font-family:Outfit,_sans-serif] mt-4 text-3xl font-semibold text-slate-950">
                            Quick answers for parents, students, and teachers.
                        </h2>

                        <div class="mt-6 grid gap-3">
                            @foreach ($faqItems as $faq)
                                <details data-reveal style="--d:{{ number_format(0.05 * $loop->iteration, 2) }}s"
                                    class="group rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4">
                                    <summary class="cursor-pointer list-none text-sm font-semibold text-slate-900">
                                        <span class="flex items-center justify-between gap-4">
                                            <span>{{ $faq['q'] }}</span>
                                            <span
                                                class="rounded-full bg-white p-2 text-slate-500 transition group-open:rotate-45">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path d="M12 5v14M5 12h14" />
                                                </svg>
                                            </span>
                                        </span>
                                    </summary>
                                    <p class="mt-3 text-sm leading-7 text-slate-600">{{ $faq['a'] }}</p>
                                </details>
                            @endforeach
                        </div>
                    </div>

                    <div data-reveal style="--d:.08s" class="lg:col-span-4">
                        <div
                            class="rounded-[2rem] border border-slate-900/10 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-900 p-7 text-white shadow-lg">
                            <p class="text-[11px] font-extrabold uppercase tracking-[0.24em] text-cyan-200">Need more
                                help?</p>
                            <h3 class="[font-family:Outfit,_sans-serif] mt-4 text-2xl font-semibold">
                                Talk to the admissions team directly.
                            </h3>
                            <p class="mt-4 text-sm leading-7 text-slate-100">
                                If a visitor still has questions after reading the page, the next step stays visible and
                                immediate.
                            </p>
                            <a href="#contact"
                                class="mt-6 inline-flex items-center justify-center rounded-2xl bg-white px-5 py-3 text-sm font-bold text-slate-900 transition hover:bg-cyan-50">
                                Open contact section
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <section id="contact" class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:pb-20">
                <div class="grid gap-6 lg:grid-cols-12">
                    <div data-reveal
                        class="rounded-[2rem] border border-slate-200 bg-white p-7 shadow-sm lg:col-span-7">
                        <span
                            class="inline-flex items-center gap-2 rounded-full bg-cyan-50 px-4 py-2 text-[11px] font-extrabold uppercase tracking-[0.28em] text-cyan-800 ring-1 ring-cyan-100">
                            <span class="h-2 w-2 rounded-full bg-cyan-500"></span>
                            Contact admissions
                        </span>

                        <h2 class="[font-family:Outfit,_sans-serif] mt-4 text-3xl font-semibold text-slate-950">
                            Send your message from the homepage.
                        </h2>

                        <p class="mt-3 text-sm leading-7 text-slate-600">
                            The form stays simple and welcoming while still sending messages directly to the admin
                            dashboard.
                        </p>

                        @if (session('contact_success'))
                            <div
                                class="mt-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                                {{ session('contact_success') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('contact.store') }}" class="mt-6 space-y-4">
                            @csrf

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="contact_name"
                                        class="mb-1 block text-[11px] font-extrabold uppercase tracking-[0.22em] text-slate-500">
                                        Full name
                                    </label>
                                    <input id="contact_name" name="name" type="text"
                                        value="{{ old('name') }}"
                                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-cyan-300 focus:bg-white focus:ring-4 focus:ring-cyan-100"
                                        placeholder="Your full name">
                                    @error('name')
                                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="contact_phone"
                                        class="mb-1 block text-[11px] font-extrabold uppercase tracking-[0.22em] text-slate-500">
                                        Phone (optional)
                                    </label>
                                    <input id="contact_phone" name="phone" type="text"
                                        value="{{ old('phone') }}"
                                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-cyan-300 focus:bg-white focus:ring-4 focus:ring-cyan-100"
                                        placeholder="+855 ...">
                                    @error('phone')
                                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="contact_email"
                                        class="mb-1 block text-[11px] font-extrabold uppercase tracking-[0.22em] text-slate-500">
                                        Email
                                    </label>
                                    <input id="contact_email" name="email" type="email"
                                        value="{{ old('email') }}"
                                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-cyan-300 focus:bg-white focus:ring-4 focus:ring-cyan-100"
                                        placeholder="you@example.com">
                                    @error('email')
                                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="contact_subject"
                                        class="mb-1 block text-[11px] font-extrabold uppercase tracking-[0.22em] text-slate-500">
                                        Subject
                                    </label>
                                    <input id="contact_subject" name="subject" type="text"
                                        value="{{ old('subject') }}"
                                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-cyan-300 focus:bg-white focus:ring-4 focus:ring-cyan-100"
                                        placeholder="Admission question">
                                    @error('subject')
                                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="contact_message"
                                    class="mb-1 block text-[11px] font-extrabold uppercase tracking-[0.22em] text-slate-500">
                                    Message
                                </label>
                                <textarea id="contact_message" name="message" rows="5"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-cyan-300 focus:bg-white focus:ring-4 focus:ring-cyan-100"
                                    placeholder="Write your message here...">{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex flex-wrap items-center gap-3">
                                <button type="submit"
                                    class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-800">
                                    Send message
                                </button>
                            </div>

                            @if ($errors->any())
                                <p class="mt-3 text-xs font-semibold text-red-600">Please check the contact form and
                                    try again.</p>
                            @endif
                        </form>
                    </div>

                    <div data-reveal style="--d:.08s" class="lg:col-span-5">
                        <div
                            class="rounded-[2rem] border border-slate-900/10 bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-900 p-7 text-white shadow-lg">
                            <p class="text-[11px] font-extrabold uppercase tracking-[0.24em] text-cyan-200">Campus
                                information</p>
                            <h3 class="[font-family:Outfit,_sans-serif] mt-4 text-2xl font-semibold">Visit or call our
                                team.</h3>
                            <p class="mt-3 text-sm leading-7 text-cyan-100">
                                We are ready to help with admissions, programs, and school guidance.
                            </p>

                            <div class="mt-6 space-y-3">
                                @foreach ($contactCards as $card)
                                    <article data-reveal
                                        style="--d:{{ number_format(0.05 * $loop->iteration, 2) }}s"
                                        class="home-lift-card rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                                        <p
                                            class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-cyan-200">
                                            {{ $card['label'] }}
                                        </p>
                                        <p class="mt-2 text-sm font-semibold text-slate-100">{{ $card['value'] }}</p>
                                    </article>
                                @endforeach
                            </div>

                            <div class="mt-6 space-y-3">
                                @guest
                                    <a href="{{ route('login') }}"
                                        class="block rounded-2xl border border-white/20 px-4 py-3 text-center text-sm font-semibold text-white transition hover:bg-white/10">
                                        Login
                                    </a>
                                @else
                                    <a href="{{ route($dashboardRoute) }}"
                                        class="block rounded-2xl bg-white px-4 py-3 text-center text-sm font-bold text-slate-900 transition hover:bg-cyan-50">
                                        Open dashboard
                                    </a>
                                @endguest
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-slate-200 bg-slate-950 text-slate-200">
            <div class="mx-auto max-w-7xl px-6 py-14">
                <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">
                    <div class="lg:col-span-2">
                        <div class="flex items-center gap-3">
                            <span
                                class="grid h-12 w-12 place-items-center rounded-2xl bg-white/10 text-cyan-300 ring-1 ring-white/10">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path d="M12 2 2 7l10 5 10-5-10-5Zm0 7L2 4v9l10 5 10-5V4l-10 5Z" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="[font-family:Outfit,_sans-serif] text-xl font-semibold text-white">
                                    {{ $schoolName }}</h3>
                                <p class="text-sm text-slate-400">Smart School Management</p>
                            </div>
                        </div>

                        <p class="mt-5 max-w-xl text-sm leading-8 text-slate-400">
                            A stronger homepage should make the school feel credible from the first second. This version
                            is cleaner, easier to scan, and more consistent across sections.
                        </p>
                    </div>

                    <div>
                        <h4 class="text-[11px] font-extrabold uppercase tracking-[0.24em] text-cyan-300">Explore</h4>
                        <ul class="mt-5 space-y-3 text-sm">
                            @foreach ($footerLinks as $link)
                                <li>
                                    <a href="{{ $link['href'] }}"
                                        class="transition hover:text-white">{{ $link['label'] }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div>
                        <h4 class="text-[11px] font-extrabold uppercase tracking-[0.24em] text-cyan-300">Contact</h4>
                        <ul class="mt-5 space-y-3 text-sm text-slate-400">
                            <li>Phnom Penh, Cambodia</li>
                            <li>+855 12 345 678</li>
                            <li>info@school.edu.kh</li>
                        </ul>
                    </div>
                </div>

                <div class="mt-10 border-t border-white/10 pt-6 text-center text-sm text-slate-500">
                    &copy; {{ date('Y') }} {{ $schoolName }}. All rights reserved.
                </div>
            </div>
        </footer>

        <button x-show="top" x-cloak x-transition @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
            type="button"
            class="fixed bottom-24 right-5 z-40 inline-flex h-12 w-12 items-center justify-center rounded-full bg-slate-950 text-white shadow-lg shadow-slate-900/20 transition hover:bg-slate-800"
            aria-label="Back to top">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="m18 15-6-6-6 6" />
            </svg>
        </button>

        <div id="school-chatbot" data-school-name="{{ $schoolName }}" class="fixed bottom-5 right-5 z-50">
            <div
                class="home-chatbot-chip pointer-events-none hidden items-center gap-2 rounded-full px-3 py-2 text-xs font-semibold text-slate-700 md:inline-flex">
                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                Ask {{ $schoolName }}
            </div>
            <button id="chatbot-toggle" type="button"
                class="home-chatbot-toggle inline-flex h-16 w-16 items-center justify-center rounded-full text-white shadow-xl transition focus:outline-none focus:ring-4 focus:ring-cyan-200"
                aria-expanded="false" aria-controls="chatbot-panel" aria-label="Open chatbot">
                <span class="home-chatbot-toggle__status" aria-hidden="true"></span>
                <span class="home-chatbot-mark text-cyan-50" aria-hidden="true">
                    <svg class="h-10 w-10" viewBox="0 0 40 40" fill="none">
                        <path class="home-chatbot-mark__bubble"
                            d="M9.6 14.5c0-4.2 3.4-7.6 7.6-7.6h7.8c4.2 0 7.6 3.4 7.6 7.6v6.5c0 4.2-3.4 7.6-7.6 7.6h-1.8l-4 3.8c-.5.5-1.3.1-1.3-.6v-3.2h-.7c-4.2 0-7.6-3.4-7.6-7.6v-6.5Z" />
                        <path class="home-chatbot-mark__line" d="M20 10.1V7.5" />
                        <circle class="home-chatbot-mark__signal" cx="20" cy="5.7" r="1.35" />
                        <circle class="home-chatbot-mark__signal home-chatbot-mark__signal--b" cx="15.7"
                            cy="8.5" r="1.05" />
                        <circle class="home-chatbot-mark__signal home-chatbot-mark__signal--c" cx="24.3"
                            cy="8.5" r="1.05" />
                        <rect class="home-chatbot-mark__head" x="13" y="12.4" width="14" height="10.8"
                            rx="4.2" />
                        <rect class="home-chatbot-mark__face" x="15.2" y="14.4" width="9.6" height="6.1"
                            rx="3" />
                        <circle class="home-chatbot-mark__eye" cx="17.9" cy="17.35" r="1.05" />
                        <circle class="home-chatbot-mark__eye home-chatbot-mark__eye--right" cx="22.1"
                            cy="17.35" r="1.05" />
                        <path class="home-chatbot-mark__mouth" d="M17.7 19.6c.8.65 3.8.65 4.6 0" />
                        <path class="home-chatbot-mark__arm" d="M13 17.5h-2.2" />
                        <path class="home-chatbot-mark__arm home-chatbot-mark__arm--right" d="M27 17.5h2.2" />
                        <path class="home-chatbot-mark__line" d="M17.4 23.2v2.4M22.6 23.2v2.4" />
                        <circle class="home-chatbot-mark__core" cx="20" cy="27.2" r="1.5" />
                    </svg>
                </span>
            </button>

            <section id="chatbot-panel"
                class="home-chatbot-panel mt-3 hidden w-[min(92vw,360px)] overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl"
                aria-live="polite">
                <header class="bg-slate-900 px-4 py-3 text-white">
                    <div class="flex items-center gap-3">
                        <span
                            class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan-500/15 text-cyan-300 ring-1 ring-white/10">
                            <span class="home-chatbot-mark home-chatbot-mark--sm" aria-hidden="true">
                                <svg class="h-8 w-8" viewBox="0 0 40 40" fill="none">
                                    <path class="home-chatbot-mark__bubble"
                                        d="M9.6 14.5c0-4.2 3.4-7.6 7.6-7.6h7.8c4.2 0 7.6 3.4 7.6 7.6v6.5c0 4.2-3.4 7.6-7.6 7.6h-1.8l-4 3.8c-.5.5-1.3.1-1.3-.6v-3.2h-.7c-4.2 0-7.6-3.4-7.6-7.6v-6.5Z" />
                                    <path class="home-chatbot-mark__line" d="M20 10.1V7.5" />
                                    <circle class="home-chatbot-mark__signal" cx="20" cy="5.7"
                                        r="1.35" />
                                    <circle class="home-chatbot-mark__signal home-chatbot-mark__signal--b"
                                        cx="15.7" cy="8.5" r="1.05" />
                                    <circle class="home-chatbot-mark__signal home-chatbot-mark__signal--c"
                                        cx="24.3" cy="8.5" r="1.05" />
                                    <rect class="home-chatbot-mark__head" x="13" y="12.4" width="14"
                                        height="10.8" rx="4.2" />
                                    <rect class="home-chatbot-mark__face" x="15.2" y="14.4" width="9.6"
                                        height="6.1" rx="3" />
                                    <circle class="home-chatbot-mark__eye" cx="17.9" cy="17.35" r="1.05" />
                                    <circle class="home-chatbot-mark__eye home-chatbot-mark__eye--right"
                                        cx="22.1" cy="17.35" r="1.05" />
                                    <path class="home-chatbot-mark__mouth" d="M17.7 19.6c.8.65 3.8.65 4.6 0" />
                                    <path class="home-chatbot-mark__arm" d="M13 17.5h-2.2" />
                                    <path class="home-chatbot-mark__arm home-chatbot-mark__arm--right"
                                        d="M27 17.5h2.2" />
                                    <path class="home-chatbot-mark__line" d="M17.4 23.2v2.4M22.6 23.2v2.4" />
                                    <circle class="home-chatbot-mark__core" cx="20" cy="27.2" r="1.5" />
                                </svg>
                            </span>
                        </span>
                        <div>
                            <p class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-cyan-300">Assistant
                            </p>
                            <h3 class="mt-1 text-sm font-semibold">School Q&amp;A Chatbot</h3>
                        </div>
                    </div>
                </header>

                <div id="chatbot-messages" class="h-80 space-y-3 overflow-y-auto bg-slate-50 px-3 py-3"></div>

                <div class="border-t border-slate-200 bg-white p-3">
                    <div id="chatbot-quick-questions" class="mb-3 flex flex-wrap gap-2"></div>
                    <form id="chatbot-form" class="flex items-center gap-2">
                        <input id="chatbot-input" type="text"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-cyan-300 focus:ring-4 focus:ring-cyan-100"
                            placeholder="Ask a question..." autocomplete="off">
                        <button type="submit"
                            class="rounded-xl bg-cyan-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-cyan-500">
                            Send
                        </button>
                    </form>
                </div>
            </section>
        </div>
    </div>

    @vite(['resources/js/chat-bot.js'])
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>

</html>
