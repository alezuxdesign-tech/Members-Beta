<?php
namespace Alezux_Members\Modules\Config\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Recover_Widget extends Widget_Base {

	public function get_name() {
		return 'alezux-recover-form';
	}

	public function get_title() {
		return esc_html__( 'Recover Password (Alezux)', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-email-field';
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
				'default' => esc_html__( 'Recuperar Contraseña', 'alezux-members' ),
			]
		);

		$this->add_control(
			'description',
			[
				'label' => esc_html__( 'Descripción', 'alezux-members' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Ingresa tu correo electrónico y te enviaremos instrucciones para restablecer tu contraseña.', 'alezux-members' ),
			]
		);

		$this->add_control(
			'placeholder',
			[
				'label' => esc_html__( 'Placeholder Correo', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Tu correo electrónico', 'alezux-members' ),
			]
		);

		$this->add_control(
			'submit_text',
			[
				'label' => esc_html__( 'Texto Botón', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Enviar Correo', 'alezux-members' ),
			]
		);

		$this->end_controls_section();

		// --- STYLE SECTION: CONTAINER ---
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

		$this->add_responsive_control(
			'container_padding',
			[
				'label' => esc_html__( 'Padding', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-auth-form-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'container_max_width',
			[
				'label' => esc_html__( 'Ancho Máximo', 'alezux-members' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [ 'min' => 200, 'max' => 1200 ],
				],
				'selectors' => [
					'{{WRAPPER}} .alezux-auth-form-card' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// --- STYLE SECTION: TITLE & DESC ---
		$this->start_controls_section(
			'section_style_header',
			[
				'label' => esc_html__( 'Título y Descripción', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_heading',
			[
				'label' => esc_html__( 'Título', 'alezux-members' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Color Título', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-auth-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .alezux-auth-title',
			]
		);

		$this->add_responsive_control(
			'title_margin_bottom',
			[
				'label' => esc_html__( 'Margen Inferior Título', 'alezux-members' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range' => [
					'px' => [ 'min' => 0, 'max' => 100 ],
				],
				'selectors' => [
					'{{WRAPPER}} .alezux-auth-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'desc_heading',
			[
				'label' => esc_html__( 'Descripción', 'alezux-members' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label' => esc_html__( 'Color Descripción', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-auth-desc' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'desc_typography',
				'selector' => '{{WRAPPER}} .alezux-auth-desc',
			]
		);

		$this->end_controls_section();

		// --- STYLE SECTION: FIELDS ---
		$this->start_controls_section(
			'section_style_fields',
			[
				'label' => esc_html__( 'Campos (Inputs)', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'field_typography',
				'selector' => '{{WRAPPER}} .alezux-auth-field input',
			]
		);

		$this->start_controls_tabs( 'tabs_field_style' );

		$this->start_controls_tab(
			'tab_field_normal',
			[
				'label' => esc_html__( 'Normal', 'alezux-members' ),
			]
		);

		$this->add_control(
			'field_bg',
			[
				'label' => esc_html__( 'Color de Fondo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-auth-field input' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'field_color',
			[
				'label' => esc_html__( 'Color de Texto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-auth-field input' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'field_placeholder_color',
			[
				'label' => esc_html__( 'Color Placeholder', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-auth-field input::placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'field_border',
				'selector' => '{{WRAPPER}} .alezux-auth-field input',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_field_focus',
			[
				'label' => esc_html__( 'Foco', 'alezux-members' ),
			]
		);

		$this->add_control(
			'field_bg_focus',
			[
				'label' => esc_html__( 'Color de Fondo (Foco)', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-auth-field input:focus' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'field_border_color_focus',
			[
				'label' => esc_html__( 'Color de Borde (Foco)', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-auth-field input:focus' => 'border-color: {{VALUE}} !important;',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'field_radius',
			[
				'label' => esc_html__( 'Borde Redondeado', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-auth-field input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'field_padding',
			[
				'label' => esc_html__( 'Padding', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-auth-field input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->end_controls_section();

		// --- STYLE SECTION: BUTTON ---
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

		$this->add_responsive_control(
			'button_align',
			[
				'label' => esc_html__( 'Posición', 'alezux-members' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => esc_html__( 'Izquierda', 'alezux-members' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Centro', 'alezux-members' ),
						'icon' => 'eicon-text-align-center',
					],
					'flex-end' => [
						'title' => esc_html__( 'Derecha', 'alezux-members' ),
						'icon' => 'eicon-text-align-right',
					],
					'stretch' => [
						'title' => esc_html__( 'Justificado', 'alezux-members' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'default' => 'stretch',
				'selectors' => [
					'{{WRAPPER}} .alezux-auth-submit' => 'align-self: {{VALUE}}; width: {{VALUE === "stretch" ? "100%" : "auto"}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'label'    => esc_html__( 'Tipografía', 'alezux-members' ),
				'selector' => '{{WRAPPER}} .alezux-auth-submit',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'button_text_shadow',
				'selector' => '{{WRAPPER}} .alezux-auth-submit',
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__( 'Normal', 'alezux-members' ),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label'     => esc_html__( 'Color de texto', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-auth-submit' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'button_background',
				'label'    => esc_html__( 'Color', 'alezux-members' ),
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .alezux-auth-submit',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .alezux-auth-submit',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__( 'Al pasar el cursor', 'alezux-members' ),
			]
		);

		$this->add_control(
			'button_text_color_hover',
			[
				'label'     => esc_html__( 'Color de texto', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-auth-submit:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'button_background_hover',
				'label'    => esc_html__( 'Color', 'alezux-members' ),
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .alezux-auth-submit:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow_hover',
				'selector' => '{{WRAPPER}} .alezux-auth-submit:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'button_border',
				'selector'  => '{{WRAPPER}} .alezux-auth-submit',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'      => esc_html__( 'Radio del borde', 'alezux-members' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .alezux-auth-submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => esc_html__( 'Relleno', 'alezux-members' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .alezux-auth-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label' => esc_html__( 'Padding', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-auth-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

			<?php if ( ! empty( $settings['description'] ) ) : ?>
				<p class="alezux-auth-desc"><?php echo esc_html( $settings['description'] ); ?></p>
			<?php endif; ?>

			<form id="alezux-recover-form" class="alezux-auth-form">
				<div class="alezux-auth-field">
					<input type="text" name="user_login" placeholder="<?php echo esc_attr( $settings['placeholder'] ); ?>" required>
				</div>

				<button type="submit" class="alezux-auth-submit">
					<span class="alezux-btn-text"><?php echo esc_html( $settings['submit_text'] ); ?></span>
					<span class="alezux-loader" style="display: none;"></span>
				</button>
			</form>
		</div>
		<?php
	}
}
