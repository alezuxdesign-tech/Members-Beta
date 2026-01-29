jQuery(document).ready(function ($) {
    /**
     * Handle Topic Completion Toggle
     */
    $(document).on('click', '.alezux-btn-complete-topic', function (e) {
        e.preventDefault();

        var $button = $(this);
        var postId = $button.data('post-id');
        var nonce = $button.data('nonce');
        var $contentWrapper = $button.find('.alezux-btn-content-wrapper');
        var $loader = $button.find('.alezux-btn-loader');

        // Use more generic selectors to avoid issues with specific classes added by elementor or typos
        var $stateIncomplete = $button.find('.alezux-btn-state.state-incomplete');
        var $stateCompleted = $button.find('.alezux-btn-state.state-completed');

        if ($button.hasClass('is-loading')) {
            return;
        }

        // Add loading state
        $button.addClass('is-loading');
        $stateIncomplete.hide();
        $stateCompleted.hide();
        $loader.show();

        // Ensure alezux_vars is defined
        var ajaxUrl = (typeof alezux_vars !== 'undefined') ? alezux_vars.ajax_url : '/wp-admin/admin-ajax.php';

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'alezux_toggle_topic_complete',
                post_id: postId,
                nonce: nonce
            },
            success: function (response) {
                $button.removeClass('is-loading');
                $loader.hide();

                if (response.success) {
                    if (response.data.status === 'completed') {
                        $button.addClass('is-completed');
                        $stateIncomplete.hide();
                        $stateCompleted.css('display', 'flex');
                    } else {
                        $button.removeClass('is-completed');
                        $stateCompleted.hide();
                        $stateIncomplete.css('display', 'flex');
                    }
                } else {
                    console.error('Error toggling completion:', response);
                    // Revert to best guess state
                    if ($button.hasClass('is-completed')) {
                        $stateCompleted.show();
                    } else {
                        $stateIncomplete.show();
                    }
                }
            },
            error: function (xhr, status, error) {
                $button.removeClass('is-loading');
                $loader.hide();
                console.error('AJAX Error:', error);

                // Revert
                if ($button.hasClass('is-completed')) {
                    $stateCompleted.show();
                } else {
                    $stateIncomplete.show();
                }
            }
        });
    });
});
