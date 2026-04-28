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
