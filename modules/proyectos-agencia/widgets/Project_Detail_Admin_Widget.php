<?php
namespace Alezux_Members\Modules\Proyectos_Agencia\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Alezux_Members\Modules\Proyectos_Agencia\Includes\Project_Manager;

use function \esc_html__;
use function \esc_html;
use function \esc_attr;
use function \selected;
use function \get_userdata;
use function \current_user_can;
use function \get_avatar;
use function \wp_kses_post;
use function \absint;
use function \esc_url;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Project_Detail_Admin_Widget extends Widget_Base {

	public function get_name() {
		return 'alezux_project_detail_admin';
	}

	public function get_title() {
		return esc_html__( 'Detalle Proyecto (Admin)', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-edit';
	}

	public function get_categories() {
		return [ 'alezux-otros' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Configuración', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'info_msg',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => 'Este widget lee automáticamente el ID del proyecto de la URL (?project_id=NB).',
			]
		);
		$this->end_controls_section();
	}

	protected function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			echo '<div class="alezux-alert error">Acceso restringido a administradores.</div>';
			return;
		}

		$project_id = isset( $_GET['project_id'] ) ? absint( $_GET['project_id'] ) : 0;
		$manager = new Project_Manager();
		$project = $manager->get_project( $project_id );

		if ( ! $project ) {
			echo '<div class="alezux-alert warning">Proyecto no encontrado o ID no válido.</div>';
			return;
		}

		$user_info = get_userdata( $project->customer_id );
		$params_meta = [
			'briefing_data' => $manager->get_project_meta( $project->id, 'briefing_data' ),
			'design_url'    => $manager->get_project_meta( $project->id, 'design_proposal_url' ),
			'feedback'      => $manager->get_project_meta( $project->id, 'client_feedback' )
		];

		// Decodificar JSON del briefing si existe
		$briefing = json_decode( $params_meta['briefing_data'], true );

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
			<div class="alezux-detail-header">
				<h2><?php echo esc_html( $project->name ); ?></h2>
				<span class="alezux-status-badge status-<?php echo esc_attr( $project->status ); ?>">
					<?php echo esc_html( ucfirst( $project->status ) ); ?>
				</span>
			</div>

			<div class="alezux-detail-grid">
				<div class="alezux-detail-card">
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
								<a href="#briefing-data" class="alezux-btn alezux-btn-sm alezux-btn-secondary" style="width:100%; text-align:center;">Ver Datos Abajo</a>
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
							<button class="alezux-btn alezux-btn-sm alezux-btn-primary" style="width:100%;" onclick="alert('Funcionalidad de subida rápida en desarrollo. Por ahora usa la edición manual.');">
								<i class="eicon-upload"></i> Subir Propuestas
							</button>
						</div>
					</div>

					<!-- FASE GLOBAL UPDATE -->
					<hr>
					<h4>Control Manual</h4>
					<form id="update-project-status-form">
						<input type="hidden" name="project_id" value="<?php echo esc_attr( $project->id ); ?>">
						
						<div class="alezux-form-group">
							<label>Fase Actual (Override)</label>
							<select name="current_step" class="alezux-select">
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
							<select name="status" class="alezux-select">
								<option value="pending" <?php selected( $project->status, 'pending' ); ?>>Pendiente</option>
								<option value="briefing_completed" <?php selected( $project->status, 'briefing_completed' ); ?>>Briefing OK</option>
								<option value="in_progress" <?php selected( $project->status, 'in_progress' ); ?>>En Progreso</option>
								<option value="completed" <?php selected( $project->status, 'completed' ); ?>>Finalizado</option>
							</select>
						</div>

						<div class="alezux-form-group">
							<label>URL Propuesta de Diseño</label>
							<input type="url" name="design_url" value="<?php echo esc_attr( $params_meta['design_url'] ); ?>" placeholder="https://...">
						</div>

						<button type="submit" class="alezux-btn alezux-btn-primary">Actualizar Estado</button>
					</form>
				</div>

				<!-- Columna Derecha: Información Cliente -->
				<div class="alezux-detail-card">
					<h3>Información del Cliente</h3>
					<div class="alezux-user-info-display">
						<?php echo get_avatar( $project->customer_id, 48 ); ?>
						<div>
							<strong><?php echo esc_html( $user_info ? $user_info->display_name : 'N/A' ); ?></strong><br>
							<small><?php echo esc_html( $user_info ? $user_info->user_email : 'N/A' ); ?></small>
						</div>
					</div>

					<hr>

					<h4>Datos del Briefing</h4>
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
						<ul class="alezux-data-list">
							<?php foreach ( $briefing as $key => $val ) : 
								if ( empty($val) ) continue; // Skip empty fields

								$label = isset($labels_map[$key]) ? $labels_map[$key] : ucfirst( str_replace( '_', ' ', $key ) );
							?>
								<li>
									<strong><?php echo esc_html( $label ); ?>:</strong>
									
									<?php if ( 'brand_colors' === $key && is_array( $val ) ) : ?>
										<div style="display:flex; gap:5px; margin-top:5px;">
											<?php foreach ( $val as $color ) : ?>
												<span style="background:<?php echo esc_attr($color); ?>; width:20px; height:20px; display:inline-block; border:1px solid #ccc; border-radius:4px;" title="<?php echo esc_attr($color); ?>"></span>
												<span style="font-size:12px; color:#aaa;"><?php echo esc_html($color); ?></span>
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

					<hr>

					<h4>Feedback Cliente</h4>
					<div class="alezux-feedback-box">
						<?php echo ! empty( $params_meta['feedback'] ) ? wp_kses_post( $params_meta['feedback'] ) : 'Sin feedback aún.'; ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
