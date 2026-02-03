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
        if ( ! \current_user_can( 'manage_options' ) ) {
            echo '<div style="padding: 20px; color: #718096; font-weight: bold;">' . \esc_html__( 'No tienes permisos administrativos', 'alezux-members' ) . '</div>';
            return;
        }
		?>
		<div class="alezux-finanzas-app alezux-marketing-dashboard">
            <!-- Header de la Tabla -->
            <div class="alezux-table-header">
                <div class="alezux-header-left">
                    <h2 class="alezux-table-title"><?php \esc_html_e( 'Automatizaciones de Marketing', 'alezux-members' ); ?></h2>
                    <p class="alezux-table-desc"><?php \esc_html_e( 'Gestiona tus flujos de trabajo y correos automáticos.', 'alezux-members' ); ?></p>
                </div>
                <div class="alezux-header-right">
                    <button id="btn-create-automation" class="alezux-marketing-btn">
                        <span class="dashicons dashicons-plus"></span> <?php \esc_html_e( 'Nueva Automatización', 'alezux-members' ); ?>
                    </button>
                </div>
            </div>

            <!-- Tabla de Automatizaciones -->
            <div class="alezux-table-wrapper">
                <table class="alezux-finanzas-table" id="marketing-automations-table">
                    <thead>
                        <tr>
                            <th><?php \esc_html_e( 'Automatización', 'alezux-members' ); ?></th>
                            <th><?php \esc_html_e( 'Estado', 'alezux-members' ); ?></th>
                            <th><?php \esc_html_e( 'Ejecuciones', 'alezux-members' ); ?></th>
                            <th><?php \esc_html_e( 'Fecha', 'alezux-members' ); ?></th>
                            <th style="text-align: right;"><?php \esc_html_e( 'Acciones', 'alezux-members' ); ?></th>
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
                            <button id="add-global-node" class="alezux-action-btn" style="background: #4a5568;">
                                <span class="dashicons dashicons-plus"></span> Añadir Nodo
                            </button>
                            <button id="save-marketing-automation" class="alezux-action-btn">
                                <span class="dashicons dashicons-saved"></span> Guardar
                            </button>
                            <button id="close-editor-popup" class="alezux-btn-danger">
                                <span class="dashicons dashicons-no-alt"></span> Cerrar
                            </button>
                        </div>
                    </div>
                    
                    <div class="popup-body">
                        <!-- Área Central: Canvas (Ahora pantalla completa) -->
                        <div id="alezux-marketing-canvas" class="editor-canvas">
                            <div class="canvas-placeholder">
                                <span class="dashicons dashicons-move"></span>
                                <p>Haz clic en el nodo inicial para configurar tu automatización</p>
                            </div>
                        </div>

                        <!-- PANEL LATERAL DERECHO (Drawer) para Emails -->
                        <div id="alezux-side-panel" class="alezux-editor-drawer">
                            <div class="drawer-header">
                                <h3><span class="dashicons dashicons-email"></span> Configurar Email</h3>
                                <button id="close-side-panel" class="alezux-btn-icon"><span class="dashicons dashicons-no-alt"></span></button>
                            </div>
                            <div class="drawer-content">
                                <div class="alezux-field-group">
                                    <label>Asunto del Correo</label>
                                    <input type="text" id="drawer-subject" placeholder="Ej: ¡Bienvenido a la academia!">
                                </div>
                                <div class="alezux-field-group">
                                    <label>Mensaje (Soporta HTML)</label>
                                    <textarea id="drawer-content" placeholder="Escribe o pega aquí el código HTML de tu correo..."></textarea>
                                </div>
                                <div id="drawer-placeholders" class="drawer-info-box">
                                    <!-- Se llena vía JS -->
                                </div>
                            </div>
                            <div class="drawer-footer">
                                <button id="preview-email-btn" class="alezux-btn-soft">
                                    <span class="dashicons dashicons-visibility"></span> Vista Previa
                                </button>
                                <button id="save-side-panel" class="alezux-action-btn">
                                    <span class="dashicons dashicons-saved"></span> Aplicar Cambios
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MODAL DE VISTA PREVIA -->
            <div id="alezux-preview-modal" class="alezux-modal-overlay" style="display:none;">
                <div class="alezux-modal-content" style="width: 800px; height: 80vh; display: flex; flex-direction: column;">
                    <div class="modal-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                        <h3 id="preview-title" style="margin:0; font-size:18px;">Vista Previa del Correo</h3>
                        <button id="close-preview-modal" class="alezux-btn-icon"><span class="dashicons dashicons-no-alt"></span></button>
                    </div>
                    <div id="email-preview-body" style="flex-grow:1; background:white; border-radius:15px; overflow:hidden;">
                        <iframe id="preview-iframe" style="width:100%; height:100%; border:none;"></iframe>
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
		<?php
	}
}
