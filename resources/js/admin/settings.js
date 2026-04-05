document.addEventListener('DOMContentLoaded', () => {
    const page = document.getElementById('admin-settings-page');

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
});
