<?php
namespace Alezux_Members\Modules\Estudiantes\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Elementor_Widget_Student_Heatmap extends \Elementor\Widget_Base {

	public function get_name() {
		return 'alezux_student_heatmap';
	}

	public function get_title() {
		return __( 'Mapa de Calor de Estudio', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-calendar';
	}

	public function get_categories() {
		return [ 'alezux-lms' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Configuración', 'alezux-members' ),
			]
		);

		$this->add_control(
			'color_base',
			[
				'label' => __( 'Color Principal', 'alezux-members' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#40c463', // GitHub Green
				'selectors' => [
					'{{WRAPPER}} .alezux-heatmap-day[data-level="4"]' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .alezux-heatmap-day[data-level="3"]' => 'background-color: {{VALUE}}CC', // 80%
					'{{WRAPPER}} .alezux-heatmap-day[data-level="2"]' => 'background-color: {{VALUE}}80', // 50%
					'{{WRAPPER}} .alezux-heatmap-day[data-level="1"]' => 'background-color: {{VALUE}}40', // 25%
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		if ( ! is_user_logged_in() ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="alezux-alert">Debes iniciar sesión para ver tu mapa de calor.</div>';
			}
			return;
		}

		$user_id = get_current_user_id();
		global $wpdb;
		$table_name = $wpdb->prefix . 'alezux_study_log';

		// Verificar tabla
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
			echo 'Tabla de actividad no encontrada.';
			return;
		}

		// Get data for last 365 days
		$end_date = current_time( 'Y-m-d' );
		$start_date = date( 'Y-m-d', strtotime( '-365 days', strtotime( $end_date ) ) );

		$results = $wpdb->get_results( $wpdb->prepare(
			"SELECT date, SUM(seconds) as total_seconds 
			 FROM $table_name 
			 WHERE user_id = %d AND date BETWEEN %s AND %s 
			 GROUP BY date",
			$user_id, $start_date, $end_date
		) );

		// Map results to easy lookup array
		$activity_map = [];
		$max_seconds = 0;
		foreach ( $results as $row ) {
			$activity_map[ $row->date ] = intval( $row->total_seconds );
			if ( $row->total_seconds > $max_seconds ) {
				$max_seconds = intval( $row->total_seconds );
			}
		}

		// Calculations for levels (quartiles roughly)
		$level_step = $max_seconds > 0 ? $max_seconds / 4 : 1;

		?>
		<style>
			.alezux-heatmap-container {
				display: flex;
				flex-direction: column;
				gap: 10px;
				font-family: sans-serif;
			}
			.alezux-heatmap-grid {
				display: grid;
				grid-template-rows: repeat(7, 12px);
				grid-auto-flow: column;
				gap: 3px;
				overflow-x: auto;
				padding-bottom: 5px;
			}
			.alezux-heatmap-day {
				width: 12px;
				height: 12px;
				border-radius: 2px;
				background-color: #ebedf0; /* Default empty color */
				position: relative;
			}
            /* Dark mode support fallback */
            @media (prefers-color-scheme: dark) {
                .alezux-heatmap-day {
                    background-color: #161b22;
                }
            }
            /* Default Levels (Will be overridden by Elementor control if set, but good as fallback) */
			.alezux-heatmap-day[data-level="1"] { background-color: #9be9a8; }
			.alezux-heatmap-day[data-level="2"] { background-color: #40c463; }
			.alezux-heatmap-day[data-level="3"] { background-color: #30a14e; }
			.alezux-heatmap-day[data-level="4"] { background-color: #216e39; }

			/* Tooltip simple CSS */
			.alezux-heatmap-day:hover::after {
				content: attr(data-tooltip);
				position: absolute;
				bottom: 100%;
				left: 50%;
				transform: translateX(-50%);
				background: rgba(0,0,0,0.8);
				color: white;
				padding: 4px 8px;
				border-radius: 4px;
				font-size: 11px;
				white-space: nowrap;
				z-index: 10;
				pointer-events: none;
				margin-bottom: 5px;
			}
            .alezux-heatmap-legend {
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 4px;
                font-size: 11px;
                color: #666;
            }
            .legend-item {
                width: 10px;
                height: 10px;
                border-radius: 2px;
            }
		</style>

		<div class="alezux-heatmap-container">
			<div class="alezux-heatmap-grid">
				<?php
				// Render 365 days roughly ending today
                // To align weeks properly (start on Sunday or Monday), we might need padding.
                // For simplicity, we just loop last 52 weeks * 7 days.
                
                // Start date adjusted to start of week of 52 weeks ago
                $start_of_week_timestamp = strtotime('last sunday', strtotime($start_date));
                // Wait, GitHub starts left to right, cols are weeks, rows are days (Sun-Sat or Mon-Sun).
                
                $current_loop_date = $start_of_week_timestamp;
                $today_timestamp = strtotime(current_time('Y-m-d'));
                
                // We render 53 weeks to cover full year nicely
                for ( $i = 0; $i < (53 * 7); $i++ ) {
                    $loop_date_str = date( 'Y-m-d', $current_loop_date );
                    
                    // Break if future (optional, GitHub shows full grid usually, but we don't want future squares hoverable maybe)
                    if ( $current_loop_date > $today_timestamp ) {
                       // Render empty placeholders or break? GitHub renders placeholders.
                    }

                    $seconds = isset( $activity_map[ $loop_date_str ] ) ? $activity_map[ $loop_date_str ] : 0;
                    $minutes = round( $seconds / 60 );
                    
                    $level = 0;
                    if ( $seconds > 0 ) {
                        if ( $seconds >= ($level_step * 3) ) $level = 4;
                        elseif ( $seconds >= ($level_step * 2) ) $level = 3;
                        elseif ( $seconds >= $level_step ) $level = 2;
                        else $level = 1;
                    }
                    
                    // Tooltip text
                    $tooltip = sprintf( '%s min el %s', $minutes, date_i18n( get_option( 'date_format' ), $current_loop_date ) );

                    echo sprintf( 
                        '<div class="alezux-heatmap-day" data-level="%d" data-tooltip="%s"></div>', 
                        $level, 
                        esc_attr( $tooltip ) 
                    );

                    $current_loop_date = strtotime( '+1 day', $current_loop_date );
                }
				?>
			</div>
            <div class="alezux-heatmap-legend">
                <span>Menos</span>
                <div class="legend-item" style="background-color: #ebedf0"></div>
                <div class="legend-item alezux-heatmap-day" data-level="1"></div>
                <div class="legend-item alezux-heatmap-day" data-level="2"></div>
                <div class="legend-item alezux-heatmap-day" data-level="3"></div>
                <div class="legend-item alezux-heatmap-day" data-level="4"></div>
                <span>Más</span>
            </div>
		</div>

		<?php
	}
}
