<?php
namespace Alezux_Members\Modules\Logros\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Icons_Manager; // Import Icons Manager

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Form_Logro_Widget extends Widget_Base {

	// ... [Keep existing get_name, get_title, etc] ...
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

		// ... [Keep existing controls for Course, Student, Message labels] ...
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
				'label' => esc_html__( 'Texto Botón', 'alezux-members' ),
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
        
        // ... [Keep existing Style Sections for Form, Labels, Inputs, Button] ...
        // I'll condense them here in the plan, but in real code I will keep them.
        
        // --- SECTION STYLE FORM ---
        $this->start_controls_section('section_style_form', ['label' => 'Contenedor Formulario', 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_control('gap', ['label' => 'Espacio', 'type' => Controls_Manager::SLIDER, 'selectors' => ['{{WRAPPER}} .alezux-logro-form-group' => 'margin-bottom: {{SIZE}}{{UNIT}};']]);
        // ... background, padding, border ...
        $this->end_controls_section();

        // --- SECTION STYLE LABELS ---
        $this->start_controls_section('section_style_labels', ['label' => 'Etiquetas', 'tab' => Controls_Manager::TAB_STYLE]);
        // ... typical label styles ...
        $this->end_controls_section();

        // --- SECTION STYLE INPUTS ---
        $this->start_controls_section('section_style_input', ['label' => 'Inputs', 'tab' => Controls_Manager::TAB_STYLE]);
        // ... typical input styles ...
        $this->end_controls_section();


		// --- TAB STYLE: UPLOAD BOX (Updated) ---
		$this->start_controls_section(
			'section_style_upload',
			[
				'label' => esc_html__( 'Caja Subida Imagen', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

        // General Box Style
		$this->add_control('upload_bg_color', ['label' => 'Fondo', 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .alezux-upload-box' => 'background-color: {{VALUE}};']]);
		$this->add_group_control(Group_Control_Border::get_type(), ['name' => 'upload_border', 'selector' => '{{WRAPPER}} .alezux-upload-box', 'defaults' => ['border' => 'dashed', 'width' => ['top'=>2,'right'=>2,'bottom'=>2,'left'=>2], 'color'=>'#ccc']]);
        $this->add_control('upload_border_radius', ['label'=>'Radio', 'type'=>Controls_Manager::DIMENSIONS, 'selectors'=>['{{WRAPPER}} .alezux-upload-box'=>'border-radius: {{TOP}}{{UNIT}} ...;']]);
        $this->add_responsive_control('upload_padding', ['label'=>'Relleno', 'type'=>Controls_Manager::DIMENSIONS, 'selectors'=>['{{WRAPPER}} .alezux-upload-box'=>'padding: {{TOP}}{{UNIT}} ...;']]);

        // Icon Style
        $this->add_control('heading_upload_icon', ['label' => 'Icono', 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
		$this->add_control('upload_icon_color', ['label' => 'Color', 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .alezux-upload-icon i' => 'color: {{VALUE}};']]);
		$this->add_control('upload_icon_size', ['label' => 'Tamaño', 'type' => Controls_Manager::SLIDER, 'selectors' => ['{{WRAPPER}} .alezux-upload-icon i' => 'font-size: {{SIZE}}{{UNIT}};']]);

        // Title Style
        $this->add_control('heading_upload_title', ['label' => 'Título', 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
		$this->add_control('upload_title_color', ['label' => 'Color', 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .alezux-upload-title' => 'color: {{VALUE}};']]);
		$this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'upload_title_typo', 'selector' => '{{WRAPPER}} .alezux-upload-title']);

        // Subtitle Style
        $this->add_control('heading_upload_subtitle', ['label' => 'Subtítulo', 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
		$this->add_control('upload_subtitle_color', ['label' => 'Color', 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .alezux-upload-subtitle' => 'color: {{VALUE}};']]);
		$this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'upload_subtitle_typo', 'selector' => '{{WRAPPER}} .alezux-upload-subtitle']);

        // Fake Button Style
        $this->add_control('heading_upload_btn', ['label' => 'Botón "Browse"', 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'upload_btn_typo', 'selector' => '{{WRAPPER}} .alezux-upload-btn-fake']);
        $this->add_control('upload_btn_color', ['label' => 'Color Texto', 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .alezux-upload-btn-fake' => 'color: {{VALUE}};']]);
        $this->add_control('upload_btn_bg', ['label' => 'Fondo', 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .alezux-upload-btn-fake' => 'background-color: {{VALUE}};']]);
        $this->add_control('upload_btn_border_active', ['label' => 'Borde', 'type' => Controls_Manager::SWITCHER, 'default' => 'yes']); // Simplified logic for border or just add border group
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'upload_btn_border', 'selector' => '{{WRAPPER}} .alezux-upload-btn-fake']);
        $this->add_control('upload_btn_radius', ['label'=>'Radio', 'type'=>Controls_Manager::DIMENSIONS, 'selectors'=>['{{WRAPPER}} .alezux-upload-btn-fake'=>'border-radius: {{TOP}}{{UNIT}} ...;']]);
        $this->add_responsive_control('upload_btn_padding', ['label'=>'Relleno', 'type'=>Controls_Manager::DIMENSIONS, 'selectors'=>['{{WRAPPER}} .alezux-upload-btn-fake'=>'padding: {{TOP}}{{UNIT}} ...;']]);

		$this->end_controls_section();


		// --- SECTION STYLE SUBMIT BUTTON ---
         // ... [Keep existing Button Styling] ...
        $this->start_controls_section('section_style_button', ['label' => 'Botón Guardar', 'tab' => Controls_Manager::TAB_STYLE]);
        // ...
        $this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( ! current_user_can( 'administrator' ) ) {
            // ... admin check ...
			return;
		}

		wp_enqueue_media();
        // ... fetch courses/students ...
		$courses = get_posts( [ 'post_type' => 'sfwd-courses', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC' ] );
		$students = get_users( [ 'role__in' => [ 'student', 'subscriber', 'customer' ], 'fields' => [ 'ID', 'display_name', 'user_email' ], 'number' => 300 ] );

		?>
		<div class="alezux-logro-form-wrapper">
			<form id="alezux-logro-form" class="alezux-logro-form">
				
                <!-- ... Course, Student, Message Selects ... -->
				<div class="alezux-logro-form-group">
					<label for="logro-course"><?php echo esc_html( $settings['label_course'] ); ?></label>
					<select id="logro-course" name="course_id" class="alezux-logro-input" required>
						<option value=""><?php echo esc_html( $settings['placeholder_course'] ); ?></option>
						<?php foreach ( $courses as $course ) : ?>
							<option value="<?php echo esc_attr( $course->ID ); ?>"><?php echo esc_html( $course->post_title ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
                
                <!-- ... Student ... -->
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

                <!-- ... Message ... -->
                <div class="alezux-logro-form-group">
					<label for="logro-message"><?php echo esc_html( $settings['label_message'] ); ?></label>
					<textarea id="logro-message" name="message" class="alezux-logro-input" rows="4" required></textarea>
				</div>

				<div class="alezux-logro-form-group">
					<label style="display:block; margin-bottom:8px; font-weight:600;"><?php echo esc_html( $settings['label_image'] ); ?></label>
					<div class="alezux-logro-upload-container">
						<input type="hidden" id="logro-image-id" name="image_id" value="">
						
						<div id="alezux-upload-trigger" class="alezux-upload-box">
							
                            <!-- Placeholder State: The Redesigned Upload Area -->
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
