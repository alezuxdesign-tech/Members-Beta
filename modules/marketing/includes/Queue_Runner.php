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
        global $wpdb;
        $table_queue = $wpdb->prefix . 'alezux_marketing_queue';

        // 1. Obtener correos pendientes que ya deberían haberse enviado
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
