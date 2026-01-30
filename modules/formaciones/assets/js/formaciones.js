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
    /**
     * Study Time Tracker (Event Driven - SQL)
     */
    (function () {
        console.log('Alezux Tracker: Init');
        if (typeof alezux_vars === 'undefined' || !alezux_vars.ajax_url || !alezux_vars.post_id) {
            console.error('Alezux Tracker: Missing vars', typeof alezux_vars !== 'undefined' ? alezux_vars : 'undefined');
            return;
        }

        const ajaxUrl = alezux_vars.ajax_url;
        const postId = alezux_vars.post_id;

        // State
        let startTime = 0;
        let accumulatedTime = 0;
        let isTracking = false;
        let activeSources = new Set();

        console.log('Alezux Tracker: Ready. Post ID:', postId);

        function startTracking(source) {
            console.log('Alezux Tracker: Request Start from', source);
            if (!activeSources.has(source)) {
                activeSources.add(source);
                if (!isTracking) {
                    isTracking = true;
                    startTime = Date.now();
                    console.log('Alezux Tracker: STARTED TRACKING. StartTime:', new Date(startTime).toLocaleTimeString());
                }
            }
            console.log('Alezux Tracker: Active Sources:', Array.from(activeSources));
        }

        function stopTracking(source) {
            console.log('Alezux Tracker: Request Stop from', source);
            if (activeSources.has(source)) {
                activeSources.delete(source);
                if (activeSources.size === 0 && isTracking) {
                    isTracking = false;
                    const now = Date.now();
                    const sessionTime = Math.floor((now - startTime) / 1000);
                    if (sessionTime > 0) {
                        accumulatedTime += sessionTime;
                        console.log('Alezux Tracker: STOPPED TRACKING. Session Secs:', sessionTime, 'Total Accum:', accumulatedTime);
                        sendData();
                    } else {
                        console.log('Alezux Tracker: STOPPED ignored (0s duration)');
                    }
                }
            }
            console.log('Alezux Tracker: Active Sources:', Array.from(activeSources));
        }

        function sendData() {
            if (accumulatedTime > 0) {
                const timeToSend = accumulatedTime;
                accumulatedTime = 0;

                console.log('Alezux Tracker: SENDING DATA...', timeToSend, 'seconds');

                // Reliable send on unload
                if (navigator.sendBeacon) {
                    const formData = new FormData();
                    formData.append('action', 'alezux_track_study_time');
                    formData.append('seconds', timeToSend);
                    formData.append('post_id', postId);
                    const success = navigator.sendBeacon(ajaxUrl, formData); // Returns true if queued
                    console.log('Alezux Tracker: Beacon Queued?', success);
                } else {
                    $.post(ajaxUrl, {
                        action: 'alezux_track_study_time',
                        seconds: timeToSend,
                        post_id: postId
                    }).done(function (res) {
                        console.log('Alezux Tracker: AJAX Success', res);
                    }).fail(function (err) {
                        console.error('Alezux Tracker: AJAX Fail', err);
                    });
                }
            } else {
                console.log('Alezux Tracker: sendData called but nothing to send.');
            }
        }

        // Adapter for existing calls
        const logState = (source, active) => {
            if (active) startTracking(source);
            else stopTracking(source);
        };

        // --- HTML5 Events ---
        $('video, audio').on('play', function () {
            console.log('Alezux Tracker Event: HTML5 Play');
            startTracking('html5-' + (this.id || 'gen'));
        }).on('pause ended', function () {
            console.log('Alezux Tracker Event: HTML5 Pause/Ended');
            stopTracking('html5-' + (this.id || 'gen'));
        });

        // --- YouTube API ---
        function onYouTubeIframeAPIReady() {
            $('iframe[src*="youtube.com"], iframe[src*="youtu.be"]').each(function (index) {
                if (!this.id) this.id = 'alezux-yt-' + index;
                try {
                    new YT.Player(this.id, {
                        events: {
                            'onStateChange': function (event) {
                                if (event.data === YT.PlayerState.PLAYING) {
                                    console.log('Alezux Tracker Event: YT Playing');
                                    startTracking('yt-' + event.target.getIframe().id);
                                } else if (event.data === YT.PlayerState.PAUSED || event.data === YT.PlayerState.ENDED) {
                                    console.log('Alezux Tracker Event: YT Paused/Ended');
                                    stopTracking('yt-' + event.target.getIframe().id);
                                }
                            }
                        }
                    });
                } catch (e) { }
            });
        }

        if (typeof YT === 'undefined' || typeof YT.Player === 'undefined') {
            var tag = document.createElement('script');
            tag.src = "https://www.youtube.com/iframe_api";
            var firstScriptTag = document.getElementsByTagName('script')[0];
            firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
            window.onYouTubeIframeAPIReady = onYouTubeIframeAPIReady;
        } else {
            onYouTubeIframeAPIReady();
        }

        // --- Vimeo API ---
        const vimeoIframes = $('iframe[src*="vimeo.com"]');
        if (vimeoIframes.length > 0) {
            if (typeof Vimeo === 'undefined') {
                $.getScript('https://player.vimeo.com/api/player.js').done(bindVimeo);
            } else {
                bindVimeo();
            }
        }

        function bindVimeo() {
            vimeoIframes.each(function () {
                const player = new Vimeo.Player(this);
                player.on('play', () => { console.log('Alezux Tracker Event: Vimeo Play'); startTracking('vimeo'); });
                player.on('pause', () => { console.log('Alezux Tracker Event: Vimeo Pause'); stopTracking('vimeo'); });
                player.on('ended', () => { console.log('Alezux Tracker Event: Vimeo Ended'); stopTracking('vimeo'); });
            });
        }

        function sendData() {
            if (accumulatedTime > 0) {
                const timeToSend = accumulatedTime;
                accumulatedTime = 0;

                var ajaxUrl = (typeof alezux_vars !== 'undefined') ? alezux_vars.ajax_url : '/wp-admin/admin-ajax.php';
                var postId = (typeof alezux_vars !== 'undefined') ? alezux_vars.post_id : 0;

                console.log('Alezux Tracker: Sending Data (Auto/Beacon)...', timeToSend);

                // Reliable send on unload
                if (navigator.sendBeacon) {
                    const formData = new FormData();
                    formData.append('action', 'alezux_track_study_time');
                    formData.append('seconds', timeToSend);
                    formData.append('post_id', postId);
                    navigator.sendBeacon(ajaxUrl, formData);
                } else {
                    $.post(ajaxUrl, {
                        action: 'alezux_track_study_time',
                        seconds: timeToSend,
                        post_id: postId
                    });
                }
            }
        }

        $(window).on('beforeunload visibilitychange', function () {
            console.log('Alezux Tracker Event: Window/Vis Change', document.visibilityState);
            if (document.visibilityState === 'hidden') {
                if (isTracking) {
                    const now = Date.now();
                    const sessionTime = Math.floor((now - startTime) / 1000);
                    if (sessionTime > 0) {
                        accumulatedTime += sessionTime;
                        startTime = now;
                        console.log('Alezux Tracker: Accumulating before exit:', sessionTime);
                    }
                }
                sendData();
            }
        });

    })();

});
