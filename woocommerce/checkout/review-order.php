<?php
/**
 * "Tvoja porudžbina" summary card. Returned as an AJAX fragment by
 * update_order_review, so the root keeps the woocommerce-checkout-review-order-table class.
 */
defined( 'ABSPATH' ) || exit;

$cart     = WC()->cart;
$shipping = $cart->needs_shipping() ? danci_price( $cart->get_shipping_total() ) : '—';
$total    = danci_price( $cart->get_total( 'edit' ) );
?>
<div class="woocommerce-checkout-review-order-table d-summary">
	<div class="d-summary__head">
		<span class="d-summary__title">Tvoja porudžbina</span>
		<span class="d-summary__head-total d-mobile-inline"><?php echo esc_html( $total ); ?></span>
	</div>

	<?php
	foreach ( $cart->get_cart() as $key => $item ) :
		$_product = apply_filters( 'woocommerce_cart_item_product', $item['data'], $item, $key );
		if ( ! $_product || ! $_product->exists() || $item['quantity'] <= 0 ) {
			continue;
		}
		$name  = $_product->is_type( 'variation' ) ? wc_get_product( $item['product_id'] )->get_name() : $_product->get_name();
		$thumb = wp_get_attachment_image_url( $_product->get_image_id(), 'woocommerce_thumbnail' ) ?: wc_placeholder_img_src();
		?>
		<div class="d-summary__item">
			<span class="d-summary__img"><img src="<?php echo esc_url( $thumb ); ?>" alt=""><span class="d-badge d-desktop-flex"><?php echo (int) $item['quantity']; ?></span></span>
			<div class="d-summary__meta">
				<div class="d-summary__name"><?php echo esc_html( $name ); ?></div>
				<div class="d-summary__variant"><?php echo esc_html( danci_cart_item_variant( $item ) ); ?><span class="d-mobile-inline"> · <?php echo (int) $item['quantity']; ?> kom</span></div>
			</div>
			<span class="d-summary__price"><?php echo esc_html( danci_price( $item['line_subtotal'] ) ); ?></span>
		</div>
	<?php endforeach; ?>

	<div class="d-summary__rule"></div>
	<div class="d-summary__row"><span><span class="d-desktop-inline">Ukupan iznos korpe</span><span class="d-mobile-inline">Korpa</span></span><b><?php echo esc_html( danci_price( $cart->get_subtotal() ) ); ?></b></div>
	<div class="d-summary__row"><span>Dostava</span><b><?php echo esc_html( $shipping ); ?></b></div>
	<div class="d-summary__rule d-summary__rule--thick d-desktop-block"></div>
	<div class="d-summary__total d-desktop-flex"><span>Ukupno</span><span class="d-summary__total-value"><?php echo esc_html( $total ); ?></span></div>
	<div class="d-summary__vat d-desktop-block">U cenu je uračunat PDV.</div>

	<?php
	// Keep the chosen shipping method in the form (checkout.js and validation read it).
	if ( $cart->needs_shipping() ) {
		$chosen = WC()->session->get( 'chosen_shipping_methods', array() );
		foreach ( WC()->shipping()->get_packages() as $i => $package ) {
			$method = $chosen[ $i ] ?? ( $package['rates'] ? array_key_first( $package['rates'] ) : '' );
			if ( $method ) {
				printf( '<input type="hidden" name="shipping_method[%1$d]" data-index="%1$d" value="%2$s" class="shipping_method">', (int) $i, esc_attr( $method ) );
			}
		}
	}
	?>
</div>
