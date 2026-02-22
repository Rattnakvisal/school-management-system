<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $schoolName ?? 'Schooli' }} | School Management System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html { scroll-behavior: smooth; }

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
            background: linear-gradient(120deg, transparent, rgba(255,255,255,.24), transparent);
            transform: rotate(18deg);
            animation: site-shimmer 8s linear infinite;
            pointer-events: none;
            z-index: 1;
        }

        .dot-pattern {
            background-image: radial-gradient(rgba(148, 163, 184, 0.32) 1px, transparent 1px);
            background-size: 12px 12px;
        }

        @keyframes site-shimmer {
            from { transform: translateX(-40%) rotate(18deg); }
            to { transform: translateX(220%) rotate(18deg); }
        }
    </style>
</head>
<body class="site-stage min-h-screen bg-slate-50 text-slate-900" x-data="{ open:false, top:false }" x-init="window.addEventListener('scroll', () => top = window.scrollY > 520)">
@php
    $schoolName = $schoolName ?? 'Schooli';
    $studentsTotal = $studentsTotal ?? 0;
    $teachersTotal = $teachersTotal ?? 0;
    $ratio = $teachersTotal > 0 ? number_format($studentsTotal / $teachersTotal, 1) : '0.0';
    $dashboardRoute = $dashboardRoute ?? 'home';
    $links = [
        ['#about', 'About'],
        ['#programs', 'Programs'],
        ['#facilities', 'Facilities'],
        ['#admission', 'Admission'],
        ['#faq', 'FAQ'],
        ['#contact', 'Contact'],
    ];
@endphp

<div class="relative overflow-hidden">
    <div class="pointer-events-none absolute inset-0">
        <div class="absolute -left-28 top-0 h-80 w-80 rounded-full bg-cyan-300/30 blur-3xl"></div>
        <div class="absolute right-0 top-20 h-96 w-96 rounded-full bg-indigo-300/20 blur-3xl"></div>
        <div class="absolute bottom-0 left-1/3 h-80 w-80 rounded-full bg-amber-200/30 blur-3xl"></div>
    </div>

    <header class="sticky top-0 z-50 border-b border-slate-200/80 bg-slate-50/90 backdrop-blur">
        <div class="mx-auto flex w-full max-w-7xl items-center justify-between gap-3 px-4 py-3 sm:px-6">
            <a href="{{ route('home') }}" class="site-reveal flex items-center gap-3" style="--sd: 1;">
                <span class="grid h-11 w-11 place-items-center rounded-2xl bg-slate-900 text-cyan-300 shadow-sm">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M12 2 2 7l10 5 10-5-10-5Zm0 7L2 4v9l10 5 10-5V4l-10 5Z" />
                    </svg>
                </span>
                <div>
                    <p class="text-2xl font-black leading-none tracking-tight">{{ $schoolName }}</p>
                    <p class="text-xs font-semibold text-slate-500">Smart School Management</p>
                </div>
            </a>

            <nav class="hidden items-center gap-6 text-sm font-semibold text-slate-600 lg:flex">
                @foreach($links as [$href, $label])
                    <a href="{{ $href }}" class="transition hover:text-slate-950">{{ $label }}</a>
                @endforeach
            </nav>

            <div class="flex items-center gap-2">
                <button @click="open = !open" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-700 lg:hidden" aria-label="Toggle menu">
                    <svg x-show="!open" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="open" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="site-reveal hidden items-center gap-2 md:flex" style="--sd: 2;">
                    @auth
                        <a href="{{ route($dashboardRoute) }}" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">Login</a>
                        <a href="{{ route('register') }}" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800">Register</a>
                    @endauth
                </div>
            </div>
        </div>

        <div x-show="open" x-cloak x-transition class="border-t border-slate-200 bg-white/95 lg:hidden">
            <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6">
                <div class="grid gap-2 text-sm font-semibold text-slate-700">
                    @foreach($links as [$href, $label])
                        <a @click="open=false" href="{{ $href }}" class="rounded-xl px-3 py-2 transition hover:bg-slate-100">{{ $label }}</a>
                    @endforeach
                </div>

                <div class="mt-3 grid gap-2">
                    @auth
                        <a href="{{ route($dashboardRoute) }}" class="rounded-xl bg-slate-900 px-4 py-2 text-center text-sm font-bold text-white">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-center text-sm font-semibold text-slate-700">Login</a>
                        <a href="{{ route('register') }}" class="rounded-xl bg-slate-900 px-4 py-2 text-center text-sm font-bold text-white">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <main class="relative z-10">
        <section class="mx-auto w-full max-w-7xl px-4 pb-10 pt-10 sm:px-6 lg:pb-14 lg:pt-14">
            <div class="grid items-center gap-8 lg:grid-cols-12 lg:gap-10">
                <div class="lg:col-span-6">
                    <span class="site-reveal inline-flex items-center gap-2 rounded-full bg-slate-900 px-3 py-1 text-xs font-bold uppercase tracking-wide text-cyan-300" style="--sd: 2;">
                        <span class="inline-block h-2 w-2 rounded-full bg-cyan-300"></span>
                        Future Ready School Platform
                    </span>

                    <h1 class="site-reveal mt-5 text-4xl font-black leading-tight text-slate-950 sm:text-5xl lg:text-6xl" style="--sd: 3;">
                        Build a Stronger
                        <span class="block text-cyan-700">School Community</span>
                    </h1>

                    <p class="site-reveal mt-5 max-w-xl text-base text-slate-600 sm:text-lg" style="--sd: 4;">
                        {{ $schoolName }} combines administration, teaching, and student life in one connected system. Track academics, attendance, communication, and performance from one dashboard.
                    </p>

                    <div class="site-reveal mt-7 flex flex-wrap items-center gap-3" style="--sd: 5;">
                        @auth
                            <a href="{{ route($dashboardRoute) }}" class="rounded-2xl bg-slate-900 px-6 py-3 text-sm font-bold text-white transition hover:bg-slate-800">Open Dashboard</a>
                        @else
                            <a href="{{ route('register') }}" class="rounded-2xl bg-slate-900 px-6 py-3 text-sm font-bold text-white transition hover:bg-slate-800">Start Enrollment</a>
                            <a href="{{ route('login') }}" class="rounded-2xl border border-slate-300 bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">Login to Portal</a>
                        @endauth
                    </div>

                    <div class="site-reveal mt-7 grid max-w-xl gap-3 sm:grid-cols-2" style="--sd: 6;">
                        <div class="rounded-2xl bg-white p-4 ring-1 ring-slate-200">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">School Type</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900">Primary to High School</p>
                        </div>
                        <div class="rounded-2xl bg-white p-4 ring-1 ring-slate-200">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Learning Model</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900">Academic + Activity Based</p>
                        </div>
                    </div>
                </div>

                <div class="site-reveal lg:col-span-6" style="--sd: 7;">
                    <div class="hero-shine relative overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl">
                        <img src="{{ asset('images/school.jpg') }}" alt="School campus" class="h-72 w-full object-cover sm:h-80 lg:h-[25rem]" />
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-900/20 to-transparent"></div>
                        <div class="absolute left-5 top-5 rounded-2xl bg-white/90 px-4 py-3 shadow-sm ring-1 ring-slate-200 backdrop-blur">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Student Teacher Ratio</p>
                            <p class="mt-1 text-2xl font-black text-slate-900">{{ $ratio }} : 1</p>
                        </div>
                        <div class="absolute bottom-5 left-5 right-5 rounded-2xl bg-slate-900/85 p-4 text-white ring-1 ring-white/15 backdrop-blur">
                            <p class="text-xs font-bold uppercase tracking-wide text-cyan-200">Operational Excellence</p>
                            <p class="mt-1 text-sm text-slate-100">Admissions, class management, attendance, and communications managed in one secure platform.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="site-reveal mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-4" style="--sd: 8;">
                <article class="site-float rounded-2xl bg-cyan-50 p-5 ring-1 ring-cyan-100">
                    <p class="text-xs font-bold uppercase tracking-wide text-cyan-700">Total Students</p>
                    <p class="mt-2 text-3xl font-black text-slate-950">{{ number_format($studentsTotal) }}</p>
                    <p class="mt-1 text-xs font-medium text-slate-600">Enrolled learners across grades</p>
                </article>
                <article class="site-float rounded-2xl bg-amber-50 p-5 ring-1 ring-amber-100">
                    <p class="text-xs font-bold uppercase tracking-wide text-amber-700">Total Teachers</p>
                    <p class="mt-2 text-3xl font-black text-slate-950">{{ number_format($teachersTotal) }}</p>
                    <p class="mt-1 text-xs font-medium text-slate-600">Professional teaching staff</p>
                </article>
                <article class="site-float rounded-2xl bg-emerald-50 p-5 ring-1 ring-emerald-100">
                    <p class="text-xs font-bold uppercase tracking-wide text-emerald-700">Portal Access</p>
                    <p class="mt-2 text-lg font-black text-slate-950">Admin, Teacher, Student</p>
                    <p class="mt-1 text-xs font-medium text-slate-600">Role-based dashboard controls</p>
                </article>
                <article class="site-float rounded-2xl bg-indigo-50 p-5 ring-1 ring-indigo-100">
                    <p class="text-xs font-bold uppercase tracking-wide text-indigo-700">Communication</p>
                    <p class="mt-2 text-lg font-black text-slate-950">Notices and Updates</p>
                    <p class="mt-1 text-xs font-medium text-slate-600">Fast school-wide announcements</p>
                </article>
            </div>
        </section>

        <section id="about" class="mx-auto w-full max-w-7xl px-4 pb-12 sm:px-6">
            <div class="site-reveal grid gap-6 rounded-3xl bg-white p-7 shadow-sm ring-1 ring-slate-200 lg:grid-cols-12" style="--sd: 9;">
                <div class="lg:col-span-7">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-cyan-700">About {{ $schoolName }}</p>
                    <h2 class="mt-2 text-3xl font-black text-slate-950">A complete learning ecosystem</h2>
                    <p class="mt-4 text-slate-600">
                        We focus on balanced education: strong academics, digital literacy, values, and student wellbeing. Our system supports families and staff with clear data, transparent progress, and structured school operations.
                    </p>

                    <div class="mt-6 grid gap-3 sm:grid-cols-2">
                        <article class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Mission</p>
                            <p class="mt-2 text-sm text-slate-700">Empower each learner with quality teaching, discipline, and confidence for lifelong success.</p>
                        </article>
                        <article class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Vision</p>
                            <p class="mt-2 text-sm text-slate-700">Build a modern school where technology and human guidance work together.</p>
                        </article>
                    </div>
                </div>

                <div class="lg:col-span-5">
                    <div class="dot-pattern h-full rounded-2xl bg-gradient-to-br from-slate-900 to-cyan-900 p-6 text-white">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-cyan-200">Principal Message</p>
                        <p class="mt-4 text-sm leading-7 text-slate-100">
                            Education is not only about passing exams. It is about character, curiosity, and responsibility. We prepare students to lead with knowledge and purpose.
                        </p>
                        <p class="mt-4 text-sm font-semibold text-cyan-200">- School Leadership Team</p>

                        <div class="mt-6 grid gap-3 sm:grid-cols-2">
                            <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/15">
                                <p class="text-xs font-bold text-cyan-200">Value 01</p>
                                <p class="mt-1 text-sm font-semibold">Respect and Discipline</p>
                            </div>
                            <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/15">
                                <p class="text-xs font-bold text-cyan-200">Value 02</p>
                                <p class="mt-1 text-sm font-semibold">Innovation and Growth</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="programs" class="mx-auto w-full max-w-7xl px-4 pb-12 sm:px-6">
            <div class="site-reveal" style="--sd: 10;">
                <div class="flex flex-wrap items-end justify-between gap-3">
                    <h2 class="text-3xl font-black text-slate-950">Academic Programs</h2>
                    <p class="text-sm text-slate-500">Structured progression from foundation to career preparation.</p>
                </div>

                <div class="mt-6 grid gap-4 lg:grid-cols-4">
                    @php
                        $programs = [
                            ['level' => 'Primary', 'title' => 'Foundation Learning', 'desc' => 'Literacy, numeracy, language, social and emotional growth.'],
                            ['level' => 'Lower Secondary', 'title' => 'Core Expansion', 'desc' => 'Stronger science, math, language, and collaborative projects.'],
                            ['level' => 'Upper Secondary', 'title' => 'Exam and Career Focus', 'desc' => 'Academic depth, mentorship, and university readiness support.'],
                            ['level' => 'Co-Curricular', 'title' => 'Arts, Sports, Clubs', 'desc' => 'Leadership, creativity, confidence, and teamwork development.'],
                        ];
                    @endphp

                    @foreach($programs as $item)
                        <article class="site-float rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                            <p class="text-xs font-bold uppercase tracking-wide text-cyan-700">{{ $item['level'] }}</p>
                            <h3 class="mt-2 text-xl font-black text-slate-900">{{ $item['title'] }}</h3>
                            <p class="mt-3 text-sm text-slate-600">{{ $item['desc'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="facilities" class="mx-auto w-full max-w-7xl px-4 pb-12 sm:px-6">
            <div class="site-reveal grid gap-6 rounded-3xl bg-white p-7 ring-1 ring-slate-200 lg:grid-cols-12" style="--sd: 11;">
                <div class="lg:col-span-5">
                    <h2 class="text-3xl font-black text-slate-950">Campus and Facilities</h2>
                    <p class="mt-3 text-slate-600">Safe spaces and modern resources for practical, engaging learning.</p>

                    <div class="mt-6 space-y-3">
                        <article class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-sm font-bold text-slate-900">Smart classrooms</p>
                            <p class="mt-1 text-sm text-slate-600">Interactive tools to support focused and visual instruction.</p>
                        </article>
                        <article class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-sm font-bold text-slate-900">Science and computer labs</p>
                            <p class="mt-1 text-sm text-slate-600">Hands-on learning for experimentation, coding, and research.</p>
                        </article>
                        <article class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-sm font-bold text-slate-900">Library and activity zones</p>
                            <p class="mt-1 text-sm text-slate-600">Quiet reading areas and open spaces for clubs and collaboration.</p>
                        </article>
                    </div>
                </div>

                <div class="lg:col-span-7">
                    <div class="overflow-hidden rounded-2xl border border-slate-200">
                        <img src="{{ asset('images/experience.jpg') }}" alt="Student learning experience" class="h-72 w-full object-cover lg:h-[26rem]" />
                    </div>
                </div>
            </div>
        </section>

        <section id="admission" class="mx-auto w-full max-w-7xl px-4 pb-12 sm:px-6">
            <div class="site-reveal rounded-3xl bg-slate-900 p-7 text-white sm:p-8" style="--sd: 12;">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-cyan-200">Admissions</p>
                        <h2 class="mt-2 text-3xl font-black">Simple Enrollment Process</h2>
                        <p class="mt-2 max-w-2xl text-sm text-slate-200">Easy steps for new families to join {{ $schoolName }}.</p>
                    </div>
                    <span class="rounded-full bg-white/10 px-4 py-2 text-xs font-bold ring-1 ring-white/20">Open Intake 2026</span>
                </div>

                <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <article class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/15">
                        <p class="text-xs font-bold text-cyan-200">Step 01</p>
                        <p class="mt-1 text-sm font-bold">Create account</p>
                        <p class="mt-1 text-xs text-slate-200">Register as parent or student in the portal.</p>
                    </article>
                    <article class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/15">
                        <p class="text-xs font-bold text-cyan-200">Step 02</p>
                        <p class="mt-1 text-sm font-bold">Submit information</p>
                        <p class="mt-1 text-xs text-slate-200">Upload profile and required school documents.</p>
                    </article>
                    <article class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/15">
                        <p class="text-xs font-bold text-cyan-200">Step 03</p>
                        <p class="mt-1 text-sm font-bold">Assessment and review</p>
                        <p class="mt-1 text-xs text-slate-200">Placement review with academic coordinator.</p>
                    </article>
                    <article class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/15">
                        <p class="text-xs font-bold text-cyan-200">Step 04</p>
                        <p class="mt-1 text-sm font-bold">Confirm enrollment</p>
                        <p class="mt-1 text-xs text-slate-200">Receive account activation and class schedule.</p>
                    </article>
                </div>

                <div class="mt-6">
                    @guest
                        <a href="{{ route('register') }}" class="inline-flex rounded-xl bg-cyan-300 px-5 py-3 text-sm font-black text-slate-900 transition hover:bg-cyan-200">Apply Now</a>
                    @else
                        <a href="{{ route($dashboardRoute) }}" class="inline-flex rounded-xl bg-cyan-300 px-5 py-3 text-sm font-black text-slate-900 transition hover:bg-cyan-200">Continue to Dashboard</a>
                    @endguest
                </div>
            </div>
        </section>

        <section id="faq" class="mx-auto w-full max-w-7xl px-4 pb-12 sm:px-6">
            <div class="site-reveal rounded-3xl bg-white p-7 ring-1 ring-slate-200" style="--sd: 13;">
                <h2 class="text-3xl font-black text-slate-950">Frequently Asked Questions</h2>
                <div class="mt-6 grid gap-3 lg:grid-cols-2">
                    <details class="rounded-2xl bg-slate-50 p-5">
                        <summary class="cursor-pointer text-sm font-black text-slate-900">Can parents track student progress online?</summary>
                        <p class="mt-2 text-sm text-slate-600">Yes. Parents can receive notices, attendance updates, and academic progress reports.</p>
                    </details>
                    <details class="rounded-2xl bg-slate-50 p-5">
                        <summary class="cursor-pointer text-sm font-black text-slate-900">Do teachers manage attendance in the system?</summary>
                        <p class="mt-2 text-sm text-slate-600">Yes. Attendance can be recorded daily with clear records for administration.</p>
                    </details>
                    <details class="rounded-2xl bg-slate-50 p-5">
                        <summary class="cursor-pointer text-sm font-black text-slate-900">Is this platform role based?</summary>
                        <p class="mt-2 text-sm text-slate-600">Yes. Admin, teacher, and student roles each have dedicated dashboards and permissions.</p>
                    </details>
                    <details class="rounded-2xl bg-slate-50 p-5">
                        <summary class="cursor-pointer text-sm font-black text-slate-900">How do we contact admissions?</summary>
                        <p class="mt-2 text-sm text-slate-600">Use the contact details below or create an account to submit questions directly.</p>
                    </details>
                </div>
            </div>
        </section>

        <section id="contact" class="mx-auto w-full max-w-7xl px-4 pb-16 sm:px-6 lg:pb-20">
            <div class="site-reveal grid gap-6 lg:grid-cols-12" style="--sd: 14;">
                <div class="rounded-3xl bg-white p-7 ring-1 ring-slate-200 lg:col-span-7">
                    <h2 class="text-3xl font-black text-slate-950">Contact Admissions</h2>
                    <p class="mt-3 text-slate-600">Send your message directly from this page. Admin can read it in the dashboard.</p>

                    @if (session('contact_success'))
                        <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                            {{ session('contact_success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('contact.store') }}" class="mt-6 space-y-4">
                        @csrf

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="contact_name" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Full Name</label>
                                <input id="contact_name" name="name" type="text" value="{{ old('name') }}"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-cyan-300 focus:ring-4 focus:ring-cyan-100"
                                    placeholder="Your full name">
                                @error('name')
                                    <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="contact_phone" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Phone (Optional)</label>
                                <input id="contact_phone" name="phone" type="text" value="{{ old('phone') }}"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-cyan-300 focus:ring-4 focus:ring-cyan-100"
                                    placeholder="+855 ...">
                                @error('phone')
                                    <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="contact_email" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Email</label>
                                <input id="contact_email" name="email" type="email" value="{{ old('email') }}"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-cyan-300 focus:ring-4 focus:ring-cyan-100"
                                    placeholder="you@example.com">
                                @error('email')
                                    <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="contact_subject" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Subject</label>
                                <input id="contact_subject" name="subject" type="text" value="{{ old('subject') }}"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-cyan-300 focus:ring-4 focus:ring-cyan-100"
                                    placeholder="Admission question">
                                @error('subject')
                                    <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="contact_message" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Message</label>
                            <textarea id="contact_message" name="message" rows="5"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-cyan-300 focus:ring-4 focus:ring-cyan-100"
                                placeholder="Write your message here...">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                            class="inline-flex rounded-xl bg-slate-900 px-5 py-3 text-sm font-black text-white transition hover:bg-slate-800">
                            Send Message
                        </button>
                    </form>
                    @if ($errors->any())
                        <p class="mt-3 text-xs font-semibold text-red-600">Please check the contact form and try again.</p>
                    @endif
                </div>

                <div class="rounded-3xl bg-gradient-to-br from-cyan-700 to-slate-900 p-7 text-white lg:col-span-5">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-cyan-200">Campus Information</p>
                    <h3 class="mt-2 text-2xl font-black">Visit or call our team</h3>
                    <p class="mt-3 text-sm text-cyan-100">We will help with admissions, programs, and school guidance.</p>

                    <div class="mt-5 space-y-3">
                        <article class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/15">
                            <p class="text-xs font-bold uppercase tracking-wide text-cyan-200">Address</p>
                            <p class="mt-1 text-sm font-semibold text-slate-100">Phnom Penh, Cambodia</p>
                        </article>
                        <article class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/15">
                            <p class="text-xs font-bold uppercase tracking-wide text-cyan-200">Phone</p>
                            <p class="mt-1 text-sm font-semibold text-slate-100">+855 XXX XXX XXX</p>
                        </article>
                        <article class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/15">
                            <p class="text-xs font-bold uppercase tracking-wide text-cyan-200">Email</p>
                            <p class="mt-1 text-sm font-semibold text-slate-100">admissions@schooli.edu</p>
                        </article>
                        <article class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/15">
                            <p class="text-xs font-bold uppercase tracking-wide text-cyan-200">Office Hours</p>
                            <p class="mt-1 text-sm font-semibold text-slate-100">Mon-Fri, 8:00 AM - 5:00 PM</p>
                        </article>
                    </div>

                    <div class="mt-6 space-y-3">
                        @guest
                            <a href="{{ route('register') }}" class="block rounded-xl bg-white px-4 py-3 text-center text-sm font-black text-slate-900 transition hover:bg-cyan-50">Create Account</a>
                            <a href="{{ route('login') }}" class="block rounded-xl border border-white/30 px-4 py-3 text-center text-sm font-semibold text-white transition hover:bg-white/10">Login</a>
                        @else
                            <a href="{{ route($dashboardRoute) }}" class="block rounded-xl bg-white px-4 py-3 text-center text-sm font-black text-slate-900 transition hover:bg-cyan-50">Open Dashboard</a>
                        @endguest
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-slate-200 bg-white/80">
        <div class="mx-auto flex w-full max-w-7xl flex-wrap items-center justify-between gap-3 px-4 py-5 text-sm text-slate-500 sm:px-6">
            <p>Copyright {{ date('Y') }} {{ $schoolName }}. All rights reserved.</p>
            <div class="flex items-center gap-4 font-semibold">
                <a href="#about" class="transition hover:text-slate-800">About</a>
                <a href="#admission" class="transition hover:text-slate-800">Admission</a>
                <a href="#contact" class="transition hover:text-slate-800">Contact</a>
            </div>
        </div>
    </footer>

    <button x-show="top" x-cloak x-transition @click="window.scrollTo({ top: 0, behavior: 'smooth' })" class="fixed bottom-5 right-5 z-50 rounded-2xl bg-slate-900 px-4 py-3 text-sm font-bold text-white shadow-lg transition hover:bg-slate-800">
        Top
    </button>
</div>
</body>
</html>
