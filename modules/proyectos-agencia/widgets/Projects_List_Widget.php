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
		$users = get_users( [ 'role__in' => [ 'subscriber', 'customer', 'administrator' ], 'number' => 100 ] ); // Ajustar roles según necesidad

		?>
		<div class="alezux-projects-dashboard">
			<div class="alezux-projects-header">
				<h2>Gestión de Proyectos</h2>
				<button id="open-new-project-modal" class="alezux-btn alezux-btn-primary">
					<i class="eicon-plus"></i> Nuevo Proyecto
				</button>
			</div>

			<div class="alezux-projects-table-wrapper">
				<table class="alezux-projects-table">
					<thead>
						<tr>
							<th>ID</th>
							<th>Proyecto</th>
							<th>Cliente</th>
							<th>Estado</th>
							<th>Fase Actual</th>
							<th>Fecha</th>
							<th>Acciones</th>
						</tr>
					</thead>
					<tbody>
						<?php if ( empty( $projects ) ) : ?>
							<tr>
								<td colspan="7" style="text-align:center;">No hay proyectos creados.</td>
							</tr>
						<?php else : ?>
							<?php foreach ( $projects as $project ) : ?>
								<?php 
								$user_info = get_userdata( $project->customer_id );
								$user_name = $user_info ? $user_info->display_name : 'Usuario Desconocido';
								$project_url = add_query_arg( 'project_id', $project->id, $detail_url );
								?>
								<tr>
									<td>#<?php echo esc_html( $project->id ); ?></td>
									<td><strong><?php echo esc_html( $project->name ); ?></strong></td>
									<td>
										<div class="alezux-user-badge">
											<?php echo get_avatar( $project->customer_id, 24 ); ?>
											<span><?php echo esc_html( $user_name ); ?></span>
										</div>
									</td>
									<td><span class="alezux-status-badge status-<?php echo esc_attr( $project->status ); ?>"><?php echo esc_html( ucfirst( $project->status ) ); ?></span></td>
									<td><?php echo esc_html( ucfirst( $project->current_step ) ); ?></td>
									<td><?php echo date_i18n( get_option( 'date_format' ), strtotime( $project->created_at ) ); ?></td>
									<td>
										<a href="<?php echo esc_url( $project_url ); ?>" class="alezux-btn alezux-btn-sm alezux-btn-secondary">Gestionar</a>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
			</div>

			<!-- MODAL NUEVO PROYECTO -->
			<div id="new-project-modal" class="alezux-modal-overlay" style="display:none;">
				<div class="alezux-modal-content">
					<div class="alezux-modal-header">
						<h3>Crear Nuevo Proyecto</h3>
						<span class="close-modal">&times;</span>
					</div>
					<div class="alezux-modal-body">
						<form id="create-project-form">
							<div class="alezux-form-group">
								<label>Nombre del Proyecto</label>
								<input type="text" name="project_name" required placeholder="Ej: Rediseño Web Corporativo">
							</div>
							<div class="alezux-form-group">
								<label>Cliente Asignado</label>
								<select name="customer_id" required class="alezux-select-search">
									<option value="">Seleccionar Cliente...</option>
									<?php foreach ( $users as $user ) : ?>
										<option value="<?php echo esc_attr( $user->ID ); ?>">
											<?php echo esc_html( $user->display_name . ' (' . $user->user_email . ')' ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="alezux-modal-actions">
								<button type="button" class="alezux-btn alezux-btn-ghost close-modal-btn">Cancelar</button>
								<button type="submit" class="alezux-btn alezux-btn-primary">Crear Proyecto</button>
							</div>
						</form>
					</div>
				</div>
			</div>

		</div>
		<?php
	}
}
