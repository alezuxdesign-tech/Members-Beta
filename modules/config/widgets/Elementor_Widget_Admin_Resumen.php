<?php
namespace Alezux_Members\Modules\Config\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Alezux_Members\Modules\Config\Includes\Admin_Dashboard_Stats;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Elementor_Widget_Admin_Resumen extends Widget_Base {

	public function get_name() {
		return 'alezux_admin_summary';
	}

	public function get_title() {
		return __( 'Admin: Resumen KPIs', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-dashboard';
	}

	public function get_categories() {
		return [ 'alezux-lms' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Contenido', 'alezux-members' ),
			]
		);

		$this->add_control(
			'show_total',
			[
				'label' => __( 'Mostrar Total Estudiantes', 'alezux-members' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

        $this->add_control(
			'show_new',
			[
				'label' => __( 'Mostrar Nuevos (30 días)', 'alezux-members' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

        $this->add_control(
			'show_online',
			[
				'label' => __( 'Mostrar Usuarios Online', 'alezux-members' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->end_controls_section();

        // Style Tab
        $this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Estilos', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_control(
			'card_bg_color',
			[
				'label' => __( 'Color de Fondo Cards', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'rgba(25, 25, 35, 0.6)', 
			]
		);

        $this->add_control(
			'text_color',
			[
				'label' => __( 'Color de Texto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
			]
		);
        
         $this->add_control(
			'accent_color',
			[
				'label' => __( 'Color Acento (Iconos)', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#4CAF50',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
        
        // Obtener Datps
        $total_students = Admin_Dashboard_Stats::get_total_students();
        $new_students   = Admin_Dashboard_Stats::get_new_students('month');
        $online_users   = Admin_Dashboard_Stats::get_online_users_count();

        ?>
        <div class="alezux-admin-kpi-grid">
            <?php if ( 'yes' === $settings['show_total'] ) : ?>
            <div class="alezux-kpi-card">
                <div class="kpi-icon">
                    <i class="eicon-person" aria-hidden="true"></i>
                </div>
                <div class="kpi-content">
                    <span class="kpi-label">Total Estudiantes</span>
                    <span class="kpi-value"><?php echo number_format( $total_students ); ?></span>
                </div>
            </div>
            <?php endif; ?>

            <?php if ( 'yes' === $settings['show_new'] ) : ?>
            <div class="alezux-kpi-card">
                <div class="kpi-icon">
                   <i class="eicon-plus-circle-o" aria-hidden="true"></i>
                </div>
                <div class="kpi-content">
                    <span class="kpi-label">Nuevos (Mes)</span>
                    <span class="kpi-value"><?php echo number_format( $new_students ); ?></span>
                     <span class="kpi-trend">+<?php echo number_format( $new_students ); ?> este mes</span>
                </div>
            </div>
            <?php endif; ?>

            <?php if ( 'yes' === $settings['show_online'] ) : ?>
            <div class="alezux-kpi-card online-status">
                <div class="kpi-icon pulse-icon">
                    <i class="eicon-wifi" aria-hidden="true"></i>
                </div>
                <div class="kpi-content">
                    <span class="kpi-label">Online Ahora</span>
                    <span class="kpi-value"><?php echo number_format( $online_users ); ?></span>
                    <span class="kpi-badge">En vivo</span>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <style>
            .alezux-admin-kpi-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
            }
            .alezux-kpi-card {
                background: <?php echo $settings['card_bg_color']; ?>;
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 16px;
                padding: 25px;
                display: flex;
                align-items: center;
                gap: 15px;
                transition: transform 0.3s ease;
                color: <?php echo $settings['text_color']; ?>;
            }
            .alezux-kpi-card:hover {
                transform: translateY(-5px);
                border-color: rgba(255, 255, 255, 0.2);
            }
            .kpi-icon {
                font-size: 28px;
                background: rgba(255, 255, 255, 0.05);
                width: 60px;
                height: 60px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 12px;
                color: <?php echo $settings['accent_color']; ?>;
            }
            .kpi-content {
                display: flex;
                flex-direction: column;
            }
            .kpi-label {
                font-size: 14px;
                opacity: 0.7;
                font-weight: 500;
            }
            .kpi-value {
                font-size: 28px;
                font-weight: 700;
                line-height: 1.2;
            }
            .kpi-trend {
                font-size: 11px;
                color: #4CAF50;
                margin-top: 2px;
            }
            .kpi-badge {
                display: inline-block;
                background: #4CAF50;
                color: white;
                font-size: 10px;
                padding: 2px 8px;
                border-radius: 10px;
                margin-top: 4px;
                align-self: flex-start;
            }
            
            /* Pulse Animation for Online */
            .online-status .pulse-icon {
                color: #4CAF50;
                position: relative;
            }
            .online-status .pulse-icon::after {
                content: '';
                position: absolute;
                width: 100%;
                height: 100%;
                border-radius: 12px;
                box-shadow: 0 0 0 0 rgba(76, 175, 80, 0.7);
                animation: pulse-green 2s infinite;
            }

            @keyframes pulse-green {
                0% {
                    transform: scale(0.95);
                    box-shadow: 0 0 0 0 rgba(76, 175, 80, 0.7);
                }
                70% {
                    transform: scale(1);
                    box-shadow: 0 0 0 10px rgba(76, 175, 80, 0);
                }
                100% {
                    transform: scale(0.95);
                    box-shadow: 0 0 0 0 rgba(76, 175, 80, 0);
                }
            }
        </style>
        <?php
	}
}
