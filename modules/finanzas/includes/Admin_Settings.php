<?php
namespace Alezux_Members\Modules\Finanzas\Includes;

if ( ! \defined( 'ABSPATH' ) ) {
	exit;
}

class Admin_Settings {

    public static function init() {
        \add_action( 'admin_menu', [ __CLASS__, 'add_settings_page' ] );
        \add_action( 'admin_init', [ __CLASS__, 'register_settings' ] );
    }

    public static function add_settings_page() {
        \add_submenu_page(
            'alezux-members', // Parent slug
            'Finanzas Config', // Page Title
            'Finanzas', // Menu Title
            'manage_options',
            'alezux-finanzas-settings',
            [ __CLASS__, 'render_page' ]
        );
    }

    public static function register_settings() {
        \register_setting( 'alezux_finanzas_group', 'alezux_stripe_public_key' );
        \register_setting( 'alezux_finanzas_group', 'alezux_stripe_secret_key' );
    }

    public static function render_page() {
        ?>
        <div class="wrap alezux-settings-wrap">
            <h1>Configuración de Finanzas & Stripe</h1>
            <form method="post" action="options.php">
                <?php \settings_fields( 'alezux_finanzas_group' ); ?>
                <?php \do_settings_sections( 'alezux_finanzas_group' ); ?>
                
                <div class="card" style="max-width: 600px; padding: 20px; margin-top: 20px;">
                    <h2>Credenciales de Stripe</h2>
                    <p class="description">Ingresa tus claves de API de Stripe (Modo Test o Live según corresponda).</p>
                    
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">Publishable Key</th>
                            <td>
                                <input type="text" name="alezux_stripe_public_key" value="<?php echo \esc_attr( \get_option('alezux_stripe_public_key') ); ?>" class="regular-text" />
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Secret Key</th>
                            <td>
                                <input type="password" name="alezux_stripe_secret_key" value="<?php echo \esc_attr( \get_option('alezux_stripe_secret_key') ); ?>" class="regular-text" />
                                <p class="description">Comienza con sk_test_... o sk_live_...</p>
                            </td>
                        </tr>
                    </table>
                </div>

                <?php \submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
