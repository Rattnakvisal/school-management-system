document.addEventListener('DOMContentLoaded', function () {
    const hasSwal = typeof window.Swal !== 'undefined';
    const pageDataNode = document.getElementById('admin-classes-data');
    const pageData = {
        periodOptions: {},
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

            if (parsed && typeof parsed === 'object') {
                Object.assign(pageData, parsed);
                pageData.flash = {
                    ...pageData.flash,
                    ...(parsed.flash && typeof parsed.flash === 'object'
                        ? parsed.flash
                        : {}),
                };
            }
        } catch (error) {
            console.error('Unable to parse class page data.', error);
        }
    }

    const periodOptions = pageData.periodOptions || {};

    const escapeHtml = (value) => {
        return String(value || '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#39;');
    };

    const periodOptionHtml = (selectedValue) => {
        return Object.entries(periodOptions)
            .map(([value, label]) => {
                const selected = String(value) === String(selectedValue) ? 'selected' : '';
                return `<option value="${escapeHtml(value)}" ${selected}>${escapeHtml(label)}</option>`;
            })
            .join('');
    };

    const buildSlotRowHtml = (namePrefix, index, defaults = {}) => {
        const period = defaults.period || 'morning';
        const startTime = defaults.start_time || '';
        const endTime = defaults.end_time || '';

        return `
            <div class="js-slot-row grid gap-2 rounded-xl border border-slate-200 bg-slate-50/80 p-2 sm:grid-cols-[1fr_1fr_1fr_auto]">
                <select name="${escapeHtml(namePrefix)}[${index}][period]"
                    class="rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                    ${periodOptionHtml(period)}
                </select>
                <input type="time" name="${escapeHtml(namePrefix)}[${index}][start_time]" value="${escapeHtml(startTime)}"
                    class="rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                <input type="time" name="${escapeHtml(namePrefix)}[${index}][end_time]" value="${escapeHtml(endTime)}"
                    class="rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                <button type="button"
                    class="js-remove-study-slot rounded-xl border border-rose-200 bg-rose-50 px-2.5 py-2 text-[11px] font-semibold text-rose-700 hover:bg-rose-100">
                    Remove
                </button>
            </div>
        `;
    };

    const syncRemoveButtons = (container) => {
        const rows = container.querySelectorAll('.js-slot-row');
        rows.forEach((row) => {
            const button = row.querySelector('.js-remove-study-slot');
            if (!button) {
                return;
            }

            if (rows.length <= 1) {
                button.classList.add('opacity-50', 'cursor-not-allowed');
                button.setAttribute('disabled', 'disabled');
            } else {
                button.classList.remove('opacity-50', 'cursor-not-allowed');
                button.removeAttribute('disabled');
            }
        });
    };

    document.querySelectorAll('.js-study-slots').forEach((container) => {
        syncRemoveButtons(container);

        container.addEventListener('click', function (event) {
            const removeButton = event.target.closest('.js-remove-study-slot');
            if (!removeButton) {
                return;
            }

            const rows = container.querySelectorAll('.js-slot-row');
            const row = removeButton.closest('.js-slot-row');
            if (!row) {
                return;
            }

            if (rows.length <= 1) {
                row.querySelectorAll('input[type="time"]').forEach((input) => {
                    input.value = '';
                });
                const select = row.querySelector('select');
                if (select) {
                    select.value = 'morning';
                }
                return;
            }

            row.remove();
            syncRemoveButtons(container);
        });
    });

    document.querySelectorAll('.js-add-study-slot').forEach((button) => {
        button.addEventListener('click', function () {
            const targetId = button.dataset.target || '';
            const container = targetId ? document.getElementById(targetId) : null;
            if (!container) {
                return;
            }

            const namePrefix = container.dataset.namePrefix || 'study_slots';
            const index = Number(container.dataset.nextIndex || 0);
            container.insertAdjacentHTML('beforeend', buildSlotRowHtml(namePrefix, index));
            container.dataset.nextIndex = String(index + 1);
            syncRemoveButtons(container);
        });
    });

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
        title: 'Create class?',
        text: 'A new class will be saved.',
        icon: 'question',
        confirmButtonText: 'Yes, create',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#4f46e5',
    }));

    confirmSubmit('.js-edit-form', (form) => ({
        title: 'Save changes?',
        text: `Update details for ${form.dataset.class || 'this class'}.`,
        icon: 'question',
        confirmButtonText: 'Yes, save',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#4f46e5',
    }));

    confirmSubmit('.js-status-form', (form) => ({
        title: 'Change class status?',
        text: `This will ${form.dataset.action || 'change status'} for ${form.dataset.class || 'this class'}.`,
        icon: 'warning',
        confirmButtonText: 'Yes, continue',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#f59e0b',
    }));

    confirmSubmit('.js-delete-form', (form) => ({
        title: 'Delete class?',
        text: `Delete ${form.dataset.class || 'this class'} permanently. This cannot be undone.`,
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
