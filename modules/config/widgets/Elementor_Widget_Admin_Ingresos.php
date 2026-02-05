<?php
namespace Alezux_Members\Modules\Config\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Alezux_Members\Modules\Config\Includes\Admin_Dashboard_Stats;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Elementor_Widget_Admin_Ingresos extends Widget_Base {

	public function get_name() {
		return 'alezux_admin_revenue';
	}

	public function get_title() {
		return __( 'Admin: Ingresos (7 Días)', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-bar-chart';
	}

	public function get_categories() {
		return [ 'alezux-lms' ];
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
				'default' => __( 'Ingresos Recientes', 'alezux-members' ),
			]
		);
        
        $this->add_control(
			'simulate_data',
			[
				'label' => __( 'Simular datos (Demo)', 'alezux-members' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => 'Activa esto para ver el gráfico con datos falsos si aún no tienes ventas.',
				'default' => 'no',
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

        // --- SECCIÓN: ESTILO - GRÁFICO (CHART) ---
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
					'{{WRAPPER}} .alezux-axis-y span, {{WRAPPER}} .alezux-axis-xspan' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .alezux-chart-grid-line' => 'border-top-color: {{VALUE}}; opacity: 0.2;',
                    '{{WRAPPER}} .alezux-axis-x-label' => 'color: {{VALUE}};',
				],
			]
		);

        $this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
        
        // Obtener datos de ingresos (desde Admin_Dashboard_Stats o lógica directa)
        // Admin_Dashboard_Stats::get_recent_revenue() retorna un array de objetos con date y total
        // Necesitamos procesarlo para el estilo de Financial_Performance_Widget (últimos 7 días)

        $db_data = Admin_Dashboard_Stats::get_recent_revenue(); // Asume que retorna ultimos 30 dias o similar
        
        // Convertir a mapa date => total
        $log = [];
        if ( ! empty($db_data) ) {
            foreach($db_data as $row) {
                // $row puede ser objeto o array según implementación de get_recent_revenue
                $date = is_object($row) ? $row->date : $row['date'];
                $total = is_object($row) ? $row->total : $row['total'];
                $log[$date] = (float)$total;
            }
        }

        // Demo Data
        if ( 'yes' === $settings['simulate_data'] || (empty($log) && \Elementor\Plugin::$instance->editor->is_edit_mode()) ) {
            $today_ts = current_time('timestamp');
             for ($i = 6; $i >= 0; $i--) {
                $d = date('Y-m-d', strtotime("-$i days", $today_ts));
                if (!isset($log[$d])) $log[$d] = rand(50, 200) + (rand(0,99)/100);
             }
        }
        
        // Generar Array de 7 Días
        $chart_data = [];
        $max_val = 0;
        $total_period = 0;
        $wp_local_timestamp = current_time('timestamp');
        
        // Generar últimos 7 días explícitos, terminando en Hoy
        // Financial Widget original mostraba semana lun-dom, aqui haremos "Últimos 7 días" rodantes para Dashboard
        // O si quieres igual a Finanzas (semana calendario), copiamos su lógica.
        // El usuario dijo "igual al widget solo que ... ultimos 7 días". Interpretacion: Rolling 7 days.
        
        for ($i = 6; $i >= 0; $i--) {
            $day_ts = strtotime("-$i days", $wp_local_timestamp);
            $d = date('Y-m-d', $day_ts);
            
            $val = isset($log[$d]) ? $log[$d] : 0;
            if ($val > $max_val) $max_val = $val;
            $total_period += $val;
            
            $is_today = ($d === date('Y-m-d', $wp_local_timestamp));
            
            $chart_data[] = [
                'val' => $val,
                'label' => date_i18n('D', $day_ts), // Mon, Tue...
                'full_date' => date_i18n(get_option('date_format'), $day_ts),
                'is_today' => $is_today
            ];
        }

        // Calcular Escala Eje Y
        if ($max_val == 0) $max_val = 100;
        $magnitude = pow(10, floor(log10($max_val)));
        $max_val = ceil($max_val / ($magnitude/2)) * ($magnitude/2);

        $currency_symbol = '$'; // TODO: Get from settings

        // Render Func Helpers
        $format_axis = function($amount) use ($currency_symbol) {
             if ($amount >= 1000) return $currency_symbol . number_format($amount/1000, 1) . 'k';
             return $currency_symbol . number_format($amount, 0);
        };
        $format_money = function($amount) use ($currency_symbol) {
             return $currency_symbol . number_format($amount, 2);
        };

		?>
		<div class="alezux-rendimiento-wrapper">
            <div class="alezux-rendimiento-header">
                <div class="alezux-rendimiento-title">
                    <?php echo esc_html( $settings['title_text'] ); ?>
                </div>
                <!-- Total pill aligned to right -->
                 <div class="alezux-revenue-pill">
                     <?php echo $format_money($total_period); ?>
                 </div>
            </div>

            <!-- Chart Area -->
             <div class="alezux-rendimiento-chart-area">
                <!-- Y Axis -->
                <div class="alezux-axis-y">
                    <?php 
                        $steps = 4;
                        for ($i = $steps; $i >= 0; $i--) {
                            $amount = ($max_val / $steps) * $i;
                            echo '<span>' . $format_axis($amount) . '</span>';
                        }
                    ?>
                </div>

                <!-- Grid & Bars -->
                <div class="alezux-chart-plot">
                    <?php 
                        for ($i = 0; $i <= $steps; $i++) {
                            $top_pct = ($i / $steps) * 100;
                            echo '<div class="alezux-chart-grid-line" style="top: ' . $top_pct . '%;"></div>';
                        }
                    ?>
                    
                    <div class="alezux-bars-container">
                        <?php foreach($chart_data as $data): 
                            $height_pct = ($max_val > 0) ? ($data['val'] / $max_val) * 100 : 0;
                            $formatted_value = $format_money($data['val']);
                        ?>
                            <div class="alezux-bar-column">
                                <div class="alezux-bar-item <?php echo $data['is_today'] ? 'is-today' : ''; ?>" style="height: <?php echo $height_pct; ?>%;">
                                    <div class="alezux-bar-fill"></div>
                                    <div class="alezux-bar-tooltip">
                                        <div class="tooltip-time"><?php echo $formatted_value; ?></div>
                                        <div class="tooltip-meta"><?php echo $data['full_date']; ?></div>
                                    </div>
                                </div>
                                <div class="alezux-axis-x-label"><?php echo $data['label']; ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
		</div>

        <style>
            /* Reutilizando estilos de Financial_Performance_Widget (Alezux Ingresos) */
            .alezux-rendimiento-wrapper {
                font-family: 'Roboto', sans-serif;
                overflow: hidden;
                /* El borde y fondo son manejados por controles Elementor, defaults aqui */
            }
            .alezux-rendimiento-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 25px;
            }
            .alezux-rendimiento-title {
                font-size: 18px;
                font-weight: 600;
            }
            .alezux-revenue-pill {
                background: rgba(86, 85, 214, 0.1);
                color: #5655d6;
                padding: 4px 12px;
                border-radius: 20px;
                font-weight: 700;
                font-size: 14px;
            }

            /* Chart Layout */
            .alezux-rendimiento-chart-area {
                display: flex;
                height: 200px; /* Un poco mas compacto que el full dashboard */
                width: 100%;
            }
            .alezux-axis-y {
                width: 50px;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                padding-right: 8px;
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
                padding: 0 5px;
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
                width: 50%;
                max-width: 30px;
                min-height: 4px; /* Ensure visible even if 0 */
                border-radius: 4px 4px 0 0;
                position: relative;
                transition: height 0.5s ease;
                cursor: pointer;
            }
            
            .alezux-bar-fill {
                width: 100%;
                height: 100%;
                border-radius: 4px 4px 0 0;
                opacity: 0.8;
                transition: opacity 0.3s;
                /* Background set by controls */
            }
            .alezux-bar-item:hover .alezux-bar-fill {
                opacity: 1;
            }
            
            .alezux-axis-x-label {
                margin-top: 8px;
                font-size: 12px;
                color: #999;
                text-align: center;
                white-space: nowrap;
            }
            
            /* Tooltip */
            .alezux-bar-tooltip {
                position: absolute;
                bottom: 100%;
                left: 50%;
                transform: translateX(-50%) translateY(10px); /* Initially hidden lower */
                background: #fff;
                padding: 6px 10px;
                border-radius: 6px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.15);
                white-space: nowrap;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                pointer-events: none;
                z-index: 10;
                text-align: center;
            }
            .alezux-bar-item:hover .alezux-bar-tooltip {
                opacity: 1;
                visibility: visible;
                transform: translateX(-50%) translateY(-8px);
            }
            
            .tooltip-time {
                font-weight: 700;
                font-size: 13px;
                color: #000;
                line-height: 1.2;
            }
            .tooltip-meta {
                font-size: 10px;
                color: #888;
                margin-top: 2px;
                line-height: 1.2;
            }
            .alezux-bar-tooltip::after {
                content: '';
                position: absolute;
                top: 100%;
                left: 50%;
                margin-left: -5px;
                border-width: 5px;
                border-style: solid;
                border-color: #fff transparent transparent transparent;
            }
        </style>
		<?php
	}
}
