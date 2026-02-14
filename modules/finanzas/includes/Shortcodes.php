<?php
namespace Alezux_Members\Modules\Finanzas\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Shortcodes {

	public static function init() {
        // --- Nuevos Shortcodes Dashboard (AJAX Powered) ---
        add_shortcode( 'alezux_sales_stats', [ __CLASS__, 'render_sales_stats' ] );
        add_shortcode( 'alezux_sales_chart', [ __CLASS__, 'render_sales_chart' ] );
        add_shortcode( 'alezux_date_range_filter', [ __CLASS__, 'render_date_filter' ] );
	}



    /**
     * Nuevos Renders para Dashboard (AJAX)
     */
    public static function render_sales_stats( $atts ) {
        $a = shortcode_atts( [
            'type' => 'month_revenue', // month_revenue, today_revenue
            'format' => 'raw', // raw (number only) or card (full widget)
            'label' => ''
        ], $atts );

        $stat_type = esc_attr($a['type']);

        // RAW OUTPUT (For custom designs)
        if ( $a['format'] === 'raw' ) {
            // Output a span that JS will target to update
            return sprintf(
                '<span class="alezux-dynamic-stat-raw" data-stat-type="%s">--</span>',
                $stat_type
            );
        }

        // ... (Legacy Card Output if needed, but user asked for raw)
        $label = $a['label'];
        if(!$label) {
            $label = ($a['type'] === 'today_revenue') ? 'Ingresos Hoy' : 'Ingresos Mes';
        }

        ob_start();
        ?>
        <div class="alezux-kpi-card" data-stat-type="<?php echo $stat_type; ?>" style="padding: 20px; background: #1a1a1a; border: 1px solid #333; border-radius: 12px; position: relative; overflow: hidden; min-width: 200px; display: inline-block; margin: 10px;">
            <div class="alezux-kpi-content" style="display: flex; align-items: center; justify-content: space-between; gap: 15px;">
                <div class="alezux-kpi-info" style="display: flex; flex-direction: column; gap: 5px;">
                    <span class="alezux-kpi-label" style="font-size: 14px; color: #888; font-weight: 500;"><?php echo esc_html($label); ?></span>
                    <h3 class="alezux-kpi-value" style="font-size: 28px; color: #fff; margin: 0; font-weight: 700;">
                        <span class="alezux-kpi-number">--</span>
                    </h3>
                </div>
                <div class="alezux-kpi-icon" style="width: 50px; height: 50px; background: rgba(108, 92, 231, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: #6c5ce7;">
                    <span class="dashicons dashicons-money-alt" style="font-size:24px; height:24px; width:24px; display:flex; align-items:center; justify-content:center;"></span>
                </div>
            </div>
             <div class="alezux-kpi-loading" style="display:none; position: absolute; top:0; left:0; right:0; bottom:0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center;">...</div>
        </div>
        <?php
        return ob_get_clean();
    }

    public static function render_sales_chart( $atts ) {
        $a = shortcode_atts( [
            'type' => 'doughnut',
            'title' => 'Ingresos por Método'
        ], $atts );
        
        $chart_id = 'chart-' . uniqid();

        ob_start();
        ?>
        <div class="alezux-chart-container" style="padding: 20px; background: #1a1a1a; border: 1px solid #333; border-radius: 12px; margin: 10px;">
            <?php if($a['title']): ?>
                <h3 class="alezux-chart-title" style="margin: 0 0 20px 0; font-size: 18px; color: #fff; font-weight: 600;"><?php echo esc_html($a['title']); ?></h3>
            <?php endif; ?>
            
            <div class="alezux-chart-wrapper" style="position: relative; height:300px; width:100%;">
                <!-- Data attributes for colors (default) -->
                <canvas id="<?php echo esc_attr($chart_id); ?>" class="alezux-chart-card" data-chart-type="<?php echo esc_attr($a['type']); ?>" data-color-stripe="#6772e5" data-color-manual="#2ecc71" data-color-paypal="#003087"></canvas>
                 <div class="alezux-chart-loading" style="display:none; position:absolute; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); color:white; justify-content:center; align-items:center;">Cargando...</div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public static function render_date_filter( $atts ) {
        ob_start();
        ?>
        <div class="alezux-date-filter-wrapper" style="display: inline-block; width: 100%; max-width: 300px; margin: 10px;">
             <div class="alezux-input-icon-wrapper" style="position: relative; display: flex; align-items: center;">
                <span class="dashicons dashicons-calendar-alt" style="position: absolute; left: 10px; color: #888; pointer-events: none;"></span>
                <input type="text" class="alezux-date-global-input" placeholder="Filtrar por Fecha..." style="width: 100%; padding: 10px 10px 10px 35px; border: 1px solid #444; border-radius: 6px; background: #1a1a1a; color: #fff; cursor: pointer;" readonly>
                <span class="dashicons dashicons-dismiss alezux-clear-global-date" title="Limpiar" style="display:none; cursor:pointer; position: absolute; right: 10px; color: #888;"></span>
             </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
