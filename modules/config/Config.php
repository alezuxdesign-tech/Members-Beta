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

		// Redirección de Login Personalizado
		add_action( 'init', [ $this, 'redirect_to_custom_login' ] );

		// AJAX Auth Actions
		add_action( 'wp_ajax_nopriv_alezux_ajax_login', [ $this, 'handle_ajax_login' ] );
		add_action( 'wp_ajax_nopriv_alezux_ajax_recover', [ $this, 'handle_ajax_recover' ] );

		// Shortcode de Alertas
		$this->register_shortcode( 'alezux_login_alerts', [ $this, 'render_login_alerts' ], 'Muestra mensajes de error/éxito en el login.' );
	}

	public function enqueue_assets() {
		// CSS
		wp_enqueue_style( 
			'alezux-config-css', 
			$this->get_asset_url( 'assets/css/config.css' ), 
			[], 
			ALEZUX_MEMBERS_VERSION 
		);

		// JS
		wp_enqueue_script(
			'alezux-config-js',
			$this->get_asset_url( 'assets/js/config.js' ),
			[ 'jquery' ],
			ALEZUX_MEMBERS_VERSION,
			true
		);

		// Localizar script para AJAX
		wp_localize_script( 'alezux-config-js', 'alezux_auth_obj', [
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'alezux-auth-nonce' ),
			'home_url' => home_url()
		]);
	}

	public function register_elementor_widgets( $widgets_manager ) {
		require_once __DIR__ . '/widgets/Config_Widget.php';
		require_once __DIR__ . '/widgets/Login_Widget.php';
		require_once __DIR__ . '/widgets/Recover_Widget.php';

		$widgets_manager->register( new \Alezux_Members\Modules\Config\Widgets\Config_Widget() );
		$widgets_manager->register( new \Alezux_Members\Modules\Config\Widgets\Login_Widget() );
		$widgets_manager->register( new \Alezux_Members\Modules\Config\Widgets\Recover_Widget() );
	}

	/**
	 * Redirigir /wp-login.php a la página personalizada si está configurada
	 */
	public function redirect_to_custom_login() {
		global $pagenow;
		if ( 'wp-login.php' == $pagenow && ! isset( $_REQUEST['action'] ) && ! isset( $_REQUEST['key'] ) ) {
			$login_page_id = get_option( 'alezux_login_page_id' );
			if ( $login_page_id ) {
				wp_redirect( get_permalink( $login_page_id ) );
				exit;
			}
		}
	}

	/**
	 * Manejar Login por AJAX
	 */
	public function handle_ajax_login() {
		check_ajax_referer( 'alezux-auth-nonce', 'nonce' );

		$info = [];
		$info['user_login']    = sanitize_user( $_POST['username'] );
		$info['user_password'] = $_POST['password'];
		$info['remember']      = isset( $_POST['remember'] ) ? true : false;

		$user_signon = wp_signon( $info, is_ssl() );

		if ( is_wp_error( $user_signon ) ) {
			wp_send_json_error( [ 'message' => 'Usuario o contraseña incorrectos.' ] );
		} else {
			wp_send_json_success( [ 'redirect' => home_url() ] );
		}
	}

	/**
	 * Manejar Recuperación de Contraseña por AJAX
	 */
	public function handle_ajax_recover() {
		check_ajax_referer( 'alezux-auth-nonce', 'nonce' );

		$user_login = sanitize_text_field( $_POST['user_login'] );
		
		if ( empty( $user_login ) ) {
			wp_send_json_error( [ 'message' => 'Por favor ingresa tu correo o usuario.' ] );
		}

		$user_data = get_user_by( 'email', $user_login );
		if ( ! $user_data ) {
			$user_data = get_user_by( 'login', $user_login );
		}

		if ( ! $user_data ) {
			wp_send_json_error( [ 'message' => 'No existe ningún usuario con ese correo o nombre.' ] );
		}

		$user_id = $user_data->ID;
		$key = get_password_reset_key( $user_data );
		
		if ( is_wp_error( $key ) ) {
			wp_send_json_error( [ 'message' => 'No se pudo generar la clave de recuperación.' ] );
		}

		// Enviar el correo nativo de WP o personalizarlo
		// Por ahora usaremos la lógica nativa simplificada para asegurar entrega
		$message = "Alguien ha solicitado restablecer la contraseña de la siguiente cuenta:\r\n\r\n";
		$message .= network_home_url( '/' ) . "\r\n\r\n";
		$message .= sprintf( 'Nombre de usuario: %s', $user_data->user_login ) . "\r\n\r\n";
		$message .= "Si ha sido un error, ignora este correo.\r\n\r\n";
		$message .= "Para restablecer la contraseña, visita la siguiente dirección:\r\n\r\n";
		$message .= network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_data->user_login ), 'login' ) . "\r\n";

		if ( false !== wp_mail( $user_data->user_email, 'Recuperación de Contraseña', $message ) ) {
			wp_send_json_success( [ 'message' => 'Se ha enviado un correo con instrucciones.' ] );
		} else {
			wp_send_json_error( [ 'message' => 'El correo no pudo ser enviado.' ] );
		}
	}

	/**
	 * Renderizar alertas de login (Shortcode)
	 */
	public function render_login_alerts() {
		if ( isset( $_GET['login'] ) && 'failed' === $_GET['login'] ) {
			return '<div class="alezux-auth-alert error">Credenciales incorrectas. Inténtalo de nuevo.</div>';
		}
		return '';
	}
}
