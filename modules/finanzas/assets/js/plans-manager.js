jQuery(document).ready(function ($) {

    if ($('.alezux-plans-app').length === 0) return;

    const $wrapper = $('.alezux-plans-app');
    const $tbody = $wrapper.find('.alezux-finanzas-table tbody');
    const $spinner = $wrapper.find('.alezux-loading');
    const $searchInput = $wrapper.find('#alezux-plans-search');
    const $courseFilter = $wrapper.find('#alezux-plans-course');
    const $limitSelect = $wrapper.find('#alezux-limit-select');
    const $pagination = $wrapper.find('.alezux-pagination');

    let currentPage = 1;

    function fetchPlans() {
        if (!alezux_finanzas_vars || !alezux_finanzas_vars.is_logged_in) {
            return;
        }

        $tbody.css('opacity', '0.5');

        const data = {
            action: 'alezux_get_plans_list',
            nonce: alezux_finanzas_vars.nonce,
            search: $searchInput.val(),
            course_id: $courseFilter.val(),
            limit: $limitSelect.val(),
            paged: currentPage
        };

        $.post(alezux_finanzas_vars.ajax_url, data, function (response) {
            $spinner.hide();
            $tbody.css('opacity', '1');

            if (response.success) {
                renderTable(response.data.rows || response.data); // Handle both array and object responses
                renderPagination(response.data.total_pages || 1);
            } else {
                $tbody.html('<tr><td colspan="7">Error: ' + response.data + '</td></tr>');
            }
        });
    }

    function renderTable(rows) {
        $tbody.empty();

        if (rows.length === 0) {
            $tbody.html('<tr><td colspan="7" style="text-align:center;">No se encontraron planes.</td></tr>');
            return;
        }

        rows.forEach(row => {
            const html = `
                <tr>

                    <td><strong>${row.name}</strong></td>
                    <td>${row.course}</td>
                    <td>${row.price}</td>
                    <td>${row.quotas}</td>
                    <td>${row.frequency}</td>
                    <td>
                        <button class="page-btn btn-copy-link" data-link="${row.buy_link}" title="Copiar Link de Pago Directo">
                            <i class="eicon-link"></i> Link
                        </button>
                        <button class="page-btn btn-edit-plan" data-id="${row.id}" title="Editar Plan">
                            <i class="eicon-pencil"></i>
                        </button>
                        <button class="page-btn btn-delete-plan" data-id="${row.id}" title="Eliminar Plan" style="color:#d9534f; border-color:#d9534f;">
                            <i class="eicon-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $tbody.append(html);
        });
    }

    // Event Listeners
    let timeout = null;
    $searchInput.on('keyup', function () {
        clearTimeout(timeout);
        timeout = setTimeout(function () {
            currentPage = 1;
            fetchPlans();
        }, 500);
    });

    $courseFilter.on('change', function () {
        currentPage = 1;
        fetchPlans();
    });

    $limitSelect.on('change', function () {
        currentPage = 1;
        fetchPlans();
    });

    $pagination.on('click', '.page-btn', function () {
        if ($(this).hasClass('active') || $(this).hasClass('disabled')) return;
        currentPage = $(this).data('page');
        fetchPlans();
    });

    function renderPagination(totalPages) {
        $pagination.empty();
        if (totalPages <= 1) return;

        let html = '';

        // Previous
        if (currentPage > 1) {
            html += `<button class="page-btn" data-page="${currentPage - 1}">«</button>`;
        }

        // Pages
        for (let i = 1; i <= totalPages; i++) {
            // Show first, last, current, and surrounding pages
            if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                const activeClass = i === currentPage ? 'active' : '';
                html += `<button class="page-btn ${activeClass}" data-page="${i}">${i}</button>`;
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                html += `<span class="page-dots">...</span>`;
            }
        }

        // Next
        if (currentPage < totalPages) {
            html += `<button class="page-btn" data-page="${currentPage + 1}">»</button>`;
        }

        $pagination.html(html);
    }

    // Acciones existing...
    $tbody.on('click', '.btn-delete-plan', function () {
        const id = $(this).data('id');
        if (confirm('¿Estás seguro de eliminar este plan? Esto no eliminará las suscripciones activas en Stripe, pero lo quitará de la base de datos local.')) {
            $.post(alezux_finanzas_vars.ajax_url, {
                action: 'alezux_delete_plan',
                nonce: alezux_finanzas_vars.nonce,
                id: id
            }, function (response) {
                if (response.success) {
                    fetchPlans();
                } else {
                    alert('Error al eliminar: ' + response.data);
                }
            });
        }
    });

    $tbody.on('click', '.btn-copy-link', function () {
        const link = $(this).data('link');
        navigator.clipboard.writeText(link).then(function () {
            alert('Enlace copiado al portapapeles:\n' + link);
        }, function (err) {
            alert('No se pudo copiar el enlace. Cópielo manualmente:\n' + link);
        });
    });

    /* --- EDIT PLAN LOGIC --- */
    const $editModal = $('#alezux-edit-plan-modal');
    const $editForm = $('#alezux-edit-plan-form');

    // Open Modal
    $tbody.on('click', '.btn-edit-plan', function (e) {
        e.preventDefault();
        console.log('Edit button clicked');
        const planId = $(this).data('id');
        console.log('Plan ID:', planId);

        // Debug Modal Presence
        if ($('#alezux-edit-plan-modal').length === 0) {
            console.error('ERROR: Modal #alezux-edit-plan-modal NOT FOUND in DOM.');
            alert('Error: Modal de edición no encontrado en el HTML.');
            return;
        }

        openEditModal(planId);
    });

    // Close Modal
    $(document).on('click', '.alezux-close-modal', function () {
        $('#alezux-edit-plan-modal').fadeOut();
    });

    function openEditModal(planId) {
        console.log('Opening modal for plan:', planId);
        $editModal.css('display', 'flex').hide().fadeIn(); // Flex for centering

        // Reset Form
        $editForm[0].reset();
        $('#edit-plan-rules-container').html('<div class="alezux-spinner">Cargando datos...</div>');
        $('#edit-plan-id').val(planId);

        // Fetch Plan Details
        $.post(alezux_finanzas_vars.ajax_url, {
            action: 'alezux_get_plan_details',
            nonce: alezux_finanzas_vars.nonce,
            plan_id: planId
        }, function (response) {
            if (response.success) {
                const plan = response.data;

                // Populate Basic Fields
                $('#edit-plan-name').val(plan.name);
                $('#edit-plan-course').val(plan.course_name);
                $('#edit-plan-price').val(plan.quota_amount + ' USD');
                $('#edit-plan-quotas').val(plan.total_quotas);

                // Helper to build rules table
                fetchCourseModulesForEdit(plan.course_id, plan.access_rules, plan.total_quotas);

            } else {
                alert('Error recuperando el plan: ' + response.data);
                $editModal.fadeOut();
            }
        });
    }

    function fetchCourseModulesForEdit(courseId, currentRules, totalQuotas) {
        $.post(alezux_finanzas_vars.ajax_url, {
            action: 'alezux_get_course_modules',
            nonce: alezux_finanzas_vars.nonce,
            course_id: courseId
        }, function (response) {
            if (response.success) {
                renderEditRulesTable(response.data, currentRules, totalQuotas);
            } else {
                $('#edit-plan-rules-container').html('<p style="color:red">Error cargando módulos.</p>');
            }
        });
    }

    function renderEditRulesTable(modules, currentRules, totalQuotas) {
        // currentRules might be object or array. Convert to object map for easy lookup.
        // PHP json_encode of object with numeric keys might become array in JS if sequential, or object if gaps.
        // Let's ensure it's handled.

        let html = '<table class="alezux-rules-table">';
        html += '<thead><tr><th>Módulo / Lección</th><th>Se desbloquea al pagar:</th></tr></thead><tbody>';

        modules.forEach(function (mod) {
            const ruleValue = (currentRules && currentRules[mod.id]) ? currentRules[mod.id] : 1;

            html += '<tr>';
            html += '<td>' + mod.title + '</td>';
            html += '<td>';
            html += `<select name="rules[${mod.id}]" class="alezux-quota-select">`;
            html += '<option value="1">Cuota 1 (Inmediato)</option>';
            for (var i = 2; i <= totalQuotas; i++) {
                const selected = (i == ruleValue) ? 'selected' : '';
                html += `<option value="${i}" ${selected}>Cuota ${i}</option>`;
            }
            html += '</select>';
            html += '</td>';
            html += '</tr>';
        });

        html += '</tbody></table>';
        $('#edit-plan-rules-container').html(html);
    }

    // Save Changes
    $editForm.on('submit', function (e) {
        e.preventDefault();
        const btn = $(this).find('.alezux-btn-save');
        const originalText = btn.text();
        btn.text('Guardando...').prop('disabled', true);

        const formData = $(this).serialize(); // Includes rules array

        $.post(alezux_finanzas_vars.ajax_url,
            formData + '&action=alezux_update_plan&nonce=' + alezux_finanzas_vars.nonce,
            function (response) {
                btn.text(originalText).prop('disabled', false);

                if (response.success) {
                    alert('Plan actualizado correctamente.');
                    $editModal.fadeOut();
                    fetchPlans(); // Refresh Table
                } else {
                    alert('Error: ' + response.data);
                }
            }
        );
    });

    // Init
    fetchPlans();
});
