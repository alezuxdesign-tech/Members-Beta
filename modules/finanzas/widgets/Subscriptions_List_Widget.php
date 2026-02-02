<?php
namespace Alezux_Members\Modules\Finanzas\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Subscriptions_List_Widget extends Widget_Base {

	public function get_name() {
		return 'alezux_subs_list';
	}

	public function get_title() {
		return esc_html__( 'Listado Suscripciones (Alezux)', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-person';
	}

	public function get_categories() {
		return [ 'alezux-members' ];
	}

	public function get_script_depends() {
		return [ 'alezux-subs-list-js' ];
	}

    public function get_style_depends() {
		return [ 'alezux-subs-list-css' ]; 
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
			'view_mode',
			[
				'label' => esc_html__( 'Vista Inicial', 'alezux-members' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'subscriptions',
				'options' => [
					'subscriptions' => esc_html__( 'Suscripciones de Usuarios', 'alezux-members' ),
				],
			]
		);

		$this->end_controls_section();

        // --- ESTILO: CONTENEDOR PRINCIPAL ---
        $this->start_controls_section(
            'style_section_container',
            [
                'label' => esc_html__('Contenedor Principal', 'alezux-members'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'container_background',
                'label' => esc_html__('Fondo', 'alezux-members'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .alezux-subs-list-app, {{WRAPPER}} .alezux-subs-wrapper',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'container_border',
                'label' => esc_html__('Borde', 'alezux-members'),
                'selector' => '{{WRAPPER}} .alezux-subs-list-app, {{WRAPPER}} .alezux-subs-wrapper',
            ]
        );

        $this->add_control(
            'container_border_radius',
            [
                'label' => esc_html__('Radio del Borde', 'alezux-members'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .alezux-subs-list-app' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .alezux-subs-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'container_box_shadow',
                'label' => esc_html__('Sombra de Caja', 'alezux-members'),
                'selector' => '{{WRAPPER}} .alezux-subs-list-app, {{WRAPPER}} .alezux-subs-wrapper',
            ]
        );

        $this->add_responsive_control(
            'container_padding',
            [
                'label' => esc_html__('Relleno (Padding)', 'alezux-members'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .alezux-subs-list-app' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // --- ESTILO: BUSCADOR ---
        $this->start_controls_section(
            'style_section_search',
            [
                'label' => esc_html__('Buscador', 'alezux-members'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'search_icon_color',
            [
                'label' => esc_html__('Color Icono', 'alezux-members'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .alezux-search-wrapper .dashicons-search' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'search_text_color',
            [
                'label' => esc_html__('Color Texto', 'alezux-members'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} #alezux-subs-search' => 'color: {{VALUE}};',
                ],
            ]
        );

         $this->add_control(
            'search_placeholder_color',
            [
                'label' => esc_html__('Color Placeholder', 'alezux-members'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} #alezux-subs-search::placeholder' => 'color: {{VALUE}};',
                    '{{WRAPPER}} #alezux-subs-search::-webkit-input-placeholder' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'search_bg_color',
            [
                'label' => esc_html__('Color Fondo', 'alezux-members'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} #alezux-subs-search' => 'background-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'search_border_color',
            [
                'label' => esc_html__('Color Borde', 'alezux-members'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} #alezux-subs-search' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'search_typography',
                'selector' => '{{WRAPPER}} #alezux-subs-search',
            ]
        );

         $this->add_control(
            'search_border_radius',
            [
                'label' => esc_html__('Radio del Borde', 'alezux-members'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} #alezux-subs-search' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // --- ESTILO: ENCABEZADO TABLA ---
        $this->start_controls_section(
            'style_section_thead',
            [
                'label' => esc_html__('Tabla: Encabezado', 'alezux-members'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'thead_bg_color',
            [
                'label' => esc_html__('Color Fondo', 'alezux-members'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .alezux-subs-table thead th' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .alezux-subs-table thead' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'thead_text_color',
            [
                'label' => esc_html__('Color Texto', 'alezux-members'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .alezux-subs-table thead th' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'thead_typography',
                'selector' => '{{WRAPPER}} .alezux-subs-table thead th',
            ]
        );

        $this->add_responsive_control(
            'thead_padding',
            [
                'label' => esc_html__('Relleno (Padding)', 'alezux-members'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .alezux-subs-table thead th' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // --- ESTILO: CUERPO TABLA ---
        $this->start_controls_section(
            'style_section_tbody',
            [
                'label' => esc_html__('Tabla: Cuerpo', 'alezux-members'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'tbody_bg_color',
            [
                'label' => esc_html__('Color Fondo Filas', 'alezux-members'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .alezux-subs-table tbody td' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .alezux-subs-table tbody tr' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'tbody_bg_hover_color',
            [
                'label' => esc_html__('Color Fondo Hover', 'alezux-members'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .alezux-subs-table tbody tr:hover td' => 'background-color: {{VALUE}};',
                ],
            ]
        );

         $this->add_control(
            'tbody_text_color',
            [
                'label' => esc_html__('Color Texto General', 'alezux-members'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .alezux-subs-table tbody td' => 'color: {{VALUE}};',
                ],
            ]
        );

         $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'tbody_typography',
                'selector' => '{{WRAPPER}} .alezux-subs-table tbody td',
            ]
        );

        $this->add_control(
            'tbody_border_color',
            [
                'label' => esc_html__('Color Bordes Separadores', 'alezux-members'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .alezux-subs-table tbody td' => 'border-bottom-color: {{VALUE}};',
                    '{{WRAPPER}} .alezux-subs-table thead th' => 'border-bottom-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'tbody_padding',
            [
                'label' => esc_html__('Relleno (Padding)', 'alezux-members'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .alezux-subs-table tbody td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // --- ESTILO: ESTUDIANTE ---
        $this->start_controls_section(
            'style_section_student',
            [
                'label' => esc_html__('Estudiante', 'alezux-members'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'student_name_color',
            [
                'label' => esc_html__('Color Nombre', 'alezux-members'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .student-name' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'student_name_typography',
                'label' => 'Tipografía Nombre',
                'selector' => '{{WRAPPER}} .student-name',
            ]
        );

        $this->add_control(
            'student_email_color',
            [
                'label' => esc_html__('Color Email', 'alezux-members'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .student-email' => 'color: {{VALUE}}; word-break: break-all; white-space: normal; display: block; max-width: 100%;',
                    '{{WRAPPER}} .alezux-student-text' => 'min-width: 0; flex: 1;', 
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'student_email_typography',
                'label' => 'Tipografía Email',
                'selector' => '{{WRAPPER}} .student-email',
            ]
        );

        $this->add_responsive_control(
            'student_avatar_size',
            [
                'label' => esc_html__('Tamaño Avatar', 'alezux-members'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 20,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .alezux-student-avatar' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

         // --- ESTILO: PROGRESO ---
         $this->start_controls_section(
            'style_section_progress',
            [
                'label' => esc_html__('Progreso', 'alezux-members'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'progress_label_color',
            [
                'label' => esc_html__('Color Texto Etiquetas', 'alezux-members'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .progress-Label span' => 'color: {{VALUE}};',
                ],
            ]
        );

         $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'progress_label_typography',
                'selector' => '{{WRAPPER}} .progress-Label span',
            ]
        );

        $this->add_control(
            'progress_bg_color',
            [
                'label' => esc_html__('Color Fondo Barra', 'alezux-members'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .alezux-progress-bar-bg' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'progress_fill_color',
            [
                'label' => esc_html__('Color Relleno Barra', 'alezux-members'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .alezux-progress-bar-fill' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'progress_height',
            [
                'label' => esc_html__('Altura Barra', 'alezux-members'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 2,
                        'max' => 30,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .alezux-progress-bar-bg' => 'height: {{SIZE}}{{UNIT}};',
                     '{{WRAPPER}} .alezux-progress-bar-fill' => 'height: 100%;',
                ],
            ]
        );

         $this->add_control(
            'progress_radius',
            [
                'label' => esc_html__('Redondeo', 'alezux-members'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .alezux-progress-bar-bg' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .alezux-progress-bar-fill' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->end_controls_section();

        // --- ESTILO: ESTADO (BADGES) ---
         $this->start_controls_section(
            'style_section_badges',
            [
                'label' => esc_html__('Estado (Badges)', 'alezux-members'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'badge_typography',
                'selector' => '{{WRAPPER}} .alezux-status-badge',
            ]
        );

        $this->add_control(
            'badge_padding',
             [
                'label' => esc_html__('Relleno', 'alezux-members'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .alezux-status-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'badge_radius',
             [
                'label' => esc_html__('Redondeo', 'alezux-members'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .alezux-status-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        // Colores por estado
        $this->add_control('heading_badge_active', ['type' => Controls_Manager::HEADING, 'label' => 'Activo']);
        $this->add_control('badge_active_text', ['label' => 'Texto', 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .status-active' => 'color: {{VALUE}};']]);
        $this->add_control('badge_active_bg', ['label' => 'Fondo', 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .status-active' => 'background-color: {{VALUE}};']]);

        $this->add_control('heading_badge_completed', ['type' => Controls_Manager::HEADING, 'label' => 'Completado']);
        $this->add_control('badge_completed_text', ['label' => 'Texto', 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .status-completed' => 'color: {{VALUE}};']]);
        $this->add_control('badge_completed_bg', ['label' => 'Fondo', 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .status-completed' => 'background-color: {{VALUE}};']]);

        $this->add_control('heading_badge_past_due', ['type' => Controls_Manager::HEADING, 'label' => 'Vencido']);
        $this->add_control('badge_past_due_text', ['label' => 'Texto', 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .status-past_due' => 'color: {{VALUE}};']]);
        $this->add_control('badge_past_due_bg', ['label' => 'Fondo', 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .status-past_due' => 'background-color: {{VALUE}};']]);

         $this->add_control('heading_badge_canceled', ['type' => Controls_Manager::HEADING, 'label' => 'Cancelado']);
        $this->add_control('badge_canceled_text', ['label' => 'Texto', 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .status-canceled' => 'color: {{VALUE}};']]);
        $this->add_control('badge_canceled_bg', ['label' => 'Fondo', 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .status-canceled' => 'background-color: {{VALUE}};']]);


        $this->end_controls_section();


        // --- ESTILO: BOTONES ---
        $this->start_controls_section(
            'style_section_buttons',
            [
                'label' => esc_html__('Botones Acciones', 'alezux-members'),
                'tab'   => Controls_Manager::TAB_STYLE,
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
                'label' => esc_html__( 'Color Texto', 'alezux-members' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .alezux-btn-manual-pay' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_bg_color',
            [
                'label' => esc_html__( 'Color Fondo', 'alezux-members' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .alezux-btn-manual-pay' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'button_border',
                'selector' => '{{WRAPPER}} .alezux-btn-manual-pay',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover',
            [
                'label' => esc_html__( 'Hover', 'alezux-members' ),
            ]
        );

        $this->add_control(
            'button_hover_text_color',
            [
                'label' => esc_html__( 'Color Texto', 'alezux-members' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .alezux-btn-manual-pay:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_bg_color',
            [
                'label' => esc_html__( 'Color Fondo', 'alezux-members' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .alezux-btn-manual-pay:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_border_color',
            [
                'label' => esc_html__( 'Color Borde', 'alezux-members' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .alezux-btn-manual-pay:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

         $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .alezux-btn-manual-pay',
                 'separator' => 'before',
            ]
        );

        $this->add_control(
            'button_radius',
             [
                'label' => esc_html__('Redondeo', 'alezux-members'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .alezux-btn-manual-pay' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'button_padding',
             [
                'label' => esc_html__('Relleno', 'alezux-members'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .alezux-btn-manual-pay' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // --- ESTILO: MONTO ---
        $this->start_controls_section(
            'style_section_amount',
            [
                'label' => esc_html__('Monto', 'alezux-members'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'amount_text_color',
            [
                'label' => esc_html__('Color Texto', 'alezux-members'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .col-amount strong' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .col-amount' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'amount_typography',
                'selector' => '{{WRAPPER}} .col-amount strong, {{WRAPPER}} .col-amount',
            ]
        );

        $this->add_control(
            'amount_bg_color',
            [
                'label' => esc_html__('Color Fondo Celda', 'alezux-members'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .col-amount' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings_for_display();
        ?>
        <div class="alezux-subs-list-app">
            
            <div class="alezux-filter-bar">
                <div class="alezux-filter-item search-item">
                     <div class="alezux-search-wrapper">
                        <span class="dashicons dashicons-search"></span>
                        <input type="text" id="alezux-subs-search" placeholder="<?php esc_attr_e('Buscar por estudiante o plan...', 'alezux-members'); ?>">
                     </div>
                </div>
            </div>

            <div class="alezux-loading-subs" style="display:none;">
                <i class="eicon-loading eicon-animation-spin"></i> <?php esc_html_e('Cargando suscripciones...', 'alezux-members'); ?>
            </div>

            <div class="alezux-subs-wrapper">
                <table class="alezux-subs-table alezux-sales-table"> 
                    <thead>
                        <tr>
                            <th class="col-id"><?php esc_html_e('ID', 'alezux-members'); ?></th>
                            <th class="col-student"><?php esc_html_e('ESTUDIANTE', 'alezux-members'); ?></th>
                            <th class="col-plan"><?php esc_html_e('PLAN ACADÉMICO', 'alezux-members'); ?></th>
                            <th class="col-amount"><?php esc_html_e('MONTO', 'alezux-members'); ?></th>
                            <th class="col-status"><?php esc_html_e('ESTADO', 'alezux-members'); ?></th>
                            <th class="col-progress"><?php esc_html_e('PROGRESO', 'alezux-members'); ?></th>
                            <th class="col-next-payment"><?php esc_html_e('VENCIMIENTO', 'alezux-members'); ?></th>
                            <th class="col-actions"><?php esc_html_e('ACCIONES', 'alezux-members'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- AJAX Content -->
                    </tbody>
                </table>
            </div>

            <!-- Modal de Pago Manual -->
            <div id="alezux-manual-pay-modal" class="alezux-modal" style="display:none;">
                <div class="alezux-modal-content">
                    <span class="alezux-close-modal">&times;</span>
                    <h3><?php esc_html_e('Registrar Pago Manual', 'alezux-members'); ?></h3>
                    <p><?php esc_html_e('Suscripción ID:', 'alezux-members'); ?> <span id="modal-sub-id"></span></p>
                    
                    <div class="alezux-form-group">
                        <label><?php esc_html_e('Monto ($)', 'alezux-members'); ?></label>
                        <input type="number" id="manual-pay-amount" step="0.01" placeholder="Ej: 50.00">
                    </div>

                    <div class="alezux-form-group">
                        <label><?php esc_html_e('Motivo / Nota', 'alezux-members'); ?></label>
                        <textarea id="manual-pay-note" placeholder="Ej: Transferencia Bancaria #1234"></textarea>
                    </div>

                    <button id="btn-confirm-manual-pay" class="alezux-btn-primary"><?php esc_html_e('Registrar Pago', 'alezux-members'); ?></button>
                </div>
            </div>

        </div>
        <?php
	}
}
