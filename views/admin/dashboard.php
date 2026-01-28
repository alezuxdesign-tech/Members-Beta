<?php
/**
 * Vista del Dashboard de Administración
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<style>
	/* Estilos específicos para el Dashboard que sobrescriben WP Admin */
	#wpcontent {
		padding-left: 0 !important;
		background-color: #0f0f0f !important; /* Fallback */
		background-color: var(--alezux-bg-base, #0f0f0f) !important;
	}
	.alezux-dashboard-wrapper {
		margin: 20px;
		max-width: 1200px;
		font-family: 'Inter', system-ui, -apple-system, sans-serif;
	}
	.alezux-tabs {
		display: flex;
		gap: 10px;
		margin-bottom: 20px;
		border-bottom: 1px solid #333;
		padding-bottom: 10px;
	}
	.alezux-tab-link {
		padding: 10px 25px;
		background: transparent;
		color: #888;
		border: 1px solid transparent;
		cursor: pointer;
		font-weight: 600;
		border-radius: 50px;
		text-decoration: none;
		transition: all 0.3s ease;
		outline: none !important;
		box-shadow: none !important;
	}
	.alezux-tab-link:hover {
		color: #fff;
		background: #222;
	}
	.alezux-tab-link.active {
		background: var(--alezux-primary, #6c5ce7);
		color: white;
	}
	
	.alezux-tab-panel {
		/* El control de display se hará via JS inline style para asegurar prioridad */
		animation: fadeIn 0.3s ease;
	}
	
	@keyframes fadeIn {
		from { opacity: 0; transform: translateY(5px); }
		to { opacity: 1; transform: translateY(0); }
	}

	.alezux-form-group {
		margin-bottom: 25px;
	}
	.alezux-form-label {
		display: block;
		margin-bottom: 10px;
		color: #eee;
		font-weight: 500;
	}
	.alezux-color-input {
		height: 40px;
		width: 100px;
		border: none;
		background: transparent;
		cursor: pointer;
		padding: 0;
	}
	
	/* Estilos de Tarjetas */
	.alezux-card {
		background: #1a1a1a;
		padding: 30px;
		border-radius: 16px;
		box-shadow: 0 4px 20px rgba(0,0,0,0.2);
		border: 1px solid #333;
		margin-bottom: 20px;
	}
	.alezux-title {
		color: #ffffff;
		margin-top: 0;
		margin-bottom: 20px;
		font-weight: 700;
		font-size: 1.5em;
	}
	.alezux-text {
		color: #ccc;
		margin-bottom: 20px;
		line-height: 1.6;
		font-size: 14px;
	}

	/* Shortcodes Items */
	.alezux-shortcode-item {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 20px;
		background: #252525;
		border-radius: 12px;
		margin-bottom: 15px;
		border: 1px solid #444;
		transition: all 0.2s ease;
	}
	.alezux-shortcode-item:hover {
		transform: translateY(-2px);
		border-color: var(--alezux-primary, #6c5ce7);
		background: #2a2a2a;
	}
	.alezux-shortcode-tag {
		font-family: 'Courier New', monospace;
		background: #111;
		color: #fab1a0;
		padding: 6px 10px;
		border-radius: 6px;
		font-size: 14px;
		border: 1px solid #333;
		display: inline-block;
	}
	.alezux-copy-btn {
		background: #333;
		color: white;
		border: none;
		padding: 8px 16px;
		border-radius: 6px;
		cursor: pointer;
		font-size: 13px;
		transition: all 0.2s ease;
		display: flex;
		align-items: center;
		gap: 6px;
		font-weight: 500;
	}
	.alezux-copy-btn:hover {
		background: var(--alezux-primary, #6c5ce7);
		transform: scale(1.05);
	}
	.alezux-copy-btn.copied {
		background: #00b894;
		color: black;
	}
</style>

<div class="alezux-dashboard-wrapper">
	<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px;">
		<h1 class="alezux-title" style="margin: 0; font-size: 28px;">
			🚀 Alezux Members 
			<span style="font-size: 12px; background: #333; padding: 4px 8px; border-radius: 4px; vertical-align: middle; margin-left: 10px;">v<?php echo ALEZUX_MEMBERS_VERSION; ?></span>
		</h1>
	</div>
	
	<?php if ( isset( $_GET['status'] ) && 'success' === $_GET['status'] ) : ?>
		<div class="alezux-notice" style="background: rgba(0, 184, 148, 0.1); border: 1px solid #00b894; color: #00b894; padding: 15px; border-radius: 8px; margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
			<span class="dashicons dashicons-yes"></span>
			<strong>¡Configuración guardada correctamente!</strong>
		</div>
	<?php endif; ?>

	<?php if ( isset( $_GET['status'] ) && 'notification_sent' === $_GET['status'] ) : ?>
		<div class="alezux-notice" style="background: rgba(0, 184, 148, 0.1); border: 1px solid #00b894; color: #00b894; padding: 15px; border-radius: 8px; margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
			<span class="dashicons dashicons-megaphone"></span>
			<strong>¡Notificación de prueba enviada correctamente!</strong>
		</div>
	<?php endif; ?>

	<!-- TABS NAVIGATION -->
	<div class="alezux-tabs">
		<a href="javascript:void(0);" onclick="openAlezuxTab(event, 'tab-settings')" class="alezux-tab-link active" id="link-tab-settings">Configuración Global</a>
		<a href="javascript:void(0);" onclick="openAlezuxTab(event, 'tab-shortcodes')" class="alezux-tab-link" id="link-tab-shortcodes">Shortcodes</a>
		<a href="javascript:void(0);" onclick="openAlezuxTab(event, 'tab-notifications')" class="alezux-tab-link" id="link-tab-notifications">Notificaciones</a>
	</div>

	<!-- TAB 1: CONFIGURACION -->
	<div id="tab-settings" class="alezux-tab-panel" style="display: block;">
		<div class="alezux-card">
			<h2 class="alezux-title">🎨 Personalización Visual</h2>
			<p class="alezux-text">Define los colores maestros de tu plataforma. Todos los bloques se actualizarán automáticamente.</p>
			
			<form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="POST">
				<input type="hidden" name="action" value="alezux_save_settings">
				<?php wp_nonce_field( 'alezux_save_settings_action', 'alezux_settings_nonce' ); ?>
				
				<div class="alezux-form-group">
					<label class="alezux-form-label">Color Primario (Accent)</label>
					<div style="display: flex; gap: 15px; align-items: center;">
						<div>
							<input type="color" name="alezux_primary_color" class="alezux-color-input" value="<?php echo esc_attr( $settings['primary_color'] ); ?>">
							<span style="font-size: 12px; color: #777; display: block; text-align: center;">Normal</span>
						</div>
						<div>
							<input type="color" name="alezux_primary_hover" class="alezux-color-input" value="<?php echo esc_attr( $settings['primary_hover'] ); ?>">
							<span style="font-size: 12px; color: #777; display: block; text-align: center;">Hover</span>
						</div>
					</div>
				</div>

				<div style="border-top: 1px solid #333; margin: 25px 0;"></div>

				<div class="alezux-form-group">
					<h3 class="alezux-title" style="font-size: 18px; margin-bottom: 20px;">📐 Apariencia de Componentes</h3>
					
					<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
						<div>
							<label class="alezux-form-label">Radio del Borde</label>
							<input type="text" name="alezux_border_radius" 
								   style="background: #252525; border: 1px solid #444; color: white; padding: 10px; border-radius: 8px; width: 100%; box-sizing: border-box;"
								   value="<?php echo esc_attr( $settings['border_radius'] ); ?>" placeholder="50px">
						</div>
						
						<div>
							<label class="alezux-form-label">Color del Borde</label>
							<input type="color" name="alezux_border_color" class="alezux-color-input" value="<?php echo esc_attr( $settings['border_color'] ); ?>">
						</div>
					</div>

					<div style="margin-top: 20px;">
						<label class="alezux-form-label">Sombra (Box Shadow)</label>
						<input type="text" name="alezux_box_shadow" 
							   style="background: #252525; border: 1px solid #444; color: white; padding: 10px; border-radius: 8px; width: 100%; box-sizing: border-box;"
							   value="<?php echo esc_attr( $settings['box_shadow'] ); ?>" placeholder="0 10px 30px rgba(0, 0, 0, 0.3)">
					</div>
				</div>

				<div style="border-top: 1px solid #333; margin: 25px 0;"></div>

				<div class="alezux-form-group">
					<h3 class="alezux-title" style="font-size: 18px; margin-bottom: 20px;">🌑 Modo Oscuro / Fondo</h3>
					<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px;">
						<div>
							<label class="alezux-form-label">Fondo Principal</label>
							<input type="color" name="alezux_bg_base" class="alezux-color-input" value="<?php echo esc_attr( $settings['bg_base'] ); ?>">
						</div>
						<div>
							<label class="alezux-form-label">Fondo Tarjetas</label>
							<input type="color" name="alezux_bg_card" class="alezux-color-input" value="<?php echo esc_attr( $settings['bg_card'] ); ?>">
						</div>
					</div>
				</div>

				<div style="margin-top: 40px; text-align: right;">
					<button type="submit" class="button button-primary" 
							style="background: var(--alezux-primary, #6c5ce7); border-color: var(--alezux-primary, #6c5ce7); padding: 5px 30px; font-size: 16px; font-weight: 600; height: auto; line-height: 2;">
						Guardar Cambios
					</button>
				</div>
			</form>
		</div>
	</div>

	<!-- TAB 2: SHORTCODES -->
	<div id="tab-shortcodes" class="alezux-tab-panel" style="display: none;">
		<div class="alezux-card">
			<h2 class="alezux-title">🧩 Librería de Shortcodes</h2>
			<p class="alezux-text">Utiliza estos códigos cortos para insertar los módulos de Alezux en tus páginas de Elementor o editor de bloques.</p>
			
			<?php if ( empty( $shortcodes ) ) : ?>
				<div style="padding: 40px; text-align: center; border: 1px dashed #444; border-radius: 10px; color: #666;">
					<span class="dashicons dashicons-info" style="font-size: 30px; width: 30px; height: 30px; margin-bottom: 10px;"></span>
					<p>No se encontraron shortcodes registrados.</p>
				</div>
			<?php else : ?>
				<div class="alezux-shortcodes-list">
					<?php foreach ( $shortcodes as $sc ) : ?>
						<div class="alezux-shortcode-item">
							<div style="flex: 1; padding-right: 20px;">
								<div class="alezux-shortcode-header" style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
									<span class="alezux-shortcode-tag">[<?php echo esc_html( $sc['tag'] ); ?>]</span>
									<span style="font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #777; border: 1px solid #333; padding: 3px 8px; border-radius: 100px;"><?php echo esc_html( $sc['module'] ); ?></span>
								</div>
								<p class="alezux-text" style="margin: 0; font-size: 13px; color: #aaa;"><?php echo esc_html( $sc['description'] ); ?></p>
							</div>
							
							<button class="alezux-copy-btn" onclick="copyToClipboard(this, '[<?php echo esc_js( $sc['tag'] ); ?>]')">
								<span class="dashicons dashicons-admin-page"></span>
								<span class="btn-text">Copiar</span>
							</button>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>

</div>

<script>
// Funciones globales para evitar problemas de scope
function openAlezuxTab(evt, tabName) {
	evt.preventDefault();
	
	// 1. Ocultar todos los paneles
	var tabcontent = document.getElementsByClassName("alezux-tab-panel");
	for (var i = 0; i < tabcontent.length; i++) {
		tabcontent[i].style.display = "none";
	}

	// 2. Desactivar todos los links
	var tablinks = document.getElementsByClassName("alezux-tab-link");
	for (var i = 0; i < tablinks.length; i++) {
		tablinks[i].className = tablinks[i].className.replace(" active", "");
	}

	// 3. Mostrar el panel actual y activar el link actual
	document.getElementById(tabName).style.display = "block";
	evt.currentTarget.className += " active";
}

function copyToClipboard(btnElement, text) {
	navigator.clipboard.writeText(text).then(() => {
		// Feedback visual
		btnElement.classList.add('copied');
		var textSpan = btnElement.querySelector('.btn-text');
		var originalText = textSpan.innerText;
		textSpan.innerText = '¡Copiado!';
		
		setTimeout(() => {
			btnElement.classList.remove('copied');
			textSpan.innerText = originalText; // Restaurar texto original (usualmente 'Copiar')
			if(originalText !== 'Copiar') textSpan.innerText = 'Copiar'; // Fallback por si acaso
		}, 2000);
	}).catch(err => {
		console.error('Error al copiar:', err);
		alert('No se pudo copiar el texto automáticamente.');
	});
}
</script>
