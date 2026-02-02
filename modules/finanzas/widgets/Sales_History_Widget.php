<?php
namespace Alezux_Members\Modules\Finanzas\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Sales_History_Widget extends Widget_Base {

	public function get_name() {
		return 'alezux_sales_history';
	}

	public function get_title() {
		return esc_html__( 'Historial de Ventas (Alezux)', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-price-table';
	}

	public function get_categories() {
		return [ 'alezux-members' ];
	}

    public function get_style_depends() {
		return [ 'alezux-finanzas-tables-css' ];
	}

    public function get_script_depends() {
		return [ 'alezux-sales-history-js' ];
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
			'table_title',
			[
				'label' => esc_html__( 'Título de la Tabla', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Historial de Ventas', 'alezux-members' ),
                'placeholder' => esc_html__( 'Escribe un título', 'alezux-members' ),
			]
		);

        $this->add_control(
			'table_description',
			[
				'label' => esc_html__( 'Descripción', 'alezux-members' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Registro de todas las transacciones.', 'alezux-members' ),
                'placeholder' => esc_html__( 'Escribe una descripción', 'alezux-members' ),
			]
		);

        $this->add_control(
			'limit',
			[
				'label' => esc_html__( 'Límite de Registros', 'alezux-members' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 5,
				'max' => 100,
				'step' => 5,
				'default' => 20,
			]
		);

		$this->end_controls_section();

        // Estilos Tabla
        $this->start_controls_section(
			'style_table_section',
			[
				'label' => esc_html__( 'Tabla General', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_control(
			'table_width',
			[
				'label' => esc_html__( 'Ancho', 'alezux-members' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'selectors' => [
					'{{WRAPPER}} .alezux-sales-table' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'table_border',
				'label' => esc_html__( 'Borde Tabla', 'alezux-members' ),
				'selector' => '{{WRAPPER}} .alezux-sales-table',
			]
		);

        $this->end_controls_section();

        // Cabecera
        $this->start_controls_section(
			'style_header_section',
			[
				'label' => esc_html__( 'Cabecera (TH)', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_control(
			'header_bg_color',
			[
				'label' => esc_html__( 'Color de Fondo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-sales-table th' => 'background-color: {{VALUE}}',
				],
			]
		);

        $this->add_control(
			'header_text_color',
			[
				'label' => esc_html__( 'Color de Texto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-sales-table th' => 'color: {{VALUE}}',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'header_typography',
				'selector' => '{{WRAPPER}} .alezux-sales-table th',
			]
		);

        $this->end_controls_section();

        // Celdas
        $this->start_controls_section(
			'style_cell_section',
			[
				'label' => esc_html__( 'Celdas (TD)', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_control(
			'cell_text_color',
			[
				'label' => esc_html__( 'Color de Texto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-sales-table td' => 'color: {{VALUE}}',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cell_typography',
				'selector' => '{{WRAPPER}} .alezux-sales-table td',
			]
		);

        $this->add_control(
			'row_zebra',
			[
				'label' => esc_html__( 'Color Filas Pares', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-sales-table tr:nth-child(even)' => 'background-color: {{VALUE}}',
				],
			]
		);

        $this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings_for_display();
        
        // Obtener cursos para el filtro
        global $wpdb;
        $t_plans = $wpdb->prefix . 'alezux_finanzas_plans';
        $courses_ids = $wpdb->get_col("SELECT DISTINCT course_id FROM $t_plans");
        $courses = [];
        
        if ( ! empty( $courses_ids ) ) {
            foreach( $courses_ids as $cid ) {
                $c_title = get_the_title( $cid );
                if( $c_title ) {
                    $courses[ $cid ] = $c_title;
                }
            }
        }
        ?>
        <div class="alezux-finanzas-app alezux-sales-app">
            
            <!-- Cabecera Estándar -->
            <div class="alezux-table-header">
                <div class="alezux-header-left">
                    <h3 class="alezux-table-title"><?php echo esc_html($settings['table_title']); ?></h3>
                    <?php if ( ! empty( $settings['table_description'] ) ) : ?>
                        <p class="alezux-table-desc"><?php echo esc_html($settings['table_description']); ?></p>
                    <?php endif; ?>
                </div>

                <div class="alezux-header-right" style="flex-wrap: wrap; justify-content: flex-end;">
                     <!-- Search -->
                    <div class="alezux-filter-item search-item">
                         <div class="alezux-search-wrapper">
                            <span class="dashicons dashicons-search"></span>
                            <input type="text" id="alezux-sales-search" class="alezux-table-search-input" placeholder="Buscar transacción...">
                         </div>
                    </div>
                </div>
            </div>
            
            <!-- Barra de Filtros Secundaria (Debajo del título para no saturar header) -->
            <div class="alezux-filters-secondary">
                
                <div class="alezux-filter-item">
                    <label>Curso</label>
                    <select id="alezux-filter-course">
                        <option value="0">Todos</option>
                        <?php foreach($courses as $id => $title): ?>
                            <option value="<?php echo esc_attr($id); ?>"><?php echo esc_html($title); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="alezux-filter-item">
                    <label>Estado</label>
                    <select id="alezux-filter-status">
                            <option value="">Todos</option>
                            <option value="succeeded">Completado</option>
                            <option value="pending">Pendiente</option>
                            <option value="failed">Fallido</option>
                            <option value="refunded">Reembolsado</option>
                    </select>
                </div>

                <div class="alezux-filter-item" style="max-width: 100px;">
                    <label>Filas</label>
                    <select id="alezux-limit-select">
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

            </div>

            <!-- Loading -->
            <div class="alezux-loading">
                <i class="eicon-loading eicon-animation-spin"></i> Cargando transacciones...
            </div>

            <!-- Tabla Container -->
            <div class="alezux-table-wrapper">
                <table class="alezux-finanzas-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Alumno</th>
                            <th>Método</th>
                            <th>Monto</th>
                            <th>Curso / Plan</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Content via AJAX -->
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="alezux-pagination"></div>

        </div>
        <?php
	}
}
