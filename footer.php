<?php
/**
 * Site footer.
 *
 * $args['variant']:
 *   full – dark footer with logo and link columns (home, product, pages)
 *   bar  – single copyright/links bar (cart)
 *   none – no footer (checkout, thank-you)
 * The "Dodato u korpu" drawer is printed with the full footer, since home
 * cards and the product page both add to the cart.
 */
$variant = $args['variant'] ?? 'full';

$shop_links = array(
	array( 'Sve majice', danci_products_url() ),
	array( 'Vodič za veličine', danci_page_url( 'vodic-za-velicine' ) ),
	array( 'Zamena veličina', danci_page_url( 'zamena-velicina' ) ),
);
$help_links = array(
	array( 'Politika privatnosti', danci_page_url( 'politika-privatnosti' ) ),
	array( 'Uslovi korišćenja', danci_page_url( 'uslovi-koriscenja' ) ),
	array( 'Odustanak od kupovine', danci_page_url( 'odustanak-od-kupovine' ) ),
);
$contact   = array( 'Kontakt', danci_page_url( 'kontakt' ) );
$instagram = 'https://instagram.com/danchy_official';

$links = function ( $items ) {
	foreach ( $items as [ $label, $url ] ) {
		echo '<a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
	}
};
?>

<?php if ( 'full' === $variant ) : ?>
	<footer class="d-footer" id="kontakt">
		<div class="d-footer__grid">
			<div class="d-footer__brand">
				<?php echo danci_logo(); ?>
				<p>Zvanična prodavnica Danči majica.</p>
			</div>
			<div class="d-footer__col">
				<div class="d-footer__head">PRODAVNICA</div>
				<?php $links( $shop_links ); ?>
			</div>
			<div class="d-footer__col">
				<div class="d-footer__head">POMOĆ</div>
				<?php $links( $help_links ); ?>
				<a class="d-mobile-only" href="<?php echo esc_url( $instagram ); ?>">Instagram</a>
			</div>
			<div class="d-footer__col d-footer__col--contact">
				<div class="d-footer__head">KONTAKT</div>
				<?php $links( array( $contact ) ); ?>
				<a href="<?php echo esc_url( $instagram ); ?>">Instagram @danchy_official</a>
			</div>
		</div>
		<div class="d-footer__bottom">© <?php echo esc_html( gmdate( 'Y' ) ); ?> Danči Shop</div>
	</footer>
	<?php get_template_part( 'template-parts/drawer' ); ?>
<?php elseif ( 'bar' === $variant ) : ?>
	<footer class="d-footbar">
		<span>© <?php echo esc_html( gmdate( 'Y' ) ); ?> Danči Shop</span>
		<div class="d-footbar__links"><?php $links( array( $help_links[0], $help_links[1], $contact ) ); ?></div>
	</footer>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
