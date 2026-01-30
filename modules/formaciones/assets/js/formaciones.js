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

    /**
     * Time Tracking Logic for Study
     * Detects HTML5 video/audio playback and tracks time.
     */
    (function () {
        let activeSeconds = 0;
        let accumulatedSeconds = 0;
        let isPlaying = false;
        let lastSyncTime = Date.now();
        const SYNC_INTERVAL = 30000; // 30 seconds

        function startTracking() {
            isPlaying = true;
        }

        function stopTracking() {
            isPlaying = false;
        }

        // Detect HTML5 Media Elements
        $('video, audio').on('play', startTracking).on('pause ended', stopTracking);

        // Initial check if any media is already playing (autostart)
        $('video, audio').each(function () {
            if (!this.paused && !this.ended) {
                isPlaying = true;
            }
        });

        // Main Timer Loop
        setInterval(function () {
            // Only count if playing AND tab is visible
            if (isPlaying && document.visibilityState === 'visible') {
                activeSeconds++;
                accumulatedSeconds++;
            }

            // Auto sync every 30s if we have data
            if (Date.now() - lastSyncTime > SYNC_INTERVAL && accumulatedSeconds > 0) {
                syncTime();
            }
        }, 1000);

        // Sync function
        function syncTime() {
            if (accumulatedSeconds === 0) return;

            const secondsToSend = accumulatedSeconds;
            accumulatedSeconds = 0; // Reset immediately
            lastSyncTime = Date.now();

            var ajaxUrl = (typeof alezux_vars !== 'undefined') ? alezux_vars.ajax_url : '/wp-admin/admin-ajax.php';

            // Use navigator.sendBeacon if available for unload events, otherwise standard AJAX
            // But for interval, standard AJAX is fine.
            // Note: jQuery AJAX might fail on unload, but we'll try best effort.

            $.post(ajaxUrl, {
                action: 'alezux_track_study_time',
                seconds: secondsToSend,
                date: new Date().toISOString().slice(0, 10) // YYYY-MM-DD
            });
        }

        // Save on unload / visibility change (mobile)
        $(window).on('beforeunload', function () {
            syncTime();
        });

        // Also sync on visibility hidden (user switches tabs)
        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'hidden') {
                syncTime();
                // If it was playing, it might pause automatically depending on browser, 
                // but we keep the isPlaying flag as is because the browser pauses the video usually.
                // However, we stop counting because of the visibility check in the loop.
            }
        });

    })();

});
