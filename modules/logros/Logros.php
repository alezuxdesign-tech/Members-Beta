<?php
namespace Alezux_Members\Modules\Logros;

use Alezux_Members\Core\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Logros extends Module_Base {

	public function init() {
		// Crear tabla de base de datos si no existe
		$this->maybe_create_table();

		// Registrar Categoría de Elementor
		add_action( 'elementor/elements/categories_registered', [ $this, 'register_elementor_category' ] );

		// Registrar Widgets de Elementor
		add_action( 'elementor/widgets/register', [ $this, 'register_elementor_widgets' ] );

		// AJAX para guardar logro (Solo Admin)
		add_action( 'wp_ajax_alezux_save_achievement', [ $this, 'ajax_save_achievement' ] );
		
		// Encolar scripts necesarios
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	public function register_elementor_category( $elements_manager ) {
		$elements_manager->add_category(
			'alezux-admin',
			[
				'title' => esc_html__( 'Alezux Admin', 'alezux-members' ),
				'icon'  => 'fa fa-lock',
			]
		);
	}

	public function register_elementor_widgets( $widgets_manager ) {
		require_once __DIR__ . '/widgets/Form_Logro_Widget.php';
		require_once __DIR__ . '/widgets/Grid_Logros_Widget.php';

		$widgets_manager->register( new \Alezux_Members\Modules\Logros\Widgets\Form_Logro_Widget() );
		$widgets_manager->register( new \Alezux_Members\Modules\Logros\Widgets\Grid_Logros_Widget() );
	}

	public function enqueue_scripts() {
		wp_enqueue_script( 'jquery' );
		
		// Script para manejar el Popup y el Formulario
		wp_enqueue_script(
			'alezux-logros-js',
			$this->get_asset_url( 'assets/js/logros.js' ),
			[ 'jquery' ],
			ALEZUX_MEMBERS_VERSION,
			true
		);

		wp_localize_script( 'alezux-logros-js', 'alezux_logros_vars', [
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'alezux_logros_nonce' ),
		] );
		
		// Estilos para el popup y grid
		wp_enqueue_style(
			'alezux-logros-css',
			$this->get_asset_url( 'assets/css/logros.css' ),
			[],
			ALEZUX_MEMBERS_VERSION
		);
	}

	private function maybe_create_table() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'alezux_achievements';
		
		// Verificar versión de la DB para no ejecutar dbDelta siempre
		$installed_ver = get_option( 'alezux_achievements_db_version' );
		$version = '1.0.0';

		if ( $installed_ver !== $version ) {
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				course_id bigint(20) NOT NULL,
				student_id bigint(20) DEFAULT NULL,
				message text NOT NULL,
				image_id bigint(20) DEFAULT NULL,
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

			update_option( 'alezux_achievements_db_version', $version );
		}
	}

	public function ajax_save_achievement() {
		check_ajax_referer( 'alezux_logros_nonce', 'nonce' );

		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( [ 'message' => 'No tienes permisos.' ] );
		}

		$course_id = isset( $_POST['course_id'] ) ? intval( $_POST['course_id'] ) : 0;
		$student_id = ! empty( $_POST['student_id'] ) ? intval( $_POST['student_id'] ) : null;
		$message = isset( $_POST['message'] ) ? sanitize_textarea_field( $_POST['message'] ) : '';
		$image_id = isset( $_POST['image_id'] ) ? intval( $_POST['image_id'] ) : null;

		if ( ! $course_id || empty( $message ) ) {
			wp_send_json_error( [ 'message' => 'Faltan datos requeridos (Curso o Mensaje).' ] );
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'alezux_achievements';

		$result = $wpdb->insert(
			$table_name,
			[
				'course_id'  => $course_id,
				'student_id' => $student_id,
				'message'    => $message,
				'image_id'   => $image_id,
			],
			[
				'%d',
				'%d',
				'%s',
				'%d'
			]
		);

		if ( $result ) {
			wp_send_json_success( [ 'message' => 'Logro guardado correctamente.' ] );
		} else {
			wp_send_json_error( [ 'message' => 'Error al guardar en la base de datos.' ] );
		}
	}
}
