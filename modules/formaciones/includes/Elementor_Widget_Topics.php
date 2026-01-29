<?php
namespace Alezux_Members\Modules\Formaciones\Includes;

use Alezux_Members\Core\Elementor_Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Elementor_Widget_Topics extends Elementor_Widget_Base {

	public function get_name() {
		return 'alezux_topics_list';
	}

	public function get_title() {
		return __( 'Lista de Clases (Topics)', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-bullet-list';
	}

	protected function register_widget_controls() {
		
		// --- Sección de Contenido ---
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Contenido', 'alezux-members' ),
			]
		);

		$this->add_control(
			'show_header',
			[
				'label' => __( 'Mostrar Cabecera (Lección)', 'alezux-members' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Sí', 'alezux-members' ),
				'label_off' => __( 'No', 'alezux-members' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->end_controls_section();

		// --- Sección de Estilo: Cabecera ---
		$this->start_controls_section(
			'section_style_header',
			[
				'label' => __( 'Cabecera', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_header' => 'yes',
				],
			]
		);

		$this->add_control(
			'header_bg_color',
			[
				'label' => __( 'Color de Fondo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-topics-header' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'header_title_color',
			[
				'label' => __( 'Color Título', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .alezux-topics-header-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'header_title_typography',
				'label' => __( 'Tipografía Título', 'alezux-members' ),
				'selector' => '{{WRAPPER}} .alezux-topics-header-title',
			]
		);

		$this->add_control(
			'header_meta_color',
			[
				'label' => __( 'Color Meta (Progreso)', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#a0a0a0',
				'selectors' => [
					'{{WRAPPER}} .alezux-topics-header-meta' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// --- Sección de Estilo: Lista ---
		$this->start_controls_section(
			'section_style_list',
			[
				'label' => __( 'Lista de Topics', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'item_bg_color',
			[
				'label' => __( 'Color de Fondo Item', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'transparent',
				'selectors' => [
					'{{WRAPPER}} .alezux-topic-item' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'item_bg_color_active',
			[
				'label' => __( 'Color de Fondo Activo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-topic-item.is-active' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'item_border_color',
			[
				'label' => __( 'Color de Borde (Separador)', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#333333',
				'selectors' => [
					'{{WRAPPER}} .alezux-topic-item' => 'border-bottom: 1px solid {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'active_indicator_color',
			[
				'label' => __( 'Indicador Activo (Borde Izq)', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#bf00ff', // Un morado vibrante por defecto
				'selectors' => [
					'{{WRAPPER}} .alezux-topic-item.is-active' => 'border-left-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label' => __( 'Relleno (Padding)', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-topic-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// --- Sección de Estilo: Contenido Item ---
		$this->start_controls_section(
			'section_style_item_content',
			[
				'label' => __( 'Contenido del Item', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'item_title_color',
			[
				'label' => __( 'Color Título', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .alezux-topic-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'item_title_typography',
				'selector' => '{{WRAPPER}} .alezux-topic-title',
			]
		);

		$this->add_control(
			'item_author_color',
			[
				'label' => __( 'Color Autor', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#a0a0a0',
				'selectors' => [
					'{{WRAPPER}} .alezux-topic-author' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'item_author_typography',
				'selector' => '{{WRAPPER}} .alezux-topic-author',
			]
		);

		$this->add_responsive_control(
			'image_width',
			[
				'label' => __( 'Ancho Imagen', 'alezux-members' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .alezux-topic-thumbnail' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'image_border_radius',
			[
				'label' => __( 'Radio Borde Imagen', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-topic-thumbnail img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .alezux-topic-thumbnail' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// --- Sección de Estilo: Contenedor Principal ---
		$this->start_controls_section(
			'section_style_container',
			[
				'label' => __( 'Contenedor del Widget', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'container_bg_color',
			[
				'label' => __( 'Color de Fondo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#121212',
				'selectors' => [
					'{{WRAPPER}} .alezux-topics-widget' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'container_border',
				'selector' => '{{WRAPPER}} .alezux-topics-widget',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'container_border_radius',
			[
				'label' => __( 'Radio de Borde', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' => 15,
					'right' => 15,
					'bottom' => 15,
					'left' => 15,
					'unit' => 'px',
					'isLinked' => true,
				],
				'selectors' => [
					'{{WRAPPER}} .alezux-topics-widget' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'container_padding',
			[
				'label' => __( 'Relleno (Padding)', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => 20,
					'right' => 20,
					'bottom' => 20,
					'left' => 20,
					'unit' => 'px',
					'isLinked' => true,
				],
				'selectors' => [
					'{{WRAPPER}} .alezux-topics-widget' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'container_box_shadow',
				'selector' => '{{WRAPPER}} .alezux-topics-widget',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		
		$current_id = get_the_ID();
		if ( ! $current_id ) {
			return;
		}

		$post_type = get_post_type( $current_id );
		
		$lesson_id = 0;
		$current_topic_id = 0;

		if ( 'sfwd-lessons' === $post_type ) {
			$lesson_id = $current_id;
		} elseif ( 'sfwd-topic' === $post_type ) {
			$current_topic_id = $current_id;
			if ( function_exists( 'learndash_get_setting' ) ) {
				$lesson_id = learndash_get_setting( $current_id, 'lesson' );
			}
			// Fallback si learndash_get_setting no devuelve el ID directamente o formato antiguo
			if ( ! $lesson_id && function_exists( 'learndash_get_course_id' ) ) {
				// Esto devuelve el curso, no la lección directamente si no está vinculada.
				// Mejor usar get_post_meta para course_id y luego buscar. 
				// Pero en LearnDash moderno 'lesson_id' suele ser meta 'lesson_id' o 'course_id'.
				$lesson_id = get_post_meta( $current_id, 'lesson_id', true );
			}
		} else {
			// Si estamos en el editor de Elementor y no es contexto de LD, mostrar placeholder
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="alezux-elementor-placeholder" style="color:white; padding: 20px; background: #333;">Este widget solo funciona en contextos de Lección o Topic de LearnDash.</div>';
				return;
			}
			return;
		}

		if ( ! $lesson_id ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="alezux-elementor-placeholder" style="color:white;">No se pudo determinar la Lección actual.</div>';
			}
			return;
		}

		// Obtener Topics
		$topics = [];
		if ( function_exists( 'learndash_get_topic_list' ) ) {
			$topics = learndash_get_topic_list( $lesson_id );
		}

		if ( empty( $topics ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="alezux-elementor-placeholder" style="color:white;">Esta lección no tiene Topics (Clases).</div>';
			}
			return; // No mostrar nada si no hay topics
		}

		// Datos de la Lección para cabecera
		$lesson_title = get_the_title( $lesson_id );
		
		// Progreso (Placeholder lógica básica, LD tiene funciones complejas para esto)
		// Para simplificar, obtenemos total y completados
		$total_topics = count( $topics );
		$completed_count = 0;
		$user_id = get_current_user_id();

		if ( $user_id && function_exists( 'learndash_is_topic_complete' ) ) {
			foreach ( $topics as $topic ) {
				if ( learndash_is_topic_complete( $user_id, $topic->ID ) ) {
					$completed_count++;
				}
			}
		}

		?>
		<div class="alezux-topics-widget">
			<?php if ( 'yes' === $settings['show_header'] ) : ?>
			<div class="alezux-topics-header">
				<div class="alezux-topics-header-content">
					<h3 class="alezux-topics-header-title"><?php echo esc_html( $lesson_title ); ?></h3>
					<div class="alezux-topics-header-meta">
						<?php echo esc_html( sprintf( '%d/%d terminado', $completed_count, $total_topics ) ); ?>
					</div>
				</div>
				<div class="alezux-topics-header-icon">
					<i class="eicon-chevron-down"></i>
				</div>
			</div>
			<?php endif; ?>

			<div class="alezux-topics-list">
				<?php foreach ( $topics as $topic ) : 
					$is_active = ( $current_topic_id === $topic->ID ) ? 'is-active' : '';
					$user_id = get_current_user_id();
					$is_completed = ( $user_id && function_exists( 'learndash_is_topic_complete' ) ) ? learndash_is_topic_complete( $user_id, $topic->ID ) : false;
					$permalink = get_permalink( $topic->ID );
					$title = get_the_title( $topic->ID );
					$author_id = $topic->post_author;
					$author_name = get_the_author_meta( 'display_name', $author_id );
					
					// Thumbnail
					$has_thumbnail = has_post_thumbnail( $topic->ID );
					$thumbnail_url = '';
					if ( $has_thumbnail ) {
						$thumbnail_url = get_the_post_thumbnail_url( $topic->ID, 'medium' );
					}
				?>
				<a href="<?php echo esc_url( $permalink ); ?>" class="alezux-topic-item <?php echo esc_attr( $is_active ); ?> <?php echo $is_completed ? 'is-completed' : ''; ?>">
					
					<div class="alezux-topic-thumbnail-wrapper">
						<!-- Checkmark Icon (Inside wrapper for absolute positioning) -->
						<div class="alezux-topic-check <?php echo $is_completed ? 'completed' : ''; ?>">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
								<polyline points="20 6 9 17 4 12"></polyline>
							</svg>
						</div>

						<?php if ( $has_thumbnail ) : ?>
							<div class="alezux-topic-thumbnail">
								<img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( $title ); ?>">
							</div>
						<?php else : ?>
							<div class="alezux-topic-thumbnail placeholder"></div>
						<?php endif; ?>
					</div>
					
					<div class="alezux-topic-info">
						<h4 class="alezux-topic-title"><?php echo esc_html( $title ); ?></h4>
						<span class="alezux-topic-author"><?php echo esc_html__( 'Autor:', 'alezux-members' ); ?> <?php echo esc_html( $author_name ); ?></span>
					</div>
				</a>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}
