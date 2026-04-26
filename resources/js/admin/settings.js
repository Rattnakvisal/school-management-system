document.addEventListener('DOMContentLoaded', () => {
    const page = document.getElementById('admin-settings-page') || document.getElementById('student-settings-page');

    if (!page) {
        return;
    }

    const navItems = Array.from(page.querySelectorAll('[data-settings-nav]'));
    const panels = Array.from(page.querySelectorAll('[data-settings-panel]'));
    const defaultTab = String(page.dataset.settingsDefaultTab || 'profile');

    if (navItems.length > 0 && panels.length > 0) {
        const activateTab = (tab) => {
            navItems.forEach((item) => {
                const active = item.dataset.settingsNav === tab;
                item.classList.toggle('border-indigo-200', active);
                item.classList.toggle('bg-indigo-50', active);
                item.classList.toggle('text-indigo-700', active);
                item.classList.toggle('text-slate-600', !active);
            });

            panels.forEach((panel) => {
                panel.classList.toggle('hidden', panel.dataset.settingsPanel !== tab);
            });
        };

        activateTab(defaultTab);

        navItems.forEach((item) => {
            item.addEventListener('click', () => {
                activateTab(item.dataset.settingsNav || defaultTab);
            });
        });
    }

    const avatarInput = page.querySelector('#admin_avatar_input');
    const avatarPreview = page.querySelector('#admin_avatar_preview');
    const uploadAvatarBtn = page.querySelector('#upload_avatar_btn');
    const triggerUploadBtn = page.querySelector('#trigger_avatar_upload');
    const deleteAvatarBtn = page.querySelector('#delete_avatar_btn');
    const removeAvatarField = page.querySelector('#remove_admin_avatar');

    if (
        !avatarInput ||
        !avatarPreview ||
        !uploadAvatarBtn ||
        !triggerUploadBtn ||
        !deleteAvatarBtn ||
        !removeAvatarField
    ) {
        return;
    }

    let currentObjectUrl = null;

    const revokeCurrentObjectUrl = () => {
        if (!currentObjectUrl) {
            return;
        }

        URL.revokeObjectURL(currentObjectUrl);
        currentObjectUrl = null;
    };

    const openFilePicker = () => avatarInput.click();

    uploadAvatarBtn.addEventListener('click', openFilePicker);
    triggerUploadBtn.addEventListener('click', openFilePicker);

    avatarInput.addEventListener('change', () => {
        const file = avatarInput.files && avatarInput.files[0] ? avatarInput.files[0] : null;
        if (!file) {
            return;
        }

        revokeCurrentObjectUrl();

        currentObjectUrl = URL.createObjectURL(file);
        avatarPreview.src = currentObjectUrl;
        removeAvatarField.value = '0';
    });

    deleteAvatarBtn.addEventListener('click', () => {
        revokeCurrentObjectUrl();

        avatarInput.value = '';
        avatarPreview.src = avatarPreview.dataset.fallback || avatarPreview.src;
        removeAvatarField.value = '1';
    });

    const homeImageInput = page.querySelector('#home_hero_image_input');
    const homeImagePreview = page.querySelector('#home_hero_image_preview');
    const uploadHomeImageBtn = page.querySelector('#upload_home_hero_image_btn');
    const deleteHomeImageBtn = page.querySelector('#delete_home_hero_image_btn');
    const removeHomeImageField = page.querySelector('#remove_home_hero_image');

    if (
        !homeImageInput ||
        !homeImagePreview ||
        !uploadHomeImageBtn ||
        !deleteHomeImageBtn ||
        !removeHomeImageField
    ) {
        return;
    }

    let currentHomeObjectUrl = null;
    const defaultHomeImage = '/images/school.jpg';

    const revokeCurrentHomeObjectUrl = () => {
        if (!currentHomeObjectUrl) {
            return;
        }

        URL.revokeObjectURL(currentHomeObjectUrl);
        currentHomeObjectUrl = null;
    };

    uploadHomeImageBtn.addEventListener('click', () => homeImageInput.click());

    homeImageInput.addEventListener('change', () => {
        const file = homeImageInput.files && homeImageInput.files[0] ? homeImageInput.files[0] : null;
        if (!file) {
            return;
        }

        revokeCurrentHomeObjectUrl();
        currentHomeObjectUrl = URL.createObjectURL(file);
        homeImagePreview.src = currentHomeObjectUrl;
        removeHomeImageField.value = '0';
    });

    deleteHomeImageBtn.addEventListener('click', () => {
        revokeCurrentHomeObjectUrl();
        homeImageInput.value = '';
        homeImagePreview.src = defaultHomeImage;
        removeHomeImageField.value = '1';
    });

    const previewInputs = Array.from(page.querySelectorAll('[data-home-preview-target]'));

    previewInputs.forEach((input) => {
        const target = page.querySelector(`#${input.dataset.homePreviewTarget}`);

        if (!target) {
            return;
        }

        const updatePreview = () => {
            target.textContent = input.value.trim() || input.placeholder || '';
        };

        input.addEventListener('input', updatePreview);
        updatePreview();
    });

    const syncActiveCardState = (checkbox) => {
        const card = checkbox.closest('[data-home-editor-card]');

        if (!card) {
            return;
        }

        const inactive = !checkbox.checked;
        card.classList.toggle('bg-slate-100', inactive);
        card.classList.toggle('border-dashed', inactive);
        card.classList.toggle('border-slate-300', inactive);
        card.classList.toggle('opacity-75', inactive);
        card.querySelectorAll('input[type="text"], textarea').forEach((field) => {
            field.classList.toggle('bg-slate-100', inactive);
            field.classList.toggle('text-slate-400', inactive);
        });

        let badge = card.querySelector('[data-home-inactive-badge]');
        if (inactive && !badge) {
            badge = document.createElement('span');
            badge.dataset.homeInactiveBadge = 'true';
            badge.className = 'inline-flex rounded-full bg-slate-200 px-3 py-1 text-xs font-bold text-slate-500';
            badge.textContent = 'Hidden from homepage';
            card.querySelector('[data-home-card-actions]')?.prepend(badge);
        } else if (!inactive && badge) {
            badge.remove();
        }
    };

    const statIconDetails = {
        graduation: { label: 'Graduation cap', detail: 'Students and learning', symbol: 'G' },
        chart: { label: 'Chart bars', detail: 'Growth and reporting', symbol: 'B' },
        users: { label: 'Users', detail: 'People and community', symbol: 'U' },
        clock: { label: 'Clock', detail: 'Time and access', symbol: 'T' },
        shield: { label: 'Shield', detail: 'Safety and trust', symbol: 'S' },
        calendar: { label: 'Calendar', detail: 'Schedule and planning', symbol: 'D' },
    };

    const statColorClasses = {
        emerald: 'bg-emerald-500',
        blue: 'bg-blue-600',
        violet: 'bg-violet-600',
        amber: 'bg-amber-500',
        rose: 'bg-rose-500',
        cyan: 'bg-cyan-500',
    };

    const syncStatPreview = (card) => {
        const preview = card.querySelector('[data-stat-preview]');

        if (!preview) {
            return;
        }

        const iconSelect = card.querySelector('[data-stat-icon-select]');
        const colorSelect = card.querySelector('[data-stat-color-select]');
        const swatch = card.querySelector('[data-stat-preview-swatch]');
        const text = card.querySelector('[data-stat-preview-text]');
        const icon = iconSelect?.value || preview.dataset.icon || 'graduation';
        const color = colorSelect?.value || preview.dataset.color || 'blue';
        const detail = statIconDetails[icon] || statIconDetails.graduation;

        if (swatch) {
            swatch.className = `grid h-9 w-9 place-items-center rounded-xl text-white shadow-sm ${statColorClasses[color] || statColorClasses.blue}`;
            swatch.textContent = detail.symbol;
        }

        if (text) {
            text.textContent = `${detail.label}: ${detail.detail}`;
        }
    };

    page.querySelectorAll('[data-home-active-toggle]').forEach((checkbox) => {
        syncActiveCardState(checkbox);
        checkbox.addEventListener('change', () => syncActiveCardState(checkbox));
    });

    page.querySelectorAll('[data-stat-preview]').forEach((preview) => {
        const card = preview.closest('[data-home-editor-card]');

        if (!card) {
            return;
        }

        syncStatPreview(card);
        card.querySelector('[data-stat-icon-select]')?.addEventListener('input', () => syncStatPreview(card));
        card.querySelector('[data-stat-color-select]')?.addEventListener('change', () => syncStatPreview(card));
    });

    page.querySelectorAll('[data-home-edit-card]').forEach((button) => {
        button.addEventListener('click', () => {
            const card = button.closest('[data-home-editor-card]');
            const editable = card?.querySelector('input[type="text"], textarea');

            editable?.focus();
            editable?.select?.();
        });
    });

    const syncAddButtonState = (group) => {
        const button = page.querySelector(`[data-add-home-card="${group}"]`);

        if (!button) {
            return;
        }

        const hasHiddenCard = Boolean(page.querySelector(`[data-addable-card="${group}"].hidden`));
        button.disabled = !hasHiddenCard;
        button.classList.toggle('opacity-50', !hasHiddenCard);
        button.classList.toggle('cursor-not-allowed', !hasHiddenCard);
    };

    page.querySelectorAll('[data-add-home-card]').forEach((button) => {
        const group = button.dataset.addHomeCard || '';

        syncAddButtonState(group);

        button.addEventListener('click', () => {
            const card = page.querySelector(`[data-addable-card="${group}"].hidden`);

            if (!card) {
                syncAddButtonState(group);
                return;
            }

            card.classList.remove('hidden', 'opacity-50', 'ring-2', 'ring-red-100');

            const deleteField = card.querySelector('[data-home-delete-field]');
            if (deleteField) {
                deleteField.value = '0';
            }

            card.querySelectorAll('input, textarea').forEach((field) => {
                field.disabled = false;
            });

            const activeToggle = card.querySelector('[data-home-active-toggle]');
            if (activeToggle) {
                activeToggle.checked = true;
                syncActiveCardState(activeToggle);
            }

            if (group === 'hero-stats') {
                const icons = Object.keys(statIconDetails);
                const colors = Object.keys(statColorClasses);
                const iconSelect = card.querySelector('[data-stat-icon-select]');
                const colorSelect = card.querySelector('[data-stat-color-select]');

                if (iconSelect) {
                    iconSelect.value = icons[Math.floor(Math.random() * icons.length)];
                }

                if (colorSelect) {
                    colorSelect.value = colors[Math.floor(Math.random() * colors.length)];
                }

                syncStatPreview(card);
            } else {
                const icons = Object.keys(statIconDetails);
                const iconInput = card.querySelector('[data-random-icon-input]');

                if (iconInput && !iconInput.value.trim()) {
                    iconInput.value = icons[Math.floor(Math.random() * icons.length)];
                }
            }

            const removeButton = card.querySelector('[data-home-remove-card]');
            if (removeButton) {
                removeButton.textContent = 'Remove';
                removeButton.disabled = false;
            }

            const editable = card.querySelector('input[type="text"], textarea');
            editable?.focus();
            syncAddButtonState(group);
        });
    });

    page.querySelectorAll('[data-home-remove-card]').forEach((button) => {
        button.addEventListener('click', () => {
            const card = button.closest('[data-home-editor-card]');
            const deleteField = card?.querySelector('[data-home-delete-field]');

            if (!card || !deleteField) {
                return;
            }

            deleteField.value = '1';
            card.classList.add('opacity-50', 'ring-2', 'ring-red-100');
            card.querySelectorAll('input[type="text"], textarea, input[type="checkbox"]').forEach((field) => {
                field.disabled = true;
            });
            button.textContent = 'Removed';
            button.disabled = true;

            const group = card.dataset.addableCard;
            if (group) {
                syncAddButtonState(group);
            }
        });
    });
});
