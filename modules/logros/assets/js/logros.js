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
            var url = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;
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
    // Using body delegation to be absolutely sure we catch it even if DOM changes
    $('body').on('click', '.alezux-upload-box', function (event) {

        // Prevent default only if we are not clicking a remove button or something interactive inside that handles itself
        if ($(event.target).closest('.alezux-remove-img').length) {
            event.preventDefault();
            // Logic handled below
            return;
        }

        event.preventDefault();

        // Safety check for WP Media
        if (typeof wp === 'undefined' || !wp.media) {
            console.error('Alezux Members: WP Media Library is missing. Ensure you are logged in and wp_enqueue_media() is called.');
            return;
        }

        var $box = $(this);

        // If the media frame already exists, reopen it.
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

        // Remove previous 'select' handlers to avoid stacking
        file_frame.off('select');

        // When an image is selected, run a callback.
        file_frame.on('select', function () {
            var attachment = file_frame.state().get('selection').first().toJSON();
            updateUploadUI($box, attachment);
        });

        file_frame.open();
    });

    // --- Handle Remove Image Button ---
    $('body').on('click', '.alezux-remove-img', function (e) {
        e.preventDefault();
        e.stopPropagation(); // Stop bubbling to box click
        var $box = $(this).closest('.alezux-upload-box');
        updateUploadUI($box, null);
    });


    // --- 2. Visual Drag & Drop Feedback ---
    $(document).on('dragover', '.alezux-upload-box', function (e) {
        e.preventDefault();
        $(this).addClass('drag-over');
    });

    $(document).on('dragleave', '.alezux-upload-box', function (e) {
        e.preventDefault();
        $(this).removeClass('drag-over');
    });

    $(document).on('drop', '.alezux-upload-box', function (e) {
        e.preventDefault();
        $(this).removeClass('drag-over');
        // Trigger click to open media manager as simple fallback for drop
        $(this).trigger('click');
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

        $btn.prop('disabled', true).css('opacity', '0.7');
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
                    $response.html('<div class="alezux-success" style="padding:10px; background:#d1e7dd; color:#0f5132; border-radius:4px; margin-top:10px;"><i class="fas fa-check-circle"></i> ' + res.data.message + '</div>');
                    $form[0].reset();

                    // Reset all upload boxes within this form
                    $form.find('.alezux-upload-box').each(function () {
                        updateUploadUI($(this), null);
                    });

                } else {
                    $response.html('<div class="alezux-error" style="padding:10px; background:#f8d7da; color:#842029; border-radius:4px; margin-top:10px;"><i class="fas fa-exclamation-circle"></i> ' + (res.data.message || 'Error desconocido') + '</div>');
                }
            },
            error: function (xhr, status, error) {
                $response.html('<div class="alezux-error" style="padding:10px; background:#f8d7da; color:#842029; border-radius:4px; margin-top:10px;"><i class="fas fa-exclamation-triangle"></i> Error de conexión: ' + error + '</div>');
            },
            complete: function () {
                $btn.prop('disabled', false).css('opacity', '1');
            }
        });
    });

    // Initialize logic on Elementor Frontend Load (for popup actions)
    $(window).on('elementor/frontend/init', function () {
        // Future Elementor specific hooks
    });

});
