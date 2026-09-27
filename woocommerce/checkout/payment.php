<?php
/**
 * "ZAVRŠI KUPOVINU" button (label only, no total). Returned as an AJAX
 * fragment, so the root keeps id="payment" / woocommerce-checkout-payment.
 *
 * The store only offers cash on delivery (shown in the details card, see
 * form-checkout.php); the gateway radio stays in the form (hidden) so
 * WooCommerce receives payment_method as usual.
 */
defined( 'ABSPATH' ) || exit;

$gateways = WC()->cart->needs_payment() ? WC()->payment_gateways()->get_available_payment_gateways() : array();
$first    = $gateways ? array_key_first( $gateways ) : '';
?>
<div id="payment" class="woocommerce-checkout-payment d-payment">
	<?php if ( $gateways ) : ?>
		<ul class="wc_payment_methods payment_methods methods d-payment__methods">
			<?php foreach ( $gateways as $id => $gateway ) : ?>
				<li class="wc_payment_method payment_method_<?php echo esc_attr( $id ); ?>">
					<input id="payment_method_<?php echo esc_attr( $id ); ?>" type="radio" class="input-radio" name="payment_method" value="<?php echo esc_attr( $id ); ?>" <?php checked( $id, $first ); ?>>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<div class="form-row place-order d-place-order">
		<noscript><button type="submit" class="button" name="woocommerce_checkout_update_totals" value="1">Ažuriraj</button></noscript>
		<?php if ( wc_terms_and_conditions_checkbox_enabled() ) : ?>
			<input type="hidden" name="terms" value="on"><input type="hidden" name="terms-field" value="1">
		<?php endif; ?>
		<?php do_action( 'woocommerce_review_order_before_submit' ); ?>
		<button type="submit" class="d-btn d-btn--primary d-place-order__btn" name="woocommerce_checkout_place_order" id="place_order" value="ZAVRŠI KUPOVINU" data-value="ZAVRŠI KUPOVINU">ZAVRŠI KUPOVINU</button>
		<?php do_action( 'woocommerce_review_order_after_submit' ); ?>
		<?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
		<p class="d-terms">Klikom na dugme potvrđujem saglasnost sa <a href="<?php echo esc_url( danci_page_url( 'uslovi-koriscenja' ) ); ?>">Uslovima korišćenja</a> i <a href="<?php echo esc_url( danci_page_url( 'politika-privatnosti' ) ); ?>">Politikom privatnosti</a>.</p>
	</div>
</div>
