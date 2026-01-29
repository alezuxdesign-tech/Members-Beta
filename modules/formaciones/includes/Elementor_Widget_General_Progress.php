<?php
namespace Alezux_Members\Modules\Formaciones\Includes;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget de Elementor: Progreso General (Semicírculo y Lista)
 */
class Elementor_Widget_General_Progress extends Widget_Base {

	public function get_name() {
		return 'alezux_general_progress';
	}

	public function get_title() {
		return __( 'Alezux Progreso General', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-gauge';
	}

	public function get_categories() {
		return [ 'alezux-members' ];
	}

	protected function register_controls() {
		// --- SECCIÓN: CONTENIDO GENERAL ---
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Contenido', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_chart',
			[
				'label' => __( 'Mostrar Gráfico', 'alezux-members' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'chart_label',
			[
				'label' => __( 'Etiqueta del Gráfico', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Avance total', 'alezux-members' ),
				'condition' => [ 'show_chart' => 'yes' ],
			]
		);

		$this->add_control(
			'show_list',
			[
				'label' => __( 'Mostrar Lista de Cursos', 'alezux-members' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->end_controls_section();

		// --- SECCIÓN: ESTILO - GRÁFICO ---
		$this->start_controls_section(
			'section_style_chart',
			[
				'label' => __( 'Estilo Gráfico', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [ 'show_chart' => 'yes' ],
			]
		);

		$this->add_control(
			'chart_size',
			[
				'label' => __( 'Tamaño del Gráfico (px)', 'alezux-members' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 150,
						'max' => 500,
					],
				],
				'default' => [
					'size' => 300,
				],
				'selectors' => [
					'{{WRAPPER}} .alezux-general-chart-container' => 'width: {{SIZE}}px; height: calc({{SIZE}}px / 2 + 20px);', // +20px buffer
					'{{WRAPPER}} .alezux-general-chart-svg' => 'width: {{SIZE}}px; height: {{SIZE}}px;', // SVG is square but clipped
				],
			]
		);

		$this->add_control(
			'chart_track_color',
			[
				'label' => __( 'Color de Fondo (Track)', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#333333',
				'selectors' => [
					'{{WRAPPER}} .alezux-chart-track' => 'stroke: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'chart_fill_color_start',
			[
				'label' => __( 'Color Relleno (Inicio)', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#f1c40f',
			]
		);

		$this->add_control(
			'chart_fill_color_end',
			[
				'label' => __( 'Color Relleno (Fin)', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#e67e22',
			]
		);

		$this->add_control(
			'chart_percent_color',
			[
				'label' => __( 'Color Porcentaje', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#f1c40f',
				'selectors' => [
					'{{WRAPPER}} .alezux-chart-percent' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'chart_percent_typography',
				'label' => __( 'Tipografía Porcentaje', 'alezux-members' ),
				'selector' => '{{WRAPPER}} .alezux-chart-percent',
			]
		);

		$this->add_control(
			'chart_label_color',
			[
				'label' => __( 'Color Etiqueta', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .alezux-chart-label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'chart_label_typography',
				'label' => __( 'Tipografía Etiqueta', 'alezux-members' ),
				'selector' => '{{WRAPPER}} .alezux-chart-label',
			]
		);

		$this->end_controls_section();

		// --- SECCIÓN: ESTILO - LISTA DE CURSOS ---
		$this->start_controls_section(
			'section_style_list',
			[
				'label' => __( 'Estilo Lista de Cursos', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [ 'show_list' => 'yes' ],
			]
		);

		$this->add_control(
			'list_gap',
			[
				'label' => __( 'Espacio entre items', 'alezux-members' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .alezux-course-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_control(
			'list_title_color',
			[
				'label' => __( 'Color Título Curso', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .alezux-course-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'list_title_typography',
				'label' => __( 'Tipografía Título', 'alezux-members' ),
				'selector' => '{{WRAPPER}} .alezux-course-title',
			]
		);

		$this->add_control(
			'list_percent_color',
			[
				'label' => __( 'Color Porcentaje Individual', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#f1c40f',
				'selectors' => [
					'{{WRAPPER}} .alezux-course-percent' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'list_percent_typography',
				'label' => __( 'Tipografía Porcentaje', 'alezux-members' ),
				'selector' => '{{WRAPPER}} .alezux-course-percent',
			]
		);

		$this->add_control(
			'list_divider_color',
			[
				'label' => __( 'Color Divisor', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#444444',
				'selectors' => [
					'{{WRAPPER}} .alezux-course-divider' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		
		if ( ! is_user_logged_in() ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="alezux-msg">' . __( 'Debes iniciar sesión para ver el progreso real.', 'alezux-members' ) . '</div>';
				
				// Mock data for editor
				$courses_data = [
					[ 'title' => 'Curso Demo 1', 'percentage' => 45 ],
					[ 'title' => 'Curso Demo 2', 'percentage' => 80 ],
					[ 'title' => 'Curso Demo 3', 'percentage' => 20 ],
				];
				$total_courses = 3;
				$total_progress_sum = 145;
			} else {
				return;
			}
		} else {
			$user_id = get_current_user_id();
			$enrolled_courses = learndash_user_get_enrolled_courses( $user_id, [], true ); // Return IDs only
			
			$courses_data = [];
			$total_progress_sum = 0;
			$total_courses = count( $enrolled_courses );

			foreach ( $enrolled_courses as $course_id ) {
				$progress = learndash_course_progress( [
					'user_id'   => $user_id,
					'course_id' => $course_id,
					'array'     => true,
				] );
				
				$percentage = isset( $progress['percentage'] ) ? intval( $progress['percentage'] ) : 0;
				$total_progress_sum += $percentage;
				
				$courses_data[] = [
					'title'      => get_the_title( $course_id ),
					'percentage' => $percentage,
					'permalink'  => get_permalink( $course_id ),
				];
			}
		}

		// Calculate Global Average
		$average_progress = ( $total_courses > 0 ) ? round( $total_progress_sum / $total_courses ) : 0;

		// --- RENDER ---
		?>
		<div class="alezux-general-progress-wrapper">
			
			<?php if ( 'yes' === $settings['show_chart'] ) : ?>
				<?php 
					// SVG Geometry
					// We want a semicircle.
					// Radius r = 90 (viewport 200x100)
					// Circumference (half) = PI * r = 3.1416 * 90 ≈ 283
					// Dasharray (segmented look): e.g. 5 stroke, 3 gap.
					
					$r = 90;
					$c = M_PI * $r; // Sempercircle length
					$pct = $average_progress / 100;
					$dash_offset = $c * (1 - $pct); 
					// Semicircle starts at 180deg (left) to 0deg (right)? No, usually 270 (-90) to 90.
					// Standard SVG circle starts at 3 o'clock (0deg). 
					// We need dashed stroke for "ticks".
					
					$unique_id = $this->get_id();
				?>
				<div class="alezux-general-chart-container">
					<svg class="alezux-general-chart-svg" viewBox="0 0 200 110" preserveAspectRatio="xMidYMax meet">
						<defs>
							<linearGradient id="grad_<?php echo esc_attr($unique_id); ?>" x1="0%" y1="0%" x2="100%" y2="0%">
								<stop offset="0%" style="stop-color:<?php echo esc_attr($settings['chart_fill_color_start']); ?>;stop-opacity:1" />
								<stop offset="100%" style="stop-color:<?php echo esc_attr($settings['chart_fill_color_end']); ?>;stop-opacity:1" />
							</linearGradient>
						</defs>
						
						<!-- Track (Fondo) -->
						<!-- Path: Arc from left (10,100) to right (190,100) with radius 90 -->
						<path class="alezux-chart-track" d="M 10 100 A 90 90 0 0 1 190 100" fill="none" stroke-width="20" stroke-linecap="round" stroke-dasharray="6 4" />
						
						<!-- Fill (Progreso) -->
						<!-- Same path, but dashed based on percentage -->
						<!-- stroke-dasharray: [Length of filled part] [Length of total path] -->
						<!-- But we want segmented ticks also painted. -->
						<!-- Trick: Use the same dasharray '6 4' but mask it? or simply verify SVG Dash offset logic. -->
						<!-- Easier: Two paths. Top path is colored gradient, but 'stroke-dasharray' matches key value. -->
						<!-- Actually, to "fill" the ticks progressively, we need a mask or clip-path. -->
						
						<mask id="mask_<?php echo esc_attr($unique_id); ?>">
							<!-- White path reveals, Black hides. -->
							<!-- We draw a solid thick white path that represents the progress length -->
							<path d="M 10 100 A 90 90 0 0 1 190 100" fill="none" stroke="white" stroke-width="22" stroke-dasharray="<?php echo $c * $pct; ?> <?php echo $c; ?>" />
						</mask>

						<!-- Colored Path (Apply Mask) -->
						<path class="alezux-chart-fill" d="M 10 100 A 90 90 0 0 1 190 100" 
							fill="none" 
							stroke="url(#grad_<?php echo esc_attr($unique_id); ?>)" 
							stroke-width="20" 
							stroke-linecap="round" 
							stroke-dasharray="6 4"
							mask="url(#mask_<?php echo esc_attr($unique_id); ?>)"
							/>
					</svg>
					
					<div class="alezux-chart-content">
						<div class="alezux-chart-percent"><?php echo esc_html( $average_progress ); ?>%</div>
						<?php if ( $settings['chart_label'] ) : ?>
							<div class="alezux-chart-label"><?php echo esc_html( $settings['chart_label'] ); ?></div>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( 'yes' === $settings['show_list'] ) : ?>
				<div class="alezux-general-list">
					<?php foreach ( $courses_data as $course ) : ?>
						<div class="alezux-course-item">
							<div class="alezux-course-info">
								<a href="<?php echo isset($course['permalink']) ? esc_url($course['permalink']) : '#'; ?>" class="alezux-course-title">
									<?php echo esc_html( $course['title'] ); ?>
								</a>
								<span class="alezux-course-percent"><?php echo esc_html( $course['percentage'] ); ?>%</span>
							</div>
							<div class="alezux-course-divider">
								<div class="alezux-course-divider-fill" style="width: <?php echo esc_attr( $course['percentage'] ); ?>%; background-color: <?php echo esc_attr( $settings['list_percent_color'] ); ?>;"></div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

		</div>
		
		<style>
			.alezux-general-progress-wrapper {
				display: flex;
				flex-direction: column;
				align-items: center;
				/* Default dark background handling depending on theme, widget shouldn't enforce bg unless set in Advanced */
			}
			.alezux-general-chart-container {
				position: relative;
				display: flex;
				justify-content: center;
				margin-bottom: 20px;
				overflow: hidden; /* Hide bottom half of circle space if using full circle SVG tech */
			}
			.alezux-chart-content {
				position: absolute;
				bottom: 0;
				left: 0;
				right: 0;
				text-align: center;
				display: flex;
				flex-direction: column;
				justify-content: flex-end;
				height: 100%;
				padding-bottom: 10px; /* Adjust based on arc height */
			}
			.alezux-chart-percent {
				font-size: 40px;
				font-weight: bold;
				line-height: 1;
				margin-bottom: 5px;
				/* Glow effect simulation */
				text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
			}
			.alezux-chart-label {
				font-size: 14px;
				opacity: 0.8;
			}
			
			/* List Styles */
			.alezux-general-list {
				width: 100%;
			}
			.alezux-course-item {
				margin-bottom: 15px;
			}
			.alezux-course-info {
				display: flex;
				justify-content: space-between;
				align-items: center;
				margin-bottom: 5px;
			}
			.alezux-course-title {
				text-decoration: none;
				font-weight: 500;
				flex-grow: 1;
				margin-right: 10px;
				white-space: nowrap;
				overflow: hidden;
				text-overflow: ellipsis;
			}
			.alezux-course-divider {
				height: 2px;
				width: 100%;
				position: relative;
			}
			.alezux-course-divider-fill {
				height: 100%;
				position: absolute;
				left: 0;
				top: 0;
				box-shadow: 0 0 5px currentColor; /* simple glow */
			}
		</style>
		<?php
	}
}
