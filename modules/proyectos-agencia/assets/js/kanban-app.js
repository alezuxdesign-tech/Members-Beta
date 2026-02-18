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

    function updateProjectStatus(id, status) {
        $.ajax({
            url: alezux_agency_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'alezux_agency_update_project_status',
                project_id: id,
                status: status,
                nonce: alezux_agency_vars.nonce // Assuming we handle nonce verification
            },
            success: function (response) {
                if (!response.success) {
                    alert('Error actualizando estado: ' + response.data);
                    // Revert move? (Complex to undo in simple sortable without reload)
                }
            }
        });
    }

    // Modal Logic
    const modal = $('#project-modal');
    const closeBtn = $('.close-modal');

    // Open Modal on Card Click
    $(document).on('click', '.kanban-card', function () {
        const projectId = $(this).data('id');
        const modalBody = $('#modal-body-content');

        $('#modal-project-title').text('Cargando...');
        modalBody.html('<div class="kanban-loading">Obteniendo datos...</div>');
        modal.show();

        // Fetch details
        $.ajax({
            url: alezux_agency_vars.ajax_url,
            type: 'POST', // Changed to POST for consistency, though GET is in PHP
            data: {
                action: 'alezux_agency_get_project_details',
                project_id: projectId, // PHP expects GET usually but let's try REQUEST or fix PHP
                nonce: alezux_agency_vars.nonce
            },
            method: 'GET', // PHP defines $_GET for this one
            success: function (response) {
                if (response.success) {
                    renderModalContent(response.data);
                } else {
                    modalBody.html('<div class="alezux-alert-error">' + response.data + '</div>');
                }
            }
        });
    });

    function renderModalContent(project) {
        $('#modal-project-title').text('Proyecto: ' + project.client_name);
        const data = project.data || {};

        // Helper to get value
        const getVal = (path, defaultVal = '') => {
            return path.split('.').reduce((o, i) => o ? o[i] : null, data) || defaultVal;
        };

        const html = `
            <form id="project-edit-form" data-id="${project.id}">
                <div class="alezux-form-group">
                    <h4>Información General</h4>
                    <p><strong>Cliente:</strong> ${project.client_name} (${project.client_email})</p>
                    <p><strong>Estado:</strong> ${project.status}</p>
                </div>

                <hr>

                <div class="alezux-tabs">
                    <!-- Simple tabs logic could go here, for now linear -->
                    
                    <!-- STEP 1: Briefing (Read Only for Admin mostly) -->
                    <div class="step-section">
                        <h4>1. Briefing</h4>
                        <label>Preferencias Web (Cliente):</label>
                        <textarea class="alezux-input" readonly>${getVal('briefing.web_preferences')}</textarea>
                        
                        <label>Tiene Logo:</label>
                        <input type="text" class="alezux-input" readonly value="${getVal('briefing.has_logo', 'No definido')}">
                    </div>

                    <!-- STEP 2: Identity -->
                    <div class="step-section">
                        <h4>2. Identidad (Logo)</h4>
                        <label>Archivos Propuesta (URLs sep. por coma):</label>
                        <input type="text" class="alezux-input" name="identity[proposal_files]" value="${getVal('identity.proposal_files', []).join(', ')}">
                        <small>Feedback Cliente: ${getVal('identity.client_feedback', 'Pendiente')}</small>
                    </div>

                    <!-- STEP 3: Web Design -->
                    <div class="step-section">
                        <h4>3. Diseño Web (Figma)</h4>
                        <label>URL Prototipo Figma:</label>
                        <input type="text" class="alezux-input" name="web_design[figma_url]" value="${getVal('web_design.figma_url')}">
                        <small>Estado: ${getVal('web_design.status', 'Pendiente')}</small>
                    </div>
                    
                    <!-- STEP 4: Development -->
                     <div class="step-section">
                        <h4>4. Desarrollo</h4>
                        <label>URL Staging:</label>
                        <input type="text" class="alezux-input" name="development[staging_url]" value="${getVal('development.staging_url')}">
                    </div>

                    <!-- STEP 5: Delivery -->
                    <div class="step-section">
                        <h4>5. Entrega Final</h4>
                        <label>Credenciales (JSON):</label>
                        <textarea class="alezux-input" name="delivery[credentials]">${JSON.stringify(getVal('delivery.credentials', {}))}</textarea>
                        
                        <label>Archivos Finales (URLs):</label>
                        <textarea class="alezux-input" name="delivery[final_assets]">${getVal('delivery.final_assets', []).join('\n')}</textarea>
                    </div>
                </div>
            </form>
        `;

        $('#modal-body-content').html(html);
    }

    // Save Project Data
    $('#save-project-btn').on('click', function () {
        const form = $('#project-edit-form');
        const projectId = form.data('id');
        if (!projectId) return;

        // Collect Data
        // This is a naive collection, in production we need better parsing
        const formData = {
            identity: {
                proposal_files: $('input[name="identity[proposal_files]"]').val().split(',').map(s => s.trim()).filter(s => s)
            },
            web_design: {
                figma_url: $('input[name="web_design[figma_url]"]').val()
            },
            development: {
                staging_url: $('input[name="development[staging_url]"]').val()
            },
            delivery: {
                // simple parse for now
                final_assets: $('textarea[name="delivery[final_assets]"]').val().split('\n')
            }
        };

        $.ajax({
            url: alezux_agency_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'alezux_agency_update_project_data',
                project_id: projectId,
                project_data: JSON.stringify(formData),
                nonce: alezux_agency_vars.nonce
            },
            success: function (response) {
                if (response.success) {
                    alert('Guardado correctamente');
                    modal.hide();
                } else {
                    alert('Error: ' + response.data);
                }
            }
        });
    });

    closeBtn.on('click', function () {
        modal.hide();
    });

    $(window).on('click', function (event) {
        if (event.target == modal[0]) {
            modal.hide();
        }
    });

    // Add Project Logic
    $('#add-project-btn').on('click', function () {
        // Simple User Selection Logic for MVP
        // In a real scenario we would use Select2 with AJAX search
        const clientEmail = prompt("Ingresa el ID del usuario cliente para crear un proyecto:");
        if (clientEmail) {
            $.ajax({
                url: alezux_agency_vars.ajax_url,
                type: 'POST',
                data: {
                    action: 'alezux_agency_create_project',
                    client_id: clientEmail, // For now passing ID directly
                    nonce: alezux_agency_vars.nonce
                },
                success: function (response) {
                    if (response.success) {
                        loadProjects();
                    } else {
                        alert(response.data);
                    }
                }
            });
        }
    });

});
