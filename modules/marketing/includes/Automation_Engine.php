<?php
namespace Alezux_Members\Modules\Marketing\Includes;

if ( ! \defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Automation_Engine
 * Maneja la lógica de ejecución de automatizaciones basadas en Grafos (Nodos y Conexiones).
 */
class Automation_Engine {

    /**
     * Procesa un evento disparado por el sistema.
     * 
     * @param string $event_key El slug del evento (ej: 'pago_exitoso').
     * @param array $data Datos iniciales (usuario, plan, etc).
     */
    public static function process_event( $event_key, $data = [] ) {
        global $wpdb;
        $table_automations = $wpdb->prefix . 'alezux_marketing_automations';

        // 1. Buscar automatizaciones activas para este trigger
        $automations = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM $table_automations WHERE event_trigger = %s AND status = 'active'",
            $event_key
        ) );

        if ( empty( $automations ) ) {
            return;
        }

        foreach ( $automations as $automation ) {
            self::start_automation( $automation, $data );
        }
    }

    /**
     * Inicia una automatización desde cero.
     * @param object $automation Objeto de la BD
     * @param array $context_data Datos del trigger
     * @param bool $is_dry_run Si es true, no ejecuta acciones reales
     * @return array|void Retorna el trace si es dry_run
     */
    public static function start_automation( $automation, $context_data, $is_dry_run = false ) {
        $blueprint = \json_decode( $automation->blueprint, true );
        if ( ! $blueprint || ! isset( $blueprint['nodes'] ) ) return;

        // Validar usuario
        $user_id = $context_data['user']->ID ?? 0;

        // Contexto de Ejecución
        $execution_id = uniqid('exec_');
        $trace = []; // Array de pasos

        // 1. Encontrar el Nodo Trigger
        $trigger_node = null;
        foreach ( $blueprint['nodes'] as $node ) {
            if ( \in_array( $node['type'], ['trigger', 'inactivity', 'expiration'] ) ) {
                $trigger_node = $node;
                break;
            }
        }

        if ( ! $trigger_node ) return;

        // Registro inicial del log
        if ( ! $is_dry_run ) {
            // Podríamos crear el registro "running" aquí y actualizarlo al final
        }

        // 2. Iniciar ejecución
        $start_time = microtime(true);
        
        // Pasamos el trace por referencia para ir llenándolo
        self::run_step( $trigger_node['id'], $blueprint, $context_data, $automation->id, $is_dry_run, $trace );

        $duration =  round((microtime(true) - $start_time) * 1000);

        // 3. Guardar log en BD
        if ( ! $is_dry_run ) {
             global $wpdb;
             $table_logs = $wpdb->prefix . 'alezux_marketing_logs';
             $wpdb->insert( $table_logs, [
                 'automation_id'  => $automation->id,
                 'user_id'        => $user_id,
                 'execution_mode' => 'production',
                 'status'         => 'success', // Ojo: Si hay un error fatal en PHP morirá antes, mejorar con try/catch en run_step
                 'steps_trace'    => \json_encode( $trace ),
                 'duration_ms'    => $duration
             ] );
        }

        if ( $is_dry_run ) {
            return [
                'status' => 'success',
                'trace'  => $trace,
                'duration' => $duration
            ];
        }
    }

    /**
     * Retoma una automatización pausada (por Delay).
     */
    public static function resume_automation( $task_id ) {
        global $wpdb;
        $table_tasks = $wpdb->prefix . 'alezux_marketing_tasks';
        $table_automations = $wpdb->prefix . 'alezux_marketing_automations';

        $task = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_tasks WHERE id = %d", $task_id ) );
        if ( ! $task ) return;

        $automation = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_automations WHERE id = %d", $task->automation_id ) );
        if ( ! $automation ) return; // Automatización borrada?

        $blueprint = \json_decode( $automation->blueprint, true );
        $context_data = \json_decode( $task->context_data, true );

        // Obtener el nodo siguiente al delay (el que guardamos como objetivo)
        // Ojo: En la lógica de delay, guardamos el ID del nodo QUE SE VA A EJECUTAR AHORA.
        self::run_step( $task->node_id, $blueprint, $context_data, $automation->id );

        // Marcar tarea como completada
        $wpdb->update( $table_tasks, [ 'status' => 'completed' ], [ 'id' => $task_id ] );
    }

    /**
     * Ejecuta la lógica de un nodo recursivamente.
     */
    private static function run_step( $node_id, $blueprint, $context, $automation_id, $is_dry_run = false, &$trace = [] ) {
        $node = self::get_node_by_id( $node_id, $blueprint['nodes'] );
        if ( ! $node ) return;

        // Registrar paso en el trace
        $step_log = [
            'node_id'   => $node_id,
            'node_type' => $node['type'],
            'status'    => 'running',
            'timestamp' => current_time('mysql'),
            'output'    => []
        ];

        /* =========================================
           LÓGICA DRY RUN: SIMULACIÓN DE ACCIONES
           ========================================= */

        $next_branch = 'default';

        // --- EJECUCIÓN DEL NODO ---
        switch ( $node['type'] ) {
            case 'trigger':
            case 'inactivity':
            case 'expiration':
                // Passthrough, ya se disparó
                break;

            case 'email':
                $step_log['output']['action'] = 'send_email';
                if ( $is_dry_run ) {
                    $step_log['output']['dry_run_skipped'] = true;
                    $step_log['output']['email_data'] = $node['data'];
                } else {
                    self::action_send_email( $node, $context, $automation_id );
                    $step_log['output']['sent'] = true;
                }
                break;

            case 'condition':
                $result = self::evaluate_condition( $node, $context );
                $next_branch = $result ? 'true' : 'false';
                $step_log['output']['condition_result'] = $result;
                $step_log['output']['branch'] = $next_branch;
                break;

            case 'delay':
                // Calcular tiempo y PAUSAR
                $minutes = \intval( $node['data']['minutes'] ?? 0 );
                $step_log['output']['delay_minutes'] = $minutes;
                
                if ( $minutes > 0 ) {
                    if ( $is_dry_run ) {
                        $step_log['output']['dry_run_note'] = "Delay skipped for simulation (would wait $minutes mins)";
                        // En Dry Run seguimos ejecutando como si hubiera pasado el tiempo
                    } else {
                        $next_node_id = self::get_next_node_id( $node_id, $blueprint['connections'], 'default' );
                        if ( $next_node_id ) {
                            self::schedule_task( $next_node_id, $minutes, $context, $automation_id );
                        }
                        $step_log['status'] = 'paused';
                        $trace[] = $step_log; // Guardar log antes de pausar
                        return; // DETENEMOS LA EJECUCIÓN AQUÍ
                    }
                }
                break;

            case 'course':
                // Puede ser Acción o Condición
                if ( ($node['data']['action'] ?? '') === 'check' ) {
                    $result = self::evaluate_condition( $node, $context );
                    $next_branch = $result ? 'true' : 'false';
                    $step_log['output']['condition_result'] = $result;
                    $step_log['output']['branch'] = $next_branch;
                } else {
                     if ( $is_dry_run ) {
                        $step_log['output']['dry_run_skipped'] = true;
                        $step_log['output']['action'] = 'enroll_course';
                        $step_log['output']['course_id'] = $node['data']['course_id'];
                    } else {
                        self::action_course( $node, $context );
                        $step_log['output']['enrolled'] = true;
                    }
                }
                break;

            case 'student_tag':
                // Puede ser Acción o Condición
                if ( ($node['data']['action'] ?? '') === 'check_has' ) {
                    $result = self::evaluate_condition( $node, $context );
                    $next_branch = $result ? 'true' : 'false';
                    $step_log['output']['condition_result'] = $result;
                    $step_log['output']['branch'] = $next_branch;
                } else {
                     if ( $is_dry_run ) {
                        $step_log['output']['dry_run_skipped'] = true;
                        $step_log['output']['action'] = 'change_tag';
                        $step_log['output']['tag_action'] = $node['data']['action'];
                        $step_log['output']['tag'] = $node['data']['tag'];
                    } else {
                        self::action_student_tag( $node, $context );
                        $step_log['output']['tag_changed'] = true;
                    }
                }
                break;

            case 'payment_status':
                $result = self::evaluate_condition( $node, $context );
                $next_branch = $result ? 'true' : 'false';
                $step_log['output']['condition_result'] = $result;
                $step_log['output']['branch'] = $next_branch;
                break;
        }



        $step_log['status'] = 'completed';
        $trace[] = $step_log;

        // --- NAVEGACIÓN AL SIGUIENTE NODO ---
        $next_node_id = self::get_next_node_id( $node_id, $blueprint['connections'], $next_branch );

        if ( $next_node_id ) {
            self::run_step( $next_node_id, $blueprint, $context, $automation_id, $is_dry_run, $trace );
        }
    }

    /**
     * Busca el ID del siguiente nodo basado en conexiones.
     */
    private static function get_next_node_id( $current_id, $connections, $branch = 'default' ) {
        foreach ( $connections as $conn ) {
            if ( $conn['from'] === $current_id ) {
                // Compatibilidad hacia atrás: si no tiene sourceHandle, asumimos default
                $conn_handle = $conn['sourceHandle'] ?? 'default';
                
                // Si buscamos 'default', aceptamos 'default', null o vacio.
                // Si buscamos 'true'/'false', debe coincidir exacto.
                
                if ( $branch === 'default' ) {
                    // Acepta cualquier conexión que NO sea explícitamente true/false (o si es default)
                    if ( empty($conn_handle) || $conn_handle === 'default' ) return $conn['to'];
                } else {
                    if ( $conn_handle === $branch ) return $conn['to'];
                }
            }
        }
        return null;
    }

    private static function get_node_by_id( $id, $nodes ) {
        foreach ( $nodes as $node ) {
            if ( $node['id'] === $id ) return $node;
        }
        return null;
    }

    /**
     * Agenda una tarea para el futuro en alezux_marketing_tasks
     */
    private static function schedule_task( $target_node_id, $minutes, $context, $automation_id ) {
        global $wpdb;
        $table_tasks = $wpdb->prefix . 'alezux_marketing_tasks';

        $scheduled_at = \date( 'Y-m-d H:i:s', \strtotime( "+$minutes minutes" ) );
        $user_id = $context['user']->ID ?? 0;

        $wpdb->insert( $table_tasks, [
            'automation_id' => $automation_id,
            'user_id'       => $user_id,
            'node_id'       => $target_node_id,
            'context_data'  => \json_encode( $context ),
            'status'        => 'pending',
            'scheduled_at'  => $scheduled_at
        ] );
    }

    private static function evaluate_condition( $node, $context ) {
        $type = $node['type'] === 'condition' ? ($node['data']['condition_type'] ?? '') : $node['type'];
        $user = $context['user'] ?? null;

        if ( ! $user ) return false;

        if ( $type === 'course' ) {
            // Verificar si tiene el curso (LearnDash)
            $course_id = $node['data']['course_id'] ?? 0;
            if ( function_exists('sfwd_lms_has_access') ) {
                return \sfwd_lms_has_access( $course_id, $user->ID );
            }
            return false;
        }

        if ( $type === 'student_tag' || $type === 'has_tag' ) {
            $tag_to_check = $node['data']['tag'] ?? ($node['data']['condition_value'] ?? '');
            $user_tags = \get_user_meta( $user->ID, 'alezux_tags', true ); 
            if ( empty($user_tags) ) return false;
            if ( ! is_array( $user_tags ) ) $user_tags = explode(',', $user_tags); // Soporte string
            return \in_array( $tag_to_check, $user_tags );
        }

        if ( $type === 'payment_status' ) {
            $status_check = $node['data']['status'] ?? $node['data']['condition_value'] ?? 'active';
            // Placeholder: En un sistema real chequearíamos la tabla de suscripciones
            // Por simplicidad, chequeamos usermeta
            $current_status = \get_user_meta( $user->ID, 'alezux_payment_status', true ) ?: 'active'; 
            return $current_status === $status_check;
        }

        // Caso genérico 'condition' nodo antiguo
        $value = $node['data']['condition_value'] ?? '';
        
        switch ( $type ) {
            case 'in_course': // Legacy condition type
                 if ( function_exists('sfwd_lms_has_access') ) {
                    return \sfwd_lms_has_access( intval($value), $user->ID );
                }
                return false; 

            case 'payment_status': // Legacy condition type
                $current_status = \get_user_meta( $user->ID, 'alezux_payment_status', true ) ?: 'active';
                return $current_status === $value;
        }

        return false;
    }

    private static function action_course( $node, $context ) {
        $user = $context['user'] ?? null;
        if ( !$user ) return;
        $course_id = $node['data']['course_id'] ?? 0;
        
        if ( ($node['data']['action'] ?? '') === 'enroll' && $course_id ) {
            if ( function_exists('ld_update_course_access') ) {
                \ld_update_course_access( $user->ID, $course_id, false ); // False = Add access
            }
        }
    }

    private static function action_student_tag( $node, $context ) {
        $user = $context['user'] ?? null;
        if ( !$user ) return;
        $tag = $node['data']['tag'] ?? '';
        if ( !$tag ) return;

        $current_tags = \get_user_meta( $user->ID, 'alezux_tags', true );
        if ( !is_array($current_tags) ) $current_tags = !empty($current_tags) ? explode(',', $current_tags) : [];

        $action = $node['data']['action'] ?? 'add';

        if ( $action === 'add' ) {
            if ( !in_array($tag, $current_tags) ) {
                $current_tags[] = $tag;
                \update_user_meta( $user->ID, 'alezux_tags', $current_tags );
            }
        } elseif ( $action === 'remove' ) {
            $current_tags = array_diff( $current_tags, [$tag] );
            \update_user_meta( $user->ID, 'alezux_tags', array_values($current_tags) );
        }
    }

    private static function action_send_email( $node, $context, $automation_id ) {
        global $wpdb;
        $table_queue = $wpdb->prefix . 'alezux_marketing_queue';
        $user = $context['user'] ?? null;
        if ( ! $user ) return;

        $node_data = $node['data'] ?? [];
        $subject = self::replace_variables( $node_data['subject'] ?? '', $context );
        $message = self::replace_variables( $node_data['content'] ?? '', $context );

        $wpdb->insert( $table_queue, [
            'automation_id' => $automation_id,
            'user_id'       => $user->ID,
            'email_to'      => $user->user_email,
            'subject'       => $subject,
            'message'       => $message,
            'from_name'     => \get_bloginfo('name'),
            'from_email'    => \get_bloginfo('admin_email'),
            'status'        => 'pending',
            'scheduled_at'  => \current_time('mysql')
        ] );
    }

    private static function replace_variables( $content, $data ) {
         $user      = $data['user'] ?? null;
         $plan_name = $data['plan_name'] ?? '';
         $amount    = $data['amount'] ?? '';
 
         $replacements = [
             '{{student_name}}'      => $user ? $user->display_name : '',
             '{{student_email}}'     => $user ? $user->user_email : '',
             '{{plan_name}}'         => $plan_name,
             '{{amount}}'            => $amount,
             '{{site_name}}'         => \get_bloginfo('name'),
         ];
 
         return \str_replace( \array_keys($replacements), \array_values($replacements), $content );
    }
}
