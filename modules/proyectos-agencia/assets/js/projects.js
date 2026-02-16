/**
 * Scripts para el módulo de Proyectos de Agencia
 */
jQuery(document).ready(function ($) {

    // Abrir Modal (Delegación de eventos)
    $(document).on('click', '#open-new-project-modal', function (e) {
        e.preventDefault();
        $('#new-project-modal').css('display', 'flex');
    });

    // Cerrar Modal
    $(document).on('click', '.close-modal, .close-modal-btn, .alezux-close-modal', function () {
        $(this).closest('.alezux-modal').hide();
    });

    // Cerrar al hacer clic fuera
    $(window).on('click', function (e) {
        if ($(e.target).hasClass('alezux-modal')) {
            $('.alezux-modal').hide();
        }
    });

    // Crear Proyecto AJAX
    $('#create-project-form').on('submit', function (e) {
        e.preventDefault();

        var $form = $(this);
        var $btn = $form.find('button[type="submit"]');
        var originalText = $btn.text();

        $btn.prop('disabled', true).text('Creando...');

        var data = {
            action: 'alezux_create_project',
            nonce: AlezuxProjects.nonce,
            project_name: $form.find('input[name="project_name"]').val(),
            customer_id: $form.find('select[name="customer_id"]').val()
        };

        $.post(AlezuxProjects.ajaxurl, data, function (response) {
            if (response.success) {
                // Redirigir a la misma página para ver el nuevo proyecto
                // O idealmente a la página de detalle si se configuró
                window.location.reload();
            } else {
                alert('Error: ' + (response.data || 'Error desconocido'));
                $btn.prop('disabled', false).text(originalText);
            }
        }).fail(function () {
            alert('Error de conexión con el servidor.');
            $btn.prop('disabled', false).text(originalText);
        });
    });

    // Actualizar Proyecto AJAX (Delegado porque el form se carga dinámicamente)
    $(document).on('submit', '#update-project-status-form', function (e) {
        e.preventDefault();

        var $form = $(this);
        var $btn = $form.find('button[type="submit"]');
        var originalText = $btn.text();

        $btn.prop('disabled', true).text('Guardando...');

        var data = {
            action: 'alezux_update_project',
            nonce: AlezuxProjects.nonce,
            project_id: $form.find('input[name="project_id"]').val(),
            status: $form.find('select[name="status"]').val(),
            current_step: $form.find('select[name="current_step"]').val(),
            design_url: $form.find('input[name="design_url"]').val(),
            site_url: $form.find('input[name="site_url"]').val(),
            access_user: $form.find('input[name="access_user"]').val(),
            access_pass: $form.find('input[name="access_pass"]').val()
        };

        $.post(AlezuxProjects.ajaxurl, data, function (response) {
            if (response.success) {
                alert('Proyecto actualizado correctamente.');
                window.location.reload();
            } else {
                alert('Error: ' + (response.data || 'Error desconocido'));
            }
        }).fail(function () {
            alert('Error de conexión.');
        }).always(function () {
            $btn.prop('disabled', false).text(originalText);
        });
    });

    // Cliente: Enviar Briefing
    $('#client-briefing-form').on('submit', function (e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $form.find('button[type="submit"]');
        var originalText = $btn.html();

        $btn.prop('disabled', true).html('<i class="eicon-loading eicon-animation-spin"></i> Enviando...');

        var data = $form.serialize() + '&action=alezux_submit_briefing&nonce=' + AlezuxProjects.nonce;

        $.post(AlezuxProjects.ajaxurl, data, function (response) {
            if (response.success) {
                alert('¡Gracias! Hemos recibido tu información.');
                window.location.reload();
            } else {
                alert('Error: ' + response.data);
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Cliente: Aprobar Diseño
    $('#btn-approve-design').on('click', function (e) {
        e.preventDefault();
        if (!confirm('¿Estás seguro de aprobar el diseño? Esto dará inicio a la fase de desarrollo.')) return;

        var $btn = $(this);
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="eicon-loading eicon-animation-spin"></i> Procesando...');

        var data = {
            action: 'alezux_approve_design',
            nonce: AlezuxProjects.nonce,
            project_id: $btn.data('id')
        };

        $.post(AlezuxProjects.ajaxurl, data, function (response) {
            if (response.success) {
                alert('¡Genial! Diseño aprobado.');
                window.location.reload();
            } else {
                alert('Error: ' + response.data);
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Cliente: Rechazar / Solicitar Cambios (Toggle Modal)
    $('#btn-reject-modal-trigger').on('click', function (e) {
        e.preventDefault();
        $('#reject-modal').slideToggle();
    });

    $('#btn-submit-rejection').on('click', function (e) {
        e.preventDefault();
        var feedback = $('#reject-feedback').val();
        if (feedback.length < 10) {
            alert('Por favor describe los cambios con más detalle.');
            return;
        }

        var $btn = $(this);
        var originalText = $btn.text();
        $btn.prop('disabled', true).text('Enviando...');

        $.post(AlezuxProjects.ajaxurl, {
            action: 'alezux_submit_rejection',
            nonce: AlezuxProjects.nonce,
            project_id: $btn.data('id'),
            feedback: feedback
        }, function (response) {
            if (response.success) {
                alert(response.data);
                $('#reject-modal').slideUp();
                $('#reject-feedback').val('');
            } else {
                alert('Error: ' + response.data);
            }
        }).fail(function () {
            alert('Error de conexión.');
        }).always(function () {
            $btn.prop('disabled', false).text(originalText);
        });
    });

    // --- PANEL LATERAL (OFF-CANVAS) ---

    // Función global para abrir el panel
    AlezuxProjects.openPanel = function (projectId) {
        var $panel = $('#project-offcanvas');
        var $overlay = $('#project-offcanvas-overlay');
        var $content = $('#offcanvas-content');
        var $loading = $('#offcanvas-loading');
        var $title = $('#offcanvas-title');

        // Reset UI
        $title.text('Cargando...');
        $content.hide().html('');
        $loading.show();

        // Show Panel
        $overlay.fadeIn(200);
        $panel.addClass('open');

        // AJAX Load
        $.post(AlezuxProjects.ajaxurl, {
            action: 'alezux_get_project_details',
            nonce: AlezuxProjects.nonce,
            project_id: projectId
        }, function (response) {
            if (response.success) {
                var data = response.data;
                $title.text(data.project.name);
                $content.html(data.html).fadeIn();
                $loading.hide();

                // Init Chat
                AlezuxProjects.currProjectId = projectId;
                AlezuxProjects.loadChatMessages(projectId);
            } else {
                $title.text('Error');
                $content.html('<div class="alezux-alert error">' + response.data + '</div>').fadeIn();
                $loading.hide();
            }
        }).fail(function () {
            $title.text('Error de Conexión');
            $loading.hide();
        });
    };

    // Cerrar Panel
    function closeOffcanvas() {
        $('#project-offcanvas').removeClass('open');
        $('#project-offcanvas-overlay').fadeOut(200);
        AlezuxProjects.currProjectId = null;
    }

    $(document).on('click', '.close-offcanvas-btn', closeOffcanvas);
    $(document).on('click', '#project-offcanvas-overlay', closeOffcanvas);

    // ESC key to close
    $(document).keyup(function (e) {
        if (e.key === "Escape") closeOffcanvas();
    });

    // --- TABS LOGIC ---
    $(document).on('click', '.tab-btn', function () {
        var tabId = $(this).data('tab');
        var $container = $(this).closest('.panel-details-container');

        // Buttons
        $container.find('.tab-btn').removeClass('active');
        $(this).addClass('active');

        // Content
        $container.find('.tab-content').removeClass('active');
        $container.find('#' + tabId).addClass('active');
    });

    // --- CHAT LOGIC (SMART POLLING) ---
    AlezuxProjects.chatTimer = null;
    AlezuxProjects.isPageVisible = true;

    // Visibility Listener
    document.addEventListener("visibilitychange", function () {
        AlezuxProjects.isPageVisible = !document.hidden;
        if (AlezuxProjects.currProjectId) {
            // Restart polling with new frequency
            AlezuxProjects.startChatPolling(AlezuxProjects.currProjectId);
        }
    });

    AlezuxProjects.startChatPolling = function (projectId) {
        // Clear existing
        if (AlezuxProjects.chatTimer) clearInterval(AlezuxProjects.chatTimer);

        // Initial Load
        AlezuxProjects.loadChatMessages(projectId);

        // Set Interval based on visibility
        var interval = AlezuxProjects.isPageVisible ? 5000 : 60000; // 5s active, 60s background

        AlezuxProjects.chatTimer = setInterval(function () {
            if (AlezuxProjects.currProjectId === projectId) {
                AlezuxProjects.loadChatMessages(projectId);
            } else {
                clearInterval(AlezuxProjects.chatTimer);
            }
        }, interval);
    };

    AlezuxProjects.stopChatPolling = function () {
        if (AlezuxProjects.chatTimer) clearInterval(AlezuxProjects.chatTimer);
        AlezuxProjects.chatTimer = null;
    };

    // Expose globally for Widget use
    AlezuxProjects.loadChatMessages = function (projectId) {
        var $container = $('#chat-messages-list');
        // Check if user is scrolling up to read history? If so, maybe don't scroll bottom?
        // For now simple implementation.

        $.post(AlezuxProjects.ajaxurl, {
            action: 'alezux_get_project_messages',
            nonce: AlezuxProjects.nonce,
            project_id: projectId
        }, function (response) {
            if (response.success) {
                var messages = response.data;
                $container.html(''); // Simple redraw for now. Ideal: append only new.

                if (messages.length === 0) {
                    $container.html('<div class="chat-loading">No hay mensajes aún.</div>');
                } else {
                    messages.forEach(function (msg) {
                        appendMessageToChat(msg);
                    });
                    scrollToBottom();
                }
            }
        });
    }

    function appendMessageToChat(msg) {
        var $container = $('#chat-messages-list');
        var isMeClass = msg.is_me ? 'is-me' : '';

        var html = `
            <div class="chat-message ${isMeClass}">
                <div class="chat-avatar">
                    <img src="${msg.sender_avatar}" alt="${msg.sender_name}">
                </div>
                <div class="chat-content-wrapper">
                    <div class="chat-bubble">
                        ${msg.content}
                    </div>
                    <div class="chat-meta">
                        ${msg.sender_name} • ${msg.time}
                    </div>
                </div>
            </div>
        `;
        $container.append(html);
    }

    function scrollToBottom() {
        var $wrapper = $('#chat-messages-list');
        $wrapper.scrollTop($wrapper[0].scrollHeight);
    }

    // Send Message
    $(document).on('click', '#btn-send-chat', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var $input = $('#chat-message-input');
        var content = $input.val().trim();
        var projectId = AlezuxProjects.currProjectId;

        if (!content || !projectId) return;

        $btn.prop('disabled', true);
        $input.prop('disabled', true);

        $.post(AlezuxProjects.ajaxurl, {
            action: 'alezux_send_project_message',
            nonce: AlezuxProjects.nonce,
            project_id: projectId,
            content: content
        }, function (response) {
            if (response.success) {
                $input.val('');
                // Force immediate reload and reset timer
                AlezuxProjects.loadChatMessages(projectId);
                AlezuxProjects.startChatPolling(projectId);
            } else {
                alert('Error: ' + response.data);
            }
        }).always(function () {
            $btn.prop('disabled', false);
            $input.prop('disabled', false).focus();
        });
    });

    // Enter to send
    $(document).on('keypress', '#chat-message-input', function (e) {
        if (e.which == 13 && !e.shiftKey) {
            e.preventDefault();
            $('#btn-send-chat').click();
        }
    });

    // --- PHASE LOGIC (Dynamic Fields) ---
    AlezuxProjects.initPhaseLogic = function () {
        var $select = $('#project-phase-select');
        if ($select.length === 0) return;

        function updateFields() {
            var phase = $select.val();
            // Hide all first
            $('.dynamic-section').hide();

            // Logic
            if (phase === 'briefing') {
                $('#section-briefing').fadeIn();
            } else if (phase === 'design_review') {
                $('#section-design').fadeIn();
            } else if (phase === 'in_progress' || phase === 'completed') {
                $('#section-development').fadeIn();
                // Optionally show design too?
                $('#section-design').show();
            }
        }

        $select.on('change', updateFields);
        updateFields(); // Run on init
    };

});
