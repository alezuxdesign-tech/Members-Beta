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
		require_once ALEZUX_FINANZAS_PATH . 'includes/Admin_Settings.php';
		require_once ALEZUX_FINANZAS_PATH . 'includes/Webhook_Handler.php';
        require_once ALEZUX_FINANZAS_PATH . 'includes/Access_Control.php';
	}
	// The includes method is largely replaced by direct requires in init_hooks or removed.
	// private function includes() {
	// 	require_once ALEZUX_FINANZAS_PATH . 'includes/Database_Installer.php';
	// 	require_once ALEZUX_FINANZAS_PATH . 'includes/Ajax_Handler.php';
	// 	require_once ALEZUX_FINANZAS_PATH . 'includes/Stripe_API.php';
	// 	require_once ALEZUX_FINANZAS_PATH . 'includes/Admin_Settings.php';
	// 	require_once ALEZUX_FINANZAS_PATH . 'includes/Webhook_Handler.php';
    //     require_once ALEZUX_FINANZAS_PATH . 'includes/Access_Control.php';
	// }

	private function init_hooks() {
		// Incluir archivos necesarios
		require_once ALEZUX_FINANZAS_PATH . 'includes/Database_Installer.php'; // Moved here from includes()
		require_once ALEZUX_FINANZAS_PATH . 'includes/Ajax_Handler.php'; // Moved here from includes()
		require_once ALEZUX_FINANZAS_PATH . 'includes/Stripe_API.php'; // Moved here from includes()
		require_once ALEZUX_FINANZAS_PATH . 'includes/Admin_Settings.php'; // Moved here from includes()
		require_once ALEZUX_FINANZAS_PATH . 'includes/Webhook_Handler.php'; // Moved here from includes()
        require_once ALEZUX_FINANZAS_PATH . 'includes/Access_Control.php'; // Moved here from includes()

		// Inicializar manejadores AJAX
		\Alezux_Members\Modules\Finanzas\Includes\Ajax_Handler::init();
        
        // Inicializar Configuración Admin
        \Alezux_Members\Modules\Finanzas\Includes\Admin_Settings::init();

        // Inicializar Webhooks API
        \Alezux_Members\Modules\Finanzas\Includes\Webhook_Handler::init();
        
        // Inicializar Control de Acceso (Hooks LearnDash)
        if ( class_exists( 'Alezux_Members\Modules\Finanzas\Includes\Access_Control' ) ) {
            \Alezux_Members\Modules\Finanzas\Includes\Access_Control::init();
        }

        // Widgets Files (Include but register in 'elementor/widgets/register' hook)
        require_once ALEZUX_FINANZAS_PATH . 'widgets/Create_Plan_Widget.php';
        require_once ALEZUX_FINANZAS_PATH . 'widgets/Sales_History_Widget.php';
        require_once ALEZUX_FINANZAS_PATH . 'widgets/Subscriptions_List_Widget.php';
        require_once ALEZUX_FINANZAS_PATH . 'widgets/Manual_Payment_Widget.php';

        // Dashboard UI (Legacy/Admin Page - Disabled but file kept for reference logic)
        require_once ALEZUX_FINANZAS_PATH . 'includes/Finance_Dashboard.php';
        \Alezux_Members\Modules\Finanzas\Includes\Finance_Dashboard::init();

		// Instalación de Tablas al activar (o usar otro hook si es carga dinámica)
		\add_action( 'admin_init', array( __NAMESPACE__ . '\\Includes\\Database_Installer', 'check_updates' ) );
		
        // Registrar Widgets Elementor
        \add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
        
        // Encolar Estilos de Widgets
        \add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_widget_styles' ] );
	}

	public function register_widgets( $widgets_manager ) {
}

