<?php
namespace Alezux_Members\Modules\Logros\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Form_Logro_Widget extends Widget_Base {

	public function get_name() {
		return 'alezux-form-logro';
	}

	public function get_title() {
		return esc_html__( 'Form Logro', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	public function get_categories() {
		return [ 'alezux-admin' ];
	}

	public function get_script_depends() {
		return [ 'alezux-logros-js', 'jquery' ]; // Aseguramos que cargue nuestro JS y jQuery
	}

	protected function register_controls() {

		// --- Sección de Estilo: Formulario ---
		$this->start_controls_section(
			'section_style_form',
			[
				'label' => esc_html__( 'Formulario', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'gap',
			[
				'label' => esc_html__( 'Espacio entre campos', 'alezux-members' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-form-group' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'form_background',
				'label' => esc_html__( 'Fondo', 'alezux-members' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .alezux-logro-form',
			]
		);

		$this->add_responsive_control(
			'form_padding',
			[
				'label' => esc_html__( 'Relleno', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-form' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'form_border',
				'selector' => '{{WRAPPER}} .alezux-logro-form',
			]
		);

		$this->add_control(
			'form_border_radius',
			[
				'label' => esc_html__( 'Radio del Borde', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-form' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// --- Sección de Estilo: Inputs ---
		$this->start_controls_section(
			'section_style_input',
			[
				'label' => esc_html__( 'Campos (Inputs)', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'input_bg_color',
			[
				'label' => esc_html__( 'Color de Fondo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-input' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_text_color',
			[
				'label' => esc_html__( 'Color de Texto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-input' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'input_typography',
				'selector' => '{{WRAPPER}} .alezux-logro-input',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'input_border',
				'selector' => '{{WRAPPER}} .alezux-logro-input',
			]
		);

		$this->add_control(
			'input_border_radius',
			[
				'label' => esc_html__( 'Radio del Borde', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_responsive_control(
			'input_padding',
			[
				'label' => esc_html__( 'Relleno', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// --- Sección de Estilo: Botón ---
		$this->start_controls_section(
			'section_style_button',
			[
				'label' => esc_html__( 'Botón Enviar', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => esc_html__( 'Color de Texto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-submit' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_bg_color',
			[
				'label' => esc_html__( 'Color de Fondo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-submit' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .alezux-logro-submit',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		if ( ! current_user_can( 'administrator' ) ) {
			// Opción: no mostrar nada o mostrar mensaje
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="alezux-alert">Solo visible para administradores en frontend.</div>';
			}
			return;
		}

		// Encolar media upload scripts si no están ya
		wp_enqueue_media();

		// Obtener Cursos
		$courses = get_posts( [
			'post_type' => 'sfwd-courses',
			'numberposts' => -1,
			'orderby' => 'title',
			'order' => 'ASC',
		] );

		// Obtener Estudiantes (limitado para no reventar memoria si son muchos, 
		// pero asumiendo uso interno moderado)
		$students = get_users( [
			'role__in' => [ 'student', 'subscriber', 'customer' ], 
			'fields' => [ 'ID', 'display_name', 'user_email' ],
			'number' => 300, // Limite razonable
		] );

		?>
		<div class="alezux-logro-form-wrapper">
			<form id="alezux-logro-form" class="alezux-logro-form">
				
				<div class="alezux-logro-form-group">
					<label for="logro-course"><?php esc_html_e( 'Curso', 'alezux-members' ); ?></label>
					<select id="logro-course" name="course_id" class="alezux-logro-input" required>
						<option value=""><?php esc_html_e( 'Selecciona un curso', 'alezux-members' ); ?></option>
						<?php foreach ( $courses as $course ) : ?>
							<option value="<?php echo esc_attr( $course->ID ); ?>"><?php echo esc_html( $course->post_title ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="alezux-logro-form-group">
					<label for="logro-student"><?php esc_html_e( 'Estudiante (Opcional)', 'alezux-members' ); ?></label>
					<select id="logro-student" name="student_id" class="alezux-logro-input">
						<option value=""><?php esc_html_e( 'Ninguno (General)', 'alezux-members' ); ?></option>
						<?php foreach ( $students as $student ) : ?>
							<option value="<?php echo esc_attr( $student->ID ); ?>">
								<?php echo esc_html( $student->display_name . ' (' . $student->user_email . ')' ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="alezux-logro-form-group">
					<label for="logro-message"><?php esc_html_e( 'Mensaje del Logro', 'alezux-members' ); ?></label>
					<textarea id="logro-message" name="message" class="alezux-logro-input" rows="4" required></textarea>
				</div>

				<div class="alezux-logro-form-group">
					<label style="display:block; margin-bottom:8px; font-weight:600;"><?php esc_html_e( 'Imagen del Logro', 'alezux-members' ); ?></label>
					<div class="alezux-logro-upload-container">
						<input type="hidden" id="logro-image-id" name="image_id" value="">
						
						<!-- Area visual clickeable -->
						<div id="alezux-upload-trigger" class="alezux-upload-box">
							<div class="alezux-upload-placeholder">
								<i class="eicon-image-bold" aria-hidden="true"></i>
								<span><?php esc_html_e( 'Haz clic para subir imagen', 'alezux-members' ); ?></span>
							</div>
							<div class="alezux-upload-preview" style="display: none;">
								<img id="alezux-preview-img" src="" alt="Preview">
								<span class="alezux-remove-img" title="<?php esc_html_e( 'Eliminar imagen', 'alezux-members' ); ?>"><i class="eicon-close"></i></span>
							</div>
						</div>
					</div>
				</div>

				<div class="alezux-logro-form-actions">
					<button type="submit" class="alezux-logro-submit button-primary">
						<?php esc_html_e( 'Guardar Logro', 'alezux-members' ); ?>
					</button>
				</div>
				
				<div id="alezux-logro-response"></div>

			</form>
		</div>
		<?php
	}
}
