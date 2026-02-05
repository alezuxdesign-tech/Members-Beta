<?php
namespace Alezux_Members\Modules\Marketing\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Marketing_Config_Widget extends Widget_Base {

	public function get_name() {
		return 'alezux_marketing_config';
	}

	public function get_title() {
		return __( 'Alezux Marketing Manager', 'alezux-members' );
	}

	public function get_icon() {
		return 'eicon-envelope';
	}

	public function get_categories() {
		return [ 'alezux-admin' ];
	}

	public function get_script_depends() {
		return [ 'alezux-marketing-admin-js' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Configuración', 'alezux-members' ),
			]
		);
		$this->end_controls_section();
	}

	protected function render() {
		if ( ! current_user_can( 'administrator' ) ) {
			echo '<p>Acceso restringido.</p>';
			return;
		}

		?>
		<div class="alezux-marketing-wrapper">
			
			<div class="marketing-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
				<h2 style="margin:0;">Gestor de Correos</h2>
				<button id="btn-marketing-settings" class="alezux-action-btn primary">
					<i class="fa fa-cog"></i> Configuración General
				</button>
			</div>

			<div class="alezux-table-responsive">
				<table class="alezux-table" id="marketing-templates-table">
					<thead>
						<tr>
							<th>Tipo de Correo</th>
							<th>Asunto Actual</th>
							<th>Estado</th>
							<th>Acciones</th>
						</tr>
					</thead>
					<tbody>
						<!-- AJAX Loaded -->
						<tr><td colspan="4" style="text-align:center;">Cargando plantillas...</td></tr>
					</tbody>
				</table>
			</div>

			<!-- MODAL TEMPLATE EDITOR -->
			<div id="marketing-template-modal" class="alezux-modal" style="display:none;">
				<div class="alezux-modal-content" style="max-width: 800px;">
					<span class="alezux-close-modal">&times;</span>
					<h3 id="modal-title">Editar Plantilla</h3>
					
					<form id="marketing-template-form">
						<input type="hidden" id="tpl-type" name="type">
						
						<div class="form-group">
							<label>Asunto:</label>
							<input type="text" id="tpl-subject" name="subject" class="alezux-input" required>
							<small>Variables disponibles: {{user.name}}, {{site_name}}...</small>
						</div>

						<div class="form-group">
							<label>Contenido (HTML):</label>
							<textarea id="tpl-content" name="content" rows="15" class="alezux-input" style="font-family: monospace;"></textarea>
							<small>Pega aquí tu código HTML. La etiqueta &lt;body&gt; es opcional.</small>
						</div>

						<div class="form-group" style="display: flex; align-items: center; gap: 10px; margin-top: 15px;">
							<label>Activo:</label>
							<label class="switch">
								<input type="checkbox" id="tpl-active" name="is_active">
								<span class="slider round"></span>
							</label>
						</div>

						<div class="form-actions" style="margin-top: 20px; text-align: right;">
							<button type="submit" class="alezux-action-btn primary">Guardar Cambios</button>
						</div>
					</form>
				</div>
			</div>

			<!-- MODAL SETTINGS -->
			<div id="marketing-settings-modal" class="alezux-modal" style="display:none;">
				<div class="alezux-modal-content">
					<span class="alezux-close-modal">&times;</span>
					<h3>Configuración General</h3>
					
					<form id="marketing-settings-form">
						<div class="form-group">
							<label>Nombre del Remitente (From Name):</label>
							<input type="text" id="set-from-name" name="from_name" class="alezux-input">
						</div>

						<div class="form-group">
							<label>Email del Remitente (From Email):</label>
							<input type="email" id="set-from-email" name="from_email" class="alezux-input">
						</div>

						<div class="form-group">
							<label>URL del Logo (Variable {{logo_url}}):</label>
							<input type="url" id="set-logo-url" name="logo_url" class="alezux-input">
							<small>Sube tu logo a Medios y pega la URL aquí.</small>
						</div>

						<div class="form-actions" style="margin-top: 20px; text-align: right;">
							<button type="submit" class="alezux-action-btn primary">Guardar Configuración</button>
						</div>
					</form>
				</div>
			</div>

		</div>
		<?php
	}
}
