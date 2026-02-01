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
		return [ 'alezux-sales-history-css' ];
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
        $limit = $settings['limit'];

        global $wpdb;
        $table_trans = $wpdb->prefix . 'alezux_finanzas_transactions';
        
        // Solo administradores deberian ver esto, pero por si acaso check
        // if ( ! current_user_can('manage_options') ) return;

        $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_trans ORDER BY created_at DESC LIMIT %d", $limit ) );

        if ( empty( $results ) ) {
            echo '<p>No se encontraron transacciones.</p>';
            return;
        }

        ?>
        <div class="alezux-sales-wrapper">
            <table class="alezux-sales-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Método</th>
                        <th>Monto</th>
                        <th>Ref</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $results as $row ) : 
                        $user_info = get_userdata( $row->user_id );
                        $user_name = $user_info ? $user_info->user_login : 'ID: ' . $row->user_id;
                    ?>
                        <tr>
                            <td><?php echo esc_html( $row->id ); ?></td>
                            <td><?php echo esc_html( $user_name ); ?></td>
                            <td><?php echo esc_html( ucfirst( $row->method ) ); ?></td>
                            <td><?php echo esc_html( $row->amount . ' ' . $row->currency ); ?></td>
                            <td><?php echo esc_html( $row->transaction_ref ); ?></td>
                            <td><span class="alezux-status-badge status-<?php echo esc_attr( $row->status ); ?>"><?php echo esc_html( $row->status ); ?></span></td>
                            <td><?php echo esc_html( $row->created_at ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
	}
}
