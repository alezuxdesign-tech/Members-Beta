<?php
namespace Alezux_Members\Modules\Learning_Modules;

use Alezux_Members\Core\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Learning_Modules extends Module_Base {

	public function init() {
		// Cargar lógica del Custom Post Type
		require_once __DIR__ . '/includes/Post_Type_Module.php';
		$cpt = new Includes\Post_Type_Module();
		$cpt->register();

		// Cargar Assets
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );

		// Registrar Widgets de Elementor
		add_action( 'elementor/widgets/register', [ $this, 'register_elementor_widgets' ] );
	}

	public function enqueue_assets() {
		wp_enqueue_style( 
			'alezux-learning-modules-css', 
			$this->get_asset_url( 'assets/css/learning-modules.css' ), 
			[], 
			ALEZUX_MEMBERS_VERSION 
		);
	}

	public function register_elementor_widgets( $widgets_manager ) {
		require_once __DIR__ . '/widgets/Module_List_Widget.php';
		$widgets_manager->register( new \Alezux_Members\Modules\Learning_Modules\Widgets\Module_List_Widget() );
	}
}
