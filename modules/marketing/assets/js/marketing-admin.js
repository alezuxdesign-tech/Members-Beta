jQuery(document).ready(function ($) {

    // Sólo ejecutar si existe el wrapper
    if ($('.alezux-marketing-wrapper').length === 0) return;

    var tableBody = $('#marketing-templates-table tbody');
    var modalTemplate = $('#marketing-template-modal');
    var modalSettings = $('#marketing-settings-modal');

    // 1. Load Templates
    function loadTemplates() {
        tableBody.html('<tr><td colspan="4" style="text-align:center;">Cargando...</td></tr>');

        $.ajax({
            url: alezux_marketing_vars.ajax_url,
            method: 'POST',
            data: {
                action: 'alezux_marketing_get_templates',
                nonce: alezux_marketing_vars.nonce
            },
            success: function (res) {
                if (res.success) {
                    renderTable(res.data);
                } else {
                    tableBody.html('<tr><td colspan="4">Error al cargar datos.</td></tr>');
                }
            }
        });
    }

    function renderTable(data) {
        tableBody.empty();
        data.forEach(function (item) {
            var statusBadge = item.is_active
                ? '<span class="status-badge status-active">Activo</span>'
                : '<span class="status-badge status-inactive">Inactivo</span>';

            var row = `
                <tr>
                    <td><strong>${item.label}</strong><br><small style="color:#888">${item.type}</small></td>
                    <td>${item.subject}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <button class="alezux-action-btn edit-template-btn" data-type="${item.type}">
                            <i class="fa fa-pencil"></i> Editar
                        </button>
                    </td>
                </tr>
            `;
            tableBody.append(row);
        });
    }

    loadTemplates();

    // 2. Open Edit Modal
    $(document).on('click', '.edit-template-btn', function () {
        var type = $(this).data('type');

        // Cargar datos individuales
        $.ajax({
            url: alezux_marketing_vars.ajax_url,
            method: 'POST',
            data: {
                action: 'alezux_marketing_get_template',
                type: type,
                nonce: alezux_marketing_vars.nonce
            },
            success: function (res) {
                if (res.success) {
                    var tpl = res.data;
                    $('#tpl-type').val(tpl.type);
                    $('#tpl-subject').val(tpl.subject);
                    $('#tpl-content').val(tpl.content);
                    $('#tpl-active').prop('checked', tpl.is_active == 1);

                    $('#modal-title').text('Editando: ' + type);
                    modalTemplate.css('display', 'flex');
                }
            }
        });
    });

    // 3. Open Settings Modal
    $('#btn-marketing-settings').on('click', function () {
        // Cargar settings
        $.ajax({
            url: alezux_marketing_vars.ajax_url,
            method: 'POST',
            data: {
                action: 'alezux_marketing_get_settings',
                nonce: alezux_marketing_vars.nonce
            },
            success: function (res) {
                if (res.success) {
                    var s = res.data;
                    $('#set-from-name').val(s.from_name);
                    $('#set-from-email').val(s.from_email);
                    $('#set-logo-url').val(s.logo_url);
                    modalSettings.css('display', 'flex');
                }
            }
        });
    });

    // 4. Save Template
    $('#marketing-template-form').on('submit', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();
        formData += '&action=alezux_marketing_save_template&nonce=' + alezux_marketing_vars.nonce;

        $.post(alezux_marketing_vars.ajax_url, formData, function (res) {
            if (res.success) {
                alert(res.data.message);
                modalTemplate.hide();
                loadTemplates(); // Reload table
            } else {
                alert('Error: ' + res.data);
            }
        });
    });

    // 5. Save Settings
    $('#marketing-settings-form').on('submit', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();
        formData += '&action=alezux_marketing_save_settings&nonce=' + alezux_marketing_vars.nonce;

        $.post(alezux_marketing_vars.ajax_url, formData, function (res) {
            if (res.success) {
                alert(res.data.message);
                modalSettings.hide();
            }
        });
    });

    // Close Modals
    $('.alezux-close-modal').on('click', function () {
        $(this).closest('.alezux-modal').hide();
    });

    $(window).on('click', function (e) {
        if ($(e.target).hasClass('alezux-modal')) {
            $('.alezux-modal').hide();
        }
    });

});
