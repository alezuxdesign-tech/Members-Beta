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

    // Init
    fetchPlans();
});
