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
	}

    public function register_widgets( $widgets_manager ) {
        require_once ALEZUX_MARKETING_PATH . 'widgets/Marketing_Automation_Widget.php';
        $widgets_manager->register( new \Alezux_Members\Modules\Marketing\Widgets\Marketing_Automation_Widget() );
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
}
