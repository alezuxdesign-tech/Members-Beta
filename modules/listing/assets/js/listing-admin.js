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
            <div id="${id}" class="alezux-modal-overlay confirm-modal-alezux" style="display:flex; z-index: 999999;">
                <div class="alezux-modal-content" style="max-width: 400px; text-align: center;">
                    <div style="font-size: 40px; color: #ff4757; margin-bottom: 15px;"><i class="fas fa-exclamation-triangle"></i></div>
                    <h3 style="margin-bottom: 10px;">¿Estás seguro?</h3>
                    <p style="color: #a0a0a0; margin-bottom: 25px; line-height: 1.5;">${message}</p>
                    <div style="display: flex; gap: 10px; justify-content: center;">
                        <button class="alezux-btn btn-cancel-confirm" type="button" style="background: transparent; border: 1px solid #333; color: #fff;">Cancelar</button>
                        <button class="alezux-btn btn-accept-confirm" type="button" style="background: #ff4757; color: #fff;">Sí, Eliminar</button>
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
            const html = `
                <div class="alezux-task-item" data-id="${task.id}">
                    <div class="task-info">
                        <strong class="task-title">${task.title}</strong>
                        ${task.description ? `<p class="task-desc">${task.description}</p>` : ''}
                        <small class="task-meta">Creada: ${task.formatted_date}</small>
                    </div>
                    <div class="task-actions">
                        <button class="alezux-btn-icon btn-edit-task" title="Editar Tarea" style="background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.1); width: 40px; height: 40px; border-radius: 8px; cursor: pointer; transition: all 0.3s; margin-right: 5px;">${iconEditUrl}</button>
                        <button class="alezux-btn-icon btn-delete-task" title="Eliminar Tarea" style="background: rgba(255,71,87,0.1); color: #ff4757; border: 1px solid rgba(255,71,87,0.2); width: 40px; height: 40px; border-radius: 8px; cursor: pointer; transition: all 0.3s;">${iconDeleteUrl}</button>
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
                $submitBtn.find('.btn-icon').hide();
                setTimeout(() => $msgDiv.fadeOut(), 4000);
            }
        });
    });

    // DELETE TASK
    $(document).on('click', '.btn-delete-task', function (e) {
        e.preventDefault();

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
    $(document).on('click', '.alezux-modal-close', function () {
        const $overlay = $(this).closest('.alezux-modal-overlay');
        $overlay.fadeOut(200, function () {
            // Si el modal fue clonado/movido al body lo eliminamos del DOM en lugar de solo esconderlo
            if ($overlay.hasClass('moved-to-body-modal')) {
                $overlay.remove();
            }
        });
    });

    // OPEN EDIT MODAL
    $(document).on('click', '.btn-edit-task', function (e) {
        e.preventDefault();

        const $taskItem = $(this).closest('.alezux-task-item');
        const $widget = $(this).closest('.alezux-listing-admin');
        const $originalModal = $widget.find('.alezux-edit-task-modal').not('.moved-to-body-modal');

        // Removemos cualquier modal flotante viejo que haya quedado en el body
        $('body > .alezux-edit-task-modal.moved-to-body-modal').remove();

        // Para evitar problemas de z-index del iframe de Elementor, clonamos el modal a la raíz de body
        let $editModal;
        if ($originalModal.length > 0) {
            $editModal = $originalModal.clone().addClass('moved-to-body-modal');
            $('body').append($editModal);
        } else {
            // Fallback por si acaso ya se había movido y no está en el widget
            $editModal = $('.alezux-edit-task-modal');
        }

        // Popular datos
        $editModal.find('.edit_task_id').val($taskItem.attr('data-id'));
        $editModal.find('.edit_task_title').val($taskItem.find('.task-title').text());
        $editModal.find('.edit_task_description').val($taskItem.find('.task-desc').text());

        // Mostrar modalidad (con display flex para el contenedor general por los estilos)
        $editModal.fadeIn().css('display', 'flex').css('z-index', '999999');

        // Referencia estricta para saber qué widget disparó y actualizar solo ése
        $editModal.data('parent-widget', $widget);
    });

    // SUBMIT EDIT FORM
    $(document).on('submit', '.alezux-edit-task-form', function (e) {
        e.preventDefault();

        const $form = $(this);
        const $modal = $form.closest('.alezux-edit-task-modal');
        const $btn = $form.find('.alezux-submit-edit-task-btn');
        const $parentWidget = $modal.data('parent-widget');

        const id = $form.find('.edit_task_id').val();
        const title = $form.find('.edit_task_title').val();
        const description = $form.find('.edit_task_description').val();

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

                    $modal.fadeOut(200, function () {
                        if ($modal.hasClass('moved-to-body-modal')) $modal.remove();
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
