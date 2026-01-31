<?php
namespace Alezux_Members\Modules\Estudiantes\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Estudiantes_Widget extends Widget_Base {

	public function get_name() {
		return 'alezux_estudiantes_table';
	}

	public function get_title() {
		return \esc_html__( 'Tabla de Estudiantes', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-table';
	}

	public function get_categories() {
		return [ 'alezux-admin' ];
	}

	public function get_style_depends() {
		return [ 'alezux-estudiantes-css' ];
	}

	public function get_script_depends() {
		return [ 'alezux-estudiantes-js' ];
	}

	protected function _register_controls() {
		// --- Sección Contenido Cabecera ---
		$this->start_controls_section(
			'section_content_header',
			[
				'label' => \esc_html__( 'Cabecera', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'header_title',
			[
				'label'       => \esc_html__( 'Título', 'alezux-members' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => \esc_html__( 'Centro de Mando Académico', 'alezux-members' ),
				'placeholder' => \esc_html__( 'Escribe el título aquí', 'alezux-members' ),
			]
		);

		$this->add_control(
			'header_description',
			[
				'label'       => \esc_html__( 'Descripción', 'alezux-members' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => \esc_html__( 'Gestión de accesos, datos personales y seguridad.', 'alezux-members' ),
				'placeholder' => \esc_html__( 'Escribe la descripción aquí', 'alezux-members' ),
			]
		);

		$this->end_controls_section();

		// --- Sección Estilo Cabecera ---
		$this->start_controls_section(
			'section_style_header',
			[
				'label' => \esc_html__( 'Estilo Cabecera', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'header_bg_color',
			[
				'label'     => \esc_html__( 'Color Fondo Cabecera', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-estudiantes-header' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'header_title_color',
			[
				'label'     => \esc_html__( 'Color Título', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-estudiantes-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'header_title_typography',
				'label'    => \esc_html__( 'Tipografía Título', 'alezux-members' ),
				'selector' => '{{WRAPPER}} .alezux-estudiantes-title',
			]
		);

		$this->add_control(
			'header_desc_color',
			[
				'label'     => \esc_html__( 'Color Descripción', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-estudiantes-desc' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'header_desc_typography',
				'label'    => esc_html__( 'Tipografía Descripción', 'alezux-members' ),
				'selector' => '{{WRAPPER}} .alezux-estudiantes-desc',
			]
		);
		
		$this->add_control(
			'heading_search_style',
			[
				'label'     => \esc_html__( 'Buscador', 'alezux-members' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'search_bg_color',
			[
				'label'     => \esc_html__( 'Fondo Buscador', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-estudiantes-search input' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'search_text_color',
			[
				'label'     => \esc_html__( 'Color Texto Buscador', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-estudiantes-search input' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'search_border_color',
			[
				'label'     => \esc_html__( 'Borde Buscador', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-estudiantes-search input' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// --- Sección de Estilo de la Tabla ---
		$this->start_controls_section(
			'section_style_table',
			[
				'label' => \esc_html__( 'Tabla', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'table_header_bg',
			[
				'label'     => \esc_html__( 'Fondo Cabecera', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-estudiantes-table th' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'table_header_color',
			[
				'label'     => \esc_html__( 'Color Texto Cabecera', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-estudiantes-table th' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'table_header_typography',
				'label'    => \esc_html__( 'Tipografía Cabecera', 'alezux-members' ),
				'selector' => '{{WRAPPER}} .alezux-estudiantes-table th',
			]
		);

		$this->add_control(
			'table_row_even_bg',
			[
				'label'     => \esc_html__( 'Fondo Filas Pares', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-estudiantes-table tr:nth-child(even) td' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'table_row_odd_bg',
			[
				'label'     => \esc_html__( 'Fondo Filas Impares', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-estudiantes-table tr:nth-child(odd) td' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'heading_table_cells',
			[
				'label'     => \esc_html__( 'Celdas y Bordes', 'alezux-members' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'cell_padding',
			[
				'label'      => \esc_html__( 'Padding Celdas', 'alezux-members' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .alezux-estudiantes-table th, {{WRAPPER}} .alezux-estudiantes-table td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'table_border',
				'label'    => \esc_html__( 'Borde Tabla', 'alezux-members' ),
				'selector' => '{{WRAPPER}} .alezux-estudiantes-wrapper',
			]
		);

		$this->add_control(
			'heading_avatar_style',
			[
				'label'     => \esc_html__( 'Avatar', 'alezux-members' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'avatar_size',
			[
				'label' => \esc_html__( 'Tamaño Avatar', 'alezux-members' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .col-foto img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'heading_status_style',
			[
				'label'     => \esc_html__( 'Estados', 'alezux-members' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'status_active_color',
			[
				'label'     => \esc_html__( 'Color Activo (Texto)', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .status-active' => 'color: {{VALUE}}; border-color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'status_active_bg',
			[
				'label'     => \esc_html__( 'Color Activo (Fondo)', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .status-active' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'status_inactive_color',
			[
				'label'     => \esc_html__( 'Color Inactivo (Texto)', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .status-inactive' => 'color: {{VALUE}}; border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'status_inactive_bg',
			[
				'label'     => \esc_html__( 'Color Inactivo (Fondo)', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .status-inactive' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// --- Sección Estilo Barra de Progreso ---
		$this->start_controls_section(
			'section_style_progress',
			[
				'label' => \esc_html__( 'Barra de Progreso', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'progress_container_heading',
			[
				'label'     => \esc_html__( 'Contenedor', 'alezux-members' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'progress_container_height',
			[
				'label' => \esc_html__( 'Alto', 'alezux-members' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [ 'min' => 2, 'max' => 20 ],
				],
				'selectors' => [
					'{{WRAPPER}} .alezux-progress-wrapper' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'progress_container_bg',
			[
				'label'     => \esc_html__( 'Color Fondo', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-progress-wrapper' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'progress_container_radius',
			[
				'label'      => \esc_html__( 'Radio Borde', 'alezux-members' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .alezux-progress-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'progress_bar_heading',
			[
				'label'     => \esc_html__( 'Barra de Relleno', 'alezux-members' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'progress_fill_color',
			[
				'label'     => \esc_html__( 'Color Relleno', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-progress-bar' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'progress_fill_radius',
			[
				'label'      => \esc_html__( 'Radio Borde Relleno', 'alezux-members' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .alezux-progress-bar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'progress_text_heading',
			[
				'label'     => \esc_html__( 'Texto (% Completado)', 'alezux-members' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'progress_text_color',
			[
				'label'     => \esc_html__( 'Color Texto', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-progress-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'progress_text_typography',
				'selector' => '{{WRAPPER}} .alezux-progress-text',
			]
		);

		$this->end_controls_section();

		// --- Sección Botón Gestionar ---
		$this->start_controls_section(
			'section_style_btn',
			[
				'label' => \esc_html__( 'Botón Gestionar', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'btn_bg_color',
			[
				'label'     => \esc_html__( 'Color Fondo', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .btn-gestionar' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_text_color',
			[
				'label'     => \esc_html__( 'Color Texto', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .btn-gestionar' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'btn_border',
				'selector' => '{{WRAPPER}} .btn-gestionar',
			]
		);

		$this->add_control(
			'btn_border_radius',
			[
				'label'      => \esc_html__( 'Radio de Borde', 'alezux-members' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .btn-gestionar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// --- SECCIÓN ESTILO MODAL: GENERAL ---
		$this->start_controls_section(
			'section_style_modal',
			[
				'label' => \esc_html__( 'Modal: General y Header', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'modal_overlay_bg',
			[
				'label'     => \esc_html__( 'Fondo Overlay', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.alezux-management-modal-overlay' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'modal_bg_color',
			[
				'label'     => \esc_html__( 'Fondo Modal', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.alezux-management-modal, .alezux-alert-modal' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'modal_border',
				'label'    => \esc_html__( 'Borde Modal', 'alezux-members' ),
				'selector' => '.alezux-management-modal, .alezux-alert-modal',
			]
		);
		
		$this->add_control(
			'modal_radius',
			[
				'label'      => \esc_html__( 'Radio Borde', 'alezux-members' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'.alezux-management-modal, .alezux-alert-modal' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'modal_shadow',
				'selector' => '.alezux-management-modal, .alezux-alert-modal',
			]
		);

		$this->add_control(
			'heading_modal_header',
			[
				'label'     => \esc_html__( 'Cabecera Modal', 'alezux-members' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'modal_header_bg',
			[
				'label'     => \esc_html__( 'Fondo Header', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.alezux-modal-header' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'modal_title_color',
			[
				'label'     => \esc_html__( 'Color Título', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.alezux-modal-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'modal_title_typo',
				'selector' => '.alezux-modal-title',
			]
		);

		$this->start_controls_tabs( 'tabs_modal_close_style' );
		
		$this->start_controls_tab(
			'tab_modal_close_normal',
			[
				'label' => \esc_html__( 'Normal', 'alezux-members' ),
			]
		);

		$this->add_control(
			'modal_close_color',
			[
				'label'     => \esc_html__( 'Color Icono', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.alezux-modal-close' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'modal_close_bg_color',
			[
				'label'     => \esc_html__( 'Color Fondo', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.alezux-modal-close' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_modal_close_hover',
			[
				'label' => \esc_html__( 'Hover', 'alezux-members' ),
			]
		);

		$this->add_control(
			'modal_close_color_hover',
			[
				'label'     => \esc_html__( 'Color Icono', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.alezux-modal-close:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'modal_close_bg_color_hover',
			[
				'label'     => \esc_html__( 'Color Fondo', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.alezux-modal-close:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// --- SECCIÓN ESTILO MODAL: FORMULARIOS ---
		$this->start_controls_section(
			'section_style_modal_form',
			[
				'label' => \esc_html__( 'Modal: Etiquetas e Inputs', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'modal_label_color',
			[
				'label'     => \esc_html__( 'Color Etiquetas', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.alezux-form-label, .alezux-section-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'modal_label_typo',
				'label'    => \esc_html__( 'Tipografía Etiquetas', 'alezux-members' ),
				'selector' => '.alezux-form-label, .alezux-section-title',
			]
		);

		$this->add_control(
			'heading_modal_inputs',
			[
				'label'     => \esc_html__( 'Campos de Texto', 'alezux-members' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'modal_input_typo',
				'selector' => '.alezux-form-control',
			]
		);

		$this->add_control(
			'modal_input_bg',
			[
				'label'     => \esc_html__( 'Fondo Input', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.alezux-form-control' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'modal_input_color',
			[
				'label'     => \esc_html__( 'Color Texto Input', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.alezux-form-control' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'modal_input_border_color',
			[
				'label'     => \esc_html__( 'Color Borde Input', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.alezux-form-control' => 'border-color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'modal_input_radius',
			[
				'label'      => \esc_html__( 'Radio Borde Input', 'alezux-members' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'.alezux-form-control' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'modal_input_padding',
			[
				'label'      => \esc_html__( 'Padding Input', 'alezux-members' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'.alezux-form-control' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// --- SECCIÓN ESTILO MODAL: BOTONES ---
		$this->start_controls_section(
			'section_style_modal_buttons',
			[
				'label' => \esc_html__( 'Modal: Botones', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		// --- Botón Primario ---
		$this->add_control(
			'heading_btn_primary',
			[
				'label'     => \esc_html__( 'Botón Guardar (Primario)', 'alezux-members' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'modal_btn_primary_typo',
				'selector' => '.alezux-btn-primary',
			]
		);

		$this->start_controls_tabs( 'tabs_btn_primary' );

		$this->start_controls_tab( 'tab_btn_primary_normal', [ 'label' => \esc_html__( 'Normal', 'alezux-members' ) ] );

		$this->add_control(
			'modal_btn_primary_text',
			[
				'label'     => \esc_html__( 'Texto', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '.alezux-btn-primary' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'modal_btn_primary_bg',
			[
				'label'     => \esc_html__( 'Fondo', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '.alezux-btn-primary' => 'background-color: {{VALUE}};' ],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'tab_btn_primary_hover', [ 'label' => \esc_html__( 'Hover', 'alezux-members' ) ] );

		$this->add_control(
			'modal_btn_primary_text_hover',
			[
				'label'     => \esc_html__( 'Texto', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '.alezux-btn-primary:hover' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'modal_btn_primary_bg_hover',
			[
				'label'     => \esc_html__( 'Fondo', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '.alezux-btn-primary:hover' => 'background-color: {{VALUE}};' ],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		// --- Botón Warning ---
		$this->add_control(
			'heading_btn_warning',
			[
				'label'     => \esc_html__( 'Botón Reset Password (Warning)', 'alezux-members' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'modal_btn_warning_typo',
				'selector' => '.alezux-btn-warning',
			]
		);

		$this->start_controls_tabs( 'tabs_btn_warning' );

		$this->start_controls_tab( 'tab_btn_warning_normal', [ 'label' => \esc_html__( 'Normal', 'alezux-members' ) ] );

		$this->add_control(
			'modal_btn_warning_text',
			[
				'label'     => \esc_html__( 'Texto', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '.alezux-btn-warning' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'modal_btn_warning_bg',
			[
				'label'     => \esc_html__( 'Fondo', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '.alezux-btn-warning' => 'background-color: {{VALUE}};' ],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'tab_btn_warning_hover', [ 'label' => \esc_html__( 'Hover', 'alezux-members' ) ] );

		$this->add_control(
			'modal_btn_warning_text_hover',
			[
				'label'     => \esc_html__( 'Texto', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '.alezux-btn-warning:hover' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'modal_btn_warning_bg_hover',
			[
				'label'     => \esc_html__( 'Fondo', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '.alezux-btn-warning:hover' => 'background-color: {{VALUE}};' ],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		// --- Botón Danger ---
		$this->add_control(
			'heading_btn_danger',
			[
				'label'     => \esc_html__( 'Botón Bloquear (Peligro)', 'alezux-members' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'modal_btn_danger_typo',
				'selector' => '.alezux-btn-danger',
			]
		);

		$this->start_controls_tabs( 'tabs_btn_danger' );

		$this->start_controls_tab( 'tab_btn_danger_normal', [ 'label' => \esc_html__( 'Normal', 'alezux-members' ) ] );

		$this->add_control(
			'modal_btn_danger_text',
			[
				'label'     => \esc_html__( 'Texto', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '.alezux-btn-danger' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'modal_btn_danger_bg',
			[
				'label'     => \esc_html__( 'Fondo', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '.alezux-btn-danger' => 'background-color: {{VALUE}};' ],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'tab_btn_danger_hover', [ 'label' => \esc_html__( 'Hover', 'alezux-members' ) ] );

		$this->add_control(
			'modal_btn_danger_text_hover',
			[
				'label'     => \esc_html__( 'Texto', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '.alezux-btn-danger:hover' => 'color: {{VALUE}};' ],
			]
		);

		$this->add_control(
			'modal_btn_danger_bg_hover',
			[
				'label'     => \esc_html__( 'Fondo', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '.alezux-btn-danger:hover' => 'background-color: {{VALUE}};' ],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$limit = 10;

		// Obtener estudiantes
		$args = [
			'role__in'    => [ 'subscriber', 'student' ],
			'number'      => $limit,
			'count_total' => true,
		];

		$user_query = new \WP_User_Query( $args );
		$students = $user_query->get_results();
		$total_users = $user_query->get_total();

		// Fallback si no hay roles específicos
		if ( empty( $students ) && $total_users === 0 ) {
			$args = [ 
				'number'      => $limit,
				'count_total' => true,
			]; 
			$user_query = new \WP_User_Query( $args );
			$students = $user_query->get_results();
			$total_users = $user_query->get_total();
		}

		$total_pages = ceil( $total_users / $limit );

		?>

		<div class="alezux-estudiantes-wrapper" data-limit="<?php echo \esc_attr( $limit ); ?>">
			<!-- Header -->
			<div class="alezux-estudiantes-header">
				<div class="alezux-header-content">
					<h2 class="alezux-estudiantes-title"><?php echo \esc_html( $settings['header_title'] ); ?></h2>
					<p class="alezux-estudiantes-desc"><?php echo \esc_html( $settings['header_description'] ); ?></p>
				</div>
				<div class="alezux-estudiantes-search">
					<i class="fa fa-search search-icon"></i>
					<input type="text" placeholder="<?php \esc_attr_e( 'Buscar por nombre o email...', 'alezux-members' ); ?>">
				</div>
			</div>

			<!-- Table -->
			<div class="alezux-estudiantes-table-container">
				<table class="alezux-estudiantes-table">
					<thead>
						<tr>
							<th><?php \esc_html_e( 'FOTO', 'alezux-members' ); ?></th>
							<th><?php \esc_html_e( 'NOMBRE', 'alezux-members' ); ?></th>
								<th class="col-correo"><?php \esc_html_e( 'Correo', 'alezux-members' ); ?></th>
								<th class="col-progreso"><?php \esc_html_e( 'Progreso', 'alezux-members' ); ?></th>
								<th class="col-estado"><?php \esc_html_e( 'Estado', 'alezux-members' ); ?></th>
							<th><?php \esc_html_e( 'FUNCIONES', 'alezux-members' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if ( ! empty( $students ) ) : ?>
							<?php foreach ( $students as $student ) : 
								$avatar_url = \get_avatar_url( $student->ID );
								$name = $student->display_name;
								$email = $student->user_email;
								
								// Calcular Progreso
								$avg_progress = 0;
								if ( function_exists( 'learndash_user_get_enrolled_courses' ) && function_exists( 'learndash_course_get_user_progress' ) ) {
									$user_courses = \learndash_user_get_enrolled_courses( $student->ID );
									if ( ! empty( $user_courses ) ) {
										$total_progress = 0;
										foreach ( $user_courses as $course_id ) {
											$progress = \learndash_course_get_user_progress( $student->ID, $course_id );
											$percentage = isset( $progress['percentage'] ) ? intval( $progress['percentage'] ) : 0;
											$total_progress += $percentage;
										}
										$avg_progress = intval( $total_progress / count( $user_courses ) );
									}
								}

								$is_blocked = (bool) \get_user_meta( $student->ID, 'alezux_is_blocked', true );
								if ( $is_blocked ) {
									$status_label = \esc_html__( 'Bloqueado', 'alezux-members' );
									$status_class = 'status-inactive'; // Asumimos que existe o se estilizará igual que 'error'
								} else {
									$status_label = 'OK';
									$status_class = 'status-active';
								}
							?>
							<tr>
								<td class="col-foto">
									<img src="<?php echo \esc_url( $avatar_url ); ?>" alt="<?php echo \esc_attr( $name ); ?>">
								</td>
								<td class="col-nombre">
									<?php echo \esc_html( $name ); ?>
									<div style="font-size: 12px; color: #999;"><?php echo '@' . \esc_html( $student->user_nicename ); ?></div>
								</td>
								<td class="col-correo">
									<?php echo \esc_html( $email ); ?>
								</td>
								<td class="col-progreso">
									<div class="alezux-progress-wrapper" style="width: 100%;">
										<div class="alezux-progress-bar" style="width: <?php echo \esc_attr( $avg_progress ); ?>%;"></div>
									</div>
									<div class="alezux-progress-text"><?php echo \esc_html( $avg_progress ); ?>% Completado</div>
								</td>
								<td class="col-estado">
									<span class="<?php echo \esc_attr( $status_class ); ?>">
										<i class="fa fa-circle" style="font-size: 8px; margin-right: 4px;"></i>
										<?php echo \esc_html( $status_label ); ?>
									</span>
								</td>
								<td class="col-funciones">
									<button class="btn-gestionar" data-student-id="<?php echo \esc_attr( $student->ID ); ?>">
										<i class="fa fa-cog"></i> <?php \esc_html_e( 'Gestionar', 'alezux-members' ); ?>
									</button>
								</td>
							</tr>
							<?php endforeach; ?>
						<?php else : ?>
							<tr>
								<td colspan="5" style="text-align:center; padding: 20px;">
									<?php \esc_html_e( 'No se encontraron estudiantes.', 'alezux-members' ); ?>
								</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
			
			<!-- Pagination -->
			<div class="alezux-estudiantes-pagination" 
				 data-total-pages="<?php echo \esc_attr( $total_pages ); ?>" 
				 data-current-page="1">
			<!-- Pagination rendered via JS -->
			</div>

            <!-- MODAL DE GESTIÓN (Hidden default) -->
            <div id="alezux-management-modal-overlay" class="alezux-management-modal-overlay" style="display:none;">
                <div class="alezux-management-modal">
                    <div class="alezux-modal-header">
                        <h3 class="alezux-modal-title"><?php \esc_html_e( 'Gestionar Estudiante', 'alezux-members' ); ?></h3>
                        <button id="alezux-modal-close" class="alezux-modal-close">&times;</button>
                    </div>
                    <div class="alezux-modal-body">
                        <!-- Loading State -->
                        <div id="alezux-modal-loading" style="text-align:center; padding: 40px;">
                            <i class="fa fa-spinner fa-spin" style="font-size: 30px; color: #6366f1;"></i>
                        </div>

                        <!-- Content State -->
                        <div id="alezux-modal-content" style="display:none;">
                            <input type="hidden" id="alezux-manage-user-id" value="">

                            <!-- 1. Editar Datos -->
                            <div class="alezux-section-title"><?php \esc_html_e( 'Información Personal', 'alezux-members' ); ?></div>
                            <div class="alezux-manage-form-grid">
                                <div>
                                    <label class="alezux-form-label"><?php \esc_html_e( 'Nombre', 'alezux-members' ); ?></label>
                                    <input type="text" id="manage-first-name" class="alezux-form-control">
                                </div>
                                <div>
                                    <label class="alezux-form-label"><?php \esc_html_e( 'Apellido', 'alezux-members' ); ?></label>
                                    <input type="text" id="manage-last-name" class="alezux-form-control">
                                </div>
                                <div class="alezux-full-width">
                                    <label class="alezux-form-label"><?php \esc_html_e( 'Correo Electrónico', 'alezux-members' ); ?></label>
                                    <input type="email" id="manage-email" class="alezux-form-control">
                                </div>
                                <div class="alezux-full-width" style="margin-top:10px;">
                                    <button class="alezux-btn alezux-btn-primary" id="btn-save-student-data">
                                        <?php \esc_html_e( 'Guardar Cambios', 'alezux-members' ); ?> <i class="fa fa-spinner alezux-spinner"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- 2. Seguridad (Password & Block) -->
                            <div class="alezux-section-title"><?php \esc_html_e( 'Seguridad y Acceso', 'alezux-members' ); ?></div>
                            <div class="alezux-manage-form-grid">
                                <div>
                                    <button class="alezux-btn alezux-btn-warning alezux-btn-block" id="btn-reset-password">
                                        <i class="fa fa-key"></i> <?php \esc_html_e( 'Restablecer Contraseña', 'alezux-members' ); ?>
                                    </button>
                                    <small style="display:block; margin-top:5px; color:#888; font-size:11px;">
                                        Genera una nueva y la envía por correo.
                                    </small>
                                </div>
                                <div>
                                    <button class="alezux-btn alezux-btn-danger alezux-btn-block" id="btn-block-user">
                                        <i class="fa fa-ban"></i> <span id="lbl-block-user"><?php \esc_html_e( 'Bloquear Acceso Academia', 'alezux-members' ); ?></span>
                                    </button>
                                </div>
                            </div>

                            <!-- 3. Cursos -->
                            <div class="alezux-section-title"><?php \esc_html_e( 'Cursos Activos', 'alezux-members' ); ?></div>
                            <ul id="list-enrolled-courses" class="alezux-course-list">
                                <!-- Populated via JS -->
                            </ul>
                            <div id="no-enrolled-msg" style="color:#666; font-size:13px; font-style:italic; display:none;"><?php \esc_html_e( 'No tiene cursos activos.', 'alezux-members' ); ?></div>

                            <div class="alezux-section-title"><?php \esc_html_e( 'Cursos Disponibles (Conceder Acceso)', 'alezux-members' ); ?></div>
                            <div style="max-height: 150px; overflow-y:auto; border:1px solid #333; padding:5px; border-radius:6px;">
                                <ul id="list-available-courses" class="alezux-course-list">
                                    <!-- Populated via JS -->
                                </ul>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

			<!-- SYSTEM ALERT MODAL -->
            <div id="alezux-alert-modal-overlay" class="alezux-management-modal-overlay" style="display:none; z-index: 10001;">
                <div class="alezux-alert-modal">
                    <div class="alezux-alert-icon" id="alezux-alert-icon">
                        <i class="fa fa-info-circle"></i>
                    </div>
                    <h3 id="alezux-alert-title">Título Alerta</h3>
                    <p id="alezux-alert-message">Mensaje de alerta</p>
                    <div class="alezux-alert-actions">
                        <button id="alezux-alert-cancel" class="alezux-btn alezux-btn-secondary" style="display:none;"><?php \esc_html_e( 'Cancelar', 'alezux-members' ); ?></button>
                        <button id="alezux-alert-confirm" class="alezux-btn alezux-btn-primary"><?php \esc_html_e( 'Aceptar', 'alezux-members' ); ?></button>
                    </div>
                </div>
            </div>

		</div>
		<?php
	}
}
