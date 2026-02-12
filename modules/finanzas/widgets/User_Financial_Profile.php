<?php
namespace Alezux_Members\Modules\Finanzas\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Alezux_Members\Core\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class User_Financial_Profile extends Widget_Base {

	public function get_name() {
		return 'alezux_user_financial_profile';
	}

	public function get_title() {
		return esc_html__( 'Perfil Financiero de Usuario', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-price-table';
	}

	public function get_categories() {
		return [ 'alezux-lms' ];
	}

	public function get_script_depends() {
		return [ 'alezux-finanzas-frontend' ];
	}

	public function get_style_depends() {
		return [ 'alezux-finanzas-tables-css' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Contenido', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title_status',
			[
				'label'   => esc_html__( 'Título Estado de Cuenta', 'alezux-members' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Estado de Cuenta', 'alezux-members' ),
			]
		);

		$this->add_control(
			'title_history',
			[
				'label'   => esc_html__( 'Título Historial', 'alezux-members' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Historial de Pagos', 'alezux-members' ),
			]
		);

		$this->end_controls_section();

		// -- ESTILOS --
		$this->start_controls_section(
			'style_section_container',
			[
				'label' => esc_html__( 'Contenedor', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'container_bg_color',
			[
				'label'     => esc_html__( 'Color de Fondo', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-financial-widget' => 'background-color: {{VALUE}};',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'container_border',
				'label'    => esc_html__( 'Borde', 'alezux-members' ),
				'selector' => '{{WRAPPER}} .alezux-financial-widget',
			]
		);

        $this->add_control(
			'container_padding',
			[
				'label'      => esc_html__( 'Relleno', 'alezux-members' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .alezux-financial-widget' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

        // Estilos de botones
        $this->start_controls_section(
			'style_section_buttons',
			[
				'label' => esc_html__( 'Botones', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_control(
			'pay_button_color',
			[
				'label'     => esc_html__( 'Color Botón Pagar', 'alezux-members' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#28a745',
				'selectors' => [
					'{{WRAPPER}} .alezux-btn-pay' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
				],
			]
		);

        $this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
        
        // Verificar login
        if ( ! is_user_logged_in() ) {
            echo '<div class="alezux-alert alezux-alert-warning">Debes iniciar sesión para ver tu estado financiero.</div>';
            return;
        }

		?>
		<div class="alezux-financial-widget" 
             x-data="alezuxFinancialProfile()" 
             x-init="initData()">
			
            <!-- SECCIÓN 1: ESTADO DE CUENTA -->
            <div class="alezux-financial-section mb-8">
                <h3 class="alezux-financial-title text-xl font-bold mb-4"><?php echo esc_html( $settings['title_status'] ); ?></h3>
                
                <div x-show="loading" class="alezux-loading p-4 text-center">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Cargando información...
                </div>

                <div x-show="!loading && subscriptions.length === 0" class="p-4 bg-gray-50 rounded text-center text-gray-500">
                    No tienes suscripciones activas.
                </div>

                <div x-show="!loading && subscriptions.length > 0" class="alezux-table-wrapper">
                    <table class="alezux-finanzas-table w-full text-sm text-left">
                        <thead>
                            <tr>
                                <th scope="col" class="px-6 py-3">Plan Académico</th>
                                <th scope="col" class="px-6 py-3">Monto</th>
                                <th scope="col" class="px-6 py-3">Estado</th>
                                <th scope="col" class="px-6 py-3">Progreso</th>
                                <th scope="col" class="px-6 py-3">Vencimiento</th>
                                <th scope="col" class="px-6 py-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="sub in subscriptions" :key="sub.id">
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900" x-text="sub.plan_name"></div>
                                        <div class="text-xs text-gray-500" x-text="sub.plan_quotas + ' CUOTAS'"></div>
                                    </td>
                                    <td class="px-6 py-4 font-semibold" x-text="sub.formatted_price"></td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs rounded-full font-semibold"
                                              :class="{
                                                  'bg-green-100 text-green-800': sub.status === 'active' || sub.status === 'completed',
                                                  'bg-red-100 text-red-800': sub.status === 'past_due' || sub.status === 'unpaid',
                                                  'bg-gray-100 text-gray-800': sub.status === 'canceled'
                                              }"
                                              x-text="translateStatus(sub.status)">
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-1 w-32">
                                            <span class="text-xs text-gray-600" x-text="sub.progress_text + ' PAGADOS'"></span>
                                            <div class="w-full bg-gray-200 rounded-full h-1.5 dark:bg-gray-700">
                                                <div class="bg-green-500 h-1.5 rounded-full" :style="'width: ' + sub.progress_percent + '%'"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div x-text="sub.next_payment_date_formatted"></div>
                                        <div class="text-xs text-gray-500" x-show="sub.is_active">
                                           <i class="far fa-calendar-alt mr-1"></i>Cuota #<span x-text="parseInt(sub.quotas_paid) + 1"></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <template x-if="sub.can_pay_manually">
                                            <button @click="payInstallment(sub.id)" 
                                                    class="alezux-btn-pay text-white px-3 py-1.5 text-xs rounded hover:opacity-90 transition-opacity whitespace-nowrap">
                                                Pagar Cuota
                                            </button>
                                        </template>
                                        <template x-if="!sub.can_pay_manually && sub.status === 'completed'">
                                            <span class="text-green-600 font-bold text-xs"><i class="fas fa-check"></i> Pagado</span>
                                        </template>
                                        <template x-if="!sub.can_pay_manually && sub.status !== 'completed'">
                                            <span class="text-gray-400 text-xs">-</span>
                                        </template>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- SECCIÓN 2: HISTORIAL -->
            <div class="alezux-financial-section mt-8 alezux-finanzas-app">

                
                <div class="alezux-table-wrapper">
                    <table class="alezux-finanzas-table w-full text-sm text-left">
                        <thead>
                            <tr>
                                <th scope="col" class="px-6 py-3">Fecha</th>
                                <th scope="col" class="px-6 py-3">Concepto</th>
                                <th scope="col" class="px-6 py-3">Monto</th>
                                <th scope="col" class="px-6 py-3">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-show="transactions.length === 0">
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center">No hay transacciones recientes.</td>
                                </tr>
                            </template>
                            <template x-for="trans in transactions" :key="trans.id">
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4" x-text="trans.date_formatted"></td>
                                    <td class="px-6 py-4">Pago de cuota</td>
                                    <td class="px-6 py-4 font-bold" x-text="trans.formatted_amount"></td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded text-xs"
                                              :class="{
                                                  'bg-green-100 text-green-800': trans.status === 'succeeded',
                                                  'bg-yellow-100 text-yellow-800': trans.status === 'pending',
                                                  'bg-red-100 text-red-800': trans.status === 'failed'
                                              }"
                                              x-text="trans.status_label">
                                        </span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

		</div>

        <script>
        function alezuxFinancialProfile() {
            return {
                loading: true,
                subscriptions: [],
                transactions: [],
                
                initData() {
                    const formData = new FormData();
                    formData.append('action', 'alezux_get_my_financial_data');
                    formData.append('nonce', '<?php echo wp_create_nonce( "alezux_finanzas_nonce" ); ?>');

                    fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.loading = false;
                        if (data.success) {
                            this.subscriptions = data.data.subscriptions;
                            this.transactions = data.data.transactions;
                        } else {
                            console.error('Error fetching financial data:', data);
                        }
                    })
                    .catch(error => {
                        this.loading = false;
                        console.error('Fetch error:', error);
                    });
                },

                translateStatus(status) {
                    const statuses = {
                        'active': 'Activo',
                        'past_due': 'Pago Pendiente',
                        'unpaid': 'Impago',
                        'canceled': 'Cancelado',
                        'incomplete': 'Incompleto',
                        'incomplete_expired': 'Expirado',
                        'trialing': 'Prueba',
                        'completed': 'Completado'
                    };
                    return statuses[status] || status;
                },

                payInstallment(subscriptionId) {
                    if (!confirm('¿Deseas proceder con el pago de la cuota pendiente?')) return;
                    
                    this.loading = true;
                    const formData = new FormData();
                    formData.append('action', 'alezux_create_installment_checkout');
                    formData.append('subscription_id', subscriptionId);
                    formData.append('nonce', '<?php echo wp_create_nonce( "alezux_finanzas_nonce" ); ?>');

                    fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = data.data.url;
                        } else {
                            alert('Error: ' + (data.data || 'Desconocido'));
                            this.loading = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error de conexión.');
                        this.loading = false;
                    });
                }
            }
        }
        </script>
		<?php
	}
}
