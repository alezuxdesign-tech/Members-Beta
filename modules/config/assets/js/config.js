jQuery(document).ready(function ($) {
    // --- DROPDOWN PROFILE WIDGET ---
    $(document).on('click', '.alezux-config-header', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var $wrapper = $(this).closest('.alezux-config-card');
        $wrapper.toggleClass('active');
        $('.alezux-config-card').not($wrapper).removeClass('active');
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('.alezux-config-card').length) {
            $('.alezux-config-card').removeClass('active');
        }
    });

    // --- AJAX LOGIN ---
    $(document).on('submit', '#alezux-login-form', function (e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $form.find('.alezux-auth-submit');
        var $loader = $btn.find('.alezux-loader');
        var $text = $btn.find('.alezux-btn-text');

        $btn.prop('disabled', true);
        $text.hide();
        $loader.show();

        var formData = $form.serialize();
        formData += '&action=alezux_ajax_login&nonce=' + alezux_auth_obj.nonce;

        $.ajax({
            type: 'POST',
            url: alezux_auth_obj.ajax_url,
            data: formData,
            success: function (response) {
                if (response.success) {
                    window.location.href = response.data.redirect;
                } else {
                    $btn.prop('disabled', false);
                    $text.show();
                    $loader.hide();
                    alezuxShowModal('Error', response.data.message, 'error');
                }
            },
            error: function () {
                $btn.prop('disabled', false);
                $text.show();
                $loader.hide();
                alezuxShowModal('Error', 'Hubo un problema técnico. Inténtalo más tarde.', 'error');
            }
        });
    });

    // --- AJAX RECOVER PASSWORD ---
    $(document).on('submit', '#alezux-recover-form', function (e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $form.find('.alezux-auth-submit');
        var $loader = $btn.find('.alezux-loader');
        var $text = $btn.find('.alezux-btn-text');

        $btn.prop('disabled', true);
        $text.hide();
        $loader.show();

        var formData = $form.serialize();
        formData += '&action=alezux_ajax_recover&nonce=' + alezux_auth_obj.nonce;

        $.ajax({
            type: 'POST',
            url: alezux_auth_obj.ajax_url,
            data: formData,
            success: function (response) {
                $btn.prop('disabled', false);
                $text.show();
                $loader.hide();
                if (response.success) {
                    window.alezuxShowModal('Éxito', response.data.message, 'success');
                    $form[0].reset();
                } else {
                    alezuxShowModal('Error', response.data.message, 'error');
                }
            },
            error: function () {
                $btn.prop('disabled', false);
                $text.show();
                $loader.hide();
                alezuxShowModal('Error', 'Hubo un problema técnico. Inténtalo más tarde.', 'error');
            }
        });
    });

    // --- CUSTOM MODAL FUNCTIONS ---
    window.alezuxShowModal = function (title, message, type) {
        // Remove existing modal if any
        $('.alezux-modal-overlay').remove();

        var icon = type === 'success' ? '✅' : '❌';
        var iconClass = type === 'success' ? 'success' : 'error';

        var modalHtml = `
            <div class="alezux-modal-overlay">
                <div class="alezux-modal-card">
                    <div class="alezux-modal-icon ${iconClass}">${icon}</div>
                    <h3 class="alezux-modal-title">${title}</h3>
                    <p class="alezux-modal-message">${message}</p>
                    <button class="alezux-modal-close">Cerrar</button>
                </div>
            </div>
        `;

        $('body').append(modalHtml);

        // Trigger reflow for animation
        setTimeout(function () {
            $('.alezux-modal-overlay').addClass('active');
        }, 10);
    }

    $(document).on('click', '.alezux-modal-close, .alezux-modal-overlay', function (e) {
        if (e.target !== this && !$(e.target).hasClass('alezux-modal-close')) return;

        $('.alezux-modal-overlay').removeClass('active');
        setTimeout(function () {
            $('.alezux-modal-overlay').remove();
        }, 300);
    });

    // --- TOGGLE PASSWORD VISIBILITY ---
    $(document).on('click', '.alezux-toggle-password', function (e) {
        e.preventDefault();
        var $wrapper = $(this).closest('.alezux-input-wrapper');
        var $input = $wrapper.find('input');
        var $openIcon = $(this).find('.eye-open');
        var $closedIcon = $(this).find('.eye-closed');

        if ($input.attr('type') === 'password') {
            $input.attr('type', 'text');
            $openIcon.hide();
            $closedIcon.show();
        } else {
            $input.attr('type', 'password');
            $openIcon.show();
            $closedIcon.hide();
        }
    });
});
