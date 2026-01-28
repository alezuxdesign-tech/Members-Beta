jQuery(document).ready(function ($) {
    const $widget = $('.alezux-notifications-widget');
    const $bell = $widget.find('.alezux-bell-icon');
    const $badge = $widget.find('.alezux-notification-badge');
    const $list = $widget.find('#alezux-notif-list-inbox');
    const $markAll = $widget.find('.alezux-mark-all-read');

    // Toggle Dropdown using delegation for robustness
    $(document).on('click', '.alezux-bell-icon', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var $parent = $(this).closest('.alezux-notifications-widget');
        $parent.toggleClass('active');

        // Close other open widgets if any
        $('.alezux-notifications-widget').not($parent).removeClass('active');
    });

    // Close on click outside
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.alezux-notifications-widget').length) {
            $('.alezux-notifications-widget').removeClass('active');
        }
    });

    // Mark All Read
    $(document).on('click', '.alezux-mark-all-read', function (e) {
        e.stopPropagation();
        $.post(alezux_notifications_obj.ajaxurl, {
            action: 'alezux_mark_all_read',
            nonce: alezux_notifications_obj.nonce
        }, function (response) {
            if (response.success) {
                $('.alezux-notification-item').removeClass('unread');
                updateBadge(0);
            }
        });
    });

    // Click on Notification Item
    $(document).on('click', '.alezux-notification-item', function () {
        const $item = $(this);
        const id = $item.data('id');
        const link = $item.data('link');

        if ($item.hasClass('unread')) {
            $.post(alezux_notifications_obj.ajaxurl, {
                action: 'alezux_mark_read',
                id: id,
                nonce: alezux_notifications_obj.nonce
            });
            $item.removeClass('unread');
        }

        if (link && link !== '#') {
            window.location.href = link;
        }
    });

    // Fetch Notifications
    function fetchNotifications() {
        $.post(alezux_notifications_obj.ajaxurl, {
            action: 'alezux_get_notifications',
            nonce: alezux_notifications_obj.nonce
        }, function (response) {
            if (response.success) {
                renderNotifications(response.data.notifications);
                updateBadge(response.data.unread_count);
            }
        });
    }

    function renderNotifications(notifications) {
        $list.empty();
        if (notifications.length === 0) {
            $list.html(`
                <div class="alezux-no-notifications">
                    <i class="eicon-bell-o alezux-no-notifications-icon"></i>
                    <div>No hay notificaciones</div>
                </div>
            `);
            return;
        }

        notifications.forEach(notif => {
            const isUnread = notif.is_read == '0' ? 'unread' : '';
            const avatar = notif.avatar_url ? `<img src="${notif.avatar_url}" class="notif-avatar">` : `<div class="notif-avatar" style="background:#eee; display:flex; align-items:center; justify-content:center;">?</div>`;

            const html = `
                <div class="alezux-notification-item ${isUnread}" data-id="${notif.id}" data-link="${notif.link}">
                    ${avatar}
                    <div class="notif-content">
                        <div class="notif-text">${notif.title} ${notif.message}</div>
                        <div class="notif-meta">${notif.time_ago}</div>
                    </div>
                </div>
            `;
            $list.append(html);
        });
    }

    function updateBadge(count) {
        if (count > 0) {
            $('.alezux-notification-badge').text(count).show();
        } else {
            $('.alezux-notification-badge').hide();
        }
    }

    // Initial Load
    fetchNotifications();

    // Poll every 60 seconds (optional)
    setInterval(fetchNotifications, 60000);
});
