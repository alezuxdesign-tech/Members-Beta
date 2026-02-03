<?php
namespace Alezux_Members\Modules\Marketing\Includes;

if ( ! \defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Queue_Runner
 * Maneja el envío físico de correos desde la tabla de cola.
 */
class Queue_Runner {

    /**
     * Procesa una porción de la cola.
     * Se puede llamar vía Cron o manualmente.
     */
    public static function process_queue( $limit = 10 ) {
        // 1. Procesar disparadores de tiempo (Inactividad / Vencimiento)
        self::process_time_triggers();

        global $wpdb;
        $table_queue = $wpdb->prefix . 'alezux_marketing_queue';

        // 2. Obtener correos pendientes que ya deberían haberse enviado
        $now = \current_time('mysql');
        $emails = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM $table_queue 
             WHERE status = 'pending' AND scheduled_at <= %s 
             ORDER BY scheduled_at ASC LIMIT %d",
            $now,
            $limit
        ) );

        if ( empty( $emails ) ) {
            return;
        }

        foreach ( $emails as $email ) {
            self::send_single_email( $email );
        }
    }

    /**
     * Escanea automatizaciones de inactividad y vencimiento.
     */
    private static function process_time_triggers() {
        global $wpdb;
        $table_automations = $wpdb->prefix . 'alezux_marketing_automations';

        // Evitar que esto corra demasiadas veces seguidas si el cron es muy frecuente
        // Podríamos usar un transiente, pero por ahora confiaremos en has_been_triggered_today.

        // 1. Inactividad
        $inactivity_automations = $wpdb->get_results( "SELECT * FROM $table_automations WHERE event_trigger = 'inactivity' AND status = 'active'" );
        foreach ( $inactivity_automations as $automation ) {
            self::run_inactivity_trigger( $automation );
        }

        // 2. Vencimiento
        $expiration_automations = $wpdb->get_results( "SELECT * FROM $table_automations WHERE event_trigger = 'expiration' AND status = 'active'" );
        foreach ( $expiration_automations as $automation ) {
            self::run_expiration_trigger( $automation );
        }
    }

    private static function run_inactivity_trigger( $automation ) {
        $blueprint = \json_decode( $automation->blueprint, true );
        $days = 0;
        if ( ! isset( $blueprint['nodes'] ) ) return;

        foreach ( $blueprint['nodes'] as $node ) {
            if ( $node['type'] === 'inactivity' ) {
                $days = \intval( $node['data']['days'] ?? 0 );
                break;
            }
        }

        if ( $days <= 0 ) return;

        $date_target = \date( 'Y-m-d', \strtotime( "-$days days" ) );

        $users = \get_users( [
            'meta_key'     => 'alezux_last_active',
            'meta_value'   => $date_target,
            'meta_compare' => 'LIKE',
            'fields'       => 'ID'
        ] );

        foreach ( $users as $user_id ) {
            if ( self::has_been_triggered_today( $user_id, $automation->id ) ) continue;

            $user = \get_userdata( $user_id );
            if ( ! $user ) continue;

            Automation_Engine::execute_automation( $automation, [ 'user' => $user ] );
            self::mark_as_triggered_today( $user_id, $automation->id );
        }
    }

    private static function run_expiration_trigger( $automation ) {
        $blueprint = \json_decode( $automation->blueprint, true );
        $days = 0;
        if ( ! isset( $blueprint['nodes'] ) ) return;

        foreach ( $blueprint['nodes'] as $node ) {
            if ( $node['type'] === 'expiration' ) {
                $days = \intval( $node['data']['days'] ?? 0 );
                break;
            }
        }

        if ( $days <= 0 ) return;

        global $wpdb;
        $date_target = \date( 'Y-m-d', \strtotime( "+$days days" ) );
        $table_subs  = $wpdb->prefix . 'alezux_finanzas_subscriptions';
        $table_plans = $wpdb->prefix . 'alezux_finanzas_plans';

        $near_expirations = $wpdb->get_results( $wpdb->prepare(
            "SELECT s.*, p.name as plan_name, p.token as plan_token, p.quota_amount 
             FROM $table_subs s
             JOIN $table_plans p ON s.plan_id = p.id
             WHERE DATE(s.next_payment_date) = %s AND s.status = 'active'",
            $date_target
        ) );

        foreach ( $near_expirations as $sub ) {
            if ( self::has_been_triggered_today( $sub->user_id, $automation->id ) ) continue;

            $user = \get_userdata( $sub->user_id );
            if ( ! $user ) continue;

            Automation_Engine::execute_automation( $automation, [
                'user'       => $user,
                'plan_name'  => $sub->plan_name,
                'plan_token' => $sub->plan_token,
                'amount'     => $sub->quota_amount
            ] );
            self::mark_as_triggered_today( $sub->user_id, $automation->id );
        }
    }

    private static function has_been_triggered_today( $user_id, $automation_id ) {
        $last_run = \get_user_meta( $user_id, "alezux_auto_run_{$automation_id}", true );
        return $last_run === \date( 'Y-m-d' );
    }

    private static function mark_as_triggered_today( $user_id, $automation_id ) {
        \update_user_meta( $user_id, "alezux_auto_run_{$automation_id}", \date( 'Y-m-d' ) );
    }


    /**
     * Envía un único email de la cola.
     */
    private static function send_single_email( $email ) {
        global $wpdb;
        $table_queue = $wpdb->prefix . 'alezux_marketing_queue';
        $table_stats = $wpdb->prefix . 'alezux_marketing_stats';

        // 1. Preparar Cabeceras (Branding Alezux)
        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $email->from_name . ' <' . $email->from_email . '>'
        ];

        // 2. Inyectar Píxel de Tracking
        $tracking_pixel = self::generate_tracking_pixel( $email->id );
        $final_message = $email->message . $tracking_pixel;

        // 3. Intentar Envío
        $sent = \wp_mail( $email->email_to, $email->subject, $final_message, $headers );

        if ( $sent ) {
            // Actualizar cola
            $wpdb->update( $table_queue, 
                [ 'status' => 'sent', 'sent_at' => \current_time('mysql') ], 
                [ 'id' => $email->id ] 
            );

            // Crear entrada en stats para el tracking futuro
            $wpdb->insert( $table_stats, [
                'queue_id' => $email->id,
                'opened'   => 0
            ] );
        } else {
            $wpdb->update( $table_queue, [ 'status' => 'failed' ], [ 'id' => $email->id ] );
        }
    }

    /**
     * Genera un <img> transparente para tracking de aperturas.
     */
    private static function generate_tracking_pixel( $queue_id ) {
        $pixel_url = \home_url( '/?alezux_action=track_email&qid=' . $queue_id );
        return '<img src="' . \esc_url( $pixel_url ) . '" width="1" height="1" style="display:none;" alt="" />';
    }
}
