<?php
/**
 * Fallback for anything without a specific template (posts, archives, 404).
 */
get_header( null, array( 'variant' => 'default' ) );
?>
<main class="d-page">
	<?php if ( is_404() || ! have_posts() ) : ?>
		<article class="d-page__card d-page__card--center">
			<h1 class="d-page__title">Stranica nije pronađena</h1>
			<p class="d-page__content">Ova stranica ne postoji ili je premeštena.</p>
			<a class="d-btn d-btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">NAZAD NA POČETNU</a>
		</article>
	<?php else : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article class="d-page__card">
				<h1 class="d-page__title"><?php the_title(); ?></h1>
				<div class="d-page__content"><?php the_content(); ?></div>
			</article>
		<?php endwhile; ?>
	<?php endif; ?>
</main>
<?php
get_footer( null, array( 'variant' => 'full' ) );
