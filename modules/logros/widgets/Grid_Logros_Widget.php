<?php
namespace Alezux_Members\Modules\Logros\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Image_Size;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Grid_Logros_Widget extends Widget_Base {

	public function get_name() {
		return 'alezux-grid-logros';
	}

	public function get_title() {
		return esc_html__( 'Grid Logros', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return [ 'alezux-members' ];
	}

	public function get_script_depends() {
		return [ 'alezux-logros-js', 'jquery' ];
	}

	protected function register_controls() {
		// --- Sección de Diseño ---
		$this->start_controls_section(
			'section_layout',
			[
				'label' => esc_html__( 'Diseño', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label' => esc_html__( 'Columnas', 'alezux-members' ),
				'type' => Controls_Manager::SELECT,
				'default' => '3',
				'options' => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				],
				'selectors' => [
					'{{WRAPPER}} .alezux-logros-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
				],
			]
		);

		$this->add_responsive_control(
			'gap',
			[
				'label' => esc_html__( 'Espacio entre tarjetas', 'alezux-members' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .alezux-logros-grid' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// --- Sección de Estilo: Tarjeta ---
		$this->start_controls_section(
			'section_style_card',
			[
				'label' => esc_html__( 'Tarjeta', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'card_bg_color',
			[
				'label' => esc_html__( 'Color de Fondo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-card' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'card_border',
				'selector' => '{{WRAPPER}} .alezux-logro-card',
			]
		);

		$this->add_control(
			'card_border_radius',
			[
				'label' => esc_html__( 'Radio del Borde', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'card_box_shadow',
				'selector' => '{{WRAPPER}} .alezux-logro-card',
			]
		);

		$this->add_responsive_control(
			'card_padding',
			[
				'label' => esc_html__( 'Relleno', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// --- Sección de Estilo: Botón ---
		$this->start_controls_section(
			'section_style_btn',
			[
				'label' => esc_html__( 'Botón Ver', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'btn_typography',
				'selector' => '{{WRAPPER}} .alezux-logro-view-btn',
			]
		);

		$this->add_control(
			'btn_color',
			[
				'label' => esc_html__( 'Color Texto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-view-btn' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_bg_color',
			[
				'label' => esc_html__( 'Color Fondo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-view-btn' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// --- Sección de Estilo: Popup ---
		$this->start_controls_section(
			'section_style_popup',
			[
				'label' => esc_html__( 'Popup', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'popup_bg_color',
			[
				'label' => esc_html__( 'Color Fondo Popup', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-popup-content' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'popup_padding',
			[
				'label' => esc_html__( 'Relleno Popup', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-popup-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'popup_border_radius',
			[
				'label' => esc_html__( 'Radio del Borde Popup', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-popup-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'popup_msg_typography',
				'label' => 'Tipografía Mensaje',
				'selector' => '{{WRAPPER}} .popup-message-el',
			]
		);

		$this->add_control(
			'popup_text_color',
			[
				'label' => esc_html__( 'Color Texto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .popup-message-el, {{WRAPPER}} .popup-student-el' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		if ( ! is_user_logged_in() ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div>Grid visible solo para usuarios logueados.</div>';
			}
			return;
		}

		$user_id = get_current_user_id();
		
		// Obtener cursos inscritos
		$enrolled_courses = function_exists( 'learndash_user_get_enrolled_courses' ) 
			? learndash_user_get_enrolled_courses( $user_id ) 
			: [];

		if ( empty( $enrolled_courses ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div>El usuario no tiene cursos inscritos.</div>';
			}
			return;
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'alezux_achievements';
		
		// Verificar tabla
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
			return;
		}

		$ids_placeholder = implode( ',', array_fill( 0, count( $enrolled_courses ), '%d' ) );
		$sql = "SELECT * FROM $table_name WHERE course_id IN ($ids_placeholder) ORDER BY created_at DESC";
		
		$achievements = $wpdb->get_results( $wpdb->prepare( $sql, $enrolled_courses ) );

		if ( empty( $achievements ) ) {
			echo '<div class="alezux-no-achievements">' . esc_html__( 'No hay logros disponibles.', 'alezux-members' ) . '</div>';
			return;
		}

		// ID único para el popup de esta instancia
		$popup_id = 'alezux-popup-' . $this->get_id();

		?>
		<div class="alezux-logros-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
			<?php foreach ( $achievements as $logro ) : 
				$image_url = $logro->image_id ? wp_get_attachment_image_url( $logro->image_id, 'medium' ) : '';
				if ( ! $image_url ) $image_url = ALEZUX_MEMBERS_URL . 'assets/images/placeholder.jpg'; 
				
				$student_data = $logro->student_id ? get_userdata( $logro->student_id ) : null;
				$student_name = $student_data ? $student_data->display_name : esc_html__( 'Sistema', 'alezux-members' );
				$student_avatar = get_avatar_url( $logro->student_id ? $logro->student_id : 0 );
				
				$short_message = mb_strimwidth( strip_tags( $logro->message ), 0, 100, '...' );
				?>
				<div class="alezux-logro-card" style="border: 1px solid #ccc; padding: 15px; border-radius: 10px;">
					<div class="alezux-logro-image" style="height: 150px; background-image: url('<?php echo esc_url( $image_url ); ?>'); background-size: cover; background-position: center; border-radius: 5px; margin-bottom: 10px;"></div>
					
					<div class="alezux-logro-content">
						<p class="alezux-logro-text"><?php echo esc_html( $short_message ); ?></p>
						
						<div class="alezux-logro-author" style="display: flex; align-items: center; margin-top: 10px;">
							<img src="<?php echo esc_url( $student_avatar ); ?>" alt="Avatar" style="width: 30px; height: 30px; border-radius: 50%; margin-right: 10px;">
							<span style="font-weight: bold; font-size: 0.9em;"><?php echo esc_html( $student_name ); ?></span>
						</div>

						<a href="#" class="alezux-logro-view-btn button" 
						   data-popup-target="#<?php echo esc_attr( $popup_id ); ?>"
						   data-id="<?php echo esc_attr( $logro->id ); ?>"
						   data-image="<?php echo esc_url( wp_get_attachment_image_url( $logro->image_id, 'large' ) ); ?>"
						   data-message="<?php echo esc_attr( $logro->message ); ?>"
						   data-student="<?php echo esc_attr( $student_name ); ?>"
						   data-avatar="<?php echo esc_url( $student_avatar ); ?>"
						   style="display: block; text-align: center; margin-top: 15px; padding: 8px; background: #000; color: #fff; text-decoration: none; border-radius: 5px;">
							<?php esc_html_e( 'Ver Logro', 'alezux-members' ); ?>
						</a>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<!-- Popup Estático para esta instancia -->
		<div id="<?php echo esc_attr( $popup_id ); ?>" class="alezux-logro-popup-overlay" style="display: none; position: fixed; top:0; left:0; width:100%; height:100%; z-index: 9999; justify-content: center; align-items: center; background: rgba(0,0,0,0.8);">
			<div class="alezux-logro-popup-content" style="background: white; padding: 20px; border-radius: 10px; max-width: 600px; width: 90%; position: relative;">
				<span class="alezux-popup-close" style="position: absolute; top: 10px; right: 15px; cursor: pointer; font-size: 20px;">&times;</span>
				
				<img class="popup-image-el" src="" style="width: 100%; max-height: 300px; object-fit: cover; margin-bottom: 15px; border-radius: 5px;">
				
				<div style="display: flex; align-items: center; margin-bottom: 15px;">
					<img class="popup-avatar-el" src="" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 10px;">
					<h4 class="popup-student-el" style="margin: 0;"></h4>
				</div>
				
				<p class="popup-message-el" style="line-height: 1.6;"></p>
			</div>
		</div>
		<?php
	}
}
