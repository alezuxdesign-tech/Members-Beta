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
     * Detects HTML5 video/audio playback AND Vimeo/YouTube iframes to track time.
     */
    (function () {
        let activeSeconds = 0;
        let accumulatedSeconds = 0;
        let isPlaying = false;
        let lastSyncTime = Date.now();
        const SYNC_INTERVAL = 30000; // 30 seconds

        // Flags for multiple players
        let activePlayers = {
            html5: false,
            vimeo: false,
            youtube: false
        };

        function updateMasterState() {
            // If any player is active, we are playing
            isPlaying = activePlayers.html5 || activePlayers.vimeo || activePlayers.youtube;
            // distinct visual debugger
            // console.log('Alezux Tracker State:', activePlayers, 'IsPlaying:', isPlaying);
        }

        function startTracking(source) {
            activePlayers[source] = true;
            updateMasterState();
        }

        function stopTracking(source) {
            activePlayers[source] = false;
            updateMasterState();
        }

        // --- 1. HTML5 Media Elements ---
        $('video, audio').on('play', function () { startTracking('html5'); })
            .on('pause ended', function () { stopTracking('html5'); });

        // Check initial state
        $('video, audio').each(function () {
            if (!this.paused && !this.ended) {
                startTracking('html5');
            }
        });

        // --- 2. Vimeo Support (PostMessage API) ---
        function initVimeo() {
            const vimeoIframes = $('iframe[src*="vimeo.com"]');

            if (vimeoIframes.length === 0) return;

            // Listen for messages from Vimeo
            window.addEventListener('message', function (e) {
                // Validate origin somewhat (Vimeo usually sends from player.vimeo.com)
                if (e.origin.indexOf('vimeo') === -1) return;

                try {
                    var data = JSON.parse(e.data);
                } catch (err) { return; }

                if (data.event === 'play') startTracking('vimeo');
                if (data.event === 'pause' || data.event === 'finish') stopTracking('vimeo');
                if (data.event === 'ready') {
                    // Re-bind if a player reports ready late
                    bindVimeoListeners();
                }
            });

            function bindVimeoListeners() {
                vimeoIframes.each(function () {
                    const url = new URL(this.src);
                    // Ensure api=1 parameter (Elementor/LearnDash usually add it, but allow JS interaction just in case)
                    // Note: We can't change src easily without reloading iframe, assuming it supports API.

                    // Send events
                    this.contentWindow.postMessage(JSON.stringify({ method: 'addEventListener', value: 'play' }), '*');
                    this.contentWindow.postMessage(JSON.stringify({ method: 'addEventListener', value: 'pause' }), '*');
                    this.contentWindow.postMessage(JSON.stringify({ method: 'addEventListener', value: 'finish' }), '*');
                });
            }

            // Bind immediately
            bindVimeoListeners();
            // And periodically check for new iframes (e.g. popups) or ready states? 
            // For now, load once.
        }
        initVimeo();

        // --- 3. YouTube Support (IFrame API) ---
        // YouTube requires the IFrame API to be loaded and `enablejsapi=1` on the iframe.
        function initYouTube() {
            // Function to check player state if we can access it
            // This is tricky without fully taking over the iframe. 
            // We rely on the global YT object or Poll for state if possible. 

            // Wait for YT API
            var checkYT = setInterval(function () {
                if (typeof YT !== 'undefined' && YT && YT.Player) {
                    clearInterval(checkYT);
                    bindYouTube();
                }
            }, 1000);

            // Timeout after 10s to stop checking
            setTimeout(function () { clearInterval(checkYT); }, 10000);

            function bindYouTube() {
                $('iframe[src*="youtube.com"]').each(function () {
                    var $iframe = $(this);
                    // Try to interact existing player or create new light wrapper
                    // Note: Creating 'new YT.Player' on an existing valid iframe ID usually hooks into it without reloading if configured right.
                    if (!$iframe.attr('id')) {
                        $iframe.attr('id', 'alezux-yt-' + Math.floor(Math.random() * 10000));
                    }

                    try {
                        new YT.Player($iframe.attr('id'), {
                            events: {
                                'onStateChange': onPlayerStateChange
                            }
                        });
                    } catch (e) {
                        console.warn('Alezux Tracker: Could not bind YouTube', e);
                    }
                });
            }

            function onPlayerStateChange(event) {
                // YT.PlayerState: PLAYING=1, PAUSED=2, ENDED=0, BUFFERING=3
                if (event.data === YT.PlayerState.PLAYING) {
                    startTracking('youtube');
                } else if (event.data === YT.PlayerState.PAUSED || event.data === YT.PlayerState.ENDED) {
                    stopTracking('youtube');
                }
            }
        }
        initYouTube();


        // --- Main Timer Loop ---
        setInterval(function () {
            // Only count if playing AND tab is visible
            if (isPlaying && document.visibilityState === 'visible') {
                activeSeconds++;
                accumulatedSeconds++;
            }
        }, 1000);

        // Sync function
        function syncTime() {
            if (accumulatedSeconds === 0) return;

            const secondsToSend = accumulatedSeconds;
            accumulatedSeconds = 0; // Reset immediately
            lastSyncTime = Date.now();

            var ajaxUrl = (typeof alezux_vars !== 'undefined') ? alezux_vars.ajax_url : '/wp-admin/admin-ajax.php';

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
            }
        });

    })();

});
