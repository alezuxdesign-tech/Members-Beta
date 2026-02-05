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
				'label' => __( 'Configuración', 'alezux-members' ),
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
	}

	protected function render() {
		// $settings = $this->get_settings_for_display(); // Si tuviera controles dinámicos de título
		if ( ! current_user_can( 'administrator' ) ) {
			echo '<p>Acceso restringido.</p>';
			return;
		}
		?>
		<div class="alezux-finanzas-app alezux-marketing-app">
			
			<!-- Cabecera Estándar (Igual a Finanzas) -->
			<div class="alezux-table-header">
				<div class="alezux-header-left">
					<h3 class="alezux-table-title">Gestor de Correos</h3>
					<p class="alezux-table-desc">Administra las plantillas de email del sistema.</p>
				</div>

				<div class="alezux-header-right alezux-filters-inline">
					<div class="alezux-filter-item">
						<button id="btn-marketing-settings" class="alezux-action-btn primary">
							<i class="fa fa-cog"></i> Configuración General
						</button>
					</div>
				</div>
			</div>

			<!-- Tabla Container -->
			<div class="alezux-table-wrapper">
				<table class="alezux-finanzas-table" id="marketing-templates-table">
					<thead>
						<tr>
							<th>Tipo de Correo</th>
							<th>Asunto Actual</th>
							<th>Estado</th>
							<th>Acciones</th>
						</tr>
					</thead>
					<tbody>
						<!-- AJAX Loaded -->
						<tr><td colspan="4" style="text-align:center; padding: 20px;">Cargando plantillas...</td></tr>
					</tbody>
				</table>
			</div>

			<!-- MODAL TEMPLATE EDITOR -->
			<div id="marketing-template-modal" class="alezux-modal" style="display:none;">
				<div class="alezux-modal-content" style="max-width: 800px;">
					<span class="alezux-close-modal">&times;</span>
					<h3 id="modal-title">Editar Plantilla</h3>
					
					<form id="marketing-template-form">
						<input type="hidden" id="tpl-type" name="type">
						
						<div class="form-group">
							<label>Asunto:</label>
							<input type="text" id="tpl-subject" name="subject" class="alezux-input" required>
							<small>Variables disponibles: {{user.name}}, {{site_name}}...</small>
						</div>

						<div class="form-group">
							<label>Contenido (HTML):</label>
							<textarea id="tpl-content" name="content" rows="15" class="alezux-input" style="font-family: monospace;"></textarea>
							<small>Pega aquí tu código HTML. La etiqueta &lt;body&gt; es opcional.</small>
						</div>

						<div class="form-group" style="display: flex; align-items: center; gap: 10px; margin-top: 15px;">
							<label>Activo:</label>
							<label class="switch">
								<input type="checkbox" id="tpl-active" name="is_active">
								<span class="slider round"></span>
							</label>
						</div>

						<div class="form-actions" style="margin-top: 20px; text-align: right;">
							<button type="submit" class="alezux-action-btn primary">Guardar Cambios</button>
						</div>
					</form>
				</div>
			</div>

			<!-- MODAL SETTINGS -->
			<div id="marketing-settings-modal" class="alezux-modal" style="display:none;">
				<div class="alezux-modal-content">
					<span class="alezux-close-modal">&times;</span>
					<h3>Configuración General</h3>
					
					<form id="marketing-settings-form">
						<div class="form-group">
							<label>Nombre del Remitente (From Name):</label>
							<input type="text" id="set-from-name" name="from_name" class="alezux-input">
						</div>

						<div class="form-group">
							<label>Email del Remitente (From Email):</label>
							<input type="email" id="set-from-email" name="from_email" class="alezux-input">
						</div>

						<div class="form-group">
							<label>URL del Logo (Variable {{logo_url}}):</label>
							<input type="url" id="set-logo-url" name="logo_url" class="alezux-input">
							<small>Sube tu logo a Medios y pega la URL aquí.</small>
						</div>

						<div class="form-actions" style="margin-top: 20px; text-align: right;">
							<button type="submit" class="alezux-action-btn primary">Guardar Configuración</button>
						</div>
					</form>
				</div>
			</div>

		</div>
		<?php
	}
}
