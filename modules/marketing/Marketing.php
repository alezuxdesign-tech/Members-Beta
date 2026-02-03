<?php
namespace Alezux_Members\Modules\Marketing;

use Alezux_Members\Core\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Marketing extends Module_Base {

	public function init() {
		$this->define_constants();
		$this->init_hooks();
	}

	private function define_constants() {
		if ( ! \defined( 'ALEZUX_MARKETING_PATH' ) ) {
			\define( 'ALEZUX_MARKETING_PATH', \plugin_dir_path( __FILE__ ) );
		}
		if ( ! \defined( 'ALEZUX_MARKETING_URL' ) ) {
			\define( 'ALEZUX_MARKETING_URL', \plugin_dir_url( __FILE__ ) );
		}
	}

    private function init_hooks() {
        \add_action( 'admin_enqueue_scripts', [ $this, 'register_assets' ] );
        \add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );

        // Incluir componentes
        require_once ALEZUX_MARKETING_PATH . 'includes/Database_Installer.php';
        require_once ALEZUX_MARKETING_PATH . 'includes/Automation_Engine.php';
        require_once ALEZUX_MARKETING_PATH . 'includes/Queue_Runner.php';
        require_once ALEZUX_MARKETING_PATH . 'includes/Email_Sender.php';
        require_once ALEZUX_MARKETING_PATH . 'includes/Event_Listeners.php';

        // Ejecutar actualizaciones de DB
        \add_action( 'admin_init', [ \Alezux_Members\Modules\Marketing\Includes\Database_Installer::class, 'check_updates' ] );

        // Registrar Cron para la cola (cada 5 min)
        if ( ! \wp_next_scheduled( 'alezux_marketing_process_queue' ) ) {
            \wp_schedule_event( \time(), 'five_minutes', 'alezux_marketing_process_queue' );
        }
        \add_action( 'alezux_marketing_process_queue', [ \Alezux_Members\Modules\Marketing\Includes\Queue_Runner::class, 'process_queue' ] );

        // Manejar Tracking de Email
        \add_action( 'init', [ $this, 'handle_email_tracking' ] );

        // Inicializar Listeners
        \Alezux_Members\Modules\Marketing\Includes\Event_Listeners::init();

        // Registrar Widget de Elementor
        \add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );

        // AJAX Handlers
        \add_action( 'wp_ajax_alezux_save_automation', [ $this, 'ajax_save_automation' ] );
        \add_action( 'wp_ajax_alezux_load_automation', [ $this, 'ajax_load_automation' ] );
        \add_action( 'wp_ajax_alezux_get_automations_list', [ $this, 'ajax_get_automations_list' ] );
        \add_action( 'wp_ajax_alezux_delete_automation', [ $this, 'ajax_delete_automation' ] );
	}

    public function register_widgets( $widgets_manager ) {
        require_once ALEZUX_MARKETING_PATH . 'widgets/Marketing_Automation_Widget.php';
        $widgets_manager->register( new \Alezux_Members\Modules\Marketing\Widgets\Marketing_Automation_Widget() );
    }

    public function register_assets() {
        $ver = \time(); // Cache busting para desarrollo
        \wp_register_script( 'alezux-marketing-js', ALEZUX_MARKETING_URL . 'assets/js/marketing-automation.js', [ 'jquery' ], $ver, true );
        \wp_register_style( 'alezux-marketing-css', ALEZUX_MARKETING_URL . 'assets/css/marketing-automation.css', [], $ver );

        \wp_localize_script( 'alezux-marketing-js', 'alezux_marketing_vars', [
            'ajax_url' => \admin_url( 'admin-ajax.php' ),
            'nonce'    => \wp_create_nonce( 'alezux_marketing_nonce' )
        ] );
    }

    /**
     * Procesa la apertura de un email detectada por el píxel transparente.
     */
    public function handle_email_tracking() {
        if ( isset( $_GET['alezux_action'] ) && $_GET['alezux_action'] === 'track_email' && isset( $_GET['qid'] ) ) {
            $queue_id = \absint( $_GET['qid'] );
            
            global $wpdb;
            $table_stats = $wpdb->prefix . 'alezux_marketing_stats';

            // Marcar como abierto si no lo estaba
            $wpdb->update( $table_stats, 
                [ 
                    'opened'    => 1, 
                    'opened_at' => \current_time('mysql'),
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? ''
                ], 
                [ 'queue_id' => $queue_id, 'opened' => 0 ] 
            );

            // Servir imagen 1x1 transparente
            \header('Content-Type: image/png');
            echo \base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=');
            exit;
        }
    }

    /**
     * AJAX: Guarda una automatización (Nueva o Actualización)
     */
    public function ajax_save_automation() {
        \check_ajax_referer( 'alezux_marketing_nonce', 'nonce' );
        
        if ( ! \current_user_can( 'manage_options' ) ) {
            \wp_send_json_error( [ 'message' => 'Sin permisos.' ] );
        }

        $id        = isset( $_POST['id'] ) ? \intval( $_POST['id'] ) : 0;
        $name      = isset( $_POST['name'] ) ? \sanitize_text_field( $_POST['name'] ) : 'Sin Nombre';
        $blueprint = isset( $_POST['blueprint'] ) ? $_POST['blueprint'] : ''; // JSON raw
        
        // Extraer el trigger del blueprint para indexar
        $blueprint_data = \json_decode( $blueprint, true );
        $trigger = '';
        if ( isset( $blueprint_data['nodes'] ) ) {
            foreach ( $blueprint_data['nodes'] as $node ) {
                if ( $node['type'] === 'trigger' && isset( $node['data']['event'] ) ) {
                    $trigger = $node['data']['event'];
                    break;
                }
            }
        }

        global $wpdb;
        $table = $wpdb->prefix . 'alezux_marketing_automations';

        if ( $id > 0 ) {
            $wpdb->update( $table, 
                [ 'name' => $name, 'blueprint' => $blueprint, 'event_trigger' => $trigger ], 
                [ 'id' => $id ] 
            );
            $new_id = $id;
        } else {
            $wpdb->insert( $table, [ 
                'name' => $name, 
                'blueprint' => $blueprint, 
                'event_trigger' => $trigger,
                'status' => 'active'
            ] );
            $new_id = $wpdb->insert_id;
        }

        \wp_send_json_success( [ 'id' => $new_id, 'message' => 'Automatización guardada.' ] );
    }

    /**
     * AJAX: Carga una automatización por ID
     */
    public function ajax_load_automation() {
        \check_ajax_referer( 'alezux_marketing_nonce', 'nonce' );
        
        $id = isset( $_POST['id'] ) ? \intval( $_POST['id'] ) : 0;
        if ( ! $id ) \wp_send_json_error( [ 'message' => 'ID inválido.' ] );

        global $wpdb;
        $table = $wpdb->prefix . 'alezux_marketing_automations';
        $automation = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $id ) );

        if ( ! $automation ) \wp_send_json_error( [ 'message' => 'No encontrada.' ] );

        \wp_send_json_success( [
            'id'        => $automation->id,
            'name'      => $automation->name,
            'blueprint' => \json_decode( $automation->blueprint )
        ] );
    }

    /**
     * AJAX: Obtiene la lista completa de automatizaciones para la tabla
     */
    public function ajax_get_automations_list() {
        \check_ajax_referer( 'alezux_marketing_nonce', 'nonce' );
        
        global $wpdb;
        $table = $wpdb->prefix . 'alezux_marketing_automations';
        $results = $wpdb->get_results( "SELECT id, name, blueprint, created_at FROM $table ORDER BY created_at DESC" );

        \wp_send_json_success( $results );
    }

    /**
     * AJAX: Elimina una automatización
     */
    public function ajax_delete_automation() {
        \check_ajax_referer( 'alezux_marketing_nonce', 'nonce' );
        
        if ( ! \current_user_can( 'manage_options' ) ) {
            \wp_send_json_error( 'Sin permisos.' );
        }

        $id = isset( $_POST['id'] ) ? \intval( $_POST['id'] ) : 0;
        if ( ! $id ) \wp_send_json_error( 'ID inválido.' );

        global $wpdb;
        $table = $wpdb->prefix . 'alezux_marketing_automations';
        $wpdb->delete( $table, [ 'id' => $id ] );

        \wp_send_json_success( 'Automatización eliminada.' );
    }
}
