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
                    
                    <form id="briefing-form" enctype="multipart/form-data">
                        <div class="alezux-form-group">
                            <label>Referencias de Sitios Web (URLs y qué te gusta):</label>
                            <textarea class="alezux-input" name="web_preferences"><?php echo isset($data['briefing']['web_preferences']) ? esc_textarea($data['briefing']['web_preferences']) : ''; ?></textarea>
                        </div>
                        
                        <div class="alezux-form-group">
                            <label>¿Ya tienes Logo?</label>
                            <select class="alezux-input" name="has_logo" id="has-logo-select">
                                <option value="no" <?php selected( ($data['briefing']['has_logo'] ?? ''), 'no' ); ?>>No, necesito uno</option>
                                <option value="yes" <?php selected( ($data['briefing']['has_logo'] ?? ''), 'yes' ); ?>>Sí, ya tengo</option>
                            </select>
                        </div>
                        
                        <div class="alezux-form-group" id="logo-upload-group" style="display: <?php echo ($data['briefing']['has_logo'] ?? '') === 'yes' ? 'block' : 'none'; ?>;">
                            <label>Sube tu Logo (PNG, SVG, JPG):</label>
                            <input type="file" class="alezux-input" name="logo_file" accept="image/*">
                            <?php if(!empty($data['briefing']['logo_url'])): ?>
                                <p>Logo actual: <a href="<?php echo esc_url($data['briefing']['logo_url']); ?>" target="_blank">Ver Logo</a></p>
                            <?php endif; ?>
                        </div>

                         <div class="alezux-section-title" style="margin-top:30px; margin-bottom:15px; border-bottom:1px solid #eee; padding-bottom:10px;">
                            <h4>Datos Legales y de Contacto</h4>
                        </div>

                        <div class="alezux-grid-2">
                             <div class="alezux-form-group">
                                <label>1. Nombre completo o Razón Social</label>
                                <input type="text" class="alezux-input" name="legal_name" value="<?php echo esc_attr($data['briefing']['legal_name'] ?? ''); ?>">
                            </div>
                            <div class="alezux-form-group">
                                <label>2. CIF / NIF / NIT</label>
                                <input type="text" class="alezux-input" name="legal_id" value="<?php echo esc_attr($data['briefing']['legal_id'] ?? ''); ?>">
                            </div>
                            <div class="alezux-form-group">
                                <label>3. Dirección fiscal completa</label>
                                <input type="text" class="alezux-input" name="legal_address" value="<?php echo esc_attr($data['briefing']['legal_address'] ?? ''); ?>">
                            </div>
                            <div class="alezux-form-group">
                                <label>4. Teléfono de contacto</label>
                                <input type="text" class="alezux-input" name="legal_phone" value="<?php echo esc_attr($data['briefing']['legal_phone'] ?? ''); ?>">
                            </div>
                             <div class="alezux-form-group">
                                <label>5. Correo electrónico de contacto</label>
                                <input type="email" class="alezux-input" name="legal_email" value="<?php echo esc_attr($data['briefing']['legal_email'] ?? ''); ?>">
                            </div>
                            <div class="alezux-form-group">
                                <label>6. Correo privacidad (si es diferente)</label>
                                <input type="email" class="alezux-input" name="privacy_email" value="<?php echo esc_attr($data['briefing']['privacy_email'] ?? ''); ?>">
                            </div>
                            <div class="alezux-form-group">
                                <label>7. URL del sitio web</label>
                                <input type="text" class="alezux-input" name="legal_url" value="<?php echo esc_attr($data['briefing']['legal_url'] ?? ''); ?>">
                            </div>
                            <div class="alezux-form-group">
                                <label>8. Nombre comercial o de marca</label>
                                <input type="text" class="alezux-input" name="brand_name" value="<?php echo esc_attr($data['briefing']['brand_name'] ?? ''); ?>">
                            </div>
                             <div class="alezux-form-group">
                                <label>9. Actividad del negocio</label>
                                <input type="text" class="alezux-input" name="business_activity" value="<?php echo esc_attr($data['briefing']['business_activity'] ?? ''); ?>">
                            </div>
                            <div class="alezux-form-group">
                                <label>10. Registro Mercantil</label>
                                <input type="text" class="alezux-input" name="legal_registry" value="<?php echo esc_attr($data['briefing']['legal_registry'] ?? ''); ?>">
                            </div>
                            <div class="alezux-form-group">
                                <label>11. Correo DPO (si aplica)</label>
                                <input type="email" class="alezux-input" name="dpo_email" value="<?php echo esc_attr($data['briefing']['dpo_email'] ?? ''); ?>">
                            </div>
                             <div class="alezux-form-group">
                                <label>12. Sectores de actividad (Marketing)</label>
                                <input type="text" class="alezux-input" name="marketing_sectors" value="<?php echo esc_attr($data['briefing']['marketing_sectors'] ?? ''); ?>">
                            </div>
                            <div class="alezux-form-group">
                                <label>13. Jurisdicción (Ciudad/País)</label>
                                <input type="text" class="alezux-input" name="jurisdiction" value="<?php echo esc_attr($data['briefing']['jurisdiction'] ?? ''); ?>">
                            </div>
                             <div class="alezux-form-group">
                                <label>14. WhatsApp para contrataciones</label>
                                <input type="text" class="alezux-input" name="whatsapp" value="<?php echo esc_attr($data['briefing']['whatsapp'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-actions" style="margin-top:30px;">
                            <button type="button" class="alezux-btn alezux-btn-secondary" id="btn-full-control" style="display:none;">¡Todo en sus manos!</button>
                            <button type="submit" class="alezux-btn alezux-btn-primary" id="save-briefing-btn">Guardar y Enviar</button>
                        </div>
                    </form>
                    
                    <script>
                        jQuery(document).ready(function($){
                            $('#has-logo-select').change(function(){
                                if($(this).val() == 'yes') {
                                    $('#logo-upload-group').slideDown();
                                } else {
                                    $('#logo-upload-group').slideUp();
                                }
                            });
                        });
                    </script>
                </div>
                <?php endif; ?>

                <!-- STEP 2: IDENTITY -->
                 <?php if ($current_step === 'identity'): ?>
                <div class="step-content active" id="step-identity">
                    <h3>Propuesta de Identidad</h3>
                    
                    <?php 
                    $files = isset($data['identity']['proposal_files']) ? $data['identity']['proposal_files'] : [];
                    $status = isset($data['identity']['status']) ? $data['identity']['status'] : '';
                    
                    if(empty($files)): ?>
                        <div class="alezux-alert-warning">
                            <i class="fas fa-paint-brush"></i> Estamos trabajando en tus propuestas de diseño. Te notificaremos pronto.
                        </div>
                    <?php else: ?>
                        
                        <?php if($status === 'changes_requested'): ?>
                            <div class="alezux-alert-info">Has solicitado cambios. Estamos trabajando en ello.</div>
                        <?php elseif($status === 'approved'): ?>
                             <div class="alezux-alert-success">¡Has aprobado esta etapa! Pasaremos al desarrollo web pronto.</div>
                        <?php else: ?>
                             <div class="alezux-alert-info">Por favor revisa las propuestas y danos tu feedback.</div>
                        <?php endif; ?>

                        <div class="files-grid" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap:15px; margin: 20px 0;">
                            <?php foreach($files as $index => $file): 
                                $ext = pathinfo($file, PATHINFO_EXTENSION);
                                $icon = in_array(strtolower($ext), ['jpg','jpeg','png','gif','webp']) ? 'fa-file-image' : 'fa-file-pdf';
                            ?>
                                <div class="file-card" style="border:1px solid #ddd; padding:15px; border-radius:8px; text-align:center;">
                                    <i class="fas <?php echo $icon; ?>" style="font-size:30px; color:#555; margin-bottom:10px; display:block;"></i>
                                    <span style="display:block; margin-bottom:10px; font-weight:500;">Propuesta <?php echo $index + 1; ?></span>
                                    <a href="<?php echo esc_url($file); ?>" target="_blank" class="alezux-btn alezux-btn-secondary" style="font-size:12px;">
                                        <i class="fas fa-eye"></i> Ver / Descargar
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if($status !== 'approved'): ?>
                        <div class="action-buttons">
                            <button class="alezux-btn alezux-btn-success" data-action="approve" data-step="identity">
                                <i class="fas fa-check-circle"></i> Aprobar Diseño
                            </button>
                            <button class="alezux-btn alezux-btn-danger" onclick="jQuery('#feedback-identity').slideToggle()">
                                <i class="fas fa-edit"></i> Sugerir Cambios
                            </button>
                        </div>
                        
                        <div id="feedback-identity" style="display:none; margin-top:20px; background:#f9f9f9; padding:15px; border-radius:8px;">
                            <label>Describe los cambios que te gustaría ver:</label>
                            <textarea class="alezux-input" id="identity-feedback-text" rows="4" placeholder="Ej: Me gustaría un color azul más oscuro en el logo..."></textarea>
                            <div style="text-align:right; margin-top:10px;">
                                <button class="alezux-btn alezux-btn-primary" data-action="changes" data-step="identity">Enviar Sugerencias</button>
                            </div>
                        </div>
                        <?php endif; ?>

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
