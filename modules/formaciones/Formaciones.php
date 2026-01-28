<?php
namespace Alezux_Members\Modules\Formaciones;

use Alezux_Members\Core\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Formaciones
 * Módulo para gestionar formaciones.
 */
class Formaciones extends Module_Base {

	public function init() {
		// Cargar clases internas
		require_once __DIR__ . '/includes/Course_Meta_Fields.php';
		// Instanciar Meta Fields para que corran los hooks del backend
		new \Alezux_Members\Modules\Formaciones\Includes\Course_Meta_Fields();

		// Encolar assets de administración
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );

		// Encolar assets del frontend
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_assets' ] );
		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'enqueue_frontend_assets' ] );

		// Registrar Widget de Elementor
		add_action( 'elementor/widgets/register', [ $this, 'register_elementor_widgets' ] );
	}

	public function enqueue_frontend_assets() {
		wp_enqueue_style(
			'alezux-formaciones-front',
			$this->get_asset_url( 'assets/css/formaciones.css' ),
			[],
			'1.0.3' // Version bumped to force cache clear
		);
	}

	public function register_elementor_widgets( $widgets_manager ) {
		require_once __DIR__ . '/includes/Elementor_Widget_Formaciones_Grid.php';
		
		$widgets_manager->register( new \Alezux_Members\Modules\Formaciones\Includes\Elementor_Widget_Formaciones_Grid() );
	}

	public function enqueue_admin_assets( $hook ) {
		global $post_type;
		
		// Solo cargar en la edición de cursos de LearnDash
		if ( 'sfwd-courses' !== $post_type ) {
			return;
		}

		wp_enqueue_media(); // Necesario para el uploader de imágenes

		wp_enqueue_style(
			'alezux-formaciones-admin',
			$this->get_asset_url( 'assets/css/admin-formaciones.css' ),
			[],
			'1.0.0'
		);

		wp_enqueue_script(
			'alezux-formaciones-admin',
			$this->get_asset_url( 'assets/js/admin-formaciones.js' ),
			[ 'jquery' ],
			'1.0.0',
			true
		);
	}
}
