jQuery(window).on('elementor/frontend/init', function () {
    elementorFrontend.hooks.addAction('frontend/element_ready/alezux_sales_history.default', function ($scope, $) {

        const $wrapper = $scope.find('.alezux-sales-app');
        if ($wrapper.length === 0) return;

        const $tbody = $wrapper.find('.alezux-finanzas-table tbody');
        const $pagination = $wrapper.find('.alezux-pagination');
        const $spinner = $wrapper.find('.alezux-loading');

        // Filtros scoped
        const $searchInput = $wrapper.find('#alezux-sales-search');
        const $courseFilter = $wrapper.find('#alezux-filter-course');
        const $statusFilter = $wrapper.find('#alezux-filter-status');
        const $dateFilter = $wrapper.find('#alezux-filter-date');
        const $limitSelect = $wrapper.find('#alezux-limit-select');
        const $clearDateBtn = $wrapper.find('#alezux-clear-date');

        let currentPage = 1;

        function fetchSales() {
            if (!alezux_finanzas_vars || !alezux_finanzas_vars.is_logged_in) {
                return;
            }

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

            $.post(alezux_finanzas_vars.ajax_url, data, function (response) {
                if (response.success) {
                    renderTable(response.data.rows);
                    renderPagination(response.data);
                } else {
                    $tbody.html('<tr><td colspan="7" style="text-align:center; padding: 20px; color: #ff6b6b;">Error: ' + response.data + '</td></tr>');
                }
            })
                .fail(function (xhr, status, error) {
                    $tbody.html('<tr><td colspan="7" style="text-align:center; padding: 20px; color: #ff6b6b;">Error de conexión.</td></tr>');
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

            for (let i = 1; i <= totalPages; i++) {
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

        // Events - Scoped
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

        // Event listener for clear date button
        $clearDateBtn.on('click', function () {
            if ($dateFilter.length && $dateFilter[0]._flatpickr) {
                const fp = $dateFilter[0]._flatpickr;
                fp.clear();
            }
            $dateFilter.val('');
            $(this).hide();
            currentPage = 1;
            fetchSales();
        });

        // Initialize Flatpickr if available
        if ($dateFilter.length > 0) {
            if (typeof flatpickr !== 'undefined') {
                flatpickr($dateFilter[0], {
                    mode: "range",
                    dateFormat: "Y-m-d",
                    locale: "es",
                    onChange: function (selectedDates) {
                        if (selectedDates.length > 0) {
                            $clearDateBtn.show();
                        } else {
                            $clearDateBtn.hide();
                        }
                    },
                    onClose: function (selectedDates) {
                        if (selectedDates.length > 0) {
                            $clearDateBtn.show();
                        } else {
                            $clearDateBtn.hide();
                        }
                        currentPage = 1;
                        fetchSales();
                    }
                });
            }
        }

        // Init
        fetchSales();

    });
});
