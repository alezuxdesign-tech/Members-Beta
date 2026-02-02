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
		return [ 'alezux-finanzas-tables-css' ]; 
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

        $this->add_control(
			'table_title',
			[
				'label' => esc_html__( 'Título de la Tabla', 'alezux-members' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Planes', 'alezux-members' ),
                'placeholder' => esc_html__( 'Escribe un título', 'alezux-members' ),
			]
		);

        $this->add_control(
			'table_description',
			[
				'label' => esc_html__( 'Descripción', 'alezux-members' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Administra los planes de suscripción.', 'alezux-members' ),
                'placeholder' => esc_html__( 'Escribe una descripción', 'alezux-members' ),
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
            
            <div class="alezux-table-header">
                <div class="alezux-header-left">
                    <h3 class="alezux-table-title"><?php echo esc_html($settings['table_title']); ?></h3>
                    <?php if ( ! empty( $settings['table_description'] ) ) : ?>
                        <p class="alezux-table-desc"><?php echo esc_html($settings['table_description']); ?></p>
                    <?php endif; ?>
                </div>

                <div class="alezux-header-right">
                    <div class="alezux-filter-item search-item">
                         <div class="alezux-search-wrapper">
                            <span class="dashicons dashicons-search"></span>
                            <input type="text" id="alezux-plans-search" placeholder="Buscar por nombre...">
                         </div>
                    </div>
                </div>
            </div>

            <!-- Filtros adicionales debajo o integrados si caben -->
            <div class="alezux-filters-secondary" style="margin-bottom: 20px; display:flex; justify-content: flex-end; gap: 15px;">
                <div class="alezux-filter-item">
                     <label style="display:block; font-size:12px; color:#718096; margin-bottom:5px;">Filtrar por Curso</label>
                     <select id="alezux-plans-course" style="padding: 8px; border-radius:6px; background:#121620; border:1px solid #2d3748; color:#fff;">
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

            <table class="alezux-plans-table">
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
