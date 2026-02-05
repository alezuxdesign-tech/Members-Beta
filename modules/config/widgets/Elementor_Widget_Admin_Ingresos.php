<?php
namespace Alezux_Members\Modules\Config\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Alezux_Members\Modules\Config\Includes\Admin_Dashboard_Stats;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Elementor_Widget_Admin_Ingresos extends Widget_Base {

	public function get_name() {
		return 'alezux_admin_revenue';
	}

	public function get_title() {
		return __( 'Admin: Ingresos Recientes', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-bar-chart';
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
			'simulate_data',
			[
				'label' => __( 'Simular datos (Demo)', 'alezux-members' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => 'Activa esto para ver el gráfico con datos falsos si aún no tienes ventas.',
				'default' => 'no',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Estilos', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'bar_color',
			[
				'label' => __( 'Color Barras', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-chart-bar-fill' => 'background: {{VALUE}};',
				],
                'default' => '#3F51B5',
			]
		);
        
        $this->add_control(
			'text_color',
			[
				'label' => __( 'Color Texto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#aaaaaa',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
        
        // Obtener datos
        $revenue_data = Admin_Dashboard_Stats::get_recent_revenue();
        $total_in_period = 0;
        
        // Simulación Demo
        if ( 'yes' === $settings['simulate_data'] || empty( $revenue_data ) ) {
             if ( 'yes' === $settings['simulate_data'] ) {
                $revenue_data = [];
                for ($i = 6; $i >= 0; $i--) {
                    $val = rand(100, 500);
                    $revenue_data[] = [ 'date' => date('M d', strtotime("-$i days")), 'total' => $val ];
                    $total_in_period += $val;
                }
             } else {
                 echo '<div style="padding: 20px; text-align: center; color: #888;">Sin datos de ventas recientes.</div>';
                 return;
             }
        }
        
        // Encontrar valor máximo para escala
        $max_val = 0;
        foreach ($revenue_data as $day) {
            if ($day['total'] > $max_val) $max_val = $day['total'];
            // Sumar si no es data simulada (ya sumada arriba)
             if ( 'yes' !== $settings['simulate_data'] ) {
                 $total_in_period += $day['total'];
             }
        }
        if ($max_val == 0) $max_val = 1; // Evitar division por cero

		?>
		<div class="alezux-revenue-widget">
            <div class="revenue-header">
                <h4 class="revenue-title">Ingresos (7 días)</h4>
                <div class="revenue-total">$<?php echo number_format($total_in_period, 2); ?></div>
            </div>
            
            <div class="alezux-chart-container">
                <?php foreach ($revenue_data as $item) : 
                    // Calcular altura porcentaje
                    $height_pct = ($item['total'] / $max_val) * 100;    
                    // Formatear fecha si viene de DB SQL Y-m-d
                    $label = ( strlen($item['date']) > 6 ) ? date('d/m', strtotime($item['date'])) : $item['date'];
                ?>
                <div class="chart-col">
                    <div class="bar-track">
                        <div class="alezux-chart-bar-fill" style="height: <?php echo $height_pct; ?>%;">
                            <span class="bar-tooltip">$<?php echo $item['total']; ?></span>
                        </div>
                    </div>
                    <span class="bar-label"><?php echo $label; ?></span>
                </div>
                <?php endforeach; ?>
            </div>
		</div>
        
        <style>
             .alezux-revenue-widget {
                 padding: 20px;
                 background: rgba(25, 25, 35, 0.4);
                 border-radius: 16px;
                 border: 1px solid rgba(255, 255, 255, 0.05);
             }
             .revenue-header {
                 display: flex;
                 justify-content: space-between;
                 align-items: center;
                 margin-bottom: 20px;
             }
             .revenue-title {
                 margin: 0;
                 font-size: 16px;
                 color: <?php echo $settings['text_color']; ?>;
             }
             .revenue-total {
                 font-size: 20px;
                 font-weight: 700;
                 color: #fff;
             }
             .alezux-chart-container {
                 display: flex;
                 justify-content: space-between;
                 align-items: flex-end;
                 height: 150px;
                 gap: 8px;
             }
             .chart-col {
                 flex: 1;
                 display: flex;
                 flex-direction: column;
                 align-items: center;
                 height: 100%;
             }
             .bar-track {
                 flex-grow: 1;
                 width: 100%;
                 display: flex;
                 align-items: flex-end;
                 justify-content: center;
                 position: relative;
             }
             .alezux-chart-bar-fill {
                 width: 80%;
                 max-width: 30px;
                 background: #3F51B5;
                 border-radius: 4px 4px 0 0;
                 transition: height 1s ease;
                 position: relative;
                 min-height: 2px;
             }
             .bar-label {
                 margin-top: 8px;
                 font-size: 11px;
                 color: <?php echo $settings['text_color']; ?>;
             }
             
             /* Tooltip Hover */
             .bar-tooltip {
                 position: absolute;
                 bottom: 100%;
                 left: 50%;
                 transform: translateX(-50%);
                 background: #000;
                 color: #fff;
                 padding: 4px 8px;
                 border-radius: 4px;
                 font-size: 10px;
                 opacity: 0;
                 visibility: hidden;
                 transition: opacity 0.2s;
                 white-space: nowrap;
                 pointer-events: none;
                 margin-bottom: 5px;
             }
             .alezux-chart-bar-fill:hover .bar-tooltip {
                 opacity: 1;
                 visibility: visible;
             }
        </style>
		<?php
	}
}
