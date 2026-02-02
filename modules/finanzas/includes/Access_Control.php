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
        if ( ! $user_id ) {
            $user_id = \get_current_user_id();
        }

        // Si es admin o editor, pase libre
        if ( \user_can( $user_id, 'edit_posts' ) ) {
            return false; 
        }

        // 1. Identificar el Curso del Post
        $course_id = 0;
        if ( \function_exists( 'learndash_get_course_id' ) ) {
            $course_id = \learndash_get_course_id( $post_id );
        }
        
        if ( ! $course_id ) {
            return false; // No es contenido LearnDash, no gestionamos bloqueo aquí
        }

        global $wpdb;
        $plans_table = $wpdb->prefix . 'alezux_finanzas_plans';
        
        // 2. Verificar si el curso tiene un Plan asociado en nuestro sistema
        $plan = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $plans_table WHERE course_id = %d LIMIT 1", $course_id ) );

        if ( ! $plan ) {
            return false; // El curso no tiene restricciones de pago por cuotas en nuestro sistema
        }

        // 3. Revisar reglas de acceso del Plan (JSON)
        // Estructura esperada: { "quota_1": [id1, id2], "quota_2": [id3, id4] }
        $access_rules = \json_decode( $plan->access_rules, true );
        
        if ( empty( $access_rules ) || ! \is_array( $access_rules ) ) {
            return false; // No hay reglas definidas, acceso libre
        }

        // Buscamos en qué cuota está restringido este post específico
        $required_quota = 0;
        foreach ( $access_rules as $quota_key => $module_ids ) {
            // Defensive Check: Ensure module_ids is actually an array
            if ( ! \is_array( $module_ids ) ) {
                continue;
            }

            if ( \in_array( $post_id, $module_ids ) ) {
                 // SAFER ALTERNATIVE: Use regex instead of filter_var to avoid constant issues
                 $numeric_part = preg_replace( '/[^0-9]/', '', $quota_key );
                 $required_quota = (int) $numeric_part;
                 break;
            }
        }

        if ( $required_quota === 0 ) {
            // El post no está en ninguna regla de restricción explícita
            return false; 
        }

        // 4. Verificar estado del usuario (Suscripción)
        $subs_table = $wpdb->prefix . 'alezux_finanzas_subscriptions';
        $subscription = $wpdb->get_row( $wpdb->prepare( 
            "SELECT * FROM $subs_table WHERE user_id = %d AND plan_id = %d AND status IN ('active', 'completed') LIMIT 1", 
            $user_id, $plan->id 
        ) );

        if ( ! $subscription ) {
            // Curso tiene plan, Post requiere cuota, Usuario NO tiene suscripción activa
            return true; // Bloqueado.
        }

        if ( $subscription->status === 'completed' ) {
            return false; // Pagó todo, acceso total.
        }

        // 5. Comparar Cuotas
        if ( $subscription->quotas_paid >= $required_quota ) {
            return false; // Tiene suficientes cuotas pagadas
        }

        return true; // Le faltan cuotas. BLOQUEADO.
    }
}
