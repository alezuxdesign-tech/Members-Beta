<?php
namespace Alezux_Members\Modules\Formaciones\Includes;

use Alezux_Members\Core\Elementor_Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Elementor_Widget_Formaciones_Grid extends Elementor_Widget_Base {

	public function get_name() {
		return 'alezux_formaciones_grid';
	}

	public function get_title() {
		return __( 'Grid cursos (Masonry)', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-gallery-masonry';
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
			'posts_per_page',
			[
				'label' => __( 'Cantidad de Cursos', 'alezux-members' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 6,
			]
		);

		$this->add_control(
			'orderby',
			[
				'label' => __( 'Ordenar por', 'alezux-members' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => [
					'date' => __( 'Fecha', 'alezux-members' ),
					'title' => __( 'Título', 'alezux-members' ),
					'rand' => __( 'Aleatorio', 'alezux-members' ),
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label' => __( 'Orden', 'alezux-members' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => [
					'ASC' => __( 'Ascendente', 'alezux-members' ),
					'DESC' => __( 'Descendente', 'alezux-members' ),
				],
			]
		);

		$this->end_controls_section();

		// --- Sección de Estilo: Grid ---
		$this->start_controls_section(
			'section_style_grid',
			[
				'label' => __( 'Grid', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label' => __( 'Columnas', 'alezux-members' ),
				'type' => Controls_Manager::SELECT,
				'default' => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options' => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				],
				'selectors' => [
					'{{WRAPPER}} .alezux-formaciones-grid' => 'column-count: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'gap',
			[
				'label' => __( 'Espaciado (Gap)', 'alezux-members' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .alezux-formaciones-grid' => 'column-gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .alezux-formacion-card' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// --- Sección de Estilo: Tarjeta ---
		$this->start_controls_section(
			'section_style_card',
			[
				'label' => __( 'Tarjeta', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'card_bg_color',
			[
				'label' => __( 'Color de Fondo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#1a1a1a', // Color base del sistema Alezux
				'selectors' => [
					'{{WRAPPER}} .alezux-formacion-card' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'card_border_radius',
			[
				'label' => __( 'Borde Radio', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' => 50,
					'right' => 50,
					'bottom' => 50,
					'left' => 50,
					'unit' => 'px',
					'isLinked' => true,
				],
				'selectors' => [
					'{{WRAPPER}} .alezux-formacion-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'card_box_shadow',
				'selector' => '{{WRAPPER}} .alezux-formacion-card',
			]
		);

		$this->end_controls_section();

		// --- Sección de Estilo: Tipografía ---
		$this->start_controls_section(
			'section_style_typography',
			[
				'label' => __( 'Tipografía y Colores', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Color Título', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .alezux-formacion-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => __( 'Tipografía Título', 'alezux-members' ),
				'selector' => '{{WRAPPER}} .alezux-formacion-title',
			]
		);

		$this->add_control(
			'price_color',
			[
				'label' => __( 'Color Precio', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#6c5ce7', // Acento Alezux
				'selectors' => [
					'{{WRAPPER}} .alezux-formacion-price' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$args = [
			'post_type' => 'sfwd-courses', // LearnDash courses
			'posts_per_page' => $settings['posts_per_page'],
			'orderby' => $settings['orderby'],
			'order' => $settings['order'],
		];

		$query = new \WP_Query( $args );

		if ( $query->have_posts() ) {
			echo '<div class="alezux-formaciones-grid">';
			
			while ( $query->have_posts() ) {
				$query->the_post();
				$post_id = get_the_ID();
				
				// Obtener datos
				$price = get_post_meta( $post_id, '_alezux_course_price', true );
				$mentors = get_post_meta( $post_id, '_alezux_course_mentors', true );
				$image_url = get_the_post_thumbnail_url( $post_id, 'large' );
				$description = get_the_excerpt();

				// Renderizar Tarjeta
				?>
				<div class="alezux-formacion-card">
					<?php if ( $image_url ) : ?>
						<div class="alezux-formacion-image">
							<a href="<?php the_permalink(); ?>">
								<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php the_title_attribute(); ?>">
							</a>
						</div>
					<?php endif; ?>
					
					<div class="alezux-formacion-content">
						
						<!-- Mentores -->
						<?php if ( ! empty( $mentors ) && is_array( $mentors ) ) : ?>
							<div class="alezux-formacion-mentors">
								<?php foreach ( $mentors as $mentor ) : ?>
									<?php if ( ! empty( $mentor['image'] ) ) : ?>
										<div class="alezux-mentor-avatar" title="<?php echo esc_attr( $mentor['name'] ); ?>">
											<img src="<?php echo esc_url( $mentor['image'] ); ?>" alt="<?php echo esc_attr( $mentor['name'] ); ?>">
										</div>
									<?php endif; ?>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

						<a href="<?php the_permalink(); ?>" class="alezux-formacion-title-link">
							<h3 class="alezux-formacion-title"><?php the_title(); ?></h3>
						</a>

						<div class="alezux-formacion-excerpt">
							<?php echo wp_trim_words( $description, 15 ); ?>
						</div>

						<div class="alezux-formacion-footer">
							<div class="alezux-formacion-price">
								<?php echo esc_html( $price ); ?>
							</div>
							<a href="<?php the_permalink(); ?>" class="alezux-btn-arrow">
								<span class="dashicons dashicons-arrow-right-alt2"></span>
							</a>
						</div>
					</div>
				</div>
				<?php
			}
			
			echo '</div>'; // .alezux-formaciones-grid
			wp_reset_postdata();
		} else {
			echo '<p>' . __( 'No se encontraron formaciones.', 'alezux-members' ) . '</p>';
		}
	}
}
