<?php
namespace Alezux_Members\Modules\Marketing\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Event_Listeners {

    public static function init() {
        // Escuchar eventos de Finanzas
        add_action( 'alezux_finance_payment_received', [ __CLASS__, 'on_payment_received' ], 10, 3 );
        add_action( 'alezux_finance_payment_failed', [ __CLASS__, 'on_payment_failed' ], 10, 2 );
        
        // Escuchar otros eventos si es necesario (ej: registro nuevo usuario)
        add_action( 'user_register', [ __CLASS__, 'on_user_register' ] );
    }

    /**
     * Se dispara cuando se confirma un pago (Stripe o Manual)
     * @param int $user_id
     * @param int $plan_id
     * @param int $quota_number Número de cuota que se acabó de pagar
     */
    public static function on_payment_received( $user_id, $plan_id, $quota_number ) {
        $user = get_userdata( $user_id );
        if ( ! $user ) return;

        // Lógica de Automatización
        // 1. Si es la primera cuota -> Email de Bienvenida / Acceso Inicial
        if ( $quota_number === 1 ) {
            Email_Sender::send_payment_success_email( $user, $plan_id, true );
        } else {
            // 2. Si es una cuota recurrente -> Email de "Pago Recibido / Nuevo Módulo Desbloqueado"
            Email_Sender::send_payment_success_email( $user, $plan_id, false );
        }
    }

    /**
     * Se dispara cuando un pago falla
     */
    public static function on_payment_failed( $user_id, $plan_id ) {
        $user = get_userdata( $user_id );
        if ( ! $user ) return;

        Email_Sender::send_payment_failed_email( $user, $plan_id );
    }

    public static function on_user_register( $user_id ) {
        // Podríamos enviar un email de "Cuenta Creada" genérico aquí si no depende del plan
    }
}
