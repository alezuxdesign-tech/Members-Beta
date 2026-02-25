jQuery(document).ready(function ($) {

    // Helper functions for Notifications & Modals
    function showNotification(msg, type) {
        const id = 'notif-' + Date.now();
        const color = type === 'error' ? '#ff4757' : '#2ed573';
        const bg = type === 'error' ? 'rgba(255, 71, 87, 0.1)' : 'rgba(46, 213, 115, 0.1)';

        const markup = `
            <div id="${id}" style="position: fixed; bottom: 20px; right: 20px; z-index: 999999; background: ${bg}; border: 1px solid ${color}; color: ${color}; padding: 15px 25px; border-radius: 12px; font-weight: 600; box-shadow: 0 10px 30px rgba(0,0,0,0.5); display: none; align-items: center; gap: 10px;">
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
        // Eliminar confirmaciones pasadas si existen
        $('.confirm-modal-alezux').remove();

        const id = 'confirm-' + Date.now();
        const markup = `
            <div id="${id}" class="alezux-listing-modal-overlay confirm-modal-alezux" style="display: flex; z-index: 999999;">
                <div class="alezux-listing-modal-content" style="max-width: 400px; text-align: center;">
                    <div style="font-size: 40px; color: #ff4757; margin-bottom: 15px;"><i class="fas fa-exclamation-triangle"></i></div>
                    <h3 style="margin-bottom: 10px;">¿Estás seguro?</h3>
                    <p style="color: #a0a0a0; margin-bottom: 25px; line-height: 1.5;">${message}</p>
                    <div style="display: flex; gap: 10px; justify-content: center;">
                        <span class="alezux-btn btn-cancel-confirm" role="button" tabindex="0" style="background: transparent; border: 1px solid #333; color: #fff; padding: 10px 20px; border-radius: 8px; cursor: pointer;">Cancelar</span>
                        <span class="alezux-btn btn-accept-confirm" role="button" tabindex="0" style="background: #ff4757; color: #fff; padding: 10px 20px; border-radius: 8px; cursor: pointer;">Sí, Eliminar</span>
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

    // Load Tasks Logic (Independent from static variables to survive Elementor re-render)
    function loadTasksForWidget($widget) {
        const $tasksList = $widget.find('#alezux-admin-tasks-list');
        if ($tasksList.length === 0) return;

        // Evitar carga AJAX en el editor de Elementor si se están renderizando datos dummy por PHP
        if ($('body').hasClass('elementor-editor-active') && $tasksList.find('.alezux-task-item').length > 0) {
            return;
        }

        $tasksList.html('<div class="alezux-loading-tasks"><i class="fas fa-circle-notch fa-spin"></i> Cargando tareas...</div>');

        const iconEdit = $widget.attr('data-icon-edit') || '<i class="fas fa-edit"></i>';
        const iconDelete = $widget.attr('data-icon-delete') || '<i class="fas fa-trash"></i>';

        // Set Loading state
        $tasksList.html('<div class="alezux-loading-tasks" style="padding: 20px; text-align: center; color: #a0a0a0;"><i class="fas fa-circle-notch fa-spin"></i> Cargando tareas...</div>');

        $.ajax({
            url: alezux_listing_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'alezux_listing_get_tasks_admin',
                nonce: alezux_listing_vars.nonce
            },
            success: function (response) {
                if (response.success) {
                    renderTasks($tasksList, response.data, iconEdit, iconDelete);
                } else {
                    $tasksList.html('<div class="alezux-error-notice">Error al cargar tareas.</div>');
                }
            },
            error: function () {
                $tasksList.html('<div class="alezux-error-notice">Error de red al cargar tareas.</div>');
            }
        });
    }

    function renderTasks($listContainer, tasks, iconEditUrl, iconDeleteUrl) {
        $listContainer.empty();

        if (tasks.length === 0) {
            $listContainer.html('<div class="alezux-no-tasks" style="padding: 20px; text-align: center; color: #a0a0a0;">Aún no has creado ninguna tarea.</div>');
            return;
        }

        tasks.forEach(task => {
            let completedHtml = '';
            if (task.completed_by && task.completed_by.length > 0) {
                completedHtml = `
                    <div class="task-completed-users" style="margin-top: 10px; padding: 10px; background: rgba(46, 213, 115, 0.1); border-radius: 8px;">
                        <strong style="font-size: 12px; display: block; margin-bottom: 5px; color: #2ed573;">
                            <i class="fas fa-check-circle"></i> Completado por (${task.completed_by.length}):
                        </strong>
                        <ul style="list-style: none; padding: 0; margin: 0; font-size: 12px; color: #a0a0a0;">
                            ${task.completed_by.map(user => `<li>- ${user.display_name} (${user.user_email})</li>`).join('')}
                        </ul>
                    </div>
                `;
            } else {
                completedHtml = `<div class="task-completed-users" style="margin-top: 10px; font-size: 12px; color: #a0a0a0;">Nadie ha completado esta tarea aún.</div>`;
            }

            const html = `
                <div class="alezux-task-item" data-id="${task.id}">
                    <div class="task-info">
                        <strong class="task-title">${task.title}</strong>
                        ${task.description ? `<p class="task-desc">${task.description}</p>` : ''}
                        <small class="task-meta"><i class="far fa-calendar-alt"></i> ${task.formatted_date}</small>
                        ${completedHtml}
                    </div>
                    <div class="task-actions">
                        <span class="alezux-btn-icon btn-edit-task" role="button" title="Editar Tarea">
                            <span style="pointer-events: none;">${iconEditUrl}</span>
                        </span>
                        <span class="alezux-btn-icon btn-delete-task" role="button" title="Eliminar Tarea">
                            <span style="pointer-events: none;">${iconDeleteUrl}</span>
                        </span>
                    </div>
                </div>
            `;
            $listContainer.append(html);
        });
    }

    // Inicializar todos los widgets en la página
    function initAllWidgets() {
        $('.alezux-listing-admin').each(function () {
            loadTasksForWidget($(this));
        });
    }

    // Inicializa la primera vez que carga script
    initAllWidgets();

    // Compatibilidad con los re-renders del Editor de Elementor
    $(window).on('elementor/frontend/init', function () {
        if (elementorFrontend && elementorFrontend.hooks) {
            elementorFrontend.hooks.addAction('frontend/element_ready/alezux_listing_admin.default', function ($scope) {
                const $widget = $scope.find('.alezux-listing-admin');
                if ($widget.length > 0) {
                    loadTasksForWidget($widget);
                }
            });
        }
    });

    // === DELEGACIÓN DE EVENTOS ===
    // De esta manera no se "pierden" los botones cuando Elementor reinicializa el DOM

    // ADD TASK
    $(document).on('submit', '#alezux-add-task-form', function (e) {
        e.preventDefault();

        const $form = $(this);
        const $widget = $form.closest('.alezux-listing-admin');
        const $submitBtn = $form.find('#alezux-submit-task-btn');
        const $msgDiv = $form.find('#alezux-task-form-msg');

        const title = $form.find('#task_title').val();
        const description = $form.find('#task_description').val();

        if (!title) return;

        $submitBtn.prop('disabled', true);
        $submitBtn.find('.btn-text').hide();
        $submitBtn.find('.btn-loading').show();
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
                    $form[0].reset();
                    loadTasksForWidget($widget); // Recargar
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
                $submitBtn.find('.btn-loading').hide();
                setTimeout(() => $msgDiv.fadeOut(), 4000);
            }
        });
    });

    // DELETE TASK
    $(document.body).on('click', '.btn-delete-task', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const $btn = $(this);
        const $taskItem = $btn.closest('.alezux-task-item');
        const taskId = $taskItem.attr('data-id');
        const originalHtml = $btn.html();

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
                        $btn.html(originalHtml).prop('disabled', false);
                    }
                },
                error: function () {
                    showNotification('Error al intentar borrar la tarea.', 'error');
                    $btn.html(originalHtml).prop('disabled', false);
                }
            });
        });
    });

    // CLOSE ANY MODAL
    $(document.body).on('click', '.alezux-listing-modal-close', function () {
        const $overlay = $(this).closest('.alezux-listing-modal-overlay');
        $overlay.fadeOut(200, function () {
            if ($overlay.hasClass('moved-to-body-modal')) {
                $overlay.remove(); // Eliminamos clon fantasma del body
            }
        });
    });

    // OPEN EDIT MODAL
    $(document.body).on('click', '.btn-edit-task', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const $taskItem = $(this).closest('.alezux-task-item');
        const $widget = $(this).closest('.alezux-listing-admin');

        // El modal base original de Elementor lo dejamos donde está (oculto en el DOM del widget)
        const $originalModal = $widget.find('.alezux-edit-task-modal').not('.moved-to-body-modal');

        // Removemos de memoria cualquier clon activo "fantasma" que exista en body por intentos previos
        $('body > .alezux-edit-task-modal.moved-to-body-modal').remove();

        if ($originalModal.length === 0) {
            console.error("No se encontró el modal primario en el Widget DOM. Recargue editor.");
            return;
        }

        // Clonamos fresco para evitar duplicados de events
        const $editModal = $originalModal.clone().addClass('moved-to-body-modal');

        // Extraemos las clases y el ID de Elementor para que {{WRAPPER}} pueda afectar estilos
        const $elementorElement = $widget.closest('.elementor-element');
        const elementorClasses = $elementorElement.attr('class') || '';
        const elementorId = $elementorElement.attr('data-id') || '';

        // Obtenemos la estructura base global que Elementor inyecta en el frontend (ej. elementor-1234)
        const $elementorRoot = $widget.closest('.elementor');
        const rootClasses = $elementorRoot.attr('class') || '';

        // Lo envolvemos en un doble contenedor falso transparente para imitar el árbol DOM de Elementor
        if (elementorClasses && elementorId) {
            const $innerWrapper = $('<div>', {
                'class': elementorClasses,
                'data-id': elementorId,
                'style': 'position: static; display: contents;'
            }).append($editModal);

            if (rootClasses) {
                const $outerWrapper = $('<div>', {
                    'class': rootClasses,
                    'style': 'position: static; display: contents;'
                }).append($innerWrapper);
                $('body').append($outerWrapper);
            } else {
                $('body').append($innerWrapper);
            }
        } else {
            $('body').append($editModal);
        }

        // Popular datos en el clon
        $editModal.find('.edit_task_id').val($taskItem.attr('data-id'));
        $editModal.find('.edit_task_title').val($taskItem.find('.task-title').text());
        $editModal.find('.edit_task_description').val($taskItem.find('.task-desc').text());

        // Mostrar Modalidad forzando propiedades que puedan estar bloqueadas
        $editModal.removeClass('alezux-hidden').css({
            'display': 'flex',
            'opacity': '1',
            'visibility': 'visible',
            'z-index': '999999'
        }).hide().fadeIn(200);

        // Referencia estricta para saber qué widget disparó original
        $editModal.data('parent-widget', $widget);
    });

    // SUBMIT EDIT FORM
    $(document.body).on('submit', '.alezux-edit-task-form', function (e) {
        e.preventDefault();

        const $form = $(this);
        const $modal = $form.closest('.alezux-edit-task-modal');
        // Identificando botones Submit dentro de un form clonado
        const $btn = $form.find('button[type="submit"]');
        const $parentWidget = $modal.data('parent-widget');

        const id = $form.find('.edit_task_id').val();
        const title = $form.find('.edit_task_title').val();
        const description = $form.find('.edit_task_description').val();

        if (!title) return;

        $btn.prop('disabled', true);
        $btn.find('.btn-text').hide();
        $btn.find('.btn-loading').show();

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

                    $modal.fadeOut(200, function () {
                        if ($modal.hasClass('moved-to-body-modal')) {
                            $modal.remove(); // Eliminamos el clon despues de éxito
                        }
                    });

                    // Recargamos el listado correspondiente
                    if ($parentWidget && $parentWidget.length > 0) {
                        loadTasksForWidget($parentWidget);
                    } else {
                        initAllWidgets();
                    }
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
