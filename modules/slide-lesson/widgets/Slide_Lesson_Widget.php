<?php
namespace Alezux_Members\Modules\Slide_Lesson\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Slide_Lesson_Widget extends Widget_Base {

	public function get_name() {
		return 'alezux-slide-lesson';
	}

	public function get_title() {
		return esc_html__( 'Slide Lesson', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-slides';
	}

	public function get_categories() {
		return [ 'basic' ]; // O crear una categoría propia 'alezux-members'
	}

	public function get_script_depends() {
		return [ 'alezux-slide-lesson-js' ]; // Asegúrate de registrar este script primero
	}

	public function get_style_depends() {
		return [ 'alezux-slide-lesson-css' ]; // Asegúrate de registrar este estilo primero
	}

	protected function register_controls() {
		// --- Sección de Contenido ---
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Contenido', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'limit',
			[
				'label' => esc_html__( 'Límite de Lecciones', 'alezux-members' ),
				'type' => Controls_Manager::NUMBER,
				'min' => -1,
				'max' => 50,
				'step' => 1,
				'default' => -1,
				'description' => esc_html__( 'Número de lecciones a mostrar (-1 para todas).', 'alezux-members' ),
			]
		);

		$this->end_controls_section();

		// --- Sección de Estilo: Contenedor Slider ---
		$this->start_controls_section(
			'section_style_container',
			[
				'label' => esc_html__( 'Contenedor Slider', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'container_padding',
			[
				'label' => esc_html__( 'Relleno (Padding)', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-slide-lesson-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'container_background',
				'label' => esc_html__( 'Fondo', 'alezux-members' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .alezux-slide-lesson-container',
			]
		);

		$this->end_controls_section();

		// --- Sección de Estilo: Tarjeta (Card) ---
		$this->start_controls_section(
			'section_style_card',
			[
				'label' => esc_html__( 'Tarjeta de Lección', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'card_width',
			[
				'label' => esc_html__( 'Ancho de Tarjeta', 'alezux-members' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 500,
						'step' => 5,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 300,
				],
				'selectors' => [
					'{{WRAPPER}} .alezux-slide-item' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'card_border_radius',
			[
				'label' => esc_html__( 'Radio del Borde', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-slide-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'card_border',
				'label' => esc_html__( 'Borde', 'alezux-members' ),
				'selector' => '{{WRAPPER}} .alezux-slide-item',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'card_box_shadow',
				'label' => esc_html__( 'Sombra de Caja', 'alezux-members' ),
				'selector' => '{{WRAPPER}} .alezux-slide-item',
			]
		);
		
		$this->add_control(
			'card_bg_color',
			[
				'label' => esc_html__( 'Color de Fondo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-slide-item' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// --- Sección de Estilo: Título ---
		$this->start_controls_section(
			'section_style_title',
			[
				'label' => esc_html__( 'Título', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Color del Texto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-slide-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => esc_html__( 'Tipografía', 'alezux-members' ),
				'selector' => '{{WRAPPER}} .alezux-slide-title',
			]
		);
		
		$this->add_control(
			'title_bg_gradient',
			[
				'label' => esc_html__( 'Fondo (Gradiente)', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'description' => 'Color base para la superposición del titulo',
				'selectors' => [
					'{{WRAPPER}} .alezux-slide-content' => 'background: linear-gradient(to top, {{VALUE}} 0%, rgba(0,0,0,0) 100%);',
				],
			]
		);

		$this->end_controls_section();

		// --- Sección de Estilo: Botones de Navegación ---
		$this->start_controls_section(
			'section_style_nav',
			[
				'label' => esc_html__( 'Botones de Navegación', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'nav_color',
			[
				'label' => esc_html__( 'Color del Icono', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-slide-nav' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'nav_bg_color',
			[
				'label' => esc_html__( 'Color de Fondo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-slide-nav' => 'background-color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'nav_border_radius',
			[
				'label' => esc_html__( 'Radio del Borde', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-slide-nav' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_control(
			'nav_size',
			[
				'label' => esc_html__( 'Tamaño del Botón', 'alezux-members' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .alezux-slide-nav' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$limit = $settings['limit'];

		// Lógica duplicada para asegurar funcionamiento independiente
		// Idealmente, esto debería estar en un Helper o Service
		$args = [
			'post_type'      => 'sfwd-lessons',
			'posts_per_page' => intval( $limit ),
			'post_status'    => 'publish',
		];

		$query = new \WP_Query( $args );
		$lessons = [];

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$lessons[] = [
					'title'     => get_the_title(),
					'permalink' => get_permalink(),
					'image_url' => get_the_post_thumbnail_url( get_the_ID(), 'full' ),
				];
			}
			wp_reset_postdata();
		}

		// Reutilizar la vista existente
		// Necesitamos la ruta absoluta a la vista. 
		// Como estamos en modules/slide-lesson/widgets/Slide_Lesson_Widget.php
		// La vista está en ../views/slide-lesson.php
		$view_path = plugin_dir_path( __DIR__ ) . 'views/slide-lesson.php';
		
		if ( file_exists( $view_path ) ) {
			include $view_path;
		} else {
			echo 'View definition not found';
		}
	}
}
