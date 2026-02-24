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
                        <button class="alezux-btn-icon btn-delete-task" title="Eliminar Tarea"><i class="fas fa-trash"></i></button>
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

    // Delete Task
    $tasksList.on('click', '.btn-delete-task', function (e) {
        e.preventDefault();

        if (!confirm("¿Seguro que deseas eliminar esta tarea global? Esto no se puede deshacer y borrará el progreso de los usuarios.")) {
            return;
        }

        const $taskItem = $(this).closest('.alezux-task-item');
        const taskId = $taskItem.data('id');
        const $btn = $(this);

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
                    $taskItem.fadeOut(300, function () { $(this).remove(); });
                } else {
                    alert(response.data.message);
                    $btn.html('<i class="fas fa-trash"></i>').prop('disabled', false);
                }
            },
            error: function () {
                alert('Error al intentar borrar la tarea.');
                $btn.html('<i class="fas fa-trash"></i>').prop('disabled', false);
            }
        });
    });

});
