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
		// La lógica de inclusión se ha movido a init_hooks para asegurar el orden de carga
	}

	private function init_hooks() {
		// Incluir archivos necesarios
		require_once ALEZUX_FINANZAS_PATH . 'includes/Database_Installer.php';
		require_once ALEZUX_FINANZAS_PATH . 'includes/Ajax_Handler.php';
		require_once ALEZUX_FINANZAS_PATH . 'includes/Stripe_API.php';
		require_once ALEZUX_FINANZAS_PATH . 'includes/Admin_Settings.php';
		require_once ALEZUX_FINANZAS_PATH . 'includes/Webhook_Handler.php';
        require_once ALEZUX_FINANZAS_PATH . 'includes/Access_Control.php';

		// Inicializar manejadores
		\Alezux_Members\Modules\Finanzas\Includes\Ajax_Handler::init();
        \Alezux_Members\Modules\Finanzas\Includes\Admin_Settings::init();
        \Alezux_Members\Modules\Finanzas\Includes\Webhook_Handler::init();
        
        // Inicializar Control de Acceso (Hooks LearnDash)
        if ( class_exists( 'Alezux_Members\Modules\Finanzas\Includes\Access_Control' ) ) {
            \Alezux_Members\Modules\Finanzas\Includes\Access_Control::init();
        }

        // Dashboard legacy (opcional, mantener por si acaso se necesita lógica interna)
        // require_once ALEZUX_FINANZAS_PATH . 'includes/Finance_Dashboard.php';
        // \Alezux_Members\Modules\Finanzas\Includes\Finance_Dashboard::init();
 
 		// Hooks
 		\add_action( 'admin_init', array( __NAMESPACE__ . '\\Includes\\Database_Installer', 'check_updates' ) );
        // \add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
        // \add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_widget_styles' ] );
	}

	public function register_widgets( $widgets_manager ) {
        if ( ! did_action( 'elementor/loaded' ) ) {
			return;
		}
        
        // Incluir archivos de widgets aquí para asegurar que Elementor está cargado
        require_once ALEZUX_FINANZAS_PATH . 'widgets/Create_Plan_Widget.php';
        require_once ALEZUX_FINANZAS_PATH . 'widgets/Sales_History_Widget.php';
        require_once ALEZUX_FINANZAS_PATH . 'widgets/Subscriptions_List_Widget.php';
        require_once ALEZUX_FINANZAS_PATH . 'widgets/Manual_Payment_Widget.php';

        // Registrar Widgets
        if ( class_exists( 'Alezux_Members\Modules\Finanzas\Widgets\Create_Plan_Widget' ) ) {
            $widgets_manager->register( new Widgets\Create_Plan_Widget() );
        }
        
        if ( class_exists( 'Alezux_Members\Modules\Finanzas\Widgets\Sales_History_Widget' ) ) {
            $widgets_manager->register( new Widgets\Sales_History_Widget() );
        }
        
        if ( class_exists( 'Alezux_Members\Modules\Finanzas\Widgets\Subscriptions_List_Widget' ) ) {
            $widgets_manager->register( new Widgets\Subscriptions_List_Widget() );
        }
        
        if ( class_exists( 'Alezux_Members\Modules\Finanzas\Widgets\Manual_Payment_Widget' ) ) {
            $widgets_manager->register( new Widgets\Manual_Payment_Widget() );
        }
	}

    public function enqueue_widget_styles() {
        wp_register_style( 'alezux-sales-history-css', ALEZUX_FINANZAS_URL . 'assets/css/sales-history.css', [], '1.0.0' );
        wp_register_style( 'alezux-subs-list-css', ALEZUX_FINANZAS_URL . 'assets/css/subscriptions-list.css', [], '1.0.0' );
        wp_register_style( 'alezux-manual-payment-css', ALEZUX_FINANZAS_URL . 'assets/css/manual-payment.css', [], '1.0.0' );
    }
}

