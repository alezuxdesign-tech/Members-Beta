<?php
namespace Alezux_Members\Modules\Proyectos_Agencia\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Alezux_Members\Modules\Proyectos_Agencia\Includes\Project_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Projects_List_Widget extends Widget_Base {

	public function get_name() {
		return 'alezux_projects_list';
	}

	public function get_title() {
		return esc_html__( 'Lista de Proyectos (Admin)', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-table';
	}

	public function get_categories() {
		return [ 'alezux-otros' ]; // O una categoría específica si se crea
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Configuración', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'detail_page_url',
			[
				'label' => esc_html__( 'URL Página Detalle', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => 'https://tudominio.com/detalle-proyecto',
				'description' => 'URL de la página donde está el widget de detalle. Se le añadirá ?project_id=123',
			]
		);

		$this->end_controls_section();

		// --- STYLE: CONTAINER ---
		$this->start_controls_section(
			'style_container_section',
			[
				'label' => esc_html__( 'Contenedor Principal', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'container_bg_color',
			[
				'label' => esc_html__( 'Color de Fondo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-projects-app' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'container_border',
				'selector' => '{{WRAPPER}} .alezux-projects-app',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'container_shadow',
				'selector' => '{{WRAPPER}} .alezux-projects-app',
			]
		);

		$this->add_responsive_control(
			'container_padding',
			[
				'label' => esc_html__( 'Relleno (Padding)', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-projects-app' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'container_radius',
			[
				'label' => esc_html__( 'Radio del Borde', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-projects-app' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// --- STYLE: HEADER ---
		$this->start_controls_section(
			'style_header_section',
			[
				'label' => esc_html__( 'Cabecera (Header)', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Color Título', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-table-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .alezux-table-title',
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label' => esc_html__( 'Color Descripción', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-table-desc' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'desc_typography',
				'selector' => '{{WRAPPER}} .alezux-table-desc',
			]
		);

		$this->end_controls_section();

		// --- STYLE: CARDS ---
		$this->start_controls_section(
			'style_cards_section',
			[
				'label' => esc_html__( 'Tarjetas de Proyecto', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'card_bg_color',
			[
				'label' => esc_html__( 'Fondo Tarjeta', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-project-card' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'card_border',
				'selector' => '{{WRAPPER}} .alezux-project-card',
			]
		);

		$this->add_control(
			'card_title_color',
			[
				'label' => esc_html__( 'Color Nombre Proyecto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .project-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// --- STYLE: CARD INTERNALS ---
		$this->start_controls_section(
			'style_card_internals',
			[
				'label' => esc_html__( 'Elementos de la Tarjeta', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_status',
			[
				'label' => esc_html__( 'Etiqueta de Estado', 'alezux-members' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'status_typography',
				'selector' => '{{WRAPPER}} .alezux-status-badge',
			]
		);

		$this->add_control(
			'status_border_radius',
			[
				'label' => esc_html__( 'Radio Borde Estado', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-status-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'heading_client',
			[
				'label' => esc_html__( 'Información del Cliente', 'alezux-members' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'client_name_color',
			[
				'label' => esc_html__( 'Color Nombre Cliente', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .client-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'client_name_typography',
				'selector' => '{{WRAPPER}} .client-name',
			]
		);

		$this->add_control(
			'client_role_color',
			[
				'label' => esc_html__( 'Color Rol Cliente', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .client-role' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'heading_progress',
			[
				'label' => esc_html__( 'Barra de Progreso', 'alezux-members' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'progress_bg_color',
			[
				'label' => esc_html__( 'Fondo Barra', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .progress-bar' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'progress_fill_color',
			[
				'label' => esc_html__( 'Color Relleno', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .progress-fill' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'progress_height',
			[
				'label' => esc_html__( 'Altura Barra', 'alezux-members' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 2,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .progress-bar' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// --- STYLE: SIDE PANEL (OFF-CANVAS) ---
		$this->start_controls_section(
			'style_side_panel',
			[
				'label' => esc_html__( 'Panel Lateral', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'panel_bg_color',
			[
				'label' => esc_html__( 'Fondo Panel', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-offcanvas-panel' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'panel_width',
			[
				'label' => esc_html__( 'Ancho Panel', 'alezux-members' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 300,
						'max' => 1000,
					],
					'%' => [
						'min' => 20,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .alezux-offcanvas-panel' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'panel_header_bg',
			[
				'label' => esc_html__( 'Fondo Cabecera Panel', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .offcanvas-header' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'panel_title_color',
			[
				'label' => esc_html__( 'Color Título Panel', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .offcanvas-header h3' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'panel_title_typography',
				'selector' => '{{WRAPPER}} .offcanvas-header h3',
			]
		);

		$this->add_control(
			'panel_text_color',
			[
				'label' => esc_html__( 'Color Texto General Panel', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .alezux-offcanvas-panel' => 'color: {{VALUE}};',
					'{{WRAPPER}} .alezux-offcanvas-panel p' => 'color: {{VALUE}};',
					'{{WRAPPER}} .alezux-offcanvas-panel label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// --- STYLE: BUTTONS ---
		$this->start_controls_section(
			'style_buttons',
			[
				'label' => esc_html__( 'Botones', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .alezux-marketing-btn',
			]
		);

		$this->start_controls_tabs( 'tabs_buttons' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__( 'Normal', 'alezux-members' ),
			]
		);

		$this->add_control(
			'button_bg_color',
			[
				'label' => esc_html__( 'Fondo Botón', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-marketing-btn.primary' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => esc_html__( 'Texto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-marketing-btn' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__( 'Al Pasar Cursor', 'alezux-members' ),
			]
		);

		$this->add_control(
			'button_bg_color_hover',
			[
				'label' => esc_html__( 'Fondo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-marketing-btn.primary:hover' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_text_color_hover',
			[
				'label' => esc_html__( 'Texto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-marketing-btn:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'button_border_radius',
			[
				'label' => esc_html__( 'Radio del Borde', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-marketing-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			echo '<div class="alezux-alert error">Acceso restringido a administradores.</div>';
			return;
		}

		$settings = $this->get_settings_for_display();
		$detail_url = ! empty( $settings['detail_page_url'] ) ? $settings['detail_page_url'] : '#';

		$manager = new Project_Manager();
		$projects = $manager->get_all_projects();
		
		// Obtener usuarios para el select del modal
		$users = get_users( [ 'role__in' => [ 'subscriber', 'customer', 'administrator' ], 'number' => 100 ] );
		?>
		
		<!-- Usamos las clases globales de Finanzas/App para consistencia visual -->
		<div class="alezux-finanzas-app alezux-projects-app">
			
			<!-- Cabecera Estándar -->
			<div class="alezux-table-header">
				<div class="alezux-header-left">
					<h3 class="alezux-table-title">Gestión de Proyectos</h3>
					<p class="alezux-table-desc">Administra los proyectos de desarrollo web de tus clientes.</p>
				</div>

				<div class="alezux-header-right alezux-filters-inline">
					<div class="alezux-filter-item">
						<button id="open-new-project-modal" class="alezux-marketing-btn primary">
							<i class="eicon-plus"></i> Nuevo Proyecto
						</button>
					</div>
				</div>
			</div>

			<!-- Grid Container -->
			<div class="alezux-projects-grid">
				<?php if ( empty( $projects ) ) : ?>
					<div class="alezux-empty-state">
						<i class="eicon-folder-o"></i>
						<h3>No hay proyectos aún</h3>
						<p>Comienza creando el primero para gestionar tus desarrollos.</p>
					</div>
				<?php else : ?>
					<?php foreach ( $projects as $project ) : ?>
						<?php 
						$user_info = get_userdata( $project->customer_id );
						$user_name = $user_info ? $user_info->display_name : 'Usuario Desconocido';
						$user_email = $user_info ? $user_info->user_email : '';
						
						// Calcular progreso basado en la fase (Simplificado)
						$progress = 0;
						switch($project->current_step) {
							case 'briefing': $progress = 10; break;
							case 'design_review': $progress = 40; break;
							case 'in_progress': $progress = 70; break;
							case 'completed': $progress = 100; break;
						}
						?>
						
						<div class="alezux-project-card" onclick="AlezuxProjects.openPanel(<?php echo esc_attr($project->id); ?>)">
							<div class="card-header">
								<span class="alezux-status-badge status-<?php echo esc_attr( $project->status ); ?>">
									<?php echo esc_html( ucfirst( $project->status ) ); ?>
								</span>
								<button class="card-action-btn" title="Opciones"><i class="eicon-ellipsis-h"></i></button>
							</div>
							
							<div class="card-body">
								<h4 class="project-name"><?php echo esc_html( $project->name ); ?></h4>
								
								<div class="client-info">
									<?php echo get_avatar( $project->customer_id, 32, '', '', ['class' => 'client-avatar'] ); ?>
									<div class="client-details">
										<span class="client-name"><?php echo esc_html( $user_name ); ?></span>
										<span class="client-role">Cliente</span>
									</div>
								</div>
							</div>

							<div class="card-footer">
								<div class="progress-section">
									<div class="progress-labels">
										<span>Progreso</span>
										<span><?php echo $progress; ?>%</span>
									</div>
									<div class="progress-bar">
										<div class="progress-fill" style="width: <?php echo $progress; ?>%;"></div>
									</div>
								</div>
								
								<div class="card-dates">
									<i class="eicon-calendar"></i> <?php echo date_i18n( 'M j, Y', strtotime( $project->created_at ) ); ?>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>

			<!-- MODAL NUEVO PROYECTO (Estilo Marketing) -->
			<div id="new-project-modal" class="alezux-modal">
				<div class="alezux-modal-content" style="max-width: 500px;">
					<span class="alezux-close-modal close-modal">&times;</span>
					<h3 style="margin-top:0; margin-bottom: 20px; color: #2d3748;">Crear Nuevo Proyecto</h3>
					
					<form id="create-project-form">
						<div class="form-group">
							<label>Nombre del Proyecto</label>
							<input type="text" name="project_name" class="alezux-input" required placeholder="Ej: E-commerce de Zapatos">
						</div>
						
						<div class="form-group">
							<label>Cliente Asignado</label>
							<select name="customer_id" required class="alezux-input">
								<option value="">Seleccionar Cliente...</option>
								<?php foreach ( $users as $user ) : ?>
									<option value="<?php echo esc_attr( $user->ID ); ?>">
										<?php echo esc_html( $user->display_name . ' (' . $user->user_email . ')' ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>

						<div class="form-actions" style="margin-top: 25px; text-align: right; display: flex; justify-content: flex-end; gap: 10px;">
							<button type="button" class="alezux-marketing-btn close-modal-btn" style="background: #e2e8f0; color: #4a5568; box-shadow: none;">Cancelar</button>
							<button type="submit" class="alezux-marketing-btn primary">
								<i class="eicon-plus"></i> Crear Proyecto
							</button>
						</div>
					</form>
				</div>
			</div>



			<!-- PANEL LATERAL (Off-Canvas) -->
			<div id="project-offcanvas-overlay" class="alezux-offcanvas-overlay"></div>
			<div id="project-offcanvas" class="alezux-offcanvas-panel">
				<div class="offcanvas-header">
					<h3 id="offcanvas-title">Cargando...</h3>
					<button class="close-offcanvas-btn"><i class="eicon-close"></i></button>
				</div>
				
				<div id="offcanvas-loading" style="text-align: center; padding: 50px; color: #a0aec0;">
					<i class="eicon-loading eicon-animation-spin" style="font-size: 30px;"></i>
					<p style="margin-top: 10px;">Cargando proyecto...</p>
				</div>

				<div id="offcanvas-content" style="display: none;">
					<!-- Contenido cargado vía AJAX -->
				</div>
			</div>

		</div>
		<?php
	}
}
