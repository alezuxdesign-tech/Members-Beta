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
			'limit',
			[
				'label' => esc_html__( 'Límite', 'alezux-members' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 20,
			]
		);

		$this->end_controls_section();

        // Estilos
        $this->start_controls_section(
			'style_section',
			[
				'label' => esc_html__( 'Estilo Tabla', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
        
        // Similar controls to Sales History can be added here
        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'table_typography',
				'selector' => '{{WRAPPER}} .alezux-subs-table',
			]
		);

        $this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
        $limit = $settings['limit'];

        global $wpdb;
        $subs_table = $wpdb->prefix . 'alezux_finanzas_subscriptions';
        $plans_table = $wpdb->prefix . 'alezux_finanzas_plans';
        
        $results = $wpdb->get_results( $wpdb->prepare( "
            SELECT s.*, p.name as plan_name 
            FROM $subs_table s 
            LEFT JOIN $plans_table p ON s.plan_id = p.id 
            ORDER BY s.id DESC LIMIT %d", $limit 
        ) );

        if ( empty( $results ) ) {
            echo '<p>No hay suscripciones activas.</p>';
            return;
        }

        ?>
        <div class="alezux-subs-wrapper">
            <table class="alezux-subs-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Plan</th>
                        <th>Estado</th>
                        <th>Cuotas</th>
                        <th>Próximo Pago</th>
                        <th>Stripe ID</th>
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
                            <td><?php echo esc_html( $row->plan_name ); ?></td>
                            <td><span class="alezux-status-badge status-<?php echo esc_attr( $row->status ); ?>"><?php echo esc_html( ucfirst( $row->status ) ); ?></span></td>
                            <td><?php echo esc_html( $row->quotas_paid ); ?></td>
                            <td><?php echo esc_html( $row->next_payment_date ); ?></td>
                            <td><?php echo esc_html( $row->stripe_subscription_id ?: 'Manual' ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
	}
}
