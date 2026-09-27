<?php
/**
 * Thank-you screen (mockup screen 07).
 *
 * @var WC_Order|false $order
 */
defined( 'ABSPATH' ) || exit;

$failed = $order && $order->has_status( 'failed' );
?>
<section class="d-thanks">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img class="d-thanks__banner" src="<?php echo esc_url( danci_asset( 'images/banner.jpg' ) ); ?>" alt="Danči"></a>
	<div class="d-thanks__wrap">
		<div class="d-thanks__card">
			<?php if ( $order && ! $failed ) :
				$name = trim( $order->get_billing_first_name() );
				?>
				<div class="d-thanks__head"><span class="d-thanks__check"><?php echo danci_icon( 'check', 28 ); ?></span><h1 class="d-thanks__title">Hvala<?php echo $name ? ', ' . esc_html( $name ) : ''; ?>!</h1></div>
				<p class="d-thanks__text">Porudžbina <b>#<?php echo esc_html( $order->get_order_number() ); ?></b> je primljena.<span class="d-desktop-inline"> Potvrdu šaljemo na <?php echo esc_html( $order->get_billing_email() ); ?>.</span> Plaćaš kuriru prilikom dostave, Post Express 1–3 radna dana.</p>
				<div class="d-thanks__items">
					<?php foreach ( $order->get_items() as $item ) :
						$p     = $item->get_product();
						$thumb = $p ? ( wp_get_attachment_image_url( $p->get_image_id(), 'woocommerce_thumbnail' ) ?: wc_placeholder_img_src() ) : wc_placeholder_img_src();
						$parts = array();
						foreach ( $item->get_meta_data() as $meta ) {
							$key = $meta->key;
							if ( 0 !== strpos( $key, '_' ) ) {
								$label   = taxonomy_exists( $key ) ? ( get_term_by( 'slug', $meta->value, $key )->name ?? $meta->value ) : $meta->value;
								$parts[] = $label;
							}
						}
						$parts[] = $item->get_quantity() . ' kom';
						?>
						<div class="d-thanks__item">
							<img class="d-desktop-block" src="<?php echo esc_url( $thumb ); ?>" alt="">
							<div class="d-thanks__meta"><b><?php echo esc_html( $item->get_name() ? preg_replace( '/ - .*$/', '', $item->get_name() ) : '' ); ?></b><span><?php echo esc_html( implode( ' · ', $parts ) ); ?></span></div>
							<b><?php echo esc_html( danci_price( $item->get_subtotal() ) ); ?></b>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="d-thanks__total"><span><span class="d-desktop-inline">Ukupno za plaćanje</span><span class="d-mobile-inline">Ukupno</span></span><span class="d-thanks__total-value"><?php echo esc_html( danci_price( $order->get_total() ) ); ?></span></div>
			<?php elseif ( $failed ) : ?>
				<h1 class="d-thanks__title">Porudžbina nije uspela</h1>
				<p class="d-thanks__text">Nažalost, porudžbina nije mogla da bude obrađena. Pokušaj ponovo.</p>
			<?php else : ?>
				<h1 class="d-thanks__title">Hvala!</h1>
				<p class="d-thanks__text">Porudžbina je primljena.</p>
			<?php endif; ?>
			<a class="d-btn d-btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">NAZAD NA POČETNU</a>
		</div>
	</div>
</section>
<?php
if ( $order ) {
	// Lets tracking/analytics integrations run; the default order-details table is unhooked in functions.php.
	do_action( 'woocommerce_thankyou', $order->get_id() );
}
