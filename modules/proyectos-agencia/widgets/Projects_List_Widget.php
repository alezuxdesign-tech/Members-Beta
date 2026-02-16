<?php
namespace Alezux_Members\Modules\Proyectos_Agencia\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Alezux_Members\Modules\Proyectos_Agencia\Includes\Project_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Projects_List_Widget extends Widget_Base {

	public function get_name() {
		return 'alezux_projects_list';
	}

	public function get_title() {
		return esc_html__( 'Lista de Proyectos (Admin)', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-table';
	}

	public function get_categories() {
		return [ 'alezux-otros' ]; // O una categoría específica si se crea
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
			'detail_page_url',
			[
				'label' => esc_html__( 'URL Página Detalle', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => 'https://tudominio.com/detalle-proyecto',
				'description' => 'URL de la página donde está el widget de detalle. Se le añadirá ?project_id=123',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			echo '<div class="alezux-alert error">Acceso restringido a administradores.</div>';
			return;
		}

		$settings = $this->get_settings_for_display();
		$detail_url = ! empty( $settings['detail_page_url'] ) ? $settings['detail_page_url'] : '#';

		$manager = new Project_Manager();
		$projects = $manager->get_all_projects();
		
		// Obtener usuarios para el select del modal
		$users = get_users( [ 'role__in' => [ 'subscriber', 'customer', 'administrator' ], 'number' => 100 ] );
		?>
		
		<!-- Usamos las clases globales de Finanzas/App para consistencia visual -->
		<div class="alezux-finanzas-app alezux-projects-app">
			
			<!-- Cabecera Estándar -->
			<div class="alezux-table-header">
				<div class="alezux-header-left">
					<h3 class="alezux-table-title">Gestión de Proyectos</h3>
					<p class="alezux-table-desc">Administra los proyectos de desarrollo web de tus clientes.</p>
				</div>

				<div class="alezux-header-right alezux-filters-inline">
					<div class="alezux-filter-item">
						<button id="open-new-project-modal" class="alezux-marketing-btn primary">
							<i class="eicon-plus"></i> Nuevo Proyecto
						</button>
					</div>
				</div>
			</div>

			<!-- Grid Container -->
			<div class="alezux-projects-grid">
				<?php if ( empty( $projects ) ) : ?>
					<div class="alezux-empty-state">
						<i class="eicon-folder-o"></i>
						<h3>No hay proyectos aún</h3>
						<p>Comienza creando el primero para gestionar tus desarrollos.</p>
					</div>
				<?php else : ?>
					<?php foreach ( $projects as $project ) : ?>
						<?php 
						$user_info = get_userdata( $project->customer_id );
						$user_name = $user_info ? $user_info->display_name : 'Usuario Desconocido';
						$user_email = $user_info ? $user_info->user_email : '';
						
						// Calcular progreso basado en la fase (Simplificado)
						$progress = 0;
						switch($project->current_step) {
							case 'briefing': $progress = 10; break;
							case 'design_review': $progress = 40; break;
							case 'in_progress': $progress = 70; break;
							case 'completed': $progress = 100; break;
						}
						?>
						
						<div class="alezux-project-card" onclick="AlezuxProjects.openPanel(<?php echo esc_attr($project->id); ?>)">
							<div class="card-header">
								<span class="alezux-status-badge status-<?php echo esc_attr( $project->status ); ?>">
									<?php echo esc_html( ucfirst( $project->status ) ); ?>
								</span>
								<button class="card-action-btn" title="Opciones"><i class="eicon-ellipsis-h"></i></button>
							</div>
							
							<div class="card-body">
								<h4 class="project-name"><?php echo esc_html( $project->name ); ?></h4>
								
								<div class="client-info">
									<?php echo get_avatar( $project->customer_id, 32, '', '', ['class' => 'client-avatar'] ); ?>
									<div class="client-details">
										<span class="client-name"><?php echo esc_html( $user_name ); ?></span>
										<span class="client-role">Cliente</span>
									</div>
								</div>
							</div>

							<div class="card-footer">
								<div class="progress-section">
									<div class="progress-labels">
										<span>Progreso</span>
										<span><?php echo $progress; ?>%</span>
									</div>
									<div class="progress-bar">
										<div class="progress-fill" style="width: <?php echo $progress; ?>%;"></div>
									</div>
								</div>
								
								<div class="card-dates">
									<i class="eicon-calendar"></i> <?php echo date_i18n( 'M j, Y', strtotime( $project->created_at ) ); ?>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>

			<!-- MODAL NUEVO PROYECTO (Estilo Marketing) -->
			<div id="new-project-modal" class="alezux-modal">
				<div class="alezux-modal-content" style="max-width: 500px;">
					<span class="alezux-close-modal close-modal">&times;</span>
					<h3 style="margin-top:0; margin-bottom: 20px; color: #2d3748;">Crear Nuevo Proyecto</h3>
					
					<form id="create-project-form">
						<div class="form-group">
							<label>Nombre del Proyecto</label>
							<input type="text" name="project_name" class="alezux-input" required placeholder="Ej: E-commerce de Zapatos">
						</div>
						
						<div class="form-group">
							<label>Cliente Asignado</label>
							<select name="customer_id" required class="alezux-input">
								<option value="">Seleccionar Cliente...</option>
								<?php foreach ( $users as $user ) : ?>
									<option value="<?php echo esc_attr( $user->ID ); ?>">
										<?php echo esc_html( $user->display_name . ' (' . $user->user_email . ')' ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>

						<div class="form-actions" style="margin-top: 25px; text-align: right; display: flex; justify-content: flex-end; gap: 10px;">
							<button type="button" class="alezux-marketing-btn close-modal-btn" style="background: #e2e8f0; color: #4a5568; box-shadow: none;">Cancelar</button>
							<button type="submit" class="alezux-marketing-btn primary">
								<i class="eicon-plus"></i> Crear Proyecto
							</button>
						</div>
					</form>
				</div>
			</div>



			<!-- PANEL LATERAL (Off-Canvas) -->
			<div id="project-offcanvas-overlay" class="alezux-offcanvas-overlay"></div>
			<div id="project-offcanvas" class="alezux-offcanvas-panel">
				<div class="offcanvas-header">
					<h3 id="offcanvas-title">Cargando...</h3>
					<button class="close-offcanvas-btn"><i class="eicon-close"></i></button>
				</div>
				
				<div id="offcanvas-loading" style="text-align: center; padding: 50px; color: #a0aec0;">
					<i class="eicon-loading eicon-animation-spin" style="font-size: 30px;"></i>
					<p style="margin-top: 10px;">Cargando proyecto...</p>
				</div>

				<div id="offcanvas-content" style="display: none;">
					<!-- Contenido cargado vía AJAX -->
				</div>
			</div>

		</div>
		<?php
	}
}
