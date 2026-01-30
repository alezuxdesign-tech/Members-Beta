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
 * Widget de Elementor: Rendimiento (Analítica de Tiempo)
 */
class Elementor_Widget_Rendimiento extends Widget_Base {

	public function get_name() {
		return 'alezux_rendimiento';
	}

	public function get_title() {
		return __( 'Alezux Rendimiento', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-time-line';
	}

	public function get_categories() {
		return [ 'alezux-members' ];
	}

	protected function register_controls() {
		// --- SECCIÓN: CONTENIDO ---
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Contenido', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title_text',
			[
				'label' => __( 'Título', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Time Spent Analytic', 'alezux-members' ),
                'placeholder' => __( 'Time Spent Analytic', 'alezux-members' ),
			]
		);

        // Labels for Tabs
        $this->add_control(
			'tab_daily_label',
			[
				'label' => __( 'Etiqueta Diario', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Daily', 'alezux-members' ),
			]
		);
        $this->add_control(
			'tab_weekly_label',
			[
				'label' => __( 'Etiqueta Semanal', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Weekly', 'alezux-members' ),
			]
		);
        $this->add_control(
			'tab_monthly_label',
			[
				'label' => __( 'Etiqueta Mensual', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Monthly', 'alezux-members' ),
			]
		);

		$this->end_controls_section();

		// --- SECCIÓN: ESTILO - GENERAL ---
		$this->start_controls_section(
			'section_style_general',
			[
				'label' => __( 'General', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
        
        $this->add_control(
			'container_bg_color',
			[
				'label' => __( 'Color de Fondo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .alezux-rendimiento-wrapper' => 'background-color: {{VALUE}};',
				],
			]
		);

        $this->add_control(
			'container_padding',
			[
				'label' => __( 'Relleno (Padding)', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-rendimiento-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'default' => [
                    'top' => '20',
                    'right' => '20',
                    'bottom' => '25',
                    'left' => '20',
                    'isLinked' => true,
                ],
			]
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'container_border',
				'label' => __( 'Borde', 'alezux-members' ),
				'selector' => '{{WRAPPER}} .alezux-rendimiento-wrapper',
			]
		);

        $this->add_control(
			'container_border_radius',
			[
				'label' => __( 'Radio del Borde', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-rendimiento-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'default' => [
                    'top' => '15',
                    'right' => '15',
                    'bottom' => '15',
                    'left' => '15',
                    'isLinked' => true,
                ],
			]
		);

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'container_box_shadow',
				'label' => __( 'Sombra', 'alezux-members' ),
				'selector' => '{{WRAPPER}} .alezux-rendimiento-wrapper',
			]
		);

        $this->end_controls_section();

        // --- SECCIÓN: ESTILO - TÍTULO ---
        $this->start_controls_section(
			'section_style_title',
			[
				'label' => __( 'Título', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .alezux-rendimiento-title' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .alezux-rendimiento-header svg' => 'fill: {{VALUE}}; stroke: {{VALUE}};',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => __( 'Tipografía', 'alezux-members' ),
				'selector' => '{{WRAPPER}} .alezux-rendimiento-title',
			]
		);

        $this->end_controls_section();

        // --- SECCIÓN: ESTILO - TABS ---
        $this->start_controls_section(
			'section_style_tabs',
			[
				'label' => __( 'Pestañas (Tabs)', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_control(
			'tabs_bg_color',
			[
				'label' => __( 'Fondo Contenedor Tabs', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#f5f5f5',
				'selectors' => [
					'{{WRAPPER}} .alezux-rendimiento-tabs' => 'background-color: {{VALUE}};',
				],
			]
		);

        $this->add_control(
			'tab_item_color',
			[
				'label' => __( 'Color Texto Inactivo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#666666',
				'selectors' => [
					'{{WRAPPER}} .alezux-tab-item' => 'color: {{VALUE}};',
				],
			]
		);

        $this->add_control(
			'tab_item_active_color',
			[
				'label' => __( 'Color Texto Activo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .alezux-tab-item.active' => 'color: {{VALUE}};',
				],
			]
		);

        $this->add_control(
			'tab_item_active_bg',
			[
				'label' => __( 'Fondo Activo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .alezux-tab-item.active' => 'background-color: {{VALUE}}; box-shadow: 0 2px 5px rgba(0,0,0,0.05);',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'tabs_typography',
				'label' => __( 'Tipografía', 'alezux-members' ),
				'selector' => '{{WRAPPER}} .alezux-tab-item',
			]
		);

        $this->end_controls_section();

        // --- SECCIÓN: ESTILO - GRÁFICO ---
        $this->start_controls_section(
			'section_style_chart',
			[
				'label' => __( 'Gráfico', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_control(
			'chart_bar_color',
			[
				'label' => __( 'Color Barras', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#e0e0ff',
				'selectors' => [
					'{{WRAPPER}} .alezux-bar-fill' => 'background: linear-gradient(180deg, {{VALUE}} 0%, rgba(255,255,255,0) 100%);',
				],
			]
		);

        $this->add_control(
			'chart_bar_active_color',
			[
				'label' => __( 'Color Barra Activa / Hoy', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#5655d6',
				'selectors' => [
					'{{WRAPPER}} .alezux-bar-item.is-today .alezux-bar-fill, {{WRAPPER}} .alezux-bar-item:hover .alezux-bar-fill' => 'background: linear-gradient(180deg, {{VALUE}} 0%, rgba(255,255,255,0.2) 100%);',
				],
			]
		);

        $this->add_control(
			'chart_axis_color',
			[
				'label' => __( 'Color Ejes/Texto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#bfbfbf',
				'selectors' => [
					'{{WRAPPER}} .alezux-axis-y span, {{WRAPPER}} .alezux-axis-x span' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .alezux-chart-grid-line' => 'border-top-color: {{VALUE}}; opacity: 0.2;',
				],
			]
		);

        $this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
        
        $user_id = get_current_user_id();
        $is_editor = \Elementor\Plugin::$instance->editor->is_edit_mode();
        
        // --- DATA FETCHING ---
        // --- DATA FETCHING (SQL) ---
        $log = [];
        if ( is_user_logged_in() ) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'alezux_study_log';
            $six_days_ago = date('Y-m-d', strtotime('-6 days'));
            
            // Query: Sum seconds per day for this user, from 6 days ago until now
            $results = $wpdb->get_results( $wpdb->prepare(
                "SELECT date, SUM(seconds) as total_seconds 
                 FROM $table_name 
                 WHERE user_id = %d 
                 AND date >= %s 
                 GROUP BY date",
                $user_id,
                $six_days_ago
            ) );

            if ( $results ) {
                foreach ( $results as $row ) {
                    $log[ $row->date ] = (int) $row->total_seconds;
                }
            }
        } else if ( $is_editor ) {
            // Mock data for editor
            $today = date('Y-m-d');
            $log = [
                date('Y-m-d', strtotime('-1 day')) => 3600,
                date('Y-m-d', strtotime('-2 days')) => 7200,
                date('Y-m-d', strtotime('-3 days')) => 1800,
                $today => 5400,
            ];
        }

        // Helper to format seconds
        // $format_time = function($seconds) {
        //     $h = floor($seconds / 3600);
        //     $m = floor(($seconds % 3600) / 60);
        //     return sprintf('%02dh %02dm', $h, $m);
        // };

        // Process Data for Daily (Last 7 Days)
        $daily_data = [];
        $max_daily = 0;
        $days_labels = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-$i days"));
            $val = isset($log[$d]) ? $log[$d] : 0;
            if ($val > $max_daily) $max_daily = $val;
            
            $day_label = date_i18n('D', strtotime($d)); // Localized day name (Mon, Tue)
            $is_today = ($i === 0);
            
            $daily_data[] = [
                'val' => $val,
                'label' => $day_label,
                'date' => $d,
                'is_today' => $is_today
            ];
        }
        // Calculate Real Max for "Best of" logic
        $real_max_val = 0;
        foreach ($daily_data as $d) {
            if ($d['val'] > $real_max_val) $real_max_val = $d['val'];
        }

        if ($max_daily == 0) $max_daily = 3600; // Prevent div by zero, default to 1h scale
        $max_daily = ceil($max_daily / 1800) * 1800; // Round up to nearest 30min for nice scale

        // --- RENDER HTML ---
		?>
		<div class="alezux-rendimiento-wrapper">
            <div class="alezux-rendimiento-header">
                <div class="alezux-rendimiento-title"><?php echo esc_html( $settings['title_text'] ); ?></div>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="7" y1="17" x2="17" y2="7"></line>
                    <polyline points="7 7 17 7 17 17"></polyline>
                </svg>
            </div>

            <div class="alezux-rendimiento-tabs">
                <div class="alezux-tab-item active" data-tab="daily"><?php echo esc_html( $settings['tab_daily_label'] ); ?></div>
                <div class="alezux-tab-item" data-tab="weekly"><?php echo esc_html( $settings['tab_weekly_label'] ); ?></div>
                <div class="alezux-tab-item" data-tab="monthly"><?php echo esc_html( $settings['tab_monthly_label'] ); ?></div>
            </div>

            <div class="alezux-rendimiento-chart-area" id="chart-daily">
                <!-- Y Axis -->
                <div class="alezux-axis-y">
                    <?php 
                        $steps = 4;
                        for ($i = $steps; $i >= 0; $i--) {
                            $seconds = ($max_daily / $steps) * $i;
                            echo '<span>' . gmdate("H:i:s", $seconds) . '</span>';
                        }
                    ?>
                </div>

                <!-- Grid & Bars -->
                <div class="alezux-chart-plot">
                    <?php 
                        // Grid Lines
                        for ($i = 0; $i <= $steps; $i++) {
                            $top_pct = ($i / $steps) * 100;
                            echo '<div class="alezux-chart-grid-line" style="top: ' . $top_pct . '%;"></div>';
                        }
                    ?>
                    
                    <div class="alezux-bars-container">
                        <?php foreach($daily_data as $data): 
                            $height_pct = ($data['val'] / $max_daily) * 100;
                            $formatted_time = gmdate("H:i:s", $data['val']);
                            $is_best = ($data['val'] > 0 && $data['val'] === $real_max_val);
                        ?>
                            <div class="alezux-bar-column">
                                <div class="alezux-bar-item <?php echo $data['is_today'] ? 'is-today' : ''; ?> <?php echo $is_best ? 'is-best' : ''; ?>" style="height: <?php echo $height_pct; ?>%;">
                                    <div class="alezux-bar-fill"></div>
                                    <div class="alezux-bar-tooltip">
                                        <?php if($is_best): ?>
                                            <div class="tooltip-icon">🔥</div>
                                        <?php endif; ?>
                                        <div class="tooltip-time"><?php echo $formatted_time; ?></div>
                                        <?php if($is_best): ?>
                                            <div class="tooltip-meta"><?php _e('Best of the week!', 'alezux-members'); ?></div>
                                        <?php elseif($data['is_today']): ?>
                                            <div class="tooltip-meta"><?php _e('Today', 'alezux-members'); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="alezux-axis-x-label"><?php echo $data['label']; ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Placeholders for Weekly/Monthly (Logic could be expanded similarly via JS or PHP) -->
            <div class="alezux-rendimiento-chart-area" id="chart-weekly" style="display:none;">
                <div style="padding: 40px; text-align: center; color: #999;"><?php _e('Weekly view coming soon', 'alezux-members'); ?></div>
            </div>
            <div class="alezux-rendimiento-chart-area" id="chart-monthly" style="display:none;">
                <div style="padding: 40px; text-align: center; color: #999;"><?php _e('Monthly view coming soon', 'alezux-members'); ?></div>
            </div>

		</div>

        <script>
        jQuery(document).ready(function($) {
            $('.alezux-rendimiento-wrapper .alezux-tab-item').on('click', function() {
                var tab = $(this).data('tab');
                var $wrapper = $(this).closest('.alezux-rendimiento-wrapper');
                
                $wrapper.find('.alezux-tab-item').removeClass('active');
                $(this).addClass('active');
                
                $wrapper.find('.alezux-rendimiento-chart-area').hide();
                $wrapper.find('#chart-' + tab).fadeIn(200);
            });
        });
        </script>

        <style>
            .alezux-rendimiento-wrapper {
                font-family: 'Roboto', sans-serif;
                overflow: hidden;
            }
            .alezux-rendimiento-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
            }
            .alezux-rendimiento-title {
                font-size: 18px;
                font-weight: 600;
            }
            .alezux-rendimiento-tabs {
                display: flex;
                border-radius: 8px;
                padding: 4px;
                margin-bottom: 30px;
            }
            .alezux-tab-item {
                flex: 1;
                text-align: center;
                padding: 8px 12px;
                font-size: 14px;
                font-weight: 500;
                cursor: pointer;
                border-radius: 6px;
                transition: all 0.3s;
            }
            
            /* Chart Layout */
            .alezux-rendimiento-chart-area {
                display: flex;
                height: 250px;
                width: 100%;
            }
            .alezux-axis-y {
                width: 60px;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                padding-right: 10px;
                font-size: 11px;
                text-align: right;
                padding-bottom: 20px; /* Offset for x labels */
            }
            .alezux-axis-y span {
                transform: translateY(-50%);
            }
            .alezux-axis-y span:first-child { transform: translateY(0); } /* Top */
            .alezux-axis-y span:last-child { transform: translateY(0); } /* Bottom */
            
            .alezux-chart-plot {
                flex-grow: 1;
                position: relative;
                border-left: 0px solid #eee;
                border-bottom: 0px solid #eee;
                display: flex;
                align-items: flex-end;
            }
            
            .alezux-chart-grid-line {
                position: absolute;
                left: 0;
                right: 0;
                border-top: 1px dashed #ccc;
                z-index: 0;
            }
            
            .alezux-bars-container {
                display: flex;
                width: 100%;
                height: 100%;
                justify-content: space-between;
                align-items: flex-end;
                z-index: 1;
                padding: 0 10px;
            }
            
            .alezux-bar-column {
                flex: 1;
                height: 100%;
                display: flex;
                flex-direction: column;
                justify-content: flex-end;
                align-items: center;
                position: relative;
            }
            
            .alezux-bar-item {
                width: 60%;
                max-width: 40px;
                min-height: 4px; /* Ensure visible even if 0 */
                border-radius: 6px 6px 0 0;
                position: relative;
                transition: height 0.5s ease;
                cursor: pointer;
            }
            
            .alezux-bar-fill {
                width: 100%;
                height: 100%;
                border-radius: 6px 6px 0 0;
                opacity: 0.8;
                transition: opacity 0.3s;
            }
            .alezux-bar-item:hover .alezux-bar-fill {
                opacity: 1;
            }
            
            .alezux-bar-active-color-placeholder {} /* Dummy for finding linter position if needed */
            
            .alezux-bar-item.is-today .alezux-bar-fill, 
            .alezux-bar-item.is-best .alezux-bar-fill,
            .alezux-bar-item:hover .alezux-bar-fill {
                /* Color controlled by chart_bar_active_color */
                opacity: 1;
            }
            
            .alezux-axis-x-label {
                margin-top: 8px;
                font-size: 12px;
                color: #999;
            }
            
            /* Tooltip */
            .alezux-bar-tooltip {
                position: absolute;
                bottom: 100%;
                left: 50%;
                transform: translateX(-50%) translateY(10px); /* Initially hidden lower */
                background: #fff;
                padding: 8px 12px;
                border-radius: 8px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.15);
                white-space: nowrap;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                pointer-events: none;
                z-index: 10;
                text-align: center;
                display: flex;
                flex-direction: column;
                align-items: center;
                min-width: 80px;
            }
            .alezux-bar-item.is-today .alezux-bar-tooltip,
            .alezux-bar-item:hover .alezux-bar-tooltip {
                opacity: 1;
                visibility: visible;
                transform: translateX(-50%) translateY(-10px);
            }
            
            /* Always show tooltip for best day to match image style? Or just hover? 
               Image shows it persistent. Let's make it persistent if it's the best day, 
               but that might clutter if multiple widgets. Let's stick to hover + initial animation or just hover.
               The image implies it's a hover state or selected state on the bar. 
               Let's make it appear on hover AND if it is the 'is-best' one maybe show it by default? 
               No, that might be annoying. Sticky hover is better.
            */ 
            
            .tooltip-icon {
                font-size: 16px;
                margin-bottom: 4px;
                line-height: 1;
            }
            .tooltip-time {
                font-weight: 700;
                font-size: 14px;
                color: #000;
                line-height: 1.2;
            }
            .tooltip-meta {
                font-size: 11px;
                color: #888;
                margin-top: 2px;
                line-height: 1.2;
            }
            .alezux-bar-tooltip::after {
                content: '';
                position: absolute;
                top: 100%;
                left: 50%;
                margin-left: -6px;
                border-width: 6px;
                border-style: solid;
                border-color: #fff transparent transparent transparent;
            }
        </style>
		<?php
	}
}
