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
		return [ 'alezux-logros-js', 'jquery' ];
	}

	public function get_style_depends() {
		return [ 'alezux-logros-css' ];
	}

	protected function register_controls() {

		// --- TAB CONTENT: TEXTS ---
		$this->start_controls_section(
			'section_content_texts',
			[
				'label' => esc_html__( 'Textos y Etiquetas', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'label_course',
			[
				'label' => esc_html__( 'Etiqueta Curso', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Curso', 'alezux-members' ),
			]
		);

		$this->add_control(
			'placeholder_course',
			[
				'label' => esc_html__( 'Placeholder Curso', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Selecciona un curso', 'alezux-members' ),
			]
		);

		$this->add_control(
			'label_student',
			[
				'label' => esc_html__( 'Etiqueta Estudiante', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Estudiante (Opcional)', 'alezux-members' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'placeholder_student',
			[
				'label' => esc_html__( 'Placeholder Estudiante', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Ninguno (General)', 'alezux-members' ),
			]
		);

		$this->add_control(
			'label_message',
			[
				'label' => esc_html__( 'Etiqueta Mensaje', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Mensaje del Logro', 'alezux-members' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'label_image',
			[
				'label' => esc_html__( 'Etiqueta Imagen', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Imagen del Logro', 'alezux-members' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'text_upload_instructions',
			[
				'label' => esc_html__( 'Instrucciones Subida', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Haz clic para subir imagen', 'alezux-members' ),
			]
		);

		$this->add_control(
			'text_submit_button',
			[
				'label' => esc_html__( 'Texto Botón Guardar', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Guardar Logro', 'alezux-members' ),
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		// --- TAB STYLE: FORM WRAPPER ---
		$this->start_controls_section(
			'section_style_form',
			[
				'label' => esc_html__( 'Contenedor Formulario', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
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
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-form' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// --- TAB STYLE: LABELS ---
		$this->start_controls_section(
			'section_style_labels',
			[
				'label' => esc_html__( 'Etiquetas (Labels)', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'label_color',
			[
				'label' => esc_html__( 'Color Texto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-form label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'selector' => '{{WRAPPER}} .alezux-logro-form label',
			]
		);

		$this->add_responsive_control(
			'label_spacing',
			[
				'label' => esc_html__( 'Espacio Inferior', 'alezux-members' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-form label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// --- TAB STYLE: INPUTS ---
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
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// --- TAB STYLE: UPLOAD BOX ---
		$this->start_controls_section(
			'section_style_upload',
			[
				'label' => esc_html__( 'Caja Subida Imagen', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'tabs_upload_style' );

		// Normal
		$this->start_controls_tab(
			'tab_upload_normal',
			[
				'label' => esc_html__( 'Normal', 'alezux-members' ),
			]
		);

		$this->add_control(
			'upload_bg_color',
			[
				'label' => esc_html__( 'Color de Fondo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-upload-box' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'upload_border',
				'selector' => '{{WRAPPER}} .alezux-upload-box',
				'defaults' => [
					'border' => 'dashed',
					'width' => [ 'top' => 2, 'right' => 2, 'bottom' => 2, 'left' => 2, 'unit' => 'px' ],
					'color' => '#ccc',
				],
			]
		);

		$this->add_control(
			'upload_icon_color',
			[
				'label' => esc_html__( 'Color Icono', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-upload-placeholder i' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'upload_text_color',
			[
				'label' => esc_html__( 'Color Texto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-upload-placeholder span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		// Hover
		$this->start_controls_tab(
			'tab_upload_hover',
			[
				'label' => esc_html__( 'Hover', 'alezux-members' ),
			]
		);

		$this->add_control(
			'upload_bg_color_hover',
			[
				'label' => esc_html__( 'Color de Fondo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-upload-box:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'upload_border_color_hover',
			[
				'label' => esc_html__( 'Color de Borde', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-upload-box:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'upload_icon_color_hover',
			[
				'label' => esc_html__( 'Color Icono', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-upload-box:hover .alezux-upload-placeholder i' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'upload_text_color_hover',
			[
				'label' => esc_html__( 'Color Texto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-upload-box:hover .alezux-upload-placeholder span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'upload_icon_size',
			[
				'label' => esc_html__( 'Tamaño Icono', 'alezux-members' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .alezux-upload-placeholder i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'upload_typography',
				'selector' => '{{WRAPPER}} .alezux-upload-placeholder span',
			]
		);

		$this->add_control(
			'upload_border_radius',
			[
				'label' => esc_html__( 'Radio Borde', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .alezux-upload-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// --- TAB STYLE: BUTTON ---
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
				'selector' => '{{WRAPPER}} .alezux-logro-submit',
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		// Normal
		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__( 'Normal', 'alezux-members' ),
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

		$this->end_controls_tab();

		// Hover
		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__( 'Hover', 'alezux-members' ),
			]
		);

		$this->add_control(
			'button_text_color_hover',
			[
				'label' => esc_html__( 'Color de Texto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-submit:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_bg_color_hover',
			[
				'label' => esc_html__( 'Color de Fondo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-submit:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_border_color_hover',
			[
				'label' => esc_html__( 'Color de Borde', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-submit:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'button_border',
				'selector' => '{{WRAPPER}} .alezux-logro-submit',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label' => esc_html__( 'Radio del Borde', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .alezux-logro-submit',
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label' => esc_html__( 'Relleno', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_responsive_control(
			'button_align',
			[
				'label' => esc_html__( 'Alineación', 'alezux-members' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left'    => [
						'title' => esc_html__( 'Izquierda', 'alezux-members' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'  => [
						'title' => esc_html__( 'Centro', 'alezux-members' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'   => [
						'title' => esc_html__( 'Derecha', 'alezux-members' ),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__( 'Justificado', 'alezux-members' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'prefix_class' => 'elementor%s-align-',
				'default' => '',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( ! current_user_can( 'administrator' ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="alezux-alert">Solo visible para administradores en frontend.</div>';
			}
			return;
		}

		wp_enqueue_media();

		$courses = get_posts( [
			'post_type' => 'sfwd-courses',
			'numberposts' => -1,
			'orderby' => 'title',
			'order' => 'ASC',
		] );

		$students = get_users( [
			'role__in' => [ 'student', 'subscriber', 'customer' ], 
			'fields' => [ 'ID', 'display_name', 'user_email' ],
			'number' => 300, 
		] );

		// Alineación botón container helper
		$btn_align_style = '';
		// Elementor añade clases para alineación si usamos prefix_class, pero si queremos custom wrapper:
		// La clase elementor-align-xxx se añade al wrapper del widget o wrapper interno si está definido.
		// Para asegurar la alineación del botón dentro de nuestro div actions:
		// Pero al user prefix_class, elementor lo maneja en el container principal.
		// Solo necesitamos asegurarnos que nuestro .alezux-logro-form-actions respeta text-align o similar.
		// Agregaremos style dinámico si needed, pero con prefix_class suele bastar si el botón es inline-block.
		// Si es block, necesitamos margin auto.
		
		?>
		<div class="alezux-logro-form-wrapper">
			<form id="alezux-logro-form" class="alezux-logro-form">
				
				<div class="alezux-logro-form-group">
					<label for="logro-course"><?php echo esc_html( $settings['label_course'] ); ?></label>
					<select id="logro-course" name="course_id" class="alezux-logro-input" required>
						<option value=""><?php echo esc_html( $settings['placeholder_course'] ); ?></option>
						<?php foreach ( $courses as $course ) : ?>
							<option value="<?php echo esc_attr( $course->ID ); ?>"><?php echo esc_html( $course->post_title ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="alezux-logro-form-group">
					<label for="logro-student"><?php echo esc_html( $settings['label_student'] ); ?></label>
					<select id="logro-student" name="student_id" class="alezux-logro-input">
						<option value=""><?php echo esc_html( $settings['placeholder_student'] ); ?></option>
						<?php foreach ( $students as $student ) : ?>
							<option value="<?php echo esc_attr( $student->ID ); ?>">
								<?php echo esc_html( $student->display_name . ' (' . $student->user_email . ')' ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="alezux-logro-form-group">
					<label for="logro-message"><?php echo esc_html( $settings['label_message'] ); ?></label>
					<textarea id="logro-message" name="message" class="alezux-logro-input" rows="4" required></textarea>
				</div>

				<div class="alezux-logro-form-group">
					<label style="display:block; margin-bottom:8px; font-weight:600;"><?php echo esc_html( $settings['label_image'] ); ?></label>
					<div class="alezux-logro-upload-container">
						<input type="hidden" id="logro-image-id" name="image_id" value="">
						
						<div id="alezux-upload-trigger" class="alezux-upload-box">
							<div class="alezux-upload-placeholder">
								<i class="eicon-image-bold" aria-hidden="true"></i>
								<span><?php echo esc_html( $settings['text_upload_instructions'] ); ?></span>
							</div>
							<div class="alezux-upload-preview" style="display: none;">
								<img id="alezux-preview-img" src="" alt="Preview">
								<span class="alezux-remove-img" title="Eliminar"><i class="eicon-close"></i></span>
							</div>
						</div>
					</div>
				</div>

				<div class="alezux-logro-form-actions">
					<button type="submit" class="alezux-logro-submit">
						<?php echo esc_html( $settings['text_submit_button'] ); ?>
					</button>
				</div>
				
				<div id="alezux-logro-response"></div>

			</form>
		</div>
		<?php
	}
}
