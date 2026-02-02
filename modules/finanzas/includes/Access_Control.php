<?php
namespace Alezux_Members\Modules\Finanzas\Includes;

if ( ! \defined( 'ABSPATH' ) ) {
	exit;
}

class Access_Control {

    public static function init() {
        \add_filter( 'learndash_content_access', [ __CLASS__, 'filter_content_access' ], 10, 3 );
        
        // SECURITY ENFORCEMENT: Force check on template load (Backup in case LD filter is bypassed)
        \add_action( 'template_redirect', [ __CLASS__, 'enforce_template_restriction' ] );
    }

    /**
     * Verificación forzada antes de cargar la plantilla.
     * Garantiza que el bloqueo funcione incluso si LD ignora el filtro.
     */
    public static function enforce_template_restriction() {
        if ( ! \is_singular( ['sfwd-lessons', 'sfwd-topic'] ) ) {
            return;
        }

        $post_id = \get_queried_object_id();
        $user_id = \get_current_user_id();

        // 1. Check Lock security
        // Note: is_post_locked already handles ?alezux_debug=1 output internally
        $is_locked = self::is_post_locked( $post_id, $user_id );

        if ( $is_locked ) {
            // Contenido Bloqueado
            // Opcional: Redirigir a página de venta o mostrar mensaje
            $message = "<h1>Acceso Restringido</h1>";
            $message .= "<p>Este contenido pertenece a una cuota que aún no has desbloqueado.</p>";
            $message .= "<p><a href='" . \home_url('/mis-finanzas') . "'>Ver mi estado de cuenta</a></p>";
            
            \wp_die( $message, 'Contenido Bloqueado', [ 'response' => 403 ] );
        }
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
        // Pero si dijo SI, nosotros verificamos doblemente con is_post_locked
        // if ( ! $access ) { ... } // Comentado: Queremos verificar incluso si LD da acceso

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
        // FORCE DEBUG MODE DETECTION
        $debug_mode = isset( $_GET['alezux_debug'] );

        if ( ! $user_id ) {
            $user_id = \get_current_user_id();
        }

        // Si es admin o editor, pase libre
        if ( \user_can( $user_id, 'edit_posts' ) ) {
            if ( $debug_mode ) \wp_die( "DEBUG: User is Admin/Editor. Access Granted. (ID: $user_id)" );
            return false; 
        }

        // 1. Identificar el Curso del Post
        $course_id = 0;
        if ( \function_exists( 'learndash_get_course_id' ) ) {
            $course_id = \learndash_get_course_id( $post_id );
        }
        
        if ( ! $course_id ) {
            if ( $debug_mode ) \wp_die( "DEBUG: No Course ID found for Post $post_id." );
            return false; // No es contenido LearnDash, no gestionamos bloqueo aquí
        }

        global $wpdb;
        $plans_table = $wpdb->prefix . 'alezux_finanzas_plans';
        
        // 2. Verificar si el curso tiene un Plan asociado en nuestro sistema
        $plan = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $plans_table WHERE course_id = %d LIMIT 1", $course_id ) );

        if ( ! $plan ) {
            if ( $debug_mode ) \wp_die( "DEBUG: No Plan found for Course $course_id in table $plans_table." );
            return false; // El curso no tiene restricciones de pago por cuotas en nuestro sistema
        }

        // 3. Revisar reglas de acceso del Plan (JSON)
        $access_rules = \json_decode( $plan->access_rules, true );
        
        if ( empty( $access_rules ) || ! \is_array( $access_rules ) ) {
            if ( $debug_mode ) \wp_die( "DEBUG: No Rules found in Plan ID " . $plan->id );
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
        
        // DEBUG: Imprimir estructura para confirmar en logs si es necesario
        // error_log( "Alezux Rules Structure: " . print_r($access_rules, true) );

        foreach ( $access_rules as $rule_post_id => $rule_quota ) {
            // Estructura Real Confirmada: [ "342" => 2, "424" => 3 ] 
            // Donde Key es el Post ID y Value es el Número de Cuota
            
            // Cast to int for comparison
            $rule_post_id = (int)$rule_post_id;
            $rule_quota   = (int)$rule_quota;

            // Check if CURRENT POST or PARENT POST matches the Rule Key
            if ( $post_id === $rule_post_id || ( $parent_id && $parent_id === $rule_post_id ) ) {
                 $required_quota = $rule_quota;
                 break; // Encontramos la regla, salimos.
            }
        }

        // 4. Retrieve Subscription for Debug and Logic
        $subs_table = $wpdb->prefix . 'alezux_finanzas_subscriptions';
        $subscription = $wpdb->get_row( $wpdb->prepare( 
            "SELECT * FROM $subs_table WHERE user_id = %d AND plan_id = %d AND status IN ('active', 'completed') LIMIT 1", 
            $user_id, $plan->id 
        ) );

        if ( $debug_mode ) {
            $msg = "<div style='background:white; color:black; padding:20px; border:2px solid red;'>";
            $msg .= "<h3>Alezux Debug (TEMPLATE_REDIRECT MODE)</h3>";
            $msg .= "Post ID: $post_id <br>";
            $msg .= "Parent ID (Lesson): $parent_id <br>";
            $msg .= "User ID: $user_id <br>";
            $msg .= "Course ID: $course_id <br>";
            $msg .= "Plan ID: " . ($plan ? $plan->id : 'NONE') . "<br>";
            $msg .= "Required Quota: $required_quota <br>";
            $msg .= "User Subscription: " . ($subscription ? 'FOUND' : 'NOT FOUND') . "<br>";
            if ( $subscription ) {
                $msg .= "Sub Status: " . $subscription->status . "<br>";
                $msg .= "Quotas Paid: " . $subscription->quotas_paid . "<br>";
            }
            
            // Logic Check for Visual Label
            $is_locked_visual = false;
            if ( ! $subscription ) {
                $is_locked_visual = true;
            } elseif ( $subscription->status !== 'completed' && $subscription->quotas_paid < $required_quota ) {
                $is_locked_visual = true;
            }

            $msg .= "<strong>RESULT: " . ($is_locked_visual ? '<span style="color:red">LOCKED</span>' : '<span style="color:green">UNLOCKED</span>') . "</strong>";
            $msg .= "<pre>Rules: " . print_r($access_rules, true) . "</pre>";
            $msg .= "</div>";
            
            \wp_die( $msg );
        }

        if ( $required_quota === 0 ) {
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
