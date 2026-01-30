jQuery(document).ready(function ($) {

    /**
     * ALEZUX MEDIA UPLOADER LOGIC
     * Handles opening the WP Media Library and interacting with the upload box.
     */

    var file_frame;

    // --- Helper: Update UI when image is selected ---
    function updateUploadUI($box, attachment) {
        var $container = $box.closest('.alezux-logro-upload-container');
        var $input = $container.find('.alezux-logro-image-id');
        var $previewBox = $box.find('.alezux-upload-preview');
        var $placeholder = $box.find('.alezux-upload-placeholder');
        var $previewImg = $box.find('.alezux-preview-img');

        // Update Input ID
        if (attachment) {
            $input.val(attachment.id);
            var url = attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;
            $previewImg.attr('src', url);
            $placeholder.hide();
            $previewBox.css('display', 'flex').hide().fadeIn();
        } else {
            // Reset
            $input.val('');
            $previewImg.attr('src', '');
            $previewBox.hide();
            $placeholder.fadeIn();
        }
    }

    // --- 1. Click Event on Upload Box ---
    $(document).on('click', '.alezux-upload-box', function (event) {
        event.preventDefault();

        // Check if removing image
        if ($(event.target).closest('.alezux-remove-img').length) {
            updateUploadUI($(this), null);
            return;
        }

        // Safety check for WP Media
        if (typeof wp === 'undefined' || !wp.media) {
            console.error('Alezux Members: WP Media Library is not missing. Ensure user is admin or script is enqueued.');
            alert('Error: La librería de medios no está disponible.');
            return;
        }

        var $box = $(this);

        // If the media frame already exists, reopen it.
        // Note: strictly, we should create a new frame if we want individual settings, 
        // but for a single generic image picker, reusing is fine. 
        // We just need to know which box triggered it.
        if (file_frame) {
            file_frame.open();
        } else {
            // Create the media frame.
            file_frame = wp.media.frames.file_frame = wp.media({
                title: 'Seleccionar Imagen del Logro',
                button: {
                    text: 'Usar esta imagen'
                },
                multiple: false
            });


        }

        // Remove previous 'select' handlers to avoid multiple firings on different boxes
        file_frame.off('select');

        // When an image is selected, run a callback.
        file_frame.on('select', function () {
            var attachment = file_frame.state().get('selection').first().toJSON();
            updateUploadUI($box, attachment);
        });

        file_frame.open();
    });


    // --- 2. Visual Drag & Drop Feedback (Optional enhancement) ---
    var dragTimer;
    $(document).on('dragover', '.alezux-upload-box', function (e) {
        e.preventDefault();
        var dt = e.originalEvent.dataTransfer;
        if (dt.types && (dt.types.indexOf ? dt.types.indexOf('Files') != -1 : true)) {
            $(this).addClass('drag-over');
            window.clearTimeout(dragTimer);
        }
    });

    $(document).on('dragleave', '.alezux-upload-box', function (e) {
        e.preventDefault();
        dragTimer = window.setTimeout(function () {
            $('.alezux-upload-box').removeClass('drag-over');
        }, 50);
    });

    $(document).on('drop', '.alezux-upload-box', function (e) {
        e.preventDefault();
        $(this).removeClass('drag-over');
        // Note: Actual dropping of files to upload via AJAX is complex and requires 
        // separate handling (uploading to WP media library via API).
        // For now, we just open the library if they drop, or show a message.
        // Since implementing full AJAX drag-drop upload is out of scope for a quick fix 
        // without backend support, we'll just trigger the click to open library.
        alert('Por favor, haz clic para seleccionar o subir la imagen desde la librería.');
    });


    // --- 3. Form Submission Handling ---
    $(document).on('submit', '#alezux-logro-form', function (e) {
        e.preventDefault();

        // Ensure we are using the localized variables
        if (typeof alezux_logros_vars === 'undefined') {
            console.error('Alezux Logros Vars not found');
            return;
        }

        var $form = $(this);
        var $response = $('#alezux-logro-response');
        var $btn = $form.find('button[type="submit"]');
        var originalText = $btn.text();

        $btn.prop('disabled', true).text('Guardando...');
        $response.html('');

        var formData = new FormData(this);
        formData.append('action', 'alezux_save_achievement');
        formData.append('nonce', alezux_logros_vars.nonce);

        $.ajax({
            url: alezux_logros_vars.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.success) {
                    $response.html('<div class="alezux-success"><i class="fas fa-check-circle"></i> ' + res.data.message + '</div>');
                    $form[0].reset();

                    // Reset all upload boxes within this form
                    $form.find('.alezux-upload-box').each(function () {
                        updateUploadUI($(this), null);
                    });

                } else {
                    $response.html('<div class="alezux-error"><i class="fas fa-exclamation-circle"></i> ' + (res.data.message || 'Error desconocido') + '</div>');
                }
            },
            error: function () {
                $response.html('<div class="alezux-error"><i class="fas fa-exclamation-triangle"></i> Error de conexión con el servidor.</div>');
            },
            complete: function () {
                $btn.prop('disabled', false).text(originalText);
            }
        });
    });


    // --- 4. Frontend Grid Popup Logic (Existing) ---
    $(document).on('click', '.alezux-logro-view-btn', function (e) {
        e.preventDefault();

        var $btn = $(this);
        var target = $btn.data('popup-target');
        var image = $btn.data('image');
        var message = $btn.data('message');
        var student = $btn.data('student');
        var avatar = $btn.data('avatar');

        var $popup = $(target);
        if ($popup.length === 0) return;

        // Populate
        $popup.find('.popup-image-el').attr('src', image);
        $popup.find('.popup-message-el').text(message);
        $popup.find('.popup-student-el').text(student);
        $popup.find('.popup-avatar-el').attr('src', avatar);

        // Show
        $popup.css('display', 'flex').hide().fadeIn(200);
    });

    $(document).on('click', '.alezux-popup-close, .alezux-logro-popup-overlay', function (e) {
        if (e.target === this || $(e.target).hasClass('alezux-popup-close')) {
            $(this).closest('.alezux-logro-popup-overlay').fadeOut(200);
        }
    });

    $(document).on('click', '.alezux-logro-popup-content', function (e) {
        e.stopPropagation();
    });

});
