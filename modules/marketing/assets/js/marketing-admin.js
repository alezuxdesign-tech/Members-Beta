
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
                // Reset mode to edit
                $('#toggle-preview-mode').prop('checked', false).trigger('change');
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

                        // Reset mode to edit
                        $('#toggle-preview-mode').prop('checked', false).trigger('change');
                    }
                }
            });
        });

        // Tab Handler (Preview)
        // Toggle Edit/Preview Mode
        wrapper.find('#toggle-preview-mode').on('change', function () {
            var isPreview = $(this).is(':checked');

            if (isPreview) {
                // Switch to Preview
                wrapper.find('#mode-status-text').text('Vista Previa');
                $('#tab-content-edit').hide();
                $('#tab-content-preview').show();

                // Render Preview with Variable Replacement
                var content = wrapper.find('#tpl-content').val();

                // Replace common vars to avoid 404s and show better preview
                var logoUrl = (typeof alezux_marketing_vars !== 'undefined' && alezux_marketing_vars.logo_url)
                    ? alezux_marketing_vars.logo_url
                    : 'https://via.placeholder.com/150x50?text=LOGO';

                // Replace {{logo_url}} BEFORE inserting into DOM to prevent 404 request
                content = content.replace(/{{logo_url}}/g, logoUrl);
                content = content.replace(/{{site_name}}/g, 'Mi Escuela');
                content = content.replace(/{{user.name}}/g, 'Estudiante');
                content = content.replace(/{{home_url}}/g, '#');

                wrapper.find('#email-preview-frame').html(content);

                // CSS Fix: If content starts with "body {", wrap it in <style>
                // This handles cases where raw CSS is stored at the beginning of the template
                var isRawCss = content.trim().startsWith('body {') || content.trim().startsWith('.container {');
                if (isRawCss) {
                    wrapper.find('#email-preview-frame').html('<style>' + content + '</style><body><p>Vista previa no disponible para este formato de CSS.</p></body>');
                }

                // BETTER APPROACH for the specific issue shown in screenshot:
                // The screenshot shows raw CSS text being rendered as body content.
                // This means the template likely contains the CSS *outside* of a <style> tag or inside <body> but intended for head.

                // Let's wrap standard CSS patterns in <style> if they appear raw at start
                var previewHtml = content;
                if (previewHtml.trim().indexOf('body {') === 0) {
                    // It seems the template is JUST css + html mixed without tags?
                    // Or maybe it was saved without <style> tags.
                    // Let's look for the first HTML tag
                    var firstTag = previewHtml.indexOf('<');
                    if (firstTag > 0) {
                        var cssPart = previewHtml.substring(0, firstTag);
                        var htmlPart = previewHtml.substring(firstTag);
                        previewHtml = '<style>' + cssPart + '</style>' + htmlPart;
                    }
                }
                wrapper.find('#email-preview-frame').html(previewHtml);

            } else {
                // Switch to Edit
                wrapper.find('#mode-status-text').text('Editor HTML');
                $('#tab-content-edit').show();
                $('#tab-content-preview').hide();
            }
        });

        // Helper: Show Message Modal
        function showModalMessage(title, message, isError) {
            var modal = $('#alezux-message-modal');
            modal.find('#msg-modal-title').text(title);
            modal.find('#msg-modal-content').html(message);

            var icon = modal.find('#msg-modal-icon');
            var btn = modal.find('#msg-modal-btn');

            // Customize based on type
            icon.removeClass('fa-info-circle fa-check-circle fa-times-circle fa-exclamation-triangle');

            if (isError) {
                icon.addClass('fa-times-circle').css('color', '#d63638');
                btn.css('background-color', '#d63638');
            } else {
                icon.addClass('fa-check-circle').css('color', '#2271b1');
                btn.css('background-color', '#2271b1');
            }

            modal.css('display', 'flex');
        }

        // Close Message Modal
        wrapper.find('#msg-modal-btn').on('click', function () {
            $('#alezux-message-modal').hide();
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

                        // Update Logo Preview
                        updateLogoPreview(s.logo_url);

                        modalSettings.css('display', 'flex');
                    }
                },
                error: function () {
                    // Fallback for editor usage if ajax fails
                    modalSettings.css('display', 'flex');
                }
            });
        });

        // --- MEDIA UPLOADER LOGIC ---

        function updateLogoPreview(url) {
            var previewArea = wrapper.find('#logo-preview-area');
            var uploadPlace = wrapper.find('#logo-upload-placeholder');
            var input = wrapper.find('#set-logo-url');
            var img = wrapper.find('#logo-preview-img');

            if (url) {
                input.val(url);
                img.attr('src', url);
                previewArea.show();
                uploadPlace.hide();
            } else {
                input.val('');
                img.attr('src', '');
                previewArea.hide();
                uploadPlace.show();
            }
        }

        // Click on Upload Box or Button -> Trigger Hidden Input
        wrapper.find('#logo-upload-trigger').on('click', function (e) {
            // STOP RECURSION: If the event came from the file input itself (bubbling), ignore it.
            if ($(e.target).is('#logo-file-input')) {
                return;
            }

            // Prevent if clicking remove link
            if ($(e.target).is('#remove-logo')) return;

            e.preventDefault();
            // Trigger native file input
            wrapper.find('#logo-file-input').trigger('click');
        });

        // Handle File Selection (Native Input)
        wrapper.find('#logo-file-input').on('change', function () {
            var fileInput = this;
            if (fileInput.files && fileInput.files[0]) {
                var file = fileInput.files[0];
                var formData = new FormData();
                formData.append('file', file);
                formData.append('action', 'alezux_marketing_upload_logo');
                formData.append('nonce', alezux_marketing_vars.nonce);

                // Show loading state
                var btn = wrapper.find('.alezux-upload-btn-styled');
                var originalText = btn.text();
                btn.text('Subiendo...').prop('disabled', true);

                $.ajax({
                    url: alezux_marketing_vars.ajax_url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        btn.text(originalText).prop('disabled', false);
                        if (res.success) {
                            updateLogoPreview(res.data.url);
                        } else {
                            // Show error in modal
                            showModalMessage('Error', 'Error al subir la imagen: ' + (res.data || 'Desconocido'), true);
                        }
                        // Clear input to allow re-uploading same file if needed
                        $(fileInput).val('');
                    },
                    error: function (err) {
                        btn.text(originalText).prop('disabled', false);
                        console.error('Upload error', err);
                        showModalMessage('Error', 'Error de conexión al subir la imagen.', true);
                    }
                });
            }
        });

        // Remove Image
        wrapper.find('#remove-logo').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation(); // prevent bubbling to upload trigger
            updateLogoPreview('');
        });


        // 4. Save Template
        wrapper.find('#marketing-template-form').on('submit', function (e) {
            e.preventDefault();
            var formData = $(this).serialize();
            formData += '&action=alezux_marketing_save_template&nonce=' + alezux_marketing_vars.nonce;

            $.post(alezux_marketing_vars.ajax_url, formData, function (res) {
                if (res.success) {
                    showModalMessage('Éxito', res.data.message, false);
                    modalTemplate.hide();
                    loadTemplates();
                } else {
                    showModalMessage('Error', 'No se pudo guardar: ' + res.data, true);
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
                    showModalMessage('Guardado', res.data.message, false);
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
