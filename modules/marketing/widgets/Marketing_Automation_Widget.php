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
		<div class="alezux-marketing-canvas-wrapper" style="background: #111; border-radius: 50px; padding: 20px; min-height: 600px; position: relative; overflow: hidden;">
            <div id="alezux-marketing-sidebar" style="width: 250px; background: #1a1a1a; border-radius: 30px; padding: 20px; float: left; margin-right: 20px; box-shadow: 10px 0 20px rgba(0,0,0,0.5);">
                <h3 style="color: #fff; font-size: 16px; margin-bottom: 20px;">Componentes</h3>
                
                <div class="automation-node-template trigger" data-type="trigger" draggable="true" style="background: #f1c40f; color: #000; padding: 15px; border-radius: 50px; margin-bottom: 10px; cursor: move; font-weight: bold; text-align: center;">
                    ⚡ Trigger Evento
                </div>
                <div class="automation-node-template action" data-type="email" draggable="true" style="background: #2ecc71; color: #fff; padding: 15px; border-radius: 50px; margin-bottom: 10px; cursor: move; font-weight: bold; text-align: center;">
                    ✉️ Enviar Email
                </div>
                <div class="automation-node-template logic" data-type="delay" draggable="true" style="background: #3498db; color: #fff; padding: 15px; border-radius: 50px; margin-bottom: 10px; cursor: move; font-weight: bold; text-align: center;">
                    ⏳ Esperar (Delay)
                </div>

                <hr style="border: 0; border-top: 1px solid #333; margin: 20px 0;">
                
                <button id="clear-canvas" style="width: 100%; background: transparent; border: 1px solid #444; color: #888; padding: 10px; border-radius: 50px; cursor: pointer; font-size: 12px;">
                    🗑️ Limpiar Lienzo
                </button>
            </div>

            <div id="alezux-marketing-canvas" style="flex-grow: 1; height: 600px; background-image: radial-gradient(#333 1px, transparent 1px); background-size: 20px 20px; border-radius: 30px; position: relative;">
                <!-- El sistema de JS inyectará el SVG aquí -->
                <div class="canvas-placeholder" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #666; text-align: center; pointer-events: none;">
                    <p>Arrastra componentes aquí para empezar</p>
                </div>
            </div>

            <!-- Modal Personalizado Alezux -->
            <div id="alezux-node-modal" class="alezux-modal-overlay" style="display:none; position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:9999; backdrop-filter: blur(5px); justify-content: center; align-items: center;">
                <div class="alezux-modal-content" style="background: #1a1a1a; width: 400px; border-radius: 30px; border: 1px solid #333; padding: 30px; box-shadow: 0 20px 50px rgba(0,0,0,1);">
                    <h3 id="modal-title" style="color:#fff; margin-bottom: 20px;">Configurar Nodo</h3>
                    
                    <div id="modal-fields">
                        <!-- Campos dinámicos aquí -->
                    </div>

                    <div style="margin-top: 30px; display: flex; gap: 10px;">
                        <button id="modal-save" style="flex:1; background: #e74c3c; color:#fff; border:none; padding:12px; border-radius:50px; cursor:pointer; font-weight:bold;">Guardar</button>
                        <button id="modal-cancel" style="flex:1; background: transparent; color:#888; border:1px solid #333; padding:12px; border-radius:50px; cursor:pointer;">Cancelar</button>
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

            <div style="clear: both;"></div>
            
            <div class="alezux-canvas-actions" style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center;">
                <div class="automation-meta" style="display: flex; gap: 15px; align-items: center;">
                    <input type="text" id="automation-name" placeholder="Nombre de la automatización..." style="background: #000; color: #fff; border: 1px solid #333; padding: 10px 20px; border-radius: 50px; width: 300px;">
                    <select id="load-automation-select" style="background: #000; color: #888; border: 1px solid #333; padding: 10px 20px; border-radius: 50px; min-width: 200px;">
                        <option value="">Cargar automatización...</option>
                        <?php 
                        $autos = $this->get_automations_list(); 
                        foreach($autos as $id => $name) {
                            if($id) echo "<option value='$id'>$name</option>";
                        }
                        ?>
                    </select>
                </div>
                <button id="save-marketing-automation" class="alezux-save-btn" style="background: #e74c3c; color: white; border: none; padding: 12px 30px; border-radius: 50px; font-weight: bold; cursor: pointer; box-shadow: 0 5px 15px rgba(231, 76, 60, 0.4);">
                    Guardar Automatización
                </button>
            </div>
		</div>

        <style>
            .automation-node-template:hover {
                transform: scale(1.05);
                transition: all 0.3s ease;
            }
            #alezux-marketing-canvas {
                border: 2px dashed #444;
            }
        </style>
		<?php
	}
}
