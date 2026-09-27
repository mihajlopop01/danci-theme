<?php
/**
 * Pages. Cart and checkout render WooCommerce's classic templates (overridden in
 * theme/woocommerce/) instead of the page's block content; "Vodič za veličine"
 * shows the size table; other pages (legal/info) get a white content card,
 * which the design leaves unspecified.
 */

if ( function_exists( 'is_cart' ) && is_cart() ) {
	get_header( null, array( 'variant' => 'default', 'back' => danci_products_url(), 'cart' => false ) );
	echo '<main class="d-cart-page">';
	echo do_shortcode( '[woocommerce_cart]' );
	echo '</main>';
	get_footer( null, array( 'variant' => 'bar' ) );
	return;
}

if ( function_exists( 'is_checkout' ) && is_checkout() ) {
	$thanks = is_wc_endpoint_url( 'order-received' );
	get_header( null, array( 'variant' => $thanks ? 'bare' : 'checkout' ) );
	echo '<main class="' . ( $thanks ? 'd-thanks-page' : 'd-checkout-page' ) . '">';
	echo do_shortcode( '[woocommerce_checkout]' );
	echo '</main>';
	get_footer( null, array( 'variant' => 'none' ) );
	return;
}

get_header( null, array( 'variant' => 'default' ) );
?>
<main class="d-page">
	<?php while ( have_posts() ) : the_post(); ?>
		<article class="d-page__card">
			<h1 class="d-page__title"><?php the_title(); ?></h1>
			<?php if ( is_page( 'vodic-za-velicine' ) ) : ?>
				<?php get_template_part( 'template-parts/size-table' ); ?>
			<?php endif; ?>
			<div class="d-page__content"><?php the_content(); ?></div>
		</article>
	<?php endwhile; ?>
</main>
<?php
get_footer( null, array( 'variant' => 'full' ) );
