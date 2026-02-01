<?php
namespace Alezux_Members\Modules\Finanzas;

use Alezux_Members\Core\Module_Base;

if ( ! \defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Finanzas extends Module_Base {

	public function init() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	private function define_constants() {
		if ( ! \defined( 'ALEZUX_FINANZAS_PATH' ) ) {
			\define( 'ALEZUX_FINANZAS_PATH', \plugin_dir_path( __FILE__ ) );
		}
		if ( ! \defined( 'ALEZUX_FINANZAS_URL' ) ) {
			\define( 'ALEZUX_FINANZAS_URL', \plugin_dir_url( __FILE__ ) );
		}
	}

	private function includes() {
		require_once ALEZUX_FINANZAS_PATH . 'includes/Database_Installer.php';
		require_once ALEZUX_FINANZAS_PATH . 'includes/Ajax_Handler.php';
		require_once ALEZUX_FINANZAS_PATH . 'includes/Stripe_API.php';
	}

	private function init_hooks() {
		// Inicializar manejadores AJAX
		\Alezux_Members\Modules\Finanzas\Includes\Ajax_Handler::init();

		// Instalación de Tablas al activar (o usar otro hook si es carga dinámica)
		\add_action( 'admin_init', array( __NAMESPACE__ . '\\Includes\\Database_Installer', 'check_updates' ) );
		
		\add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
	}

	public function register_widgets( $widgets_manager ) {
		require_once( ALEZUX_FINANZAS_PATH . 'widgets/Create_Plan_Widget.php' );
		$widgets_manager->register( new Widgets\Create_Plan_Widget() );
	}
}

