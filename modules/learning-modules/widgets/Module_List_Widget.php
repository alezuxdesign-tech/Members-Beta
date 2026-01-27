<?php
namespace Alezux_Members\Modules\Learning_Modules\Widgets;

use Alezux_Members\Core\Elementor_Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Module_List_Widget extends Elementor_Widget_Base {

	public function get_name() {
		return 'alezux_module_list';
	}

	public function get_title() {
		return esc_html__( 'Lista de Módulos (LMS)', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-post-list';
	}

	protected function register_widget_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Configuración', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label' => esc_html__( 'Módulos a mostrar', 'alezux-members' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 6,
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$args = [
			'post_type' => 'alz_module',
			'posts_per_page' => $settings['posts_per_page'],
			'post_status' => 'publish',
		];

		$query = new \WP_Query( $args );

		echo '<div class="alezux-modules-grid">';
		
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				// Incluir la vista de la tarjeta del módulo
				include dirname( __DIR__ ) . '/views/card-module.php';
			}
			wp_reset_postdata();
		} else {
			echo '<p class="alezux-text">' . esc_html__( 'No hay módulos disponibles aún.', 'alezux-members' ) . '</p>';
		}

		echo '</div>';
	}
}
