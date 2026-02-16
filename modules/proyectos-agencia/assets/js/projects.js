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

    // Actualizar Proyecto AJAX
    $('#update-project-status-form').on('submit', function (e) {
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
            design_url: $form.find('input[name="design_url"]').val()
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

        // Aquí iría la lógica AJAX para guardar el feedback (MVP: Solo alert)
        alert('Gracias. Hemos registrado tus comentarios y el equipo se pondrá en contacto.');
        $('#reject-modal').slideUp();
        $('#reject-feedback').val('');
    });

});
