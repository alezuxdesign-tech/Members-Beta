<?php
namespace Alezux_Members\Modules\Estudiantes;

use Alezux_Members\Core\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Estudiantes extends Module_Base {

	public function init() {
		// Registrar Shortcodes
		add_shortcode( 'alezux_estudiantes_total', [ $this, 'shortcode_total_students' ] );
		add_shortcode( 'alezux_estudiantes_nuevos_mes', [ $this, 'shortcode_new_students_month' ] );

		// Registrar Widgets de Elementor
		add_action( 'elementor/widgets/register', [ $this, 'register_elementor_widgets' ] );
		
		// Registrar scripts y estilos
		add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'register_assets' ] );

		// AJAX Endpoints
		add_action( 'wp_ajax_alezux_search_students', [ $this, 'ajax_search_students' ] );
		add_action( 'wp_ajax_alezux_register_student', [ $this, 'ajax_register_student' ] );
		add_action( 'wp_ajax_alezux_register_batch_csv', [ $this, 'ajax_register_batch_csv' ] );
	}

	public function register_assets() {
		// Estilos
		\wp_enqueue_style( 'alezux-estudiantes-css', \plugin_dir_url( __FILE__ ) . 'assets/css/estudiantes.css', [], '1.0.0' );
		\wp_register_style( 'alezux-estudiantes-register-css', \plugin_dir_url( __FILE__ ) . 'assets/css/estudiantes-register.css', [], '1.0.0' );
		\wp_register_style( 'alezux-estudiantes-csv-css', \plugin_dir_url( __FILE__ ) . 'assets/css/estudiantes-csv.css', [], '1.0.0' ); // Se registra pero no se encola globalmente

		// Scripts
		\wp_enqueue_script( 'alezux-estudiantes-js', \plugin_dir_url( __FILE__ ) . 'assets/js/estudiantes.js', [ 'jquery' ], '1.0.0', true );
		\wp_register_script( 'alezux-estudiantes-register-js', \plugin_dir_url( __FILE__ ) . 'assets/js/estudiantes-register.js', [ 'jquery' ], '1.0.0', true );
		\wp_register_script( 'alezux-estudiantes-csv-js', \plugin_dir_url( __FILE__ ) . 'assets/js/estudiantes-csv.js', [ 'jquery' ], '1.0.0', true );

		// Localize Scripts (Variables comunes)
		$vars = [
			'ajax_url' => \admin_url( 'admin-ajax.php' ),
			'nonce'    => \wp_create_nonce( 'alezux_estudiantes_nonce' ),
		];

		\wp_localize_script( 'alezux-estudiantes-js', 'alezux_estudiantes_vars', $vars );
		\wp_localize_script( 'alezux-estudiantes-register-js', 'alezux_estudiantes_vars', $vars );
		\wp_localize_script( 'alezux-estudiantes-csv-js', 'alezux_estudiantes_vars', $vars );
	}

	public function register_elementor_widgets( $widgets_manager ) {
		// Include widget files
		if ( file_exists( __DIR__ . '/widgets/Estudiantes_Widget.php' ) ) {
			require_once __DIR__ . '/widgets/Estudiantes_Widget.php';
			$widgets_manager->register( new \Alezux_Members\Modules\Estudiantes\Widgets\Estudiantes_Widget() );
		}
		
		if ( file_exists( __DIR__ . '/widgets/Estudiantes_Register_Widget.php' ) ) {
			require_once __DIR__ . '/widgets/Estudiantes_Register_Widget.php';
			$widgets_manager->register( new \Alezux_Members\Modules\Estudiantes\Widgets\Estudiantes_Register_Widget() );
		}

		if ( file_exists( __DIR__ . '/widgets/Estudiantes_CSV_Widget.php' ) ) {
			require_once __DIR__ . '/widgets/Estudiantes_CSV_Widget.php';
			$widgets_manager->register( new \Alezux_Members\Modules\Estudiantes\Widgets\Estudiantes_CSV_Widget() );
		}
	}

	public function shortcode_total_students() {
		$result = count_users();
		return $result['total_users'];
	}

	public function shortcode_new_students_month() {
		$args = array(
			'role__in'    => array( 'subscriber', 'student' ),
			'date_query'  => array(
				array( 'after' => 'first day of this month' )
			)
		);
		$user_query = new \WP_User_Query( $args );
		return $user_query->get_total();
	}

	/**
	 * AJAX Handler: Buscar estudiantes
	 */
	public function ajax_search_students() {
        // ... (existing code)
        check_ajax_referer( 'alezux_estudiantes_nonce', 'nonce' );

		if ( ! current_user_can( 'edit_users' ) ) {
			wp_send_json_error( [ 'message' => 'No tienes permisos.' ] );
		}

		$search = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';
		$page   = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
		$limit  = isset( $_POST['limit'] ) ? intval( $_POST['limit'] ) : 10;
		$offset = ( $page - 1 ) * $limit;

		$args = [
			'role__in'       => [ 'subscriber', 'student' ],
			'number'         => $limit,
			'offset'         => $offset,
			'search'         => '*' . $search . '*',
			'search_columns' => [ 'user_login', 'user_email', 'display_name' ],
			'count_total'    => true, // Asegurar que cuente el total
		];

		// Si string vacío, devuelve lista inicial sin filtro de búsqueda
		if ( empty( $search ) ) {
			unset( $args['search'] );
		}

		$user_query = new \WP_User_Query( $args );
		$students = $user_query->get_results();
		$total_users = $user_query->get_total();
		$total_pages = ceil( $total_users / $limit );

		$data = [];
		foreach ( $students as $student ) {
			$data[] = [
				'id'           => $student->ID,
				'name'         => $student->display_name,
				'username'     => $student->user_nicename,
				'email'        => $student->user_email,
				'avatar_url'   => get_avatar_url( $student->ID ),
				'status_label' => 'OK',
				'status_class' => 'status-active',
			];
		}

		wp_send_json_success( [
			'students'     => $data,
			'total_pages'  => $total_pages,
			'current_page' => $page,
			'total_users'  => $total_users
		] );
	}

	/**
	 * AJAX Handler: Registro Manual de 1 Estudiante
	 */
	public function ajax_register_student() {
		check_ajax_referer( 'alezux_estudiantes_nonce', 'nonce' );

		if ( ! current_user_can( 'edit_users' ) ) {
			wp_send_json_error( [ 'message' => 'No tienes permisos.' ] );
		}

		$first_name = isset( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : '';
		$last_name  = isset( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : '';
		$email      = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
		$course_id  = isset( $_POST['course_id'] ) ? intval( $_POST['course_id'] ) : 0;

		if ( empty( $email ) || ! is_email( $email ) ) {
			wp_send_json_error( [ 'message' => 'Email inválido.' ] );
		}

		$result = $this->register_single_student( [
			'first_name' => $first_name,
			'last_name'  => $last_name,
			'email'      => $email,
			'course_id'  => $course_id,
		] );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( [ 'message' => $result->get_error_message() ] );
		}

		wp_send_json_success( [ 'message' => 'Estudiante registrado correctamente.' ] );
	}

	/**
	 * AJAX Handler: Registro Batch CSV (Procesa lote pequeño)
	 */
	public function ajax_register_batch_csv() {
		check_ajax_referer( 'alezux_estudiantes_nonce', 'nonce' );

		if ( ! current_user_can( 'edit_users' ) ) {
			wp_send_json_error( [ 'message' => 'No tienes permisos.' ] );
		}

		$students  = isset( $_POST['students'] ) ? $_POST['students'] : []; // Array de {name, email, ...}
		$course_id = isset( $_POST['course_id'] ) ? intval( $_POST['course_id'] ) : 0;

		if ( empty( $students ) || ! is_array( $students ) ) {
			wp_send_json_error( [ 'message' => 'No hay datos para procesar.' ] );
		}

		$success_count = 0;
		$errors = [];

		foreach ( $students as $student_data ) {
			// Mapeo básico CSV (asumimos frontend envía claves normalizadas)
			$data = [
				'first_name' => isset( $student_data['first_name'] ) ? sanitize_text_field( $student_data['first_name'] ) : '',
				'last_name'  => isset( $student_data['last_name'] ) ? sanitize_text_field( $student_data['last_name'] ) : '',
				'email'      => isset( $student_data['email'] ) ? sanitize_email( $student_data['email'] ) : '',
				'course_id'  => $course_id,
			];

			if ( empty( $data['email'] ) ) continue;

			$res = $this->register_single_student( $data );
			if ( ! is_wp_error( $res ) ) {
				$success_count++;
			} else {
				$errors[] = $data['email'] . ': ' . $res->get_error_message();
			}
		}

		wp_send_json_success( [
			'processed' => count( $students ),
			'success'   => $success_count,
			'errors'    => $errors,
		] );
	}

	/**
	 * Lógica centralizada para registrar/matricular un estudiante
	 */
	private function register_single_student( $data ) {
		$email      = $data['email'];
		$first_name = $data['first_name'];
		$last_name  = $data['last_name'];
		$course_id  = $data['course_id'];

		$user = get_user_by( 'email', $email );
		$is_new_user = false;
		$password = '';

		if ( ! $user ) {
			// Crear usuario
			$password = wp_generate_password( 12, true );
			$username = sanitize_user( current( explode( '@', $email ) ), true );
			
			// Asegurar username único
			$base_username = $username;
			$i = 1;
			while ( username_exists( $username ) ) {
				$username = $base_username . $i;
				$i++;
			}

			$user_id = wp_create_user( $username, $password, $email );

			if ( is_wp_error( $user_id ) ) {
				return $user_id;
			}

			// Actualizar meta
			wp_update_user( [
				'ID' => $user_id,
				'first_name' => $first_name,
				'last_name'  => $last_name,
				'role'       => 'subscriber' // O 'student' si se prefiere
			] );

			$is_new_user = true;
			$user = get_user_by( 'id', $user_id );
		} else {
			// Usuario existe, solo matricularemos
			$user_id = $user->ID;
		}

		// Asignar Curso LearnDash
		if ( $course_id > 0 && function_exists( 'ld_update_course_access' ) ) {
			ld_update_course_access( $user_id, $course_id, false ); // false = add access
		}

		// Enviar Email
		if ( $is_new_user ) {
			$this->send_new_user_email( $user, $password, $course_id );
		} else {
			// Opcional: Notificar nueva matriculación a usuario existente?
			// Por ahora solo credenciales a nuevos según requerimiento.
		}

		return true;
	}

	/**
	 * Enviar Correo con Credenciales
	 */
	private function send_new_user_email( $user, $password, $course_id ) {
		$site_name = get_bloginfo( 'name' );
		$site_url  = home_url(); // URL para ingresar
		$login_url = wp_login_url(); // O URL personalizada de login
		
		$course_title = '';
		if ( $course_id ) {
			$course = get_post( $course_id );
			if ( $course ) $course_title = $course->post_title;
		}

		$subject = "Bienvenido a $site_name - Tus Credenciales de Acceso";
		
		$message  = "<p>Hola <strong>" . esc_html( $user->first_name ) . "</strong>,</p>";
		$message .= "<p>Se ha creado tu cuenta en <strong>$site_name</strong> exitosamente.</p>";
		
		if ( $course_title ) {
			$message .= "<p>Has sido inscrito en el curso: <strong>$course_title</strong>.</p>";
		}

		$message .= "<p>Aquí tienes tus datos de acceso:</p>";
		$message .= "<ul>";
		$message .= "<li><strong>URL de Acceso:</strong> <a href='$login_url'>$login_url</a></li>";
		$message .= "<li><strong>Usuario:</strong> " . esc_html( $user->user_login ) . "</li>";
		$message .= "<li><strong>Contraseña:</strong> " . esc_html( $password ) . "</li>";
		$message .= "</ul>";
		$message .= "<p>Te recomendamos cambiar tu contraseña al ingresar.</p>";
		$message .= "<p>¡Nos vemos dentro!</p>";

		$headers = [ 'Content-Type: text/html; charset=UTF-8' ];
		// From header personalizado con nombre del dominio
		$domain = $_SERVER['SERVER_NAME'];
		$headers[] = "From: $site_name <no-reply@$domain>";

		wp_mail( $user->user_email, $subject, $message, $headers );
	}
}
