<?php
namespace Alezux_Members\Modules\Finanzas\Includes;

if ( ! \defined( 'ABSPATH' ) ) {
	exit;
}

class Ajax_Handler {

	public static function init() {
		\add_action( 'wp_ajax_alezux_get_course_modules', [ __CLASS__, 'get_course_modules' ] );
		\add_action( 'wp_ajax_alezux_create_stripe_plan', [ __CLASS__, 'create_stripe_plan' ] );
	}

	public static function get_course_modules() {
		\check_ajax_referer( 'alezux_finanzas_nonce', 'nonce' );

		if ( ! \current_user_can( 'manage_options' ) ) {
			\wp_send_json_error( 'Permisos insuficientes.' );
		}

		$course_id = \intval( $_POST['course_id'] );
		
		if ( ! $course_id ) {
			\wp_send_json_error( 'ID de curso inválido.' );
		}

		// Obtener módulos/lecciones del curso usando funciones de LearnDash
        // Nota: Asumimos que LearnDash está activo. Si no, usamos posts normales.
        $modules = [];
        
        if ( \function_exists( 'learndash_get_course_steps' ) ) {
            $steps = \learndash_get_course_steps( $course_id );
            foreach ( $steps as $step_id ) {
                $post = \get_post( $step_id );
                // Solo incluimos lecciones y tópicos principales, no quices
                if ( $post->post_type === 'sfwd-lessons' || $post->post_type === 'sfwd-topic' ) {
                    $modules[] = [
                        'id' => $post->ID,
                        'title' => $post->post_title,
                        'type' => $post->post_type
                    ];
                }
            }
        } else {
            // Fallback si función LD no existe (raro)
            $modules[] = ['id' => 0, 'title' => 'LearnDash no detectado', 'type' => 'error'];
        }

		\wp_send_json_success( $modules );
	}

	public static function create_stripe_plan() {
		\check_ajax_referer( 'alezux_finanzas_nonce', 'nonce' );

        if ( ! \current_user_can( 'manage_options' ) ) {
			\wp_send_json_error( 'Permisos insuficientes.' );
		}

        $plan_name = \sanitize_text_field( $_POST['plan_name'] );
        $course_id = \intval( $_POST['course_id'] );
        $quota_amount = \floatval( $_POST['quota_amount'] );
        $total_quotas = \intval( $_POST['total_quotas'] );
        $rules = isset($_POST['rules']) ? $_POST['rules'] : [];

        // AQUÍ VA LA INTEGRACIÓN CON STRIPE REAL (PENDIENTE)
        // Por ahora simularemos la respuesta exitosa para probar el UI.
        
        // Simulación:
        $fake_stripe_prod_id = 'prod_TEST_' . \uniqid();
        $fake_stripe_price_id = 'price_TEST_' . \uniqid();

        // Guardar en DB
        global $wpdb;
        $table_plans = $wpdb->prefix . 'alezux_finanzas_plans';
        
        $wpdb->insert( 
            $table_plans, 
            [ 
                'name' => $plan_name, 
                'course_id' => $course_id,
                'stripe_product_id' => $fake_stripe_prod_id,
                'stripe_price_id' => $fake_stripe_price_id,
                'total_quotas' => $total_quotas,
                'quota_amount' => $quota_amount,
                'access_rules' => \json_encode( $rules ),
            ] 
        );

        $plan_id = $wpdb->insert_id;

        if ( $plan_id ) {
            \wp_send_json_success( [ 'plan_id' => $plan_id, 'message' => 'Plan creado y guardado en DB (Simulación Stripe).' ] );
        } else {
            \wp_send_json_error( 'Error al guardar en base de datos.' );
        }
	}
}
