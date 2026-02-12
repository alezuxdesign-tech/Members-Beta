<?php
namespace Alezux_Members\Modules\Finanzas\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Alezux_Members\Core\Core;

// use Elementor\Group_Control_Box_Shadow; // Already imported above

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

        // 1. DISEÑO DE LA TABLA (Tabla y Encabezados)
        $this->start_controls_section(
            'style_section_table',
            [
                'label' => esc_html__('Diseño de la Tabla', 'alezux-members'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'table_container_heading',
            [
                'label' => esc_html__('Contenedor & Cuerpo', 'alezux-members'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'table_background',
                'label' => esc_html__('Fondo Tabla', 'alezux-members'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .alezux-finanzas-app, {{WRAPPER}} .alezux-table-wrapper',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'table_border',
                'label' => esc_html__('Borde Tabla', 'alezux-members'),
                'selector' => '{{WRAPPER}} .alezux-finanzas-app',
            ]
        );

        $this->add_control(
            'table_radius',
            [
                'label' => esc_html__('Radio de Borde', 'alezux-members'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .alezux-finanzas-app' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->add_control(
            'table_row_bg_general',
            [
                'label' => esc_html__('Fondo Filas (General)', 'alezux-members'),
                'type' => Controls_Manager::COLOR,
                 'selectors' => [
                    '{{WRAPPER}} .alezux-finanzas-table tbody tr' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .alezux-finanzas-table tbody td' => 'background-color: {{VALUE}};',
                ],
            ]
        );

         $this->add_control(
            'table_row_bg',
            [
                'label' => esc_html__('Fondo Filas (Alterno)', 'alezux-members'),
                'type' => Controls_Manager::COLOR,
                 'selectors' => [
                    '{{WRAPPER}} .alezux-finanzas-table tbody tr:nth-child(even)' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'table_header_heading',
            [
                'label' => esc_html__('Encabezados (Títulos)', 'alezux-members'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'header_bg_color',
            [
                'label' => esc_html__('Color Fondo Encabezado', 'alezux-members'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .alezux-finanzas-table thead th' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'header_text_color',
            [
                'label' => esc_html__('Color Texto Encabezado', 'alezux-members'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .alezux-finanzas-table thead th' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'header_typography',
                'selector' => '{{WRAPPER}} .alezux-finanzas-table thead th',
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
			
            <!-- ACTIVIDAD FINANCIERA UNIFICADA -->
            <div class="alezux-finanzas-app">
                <div class="alezux-financial-section mb-8">
                    <h3 class="alezux-financial-title text-xl font-bold mb-4"><?php echo esc_html( $settings['title_status'] ); ?></h3>
                    
                    <div x-show="loading" class="alezux-loading p-4 text-center">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Cargando información...
                    </div>

                    <div x-show="!loading && subscriptions.length === 0 && transactions.length === 0" class="p-4 bg-gray-50 rounded text-center text-gray-500">
                        No tienes actividad financiera registrada.
                    </div>

                    <div x-show="!loading && (subscriptions.length > 0 || transactions.length > 0)" class="alezux-table-wrapper">
                        <table class="alezux-finanzas-table w-full text-sm text-left">
                            <thead>
                                <tr>
                                    <th scope="col" class="px-6 py-3">Concepto</th>
                                    <th scope="col" class="px-6 py-3">Monto</th>
                                    <th scope="col" class="px-6 py-3">Estado</th>
                                    <th scope="col" class="px-6 py-3">Progreso / Detalle</th>
                                    <th scope="col" class="px-6 py-3">Fecha</th>
                                    <th scope="col" class="px-6 py-3">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Suscripciones Activas (Primero) -->
                                <template x-for="sub in subscriptions" :key="'sub-'+sub.id">
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
                                            <div class="text-xs text-gray-500"><i class="far fa-clock mr-1"></i> Vencimiento:</div>
                                            <div x-text="sub.next_payment_date_formatted"></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <template x-if="sub.can_pay_manually">
                                                <button @click="confirmPayment(sub.id)" 
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

                                <!-- Historial de Transacciones (Después) -->
                                <template x-for="trans in transactions" :key="'trans-'+trans.id">
                                    <tr class="bg-gray-50 border-b hover:bg-gray-100">
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-gray-700">Pago de Cuota</div>
                                            <div class="text-xs text-gray-500">Transacción ID: <span x-text="trans.id"></span></div>
                                        </td>
                                        <td class="px-6 py-4" x-text="trans.formatted_amount"></td>
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
                                        <td class="px-6 py-4 text-center text-gray-400">-</td>
                                        <td class="px-6 py-4">
                                            <div x-text="trans.date_formatted"></div>
                                        </td>
                                        <td class="px-6 py-4 text-center text-gray-400">
                                            <span class="text-xs text-green-600" x-show="trans.status === 'succeeded'"><i class="fas fa-check-double"></i></span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- MODAL PERSONALIZADO (Teleportado) -->
            <div id="alezux-profile-modal-<?php echo $this->get_id(); ?>" x-show="showModal" class="fixed inset-0 z-[999999] flex items-center justify-center overflow-y-auto" style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; z-index: 999999 !important; display: none;">
                <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" style="position: fixed; inset: 0; background-color: rgba(0,0,0,0.5);" @click="if(showCancelButton) closeModal()"></div>
                <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full m-4 relative p-6 z-50">
                    
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4"
                             :class="{ 'bg-red-100': modalType === 'error', 'bg-green-100': modalType === 'success', 'bg-blue-100': modalType === 'info' }">
                            <i class="fas text-xl" :class="{ 
                                'fa-exclamation-triangle text-red-600': modalType === 'error', 
                                'fa-check text-green-600': modalType === 'success', 
                                'fa-info-circle text-blue-600': modalType === 'info' 
                            }"></i>
                        </div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900" x-text="modalTitle"></h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500" x-text="modalMessage"></p>
                        </div>
                    </div>

                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button type="button" 
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none sm:col-start-2 sm:text-sm"
                                :class="{ 'bg-red-600 hover:bg-red-700': modalType === 'error', 'bg-green-600 hover:bg-green-700': modalType === 'success', 'bg-blue-600 hover:bg-blue-700': modalType === 'info' }"
                                @click="handleConfirm()">
                            <span x-text="modalConfirmText"></span>
                        </button>
                        <button type="button" 
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:col-start-1 sm:text-sm"
                                @click="closeModal()"
                                x-show="showCancelButton">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>

		</div>

        <script>
        function alezuxFinancialProfile() {
            return {
                loading: true,
                subscriptions: [],
                transactions: [],
                
                // Modal State
                showModal: false,
                modalTitle: '',
                modalMessage: '',
                modalType: 'info', // info, success, error
                modalConfirmText: 'Aceptar',
                showCancelButton: false,
                onConfirmAction: null,

                initData() {
                    // Teleport Modal to Body to avoid stacking context issues
                    // But first check if it's already there to avoid duplicates on re-init
                    const modalId = 'alezux-profile-modal-<?php echo $this->get_id(); ?>';
                    const modalEl = document.getElementById(modalId);
                    if (modalEl && modalEl.parentElement !== document.body) {
                        document.body.appendChild(modalEl);
                    }

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

                // Modal Helpers
                openModal(title, message, type = 'info', confirmText = 'Aceptar', showCancel = false, onConfirm = null) {
                    this.modalTitle = title;
                    this.modalMessage = message;
                    this.modalType = type;
                    this.modalConfirmText = confirmText;
                    this.showCancelButton = showCancel;
                    this.onConfirmAction = onConfirm;
                    this.showModal = true;
                },

                closeModal() {
                    this.showModal = false;
                    this.onConfirmAction = null;
                },

                handleConfirm() {
                    if (this.onConfirmAction) {
                        this.onConfirmAction();
                    } else {
                        this.closeModal();
                    }
                },

                confirmPayment(subscriptionId) {
                    this.openModal(
                        'Confirmar Pago',
                        '¿Deseas generar el enlace de pago para la próxima cuota pendiente?',
                        'info',
                        'Sí, Pagar',
                        true,
                        () => this.processPayment(subscriptionId)
                    );
                },

                processPayment(subscriptionId) {
                    this.loading = true;
                    this.closeModal(); // Close confirmation modal
                    
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
                        // this.loading = false; // Don't stop loading if redirecting
                        if (data.success) {
                            window.location.href = data.data.url;
                        } else {
                            this.loading = false;
                            this.openModal('Error', data.data || 'Ocurrió un error desconocido.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.loading = false;
                        this.openModal('Error de Conexión', 'No se pudo conectar con el servidor.', 'error');
                    });
                }
            }
        }
        </script>
		<?php
	}
}
