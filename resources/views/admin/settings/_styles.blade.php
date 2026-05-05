    <style>
        #admin-settings-page .settings-sidebar-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: rgba(99, 102, 241, 0.35) transparent;
            overscroll-behavior: contain;
        }

        #admin-settings-page .settings-sidebar-scrollbar::-webkit-scrollbar {
            width: 5px;
        }

        #admin-settings-page .settings-sidebar-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        #admin-settings-page .settings-sidebar-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(99, 102, 241, 0.32);
            border-radius: 999px;
        }

        #admin-settings-page .settings-sidebar-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(79, 70, 229, 0.55);
        }

        #admin-settings-page input[type="color"] {
            height: 2.5rem;
            min-width: 3rem;
            cursor: pointer;
            border-color: #bfdbfe;
            background: #eff6ff;
            padding: 0.25rem;
        }

        #admin-settings-page .settings-sidebar-shell {
            scrollbar-width: none;
        }

        #admin-settings-page .settings-sidebar-shell::-webkit-scrollbar {
            display: none;
        }

        #admin-settings-page[data-homepage-ui="1"] {
            --home-editor-accent: #2563eb;
            --home-editor-accent-soft: #dbeafe;
            --home-editor-ink: #0f172a;
            --home-editor-muted: #64748b;
            --home-editor-line: #dbe4f0;
            --home-editor-surface: #ffffff;
            --home-editor-soft: #f8fafc;
        }

        #admin-settings-page[data-homepage-ui="1"] > section {
            border-color: #dbe4f0;
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, 0.07), transparent 28rem),
                linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            box-shadow: 0 24px 80px -48px rgba(15, 23, 42, 0.45);
        }

        #admin-settings-page[data-homepage-ui="1"] .settings-sidebar-shell {
            border-color: rgba(148, 163, 184, 0.35);
            background: rgba(255, 255, 255, 0.82);
            box-shadow: 0 18px 50px -36px rgba(15, 23, 42, 0.45);
        }

        #admin-settings-page[data-homepage-ui="1"] .settings-nav-item {
            min-height: 2.75rem;
            border-radius: 0.9rem;
            color: #475569;
        }

        #admin-settings-page[data-homepage-ui="1"] .settings-nav-item:hover {
            border-color: #bfdbfe;
            background: #eff6ff;
            color: #1d4ed8;
        }

        #admin-settings-page[data-homepage-ui="1"] [data-settings-panel] > form {
            display: grid;
            gap: 1.25rem;
        }

        #admin-settings-page[data-homepage-ui="1"] [data-settings-panel] > form > div:not(.sticky) {
            overflow: hidden;
            border: 1px solid var(--home-editor-line);
            border-radius: 1.35rem;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.92));
            box-shadow: 0 18px 45px -34px rgba(15, 23, 42, 0.55);
        }

        #admin-settings-page[data-homepage-ui="1"] [data-settings-panel] > form > div:not(.sticky) > :first-child:not(.grid) {
            padding-top: 0.15rem;
        }

        #admin-settings-page[data-homepage-ui="1"] h2,
        #admin-settings-page[data-homepage-ui="1"] h3 {
            color: var(--home-editor-ink);
            letter-spacing: 0;
        }

        #admin-settings-page[data-homepage-ui="1"] label {
            color: #64748b;
        }

        #admin-settings-page[data-homepage-ui="1"] input[type="text"],
        #admin-settings-page[data-homepage-ui="1"] input[type="email"],
        #admin-settings-page[data-homepage-ui="1"] input[type="number"],
        #admin-settings-page[data-homepage-ui="1"] input[type="url"],
        #admin-settings-page[data-homepage-ui="1"] textarea,
        #admin-settings-page[data-homepage-ui="1"] select {
            border-color: #cbd5e1;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
            transition: border-color 160ms ease, box-shadow 160ms ease, background-color 160ms ease;
        }

        #admin-settings-page[data-homepage-ui="1"] input[type="text"]:focus,
        #admin-settings-page[data-homepage-ui="1"] input[type="email"]:focus,
        #admin-settings-page[data-homepage-ui="1"] input[type="number"]:focus,
        #admin-settings-page[data-homepage-ui="1"] input[type="url"]:focus,
        #admin-settings-page[data-homepage-ui="1"] textarea:focus,
        #admin-settings-page[data-homepage-ui="1"] select:focus {
            border-color: var(--home-editor-accent);
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }

        #admin-settings-page[data-homepage-ui="1"] [data-home-editor-card] {
            position: relative;
            border-color: #dbe4f0;
            border-radius: 1rem;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 14px 34px -30px rgba(15, 23, 42, 0.55);
            transition: transform 160ms ease, box-shadow 160ms ease, border-color 160ms ease;
        }

        #admin-settings-page[data-homepage-ui="1"] [data-home-editor-card]:hover {
            transform: translateY(-1px);
            border-color: #bfdbfe;
            box-shadow: 0 20px 46px -34px rgba(15, 23, 42, 0.62);
        }

        #admin-settings-page[data-homepage-ui="1"] [data-home-card-actions] {
            align-items: center;
        }

        #admin-settings-page[data-homepage-ui="1"] [data-add-home-card],
        #admin-settings-page[data-homepage-ui="1"] button[type="submit"] {
            border-radius: 0.9rem;
            background: #1d4ed8;
            box-shadow: 0 14px 28px -20px rgba(29, 78, 216, 0.72);
        }

        #admin-settings-page[data-homepage-ui="1"] [data-add-home-card]:hover,
        #admin-settings-page[data-homepage-ui="1"] button[type="submit"]:hover {
            background: #2563eb;
        }

        #admin-settings-page[data-homepage-ui="1"] [data-home-remove-card] {
            border-color: #fecaca;
            color: #b91c1c;
        }

        #admin-settings-page[data-homepage-ui="1"] [data-home-remove-card]:hover {
            background: #fef2f2;
        }

        #admin-settings-page[data-homepage-ui="1"] .sticky.bottom-4 > div {
            border-color: rgba(203, 213, 225, 0.86);
            border-radius: 1.1rem;
            background: rgba(255, 255, 255, 0.88);
            box-shadow: 0 24px 60px -34px rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(18px);
        }

        #admin-settings-page[data-homepage-ui="1"] [data-home-inactive-badge] {
            background: #e2e8f0;
            color: #64748b;
        }

        @media (max-width: 1279px) {
            #admin-settings-page .settings-sidebar-shell {
                max-height: calc(100vh - 4.75rem);
                overflow-y: auto;
            }

            #admin-settings-page .settings-sidebar-scrollbar {
                max-height: 16rem;
            }
        }
    </style>
