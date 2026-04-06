document.addEventListener('DOMContentLoaded', () => {
    const root = document.querySelector('[data-teacher-notification-root]');
    if (!root) {
        return;
    }

    const pollUrl = String(root.dataset.notificationPollUrl || '').trim();
    if (!pollUrl) {
        return;
    }

    const badge = document.getElementById('teacher-notif-badge');
    const list = document.getElementById('teacher-notif-list');
    const emptyState = document.getElementById('teacher-notif-empty');
    const hasSwal = typeof window.Swal !== 'undefined';
    const state = {
        latestId: Number(root.dataset.notificationLatestId || 0) || 0,
        unreadCount: Number(root.dataset.notificationUnreadCount || 0) || 0,
    };

    const escapeHtml = (value) => {
        return String(value || '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    };

    const updateBadge = (count) => {
        state.unreadCount = Number(count || 0) || 0;
        if (!badge) {
            return;
        }

        badge.textContent = String(state.unreadCount);
        badge.classList.toggle('hidden', state.unreadCount <= 0);
    };

    const renderNotifications = (notifications) => {
        if (!list) {
            return;
        }

        if (!Array.isArray(notifications) || notifications.length === 0) {
            list.innerHTML = '<div id="teacher-notif-empty" class="px-4 py-8 text-center text-sm text-slate-500">No notifications yet.</div>';
            return;
        }

        list.innerHTML = notifications.map((notification) => {
            const isRead = Boolean(notification?.is_read);
            const iconClass = isRead ? 'bg-slate-300' : 'bg-indigo-600';
            const url = String(notification?.url || '#');
            const title = escapeHtml(notification?.title || 'Notification');
            const message = escapeHtml(notification?.message || '');
            const time = escapeHtml(notification?.created_at_human || '');

            return `
                <a href="${escapeHtml(url)}" class="block px-4 py-3 hover:bg-slate-50">
                    <div class="flex items-start gap-3">
                        <span class="mt-2 h-2 w-2 rounded-full ${iconClass}"></span>
                        <div class="min-w-0">
                            <div class="text-sm font-semibold text-slate-800">${title}</div>
                            <div class="text-xs text-slate-500 truncate">${message}</div>
                            <div class="text-[11px] text-slate-400 mt-1">${time}</div>
                        </div>
                    </div>
                </a>
            `;
        }).join('');
    };

    const showApprovalAlert = (title, text) => {
        const cleanTitle = String(title || 'Notification');
        const cleanText = String(text || '').trim();
        if (cleanText === '') {
            return;
        }

        if (hasSwal) {
            window.Swal.fire({
                icon: 'success',
                title: cleanTitle,
                text: cleanText,
                confirmButtonText: 'OK',
                confirmButtonColor: '#4f46e5',
            });
            return;
        }

        window.alert(`${cleanTitle}\n\n${cleanText}`);
    };

    const poll = async () => {
        try {
            const response = await window.fetch(pollUrl, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                return;
            }

            const data = await response.json();
            const notifications = Array.isArray(data?.notifications) ? data.notifications : [];
            const latestId = Number(data?.latest_id || 0) || 0;

            if (typeof data?.unread_count === 'number') {
                updateBadge(data.unread_count);
            }

            if (notifications.length > 0) {
                renderNotifications(notifications);

                if (latestId > state.latestId) {
                    const newNotifications = notifications.filter((item) => Number(item?.id || 0) > state.latestId);
                    newNotifications
                        .filter((item) => String(item?.type || '') === 'teacher_law_request_approved')
                        .forEach((item) => {
                            showApprovalAlert(item?.title || 'Law request approved', item?.message || 'Your law request has been approved.');
                        });
                }

                state.latestId = latestId;
            }
        } catch (error) {
            console.error('Unable to poll teacher notifications.', error);
        }
    };

    poll();
    window.setInterval(poll, 15000);
});
