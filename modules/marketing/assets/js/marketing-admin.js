jQuery(document).ready(function ($) {

    // Main Init Function
    function initMarketingAdmin($scope) {
        var wrapper = $scope ? $scope.find('.alezux-marketing-wrapper') : $('.alezux-marketing-wrapper');

        if (wrapper.length === 0) return;

        var tableBody = wrapper.find('#marketing-templates-table tbody');
        var modalTemplate = wrapper.find('#marketing-template-modal');
        var modalSettings = wrapper.find('#marketing-settings-modal');

        // Unbind previous events to avoid duplicates if re-initialized
        $(document).off('click', '.edit-template-btn');
        wrapper.find('#btn-marketing-settings').off('click');
        wrapper.find('#marketing-template-form').off('submit');
        wrapper.find('#marketing-settings-form').off('submit');
        wrapper.find('.alezux-close-modal').off('click');

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
                },
                error: function (err) {
                    console.error('Marketing AJAX Error', err);
                    tableBody.html('<tr><td colspan="4">Error de conexión.</td></tr>');
                }
            });
        }

        function renderTable(data) {
            tableBody.empty();
            if (!data || data.length === 0) {
                tableBody.html('<tr><td colspan="4">No hay plantillas disponibles.</td></tr>');
                return;
            }

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
                            <button class="alezux-marketing-btn edit-template-btn" data-type="${item.type}">
                                <i class="fa fa-pencil"></i> Editar
                            </button>
                        </td>
                    </tr>
                `;
                tableBody.append(row);
            });
        }

        loadTemplates();

        // 2. Open Edit Modal - Delegated to document for dynamic elements, but scoped check would be good. 
        // using document is safer for re-renders.
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
        wrapper.find('#btn-marketing-settings').on('click', function () {
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
        wrapper.find('#marketing-template-form').on('submit', function (e) {
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
        wrapper.find('#marketing-settings-form').on('submit', function (e) {
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
        wrapper.find('.alezux-close-modal').on('click', function () {
            $(this).closest('.alezux-modal').hide();
        });

        $(window).on('click', function (e) {
            if ($(e.target).hasClass('alezux-modal')) {
                $('.alezux-modal').hide();
            }
        });
    }

    // Initialize on Elementor Frontend Init
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/alezux_marketing_config.default', function ($scope) {
            initMarketingAdmin($scope);
        });
    });

    // Also fallback for direct load (if not loaded via Elementor JS framework or standalone)
    initMarketingAdmin(null);

});
