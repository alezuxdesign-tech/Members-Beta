<?php
namespace Alezux_Members\Modules\Finanzas\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Shortcodes {

	public static function init() {
		// 1. Ingresos del Periodo (Dinámico)
		add_shortcode( 'ax_fin_revenue_period', [ __CLASS__, 'render_revenue_period' ] );
		
		// 2. Deuda Total por Recoger (Global - Estático)
		add_shortcode( 'ax_fin_pending_total', [ __CLASS__, 'render_pending_total' ] );
		
		// 3. Proyección de Cobro Periodo (Dinámico)
		add_shortcode( 'ax_fin_projected_period', [ __CLASS__, 'render_projected_period' ] );
		
		// 4. Ingresos de Hoy (Ticker - Estático/Diario)
		add_shortcode( 'ax_fin_revenue_today', [ __CLASS__, 'render_revenue_today' ] );

        // --- Nuevos Shortcodes Dashboard (AJAX Powered) ---
        add_shortcode( 'alezux_sales_stats', [ __CLASS__, 'render_sales_stats' ] );
        add_shortcode( 'alezux_sales_chart', [ __CLASS__, 'render_sales_chart' ] );
        add_shortcode( 'alezux_date_range_filter', [ __CLASS__, 'render_date_filter' ] );
	}

    /**
     * Helpers de Cálculo Interno
     */
    public static function calculate_revenue( $start_date, $end_date ) {
        global $wpdb;
        $table = $wpdb->prefix . 'alezux_finanzas_transactions';
        
        $sql = "SELECT SUM(amount) FROM $table WHERE status = 'succeeded'";
        
        if ( $start_date && $end_date ) {
             $sql .= $wpdb->prepare( " AND created_at BETWEEN %s AND %s", $start_date . ' 00:00:00', $end_date . ' 23:59:59' );
        } elseif ( $start_date ) {
            // Solo start date (hoy o dia X)
             $sql .= $wpdb->prepare( " AND created_at >= %s", $start_date . ' 00:00:00' );
        }

        $amount = $wpdb->get_var( $sql );
        return $amount ? (float) $amount : 0.00;
    }

    public static function calculate_pending_total() {
        global $wpdb;
        $t_subs = $wpdb->prefix . 'alezux_finanzas_subscriptions';
        $t_plans = $wpdb->prefix . 'alezux_finanzas_plans';
        
        // Sumar (total_quotas - quotas_paid) * quota_amount de suscripciones ACTIVAS
        // Solo para planes que no sean de contado (total_quotas > 1) y que no hayan terminado
        $sql = "SELECT SUM( (p.total_quotas - s.quotas_paid) * p.quota_amount ) 
                FROM $t_subs s
                JOIN $t_plans p ON s.plan_id = p.id
                WHERE s.status = 'active'
                AND p.total_quotas > 1
                AND s.quotas_paid < p.total_quotas";

        $amount = $wpdb->get_var( $sql );
        return $amount ? (float) $amount : 0.00;
    }

    public static function calculate_projected( $start_date, $end_date ) {
        global $wpdb;
        $t_subs = $wpdb->prefix . 'alezux_finanzas_subscriptions';
        $t_plans = $wpdb->prefix . 'alezux_finanzas_plans';
        
        // Obtener suscripciones activas (o atrasadas, pues se espera cobrar) que tienen próxima fecha definida
        $sql = "SELECT s.id, s.next_payment_date, s.quotas_paid, p.quota_amount, p.total_quotas, p.frequency 
                FROM $t_subs s
                JOIN $t_plans p ON s.plan_id = p.id
                WHERE s.status IN ('active', 'past_due') 
                AND s.next_payment_date IS NOT NULL";
        
        $subs = $wpdb->get_results( $sql );
        $total = 0.0;
        
        // Convertir strings a timestamp para comparación
        $period_start = strtotime( $start_date . ' 00:00:00' );
        $period_end = strtotime( $end_date . ' 23:59:59' );
        
        foreach ( $subs as $sub ) {
            // Fecha del próximo pago real registrado en DB
            $payment_date = strtotime( $sub->next_payment_date );
            $quotas_paid = (int) $sub->quotas_paid;
            $max_quotas = (int) $sub->total_quotas; 
            
            // Si es 0, asumimos indefinido (o lógica de negocio específica). 
            // Si es 1, es pago único, ya debería estar pagado si está activo, pero verificamos.
            if ( $max_quotas <= 0 ) $max_quotas = 9999; 

            // Si el próximo pago ya está fuera del rango futuro, ignorar esta subs
            if ( $payment_date > $period_end ) continue;
            
            // Proyección Iterativa: Simular ocurrencias dentro del rango
            // Safety break: max 50 iteraciones para evitar bucles infinitos (ej. 1 año de pagos diarios)
            $iterations = 0;
            
            while ( $payment_date <= $period_end && $quotas_paid < $max_quotas && $iterations < 50 ) {
                
                // Si la fecha cae dentro del rango seleccionado (y es >= inicio), sumar
                if ( $payment_date >= $period_start ) {
                    $total += (float) $sub->quota_amount;
                }
                
                // Avanzar fecha según frecuencia
                $freq = $sub->frequency ?: 'month';
                $freq_str = '+1 month';
                if ( $freq === 'week' ) $freq_str = '+1 week';
                if ( $freq === 'year' ) $freq_str = '+1 year';
                if ( $freq === 'day' ) $freq_str = '+1 day';
                if ( $freq === 'contado' ) break; // No recurre
                
                $payment_date = strtotime( $freq_str, $payment_date );
                $quotas_paid++;
                $iterations++;
            }
        }
        
        return $total;
    }

    /**
     * Renders
     */
    public static function render_revenue_period( $atts ) {
        // Por defecto: Mes Actual
        $start = date( 'Y-m-01' );
        $end = date( 'Y-m-t' );
        $val = self::calculate_revenue( $start, $end );
        
        return sprintf( 
            '<span id="kpi-revenue-period" class="ax-fin-kpi" data-kpi="revenue_period" data-default="%s">%s</span>',
            esc_attr( number_format( $val, 2 ) ),
            '$' . number_format( $val, 2 )
        );
    }

    public static function render_pending_total( $atts ) {
        // Global
        $val = self::calculate_pending_total();
        
        return sprintf( 
            '<span id="kpi-pending-total" class="ax-fin-kpi" data-kpi="pending_total">%s</span>',
            '$' . number_format( $val, 2 )
        );
    }

    public static function render_projected_period( $atts ) {
        // Por defecto: Mes Actual
        $start = date( 'Y-m-01' );
        $end = date( 'Y-m-t' );
         $val = self::calculate_projected( $start, $end );
        
        return sprintf( 
            '<span id="kpi-projected-period" class="ax-fin-kpi" data-kpi="projected_period" data-default="%s">%s</span>',
             esc_attr( number_format( $val, 2 ) ),
            '$' . number_format( $val, 2 )
        );
    }

    public static function render_revenue_today( $atts ) {
        // Hoy
        $today = date( 'Y-m-d' );
        $val = self::calculate_revenue( $today, $today ); // Start=End
        
        return sprintf( 
            '<span id="kpi-revenue-today" class="ax-fin-kpi-static" data-kpi="revenue_today">%s</span>',
            '$' . number_format( $val, 2 )
        );
    }

    /**
     * Nuevos Renders para Dashboard (AJAX)
     */
    public static function render_sales_stats( $atts ) {
        $a = shortcode_atts( [
            'type' => 'month_revenue', // month_revenue, today_revenue
            'label' => ''
        ], $atts );

        $label = $a['label'];
        if(!$label) {
            $label = ($a['type'] === 'today_revenue') ? 'Ingresos Hoy' : 'Ingresos Mes';
        }

        ob_start();
        ?>
        <div class="alezux-kpi-card" data-stat-type="<?php echo esc_attr($a['type']); ?>" style="padding: 20px; background: #1a1a1a; border: 1px solid #333; border-radius: 12px; position: relative; overflow: hidden; min-width: 200px; display: inline-block; margin: 10px;">
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
