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
		require_once __DIR__ . '/includes/Proyectos_Agencia_Emails.php';
	}

	private function init_hooks() {
		// Inicializar Emails
		$emails = new \Alezux_Members\Modules\Proyectos_Agencia\Includes\Proyectos_Agencia_Emails();
		$emails->init();

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
		add_action( 'wp_ajax_alezux_submit_rejection', [ $this, 'ajax_submit_rejection' ] );
		
		// Panel Lateral
		add_action( 'wp_ajax_alezux_get_project_details', [ $this, 'ajax_get_project_details' ] );
		
		// Chat Interno
		add_action( 'wp_ajax_alezux_get_project_messages', [ $this, 'ajax_get_project_messages' ] );
		add_action( 'wp_ajax_alezux_send_project_message', [ $this, 'ajax_send_project_message' ] );
	}

	public function register_widgets( $widgets_manager ) {
		// ... (código existente) ...
		// Cargar archivos de widgets aquí para asegurar que Elementor ya está cargado
		require_once __DIR__ . '/widgets/Projects_List_Widget.php';
		require_once __DIR__ . '/widgets/Project_Detail_Admin_Widget.php';
		require_once __DIR__ . '/widgets/Client_Project_Widget.php';

		$widgets_manager->register( new \Alezux_Members\Modules\Proyectos_Agencia\Widgets\Projects_List_Widget() );
		$widgets_manager->register( new \Alezux_Members\Modules\Proyectos_Agencia\Widgets\Project_Detail_Admin_Widget() );
		$widgets_manager->register( new \Alezux_Members\Modules\Proyectos_Agencia\Widgets\Client_Project_Widget() );
	}

	public function ajax_get_project_details() {
		check_ajax_referer( 'alezux_projects_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'No tienes permisos.' );
		}

		$project_id = absint( $_POST['project_id'] );
		$manager = new Project_Manager();
		$project = $manager->get_project( $project_id );

		if ( ! $project ) {
			wp_send_json_error( 'Proyecto no encontrado.' );
		}

		$customer = get_userdata( $project->customer_id );
		$meta = $manager->get_all_project_meta( $project_id );
		$design_url = isset($meta['design_proposal_url']) ? $meta['design_proposal_url'] : '';

		ob_start();
		?>

		<div class="panel-details-container" data-project-id="<?php echo esc_attr($project->id); ?>">
			
			<!-- Tabs Header -->
			<div class="alezux-tabs-header">
				<button class="tab-btn active" data-tab="tab-overview"><i class="eicon-dashboard"></i> Overview</button>
				<button class="tab-btn" data-tab="tab-chat"><i class="eicon-comment"></i> Chat</button>
				<button class="tab-btn" data-tab="tab-docs" disabled title="Próximamente"><i class="eicon-file-text"></i> Docs</button>
			</div>

			<!-- Tab Content: Overview -->
			<div id="tab-overview" class="tab-content active">
				<div class="panel-section">
					<h4 class="panel-section-title">Información General</h4>
					<div class="detail-grid">
						<div class="detail-item">
							<label>Cliente</label>
							<div class="client-mini-profile">
								<?php echo get_avatar( $project->customer_id, 32 ); ?>
								<div>
									<span class="d-block"><?php echo $customer ? esc_html( $customer->display_name ) : 'Desconocido'; ?></span>
									<small class="d-block text-muted"><?php echo $customer ? esc_html( $customer->user_email ) : ''; ?></small>
								</div>
							</div>
						</div>
						<div class="detail-item">
							<label>Fecha Inicio</label>
							<p><i class="eicon-calendar"></i> <?php echo date_i18n( get_option('date_format'), strtotime($project->created_at) ); ?></p>
						</div>
					</div>
				</div>

				<div class="panel-section">
					<h4 class="panel-section-title">Actualizar Estado</h4>
					<form id="update-project-status-form" class="alezux-form-group">
						<input type="hidden" name="project_id" value="<?php echo esc_attr($project->id); ?>">
						
						<div class="form-row" style="display:flex; gap:15px; margin-bottom:15px;">
							<div style="flex:1;">
								<label>Estado del Sistema</label>
								<select name="status" class="alezux-input">
									<option value="pending" <?php selected( $project->status, 'pending' ); ?>>Pendiente</option>
									<option value="in_progress" <?php selected( $project->status, 'in_progress' ); ?>>En Progreso</option>
									<option value="completed" <?php selected( $project->status, 'completed' ); ?>>Completado</option>
									<option value="cancelled" <?php selected( $project->status, 'cancelled' ); ?>>Cancelado</option>
								</select>
							</div>
							<div style="flex:1;">
								<label>Fase (Cliente)</label>
								<select name="current_step" class="alezux-input">
									<option value="briefing" <?php selected( $project->current_step, 'briefing' ); ?>>1. Briefing</option>
									<option value="design_review" <?php selected( $project->current_step, 'design_review' ); ?>>2. Revisión Diseño</option>
									<option value="in_progress" <?php selected( $project->current_step, 'in_progress' ); ?>>3. Desarrollo</option>
									<option value="completed" <?php selected( $project->current_step, 'completed' ); ?>>4. Completado</option>
								</select>
							</div>
						</div>

						<div style="margin-bottom:15px;">
							<label>URL Propuesta de Diseño</label>
							<div class="alezux-input-group">
								<span class="input-group-text"><i class="eicon-link"></i></span>
								<input type="url" name="design_url" class="alezux-input" value="<?php echo esc_url($design_url); ?>" placeholder="https://figma.com/..." style="padding-left: 35px;">
							</div>
						</div>

						<button type="submit" class="alezux-marketing-btn primary" style="width:100%;">
							<i class="eicon-save"></i> Guardar Cambios
						</button>
					</form>
				</div>
			</div>
			
			<!-- Tab Content: Chat -->
			<div id="tab-chat" class="tab-content">
				<div class="panel-section chat-section" style="height: 100%; display: flex; flex-direction: column;">
					<div id="project-chat-container" class="project-chat-container" style="flex-grow: 1; height: auto;">
						<div id="chat-messages-list" class="chat-messages-list">
							<!-- Mensajes cargados vía JS -->
							<div class="chat-loading"><i class="eicon-loading eicon-animation-spin"></i> Cargando historial...</div>
						</div>
						<div class="chat-input-area">
							<textarea id="chat-message-input" placeholder="Escribe un mensaje al cliente..."></textarea>
							<button id="btn-send-chat" class="alezux-marketing-btn"><i class="eicon-send"></i></button>
						</div>
					</div>
				</div>
			</div>

			<!-- Tab Content: Docs (Placeholder) -->
			<div id="tab-docs" class="tab-content">
				<div class="alezux-empty-state">
					<i class="eicon-file-text"></i>
					<h3>Documentación</h3>
					<p>Próximamente podrás gestionar archivos aquí.</p>
				</div>
			</div>

		</div>
		<?php
		$html = ob_get_clean();

		wp_send_json_success( [
			'project' => $project,
			'html'    => $html
		] );
	}
	
	public function ajax_get_project_messages() {
		check_ajax_referer( 'alezux_projects_nonce', 'nonce' );
		
		if ( ! current_user_can( 'manage_options' ) && ! is_user_logged_in() ) {
			wp_send_json_error( 'No autorizado.' );
		}

		$project_id = absint( $_POST['project_id'] );
		$manager = new Project_Manager();
		
		// Verificar if user can access (Admin or Customer)
		// ... (Simplificado por ahora asumimos Admin o Cliente dueño)

		$messages = $manager->get_project_messages( $project_id );
		$formatted_messages = [];

		foreach ( $messages as $msg ) {
			$sender = get_userdata( $msg->sender_id );
			$is_me = ( $msg->sender_id == get_current_user_id() );
			
			$formatted_messages[] = [
				'id'         => $msg->id,
				'content'    => wpautop( make_clickable( $msg->content ) ),
				'sender_name'=> $sender ? $sender->display_name : 'Usuario',
				'sender_avatar' => get_avatar_url( $msg->sender_id ),
				'is_me'      => $is_me,
				'time'       => date_i18n( 'H:i', strtotime( $msg->created_at ) ),
				'date'       => date_i18n( 'd M', strtotime( $msg->created_at ) ),
			];
		}

		wp_send_json_success( $formatted_messages );
	}

	public function ajax_send_project_message() {
		check_ajax_referer( 'alezux_projects_nonce', 'nonce' );

		$project_id = absint( $_POST['project_id'] );
		$content    = wp_kses_post( $_POST['content'] );
		$sender_id  = get_current_user_id();

		if ( empty( $content ) ) {
			wp_send_json_error( 'Mensaje vacío.' );
		}

		$manager = new Project_Manager();
		$msg_id = $manager->add_project_message( $project_id, $sender_id, $content );

		if ( $msg_id ) {
			wp_send_json_success( 'Mensaje enviado.' );
		} else {
			wp_send_json_error( 'Error al guardar mensaje.' );
		}
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
		
		// Meta Fields
		$design_url   = esc_url_raw( $_POST['design_url'] );
		$site_url     = esc_url_raw( $_POST['site_url'] );
		$access_user  = sanitize_text_field( $_POST['access_user'] );
		$access_pass  = sanitize_text_field( $_POST['access_pass'] );

		$manager = new Project_Manager();
		$manager->update_status( $project_id, $status, $current_step );

		// Update Meta
		if ( isset($_POST['design_url']) ) $manager->update_project_meta( $project_id, 'design_proposal_url', $design_url );
		if ( isset($_POST['site_url']) ) $manager->update_project_meta( $project_id, 'site_url', $site_url );
		if ( isset($_POST['access_user']) ) $manager->update_project_meta( $project_id, 'access_user', $access_user );
		if ( isset($_POST['access_pass']) ) $manager->update_project_meta( $project_id, 'access_pass', $access_pass );

		wp_send_json_success( 'Datos del proyecto actualizados.' );
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

	public function ajax_submit_rejection() {
		check_ajax_referer( 'alezux_projects_nonce', 'nonce' );

		$project_id = absint( $_POST['project_id'] );
		$feedback   = sanitize_textarea_field( $_POST['feedback'] );

		if ( empty( $feedback ) ) {
			wp_send_json_error( 'El mensaje no puede estar vacío.' );
		}
		
		$manager = new Project_Manager();
		$project = $manager->get_project( $project_id );

		if ( ! $project || $project->customer_id != get_current_user_id() ) {
			wp_send_json_error( 'No tienes permiso.' );
		}

		// Guardar Feedback en Meta
		$rejection_data = [
			'submitted_at' => current_time( 'mysql' ),
			'feedback'     => $feedback,
			'ip_address'   => $_SERVER['REMOTE_ADDR']
		];

		// Podríamos guardar un historial de feedbacks, pero por ahora sobrescribimos o añadimos uno nuevo
		// Para MVP guardamos el último feedback
		$manager->update_project_meta( $project_id, 'last_design_feedback', json_encode( $rejection_data ) );
		
		// Opcional: Cambiar estado a "revisión con cambios" si existiera, o dejarlo en design_review
		// Decisión: Mantener en design_review pero notificar admin

		do_action( 'alezux_project_design_feedback', $project_id, $feedback );

		wp_send_json_success( 'Tus comentarios han sido enviados al equipo.' );
	}

	public function check_install() {
		$installed_ver = get_option( 'alezux_projects_version' );
		$current_ver   = '1.0.1';

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
			ALEZUX_MEMBERS_VERSION . time() 
		);
		wp_enqueue_script( 
			'alezux-projects-js', 
			plugin_dir_url( __FILE__ ) . 'assets/js/projects.js', 
			[ 'jquery' ], 
			ALEZUX_MEMBERS_VERSION . time(), 
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
