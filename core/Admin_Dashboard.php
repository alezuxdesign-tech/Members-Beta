<?php
namespace Alezux_Members\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Admin_Dashboard {

	public function init() {
		add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
		add_action( 'admin_post_alezux_save_settings', [ $this, 'save_settings' ] );
	}

	public function add_admin_menu() {
		add_menu_page(
			'Alezux Members',
			'Alezux Members',
			'manage_options',
			'alezux-members',
			[ $this, 'render_dashboard' ],
			ALEZUX_MEMBERS_URL . 'modules/demo-block/assets/css/img/LOGO.svg',
			2
		);
	}

	public function enqueue_admin_assets( $hook ) {
		// Solo cargar scripts en nuestra página
		if ( 'toplevel_page_alezux-members' !== $hook ) {
			return;
		}
		
		// Encolar estilos globales también en el admin para nuestra página
		wp_enqueue_style( 
			'alezux-members-global', 
			ALEZUX_MEMBERS_URL . 'assets/css/global.css', 
			[], 
			ALEZUX_MEMBERS_VERSION 
		);

		// Pequeño script para manejar tabs
		wp_add_inline_script( 'common', '
			document.addEventListener("DOMContentLoaded", function() {
				const tabs = document.querySelectorAll(".alezux-tab-link");
				const panels = document.querySelectorAll(".alezux-tab-panel");
				
				tabs.forEach(tab => {
					tab.addEventListener("click", function(e) {
						e.preventDefault();
						
						// Remove active class
						tabs.forEach(t => t.classList.remove("active"));
						panels.forEach(p => p.classList.remove("active"));
						panels.forEach(p => p.style.display = "none");
						
						// Add active class
						this.classList.add("active");
						const target = document.getElementById(this.dataset.target);
						target.classList.add("active");
						target.style.display = "block";
					});
				});
			});
		' );
	}

	public function render_dashboard() {
		// Obtener opciones guardadas
		$settings = [
			'primary_color' => get_option( 'alezux_primary_color', '#6c5ce7' ),
			'primary_hover' => get_option( 'alezux_primary_hover', '#5649c0' ),
			'bg_base'       => get_option( 'alezux_bg_base', '#0f0f0f' ),
			'bg_card'       => get_option( 'alezux_bg_card', '#1a1a1a' ),
			'border_radius' => get_option( 'alezux_border_radius', '50px' ),
			'border_color'  => get_option( 'alezux_border_color', '#333333' ),
			'box_shadow'    => get_option( 'alezux_box_shadow', '0 10px 30px rgba(0, 0, 0, 0.3)' ),
		];

		// Obtener shortcodes registrados desde Module_Base
		$shortcodes = Module_Base::get_registered_shortcodes();

		include ALEZUX_MEMBERS_PATH . 'views/admin/dashboard.php';
	}

	public function save_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'No tienes permisos.' );
		}

		check_admin_referer( 'alezux_save_settings_action', 'alezux_settings_nonce' );

		// Guardar colores y estilos
		if ( isset( $_POST['alezux_primary_color'] ) ) {
			update_option( 'alezux_primary_color', sanitize_hex_color( $_POST['alezux_primary_color'] ) );
		}
		if ( isset( $_POST['alezux_primary_hover'] ) ) {
			update_option( 'alezux_primary_hover', sanitize_hex_color( $_POST['alezux_primary_hover'] ) );
		}
		if ( isset( $_POST['alezux_bg_base'] ) ) {
			update_option( 'alezux_bg_base', sanitize_hex_color( $_POST['alezux_bg_base'] ) );
		}
		if ( isset( $_POST['alezux_bg_card'] ) ) {
			update_option( 'alezux_bg_card', sanitize_hex_color( $_POST['alezux_bg_card'] ) );
		}
		if ( isset( $_POST['alezux_border_radius'] ) ) {
			update_option( 'alezux_border_radius', sanitize_text_field( $_POST['alezux_border_radius'] ) );
		}
		if ( isset( $_POST['alezux_border_color'] ) ) {
			update_option( 'alezux_border_color', sanitize_hex_color( $_POST['alezux_border_color'] ) );
		}
		if ( isset( $_POST['alezux_box_shadow'] ) ) {
			update_option( 'alezux_box_shadow', sanitize_text_field( $_POST['alezux_box_shadow'] ) );
		}

		wp_redirect( admin_url( 'admin.php?page=alezux-members&status=success' ) );
		exit;
	}
}
