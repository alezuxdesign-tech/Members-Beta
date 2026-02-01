<?php
namespace Alezux_Members\Modules\Marketing\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Email_Sender {

    public static function send_payment_success_email( $user, $plan_id, $is_first_payment ) {
        $to = $user->user_email;
        $subject = '';
        $message = '';
        
        // Obtener nombre del plan
        global $wpdb;
        $plans_table = $wpdb->prefix . 'alezux_finanzas_plans';
        $plan_name = $wpdb->get_var( $wpdb->prepare( "SELECT name FROM $plans_table WHERE id = %d", $plan_id ) );
        $plan_name = $plan_name ? $plan_name : 'Tu Plan';

        if ( $is_first_payment ) {
            $subject = '¡Bienvenido a ' . $plan_name . '! Acceso Confirmado 🚀';
            $message = self::get_welcome_template( $user, $plan_name );
        } else {
            $subject = 'Pago Recibido: ' . $plan_name . ' - Acceso Renovado ✅';
            $message = self::get_recurring_payment_template( $user, $plan_name );
        }

        self::send( $to, $subject, $message );
    }

    public static function send_payment_failed_email( $user, $plan_id ) {
        $to = $user->user_email;
        $subject = 'URGENTE: Problema con tu pago ⚠️';
        $message = self::get_failed_payment_template( $user );

        self::send( $to, $subject, $message );
    }

    private static function send( $to, $subject, $message ) {
        $headers = array( 'Content-Type: text/html; charset=UTF-8' );
        wp_mail( $to, $subject, $message, $headers );
    }

    // --- Templates Básicos (Luego pueden moverse a una tabla de plantillas editable) ---

    private static function get_welcome_template( $user, $plan_name ) {
        $login_url = wp_login_url();
        return "
            <h2>Hola " . esc_html( $user->display_name ) . ",</h2>
            <p>¡Tu inscripción a <strong>$plan_name</strong> ha sido procesada con éxito!</p>
            <p>Ya puedes acceder a tu primera cuota de contenido.</p>
            <p><a href='$login_url' style='background:#007bff;color:white;padding:10px 20px;text-decoration:none;'>Acceder al Campus</a></p>
            <p>Si tienes dudas, contáctanos.</p>
        ";
    }

    private static function get_recurring_payment_template( $user, $plan_name ) {
        return "
            <h2>Hola " . esc_html( $user->display_name ) . ",</h2>
            <p>Hemos recibido tu pago para <strong>$plan_name</strong> correctamente.</p>
            <p>Se ha desbloqueado el siguiente bloque de contenido.</p>
            <p>¡Sigue así!</p>
        ";
    }

    private static function get_failed_payment_template( $user ) {
         return "
            <h2>Hola " . esc_html( $user->display_name ) . ",</h2>
            <p>Intentamos procesar el pago de tu cuota pero falló.</p>
            <p>Por favor, actualiza tu método de pago para no perder el acceso a las clases.</p>
            <p>Inténtalo de nuevo o contáctanos.</p>
        ";
    }
}
