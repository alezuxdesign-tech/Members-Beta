<?php
namespace Alezux_Members\Modules\Proyectos_Agencia;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Alezux_Members\Modules\Proyectos_Agencia\Includes\Project_Manager;

use function \add_action;
use function \is_admin;
use function \check_ajax_referer;
use function \current_user_can;
use function \wp_send_json_error;
use function \wp_send_json_success;
use function \absint;
use function \get_userdata;
use function \esc_attr;
use function \esc_html;
use function \esc_url;
use function \esc_url_raw;
use function \get_avatar;
use function \date_i18n;
use function \get_option;
use function \update_option;
use function \selected;
use function \is_user_logged_in;
use function \get_current_user_id;
use function \wpautop;
use function \make_clickable;
use function \get_avatar_url;
use function \wp_kses_post;
use function \sanitize_text_field;
use function \sanitize_textarea_field;
use function \sanitize_email;
use function \current_time;
use function \wp_handle_upload;
use function \do_action;
use function \wp_enqueue_style;
use function \wp_enqueue_script;
use function \wp_localize_script;
use function \plugin_dir_url;
use function \admin_url;
use function \wp_create_nonce;
use function \get_users;
use const \ABSPATH;

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
		add_action( 'wp_ajax_alezux_approve_logo', [ $this, 'ajax_approve_logo' ] ); // Fix: Hook Added
		add_action( 'wp_ajax_alezux_approve_design', [ $this, 'ajax_approve_design' ] );
		add_action( 'wp_ajax_alezux_approve_final', [ $this, 'ajax_approve_final' ] ); // Fix: Hook Added
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
		$start_date = isset($meta['project_start_date']) ? $meta['project_start_date'] : '';
		$end_date   = isset($meta['project_end_date']) ? $meta['project_end_date'] : '';

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
				<?php
				// Prepare Variables for the View
				$briefing = isset($meta['briefing_data']) ? json_decode($meta['briefing_data'], true) : null;
				$feedback = isset($meta['client_feedback']) ? $meta['client_feedback'] : '';
				?>
				<style>
					.alezux-project-detail-dashboard .phase-card {
						background: #f7fafc;
						border: 1px solid #e2e8f0;
						border-radius: 8px;
						padding: 15px;
						margin-bottom: 15px;
						transition: all 0.3s ease;
					}
					.alezux-project-detail-dashboard .phase-card.active {
						background: #fff;
						border-color: #4299e1;
						box-shadow: 0 4px 6px rgba(66, 153, 225, 0.1);
					}
					.alezux-project-detail-dashboard .phase-header {
						display: flex;
						justify-content: space-between;
						align-items: center;
						margin-bottom: 10px;
						font-size: 14px;
					}
					.alezux-project-detail-dashboard .phase-actions {
						margin-top: 10px;
					}
				</style>

				<div class="alezux-project-detail-dashboard">
					
					<div class="alezux-detail-grid">
						<div class="alezux-detail-card" style="margin-bottom: 20px;">
							<h3>Gestión de Fases</h3>
							
							<!-- FASE 1: Briefing -->
							<div class="phase-card <?php echo $project->current_step === 'briefing' ? 'active' : ''; ?>">
								<div class="phase-header">
									<strong>1. Briefing</strong>
									<?php if($project->status === 'briefing_completed' || !empty($briefing)): ?>
										<span style="color:#48bb78;"><i class="eicon-check-circle"></i> Completado</span>
									<?php else: ?>
										<span style="color:#ecc94b;"><i class="eicon-clock-o"></i> Pendiente</span>
									<?php endif; ?>
								</div>
								<div class="phase-actions">
									<?php if(!empty($briefing)): ?>
										<button onclick="jQuery('#briefing-details-container').slideToggle()" class="alezux-btn alezux-btn-sm alezux-btn-secondary" style="width:100%; text-align:center;">
											<i class="eicon-eye"></i> Ver Datos Briefing
										</button>
									<?php else: ?>
										<small>Esperando al cliente...</small>
									<?php endif; ?>
								</div>
							</div>

							<!-- FASE 2: Logo -->
							<div class="phase-card <?php echo strpos($project->current_step, 'logo') !== false ? 'active' : ''; ?>">
								<div class="phase-header">
									<strong>2. Logo</strong>
									<span><?php echo $project->current_step === 'logo_creation' ? 'En Diseño' : ''; ?></span>
								</div>
								<div class="phase-actions">
									<div class="alezux-alert info" style="font-size:12px; padding:5px;">
										Gestionar subida de propuestas desde el formulario manual inferior por ahora.
									</div>
								</div>
							</div>

							<!-- FASE 3: Diseño -->
							<div class="phase-card <?php echo strpos($project->current_step, 'design') !== false ? 'active' : ''; ?>">
								<div class="phase-header">
									<strong>3. Diseño Web</strong>
									<span><?php echo $project->current_step === 'design_creation' ? 'En Producción' : ''; ?></span>
								</div>
							</div>

							<!-- FASE 4: Desarrollo -->
							<div class="phase-card <?php echo $project->current_step === 'in_progress' ? 'active' : ''; ?>">
								<div class="phase-header">
									<strong>4. Desarrollo</strong>
								</div>
							</div>

							<!-- FASE GLOBAL UPDATE -->
							<hr>
							<h4>Control Manual del Proyecto</h4>
							<form id="update-project-status-form">
								<input type="hidden" name="project_id" value="<?php echo esc_attr( $project->id ); ?>">
								
								<div class="alezux-form-group">
									<label>Fase Actual (Override)</label>
									<select name="current_step" class="alezux-select alezux-input">
										<option value="briefing" <?php selected( $project->current_step, 'briefing' ); ?>>1. Briefing</option>
										<option value="logo_creation" <?php selected( $project->current_step, 'logo_creation' ); ?>>2. Creación Logo</option>
										<option value="logo_review" <?php selected( $project->current_step, 'logo_review' ); ?>>2.1. Revisión Logo</option>
										<option value="design_creation" <?php selected( $project->current_step, 'design_creation' ); ?>>3. Creación Diseño</option>
										<option value="design_review" <?php selected( $project->current_step, 'design_review' ); ?>>3.1. Revisión Diseño</option>
										<option value="in_progress" <?php selected( $project->current_step, 'in_progress' ); ?>>4. Desarrollo</option>
										<option value="optimization" <?php selected( $project->current_step, 'optimization' ); ?>>5. Optimización</option>
										<option value="final_review" <?php selected( $project->current_step, 'final_review' ); ?>>6. Revisión Final</option>
										<option value="completed" <?php selected( $project->current_step, 'completed' ); ?>>7. Completado</option>
									</select>
								</div>

								<div class="alezux-form-group">
									<label>Estado General</label>
									<select name="status" class="alezux-select alezux-input">
										<option value="pending" <?php selected( $project->status, 'pending' ); ?>>Pendiente</option>
										<option value="briefing_completed" <?php selected( $project->status, 'briefing_completed' ); ?>>Briefing OK</option>
										<option value="in_progress" <?php selected( $project->status, 'in_progress' ); ?>>En Progreso</option>
										<option value="completed" <?php selected( $project->status, 'completed' ); ?>>Finalizado</option>
									</select>
								</div>

								<div class="alezux-form-group">
									<label>URL Propuesta de Diseño</label>
									<input type="url" name="design_url" class="alezux-input" value="<?php echo esc_url( $design_url ); ?>" placeholder="https://...">
								</div>

								<div class="alezux-form-group">
									<label>URL del Sitio (Staging)</label>
									<input type="url" name="site_url" class="alezux-input" value="<?php echo isset($meta['site_url']) ? esc_url($meta['site_url']) : ''; ?>" placeholder="https://...">
								</div>

								<div style="display:flex; gap:10px;">
									<div style="flex:1;">
										<label>Usuario WP</label>
										<input type="text" name="access_user" class="alezux-input" value="<?php echo isset($meta['access_user']) ? esc_attr($meta['access_user']) : ''; ?>">
									</div>
									<div style="flex:1;">
										<label>Pass WP</label>
										<input type="text" name="access_pass" class="alezux-input" value="<?php echo isset($meta['access_pass']) ? esc_attr($meta['access_pass']) : ''; ?>">
									</div>
								</div>

								<br>
								<button type="submit" class="alezux-marketing-btn primary" style="width:100%;">Actualizar Estado</button>
							</form>
						</div>

						<!-- Columna: Información Detallada -->
						<div class="alezux-detail-card">
							
							<!-- Data Briefing Hidden by Default -->
							<div id="briefing-details-container" style="display:none; border:1px solid #eee; padding:15px; border-radius:5px; margin-bottom:20px;">
								<h4>Datos del Briefing</h4>
								<?php if ( ! empty( $briefing ) ) : 
									$labels_map = [
										'brand_name'          => 'Marca Comercial',
										'legal_name'          => 'Razón Social',
										'tax_id'              => 'CIF / NIT',
										'fiscal_address'      => 'Dirección Fiscal',
										'commercial_registry' => 'Registro Mercantil',
										'jurisdiction'        => 'Jurisdicción',
										'phone'               => 'Teléfono',
										'whatsapp'            => 'WhatsApp',
										'contact_email'       => 'Email Contacto',
										'privacy_email'       => 'Email Privacidad',
										'dpo_email'           => 'Email DPO',
										'website_url'         => 'Sitio Web',
										'business_activity'   => 'Actividad',
										'business_sectors'    => 'Sectores',
										'slogan'              => 'Slogan',
										'colors'              => 'Colores',
										'business_desc'       => 'Descripción',
										'submitted_at'        => 'Enviado el'
									];
								?>
									<ul class="alezux-data-list" style="list-style:none; padding:0;">
										<?php foreach ( $briefing as $key => $val ) : 
											if ( empty($val) ) continue; // Skip empty fields
											$label = isset($labels_map[$key]) ? $labels_map[$key] : ucfirst( str_replace( '_', ' ', $key ) );
										?>
											<li style="margin-bottom:8px; font-size:13px;">
												<strong style="color:#718096;"><?php echo esc_html( $label ); ?>:</strong>
												
												<?php if ( 'brand_colors' === $key && is_array( $val ) ) : ?>
													<div style="display:flex; gap:5px; margin-top:5px;">
														<?php foreach ( $val as $color ) : ?>
															<span style="background:<?php echo esc_attr($color); ?>; width:20px; height:20px; display:inline-block; border:1px solid #ccc; border-radius:4px;" title="<?php echo esc_attr($color); ?>"></span>
														<?php endforeach; ?>
													</div>
												<?php elseif ( 'logo_url' === $key ) : ?>
													<br><a href="<?php echo esc_url( $val ); ?>" target="_blank" style="color:#e11d48; text-decoration:underline;">Ver Logo Subido <i class="eicon-external-link-square"></i></a>
												<?php else : ?>
													<span><?php echo esc_html( is_array($val) ? implode(', ', $val) : $val ); ?></span>
												<?php endif; ?>
											</li>
										<?php endforeach; ?>
									</ul>
								<?php else : ?>
									<p class="description">El cliente aún no ha enviado el briefing.</p>
								<?php endif; ?>
							</div>

							<h4>Feedback Cliente</h4>
							<div class="alezux-feedback-box" style="background:#fff3cd; padding:15px; border-left:4px solid #ecc94b; border-radius:4px;">
								<p style="margin:0; font-style:italic;">
									<?php echo ! empty( $feedback ) ? wp_kses_post( $feedback ) : 'Sin feedback reciente.'; ?>
								</p>
							</div>

							<!-- Previous Approvals Log -->
							<div style="margin-top:20px;">
								<h4>Historial de Aprobaciones</h4>
								<?php
									$logo_approval  = isset($meta['logo_approval_data']) ? json_decode($meta['logo_approval_data'], true) : null;
									$design_approval= isset($meta['approval_data']) ? json_decode($meta['approval_data'], true) : null;
									$final_approval = isset($meta['final_approval_data']) ? json_decode($meta['final_approval_data'], true) : null;
								?>
								<ul style="font-size:12px; color:#718096;">
									<li><strong>Logo:</strong> <?php echo $logo_approval ? '✅ ' . date_i18n('d M H:i', strtotime($logo_approval['approved_at'])) : 'Pendiente'; ?></li>
									<li><strong>Diseño:</strong> <?php echo $design_approval ? '✅ ' . date_i18n('d M H:i', strtotime($design_approval['approved_at'])) : 'Pendiente'; ?></li>
									<li><strong>Final:</strong> <?php echo $final_approval ? '✅ ' . date_i18n('d M H:i', strtotime($final_approval['approved_at'])) : 'Pendiente'; ?></li>
								</ul>
							</div>
						</div>
					</div>
				</div>
                
                <script>
                // Re-bind form submission for the new form
                jQuery(document).ready(function($){
                    $('#update-project-status-form').on('submit', function(e){
                        e.preventDefault();
                        var $form = $(this);
                        var $btn = $form.find('button[type="submit"]');
                        var originalText = $btn.text();
                        $btn.prop('disabled', true).text('Guardando...');

                        $.post(AlezuxProjects.ajaxurl, {
                            action: 'alezux_update_project',
                            nonce: AlezuxProjects.nonce,
                            project_id: $form.find('input[name="project_id"]').val(),
                            status: $form.find('select[name="status"]').val(),
                            current_step: $form.find('select[name="current_step"]').val(),
                            design_url: $form.find('input[name="design_url"]').val(),
                            site_url: $form.find('input[name="site_url"]').val(),
                            access_user: $form.find('input[name="access_user"]').val(),
                            access_pass: $form.find('input[name="access_pass"]').val()
                        }, function(response) {
                             if(response.success) {
                                  alert(response.data);
                                  // Refresh Panel by clicking the card again or just reloading
                                  // AlezuxProjects.openPanel($form.find('input[name="project_id"]').val());
                             } else {
                                  alert('Error: ' + response.data);
                             }
                        }).always(function(){
                            $btn.prop('disabled', false).text(originalText);
                        });
                    });
                });
                </script>
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

		// Mark messages as read (if I am reading them)
		$manager->mark_messages_read( $project_id, get_current_user_id() );

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
				'is_read'    => (bool) $msg->is_read,
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
			// Guardar fechas de inicio y fin
			$start_date = isset( $_POST['project_start_date'] ) ? sanitize_text_field( $_POST['project_start_date'] ) : '';
			$end_date   = isset( $_POST['project_end_date'] ) ? sanitize_text_field( $_POST['project_end_date'] ) : '';

			if ( ! empty( $start_date ) ) {
				$manager->update_project_meta( $project_id, 'project_start_date', $start_date );
			}
			if ( ! empty( $end_date ) ) {
				$manager->update_project_meta( $project_id, 'project_end_date', $end_date );
			}

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

		// DEBUG LOGGING
		error_log( "Alezux Projects Debug: Update Project ID: $project_id" );
		error_log( "Status: $status, Step: $current_step" );
		error_log( "Meta: Site=$site_url, User=$access_user" );

		$manager = new Project_Manager();
		$update_result = $manager->update_status( $project_id, $status, $current_step );
		error_log( "Update Status Result: " . ($update_result === false ? 'FALSE' : $update_result) );

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
			'brand_name'          => sanitize_text_field( $_POST['brand_name'] ),
			'legal_name'          => sanitize_text_field( $_POST['legal_name'] ),
			'tax_id'              => sanitize_text_field( $_POST['tax_id'] ),
			'fiscal_address'      => sanitize_textarea_field( $_POST['fiscal_address'] ),
			'commercial_registry' => sanitize_text_field( $_POST['commercial_registry'] ),
			'jurisdiction'        => sanitize_text_field( $_POST['jurisdiction'] ),
			'phone'               => sanitize_text_field( $_POST['phone'] ),
			'whatsapp'            => sanitize_text_field( $_POST['whatsapp'] ),
			'contact_email'       => sanitize_email( $_POST['contact_email'] ),
			'privacy_email'       => sanitize_email( $_POST['privacy_email'] ),
			'dpo_email'           => sanitize_email( $_POST['dpo_email'] ),
			'website_url'         => esc_url_raw( $_POST['website_url'] ),
			'business_activity'   => sanitize_textarea_field( $_POST['business_activity'] ),
			'business_sectors'    => sanitize_text_field( $_POST['business_sectors'] ),
			'logo_details'        => sanitize_textarea_field( $_POST['logo_details'] ),
			'has_logo'            => sanitize_text_field( $_POST['has_logo'] ),
			// Legacy/Optional
			'slogan'              => sanitize_text_field( $_POST['slogan'] ),
			'business_desc'       => sanitize_textarea_field( $_POST['business_desc'] ), 
			'submitted_at'        => current_time( 'mysql' )
		];

		// Handle Logo Upload
		if ( ! empty( $_FILES['logo_file']['name'] ) ) {
			if ( ! function_exists( 'wp_handle_upload' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}
			$uploadedfile = $_FILES['logo_file'];
			$upload_overrides = array( 'test_form' => false );
			$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
			
			if ( $movefile && ! isset( $movefile['error'] ) ) {
				$briefing_data['logo_url'] = $movefile['url'];
			}
		}

		// Handle Colors (JSON Array)
		if ( ! empty( $_POST['brand_colors'] ) ) {
			$colors_json = stripslashes( $_POST['brand_colors'] );
			$decoded = json_decode( $colors_json );
			if ( is_array( $decoded ) ) {
				$briefing_data['brand_colors'] = array_map( 'sanitize_hex_color', $decoded );
			}
		}

		// Guardar Meta
		$manager->update_project_meta( $project_id, 'briefing_data', json_encode( $briefing_data ) );
		
		// Update needs_logo_design meta
		$needs_logo = ( isset($_POST['has_logo']) && $_POST['has_logo'] === 'no' ) ? 'yes' : 'no';
		$manager->update_project_meta( $project_id, 'needs_logo_design', $needs_logo );

		// Actualizar Estado
		// Si necesita logo, el siguiente paso es logo_creation
		// Si NO necesita logo (ya lo tiene), el siguiente paso es design_creation
		$next_step = ($needs_logo === 'yes') ? 'logo_creation' : 'design_creation';
		
		$manager->update_status( $project_id, 'briefing_completed', $next_step ); 

		wp_send_json_success( 'Briefing enviado correctamente.' );
	}

	public function ajax_approve_logo() {
		check_ajax_referer( 'alezux_projects_nonce', 'nonce' );

		$project_id = absint( $_POST['project_id'] );
		
		$manager = new Project_Manager();
		$project = $manager->get_project( $project_id );

		if ( ! $project || $project->customer_id != get_current_user_id() ) {
			wp_send_json_error( 'No tienes permiso.' );
		}

		// Marcar aprobación de logo
		$approval_data = [
			'approved_at' => current_time( 'mysql' ),
			'ip_address'  => $_SERVER['REMOTE_ADDR']
		];
		$manager->update_project_meta( $project_id, 'logo_approval_data', json_encode( $approval_data ) );
		
		// Avanzar a Creación de Diseño
		$manager->update_status( $project_id, 'design_creation', 'design_creation' );

		wp_send_json_success( 'Logo aprobado. ¡Comenzamos con el diseño web!' );
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

	public function ajax_approve_final() {
		check_ajax_referer( 'alezux_projects_nonce', 'nonce' );

		$project_id = absint( $_POST['project_id'] );
		
		$manager = new Project_Manager();
		$project = $manager->get_project( $project_id );

		if ( ! $project || $project->customer_id != get_current_user_id() ) {
			wp_send_json_error( 'No tienes permiso.' );
		}

		// Marcar aprobación final
		$approval_data = [
			'approved_at' => current_time( 'mysql' ),
			'ip_address'  => $_SERVER['REMOTE_ADDR']
		];

		$manager->update_project_meta( $project_id, 'final_approval_data', json_encode( $approval_data ) );
		
		// Finalizar Proyecto
		$manager->update_status( $project_id, 'completed', 'completed' );

		wp_send_json_success( '¡Felicidades! Tu proyecto ha sido aprobado y finalizado.' );
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

		// Flatpickr (Date Range Picker)
		wp_enqueue_style( 'flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css', [], '4.6.13' );
		wp_enqueue_script( 'flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr', [], '4.6.13', true );
		wp_enqueue_script( 'flatpickr-es', 'https://npmcdn.com/flatpickr/dist/l10n/es.js', ['flatpickr'], '4.6.13', true );

		wp_enqueue_script( 
			'alezux-projects-js', 
			plugin_dir_url( __FILE__ ) . 'assets/js/projects.js', 
			[ 'jquery', 'flatpickr' ], 
			ALEZUX_MEMBERS_VERSION . time() . '_v2', 
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
