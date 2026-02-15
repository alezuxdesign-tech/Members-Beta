<?php
namespace Alezux_Members\Modules\Marketing\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Marketing_Config_Widget extends Widget_Base {

	public function get_name() {
		return 'alezux_marketing_config';
	}

	public function get_title() {
		return __( 'Alezux Marketing Manager', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-envelope';
	}

	public function get_categories() {
		return [ 'alezux-admin' ];
	}

	public function get_script_depends() {
		return [ 'alezux-marketing-admin-js' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Modo Editor (Preview)', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_editor_modal_template',
			[
				'label' => __( 'Mostrar Modal Edición', 'alezux-members' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Sí', 'alezux-members' ),
				'label_off' => __( 'No', 'alezux-members' ),
				'return_value' => 'yes',
				'default' => '',
				'description' => 'Activa esto para ver y diseñar la ventana modal de edición de plantilla.',
			]
		);

		$this->add_control(
			'show_editor_modal_settings',
			[
				'label' => __( 'Mostrar Modal Ajustes', 'alezux-members' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Sí', 'alezux-members' ),
				'label_off' => __( 'No', 'alezux-members' ),
				'return_value' => 'yes',
				'default' => '',
				'description' => 'Activa esto para ver y diseñar la ventana modal de configuración.',
			]
		);

		$this->end_controls_section();

		// 1. DISEÑO DE LA TABLA (Tabla y Encabezados)
        $this->start_controls_section(
            'style_section_table',
            [
                'label' => esc_html__('Diseño de la Tabla', 'alezux-members'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'table_container_heading',
            [
                'label' => esc_html__('Contenedor & Cuerpo', 'alezux-members'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'table_background',
                'label' => esc_html__('Fondo Tabla', 'alezux-members'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .alezux-marketing-wrapper',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'table_border',
                'label' => esc_html__('Borde Tabla', 'alezux-members'),
                'selector' => '{{WRAPPER}} .alezux-marketing-wrapper',
            ]
        );

        $this->add_control(
            'table_radius',
            [
                'label' => esc_html__('Radio de Borde', 'alezux-members'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .alezux-marketing-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->add_control(
            'table_row_bg_general',
            [
                'label' => esc_html__('Fondo Filas (General)', 'alezux-members'),
                'type' => Controls_Manager::COLOR,
                 'selectors' => [
                    '{{WRAPPER}} .alezux-finanzas-table tbody tr' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .alezux-finanzas-table tbody td' => 'background-color: {{VALUE}};',
                ],
            ]
        );

         $this->add_control(
            'table_row_bg',
            [
                'label' => esc_html__('Fondo Filas (Alterno)', 'alezux-members'),
                'type' => Controls_Manager::COLOR,
                 'selectors' => [
                    '{{WRAPPER}} .alezux-finanzas-table tbody tr:nth-child(even)' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'table_header_heading',
            [
                'label' => esc_html__('Encabezados (Títulos)', 'alezux-members'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'header_bg_color',
            [
                'label' => esc_html__('Color Fondo Encabezado', 'alezux-members'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .alezux-finanzas-table th' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'header_text_color',
            [
                'label' => esc_html__('Color Texto Encabezado', 'alezux-members'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .alezux-finanzas-table th' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'header_typography',
                'selector' => '{{WRAPPER}} .alezux-finanzas-table th',
            ]
        );

        $this->end_controls_section();


        // 2. Celdas (Texto y Tipografía General)
        $this->start_controls_section(
            'style_section_cells',
            [
                'label' => esc_html__('Celdas (TD)', 'alezux-members'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

         $this->add_control(
            'cell_text_color',
            [
                'label' => esc_html__('Color Texto Celdas', 'alezux-members'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .alezux-finanzas-table td' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'cell_typography',
                'selector' => '{{WRAPPER}} .alezux-finanzas-table td',
            ]
        );

        $this->end_controls_section();


        // 3. ESTADO (Badges)
        $this->start_controls_section(
            'style_section_badges',
            [
                'label' => esc_html__('Estado (Badges)', 'alezux-members'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'badge_typography',
                'selector' => '{{WRAPPER}} .status-badge',
            ]
        );

        $this->add_control(
            'badge_radius',
             [
                'label' => esc_html__('Redondeo', 'alezux-members'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .status-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'badge_padding',
             [
                'label' => esc_html__('Relleno', 'alezux-members'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .status-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_control('badge_active_text', ['label' => 'Texto (Activo)', 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .status-active' => 'color: {{VALUE}};']]);
        $this->add_control('badge_active_bg', ['label' => 'Fondo (Activo)', 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .status-active' => 'background-color: {{VALUE}};']]);

        $this->add_control('badge_inactive_text', ['label' => 'Texto (Inactivo)', 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .status-inactive' => 'color: {{VALUE}};']]);
        $this->add_control('badge_inactive_bg', ['label' => 'Fondo (Inactivo)', 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .status-inactive' => 'background-color: {{VALUE}};']]);

        $this->end_controls_section();

        // 4. MODALES
        $this->start_controls_section(
            'style_section_modals',
            [
                'label' => esc_html__('Ventanas Emergentes (Modales)', 'alezux-members'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'modal_overlay_bg',
            [
                'label' => esc_html__('Color Fondo Overlay', 'alezux-members'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .alezux-modal' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'modal_content_bg',
            [
                'label' => esc_html__('Color Fondo Contenido', 'alezux-members'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .alezux-modal-content' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'modal_border',
                'selector' => '{{WRAPPER}} .alezux-modal-content',
            ]
        );

        $this->add_control(
            'modal_radius',
            [
                'label' => esc_html__('Redondeo', 'alezux-members'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .alezux-modal-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'modal_shadow',
                'selector' => '{{WRAPPER}} .alezux-modal-content',
            ]
        );

        $this->add_control(
            'modal_title_color',
            [
                'label' => esc_html__('Color Título', 'alezux-members'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .alezux-modal-content h3' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'modal_title_typography',
                'selector' => '{{WRAPPER}} .alezux-modal-content h3',
            ]
        );

         $this->add_control(
            'modal_label_color',
            [
                'label' => esc_html__('Color Etiquetas (Labels)', 'alezux-members'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .alezux-modal-content label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // 5. BOTONES (Marketing)
        $this->start_controls_section(
            'style_section_buttons',
            [
                'label' => esc_html__('Botones', 'alezux-members'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_buttons_style');

        $this->start_controls_tab(
            'tab_button_normal',
            [
                'label' => esc_html__('Normal', 'alezux-members'),
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .alezux-marketing-btn',
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => esc_html__('Color Texto', 'alezux-members'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .alezux-marketing-btn' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'button_background',
                'label' => esc_html__('Fondo', 'alezux-members'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .alezux-marketing-btn',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'button_border',
                'selector' => '{{WRAPPER}} .alezux-marketing-btn',
            ]
        );

        $this->add_control(
            'button_radius',
            [
                'label' => esc_html__('Redondeo', 'alezux-members'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .alezux-marketing-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover',
            [
                'label' => esc_html__('Hover', 'alezux-members'),
            ]
        );

        $this->add_control(
            'button_text_color_hover',
            [
                'label' => esc_html__('Color Texto', 'alezux-members'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .alezux-marketing-btn:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'button_background_hover',
                'label' => esc_html__('Fondo', 'alezux-members'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .alezux-marketing-btn:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_hover_shadow',
                'selector' => '{{WRAPPER}} .alezux-marketing-btn:hover',
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();
	}

	protected function render() {
		$is_edit = \Elementor\Plugin::instance()->editor->is_edit_mode();
		
		if ( ! current_user_can( 'administrator' ) && ! $is_edit ) {
			echo '<p>Acceso restringido.</p>';
			return;
		}

		$settings = $this->get_settings_for_display();
		$show_edit_modal = $is_edit && 'yes' === $settings['show_editor_modal_template'];
		$show_settings_modal = $is_edit && 'yes' === $settings['show_editor_modal_settings'];
		
		// Data attr to prevent JS overwrite in editor
		$wrapper_attrs = $is_edit ? ' data-is-editor="yes"' : '';
		?>
		<div class="alezux-finanzas-app alezux-marketing-app"<?php echo $wrapper_attrs; ?>>
			
			<!-- Cabecera Estándar -->
			<div class="alezux-table-header">
				<div class="alezux-header-left">
					<h3 class="alezux-table-title">Gestor de Correos</h3>
					<p class="alezux-table-desc">Administra las plantillas de email del sistema.</p>
				</div>

				<div class="alezux-header-right alezux-filters-inline">
					<div class="alezux-filter-item">
						<button id="btn-marketing-settings" class="alezux-marketing-btn primary">
							<i class="fa fa-cog"></i> Configuración General
						</button>
					</div>
				</div>
			</div>

			<!-- Tabla Container -->
			<div class="alezux-table-wrapper">
				<table class="alezux-finanzas-table marketing-templates-table" id="marketing-templates-table">
					<thead>
						<tr>
							<th style="width: 35%;">Detalles del Correo</th>
							<th style="width: 25%;">Asunto</th>
							<th style="width: 10%;">Enviados</th>
							<th style="width: 10%;">Estado</th>
							<th style="width: 20%;">Acciones</th>
						</tr>
					</thead>
					<tbody>
						<?php if ( $is_edit ) : ?>
							<!-- Dummy Data for Editor -->
							<tr>
								<td><strong>Registro - Bienvenida</strong><br><small style="color:#888">student_welcome</small></td>
								<td>¡Bienvenido a la Academia!</td>
								<td><span class="status-badge status-active">Activo</span></td>
								<td><button class="alezux-marketing-btn edit-template-btn"><i class="fa fa-pencil"></i> Editar</button></td>
							</tr>
							<tr>
								<td><strong>Finanzas - Pago Exitoso</strong><br><small style="color:#888">payment_success</small></td>
								<td>Recibo de tu pago</td>
								<td><span class="status-badge status-active">Activo</span></td>
								<td><button class="alezux-marketing-btn edit-template-btn"><i class="fa fa-pencil"></i> Editar</button></td>
							</tr>
                             <tr>
								<td><strong>Logros - Nuevo Logro</strong><br><small style="color:#888">achievement_assigned</small></td>
								<td>¡Felicidades! Has desbloqueado un logro</td>
								<td><span class="status-badge status-inactive">Inactivo</span></td>
								<td><button class="alezux-marketing-btn edit-template-btn"><i class="fa fa-pencil"></i> Editar</button></td>
							</tr>
						<?php else : ?>
							<!-- AJAX Loaded -->
							<tr><td colspan="4" style="text-align:center; padding: 20px;">Cargando plantillas...</td></tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>

			<?php if ( $is_edit ) : ?>
				<div style="margin-top:20px; padding:10px; border:1px dashed #666; color:#aaa; font-size:12px;">
					<strong>Modo Editor:</strong> Los modales no son visibles aquí por defecto. Usa los controles "Modo Editor (Preview)" para mostrarlos y diseñarlos.
				</div>
			<?php endif; ?>

			<!-- MODAL TEMPLATE EDITOR -->
			<div id="marketing-template-modal" class="alezux-modal" style="<?php echo $show_edit_modal ? 'display:flex !important;' : 'display:none;'; ?>">
				<div class="alezux-modal-content" style="max-width: 800px;">
					<span class="alezux-close-modal">&times;</span>
					<h3 id="modal-title">Editar Plantilla (Preview)</h3>
					
					<form id="marketing-template-form" onsubmit="return false;">
						<input type="hidden" id="tpl-type" name="type" value="dummy">
						
						<div class="form-group">
							<label>Asunto:</label>
							<input type="text" id="tpl-subject" name="subject" class="alezux-input" value="Asunto de Ejemplo" required>
							<div id="tpl-variables-hint" style="margin-top: 5px; font-size: 11px; color: #666; background: #f0f0f1; padding: 5px; border-radius: 4px;">
                                <strong>Variables disponibles:</strong> <span id="vars-list">Cargando...</span>
                            </div>
						</div>

						<div class="form-group">
							<label>Contenido:</label>
                            
    						<div class="editor-mode-switcher" style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <span id="mode-status-text" style="font-weight:bold; font-size:13px; color:#444;">Editor HTML</span>
                            <label class="switch">
                                <input type="checkbox" id="toggle-preview-mode">
                                <span class="slider round"></span>
                            </label>
                        </div>

                        <div id="tab-content-edit">
							    <textarea id="tpl-content" name="content" rows="15" class="alezux-input" style="font-family: monospace;">&lt;h1&gt;Hola Mundo&lt;/h1&gt;</textarea>
							    <small>Pega aquí tu código HTML. La etiqueta &lt;body&gt; es opcional.</small>
                            </div>

                            <div id="tab-content-preview" style="display:none;">
                                <div id="email-preview-frame" class="preview-container">
                                    <!-- Preview rendered here -->
                                </div>
                            </div>
						</div>

						<div class="form-group" style="display: flex; align-items: center; gap: 10px; margin-top: 15px;">
							<label>Activo:</label>
							<label class="switch">
								<input type="checkbox" id="tpl-active" name="is_active" checked>
								<span class="slider round"></span>
							</label>
						</div>

						<div class="form-actions" style="margin-top: 20px; text-align: right;">
							<button type="submit" class="alezux-marketing-btn primary">Guardar (Simulado)</button>
						</div>
					</form>
				</div>
			</div>

			<div id="marketing-settings-modal" class="alezux-modal" style="<?php echo $show_settings_modal ? 'display:flex !important;' : 'display:none;'; ?>">
				<div class="alezux-modal-content">
					<span class="alezux-close-modal">&times;</span>
					<h3>Configuración General (Preview)</h3>
					
					<form id="marketing-settings-form" onsubmit="return false;">
						<div class="form-group">
							<label>Nombre del Remitente (From Name):</label>
							<input type="text" id="set-from-name" name="from_name" class="alezux-input" value="Mi Escuela">
						</div>

						<div class="form-group">
							<label>Email del Remitente (From Email):</label>
							<input type="email" id="set-from-email" name="from_email" class="alezux-input" value="info@escuela.com">
						</div>

						<div class="form-group">
						<label>URL del Logo (Variable {{logo_url}}):</label>
                        <!-- UPLOAD BOX -->
                        <div class="alezux-upload-box" id="logo-upload-trigger">
                            <input type="hidden" id="set-logo-url" name="logo_url">
                            <!-- Hidden File Input for Native Upload -->
                            <input type="file" id="logo-file-input" accept="image/*" style="display:none;">
                            
                            <div id="logo-preview-area" style="display:none;">
                                <img src="" class="alezux-preview-image" id="logo-preview-img">
                                <span class="remove-image-link" id="remove-logo">Eliminar imagen</span>
                            </div>

                            <div id="logo-upload-placeholder">
                                <span class="alezux-upload-icon"><i class="fa fa-cloud-upload"></i></span>
                                <span class="alezux-upload-title">Subir logo</span>
                                <span class="alezux-upload-desc">JPEG, PNG, máximo 50 MB.</span>
                                <button type="button" class="alezux-upload-btn-styled">Subir imagen</button>
                            </div>
                        </div>
					</div>

					<div class="form-actions" style="margin-top: 20px; text-align: right;">
						<button type="submit" class="alezux-marketing-btn primary">Guardar Configuración</button>
					</div>
					</form>
				</div>
			</div>

            <!-- MODAL HISTORY -->
            <div id="marketing-history-modal" class="alezux-modal" style="display:none;">
                <div class="alezux-modal-content" style="max-width: 600px;">
                    <span class="alezux-close-modal">&times;</span>
                    <h3 id="history-modal-title">Historial de Envíos</h3>
                    <div class="alezux-table-wrapper" style="max-height: 400px; overflow-y: auto; margin-top: 15px;">
                        <table class="alezux-finanzas-table" id="history-table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Destinatario</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- JS Populated -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

		<!-- GENERIC MESSAGE MODAL -->
		<div id="alezux-message-modal" class="alezux-modal" style="display:none; z-index: 99999;">
			<div class="alezux-modal-content" style="max-width: 400px; text-align: center;">
				<span class="alezux-close-modal">&times;</span>
				<div style="margin-bottom: 15px;">
					<i id="msg-modal-icon" class="fa fa-info-circle" style="font-size: 40px; color: #2271b1;"></i>
				</div>
				<h3 id="msg-modal-title" style="margin-top:0;">Mensaje</h3>
				<p id="msg-modal-content" style="margin: 20px 0; font-size: 15px; color: #555; line-height: 1.5;"></p>
				<button type="button" class="alezux-marketing-btn primary" id="msg-modal-btn">Entendido</button>
			</div>
		</div>

		</div>
		<?php
	}
}
