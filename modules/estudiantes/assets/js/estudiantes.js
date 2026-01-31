jQuery(document).ready(function ($) {
    console.log('[Estudiantes] JS Loaded and Ready (v1.0.5)');
    var timer;

    // Search Logic
    $('.alezux-estudiantes-search input').on('keyup', function () {
        var $input = $(this);
        var $wrapper = $input.closest('.alezux-estudiantes-wrapper');
        // Simple client-side filter or Ajax hook could be here. 
        // For now keeping structure as requested in previous contexts.
    });

    // ==========================================================
    // GESTIÓN DE ESTUDIANTES (MODAL)
    // ==========================================================

    // 1. Abrir Modal y Cargar Datos
    $(document).on('click', '.btn-gestionar', function (e) {
        e.preventDefault();
        var userId = $(this).data('student-id');
        console.log('[Estudiantes] Click en gestionar. User ID:', userId);

        $('#alezux-management-modal-overlay').fadeIn(200).css('display', 'flex');
        $('#alezux-modal-loading').show();
        $('#alezux-modal-content').hide();
        $('#alezux-manage-user-id').val(userId);

        // Fetch Data
        $.ajax({
            url: alezux_estudiantes_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'alezux_get_student_details',
                nonce: alezux_estudiantes_vars.nonce,
                user_id: userId
            },
            success: function (response) {
                if (response.success) {
                    var data = response.data;

                    // Populate Form
                    $('#manage-first-name').val(data.first_name);
                    $('#manage-last-name').val(data.last_name);
                    $('#manage-email').val(data.email);

                    // Block Status
                    updateBlockButton(data.is_blocked);

                    // Render Courses
                    renderCoursesLists(data.enrolled_courses, data.available_courses);

                    $('#alezux-modal-loading').hide();
                    $('#alezux-modal-content').fadeIn();
                } else {
                    alert('Error al cargar datos: ' + response.data.message);
                    $('#alezux-management-modal-overlay').fadeOut();
                }
            },
            error: function () {
                alert('Error de conexión');
                $('#alezux-management-modal-overlay').fadeOut();
            }
        });
    });

    // Cerrar Modal
    $('#alezux-modal-close').on('click', function () {
        $('#alezux-management-modal-overlay').fadeOut();
    });

    // 2. Guardar Datos Personales
    $('#btn-save-student-data').on('click', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var $spinner = $btn.find('.alezux-spinner');

        $btn.prop('disabled', true);
        $spinner.show();

        $.ajax({
            url: alezux_estudiantes_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'alezux_update_student',
                nonce: alezux_estudiantes_vars.nonce,
                user_id: $('#alezux-manage-user-id').val(),
                first_name: $('#manage-first-name').val(),
                last_name: $('#manage-last-name').val(),
                email: $('#manage-email').val()
            },
            success: function (response) {
                if (response.success) {
                    showToastOrAlert(response.data.message, 'success');
                } else {
                    showToastOrAlert(response.data.message, 'error');
                }
            },
            complete: function () {
                $btn.prop('disabled', false);
                $spinner.hide();
            }
        });
    });

    // 3. Reset Password
    $('#btn-reset-password').on('click', function (e) {
        e.preventDefault();
        if (!confirm('¿Estás seguro? Esto cambiará la contraseña inmediatamente y enviará un correo al usuario.')) return;

        var $btn = $(this);
        $btn.prop('disabled', true).text('Procesando...');

        $.ajax({
            url: alezux_estudiantes_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'alezux_reset_password',
                nonce: alezux_estudiantes_vars.nonce,
                user_id: $('#alezux-manage-user-id').val()
            },
            success: function (response) {
                showToastOrAlert(response.data.message, response.success ? 'success' : 'error');
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="fa fa-key"></i> Restablecer Contraseña');
            }
        });
    });

    // 4. Bloquear / Desbloquear
    $('#btn-block-user').on('click', function (e) {
        e.preventDefault();
        var isBlocked = $(this).data('is-blocked');
        var action = isBlocked ? 'unblock' : 'block';
        var confirmMsg = isBlocked ? '¿Desbloquear acceso?' : '¿Bloquear acceso a la academia?';

        if (!confirm(confirmMsg)) return;

        var $btn = $(this);

        $.ajax({
            url: alezux_estudiantes_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'alezux_toggle_block_user',
                nonce: alezux_estudiantes_vars.nonce,
                user_id: $('#alezux-manage-user-id').val(),
                block_action: action
            },
            success: function (response) {
                if (response.success) {
                    showToastOrAlert(response.data.message, 'success');
                    updateBlockButton(!isBlocked);
                } else {
                    showToastOrAlert(response.data.message, 'error');
                }
            }
        });
    });

    // 5. Cursos: Quitar / Agregar
    $(document).on('click', '.btn-remove-access, .btn-grant-access', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var courseId = $btn.data('course-id');
        var action = $btn.hasClass('btn-grant-access') ? 'add' : 'remove';
        var btnText = $btn.text();

        $btn.prop('disabled', true).text('...');

        $.ajax({
            url: alezux_estudiantes_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'alezux_update_course_access',
                nonce: alezux_estudiantes_vars.nonce,
                user_id: $('#alezux-manage-user-id').val(),
                course_id: courseId,
                access_action: action
            },
            success: function (response) {
                if (response.success) {
                    showToastOrAlert(response.data.message, 'success');
                    // Refresh data to update lists
                    $('.btn-gestionar[data-student-id="' + $('#alezux-manage-user-id').val() + '"]').click();
                } else {
                    showToastOrAlert(response.data.message, 'error');
                    $btn.prop('disabled', false).text(btnText);
                }
            },
            error: function () {
                $btn.prop('disabled', false).text(btnText);
            }
        });
    });

    // --- Helpers ---

    function updateBlockButton(isBlocked) {
        var $btn = $('#btn-block-user');
        $btn.data('is-blocked', isBlocked);
        if (isBlocked) {
            $btn.removeClass('alezux-btn-danger').addClass('alezux-btn-primary');
            $btn.html('<i class="fa fa-unlock"></i> Desbloquear Acceso');
            $btn.css('background-color', '#10b981');
        } else {
            $btn.removeClass('alezux-btn-primary').addClass('alezux-btn-danger');
            $btn.html('<i class="fa fa-ban"></i> Bloquear Acceso Academia');
            $btn.css('background-color', '');
        }
    }

    function renderCoursesLists(enrolled, available) {
        var $enrolledList = $('#list-enrolled-courses');
        var $availableList = $('#list-available-courses');

        $enrolledList.empty();
        $availableList.empty();

        if (enrolled.length === 0) {
            $('#no-enrolled-msg').show();
        } else {
            $('#no-enrolled-msg').hide();
            enrolled.forEach(function (c) {
                var item = `
                    <li class="alezux-course-item">
                        <span>${c.title}</span>
                        <div class="alezux-course-actions">
                            <button class="btn-remove-access" data-course-id="${c.id}">Quitar Acceso</button>
                        </div>
                    </li>
                `;
                $enrolledList.append(item);
            });
        }

        available.forEach(function (c) {
            var item = `
                <li class="alezux-course-item">
                    <span>${c.title}</span>
                    <div class="alezux-course-actions">
                        <button class="btn-grant-access" data-course-id="${c.id}">Conceder Acceso</button>
                    </div>
                </li>
            `;
            $availableList.append(item);
        });
    }

    function showToastOrAlert(msg, type) {
        if (window.alezuxShowToast) {
            alezuxShowToast(msg, type);
        } else {
            alert(msg);
        }
    }

});
