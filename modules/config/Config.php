<?php
namespace Alezux_Members\Modules\Config;

use Alezux_Members\Core\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Config extends Module_Base {

	public function init() {
		// Encolar estilos específicos del módulo
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		
		// Registrar Widgets de Elementor
		add_action( 'elementor/widgets/register', [ $this, 'register_elementor_widgets' ] );
	}

	public function enqueue_assets() {
		// CSS
		wp_enqueue_style( 
			'alezux-config-css', 
			$this->get_asset_url( 'assets/css/config.css' ), 
			[], 
			time() // Development mode: force reload
		);
	}

	public function register_elementor_widgets( $widgets_manager ) {
		require_once __DIR__ . '/widgets/Config_Widget.php';
		$widgets_manager->register( new \Alezux_Members\Modules\Config\Widgets\Config_Widget() );
	}
}
