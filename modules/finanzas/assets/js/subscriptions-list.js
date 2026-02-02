jQuery(document).ready(function ($) {

    if ($('.alezux-subs-list-app').length === 0) return;

    const $wrapper = $('.alezux-subs-list-app');
    const $tbody = $wrapper.find('.alezux-subs-table tbody');
    const $spinner = $wrapper.find('.alezux-loading-subs');
    const $searchInput = $wrapper.find('#alezux-subs-search');

    function fetchSubscriptions() {
        $tbody.css('opacity', '0.5');
        $spinner.show();

        const data = {
            action: 'alezux_get_subscriptions_list',
            nonce: alezux_finanzas_vars.nonce,
            search: $searchInput.val()
        };

        $.post(alezux_finanzas_vars.ajax_url, data, function (response) {
            $spinner.hide();
            $tbody.css('opacity', '1');

            if (response.success) {
                renderTable(response.data);
            } else {
                $tbody.html('<tr><td colspan="6">Error: ' + response.data + '</td></tr>');
            }
        });
    }

    function renderTable(rows) {
        $tbody.empty();

        if (rows.length === 0) {
            $tbody.html('<tr><td colspan="6" style="text-align:center;">No se encontraron suscripciones.</td></tr>');
            return;
        }

        rows.forEach(row => {
            let statusClass = 'status-pending';
            if (row.status === 'active') statusClass = 'status-active';
            if (row.status === 'completed') statusClass = 'status-completed';
            if (row.status === 'canceled') statusClass = 'status-canceled';
            if (row.status === 'past_due') statusClass = 'status-past_due';

            const html = `
                <tr>
                    <td>#${row.id}</td>
                    <td>${row.student}</td>
                    <td><strong>${row.plan}</strong></td>
                    <td>${row.amount}</td>
                    <td><span class="alezux-status-badge ${statusClass}">${row.status.toUpperCase()}</span></td>
                    <td>${row.progress}</td>
                    <td>${row.next_payment}</td>
                    <td>
                        <button class="alezux-btn-manual-pay" data-id="${row.id}" title="Pago Manual">
                            <i class="eicon-wallet"></i>
                        </button>
                    </td>
                </tr>
            `;
            $tbody.append(html);
        });
    }

    // Modal Logic
    const $modal = $('#alezux-manual-pay-modal');
    const $closeModal = $('.alezux-close-modal');
    const $btnConfirm = $('#btn-confirm-manual-pay');
    let currentSubId = 0;

    // Abrir Modal
    $(document).on('click', '.alezux-btn-manual-pay', function () {
        currentSubId = $(this).data('id');
        $('#modal-sub-id').text(currentSubId);
        $('#manual-pay-amount').val('');
        $('#manual-pay-note').val('');
        $modal.show();
    });

    // Cerrar Modal
    $closeModal.on('click', function () {
        $modal.hide();
    });

    $(window).on('click', function (event) {
        if ($(event.target).is($modal)) {
            $modal.hide();
        }
    });

    // Confirmar Pago
    $btnConfirm.on('click', function () {
        const amount = $('#manual-pay-amount').val();
        const note = $('#manual-pay-note').val();

        if (!amount || !note) {
            alert('Por favor ingrese monto y motivo.');
            return;
        }

        $btnConfirm.text('Procesando...').prop('disabled', true);

        $.post(alezux_finanzas_vars.ajax_url, {
            action: 'alezux_manual_subs_payment',
            nonce: alezux_finanzas_vars.nonce,
            subscription_id: currentSubId,
            amount: amount,
            note: note
        }, function (response) {
            $btnConfirm.text('Registrar Pago').prop('disabled', false);
            if (response.success) {
                alert('Pago registrado correctamente.');
                $modal.hide();
                fetchSubscriptions(); // Recargar tabla
            } else {
                alert('Error: ' + response.data);
            }
        });
    });

    // Filtros
    let timeout = null;
    $searchInput.on('keyup', function () {
        clearTimeout(timeout);
        timeout = setTimeout(fetchSubscriptions, 500);
    });

    // Init
    fetchSubscriptions();
});
