<?php
/**
 * Vista: Slide Lesson
 * @var array $lessons Lista de lecciones con 'title', 'permalink', 'image_url'.
 * @var array $settings Configuración de Elementor (opcional).
 */

if ( empty( $lessons ) ) {
	echo '<p>No se encontraron lecciones.</p>';
	return;
}

// Asegurar que $settings existe (para Shortcode)
if ( ! isset( $settings ) || ! is_array( $settings ) ) {
	$settings = [];
}

// Valores por defecto
$show_arrows = isset( $settings['show_arrows'] ) ? $settings['show_arrows'] : 'yes';

// Helpers para renderizar iconos de Elementor o fallback
$render_icon = function( $setting_key, $default_class ) use ( $settings ) {
	if ( isset( $settings[ $setting_key ] ) && ! empty( $settings[ $setting_key ]['value'] ) ) {
		\Elementor\Icons_Manager::render_icon( $settings[ $setting_key ], [ 'aria-hidden' => 'true' ] );
	} else {
		echo '<i class="' . esc_attr( $default_class ) . '"></i>';
	}
};
?>

<div class="alezux-slide-lesson-main-container">

	<?php 
	// Compatibilidad hacia atrás: si llega $lessons pero no $slide_groups, lo convertimos
	if ( empty( $slide_groups ) && ! empty( $lessons ) ) {
		$slide_groups = [
			[
				'title' => '',
				'lessons' => $lessons,
			]
		];
	}

	foreach ( $slide_groups as $group_index => $group ) : 
		$group_lessons = $group['lessons'];
		$group_title   = $group['title'];
		
		if ( empty( $group_lessons ) ) {
			continue;
		}
	?>
	
		<!-- Título del Separador (si existe) -->
		<?php if ( ! empty( $group_title ) ) : ?>
			<h3 class="alezux-slide-group-title"><?php echo esc_html( $group_title ); ?></h3>
		<?php endif; ?>

		<div class="alezux-slide-lesson-container">
			
			<?php if ( 'yes' === $show_arrows ) : ?>
				<div class="alezux-slide-nav alezux-slide-nav-prev" aria-label="Anterior">
					<?php $render_icon( 'prev_arrow_icon', 'fas fa-chevron-left' ); ?>
				</div>
			<?php endif; ?>
			
			<div class="alezux-slide-wrapper">
				<?php foreach ( $group_lessons as $lesson ) : ?>
					<?php if ( ! empty( $lesson['image_url'] ) ) : ?>
						<div class="alezux-slide-item">
							<a href="<?php echo esc_url( $lesson['permalink'] ); ?>" class="alezux-slide-link" title="<?php echo esc_attr( $lesson['title'] ); ?>">
								<img src="<?php echo esc_url( $lesson['image_url'] ); ?>" alt="<?php echo esc_attr( $lesson['title'] ); ?>" loading="lazy" />
								<div class="alezux-slide-content">
									<span class="alezux-slide-title"><?php echo esc_html( $lesson['title'] ); ?></span>
								</div>
							</a>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>

			<?php if ( 'yes' === $show_arrows ) : ?>
				<div class="alezux-slide-nav alezux-slide-nav-next" aria-label="Siguiente">
					<?php $render_icon( 'next_arrow_icon', 'fas fa-chevron-right' ); ?>
				</div>
			<?php endif; ?>
		</div>

	<?php endforeach; ?>

</div>
