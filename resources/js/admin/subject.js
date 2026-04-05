document.addEventListener('DOMContentLoaded', () => {
    const hasSwal = typeof window.Swal !== 'undefined';
    const pageDataNode = document.getElementById('admin-subject-data');
    const pageData = {
        success: '',
    };

    if (pageDataNode) {
        try {
            const parsed = JSON.parse(pageDataNode.textContent || '{}');
            if (parsed && typeof parsed === 'object' && typeof parsed.success === 'string') {
                pageData.success = parsed.success;
            }
        } catch (error) {
            console.error('Unable to parse subject page data.', error);
        }
    }

    const confirmSubmit = (selector, buildConfig) => {
        document.querySelectorAll(selector).forEach((form) => {
            form.addEventListener('submit', (event) => {
                if (form.dataset.confirmed === '1') {
                    return;
                }

                event.preventDefault();
                const config = buildConfig(form);
                const proceed = () => {
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
                            proceed();
                        }
                    });

                    return;
                }

                if (window.confirm(`${config.title}\n\n${config.text}`)) {
                    proceed();
                }
            });
        });
    };

    confirmSubmit('.js-create-form', () => ({
        title: 'Create subject?',
        text: 'A new subject will be saved.',
        icon: 'question',
        confirmButtonText: 'Yes, create',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#4f46e5',
    }));

    confirmSubmit('.js-edit-form', (form) => ({
        title: 'Save changes?',
        text: `Update details for ${form.dataset.subject || 'this subject'}.`,
        icon: 'question',
        confirmButtonText: 'Yes, save',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#4f46e5',
    }));

    confirmSubmit('.js-status-form', (form) => ({
        title: 'Change subject status?',
        text: `This will ${form.dataset.action || 'change status'} for ${form.dataset.subject || 'this subject'}.`,
        icon: 'warning',
        confirmButtonText: 'Yes, continue',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#f59e0b',
    }));

    confirmSubmit('.js-delete-form', (form) => ({
        title: 'Delete subject?',
        text: `Delete ${form.dataset.subject || 'this subject'} permanently. This cannot be undone.`,
        icon: 'warning',
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#dc2626',
    }));

    if (pageData.success) {
        if (hasSwal) {
            window.Swal.fire({
                icon: 'success',
                title: 'Success',
                text: pageData.success,
                timer: 2200,
                showConfirmButton: false,
            });
        } else {
            window.alert(`Success\n\n${pageData.success}`);
        }
    }
});
