jQuery(document).ready(function ($) {
    /**
     * Handle Topic Completion Toggle
     */
    $('body').on('click', '.alezux-btn-complete-topic', function (e) {
        e.preventDefault();

        var $button = $(this);
        // Ensure we are hitting the anchor if clicked on inner elements
        if (!$button.hasClass('alezux-btn-complete-topic')) {
            $button = $button.closest('.alezux-btn-complete-topic');
        }

        var postId = $button.data('post-id');
        var nonce = $button.data('nonce');
        var $contentWrapper = $button.find('.alezux-btn-content-wrapper');
        var $loader = $button.find('.alezux-btn-loader');

        var $stateIncomplete = $button.find('.alezux-btn-state.state-incomplete');
        var $stateCompleted = $button.find('.alezux-btn-state.state-completed');

        if ($button.hasClass('is-loading')) {
            return;
        }

        // Add loading state
        $button.addClass('is-loading');
        $contentWrapper.children().hide();
        $loader.css('display', 'flex'); // Force flex for centering

        // Ensure alezux_vars is defined
        var ajaxUrl = (typeof alezux_vars !== 'undefined') ? alezux_vars.ajax_url : '/wp-admin/admin-ajax.php';

        // Determine method based on current state
        var method = $button.hasClass('is-completed') ? 'unmark' : 'mark';

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'alezux_toggle_topic_complete',
                post_id: postId,
                nonce: nonce,
                method: method
            },
            success: function (response) {
                // Force reload to deal with aggressive caching issues (LiteSpeed)
                // The user requested explicit page reload to ensure state is correct
                if (response.success) {
                    location.reload();
                    return; // Stop processing UI updates since we are reloading
                }

                $button.removeClass('is-loading');
                $loader.hide();

                if (!response.success) {
                    // Revert on logic error if success is false but didn't throw error
                    console.warn('Alezux Toggle: ', response);
                    if ($button.hasClass('is-completed')) {
                        $stateCompleted.css('display', 'flex');
                    } else {
                        $stateIncomplete.css('display', 'flex');
                    }
                }
            },
            error: function (xhr, status, error) {
                $button.removeClass('is-loading');
                $loader.hide();
                console.error('AJAX Error:', error);

                // Revert UI on connection error
                if ($button.hasClass('is-completed')) {
                    $stateCompleted.css('display', 'flex');
                } else {
                    $stateIncomplete.css('display', 'flex');
                }
            }
        });
    });
});
