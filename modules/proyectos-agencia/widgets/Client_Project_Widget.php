<?php
namespace Alezux_Members\Modules\Proyectos_Agencia\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Alezux_Members\Modules\Proyectos_Agencia\Includes\Project_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Client_Project_Widget extends Widget_Base {

	public function get_name() {
		return 'alezux_client_project';
	}

	public function get_title() {
		return esc_html__( 'Mi Proyecto (Cliente)', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-code';
	}

	public function get_categories() {
		return [ 'alezux-otros' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Textos Personalizados', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		
		$this->add_control(
			'no_project_msg',
			[
				'label' => esc_html__( 'Mensaje Sin Proyecto', 'alezux-members' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => 'No tienes ningún proyecto activo en este momento.',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		if ( ! is_user_logged_in() ) {
			echo '<div class="alezux-alert info">Inicia sesión para ver tu proyecto.</div>';
			return;
		}

		$user_id = get_current_user_id();
		$manager = new Project_Manager();
		
		// Obtener el proyecto más reciente del usuario
		$projects = $manager->get_projects_by_user( $user_id );
		
		if ( empty( $projects ) ) {
			$settings = $this->get_settings_for_display();
			echo '<div class="alezux-no-project-box">';
			echo '<i class="eicon-folder-o"></i>';
			echo '<p>' . esc_html( $settings['no_project_msg'] ) . '</p>';
			echo '</div>';
			return;
		}

		// Tomamos el primer proyecto activo
		$project = $projects[0];
		
		// Renderizar contenedor principal
		echo '<div class="alezux-client-project-dashboard">';
		
		// Header del Proyecto
		$this->render_header( $project );

		// Cuerpo Variable según el Estado
		echo '<div class="alezux-project-body">';
		
		switch ( $project->current_step ) {
			case 'briefing':
				$this->render_briefing_step( $project );
				break;
			case 'design_review':
				$this->render_design_review_step( $project, $manager );
				break;
			case 'in_progress':
				$this->render_progress_step( $project );
				break;
			case 'completed':
				$this->render_completed_step( $project );
				break;
			default:
				echo '<p>Estado del proyecto desconocido.</p>';
		}
		
		echo '</div>'; // .alezux-project-body
		echo '</div>'; // .alezux-client-project-dashboard
	}

	private function render_header( $project ) {
		?>
		<div class="alezux-cp-header">
			<div class="cp-title">
				<small>PROYECTO ACTIVO</small>
				<h2><?php echo esc_html( $project->name ); ?></h2>
			</div>
			<div class="cp-status">
				<span class="alezux-status-pill status-<?php echo esc_attr( $project->status ); ?>">
					<?php echo esc_html( $this->get_status_label( $project->status ) ); ?>
				</span>
			</div>
		</div>
		<?php
	}

	private function render_briefing_step( $project ) {
		?>
		<div class="alezux-step-container">
			<div class="step-intro">
				<h3>👋 ¡Hola! Comencemos tu proyecto.</h3>
				<p>Para empezar, necesitamos conocer los detalles de tu marca. Por favor completa este formulario.</p>
			</div>
			
			<form id="client-briefing-form" class="alezux-briefing-form">
				<input type="hidden" name="project_id" value="<?php echo esc_attr( $project->id ); ?>">
				
				<div class="form-section">
					<h4>Información Básica</h4>
					<div class="alezux-form-group">
						<label>Nombre de tu Marca / Empresa</label>
						<input type="text" name="brand_name" required placeholder="Ej: TechSolutions C.A.">
					</div>
					<div class="alezux-form-group">
						<label>Slogan (Opcional)</label>
						<input type="text" name="slogan" placeholder="Ej: Innovación a tu alcance">
					</div>
				</div>

				<div class="form-section">
					<h4>Identidad Visual</h4>
					<div class="alezux-form-group">
						<label>¿Tienes colores corporativos? (Códigos HEX)</label>
						<input type="text" name="colors" placeholder="#FF5733, #333333">
					</div>
					<div class="alezux-form-group">
						<label>Describe tu negocio y público objetivo</label>
						<textarea name="business_desc" rows="4" required></textarea>
					</div>
				</div>

				<div class="form-section">
					<p><small>Al enviar este formulario, notificaremos al equipo para iniciar el diseño.</small></p>
					<button type="submit" class="alezux-btn alezux-btn-primary alezux-btn-lg">
						Enviar Briefing <i class="eicon-arrow-right"></i>
					</button>
				</div>
			</form>
		</div>
		<?php
	}

	private function render_design_review_step( $project, $manager ) {
		$design_url = $manager->get_project_meta( $project->id, 'design_proposal_url' );
		?>
		<div class="alezux-step-container center-text">
			<div class="step-intro">
				<h3>✨ Tu Diseño está Listo</h3>
				<p>Hemos creado una propuesta para ti. Revísala y aprueba para continuar con el desarrollo.</p>
			</div>

			<div class="design-preview-box">
				<?php if ( $design_url ) : ?>
					<a href="<?php echo esc_url( $design_url ); ?>" target="_blank" class="design-preview-link">
						<img src="<?php echo esc_url( $design_url ); ?>" alt="Propuesta de Diseño">
					</a>
					<a href="<?php echo esc_url( $design_url ); ?>" target="_blank" class="alezux-btn alezux-btn-secondary download-btn">
						<i class="eicon-download-bold"></i> Ver Diseño Completo
					</a>
				<?php else : ?>
					<div class="aleuz-alert warning">La imagen de la propuesta no se encuentra disponible.</div>
				<?php endif; ?>
			</div>

			<div class="approval-actions">
				<button id="btn-approve-design" data-id="<?php echo esc_attr( $project->id ); ?>" class="alezux-btn alezux-btn-success alezux-btn-block">
					<i class="eicon-check-circle-o"></i> Aprobar Diseño y Comenzar Desarrollo
				</button>
				
				<button id="btn-reject-modal-trigger" class="alezux-btn alezux-btn-ghost alezux-btn-sm">
					Solicitar Cambios / Rechazar
				</button>
			</div>

			<!-- Modal Rechazo (Hidden) -->
			<div id="reject-modal" style="display:none;" class="alezux-mini-modal">
				<h4>Solicitud de Cambios</h4>
				<textarea id="reject-feedback" placeholder="Describe qué cambios necesitas..."></textarea>
				<button id="btn-submit-rejection" data-id="<?php echo esc_attr( $project->id ); ?>" class="alezux-btn alezux-btn-primary alezux-btn-sm">Enviar Solicitud</button>
			</div>
		</div>
		<?php
	}

	private function render_progress_step( $project ) {
		?>
		<div class="alezux-step-container">
			<div class="step-intro">
				<h3>🏗️ Construyendo tu Sitio</h3>
				<p>Tu diseño ha sido aprobado. Nuestro equipo está trabajando en el código.</p>
			</div>

			<div class="project-timeline">
				<div class="timeline-item completed">
					<div class="point"></div>
					<div class="content">
						<strong>Briefing Recibido</strong>
						<span>Información recopilada</span>
					</div>
				</div>
				<div class="timeline-item completed">
					<div class="point"></div>
					<div class="content">
						<strong>Diseño Aprobado</strong>
						<span>Prototipo validado</span>
					</div>
				</div>
				<div class="timeline-item active">
					<div class="point pulse"></div>
					<div class="content">
						<strong>Desarrollo & Montaje</strong>
						<span>Estamos programando tu sitio web...</span>
					</div>
				</div>
				<div class="timeline-item">
					<div class="point"></div>
					<div class="content">
						<strong>Revisión Final</strong>
						<span>Entrega y Capacitación</span>
					</div>
				</div>
			</div>
			
			<div class="dev-note">
				<i class="eicon-info-circle-o"></i> Te notificaremos por correo cuando el sitio esté listo para revisión.
			</div>
		</div>
		<?php
	}

	private function render_completed_step( $project ) {
		?>
		<div class="alezux-step-container center-text">
			<div class="success-icon">🎉</div>
			<h3>¡Proyecto Finalizado!</h3>
			<p>Gracias por confiar en nosotros. Tu sitio web está en línea y funcionando.</p>
			<a href="#" class="alezux-btn alezux-btn-primary">Ver Documentación de Entrega</a>
		</div>
		<?php
	}

	private function get_status_label( $status ) {
		$labels = [
			'pending' => 'Pendiente',
			'briefing_completed' => 'Analizando Datos',
			'design_review' => 'Esperando Aprobación',
			'approved' => 'Aprobado',
			'in_progress' => 'En Desarrollo',
			'completed' => 'Finalizado'
		];
		return isset( $labels[$status] ) ? $labels[$status] : ucfirst( $status );
	}
}
