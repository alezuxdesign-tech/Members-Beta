<?php
namespace Alezux_Members\Modules\Marketing\Includes;

if ( ! \defined( 'ABSPATH' ) ) {
	exit;
}

class Event_Listeners {

    public static function init() {
        // Escuchar eventos de Finanzas
        \add_action( 'alezux_finance_payment_received', [ __CLASS__, 'on_payment_received' ], 10, 3 );
        \add_action( 'alezux_finance_payment_failed', [ __CLASS__, 'on_payment_failed' ], 10, 2 );
        
        // Registro de usuario
        \add_action( 'user_register', [ __CLASS__, 'on_user_register' ] );

        // Tracking de actividad para Inactividad
        \add_action( 'init', [ __CLASS__, 'track_user_activity' ] );
    }

    /**
     * Registra la última actividad del usuario para detectar inactividad.
     */
    public static function track_user_activity() {
        if ( \is_user_logged_in() ) {
            $user_id = \get_current_user_id();
            $last_active = \get_user_meta( $user_id, 'alezux_last_active', true );
            $now_ts = \time();
            $last_active_ts = $last_active ? \strtotime( $last_active ) : 0;

            if ( $now_ts - $last_active_ts > 3600 ) {
                \update_user_meta( $user_id, 'alezux_last_active', \current_time('mysql') );
            }
        }
    }

    /**
     * Se dispara cuando se confirma un pago.
     */
    public static function on_payment_received( $user_id, $plan_id, $quota_number ) {
        $user = \get_userdata( $user_id );
        if ( ! $user ) return;

        $event_key = ( $quota_number === 1 ) ? 'primer_pago' : 'pago_exitoso';
        
        global $wpdb;
        $plans_table = $wpdb->prefix . 'alezux_finanzas_plans';
        $plan_name = $wpdb->get_var( $wpdb->prepare( "SELECT name FROM $plans_table WHERE id = %d", $plan_id ) );

        Automation_Engine::process_event( $event_key, [
            'user'      => $user,
            'plan_name' => $plan_name ?: 'Plan de Estudios'
        ] );
    }

    /**
     * Se dispara cuando un pago falla.
     */
    public static function on_payment_failed( $user_id, $plan_id ) {
        $user = \get_userdata( $user_id );
        if ( ! $user ) return;

        Automation_Engine::process_event( 'pago_fallido', [
            'user' => $user
        ] );
    }

    public static function on_user_register( $user_id ) {
        $user = \get_userdata( $user_id );
        if ( ! $user ) return;

        Automation_Engine::process_event( 'user_registered', [
            'user' => $user
        ] );
    }
}
