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
            if ( isset( $_GET['alezux_debug'] ) ) echo "<div style='background:darkgreen;color:white;z-index:9999;position:relative;padding:10px;'>DEBUG: User is Admin/Editor. Access Granted.</div>";
            return false; 
        }

        // 1. Identificar el Curso del Post
        $course_id = 0;
        if ( \function_exists( 'learndash_get_course_id' ) ) {
            $course_id = \learndash_get_course_id( $post_id );
        }
        
        if ( ! $course_id ) {
            if ( isset( $_GET['alezux_debug'] ) ) echo "<div style='background:darkred;color:white;z-index:9999;position:relative;padding:10px;'>DEBUG: No Course ID found.</div>";
            return false; // No es contenido LearnDash, no gestionamos bloqueo aquí
        }

        global $wpdb;
        $plans_table = $wpdb->prefix . 'alezux_finanzas_plans';
        
        // 2. Verificar si el curso tiene un Plan asociado en nuestro sistema
        $plan = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $plans_table WHERE course_id = %d LIMIT 1", $course_id ) );

        if ( ! $plan ) {
            if ( isset( $_GET['alezux_debug'] ) ) echo "<div style='background:darkorange;color:white;z-index:9999;position:relative;padding:10px;'>DEBUG: No Plan found for Course $course_id.</div>";
            return false; // El curso no tiene restricciones de pago por cuotas en nuestro sistema
        }

        // 3. Revisar reglas de acceso del Plan (JSON)
        $access_rules = \json_decode( $plan->access_rules, true );
        
        if ( empty( $access_rules ) || ! \is_array( $access_rules ) ) {
            if ( isset( $_GET['alezux_debug'] ) ) echo "<div style='background:darkorange;color:white;z-index:9999;position:relative;padding:10px;'>DEBUG: No Rules found in Plan.</div>";
            return false; // No hay reglas definidas, acceso libre
        }

        // Hierarchy Check: Detect Parent Lesson (for Topics)
        $parent_id = 0;
        if ( \function_exists( 'learndash_get_lesson_id' ) ) {
            $parent_id = \learndash_get_lesson_id( $post_id );
        }
        if ( ! $parent_id ) {
            $parent_id = \wp_get_post_parent_id( $post_id );
        }
        // Avoid self-reference or course-reference
        if ( $parent_id == $post_id || $parent_id == $course_id ) {
            $parent_id = 0;
        }

        // Buscamos en qué cuota está restringido este post específico
        $required_quota = 0;
        foreach ( $access_rules as $quota_key => $module_ids ) {
            // Defensive Check: Ensure module_ids is actually an array
            if ( ! \is_array( $module_ids ) ) {
                continue;
            }

            // Check if CURRENT POST or PARENT POST is in the rule
            if ( \in_array( $post_id, $module_ids ) || ( $parent_id && \in_array( $parent_id, $module_ids ) ) ) {
                 // SAFER ALTERNATIVE: Use regex instead of filter_var to avoid constant issues
                 $numeric_part = preg_replace( '/[^0-9]/', '', $quota_key );
                 $required_quota = (int) $numeric_part;
                 break;
            }
        }

        // DEBUG VISUAL EN PANTALLA
        $subscription = null; // Initialize for debug safety
        
        // 4. Retrieve Subscription for Debug and Logic
        $subs_table = $wpdb->prefix . 'alezux_finanzas_subscriptions';
        $subscription = $wpdb->get_row( $wpdb->prepare( 
            "SELECT * FROM $subs_table WHERE user_id = %d AND plan_id = %d AND status IN ('active', 'completed') LIMIT 1", 
            $user_id, $plan->id 
        ) );

        if ( isset( $_GET['alezux_debug'] ) ) {
            echo "<div style='background:white; color:black; padding:20px; z-index:9999; position:relative; border:2px solid red;'>";
            echo "<h3>Alezux Debug</h3>";
            echo "Post ID: $post_id <br>";
            echo "Parent ID (Lesson): $parent_id <br>";
            echo "User ID: $user_id <br>";
            echo "Course ID: $course_id <br>";
            echo "Plan ID: " . ($plan ? $plan->id : 'NONE') . "<br>";
            echo "Required Quota: $required_quota <br>";
            echo "User Subscription: " . ($subscription ? 'FOUND' : 'NOT FOUND') . "<br>";
            if ( $subscription ) {
                echo "Sub Status: " . $subscription->status . "<br>";
                echo "Quotas Paid: " . $subscription->quotas_paid . "<br>";
            }
            echo "<strong>RESULT: " . ($subscription && $subscription->quotas_paid < $required_quota ? 'LOCKED' : 'UNLOCKED') . "</strong>";
            echo "<pre>Rules: " . print_r($access_rules, true) . "</pre>";
            echo "</div>";
        }

        if ( $required_quota === 0 ) {
            if ( isset( $_GET['alezux_debug'] ) ) echo "<div style='background:darkorange;color:white;z-index:9999;position:relative;padding:10px;'>BLOCK: Quota 0 (No rule matched).</div>";
            return false; 
        }

        if ( ! $subscription ) {
            return true; // Bloqueado
        }

        if ( $subscription->status === 'completed' ) {
            return false; 
        }

        if ( $subscription->quotas_paid >= $required_quota ) {
            return false; 
        }

        return true; // Le faltan cuotas. BLOQUEADO.
    }
}
