<?php
namespace Alezux_Members\Modules\Finanzas\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plans_List_Widget extends Widget_Base {

	public function get_name() {
		return 'alezux_plans_manager';
	}

	public function get_title() {
		return esc_html__( 'Gestor de Planes (Alezux)', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-price-table';
	}

	public function get_categories() {
		return [ 'alezux-members' ];
	}

	public function get_script_depends() {
		return [ 'alezux-plans-manager-js' ];
	}

    public function get_style_depends() {
		return [ 'alezux-sales-history-css' ]; // Reutilizar estilos de tabla
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
				'default' => 'plans',
				'options' => [
					'plans' => esc_html__( 'Gestor de Planes', 'alezux-members' ),
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
        
        // Obtener cursos para filtro (Select options)
        global $wpdb;
        $t_plans = $wpdb->prefix . 'alezux_finanzas_plans';
        $courses_ids = $wpdb->get_col("SELECT DISTINCT course_id FROM $t_plans");
        $courses = [];
        if ( ! empty( $courses_ids ) ) {
            foreach( $courses_ids as $cid ) {
                $c_title = get_the_title( $cid );
                if( $c_title ) $courses[ $cid ] = $c_title;
            }
        }
        ?>
        <div class="alezux-plans-manager-app">
            
            <div class="alezux-filter-bar">
                <div class="alezux-filter-item search-item">
                     <label>Buscar Plan</label>
                     <input type="text" id="alezux-plans-search" placeholder="Buscar por nombre...">
                </div>
                <div class="alezux-filter-item">
                     <label>Filtrar por Curso</label>
                     <select id="alezux-plans-course">
                         <option value="0">Todos</option>
                         <?php foreach($courses as $id => $title): ?>
                            <option value="<?php echo esc_attr($id); ?>"><?php echo esc_html($title); ?></option>
                         <?php endforeach; ?>
                     </select>
                </div>
            </div>

            <div class="alezux-loading-plans" style="display:none; text-align:center; padding:20px;">
                <i class="eicon-loading eicon-animation-spin"></i> Cargando planes...
            </div>

            <table class="alezux-plans-table alezux-sales-table"> <!-- Reutilizando estilos sales-table -->
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre del Plan</th>
                        <th>Curso Asociado</th>
                        <th>Precio</th>
                        <th>Cuotas</th>
                        <th>Frecuencia</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- AJAX Content -->
                </tbody>
            </table>

        </div>
        <?php
	}
}
