<?php
namespace Alezux_Members\Modules\Proyectos_Agencia\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Alezux_Members\Modules\Proyectos_Agencia\Includes\Projects_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Project_Client_View_Widget extends Widget_Base {

	public function get_name() {
		return 'alezux_project_client_view';
	}

	public function get_title() {
		return 'Vista Cliente (Mi Proyecto)';
	}

	public function get_icon() {
		return 'eicon-time-line';
	}

	public function get_categories() {
		return [ 'alezux_members' ];
	}
    
    public function get_style_depends() {
		return [ 'alezux-client-view-css' ];
	}

	public function get_script_depends() {
		return [ 'alezux-client-app-js' ];
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
			'no_project_message',
			[
				'label' => 'Mensaje sin proyecto',
				'type' => Controls_Manager::TEXTAREA,
				'default' => 'No tienes un proyecto activo en este momento.',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
        if ( ! is_user_logged_in() ) {
            echo '<div class="alezux-alert-info">Debes iniciar sesión para ver tu proyecto.</div>';
            return;
        }

        $user_id = get_current_user_id();
        $manager = new Projects_Manager();
        
        // Find project for this client
        $projects = $manager->get_projects( [ 'client_id' => $user_id ] );
        
        if ( empty( $projects ) ) {
            echo '<div class="alezux-alert-info">' . $this->get_settings_for_display( 'no_project_message' ) . '</div>';
            return;
        }

        // Get the most recent project
        $project = $projects[0];
        $data = json_decode( $project->project_data, true ) ?: [];
        $current_step = $project->current_step; // briefing, identity, web_design, development, delivery

        // Map steps to order
        $steps = [
            'briefing' => 'Briefing',
            'identity' => 'Identidad (Logo)',
            'web_design' => 'Diseño Web',
            'development' => 'Desarrollo',
            'delivery' => 'Entrega'
        ];
        
        $steps_keys = array_keys($steps);
        $current_index = array_search($current_step, $steps_keys);

        // Render Timeline
		?>
		<div class="alezux-client-project" id="alezux-client-app" data-id="<?php echo $project->id; ?>">
            
            <!-- Timeline Header -->
            <div class="project-timeline">
                <?php foreach($steps as $key => $label): 
                    $step_idx = array_search($key, $steps_keys);
                    $status_class = '';
                    if ($step_idx < $current_index) $status_class = 'completed';
                    elseif ($step_idx === $current_index) $status_class = 'active';
                ?>
                <div class="timeline-step <?php echo $status_class; ?>">
                    <div class="step-circle">
                        <?php if($status_class == 'completed'): ?>
                            <i class="fas fa-check"></i>
                        <?php else: ?>
                            <span><?php echo $step_idx + 1; ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="step-label"><?php echo $label; ?></div>
                    <?php if($step_idx < count($steps)-1): ?><div class="step-line"></div><?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Content Area -->
            <div class="project-content-area">
                
                <!-- STEP 1: BRIEFING -->
                <?php if ($current_step === 'briefing'): ?>
                <div class="step-content active" id="step-briefing">
                    <h3>Briefing Inicial</h3>
                    <p>Cuéntanos sobre tu visión para el sitio web y tu marca.</p>
                    
                    <form id="briefing-form">
                        <div class="alezux-form-group">
                            <label>Referencias de Sitios Web (URLs y qué te gusta):</label>
                            <textarea class="alezux-input" name="web_preferences"><?php echo isset($data['briefing']['web_preferences']) ? esc_textarea($data['briefing']['web_preferences']) : ''; ?></textarea>
                        </div>
                        
                        <div class="alezux-form-group">
                            <label>¿Ya tienes Logo?</label>
                            <select class="alezux-input" name="has_logo">
                                <option value="no">No, necesito uno</option>
                                <option value="yes">Sí, ya tengo</option>
                            </select>
                        </div>

                         <div class="alezux-form-group">
                            <label>Datos Legales (Para pie de página y políticas):</label>
                            <textarea class="alezux-input" name="legal_data"><?php echo isset($data['briefing']['legal_data']) ? esc_textarea($data['briefing']['legal_data']) : ''; ?></textarea>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="alezux-btn alezux-btn-secondary" id="btn-full-control">¡Todo en sus manos!</button>
                            <button type="submit" class="alezux-btn alezux-btn-primary">Guardar y Enviar</button>
                        </div>
                    </form>
                </div>
                <?php endif; ?>

                <!-- STEP 2: IDENTITY -->
                 <?php if ($current_step === 'identity'): ?>
                <div class="step-content active" id="step-identity">
                    <h3>Propuesta de Identidad</h3>
                    
                    <?php 
                    $files = isset($data['identity']['proposal_files']) ? $data['identity']['proposal_files'] : [];
                    if(empty($files)): ?>
                        <div class="alezux-alert-warning">Aún no hemos subido propuestas. Por favor espera.</div>
                    <?php else: ?>
                        <div class="files-grid">
                            <?php foreach($files as $file): ?>
                                <a href="<?php echo esc_url($file); ?>" target="_blank" class="file-card">
                                    <i class="fas fa-file-image"></i> Ver Propuesta
                                </a>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="action-buttons">
                            <button class="alezux-btn alezux-btn-success" data-action="approve" data-step="identity">Aprobar Diseño</button>
                            <button class="alezux-btn alezux-btn-danger" onclick="$('#feedback-identity').toggle()">Solicitar Cambios</button>
                        </div>
                        
                        <div id="feedback-identity" style="display:none; margin-top:15px;">
                            <textarea class="alezux-input" id="identity-feedback-text" placeholder="Describe los cambios solicitados..."></textarea>
                            <button class="alezux-btn alezux-btn-primary" data-action="changes" data-step="identity">Enviar Cambios</button>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <!-- STEP 3: WEB DESIGN -->
                <?php if ($current_step === 'web_design'): ?>
                <div class="step-content active" id="step-web-design">
                    <h3>Diseño UI/UX (Figma)</h3>
                     <?php 
                    $url = isset($data['web_design']['figma_url']) ? $data['web_design']['figma_url'] : '';
                    if(empty($url)): ?>
                        <div class="alezux-alert-warning">Estamos trabajando en tu diseño.</div>
                    <?php else: ?>
                        <p>Revisa el prototipo interactivo en el siguiente enlace:</p>
                        <a href="<?php echo esc_url($url); ?>" target="_blank" class="alezux-btn alezux-btn-secondary">Ver Prototipo en Figma</a>
                        
                        <div class="action-buttons" style="margin-top:20px;">
                            <button class="alezux-btn alezux-btn-success" data-action="approve" data-step="web_design">Aprobar Diseño Web</button>
                            <button class="alezux-btn alezux-btn-danger" onclick="$('#feedback-web').toggle()">Solicitar Cambios</button>
                        </div>
                         <div id="feedback-web" style="display:none; margin-top:15px;">
                            <textarea class="alezux-input" id="web-feedback-text" placeholder="Describe los cambios..."></textarea>
                            <button class="alezux-btn alezux-btn-primary" data-action="changes" data-step="web_design">Enviar Solicitud</button>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                 <!-- STEP 4: DEVELOPMENT -->
                <?php if ($current_step === 'development'): ?>
                <div class="step-content active" id="step-development">
                    <h3>Desarrollo & Montaje</h3>
                       <?php 
                    $url = isset($data['development']['staging_url']) ? $data['development']['staging_url'] : '';
                    if(empty($url)): ?>
                        <div class="alezux-alert-warning">Estamos programando tu sitio web.</div>
                    <?php else: ?>
                        <p>Tu sitio está montado en un servidor de pruebas:</p>
                        <a href="<?php echo esc_url($url); ?>" target="_blank" class="alezux-btn alezux-btn-secondary">Ver Sitio Web (Staging)</a>
                         <div class="action-buttons" style="margin-top:20px;">
                            <button class="alezux-btn alezux-btn-success" data-action="approve" data-step="development">Aprobar y Finalizar</button>
                            <button class="alezux-btn alezux-btn-warning" onclick="$('#call-booking').toggle()">Agendar Revisión / Cambios</button>
                        </div>
                         <div id="call-booking" style="display:none; margin-top:15px;">
                            <p>Para cambios en esta etapa, es mejor agendar una llamada rápida.</p>
                            <a href="#" class="alezux-btn alezux-btn-primary">Calendly (Ejemplo)</a>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- STEP 5: DELIVERY -->
                <?php if ($current_step === 'delivery'): ?>
                <div class="step-content active" id="step-delivery">
                    <div class="delivery-hero">
                        <i class="fas fa-gift" style="font-size: 50px; color:#28a745;"></i>
                        <h2>¡Tu Proyecto está Listo!</h2>
                        <p>Aquí tienes todos los recursos de tu nueva marca y sitio web.</p>
                    </div>
                    
                    <div class="delivery-folder">
                        <h4><i class="fas fa-folder-open"></i> Archivos Finales</h4>
                        <ul>
                            <?php 
                             $assets = isset($data['delivery']['final_assets']) ? $data['delivery']['final_assets'] : [];
                             if(!empty($assets)):
                                foreach($assets as $asset): ?>
                                <li><a href="<?php echo esc_url($asset); ?>" target="_blank"><i class="fas fa-download"></i> Descargar Recurso</a></li>
                                <?php endforeach; 
                             else: ?>
                                <li>No hay archivos adjuntos.</li>
                             <?php endif; ?>
                        </ul>
                    </div>

                    <div class="delivery-folder">
                        <h4><i class="fas fa-key"></i> Credenciales</h4>
                        <div class="credentials-box">
                            <?php $creds = isset($data['delivery']['credentials']) ? $data['delivery']['credentials'] : []; ?>
                            <p><strong>URL Admin:</strong> <?php echo $creds['url'] ?? 'N/A'; ?></p>
                            <p><strong>Usuario:</strong> <?php echo $creds['user'] ?? 'N/A'; ?></p>
                            <!-- Password usually better shared securely 1-on-1 -->
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
		</div>
		<?php
	}
}
