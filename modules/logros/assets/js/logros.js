jQuery(document).ready(function ($) {

    console.log('Alezux Logros: JS Inicializado');

    /**
     * ALEZUX MEDIA UPLOADER LOGIC
     */

    var file_frame;

    function updateUploadUI($box, attachment) {
        console.log('Alezux Logros: Actualizando UI', attachment);
        var $container = $box.closest('.alezux-logro-upload-container');
        var $input = $container.find('.alezux-logro-image-id');
        var $previewBox = $box.find('.alezux-upload-preview');
        var $placeholder = $box.find('.alezux-upload-placeholder');
        var $previewImg = $box.find('.alezux-preview-img');

        if (attachment) {
            $input.val(attachment.id);
            var url = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;
            $previewImg.attr('src', url);

            $placeholder.hide();
            $previewBox.css('display', 'flex').hide().fadeIn();
        } else {
            $input.val('');
            $previewImg.attr('src', '');
            $previewBox.hide();
            $placeholder.fadeIn();
        }
    }

    // --- 1. Click Event on Upload Box ---
    $('body').on('click', '.alezux-upload-box', function (event) {
        console.log('Alezux Logros: Click detectado en .alezux-upload-box');

        if ($(event.target).closest('.alezux-remove-img').length) {
            console.log('Alezux Logros: Click en remover imagen');
            event.preventDefault();
            return;
        }

        event.preventDefault();

        // Check wp object
        if (typeof wp === 'undefined') {
            console.error('Alezux Logros Error: Objeto "wp" no definido.');
            alert('Error crítico: WordPress JS no cargado.');
            return;
        }

        if (!wp.media) {
            console.error('Alezux Logros Error: wp.media no definido. ¿wp_enqueue_media() fue llamado?');
            console.log('Alezux Logros: Intentando fallback o revisión.');
            alert('Error: Librería de medios no disponible.');
            return;
        }

        console.log('Alezux Logros: wp.media disponible. Abriendo frame...');

        var $box = $(this);

        if (file_frame) {
            file_frame.open();
        } else {
            file_frame = wp.media.frames.file_frame = wp.media({
                title: 'Seleccionar Imagen del Logro',
                button: {
                    text: 'Usar esta imagen'
                },
                multiple: false
            });
        }

        file_frame.off('select');

        file_frame.on('select', function () {
            console.log('Alezux Logros: Imagen seleccionada');
            var attachment = file_frame.state().get('selection').first().toJSON();
            updateUploadUI($box, attachment);
        });

        file_frame.open();
    });

    $('body').on('click', '.alezux-remove-img', function (e) {
        console.log('Alezux Logros: Click directo en remove');
        e.preventDefault();
        e.stopPropagation();
        var $box = $(this).closest('.alezux-upload-box');
        updateUploadUI($box, null);
    });

    // --- 2. Drag & Drop ---
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
        console.log('Alezux Logros: Drop detectado');
        $(this).trigger('click');
    });

    // --- 3. Submit ---
    $(document).on('submit', '#alezux-logro-form', function (e) {
        console.log('Alezux Logros: Submit formulario');
        e.preventDefault();

        if (typeof alezux_logros_vars === 'undefined') {
            console.error('Alezux Logros Error: Vars no definidas');
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
                console.log('Alezux Logros: Respuesta AJAX', res);
                if (res.success) {
                    $response.html('<div class="alezux-success" style="padding:10px; background:#d1e7dd; color:#0f5132; border-radius:4px; margin-top:10px;"><i class="fas fa-check-circle"></i> ' + res.data.message + '</div>');
                    $form[0].reset();
                    $form.find('.alezux-upload-box').each(function () {
                        updateUploadUI($(this), null);
                    });
                } else {
                    $response.html('<div class="alezux-error" style="padding:10px; background:#f8d7da; color:#842029; border-radius:4px; margin-top:10px;"><i class="fas fa-exclamation-circle"></i> ' + (res.data.message || 'Error desconocido') + '</div>');
                }
            },
            error: function (xhr, status, error) {
                console.error('Alezux Logros: Error AJAX', error);
                $response.html('<div class="alezux-error" style="padding:10px; background:#f8d7da; color:#842029; border-radius:4px; margin-top:10px;"><i class="fas fa-exclamation-triangle"></i> Error de conexión: ' + error + '</div>');
            },
            complete: function () {
                $btn.prop('disabled', false).css('opacity', '1');
            }
        });
    });

});
