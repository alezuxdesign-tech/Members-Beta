<?php
namespace Alezux_Members\Modules\Finanzas\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Enrollment_Manager {

    /**
     * Procesa la inscripción de un usuario tras un pago exitoso.
     *
     * @param string $email Email del usuario (Stripe Customer Email)
     * @param int $plan_id ID del Plan interno
     * @param string $stripe_sub_id ID de suscripción de Stripe (opcional)
     * @param float $amount Monto pagado
     * @param string $transaction_ref ID de referencia (PaymentIntent o Session ID)
     * @return int|false ID del usuario o false si falla
     */
    public static function enroll_user( $email, $plan_id, $stripe_sub_id = null, $amount = 0.0, $transaction_ref = '', $currency = 'USD' ) {
        global $wpdb;
        
        error_log( "Alezux Enrollment: Iniciando para $email - Plan $plan_id - Ref $transaction_ref" );

        // 1. Gestionar Usuario (Buscar o Crear)
        $user = get_user_by( 'email', $email );
        $is_new_user = false;

        if ( ! $user ) {
            $username = sanitize_user( current( explode( '@', $email ) ) );
            // Asegurar username único
            if ( username_exists( $username ) ) {
                $username .= '_' . rand( 100, 999 );
            }
            $password = wp_generate_password();
            $user_id = wp_create_user( $username, $password, $email );
            
            if ( is_wp_error( $user_id ) ) {
                error_log( 'Alezux Error: No se pudo crear usuario para ' . $email . ' - ' . $user_id->get_error_message() );
                return false;
            }

            $user = get_user_by( 'id', $user_id );
            
            // Asignar rol por defecto
            $user->set_role( 'subscriber' ); 
            
            // Notificar usuario nuevo (Opcional, se puede mejorar con módulo Marketing)
            wp_new_user_notification( $user_id, null, 'user' ); 
            
            $is_new_user = true;
        }

        $user_id = $user->ID;

        // 2. Obtener Info del Plan Local
        $plan_table = $wpdb->prefix . 'alezux_finanzas_plans';
        $plan = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $plan_table WHERE id = %d", $plan_id ) );

        if ( ! $plan ) {
            error_log( 'Alezux Error: Plan no encontrado ID ' . $plan_id );
            return false;
        }

        // 3. Matricular en LearnDash
        if ( function_exists( 'ld_update_course_access' ) ) {
            ld_update_course_access( $user_id, $plan->course_id );
            error_log( "Alezux: Usuario $user_id matriculado en curso {$plan->course_id} via LD." );
        }

        // 4. Registrar Suscripción/Compra
        $subs_table = $wpdb->prefix . 'alezux_finanzas_subscriptions';
        
        // Verificar duplicados activos
        $existing_sub = $wpdb->get_var( $wpdb->prepare( 
            "SELECT id FROM $subs_table WHERE user_id = %d AND plan_id = %d AND status = 'active'", 
            $user_id, $plan_id 
        ) );

        $subscription_id = 0;

        if ( ! $existing_sub ) {
            // Si es pago único (no hay stripe_sub_id o plan dice contado), marcamos como completed o active
            $wpdb->insert( $subs_table, [
                'user_id' => $user_id,
                'plan_id' => $plan_id,
                'stripe_subscription_id' => $stripe_sub_id, // Puede ser null si es pago único
                'status' => 'active',
                'quotas_paid' => 1,
                'last_payment_date' => current_time( 'mysql' ),
                'next_payment_date' => ( $stripe_sub_id ) ? date( 'Y-m-d H:i:s', strtotime( '+1 month' ) ) : null
            ] );
            $subscription_id = $wpdb->insert_id;
        } else {
            $subscription_id = $existing_sub;
            // Podríamos actualizar last_payment_date aquí si es recompra del mismo plan activo (raro)
        }

        // 5. Registrar Transacción
        $trans_table = $wpdb->prefix . 'alezux_finanzas_transactions';
        
        // Evitar duplicar transacción si ya existe con esa referencia
        $existing_trans = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $trans_table WHERE transaction_ref = %s", $transaction_ref ) );
        
        if ( ! $existing_trans ) {
            $inserted = $wpdb->insert( $trans_table, [
                'subscription_id' => $subscription_id,
                'plan_id' => $plan_id, // Redundante pero útil
                'user_id' => $user_id,
                'amount' => $amount,
                'currency' => $currency,
                'method' => 'stripe',
                'transaction_ref' => $transaction_ref,
                'status' => 'succeeded',
                'created_at' => current_time( 'mysql' )
            ] );
            
            if ( $inserted ) {
                error_log( "Alezux Transaction: Insertada transacción $transaction_ref para usuario $user_id" );
                // IMPORTANTE: Disparar evento para Marketing solo si es nueva transacción
                do_action( 'alezux_finance_payment_received', $user_id, $plan_id, 1 );
            } else {
                error_log( "Alezux Error: Fallo al insertar transacción. DB Error: " . $wpdb->last_error );
            }
        } else {
            error_log( "Alezux Info: Transacción $transaction_ref ya existía. Omitiendo insert." );
        }

        return $user_id;
    }
}
