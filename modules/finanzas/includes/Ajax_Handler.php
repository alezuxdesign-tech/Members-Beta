<?php
namespace Alezux_Members\Modules\Finanzas\Includes;

if ( ! \defined( 'ABSPATH' ) ) {
	exit;
}

class Ajax_Handler {

	public static function init() {
		\add_action( 'wp_ajax_alezux_get_course_modules', [ __CLASS__, 'get_course_modules' ] );
		\add_action( 'wp_ajax_alezux_create_stripe_plan', [ __CLASS__, 'create_stripe_plan' ] );
        \add_action( 'wp_ajax_alezux_get_sales_history', [ __CLASS__, 'get_sales_history' ] );
        \add_action( 'wp_ajax_alezux_get_plans_list', [ __CLASS__, 'get_plans_list' ] );
        \add_action( 'wp_ajax_alezux_get_subscriptions_list', [ __CLASS__, 'get_subscriptions_list' ] );
        \add_action( 'wp_ajax_alezux_delete_plan', [ __CLASS__, 'delete_plan' ] );
        \add_action( 'wp_ajax_alezux_manual_subs_payment', [ __CLASS__, 'manual_subscription_payment' ] );
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
                // Solo incluimos lecciones (sfwd-lessons), los tópicos se omiten
                if ( $post->post_type === 'sfwd-lessons' ) {
                    // OMITIR Separadores visuales
                    if ( strpos( $post->post_title, '[Separador' ) !== false ) {
                        continue;
                    }

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

        // Integración con Stripe
        $stripe = \Alezux_Members\Modules\Finanzas\Includes\Stripe_API::get_instance();
        
        // Determinar intervalo
        $interval = $_POST['frequency'] ?? 'month';
        if ( $total_quotas == 1 ) {
            $interval = 'contado'; // Lógica especial para pago único
        }

        $stripe_result = $stripe->create_plan( $plan_name, $quota_amount, $interval );

        if ( \is_wp_error( $stripe_result ) ) {
             \wp_send_json_error( 'Error de Stripe: ' . $stripe_result->get_error_message() );
        }

        $stripe_prod_id = $stripe_result['product_id'];
        $stripe_price_id = $stripe_result['price_id'];

        // Guardar en DB
        global $wpdb;
        $table_plans = $wpdb->prefix . 'alezux_finanzas_plans';
        
        // Generar Token Único
        $token = bin2hex( random_bytes( 16 ) );

        $wpdb->insert( 
            $table_plans, 
            [ 
                'name' => $plan_name, 
                'course_id' => $course_id,
                'stripe_product_id' => $stripe_prod_id,
                'stripe_price_id' => $stripe_price_id,
                'total_quotas' => $total_quotas,
                'quota_amount' => $quota_amount,
                'frequency'    => $interval,
                'token'        => $token,
                'access_rules' => \json_encode( $rules ),
            ] 
        );

        $plan_id = $wpdb->insert_id;

        if ( $plan_id ) {
            \wp_send_json_success( [ 'plan_id' => $plan_id, 'message' => 'Plan creado correctamene en Stripe y WordPress.' ] );
        } else {
            \wp_send_json_error( 'Error al guardar en base de datos local.' );
        }
	}


    public static function get_sales_history() {
        \check_ajax_referer( 'alezux_finanzas_nonce', 'nonce' );

        if ( ! \current_user_can( 'manage_options' ) ) {
            \wp_send_json_error( 'Permisos insuficientes.' );
        }

        global $wpdb;

        // Parametros DataTables / Custom
        $page = isset($_POST['page']) ? \intval($_POST['page']) : 1;
        $limit = isset($_POST['limit']) ? \intval($_POST['limit']) : 10;
        $search = isset($_POST['search']) ? \sanitize_text_field($_POST['search']) : '';
        $filter_course = isset($_POST['filter_course']) ? \intval($_POST['filter_course']) : 0;
        $filter_status = isset($_POST['filter_status']) ? \sanitize_text_field($_POST['filter_status']) : '';
        
        $offset = ($page - 1) * $limit;

        // Tablas
        $t_trans = $wpdb->prefix . 'alezux_finanzas_transactions';
        $t_plans = $wpdb->prefix . 'alezux_finanzas_plans';
        $t_subs = $wpdb->prefix . 'alezux_finanzas_subscriptions';
        $t_users = $wpdb->users;
        
        // Query Base
        $sql = "SELECT SQL_CALC_FOUND_ROWS 
                    t.*, 
                    u.display_name as user_name, 
                    u.user_email, 
                    p.name as plan_name, 
                    p.total_quotas, 
                    p.frequency,
                    s.quotas_paid as sub_quotas_paid,
                    s.status as sub_status
                FROM $t_trans t
                LEFT JOIN $t_users u ON t.user_id = u.ID
                LEFT JOIN $t_plans p ON t.plan_id = p.id
                LEFT JOIN $t_subs s ON t.subscription_id = s.id
                WHERE 1=1";

        $args = [];

        // Filtros
        if ( ! empty($search) ) {
            $sql .= " AND (u.display_name LIKE %s OR u.user_email LIKE %s OR t.transaction_ref LIKE %s)";
            $args[] = '%' . $wpdb->esc_like($search) . '%';
            $args[] = '%' . $wpdb->esc_like($search) . '%';
            $args[] = '%' . $wpdb->esc_like($search) . '%';
        }

        if ( $filter_course > 0 ) {
            // Nota: El filtro pide por curso, pero transaction tiene plan_id. Join con plan tiene course_id.
            $sql .= " AND p.course_id = %d";
            $args[] = $filter_course;
        }
        
        if ( ! empty($filter_status) ) {
            $sql .= " AND t.status = %s";
            $args[] = $filter_status;
        }

        // Orden y Paginación
        $sql .= " ORDER BY t.created_at DESC";
        $sql .= " LIMIT %d OFFSET %d";
        $args[] = $limit;
        $args[] = $offset;

        $results = $wpdb->get_results( $wpdb->prepare( $sql, $args ) );
        $total_rows = $wpdb->get_var( "SELECT FOUND_ROWS()" );

        // Formatear resultados
        $data = [];
        foreach ($results as $row) {
            
            // Logica "Recurrente (1/5)" vs "Contado"
            $payment_desc = 'Pago Único';
            if ( $row->total_quotas > 1 ) {
                 // Intentar calcular cuota actual aproximada: 
                 // Si sub_quotas_paid es 3, y esta es la ultima transaccion, quizas es la 3.
                 // Simplificación: Mostrar X/Total basado en subscription actual 
                 // OMEJOR: Contado vs Suscripción
                 $payment_desc = "Recurrente ({$row->sub_quotas_paid}/{$row->total_quotas})";
            } elseif ( $row->total_quotas == 1 ) {
                 $payment_desc = 'De Contado';
            }

            $data[] = [
                'id' => $row->id,
                'student' => $row->user_name ? $row->user_name . ' (' . $row->user_email . ')' : 'Usuario Eliminado',
                'method' => ucfirst($row->method),
                'amount' => $row->amount . ' ' . $row->currency,
                'course' => $row->plan_name, // O nombre del curso si preferimos
                'quotas_desc' => $payment_desc,
                'status' => $row->status,
                'date' => date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $row->created_at ) ),
                'ref' => $row->transaction_ref
            ];
        }

        \wp_send_json_success( [
            'rows' => $data,
            'total' => (int)$total_rows,
            'pages' => ceil($total_rows / $limit),
            'current_page' => $page
        ] );
    }

    public static function get_plans_list() {
        \check_ajax_referer( 'alezux_finanzas_nonce', 'nonce' );

        if ( ! \current_user_can( 'manage_options' ) ) {
            \wp_send_json_error( 'Permisos insuficientes.' );
        }

        global $wpdb;
        $search = isset($_POST['search']) ? \sanitize_text_field($_POST['search']) : '';
        $course_id = isset($_POST['course_id']) ? \intval($_POST['course_id']) : 0;
        
        $t_plans = $wpdb->prefix . 'alezux_finanzas_plans';
        
        $sql = "SELECT p.* FROM $t_plans p WHERE 1=1";
        $args = [];

        if ( ! empty( $search ) ) {
            $sql .= " AND p.name LIKE %s";
            $args[] = '%' . $wpdb->esc_like($search) . '%';
        }

        if ( $course_id > 0 ) {
            $sql .= " AND p.course_id = %d";
            $args[] = $course_id;
        }

        $sql .= " ORDER BY p.id DESC";

        if ( ! empty( $args ) ) {
            $results = $wpdb->get_results( $wpdb->prepare( $sql, $args ) );
        } else {
            $results = $wpdb->get_results( $sql );
        }

        $data = [];
        foreach ( $results as $row ) {
            $course_title = get_the_title( $row->course_id );
            
            // AUTO-MIGRACIÓN: Si no tiene token, generarlo y guardarlo ahora mismo.
            $token = $row->token;
            if ( empty( $token ) ) {
                $token = bin2hex( random_bytes( 16 ) );
                $wpdb->update( 
                    $t_plans, 
                    [ 'token' => $token ], 
                    [ 'id' => $row->id ] 
                );
            }

            // Generar Link Directo Seguro
            $buy_link = home_url( '/?alezux_buy_token=' . $token );

            $data[] = [
                'id' => $row->id,
                'name' => $row->name,
                'course' => $course_title ? $course_title : '(Curso Eliminado)',
                'price' => $row->quota_amount,
                'quotas' => $row->total_quotas,
                'frequency' => $row->frequency,
                'buy_link' => $buy_link
            ];
        }

        \wp_send_json_success( $data );
    }

    public static function delete_plan() {
        \check_ajax_referer( 'alezux_finanzas_nonce', 'nonce' );

        if ( ! \current_user_can( 'manage_options' ) ) {
            \wp_send_json_error( 'Permisos insuficientes.' );
        }

        $id = isset($_POST['id']) ? \intval($_POST['id']) : 0;
        if ( !$id ) \wp_send_json_error('ID Inválido');

        global $wpdb;
        $t_plans = $wpdb->prefix . 'alezux_finanzas_plans';
        
        $deleted = $wpdb->delete( $t_plans, ['id' => $id] );

        if ( $deleted ) {
            \wp_send_json_success( 'Plan eliminado.' );
        } else {
            \wp_send_json_error( 'No se pudo eliminar el plan o no existe.' );
        }
    }

    public static function get_subscriptions_list() {
        \check_ajax_referer( 'alezux_finanzas_nonce', 'nonce' );

        if ( ! \current_user_can( 'manage_options' ) ) {
            \wp_send_json_error( 'Permisos insuficientes.' );
        }

        global $wpdb;
        $search = isset($_POST['search']) ? \sanitize_text_field($_POST['search']) : '';
        
        $t_subs = $wpdb->prefix . 'alezux_finanzas_subscriptions';
        $t_plans = $wpdb->prefix . 'alezux_finanzas_plans';
        $t_users = $wpdb->users;

        $sql = "SELECT s.*, u.display_name, u.user_email, p.name as plan_name, p.total_quotas, p.quota_amount 
                FROM $t_subs s
                LEFT JOIN $t_users u ON s.user_id = u.ID
                LEFT JOIN $t_plans p ON s.plan_id = p.id
                WHERE 1=1";
        
        $args = [];

        if ( ! empty( $search ) ) {
            $sql .= " AND (u.display_name LIKE %s OR u.user_email LIKE %s)";
            $args[] = '%' . $wpdb->esc_like($search) . '%';
            $args[] = '%' . $wpdb->esc_like($search) . '%';
        }

        $sql .= " ORDER BY s.created_at DESC";

        if ( ! empty( $args ) ) {
            $results = $wpdb->get_results( $wpdb->prepare( $sql, $args ) );
        } else {
            $results = $wpdb->get_results( $sql );
        }

        $data = [];
        foreach ( $results as $row ) {
            // Calcular próximo pago
            $next_payment = '-';
            if ( $row->status === 'active' && $row->next_payment_date ) {
                $next_payment = \date_i18n( \get_option( 'date_format' ), \strtotime( $row->next_payment_date ) );
                
                // Si está vencido
                if ( \strtotime( $row->next_payment_date ) < \time() ) {
                    $next_payment .= ' <span style="color:red;">(Atrasado)</span>';
                }
            } elseif ( $row->status === 'completed' ) {
                $next_payment = 'Pagado Totalmente';
            }

            // Avatar
            $avatar_url = \get_avatar_url( $row->user_id, ['size' => 48] );
            
            // Porcentaje
            $percent = 0;
            if ( $row->total_quotas > 0 ) {
                $percent = \round( ( $row->quotas_paid / $row->total_quotas ) * 100 );
            }

            $data[] = [
                'id' => $row->id,
                'student' => $row->display_name ? $row->display_name : 'Usuario Eliminado',
                'student_email' => $row->user_email, // Raw email
                'student_avatar' => $avatar_url,
                'plan' => $row->plan_name,
                'total_quotas' => $row->total_quotas, // Para mostrar "3 CUOTAS"
                'amount' => '$' . \number_format($row->quota_amount, 2, ',', '.'), // Formato correcto moneda
                'raw_amount' => $row->quota_amount,
                'status' => $row->status,
                'quotas_paid' => $row->quotas_paid,
                'percent' => $percent,
                'next_payment' => $next_payment,
                'next_payment_raw' => $row->next_payment_date,
                'stripe_id' => $row->stripe_subscription_id
            ];
        }

        \wp_send_json_success( $data );
    }

    public static function manual_subscription_payment() {
        \check_ajax_referer( 'alezux_finanzas_nonce', 'nonce' );

        if ( ! \current_user_can( 'manage_options' ) ) {
            \wp_send_json_error( 'Permisos insuficientes.' );
        }

        $sub_id = isset($_POST['subscription_id']) ? \intval($_POST['subscription_id']) : 0;
        $amount = isset($_POST['amount']) ? \floatval($_POST['amount']) : 0;
        $note = isset($_POST['note']) ? \sanitize_textarea_field($_POST['note']) : '';

        if ( !$sub_id || !$amount ) {
            \wp_send_json_error( 'Datos incompletos.' );
        }

        global $wpdb;
        $t_subs = $wpdb->prefix . 'alezux_finanzas_subscriptions';
        $t_sales = $wpdb->prefix . 'alezux_finanzas_sales_history';
        $t_plans = $wpdb->prefix . 'alezux_finanzas_plans';

        // Obtener suscripción actual
        $sub = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $t_subs WHERE id = %d", $sub_id ) );

        if ( !$sub ) {
            \wp_send_json_error( 'Suscripción no encontrada.' );
        }

        // Obtener detalles del plan para saber total de cuotas
        $plan = $wpdb->get_row( $wpdb->prepare( "SELECT total_quotas FROM $t_plans WHERE id = %d", $sub->plan_id ) );
        $total_quotas = $plan ? $plan->total_quotas : 0;

        // 1. Insertar en Historial de Ventas
        $wpdb->insert(
            $t_sales,
            [
                'user_id' => $sub->user_id,
                'plan_id' => $sub->plan_id,
                'amount' => $amount,
                'currency' => 'USD', // Asumido
                'status' => 'succeeded',
                'payment_method' => 'manual', // Importante para diferenciar
                'stripe_payment_id' => 'MANUAL_' . \time(), // ID ficticio único
                'created_at' => \current_time( 'mysql' )
            ]
        );

        // 2. Actualizar Suscripción
        $current_quotas_paid = (int) $sub->quotas_paid;
        $new_quotas_paid = $current_quotas_paid + 1;
        $new_status = $sub->status;

        // Si estaba atrasado, lo reactivamos (asumiendo que pagó lo pendiente)
        if ( $sub->status === 'past_due' || $sub->status === 'canceled' ) {
            $new_status = 'active';
        }

        // Verificar si completó el plan
        if ( $new_quotas_paid >= $total_quotas ) {
            $new_status = 'completed';
        }

        // Actualizar fecha de update y quotas
        $result = $wpdb->update(
            $t_subs,
            [
                'quotas_paid' => $new_quotas_paid,
                'status' => $new_status
            ],
            [ 'id' => $sub_id ]
        );

        // Opcional: Agregar nota interna (si tuviéramos tabla de notas) o log
        
        \wp_send_json_success( 'Pago registrado y suscripción actualizada.' );
    }
}
