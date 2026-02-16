<?php
namespace Alezux_Members\Modules\Proyectos_Agencia\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Alezux_Members\Modules\Proyectos_Agencia\Includes\Project_Manager;

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
		<div class="alezux-project-detail-dashboard">
			<div class="alezux-detail-header">
				<h2><?php echo esc_html( $project->name ); ?></h2>
				<span class="alezux-status-badge status-<?php echo esc_attr( $project->status ); ?>">
					<?php echo esc_html( ucfirst( $project->status ) ); ?>
				</span>
			</div>

			<div class="alezux-detail-grid">
				<!-- Columna Izquierda: Control de Estado -->
				<div class="alezux-detail-card">
					<h3>Gestión de Estado</h3>
					<form id="update-project-status-form">
						<input type="hidden" name="project_id" value="<?php echo esc_attr( $project->id ); ?>">
						
						<div class="alezux-form-group">
							<label>Fase Actual</label>
							<select name="current_step" class="alezux-select">
								<option value="briefing" <?php selected( $project->current_step, 'briefing' ); ?>>1. Briefing</option>
								<option value="design_review" <?php selected( $project->current_step, 'design_review' ); ?>>2. Revisión Diseño</option>
								<option value="in_progress" <?php selected( $project->current_step, 'in_progress' ); ?>>3. Desarrollo</option>
								<option value="completed" <?php selected( $project->current_step, 'completed' ); ?>>4. Completado</option>
							</select>
						</div>

						<div class="alezux-form-group">
							<label>Estado General</label>
							<select name="status" class="alezux-select">
								<option value="pending" <?php selected( $project->status, 'pending' ); ?>>Pendiente</option>
								<option value="briefing_completed" <?php selected( $project->status, 'briefing_completed' ); ?>>Briefing Completado</option>
								<option value="design_review" <?php selected( $project->status, 'design_review' ); ?>>En Revisión</option>
								<option value="approved" <?php selected( $project->status, 'approved' ); ?>>Aprobado</option>
								<option value="in_progress" <?php selected( $project->status, 'in_progress' ); ?>>En Progreso</option>
								<option value="completed" <?php selected( $project->status, 'completed' ); ?>>Finalizado</option>
							</select>
						</div>

						<div class="alezux-form-group">
							<label>URL Propuesta de Diseño</label>
							<input type="url" name="design_url" value="<?php echo esc_attr( $params_meta['design_url'] ); ?>" placeholder="https://...">
							<small>Enlace a la imagen o PDF que verá el cliente.</small>
						</div>

						<button type="submit" class="alezux-btn alezux-btn-primary">Actualizar Proyecto</button>
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
								$label = isset($labels_map[$key]) ? $labels_map[$key] : ucfirst( str_replace( '_', ' ', $key ) );
							?>
								<li>
									<strong><?php echo esc_html( $label ); ?>:</strong>
									<span><?php echo esc_html( is_array($val) ? implode(', ', $val) : $val ); ?></span>
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
