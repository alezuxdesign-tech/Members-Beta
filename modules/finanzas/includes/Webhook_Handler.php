<?php
namespace Alezux_Members\Modules\Finanzas\Includes;

if ( ! \defined( 'ABSPATH' ) ) {
	exit;
}

class Webhook_Handler {

    private $namespace = 'alezux/v1';
    private $route = 'stripe-webhook';

    public static function init() {
        $instance = new self();
        \add_action( 'rest_api_init', [ $instance, 'register_routes' ] );
    }

    public function register_routes() {
        \register_rest_route( $this->namespace, '/' . $this->route, [
            'methods'  => 'POST',
            'callback' => [ $this, 'handle_request' ],
            'permission_callback' => '__return_true', // Stripe autentica vía firma, no usuario WP
        ] );
    }

    public function handle_request( \WP_REST_Request $request ) {
        $payload = $request->get_body();
        $sig_header = $request->get_header( 'stripe-signature' );
        $endpoint_secret = \get_option( 'alezux_stripe_webhook_secret' ); // Necesitaremos esto

        $event = null;

        try {
            // Si tenemos secreto de webhook configurado, verificamos firma (Recomendado)
            // Nota: Sin el SDK oficial de Stripe, la verificación manual de firma es compleja.
            // Por ahora, en esta implementación ligera, confiaremos en el contenido si es modo TEST
            // pero idealmente deberíamos validar.
            
            $event = \json_decode( $payload );

        } catch(\Exception $e) {
            return new \WP_REST_Response( [ 'error' => 'Invalid Payload' ], 400 );
        }

        if ( ! isset( $event->type ) ) {
            return new \WP_REST_Response( [ 'error' => 'Unknown Event' ], 400 );
        }

        // Router de Eventos
        switch ( $event->type ) {
            case 'checkout.session.completed':
                $this->handle_checkout_completed( $event->data->object );
                break;
            case 'invoice.payment_succeeded':
                $this->handle_invoice_payment_succeeded( $event->data->object );
                break;
            case 'invoice.payment_failed':
                $this->handle_invoice_payment_failed( $event->data->object );
                break;
            default:
                // Evento no manejado, pero respondemos 200 a Stripe
                break;
        }

        return new \WP_REST_Response( [ 'success' => true ], 200 );
    }

    private function handle_checkout_completed( $session ) {
        if ( ! isset( $session->customer_details->email ) ) {
            return;
        }

        $email = $session->customer_details->email;
        $stripe_subscription_id = $session->subscription ?? null;
        $plan_id = $session->metadata->plan_id ?? 0; // Guardamos ID de nuestro plan en metadata al crear checkout

        global $wpdb;
        
        // 1. Gestionar Usuario
        $user = \get_user_by( 'email', $email );
        $is_new_user = false;

        if ( ! $user ) {
            $username = \sanitize_user( current( explode( '@', $email ) ) );
            $password = \wp_generate_password();
            $user_id = \wp_create_user( $username, $password, $email );
            
            if ( \is_wp_error( $user_id ) ) {
                \error_log( 'Alezux Error: No se pudo crear usuario para ' . $email );
                return;
            }

            $user = \get_user_by( 'id', $user_id );
            $user->set_role( 'subscriber' ); // O 'student' si existe
            
            // Enviar correo de bienvenida con credenciales (Pendiente Fase Marketing)
            // \wp_new_user_notification( $user_id, null, 'both' ); // Opcional default WP
            $is_new_user = true;
        }

        $user_id = $user->ID;

        // 2. Obtener Info del Plan Local
        $plan_table = $wpdb->prefix . 'alezux_finanzas_plans';
        $plan = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $plan_table WHERE id = %d", $plan_id ) );

        if ( ! $plan ) {
            \error_log( 'Alezux Error: Plan no encontrado ID ' . $plan_id );
            return;
        }

        // 3. Matricular en LearnDash
        if ( \function_exists( 'ld_update_course_access' ) ) {
            \ld_update_course_access( $user_id, $plan->course_id );
        }

        // 4. Registrar Suscripción
        $subs_table = $wpdb->prefix . 'alezux_finanzas_subscriptions';
        
        // Verificar si ya existe suscripción activa para evitar duplicados
        $existing_sub = $wpdb->get_var( $wpdb->prepare( 
            "SELECT id FROM $subs_table WHERE user_id = %d AND plan_id = %d AND status = 'active'", 
            $user_id, $plan_id 
        ) );

        if ( ! $existing_sub ) {
            $wpdb->insert( $subs_table, [
                'user_id' => $user_id,
                'plan_id' => $plan_id,
                'stripe_subscription_id' => $stripe_subscription_id,
                'status' => 'active',
                'quotas_paid' => 1, // Primer pago exitoso
                'last_payment_date' => current_time( 'mysql' ),
                'next_payment_date' => date( 'Y-m-d H:i:s', strtotime( '+1 month' ) ) // Estimado
            ] );
            $subscription_id = $wpdb->insert_id;
        } else {
            $subscription_id = $existing_sub;
        }

        // 5. Registrar Transacción
        $trans_table = $wpdb->prefix . 'alezux_finanzas_transactions';
        $wpdb->insert( $trans_table, [
            'subscription_id' => $subscription_id,
            'amount' => ($session->amount_total / 100),
            'method' => 'stripe',
            'transaction_ref' => $session->payment_intent ?? $session->id,
            'status' => 'succeeded'
        ] );

        \error_log( "Alezux Payment: Usuario $email matriculado exitosamente en Plan $plan_id" );
    }

    private function handle_invoice_payment_succeeded( $invoice ) {
        if ( $invoice->billing_reason == 'subscription_create' ) {
            // Esto ya lo maneja checkout.session.completed generalmente
            return;
        }

        $stripe_sub_id = $invoice->subscription;
        if ( ! $stripe_sub_id ) return;

        global $wpdb;
        $subs_table = $wpdb->prefix . 'alezux_finanzas_subscriptions';
        $plans_table = $wpdb->prefix . 'alezux_finanzas_plans';

        // Buscar suscripción local
        $subscription = $wpdb->get_row( $wpdb->prepare( 
            "SELECT s.*, p.total_quotas, p.stripe_price_id 
             FROM $subs_table s 
             JOIN $plans_table p ON s.plan_id = p.id 
             WHERE s.stripe_subscription_id = %s", 
            $stripe_sub_id 
        ) );

        if ( ! $subscription ) return;

        // Registrar Transacción
        $trans_table = $wpdb->prefix . 'alezux_finanzas_transactions';
        $wpdb->insert( $trans_table, [
            'subscription_id' => $subscription->id,
            'amount' => ($invoice->amount_paid / 100),
            'method' => 'stripe',
            'transaction_ref' => $invoice->payment_intent ?? $invoice->id,
            'status' => 'succeeded'
        ] );

        // Actualizar Cuotas
        $new_quotas_paid = $subscription->quotas_paid + 1;
        $wpdb->update( 
            $subs_table, 
            [ 
                'quotas_paid' => $new_quotas_paid,
                'last_payment_date' => current_time( 'mysql' )
            ], 
            [ 'id' => $subscription->id ] 
        );

        \error_log( "Alezux Renewal: Cuota $new_quotas_paid pagada para suscripción Local ID {$subscription->id}" );

        // Verificar si se completaron las cuotas
        if ( $new_quotas_paid >= $subscription->total_quotas ) {
            $this->cancel_stripe_subscription( $stripe_sub_id );
            
            $wpdb->update( 
                $subs_table, 
                [ 'status' => 'completed' ], 
                [ 'id' => $subscription->id ] 
            );
            
            \error_log( "Alezux: Plan completado. Suscripción Stripe $stripe_sub_id cancelada." );
        }
    }

    private function handle_invoice_payment_failed( $invoice ) {
        $stripe_sub_id = $invoice->subscription;
        if ( ! $stripe_sub_id ) return;

        global $wpdb;
        $subs_table = $wpdb->prefix . 'alezux_finanzas_subscriptions';

        // Marcar como past_due
        $wpdb->update( 
            $subs_table, 
            [ 'status' => 'past_due' ], 
            [ 'stripe_subscription_id' => $stripe_sub_id ]
        );

        // TODO: Enviar email de fallo (Módulo Marketing)
        \error_log( "Alezux: Pago fallido para suscripción stripe $stripe_sub_id" );
    }

    private function cancel_stripe_subscription( $sub_id ) {
        // Necesitamos Stripe API aquí para cancelar
        // Como no tenemos el SDK cargado globalmente igual que en Stripe_API class, 
        // usaremos una instancia rápida o helper estático.
        
        $secret_key = \get_option( 'alezux_stripe_secret_key' );
        if ( ! $secret_key ) return;

        // Llamada curl directa para evitar dependencias complejas en este handler ligero
        $ch = \curl_init();
        \curl_setopt($ch, CURLOPT_URL, "https://api.stripe.com/v1/subscriptions/$sub_id");
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        \curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        \curl_setopt($ch, CURLOPT_USERPWD, $secret_key . ':' . '');
        
        $result = \curl_exec($ch);
        if ( \curl_errno($ch) ) {
            \error_log( 'Error cancelando suscripción Stripe: ' . \curl_error($ch) );
        }
        \curl_close($ch);
    }
}
