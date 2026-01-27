<?php
/**
 * Vista: Tarjeta de Módulo
 * Variables disponibles: contexto de The Loop (get_the_title, etc.)
 */
?>
<div class="alezux-card alezux-module-card">
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="alezux-module-image">
			<?php the_post_thumbnail( 'medium' ); ?>
		</div>
	<?php endif; ?>
	
	<div class="alezux-module-content">
		<h4 class="alezux-title alezux-module-title"><?php the_title(); ?></h4>
		<div class="alezux-text alezux-module-excerpt">
			<?php the_excerpt(); ?>
		</div>
		<a href="<?php the_permalink(); ?>" class="alezux-btn alezux-btn-sm"><?php esc_html_e( 'Ver Módulo', 'alezux-members' ); ?></a>
	</div>
</div>
