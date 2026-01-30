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
		
		// AJAX para gestión de logros (Nuevo Widget)
		add_action( 'wp_ajax_alezux_get_achievements', [ $this, 'ajax_get_achievements' ] );
		add_action( 'wp_ajax_alezux_delete_achievement', [ $this, 'ajax_delete_achievement' ] );
		add_action( 'wp_ajax_alezux_get_achievement', [ $this, 'ajax_get_achievement' ] );
		add_action( 'wp_ajax_alezux_update_achievement', [ $this, 'ajax_update_achievement' ] );
		
		// Registrar scripts y estilos (Frontend + Admin para Editor Elementor)
		add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'register_assets' ] );
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
		require_once __DIR__ . '/widgets/View_Logros_Widget.php';

		$widgets_manager->register( new \Alezux_Members\Modules\Logros\Widgets\Form_Logro_Widget() );
		$widgets_manager->register( new \Alezux_Members\Modules\Logros\Widgets\Grid_Logros_Widget() );
		$widgets_manager->register( new \Alezux_Members\Modules\Logros\Widgets\View_Logros_Widget() );
	}

	public function register_assets() {
		// Scripts
		// VIEW LOGROS JS
		wp_register_script(
			'alezux-view-logros-js',
			$this->get_asset_url( 'assets/js/view-logros.js' ),
			[ 'jquery' ],
			time(),
			true
		);

		wp_localize_script( 'alezux-view-logros-js', 'alezux_logros_vars', [
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'alezux_logros_nonce' ),
		] );
		
		// Estilos
		// VIEW LOGROS CSS
		wp_register_style(
			'alezux-view-logros-css',
			$this->get_asset_url( 'assets/css/view-logros.css' ),
			[],
			time()
		);

		// GRID LOGROS CSS
		wp_register_style(
			'alezux-grid-logros-css',
			$this->get_asset_url( 'assets/css/grid-logros.css' ),
			[],
			time()
		);
		
		// GRID LOGROS JS (Si en el futuro se necesita, por ahora Grid usa JS inline)
		// Pero es bueno registrarlo si creamos el archivo
		// wp_register_script('alezux-grid-logros-js', ...);
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

	public function ajax_get_achievements() {
		check_ajax_referer( 'alezux_logros_nonce', 'nonce' );

		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( [ 'message' => 'No tienes permisos.' ] );
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'alezux_achievements';
		
		$search = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';
		$course_filter = isset( $_POST['course_id'] ) ? intval( $_POST['course_id'] ) : 0;
		$limit = isset( $_POST['limit'] ) ? intval( $_POST['limit'] ) : 20;
		$offset = isset( $_POST['offset'] ) ? intval( $_POST['offset'] ) : 0;

		$where_clauses = [ '1=1' ];
		$params = [];

		if ( ! empty( $search ) ) {
			$where_clauses[] = "(message LIKE %s OR course_id IN (SELECT ID FROM {$wpdb->posts} WHERE post_title LIKE %s))";
			$search_term = '%' . $wpdb->esc_like( $search ) . '%';
			$params[] = $search_term;
			$params[] = $search_term;
		}

		if ( $course_filter > 0 ) {
			$where_clauses[] = "course_id = %d";
			$params[] = $course_filter;
		}

		$where_sql = implode( ' AND ', $where_clauses );
		
		// Ordenar por más reciente primero
		$sql = "SELECT * FROM $table_name WHERE $where_sql ORDER BY created_at DESC LIMIT %d OFFSET %d";
		$params[] = $limit;
		$params[] = $offset;

		$results = $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
		
		// Enriquecer datos (título del curso, email estudiante)
		foreach ( $results as $row ) {
			$row->course_title = get_the_title( $row->course_id );
			$user_info = get_userdata( $row->student_id );
			$row->student_email = $user_info ? $user_info->user_email : '---';
			$row->student_name = $user_info ? $user_info->display_name : '---';
			$row->student_avatar = $user_info ? get_avatar_url( $row->student_id ) : '';
			$row->image_url = $row->image_id ? wp_get_attachment_image_url( $row->image_id, 'medium' ) : '';
			$row->formatted_date = date_i18n( get_option( 'date_format' ), strtotime( $row->created_at ) );
		}

		wp_send_json_success( $results );
	}

	public function ajax_delete_achievement() {
		check_ajax_referer( 'alezux_logros_nonce', 'nonce' );

		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( [ 'message' => 'No tienes permisos.' ] );
		}

		$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;

		if ( ! $id ) {
			wp_send_json_error( [ 'message' => 'ID inválido.' ] );
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'alezux_achievements';

		$deleted = $wpdb->delete( $table_name, [ 'id' => $id ], [ '%d' ] );

		if ( $deleted ) {
			wp_send_json_success( [ 'message' => 'Logro eliminado correctamente.' ] );
		} else {
			wp_send_json_error( [ 'message' => 'No se pudo eliminar el logro.' ] );
		}
	}

	public function ajax_get_achievement() {
		check_ajax_referer( 'alezux_logros_nonce', 'nonce' );

		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( [ 'message' => 'No tienes permisos.' ] );
		}

		$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;

		global $wpdb;
		$table_name = $wpdb->prefix . 'alezux_achievements';

		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $id ) );

		if ( $row ) {
			$row->image_url = $row->image_id ? wp_get_attachment_image_url( $row->image_id, 'medium' ) : '';
			wp_send_json_success( $row );
		} else {
			wp_send_json_error( [ 'message' => 'Logro no encontrado.' ] );
		}
	}

	public function ajax_update_achievement() {
		check_ajax_referer( 'alezux_logros_nonce', 'nonce' );

		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( [ 'message' => 'No tienes permisos.' ] );
		}

		$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
		$course_id = isset( $_POST['course_id'] ) ? intval( $_POST['course_id'] ) : 0;
		$student_id = ! empty( $_POST['student_id'] ) ? intval( $_POST['student_id'] ) : null;
		$message = isset( $_POST['message'] ) ? sanitize_textarea_field( $_POST['message'] ) : '';
		$image_id = isset( $_POST['image_id'] ) ? intval( $_POST['image_id'] ) : null;

		if ( ! $id || ! $course_id || empty( $message ) ) {
			wp_send_json_error( [ 'message' => 'Faltan datos requeridos.' ] );
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'alezux_achievements';

		$updated = $wpdb->update(
			$table_name,
			[
				'course_id'  => $course_id,
				'student_id' => $student_id,
				'message'    => $message,
				'image_id'   => $image_id,
			],
			[ 'id' => $id ],
			[ '%d', '%d', '%s', '%d' ],
			[ '%d' ]
		);

		if ( $updated !== false ) {
			wp_send_json_success( [ 'message' => 'Logro actualizado correctamente.' ] );
		} else {
			wp_send_json_error( [ 'message' => 'Error al actualizar.' ] );
		}
	}
}
