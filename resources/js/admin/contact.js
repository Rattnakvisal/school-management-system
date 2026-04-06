document.addEventListener('DOMContentLoaded', () => {
    const hasSwal = typeof window.Swal !== 'undefined';

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

    confirmSubmit('.js-read-form', (form) => ({
        title: 'Mark as read?',
        text: `Mark message from ${form.dataset.name || 'this sender'} as read.`,
        icon: 'question',
        confirmButtonText: 'Yes, mark read',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#4f46e5',
    }));

    confirmSubmit('.js-read-all-form', () => ({
        title: 'Mark all as read?',
        text: 'This will mark all unread contact messages as read.',
        icon: 'question',
        confirmButtonText: 'Yes, continue',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#4f46e5',
    }));

    confirmSubmit('.js-delete-form', (form) => ({
        title: 'Delete message?',
        text: `Delete contact message from ${form.dataset.name || 'this sender'}. This cannot be undone.`,
        icon: 'warning',
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#dc2626',
    }));
});
