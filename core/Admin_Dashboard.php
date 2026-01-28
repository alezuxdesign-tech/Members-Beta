<?php
namespace Alezux_Members\Core;

use Alezux_Members\Modules\Notifications\Notifications;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Admin_Dashboard {

	public function init() {
		add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
		add_action( 'admin_post_alezux_save_settings', [ $this, 'save_settings' ] );
		add_action( 'admin_post_alezux_send_test_notification', [ $this, 'send_test_notification' ] );
		// Fix Icono Globalmente
		add_action( 'admin_head', [ $this, 'print_menu_icon_styles' ] );
	}

	public function print_menu_icon_styles() {
		?>
		<style>
			#adminmenu #toplevel_page_alezux-members .wp-menu-image img {
				max-width: 20px;
				max-height: 20px;
				width: 20px;
				height: auto;
				padding-top: 8px;
				opacity: 0.9;
			}
			#adminmenu #toplevel_page_alezux-members:hover .wp-menu-image img {
				opacity: 1;
			}
		</style>
		<?php
	}

	public function add_admin_menu() {
		add_menu_page(
			'Alezux Members',
			'Alezux Members',
			'manage_options',
			'alezux-members',
			[ $this, 'render_dashboard' ],
			ALEZUX_MEMBERS_URL . 'modules/demo-block/assets/css/img/LOGO.svg',
			2
		);
	}

	public function enqueue_admin_assets( $hook ) {
		// Assets específicos SOLO para nuestra página de Dashboard
		if ( 'toplevel_page_alezux-members' !== $hook ) {
			return;
		}
		
		// Encolar estilos globales también en el admin para nuestra página
		wp_enqueue_style( 
			'alezux-members-global', 
			ALEZUX_MEMBERS_URL . 'assets/css/global.css', 
			[], 
			ALEZUX_MEMBERS_VERSION 
		);

// El script de tabs se ha movido directamente a la vista dashboard.php para evitar problemas de carga
	}




	public function render_dashboard() {
		// Obtener opciones guardadas
		$settings = [
			'primary_color' => get_option( 'alezux_primary_color', '#6c5ce7' ),
			'primary_hover' => get_option( 'alezux_primary_hover', '#5649c0' ),
			'bg_base'       => get_option( 'alezux_bg_base', '#0f0f0f' ),
			'bg_card'       => get_option( 'alezux_bg_card', '#1a1a1a' ),
			'border_radius' => get_option( 'alezux_border_radius', '50px' ),
			'border_color'  => get_option( 'alezux_border_color', '#333333' ),
			'box_shadow'    => get_option( 'alezux_box_shadow', '0 10px 30px rgba(0, 0, 0, 0.3)' ),
		];

		// Obtener shortcodes registrados desde Module_Base
		$shortcodes = Module_Base::get_registered_shortcodes();

		include ALEZUX_MEMBERS_PATH . 'views/admin/dashboard.php';
	}

	public function save_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'No tienes permisos.' );
		}

		check_admin_referer( 'alezux_save_settings_action', 'alezux_settings_nonce' );

		// Guardar colores y estilos
		if ( isset( $_POST['alezux_primary_color'] ) ) {
			update_option( 'alezux_primary_color', sanitize_hex_color( $_POST['alezux_primary_color'] ) );
		}
		if ( isset( $_POST['alezux_primary_hover'] ) ) {
			update_option( 'alezux_primary_hover', sanitize_hex_color( $_POST['alezux_primary_hover'] ) );
		}
		if ( isset( $_POST['alezux_bg_base'] ) ) {
			update_option( 'alezux_bg_base', sanitize_hex_color( $_POST['alezux_bg_base'] ) );
		}
		if ( isset( $_POST['alezux_bg_card'] ) ) {
			update_option( 'alezux_bg_card', sanitize_hex_color( $_POST['alezux_bg_card'] ) );
		}
		if ( isset( $_POST['alezux_border_radius'] ) ) {
			update_option( 'alezux_border_radius', sanitize_text_field( $_POST['alezux_border_radius'] ) );
		}
		if ( isset( $_POST['alezux_border_color'] ) ) {
			update_option( 'alezux_border_color', sanitize_hex_color( $_POST['alezux_border_color'] ) );
		}
		if ( isset( $_POST['alezux_box_shadow'] ) ) {
			update_option( 'alezux_box_shadow', sanitize_text_field( $_POST['alezux_box_shadow'] ) );
		}

		wp_redirect( admin_url( 'admin.php?page=alezux-members&status=success' ) );
		exit;
	}

	public function send_test_notification() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'No tienes permisos.' );
		}

		check_admin_referer( 'alezux_send_test_notification_action', 'alezux_notification_nonce' );

		$title      = isset( $_POST['notification_title'] ) ? sanitize_text_field( $_POST['notification_title'] ) : 'Notificación de Prueba';
		$message    = isset( $_POST['notification_message'] ) ? sanitize_textarea_field( $_POST['notification_message'] ) : 'Este es un mensaje de prueba.';
		$target_user_id = isset( $_POST['target_user_id'] ) ? intval( $_POST['target_user_id'] ) : 0;
		
		// Si no se especifica usuario, enviamos al actual para la prueba (o a todos si se implementara logicamente así, pero por seguridad en prueba mejor al actual si está vacío)
		if ( empty( $target_user_id ) ) {
			$target_user_id = get_current_user_id();
		}

		// Usar la clase Notifications para enviar
		// Nota: add_notification espera $target_users como 'all', ID o array de IDs.
		Notifications::add_notification( 
			$title, 
			$message, 
			'#', 
			'', 
			$target_user_id 
		);

		wp_redirect( admin_url( 'admin.php?page=alezux-members&status=notification_sent' ) );
		exit;
	}
}
