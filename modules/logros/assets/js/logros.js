jQuery(document).ready(function ($) {

    // --- Logic for Admin Form ---

    // Media Uploader
    var file_frame;
    $('#btn-upload-logro-img').on('click', function (event) {
        event.preventDefault();

        // If the media frame already exists, reopen it.
        if (file_frame) {
            file_frame.open();
            return;
        }

        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
            title: 'Seleccionar Imagen del Logro',
            button: {
                text: 'Usar esta imagen'
            },
            multiple: false
        });

        // When an image is selected, run a callback.
        file_frame.on('select', function () {
            var attachment = file_frame.state().get('selection').first().toJSON();

            // Set ID
            $('#logro-image-id').val(attachment.id);

            // Show Preview
            var url = attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
            $('#logro-image-preview img').attr('src', url);
            $('#logro-image-preview').show();
        });

        file_frame.open();
    });

    // Form Submission
    $('#alezux-logro-form').on('submit', function (e) {
        e.preventDefault();

        var $form = $(this);
        var $response = $('#alezux-logro-response');
        var $btn = $form.find('button[type="submit"]');

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
                    $response.html('<div class="alezux-success" style="color: green; margin-top: 10px;">' + res.data.message + '</div>');
                    $form[0].reset();
                    $('#logro-image-preview').hide();
                    $('#logro-image-id').val('');
                } else {
                    $response.html('<div class="alezux-error" style="color: red; margin-top: 10px;">' + res.data.message + '</div>');
                }
            },
            error: function () {
                $response.html('<div class="alezux-error" style="color: red; margin-top: 10px;">Error del servidor.</div>');
            },
            complete: function () {
                $btn.prop('disabled', false).text('Guardar Logro');
            }
        });
    });

    // --- Logic for Frontend Grid Popup ---

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

        // Show (Flex to center)
        $popup.css('display', 'flex').hide().fadeIn(200);
    });

    $(document).on('click', '.alezux-popup-close, .alezux-logro-popup-overlay', function (e) {
        // Close if clicked on overlay (this) or close button
        if (e.target === this || $(e.target).hasClass('alezux-popup-close')) {
            $(this).closest('.alezux-logro-popup-overlay').fadeOut(200);
            // If 'this' is the overlay
            if ($(this).hasClass('alezux-logro-popup-overlay')) {
                $(this).fadeOut(200);
            }
        }
    });

    // Prevent close when clicking content
    $('.alezux-logro-popup-content').on('click', function (e) {
        e.stopPropagation();
    });

});
