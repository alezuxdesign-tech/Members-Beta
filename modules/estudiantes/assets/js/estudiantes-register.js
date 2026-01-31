jQuery(document).ready(function ($) {
    var ajax_url = alezux_estudiantes_vars.ajax_url;
    var nonce = alezux_estudiantes_vars.nonce;

    /**
     * ================================
     * Lógica de Registro Manual
     * ================================
     */
    $('#alezux-manual-register-form').on('submit', function (e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $form.find('button[type="submit"]');
        var $msg = $form.find('.alezux-form-message');
        var $spinner = $btn.find('i.fa-spinner');

        $btn.prop('disabled', true);
        $spinner.show();
        $msg.html('');

        var formData = {
            action: 'alezux_register_student',
            nonce: nonce,
            first_name: $form.find('input[name="first_name"]').val(),
            last_name: $form.find('input[name="last_name"]').val(),
            email: $form.find('input[name="email"]').val(),
            course_id: $form.find('select[name="course_id"]').val()
        };

        if (typeof alezuxShowToast === 'undefined') {
            // Define global helper if not exists (local scope wrapper)
            window.alezuxShowToast = function (message, type) {
                var $container = $('.alezux-toast-container');
                if ($container.length === 0) {
                    $('body').append('<div class="alezux-toast-container" style="display:none"></div>');
                    $container = $('.alezux-toast-container');
                }

                // Show container (backdrop)
                $container.fadeIn(200).css('display', 'flex');

                var typeClass = type === 'success' ? 'success' : 'error';
                var titleText = type === 'success' ? '¡Excelente!' : '¡Atención!';

                // Limpiar alertas previas
                $container.empty();

                var $toast = $(
                    '<div class="alezux-toast ' + typeClass + '">' +
                    '<button class="alezux-toast-close">&times;</button>' +
                    '<h3 class="alezux-toast-title">' + titleText + '</h3>' +
                    '<div class="alezux-toast-message">' + message + '</div>' +
                    '<button class="alezux-toast-action-btn">Entendido</button>' +
                    '</div>'
                );

                $container.append($toast);

                // Interactions
                var closeAlert = function () {
                    $container.fadeOut(200, function () {
                        $container.empty();
                    });
                };

                $toast.find('.alezux-toast-close, .alezux-toast-action-btn').on('click', closeAlert);

                // Close on backdrop click
                $container.on('click', function (e) {
                    if (e.target === this) {
                        closeAlert();
                    }
                });
            };
        }

        $.ajax({
            url: ajax_url,
            type: 'POST',
            data: formData,
            success: function (response) {
                if (response.success) {
                    window.alezuxShowToast(response.data.message, 'success');
                    $form[0].reset();
                } else {
                    window.alezuxShowToast(response.data.message || 'Error desconocido.', 'error');
                }
            },
            error: function () {
                window.alezuxShowToast('Error de conexión con el servidor.', 'error');
            },
            complete: function () {
                $btn.prop('disabled', false);
                $spinner.hide();
            }
        });
    });
});
