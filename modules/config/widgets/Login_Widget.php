<?php
namespace Alezux_Members\Modules\Config\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Login_Widget extends Widget_Base {

	public function get_name() {
		return 'alezux-login-form';
	}

	public function get_title() {
		return esc_html__( 'Login Form (Alezux)', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-lock-user';
	}

	public function get_categories() {
		return [ 'alezux-members' ];
	}

	public function get_style_depends() {
		return [ 'alezux-config-css' ];
	}

	public function get_script_depends() {
		return [ 'alezux-config-js' ];
	}

	protected function register_controls() {
		// --- CONTENT SECTION ---
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Contenido', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title',
			[
				'label' => esc_html__( 'Título', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Iniciar Sesión', 'alezux-members' ),
			]
		);

		$this->add_control(
			'user_placeholder',
			[
				'label' => esc_html__( 'Placeholder Usuario', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Usuario o Correo', 'alezux-members' ),
			]
		);

		$this->add_control(
			'pass_placeholder',
			[
				'label' => esc_html__( 'Placeholder Contraseña', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Contraseña', 'alezux-members' ),
			]
		);

		$this->add_control(
			'submit_text',
			[
				'label' => esc_html__( 'Texto Botón', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Entrar', 'alezux-members' ),
			]
		);

		$this->add_control(
			'show_remember',
			[
				'label' => esc_html__( 'Mostrar "Recordarme"', 'alezux-members' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_recover',
			[
				'label' => esc_html__( 'Link Recuperar Contraseña', 'alezux-members' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'recover_url',
			[
				'label' => esc_html__( 'URL Recuperar', 'alezux-members' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://tudominio.com/recuperar', 'alezux-members' ),
				'condition' => [
					'show_recover' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		// --- STYLE SECTION ---
		$this->start_controls_section(
			'section_style_container',
			[
				'label' => esc_html__( 'Contenedor', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'container_bg',
			[
				'label' => esc_html__( 'Fondo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-auth-form-card' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'container_border',
				'selector' => '{{WRAPPER}} .alezux-auth-form-card',
			]
		);

		$this->add_control(
			'container_radius',
			[
				'label' => esc_html__( 'Borde Redondeado', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-auth-form-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Botón Style
		$this->start_controls_section(
			'section_style_button',
			[
				'label' => esc_html__( 'Botón', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .alezux-auth-submit',
			]
		);

		$this->add_control(
			'button_bg',
			[
				'label' => esc_html__( 'Fondo Botón', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-auth-submit' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="alezux-auth-form-card">
			<?php if ( ! empty( $settings['title'] ) ) : ?>
				<h2 class="alezux-auth-title"><?php echo esc_html( $settings['title'] ); ?></h2>
			<?php endif; ?>

			<form id="alezux-login-form" class="alezux-auth-form">
				<div class="alezux-auth-field">
					<input type="text" name="username" placeholder="<?php echo esc_attr( $settings['user_placeholder'] ); ?>" required>
				</div>
				<div class="alezux-auth-field">
					<div class="alezux-input-wrapper">
						<input type="password" name="password" id="alezux-login-password" placeholder="<?php echo esc_attr( $settings['pass_placeholder'] ); ?>" required>
						<span class="alezux-toggle-password" id="toggle-password" role="button" aria-label="Mostrar/Ocultar contraseña">
							<!-- Eye Open -->
							<svg class="alezux-eye-icon eye-open" viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
								<path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
							</svg>
							<!-- Eye Closed -->
							<svg class="alezux-eye-icon eye-closed" viewBox="0 0 24 24" width="20" height="20" fill="currentColor" style="display:none;">
								<path d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.81l2.92 2.92c1.51-1.26 2.7-2.89 3.44-4.73-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z"/>
							</svg>
						</span>
					</div>
				</div>

				<?php if ( 'yes' === $settings['show_remember'] ) : ?>
					<div class="alezux-auth-extra">
						<label class="alezux-remember-me">
							<input type="checkbox" name="remember" id="rememberme"> 
							<span><?php esc_html_e( 'Recordarme', 'alezux-members' ); ?></span>
						</label>
					</div>
				<?php endif; ?>

				<button type="submit" class="alezux-auth-submit">
					<span class="alezux-btn-text"><?php echo esc_html( $settings['submit_text'] ); ?></span>
					<span class="alezux-loader" style="display: none;"></span>
				</button>

				<?php if ( 'yes' === $settings['show_recover'] ) : ?>
					<div class="alezux-auth-footer">
						<a href="<?php echo esc_url( $settings['recover_url']['url'] ); ?>" class="alezux-auth-link">
							<?php esc_html_e( '¿Olvidaste tu contraseña?', 'alezux-members' ); ?>
						</a>
					</div>
				<?php endif; ?>
			</form>
		</div>
		<?php
	}
}
