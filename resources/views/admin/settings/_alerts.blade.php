        @if (session('success'))
            <div class="reveal rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-900/20 dark:text-emerald-300"
                style="--sd: 2;">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 dark:border-red-900/50 dark:bg-red-900/20 dark:text-red-300"
                style="--sd: 2;">
                {{ session('error') }}
            </div>
        @endif

        @if ($profileErrors->any())
            <div class="reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-900/20 dark:text-red-300"
                style="--sd: 2;">
                {{ $profileErrors->first() }}
            </div>
        @endif

        @if ($passwordErrors->any())
            <div class="reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-900/20 dark:text-red-300"
                style="--sd: 2;">
                {{ $passwordErrors->first() }}
            </div>
        @endif

        @if ($navbarPageErrors->any())
            <div class="reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-900/20 dark:text-red-300"
                style="--sd: 2;">
                {{ $navbarPageErrors->first() }}
            </div>
        @endif

        @if ($homePageErrors->any())
            <div class="reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-900/20 dark:text-red-300"
                style="--sd: 2;">
                {{ $homePageErrors->first() }}
            </div>
        @endif

        @if ($aboutPageErrors->any())
            <div class="reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-900/20 dark:text-red-300"
                style="--sd: 2;">
                {{ $aboutPageErrors->first() }}
            </div>
        @endif

        @if ($programPageErrors->any())
            <div class="reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-900/20 dark:text-red-300"
                style="--sd: 2;">
                {{ $programPageErrors->first() }}
            </div>
        @endif

        @if ($faqPageErrors->any())
            <div class="reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-900/20 dark:text-red-300"
                style="--sd: 2;">
                {{ $faqPageErrors->first() }}
            </div>
        @endif

        @if ($contactPageErrors->any())
            <div class="reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-900/20 dark:text-red-300"
                style="--sd: 2;">
                {{ $contactPageErrors->first() }}
            </div>
        @endif

        @if ($footerPageErrors->any())
            <div class="reveal rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-900/20 dark:text-red-300"
                style="--sd: 2;">
                {{ $footerPageErrors->first() }}
            </div>
        @endif
