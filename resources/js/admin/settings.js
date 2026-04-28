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

                if (active) {
                    const group = item.closest('[data-settings-nav-group]');
                    if (group) {
                        group.open = true;
                    }
                }
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

    const brandLogoInput = page.querySelector('#home_brand_logo_input');
    const brandLogoPreview = page.querySelector('#home_brand_logo_preview');
    const uploadBrandLogoBtn = page.querySelector('#upload_home_brand_logo_btn');
    const deleteBrandLogoBtn = page.querySelector('#delete_home_brand_logo_btn');
    const removeBrandLogoField = page.querySelector('#remove_home_brand_logo');

    if (
        brandLogoInput &&
        brandLogoPreview &&
        uploadBrandLogoBtn &&
        deleteBrandLogoBtn &&
        removeBrandLogoField
    ) {
        let currentBrandLogoObjectUrl = null;
        const defaultBrandLogo = '/images/techbridge-logo-mark.svg';

        const revokeCurrentBrandLogoObjectUrl = () => {
            if (!currentBrandLogoObjectUrl) {
                return;
            }

            URL.revokeObjectURL(currentBrandLogoObjectUrl);
            currentBrandLogoObjectUrl = null;
        };

        uploadBrandLogoBtn.addEventListener('click', () => brandLogoInput.click());
        brandLogoPreview.addEventListener('click', () => brandLogoInput.click());

        brandLogoInput.addEventListener('change', () => {
            const file = brandLogoInput.files && brandLogoInput.files[0] ? brandLogoInput.files[0] : null;
            if (!file) {
                return;
            }

            revokeCurrentBrandLogoObjectUrl();
            currentBrandLogoObjectUrl = URL.createObjectURL(file);
            brandLogoPreview.src = currentBrandLogoObjectUrl;
            removeBrandLogoField.value = '0';
        });

        deleteBrandLogoBtn.addEventListener('click', () => {
            revokeCurrentBrandLogoObjectUrl();
            brandLogoInput.value = '';
            brandLogoPreview.src = defaultBrandLogo;
            removeBrandLogoField.value = '1';
        });
    }

    const facilityImageInput = page.querySelector('#facility_image_input');
    const facilityImagePreview = page.querySelector('#facility_image_preview');
    const uploadFacilityImageBtn = page.querySelector('#upload_facility_image_btn');
    const deleteFacilityImageBtn = page.querySelector('#delete_facility_image_btn');
    const removeFacilityImageField = page.querySelector('#remove_facility_image');

    if (
        facilityImageInput &&
        facilityImagePreview &&
        uploadFacilityImageBtn &&
        deleteFacilityImageBtn &&
        removeFacilityImageField
    ) {
        let currentFacilityObjectUrl = null;
        const defaultFacilityImage = '/images/study.jpg';

        const revokeCurrentFacilityObjectUrl = () => {
            if (!currentFacilityObjectUrl) {
                return;
            }

            URL.revokeObjectURL(currentFacilityObjectUrl);
            currentFacilityObjectUrl = null;
        };

        uploadFacilityImageBtn.addEventListener('click', () => facilityImageInput.click());

        facilityImageInput.addEventListener('change', () => {
            const file = facilityImageInput.files && facilityImageInput.files[0] ? facilityImageInput.files[0] : null;
            if (!file) {
                return;
            }

            revokeCurrentFacilityObjectUrl();
            currentFacilityObjectUrl = URL.createObjectURL(file);
            facilityImagePreview.src = currentFacilityObjectUrl;
            removeFacilityImageField.value = '0';
        });

        deleteFacilityImageBtn.addEventListener('click', () => {
            revokeCurrentFacilityObjectUrl();
            facilityImageInput.value = '';
            facilityImagePreview.src = defaultFacilityImage;
            removeFacilityImageField.value = '1';
        });
    }

    const footerLogoInput = page.querySelector('#footer_logo_input');
    const footerLogoPreview = page.querySelector('#footer_logo_preview');
    const uploadFooterLogoBtn = page.querySelector('#upload_footer_logo_btn');
    const deleteFooterLogoBtn = page.querySelector('#delete_footer_logo_btn');
    const removeFooterLogoField = page.querySelector('#remove_footer_logo');

    if (
        footerLogoInput &&
        footerLogoPreview &&
        uploadFooterLogoBtn &&
        deleteFooterLogoBtn &&
        removeFooterLogoField
    ) {
        let currentFooterObjectUrl = null;
        const defaultFooterLogo = '/images/techbridge-logo-mark.svg';

        const revokeCurrentFooterObjectUrl = () => {
            if (!currentFooterObjectUrl) {
                return;
            }

            URL.revokeObjectURL(currentFooterObjectUrl);
            currentFooterObjectUrl = null;
        };

        uploadFooterLogoBtn.addEventListener('click', () => footerLogoInput.click());

        footerLogoInput.addEventListener('change', () => {
            const file = footerLogoInput.files && footerLogoInput.files[0] ? footerLogoInput.files[0] : null;
            if (!file) {
                return;
            }

            revokeCurrentFooterObjectUrl();
            currentFooterObjectUrl = URL.createObjectURL(file);
            footerLogoPreview.src = currentFooterObjectUrl;
            removeFooterLogoField.value = '0';
        });

        deleteFooterLogoBtn.addEventListener('click', () => {
            revokeCurrentFooterObjectUrl();
            footerLogoInput.value = '';
            footerLogoPreview.src = defaultFooterLogo;
            removeFooterLogoField.value = '1';
        });
    }

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
        card.querySelectorAll('select').forEach((field) => {
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
    const statColorDefaults = ['#10b981', '#2563eb', '#7c3aed', '#d97706', '#e11d48', '#0891b2'];

    const syncStatPreview = (card) => {
        const preview = card.querySelector('[data-stat-preview]');

        if (!preview) {
            return;
        }

        const iconSelect = card.querySelector('[data-stat-icon-select]');
        const colorSelect = card.querySelector('[data-stat-color-select], [data-stat-color-input]');
        const swatch = card.querySelector('[data-stat-preview-swatch]');
        const text = card.querySelector('[data-stat-preview-text]');
        const icon = iconSelect?.value || preview.dataset.icon || 'graduation';
        const color = colorSelect?.value || preview.dataset.color || 'blue';
        const detail = statIconDetails[icon] || statIconDetails.graduation;

        if (swatch) {
            swatch.className = `grid h-9 w-9 place-items-center rounded-xl text-white shadow-sm ${statColorClasses[color] || ''}`;
            if (/^#[0-9A-Fa-f]{6}$/.test(color)) {
                swatch.style.backgroundColor = color;
            } else {
                swatch.style.backgroundColor = '';
                swatch.className = `grid h-9 w-9 place-items-center rounded-xl text-white shadow-sm ${statColorClasses[color] || statColorClasses.blue}`;
            }
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
        card.querySelector('[data-stat-color-input]')?.addEventListener('input', () => syncStatPreview(card));
    });

    page.querySelectorAll('[data-navbar-page-select]').forEach((select) => {
        const card = select.closest('[data-home-editor-card]');
        const labelInput = card?.querySelector('[data-navbar-label-input]');

        const syncNavbarLabel = () => {
            const selectedOption = select.selectedOptions && select.selectedOptions[0] ? select.selectedOptions[0] : null;
            const label = selectedOption?.dataset.label || selectedOption?.textContent?.trim() || '';

            if (labelInput && label && (!labelInput.value.trim() || labelInput.dataset.syncedFromSelect === 'true')) {
                labelInput.value = label;
                labelInput.dataset.syncedFromSelect = 'true';
            }
        };

        labelInput?.addEventListener('input', () => {
            labelInput.dataset.syncedFromSelect = 'false';
        });
        select.addEventListener('change', syncNavbarLabel);
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

            card.querySelectorAll('input, textarea, select').forEach((field) => {
                field.disabled = false;
            });

            const activeToggle = card.querySelector('[data-home-active-toggle]');
            if (activeToggle) {
                activeToggle.checked = true;
                syncActiveCardState(activeToggle);
            }

            if (group === 'hero-stats') {
                const icons = Object.keys(statIconDetails);
                const iconSelect = card.querySelector('[data-stat-icon-select]');
                const colorSelect = card.querySelector('[data-stat-color-select], [data-stat-color-input]');

                if (iconSelect) {
                    iconSelect.value = icons[Math.floor(Math.random() * icons.length)];
                }

                if (colorSelect) {
                    colorSelect.value = statColorDefaults[Math.floor(Math.random() * statColorDefaults.length)];
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

            const editable = card.querySelector('input[type="text"], textarea, select');
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
            card.querySelectorAll('input[type="text"], textarea, select, input[type="checkbox"]').forEach((field) => {
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
