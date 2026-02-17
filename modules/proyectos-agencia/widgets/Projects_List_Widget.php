<?php
namespace Alezux_Members\Modules\Proyectos_Agencia\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Alezux_Members\Modules\Proyectos_Agencia\Includes\Project_Manager;

use function \esc_html__;
use function \esc_html;
use function \esc_attr;
use function \esc_url;
use function \date_i18n;
use function \get_option;
use function \current_user_can;
use function \get_users;
use function \get_userdata;
use function \get_avatar;
use function \is_admin;
use function \add_action;
use function \check_ajax_referer;
use function \wp_send_json_error;
use function \wp_send_json_success;
use function \absint;
use const \ABSPATH;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Projects_List_Widget extends Widget_Base {

	public function get_name() {
		return 'alezux_projects_list';
	}

	public function get_title() {
		return esc_html__( 'Lista de Proyectos (Admin)', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-table';
	}

	public function get_categories() {
		return [ 'alezux-otros' ]; // O una categoría específica si se crea
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Configuración', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'detail_page_url',
			[
				'label' => esc_html__( 'URL Página Detalle', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => 'https://tudominio.com/detalle-proyecto',
				'description' => 'URL de la página donde está el widget de detalle. Se le añadirá ?project_id=123',
			]
		);

		$this->end_controls_section();

		// --- STYLE: CONTAINER ---
		$this->start_controls_section(
			'style_container_section',
			[
				'label' => esc_html__( 'Contenedor Principal', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'container_bg_color',
			[
				'label' => esc_html__( 'Color de Fondo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-projects-app' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'container_border',
				'selector' => '{{WRAPPER}} .alezux-projects-app',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'container_shadow',
				'selector' => '{{WRAPPER}} .alezux-projects-app',
			]
		);

		$this->add_responsive_control(
			'container_padding',
			[
				'label' => esc_html__( 'Relleno (Padding)', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-projects-app' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'container_radius',
			[
				'label' => esc_html__( 'Radio del Borde', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-projects-app' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// --- STYLE: HEADER ---
		$this->start_controls_section(
			'style_header_section',
			[
				'label' => esc_html__( 'Cabecera (Header)', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Color Título', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-table-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .alezux-table-title',
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label' => esc_html__( 'Color Descripción', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-table-desc' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'desc_typography',
				'selector' => '{{WRAPPER}} .alezux-table-desc',
			]
		);

		$this->end_controls_section();

		// --- STYLE: CARDS ---
		$this->start_controls_section(
			'style_cards_section',
			[
				'label' => esc_html__( 'Tarjetas de Proyecto', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'card_bg_color',
			[
				'label' => esc_html__( 'Fondo Tarjeta', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-project-card' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'card_border',
				'selector' => '{{WRAPPER}} .alezux-project-card',
			]
		);

		$this->add_control(
			'card_title_color',
			[
				'label' => esc_html__( 'Color Nombre Proyecto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .project-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// --- STYLE: CARD INTERNALS ---
		$this->start_controls_section(
			'style_card_internals',
			[
				'label' => esc_html__( 'Elementos de la Tarjeta', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_status',
			[
				'label' => esc_html__( 'Etiqueta de Estado', 'alezux-members' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'status_typography',
				'selector' => '{{WRAPPER}} .alezux-status-badge',
			]
		);

		$this->add_control(
			'status_border_radius',
			[
				'label' => esc_html__( 'Radio Borde Estado', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-status-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'heading_client',
			[
				'label' => esc_html__( 'Información del Cliente', 'alezux-members' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'client_name_color',
			[
				'label' => esc_html__( 'Color Nombre Cliente', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .client-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'client_name_typography',
				'selector' => '{{WRAPPER}} .client-name',
			]
		);

		$this->add_control(
			'client_role_color',
			[
				'label' => esc_html__( 'Color Rol Cliente', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .client-role' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'heading_progress',
			[
				'label' => esc_html__( 'Barra de Progreso', 'alezux-members' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'progress_bg_color',
			[
				'label' => esc_html__( 'Fondo Barra', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .progress-bar' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'progress_fill_color',
			[
				'label' => esc_html__( 'Color Relleno', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .progress-fill' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'progress_height',
			[
				'label' => esc_html__( 'Altura Barra', 'alezux-members' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 2,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .progress-bar' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// --- STYLE: SIDE PANEL (general) ---
		$this->start_controls_section(
			'style_side_panel',
			[
				'label' => esc_html__( 'Panel Lateral (General)', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'panel_bg_color',
			[
				'label' => esc_html__( 'Fondo Panel', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-offcanvas-panel' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'panel_width',
			[
				'label' => esc_html__( 'Ancho Panel', 'alezux-members' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 300,
						'max' => 1000,
					],
					'%' => [
						'min' => 20,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .alezux-offcanvas-panel' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'panel_header_bg',
			[
				'label' => esc_html__( 'Fondo Cabecera Panel', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .offcanvas-header' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'panel_title_color',
			[
				'label' => esc_html__( 'Color Título Panel', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .offcanvas-header h3' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'panel_title_typography',
				'selector' => '{{WRAPPER}} .offcanvas-header h3',
			]
		);

		$this->end_controls_section();

		// --- STYLE: SIDE PANEL TABS ---
		$this->start_controls_section(
			'style_panel_tabs',
			[
				'label' => esc_html__( 'Panel: Pestañas (Tabs)', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'tabs_bg_color',
			[
				'label' => esc_html__( 'Fondo Contenedor Tabs', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-tabs-header' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_style_tabs' );

		// Tab Normal
		$this->start_controls_tab(
			'tab_style_normal',
			[
				'label' => esc_html__( 'Normal', 'alezux-members' ),
			]
		);

		$this->add_control(
			'tab_text_color',
			[
				'label' => esc_html__( 'Color Texto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tab-btn' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_bg_color',
			[
				'label' => esc_html__( 'Fondo Tab', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tab-btn' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		// Tab Active
		$this->start_controls_tab(
			'tab_style_active',
			[
				'label' => esc_html__( 'Activo / Hover', 'alezux-members' ),
			]
		);

		$this->add_control(
			'tab_text_color_active',
			[
				'label' => esc_html__( 'Color Texto Activo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tab-btn.active, {{WRAPPER}} .tab-btn:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_bg_color_active',
			[
				'label' => esc_html__( 'Fondo Tab Activo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tab-btn.active, {{WRAPPER}} .tab-btn:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_border_color_active',
			[
				'label' => esc_html__( 'Color Borde Inferior', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tab-btn.active' => 'border-bottom-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'tab_typography',
				'selector' => '{{WRAPPER}} .tab-btn',
			]
		);

		$this->end_controls_section();

		// --- STYLE: PANEL CLIENT INFO ---
		$this->start_controls_section(
			'style_panel_client',
			[
				'label' => esc_html__( 'Panel: Info Cliente', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_client_section',
			[
				'label' => esc_html__( 'Sección Títulos', 'alezux-members' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'panel_section_title_color',
			[
				'label' => esc_html__( 'Color Títulos Sección', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .panel-section-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'panel_section_title_typography',
				'selector' => '{{WRAPPER}} .panel-section-title',
			]
		);

		$this->add_control(
			'heading_client_data',
			[
				'label' => esc_html__( 'Datos Cliente', 'alezux-members' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'panel_client_name_color',
			[
				'label' => esc_html__( 'Color Nombre', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .client-mini-profile span.d-block' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'panel_client_name_typo',
				'selector' => '{{WRAPPER}} .client-mini-profile span.d-block',
			]
		);

		$this->add_control(
			'panel_client_email_color',
			[
				'label' => esc_html__( 'Color Email', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .client-mini-profile small' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'panel_avatar_size',
			[
				'label' => esc_html__( 'Tamaño Avatar', 'alezux-members' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .client-mini-profile img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'panel_avatar_radius',
			[
				'label' => esc_html__( 'Radio Avatar', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .client-mini-profile img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'heading_date_info',
			[
				'label' => esc_html__( 'Fecha Inicio', 'alezux-members' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'panel_date_color',
			[
				'label' => esc_html__( 'Color Texto Fecha', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .detail-item p' => 'color: {{VALUE}};',
					'{{WRAPPER}} .detail-item i' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// --- STYLE: PANEL FORMS & BRIEFING ---
		$this->start_controls_section(
			'style_panel_forms',
			[
				'label' => esc_html__( 'Panel: Formularios y Datos', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_forms',
			[
				'label' => esc_html__( 'Inputs y Selects', 'alezux-members' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'form_label_color',
			[
				'label' => esc_html__( 'Color Etiquetas (Labels)', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-offcanvas-panel label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_bg_color',
			[
				'label' => esc_html__( 'Fondo Input', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-input' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_text_color',
			[
				'label' => esc_html__( 'Color Texto Input', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-input' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_border_color',
			[
				'label' => esc_html__( 'Color Borde Input', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-input' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'heading_briefing_box',
			[
				'label' => esc_html__( 'Caja de Datos (Briefing/Credenciales)', 'alezux-members' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'info_box_bg',
			[
				'label' => esc_html__( 'Fondo Caja Info', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-info-box' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'info_box_text_color',
			[
				'label' => esc_html__( 'Color Texto Info', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-info-box p' => 'color: {{VALUE}};',
					'{{WRAPPER}} .alezux-info-box h5' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'info_box_radius',
			[
				'label' => esc_html__( 'Radio Borde Caja', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-info-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// --- STYLE: PANEL CHAT ---
		$this->start_controls_section(
			'style_panel_chat',
			[
				'label' => esc_html__( 'Panel: Chat', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'chat_area_bg',
			[
				'label' => esc_html__( 'Fondo Área Mensajes', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .chat-messages-list' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'chat_input_container_bg',
			[
				'label' => esc_html__( 'Fondo Área Input', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .chat-input-area' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'chat_textarea_bg',
			[
				'label' => esc_html__( 'Fondo Campo Texto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #chat-message-input' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'chat_textarea_color',
			[
				'label' => esc_html__( 'Color Texto Campo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #chat-message-input' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'chat_send_btn_bg',
			[
				'label' => esc_html__( 'Fondo Botón Enviar', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #btn-send-chat' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'chat_send_btn_color',
			[
				'label' => esc_html__( 'Icono Botón Enviar', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #btn-send-chat' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// --- STYLE: BUTTONS ---
		$this->start_controls_section(
			'style_buttons',
			[
				'label' => esc_html__( 'Botones', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .alezux-marketing-btn',
			]
		);

		$this->start_controls_tabs( 'tabs_buttons' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__( 'Normal', 'alezux-members' ),
			]
		);

		$this->add_control(
			'button_bg_color',
			[
				'label' => esc_html__( 'Fondo Botón', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-marketing-btn.primary' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => esc_html__( 'Texto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-marketing-btn' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__( 'Al Pasar Cursor', 'alezux-members' ),
			]
		);

		$this->add_control(
			'button_bg_color_hover',
			[
				'label' => esc_html__( 'Fondo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-marketing-btn.primary:hover' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_text_color_hover',
			[
				'label' => esc_html__( 'Texto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-marketing-btn:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'button_border_radius',
			[
				'label' => esc_html__( 'Radio del Borde', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-marketing-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			echo '<div class="alezux-alert error">Acceso restringido a administradores.</div>';
			return;
		}

		$settings = $this->get_settings_for_display();
		$detail_url = ! empty( $settings['detail_page_url'] ) ? $settings['detail_page_url'] : '#';

		$manager = new Project_Manager();
		$projects = $manager->get_all_projects();
		
		// Obtener usuarios para el select del modal
		$users = get_users( [ 'role__in' => [ 'subscriber', 'customer', 'administrator' ], 'number' => 100 ] );
		?>
		
		<!-- Usamos las clases globales de Finanzas/App para consistencia visual -->
		<div class="alezux-finanzas-app alezux-projects-app">
			
			<!-- Cabecera Estándar -->
			<div class="alezux-table-header">
				<div class="alezux-header-left">
					<h3 class="alezux-table-title">Gestión de Proyectos</h3>
					<p class="alezux-table-desc">Administra los proyectos de desarrollo web de tus clientes.</p>
				</div>

				<div class="alezux-header-right alezux-filters-inline">
					<div class="alezux-filter-item">
						<button id="open-new-project-modal" class="alezux-marketing-btn primary">
							<i class="eicon-plus"></i> Nuevo Proyecto
						</button>
					</div>
				</div>
			</div>

			<!-- Grid Container -->
			<div class="alezux-projects-grid">
				<?php if ( empty( $projects ) ) : ?>
					<div class="alezux-empty-state">
						<i class="eicon-folder-o"></i>
						<h3>No hay proyectos aún</h3>
						<p>Comienza creando el primero para gestionar tus desarrollos.</p>
					</div>
				<?php else : ?>
					<?php foreach ( $projects as $project ) : ?>
						<?php 
						$user_info = get_userdata( $project->customer_id );
						$user_name = $user_info ? $user_info->display_name : 'Usuario Desconocido';
						$user_email = $user_info ? $user_info->user_email : '';
						
						// Calcular progreso basado en la fase (Simplificado)
						$progress = 0;
						switch($project->current_step) {
							case 'briefing': $progress = 10; break;
							case 'design_review': $progress = 40; break;
							case 'in_progress': $progress = 70; break;
							case 'completed': $progress = 100; break;
						}
						?>
						// Obtener fechas de inicio y fin
						$start_date_meta = $manager->get_project_meta( $project->id, 'project_start_date' );
						$end_date_meta   = $manager->get_project_meta( $project->id, 'project_end_date' );

						$start_display = ! empty( $start_date_meta ) ? date_i18n( 'd M', strtotime( $start_date_meta ) ) : '-';
						$end_display   = ! empty( $end_date_meta ) ? date_i18n( 'd M, Y', strtotime( $end_date_meta ) ) : '-';
						?>
						
						<div class="alezux-project-card" onclick="AlezuxProjects.openPanel(<?php echo esc_attr($project->id); ?>)">
							<div class="card-header">
								<span class="alezux-status-badge status-<?php echo esc_attr( $project->status ); ?>">
									<?php echo esc_html( ucfirst( $project->status ) ); ?>
								</span>
								<button class="card-action-btn" title="Opciones"><i class="eicon-ellipsis-h"></i></button>
							</div>
							
							<div class="card-body">
								<h4 class="project-name"><?php echo esc_html( $project->name ); ?></h4>
								
								<div class="client-info">
									<?php echo get_avatar( $project->customer_id, 32, '', '', ['class' => 'client-avatar'] ); ?>
									<div class="client-details">
										<span class="client-name"><?php echo esc_html( $user_name ); ?></span>
										<span class="client-role">Cliente</span>
									</div>
								</div>
							</div>

							<div class="card-footer">
								<div class="progress-section">
									<div class="progress-labels">
										<span>Progreso</span>
										<span><?php echo $progress; ?>%</span>
									</div>
									<div class="progress-bar">
										<div class="progress-fill" style="width: <?php echo $progress; ?>%;"></div>
									</div>
								</div>
								
								<div class="card-dates" style="display: flex; justify-content: space-between; font-size: 11px; margin-top: 10px; color: #718096;">
									<span title="Fecha Inicio"><i class="eicon-calendar"></i> <?php echo $start_display; ?></span>
									<span title="Fecha Fin"><i class="eicon-flag"></i> <?php echo $end_display; ?></span>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>

			<!-- MODAL NUEVO PROYECTO (Estilo Marketing) -->
			<div id="new-project-modal" class="alezux-modal">
				<div class="alezux-modal-content" style="max-width: 500px;">
					<span class="alezux-close-modal close-modal">&times;</span>
					<h3 style="margin-top:0; margin-bottom: 20px; color: #2d3748;">Crear Nuevo Proyecto</h3>
					
					<form id="create-project-form">
						<div class="form-group">
							<label>Nombre del Proyecto</label>
							<input type="text" name="project_name" class="alezux-input" required placeholder="Ej: E-commerce de Zapatos">
						</div>
						
						<div class="form-group">
							<label>Cliente Asignado</label>
							<select name="customer_id" required class="alezux-input">
								<option value="">Seleccionar Cliente...</option>
								<?php foreach ( $users as $user ) : ?>
									<option value="<?php echo esc_attr( $user->ID ); ?>">
										<?php echo esc_html( $user->display_name . ' (' . $user->user_email . ')' ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>

						<div class="form-group">
							<label>Duración del Proyecto (Inicio - Fin)</label>
							<div class="alezux-input-group">
								<span class="input-group-text"><i class="eicon-calendar"></i></span>
								<input type="text" id="project-date-range-selector" class="alezux-input" placeholder="Selecciona el rango de fechas..." required style="padding-left: 35px;">
							</div>
							<small style="color: #ecc94b; margin-top: 5px; display: block; font-size: 12px;">* Los fines de semana (Sáb y Dom) están excluidos.</small>
							
							<!-- Hidden Inputs for Backend -->
							<input type="hidden" name="project_start_date" required>
							<input type="hidden" name="project_end_date" required>
						</div>

						<div class="form-actions" style="margin-top: 25px; text-align: right; display: flex; justify-content: flex-end; gap: 10px;">
							<button type="button" class="alezux-marketing-btn close-modal-btn" style="background: #e2e8f0; color: #4a5568; box-shadow: none;">Cancelar</button>
							<button type="submit" class="alezux-marketing-btn primary">
								<i class="eicon-plus"></i> Crear Proyecto
							</button>
						</div>
					</form>
				</div>
			</div>



			<!-- PANEL LATERAL (Off-Canvas) -->
			<div id="project-offcanvas-overlay" class="alezux-offcanvas-overlay"></div>
			<div id="project-offcanvas" class="alezux-offcanvas-panel">
				<div class="offcanvas-header">
					<h3 id="offcanvas-title">Cargando...</h3>
					<button class="close-offcanvas-btn"><i class="eicon-close"></i></button>
				</div>
				
				<div id="offcanvas-loading" style="text-align: center; padding: 50px; color: #a0aec0;">
					<i class="eicon-loading eicon-animation-spin" style="font-size: 30px;"></i>
					<p style="margin-top: 10px;">Cargando proyecto...</p>
				</div>

				<div id="offcanvas-content" style="display: none;">
					<!-- Contenido cargado vía AJAX -->
				</div>
			</div>

		</div>
		<?php
	}
}
