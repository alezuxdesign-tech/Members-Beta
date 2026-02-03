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
    const $dateFilter = $wrapper.find('#alezux-filter-date');
    const $limitSelect = $wrapper.find('#alezux-limit-select');

    let currentPage = 1;
    let limit = 20;

    function fetchSales() {
        if (!alezux_finanzas_vars || !alezux_finanzas_vars.is_logged_in) {
            return;
        }

        console.log('Fetching sales history...');
        $spinner.show();

        let startDate = '';
        let endDate = '';

        // Get dates from Flatpickr instance if valid
        if ($dateFilter.length && $dateFilter[0]._flatpickr) {
            const fp = $dateFilter[0]._flatpickr;
            const selected = fp.selectedDates;
            if (selected.length > 0) {
                const formatDate = (d) => {
                    let month = '' + (d.getMonth() + 1),
                        day = '' + d.getDate(),
                        year = d.getFullYear();
                    if (month.length < 2) month = '0' + month;
                    if (day.length < 2) day = '0' + day;
                    return [year, month, day].join('-');
                };

                startDate = formatDate(selected[0]);
                if (selected.length > 1) {
                    endDate = formatDate(selected[selected.length - 1]);
                } else {
                    // Si es rango y solo eligió uno, a veces flatpickr espera el segundo.
                    // Asumiremos start=end si se cerró solo con 1, o enviamos solo start.
                    // Ajax Handler ya maneja start solo.
                }
            }
        }

        const data = {
            action: 'alezux_get_sales_history',
            nonce: alezux_finanzas_vars.nonce,
            page: currentPage,
            limit: $limitSelect.val(),
            search: $searchInput.val(),
            filter_course: $courseFilter.val(),
            filter_status: $statusFilter.val(),
            start_date: startDate,
            end_date: endDate
        };
        console.log('Request data:', data);

        $.post(alezux_finanzas_vars.ajax_url, data, function (response) {
            console.log('Server response:', response);
            if (response.success) {
                renderTable(response.data.rows);
                renderPagination(response.data);
            } else {
                console.error('Server error:', response.data);
                $tbody.html('<tr><td colspan="7" style="text-align:center; padding: 20px; color: #ff6b6b;">Error: ' + response.data + '</td></tr>');
            }
        })
            .fail(function (xhr, status, error) {
                console.error('AJAX failed:', status, error);
                console.log('XHR:', xhr);
                $tbody.html('<tr><td colspan="7" style="text-align:center; padding: 20px; color: #ff6b6b;">Error de conexión con el servidor. Revisar consola.</td></tr>');
            })
            .always(function () {
                $spinner.hide();
                $tbody.css('opacity', '1');
            });
    }

    function renderTable(rows) {
        $tbody.empty();

        if (rows.length === 0) {
            $tbody.html('<tr><td colspan="7" style="text-align:center;">No se encontraron registros.</td></tr>');
            return;
        }

        rows.forEach(row => {
            let statusClass = 'status-' + (row.status || 'pending');
            let method = row.method || 'Desconocido';
            let amount = row.amount || '0';
            let course = row.course || 'Desconocido';
            let quotas_desc = row.quotas_desc || '';
            let date = row.date || '-';

            // Badge para método
            let methodIcon = '';
            if (method.toLowerCase().includes('stripe')) methodIcon = '<i class="fab fa-stripe"></i> ';

            const html = `
                <tr>

                    <td>${row.student}</td>
                    <td>${methodIcon}${method}</td>
                    <td><strong>${amount}</strong></td>
                    <td>${course}<br><small class="text-muted">${quotas_desc}</small></td>
                    <td><span class="alezux-status-badge ${statusClass}">${row.status || 'Unknown'}</span></td>
                    <td>${date}</td>
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

    const $clearDateBtn = $wrapper.find('#alezux-clear-date');

    // ... (rest of variable definitions)

    // Event listener for clear date button
    $clearDateBtn.on('click', function () {
        if ($dateFilter.length && $dateFilter[0]._flatpickr) {
            const fp = $dateFilter[0]._flatpickr;
            fp.clear(); // Clear flatpickr
        }
        $dateFilter.val(''); // Force clear input
        $(this).hide(); // Hide button
        currentPage = 1;
        fetchSales();
    });

    // Initialize Flatpickr if available
    if ($dateFilter.length > 0) {
        if (typeof flatpickr !== 'undefined') {
            flatpickr($dateFilter[0], {
                mode: "range",
                dateFormat: "Y-m-d",
                locale: "es", // Spanish locale
                onChange: function (selectedDates, dateStr, instance) {
                    if (selectedDates.length > 0) {
                        $clearDateBtn.show();
                    } else {
                        $clearDateBtn.hide();
                    }
                },
                onClose: function (selectedDates, dateStr, instance) {
                    // Button visibility handled in onChange, but verify ensuring sync
                    if (selectedDates.length > 0) {
                        $clearDateBtn.show();
                    } else {
                        $clearDateBtn.hide();
                    }
                    currentPage = 1;
                    fetchSales();
                }
            });
        } else {
            console.warn('Flatpickr library not found. Date filtering disabled.');
        }
    }

    // Init
    fetchSales();

});
