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
                    $('body').append('<div class="alezux-toast-container"></div>');
                    $container = $('.alezux-toast-container');
                }

                var typeClass = type === 'success' ? 'success' : (type === 'error' ? 'error' : '');
                var $toast = $('<div class="alezux-toast ' + typeClass + '">' +
                    '<span>' + message + '</span>' +
                    '<button class="alezux-toast-close">&times;</button>' +
                    '</div>');

                $container.append($toast);

                // Auto remove
                var timeout = setTimeout(function () {
                    removeToast($toast);
                }, 5000);

                // Manual close
                $toast.find('.alezux-toast-close').on('click', function () {
                    clearTimeout(timeout);
                    removeToast($toast);
                });

                function removeToast($t) {
                    $t.addClass('hiding');
                    $t.on('animationend', function () {
                        $t.remove();
                    });
                }
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
