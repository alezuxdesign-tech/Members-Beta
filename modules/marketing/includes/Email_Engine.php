<?php
namespace Alezux_Members\Modules\Marketing\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Email_Engine {

	public function __construct() {
		// Hook into phpmailer_init or similar if we needed deep SMTP control, 
		// but wp_mail filters are enough for From Name/Email.
		// add_filter( 'wp_mail_from', [ $this, 'custom_mail_from' ] ); // DISABLED: Causes blocking on some hosts if domain mismatches
		add_filter( 'wp_mail_from_name', [ $this, 'custom_mail_from_name' ] );
		add_filter( 'wp_mail_content_type', [ $this, 'set_html_content_type' ] );
	}

	public function get_registered_types() {
		return [
			'student_welcome' => 'Registro - Bienvenida',
			'user_recover_password' => 'Seguridad - Recuperar Contraseña',
			'admin_reset_password' => 'Seguridad - Reset por Admin',
			'payment_success' => 'Finanzas - Pago Exitoso',
			'payment_failed' => 'Finanzas - Pago Fallido',
			'payment_reminder' => 'Finanzas - Recordatorio Renovación',
			'subscription_cancelled' => 'Finanzas - Suscripción Cancelada',
			'achievement_assigned' => 'Logros - Nuevo Logro',
			'course_available' => 'Cursos - Nuevo Curso',
			'inactivity_alert' => 'Retención - Inactividad',
		];
	}

	public function custom_mail_from( $original_email ) {
		// Only override if coming from our system calls? Or global?
		// User requested custom branding "style wordpress invalid", so let's do global override 
		// but only if option is set.
		$custom = get_option( 'alezux_marketing_from_email' );
		if ( ! empty( $custom ) && is_email( $custom ) ) {
			return $custom;
		}
		return $original_email;
	}

	public function custom_mail_from_name( $original_name ) {
		$custom = get_option( 'alezux_marketing_from_name' );
		if ( ! empty( $custom ) ) {
			return $custom;
		}
		return $original_name;
	}
	
	public function set_html_content_type() {
		return 'text/html';
	}

	/**
	 * Main function to send emails
	 */
	public function send_email( $type, $recipient_email, $data = [] ) {
		if ( ! is_email( $recipient_email ) ) return false;

		// 1. Get Template (DB or Default)
		$template = $this->get_template( $type );

		// If not active, stop (unless it's a critical system email that MUST be sent? 
		// Strategy: If user disabled 'welcome', we respect it. But for 'recover password', disabling it breaks functionality.
		// Alezux Decision: We will send Default if Custom is disabled OR not exists, only stop if explicit "Do Not Send" logic exists.
		// User requirement "Configurar to enable/disable". So if disabled in DB, we DO NOT send.
		// Wait, for Critical emails (Password Reset), disabling is dangerous.
		// Let's assume Active = Use Custom Template. Inactive = Use System Default (WordPress default or Hardcoded fallback).
		// Re-reading user request: "Opcion de configurar... switch de Activo/Inactivo (para decidir si ese correo se envía o no)".
		// Okay, explicitly sending NOTHING.
		
		if ( $template && isset( $template->is_active ) && '0' == $template->is_active ) {
			return false; // Email disabled by admin
		}

		// 2. Prepare Content
		$subject = $template ? $template->subject : '';
		$content = $template ? $template->content : '';

		// Fallback if no custom template found in DB
		if ( ! $template ) {
			require_once __DIR__ . '/Default_Templates.php';
			$defaults = Default_Templates::get( $type );
			$subject = $defaults['subject'];
			$content = $defaults['content'];
		}

		// 3. Variable Replacement
		$vars = $this->prepare_variables( $data );
		
		$subject = $this->replace_vars( $subject, $vars );
		$content = $this->replace_vars( $content, $vars );

		// 4. Inject Logic (Header/Logo)
		// Headers: Add Reply-To if configured
		$headers = [];
		$from_email = get_option( 'alezux_marketing_from_email' );
		$from_name  = get_option( 'alezux_marketing_from_name' );

		if ( ! empty( $from_email ) && is_email( $from_email ) ) {
			$name_part = ! empty( $from_name ) ? $from_name : get_bloginfo( 'name' );
			$headers[] = "Reply-To: $name_part <$from_email>";
		}

		// 5. Send
		return wp_mail( $recipient_email, $subject, $content, $headers );
	}

	private function get_template( $type ) {
		global $wpdb;
		$table = $wpdb->prefix . 'alezux_marketing_templates';
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE type = %s", $type ) );
	}

	private function prepare_variables( $data ) {
		$vars = [];
		
		// Global
		$logo_url = get_option( 'alezux_marketing_logo_url' );
		$vars['{{logo_url}}'] = $logo_url ? $logo_url : '';
		$vars['{{site_name}}'] = get_bloginfo( 'name' );
		$vars['{{home_url}}'] = home_url();
		$vars['{{login_url}}'] = wp_login_url(); // Or custom
		$vars['{{year}}'] = date('Y');

		// User specific
		if ( isset( $data['user'] ) && $data['user'] instanceof \WP_User ) {
			$u = $data['user'];
			$vars['{{user.name}}'] = $u->display_name; // Or first_name
			$vars['{{user.first_name}}'] = $u->first_name;
			$vars['{{user.email}}'] = $u->user_email;
			$vars['{{user.username}}'] = $u->user_login;
		}

		// Merge extra data
		foreach ( $data as $key => $val ) {
			if ( is_string( $val ) || is_numeric( $val ) ) {
				$vars['{{' . $key . '}}'] = $val;
			}
		}

		return $vars;
	}

	private function replace_vars( $text, $vars ) {
		return str_replace( array_keys( $vars ), array_values( $vars ), $text );
	}
}
