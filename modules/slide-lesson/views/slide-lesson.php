<?php
/**
 * Vista: Slide Lesson
 * @var array $lessons Lista de lecciones con 'title', 'permalink', 'image_url'.
 */

if ( empty( $lessons ) ) {
	echo '<p>No se encontraron lecciones.</p>';
	return;
}
?>

<div class="alezux-slide-lesson-container">
	<div class="alezux-slide-wrapper">
		<?php foreach ( $lessons as $lesson ) : ?>
			<?php if ( ! empty( $lesson['image_url'] ) ) : ?>
				<div class="alezux-slide-item">
					<a href="<?php echo esc_url( $lesson['permalink'] ); ?>" class="alezux-slide-link" title="<?php echo esc_attr( $lesson['title'] ); ?>">
						<img src="<?php echo esc_url( $lesson['image_url'] ); ?>" alt="<?php echo esc_attr( $lesson['title'] ); ?>" loading="lazy" />
						<span class="alezux-slide-title"><?php echo esc_html( $lesson['title'] ); ?></span>
					</a>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
</div>
