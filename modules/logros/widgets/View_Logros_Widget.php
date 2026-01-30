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

			<div id="alezux-logros-table-container">
				<!-- La tabla se cargará vía AJAX -->
				<div class="alezux-loading"><?php esc_html_e( 'Cargando registros...', 'alezux-members' ); ?></div>
			</div>

			<!-- Modal de Edición -->
			<div id="alezux-logro-edit-modal" class="alezux-modal" style="display:none;">
				<div class="alezux-modal-content">
					<span class="alezux-modal-close">&times;</span>
					<h3><?php esc_html_e( 'Editar Logro', 'alezux-members' ); ?></h3>
					<form id="alezux-logro-edit-form">
						<input type="hidden" id="edit-logro-id" name="id">
						
						<div class="alezux-form-group">
							<label for="edit-course-id"><?php esc_html_e( 'Curso', 'alezux-members' ); ?></label>
							<select id="edit-course-id" name="course_id" required>
								<option value=""><?php esc_html_e( 'Seleccionar Curso', 'alezux-members' ); ?></option>
								<?php foreach ( $courses as $course ) : ?>
									<option value="<?php echo esc_attr( $course->ID ); ?>">
										<?php echo esc_html( $course->post_title ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>

						<div class="alezux-form-group">
							<label for="edit-student-id"><?php esc_html_e( 'ID Estudiante', 'alezux-members' ); ?></label>
							<input type="number" id="edit-student-id" name="student_id">
						</div>

						<div class="alezux-form-group">
							<label for="edit-message"><?php esc_html_e( 'Mensaje', 'alezux-members' ); ?></label>
							<textarea id="edit-message" name="message" required></textarea>
						</div>

						<div class="alezux-form-group">
							<label for="edit-image-id"><?php esc_html_e( 'ID Imagen', 'alezux-members' ); ?></label>
							<input type="number" id="edit-image-id" name="image_id">
							<!-- Podría mejorarse con un selector de medios de WP si es necesario -->
						</div>

						<button type="submit" class="alezux-btn alezux-btn-primary"><?php esc_html_e( 'Guardar Cambios', 'alezux-members' ); ?></button>
					</form>
				</div>
			</div>
		</div>
		<?php
	}
}
