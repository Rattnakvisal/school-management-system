document.addEventListener("DOMContentLoaded", () => {
    const page = document.getElementById("teacher-settings-page");
    if (!page) {
        return;
    }

    const navItems = Array.from(page.querySelectorAll("[data-settings-nav]"));
    const panels = Array.from(page.querySelectorAll("[data-settings-panel]"));
    const defaultTab = String(page.dataset.settingsDefaultTab || "profile");

    const activateTab = (tab) => {
        navItems.forEach((item) => {
            const active = item.dataset.settingsNav === tab;
            item.classList.toggle("border-indigo-200", active);
            item.classList.toggle("bg-indigo-50", active);
            item.classList.toggle("text-indigo-700", active);
            item.classList.toggle("text-slate-600", !active);
        });

        panels.forEach((panel) => {
            panel.classList.toggle("hidden", panel.dataset.settingsPanel !== tab);
        });
    };

    if (navItems.length > 0 && panels.length > 0) {
        activateTab(defaultTab);
        navItems.forEach((item) => {
            item.addEventListener("click", () => {
                activateTab(item.dataset.settingsNav || defaultTab);
            });
        });
    }

    const avatarInput = page.querySelector("#teacher_avatar_input");
    const avatarPreview = page.querySelector("#teacher_avatar_preview");
    const uploadAvatarBtn = page.querySelector("#teacher_upload_avatar_btn");
    const triggerUploadBtn = page.querySelector("#teacher_trigger_avatar_upload");
    const resetAvatarBtn = page.querySelector("#teacher_reset_avatar_btn");

    if (avatarInput && avatarPreview && uploadAvatarBtn && triggerUploadBtn && resetAvatarBtn) {
        let currentObjectUrl = null;
        const originalSrc = avatarPreview.dataset.originalSrc || avatarPreview.src;
        const fallbackSrc = avatarPreview.dataset.fallback || originalSrc;

        const revokeCurrentObjectUrl = () => {
            if (!currentObjectUrl) {
                return;
            }

            URL.revokeObjectURL(currentObjectUrl);
            currentObjectUrl = null;
        };

        const openFilePicker = () => avatarInput.click();

        uploadAvatarBtn.addEventListener("click", openFilePicker);
        triggerUploadBtn.addEventListener("click", openFilePicker);

        avatarInput.addEventListener("change", () => {
            const file = avatarInput.files && avatarInput.files[0] ? avatarInput.files[0] : null;
            if (!file) {
                return;
            }

            revokeCurrentObjectUrl();
            currentObjectUrl = URL.createObjectURL(file);
            avatarPreview.src = currentObjectUrl;
        });

        resetAvatarBtn.addEventListener("click", () => {
            revokeCurrentObjectUrl();
            avatarInput.value = "";
            avatarPreview.src = originalSrc || fallbackSrc;
        });
    }

    const classQuery = page.querySelector("#teacher_class_query");
    const classSelect = page.querySelector("#teacher_class_id");
    if (classQuery && classSelect) {
        const filterOptions = () => {
            const query = String(classQuery.value || "").trim().toLowerCase();
            Array.from(classSelect.options).forEach((option) => {
                option.hidden = query !== "" && !String(option.text || "").toLowerCase().includes(query);
            });
        };

        classQuery.addEventListener("input", filterOptions);
    }

    const dateInput = page.querySelector("#teacher_attendance_date");
    const quickDateButtons = Array.from(page.querySelectorAll("[data-teacher-date-quick]"));
    if (dateInput && quickDateButtons.length > 0) {
        const toISODate = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, "0");
            const day = String(date.getDate()).padStart(2, "0");
            return `${year}-${month}-${day}`;
        };

        quickDateButtons.forEach((button) => {
            button.addEventListener("click", () => {
                const now = new Date();
                const offset = button.dataset.teacherDateQuick === "yesterday" ? -1 : 0;
                now.setDate(now.getDate() + offset);
                dateInput.value = toISODate(now);
            });
        });
    }
});
