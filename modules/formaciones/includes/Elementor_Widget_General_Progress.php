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
						'max' => 600,
					],
				],
				'default' => [
					'size' => 350,
				],
				'selectors' => [
					'{{WRAPPER}} .alezux-general-chart-container' => 'width: {{SIZE}}px;', 
				],
			]
		);
		
		$this->add_control(
			'chart_ticks_count',
			[
				'label' => __( 'Cantidad de Segmentos', 'alezux-members' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 10,
				'max' => 60,
				'step' => 1,
				'default' => 30,
			]
		);

		$this->add_control(
			'chart_track_color',
			[
				'label' => __( 'Color Inactivos', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
			]
		);

		$this->add_control(
			'chart_fill_color_start',
			[
				'label' => __( 'Color Activos (Glow)', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#FFB800', // Gold/Yellow
			]
		);

		$this->add_control(
			'chart_percent_color',
			[
				'label' => __( 'Color Porcentaje', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffb800',
				'selectors' => [
					'{{WRAPPER}} .alezux-chart-percent' => 'color: {{VALUE}}; text-shadow: 0 0 15px {{VALUE}};',
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
				echo '<div class="alezux-msg" style="color:#fff;">' . __( 'Debes iniciar sesión para ver el progreso real.', 'alezux-members' ) . '</div>';
				
				// Mock data for editor
				$courses_data = [
					[ 'title' => 'Curso Demo 1', 'percentage' => 45 ],
					[ 'title' => 'Curso Demo 2', 'percentage' => 80 ],
					[ 'title' => 'Curso Demo 3', 'percentage' => 20 ],
				];
				$total_courses = 3;
				$total_progress_sum = 145;
				$average_progress = 48; // Mock avg
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
			// Calculate Global Average
			$average_progress = ( $total_courses > 0 ) ? round( $total_progress_sum / $total_courses ) : 0;
		}


		// --- RENDER ---
		?>
		<div class="alezux-general-progress-wrapper">
			
			<?php if ( 'yes' === $settings['show_chart'] ) : ?>
				<?php 
					// SEGMENTED CHART LOGIC
					$unique_id = $this->get_id();
					$ticks_count = isset($settings['chart_ticks_count']) ? intval($settings['chart_ticks_count']) : 30;
					$ticks_active = round( ($average_progress / 100) * $ticks_count );
					
					// Viewport 400x220 to allow nice padding and glow
					$cx = 200;
					$cy = 200; // Semicircle bottom center
					$r = 160;
					$tick_length = 40; 
					$start_angle = -180;
					$end_angle = 0;
					$total_angle = 180;
					$step_angle = $total_angle / ($ticks_count - 1); // Spread over 180 deg
					
					$active_color = $settings['chart_fill_color_start'];
					$inactive_color = $settings['chart_track_color'];
				?>
				<div class="alezux-general-chart-container">
					<svg class="alezux-general-chart-svg" viewBox="0 0 400 230" preserveAspectRatio="xMidYMax meet">
						<defs>
							<filter id="glow-<?php echo esc_attr($unique_id); ?>" x="-50%" y="-50%" width="200%" height="200%">
								<feGaussianBlur stdDeviation="4" result="coloredBlur"/>
								<feMerge>
									<feMergeNode in="coloredBlur"/>
									<feMergeNode in="SourceGraphic"/>
								</feMerge>
							</filter>
						</defs>
						
						<!-- Ticks -->
						<g class="alezux-chart-ticks">
							<?php for ($i = 0; $i < $ticks_count; $i++) : 
								$is_active = $i < $ticks_active;
								$angle_deg = $start_angle + ($i * $step_angle);
								$angle_rad = deg2rad($angle_deg);
								
								// Calculate start and end points of the tick line
								// Outer point
								$x1 = $cx + ($r * cos($angle_rad));
								$y1 = $cy + ($r * sin($angle_rad));
								
								// Inner point
								$x2 = $cx + ( ($r - $tick_length) * cos($angle_rad));
								$y2 = $cy + ( ($r - $tick_length) * sin($angle_rad));
								
								$color = $is_active ? $active_color : $inactive_color;
								$filter = $is_active ? "url(#glow-{$unique_id})" : "none";
								$opacity = $is_active ? 1 : 1; // Inactive usually fully visible but white
							?>
								<line 
									x1="<?php echo $x1; ?>" y1="<?php echo $y1; ?>" 
									x2="<?php echo $x2; ?>" y2="<?php echo $y2; ?>" 
									stroke="<?php echo esc_attr($color); ?>" 
									stroke-width="8" 
									stroke-linecap="round"
									style="filter: <?php echo $filter; ?>;"
								/>
							<?php endfor; ?>
						</g>
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
				font-family: 'Roboto', sans-serif; /* Fallback */
			}
			.alezux-general-chart-container {
				position: relative;
				display: flex;
				justify-content: center;
				margin-bottom: 20px;
				/* Aspect ratio maintenance not critical as width controls SVG size */
			}
			.alezux-general-chart-svg {
				overflow: visible; /* Allow glow to spill */
			}
			
			.alezux-chart-content {
				position: absolute;
				bottom: 5px; /* Adjust vertical pos of text */
				left: 0;
				right: 0;
				text-align: center;
				display: flex;
				flex-direction: column;
				justify-content: flex-end;
				pointer-events: none;
			}
			.alezux-chart-percent {
				font-size: 50px;
				font-weight: 700;
				line-height: 1;
				margin-bottom: 5px;
				transition: all 0.3s;
			}
			.alezux-chart-label {
				font-size: 14px;
				font-weight: 400;
				opacity: 0.9;
				text-transform: uppercase;
				letter-spacing: 1px;
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
				margin-bottom: 8px;
			}
			.alezux-course-title {
				text-decoration: none;
				font-weight: 500;
				flex-grow: 1;
				margin-right: 15px;
				white-space: nowrap;
				overflow: hidden;
				text-overflow: ellipsis;
				font-size: 15px;
			}
			.alezux-course-percent {
				font-weight: 700;
				font-size: 14px;
			}
			.alezux-course-divider {
				height: 2px;
				width: 100%;
				position: relative;
				border-radius: 2px;
				overflow: hidden;
			}
			.alezux-course-divider-fill {
				height: 100%;
				position: absolute;
				left: 0;
				top: 0;
				border-radius: 2px;
				box-shadow: 0 0 8px currentColor; /* simple glow matching color */
			}
		</style>
		<?php
	}
}
