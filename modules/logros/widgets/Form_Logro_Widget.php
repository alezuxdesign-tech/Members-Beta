<?php
namespace Alezux_Members\Modules\Logros\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Icons_Manager; 

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


		// --- NEW CONTROLS FOR UPLOAD BOX ---
		$this->add_control(
			'heading_upload_box',
			[
				'label' => esc_html__( 'Caja de Subida', 'alezux-members' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		
		$this->add_control(
			'upload_icon',
			[
				'label' => esc_html__( 'Icono', 'alezux-members' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-cloud-upload-alt',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'upload_title_text',
			[
				'label' => esc_html__( 'Título Principal', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Choose a file or drag & drop it here', 'alezux-members' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'upload_subtitle_text',
			[
				'label' => esc_html__( 'Subtítulo', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'JPEG, PNG, PDF, and MP4 formats, up to 50 MB.', 'alezux-members' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'upload_btn_text',
			[
				'label' => esc_html__( 'Texto Botón Búsqueda', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Browse File', 'alezux-members' ),
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

		// --- STYLE: FORM CONTAINER ---
		$this->start_controls_section(
			'section_style_form',
			[
				'label' => esc_html__( 'Contenedor Formulario', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label' => esc_html__( 'Espacio entre Campos', 'alezux-members' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 15,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
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
				'selector' => '{{WRAPPER}} .alezux-logro-form-wrapper',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'form_border',
				'selector' => '{{WRAPPER}} .alezux-logro-form-wrapper',
			]
		);

		$this->add_responsive_control(
			'form_border_radius',
			[
				'label' => esc_html__( 'Radio del Borde', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-form-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'form_padding',
			[
				'label' => esc_html__( 'Relleno', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-form-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'form_box_shadow',
				'selector' => '{{WRAPPER}} .alezux-logro-form-wrapper',
			]
		);

		$this->end_controls_section();


		// --- STYLE: LABELS ---
		$this->start_controls_section(
			'section_style_labels',
			[
				'label' => esc_html__( 'Etiquetas', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'label_color',
			[
				'label' => esc_html__( 'Color', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-form-group label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'selector' => '{{WRAPPER}} .alezux-logro-form-group label',
			]
		);

		$this->add_responsive_control(
			'label_spacing',
			[
				'label' => esc_html__( 'Espacio Inferior', 'alezux-members' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-form-group label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();


		// --- STYLE: INPUTS ---
		$this->start_controls_section(
			'section_style_input',
			[
				'label' => esc_html__( 'Campos (Inputs)', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'input_typography',
				'selector' => '{{WRAPPER}} .alezux-logro-input',
			]
		);

		$this->start_controls_tabs( 'tabs_input_style' );

		// Normal State
		$this->start_controls_tab(
			'tab_input_normal',
			[
				'label' => esc_html__( 'Normal', 'alezux-members' ),
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

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'input_border',
				'selector' => '{{WRAPPER}} .alezux-logro-input',
			]
		);

		$this->add_responsive_control(
			'input_border_radius',
			[
				'label' => esc_html__( 'Radio del Borde', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		// Focus State
		$this->start_controls_tab(
			'tab_input_focus',
			[
				'label' => esc_html__( 'Focus', 'alezux-members' ),
			]
		);

		$this->add_control(
			'input_text_color_focus',
			[
				'label' => esc_html__( 'Color de Texto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-input:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_bg_color_focus',
			[
				'label' => esc_html__( 'Color de Fondo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-input:focus' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_border_color_focus',
			[
				'label' => esc_html__( 'Color de Borde', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-input:focus' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'input_padding',
			[
				'label' => esc_html__( 'Relleno Text', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
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

		// General Box Style
		$this->add_control(
			'upload_bg_color', 
			[
				'label' => 'Fondo', 
				'type' => Controls_Manager::COLOR, 
				'selectors' => [
					'{{WRAPPER}} .alezux-upload-box' => 'background-color: {{VALUE}};'
				]
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(), 
			[
				'name' => 'upload_border', 
				'selector' => '{{WRAPPER}} .alezux-upload-box', 
				'defaults' => [
					'border' => 'dashed', 
					'width' => ['top'=>2,'right'=>2,'bottom'=>2,'left'=>2], 
					'color'=>'#cccccc'
				]
			]
		);
		$this->add_control(
			'upload_border_radius', 
			[
				'label'=>'Radio del Borde', 
				'type'=>Controls_Manager::DIMENSIONS, 
				'selectors'=>[
					'{{WRAPPER}} .alezux-upload-box'=>'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);
		$this->add_responsive_control(
			'upload_padding', 
			[
				'label'=>'Relleno', 
				'type'=>Controls_Manager::DIMENSIONS, 
				'selectors'=>[
					'{{WRAPPER}} .alezux-upload-box'=>'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		// Icon Style
		$this->add_control(
			'heading_upload_icon', 
			[
				'label' => 'Icono', 
				'type' => Controls_Manager::HEADING, 
				'separator' => 'before'
			]
		);
		$this->add_control(
			'upload_icon_color', 
			[
				'label' => 'Color Icono', 
				'type' => Controls_Manager::COLOR, 
				'selectors' => [
					'{{WRAPPER}} .alezux-upload-icon i' => 'color: {{VALUE}};'
				]
			]
		);
		$this->add_control(
			'upload_icon_size', 
			[
				'label' => 'Tamaño Icono', 
				'type' => Controls_Manager::SLIDER, 
				'selectors' => [
					'{{WRAPPER}} .alezux-upload-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .alezux-upload-icon i' => 'font-size: {{SIZE}}{{UNIT}};'
				]
			]
		);

		// Title Style
		$this->add_control(
			'heading_upload_title', 
			[
				'label' => 'Título', 
				'type' => Controls_Manager::HEADING, 
				'separator' => 'before'
			]
		);
		$this->add_control(
			'upload_title_color', 
			[
				'label' => 'Color Título', 
				'type' => Controls_Manager::COLOR, 
				'selectors' => [
					'{{WRAPPER}} .alezux-upload-title' => 'color: {{VALUE}};'
				]
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(), 
			[
				'name' => 'upload_title_typo', 
				'selector' => '{{WRAPPER}} .alezux-upload-title'
			]
		);

		// Subtitle Style
		$this->add_control(
			'heading_upload_subtitle', 
			[
				'label' => 'Subtítulo', 
				'type' => Controls_Manager::HEADING, 
				'separator' => 'before'
			]
		);
		$this->add_control(
			'upload_subtitle_color', 
			[
				'label' => 'Color Subtítulo', 
				'type' => Controls_Manager::COLOR, 
				'selectors' => [
					'{{WRAPPER}} .alezux-upload-subtitle' => 'color: {{VALUE}};'
				]
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(), 
			[
				'name' => 'upload_subtitle_typo', 
				'selector' => '{{WRAPPER}} .alezux-upload-subtitle'
			]
		);

		// Fake Button Style
		$this->add_control(
			'heading_upload_btn', 
			[
				'label' => 'Botón "Browse"', 
				'type' => Controls_Manager::HEADING, 
				'separator' => 'before'
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(), 
			[
				'name' => 'upload_btn_typo', 
				'selector' => '{{WRAPPER}} .alezux-upload-btn-fake'
			]
		);
		$this->add_control(
			'upload_btn_color', 
			[
				'label' => 'Color Texto Botón', 
				'type' => Controls_Manager::COLOR, 
				'selectors' => [
					'{{WRAPPER}} .alezux-upload-btn-fake' => 'color: {{VALUE}};'
				]
			]
		);
		$this->add_control(
			'upload_btn_bg', 
			[
				'label' => 'Fondo Botón', 
				'type' => Controls_Manager::COLOR, 
				'selectors' => [
					'{{WRAPPER}} .alezux-upload-btn-fake' => 'background-color: {{VALUE}};'
				]
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(), 
			[
				'name' => 'upload_btn_border', 
				'selector' => '{{WRAPPER}} .alezux-upload-btn-fake'
			]
		);
		$this->add_control(
			'upload_btn_radius', 
			[
				'label'=>'Radio del Borde Botón', 
				'type'=>Controls_Manager::DIMENSIONS, 
				'selectors'=>[
					'{{WRAPPER}} .alezux-upload-btn-fake'=>'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);
		$this->add_responsive_control(
			'upload_btn_padding', 
			[
				'label'=>'Relleno Botón', 
				'type'=>Controls_Manager::DIMENSIONS, 
				'selectors'=>[
					'{{WRAPPER}} .alezux-upload-btn-fake'=>'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->end_controls_section();


		// --- SECTION STYLE SUBMIT BUTTON ---
		$this->start_controls_section(
			'section_style_button',
			[
				'label' => esc_html__( 'Botón Guardar', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
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
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Centro', 'alezux-members' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Derecha', 'alezux-members' ),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__( 'Justificado', 'alezux-members' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-form-actions' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .alezux-logro-submit' => 'width: {{VALUE}}',
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

		$this->start_controls_tabs( 'tabs_button_style' );

		// Normal State
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

		// Hover State
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
			'button_hover_animation',
			[
				'label' => esc_html__( 'Animación', 'alezux-members' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
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

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label' => esc_html__( 'Radio del Borde', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label' => esc_html__( 'Relleno', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( ! current_user_can( 'administrator' ) ) {
            // Check for admin capability.
			// You can create a setting to allow other roles if needed.
			return;
		}

		wp_enqueue_media();
        // Fetch courses and students
		$courses = get_posts( [ 'post_type' => 'sfwd-courses', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC' ] );
		$students = get_users( [ 'role__in' => [ 'student', 'subscriber', 'customer' ], 'fields' => [ 'ID', 'display_name', 'user_email' ], 'number' => 300 ] );

		?>
		<div class="alezux-logro-form-wrapper">
			<form id="alezux-logro-form" class="alezux-logro-form">
				
                <!-- Course -->
				<div class="alezux-logro-form-group">
					<label for="logro-course"><?php echo esc_html( $settings['label_course'] ); ?></label>
					<select id="logro-course" name="course_id" class="alezux-logro-input" required>
						<option value=""><?php echo esc_html( $settings['placeholder_course'] ); ?></option>
						<?php foreach ( $courses as $course ) : ?>
							<option value="<?php echo esc_attr( $course->ID ); ?>"><?php echo esc_html( $course->post_title ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
                
                <!-- Student -->
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

                <!-- Message -->
                <div class="alezux-logro-form-group">
					<label for="logro-message"><?php echo esc_html( $settings['label_message'] ); ?></label>
					<textarea id="logro-message" name="message" class="alezux-logro-input" rows="4" required></textarea>
				</div>

				<div class="alezux-logro-form-group">
					<label style="display:block; margin-bottom:8px; font-weight:600;"><?php echo esc_html( $settings['label_image'] ); ?></label>
					<div class="alezux-logro-upload-container">
						<input type="hidden" name="image_id" class="alezux-logro-image-id" value="">
						
						<div class="alezux-upload-box" onclick="console.log('Alezux Debug: Inline click triggered on upload box');">
							
                            <!-- Placeholder State -->
                            <div class="alezux-upload-placeholder">
                                <div class="alezux-upload-icon">
                                    <?php Icons_Manager::render_icon( $settings['upload_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                                </div>
                                <div class="alezux-upload-title">
                                    <?php echo esc_html( $settings['upload_title_text'] ); ?>
                                </div>
                                <div class="alezux-upload-subtitle">
                                    <?php echo esc_html( $settings['upload_subtitle_text'] ); ?>
                                </div>
                                <div class="alezux-upload-button-wrapper">
                                    <span class="alezux-upload-btn-fake"><?php echo esc_html( $settings['upload_btn_text'] ); ?></span>
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
						<?php echo esc_html( $settings['text_submit_button'] ); ?>
					</button>
				</div>
				
				<div id="alezux-logro-response"></div>

			</form>
		</div>
		<?php
	}
}
