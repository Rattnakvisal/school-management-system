document.addEventListener('DOMContentLoaded', function () {
    const hasSwal = typeof window.Swal !== 'undefined';
    const pageDataNode = document.getElementById('admin-staff-data');
    const pageData = {
        validationErrors: [],
        flash: {
            error: '',
            warning: '',
            success: '',
        },
    };

    if (pageDataNode) {
        try {
            const parsed = JSON.parse(pageDataNode.textContent || '{}');

            if (Array.isArray(parsed.validationErrors)) {
                pageData.validationErrors = parsed.validationErrors;
            }

            if (parsed.flash && typeof parsed.flash === 'object') {
                pageData.flash = {
                    ...pageData.flash,
                    ...parsed.flash,
                };
            }
        } catch (error) {
            console.error('Unable to parse admin/staff page data.', error);
        }
    }

    const submitWithConfirm = (form) => {
        if (form.dataset.confirmed === '1') {
            return true;
        }

        const title = form.dataset.title || 'Confirm action';
        const text = form.dataset.text || 'Are you sure you want to continue?';
        const icon = form.dataset.icon || 'question';
        const confirmButtonText = form.dataset.confirmButtonText || 'Continue';
        const cancelButtonText = form.dataset.cancelButtonText || 'Cancel';
        const confirmButtonColor = form.dataset.confirmButtonColor || '#4f46e5';
        const proceed = () => {
            form.dataset.confirmed = '1';
            form.submit();
        };

        if (hasSwal) {
            window.Swal.fire({
                title,
                text,
                icon,
                showCancelButton: true,
                confirmButtonText,
                cancelButtonText,
                confirmButtonColor,
            }).then((result) => {
                if (result.isConfirmed) {
                    proceed();
                }
            });

            return false;
        }

        if (window.confirm(`${title}\n\n${text}`)) {
            proceed();
        }

        return false;
    };

    const confirmSubmit = (selector, buildConfig) => {
        document.querySelectorAll(selector).forEach((form) => {
            form.addEventListener('submit', function (event) {
                if (form.dataset.confirmed === '1') {
                    return;
                }

                event.preventDefault();
                const config = buildConfig(form);
                form.dataset.title = config.title;
                form.dataset.text = config.text;
                form.dataset.icon = config.icon;
                form.dataset.confirmButtonText = config.confirmButtonText;
                form.dataset.cancelButtonText = config.cancelButtonText;
                form.dataset.confirmButtonColor = config.confirmButtonColor;
                submitWithConfirm(form);
            });
        });
    };

    confirmSubmit('.js-create-form', () => ({
        title: 'Create account?',
        text: 'A new admin or staff account will be saved.',
        icon: 'question',
        confirmButtonText: 'Yes, create',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#4f46e5',
    }));

    confirmSubmit('.js-edit-form', (form) => ({
        title: 'Save changes?',
        text: `Update profile for ${form.dataset.account || 'this account'}.`,
        icon: 'question',
        confirmButtonText: 'Yes, save',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#4f46e5',
    }));

    confirmSubmit('.js-status-form', (form) => ({
        title: 'Change account status?',
        text: `This will ${form.dataset.action || 'change status'} for ${form.dataset.account || 'the account'}.`,
        icon: 'warning',
        confirmButtonText: 'Yes, continue',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#f59e0b',
    }));

    confirmSubmit('.js-delete-form', (form) => ({
        title: 'Delete account?',
        text: `Delete ${form.dataset.account || 'this account'} permanently. This cannot be undone.`,
        icon: 'warning',
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#dc2626',
    }));

    const showFlash = (kind, title, text, options = {}) => {
        if (!text) {
            return;
        }

        if (hasSwal) {
            window.Swal.fire({
                icon: kind,
                title,
                text,
                ...options,
            });
            return;
        }

        window.alert(`${title}\n\n${text}`);
    };

    if (pageData.validationErrors.length > 0) {
        showFlash('error', 'Validation Error', pageData.validationErrors[0]);
        return;
    }

    if (pageData.flash.error) {
        showFlash('error', 'Error', pageData.flash.error);
    } else if (pageData.flash.warning) {
        showFlash('warning', 'Warning', pageData.flash.warning);
    } else if (pageData.flash.success) {
        showFlash('success', 'Success', pageData.flash.success, {
            timer: 2200,
            showConfirmButton: false,
        });
    }
});
