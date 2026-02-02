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
		return [ 'alezux-sales-history-css', 'alezux-subs-list-css' ]; 
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
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
        ?>
        <div class="alezux-subs-list-app">
            
            <div class="alezux-filter-bar" style="display:flex; justify-content:space-between; align-items:center;">
                <div class="alezux-filter-item search-item" style="width:100%;">
                     <div class="alezux-search-wrapper" style="position:relative;">
                        <span class="dashicons dashicons-search" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#718096;"></span>
                        <input type="text" id="alezux-subs-search" placeholder="Buscar por estudiante o plan..." style="padding-left:35px; width: 100%; max-width: 400px; background-color:#121620; border:1px solid #2d3748; color:#fff; border-radius:8px;">
                     </div>
                </div>
                <!-- Optional: Add Filters button placeholder if needed later -->
            </div>

            <div class="alezux-loading-subs" style="display:none; text-align:center; padding:20px; color: #a0aec0;">
                <i class="eicon-loading eicon-animation-spin"></i> Cargando suscripciones...
            </div>

            <div class="alezux-subs-wrapper">
                <table class="alezux-subs-table alezux-sales-table"> 
                    <thead>
                        <tr>
                            <th class="col-id">ID</th>
                            <th class="col-student">ESTUDIANTE</th>
                            <th class="col-plan">PLAN ACADÉMICO</th>
                            <th class="col-amount">MONTO</th>
                            <th class="col-status">ESTADO</th>
                            <th class="col-progress">PROGRESO</th>
                            <th class="col-next-payment">VENCIMIENTO</th>
                            <th class="col-actions">ACCIONES</th>
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
                    <h3>Registrar Pago Manual</h3>
                    <p>Suscripción ID: <span id="modal-sub-id"></span></p>
                    
                    <div class="alezux-form-group">
                        <label>Monto ($)</label>
                        <input type="number" id="manual-pay-amount" step="0.01" placeholder="Ej: 50.00">
                    </div>

                    <div class="alezux-form-group">
                        <label>Motivo / Nota</label>
                        <textarea id="manual-pay-note" placeholder="Ej: Transferencia Bancaria #1234"></textarea>
                    </div>

                    <button id="btn-confirm-manual-pay" class="alezux-btn-primary">Registrar Pago</button>
                </div>
            </div>

        </div>
        <?php
	}
}
