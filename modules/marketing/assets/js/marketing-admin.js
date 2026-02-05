
jQuery(document).ready(function ($) {

    // Main Init Function
    function initMarketingAdmin($scope) {
        var wrapper = $scope ? $scope.find('.alezux-marketing-app') : $('.alezux-marketing-app');

        if (wrapper.length === 0) return;

        // Check for localize vars before using
        if (typeof alezux_marketing_vars === 'undefined') {
            console.warn('Alezux Marketing: Variables not loaded. Likely in Editor mode without frontend assets.');
        }

        var tableBody = wrapper.find('.marketing-templates-table tbody');
        var modalTemplate = wrapper.find('#marketing-template-modal');
        var modalSettings = wrapper.find('#marketing-settings-modal');

        // Unbind previous events to avoid duplicates if re-initialized
        $(document).off('click', '.edit-template-btn');

        wrapper.find('#btn-marketing-settings').off('click');
        wrapper.find('#marketing-template-form').off('submit');
        wrapper.find('#marketing-settings-form').off('submit');
        wrapper.find('.alezux-close-modal').off('click');

        // 1. Load Templates
        // 1. Load Templates
        function loadTemplates() {
            // Skip AJAX if in editor mode (preserve PHP dummy data)
            if (wrapper.data('is-editor') === 'yes') {
                return;
            }

            console.log('Alezux Marketing: Loading templates...', { vars: typeof alezux_marketing_vars });

            if (typeof alezux_marketing_vars === 'undefined') {
                console.error('Alezux Marketing: alezux_marketing_vars missing');
                // If no vars, show dummy empty or msg
                if (tableBody.find('tr').length === 0) {
                    tableBody.html('<tr><td colspan="4">Error: Variables no cargadas.</td></tr>');
                }
                return;
            }

            tableBody.html('<tr><td colspan="4" style="text-align:center;">Cargando datos...</td></tr>');

            $.ajax({
                url: alezux_marketing_vars.ajax_url,
                method: 'POST',
                data: {
                    action: 'alezux_marketing_get_templates',
                    nonce: alezux_marketing_vars.nonce
                },
                success: function (res) {
                    console.log('Alezux Marketing: AJAX Success', res);
                    if (res.success) {
                        renderTable(res.data);
                    } else {
                        console.error('Alezux Marketing: AJAX Error Message', res);
                        tableBody.html('<tr><td colspan="4">Error: ' + (res.data || 'Respuesta desconocida') + '</td></tr>');
                    }
                },
                error: function (err) {
                    console.error('Alezux Marketing: AJAX Network Error', err);
                    tableBody.html('<tr><td colspan="4">Error de conexión (' + err.status + ').</td></tr>');
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

        // 2. Open Edit Modal
        $(document).on('click', '.edit-template-btn', function (e) {
            e.preventDefault();
            var type = $(this).data('type'); // data-type is on the button

            // Logic for Dummy/Editor buttons (no data-type)
            if (!type) {
                $('#tpl-type').val('dummy_type');
                $('#tpl-subject').val('Asunto de Prueba para Diseño');
                $('#tpl-content').val('<h1>Hola [Nombre]</h1><p>Este es un cotenido de prueba para visualizar estilos.</p>');
                $('#tpl-active').prop('checked', true);
                $('#modal-title').text('Editando: Plantilla de Prueba');
                modalTemplate.css('display', 'flex');

                // Trigger preview logic for dummy
                wrapper.find('.tab-btn[data-tab="edit"]').click();
                return;
            }

            // Real Data Load
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

                        // Reset tab to edit
                        wrapper.find('.tab-btn[data-tab="edit"]').click();
                    }
                }
            });
        });

        // Tab Handler (Preview)
        wrapper.find('.tab-btn').on('click', function () {
            var tab = $(this).data('tab');
            wrapper.find('.tab-btn').removeClass('active');
            $(this).addClass('active');

            if (tab === 'edit') {
                wrapper.find('#tab-content-edit').show();
                wrapper.find('#tab-content-preview').hide();
            } else {
                wrapper.find('#tab-content-edit').hide();
                wrapper.find('#tab-content-preview').show();

                // Render Preview
                var content = wrapper.find('#tpl-content').val();
                var previewFrame = wrapper.find('#email-preview-frame');
                previewFrame.html(content);
            }
        });

        // 3. Open Settings Modal
        wrapper.find('#btn-marketing-settings').on('click', function (e) {
            e.preventDefault();

            // Check if backend vars exist
            if (typeof alezux_marketing_vars === 'undefined') {
                modalSettings.css('display', 'flex'); // Just open in dummy mode
                return;
            }

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
                },
                error: function () {
                    // Fallback for editor usage if ajax fails
                    modalSettings.css('display', 'flex');
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
                    loadTemplates();
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

        // Global modal click listener
        $(window).off('click.alezuxMarketingModal').on('click.alezuxMarketingModal', function (e) {
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

    // Also fallback for direct load
    initMarketingAdmin(null);

});
