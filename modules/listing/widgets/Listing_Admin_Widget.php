<?php
namespace Alezux_Members\Modules\Listing\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Listing_Admin_Widget extends Widget_Base {

	public function get_name() {
		return 'alezux_listing_admin';
	}

	public function get_title() {
		return esc_html__( 'Listing Admin', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-bullet-list';
	}

	public function get_categories() {
		return [ 'alezux-admin' ]; // Usar la categoría común para admins
	}

	public function get_style_depends() {
		return [ 'alezux-listing-admin-css' ];
	}

	public function get_script_depends() {
		return [ 'alezux-listing-admin-js' ];
	}

	protected function register_controls() {
		// Content Tab
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Contenido', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'header_title',
			[
				'label' => esc_html__( 'Título del Gestor', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Gestor de Tareas', 'alezux-members' ),
			]
		);

		$this->add_control(
			'header_description',
			[
				'label' => esc_html__( 'Descripción', 'alezux-members' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Crea y administra las tareas requeridas para los estudiantes.', 'alezux-members' ),
			]
		);

		$this->add_control(
			'edit_icon',
			[
				'label' => esc_html__( 'Ícono del Botón: Editar', 'alezux-members' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-pen',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'delete_icon',
			[
				'label' => esc_html__( 'Ícono del Botón: Eliminar', 'alezux-members' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-trash',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'edit_close_icon',
			[
				'label' => esc_html__( 'Ícono de Cerrar Modal', 'alezux-members' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-times',
					'library' => 'fa-solid',
				],
			]
		);

		$this->end_controls_section();

		// Style Tab
		$this->start_controls_section(
			'style_section',
			[
				'label' => esc_html__( 'Estilos Generales', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'bg_color',
			[
				'label' => esc_html__( 'Color de Fondo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-listing-admin' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => esc_html__( 'Color de Texto Principal', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-listing-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .alezux-task-item-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'accent_color',
			[
				'label' => esc_html__( 'Color de Acento', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-btn-primary' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// Tipografía Section
		$this->start_controls_section(
			'typography_section',
			[
				'label' => esc_html__( 'Tipografías', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => esc_html__( 'Título Principal', 'alezux-members' ),
				'selector' => '{{WRAPPER}} .alezux-listing-title',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'desc_typography',
				'label' => esc_html__( 'Descripción Principal', 'alezux-members' ),
				'selector' => '{{WRAPPER}} .alezux-listing-desc',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'task_title_typography',
				'label' => esc_html__( 'Títulos de Tareas', 'alezux-members' ),
				'selector' => '{{WRAPPER}} .task-title, {{WRAPPER}} .alezux-tasks-subtitle',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'task_desc_typography',
				'label' => esc_html__( 'Descripción de Tareas', 'alezux-members' ),
				'selector' => '{{WRAPPER}} .task-desc',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'meta_typography',
				'label' => esc_html__( 'Textos Meta (Fecha)', 'alezux-members' ),
				'selector' => '{{WRAPPER}} .task-meta',
			]
		);

		$this->end_controls_section();

		// Íconos Section
		$this->start_controls_section(
			'icons_section',
			[
				'label' => esc_html__( 'Íconos', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => esc_html__( 'Color de Íconos', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-btn-icon svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .alezux-btn-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .alezux-listing-modal-close' => 'color: {{VALUE}};',
					'{{WRAPPER}} .alezux-listing-modal-close svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_color_hover',
			[
				'label' => esc_html__( 'Color de Íconos Hover', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-btn-icon:hover svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .alezux-btn-icon:hover i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .alezux-listing-modal-close:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .alezux-listing-modal-close:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => esc_html__( 'Tamaño', 'alezux-members' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 60,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .alezux-btn-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .alezux-btn-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .alezux-listing-modal-close i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .alezux-listing-modal-close svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_box_size',
			[
				'label' => esc_html__( 'Caja Atrás (Fondo)', 'alezux-members' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .alezux-btn-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Botones Section
		$this->start_controls_section(
			'buttons_style_section',
			[
				'label' => esc_html__( 'Botones', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_control(
			'button_text_color',
			[
				'label' => esc_html__( 'Color de Texto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-btn' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-btn' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'label' => esc_html__( 'Tipografía', 'alezux-members' ),
				'selector' => '{{WRAPPER}} .alezux-btn',
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label' => esc_html__( 'Padding', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( ! current_user_can( 'administrator' ) ) {
			echo '<div class="alezux-error-notice">Solo administradores pueden ver este gestor de tareas.</div>';
			return;
		}

		$edit_icon_html = '<i class="fas fa-pen"></i>';
		if ( ! empty( $settings['edit_icon']['value'] ) ) {
			ob_start();
			\Elementor\Icons_Manager::render_icon( $settings['edit_icon'], [ 'aria-hidden' => 'true' ] );
			$edit_icon_html = ob_get_clean();
		}

		$delete_icon_html = '<i class="fas fa-trash"></i>';
		if ( ! empty( $settings['delete_icon']['value'] ) ) {
			ob_start();
			\Elementor\Icons_Manager::render_icon( $settings['delete_icon'], [ 'aria-hidden' => 'true' ] );
			$delete_icon_html = ob_get_clean();
		}

		$edit_close_icon_html = '<i class="fas fa-times"></i>';
		if ( ! empty( $settings['edit_close_icon']['value'] ) ) {
			ob_start();
			\Elementor\Icons_Manager::render_icon( $settings['edit_close_icon'], [ 'aria-hidden' => 'true' ] );
			$edit_close_icon_html = ob_get_clean();
		}

		?>
		<div class="alezux-listing-admin" data-icon-edit="<?php echo esc_attr( $edit_icon_html ); ?>" data-icon-delete="<?php echo esc_attr( $delete_icon_html ); ?>">
			<div class="alezux-listing-header">
				<h2 class="alezux-listing-title"><?php echo esc_html( $settings['header_title'] ); ?></h2>
				<p class="alezux-listing-desc"><?php echo esc_html( $settings['header_description'] ); ?></p>
			</div>

			<div class="alezux-listing-form-wrapper">
				<form id="alezux-add-task-form" class="alezux-form">
					<div class="alezux-form-group">
						<label for="task_title">Nombre de la Tarea</label>
						<input type="text" id="task_title" name="task_title" class="alezux-input" placeholder="Ej. Completar evaluación inicial" required>
					</div>
					<div class="alezux-form-group">
						<label for="task_description">Descripción (Opcional)</label>
						<textarea id="task_description" name="task_description" class="alezux-input" rows="3" placeholder="Detalles extra sobre la tarea..."></textarea>
					</div>
					<button type="submit" class="alezux-btn alezux-btn-primary" id="alezux-submit-task-btn">
						<span class="btn-text">Crear Tarea</span>
						<i class="fas fa-spinner fa-spin btn-icon" style="display: none;"></i>
					</button>
					<div id="alezux-task-form-msg" class="alezux-form-msg"></div>
				</form>
			</div>

			<div class="alezux-listing-tasks-wrapper">
				<h3 class="alezux-tasks-subtitle">Tareas Creadas</h3>
				<div id="alezux-admin-tasks-list" class="alezux-tasks-list">
					<div class="alezux-loading-tasks">
						<i class="fas fa-circle-notch fa-spin"></i> Cargando tareas...
					</div>
				</div>
			</div>

			<!-- Modal Edit Task -->
			<div class="alezux-listing-modal-overlay alezux-edit-task-modal" style="display: none;">
				<div class="alezux-listing-modal-content">
					<div class="alezux-listing-modal-header">
						<h3>Editar Tarea</h3>
						<span class="alezux-listing-modal-close"><?php echo $edit_close_icon_html; ?></span>
					</div>
					<div class="alezux-listing-modal-body">
						<form class="alezux-form alezux-edit-task-form">
							<input type="hidden" class="edit_task_id" name="edit_task_id">
							<div class="alezux-form-group">
								<label>Nombre de la Tarea</label>
								<input type="text" name="edit_task_title" class="alezux-input edit_task_title" required>
							</div>
							<div class="alezux-form-group">
								<label>Descripción</label>
								<textarea name="edit_task_description" class="alezux-input edit_task_description" rows="3"></textarea>
							</div>
							<button type="submit" class="alezux-btn alezux-btn-primary alezux-submit-edit-task-btn">
								<span class="btn-text">Guardar Cambios</span>
								<i class="fas fa-spinner fa-spin btn-icon" style="display: none;"></i>
							</button>
						</form>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
