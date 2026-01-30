<?php
namespace Alezux_Members\Modules\Logros\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class View_Logros_Widget extends Widget_Base {

	public function get_name() {
		return 'alezux_view_logros';
	}

	public function get_title() {
		return esc_html__( 'Ver Logros', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-table';
	}

	public function get_categories() {
		return [ 'alezux-admin' ];
	}

	public function get_script_depends() {
		return [ 'alezux-view-logros-js' ];
	}

	public function get_style_depends() {
		return [ 'alezux-view-logros-css' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Configuración', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'items_per_page',
			[
				'label' => esc_html__( 'Elementos por página', 'alezux-members' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 5,
				'max' => 100,
				'step' => 5,
				'default' => 20,
			]
		);

		$this->end_controls_section();
		
		$this->end_controls_section();

		/* -------------------------------------------------------------------------- */
		/*                               SECTION: FILTERS                             */
		/* -------------------------------------------------------------------------- */
		$this->start_controls_section(
			'section_style_filters',
			[
				'label' => esc_html__( 'Filtros (Buscador y Select)', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'filters_container_padding',
			[
				'label' => esc_html__( 'Relleno del Contenedor', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-logros-filters' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'filters_container_bg',
			[
				'label' => esc_html__( 'Color de Fondo Contenedor', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-logros-filters' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'filters_container_sombra',
				'selector' => '{{WRAPPER}} .alezux-logros-filters',
			]
		);

		$this->add_control(
			'heading_input_styles',
			[
				'label' => esc_html__( 'Estilos de Inputs', 'alezux-members' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'input_typography',
				'selector' => '{{WRAPPER}} .alezux-logros-filters input, {{WRAPPER}} .alezux-logros-filters select',
			]
		);

		$this->add_control(
			'input_text_color',
			[
				'label' => esc_html__( 'Color de Texto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-logros-filters input, {{WRAPPER}} .alezux-logros-filters select' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_bg_color',
			[
				'label' => esc_html__( 'Color de Fondo Input', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-logros-filters input, {{WRAPPER}} .alezux-logros-filters select' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'input_border',
				'selector' => '{{WRAPPER}} .alezux-logros-filters input, {{WRAPPER}} .alezux-logros-filters select',
			]
		);

		$this->add_responsive_control(
			'input_border_radius',
			[
				'label' => esc_html__( 'Radio del Borde', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-logros-filters input, {{WRAPPER}} .alezux-logros-filters select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/* -------------------------------------------------------------------------- */
		/*                               SECTION: CARD                                */
		/* -------------------------------------------------------------------------- */
		$this->start_controls_section(
			'section_style_card',
			[
				'label' => esc_html__( 'Tarjeta (Logro)', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'card_bg_color',
			[
				'label' => esc_html__( 'Color de Fondo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-card' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_padding',
			[
				'label' => esc_html__( 'Relleno (Padding)', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_gap',
			[
				'label' => esc_html__( 'Espacio entre columnas', 'alezux-members' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-card' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'card_border',
				'selector' => '{{WRAPPER}} .alezux-logro-card',
			]
		);

		$this->add_responsive_control(
			'card_border_radius',
			[
				'label' => esc_html__( 'Radio del Borde', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-logro-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'card_box_shadow',
				'selector' => '{{WRAPPER}} .alezux-logro-card',
			]
		);

		$this->end_controls_section();

		/* -------------------------------------------------------------------------- */
		/*                               SECTION: IMAGE                               */
		/* -------------------------------------------------------------------------- */

		$this->start_controls_section(
			'section_style_image',
			[
				'label' => esc_html__( 'Imagen', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'show_image',
			[
				'label' => esc_html__( 'Mostrar Imagen', 'alezux-members' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Sí', 'alezux-members' ),
				'label_off' => esc_html__( 'No', 'alezux-members' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'image_resolution',
			[
				'label' => esc_html__( 'Resolución de Imagen', 'alezux-members' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'medium',
				'options' => [
					'thumbnail' => esc_html__( 'Miniatura (150x150)', 'alezux-members' ),
					'medium' => esc_html__( 'Medio (300x300)', 'alezux-members' ),
					'large' => esc_html__( 'Grande (1024x1024)', 'alezux-members' ),
					'full' => esc_html__( 'Completo', 'alezux-members' ),
				],
				'condition' => [
					'show_image' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'image_width',
			[
				'label' => esc_html__( 'Ancho', 'alezux-members' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .alezux-card-image' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label' => esc_html__( 'Radio del Borde', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-card-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .alezux-card-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/* -------------------------------------------------------------------------- */
		/*                               SECTION: CONTENT                             */
		/* -------------------------------------------------------------------------- */
		$this->start_controls_section(
			'section_style_content',
			[
				'label' => esc_html__( 'Contenido (Textos)', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		// --- COURSE BADGE ---
		$this->add_control(
			'heading_badge_style',
			[
				'label' => esc_html__( 'Etiqueta de Curso', 'alezux-members' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'badge_typography',
				'selector' => '{{WRAPPER}} .alezux-course-badge',
			]
		);

		$this->add_control(
			'badge_color',
			[
				'label' => esc_html__( 'Color de Texto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-course-badge' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'badge_bg_color',
			[
				'label' => esc_html__( 'Color de Fondo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-course-badge' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'badge_padding',
			[
				'label' => esc_html__( 'Relleno', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-course-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_responsive_control(
			'badge_border_radius',
			[
				'label' => esc_html__( 'Radio del Borde', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-course-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// --- DATE ---
		$this->add_control(
			'heading_date_style',
			[
				'label' => esc_html__( 'Fecha', 'alezux-members' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'date_typography',
				'selector' => '{{WRAPPER}} .alezux-card-date',
			]
		);

		$this->add_control(
			'date_color',
			[
				'label' => esc_html__( 'Color', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-card-date' => 'color: {{VALUE}};',
				],
			]
		);

		// --- MESSAGE ---
		$this->add_control(
			'heading_message_style',
			[
				'label' => esc_html__( 'Mensaje', 'alezux-members' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'message_typography',
				'selector' => '{{WRAPPER}} .alezux-card-message',
			]
		);

		$this->add_control(
			'message_color',
			[
				'label' => esc_html__( 'Color', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-card-message' => 'color: {{VALUE}};',
				],
			]
		);

		// --- STUDENT NAME ---
		$this->add_control(
			'heading_student_style',
			[
				'label' => esc_html__( 'Nombre Estudiante', 'alezux-members' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'student_typography',
				'selector' => '{{WRAPPER}} .alezux-student-name',
			]
		);

		$this->add_control(
			'student_color',
			[
				'label' => esc_html__( 'Color', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-student-name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		/* -------------------------------------------------------------------------- */
		/*                               SECTION: ACTIONS                             */
		/* -------------------------------------------------------------------------- */
		$this->start_controls_section(
			'section_style_actions',
			[
				'label' => esc_html__( 'Botones de Acción', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'actions_padding',
			[
				'label' => esc_html__( 'Relleno', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-btn-card-action' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_responsive_control(
			'actions_radius',
			[
				'label' => esc_html__( 'Radio del Borde', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-btn-card-action' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'actions_typography',
				'selector' => '{{WRAPPER}} .alezux-btn-card-action',
			]
		);

		// EDIT BTN
		$this->add_control(
			'heading_edit_btn',
			[
				'label' => esc_html__( 'Botón Editar', 'alezux-members' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs( 'tabs_edit_btn' );

		$this->start_controls_tab(
			'tab_edit_btn_normal',
			[
				'label' => esc_html__( 'Normal', 'alezux-members' ),
			]
		);

		$this->add_control(
			'edit_btn_color',
			[
				'label' => esc_html__( 'Color de Texto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-btn-card-edit' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'edit_btn_bg_color',
			[
				'label' => esc_html__( 'Color de Fondo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-btn-card-edit' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'edit_btn_border',
				'selector' => '{{WRAPPER}} .alezux-btn-card-edit',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_edit_btn_hover',
			[
				'label' => esc_html__( 'Hover', 'alezux-members' ),
			]
		);

		$this->add_control(
			'edit_btn_hover_color',
			[
				'label' => esc_html__( 'Color de Texto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-btn-card-edit:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'edit_btn_hover_bg_color',
			[
				'label' => esc_html__( 'Color de Fondo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-btn-card-edit:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'edit_btn_hover_border',
				'selector' => '{{WRAPPER}} .alezux-btn-card-edit:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		// DELETE BTN
		$this->add_control(
			'heading_delete_btn',
			[
				'label' => esc_html__( 'Botón Eliminar', 'alezux-members' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs( 'tabs_delete_btn' );

		$this->start_controls_tab(
			'tab_delete_btn_normal',
			[
				'label' => esc_html__( 'Normal', 'alezux-members' ),
			]
		);

		$this->add_control(
			'delete_btn_color',
			[
				'label' => esc_html__( 'Color de Texto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-btn-card-delete' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'delete_btn_bg_color',
			[
				'label' => esc_html__( 'Color de Fondo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-btn-card-delete' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'delete_btn_border',
				'selector' => '{{WRAPPER}} .alezux-btn-card-delete',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_delete_btn_hover',
			[
				'label' => esc_html__( 'Hover', 'alezux-members' ),
			]
		);

		$this->add_control(
			'delete_btn_hover_color',
			[
				'label' => esc_html__( 'Color de Texto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-btn-card-delete:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'delete_btn_hover_bg_color',
			[
				'label' => esc_html__( 'Color de Fondo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-btn-card-delete:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'delete_btn_hover_border',
				'selector' => '{{WRAPPER}} .alezux-btn-card-delete:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		/* -------------------------------------------------------------------------- */
		/*                               SECTION: PAGINATION                          */
		/* -------------------------------------------------------------------------- */
		$this->start_controls_section(
			'section_style_pagination',
			[
				'label' => esc_html__( 'Paginación (Cargar más)', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'pagination_align',
			[
				'label' => esc_html__( 'Alineación', 'alezux-members' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Izquierda', 'alezux-members' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Centro', 'alezux-members' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Derecha', 'alezux-members' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} #alezux-logros-pagination-container' => 'text-align: {{VALUE}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'pagination_typography',
				'selector' => '{{WRAPPER}} #alezux-load-more-logros',
			]
		);
		
		$this->add_responsive_control(
			'pagination_padding',
			[
				'label' => esc_html__( 'Relleno', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} #alezux-load-more-logros' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_pagination_btn' );

		$this->start_controls_tab(
			'tab_pagination_normal',
			[
				'label' => esc_html__( 'Normal', 'alezux-members' ),
			]
		);

		$this->add_control(
			'pagination_color',
			[
				'label' => esc_html__( 'Color de Texto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #alezux-load-more-logros' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_bg_color',
			[
				'label' => esc_html__( 'Color de Fondo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #alezux-load-more-logros' => 'background-color: {{VALUE}};',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'pagination_border',
				'selector' => '{{WRAPPER}} #alezux-load-more-logros',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_pagination_hover',
			[
				'label' => esc_html__( 'Hover', 'alezux-members' ),
			]
		);

		$this->add_control(
			'pagination_hover_color',
			[
				'label' => esc_html__( 'Color de Texto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #alezux-load-more-logros:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_hover_bg_color',
			[
				'label' => esc_html__( 'Color de Fondo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #alezux-load-more-logros:hover' => 'background-color: {{VALUE}};',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'pagination_hover_border',
				'selector' => '{{WRAPPER}} #alezux-load-more-logros:hover',
			]
		);
		
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render() {
		if ( current_user_can( 'upload_files' ) ) {
			wp_enqueue_media();
		}
		// Obtener todos los cursos para el filtro
		$courses = get_posts( [
			'post_type'      => 'sfwd-courses', // Asumiendo LearnDash, ajustar si es otro CPT
			'posts_per_page' => -1,
			'post_status'    => 'publish',
		] );

		?>
		<div class="alezux-view-logros-wrapper">
			<div class="alezux-logros-filters">
				<input type="text" id="alezux-logro-search" placeholder="<?php esc_attr_e( 'Buscar por palabra clave...', 'alezux-members' ); ?>">
				
				<select id="alezux-logro-course-filter">
					<option value=""><?php esc_html_e( 'Todos los cursos', 'alezux-members' ); ?></option>
					<?php foreach ( $courses as $course ) : ?>
						<option value="<?php echo esc_attr( $course->ID ); ?>">
							<?php echo esc_html( $course->post_title ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>

			<div id="alezux-logros-list-container" 
				 data-show-image="<?php echo esc_attr( $settings['show_image'] ?? 'yes' ); ?>"
				 data-image-size="<?php echo esc_attr( $settings['image_resolution'] ?? 'medium' ); ?>">
				<!-- La lista de tarjetas se cargará vía AJAX -->
				<div class="alezux-loading"><?php esc_html_e( 'Cargando registros...', 'alezux-members' ); ?></div>
			</div>
			
			<div id="alezux-logros-pagination-container" style="text-align:center; padding: 20px; display:none;">
				<button id="alezux-load-more-logros" class="alezux-btn alezux-btn-primary">
					<?php esc_html_e( 'Cargar más', 'alezux-members' ); ?>
				</button>
			</div>

			<!-- Modal de Edición (Estructura Avanzada) -->
			<div id="alezux-logro-edit-modal" class="alezux-modal" style="display:none;">
				<div class="alezux-modal-content alezux-logro-form-wrapper" style="max-width: 600px; padding: 0;">
					<div class="alezux-logro-form" style="box-shadow: none; border: none; margin: 0;">
						<span class="alezux-modal-close" style="position: absolute; right: 20px; top: 15px; z-index: 100;">&times;</span>
						<h3 style="margin-top: 0; margin-bottom: 20px; text-align: center; color: #333;"><?php esc_html_e( 'Editar Logro', 'alezux-members' ); ?></h3>
						
						<form id="alezux-logro-edit-form">
							<input type="hidden" id="edit-logro-id" name="id">
							
							<!-- Course -->
							<div class="alezux-logro-form-group">
								<label for="edit-course-id"><?php esc_html_e( 'Curso', 'alezux-members' ); ?></label>
								<select id="edit-course-id" name="course_id" class="alezux-logro-input" required>
									<option value=""><?php esc_html_e( 'Seleccionar Curso', 'alezux-members' ); ?></option>
									<?php foreach ( $courses as $course ) : ?>
										<option value="<?php echo esc_attr( $course->ID ); ?>">
											<?php echo esc_html( $course->post_title ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>

							<!-- Student -->
							<div class="alezux-logro-form-group">
								<label for="edit-student-id"><?php esc_html_e( 'ID Estudiante', 'alezux-members' ); ?></label>
								<input type="number" id="edit-student-id" name="student_id" class="alezux-logro-input">
							</div>

							<!-- Message -->
							<div class="alezux-logro-form-group">
								<label for="edit-message"><?php esc_html_e( 'Mensaje', 'alezux-members' ); ?></label>
								<textarea id="edit-message" name="message" class="alezux-logro-input" rows="4" required></textarea>
							</div>

							<!-- Image Upload -->
							<div class="alezux-logro-form-group">
								<label style="display:block; margin-bottom:8px; font-weight:600;"><?php esc_html_e( 'Imagen', 'alezux-members' ); ?></label>
								<div class="alezux-logro-upload-container">
									<input type="hidden" id="edit-image-id" name="image_id" class="alezux-logro-image-id" value="">
									
									<div class="alezux-upload-box">
										<!-- Placeholder State -->
										<div class="alezux-upload-placeholder">
											<div class="alezux-upload-icon">
												<i class="fas fa-cloud-upload-alt" aria-hidden="true"></i>
											</div>
											<div class="alezux-upload-title">
												<?php esc_html_e( 'Elige un archivo', 'alezux-members' ); ?>
											</div>
											<div class="alezux-upload-button-wrapper">
												<span class="alezux-upload-btn-fake"><?php esc_html_e( 'Buscar Archivo', 'alezux-members' ); ?></span>
											</div>
										</div>

										<!-- Preview State -->
										<div class="alezux-upload-preview" style="display: none;">
											<img class="alezux-preview-img" src="" alt="Preview">
											<span class="alezux-remove-img" title="Eliminar"><i class="eicon-close"></i></span>
										</div>
									</div>
								</div>
							</div>

							<div class="alezux-logro-form-actions">
								<button type="submit" class="alezux-logro-submit">
									<?php esc_html_e( 'Guardar Cambios', 'alezux-members' ); ?>
								</button>
							</div>
						</form>
					</div>
				</div>
			</div>

			<!-- Modal de Eliminación Personalizado -->
			<div id="alezux-delete-modal" class="alezux-modal" style="display:none;">
				<div class="alezux-modal-content alezux-delete-modal-content">
					<div class="alezux-delete-icon">
						<i class="fas fa-trash-alt"></i>
					</div>
					<h3><?php esc_html_e( '¿Estás seguro?', 'alezux-members' ); ?></h3>
					<p><?php esc_html_e( 'Esta acción eliminará el logro permanentemente. No se puede deshacer.', 'alezux-members' ); ?></p>
					
					<div class="alezux-delete-actions">
						<button class="alezux-btn alezux-btn-cancel alezux-modal-close-btn"><?php esc_html_e( 'Cancelar', 'alezux-members' ); ?></button>
						<button id="alezux-confirm-delete-btn" class="alezux-btn alezux-btn-danger-confirm"><?php esc_html_e( 'Sí, Eliminar', 'alezux-members' ); ?></button>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
