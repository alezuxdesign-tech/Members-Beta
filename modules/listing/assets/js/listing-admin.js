jQuery(document).ready(function ($) {

    const $adminForm = $('#alezux-add-task-form');
    const $msgDiv = $('#alezux-task-form-msg');
    const $submitBtn = $('#alezux-submit-task-btn');
    const $tasksList = $('#alezux-admin-tasks-list');

    if ($adminForm.length === 0) return;

    // Load Tasks on INIT
    function loadTasks() {
        $.ajax({
            url: alezux_listing_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'alezux_listing_get_tasks_admin',
                nonce: alezux_listing_vars.nonce
            },
            success: function (response) {
                if (response.success) {
                    renderTasks(response.data);
                } else {
                    $tasksList.html('<div class="alezux-error-notice">Error al cargar tareas.</div>');
                }
            },
            error: function () {
                $tasksList.html('<div class="alezux-error-notice">Error de red al cargar tareas.</div>');
            }
        });
    }

    function renderTasks(tasks) {
        $tasksList.empty();

        if (tasks.length === 0) {
            $tasksList.html('<div class="alezux-no-tasks">Aún no has creado ninguna tarea.</div>');
            return;
        }

        tasks.forEach(task => {
            const html = `
                <div class="alezux-task-item" data-id="${task.id}">
                    <div class="task-info">
                        <strong class="task-title">${task.title}</strong>
                        ${task.description ? `<p class="task-desc">${task.description}</p>` : ''}
                        <small class="task-meta">Creada: ${task.formatted_date}</small>
                    </div>
                    <div class="task-actions">
                        <button class="alezux-btn-icon btn-edit-task" title="Editar Tarea" style="background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.1); width: 35px; height: 35px; border-radius: 8px; cursor: pointer; transition: all 0.3s; margin-right: 5px;"><i class="fas fa-edit"></i></button>
                        <button class="alezux-btn-icon btn-delete-task" title="Eliminar Tarea" style="background: rgba(255,71,87,0.1); color: #ff4757; border: 1px solid rgba(255,71,87,0.2); width: 35px; height: 35px; border-radius: 8px; cursor: pointer; transition: all 0.3s;"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            `;
            $tasksList.append(html);
        });
    }

    loadTasks();

    // Add Task
    $adminForm.on('submit', function (e) {
        e.preventDefault();

        const title = $('#task_title').val();
        const description = $('#task_description').val();

        if (!title) return;

        $submitBtn.prop('disabled', true);
        $submitBtn.find('.btn-text').hide();
        $submitBtn.find('.btn-icon').show();
        $msgDiv.removeClass('success error').hide();

        $.ajax({
            url: alezux_listing_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'alezux_listing_add_task',
                nonce: alezux_listing_vars.nonce,
                title: title,
                description: description
            },
            success: function (response) {
                if (response.success) {
                    $msgDiv.addClass('success').text(response.data.message).fadeIn();
                    $adminForm[0].reset();
                    loadTasks(); // reload
                } else {
                    $msgDiv.addClass('error').text(response.data.message).fadeIn();
                }
            },
            error: function () {
                $msgDiv.addClass('error').text('Ocurrió un error en el servidor.').fadeIn();
            },
            complete: function () {
                $submitBtn.prop('disabled', false);
                $submitBtn.find('.btn-text').show();
                $submitBtn.find('.btn-icon').hide();
                setTimeout(() => $msgDiv.fadeOut(), 4000);
            }
        });
    });

    // === Modals Logic & Notifications ===
    function showNotification(msg, type) {
        // En vez de un alert plano, usaremos un pequeño DOM flotante dinámico
        const id = 'notif-' + Date.now();
        const color = type === 'error' ? '#ff4757' : '#2ed573';
        const bg = type === 'error' ? 'rgba(255, 71, 87, 0.1)' : 'rgba(46, 213, 115, 0.1)';

        const markup = `
            <div id="${id}" style="position: fixed; bottom: 20px; right: 20px; z-index: 99999; background: ${bg}; border: 1px solid ${color}; color: ${color}; padding: 15px 25px; border-radius: 12px; font-weight: 600; box-shadow: 0 10px 30px rgba(0,0,0,0.5); display: none; align-items: center; gap: 10px;">
                <i class="fas ${type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle'}"></i> 
                ${msg}
            </div>
        `;
        $('body').append(markup);
        $(`#${id}`).fadeIn(300);
        setTimeout(() => {
            $(`#${id}`).fadeOut(300, function () { $(this).remove(); });
        }, 4000);
    }

    // Modal de Confirmación
    function customConfirm(message, callback) {
        const id = 'confirm-' + Date.now();
        const markup = `
            <div id="${id}" class="alezux-modal-overlay" style="display:flex;">
                <div class="alezux-modal-content" style="max-width: 400px; text-align: center;">
                    <div style="font-size: 40px; color: #ff4757; margin-bottom: 15px;"><i class="fas fa-exclamation-triangle"></i></div>
                    <h3 style="margin-bottom: 10px;">¿Estás seguro?</h3>
                    <p style="color: #a0a0a0; margin-bottom: 25px; line-height: 1.5;">${message}</p>
                    <div style="display: flex; gap: 10px; justify-content: center;">
                        <button class="alezux-btn btn-cancel-confirm" style="background: transparent; border: 1px solid #333; color: #fff;">Cancelar</button>
                        <button class="alezux-btn btn-accept-confirm" style="background: #ff4757; color: #fff;">Sí, Eliminar</button>
                    </div>
                </div>
            </div>
        `;
        $('body').append(markup);

        $(`#${id} .btn-cancel-confirm`).on('click', function (e) {
            e.preventDefault();
            $(`#${id}`).fadeOut(200, function () { $(this).remove(); });
        });

        $(`#${id} .btn-accept-confirm`).on('click', function (e) {
            e.preventDefault();
            $(`#${id}`).fadeOut(200, function () { $(this).remove(); });
            if (typeof callback === 'function') callback();
        });
    }

    // Delete Task
    $tasksList.on('click', '.btn-delete-task', function (e) {
        e.preventDefault();

        const $taskItem = $(this).closest('.alezux-task-item');
        const taskId = $taskItem.data('id');
        const $btn = $(this);

        customConfirm("Esto no se puede deshacer y borrará el progreso de los usuarios que hayan marcado esta tarea.", function () {
            $btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

            $.ajax({
                url: alezux_listing_vars.ajax_url,
                type: 'POST',
                data: {
                    action: 'alezux_listing_delete_task',
                    nonce: alezux_listing_vars.nonce,
                    id: taskId
                },
                success: function (response) {
                    if (response.success) {
                        showNotification("Tarea eliminada correctamente.", "success");
                        $taskItem.fadeOut(300, function () { $(this).remove(); });
                    } else {
                        showNotification(response.data.message, "error");
                        $btn.html('<i class="fas fa-trash"></i>').prop('disabled', false);
                    }
                },
                error: function () {
                    showNotification('Error al intentar borrar la tarea.', 'error');
                    $btn.html('<i class="fas fa-trash"></i>').prop('disabled', false);
                }
            });
        });
    });

    // === Edit Task ===
    const $editModal = $('#alezux-edit-task-modal');
    const $editForm = $('#alezux-edit-task-form');

    $('.alezux-modal-close').on('click', function () {
        $(this).closest('.alezux-modal-overlay').fadeOut();
    });

    $tasksList.on('click', '.btn-edit-task', function (e) {
        e.preventDefault();
        const $taskItem = $(this).closest('.alezux-task-item');

        // Poner datos en formulario
        $('#edit_task_id').val($taskItem.data('id'));
        $('#edit_task_title').val($taskItem.find('.task-title').text());
        $('#edit_task_description').val($taskItem.find('.task-desc').text());

        $editModal.fadeIn().css('display', 'flex');
    });

    $editForm.on('submit', function (e) {
        e.preventDefault();

        const $btn = $('#alezux-submit-edit-task-btn');
        const id = $('#edit_task_id').val();
        const title = $('#edit_task_title').val();
        const description = $('#edit_task_description').val();

        if (!title) return;

        $btn.prop('disabled', true);
        $btn.find('.btn-text').hide();
        $btn.find('.btn-icon').show();

        $.ajax({
            url: alezux_listing_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'alezux_listing_edit_task',
                nonce: alezux_listing_vars.nonce,
                id: id,
                title: title,
                description: description
            },
            success: function (response) {
                if (response.success) {
                    showNotification(response.data.message, 'success');
                    $editModal.fadeOut();
                    loadTasks();
                } else {
                    showNotification(response.data.message, 'error');
                }
            },
            error: function () {
                showNotification('Ocurrió un error en el servidor.', 'error');
            },
            complete: function () {
                $btn.prop('disabled', false);
                $btn.find('.btn-text').show();
                $btn.find('.btn-icon').hide();
            }
        });
    });

});
