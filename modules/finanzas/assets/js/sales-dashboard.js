jQuery(document).ready(function ($) {
    const ajaxUrl = alezux_dashboard_vars.ajax_url;
    const nonce = alezux_dashboard_vars.nonce;
    const is_logged_in = alezux_dashboard_vars.is_logged_in;

    // 1. Global Date Filter Logic
    const $dateInput = $('.alezux-date-global-input');

    if ($dateInput.length > 0) {
        $dateInput.flatpickr({
            mode: "range",
            dateFormat: "Y-m-d",
            locale: "es", // Assuming Spanish based on context
            onChange: function (selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    const dateFrom = instance.formatDate(selectedDates[0], "Y-m-d");
                    const dateTo = instance.formatDate(selectedDates[1], "Y-m-d");

                    // Trigger Global Event
                    $(document).trigger('alezux_date_filter_change', { dateFrom: dateFrom, dateTo: dateTo });

                    // Show clear button logic if needed, or just keep input filled
                }
            }
        });

        // Clear button logic
        $('.alezux-clear-global-date').on('click', function () {
            const fp = $dateInput[0]._flatpickr;
            fp.clear();
            $(document).trigger('alezux_date_filter_change', { dateFrom: '', dateTo: '' });
        });
    }

    // 2. Stats (KPIs) Logic
    function updateStats(dateFrom, dateTo) {
        $('.alezux-kpi-card').each(function () {
            const $card = $(this);
            const statType = $card.data('stat-type');
            const $value = $card.find('.alezux-kpi-number');
            const $loading = $card.find('.alezux-kpi-loading');

            $loading.show();

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'alezux_get_sales_stats',
                    nonce: nonce,
                    stat_type: statType,
                    date_from: dateFrom,
                    date_to: dateTo
                },
                success: function (response) {
                    if (response.success) {
                        const data = response.data;
                        let val = 0;
                        if (statType === 'month_revenue') val = data.month_revenue;
                        if (statType === 'today_revenue') val = data.today_revenue;

                        // Format Currency
                        const formatted = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(val);
                        $value.text(formatted);
                    } else {
                        $value.text('Error');
                    }
                },
                complete: function () {
                    $loading.hide();
                }
            });
        });
    }

    // 3. Chart Logic
    const charts = {}; // Store chart instances by ID

    function updateCharts(dateFrom, dateTo) {
        $('.alezux-chart-card').each(function () {
            const canvas = $(this)[0];
            const chartId = canvas.id;
            const chartType = $(this).data('chart-type');
            const $container = $(this).closest('.alezux-chart-container');
            const $loading = $container.find('.alezux-chart-loading');

            // Colors
            const colorStripe = $(this).data('color-stripe') || '#6772e5';
            const colorManual = $(this).data('color-manual') || '#2ecc71';
            const colorPaypal = $(this).data('color-paypal') || '#003087';

            $loading.show(); // Need to ensure CSS positions this correctly over container

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'alezux_get_sales_stats',
                    nonce: nonce,
                    stat_type: 'chart_data',
                    date_from: dateFrom,
                    date_to: dateTo
                },
                success: function (response) {
                    if (response.success) {
                        const data = response.data.chart_data;
                        const chartData = {
                            labels: ['Stripe', 'Manual', 'PayPal'],
                            datasets: [{
                                label: 'Ingresos',
                                data: [data.stripe, data.manual, data.paypal],
                                backgroundColor: [colorStripe, colorManual, colorPaypal],
                                hoverOffset: 4
                            }]
                        };

                        if (charts[chartId]) {
                            charts[chartId].data = chartData;
                            charts[chartId].update();
                        } else {
                            // Init Chart
                            charts[chartId] = new Chart(canvas, {
                                type: chartType,
                                data: chartData,
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            position: 'bottom',
                                            labels: { color: '#fff' }
                                        }
                                    }
                                }
                            });
                        }
                    }
                },
                complete: function () {
                    $loading.hide();
                }
            });
        });
    }

    // Initial Load
    updateStats('', '');
    updateCharts('', '');

    // Listen to Global Event
    $(document).on('alezux_date_filter_change', function (e, data) {
        updateStats(data.dateFrom, data.dateTo);
        updateCharts(data.dateFrom, data.dateTo);
    });

});
