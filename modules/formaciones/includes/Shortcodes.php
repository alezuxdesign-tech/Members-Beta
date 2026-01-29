<?php
namespace Alezux_Members\Modules\Formaciones\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Shortcodes
 * Maneja los shortcodes del módulo de formaciones.
 */
class Shortcodes {

	public function get_definitions() {
		return [

			'alezux_course_whatsapp' => [
				'callback'    => [ $this, 'render_whatsapp_link' ],
				'description' => __( 'Muestra el enlace de WhatsApp configurado en el curso.', 'alezux-members' ),
			],
			'alezux_course_slack' => [
				'callback'    => [ $this, 'render_slack_link' ],
				'description' => __( 'Muestra el enlace de Slack configurado en el curso.', 'alezux-members' ),
			],
			'alezux_course_zoom' => [
				'callback'    => [ $this, 'render_zoom_link' ],
				'description' => __( 'Muestra el enlace de Zoom configurado en el curso.', 'alezux-members' ),
			],
		];
	}



	/**
	 * Shortcode: [alezux_course_whatsapp]
	 * Retorna el enlace de Whatsapp del curso actual.
	 */
	public function render_whatsapp_link( $atts ) {
		$course_id = learndash_get_course_id();
		if ( ! $course_id ) return '';

		$link = get_post_meta( $course_id, '_alezux_course_whatsapp', true );
		return esc_url( $link );
	}

	/**
	 * Shortcode: [alezux_course_slack]
	 * Retorna el enlace de Slack del curso actual.
	 */
	public function render_slack_link( $atts ) {
		$course_id = learndash_get_course_id();
		if ( ! $course_id ) return '';

		$link = get_post_meta( $course_id, '_alezux_course_slack', true );
		return esc_url( $link );
	}

	/**
	 * Shortcode: [alezux_course_zoom]
	 * Retorna el enlace de Zoom del curso actual.
	 */
	public function render_zoom_link( $atts ) {
		$course_id = learndash_get_course_id();
		if ( ! $course_id ) return '';

		$link = get_post_meta( $course_id, '_alezux_course_zoom', true );
		return esc_url( $link );
	}
}
