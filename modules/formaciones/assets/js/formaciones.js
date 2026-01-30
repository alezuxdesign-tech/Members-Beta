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
     * Time Tracking Logic for Study - UNIVERSAL VERSION
     * Supports HTML5, YouTube (API), Vimeo (API), and generic Iframe interaction.
     */
    (function () {
        let activeSeconds = 0;
        let accumulatedSeconds = 0;
        let isPlaying = false;
        let lastSyncTime = Date.now();
        const SYNC_INTERVAL = 30000; // 30 seconds

        // State tracking
        let activeSources = new Set();

        const logState = (source, active) => {
            if (active) activeSources.add(source);
            else activeSources.delete(source);

            const wasPlaying = isPlaying;
            isPlaying = activeSources.size > 0;

            // Debug only
            // if (wasPlaying !== isPlaying) console.log('Alezux Tracker:', isPlaying ? 'Reconding...' : 'Paused', [...activeSources]);
        };

        // --- 1. HTML5 Native Video/Audio ---
        $('video, audio').on('play', () => logState('html5', true))
            .on('pause ended waiting', () => logState('html5', false));

        // --- 2. Generic Iframe Focus Fallback (For unknown players) ---
        // If user clicks into an iframe, we assume they might be watching. 
        // We stop assumtion if they click back out or blur window.
        // This is a heuristic for "Any Player".
        $(window).on('blur', function () {
            setTimeout(function () {
                if (document.activeElement && document.activeElement.tagName === 'IFRAME') {
                    // console.log('Focus lost to Iframe - assuming playback interactions');
                    // We don't verify provider here, just generic fallback
                    // But to avoid double counting with YT/Vimeo APIs, we verify src
                    const src = document.activeElement.src || '';
                    if (!src.includes('youtube') && !src.includes('vimeo')) {
                        logState('generic-iframe', true);
                    }
                }
            }, 100);
        });
        $(window).on('focus', function () {
            // User came back to main window, assume interaction with generic iframe stopped or pausing?
            // Actually, for generic iframe, it's hard to know when they stop. 
            // We'll be conservative: if they focus back on body, stop generic count.
            logState('generic-iframe', false);
        });


        // --- 3. Vimeo API Support ---
        function initVimeo() {
            const iframes = $('iframe[src*="vimeo.com"]');
            if (iframes.length === 0) return;

            // Load Vimeo Player SDK
            if (typeof Vimeo === 'undefined') {
                $.getScript('https://player.vimeo.com/api/player.js').done(bindVimeo);
            } else {
                bindVimeo();
            }

            function bindVimeo() {
                iframes.each(function () {
                    const player = new Vimeo.Player(this);
                    player.on('play', () => logState('vimeo', true));
                    player.on('pause', () => logState('vimeo', false));
                    player.on('ended', () => logState('vimeo', false));
                    // Check initial status
                    player.getPaused().then((paused) => {
                        if (!paused) logState('vimeo', true);
                    });
                });
            }
        }
        initVimeo();

        // --- 4. YouTube API Support ---
        function initYouTube() {
            const ytIframes = $('iframe[src*="youtube.com"], iframe[src*="youtu.be"]');
            if (ytIframes.length === 0) return;

            // 4.1 Force 'enablejsapi=1' to allow tracking
            ytIframes.each(function () {
                let src = $(this).attr('src');
                if (src && src.indexOf('enablejsapi=1') === -1) {
                    const separator = src.indexOf('?') !== -1 ? '&' : '?';
                    $(this).attr('src', src + separator + 'enablejsapi=1');
                }
                // Ensure ID
                if (!$(this).attr('id')) {
                    $(this).attr('id', 'alezux-yt-' + Math.floor(Math.random() * 10000));
                }
            });

            // 4.2 Load YT API
            if (typeof YT === 'undefined' || typeof YT.Player === 'undefined') {
                var tag = document.createElement('script');
                tag.src = "https://www.youtube.com/iframe_api";
                var firstScriptTag = document.getElementsByTagName('script')[0];
                firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

                // Queued Init
                window.onYouTubeIframeAPIReady = function () {
                    bindYouTubePlayers();
                };
            } else {
                bindYouTubePlayers();
            }

            function bindYouTubePlayers() {
                ytIframes.each(function () {
                    const id = $(this).attr('id');
                    try {
                        new YT.Player(id, {
                            events: {
                                'onStateChange': onPlayerStateChange
                            }
                        });
                    } catch (e) { console.error('YT Bind Error', e); }
                });
            }

            function onPlayerStateChange(event) {
                if (event.data === YT.PlayerState.PLAYING) {
                    logState('youtube', true);
                } else if (event.data === YT.PlayerState.PAUSED || event.data === YT.PlayerState.ENDED) {
                    logState('youtube', false);
                }
            }
        }
        initYouTube();


        // --- Core Timer Loop ---
        setInterval(function () {
            // Only track if playing AND visible
            if (isPlaying && document.visibilityState === 'visible') {
                activeSeconds++;
                accumulatedSeconds++;
            }

            // Sync
            if (Date.now() - lastSyncTime > SYNC_INTERVAL && accumulatedSeconds > 0) {
                syncTime();
            }
        }, 1000);

        function syncTime() {
            if (accumulatedSeconds === 0) return;
            const sec = accumulatedSeconds;
            accumulatedSeconds = 0;
            lastSyncTime = Date.now();

            var ajaxUrl = (typeof alezux_vars !== 'undefined') ? alezux_vars.ajax_url : '/wp-admin/admin-ajax.php';
            $.post(ajaxUrl, {
                action: 'alezux_track_study_time',
                seconds: sec
            });
        }

        $(window).on('beforeunload visibilitychange', function () {
            if (document.visibilityState === 'hidden') syncTime();
        });

    })();

});
