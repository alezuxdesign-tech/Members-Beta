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
		// Sección de Contenido / Configuración Principal
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Configuración', 'alezux-members' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'color_base',
			[
				'label' => __( 'Color Principal (Actividad)', 'alezux-members' ),
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

        // Sección de Estilo: Meses
        $this->start_controls_section(
            'section_style_months',
            [
                'label' => __( 'Estilo de Meses', 'alezux-members' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'typography_months',
                'label' => __( 'Tipografía', 'alezux-members' ),
                'selector' => '{{WRAPPER}} .alezux-heatmap-months',
            ]
        );

        $this->add_control(
            'color_months',
            [
                'label' => __( 'Color', 'alezux-members' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#767676',
                'selectors' => [
                    '{{WRAPPER}} .alezux-heatmap-months' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();

        // Sección de Estilo: Leyenda
        $this->start_controls_section(
            'section_style_legend',
            [
                'label' => __( 'Estilo de Leyenda', 'alezux-members' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'typography_legend',
                'label' => __( 'Tipografía', 'alezux-members' ),
                'selector' => '{{WRAPPER}} .alezux-heatmap-legend',
            ]
        );

        $this->add_control(
            'color_legend',
            [
                'label' => __( 'Color', 'alezux-members' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#767676',
                'selectors' => [
                    '{{WRAPPER}} .alezux-heatmap-legend' => 'color: {{VALUE}}',
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

		// Get data for current Year (Jan 1 to Dec 31)
		$current_year = current_time( 'Y' );
		$start_date = $current_year . '-01-01';
		$end_date   = $current_year . '-12-31';

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
				font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif;
                width: 100%; /* Full width */
			}
            
            /* Wrapper for scrolling both months and grid together */
            .alezux-heatmap-scroll-wrapper {
                overflow-x: auto;
                padding-bottom: 10px;
                width: 100%;
            }

            .alezux-heatmap-content {
                display: inline-flex; /* content grows horizontally */
                flex-direction: column;
                gap: 5px;
                min-width: 100%; /* Ensure it fills at least the container */
            }

            .alezux-heatmap-months {
                display: grid;
                grid-template-columns: repeat(53, 13px); /* 13px = 10px width + 3px gap roughly, needs precise mapping */
                grid-auto-columns: 13px; 
                grid-auto-flow: column;
                height: 15px;
                font-size: 10px; /* Default, overridden by control */
                line-height: 15px;
                color: #767676; /* Default, overridden by control */
            }

            /* Dark mode text adjustment - ONLY if control not set (CSS specificity will handle override if ID used but here we use selectors) */
            @media (prefers-color-scheme: dark) {
                .alezux-heatmap-months {
                    color: inherit; /* Let elementor control handle it mostly, or fallback */
                }
            }
            
            /* Ensure text doesn't overflow */
            .alezux-month-label {
                grid-column-end: span 3; 
                overflow: visible;
                white-space: nowrap;
            }

			.alezux-heatmap-grid {
				display: grid;
				grid-template-rows: repeat(7, 10px); 
				grid-auto-flow: column;
				gap: 3px;
			}

			.alezux-heatmap-day {
				width: 10px; 
				height: 10px;
				border-radius: 2px;
				background-color: #ebedf0;
				position: relative;
			}
            
            /* Make sure grid fills space if needed, though heatmaps are usually fixed size squares. 
               To make it "adjust to container", we let scroll wrapper handle overflow. 
               If user wants squares to stretch, that's different mechanics (SVG usually). 
               PHP Grid is hard to justify width without SVG.
               We keep fixed square size but allow full container width scrolling. */

            @media (prefers-color-scheme: dark) {
                .alezux-heatmap-day {
                    background-color: #161b22;
                }
            }

			.alezux-heatmap-day[data-level="1"] { background-color: #9be9a8; }
			.alezux-heatmap-day[data-level="2"] { background-color: #40c463; }
			.alezux-heatmap-day[data-level="3"] { background-color: #30a14e; }
			.alezux-heatmap-day[data-level="4"] { background-color: #216e39; }

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
				z-index: 100;
				pointer-events: none;
				margin-bottom: 5px;
			}

            .alezux-heatmap-legend {
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 4px;
                font-size: 11px; /* Default */
                color: #767676; /* Default */
            }
            .legend-item {
                width: 10px;
                height: 10px;
                border-radius: 2px;
            }
		</style>

		<div class="alezux-heatmap-container">
            <div class="alezux-heatmap-scroll-wrapper">
                <div class="alezux-heatmap-content">
                    <?php
                    // Logic setup
                    $jan_1_timestamp = strtotime( $start_date );
                    if ( date( 'w', $jan_1_timestamp ) == 0 ) {
                        $start_loop_timestamp = $jan_1_timestamp;
                    } else {
                        $start_loop_timestamp = strtotime( 'last sunday', $jan_1_timestamp );
                    }

                    $month_labels = [
                        1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun',
                        7 => 'Jul', 8 => 'Ago', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'
                    ];
                    $month_positions = [];
                    $temp_date = $start_loop_timestamp;
                    
                    // Identify week columns where month changes
                    for ( $col = 0; $col < 53; $col++ ) {
                        // We check the month of the "majority" of the week or just the start
                        // A nice heuristic: check the month of the 4th day of the week (Wednesday)
                        // This ensures the label is centered on the month's bulk
                        $mid_week_date = strtotime( '+3 days', $temp_date );
                        $m = date( 'n', $mid_week_date );
                        $y = date( 'Y', $mid_week_date );

                        // Only label if it's the current year OR it's Jan of current year (handling edge case)
                        if ( $y == $current_year && ! isset( $month_positions[ $m ] ) ) {
                             $month_positions[ $m ] = $col;
                        }
                        
                        $temp_date = strtotime( '+1 week', $temp_date );
                    }
                    ?>

                    <div class="alezux-heatmap-months">
                        <?php
                        foreach ( $month_positions as $month_num => $col_index ) {
                            if ( isset( $month_labels[ $month_num ] ) ) {
                                // +1 for CSS Grid 1-index
                                $col_css = $col_index + 1;
                                echo sprintf( 
                                    '<span class="alezux-month-label" style="grid-column-start: %d;">%s</span>', 
                                    $col_css, 
                                    $month_labels[ $month_num ] 
                                );
                            }
                        }
                        ?>
                    </div>

                    <div class="alezux-heatmap-grid">
                        <?php
                        // Render Days
                        $current_loop_date = $start_loop_timestamp;
                        
                        for ( $i = 0; $i < (53 * 7); $i++ ) {
                            $loop_date_str = date( 'Y-m-d', $current_loop_date );
                            $loop_year = date( 'Y', $current_loop_date );
                            
                            $seconds = 0;
                            if ( $loop_year == $current_year ) {
                                $seconds = isset( $activity_map[ $loop_date_str ] ) ? $activity_map[ $loop_date_str ] : 0;
                            }
                            
                            $minutes = round( $seconds / 60 );
                            
                            $level = 0;
                            if ( $seconds > 0 ) {
                                if ( $seconds >= ($level_step * 3) ) $level = 4;
                                elseif ( $seconds >= ($level_step * 2) ) $level = 3;
                                elseif ( $seconds >= $level_step ) $level = 2;
                                else $level = 1;
                            }
                            
                            // Tooltip text (same as before)
                            // $tooltip = sprintf( '%s min el %s', $minutes, date_i18n( get_option( 'date_format' ), $current_loop_date ) ); -> No change needed

                    
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
