<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $schoolName ?? 'Schooli' }} | School Management System</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html {
            scroll-behavior: smooth;
        }

        [x-cloak] {
            display: none !important;
        }

        .page-boot [data-first] {
            opacity: 0;
            transform: translateY(22px) scale(.98);
        }

        .page-ready [data-first] {
            animation: first-in .8s cubic-bezier(.2, .8, .2, 1) forwards;
            animation-delay: var(--fd, 0s);
        }

        .page-ready .hero-pop {
            animation: hero-pop 1.05s cubic-bezier(.16, 1, .3, 1) forwards;
            animation-delay: var(--fd, .12s);
        }

        .page-ready .hero-photo {
            animation: hero-zoom 1.35s cubic-bezier(.2, .8, .2, 1) forwards;
            transform: scale(1.08);
        }

        .page-ready .pulse-cta {
            animation: cta-breathe 2.4s ease-in-out 1.2s infinite;
        }

        .scroll-cue {
            animation: cue-bounce 1.8s ease-in-out infinite;
        }

        .glow-chip {
            animation: chip-glow 2.8s ease-in-out infinite;
        }

        .reveal-ready [data-reveal] {
            opacity: 0;
            transform: translateY(22px) scale(.98);
            transition: opacity .65s ease, transform .65s cubic-bezier(.2, .8, .2, 1);
            transition-delay: var(--d, 0s);
        }

        .reveal-ready [data-reveal].is-visible {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        .card-hover {
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 35px rgba(15, 23, 42, .08);
        }

        .icon-pop {
            transition: transform .25s ease, background-color .25s ease;
        }

        .card-hover:hover .icon-pop {
            transform: translateY(-2px) scale(1.06);
        }

        .orb {
            animation: orb-float 9s ease-in-out infinite;
        }

        .orb-b {
            animation-delay: 1.4s;
        }

        .orb-c {
            animation-delay: 2.4s;
        }

        .hero-shine {
            position: relative;
            isolation: isolate;
        }

        .hero-shine::after {
            content: "";
            position: absolute;
            inset: -40% auto auto -30%;
            width: 68%;
            height: 220%;
            background: linear-gradient(120deg, transparent, rgba(255, 255, 255, .24), transparent);
            transform: rotate(18deg);
            animation: site-shimmer 8s linear infinite;
            pointer-events: none;
            z-index: 1;
        }

        @keyframes site-shimmer {
            from {
                transform: translateX(-40%) rotate(18deg);
            }

            to {
                transform: translateX(220%) rotate(18deg);
            }
        }

        .dot-pattern {
            background-image: radial-gradient(rgba(148, 163, 184, .32) 1px, transparent 1px);
            background-size: 12px 12px;
        }

        @keyframes orb-float {

            0%,
            100% {
                transform: translateY(0) translateX(0);
            }

            50% {
                transform: translateY(-16px) translateX(8px);
            }
        }

        @keyframes first-in {
            from {
                opacity: 0;
                transform: translateY(24px) scale(.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes hero-pop {
            0% {
                opacity: 0;
                transform: translateY(24px) scale(.96);
            }

            70% {
                opacity: 1;
                transform: translateY(-2px) scale(1.01);
            }

            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes hero-zoom {
            from {
                transform: scale(1.08);
            }

            to {
                transform: scale(1);
            }
        }

        @keyframes cta-breathe {

            0%,
            100% {
                box-shadow: 0 8px 18px rgba(30, 41, 59, .22);
            }

            50% {
                box-shadow: 0 14px 28px rgba(30, 41, 59, .34);
            }
        }

        @keyframes cue-bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(5px);
            }
        }

        @keyframes chip-glow {

            0%,
            100% {
                box-shadow: 0 0 0 rgba(34, 211, 238, 0);
            }

            50% {
                box-shadow: 0 0 22px rgba(34, 211, 238, .25);
            }
        }

        .chatbot-scroll {
            scrollbar-width: thin;
            scrollbar-color: #94a3b8 #f8fafc;
        }

        .chatbot-scroll::-webkit-scrollbar {
            width: 8px;
        }

        .chatbot-scroll::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 999px;
        }

        .chatbot-scroll::-webkit-scrollbar-track {
            background: #f8fafc;
        }

        .chatbot-robot {
            --robot-size: 32px;
            position: relative;
            display: inline-block;
            width: var(--robot-size);
            height: calc(var(--robot-size) + 10px);
            animation: robot-float 2.6s ease-in-out infinite;
        }

        .chatbot-robot--sm {
            --robot-size: 24px;
        }

        .chatbot-antenna {
            position: absolute;
            left: 50%;
            top: 0;
            width: 2px;
            height: 9px;
            background: #a5f3fc;
            transform: translateX(-50%);
        }

        .chatbot-antenna::before {
            content: "";
            position: absolute;
            left: 50%;
            top: -5px;
            width: 9px;
            height: 9px;
            border-radius: 999px;
            background: #22d3ee;
            transform: translateX(-50%);
            box-shadow: 0 0 0 0 rgba(34, 211, 238, .55);
            animation: robot-pulse 1.6s ease-in-out infinite;
        }

        .chatbot-head {
            position: absolute;
            top: 9px;
            left: 50%;
            width: var(--robot-size);
            height: calc(var(--robot-size) * .78);
            border-radius: 10px;
            border: 2px solid #0e7490;
            background: linear-gradient(180deg, #cffafe 0%, #a5f3fc 100%);
            transform: translateX(-50%);
            box-shadow: inset 0 -3px 0 rgba(14, 116, 144, .16);
        }

        .chatbot-head::before,
        .chatbot-head::after {
            content: "";
            position: absolute;
            top: 50%;
            width: 4px;
            height: 12px;
            border-radius: 999px;
            background: #67e8f9;
            transform-origin: top center;
            animation: robot-wave 2.1s ease-in-out infinite;
        }

        .chatbot-head::before {
            left: -5px;
            transform: translateY(-50%) rotate(-12deg);
        }

        .chatbot-head::after {
            right: -5px;
            transform: translateY(-50%) rotate(12deg);
            animation-delay: .2s;
        }

        .chatbot-face {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .chatbot-eye {
            width: 5px;
            height: 5px;
            border-radius: 999px;
            background: #0f172a;
            transform-origin: center;
            animation: robot-blink 4.2s ease-in-out infinite;
        }

        .chatbot-eye--right {
            animation-delay: .24s;
        }

        .chatbot-mouth {
            position: absolute;
            left: 50%;
            bottom: 5px;
            width: 11px;
            height: 5px;
            border-bottom: 2px solid #0e7490;
            border-radius: 0 0 8px 8px;
            transform: translateX(-50%);
        }

        @keyframes robot-float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-3px);
            }
        }

        @keyframes robot-pulse {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(34, 211, 238, .45);
            }

            60% {
                box-shadow: 0 0 0 8px rgba(34, 211, 238, 0);
            }
        }

        @keyframes robot-blink {

            0%,
            44%,
            48%,
            100% {
                transform: scaleY(1);
            }

            46% {
                transform: scaleY(.1);
            }
        }

        @keyframes robot-wave {

            0%,
            100% {
                transform: translateY(-50%) rotate(0deg);
            }

            50% {
                transform: translateY(-50%) rotate(16deg);
            }
        }

        @media (prefers-reduced-motion: reduce) {

            .hero-shine::after,
            .orb,
            .scroll-cue,
            .glow-chip,
            .pulse-cta {
                animation: none !important;
            }

            .page-boot [data-first],
            .page-ready [data-first] {
                opacity: 1;
                transform: none;
                animation: none;
            }

            .page-ready .hero-pop,
            .page-ready .hero-photo {
                animation: none;
                transform: none;
            }

            .reveal-ready [data-reveal] {
                opacity: 1;
                transform: none;
                transition: none;
            }

            .card-hover,
            .icon-pop {
                transition: none;
            }

            .chatbot-robot,
            .chatbot-antenna::before,
            .chatbot-eye,
            .chatbot-head::before,
            .chatbot-head::after {
                animation: none !important;
            }
        }
    </style>
</head>

<body class="page-boot min-h-screen bg-slate-50 text-slate-900" x-data="{
        open:false,
        top:false,
        active:'#home',
        showBanner:false,
        bannerStorageKey:'website_home_banner_hidden_until',
        setActive(){
            const ids=['#home','#about','#features','#programs','#facilities','#admission','#faq','#contact'];
            const y = window.scrollY + 120;
            for (let i=ids.length-1; i>=0; i--){
                const el = document.querySelector(ids[i]);
                if (!el) continue;
                if (el.offsetTop <= y){ this.active = ids[i]; break; }
            }
        },
        initBanner(){
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
                document.body.classList.add('overflow-hidden');
            }, 260);
        },
        closeBanner(remember = false){
            this.showBanner = false;
            document.body.classList.remove('overflow-hidden');
            if (remember) {
                try {
                    window.localStorage.setItem(this.bannerStorageKey, String(Date.now() + (24 * 60 * 60 * 1000)));
                } catch (_) {}
            }
        },
        openProgramsFromBanner(){
            this.closeBanner();
            const target = document.querySelector('#programs');
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    }" x-init="
        window.addEventListener('scroll', () => { top = window.scrollY > 520; setActive(); });
        setActive();
        initBanner();
    " @keydown.escape.window="if (showBanner) closeBanner()">
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
            ['title' => 'Student & Teacher Management', 'desc' => 'Manage profiles, classes, subjects, and roles in one secure system.'],
            ['title' => 'Attendance & Reports', 'desc' => 'Daily attendance with exportable reports and clear history tracking.'],
            ['title' => 'Grades & Performance', 'desc' => 'Record scores, track progress, and generate academic summaries.'],
            ['title' => 'Announcements & Messaging', 'desc' => 'Send notices and updates quickly to students, teachers, and parents.'],
            ['title' => 'Secure Role-Based Access', 'desc' => 'Admin, teacher, and student dashboards with proper permissions.'],
            ['title' => 'Fast Admissions & Requests', 'desc' => 'Online registration, inquiries, and admin review workflow.'],
        ];

        $programs = [
            ['level' => 'Primary', 'title' => 'Foundation Learning', 'desc' => 'Literacy, numeracy, language, and social growth.'],
            ['level' => 'Lower Secondary', 'title' => 'Core Expansion', 'desc' => 'Stronger science, math, collaboration, and projects.'],
            ['level' => 'Upper Secondary', 'title' => 'Exam & Career Focus', 'desc' => 'Academic depth, mentorship, and university readiness.'],
            ['level' => 'Co-Curricular', 'title' => 'Clubs, Arts & Sports', 'desc' => 'Leadership, creativity, confidence, and teamwork.'],
        ];

        $steps = [
            ['t' => 'Create account', 'd' => 'Register as a student or parent to access the portal.'],
            ['t' => 'Submit information', 'd' => 'Complete profile details and required documents.'],
            ['t' => 'Review & placement', 'd' => 'Academic coordinator confirms grade placement.'],
            ['t' => 'Confirm enrollment', 'd' => 'Receive activation and class schedule in the portal.'],
        ];
    @endphp

    <div class="relative overflow-hidden" id="home">
        <!-- Opening banner -->
        <div x-show="showBanner" x-cloak x-transition.opacity class="fixed inset-0 z-[90]">
            <div class="absolute inset-0 bg-slate-950/70 backdrop-blur-sm" @click="closeBanner()"></div>

            <div class="relative mx-auto flex min-h-screen w-full max-w-6xl items-center px-3 py-6 sm:px-6">
                <section @click.stop
                    class="relative w-full overflow-hidden rounded-3xl border border-indigo-200/20 bg-gradient-to-r from-indigo-700 via-blue-700 to-cyan-700 text-white shadow-2xl">
                    <button type="button" @click="closeBanner()"
                        class="absolute right-4 top-4 z-10 inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/30 bg-white/10 text-white transition hover:bg-white/20"
                        aria-label="Close banner">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 6l12 12M18 6L6 18" />
                        </svg>
                    </button>

                    <div
                        class="pointer-events-none absolute -right-14 -top-14 h-56 w-56 rounded-full bg-white/20 blur-3xl">
                    </div>
                    <div
                        class="pointer-events-none absolute -bottom-16 left-16 h-56 w-56 rounded-full bg-cyan-300/25 blur-3xl">
                    </div>

                    <div class="relative grid lg:grid-cols-12">
                        <div class="p-6 sm:p-8 lg:col-span-7">
                            <span
                                class="inline-flex rounded-full border border-white/30 bg-white/15 px-3 py-1 text-xs font-bold uppercase tracking-wide text-cyan-100">
                                New Semester 2026
                            </span>
                            <h2 class="mt-4 text-3xl font-semibold leading-tight sm:text-4xl">
                                Welcome to {{ $schoolName }}
                            </h2>
                            <p class="mt-3 max-w-2xl text-sm text-blue-100 sm:text-base">
                                Explore programs, admissions, and campus life. Start your enrollment and connect with
                                our academic team.
                            </p>

                            <div class="mt-5 grid gap-2 sm:grid-cols-2">
                                @foreach ($programs as $item)
                                    <div class="rounded-xl border border-white/20 bg-white/10 px-3 py-2">
                                        <div class="text-[11px] font-bold uppercase tracking-wide text-cyan-100">
                                            {{ $item['level'] }}
                                        </div>
                                        <div class="text-sm font-semibold">{{ $item['title'] }}</div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-6 flex flex-wrap items-center gap-2">
                                <button type="button" @click="openProgramsFromBanner()"
                                    class="rounded-xl bg-white px-4 py-2 text-sm font-bold text-indigo-700 transition hover:bg-cyan-50">
                                    Explore Programs
                                </button>
                                @guest
                                    <a href="{{ route('register') }}"
                                        class="rounded-xl border border-white/35 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20">
                                        Apply Now
                                    </a>
                                @else
                                    <a href="{{ route($dashboardRoute) }}"
                                        class="rounded-xl border border-white/35 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20">
                                        Open Dashboard
                                    </a>
                                @endguest
                                <button type="button" @click="closeBanner(true)"
                                    class="rounded-xl border border-white/30 px-4 py-2 text-sm font-semibold text-blue-50 transition hover:bg-white/10">
                                    Don't show today
                                </button>
                            </div>
                        </div>

                        <div class="relative hidden lg:block lg:col-span-5">
                            <img src="{{ asset('images/study.jpg') }}" alt="School students"
                                class="h-full min-h-[420px] w-full object-cover">
                            <div
                                class="absolute inset-0 bg-gradient-to-l from-slate-950/60 via-slate-900/20 to-transparent">
                            </div>
                            <div
                                class="absolute bottom-5 left-5 right-5 rounded-2xl border border-white/20 bg-slate-900/55 p-4 backdrop-blur">
                                <p class="text-xs font-bold uppercase tracking-wide text-cyan-200">Quick Info</p>
                                <div class="mt-2 grid grid-cols-2 gap-2 text-sm">
                                    <div class="rounded-lg bg-white/10 px-3 py-2">Students:
                                        {{ number_format($studentsTotal) }}
                                    </div>
                                    <div class="rounded-lg bg-white/10 px-3 py-2">Teachers:
                                        {{ number_format($teachersTotal) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <!-- Background blobs -->
        <div class="pointer-events-none absolute inset-0">
            <div class="orb absolute -left-28 top-0 h-80 w-80 rounded-full bg-cyan-300/30 blur-3xl"></div>
            <div class="orb orb-b absolute right-0 top-20 h-96 w-96 rounded-full bg-indigo-300/20 blur-3xl"></div>
            <div class="orb orb-c absolute bottom-0 left-1/3 h-80 w-80 rounded-full bg-amber-200/30 blur-3xl"></div>
        </div>

        <!-- Header -->
        <header class="sticky top-0 z-50 border-b border-slate-200/80 bg-slate-50/90 backdrop-blur">
            <div data-first style="--fd:.02s"
                class="mx-auto flex w-full max-w-7xl items-center justify-between gap-3 px-4 py-3 sm:px-6">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="grid h-11 w-11 place-items-center rounded-2xl bg-slate-900 text-cyan-300 shadow-sm">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M12 2 2 7l10 5 10-5-10-5Zm0 7L2 4v9l10 5 10-5V4l-10 5Z" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-2xl font-semibold leading-none tracking-tight">{{ $schoolName }}</p>
                        <p class="text-xs font-semibold text-slate-500">Smart School Management</p>
                    </div>
                </a>

                <!-- Desktop nav -->
                <nav class="hidden items-center gap-2 text-sm font-semibold text-slate-600 lg:flex">
                    @foreach($links as [$href, $label])
                        <a href="{{ $href }}" class="rounded-xl px-3 py-2 transition hover:text-slate-950"
                            :class="active === '{{ $href }}' ? 'bg-slate-900 text-white' : 'hover:bg-slate-100'">
                            {{ $label }}
                        </a>
                    @endforeach
                </nav>

                <div class="flex items-center gap-2">
                    <button @click="open = !open"
                        class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-700 lg:hidden"
                        aria-label="Toggle menu">
                        <svg x-show="!open" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="open" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <div class="hidden items-center gap-2 md:flex">
                        @auth
                            <a href="{{ route($dashboardRoute) }}"
                                class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                                Login
                            </a>
                            <a href="{{ route('register') }}"
                                class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800">
                                Register
                            </a>
                        @endauth
                    </div>
                </div>
            </div>

            <!-- Mobile nav -->
            <div x-show="open" x-cloak x-transition class="border-t border-slate-200 bg-white/95 lg:hidden">
                <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6">
                    <div class="grid gap-2 text-sm font-semibold text-slate-700">
                        @foreach($links as [$href, $label])
                            <a @click="open=false" href="{{ $href }}"
                                class="rounded-xl px-3 py-2 transition hover:bg-slate-100">{{ $label }}</a>
                        @endforeach
                    </div>

                    <div class="mt-3 grid gap-2">
                        @auth
                            <a href="{{ route($dashboardRoute) }}"
                                class="rounded-xl bg-slate-900 px-4 py-2 text-center text-sm font-bold text-white">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}"
                                class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-center text-sm font-semibold text-slate-700">Login</a>
                            <a href="{{ route('register') }}"
                                class="rounded-xl bg-slate-900 px-4 py-2 text-center text-sm font-bold text-white">Register</a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <main class="relative z-10" data-page-animate>
            <!-- HERO -->
            <section class="mx-auto w-full max-w-7xl px-4 pb-10 pt-10 sm:px-6 lg:pb-14 lg:pt-14">
                <div class="grid items-center gap-8 lg:grid-cols-12 lg:gap-10">
                    <div class="lg:col-span-6">
                        <span data-reveal data-first style="--d:.02s;--fd:.08s"
                            class="glow-chip inline-flex items-center gap-2 rounded-full bg-slate-900 px-3 py-1 text-xs font-bold uppercase tracking-wide text-cyan-300">
                            <span class="inline-block h-2 w-2 rounded-full bg-cyan-300"></span>
                            Future Ready School Platform
                        </span>

                        <h1 data-reveal data-first style="--d:.08s;--fd:.14s"
                            class="hero-pop mt-5 text-4xl font-semibold leading-tight text-slate-950 sm:text-5xl lg:text-6xl">
                            Build a Stronger
                            <span class="block text-cyan-700">School Community</span>
                        </h1>

                        <p data-reveal data-first style="--d:.14s;--fd:.20s"
                            class="mt-5 max-w-xl text-base text-slate-600 sm:text-lg">
                            {{ $schoolName }} connects administration, teaching, and student life in one platform.
                            Track academics, attendance, communication, and performance from one dashboard.
                        </p>

                        <div data-reveal data-first style="--d:.20s;--fd:.24s"
                            class="mt-7 flex flex-wrap items-center gap-3">
                            @auth
                                <a href="{{ route($dashboardRoute) }}"
                                    class="pulse-cta rounded-2xl bg-slate-900 px-6 py-3 text-sm font-bold text-white transition hover:bg-slate-800">
                                    Open Dashboard
                                </a>
                            @else
                                <a href="{{ route('register') }}"
                                    class="pulse-cta rounded-2xl bg-slate-900 px-6 py-3 text-sm font-bold text-white transition hover:bg-slate-800">
                                    Start Enrollment
                                </a>
                                <a href="{{ route('login') }}"
                                    class="rounded-2xl border border-slate-300 bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                                    Login to Portal
                                </a>
                            @endauth
                        </div>

                        <div class="mt-7 grid max-w-xl gap-3 sm:grid-cols-2">
                            <div data-reveal style="--d:.26s"
                                class="card-hover rounded-2xl bg-white p-4 ring-1 ring-slate-200">
                                <div
                                    class="icon-pop mb-3 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-100 text-cyan-700">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M3 10.5L12 4l9 6.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-9.5z" />
                                    </svg>
                                </div>
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">School Type</p>
                                <p class="mt-1 text-sm font-semibold text-slate-900">Primary to High School</p>
                            </div>
                            <div data-reveal style="--d:.32s"
                                class="card-hover rounded-2xl bg-white p-4 ring-1 ring-slate-200">
                                <div
                                    class="icon-pop mb-3 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M12 6v6l4 2" />
                                        <circle cx="12" cy="12" r="9" />
                                    </svg>
                                </div>
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Learning Model</p>
                                <p class="mt-1 text-sm font-semibold text-slate-900">Academic + Activity Based</p>
                            </div>
                        </div>
                    </div>

                    <div data-reveal data-first style="--d:.18s;--fd:.18s" class="lg:col-span-6">
                        <div
                            class="hero-shine card-hover relative overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl">
                            <img src="{{ asset('images/school.jpg') }}" alt="School campus"
                                class="hero-photo h-72 w-full object-cover sm:h-80 lg:h-[25rem]" />
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-900/20 to-transparent">
                            </div>

                            <div
                                class="absolute left-5 top-5 rounded-2xl bg-white/90 px-4 py-3 shadow-sm ring-1 ring-slate-200 backdrop-blur">
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Student Teacher
                                    Ratio</p>
                                <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $ratio }} : 1</p>
                            </div>

                            <div
                                class="absolute bottom-5 left-5 right-5 rounded-2xl bg-slate-900/85 p-4 text-white ring-1 ring-white/15 backdrop-blur">
                                <p class="text-xs font-bold uppercase tracking-wide text-cyan-200">Operational
                                    Excellence</p>
                                <p class="mt-1 text-sm text-slate-100">
                                    Admissions, class management, attendance, and communications managed in one secure
                                    platform.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KPI / Stats -->
                <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <article data-reveal data-first style="--d:.05s;--fd:.30s"
                        class="card-hover rounded-2xl bg-white p-5 ring-1 ring-slate-200 shadow-sm">
                        <div
                            class="icon-pop inline-flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-100 text-cyan-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                        </div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Total Students</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-950">{{ number_format($studentsTotal) }}</p>
                        <p class="mt-1 text-xs font-medium text-slate-600">Enrolled learners across grades</p>
                    </article>
                    <article data-reveal data-first style="--d:.11s;--fd:.34s"
                        class="card-hover rounded-2xl bg-white p-5 ring-1 ring-slate-200 shadow-sm">
                        <div
                            class="icon-pop inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 12c2.761 0 5-2.239 5-5S14.761 2 12 2 7 4.239 7 7s2.239 5 5 5z" />
                                <path d="M4 22v-2a8 8 0 0 1 16 0v2" />
                            </svg>
                        </div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Total Teachers</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-950">{{ number_format($teachersTotal) }}</p>
                        <p class="mt-1 text-xs font-medium text-slate-600">Professional teaching staff</p>
                    </article>
                    <article data-reveal data-first style="--d:.17s;--fd:.38s"
                        class="card-hover rounded-2xl bg-white p-5 ring-1 ring-slate-200 shadow-sm">
                        <div
                            class="icon-pop inline-flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2" />
                                <path d="M9 8h6M9 12h6M9 16h3" />
                            </svg>
                        </div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Portal Access</p>
                        <p class="mt-2 text-lg font-semibold text-slate-950">Admin, Teacher, Student</p>
                        <p class="mt-1 text-xs font-medium text-slate-600">Role-based dashboards</p>
                    </article>
                    <article data-reveal data-first style="--d:.23s;--fd:.42s"
                        class="card-hover rounded-2xl bg-white p-5 ring-1 ring-slate-200 shadow-sm">
                        <div
                            class="icon-pop inline-flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                            </svg>
                        </div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Communication</p>
                        <p class="mt-2 text-lg font-semibold text-slate-950">Notices & Updates</p>
                        <p class="mt-1 text-xs font-medium text-slate-600">Fast school-wide announcements</p>
                    </article>
                </div>

                <div data-first style="--fd:.48s" class="mt-8 hidden justify-center sm:flex">
                    <a href="#about"
                        class="scroll-cue inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white px-4 py-2 text-xs font-bold uppercase tracking-wide text-slate-600">
                        Scroll to Explore
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </a>
                </div>
            </section>

            <!-- ABOUT -->
            <section id="about" class="mx-auto w-full max-w-7xl px-4 pb-12 sm:px-6">
                <div data-reveal
                    class="grid gap-6 rounded-3xl bg-white p-7 shadow-sm ring-1 ring-slate-200 lg:grid-cols-12">
                    <div data-reveal style="--d:.06s" class="lg:col-span-7">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-cyan-700">About {{ $schoolName }}
                        </p>
                        <h2 class="mt-2 text-3xl font-semibold text-slate-950">A complete learning ecosystem</h2>
                        <p class="mt-4 text-slate-600">
                            Balanced education: strong academics, digital literacy, values, and student wellbeing.
                            Clear data and structured operations for families and staff.
                        </p>

                        <div class="mt-6 grid gap-3 sm:grid-cols-2">
                            <article data-reveal style="--d:.12s" class="card-hover rounded-2xl bg-slate-50 p-4">
                                <div
                                    class="icon-pop mb-2 inline-flex h-9 w-9 items-center justify-center rounded-lg bg-cyan-100 text-cyan-700">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="m9 12 2 2 4-4" />
                                        <path
                                            d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z" />
                                    </svg>
                                </div>
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Mission</p>
                                <p class="mt-2 text-sm text-slate-700">Empower each learner with quality teaching,
                                    discipline, and confidence.</p>
                            </article>
                            <article data-reveal style="--d:.16s" class="card-hover rounded-2xl bg-slate-50 p-4">
                                <div
                                    class="icon-pop mb-2 inline-flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M3 12h18" />
                                        <path d="M12 3v18" />
                                    </svg>
                                </div>
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Vision</p>
                                <p class="mt-2 text-sm text-slate-700">Build a modern school where technology and human
                                    guidance work together.</p>
                            </article>
                        </div>
                    </div>

                    <div data-reveal style="--d:.08s" class="lg:col-span-5">
                        <div
                            class="dot-pattern h-full rounded-2xl bg-gradient-to-br from-slate-900 to-cyan-900 p-6 text-white">
                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-cyan-200">Principal Message</p>
                            <p class="mt-4 text-sm leading-7 text-slate-100">
                                Education is not only about passing exams. It is about character, curiosity, and
                                responsibility.
                                We prepare students to lead with knowledge and purpose.
                            </p>
                            <p class="mt-4 text-sm font-semibold text-cyan-200">- School Leadership Team</p>

                            <div class="mt-6 grid gap-3 sm:grid-cols-2">
                                <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/15">
                                    <p class="text-xs font-bold text-cyan-200">Value 01</p>
                                    <p class="mt-1 text-sm font-semibold">Respect & Discipline</p>
                                </div>
                                <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/15">
                                    <p class="text-xs font-bold text-cyan-200">Value 02</p>
                                    <p class="mt-1 text-sm font-semibold">Innovation & Growth</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- FEATURES -->
            <section id="features" class="mx-auto w-full max-w-7xl px-4 pb-12 sm:px-6">
                <div data-reveal class="flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <h2 class="text-3xl font-semibold text-slate-950">Platform Features</h2>
                        <p class="mt-2 text-sm text-slate-500">Everything you need to run a modern school system.</p>
                    </div>
                    <span class="rounded-full bg-slate-900 px-4 py-2 text-xs font-bold text-cyan-300">Secure • Fast •
                        Simple</span>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($features as $f)
                        <article data-reveal style="--d:{{ number_format(0.04 * ($loop->index + 1), 2) }}s"
                            class="card-hover rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div
                                class="icon-pop mb-3 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-100 text-cyan-700">
                                @if($loop->index === 0)
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 21h18" />
                                        <path d="M5 21V7l7-4 7 4v14" />
                                        <path d="M9 10h.01M15 10h.01M9 14h.01M15 14h.01" />
                                    </svg>
                                @elseif($loop->index === 1)
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M8 7V3m8 4V3" />
                                        <rect x="3" y="5" width="18" height="16" rx="2" />
                                        <path d="M3 11h18" />
                                    </svg>
                                @elseif($loop->index === 2)
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 3v18h18" />
                                        <path d="M7 14l4-4 3 3 4-5" />
                                    </svg>
                                @elseif($loop->index === 3)
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                                    </svg>
                                @elseif($loop->index === 4)
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 2l8 4v6c0 5-3.5 9.5-8 10-4.5-.5-8-5-8-10V6l8-4z" />
                                        <path d="m9 12 2 2 4-4" />
                                    </svg>
                                @else
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10" />
                                        <path d="M12 6v6l4 2" />
                                    </svg>
                                @endif
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900">{{ $f['title'] }}</h3>
                            <p class="mt-2 text-sm text-slate-600">{{ $f['desc'] }}</p>
                            <div class="mt-4 h-1 w-12 rounded-full bg-cyan-300"></div>
                        </article>
                    @endforeach
                </div>
            </section>

            <!-- PROGRAMS -->
            <section id="programs" class="mx-auto w-full max-w-7xl px-4 pb-12 sm:px-6">
                <div data-reveal class="flex flex-wrap items-end justify-between gap-3">
                    <h2 class="text-3xl font-semibold text-slate-950">Academic Programs</h2>
                    <p class="text-sm text-slate-500">Structured progression from foundation to career preparation.</p>
                </div>

                <div class="mt-6 grid gap-4 lg:grid-cols-4">
                    @foreach($programs as $item)
                        <article data-reveal style="--d:{{ number_format(0.05 * ($loop->index + 1), 2) }}s"
                            class="card-hover rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div
                                class="icon-pop mb-3 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 3 2 8l10 5 10-5-10-5z" />
                                    <path d="m2 8 10 5 10-5" />
                                    <path d="M2 13l10 5 10-5" />
                                </svg>
                            </div>
                            <p class="text-xs font-bold uppercase tracking-wide text-cyan-700">{{ $item['level'] }}</p>
                            <h3 class="mt-2 text-xl font-semibold text-slate-900">{{ $item['title'] }}</h3>
                            <p class="mt-3 text-sm text-slate-600">{{ $item['desc'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <!-- FACILITIES -->
            <section id="facilities" class="mx-auto w-full max-w-7xl px-4 pb-12 sm:px-6">
                <div data-reveal class="grid gap-6 rounded-3xl bg-white p-7 ring-1 ring-slate-200 lg:grid-cols-12">
                    <div data-reveal style="--d:.06s" class="lg:col-span-5">
                        <h2 class="text-3xl font-semibold text-slate-950">Campus and Facilities</h2>
                        <p class="mt-3 text-slate-600">Safe spaces and modern resources for practical, engaging
                            learning.</p>

                        <div class="mt-6 space-y-3">
                            <article data-reveal style="--d:.10s" class="card-hover rounded-2xl bg-slate-50 p-4">
                                <div
                                    class="icon-pop mb-2 inline-flex h-9 w-9 items-center justify-center rounded-lg bg-cyan-100 text-cyan-700">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <rect x="3" y="4" width="18" height="14" rx="2" />
                                        <path d="M8 20h8" />
                                    </svg>
                                </div>
                                <p class="text-sm font-bold text-slate-900">Smart classrooms</p>
                                <p class="mt-1 text-sm text-slate-600">Interactive tools for focused and visual
                                    instruction.</p>
                            </article>
                            <article data-reveal style="--d:.14s" class="card-hover rounded-2xl bg-slate-50 p-4">
                                <div
                                    class="icon-pop mb-2 inline-flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M9 3h6v6H9z" />
                                        <path d="M4 13h16v8H4z" />
                                    </svg>
                                </div>
                                <p class="text-sm font-bold text-slate-900">Science & computer labs</p>
                                <p class="mt-1 text-sm text-slate-600">Hands-on learning for experiments, coding, and
                                    research.</p>
                            </article>
                            <article data-reveal style="--d:.18s" class="card-hover rounded-2xl bg-slate-50 p-4">
                                <div
                                    class="icon-pop mb-2 inline-flex h-9 w-9 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                                        <path d="M6.5 17A2.5 2.5 0 0 0 4 14.5V5a2 2 0 0 1 2-2h14v14" />
                                    </svg>
                                </div>
                                <p class="text-sm font-bold text-slate-900">Library & activity zones</p>
                                <p class="mt-1 text-sm text-slate-600">Quiet reading areas and collaboration spaces.</p>
                            </article>
                        </div>
                    </div>

                    <div data-reveal style="--d:.08s" class="lg:col-span-7">
                        <div class="overflow-hidden rounded-2xl border border-slate-200">
                            <img src="{{ asset('images/study.jpg') }}" alt="Student learning experience"
                                class="h-72 w-full object-cover lg:h-[26rem]" />
                        </div>
                    </div>
                </div>
            </section>

            <!-- ADMISSION -->
            <section id="admission" class="mx-auto w-full max-w-7xl px-4 pb-12 sm:px-6">
                <div data-reveal class="rounded-3xl bg-slate-900 p-7 text-white sm:p-8">
                    <div data-reveal style="--d:.05s" class="flex flex-wrap items-end justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-cyan-200">Admissions</p>
                            <h2 class="mt-2 text-3xl font-semibold">Simple Enrollment Process</h2>
                            <p class="mt-2 max-w-2xl text-sm text-slate-200">Easy steps for new families to join
                                {{ $schoolName }}.
                            </p>
                        </div>
                        <span class="rounded-full bg-white/10 px-4 py-2 text-xs font-bold ring-1 ring-white/20">Open
                            Intake 2026</span>
                    </div>

                    <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach($steps as $i => $s)
                            <article data-reveal style="--d:{{ number_format(0.07 * ($loop->index + 1), 2) }}s"
                                class="card-hover rounded-2xl bg-white/10 p-4 ring-1 ring-white/15">
                                <div
                                    class="icon-pop mb-2 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/20 text-cyan-100">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="m5 12 5 5L20 7" />
                                    </svg>
                                </div>
                                <p class="text-xs font-bold text-cyan-200">Step {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                                </p>
                                <p class="mt-1 text-sm font-bold">{{ $s['t'] }}</p>
                                <p class="mt-1 text-xs text-slate-200">{{ $s['d'] }}</p>
                            </article>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        @guest
                            <a href="{{ route('register') }}"
                                class="inline-flex rounded-xl bg-cyan-300 px-5 py-3 text-sm font-semibold text-slate-900 transition hover:bg-cyan-200">Apply
                                Now</a>
                        @else
                            <a href="{{ route($dashboardRoute) }}"
                                class="inline-flex rounded-xl bg-cyan-300 px-5 py-3 text-sm font-semibold text-slate-900 transition hover:bg-cyan-200">Continue
                                to Dashboard</a>
                        @endguest
                    </div>
                </div>
            </section>

            <!-- FAQ -->
            <section id="faq" class="mx-auto w-full max-w-7xl px-4 pb-12 sm:px-6">
                <div data-reveal class="rounded-3xl bg-white p-7 ring-1 ring-slate-200">
                    <div data-reveal style="--d:.04s" class="flex flex-wrap items-end justify-between gap-3">
                        <h2 class="text-3xl font-semibold text-slate-950">Frequently Asked Questions</h2>
                        <p class="text-sm text-slate-500">Quick answers for parents, students, and teachers.</p>
                    </div>

                    <div class="mt-6 grid gap-3 lg:grid-cols-2">
                        <details data-reveal style="--d:.08s" class="group card-hover rounded-2xl bg-slate-50 p-5">
                            <summary class="cursor-pointer text-sm font-semibold text-slate-900">
                                Can parents track student progress online?
                            </summary>
                            <p class="mt-2 text-sm text-slate-600">Yes. Parents can view notices, attendance updates,
                                and academic reports.</p>
                        </details>

                        <details data-reveal style="--d:.12s" class="group card-hover rounded-2xl bg-slate-50 p-5">
                            <summary class="cursor-pointer text-sm font-semibold text-slate-900">
                                Do teachers manage attendance in the system?
                            </summary>
                            <p class="mt-2 text-sm text-slate-600">Yes. Attendance can be recorded daily with clear
                                history and reports.</p>
                        </details>

                        <details data-reveal style="--d:.16s" class="group card-hover rounded-2xl bg-slate-50 p-5">
                            <summary class="cursor-pointer text-sm font-semibold text-slate-900">
                                Is the platform role-based?
                            </summary>
                            <p class="mt-2 text-sm text-slate-600">Yes. Admin, teacher, and student roles have different
                                permissions and dashboards.</p>
                        </details>

                        <details data-reveal style="--d:.20s" class="group card-hover rounded-2xl bg-slate-50 p-5">
                            <summary class="cursor-pointer text-sm font-semibold text-slate-900">
                                How do we contact admissions?
                            </summary>
                            <p class="mt-2 text-sm text-slate-600">Use the contact form below or reach out via
                                phone/email.</p>
                        </details>
                    </div>
                </div>
            </section>

            <!-- CONTACT -->
            <section id="contact" class="mx-auto w-full max-w-7xl px-4 pb-16 sm:px-6 lg:pb-20">
                <div class="grid gap-6 lg:grid-cols-12">
                    <div data-reveal class="rounded-3xl bg-white p-7 ring-1 ring-slate-200 lg:col-span-7">
                        <h2 class="text-3xl font-semibold text-slate-950">Contact Admissions</h2>
                        <p class="mt-3 text-slate-600">Send your message directly from this page. Admin can read it in
                            the dashboard.</p>

                        @if (session('contact_success'))
                            <div
                                class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                                {{ session('contact_success') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('contact.store') }}" class="mt-6 space-y-4">
                            @csrf

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="contact_name"
                                        class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Full
                                        Name</label>
                                    <input id="contact_name" name="name" type="text" value="{{ old('name') }}"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-cyan-300 focus:ring-4 focus:ring-cyan-100"
                                        placeholder="Your full name">
                                    @error('name') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="contact_phone"
                                        class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Phone
                                        (Optional)</label>
                                    <input id="contact_phone" name="phone" type="text" value="{{ old('phone') }}"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-cyan-300 focus:ring-4 focus:ring-cyan-100"
                                        placeholder="+855 ...">
                                    @error('phone') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}
                                    </p> @enderror
                                </div>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="contact_email"
                                        class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Email</label>
                                    <input id="contact_email" name="email" type="email" value="{{ old('email') }}"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-cyan-300 focus:ring-4 focus:ring-cyan-100"
                                        placeholder="you@example.com">
                                    @error('email') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}
                                    </p> @enderror
                                </div>

                                <div>
                                    <label for="contact_subject"
                                        class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Subject</label>
                                    <input id="contact_subject" name="subject" type="text" value="{{ old('subject') }}"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-cyan-300 focus:ring-4 focus:ring-cyan-100"
                                        placeholder="Admission question">
                                    @error('subject') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}
                                    </p> @enderror
                                </div>
                            </div>

                            <div>
                                <label for="contact_message"
                                    class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Message</label>
                                <textarea id="contact_message" name="message" rows="5"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-cyan-300 focus:ring-4 focus:ring-cyan-100"
                                    placeholder="Write your message here...">{{ old('message') }}</textarea>
                                @error('message') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit"
                                class="inline-flex rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                                Send Message
                            </button>

                            @if ($errors->any())
                                <p class="mt-3 text-xs font-semibold text-red-600">Please check the contact form and try
                                    again.</p>
                            @endif
                        </form>
                    </div>

                    <div data-reveal style="--d:.08s"
                        class="rounded-3xl bg-gradient-to-br from-cyan-700 to-slate-900 p-7 text-white lg:col-span-5">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-cyan-200">Campus Information</p>
                        <h3 class="mt-2 text-2xl font-semibold">Visit or call our team</h3>
                        <p class="mt-3 text-sm text-cyan-100">We will help with admissions, programs, and school
                            guidance.</p>

                        <div class="mt-5 space-y-3">
                            <article data-reveal style="--d:.10s"
                                class="card-hover rounded-2xl bg-white/10 p-4 ring-1 ring-white/15">
                                <div
                                    class="icon-pop mb-2 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/20 text-cyan-100">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M21 10c0 6-9 13-9 13S3 16 3 10a9 9 0 1 1 18 0z" />
                                        <circle cx="12" cy="10" r="3" />
                                    </svg>
                                </div>
                                <p class="text-xs font-bold uppercase tracking-wide text-cyan-200">Address</p>
                                <p class="mt-1 text-sm font-semibold text-slate-100">Phnom Penh, Cambodia</p>
                            </article>
                            <article data-reveal style="--d:.13s"
                                class="card-hover rounded-2xl bg-white/10 p-4 ring-1 ring-white/15">
                                <div
                                    class="icon-pop mb-2 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/20 text-cyan-100">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path
                                            d="M22 16.92V21a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h4.09a2 2 0 0 1 2 1.72l.57 3.41a2 2 0 0 1-.57 1.78L8.91 10.2a16 16 0 0 0 4.89 4.89l1.29-1.29a2 2 0 0 1 1.78-.57l3.41.57A2 2 0 0 1 22 16.92z" />
                                    </svg>
                                </div>
                                <p class="text-xs font-bold uppercase tracking-wide text-cyan-200">Phone</p>
                                <p class="mt-1 text-sm font-semibold text-slate-100">+855 XXX XXX XXX</p>
                            </article>
                            <article data-reveal style="--d:.16s"
                                class="card-hover rounded-2xl bg-white/10 p-4 ring-1 ring-white/15">
                                <div
                                    class="icon-pop mb-2 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/20 text-cyan-100">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M4 4h16v16H4z" />
                                        <path d="m22 6-10 7L2 6" />
                                    </svg>
                                </div>
                                <p class="text-xs font-bold uppercase tracking-wide text-cyan-200">Email</p>
                                <p class="mt-1 text-sm font-semibold text-slate-100">admissions@schooli.edu</p>
                            </article>
                            <article data-reveal style="--d:.19s"
                                class="card-hover rounded-2xl bg-white/10 p-4 ring-1 ring-white/15">
                                <div
                                    class="icon-pop mb-2 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/20 text-cyan-100">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <circle cx="12" cy="12" r="10" />
                                        <path d="M12 6v6l4 2" />
                                    </svg>
                                </div>
                                <p class="text-xs font-bold uppercase tracking-wide text-cyan-200">Office Hours</p>
                                <p class="mt-1 text-sm font-semibold text-slate-100">Mon–Fri, 8:00 AM – 5:00 PM</p>
                            </article>
                        </div>

                        <div class="mt-6 space-y-3">
                            @guest
                                <a href="{{ route('register') }}"
                                    class="block rounded-xl bg-white px-4 py-3 text-center text-sm font-semibold text-slate-900 transition hover:bg-cyan-50">Create
                                    Account</a>
                                <a href="{{ route('login') }}"
                                    class="block rounded-xl border border-white/30 px-4 py-3 text-center text-sm font-semibold text-white transition hover:bg-white/10">Login</a>
                            @else
                                <a href="{{ route($dashboardRoute) }}"
                                    class="block rounded-xl bg-white px-4 py-3 text-center text-sm font-semibold text-slate-900 transition hover:bg-cyan-50">Open
                                    Dashboard</a>
                            @endguest
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- FOOTER -->
        <footer class="bg-slate-900 text-slate-300">
            <div class="mx-auto max-w-7xl px-6 py-14">

                <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">

                    <!-- School Info -->
                    <div>
                        <div class="flex items-center gap-3">
                            <h3 class="text-lg font-bold text-white">{{ $schoolName }}</h3>
                        </div>

                        <p class="mt-4 text-sm leading-relaxed text-slate-400">
                            Empowering students with quality education, innovation, and leadership skills
                            for a brighter future.
                        </p>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h4 class="mb-4 text-white font-semibold">Quick Links</h4>
                        <ul class="space-y-3 text-sm">

                            <li class="flex items-center gap-2">
                                <!-- Arrow Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                                <a href="#about" class="hover:text-white transition">About Us</a>
                            </li>

                            <li class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                                <a href="#programs" class="hover:text-white transition">Programs</a>
                            </li>

                            <li class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                                <a href="#news" class="hover:text-white transition">News & Events</a>
                            </li>

                        </ul>
                    </div>

                    <!-- Admissions -->
                    <div>
                        <h4 class="mb-4 text-white font-semibold">Admissions</h4>
                        <ul class="space-y-3 text-sm">

                            <li class="flex items-center gap-2">
                                <!-- Check Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <a href="#admission" class="hover:text-white transition">Apply Now</a>
                            </li>

                            <li class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <a href="#requirements" class="hover:text-white transition">Requirements</a>
                            </li>

                            <li class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <a href="#fees" class="hover:text-white transition">Tuition Fees</a>
                            </li>

                        </ul>
                    </div>

                    <!-- Contact -->
                    <div>
                        <h4 class="mb-4 text-white font-semibold">Contact Us</h4>
                        <ul class="space-y-4 text-sm text-slate-400">

                            <!-- Location -->
                            <li class="flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
                                </svg>
                                Phnom Penh, Cambodia
                            </li>

                            <!-- Phone -->
                            <li class="flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5h2l3 7-1.5 2a11 11 0 005.5 5.5L14 17l7 3v2a1 1 0 01-1 1C10 23 1 14 1 4a1 1 0 011-1z" />
                                </svg>
                                +855 12 345 678
                            </li>

                            <!-- Email -->
                            <li class="flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 12H8m8 0l-8 0m8 0a4 4 0 10-8 0 4 4 0 008 0z" />
                                </svg>
                                info@school.edu.kh
                            </li>

                        </ul>
                    </div>
                </div>

                <!-- Bottom -->
                <div class="mt-10 border-t border-slate-700 pt-6 text-center text-sm text-slate-500">
                    © {{ date('Y') }} {{ $schoolName }}. All rights reserved.
                </div>

            </div>
        </footer>

        <!-- Static chatbot -->
        <div id="school-chatbot" class="fixed bottom-5 right-5 z-50">
            <button id="chatbot-toggle" type="button"
                class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-cyan-600 text-white shadow-xl transition hover:bg-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-200"
                aria-expanded="false" aria-controls="chatbot-panel" aria-label="Open chatbot">
                <span class="chatbot-robot" aria-hidden="true">
                    <span class="chatbot-antenna"></span>
                    <span class="chatbot-head">
                        <span class="chatbot-face">
                            <span class="chatbot-eye"></span>
                            <span class="chatbot-eye chatbot-eye--right"></span>
                        </span>
                        <span class="chatbot-mouth"></span>
                    </span>
                </span>
            </button>

            <section id="chatbot-panel"
                class="mt-3 hidden w-[min(92vw,360px)] overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl"
                aria-live="polite">
                <header class="bg-slate-900 px-4 py-3 text-white">
                    <div class="flex items-center gap-3">
                        <span class="chatbot-robot chatbot-robot--sm shrink-0" aria-hidden="true">
                            <span class="chatbot-antenna"></span>
                            <span class="chatbot-head">
                                <span class="chatbot-face">
                                    <span class="chatbot-eye"></span>
                                    <span class="chatbot-eye chatbot-eye--right"></span>
                                </span>
                                <span class="chatbot-mouth"></span>
                            </span>
                        </span>
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-cyan-300">Assistant</p>
                            <h3 class="mt-1 text-sm font-semibold">School Q&A Chatbot</h3>
                        </div>
                    </div>
                </header>

                <div id="chatbot-messages" class="chatbot-scroll h-80 space-y-3 overflow-y-auto bg-slate-50 px-3 py-3">
                </div>

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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            requestAnimationFrame(() => {
                document.body.classList.add('page-ready');
            });

            document.body.classList.add('reveal-ready');
            const items = document.querySelectorAll('[data-reveal]');
            const firstFold = document.querySelectorAll('[data-first][data-reveal]');
            firstFold.forEach((el) => el.classList.add('is-visible'));
            if (!('IntersectionObserver' in window)) {
                items.forEach((el) => el.classList.add('is-visible'));
                return;
            }
            const io = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        io.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.14, rootMargin: '0px 0px -6% 0px' });

            items.forEach((el) => io.observe(el));

            const chatbot = document.getElementById('school-chatbot');
            if (!chatbot) {
                return;
            }

            const schoolName = @json($schoolName);
            const toggle = document.getElementById('chatbot-toggle');
            const panel = document.getElementById('chatbot-panel');
            const form = document.getElementById('chatbot-form');
            const input = document.getElementById('chatbot-input');
            const messages = document.getElementById('chatbot-messages');
            const quickWrap = document.getElementById('chatbot-quick-questions');

            const quickQuestions = [
                'How can I apply for admission?',
                'What programs are available?',
                'What are the office hours?',
                'How do I contact the school?',
                'Can parents track student progress online?',
                'Do teachers manage attendance in the system?'
            ];

            const answers = [
                {
                    keywords: ['admission', 'apply', 'enroll', 'registration', 'register'],
                    answer: `You can apply online from the Admission section, then submit student details and required documents. The school team reviews your request and confirms enrollment.`
                },
                {
                    keywords: ['program', 'primary', 'secondary', 'club', 'sports', 'curricular'],
                    answer: `${schoolName} offers Primary, Lower Secondary, Upper Secondary, and Co-Curricular programs including clubs, arts, and sports.`
                },
                {
                    keywords: ['contact', 'phone', 'email', 'address', 'location'],
                    answer: `Contact admissions by the website contact form, phone (+855 XXX XXX XXX), or email (admissions@schooli.edu). Campus location is Phnom Penh, Cambodia.`
                },
                {
                    keywords: ['hour', 'time', 'open', 'office', 'schedule'],
                    answer: `Office hours are Monday to Friday, 8:00 AM to 5:00 PM.`
                },
                {
                    keywords: ['parent', 'progress', 'report', 'attendance'],
                    answer: `Yes. Parents can track attendance, notices, and academic progress through the platform.`
                },
                {
                    keywords: ['teacher', 'attendance', 'manage'],
                    answer: `Yes. Teachers can record daily attendance and view attendance history reports in the system.`
                },
                {
                    keywords: ['role', 'permission', 'dashboard', 'admin', 'student'],
                    answer: `The platform uses role-based access with separate dashboards for admin, teachers, and students.`
                },
                {
                    keywords: ['hello', 'hi', 'hey'],
                    answer: `Hello! Ask me about admission, programs, office hours, contact details, or student tracking.`
                }
            ];

            const normalize = (text) => text.toLowerCase().replace(/[^a-z0-9\s]/g, ' ').trim();

            const addMessage = (role, text) => {
                const row = document.createElement('div');
                row.className = role === 'user' ? 'flex justify-end' : 'flex justify-start';

                const bubble = document.createElement('div');
                bubble.className = role === 'user'
                    ? 'max-w-[85%] rounded-2xl rounded-br-sm bg-cyan-600 px-3 py-2 text-sm text-white'
                    : 'max-w-[85%] rounded-2xl rounded-bl-sm bg-white px-3 py-2 text-sm text-slate-700 ring-1 ring-slate-200';
                bubble.textContent = text;

                row.appendChild(bubble);
                messages.appendChild(row);
                messages.scrollTop = messages.scrollHeight;
                return bubble;
            };

            const pickAnswer = (question) => {
                const clean = normalize(question);
                if (!clean) {
                    return 'Please type a question so I can help you.';
                }

                let best = null;
                let bestScore = 0;
                for (const item of answers) {
                    let score = 0;
                    for (const key of item.keywords) {
                        if (clean.includes(key)) {
                            score += 1;
                        }
                    }
                    if (score > bestScore) {
                        best = item;
                        bestScore = score;
                    }
                }

                if (best && bestScore > 0) {
                    return best.answer;
                }

                return `I can help with admission, programs, contact details, office hours, and platform features. Please ask one of those topics.`;
            };

            const renderQuickQuestions = () => {
                quickWrap.innerHTML = '';
                const shuffled = [...quickQuestions].sort(() => Math.random() - 0.5).slice(0, 3);
                shuffled.forEach((q) => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:border-cyan-300 hover:text-cyan-700';
                    btn.textContent = q;
                    btn.addEventListener('click', () => sendQuestion(q));
                    quickWrap.appendChild(btn);
                });
            };

            const sendQuestion = (question) => {
                const text = (question || '').trim();
                if (!text) {
                    return;
                }

                addMessage('user', text);
                const thinkingBubble = addMessage('bot', 'Typing...');
                setTimeout(() => {
                    thinkingBubble.textContent = pickAnswer(text);
                    messages.scrollTop = messages.scrollHeight;
                }, 380);
                renderQuickQuestions();
                input.value = '';
            };

            toggle.addEventListener('click', () => {
                const isOpen = !panel.classList.contains('hidden');
                panel.classList.toggle('hidden', isOpen);
                toggle.setAttribute('aria-expanded', String(!isOpen));
                if (!isOpen) {
                    input.focus();
                }
            });

            form.addEventListener('submit', (event) => {
                event.preventDefault();
                sendQuestion(input.value);
            });

            addMessage('bot', `Hi, welcome to ${schoolName}. Ask me a question and I will answer automatically.`);
            renderQuickQuestions();
        });
    </script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>

</html>