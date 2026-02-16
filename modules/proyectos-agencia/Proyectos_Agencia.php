<?php
namespace Alezux_Members\Modules\Proyectos_Agencia;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Alezux_Members\Modules\Proyectos_Agencia\Includes\Project_Manager;

class Proyectos_Agencia {

	public function __construct() {
		// Constructor vacío o lógica mínima
	}

	public function init() {
		// Cargar dependencias
		$this->load_dependencies();

		// Inicializar hooks
		$this->init_hooks();

		// Verificar instalación (crear tablas si no existen)
		if ( is_admin() ) {
			$this->check_install();
		}
	}

	public function load_dependencies() {
		require_once __DIR__ . '/includes/Project_Manager.php';
		// Widgets
		require_once __DIR__ . '/widgets/Projects_List_Widget.php';
		require_once __DIR__ . '/widgets/Project_Detail_Admin_Widget.php';
		require_once __DIR__ . '/widgets/Client_Project_Widget.php';
	}

	private function init_hooks() {
		// Registrar scripts y estilos
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );

		// Registrar Widgets de Elementor
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );

		// AJAX Handlers
		add_action( 'wp_ajax_alezux_create_project', [ $this, 'ajax_create_project' ] );
		add_action( 'wp_ajax_alezux_update_project', [ $this, 'ajax_update_project' ] );
		
		// Cliente AJAX
		add_action( 'wp_ajax_alezux_submit_briefing', [ $this, 'ajax_submit_briefing' ] );
		add_action( 'wp_ajax_alezux_approve_design', [ $this, 'ajax_approve_design' ] );
	}

	public function register_widgets( $widgets_manager ) {
		$widgets_manager->register( new \Alezux_Members\Modules\Proyectos_Agencia\Widgets\Projects_List_Widget() );
		$widgets_manager->register( new \Alezux_Members\Modules\Proyectos_Agencia\Widgets\Project_Detail_Admin_Widget() );
		$widgets_manager->register( new \Alezux_Members\Modules\Proyectos_Agencia\Widgets\Client_Project_Widget() );
	}

	public function ajax_create_project() {
		check_ajax_referer( 'alezux_projects_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'No tienes permisos.' );
		}

		$name = sanitize_text_field( $_POST['project_name'] );
		$user_id = absint( $_POST['customer_id'] );

		if ( empty( $name ) || empty( $user_id ) ) {
			wp_send_json_error( 'Faltan datos requeridos.' );
		}

		$manager = new Project_Manager();
		$project_id = $manager->create_project( $name, $user_id );

		if ( $project_id ) {
			wp_send_json_success( [ 'message' => 'Proyecto creado.', 'id' => $project_id ] );
		} else {
			wp_send_json_error( 'Error al crear proyecto en DB.' );
		}
	}

	public function ajax_update_project() {
		check_ajax_referer( 'alezux_projects_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'No tienes permisos.' );
		}

		$project_id   = absint( $_POST['project_id'] );
		$status       = sanitize_text_field( $_POST['status'] );
		$current_step = sanitize_text_field( $_POST['current_step'] );
		$design_url   = esc_url_raw( $_POST['design_url'] );

		$manager = new Project_Manager();
		$manager->update_status( $project_id, $status, $current_step );

		if ( ! empty( $design_url ) ) {
			$manager->update_project_meta( $project_id, 'design_proposal_url', $design_url );
		}

		wp_send_json_success( 'Proyecto actualizado correctamente.' );
	}

	public function ajax_submit_briefing() {
		check_ajax_referer( 'alezux_projects_nonce', 'nonce' );

		$project_id = absint( $_POST['project_id'] );
		
		// Validar propiedad del proyecto
		$manager = new Project_Manager();
		$project = $manager->get_project( $project_id );
		
		if ( ! $project || $project->customer_id != get_current_user_id() ) {
			wp_send_json_error( 'No tienes permiso para editar este proyecto.' );
		}

		// Recolectar datos
		$briefing_data = [
			'brand_name'    => sanitize_text_field( $_POST['brand_name'] ),
			'slogan'        => sanitize_text_field( $_POST['slogan'] ),
			'colors'        => sanitize_text_field( $_POST['colors'] ),
			'business_desc' => sanitize_textarea_field( $_POST['business_desc'] ),
			'submitted_at'  => current_time( 'mysql' )
		];

		// Guardar Meta
		$manager->update_project_meta( $project_id, 'briefing_data', json_encode( $briefing_data ) );
		
		// Actualizar Estado
		$manager->update_status( $project_id, 'briefing_completed', 'briefing' ); // Se mantiene en briefing visiblemente o avanza a revisión? Mejor dejarlo en briefing hasta que admin revise

		// Enviar Notificación al Admin (Future Scope)

		wp_send_json_success( 'Briefing enviado correctamente.' );
	}

	public function ajax_approve_design() {
		check_ajax_referer( 'alezux_projects_nonce', 'nonce' );

		$project_id = absint( $_POST['project_id'] );
		
		$manager = new Project_Manager();
		$project = $manager->get_project( $project_id );

		if ( ! $project || $project->customer_id != get_current_user_id() ) {
			wp_send_json_error( 'No tienes permiso.' );
		}

		// Marcar aprobación
		$approval_data = [
			'approved_at' => current_time( 'mysql' ),
			'ip_address'  => $_SERVER['REMOTE_ADDR']
		];

		$manager->update_project_meta( $project_id, 'approval_data', json_encode( $approval_data ) );
		
		// Avanzar Fases Automáticamente
		$manager->update_status( $project_id, 'in_progress', 'in_progress' );

		wp_send_json_success( 'Diseño aprobado. ¡Comenzamos el desarrollo!' );
	}

	public function check_install() {
		$installed_ver = get_option( 'alezux_projects_version' );
		$current_ver   = '1.0.0';

		if ( $installed_ver !== $current_ver ) {
			$manager = new Project_Manager();
			$manager->create_tables();
			update_option( 'alezux_projects_version', $current_ver );
		}
	}

	public function enqueue_assets() {
		// Encolar assets específicos del módulo
		wp_enqueue_style( 
			'alezux-projects-css', 
			plugin_dir_url( __FILE__ ) . 'assets/css/projects.css', 
			[], 
			ALEZUX_MEMBERS_VERSION 
		);
		wp_enqueue_script( 
			'alezux-projects-js', 
			plugin_dir_url( __FILE__ ) . 'assets/js/projects.js', 
			[ 'jquery' ], 
			ALEZUX_MEMBERS_VERSION, 
			true 
		);
		
		// Pasar variables a JS
		wp_localize_script( 'alezux-projects-js', 'AlezuxProjects', [
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'alezux_projects_nonce' )
		]);
	}
}

// Inicializar el módulo (El Loader lo hará, pero si se carga manual también funciona)
// new Proyectos_Agencia();
