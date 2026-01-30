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

        $.ajax({
            url: ajax_url,
            type: 'POST',
            data: formData,
            success: function (response) {
                if (response.success) {
                    $msg.html('<div style="color: green; margin-top: 10px;">' + response.data.message + '</div>');
                    $form[0].reset();
                } else {
                    $msg.html('<div style="color: red; margin-top: 10px;">' + response.data.message + '</div>');
                }
            },
            error: function () {
                $msg.html('<div style="color: red; margin-top: 10px;">Error de conexión.</div>');
            },
            complete: function () {
                $btn.prop('disabled', false);
                $spinner.hide();
            }
        });
    });
});
