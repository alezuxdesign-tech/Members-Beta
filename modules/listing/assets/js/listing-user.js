jQuery(document).ready(function ($) {

    const $userWidget = $('.alezux-listing-user');
    if ($userWidget.length === 0) return;

    const $tasksList = $('#alezux-user-tasks-list');
    const $msgDiv = $('#alezux-user-msg');
    const emptyMsg = $userWidget.data('empty-msg') || 'No hay tareas pendientes.';

    function loadUserTasks() {
        // Verificar dummy data primero (para Elementor Editor)
        if ($userWidget.attr('data-dummy-tasks')) {
            try {
                const dummyTasks = JSON.parse($userWidget.attr('data-dummy-tasks'));
                if (dummyTasks && dummyTasks.length > 0) {
                    renderUserTasks(dummyTasks);
                    return; // Prevenimos AJAX
                }
            } catch (e) {
                console.error("Error parsing dummy tasks", e);
            }
        }

        $.ajax({
            url: alezux_listing_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'alezux_listing_get_tasks_user',
                nonce: alezux_listing_vars.nonce
            },
            success: function (response) {
                if (response.success) {
                    renderUserTasks(response.data);
                } else {
                    $tasksList.html('<div class="alezux-error-notice">Error al cargar tus tareas.</div>');
                }
            },
            error: function () {
                $tasksList.html('<div class="alezux-error-notice">Error de conexión. Intenta recargar la página.</div>');
            }
        });
    }

    function renderUserTasks(tasks) {
        $tasksList.empty();

        if (tasks.length === 0) {
            $tasksList.html(`<div class="alezux-empty-state">
                <i class="fas fa-check-circle" style="font-size: 40px; color: #2ed573; margin-bottom: 15px;"></i>
                <p>${emptyMsg}</p>
            </div>`);
            return;
        }

        tasks.forEach(task => {
            const html = `
                <div class="alezux-user-task-item" data-id="${task.id}">
                    <div class="user-task-content">
                        <strong class="user-task-title">${task.title}</strong>
                        ${task.description ? `<p class="user-task-desc">${task.description}</p>` : ''}
                    </div>
                    <div class="user-task-action">
                        <button class="alezux-btn alezux-btn-complete btn-complete-task">
                            <i class="fas fa-check btn-icon-idle"></i>
                            <i class="fas fa-spinner fa-spin btn-icon-loading" style="display: none;"></i>
                            <span class="btn-text">Completar</span>
                        </button>
                    </div>
                </div>
            `;
            $tasksList.append(html);
        });
    }

    loadUserTasks();

    // Completar Tarea
    $tasksList.on('click', '.btn-complete-task', function (e) {
        e.preventDefault();

        const $btn = $(this);
        const $taskItem = $btn.closest('.alezux-user-task-item');
        const taskId = $taskItem.data('id');

        if ($btn.prop('disabled')) return;

        $btn.prop('disabled', true);
        $btn.find('.btn-icon-idle').hide();
        $btn.find('.btn-icon-loading').show();

        $.ajax({
            url: alezux_listing_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'alezux_listing_complete_task',
                nonce: alezux_listing_vars.nonce,
                task_id: taskId
            },
            success: function (response) {
                if (response.success) {
                    // Animación de salida bonita y remover elemento
                    $taskItem.css('background-color', 'rgba(46, 213, 115, 0.1)')
                        .css('border-color', '#2ed573');

                    setTimeout(() => {
                        $taskItem.slideUp(300, function () {
                            $(this).remove();
                            // Verificar si quedan hijos
                            if ($tasksList.children('.alezux-user-task-item').length === 0) {
                                loadUserTasks(); // Mostrar empty state
                            }
                        });
                    }, 600);

                    showMsg(response.data.message, 'success');
                } else {
                    showMsg(response.data.message, 'error');
                    resetBtn($btn);
                }
            },
            error: function () {
                showMsg('Ocurrió un error en el servidor.', 'error');
                resetBtn($btn);
            }
        });
    });

    function resetBtn($btn) {
        $btn.prop('disabled', false);
        $btn.find('.btn-icon-idle').show();
        $btn.find('.btn-icon-loading').hide();
    }

    function showMsg(message, type) {
        $msgDiv.removeClass('success error')
            .addClass(type)
            .html(message)
            .slideDown();

        setTimeout(() => {
            $msgDiv.slideUp();
        }, 4000);
    }

});
