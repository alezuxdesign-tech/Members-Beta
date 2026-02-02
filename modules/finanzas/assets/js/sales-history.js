jQuery(document).ready(function ($) {

    // Solo si existe el contenedor
    if ($('.alezux-sales-app').length === 0) return;

    const $wrapper = $('.alezux-sales-app');
    const $tbody = $wrapper.find('.alezux-finanzas-table tbody');
    const $pagination = $wrapper.find('.alezux-pagination');
    const $spinner = $wrapper.find('.alezux-loading');

    // Filtros
    const $searchInput = $wrapper.find('#alezux-sales-search');
    const $courseFilter = $wrapper.find('#alezux-filter-course');
    const $statusFilter = $wrapper.find('#alezux-filter-status');
    const $limitSelect = $wrapper.find('#alezux-limit-select');

    let currentPage = 1;
    let limit = 20;

    function fetchSales() {
        $tbody.css('opacity', '0.5');
        $spinner.show();

        const data = {
            action: 'alezux_get_sales_history',
            nonce: alezux_finanzas_vars.nonce,
            page: currentPage,
            limit: $limitSelect.val(),
            search: $searchInput.val(),
            filter_course: $courseFilter.val(),
            filter_status: $statusFilter.val()
        };

        $.post(alezux_finanzas_vars.ajax_url, data, function (response) {
            $spinner.hide();
            $tbody.css('opacity', '1');

            if (response.success) {
                renderTable(response.data.rows);
                renderPagination(response.data);
            } else {
                $tbody.html('<tr><td colspan="7">Error al cargar datos: ' + response.data + '</td></tr>');
            }
        });
    }

    function renderTable(rows) {
        $tbody.empty();

        if (rows.length === 0) {
            $tbody.html('<tr><td colspan="7" style="text-align:center;">No se encontraron registros.</td></tr>');
            return;
        }

        rows.forEach(row => {
            let statusClass = 'status-' + row.status;

            // Badge para método
            let methodIcon = '';
            if (row.method.toLowerCase().includes('stripe')) methodIcon = '<i class="fab fa-stripe"></i> ';

            const html = `
                <tr>
                    <td>#${row.id}</td>
                    <td>${row.student}</td>
                    <td>${methodIcon}${row.method}</td>
                    <td><strong>${row.amount}</strong></td>
                    <td>${row.course}<br><small class="text-muted">${row.quotas_desc}</small></td>
                    <td><span class="alezux-status-badge ${statusClass}">${row.status}</span></td>
                    <td>${row.date}</td>
                </tr>
            `;
            $tbody.append(html);
        });
    }

    function renderPagination(data) {
        $pagination.empty();
        const totalPages = data.pages;
        const current = data.current_page;

        if (totalPages <= 1) return;

        let html = '';

        // Prev
        if (current > 1) {
            html += `<button class="page-btn" data-page="${current - 1}">&laquo;</button>`;
        }

        // Simple Logic: Show all or truncated? Let's show simple range
        for (let i = 1; i <= totalPages; i++) {
            // Show first, last, current, and surrounding
            if (i === 1 || i === totalPages || (i >= current - 2 && i <= current + 2)) {
                let active = (i === current) ? 'active' : '';
                html += `<button class="page-btn ${active}" data-page="${i}">${i}</button>`;
            } else if (i === current - 3 || i === current + 3) {
                html += `<span class="page-dots">...</span>`;
            }
        }

        // Next
        if (current < totalPages) {
            html += `<button class="page-btn" data-page="${current + 1}">&raquo;</button>`;
        }

        $pagination.html(html);
    }

    // Events

    // Debounce search
    let timeout = null;
    $searchInput.on('keyup', function () {
        clearTimeout(timeout);
        timeout = setTimeout(function () {
            currentPage = 1;
            fetchSales();
        }, 500);
    });

    $courseFilter.on('change', function () {
        currentPage = 1;
        fetchSales();
    });

    $statusFilter.on('change', function () {
        currentPage = 1;
        fetchSales();
    });

    $limitSelect.on('change', function () {
        currentPage = 1;
        fetchSales();
    });

    $pagination.on('click', '.page-btn', function () {
        currentPage = $(this).data('page');
        fetchSales();
    });

    // Init
    fetchSales();

});
