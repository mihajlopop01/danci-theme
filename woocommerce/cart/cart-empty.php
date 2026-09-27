<?php
/**
 * Empty cart (mockup screen 05, empty state).
 */
defined( 'ABSPATH' ) || exit;

woocommerce_output_all_notices();
?>
<div class="d-cart d-cart--empty">
	<div class="d-cart__head"><h1 class="d-cart__title">Tvoja korpa</h1><span class="d-cart__count">0 kom</span></div>
	<div class="d-empty wc-empty-cart-message">
		<div class="d-empty__title">Korpa je prazna</div>
		<p class="d-empty__text">Izaberi majicu i vrati se ovde.</p>
		<a class="d-btn d-btn--primary" href="<?php echo esc_url( danci_products_url() ); ?>">POGLEDAJ MAJICE</a>
	</div>
</div>
