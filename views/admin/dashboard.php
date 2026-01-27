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
		background-color: var(--alezux-bg-base) !important;
	}
	.alezux-dashboard-wrapper {
		margin: 20px;
		max-width: 1200px;
	}
	.alezux-tabs {
		display: flex;
		gap: 10px;
		margin-bottom: 20px;
		border-bottom: 1px solid var(--alezux-border-color);
		padding-bottom: 10px;
	}
	.alezux-tab-link {
		padding: 10px 20px;
		background: transparent;
		color: var(--alezux-text-muted);
		border: 1px solid transparent;
		cursor: pointer;
		font-weight: 600;
		border-radius: 50px;
		text-decoration: none;
		transition: all 0.3s ease;
	}
	.alezux-tab-link:hover, .alezux-tab-link.active {
		background: var(--alezux-primary);
		color: white;
	}
	
	.alezux-tab-panel {
		display: none;
		animation: fadeIn 0.3s ease;
	}
	.alezux-tab-panel.active {
		display: block;
	}
	
	@keyframes fadeIn {
		from { opacity: 0; transform: translateY(10px); }
		to { opacity: 1; transform: translateY(0); }
	}

	.alezux-form-group {
		margin-bottom: 20px;
	}
	.alezux-form-label {
		display: block;
		margin-bottom: 8px;
		color: var(--alezux-text-main);
	}
	.alezux-color-input {
		height: 40px;
		width: 100px;
		border: none;
		background: transparent;
		cursor: pointer;
	}
	
	.alezux-shortcode-item {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 15px;
		border-bottom: 1px solid var(--alezux-border-color);
	}
	.alezux-shortcode-tag {
		font-family: monospace;
		background: #333;
		padding: 5px 10px;
		border-radius: 5px;
		color: #fab1a0;
	}
</style>

<div class="alezux-dashboard-wrapper">
	<h1 class="alezux-title">🚀 Alezux Members <small style="font-size: 14px; opacity: 0.7;">v<?php echo ALEZUX_MEMBERS_VERSION; ?></small></h1>
	
	<?php if ( isset( $_GET['status'] ) && 'success' === $_GET['status'] ) : ?>
		<div class="alezux-card" style="padding: 15px; border-color: #00b894; margin-bottom: 20px;">
			<span style="color: #00b894;">¡Configuración guardada correctamente!</span>
		</div>
	<?php endif; ?>

	<div class="alezux-tabs">
		<a href="#" class="alezux-tab-link active" data-target="tab-settings">Configuración Global</a>
		<a href="#" class="alezux-tab-link" data-target="tab-shortcodes">Shortcodes</a>
	</div>

	<!-- TAB 1: SHORTCODES (Muevo esto para ser la segunda opcion segun request, pero el HTML es agnostico) -->
	
	<!-- TAB 1: CONFIGURACION (Solicitado como primera opcion) -->
	<div id="tab-settings" class="alezux-tab-panel active">
		<div class="alezux-card">
			<h2 class="alezux-title">🎨 Personalización Visual</h2>
			<p class="alezux-text">Define los colores maestros. Todos los bloques se actualizarán automáticamente.</p>
			
			<form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="POST">
				<input type="hidden" name="action" value="alezux_save_settings">
				<?php wp_nonce_field( 'alezux_save_settings_action', 'alezux_settings_nonce' ); ?>
				
				<div class="alezux-form-group">
					<label class="alezux-form-label">Color Primario (Acento)</label>
					<input type="color" name="alezux_primary_color" class="alezux-color-input" value="<?php echo esc_attr( $settings['primary_color'] ); ?>">
				</div>

				<div class="alezux-form-group">
					<label class="alezux-form-label">Fondo Base (Modo Oscuro)</label>
					<input type="color" name="alezux_bg_base" class="alezux-color-input" value="<?php echo esc_attr( $settings['bg_base'] ); ?>">
				</div>

				<div class="alezux-form-group">
					<label class="alezux-form-label">Fondo de Tarjetas</label>
					<input type="color" name="alezux_bg_card" class="alezux-color-input" value="<?php echo esc_attr( $settings['bg_card'] ); ?>">
				</div>

				<button type="submit" class="alezux-btn">Guardar Cambios</button>
			</form>
		</div>
	</div>

	<!-- TAB 2: SHORTCODES -->
	<div id="tab-shortcodes" class="alezux-tab-panel">
		<div class="alezux-card">
			<h2 class="alezux-title">🧩 Librería de Bloques (Shortcodes)</h2>
			<p class="alezux-text">Lista de todos los shortcodes disponibles generados por tus módulos Lego.</p>
			
			<?php if ( empty( $shortcodes ) ) : ?>
				<p class="alezux-text">No hay shortcodes registrados aún.</p>
			<?php else : ?>
				<div class="alezux-shortcodes-list">
					<?php foreach ( $shortcodes as $sc ) : ?>
						<div class="alezux-shortcode-item">
							<div>
								<span class="alezux-shortcode-tag">[<?php echo esc_html( $sc['tag'] ); ?>]</span>
								<p class="alezux-text" style="margin: 5px 0 0 0; font-size: 14px;"><?php echo esc_html( $sc['description'] ); ?></p>
							</div>
							<span style="font-size: 12px; opacity: 0.5;"><?php echo esc_html( $sc['module'] ); ?></span>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>

</div>
