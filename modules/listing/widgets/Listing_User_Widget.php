<?php
namespace Alezux_Members\Modules\Listing\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Listing_User_Widget extends Widget_Base {

	public function get_name() {
		return 'alezux_listing_user';
	}

	public function get_title() {
		return esc_html__( 'Listing Tareas (Estudiante)', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-checkbox';
	}

	public function get_categories() {
		return [ 'alezux-estudiantes' ]; // Categoría para elementos del estudiante
	}

	public function get_style_depends() {
		return [ 'alezux-listing-user-css' ];
	}

	public function get_script_depends() {
		return [ 'alezux-listing-user-js' ];
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
				'label' => esc_html__( 'Título', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Tus Tareas Pendientes', 'alezux-members' ),
			]
		);

		$this->add_control(
			'header_description',
			[
				'label' => esc_html__( 'Descripción', 'alezux-members' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Completa los siguientes pasos asignados por el administrador para avanzar en tu progreso.', 'alezux-members' ),
			]
		);

		$this->add_control(
			'empty_message',
			[
				'label' => esc_html__( 'Mensaje sin Tareas', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( '¡Felicidades! Has completado todas tus tareas.', 'alezux-members' ),
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
					'{{WRAPPER}} .alezux-listing-user' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'item_bg_color',
			[
				'label' => esc_html__( 'Fondo de Tarjeta (Tarea)', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-user-task-item' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => esc_html__( 'Color de Títulos', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-listing-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .user-task-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_color',
			[
				'label' => esc_html__( 'Color del Botón', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-btn-complete' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( ! is_user_logged_in() && ! \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			echo '<div class="alezux-notice">Inicia sesión para ver tus tareas.</div>';
			return;
		}

		?>
		<div class="alezux-listing-user" data-empty-msg="<?php echo esc_attr( $settings['empty_message'] ); ?>">
			<div class="alezux-listing-header">
				<h2 class="alezux-listing-title"><?php echo esc_html( $settings['header_title'] ); ?></h2>
				<p class="alezux-listing-desc"><?php echo esc_html( $settings['header_description'] ); ?></p>
			</div>

			<div class="alezux-user-tasks-wrapper">
				<div id="alezux-user-tasks-list" class="alezux-user-tasks-list">
					<div class="alezux-loading-tasks">
						<i class="fas fa-circle-notch fa-spin"></i> Buscando tus tareas...
					</div>
				</div>
				<div id="alezux-user-msg" class="alezux-user-msg" style="display: none;"></div>
			</div>
		</div>
		<?php
	}
}
