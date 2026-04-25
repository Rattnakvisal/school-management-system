document.addEventListener('DOMContentLoaded', function () {
    const hasSwal = typeof window.Swal !== 'undefined';
    const pageDataNode = document.getElementById('admin-mission-data');
    const pageData = {
        validationErrors: [],
        flash: {
            success: '',
            warning: '',
            error: '',
        },
    };

    if (pageDataNode) {
        try {
            const parsed = JSON.parse(pageDataNode.textContent || '{}');

            if (parsed && typeof parsed === 'object') {
                Object.assign(pageData, parsed);
                pageData.flash = {
                    ...pageData.flash,
                    ...(parsed.flash && typeof parsed.flash === 'object' ? parsed.flash : {}),
                };
            }
        } catch (error) {
            console.error('Unable to parse mission page data.', error);
        }
    }

    const fireMessage = (kind, title, text, options = {}) => {
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

    const confirmSubmit = (selector, buildConfig) => {
        document.querySelectorAll(selector).forEach((form) => {
            form.addEventListener('submit', function (event) {
                if (form.dataset.confirmed === '1') {
                    return;
                }

                event.preventDefault();

                const config = buildConfig(form);
                const submitForm = () => {
                    form.dataset.confirmed = '1';
                    form.submit();
                };

                if (hasSwal) {
                    window.Swal.fire({
                        title: config.title,
                        text: config.text,
                        icon: config.icon,
                        showCancelButton: true,
                        confirmButtonText: config.confirmButtonText,
                        cancelButtonText: config.cancelButtonText,
                        confirmButtonColor: config.confirmButtonColor,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            submitForm();
                        }
                    });
                    return;
                }

                if (window.confirm(`${config.title}\n\n${config.text}`)) {
                    submitForm();
                }
            });
        });
    };

    confirmSubmit('.js-create-form', () => ({
        title: 'Send mission event?',
        text: 'This mission event will be saved and sent to the selected audience.',
        icon: 'question',
        confirmButtonText: 'Yes, send',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#4f46e5',
    }));

    confirmSubmit('.js-edit-form', (form) => ({
        title: 'Save mission changes?',
        text: `Update "${form.dataset.mission || 'this mission'}" with the new details.`,
        icon: 'question',
        confirmButtonText: 'Yes, save',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#4f46e5',
    }));

    confirmSubmit('.js-status-form', (form) => ({
        title: 'Change mission status?',
        text: `This will ${form.dataset.action || 'change status'} "${form.dataset.mission || 'this mission'}".`,
        icon: 'warning',
        confirmButtonText: 'Yes, continue',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#f59e0b',
    }));

    confirmSubmit('.js-delete-form', (form) => ({
        title: 'Delete mission event?',
        text: `Delete "${form.dataset.mission || 'this mission'}" permanently. This cannot be undone.`,
        icon: 'warning',
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#dc2626',
    }));

    if (pageData.validationErrors.length > 0) {
        fireMessage('error', 'Validation Error', pageData.validationErrors[0]);
        return;
    }

    if (pageData.flash.error) {
        fireMessage('error', 'Error', pageData.flash.error);
    } else if (pageData.flash.warning) {
        fireMessage('warning', 'Warning', pageData.flash.warning);
    } else if (pageData.flash.success) {
        fireMessage('success', 'Success', pageData.flash.success, {
            timer: 2200,
            showConfirmButton: false,
        });
    }
});
