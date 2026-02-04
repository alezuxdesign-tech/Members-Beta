jQuery(document).ready(function ($) {
    const $form = $('#alezux-profile-update-form');
    const $avatarInput = $('#alezux-avatar-input');
    const $avatarPreview = $('#alezux-avatar-preview');
    const $submitBtn = $form.find('.alezux-submit-btn');

    // Previsualización de Avatar
    $avatarInput.on('change', function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                $avatarPreview.attr('src', e.target.result);
            }
            reader.readAsDataURL(file);
        }
    });

    // Envío del Formulario
    $form.on('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        $submitBtn.find('.btn-text').hide();
        $submitBtn.find('.btn-loader').show();
        $submitBtn.prop('disabled', true);

        $.ajax({
            url: alezux_auth_obj.ajax_url, // Usamos el objeto global ya definido en config.js
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    if (window.alezuxShowModal) {
                        window.alezuxShowModal('¡Éxito!', response.data.message, 'success');
                    } else {
                        alert(response.data.message);
                    }
                } else {
                    if (window.alezuxShowModal) {
                        window.alezuxShowModal('Error', response.data.message, 'error');
                    } else {
                        alert(response.data.message);
                    }
                }
            },
            error: function () {
                if (window.alezuxShowModal) {
                    window.alezuxShowModal('Error', 'Hubo un problema en el servidor.', 'error');
                } else {
                    alert('Error en el servidor.');
                }
            },
            complete: function () {
                $submitBtn.find('.btn-text').show();
                $submitBtn.find('.btn-loader').hide();
                $submitBtn.prop('disabled', false);
            }
        });
    });
});
