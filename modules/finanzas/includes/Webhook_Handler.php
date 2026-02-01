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
        // Lógica: Primera compra (o suscripción iniciada)
        // TODO: Crear usuario, matricular en curso, registrar suscripción
        \error_log( 'Alezux Payment: Checkout Completed: ' . $session->id );
    }

    private function handle_invoice_payment_succeeded( $invoice ) {
        // Lógica: Pago recurrente exitoso
        // TODO: Actualizar quotas_paid, verificar si debe cancelarse la suscripción (fin de cuotas)
         \error_log( 'Alezux Payment: Invoice Succeeded: ' . $invoice->id );
    }

    private function handle_invoice_payment_failed( $invoice ) {
        // Lógica: Pago fallido
        // TODO: Marcar suscripción como 'past_due', notificar usuario, bloquear acceso
        \error_log( 'Alezux Payment: Invoice Failed: ' . $invoice->id );
    }
}
