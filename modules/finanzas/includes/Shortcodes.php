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
        
        // Sumar quota_amount de suscripciones ACTIVAS cuyo next_payment_date cae en el rango
        $sql = "SELECT SUM( p.quota_amount ) 
                FROM $t_subs s
                JOIN $t_plans p ON s.plan_id = p.id
                WHERE s.status = 'active'
                AND s.next_payment_date BETWEEN %s AND %s";

        $amount = $wpdb->get_var( $wpdb->prepare( $sql, $start_date . ' 00:00:00', $end_date . ' 23:59:59' ) );
        return $amount ? (float) $amount : 0.00;
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
}
