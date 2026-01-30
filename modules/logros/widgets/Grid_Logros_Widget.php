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
		return [ 'jquery' ]; // Ya no depende de logros.js compartido
	}

	public function get_style_depends() {
		return [ 'alezux-grid-logros-css' ];
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
<?php 
			// Bucle de logros
			foreach ( $achievements as $logro ) : 
				// Obtener datos
				$image_url_grid = '';
				$image_url_popup = '';

				if ( ! empty( $logro->image_id ) ) {
					$image_url_grid = wp_get_attachment_image_url( $logro->image_id, 'medium' );
					$image_url_popup = wp_get_attachment_image_url( $logro->image_id, 'large' );
				}
				
				// Imagen por defecto si no hay
				// if ( ! $image_url_grid ) $image_url_grid = ALEZUX_MEMBERS_URL . 'assets/images/placeholder.jpg'; 
				// Nota: Según diseño, si no hay imagen, quizás queramos mostrar solo fondo blanco o un placeholder más sutil.
				// Dejaremos vacío si no hay imagen para que CSS maneje el fondo blanco.

				$student_data = $logro->student_id ? get_userdata( $logro->student_id ) : null;
				$student_name = $student_data ? $student_data->display_name : esc_html__( 'Sistema', 'alezux-members' );
				$student_avatar = get_avatar_url( $logro->student_id ? $logro->student_id : 0 );
				
				$course_title = get_the_title( $logro->course_id );
				if ( ! $course_title ) $course_title = esc_html__( 'Curso General', 'alezux-members' );

				$date_format = get_option( 'date_format' );
				$logro_date = date_i18n( $date_format, strtotime( $logro->created_at ) );

				$short_message = mb_strimwidth( strip_tags( $logro->message ), 0, 120, '...' );
				?>
				
				<div class="alezux-logro-card-v2">
					<!-- Sección Superior: Imagen/Fondo Blanco + Badge Curso -->
					<div class="alezux-card-v2-top" <?php echo $image_url_grid ? 'style="background-image: url(' . esc_url( $image_url_grid ) . ');"' : ''; ?>>
						<div class="alezux-card-v2-badge">
							<?php echo esc_html( $course_title ); ?>
						</div>
					</div>

					<!-- Sección Inferior: Contenido Oscuro -->
					<div class="alezux-card-v2-body">
						
						<!-- Mensaje -->
						<div class="alezux-card-v2-message">
							<?php echo esc_html( $short_message ); ?>
						</div>

						<!-- Footer: Estudiante y Botón -->
						<div class="alezux-card-v2-footer">
							<div class="alezux-card-v2-student-info">
								<img src="<?php echo esc_url( $student_avatar ); ?>" alt="<?php echo esc_attr( $student_name ); ?>" class="alezux-card-v2-avatar">
								<div class="alezux-card-v2-meta">
									<span class="alezux-card-v2-name"><?php echo esc_html( $student_name ); ?></span>
									<span class="alezux-card-v2-date"><?php echo esc_html( $logro_date ); ?></span>
								</div>
							</div>
							
							<a href="#" class="alezux-logro-view-btn alezux-card-v2-btn" 
							   data-popup-target="#<?php echo esc_attr( $popup_id ); ?>"
							   data-id="<?php echo esc_attr( $logro->id ); ?>"
							   data-image="<?php echo esc_url( $image_url_popup ? $image_url_popup : $image_url_grid ); ?>"
							   data-message="<?php echo esc_attr( $logro->message ); ?>"
							   data-student="<?php echo esc_attr( $student_name ); ?>"
							   data-avatar="<?php echo esc_url( $student_avatar ); ?>">
								<?php esc_html_e( 'Ver logro', 'alezux-members' ); ?>
							</a>
						</div>
					</div>
				</div>

			<?php endforeach; ?>
		</div>

		<!-- Popup Estático para esta instancia -->
		<div id="<?php echo esc_attr( $popup_id ); ?>" class="alezux-logro-popup-overlay" style="display: none; position: fixed; top:0; left:0; width:100%; height:100%; z-index: 9999; justify-content: center; align-items: center; background: rgba(0,0,0,0.8);">
			<div class="alezux-logro-popup-content" style="background: white; padding: 20px; border-radius: 10px; max-width: 600px; width: 90%; position: relative;">
				<span class="alezux-popup-close" style="position: absolute; top: 10px; right: 15px; cursor: pointer; font-size: 20px; color: #333;">&times;</span>
				
				<img class="popup-image-el" src="" style="width: 100%; max-height: 300px; object-fit: contain; margin-bottom: 15px; border-radius: 5px; background: #f0f0f0;">
				
				<div style="display: flex; align-items: center; margin-bottom: 15px;">
					<img class="popup-avatar-el" src="" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 10px; object-fit: cover;">
					<h4 class="popup-student-el" style="margin: 0; font-size: 1.1em;"></h4>
				</div>
				
				<p class="popup-message-el" style="line-height: 1.6; color: #444; white-space: pre-wrap;"></p>
			</div>
		</div>

        <!-- Inline Script for Popup Logic -->
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                // Popup Handler for this widget instance
                var popupId = '#<?php echo esc_js( $popup_id ); ?>';
                
                // Open Popup
                // Use document delegation to potential dynamic content if needed, but instance specific ID is safer to bind directly if element exists
                // We use delegation on body for the open button click to be sure
                $('body').on('click', '.alezux-logro-view-btn', function(e) {
                    
                    var targetId = $(this).data('popup-target');
                    
                    // Verify if this button targets OUR popup
                    if(targetId !== popupId) return; 
                    
                    e.preventDefault();

                    var popup = $(targetId);
                    var image = $(this).data('image');
                    var message = $(this).data('message');
                    var student = $(this).data('student');
                    var avatar = $(this).data('avatar');

                    // Set Content
                    if(image) {
                        popup.find('.popup-image-el').attr('src', image).show();
                    } else {
                        popup.find('.popup-image-el').hide();
                    }
                    
                    popup.find('.popup-student-el').text(student);
                    popup.find('.popup-avatar-el').attr('src', avatar);
                    popup.find('.popup-message-el').text(message);

                    // Show
                    popup.css('display', 'flex').hide().fadeIn();
                });

                // Close Popup
                $(popupId + ' .alezux-popup-close').on('click', function() {
                    $(popupId).fadeOut();
                });

                // Close on click outside
                $(popupId).on('click', function(e) {
                    if ($(e.target).is(popupId)) {
                        $(this).fadeOut();
                    }
                });
            });
        </script>
		<?php
	}
}
