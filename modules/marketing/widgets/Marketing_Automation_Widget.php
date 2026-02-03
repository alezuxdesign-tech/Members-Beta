<?php
namespace Alezux_Members\Modules\Marketing\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! \defined( 'ABSPATH' ) ) {
	exit;
}

class Marketing_Automation_Widget extends Widget_Base {

	public function get_name() {
		return 'alezux_marketing_automation';
	}

	public function get_title() {
		return \esc_html__( 'Marketing Automation', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-flow';
	}

	public function get_categories() {
		return [ 'alezux-admin' ];
	}

	public function get_script_depends() {
		return [ 'alezux-marketing-js' ];
	}

	public function get_style_depends() {
		return [ 'alezux-marketing-css' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => \esc_html__( 'Configuración', 'alezux-members' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'automation_id',
			[
				'label'   => \esc_html__( 'Seleccionar Automatización', 'alezux-members' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $this->get_automations_list(),
				'default' => '',
			]
		);

		$this->end_controls_section();
	}

    private function get_automations_list() {
        global $wpdb;
        $table = $wpdb->prefix . 'alezux_marketing_automations';
        $results = $wpdb->get_results( "SELECT id, name FROM $table ORDER BY name ASC" );
        
        $options = [ '' => 'Crear Nueva...' ];
        foreach ( $results as $row ) {
            $options[ $row->id ] = $row->name;
        }
        return $options;
    }

	protected function render() {
		?>
		<div class="alezux-finanzas-app alezux-marketing-dashboard">
            <!-- Header de la Tabla -->
            <div class="alezux-table-header">
                <div class="alezux-header-left">
                    <h2 class="alezux-table-title"><?php \esc_html_e( 'Automatizaciones de Marketing', 'alezux-members' ); ?></h2>
                    <p class="alezux-table-desc"><?php \esc_html_e( 'Gestiona tus flujos de trabajo y correos automáticos.', 'alezux-members' ); ?></p>
                </div>
                <div class="alezux-header-right">
                    <button id="btn-create-automation" class="alezux-action-btn">
                        <span class="dashicons dashicons-plus"></span> <?php \esc_html_e( 'Nueva Automatización', 'alezux-members' ); ?>
                    </button>
                </div>
            </div>

            <!-- Tabla de Automatizaciones -->
            <div class="alezux-table-wrapper">
                <table class="alezux-finanzas-table" id="marketing-automations-table">
                    <thead>
                        <tr>
                            <th><?php \esc_html_e( 'ID', 'alezux-members' ); ?></th>
                            <th><?php \esc_html_e( 'Nombre de la Automatización', 'alezux-members' ); ?></th>
                            <th><?php \esc_html_e( 'Nodos', 'alezux-members' ); ?></th>
                            <th><?php \esc_html_e( 'Fecha de Creación', 'alezux-members' ); ?></th>
                            <th style="text-align: center;"><?php \esc_html_e( 'Acciones', 'alezux-members' ); ?></th>
                        </tr>
                    </thead>
                    <tbody id="marketing-automations-list">
                        <!-- Se llena vía JS -->
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px; color: #718096;">
                                <span class="dashicons dashicons-update alezux-spin"></span> Cargando automatizaciones...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- POPUP GIGANTE DEL EDITOR -->
            <div id="alezux-editor-popup" class="alezux-full-popup" style="display:none;">
                <div class="popup-overlay"></div>
                <div class="popup-container">
                    <div class="popup-header">
                        <div class="popup-title-group">
                            <input type="text" id="automation-name" placeholder="Nombre de la automatización..." class="alezux-popup-input">
                        </div>
                        <div class="popup-actions">
                            <button id="save-marketing-automation" class="alezux-action-btn">
                                <span class="dashicons dashicons-saved"></span> Guardar
                            </button>
                            <button id="close-editor-popup" class="alezux-btn-danger">
                                <span class="dashicons dashicons-no-alt"></span> Cerrar
                            </button>
                        </div>
                    </div>
                    
                    <div class="popup-body">
                        <!-- Sidebar Izquierda: Componentes -->
                        <div class="editor-sidebar">
                            <h4 class="sidebar-section-title">Triggers</h4>
                            <div class="automation-node-template trigger" data-type="trigger" draggable="true">
                                <span class="node-icon">⚡</span> Evento
                            </div>

                            <h4 class="sidebar-section-title">Acciones</h4>
                            <div class="automation-node-template action" data-type="email" draggable="true">
                                <span class="node-icon">✉️</span> Enviar Email
                            </div>
                            <div class="automation-node-template logic" data-type="delay" draggable="true">
                                <span class="node-icon">⏳</span> Esperar (Delay)
                            </div>

                            <div class="sidebar-footer">
                                <button id="clear-canvas" class="alezux-btn-soft-danger">
                                    <span class="dashicons dashicons-trash"></span> Limpiar Lienzo
                                </button>
                            </div>
                        </div>

                        <!-- Área Central: Canvas -->
                        <div id="alezux-marketing-canvas" class="editor-canvas">
                            <div class="canvas-placeholder">
                                <span class="dashicons dashicons-move"></span>
                                <p>Arrastra componentes aquí para empezar tu flujo</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal de Configuración de Nodo (Pequeño, encima del editor) -->
            <div id="alezux-node-modal" class="alezux-modal-overlay" style="display:none;">
                <div class="alezux-modal-content">
                    <h3 id="modal-title">Configurar Nodo</h3>
                    <div id="modal-fields"></div>
                    <div class="modal-footer">
                        <button id="modal-save" class="alezux-btn-primary">Guardar Cambios</button>
                        <button id="modal-cancel" class="alezux-btn-secondary">Cancelar</button>
                    </div>
                </div>
            </div>

            <script>
                window.alezuxEventsDictionary = {
                    'primer_pago': 'Primer Pago Exitoso',
                    'pago_exitoso': 'Pago Recibido (General)',
                    'registro_usuario': 'Nuevo Registro de Usuario',
                    'curso_completado': 'Curso Finalizado',
                    'logro_obtenido': 'Nuevo Logro Desbloqueado'
                };
            </script>
		</div>
            #alezux-marketing-canvas {
                border: 2px dashed #444;
            }
        </style>
		<?php
	}
}
