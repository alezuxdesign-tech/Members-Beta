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

		// Registrar Categoría de Elementor (Ya existe 'alezux-admin' en Logros, podemos reusarla o registrarla si no existe)
		// Mejor usamos 'alezux-admin' que ya se usa en Logros si queremos agrupar herramientas de admin.
		// Pero si este modulo carga independiente, deberíamos asegurarnos.
		// Logros usa 'alezux-admin'. Usaremos la misma.

		// Registrar Widgets de Elementor
		add_action( 'elementor/widgets/register', [ $this, 'register_elementor_widgets' ] );
		
		// Registrar scripts y estilos
		add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'register_assets' ] );

		// AJAX Búsqueda Estudiantes
		add_action( 'wp_ajax_alezux_search_students', [ $this, 'ajax_search_students' ] );
		// Si queremos que funcione para no logueados (dudoso para admin panel, pero por si acaso)
		// add_action( 'wp_ajax_nopriv_alezux_search_students', [ $this, 'ajax_search_students' ] );
	}

	public function register_elementor_widgets( $widgets_manager ) {
		require_once __DIR__ . '/widgets/Estudiantes_Widget.php';
		$widgets_manager->register( new \Alezux_Members\Modules\Estudiantes\Widgets\Estudiantes_Widget() );
	}

	public function register_assets() {
		// Estilos
		wp_register_style(
			'alezux-estudiantes-css',
			$this->get_asset_url( 'assets/css/estudiantes.css' ),
			[],
			time()
		);

		// Scripts
		wp_register_script(
			'alezux-estudiantes-js',
			$this->get_asset_url( 'assets/js/estudiantes.js' ),
			[ 'jquery' ],
			time(),
			true
		);

		wp_localize_script( 'alezux-estudiantes-js', 'alezux_estudiantes_vars', [
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'alezux_estudiantes_nonce' ),
		] );
	}

	/**
	 * Shortcode: Listar número global de alumnos
	 * [alezux_estudiantes_total]
	 */
	public function shortcode_total_students() {
		// Contar usuarios. Asumimos rol 'subscriber' o todos. 
		// Generalmente en LMS los alumnos son 'subscriber' o 'student' (si es LearnDash)
		// Vamos a contar todos los usuarios que no sean administradores para tener un número "real" de alumnos?
		// O mejor, usamos count_users() y sumamos todo menos admin?
		// Simplifiquemos sumando usuarios con rol 'subscriber'.
		
		$count = 0;
		$users = count_users();
		
		// Si existe el rol subscriber, lo usamos.
		if ( isset( $users['avail_roles']['subscriber'] ) ) {
			$count += $users['avail_roles']['subscriber'];
		}
		// Si existe el rol student (LearnDash), lo sumamos también si es distinto
		if ( isset( $users['avail_roles']['student'] ) ) {
			$count += $users['avail_roles']['student'];
		}
		
		// Si no hay roles específicos, devolvemos total users
		if ( $count === 0 && isset( $users['total_users'] ) ) {
			// Excluir admin es buena práctica si es "alumnos"
			$count = $users['total_users'];
		}

		return number_format( $count );
	}

	/**
	 * Shortcode: Listar estudiantes nuevos que ingresaron el mes
	 * [alezux_estudiantes_nuevos_mes]
	 */
	public function shortcode_new_students_month() {
		$args = [
			'role__in' => [ 'subscriber', 'student' ], // Ajustar según roles reales
			'date_query' => [
				[
					'year'  => date( 'Y' ),
					'month' => date( 'm' ),
				],
			],
			'fields' => 'ID', // Solo necesitamos contar
		];

		// Si no hay roles definidos, busca en todos (menos admin idealmente, pero get_users busca todos por defecto)
		// Vamos a intentar obtener usuarios registrados este mes.
		
		$user_query = new \WP_User_Query( $args );
		$results = $user_query->get_results();
		
		// Si da 0 y puede ser porque no hay roles 'subscriber/student', intentamos sin roles
		if ( empty( $results ) ) {
			$args_bucket = [
				'date_query' => [
					[
						'year'  => date( 'Y' ),
						'month' => date( 'm' ),
					],
				],
				'fields' => 'ID',
			];
			$user_query_bucket = new \WP_User_Query( $args_bucket );
			$results_bucket = $user_query_bucket->get_results();
			return number_format( count( $results_bucket ) );
		}

		return number_format( count( $results ) );
	}

	/**
	 * AJAX Handler: Buscar estudiantes
	 */
	public function ajax_search_students() {
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

		// Fallback búsqueda global si no hay roles específicos y no encontramos nada (opcional)
		// Mantenemos la lógica simple de widget para consistencia

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
}
