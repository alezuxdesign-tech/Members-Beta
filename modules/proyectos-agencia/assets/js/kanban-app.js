jQuery(document).ready(function ($) {
    const kanbanContainer = $('#alezux-kanban-app');
    if (!kanbanContainer.length) return;

    // init Sortable
    $('.kanban-column-body').sortable({
        connectWith: '.kanban-column-body',
        placeholder: "kanban-card-placeholder",
        start: function (event, ui) {
            ui.item.addClass('active');
        },
        stop: function (event, ui) {
            ui.item.removeClass('active');
        },
        receive: function (event, ui) {
            const projectId = ui.item.data('id');
            const newStatus = $(this).closest('.kanban-column').data('status');

            updateProjectStatus(projectId, newStatus);
        }
    }).disableSelection();

    // Load Projects
    loadProjects();

    function loadProjects() {
        $.ajax({
            url: alezux_agency_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'alezux_agency_get_projects',
                nonce: alezux_agency_vars.nonce
            },
            success: function (response) {
                if (response.success) {
                    renderProjects(response.data);
                } else {
                    console.error('Error loading projects:', response.data);
                }
            }
        });
    }

    function renderProjects(projects) {
        // Clear columns
        $('.kanban-column-body').empty();

        projects.forEach(project => {
            const card = createCard(project);
            $(`#col-${project.status}`).append(card);
        });
    }

    function createCard(project) {
        let stepLabel = '';
        switch (project.step) {
            case 'briefing': stepLabel = 'Briefing'; break;
            case 'identity': stepLabel = 'Identidad'; break;
            case 'web_design': stepLabel = 'Diseño Web'; break;
            case 'development': stepLabel = 'Desarrollo'; break;
            case 'delivery': stepLabel = 'Entrega'; break;
            default: stepLabel = project.step;
        }

        const avatarHtml = project.client_avatar ? `<img src="${project.client_avatar}" style="width:24px;height:24px;border-radius:50%;margin-right:5px;">` : '';

        return $(`
            <div class="kanban-card" data-id="${project.id}">
                <div class="kanban-card-title">${project.title}</div>
                <div class="kanban-card-meta">
                    <div style="display:flex;align-items:center;">
                        ${avatarHtml} <span>${project.client_name}</span>
                    </div>
                </div>
                <div style="margin-top:8px;">
                    <span class="kanban-card-step status-${project.step}">${stepLabel}</span>
                </div>
            </div>
        `);
    }

    // Modals
    const detailsModal = $('#project-modal');
    const newProjectModal = $('#new-project-modal');

    // Step Definitions
    const PROJECT_STEPS = [
        { id: 'briefing', label: '1. Briefing' },
        { id: 'identity', label: '2. Identidad' },
        { id: 'web_design', label: '3. Diseño Web' },
        { id: 'development', label: '4. Desarrollo' },
        { id: 'delivery', label: '5. Entrega' }
    ];

    $('.close-details').on('click', function () { detailsModal.hide(); });
    $('.close-new').on('click', function () { newProjectModal.hide(); });

    // Open New Project Modal
    $('#add-project-btn').on('click', function () {
        newProjectModal.show();
        if ($.fn.datepicker) {
            $('.datepicker').datepicker({
                dateFormat: 'yy-mm-dd',
                beforeShowDay: $.datepicker.noWeekends
            });
        }

        // Load Users for Select
        const clientSelect = $('#new-project-client');
        if (clientSelect.children('option').length <= 1) {
            $.post(alezux_agency_vars.ajax_url, {
                action: 'alezux_agency_search_users',
                term: '',
                nonce: alezux_agency_vars.nonce
            }, function (response) {
                if (response.success) {
                    response.data.forEach(user => {
                        clientSelect.append(new Option(user.text, user.id));
                    });
                }
            });
        }
    });

    // Handle New Project Submit
    $('#new-project-form').on('submit', function (e) {
        e.preventDefault();

        const title = $('#new-project-name').val();
        const clientId = $('#new-project-client').val();
        const start = $('#new-project-start').val();
        const end = $('#new-project-end').val();

        if (!title || !clientId || !start || !end) {
            alert('Por favor completa todos los campos.');
            return;
        }

        $.post(alezux_agency_vars.ajax_url, {
            action: 'alezux_agency_create_project',
            title: title,
            client_id: clientId,
            start_date: start,
            end_date: end,
            nonce: alezux_agency_vars.nonce
        }, function (response) {
            if (response.success) {
                alert('Proyecto creado!');
                newProjectModal.hide();
                loadProjects();
                $('#new-project-form')[0].reset();
            } else {
                alert('Error: ' + response.data);
            }
        });
    });

    // Update Project Status Helper
    function updateProjectStatus(projectId, newStatus) {
        $.post(alezux_agency_vars.ajax_url, {
            action: 'alezux_agency_update_project_status',
            project_id: projectId,
            status: newStatus,
            nonce: alezux_agency_vars.nonce
        }, function (response) {
            if (!response.success) {
                alert('Error al actualizar estado');
                loadProjects();
            }
        });
    }

    let currentModalProject = null;
    let activeTabStep = null;

    // Open Details Modal on Card Click
    $(document).on('click', '.kanban-card', function () {
        const projectId = $(this).data('id');
        const modalBody = $('#modal-body-content');

        // Reset
        $('#project-edit-form').data('id', projectId);
        $('#save-project-btn').data('id', projectId);
        $('#modal-project-title').text('Cargando...');
        modalBody.html('<div class="kanban-loading">Obteniendo datos...</div>');
        detailsModal.show();

        $.post(alezux_agency_vars.ajax_url, {
            action: 'alezux_agency_get_project_details',
            project_id: projectId,
            nonce: alezux_agency_vars.nonce
        }, function (response) {
            if (response.success) {
                currentModalProject = response.data;
                activeTabStep = currentModalProject.current_step || 'briefing'; // Default to active step
                renderModalContent();
            } else {
                modalBody.html('<div class="alezux-alert-error">' + response.data + '</div>');
            }
        });
    });

    // Render Modal Content (Stepper UI)
    function renderModalContent() {
        const project = currentModalProject;
        $('#modal-project-title').text('Proyecto: ' + project.client_name);

        // Helper for safe access
        const getVal = (path, def = '') => {
            return path.split('.').reduce((acc, part) => acc && acc[part], project.data) || def;
        };

        // Stepper Nav
        let stepperHtml = '<div class="alezux-stepper">';
        PROJECT_STEPS.forEach((step, index) => {
            const isActive = step.id === activeTabStep ? 'active' : '';
            // Allow clicking any step to view
            stepperHtml += `<div class="step-item ${isActive}" data-step="${step.id}">${step.label}</div>`;
        });
        stepperHtml += '</div>';

        // Sections (Only render active one visually, but input fields need to persist? 
        // Better to re-render inputs based on activeTabStep to avoid clutter)

        let contentHtml = '';

        // --- BRIEFING ---
        if (activeTabStep === 'briefing') {
            contentHtml = `
                <div class="step-content animate-fade-in">
                    <h4>Briefing & Datos</h4>
                    <label>Preferencias Web:</label>
                    <textarea class="alezux-input" readonly>${getVal('briefing.web_preferences')}</textarea>
                    <div style="display:flex; gap:10px; margin-top:10px;">
                        <div style="flex:1;">
                            <label>Tiene Logo:</label>
                            <input type="text" class="alezux-input" readonly value="${getVal('briefing.has_logo', 'No definido')}">
                        </div>
                        <div style="flex:1;">
                            <label>Logo URL:</label>
                            <input type="text" class="alezux-input" readonly value="${getVal('briefing.logo_url', '-')}">
                        </div>
                    </div>
                </div>`;
        }

        // --- IDENTITY ---
        else if (activeTabStep === 'identity') {
            contentHtml = `
                <div class="step-content animate-fade-in">
                    <h4>Identidad (Propuestas)</h4>
                    <label>URLs de Propuestas (separadas por coma):</label>
                    <input type="text" class="alezux-input" name="identity[proposal_files]" value="${getVal('identity.proposal_files', []).join(', ')}">
                    <p class="guidance-text">Sube los archivos a la biblioteca y pega aquí los enlaces.</p>
                    <div class="feedback-box">
                        <strong>Feedback Cliente:</strong> 
                        <p>${getVal('identity.client_feedback', 'Sin feedback aún.')}</p>
                    </div>
                </div>`;
        }

        // --- WEB DESIGN ---
        else if (activeTabStep === 'web_design') {
            contentHtml = `
                <div class="step-content animate-fade-in">
                    <h4>Diseño Web (Figma)</h4>
                    <label>URL Prototipo Figma:</label>
                    <input type="text" class="alezux-input" name="web_design[figma_url]" value="${getVal('web_design.figma_url')}">
                    <div class="feedback-box">
                        <strong>Estado:</strong> ${getVal('web_design.status', 'En Proceso')}
                    </div>
                </div>`;
        }

        // --- DEVELOPMENT ---
        else if (activeTabStep === 'development') {
            contentHtml = `
                <div class="step-content animate-fade-in">
                    <h4>Desarrollo (Staging)</h4>
                    <label>URL Sitio Staging:</label>
                    <input type="text" class="alezux-input" name="development[staging_url]" value="${getVal('development.staging_url')}">
                    <p class="guidance-text">El cliente revisará este enlace para dar aprobación final.</p>
                </div>`;
        }

        // --- DELIVERY ---
        else if (activeTabStep === 'delivery') {
            contentHtml = `
                <div class="step-content animate-fade-in">
                    <h4>Entrega Final</h4>
                    <label>Credenciales (JSON):</label>
                    <textarea class="alezux-input" name="delivery[credentials]" rows="4">${JSON.stringify(getVal('delivery.credentials', {}), null, 2)}</textarea>
                    
                    <label style="margin-top:10px;">Archivos Finales (URLs una por línea):</label>
                    <textarea class="alezux-input" name="delivery[final_assets]" rows="4">${getVal('delivery.final_assets', []).join('\n')}</textarea>
                </div>`;
        }

        // Navigation Buttons
        const confirmBtn = `<button class="alezux-btn alezux-btn-primary" id="btn-advance-step">Completar y Avanzar</button>`;
        const saveBtnOnly = `<button class="alezux-btn alezux-btn-success" id="btn-save-step">Guardar Datos</button>`;

        const footerControls = `
            <div class="step-footer-controls">
                ${saveBtnOnly}
                ${activeTabStep !== 'delivery' ? confirmBtn : ''}
            </div>
        `;

        $('#modal-body-content').html(stepperHtml + '<form id="project-edit-form">' + contentHtml + '</form>' + footerControls);

        // Bind Stepper Clicks
        $('.step-item').on('click', function () {
            activeTabStep = $(this).data('step');
            renderModalContent(); // Re-render
        });

        // Bind Action Buttons
        $('#btn-save-step').on('click', function () { saveCurrentStepData(false); });
        $('#btn-advance-step').on('click', function () { saveCurrentStepData(true); });

        // Update main modal footer to be hidden or standard
        $('#save-project-btn').hide(); // Hide the old global save button
    }

    function saveCurrentStepData(advance = false) {
        const projectId = currentModalProject.id;

        // 1. Collect Data based on Active Step
        const proposalFiles = $('input[name="identity[proposal_files]"]').val();
        const figmaUrl = $('input[name="web_design[figma_url]"]').val();
        const stagingUrl = $('input[name="development[staging_url]"]').val();
        const finalAssets = $('textarea[name="delivery[final_assets]"]').val();
        const credentials = $('textarea[name="delivery[credentials]"]').val();

        // Construct update object (partial)
        let formData = {};

        if (activeTabStep === 'identity') {
            formData.identity = { proposal_files: proposalFiles ? proposalFiles.split(',').map(s => s.trim()).filter(s => s) : [] };
        } else if (activeTabStep === 'web_design') {
            formData.web_design = { figma_url: figmaUrl || '' };
        } else if (activeTabStep === 'development') {
            formData.development = { staging_url: stagingUrl || '' };
        } else if (activeTabStep === 'delivery') {
            formData.delivery = {
                final_assets: finalAssets ? finalAssets.split('\n') : [],
                credentials: credentials ? JSON.parse(credentials || '{}') : {}
            };
        }

        // 2. Save Data via AJAX
        $.post(alezux_agency_vars.ajax_url, {
            action: 'alezux_agency_update_project_data',
            project_id: projectId,
            project_data: JSON.stringify(formData),
            nonce: alezux_agency_vars.nonce
        }, function (response) {
            if (response.success) {
                // Update local model
                // We need to merge formData into currentModalProject.data
                $.extend(true, currentModalProject.data, formData);

                if (advance) {
                    advanceToNextStep(projectId);
                } else {
                    alert('Datos guardados.');
                }
            } else {
                alert('Error al guardar datos: ' + response.data);
            }
        });
    }

    function advanceToNextStep(projectId) {
        // Find next step
        const currentIdx = PROJECT_STEPS.findIndex(s => s.id === activeTabStep);
        if (currentIdx < PROJECT_STEPS.length - 1) {
            const nextStepId = PROJECT_STEPS[currentIdx + 1].id;

            $.post(alezux_agency_vars.ajax_url, {
                action: 'alezux_agency_update_project_step',
                project_id: projectId,
                step: nextStepId,
                nonce: alezux_agency_vars.nonce
            }, function (response) {
                if (response.success) {
                    currentModalProject.current_step = nextStepId;
                    activeTabStep = nextStepId;
                    renderModalContent(); // Refresh UI to next step
                    updateProjectStatus(projectId, 'process'); // Optionally update Kanban column too?
                } else {
                    alert('Error al avanzar: ' + response.data);
                }
            });
        }
    }

    // Close on outside click is handled above

});
