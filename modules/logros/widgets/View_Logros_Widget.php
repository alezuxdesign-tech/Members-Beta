<?php
namespace Alezux_Members\Modules\Logros\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class View_Logros_Widget extends Widget_Base {

	public function get_name() {
		return 'alezux_view_logros';
	}

	public function get_title() {
		return esc_html__( 'Ver Logros', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-table';
	}

	public function get_categories() {
		return [ 'alezux-admin' ];
	}

	public function get_script_depends() {
		return [ 'alezux-logros-js' ];
	}

	public function get_style_depends() {
		return [ 'alezux-logros-css' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Configuración', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'items_per_page',
			[
				'label' => esc_html__( 'Elementos por página', 'alezux-members' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 5,
				'max' => 100,
				'step' => 5,
				'default' => 20,
			]
		);

		$this->end_controls_section();
		
		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Estilo', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		
		// Aquí se podrían agregar controles de estilo para la tabla, botones, etc.

		$this->end_controls_section();
	}

	protected function render() {
		if ( current_user_can( 'upload_files' ) ) {
			wp_enqueue_media();
		}
		// Obtener todos los cursos para el filtro
		$courses = get_posts( [
			'post_type'      => 'sfwd-courses', // Asumiendo LearnDash, ajustar si es otro CPT
			'posts_per_page' => -1,
			'post_status'    => 'publish',
		] );

		?>
		<div class="alezux-view-logros-wrapper">
			<div class="alezux-logros-filters">
				<input type="text" id="alezux-logro-search" placeholder="<?php esc_attr_e( 'Buscar por palabra clave...', 'alezux-members' ); ?>">
				
				<select id="alezux-logro-course-filter">
					<option value=""><?php esc_html_e( 'Todos los cursos', 'alezux-members' ); ?></option>
					<?php foreach ( $courses as $course ) : ?>
						<option value="<?php echo esc_attr( $course->ID ); ?>">
							<?php echo esc_html( $course->post_title ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>

			<div id="alezux-logros-list-container">
				<!-- La lista de tarjetas se cargará vía AJAX -->
				<div class="alezux-loading"><?php esc_html_e( 'Cargando registros...', 'alezux-members' ); ?></div>
			</div>
			
			<div id="alezux-logros-pagination-container" style="text-align:center; padding: 20px; display:none;">
				<button id="alezux-load-more-logros" class="alezux-btn alezux-btn-primary">
					<?php esc_html_e( 'Cargar más', 'alezux-members' ); ?>
				</button>
			</div>

			<!-- Modal de Edición (Estructura Avanzada) -->
			<div id="alezux-logro-edit-modal" class="alezux-modal" style="display:none;">
				<div class="alezux-modal-content alezux-logro-form-wrapper" style="max-width: 600px; padding: 0;">
					<div class="alezux-logro-form" style="box-shadow: none; border: none; margin: 0;">
						<span class="alezux-modal-close" style="position: absolute; right: 20px; top: 15px; z-index: 100;">&times;</span>
						<h3 style="margin-top: 0; margin-bottom: 20px; text-align: center; color: #333;"><?php esc_html_e( 'Editar Logro', 'alezux-members' ); ?></h3>
						
						<form id="alezux-logro-edit-form">
							<input type="hidden" id="edit-logro-id" name="id">
							
							<!-- Course -->
							<div class="alezux-logro-form-group">
								<label for="edit-course-id"><?php esc_html_e( 'Curso', 'alezux-members' ); ?></label>
								<select id="edit-course-id" name="course_id" class="alezux-logro-input" required>
									<option value=""><?php esc_html_e( 'Seleccionar Curso', 'alezux-members' ); ?></option>
									<?php foreach ( $courses as $course ) : ?>
										<option value="<?php echo esc_attr( $course->ID ); ?>">
											<?php echo esc_html( $course->post_title ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>

							<!-- Student -->
							<div class="alezux-logro-form-group">
								<label for="edit-student-id"><?php esc_html_e( 'ID Estudiante', 'alezux-members' ); ?></label>
								<input type="number" id="edit-student-id" name="student_id" class="alezux-logro-input">
							</div>

							<!-- Message -->
							<div class="alezux-logro-form-group">
								<label for="edit-message"><?php esc_html_e( 'Mensaje', 'alezux-members' ); ?></label>
								<textarea id="edit-message" name="message" class="alezux-logro-input" rows="4" required></textarea>
							</div>

							<!-- Image Upload -->
							<div class="alezux-logro-form-group">
								<label style="display:block; margin-bottom:8px; font-weight:600;"><?php esc_html_e( 'Imagen', 'alezux-members' ); ?></label>
								<div class="alezux-logro-upload-container">
									<input type="hidden" id="edit-image-id" name="image_id" class="alezux-logro-image-id" value="">
									
									<div class="alezux-upload-box">
										<!-- Placeholder State -->
										<div class="alezux-upload-placeholder">
											<div class="alezux-upload-icon">
												<i class="fas fa-cloud-upload-alt" aria-hidden="true"></i>
											</div>
											<div class="alezux-upload-title">
												<?php esc_html_e( 'Elige un archivo', 'alezux-members' ); ?>
											</div>
											<div class="alezux-upload-button-wrapper">
												<span class="alezux-upload-btn-fake"><?php esc_html_e( 'Buscar Archivo', 'alezux-members' ); ?></span>
											</div>
										</div>

										<!-- Preview State -->
										<div class="alezux-upload-preview" style="display: none;">
											<img class="alezux-preview-img" src="" alt="Preview">
											<span class="alezux-remove-img" title="Eliminar"><i class="eicon-close"></i></span>
										</div>
									</div>
								</div>
							</div>

							<div class="alezux-logro-form-actions">
								<button type="submit" class="alezux-logro-submit">
									<?php esc_html_e( 'Guardar Cambios', 'alezux-members' ); ?>
								</button>
							</div>
						</form>
					</div>
				</div>
			</div>

			<!-- Modal de Eliminación Personalizado -->
			<div id="alezux-delete-modal" class="alezux-modal" style="display:none;">
				<div class="alezux-modal-content alezux-delete-modal-content">
					<div class="alezux-delete-icon">
						<i class="fas fa-trash-alt"></i>
					</div>
					<h3><?php esc_html_e( '¿Estás seguro?', 'alezux-members' ); ?></h3>
					<p><?php esc_html_e( 'Esta acción eliminará el logro permanentemente. No se puede deshacer.', 'alezux-members' ); ?></p>
					
					<div class="alezux-delete-actions">
						<button class="alezux-btn alezux-btn-cancel alezux-modal-close-btn"><?php esc_html_e( 'Cancelar', 'alezux-members' ); ?></button>
						<button id="alezux-confirm-delete-btn" class="alezux-btn alezux-btn-danger-confirm"><?php esc_html_e( 'Sí, Eliminar', 'alezux-members' ); ?></button>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
