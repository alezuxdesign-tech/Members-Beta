<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Alezux_Create_Plan_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'alezux_create_plan';
	}

	public function get_title() {
		return esc_html__( 'Creador de Planes', 'alezux' );
	}

	public function get_icon() {
		return 'eicon-price-table';
	}

	public function get_categories() {
		return [ 'general' ]; // O tu categoría personalizada
	}

	protected function register_controls() {
		
        $this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Configuración', 'alezux' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

        $this->add_control(
			'important_note',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'Este widget muestra el formulario administrativo para crear nuevos planes en Stripe.', 'alezux' ),
			]
		);

		$this->end_controls_section();

	}

	protected function render() {
		echo '<div class="alezux-create-plan-widget">';
        echo '<h3>Creador de Planes de Financiación</h3>';
        echo '<p>Aquí irá el formulario de creación de planes (Próximamente)</p>';
        echo '</div>';
	}
}
