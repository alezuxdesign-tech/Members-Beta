jQuery(document).ready(function ($) {
    const appContainer = $('#alezux-client-app');
    if (!appContainer.length) return;

    const projectId = appContainer.data('id');

    // Handle Briefing Form
    $('#briefing-form').on('submit', function (e) {
        e.preventDefault();

        const data = $(this).serialize() + '&action=alezux_agency_client_save_briefing&project_id=' + projectId + '&nonce=' + alezux_agency_vars.nonce;

        $.post(alezux_agency_vars.ajax_url, data, function (response) {
            if (response.success) {
                alert('Información guardada. ¡Gracias!');
                location.reload();
            } else {
                alert('Error: ' + response.data);
            }
        });
    });

    // Handle Full Control Button
    $('#btn-full-control').on('click', function () {
        if (confirm('¿Estás seguro? Esto indicará que confías el diseño completamente a nosotros.')) {
            $.post(alezux_agency_vars.ajax_url, {
                action: 'alezux_agency_client_save_briefing',
                project_id: projectId,
                web_preferences: 'Todo en sus manos. Confío en su criterio.',
                has_logo: 'no', // Assumption
                legal_data: 'Pendiente',
                nonce: alezux_agency_vars.nonce
            }, function (response) {
                if (response.success) {
                    alert('¡Excelente! Nos encargaremos de todo.');
                    location.reload();
                }
            });
        }
    });

    // Handle Approvals and Feedback
    $('.alezux-btn[data-action]').on('click', function (e) {
        e.preventDefault();
        const action = $(this).data('action'); // approve or changes
        const step = $(this).data('step');

        let feedback = '';
        if (action === 'changes') {
            const feedbackInput = $(this).closest('div').find('textarea');
            // Better selector strategy needed if multiple textareas, but structure is simple now
            // Actually the textarea is in a hidden div shown by another button
            if (step === 'identity') feedback = $('#identity-feedback-text').val();
            if (step === 'web_design') feedback = $('#web-feedback-text').val();

            if (!feedback) {
                alert('Por favor describe los cambios solicitados.');
                return;
            }
        } else {
            if (!confirm('¿Confirmas que apruebas esta fase?')) return;
            feedback = 'Aprobado por el cliente';
        }

        $.post(alezux_agency_vars.ajax_url, {
            action: 'alezux_agency_client_send_feedback',
            project_id: projectId,
            step: step,
            action_type: action,
            feedback: feedback,
            nonce: alezux_agency_vars.nonce
        }, function (response) {
            if (response.success) {
                alert(response.data);
                location.reload();
            } else {
                alert('Error: ' + response.data);
            }
        });

    });

});
