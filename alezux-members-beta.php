<?php
/**
 * Plugin Name: Alezux Members Beta
 * Description: Plugin modular "Lego-style" para funcionalidades robustas y escalables.
 * Version: 1.0.0
 * Author: Alezux
 * Text Domain: alezux-members
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Definir constantes del plugin
define( 'ALEZUX_MEMBERS_VERSION', '1.0.0' );
define( 'ALEZUX_MEMBERS_PATH', plugin_dir_path( __FILE__ ) );
define( 'ALEZUX_MEMBERS_URL', plugin_dir_url( __FILE__ ) );
define( 'ALEZUX_MEMBERS_MODULES_PATH', ALEZUX_MEMBERS_PATH . 'modules/' );

// Autoloader simple para clases del Core
spl_autoload_register( function ( $class ) {
	$prefix = 'Alezux_Members\\Core\\';
	$base_dir = ALEZUX_MEMBERS_PATH . 'core/';

	$len = strlen( $prefix );
	if ( strncmp( $prefix, $class, $len ) !== 0 ) {
		return;
	}

	$relative_class = substr( $class, $len );
	$file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

	if ( file_exists( $file ) ) {
		require $file;
	}
} );

// Inicializar el plugin
function alezux_members_init() {
	// Asegurarse de que el cargador existe antes de usarlo
	if ( class_exists( 'Alezux_Members\\Core\\Plugin_Loader' ) ) {
		$loader = new \Alezux_Members\Core\Plugin_Loader();
		$loader->run();
	}
}
add_action( 'plugins_loaded', 'alezux_members_init' );

/**
 * Encolar estilos globales del plugin.
 */
function alezux_members_enqueue_global_assets() {
	wp_enqueue_style( 
		'alezux-members-global', 
		ALEZUX_MEMBERS_URL . 'assets/css/global.css', 
		[], 
		ALEZUX_MEMBERS_VERSION 
	);
}
add_action( 'wp_enqueue_scripts', 'alezux_members_enqueue_global_assets' );
// Encolar también en el editor de Elementor para que se vea bien mientras se edita
add_action( 'elementor/frontend/after_enqueue_styles', 'alezux_members_enqueue_global_assets' );

