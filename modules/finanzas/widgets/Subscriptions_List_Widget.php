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
            
            <div class="alezux-filter-bar">
                <div class="alezux-filter-item search-item">
                     <label>Buscar Suscripción</label>
                     <input type="text" id="alezux-subs-search" placeholder="Nombre o email...">
                </div>
            </div>

            <div class="alezux-loading-subs" style="display:none; text-align:center; padding:20px;">
                <i class="eicon-loading eicon-animation-spin"></i> Cargando suscripciones...
            </div>

            <div class="alezux-subs-wrapper">
                <table class="alezux-subs-table alezux-sales-table"> 
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Estudiante</th>
                            <th>Plan</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Progreso</th>
                            <th>Próximo Pago</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- AJAX Content -->
                    </tbody>
                </table>
            </div>

        </div>
        <?php
	}
}
