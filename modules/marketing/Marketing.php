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
		if ( ! defined( 'ALEZUX_MARKETING_PATH' ) ) {
			define( 'ALEZUX_MARKETING_PATH', plugin_dir_path( __FILE__ ) );
		}
		if ( ! defined( 'ALEZUX_MARKETING_URL' ) ) {
			define( 'ALEZUX_MARKETING_URL', plugin_dir_url( __FILE__ ) );
		}
	}

	private function init_hooks() {
        // Incluir componentes
        require_once ALEZUX_MARKETING_PATH . 'includes/Email_Sender.php';
        require_once ALEZUX_MARKETING_PATH . 'includes/Event_Listeners.php';

        // Inicializar Listeners
        \Alezux_Members\Modules\Marketing\Includes\Event_Listeners::init();
	}
}
