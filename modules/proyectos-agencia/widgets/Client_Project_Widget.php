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
					'design_creation' => '2. Creación Diseño',
					'design_review' => '3. Revisión Diseño',
					'design_changes' => '4. Cambios Diseño',
					'in_progress' => '5. Desarrollo',
					'optimization' => '6. Optimización',
					'final_review' => '7. Revisión Final',
					'completed' => '8. Completado',
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
				<?php $this->render_project_timeline( $project->current_step, $project ); ?>
				<?php
				// Determine content to show: view_step OR current_step
				$view_step = isset($_GET['view_step']) ? $_GET['view_step'] : $project->current_step;

				switch ( $view_step ) {
					case 'briefing':
						$this->render_briefing_step( $project, $manager );
						break;
					case 'logo_creation':
						$this->render_logo_creation_step( $project );
						break;
					case 'logo_review':
						$this->render_logo_review_step( $project, $manager );
						break;
					case 'design_creation':
						$this->render_design_creation_step( $project );
						break;
					case 'design_review':
						$this->render_design_review_step( $project, $manager );
						break;
					case 'design_changes':
						$this->render_design_changes_step( $project );
						break;
					case 'in_progress':
						$this->render_progress_step( $project );
						break;
					case 'optimization':
						$this->render_optimization_step( $project );
						break;
					case 'final_review':
						$this->render_final_review_step( $project );
						break;
					case 'completed':
						$this->render_completed_step( $project );
						break;
					default:
						// Fallback to current step if view step is invalid or just show default
						$this->render_briefing_step( $project, $manager );
				}
				?>
			</div>
		</div>

		<?php $this->render_assets_tab( $project, $manager ); ?>

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

                        if(typeof AlezuxProjects !== 'undefined') {
                            if(tabName === 'tab-chat') {
                                var pid = <?php echo $project->id; ?>;
                                AlezuxProjects.currProjectId = pid;
                                if(AlezuxProjects.startChatPolling) {
                                    AlezuxProjects.startChatPolling(pid);
                                } else {
                                    // Fallback if JS not updated yet
                                    AlezuxProjects.loadChatMessages(pid); 
                                }
                            } else {
                                // Stop polling if leaving chat
                                if(AlezuxProjects.stopChatPolling) AlezuxProjects.stopChatPolling();
                            }
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
			case 'logo_creation':
				$this->render_logo_creation_step( $dummy_project );
				break;
			case 'logo_review':
				$this->render_logo_review_step( $dummy_project, $manager_mock );
				break;
			case 'design_creation':
				$this->render_design_creation_step( $dummy_project );
				break;
			case 'design_review':
				$this->render_design_review_step( $dummy_project, $manager_mock );
				break;
			case 'design_changes':
				$this->render_design_changes_step( $dummy_project );
				break;
			case 'in_progress':
				$this->render_progress_step( $dummy_project );
				break;
			case 'optimization':
				$this->render_optimization_step( $dummy_project );
				break;
			case 'final_review':
				$this->render_final_review_step( $dummy_project );
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
				<button class="client-tab-btn" onclick="openClientTab('tab-assets')" style="display: <?php echo $project->status === 'completed' ? 'inline-block' : 'none'; ?>">
					<i class="eicon-file-download"></i> Entregables
				</button>
				<button class="client-tab-btn" onclick="openClientTab('tab-chat')">
					<i class="eicon-comment-o"></i> Mensajes <span class="message-counter" style="display:none;">0</span>
				</button>
			</div>
		</div>
		<?php
	}

	private function render_project_timeline( $current_step, $project = null ) {
		$steps = [
			'briefing' => [
				'label' => 'Briefing', 
				'desc' => 'Datos'
			]
		];

		$needs_logo = get_post_meta($project->id, 'needs_logo_design', true) === 'yes';

		if ($needs_logo || $current_step === 'logo_creation' || $current_step === 'logo_review') {
			$steps['logo_creation'] = ['label' => 'Logo', 'desc' => 'Diseño'];
			$steps['logo_review'] = ['label' => 'Rev. Logo', 'desc' => 'Feedback'];
		}

		$steps += [
			'design_creation' => ['label' => 'Creación', 'desc' => 'Diseño Web'],
			'design_review' => ['label' => 'Revisión', 'desc' => 'Web'],
			'design_changes' => ['label' => 'Cambios', 'desc' => 'Ajustes'],
			'in_progress' => ['label' => 'Desarrollo', 'desc' => 'Código'],
			'optimization' => ['label' => 'Optimización', 'desc' => 'SEO/Vel'],
			'final_review' => ['label' => 'Final', 'desc' => 'Entrega'],
			'completed' => ['label' => 'Listo', 'desc' => 'Online']
		];

		$keys = array_keys( $steps );
		$current_index = array_search( $current_step, $keys );
		if ( $current_index === false ) $current_index = 0;

		// Determine the active tab based on query param or current step
		$active_tab = isset($_GET['view_step']) ? $_GET['view_step'] : $current_step;

		echo '<div class="project-timeline">';
		foreach ( $steps as $key => $info ) {
			$index = array_search( $key, $keys );
			$class = '';
			$icon = '';
			$is_clickable = true; // Make all steps clickable for navigation

			if ( $index < $current_index ) {
				$class = 'completed';
				$icon = '<i class="eicon-check"></i>';
			} elseif ( $index == $current_index ) {
				$class = 'current'; // Mark current progress
				$icon = '<i class="eicon-loading eicon-animation-spin"></i>'; 
			}

			if ($key === $active_tab) {
				$class .= ' active-view'; // Mark currently viewing
			}

			// Onclick handler to switch view without page reload (or simpler with reload for now)
			// Using ?view_step parameter to control what is rendered below
			$onclick = "window.location.search = '?alezux_pid=" . $project->id . "&view_step=" . $key . "'";

			echo '<div class="timeline-item ' . esc_attr( $class ) . '" onclick="' . $onclick . '" style="cursor:pointer;">';
			echo '<div class="point ' . ( $class === 'current' ? 'pulse' : '' ) . '">' . $icon . '</div>';
			echo '<div class="content"><strong>' . esc_html( $info['label'] ) . '</strong><span>' . esc_html( $info['desc'] ) . '</span></div>';
			echo '</div>';
		}
		echo '</div>';
	}

	private function render_briefing_step( $project, $manager ) {
		// Modo Edición Siempre Activo si se selecciona
		$is_submitted = $project->status === 'briefing_completed' || $project->status !== 'pending';
		$briefing_data = $manager->get_project_meta( $project->id, 'briefing_data' );
		
		if ( is_string( $briefing_data ) ) {
			$briefing_data = json_decode( $briefing_data, true );
		}
		
		// Helper to get value
		$get_val = function($key) use ($briefing_data) {
			return isset($briefing_data[$key]) ? esc_attr($briefing_data[$key]) : '';
		};

		// Si ya se envió, mostramos mensaje pero permitimos editar
		?>
		<div class="alezux-step-container">
			<?php if($is_submitted): ?>
				<div class="alezux-alert success">
					<i class="eicon-check-circle"></i> Briefing enviado. Puedes actualizar los datos si es necesario.
				</div>
			<?php else: ?>
				<div class="step-intro">
					<h3>👋 ¡Hola! Comencemos tu proyecto.</h3>
					<p>Para empezar, necesitamos conocer los detalles de tu marca. Por favor completa este formulario.</p>
				</div>
			<?php endif; ?>
			
			<form id="client-briefing-form" class="alezux-briefing-form" enctype="multipart/form-data">
				<input type="hidden" name="project_id" value="<?php echo esc_attr( $project->id ); ?>">
				<input type="hidden" name="is_update" value="<?php echo $is_submitted ? '1' : '0'; ?>">
				
				<div class="form-section">
					<h4>Datos Fiscales y Legales</h4>
					<div class="alezux-form-group">
						<label class="high-contrast-label">Nombre completo o Razón Social</label>
						<input type="text" name="legal_name" value="<?php echo $get_val('legal_name'); ?>" placeholder="Ej: TechSolutions S.L.">
					</div>
					<div class="alezux-form-group">
						<label class="high-contrast-label">CIF / NIF / NIT</label>
						<input type="text" name="tax_id" value="<?php echo $get_val('tax_id'); ?>" placeholder="Ej: B-12345678">
					</div>
					<div class="alezux-form-group">
						<label class="high-contrast-label">Dirección Fiscal Completa</label>
						<input type="text" name="fiscal_address" value="<?php echo $get_val('fiscal_address'); ?>" placeholder="Calle, Número, CP, Ciudad...">
					</div>
					<div class="alezux-form-group">
						<label class="high-contrast-label">Registro Mercantil (Opcional)</label>
						<input type="text" name="commercial_registry" value="<?php echo $get_val('commercial_registry'); ?>" placeholder="Tomo, Libro, Folio...">
					</div>
					<div class="alezux-form-group">
						<label class="high-contrast-label">Ciudad o País (Jurisdicción Legal)</label>
						<input type="text" name="jurisdiction" value="<?php echo $get_val('jurisdiction'); ?>" placeholder="Ej: Madrid, España">
					</div>
				</div>

				<div class="form-section">
					<h4>Información de Contacto</h4>
					<div class="alezux-form-group">
						<label class="high-contrast-label">Teléfono de Contacto</label>
						<input type="text" name="phone" value="<?php echo $get_val('phone'); ?>" placeholder="+34 600 000 000">
					</div>
					<div class="alezux-form-group">
						<label class="high-contrast-label">WhatsApp (Atención al Cliente)</label>
						<input type="text" name="whatsapp" value="<?php echo $get_val('whatsapp'); ?>" placeholder="Para botón de contacto en la web">
					</div>
					<div class="alezux-form-group">
						<label class="high-contrast-label">Correo Electrónico de Contacto</label>
						<input type="email" name="contact_email" value="<?php echo $get_val('contact_email'); ?>" placeholder="hola@miempresa.com">
					</div>
					<div class="alezux-form-group">
						<label class="high-contrast-label">Correo para Privacidad / Baja (Si es diferente)</label>
						<input type="email" name="privacy_email" value="<?php echo $get_val('privacy_email'); ?>" placeholder="privacidad@miempresa.com">
					</div>
					<div class="alezux-form-group">
						<label class="high-contrast-label">Correo del DPO / Responsable de Datos (Si aplica)</label>
						<input type="email" name="dpo_email" value="<?php echo $get_val('dpo_email'); ?>" placeholder="dpo@miempresa.com">
					</div>
				</div>

				<div class="form-section">
					<h4>Detalles del Negocio y Marca</h4>
					<div class="alezux-form-group">
						<label class="high-contrast-label">Nombre Comercial o de Marca</label>
						<input type="text" name="brand_name" value="<?php echo $get_val('brand_name'); ?>" placeholder="Ej: TechSolutions">
					</div>
					
					<!-- Logo Section -->
					<div class="alezux-form-group">
						<label class="high-contrast-label">¿Tienes un Logotipo diseñado?</label>
						<select name="has_logo" id="has_logo_selector" class="alezux-select">
							<option value="yes" <?php echo $get_val('has_logo') === 'yes' ? 'selected' : ''; ?>>Sí, ya tengo un logo (Subir archivo)</option>
							<option value="no" <?php echo $get_val('has_logo') === 'no' ? 'selected' : ''; ?>>No, necesito que lo diseñen</option>
						</select>
					</div>

					<div class="alezux-form-group" id="logo_upload_section">
						<label class="high-contrast-label">Subir Logotipo</label>
						<div class="logo-upload-wrapper">
							<input type="file" name="logo_file" id="logo_file" accept="image/*,.pdf,.svg,.eps,.ai">
							<p class="description"><small>Sube tu logo (PNG, SVG, PDF, AI).</small></p>
						</div>
					</div>

					<div class="alezux-form-group" id="logo_details_section" style="display:none;">
						<label class="high-contrast-label">Referencias para el Logo</label>
						<textarea name="logo_details" rows="3" placeholder="Descríbenos cómo te gustaría tu logo, estilo, formas, etc..."><?php echo $get_val('logo_details'); ?></textarea>
					</div>

					<script>
					jQuery(document).ready(function($){
						function toggleLogoSections() {
							var val = $('#has_logo_selector').val();
							if(val === 'yes') {
								$('#logo_upload_section').show();
								$('#logo_details_section').hide();
							} else {
								$('#logo_upload_section').hide();
								$('#logo_details_section').show();
							}
						}
						$('#has_logo_selector').on('change', toggleLogoSections);
						toggleLogoSections(); // Run on init
					});
					</script>

					<!-- Color Picker Section -->
					<div class="alezux-form-group">
						<label class="high-contrast-label">Colores Corporativos</label>
						
						<div class="alezux-color-picker-control">
							<div class="color-input-group">
								<input type="color" id="briefing-color-picker" value="#333333">
								<input type="text" id="briefing-color-hex" value="#333333" placeholder="#333333" maxlength="7">
								<button type="button" id="btn-add-color-manual" class="alezux-btn alezux-btn-sm alezux-btn-primary">
									<i class="eicon-plus"></i> Añadir
								</button>
							</div>
							<p class="description"><small>Selecciona un color y pulsa "Añadir".</small></p>
						</div>

						<div id="alezux-color-palette-container" class="alezux-color-palette">
							<!-- Dynamic items will be added here by JS -->
						</div>
						<?php
						$colors_json = isset($briefing_data['brand_colors']) ? json_encode($briefing_data['brand_colors']) : '[]';
						?>
						<input type="hidden" name="brand_colors" id="brand_colors_input" value="<?php echo esc_attr($colors_json); ?>">
					</div>

					<div class="alezux-form-group">
						<label class="high-contrast-label">URL del Sitio Web (Si ya tienes dominio)</label>
						<input type="text" name="website_url" value="<?php echo $get_val('website_url'); ?>" placeholder="https://www.miempresa.com">
					</div>
					<div class="alezux-form-group">
						<label class="high-contrast-label">Actividad del Negocio (Productos/Servicios)</label>
						<textarea name="business_activity" rows="3" placeholder="Descríbenos qué vendes o qué servicios ofreces..."><?php echo $get_val('business_activity'); ?></textarea>
					</div>
					<div class="alezux-form-group">
						<label class="high-contrast-label">Sectores de Actividad (Para promociones)</label>
						<input type="text" name="business_sectors" value="<?php echo $get_val('business_sectors'); ?>" placeholder="Ej: Educación, Salud, Marketing...">
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



	private function render_logo_creation_step( $project ) {
		?>
		<div class="alezux-step-container center-text">
			<div style="font-size: 48px; margin-bottom: 20px;">🎨</div>
			<h3>Diseñando tu Logotipo</h3>
			<p>Nuestro equipo creativo está trabajando en las propuestas para tu identidad visual.</p>
			<p>Te notificaremos pronto para que puedas revisar las opciones.</p>
		</div>
		<?php
	}

	private function render_logo_review_step( $project, $manager ) {
		// Similar to design review but for logo
		// Assuming we use same meta 'design_proposal_url' or a new one?
		// Let's assume we use 'logo_proposal_url' or re-use 'design_proposal_url' contextually if simple.
		// For robustness, let's look for 'logo_proposal_url' or fallback to design url.
		$logo_url = $manager->get_project_meta( $project->id, 'logo_proposal_url' );
		if(!$logo_url) $logo_url = $manager->get_project_meta( $project->id, 'design_proposal_url' );

		?>
		<div class="alezux-step-container center-text">
			<div class="step-intro">
				<h3>✨ Propuesta de Logotipo</h3>
				<p>Hemos diseñado una identidad para tu marca. Por favor revísala.</p>
			</div>

			<div class="design-preview-box">
				<?php if ( $logo_url ) : ?>
					<a href="<?php echo esc_url( $logo_url ); ?>" target="_blank" class="design-preview-link">
						<img src="<?php echo esc_url( $logo_url ); ?>" alt="Propuesta de Logo" style="max-height:300px; object-fit:contain;">
					</a>
					<a href="<?php echo esc_url( $logo_url ); ?>" target="_blank" class="alezux-btn alezux-btn-secondary download-btn">
						<i class="eicon-download-bold"></i> Ver Imagen
					</a>
				<?php else : ?>
					<div class="alezux-alert warning">La imagen de la propuesta no se encuentra disponible aún.</div>
				<?php endif; ?>
			</div>

			<div class="approval-actions">
				<button id="btn-approve-logo" data-id="<?php echo esc_attr( $project->id ); ?>" class="alezux-btn alezux-btn-success alezux-btn-block">
					<i class="eicon-check-circle-o"></i> Aprobar Logo
				</button>
				
				<button id="btn-reject-modal-trigger-logo" class="alezux-btn alezux-btn-ghost alezux-btn-sm" onclick="jQuery('#reject-modal').slideToggle();">
					Solicitar Cambios
				</button>
			</div>
			
			<!-- Reuse Rejection Modal logic via JS, just change context? -->
			<!-- Ideally we need to tell backend this is LOGO rejection, not web design rejection. -->
			<!-- For simplicity, we can use same endpoint but maybe add a data-type to the button? -->
			<!-- Or just use the generic feedback loop and let admin decipher context from phase. -->
			
			<div id="reject-modal" style="display:none;" class="alezux-mini-modal">
				<h4>Comentarios sobre el Logo</h4>
				<textarea id="reject-feedback" placeholder="Describe qué cambios necesitas en el logo..."></textarea>
				<button id="btn-submit-rejection" data-id="<?php echo esc_attr( $project->id ); ?>" class="alezux-btn alezux-btn-primary alezux-btn-sm">Enviar Comentarios</button>
			</div>
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
			
			<div class="dev-note">
				<i class="eicon-info-circle-o"></i> Te notificaremos por correo cuando el sitio esté listo para revisión.
			</div>
		</div>
		<?php
	}

	private function render_design_creation_step( $project ) {
		?>
		<div class="alezux-step-container center-text">
			<div style="font-size: 48px; margin-bottom: 20px;">🎨</div>
			<h3>Creando tu Diseño</h3>
			<p>Nuestro equipo de diseño está trabajando en tu propuesta visual basada en el briefing.</p>
			<p>Te notificaremos pronto para que puedas revisar el prototipo.</p>
		</div>
		<?php
	}

	private function render_design_changes_step( $project ) {
		?>
		<div class="alezux-step-container center-text">
			<div style="font-size: 48px; margin-bottom: 20px;">✏️</div>
			<h3>Aplicando Cambios</h3>
			<p>Hemos recibido tu feedback. Estamos ajustando el diseño según tus indicaciones.</p>
			<p>Pronto recibirás la versión actualizada para tu aprobación.</p>
		</div>
		<?php
	}

	private function render_optimization_step( $project ) {
		?>
		<div class="alezux-step-container center-text">
			<div style="font-size: 48px; margin-bottom: 20px;">🚀</div>
			<h3>Optimizando Rendimiento</h3>
			<p>El desarrollo principal ha terminado. Ahora estamos ajustando la velocidad, SEO y seguridad.</p>
			<div class="alezux-alert info" style="margin-top: 20px; display: inline-block;">
				<i class="eicon-loading eicon-animation-spin"></i> Realizando pruebas de carga y auditoría final...
			</div>
		</div>
		<?php
	}

	private function render_final_review_step( $project ) {
		?>
		<div class="alezux-step-container center-text">
			<div style="font-size: 48px; margin-bottom: 20px;">👀</div>
			<h3>Revisión Final</h3>
			<p>El proyecto está casi listo. Por favor revisa el sitio en el enlace de desarrollo.</p>
			
			<div class="approval-actions" style="margin-top: 30px;">
				<a href="#" target="_blank" class="alezux-btn alezux-btn-secondary" style="margin-bottom: 15px;">
					<i class="eicon-link"></i> Ver Sitio Staging
				</a>
				<br>
				<button id="btn-approve-final" data-id="<?php echo esc_attr( $project->id ); ?>" class="alezux-btn alezux-btn-success">
					<i class="eicon-check-circle-o"></i> Aprobar y Publicar
				</button>
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

	private function render_assets_tab( $project, $manager ) {
		$meta = $manager->get_all_project_meta( $project->id );
		?>
		<div id="tab-assets" class="client-tab-content">
			<div class="alezux-project-body">
				<div class="alezux-step-container">
					<h3><i class="eicon-file-download"></i> Entregables y Recursos</h3>
					<p>Aquí encontrarás todos los archivos finales y accesos de tu proyecto.</p>

					<div class="assets-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 30px;">
						
						<!-- Tarjeta de Logos -->
						<div class="asset-card alezux-card">
							<h4><i class="eicon-image-bold"></i> Kit de Marca</h4>
							<p class="description">Descarga tu logotipo en alta calidad y vectores.</p>
							
							<?php if ( ! empty( $meta['final_logo_url'] ) ) : ?>
								<div class="asset-preview">
									<img src="<?php echo esc_url( $meta['final_logo_url'] ); ?>" alt="Logo Final" style="max-height: 80px; display: block; margin: 0 auto 15px;">
								</div>
								<a href="<?php echo esc_url( $meta['final_logo_url'] ); ?>" download class="alezux-btn alezux-btn-primary alezux-btn-block">
									<i class="eicon-download-bold"></i> Descargar Logo
								</a>
							<?php else : ?>
								<div class="alezux-alert info">
									Tu kit de marca estará disponible aquí al finalizar la fase de diseño.
								</div>
							<?php endif; ?>
						</div>

						<!-- Tarjeta de Credenciales -->
						<div class="asset-card alezux-card">
							<h4><i class="eicon-lock-user"></i> Credenciales de Acceso</h4>
							<p class="description">Accesos administrativos a tu sitio web.</p>

							<?php if ( ! empty( $meta['access_user'] ) ) : ?>
								<div class="credentials-box" style="background: rgba(0,0,0,0.05); padding: 15px; border-radius: 8px;">
									<div class="cred-row" style="margin-bottom: 10px;">
										<strong>URL:</strong> 
										<a href="<?php echo isset($meta['site_url']) ? esc_url( $meta['site_url'] ) : '#'; ?>" target="_blank">Ver Sitio <i class="eicon-external-link-square"></i></a>
									</div>
									<div class="cred-row" style="margin-bottom: 10px;">
										<strong>Usuario:</strong> 
										<code id="cred-user"><?php echo esc_html( $meta['access_user'] ); ?></code>
										<button class="copy-btn" onclick="navigator.clipboard.writeText('<?php echo esc_js($meta['access_user']); ?>')"><i class="eicon-copy"></i></button>
									</div>
									<div class="cred-row">
										<strong>Contraseña:</strong> 
										<div style="display: flex; align-items: center; justify-content: space-between; background: #fff; padding: 5px; border-radius: 4px; border: 1px solid #ddd;">
											<input type="password" value="<?php echo isset($meta['access_pass']) ? esc_attr( $meta['access_pass'] ) : ''; ?>" id="cred-pass-input" readonly style="border:none; width: 100%; background: transparent;">
											<button onclick="var i = document.getElementById('cred-pass-input'); i.type = i.type === 'password' ? 'text' : 'password';"><i class="eicon-eye"></i></button>
										</div>
									</div>
								</div>
							<?php else : ?>
								<div class="alezux-alert info">
									Las credenciales se generarán al finalizar el desarrollo.
								</div>
							<?php endif; ?>
						</div>

					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
