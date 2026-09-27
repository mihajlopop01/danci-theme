<?php
/**
 * Cart (mockup screen 05): item cards + dark "Pregled" summary.
 *
 * Keeps WooCommerce's form contract (woocommerce-cart-form, cart[key][qty],
 * update_cart, nonce, .product-remove > a) so wc cart.js handles AJAX
 * quantity updates and removal.
 */
defined( 'ABSPATH' ) || exit;

$cart     = WC()->cart;
$count    = $cart->get_cart_contents_count();
$shipping = $cart->needs_shipping() && $cart->show_shipping() ? danci_price( $cart->get_shipping_total() ) : '—';

do_action( 'woocommerce_before_cart' );
?>
<form class="woocommerce-cart-form d-cart" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post" data-count="<?php echo (int) $count; ?>">
	<div class="d-cart__head"><h1 class="d-cart__title">Tvoja korpa</h1><span class="d-cart__count"><?php echo (int) $count; ?> kom</span></div>

	<div class="d-cart__grid">
		<div class="d-cart__items woocommerce-cart-form__contents">
			<?php
			foreach ( $cart->get_cart() as $key => $item ) :
				$_product = apply_filters( 'woocommerce_cart_item_product', $item['data'], $item, $key );
				if ( ! $_product || ! $_product->exists() || $item['quantity'] <= 0 || ! apply_filters( 'woocommerce_cart_item_visible', true, $item, $key ) ) {
					continue;
				}
				$link  = $_product->is_visible() ? $_product->get_permalink( $item ) : '';
				$name  = $_product->is_type( 'variation' ) ? wc_get_product( $item['product_id'] )->get_name() : $_product->get_name();
				$thumb = wp_get_attachment_image_url( $_product->get_image_id(), 'woocommerce_thumbnail' ) ?: wc_placeholder_img_src();
				?>
				<div class="d-cart-row woocommerce-cart-form__cart-item cart_item">
					<a class="d-cart-row__img" href="<?php echo esc_url( $link ?: '#' ); ?>"><img src="<?php echo esc_url( $thumb ); ?>" alt=""></a>
					<div class="d-cart-row__info">
						<div class="d-cart-row__name"><?php echo esc_html( $name ); ?></div>
						<div class="d-cart-row__variant"><?php echo esc_html( danci_cart_item_variant( $item ) ); ?></div>
						<div class="d-cart-row__unit"><?php echo esc_html( danci_price( $_product->get_price() ) ); ?> / kom</div>
					</div>
					<div class="d-qty d-qty--cart" data-d-cart-qty>
						<button type="button" aria-label="Manje" data-step="-1">−</button>
						<input type="number" class="qty" name="cart[<?php echo esc_attr( $key ); ?>][qty]" value="<?php echo esc_attr( $item['quantity'] ); ?>" min="0" step="1" inputmode="numeric" aria-label="Količina" <?php echo $_product->is_sold_individually() ? 'readonly' : ''; ?>>
						<button type="button" aria-label="Više" data-step="1">+</button>
					</div>
					<span class="d-cart-row__total"><?php echo esc_html( danci_price( $item['line_subtotal'] ) ); ?></span>
					<div class="d-cart-row__remove product-remove">
						<a class="d-remove" href="<?php echo esc_url( wc_get_cart_remove_url( $key ) ); ?>" aria-label="Ukloni" data-product_id="<?php echo esc_attr( $item['product_id'] ); ?>"><?php echo danci_icon( 'trash', 18 ); ?></a>
					</div>
				</div>
			<?php endforeach; ?>
			<a class="d-cart__more d-desktop-block" href="<?php echo esc_url( danci_products_url() ); ?>">← Dodaj još proizvoda</a>
		</div>

		<div class="d-cart__summary">
			<div class="d-cart__summary-title d-desktop-block">Pregled</div>
			<div class="d-cart__line"><span><span class="d-desktop-inline">Ukupan iznos korpe</span><span class="d-mobile-inline">Korpa</span></span><b><?php echo esc_html( danci_price( $cart->get_subtotal() ) ); ?></b></div>
			<div class="d-cart__line"><span>Dostava<span class="d-desktop-inline"> (Post Express)</span></span><b><?php echo esc_html( $shipping ); ?></b></div>
			<div class="d-cart__rule d-desktop-block"></div>
			<div class="d-cart__total"><span>Ukupno</span><span class="d-cart__total-value"><?php echo esc_html( danci_price( $cart->get_total( 'edit' ) ) ); ?></span></div>
			<a class="d-btn d-btn--cyan d-btn--onnavy" href="<?php echo esc_url( wc_get_checkout_url() ); ?>">NARUČI →</a>
			<p class="d-cart__terms d-desktop-block">Klikom na dugme <b>Naruči</b> potvrđujem saglasnost sa <a href="<?php echo esc_url( danci_page_url( 'uslovi-koriscenja' ) ); ?>">Uslovima korišćenja</a> i <a href="<?php echo esc_url( danci_page_url( 'politika-privatnosti' ) ); ?>">Politikom privatnosti</a>.</p>
		</div>
		<a class="d-cart__more d-mobile-block" href="<?php echo esc_url( danci_products_url() ); ?>">Dodaj još proizvoda</a>
	</div>

	<button type="submit" name="update_cart" value="1" class="d-hidden-submit" tabindex="-1" aria-hidden="true">Ažuriraj</button>
	<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
</form>
<?php
do_action( 'woocommerce_after_cart' );
