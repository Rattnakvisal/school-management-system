document.addEventListener('DOMContentLoaded', () => {
    const hasSwal = typeof window.Swal !== 'undefined';
    const pageDataNode = document.getElementById('teacher-attendance-data');
    const pageData = {
        attendanceAlerts: [],
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
            console.error('Unable to parse teacher attendance page data.', error);
        }
    }

    const statusFields = Array.from(document.querySelectorAll('.js-student-status'));
    const statusCards = Array.from(document.querySelectorAll('.js-attendance-card'));
    const statusCountTargets = Array.from(document.querySelectorAll('[data-count-for]'));
    const tableRows = Array.from(document.querySelectorAll('.js-attendance-row'));
    const cardTitle = document.getElementById('attendance-card-title');
    const cardCount = document.getElementById('attendance-card-count');
    const cardList = document.getElementById('attendance-card-list');
    const cardEmpty = document.getElementById('attendance-card-empty');

    let activeStatus = 'all';

    const activeBoxClasses = [
        'border-indigo-300',
        'bg-indigo-50',
        'text-indigo-700',
        'shadow-sm',
    ];
    const inactiveBoxClasses = [
        'border-slate-200',
        'bg-white',
        'text-slate-600',
    ];

    const syncStatusBoxes = (field) => {
        const row = field.closest('.js-attendance-row');
        if (!row) {
            return;
        }

        const selectedStatus = String(field.value || '').toLowerCase();
        const statusButtons = Array.from(row.querySelectorAll('.js-status-box'));

        statusButtons.forEach((button) => {
            const boxStatus = String(button.dataset.statusOption || '').toLowerCase();
            const isActive = boxStatus === selectedStatus;

            button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            activeBoxClasses.forEach((className) => {
                button.classList.toggle(className, isActive);
            });
            inactiveBoxClasses.forEach((className) => {
                button.classList.toggle(className, !isActive);
            });
        });
    };

    const syncAllStatusBoxes = () => {
        statusFields.forEach((field) => syncStatusBoxes(field));
    };

    const updateCardActiveStyles = () => {
        statusCards.forEach((card) => {
            const cardStatus = String(card.dataset.statusCard || 'all');
            if (cardStatus === activeStatus) {
                card.classList.add('ring-2', 'ring-indigo-300');
            } else {
                card.classList.remove('ring-2', 'ring-indigo-300');
            }
        });
    };

    const buildBuckets = () => {
        const buckets = {
            all: [],
            present: [],
            absent: [],
            late: [],
            excused: [],
        };

        tableRows.forEach((row) => {
            const name = String(row.dataset.studentName || '').trim();
            const statusSelect = row.querySelector('.js-student-status');
            const status = String(statusSelect?.value || '').toLowerCase();

            if (name !== '') {
                buckets.all.push(name);
                if (Array.isArray(buckets[status])) {
                    buckets[status].push(name);
                }
            }
        });

        return buckets;
    };

    const renderStudentListFromStatus = () => {
        if (!cardTitle || !cardCount || !cardList || !cardEmpty) {
            return;
        }

        const buckets = buildBuckets();
        const names = buckets[activeStatus] || [];

        statusCountTargets.forEach((target) => {
            const statusKey = String(target.getAttribute('data-count-for') || 'all');
            target.textContent = String((buckets[statusKey] || []).length);
        });

        const activeCard = statusCards.find((card) => String(card.dataset.statusCard || '') === activeStatus);
        const title = String(activeCard?.dataset.statusLabel || 'Students');

        cardTitle.textContent = title;
        cardCount.textContent = `${names.length} student(s)`;
        cardList.innerHTML = '';

        if (names.length === 0) {
            cardEmpty.classList.remove('hidden');
            return;
        }

        cardEmpty.classList.add('hidden');
        names.forEach((name, index) => {
            const li = document.createElement('li');
            li.className = 'rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700';
            li.textContent = `${index + 1}. ${name}`;
            cardList.appendChild(li);
        });
    };

    statusCards.forEach((card) => {
        card.addEventListener('click', () => {
            activeStatus = String(card.dataset.statusCard || 'all');
            updateCardActiveStyles();
            renderStudentListFromStatus();
        });
    });

    statusFields.forEach((field) => {
        field.addEventListener('change', () => {
            syncStatusBoxes(field);
            renderStudentListFromStatus();
        });
    });

    document.querySelectorAll('.js-status-box').forEach((button) => {
        button.addEventListener('click', () => {
            if (button.disabled) {
                return;
            }

            const row = button.closest('.js-attendance-row');
            if (!row) {
                return;
            }

            const field = row.querySelector('.js-student-status');
            if (!field || field.disabled) {
                return;
            }

            const status = String(button.dataset.statusOption || '').toLowerCase();
            const option = field.querySelector(`option[value="${status}"]`);
            if (!option) {
                return;
            }

            field.value = status;
            syncStatusBoxes(field);
            renderStudentListFromStatus();
        });
    });

    document.querySelectorAll('[data-set-all-status]').forEach((button) => {
        button.addEventListener('click', () => {
            const status = String(button.getAttribute('data-set-all-status') || '');
            if (!status) {
                return;
            }

            statusFields.forEach((field) => {
                const option = field.querySelector(`option[value="${status}"]`);
                if (option) {
                    field.value = status;
                    syncStatusBoxes(field);
                }
            });

            renderStudentListFromStatus();
        });
    });

    const showMessage = (kind, title, text, options = {}) => {
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

    const alertQueue = [];

    (Array.isArray(pageData.attendanceAlerts) ? pageData.attendanceAlerts : []).forEach((alert) => {
        if (!alert || typeof alert !== 'object') {
            return;
        }

        alertQueue.push({
            icon: 'success',
            title: String(alert.title || 'Attendance Checked'),
            text: String(alert.text || 'Your attendance has been checked.'),
        });
    });

    if (pageData.flash.error) {
        alertQueue.push({
            icon: 'error',
            title: 'Error',
            text: pageData.flash.error,
        });
    } else if (pageData.flash.warning) {
        alertQueue.push({
            icon: 'warning',
            title: 'Warning',
            text: pageData.flash.warning,
        });
    } else if (pageData.flash.success) {
        alertQueue.push({
            icon: 'success',
            title: 'Success',
            text: pageData.flash.success,
            timer: 2200,
            showConfirmButton: false,
        });
    }

    if (pageData.validationErrors.length > 0) {
        alertQueue.unshift({
            icon: 'error',
            title: 'Validation Error',
            text: pageData.validationErrors[0],
        });
    }

    if (alertQueue.length > 0) {
        document.querySelectorAll('.js-inline-flash').forEach((element) => {
            element.classList.add('hidden');
        });

        if (hasSwal) {
            alertQueue.reduce((chain, config) => {
                return chain.then(() => window.Swal.fire({
                    ...config,
                    confirmButtonColor: '#4f46e5',
                }));
            }, Promise.resolve());
        } else {
            alertQueue.forEach((config) => {
                showMessage(config.icon || 'info', config.title || 'Message', config.text || '');
            });
        }
    }

    syncAllStatusBoxes();
    updateCardActiveStyles();
    renderStudentListFromStatus();
});
