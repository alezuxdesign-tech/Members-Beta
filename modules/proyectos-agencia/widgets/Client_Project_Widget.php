<?php
namespace Alezux_Members\Modules\Proyectos_Agencia\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Alezux_Members\Modules\Proyectos_Agencia\Includes\Project_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Client_Project_Widget extends Widget_Base {

	public function get_name() {
		return 'alezux_client_project';
	}

	public function get_title() {
		return esc_html__( 'Mi Proyecto (Cliente)', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-code';
	}

	public function get_categories() {
		return [ 'alezux-otros' ];
	}

	protected function register_controls() {
		// --- CONTENT SECTION ---
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Contenido', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		
		$this->add_control(
			'is_preview_mode',
			[
				'label' => esc_html__( 'Modo Previsualización', 'alezux-members' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Sí', 'alezux-members' ),
				'label_off' => esc_html__( 'No', 'alezux-members' ),
				'return_value' => 'yes',
				'default' => 'no',
				'description' => 'Activa esto para ver un proyecto de ejemplo mientras editas.',
			]
		);

		$this->add_control(
			'preview_step',
			[
				'label' => esc_html__( 'Fase a Previsualizar', 'alezux-members' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'briefing' => '1. Briefing',
					'design_review' => '2. Revisión Diseño',
					'in_progress' => '3. Desarrollo',
					'completed' => '4. Completado',
				],
				'default' => 'design_review',
				'condition' => [
					'is_preview_mode' => 'yes',
				],
			]
		);

		$this->add_control(
			'no_project_msg',
			[
				'label' => esc_html__( 'Mensaje Sin Proyecto', 'alezux-members' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => 'No tienes ningún proyecto activo en este momento.',
			]
		);

		$this->end_controls_section();

		// --- STYLE: CONTAINER ---
		$this->start_controls_section(
			'style_container_section',
			[
				'label' => esc_html__( 'Contenedor Principal', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'container_bg_color',
			[
				'label' => esc_html__( 'Color de Fondo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-client-project-dashboard' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'container_border',
				'selector' => '{{WRAPPER}} .alezux-client-project-dashboard',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'container_shadow',
				'selector' => '{{WRAPPER}} .alezux-client-project-dashboard',
			]
		);

		$this->add_responsive_control(
			'container_padding',
			[
				'label' => esc_html__( 'Relleno (Padding)', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-client-project-dashboard' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'container_radius',
			[
				'label' => esc_html__( 'Radio del Borde', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-client-project-dashboard' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// --- STYLE: HEADER ---
		$this->start_controls_section(
			'style_header_section',
			[
				'label' => esc_html__( 'Cabecera (Header)', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'header_bg_color',
			[
				'label' => esc_html__( 'Fondo Cabecera', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-cp-header' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Color Título', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-cp-header h2' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .alezux-cp-header h2',
			]
		);

		$this->add_control(
			'subtitle_color',
			[
				'label' => esc_html__( 'Color Subtítulo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-cp-header small' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// --- STYLE: TEXTS & CONTENT ---
		$this->start_controls_section(
			'style_content_texts',
			[
				'label' => esc_html__( 'Textos y Contenido', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_color',
			[
				'label' => esc_html__( 'Encabezados (H3, H4)', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} h3, {{WRAPPER}} h4' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'headings_typography',
				'selector' => '{{WRAPPER}} h3, {{WRAPPER}} h4',
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => esc_html__( 'Texto General', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} p, {{WRAPPER}} li, {{WRAPPER}} label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography',
				'selector' => '{{WRAPPER}} p, {{WRAPPER}} li, {{WRAPPER}} label',
			]
		);

		$this->end_controls_section();

		// --- STYLE: BUTTONS ---
		$this->start_controls_section(
			'style_buttons',
			[
				'label' => esc_html__( 'Botones', 'alezux-members' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .alezux-btn',
			]
		);

		$this->start_controls_tabs( 'tabs_buttons' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__( 'Normal', 'alezux-members' ),
			]
		);

		$this->add_control(
			'button_bg_color',
			[
				'label' => esc_html__( 'Fondo Botón Primario', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-btn-primary, {{WRAPPER}} .alezux-btn-success' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => esc_html__( 'Texto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-btn' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__( 'Al Pasar Cursor', 'alezux-members' ),
			]
		);

		$this->add_control(
			'button_bg_color_hover',
			[
				'label' => esc_html__( 'Fondo', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-btn-primary:hover, {{WRAPPER}} .alezux-btn-success:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_text_color_hover',
			[
				'label' => esc_html__( 'Texto', 'alezux-members' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .alezux-btn:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'button_border_radius',
			[
				'label' => esc_html__( 'Radio del Borde', 'alezux-members' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .alezux-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		
		// PREVIEW MODE LOGIC
		if ( $settings['is_preview_mode'] === 'yes' ) {
			$this->render_preview( $settings['preview_step'] );
			return;
		}

		if ( ! is_user_logged_in() ) {
			echo '<div class="alezux-alert info">Inicia sesión para ver tu proyecto.</div>';
			return;
		}

		$user_id = get_current_user_id();
		$manager = new Project_Manager();
		
		// Obtener proyectos del usuario
		$projects = $manager->get_projects_by_user( $user_id );
		
		if ( empty( $projects ) ) {
			echo '<div class="alezux-no-project-box">';
			echo '<i class="eicon-folder-o"></i>';
			echo '<p>' . esc_html( $settings['no_project_msg'] ) . '</p>';
			echo '</div>';
			return;
		}

		// Lógica de Selección de Proyecto
		$project = $projects[0]; // Por defecto el más reciente
		if ( isset( $_GET['alezux_pid'] ) ) {
			$requested_pid = absint( $_GET['alezux_pid'] );
			foreach ( $projects as $p ) {
				if ( $p->id == $requested_pid ) {
					$project = $p;
					break;
				}
			}
		}
		
		// Renderizar contenedor principal
		echo '<div class="alezux-client-project-dashboard" data-project-id="' . esc_attr($project->id) . '">';
		
		// Header del Proyecto (con selector si hay varios)
		$this->render_header( $project, count($projects) > 1 ? $projects : null );
		?>



		<div id="tab-overview" class="client-tab-content active">
			<div class="alezux-project-body">
				<?php
				switch ( $project->current_step ) {
					case 'briefing':
						$this->render_briefing_step( $project, $manager );
						break;
					case 'design_review':
						$this->render_design_review_step( $project, $manager );
						break;
					case 'in_progress':
						$this->render_progress_step( $project );
						break;
					case 'completed':
						$this->render_completed_step( $project );
						break;
					default:
						echo '<p>Estado del proyecto desconocido.</p>';
				}
				?>
			</div>
		</div>

		<div id="tab-chat" class="client-tab-content">
			<div class="alezux-project-body" style="min-height: 400px; display: flex; flex-direction: column;">
				<div id="project-chat-container" class="project-chat-container" style="flex-grow: 1;">
					<div id="chat-messages-list" class="chat-messages-list">
						<div class="chat-loading"><i class="eicon-loading eicon-animation-spin"></i> Cargando mensajes...</div>
					</div>
					<div class="chat-input-area">
						<textarea id="chat-message-input" placeholder="Escribe un mensaje al equipo..."></textarea>
						<button id="btn-send-chat" class="alezux-marketing-btn"><i class="eicon-send"></i></button>
					</div>
				</div>
                <script>
                    // Simple Tab Logic for Client Widget
                    function openClientTab(tabName) {
                        var i;
                        var x = document.getElementsByClassName("client-tab-content");
                        for (i = 0; i < x.length; i++) {
                            x[i].style.display = "none";
                            x[i].classList.remove('active');
                        }
                        
                        var tabs = document.getElementsByClassName("client-tab-btn");
                        for (i = 0; i < tabs.length; i++) {
                             tabs[i].classList.remove('active');
                        }

                        document.getElementById(tabName).style.display = "block";
                        document.getElementById(tabName).classList.add('active');
                        event.currentTarget.classList.add('active');

                        // Init chat if opened
                        if(tabName === 'tab-chat' && typeof AlezuxProjects !== 'undefined') {
                             // Use existing loader from projects.js but target this container
                             // We need to ensure projects.js knows looking at client side
                             // Actually projects.js targets IDs #chat-messages-list which are unique per page usually.
                             // But if user places admin widget and client widget on same page (unlikely) IDs conflict.
                             // For now assuming distinct pages.
                             var pid = <?php echo $project->id; ?>;
                             AlezuxProjects.currProjectId = pid;
                             // Trigger global load function if exposed, or trigger a click?
                             // Best is to call the function directly if accessible, or trigger an event.
                             // We'll rely on a small init script below.
                        }
                    }

                    jQuery(document).ready(function($){
                        // Auto-load chat to check for new messages?
                        // For now just init on click or load if active.
                        var pid = <?php echo $project->id; ?>;
                        if(typeof AlezuxProjects !== 'undefined') {
                            AlezuxProjects.currProjectId = pid;
                            // Expose load function in projects.js to be globally accessible or trigger it here
                            // We will modify projects.js to expose 'loadChatMessages' globally
                        }
                    });
                </script>
			</div>
		</div>

		</div> <!-- .alezux-client-project-dashboard -->
		<?php
	}



	private function render_preview( $step ) {
		// Dummy Project Data for Preview
		$dummy_project = (object) [
			'id' => 999,
			'name' => 'Proyecto Demo Cliente',
			'status' => 'in_progress',
			'current_step' => $step
		];

		// Dummy Manager Mock (Partial)
		$manager_mock = new class {
			public function get_project_meta($id, $key) {
				if ($key === 'design_proposal_url') return 'https://via.placeholder.com/800x500.png?text=Propuesta+de+Diseño';
				if ($key === 'briefing_data') return null; // Simulate fresh briefing for 'briefing' step
				return '';
			}
		};

		echo '<div class="alezux-client-project-dashboard">';
		$this->render_header( $dummy_project );
		echo '<div class="alezux-project-body">';

		switch ( $step ) {
			case 'briefing':
				// Force briefing form display
				$this->render_briefing_step( $dummy_project, $manager_mock );
				break;
			case 'design_review':
				$this->render_design_review_step( $dummy_project, $manager_mock );
				break;
			case 'in_progress':
				$this->render_progress_step( $dummy_project );
				break;
			case 'completed':
				$this->render_completed_step( $dummy_project );
				break;
		}

		echo '</div></div>';
	}

	private function render_header( $project, $all_projects = null ) {
		?>
		<div class="alezux-cp-header" style="flex-direction: column; align-items: stretch; gap: 0;">
			<div class="cp-top-row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0;">
				<div class="cp-title">
					<div style="display:flex; align-items:center;">
						<h2 style="margin-right: 15px;"><?php echo esc_html( $project->name ); ?></h2>
						<?php if ( $all_projects && count($all_projects) > 1 ) : ?>
							<div class="project-selector-wrapper">
								<select class="project-selector" onchange="window.location.search = '?alezux_pid=' + this.value">
									<?php foreach ($all_projects as $p) : ?>
										<option value="<?php echo esc_attr($p->id); ?>" <?php selected($p->id, $project->id); ?>>
											<?php echo esc_html($p->name); ?> (<?php echo esc_html($this->get_status_label($p->status)); ?>)
										</option>
									<?php endforeach; ?>
								</select>
							</div>
						<?php endif; ?>
					</div>
					<small>PROYECTO ACTIVO: #<?php echo esc_html($project->id); ?></small>
				</div>
				<div class="cp-status">
					<span class="alezux-status-pill status-<?php echo esc_attr( $project->status ); ?>">
						<?php echo esc_html( $this->get_status_label( $project->status ) ); ?>
					</span>
				</div>
			</div>

			<div class="alezux-client-tabs-nav" style="border-top: 1px solid rgba(255,255,255,0.2); margin-top: 20px; padding-top: 0;">
				<button class="client-tab-btn active" onclick="openClientTab('tab-overview')">
					<i class="eicon-info-circle-o"></i> Mi Proyecto
				</button>
				<button class="client-tab-btn" onclick="openClientTab('tab-chat')">
					<i class="eicon-comment-o"></i> Mensajes <span class="message-counter" style="display:none;">0</span>
				</button>
			</div>
		</div>
		<?php
	}

	private function render_briefing_step( $project, $manager ) {
		// Verificar si ya envió el briefing
		$briefing_data = $manager->get_project_meta( $project->id, 'briefing_data' );
		
		if ( ! empty( $briefing_data ) || $project->status === 'briefing_completed' ) {
			?>
			<div class="alezux-step-container center-text">
				<div style="font-size: 48px; margin-bottom: 20px;">📋</div>
				<h3>Briefing Recibido</h3>
				<p>¡Gracias! Hemos recibido la información de tu proyecto.</p>
				<p>Nuestro equipo está analizando los detalles. Pronto actualizaremos el estado a <strong>Diseño</strong>.</p>
				
				<div class="alezux-alert info" style="margin-top: 20px; display: inline-block; text-align: left;">
					<i class="eicon-info-circle-o"></i> Si necesitas modificar algo urgente, por favor contáctanos directamente.
				</div>
			</div>
			<?php
			return;
		}
		?>
		<div class="alezux-step-container">
			<div class="step-intro">
				<h3>👋 ¡Hola! Comencemos tu proyecto.</h3>
				<p>Para empezar, necesitamos conocer los detalles de tu marca. Por favor completa este formulario.</p>
			</div>
			
			<form id="client-briefing-form" class="alezux-briefing-form">
				<input type="hidden" name="project_id" value="<?php echo esc_attr( $project->id ); ?>">
				
				<div class="form-section">
					<h4>Información Básica</h4>
					<div class="alezux-form-group">
						<label>Nombre de tu Marca / Empresa</label>
						<input type="text" name="brand_name" required placeholder="Ej: TechSolutions C.A.">
					</div>
					<div class="alezux-form-group">
						<label>Slogan (Opcional)</label>
						<input type="text" name="slogan" placeholder="Ej: Innovación a tu alcance">
					</div>
				</div>

				<div class="form-section">
					<h4>Identidad Visual</h4>
					<div class="alezux-form-group">
						<label>¿Tienes colores corporativos? (Códigos HEX)</label>
						<input type="text" name="colors" placeholder="#FF5733, #333333">
					</div>
					<div class="alezux-form-group">
						<label>Describe tu negocio y público objetivo</label>
						<textarea name="business_desc" rows="4" required></textarea>
					</div>
				</div>

				<div class="form-section">
					<p><small>Al enviar este formulario, notificaremos al equipo para iniciar el diseño.</small></p>
					<button type="submit" class="alezux-btn alezux-btn-primary alezux-btn-lg">
						Enviar Briefing <i class="eicon-arrow-right"></i>
					</button>
				</div>
			</form>
		</div>
		<?php
	}

	private function render_design_review_step( $project, $manager ) {
		$design_url = $manager->get_project_meta( $project->id, 'design_proposal_url' );
		?>
		<div class="alezux-step-container center-text">
			<div class="step-intro">
				<h3>✨ Tu Diseño está Listo</h3>
				<p>Hemos creado una propuesta para ti. Revísala y aprueba para continuar con el desarrollo.</p>
			</div>

			<div class="design-preview-box">
				<?php if ( $design_url ) : ?>
					<a href="<?php echo esc_url( $design_url ); ?>" target="_blank" class="design-preview-link">
						<img src="<?php echo esc_url( $design_url ); ?>" alt="Propuesta de Diseño">
					</a>
					<a href="<?php echo esc_url( $design_url ); ?>" target="_blank" class="alezux-btn alezux-btn-secondary download-btn">
						<i class="eicon-download-bold"></i> Ver Diseño Completo
					</a>
				<?php else : ?>
					<div class="aleuz-alert warning">La imagen de la propuesta no se encuentra disponible.</div>
				<?php endif; ?>
			</div>

			<div class="approval-actions">
				<button id="btn-approve-design" data-id="<?php echo esc_attr( $project->id ); ?>" class="alezux-btn alezux-btn-success alezux-btn-block">
					<i class="eicon-check-circle-o"></i> Aprobar Diseño y Comenzar Desarrollo
				</button>
				
				<button id="btn-reject-modal-trigger" class="alezux-btn alezux-btn-ghost alezux-btn-sm">
					Solicitar Cambios / Rechazar
				</button>
			</div>

			<!-- Modal Rechazo (Hidden) -->
			<div id="reject-modal" style="display:none;" class="alezux-mini-modal">
				<h4>Solicitud de Cambios</h4>
				<textarea id="reject-feedback" placeholder="Describe qué cambios necesitas..."></textarea>
				<button id="btn-submit-rejection" data-id="<?php echo esc_attr( $project->id ); ?>" class="alezux-btn alezux-btn-primary alezux-btn-sm">Enviar Solicitud</button>
			</div>
		</div>
		<?php
	}

	private function render_progress_step( $project ) {
		?>
		<div class="alezux-step-container">
			<div class="step-intro">
				<h3>🏗️ Construyendo tu Sitio</h3>
				<p>Tu diseño ha sido aprobado. Nuestro equipo está trabajando en el código.</p>
			</div>

			<div class="project-timeline">
				<div class="timeline-item completed">
					<div class="point"></div>
					<div class="content">
						<strong>Briefing Recibido</strong>
						<span>Información recopilada</span>
					</div>
				</div>
				<div class="timeline-item completed">
					<div class="point"></div>
					<div class="content">
						<strong>Diseño Aprobado</strong>
						<span>Prototipo validado</span>
					</div>
				</div>
				<div class="timeline-item active">
					<div class="point pulse"></div>
					<div class="content">
						<strong>Desarrollo & Montaje</strong>
						<span>Estamos programando tu sitio web...</span>
					</div>
				</div>
				<div class="timeline-item">
					<div class="point"></div>
					<div class="content">
						<strong>Revisión Final</strong>
						<span>Entrega y Capacitación</span>
					</div>
				</div>
			</div>
			
			<div class="dev-note">
				<i class="eicon-info-circle-o"></i> Te notificaremos por correo cuando el sitio esté listo para revisión.
			</div>
		</div>
		<?php
	}

	private function render_completed_step( $project ) {
		?>
		<div class="alezux-step-container center-text">
			<div class="success-icon">🎉</div>
			<h3>¡Proyecto Finalizado!</h3>
			<p>Gracias por confiar en nosotros. Tu sitio web está en línea y funcionando.</p>
			<a href="#" class="alezux-btn alezux-btn-primary">Ver Documentación de Entrega</a>
		</div>
		<?php
	}

	private function get_status_label( $status ) {
		$labels = [
			'pending' => 'Pendiente',
			'briefing_completed' => 'Analizando Datos',
			'design_review' => 'Esperando Aprobación',
			'approved' => 'Aprobado',
			'in_progress' => 'En Desarrollo',
			'completed' => 'Finalizado'
		];
		return isset( $labels[$status] ) ? $labels[$status] : ucfirst( $status );
	}
}
