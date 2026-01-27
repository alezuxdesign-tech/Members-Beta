<?php
namespace Alezux_Members\Modules\Slide_Lesson;

use Alezux_Members\Core\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Slide_Lesson extends Module_Base {

	public function init() {
		// Registrar Shortcode
		$this->register_shortcode( 
			'slide_lesson', 
			[ $this, 'render_shortcode' ], 
			'Muestra un slider con todas las lecciones de LearnDash (imagen destacada y enlace).' 
		);

		// Encolar estilos específicos del módulo
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
	}

	public function enqueue_assets() {
		wp_enqueue_style( 
			'alezux-slide-lesson-css', 
			$this->get_asset_url( 'assets/css/slide-lesson.css' ), 
			[], 
			ALEZUX_MEMBERS_VERSION 
		);
	}

	public function render_shortcode( $atts ) {
		$atts = shortcode_atts( [
			'limit' => -1, // Por defecto listar todas
		], $atts );

		// Consulta para obtener las lecciones de LearnDash
		$args = [
			'post_type'      => 'sfwd-lessons',
			'posts_per_page' => intval( $atts['limit'] ),
			'post_status'    => 'publish',
		];

		$query = new \WP_Query( $args );
		$lessons = [];

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$lessons[] = [
					'title'     => get_the_title(),
					'permalink' => get_permalink(),
					'image_url' => get_the_post_thumbnail_url( get_the_ID(), 'full' ),
				];
			}
			wp_reset_postdata();
		}

		ob_start();
		$this->render_view( 'slide-lesson', [ 'lessons' => $lessons ] );
		return ob_get_clean();
	}
}
