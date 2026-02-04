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

    // --- AJAX RESET PASSWORD (NEW) ---
    $(document).on('submit', '#alezux-reset-password-form', function (e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $form.find('.alezux-submit-btn'); // Note: Class is different in Reset Widget usually? Checked PHP: alezux-submit-btn
        var $loader = $btn.find('.btn-loader');
        var $text = $btn.find('.btn-text');

        $btn.prop('disabled', true);
        $text.hide();
        $loader.show();

        var formData = $form.serialize();
        // Action is already in hidden input but we ensure it matches the PHP hook
        // In PHP: wp_ajax_alezux_reset_password
        // In Form hidden input: value="alezux_reset_password" (Check Reset_Widget loop if visible)
        // Reset_Widget.php line 413: <input type="hidden" name="action" value="alezux_reset_password"> - OK
        // Nonce is also in hidden input

        $.ajax({
            type: 'POST',
            url: alezux_auth_obj.ajax_url,
            data: formData,
            success: function (response) {
                $btn.prop('disabled', false);
                $text.show();
                $loader.hide();

                if (response.success) {
                    window.alezuxShowModal('¡Contraseña Restablecida!', response.data.message, 'success');
                    // Reset form and redirection
                    $form[0].reset();
                    if (response.data.redirect) {
                        setTimeout(function () {
                            window.location.href = response.data.redirect;
                        }, 2000);
                    }
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

    // --- RESET PASSWORD STRENGTH METER ---
    $(document).on('input', '#alezux-reset-password-form #pass1', function () {
        var val = $(this).val();
        var strength = 0;
        var $form = $(this).closest('form');
        var $container = $form.find('.password-strength-wrapper');
        var $meterFill = $container.find('.meter-fill');

        var requirements = {
            length: val.length >= 8,
            upper: /[A-Z]/.test(val),
            number: /[0-9]/.test(val),
            special: /[^A-Za-z0-9]/.test(val)
        };

        $.each(requirements, function (req, met) {
            var $li = $container.find('.password-requirements li[data-req="' + req + '"]');
            if (met) {
                $li.addClass('met');
                strength++;
            } else {
                $li.removeClass('met');
            }
        });

        $meterFill.removeClass('strength-weak strength-fair strength-good strength-strong').css('width', '');

        if (val.length > 0) {
            if (strength <= 1) $meterFill.addClass('strength-weak').css('width', '25%');
            else if (strength === 2) $meterFill.addClass('strength-fair').css('width', '50%');
            else if (strength === 3) $meterFill.addClass('strength-good').css('width', '75%');
            else if (strength === 4) $meterFill.addClass('strength-strong').css('width', '100%');
        } else {
            $meterFill.css('width', '0');
        }
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
