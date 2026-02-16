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

			<!-- Tabla Container -->
			<div class="alezux-table-wrapper">
				<table class="alezux-finanzas-table alezux-projects-table">
					<thead>
						<tr>
							<th style="width: 5%;">ID</th>
							<th style="width: 25%;">Proyecto</th>
							<th style="width: 25%;">Cliente</th>
							<th style="width: 15%;">Estado</th>
							<th style="width: 15%;">Fase Actual</th>
							<th style="width: 15%;">Acciones</th>
						</tr>
					</thead>
					<tbody>
						<?php if ( empty( $projects ) ) : ?>
							<tr>
								<td colspan="6" style="text-align:center; padding: 30px;">
									<div style="color: #a0aec0;">
										<i class="eicon-folder-o" style="font-size: 24px; margin-bottom: 10px; display: block;"></i>
										No hay proyectos creados aún.
									</div>
								</td>
							</tr>
						<?php else : ?>
							<?php foreach ( $projects as $project ) : ?>
								<?php 
								$user_info = get_userdata( $project->customer_id );
								$user_name = $user_info ? $user_info->display_name : 'Usuario Desconocido';
								$user_email = $user_info ? $user_info->user_email : '';
								$project_url = add_query_arg( 'project_id', $project->id, $detail_url );
								?>
								<tr>
									<td><span style="color: #718096; font-family: monospace;">#<?php echo esc_html( $project->id ); ?></span></td>
									<td>
										<strong style="color: #fff; font-size: 14px;"><?php echo esc_html( $project->name ); ?></strong>
										<small style="display:block; color: #718096; font-size: 11px; margin-top: 4px;">
											Creado: <?php echo date_i18n( get_option( 'date_format' ), strtotime( $project->created_at ) ); ?>
										</small>
									</td>
									<td>
										<div class="alezux-user-badge" style="display:flex; align-items:center; gap:10px;">
											<?php echo get_avatar( $project->customer_id, 32, '', '', ['class' => 'rounded-circle'] ); ?>
											<div style="line-height: 1.2;">
												<span style="display:block; color: #e2e8f0; font-weight: 500; font-size: 13px;"><?php echo esc_html( $user_name ); ?></span>
												<span style="display:block; color: #718096; font-size: 11px;"><?php echo esc_html( $user_email ); ?></span>
											</div>
										</div>
									</td>
									<td>
										<span class="alezux-status-badge status-<?php echo esc_attr( $project->status ); ?>">
											<?php echo esc_html( ucfirst( $project->status ) ); ?>
										</span>
									</td>
									<td>
										<span style="background: rgba(255,255,255,0.05); padding: 4px 8px; border-radius: 4px; font-size: 12px; color: #cbd5e0;">
											<?php echo esc_html( ucfirst( $project->current_step ) ); ?>
										</span>
									</td>
									<td>
										<a href="<?php echo esc_url( $project_url ); ?>" class="alezux-marketing-btn" style="background: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%); padding: 6px 12px; font-size: 11px;">
											<i class="eicon-edit"></i> Gestionar
										</a>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
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

		</div>
		<?php
	}
}
