<?php
namespace Alezux_Members\Modules\Estudiantes\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Estudiantes_Register_Widget extends Widget_Base {

	public function get_name() {
		return 'alezux-estudiantes-register';
	}

	public function get_title() {
		return esc_html__( 'Registro Manual Estudiante', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-person';
	}

	public function get_categories() {
		return [ 'alezux-admin' ];
	}

	public function get_script_depends() {
		return [ 'alezux-estudiantes-js' ]; // Usamos el JS principal del módulo
	}

	public function get_style_depends() {
		return [ 'alezux-estudiantes-css' ];
	}

	protected function register_controls() {

		// --- CONTENIDO: ETIQUETAS ---
		$this->start_controls_section(
			'section_content_labels',
			[
				'label' => esc_html__( 'Etiquetas y Textos', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title_text',
			[
				'label' => esc_html__( 'Título del Formulario', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Registrar Nuevo Estudiante', 'alezux-members' ),
			]
		);

		$this->add_control(
			'label_firstname',
			[
				'label' => esc_html__( 'Label Nombre', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Nombre', 'alezux-members' ),
			]
		);

		$this->add_control(
			'label_lastname',
			[
				'label' => esc_html__( 'Label Apellido', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Apellido', 'alezux-members' ),
			]
		);

		$this->add_control(
			'label_email',
			[
				'label' => esc_html__( 'Label Email', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Correo Electrónico', 'alezux-members' ),
			]
		);

		$this->add_control(
			'label_course',
			[
				'label' => esc_html__( 'Label Curso', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Asignar Curso', 'alezux-members' ),
			]
		);

		$this->add_control(
			'text_button',
			[
				'label' => esc_html__( 'Texto Botón', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Registrar Estudiante', 'alezux-members' ),
			]
		);

		$this->end_controls_section();

		// --- ESTILO: CONTENEDOR ---
		$this->start_controls_section(
			'section_style_container',
			[
				'label' => esc_html__( 'Contenedor', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'container_background',
				'selector' => '{{WRAPPER}} .alezux-register-form',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'container_border',
				'selector' => '{{WRAPPER}} .alezux-register-form',
			]
		);

		$this->add_responsive_control(
			'container_padding',
			[
				'label' => esc_html__( 'Relleno', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-register-form' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'container_radius',
			[
				'label' => esc_html__( 'Radio Borde', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-register-form' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// --- ESTILO: CAMPOS ---
		$this->start_controls_section(
			'section_style_inputs',
			[
				'label' => esc_html__( 'Inputs y Etiquetas', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'input_bg_color',
			[
				'label' => esc_html__( 'Fondo Input', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-form-control' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_text_color',
			[
				'label' => esc_html__( 'Texto Input', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-form-control' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'input_border',
				'selector' => '{{WRAPPER}} .alezux-form-control',
			]
		);

		$this->add_control(
			'label_color',
			[
				'label' => esc_html__( 'Color Etiquetas', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-form-label' => 'color: {{VALUE}};',
					'{{WRAPPER}} .alezux-register-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// --- ESTILO: BOTÓN ---
		$this->start_controls_section(
			'section_style_button',
			[
				'label' => esc_html__( 'Botón', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .alezux-register-submit',
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__( 'Normal', 'alezux-members' ),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => esc_html__( 'Color Texto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-register-submit' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_bg_color',
			[
				'label' => esc_html__( 'Color Fondo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-register-submit' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__( 'Hover', 'alezux-members' ),
			]
		);

		$this->add_control(
			'button_text_color_hover',
			[
				'label' => esc_html__( 'Color Texto (Hover)', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-register-submit:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_bg_color_hover',
			[
				'label' => esc_html__( 'Color Fondo (Hover)', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-register-submit:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		// Obtener Cursos disponibles
		$courses = get_posts( [
			'post_type' => 'sfwd-courses',
			'numberposts' => -1,
			'orderby' => 'title',
			'order' => 'ASC',
		] );

		?>
		<div class="alezux-register-form">
			<h3 class="alezux-register-title"><?php echo esc_html( $settings['title_text'] ); ?></h3>
			
			<form id="alezux-manual-register-form">
				<div class="alezux-form-group">
					<label class="alezux-form-label"><?php echo esc_html( $settings['label_firstname'] ); ?></label>
					<input type="text" name="first_name" class="alezux-form-control" required placeholder="<?php esc_attr_e( 'Nombre del estudiante', 'alezux-members' ); ?>">
				</div>

				<div class="alezux-form-group">
					<label class="alezux-form-label"><?php echo esc_html( $settings['label_lastname'] ); ?></label>
					<input type="text" name="last_name" class="alezux-form-control" required placeholder="<?php esc_attr_e( 'Apellido del estudiante', 'alezux-members' ); ?>">
				</div>

				<div class="alezux-form-group">
					<label class="alezux-form-label"><?php echo esc_html( $settings['label_email'] ); ?></label>
					<input type="email" name="email" class="alezux-form-control" required placeholder="<?php esc_attr_e( 'correo@ejemplo.com', 'alezux-members' ); ?>">
				</div>

				<div class="alezux-form-group">
					<label class="alezux-form-label"><?php echo esc_html( $settings['label_course'] ); ?></label>
					<select name="course_id" class="alezux-form-control">
						<option value=""><?php esc_html_e( '-- Seleccionar Curso --', 'alezux-members' ); ?></option>
						<?php foreach ( $courses as $course ) : ?>
							<option value="<?php echo esc_attr( $course->ID ); ?>"><?php echo esc_html( $course->post_title ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="alezux-form-actions">
					<button type="submit" class="alezux-register-submit">
						<?php echo esc_html( $settings['text_button'] ); ?> <i class="fa fa-spinner fa-spin" style="display:none;"></i>
					</button>
				</div>
                <div class="alezux-form-message"></div>
			</form>
		</div>
		<?php
	}
}
