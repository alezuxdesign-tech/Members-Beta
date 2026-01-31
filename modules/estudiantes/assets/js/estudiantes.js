jQuery(document).ready(function ($) {
    console.log('[Estudiantes] JS Loaded and Ready (v1.0.6)');

    // ==========================================================
    // ALERTAS MODALES PERSONALIZADAS
    // ==========================================================
    function showAlezuxAlert(title, message, type = 'info') {
        var $overlay = $('#alezux-alert-modal-overlay');
        var $icon = $('#alezux-alert-icon');
        var $title = $('#alezux-alert-title');
        var $msg = $('#alezux-alert-message');
        var $btnConfirm = $('#alezux-alert-confirm');
        var $btnCancel = $('#alezux-alert-cancel');

        // Reset
        $icon.removeClass('success error warning').addClass(type);
        if(type === 'success') $icon.html('<i class="fa fa-check-circle"></i>');
        if(type === 'error') $icon.html('<i class="fa fa-times-circle"></i>');
        if(type === 'warning') $icon.html('<i class="fa fa-exclamation-triangle"></i>');
        if(type === 'info') $icon.html('<i class="fa fa-info-circle"></i>');

        $title.text(title);
        $msg.html(message); // Allow HTML
        $btnCancel.hide();
        $btnConfirm.off('click').on('click', function() {
            $overlay.fadeOut();
        });

        $overlay.fadeIn().css('display', 'flex');
    }

    function showAlezuxConfirm(title, message, onConfirm) {
        var $overlay = $('#alezux-alert-modal-overlay');
        var $icon = $('#alezux-alert-icon');
        var $title = $('#alezux-alert-title');
        var $msg = $('#alezux-alert-message');
        var $btnConfirm = $('#alezux-alert-confirm');
        var $btnCancel = $('#alezux-alert-cancel');

        // Setup Warning Style
        $icon.removeClass('success error info').addClass('warning');
        $icon.html('<i class="fa fa-question-circle"></i>');

        $title.text(title);
        $msg.html(message);
        $btnCancel.show();

        // Handlers
        $btnConfirm.off('click').on('click', function() {
            $overlay.fadeOut();
            if (typeof onConfirm === 'function') onConfirm();
        });

        $btnCancel.off('click').on('click', function() {
            $overlay.fadeOut();
        });

        $overlay.fadeIn().css('display', 'flex');
    }

    // ==========================================================
    // GESTIÓN DE ESTUDIANTES (MODAL)
    // ==========================================================

    function loadStudentInfo(userId) {
        $('#alezux-modal-loading').show();
        $('#alezux-modal-content').hide();
        
        $.ajax({
            url: alezux_estudiantes_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'alezux_get_student_details',
                nonce: alezux_estudiantes_vars.nonce,
                user_id: userId
            },
            success: function(response) {
                if(response.success) {
                    var data = response.data;
                    
                    // Populate Form
                    $('#manage-first-name').val(data.first_name);
                    $('#manage-last-name').val(data.last_name);
                    $('#manage-email').val(data.email);
                    
                    // Block Status Button UI
                    updateBlockButton(data.is_blocked);
                    
                    // Render Courses Lists
                    renderCoursesLists(data.enrolled_courses, data.available_courses);

                    $('#alezux-modal-loading').hide();
                    $('#alezux-modal-content').fadeIn();
                } else {
                    showAlezuxAlert('Error', response.data.message, 'error');
                    $('#alezux-management-modal-overlay').fadeOut();
                }
            },
            error: function() {
                showAlezuxAlert('Error de Conexión', 'No se pudo conectar con el servidor.', 'error');
                $('#alezux-management-modal-overlay').fadeOut();
            }
        });
    }

    // 1. Abrir Modal
    $(document).on('click', '.btn-gestionar', function(e) {
        e.preventDefault();
        var userId = $(this).data('student-id');
        console.log('[Estudiantes] Gestionando usuario ID:', userId);
        
        $('#alezux-manage-user-id').val(userId);
        $('#alezux-management-modal-overlay').fadeIn(200).css('display', 'flex');
        
        loadStudentInfo(userId);
    });

    // Cerrar Modal
    $('#alezux-modal-close').on('click', function() {
        $('#alezux-management-modal-overlay').fadeOut();
    });

    // 2. Guardar Datos Personales
    $('#btn-save-student-data').on('click', function(e) {
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
            success: function(response) {
                if(response.success) {
                    showAlezuxAlert('Éxito', response.data.message, 'success');
                } else {
                    showAlezuxAlert('Error', response.data.message, 'error');
                }
            },
            complete: function() {
                $btn.prop('disabled', false);
                $spinner.hide();
            }
        });
    });

    // 3. Reset Password
    $('#btn-reset-password').on('click', function(e) {
        e.preventDefault();
        var userId = $('#alezux-manage-user-id').val();
        
        showAlezuxConfirm('Restablecer Contraseña', '¿Estás seguro? Esto generará una nueva contraseña y se la enviará por correo al estudiante inmediatamente.', function() {
            var $btn = $('#btn-reset-password');
            $btn.prop('disabled', true).text('Procesando...');

            $.ajax({
                url: alezux_estudiantes_vars.ajax_url,
                type: 'POST',
                data: {
                    action: 'alezux_reset_password',
                    nonce: alezux_estudiantes_vars.nonce,
                    user_id: userId
                },
                success: function(response) {
                    showAlezuxAlert('Proceso Completado', response.data.message, response.success ? 'success' : 'error');
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<i class="fa fa-key"></i> Restablecer Contraseña');
                }
            });
        });
    });

    // 4. Bloquear / Desbloquear
    $('#btn-block-user').on('click', function(e) {
        e.preventDefault();
        var isBlocked = $(this).data('is-blocked');
        var action = isBlocked ? 'unblock' : 'block';
        var title = isBlocked ? 'Desbloquear Usuario' : 'Bloquear Usuario';
        var msg = isBlocked ? '¿Deseas permitir el acceso a este estudiante nuevamente?' : '¿Realmente deseas bloquear el acceso a la academia para este estudiante?';

        showAlezuxConfirm(title, msg, function() {
            $.ajax({
                url: alezux_estudiantes_vars.ajax_url,
                type: 'POST',
                data: {
                    action: 'alezux_toggle_block_user',
                    nonce: alezux_estudiantes_vars.nonce,
                    user_id: $('#alezux-manage-user-id').val(),
                    block_action: action
                },
                success: function(response) {
                    if(response.success) {
                        showAlezuxAlert('Actualizado', response.data.message, 'success');
                        updateBlockButton(!isBlocked);
                    } else {
                        showAlezuxAlert('Error', response.data.message, 'error');
                    }
                }
            });
        });
    });

    // 5. Cursos: Quitar / Agregar
    $(document).on('click', '.btn-remove-access, .btn-grant-access', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var courseId = $btn.data('course-id');
        var courseName = $btn.closest('li').find('span').text(); // Get title for confirmation
        var isGranting = $btn.hasClass('btn-grant-access');
        var action = isGranting ? 'add' : 'remove';
        var userId = $('#alezux-manage-user-id').val();
        var originalText = $btn.text();

        // Confirmación para QUITAR acceso (más delicado)
        if (!isGranting) {
            showAlezuxConfirm('Quitar Acceso', '¿Estás seguro de quitar el acceso al curso: <b>' + courseName + '</b>?', function() {
                 executeCourseUpdate();
            });
        } else {
            // Confirmación para AGREGAR (opcional, pero consistente)
            // showAlezuxConfirm('Conceder Acceso', '¿Dar acceso al curso: <b>' + courseName + '</b>?', function() {
            //      executeCourseUpdate();
            // });
            // Por UX, agregar suele ser directo, pero el usuario pidió modal para todo.
            // Voy a poner modal también para agregar para cumplir "Todas las alertas tienen que ser personalizatas".
             showAlezuxConfirm('Conceder Acceso', '¿Conceder acceso al curso: <b>' + courseName + '</b>?', function() {
                 executeCourseUpdate();
            });
        }

        function executeCourseUpdate() {
            $btn.prop('disabled', true).text('...');
            $.ajax({
                url: alezux_estudiantes_vars.ajax_url,
                type: 'POST',
                data: {
                    action: 'alezux_update_course_access',
                    nonce: alezux_estudiantes_vars.nonce,
                    user_id: userId,
                    course_id: courseId,
                    access_action: action
                },
                success: function(response) {
                    if(response.success) {
                        showAlezuxAlert('Curso Actualizado', response.data.message, 'success');
                        // RECARGAR DATOS DEL ESTUDIANTE PARA ACTUALIZAR LISTAS
                        loadStudentInfo(userId);
                    } else {
                        showAlezuxAlert('Error', response.data.message, 'error');
                        $btn.prop('disabled', false).text(originalText);
                    }
                },
                error: function() {
                    showAlezuxAlert('Error', 'Fallo de conexión', 'error');
                    $btn.prop('disabled', false).text(originalText);
                }
            });
        }
    });

    // --- Helpers ---

    function updateBlockButton(isBlocked) {
        var $btn = $('#btn-block-user');
        $btn.data('is-blocked', isBlocked);
        if(isBlocked) {
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

        if(enrolled.length === 0) {
            $('#no-enrolled-msg').show();
        } else {
            $('#no-enrolled-msg').hide();
            enrolled.forEach(function(c) {
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

        available.forEach(function(c) {
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

});
