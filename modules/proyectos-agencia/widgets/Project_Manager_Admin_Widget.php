<?php
namespace Alezux_Members\Modules\Proyectos_Agencia\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Project_Manager_Admin_Widget extends Widget_Base {

	public function get_name() {
		return 'alezux_project_manager_admin';
	}

	public function get_title() {
		return 'Gestor de Proyectos (Admin)';
	}

	public function get_icon() {
		return 'eicon-kanban';
	}

	public function get_categories() {
		return [ 'alezux_members' ];
	}
    
    public function get_style_depends() {
		return [ 'alezux-kanban-css' ];
	}

	public function get_script_depends() {
		return [ 'alezux-kanban-js' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => 'Configuración',
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'admin_roles',
			[
				'label' => 'Roles Permitidos',
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => [
					'administrator' => 'Administrador',
					'editor' => 'Editor',
                    'author' => 'Autor' 
				],
				'default' => [ 'administrator' ],
                'description' => 'Selecciona qué roles pueden ver y gestionar este tablero.'
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
        // Verificar permisos
        $settings = $this->get_settings_for_display();
        $allowed_roles = $settings['admin_roles'];
        $current_user = wp_get_current_user();
        $has_permission = false;

        foreach ( $allowed_roles as $role ) {
            if ( in_array( $role, (array) $current_user->roles ) ) {
                $has_permission = true;
                break;
            }
        }

        if ( ! $has_permission && ! current_user_can( 'administrator' ) ) {
            echo '<div class="alezux-alert-error">No tienes permiso para ver este contenido.</div>';
            return;
        }

        // Renderizar el Kanban Board Container
		?>
		<div class="alezux-kanban-board" id="alezux-kanban-app">
            <div class="kanban-header">
                <h2>Gestor de Proyectos</h2>
                 <button id="add-project-btn" class="alezux-btn alezux-btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Proyecto
                </button>
            </div>
            
            <div class="kanban-container">
                <!-- Column: Por Comenzar -->
                <div class="kanban-column" data-status="start">
                    <div class="kanban-column-header start">Por Comenzar</div>
                    <div class="kanban-column-body" id="col-start">
                        <!-- Items inject via JS -->
                        <div class="kanban-loading">Cargando...</div>
                    </div>
                </div>

                <!-- Column: En Proceso -->
                <div class="kanban-column" data-status="process">
                    <div class="kanban-column-header process">En Proceso</div>
                    <div class="kanban-column-body" id="col-process"></div>
                </div>

                 <!-- Column: En Revisión -->
                <div class="kanban-column" data-status="review">
                    <div class="kanban-column-header review">En Revisión</div>
                    <div class="kanban-column-body" id="col-review"></div>
                </div>

                <!-- Column: Aprobado -->
                <div class="kanban-column" data-status="approved">
                    <div class="kanban-column-header approved">Aprobado</div>
                    <div class="kanban-column-body" id="col-approved"></div>
                </div>

                <!-- Column: Entregado -->
                <div class="kanban-column" data-status="delivered">
                    <div class="kanban-column-header delivered">Entregado</div>
                    <div class="kanban-column-body" id="col-delivered"></div>
                </div>
            </div>
		</div>

        <!-- Project Modal (Hidden by default) -->
        <div id="project-modal" class="alezux-modal" style="display:none;">
            <div class="alezux-modal-content">
                <span class="close-modal">&times;</span>
                <h3 id="modal-project-title">Detalles del Proyecto</h3>
                <!-- Dynamic Content Here -->
                <div id="modal-body-content"></div>
                <div class="modal-footer">
                     <button id="save-project-btn" class="alezux-btn alezux-btn-success">Guardar Cambios</button>
                </div>
            </div>
        </div>
		<?php
	}
}
