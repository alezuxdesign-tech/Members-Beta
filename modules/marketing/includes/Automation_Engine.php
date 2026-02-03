<?php
namespace Alezux_Members\Modules\Marketing\Includes;

if ( ! \defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Automation_Engine
 * Maneja la lógica de ejecución de automatizaciones basadas en eventos.
 */
class Automation_Engine {

    /**
     * Procesa un evento disparado por el sistema.
     * 
     * @param string $event_key El slug del evento (ej: 'pago_exitoso').
     * @param array $data Datos asociados al evento (usuario, plan, etc).
     */
    public static function process_event( $event_key, $data = [] ) {
        global $wpdb;
        $table_automations = $wpdb->prefix . 'alezux_marketing_automations';

        // 1. Buscar automatizaciones activas para este trigger
        $automations = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM $table_automations WHERE event_trigger = %s AND status = 'active'",
            $event_key
        ) );

        if ( empty( $automations ) ) {
            return;
        }

        foreach ( $automations as $automation ) {
            self::execute_automation( $automation, $data );
        }
    }

    /**
     * Ejecuta una automatización específica (Blueprint).
     */
    public static function execute_automation( $automation, $data ) {
        $blueprint = \json_decode( $automation->blueprint, true );
        
        if ( ! $blueprint || ! isset( $blueprint['nodes'] ) ) {
            return;
        }

        foreach ( $blueprint['nodes'] as $node ) {
            if ( $node['type'] === 'email' ) {
                self::queue_email( $node['data'], $data, $automation->id );
            }
        }
    }

    /**
     * Añade un email a la cola de envío.
     */
    private static function queue_email( $node_data, $trigger_data, $automation_id ) {
        global $wpdb;
        $table_queue = $wpdb->prefix . 'alezux_marketing_queue';

        $user = $trigger_data['user'] ?? null;
        if ( ! $user ) return;

        // Procesar variables en el mensaje y asunto
        $subject = self::replace_variables( $node_data['subject'] ?? '', $trigger_data );
        $message = self::replace_variables( $node_data['message'] ?? '', $trigger_data );

        $wpdb->insert( $table_queue, [
            'automation_id' => $automation_id,
            'user_id'       => $user->ID,
            'email_to'      => $user->user_email,
            'subject'       => $subject,
            'message'       => $message,
            'from_name'     => $node_data['from_name'] ?? \get_bloginfo('name'),
            'from_email'    => $node_data['from_email'] ?? \get_bloginfo('admin_email'),
            'status'        => 'pending',
            'scheduled_at'  => \current_time('mysql')
        ] );
    }

    /**
     * Reemplaza tags dinámicos {{variable}} por datos reales.
     */
    private static function replace_variables( $content, $data ) {
        $user      = $data['user'] ?? null;
        $plan_name = $data['plan_name'] ?? '';
        $plan_token = $data['plan_token'] ?? '';
        $amount    = $data['amount'] ?? '';

        $payment_url = '';
        if ( ! empty( $plan_token ) ) {
            $payment_url = \home_url( '/?alezux_buy_token=' . $plan_token );
        }

        $replacements = [
            '{{student_name}}'      => $user ? $user->display_name : '',
            '{{student_email}}'     => $user ? $user->user_email : '',
            '{{plan_name}}'         => $plan_name,
            '{{payment_url}}'       => $payment_url,
            '{{subscription_amount}}' => $amount,
            '{{amount}}'            => $amount,
            '{{site_name}}'         => \get_bloginfo('name'),
        ];

        return \str_replace( \array_keys($replacements), \array_values($replacements), $content );
    }
}
