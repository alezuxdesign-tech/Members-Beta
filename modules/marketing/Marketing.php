<?php
namespace Alezux_Members\Modules\Marketing;

use Alezux_Members\Core\Module_Base;
use Alezux_Members\Modules\Marketing\Includes\Email_Engine;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Marketing extends Module_Base {

	private static $instance = null;
	private $email_engine;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function init() {
		// Crear tabla DB si no existe
		$this->maybe_create_table();

		// Inicializar Engine
		require_once __DIR__ . '/includes/Email_Engine.php';
		$this->email_engine = new Email_Engine();

		// Registrar Widgets Elementor
		add_action( 'elementor/widgets/register', [ $this, 'register_elementor_widgets' ] );

		// Encolar assets admin
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		// Encolar assets frontend si el widget se usa en frontend
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );

		// AJAX Actions
		add_action( 'wp_ajax_alezux_marketing_get_templates', [ $this, 'ajax_get_templates' ] );
		add_action( 'wp_ajax_alezux_marketing_get_template', [ $this, 'ajax_get_template' ] );
		add_action( 'wp_ajax_alezux_marketing_save_template', [ $this, 'ajax_save_template' ] );
		add_action( 'wp_ajax_alezux_marketing_save_settings', [ $this, 'ajax_save_settings' ] );
		add_action( 'wp_ajax_alezux_marketing_get_settings', [ $this, 'ajax_get_settings' ] );
	}

	public function get_engine() {
		return $this->email_engine;
	}

	public function register_elementor_widgets( $widgets_manager ) {
		require_once __DIR__ . '/widgets/Marketing_Config_Widget.php';
		$widgets_manager->register( new \Alezux_Members\Modules\Marketing\Widgets\Marketing_Config_Widget() );
	}

	public function enqueue_assets() {
		// Reusamos estilos de tabla globales. Calculamos URL relativa a modules/finanzas
		// __DIR__ = modules/marketing. Ups lvl to modules -> finanzas
		$custom_url = plugin_dir_url( dirname( __DIR__ ) . '/finanzas/dummy.php' ); 
		
		// Encolar media uploader para selector de imágenes
		wp_enqueue_media();

		wp_enqueue_style( 
			'alezux-tables-css', 
			$custom_url . 'assets/css/alezux-tables.css', 
			[], 
			'1.0.5' 
		);
		
		// Estilos propios del módulo
		wp_enqueue_style( 
			'alezux-marketing-admin-css', 
			plugin_dir_url( __FILE__ ) . 'assets/css/marketing-admin.css', 
			[], 
			time() 
		);

		// JS
		wp_register_script(
			'alezux-marketing-admin-js',
			plugin_dir_url( __FILE__ ) . 'assets/js/marketing-admin.js',
			[ 'jquery', 'elementor-frontend' ], 
			time(),
			true
		);

		wp_localize_script( 'alezux-marketing-admin-js', 'alezux_marketing_vars', [
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'alezux_marketing_nonce' ),
			'logo_url' => get_option( 'alezux_marketing_logo_url', '' ),
		] );
	}

	private function maybe_create_table() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'alezux_marketing_templates';
		
		$installed_ver = get_option( 'alezux_marketing_db_version' );
		$version = '1.0.0';

		if ( $installed_ver !== $version ) {
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				type varchar(50) NOT NULL UNIQUE,
				subject text NOT NULL,
				content longtext NOT NULL,
				is_active tinyint(1) DEFAULT 1,
				updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

			update_option( 'alezux_marketing_db_version', $version );
		}
	}

	// --- AJAX HANDLERS ---

	public function ajax_get_templates() {
		try {
			check_ajax_referer( 'alezux_marketing_nonce', 'nonce' );
			if ( ! current_user_can( 'administrator' ) ) throw new \Exception( 'Forbidden' );

			global $wpdb;
			$table = $wpdb->prefix . 'alezux_marketing_templates';
			
			// Ensure engine is loaded
			if ( ! $this->email_engine ) {
				require_once __DIR__ . '/includes/Email_Engine.php';
				$this->email_engine = new Email_Engine();
			}

			// 1. Obtener tipos registrados
			$registered_types = $this->email_engine->get_registered_types();
			
			// 2. Obtener configuraciones guardadas
			$saved_templates = $wpdb->get_results( "SELECT * FROM $table", OBJECT_K ); 
			
			// Safety check if table missing or error
			if ( ! is_array( $saved_templates ) ) {
				$saved_templates = [];
				// If error was strictly DB related (e.g. table not found despite checks), log it
				if ( $wpdb->last_error ) {
					error_log( 'Alezux Marketing DB Error: ' . $wpdb->last_error );
				}
			}
			
			// Reorganizar key por type
			$saved_by_type = [];
			foreach($saved_templates as $tpl) {
				if ( isset( $tpl->type ) ) {
					$saved_by_type[$tpl->type] = $tpl;
				}
			}

			$data = [];
			foreach ( $registered_types as $key => $label ) {
				$s = isset( $saved_by_type[$key] ) ? $saved_by_type[$key] : null;
				$data[] = [
					'type'      => $key,
					'label'     => $label,
					'subject'   => $s ? $s->subject : '(Por defecto)',
					'is_active' => $s ? (bool)$s->is_active : true, // Default active
					'has_custom'=> (bool)$s
				];
			}

			wp_send_json_success( $data );

		} catch ( \Exception $e ) {
			error_log( 'Alezux Marketing Error (get_templates): ' . $e->getMessage() );
			wp_send_json_error( $e->getMessage() );
		} catch ( \Error $e ) {
			error_log( 'Alezux Marketing Fatal Error (get_templates): ' . $e->getMessage() );
			wp_send_json_error( 'Server Error: ' . $e->getMessage() );
		}
	}

	public function ajax_get_template() {
		try {
			check_ajax_referer( 'alezux_marketing_nonce', 'nonce' );
			if ( ! current_user_can( 'administrator' ) ) throw new \Exception( 'Forbidden' );

			$type = sanitize_text_field( $_POST['type'] );
			
			global $wpdb;
			$table = $wpdb->prefix . 'alezux_marketing_templates';
			$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE type = %s", $type ) );

			if ( ! $row ) {
				// Return default content if not saved yet
				require_once __DIR__ . '/includes/Default_Templates.php';
				$defaults = \Alezux_Members\Modules\Marketing\Includes\Default_Templates::get( $type );
				
				wp_send_json_success( [
					'type'    => $type,
					'subject' => $defaults['subject'],
					'content' => $defaults['content'],
					'is_active' => 1
				] );
			} else {
				wp_send_json_success( $row );
			}
		} catch ( \Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}
	}

	public function ajax_save_template() {
		try {
			check_ajax_referer( 'alezux_marketing_nonce', 'nonce' );
			if ( ! current_user_can( 'administrator' ) ) throw new \Exception( 'Forbidden' );

			$type    = sanitize_text_field( $_POST['type'] );
			$subject = sanitize_text_field( $_POST['subject'] );
			$content = wp_kses_post( $_POST['content'] ); // Allow HTML
			$is_active = isset( $_POST['is_active'] ) ? 1 : 0;

			global $wpdb;
			$table = $wpdb->prefix . 'alezux_marketing_templates';

			// Check if exists
			$exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $table WHERE type = %s", $type ) );

			if ( $exists ) {
				$wpdb->update( $table, 
					[ 'subject' => $subject, 'content' => $content, 'is_active' => $is_active ], 
					[ 'type' => $type ] 
				);
			} else {
				$wpdb->insert( $table, 
					[ 'type' => $type, 'subject' => $subject, 'content' => $content, 'is_active' => $is_active ] 
				);
			}

			wp_send_json_success( [ 'message' => 'Plantilla guardada.' ] );
		} catch ( \Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}
	}

	public function ajax_save_settings() {
		check_ajax_referer( 'alezux_marketing_nonce', 'nonce' );
		if ( ! current_user_can( 'administrator' ) ) wp_send_json_error( 'Forbidden' );

		update_option( 'alezux_marketing_from_name', sanitize_text_field( $_POST['from_name'] ) );
		update_option( 'alezux_marketing_from_email', sanitize_email( $_POST['from_email'] ) );
		update_option( 'alezux_marketing_logo_url', sanitize_url( $_POST['logo_url'] ) );

		wp_send_json_success( [ 'message' => 'Configuración guardada.' ] );
	}

	public function ajax_get_settings() {
		check_ajax_referer( 'alezux_marketing_nonce', 'nonce' );
		if ( ! current_user_can( 'administrator' ) ) wp_send_json_error( 'Forbidden' );

		$data = [
			'from_name'  => get_option( 'alezux_marketing_from_name', get_bloginfo( 'name' ) ),
			'from_email' => get_option( 'alezux_marketing_from_email', get_bloginfo( 'admin_email' ) ),
			'logo_url'   => get_option( 'alezux_marketing_logo_url', '' ),
		];

		wp_send_json_success( $data );
	}
}
