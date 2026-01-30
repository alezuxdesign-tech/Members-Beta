<?php
/**
 * Plugin Name: Alezux Members Beta
 * Description: Plugin modular "Lego-style" para funcionalidades robustas y escalables.
 * Version: 1.0.3
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
	// Inicializar el plugin
	if ( class_exists( 'Alezux_Members\\Core\\Plugin_Loader' ) ) {
		$loader = new \Alezux_Members\Core\Plugin_Loader();
		$loader->run();
	}

	// Inicializar Dashboard si estamos en admin
	if ( is_admin() && class_exists( 'Alezux_Members\\Core\\Admin_Dashboard' ) ) {
		$dashboard = new \Alezux_Members\Core\Admin_Dashboard();
		$dashboard->init();
	}
}
add_action( 'plugins_loaded', 'alezux_members_init' );

/**
 * Encolar estilos globales del plugin e inyectar variables dinámicas.
 */
function alezux_members_enqueue_global_assets() {
	wp_enqueue_style( 
		'alezux-members-global', 
		ALEZUX_MEMBERS_URL . 'assets/css/global.css', 
		[], 
		ALEZUX_MEMBERS_VERSION 
	);

	// Obtener colores personalizados
	$primary       = get_option( 'alezux_primary_color', '#6c5ce7' );
	$primary_hover = get_option( 'alezux_primary_hover', '#5649c0' );
	$bg_base       = get_option( 'alezux_bg_base', '#0f0f0f' );
	$bg_card       = get_option( 'alezux_bg_card', '#1a1a1a' );
	$border_radius = get_option( 'alezux_border_radius', '50px' );
	$border_color  = get_option( 'alezux_border_color', '#333333' );
	$box_shadow    = get_option( 'alezux_box_shadow', '0 10px 30px rgba(0, 0, 0, 0.3)' );

	// CSS Dinámico para sobrescribir variables
	$custom_css = "
		:root {
			--alezux-primary: {$primary};
			--alezux-primary-hover: {$primary_hover};
			--alezux-bg-base: {$bg_base};
			--alezux-bg-card: {$bg_card};
			--alezux-border-radius: {$border_radius};
			--alezux-border-color: {$border_color};
			--alezux-box-shadow: {$box_shadow};
		}
	";
	
	wp_add_inline_style( 'alezux-members-global', $custom_css );
}
add_action( 'wp_enqueue_scripts', 'alezux_members_enqueue_global_assets' );
// Encolar también en el editor de Elementor
add_action( 'elementor/frontend/after_enqueue_styles', 'alezux_members_enqueue_global_assets' );
// Inyectar en admin también para que el dashboard se vea bien
add_action( 'admin_enqueue_scripts', function() {
	// Solo inyectar las variables si el estilo global no está presente (aunque el dashboard.php lo encola)
	// Esta es una medida de seguridad para que el CSS dinámico esté disponible globalmente si se necesita
	if ( wp_style_is( 'alezux-members-global', 'enqueued' ) ) {
		$primary       = get_option( 'alezux_primary_color', '#6c5ce7' );
		$primary_hover = get_option( 'alezux_primary_hover', '#5649c0' );
		$bg_base       = get_option( 'alezux_bg_base', '#0f0f0f' );
		$bg_card       = get_option( 'alezux_bg_card', '#1a1a1a' );
		$border_radius = get_option( 'alezux_border_radius', '50px' );
		$border_color  = get_option( 'alezux_border_color', '#333333' );
		$box_shadow    = get_option( 'alezux_box_shadow', '0 10px 30px rgba(0, 0, 0, 0.3)' );
		
		$custom_css = ":root { 
			--alezux-primary: {$primary}; 
			--alezux-primary-hover: {$primary_hover}; 
			--alezux-bg-base: {$bg_base}; 
			--alezux-bg-card: {$bg_card}; 
			--alezux-border-radius: {$border_radius}; 
			--alezux-border-color: {$border_color}; 
			--alezux-box-shadow: {$box_shadow}; 
		}";
		wp_add_inline_style( 'alezux-members-global', $custom_css );
	}
});

/**
 * Registrar categoría personalizada en Elementor
 */
add_action( 'elementor/elements/categories_registered', function( $elements_manager ) {
	$elements_manager->add_category(
		'alezux-members',
		[
			'title' => esc_html__( 'Alezux Members', 'alezux-members' ),
			'icon'  => 'fa fa-plug',
		]
	);
} );


