<?php
namespace Alezux_Members\Modules\Finanzas\Includes;

if ( ! \defined( 'ABSPATH' ) ) {
	exit;
}

class Access_Control {

    public static function init() {
        \add_filter( 'learndash_content_access', [ __CLASS__, 'filter_content_access' ], 10, 3 );
    }

    /**
     * Filtra el acceso al contenido de LearnDash.
     *
     * @param bool $access   Si el usuario tiene acceso (True default).
     * @param int  $post_id  ID del Post.
     * @param int  $user_id  ID del Usuario.
     * @return bool
     */
    public static function filter_content_access( $access, $post_id, $user_id ) {
        // Recursion Guard: Evitar loops infinitos si LD llama al filtro internamente
        static $is_running = false;
        if ( $is_running ) {
            return $access;
        }
        $is_running = true;

        // Si LearnDash ya dijo que NO (por otras razones), respetamos.
        if ( ! $access ) {
            $is_running = false;
            return $access;
        }

        try {
             // Verificamos nuestras reglas de Cuotas
            if ( self::is_post_locked( $post_id, $user_id ) ) {
                $is_running = false;
                return false; // Bloqueado por Finanzas
            }
        } catch ( \Throwable $e ) {
            error_log( 'Alezux Critical Error en Access_Control: ' . $e->getMessage() );
            // En caso de error, no bloqueamos (fail-open)
        }

        $is_running = false;
        return $access;
    }

    /**
     * Verifica si un post (lección/tópico) está bloqueado para el usuario actual
     * basado en reglas de Finanzas (Cuotas).
     *
     * @param int $post_id ID de la Lección o Tópico
     * @param int $user_id ID del Usuario (opcional, default current)
     * @return bool True si está bloqueado, False si tiene acceso libre
     */
    public static function is_post_locked( $post_id, $user_id = null ) {
        // DEBUG: Inicio
        error_log( "Alezux Debug: is_post_locked start for Post $post_id User $user_id" );

        if ( ! $user_id ) {
            $user_id = \get_current_user_id();
        }

        // Si es admin o editor, pase libre
        if ( \user_can( $user_id, 'edit_posts' ) ) {
            return false; 
        }
        
        // return false; // Descomentar esto si sigue fallando para probar solo hasta aqui.

        
        // PASO 1: LearnDash Check
        $course_id = 0;
        if ( \function_exists( 'learndash_get_course_id' ) ) {
            $course_id = \learndash_get_course_id( $post_id );
        }
        
        
        if ( ! $course_id ) {
            return false; 
        }
        error_log( "Alezux Debug: Course ID identified: $course_id" );
        

        
        // PASO 2: DB Plan Check
        global $wpdb;
        $plans_table = $wpdb->prefix . 'alezux_finanzas_plans';
        $plan = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $plans_table WHERE course_id = %d LIMIT 1", $course_id ) );

        if ( ! $plan ) {
            return false; 
        }
        error_log( "Alezux Debug: Plan identified: " . $plan->id );
        
        if ( ! $plan ) {
            return false; 
        }
        error_log( "Alezux Debug: Plan identified: " . $plan->id );
        
        // PASO 3: JSON Decode
        $access_rules = \json_decode( $plan->access_rules, true );
        error_log( "Alezux Debug: Rules decoded: " . print_r( $access_rules, true ) );

        if ( empty( $access_rules ) || ! \is_array( $access_rules ) ) {
            return false; 
        }
        
        return false; // BREAKPOINT: Stop before loop

        /*
        // Check rule match
        $required_quota = 0;
        foreach ( $access_rules as $quota_key => $module_ids ) {
            if ( \in_array( $post_id, $module_ids ) ) {
                 $required_quota = (int) filter_var( $quota_key, \FILTER_SANITIZE_NUMBER_INT );
                 break;
            }
        }

        if ( $required_quota === 0 ) {
            return false; 
        }
        */
        

        /*
        // PASO 4: Subscription Check
        $subs_table = $wpdb->prefix . 'alezux_finanzas_subscriptions';
        $subscription = $wpdb->get_row( $wpdb->prepare( 
            "SELECT * FROM $subs_table WHERE user_id = %d AND plan_id = %d AND status IN ('active', 'completed') LIMIT 1", 
            $user_id, $plan->id 
        ) );

        if ( ! $subscription ) {
            return true; // Bloqueado
        }

        if ( $subscription->status === 'completed' ) {
            return false; 
        }

        if ( $subscription->quotas_paid >= $required_quota ) {
            return false; 
        }

        return true; 
        */

        return false; // Default a 'permitido' mientras debugueamos
    }
}
