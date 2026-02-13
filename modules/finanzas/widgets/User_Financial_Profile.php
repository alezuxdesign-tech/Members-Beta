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

            <!-- MODAL PERSONALIZADO (Vanilla JS - Fuera de Alpine) -->
            <div id="alezux-profile-modal-<?php echo $this->get_id(); ?>" 
                 class="alezux-modal-overlay"
                 style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; width: 100vw !important; height: 100vh !important; z-index: 999999 !important; display: none !important; align-items: center !important; justify-content: center !important; background-color: rgba(0,0,0,0.5) !important;">
                
                <!-- Backdrop Click Handler -->
                <div id="alezux-modal-backdrop-<?php echo $this->get_id(); ?>" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0;"></div>

                <!-- Modal Content -->
                <div class="alezux-modal-content"
                     style="background-color: #ffffff !important; color: #1f2937 !important; border-radius: 0.5rem !important; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important; max-width: 28rem !important; width: 100% !important; margin: 1rem !important; position: relative !important; padding: 1.5rem !important; z-index: 1000000 !important;">
                    
                    <div style="text-align: center !important;">
                        <div id="alezux-modal-icon-container-<?php echo $this->get_id(); ?>"
                             style="margin-left: auto; margin-right: auto; display: flex; align-items: center; justify-content: center; height: 3rem; width: 3rem; border-radius: 9999px; margin-bottom: 1rem;">
                            <i id="alezux-modal-icon-<?php echo $this->get_id(); ?>" class="fas text-xl"></i>
                        </div>
                        <h3 id="alezux-modal-title-<?php echo $this->get_id(); ?>" style="font-size: 1.125rem; line-height: 1.75rem; font-weight: 500; color: #111827 !important; margin-top: 0;"></h3>
                        <div style="margin-top: 0.5rem;">
                            <p id="alezux-modal-message-<?php echo $this->get_id(); ?>" style="font-size: 0.875rem; line-height: 1.25rem; color: #6b7280 !important;"></p>
                        </div>
                    </div>

                    <div style="margin-top: 1.25rem; display: grid; gap: 0.75rem; grid-template-columns: repeat(1, minmax(0, 1fr));">
                        <!-- Botón Confirmar -->
                        <button type="button" 
                                id="alezux-modal-confirm-btn-<?php echo $this->get_id(); ?>"
                                style="width: 100%; display: inline-flex; justify-content: center; border-radius: 0.375rem; border: 1px solid transparent; padding: 0.5rem 1rem; font-size: 1rem; line-height: 1.5rem; font-weight: 500; color: #ffffff !important; cursor: pointer; transition: background-color 0.15s ease-in-out;">
                            <span id="alezux-modal-confirm-text-<?php echo $this->get_id(); ?>"></span>
                        </button>
                        
                        <!-- Botón Cancelar -->
                        <button type="button" 
                                id="alezux-modal-cancel-btn-<?php echo $this->get_id(); ?>"
                                style="margin-top: 0.75rem; width: 100%; display: inline-flex; justify-content: center; border-radius: 0.375rem; border: 1px solid #d1d5db; padding: 0.5rem 1rem; font-size: 1rem; line-height: 1.5rem; font-weight: 500; color: #374151 !important; background-color: #ffffff !important; cursor: pointer; transition: background-color 0.15s ease-in-out;">
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
                    // Teleport Modal to Body manually (robust fallback)
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

                // Modal Logic - Pure Vanilla JS
                openModal(title, message, type = 'info', confirmText = 'Aceptar', showCancel = false, onConfirm = null) {
                    console.log('Opening Modal:', title); // DEBUG
                    const modalId = 'alezux-profile-modal-<?php echo $this->get_id(); ?>';
                    const modalEl = document.getElementById(modalId);
                    
                    if (!modalEl) {
                        console.error('Modal element not found:', modalId); // DEBUG
                        return;
                    }

                    // Update Content
                    document.getElementById('alezux-modal-title-<?php echo $this->get_id(); ?>').innerText = title;
                    document.getElementById('alezux-modal-message-<?php echo $this->get_id(); ?>').innerText = message;
                    document.getElementById('alezux-modal-confirm-text-<?php echo $this->get_id(); ?>').innerText = confirmText;

                    // Update Style based on Type
                    const iconContainer = document.getElementById('alezux-modal-icon-container-<?php echo $this->get_id(); ?>');
                    const icon = document.getElementById('alezux-modal-icon-<?php echo $this->get_id(); ?>');
                    const confirmBtn = document.getElementById('alezux-modal-confirm-btn-<?php echo $this->get_id(); ?>');

                    // Reset classes
                    icon.className = 'fas text-xl';

                    if (type === 'error') {
                        iconContainer.style.backgroundColor = '#fee2e2';
                        icon.classList.add('fa-exclamation-triangle');
                        icon.style.color = '#dc2626';
                        confirmBtn.style.backgroundColor = '#dc2626';
                    } else if (type === 'success') {
                        iconContainer.style.backgroundColor = '#dcfce7';
                        icon.classList.add('fa-check');
                        icon.style.color = '#16a34a';
                        confirmBtn.style.backgroundColor = '#16a34a';
                    } else {
                        iconContainer.style.backgroundColor = '#dbeafe';
                        icon.classList.add('fa-info-circle');
                        icon.style.color = '#2563eb';
                        confirmBtn.style.backgroundColor = '#2563eb';
                    }

                    // Show/Hide Cancel Button
                    const cancelBtn = document.getElementById('alezux-modal-cancel-btn-<?php echo $this->get_id(); ?>');
                    cancelBtn.style.setProperty('display', showCancel ? 'inline-flex' : 'none', 'important');

                    // Assign Click Handlers
                    this._onConfirm = onConfirm;
                    
                    // Simple confirm handler
                    confirmBtn.onclick = () => {
                        console.log('Confirm clicked'); // DEBUG
                        if (this._onConfirm) {
                            this._onConfirm();
                        } else {
                            this.closeModal();
                        }
                    };

                    // Cancel Handlers
                    cancelBtn.onclick = () => this.closeModal();
                    document.getElementById('alezux-modal-backdrop-<?php echo $this->get_id(); ?>').onclick = () => {
                        if (showCancel) this.closeModal();
                    };

                    // Show Modal
                    console.log('Setting display to flex'); // DEBUG
                    modalEl.style.setProperty('display', 'flex', 'important');
                },

                closeModal() {
                    console.log('Closing Modal'); // DEBUG
                    const modalId = 'alezux-profile-modal-<?php echo $this->get_id(); ?>';
                    const modalEl = document.getElementById(modalId);
                    if (modalEl) {
                        modalEl.style.setProperty('display', 'none', 'important');
                    }
                    this._onConfirm = null;
                },

                confirmPayment(subscriptionId) {
                    console.log('Confirm payment called for:', subscriptionId); // DEBUG
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
                    console.log('Processing payment for:', subscriptionId); // DEBUG
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
