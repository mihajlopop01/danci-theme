<?php
/**
 * "Dodato u korpu" (mockup screen 04): right drawer on desktop, bottom sheet
 * on mobile. danci.js fills in the item that was just added.
 */
if ( ! function_exists( 'WC' ) ) {
	return;
}
?>
<div class="d-overlay d-drawer" hidden data-d-drawer>
	<div class="d-drawer__panel" role="dialog" aria-modal="true" aria-labelledby="d-drawer-title">
		<div class="d-drawer__head">
			<div class="d-drawer__title" id="d-drawer-title"><span class="d-drawer__check"><?php echo danci_icon( 'check', 18 ); ?></span>Dodato u korpu</div>
			<button type="button" class="d-close d-close--dark" aria-label="Zatvori" data-d-close>✕</button>
		</div>
		<div class="d-drawer__item">
			<img src="" alt="" data-d-drawer-img>
			<div class="d-drawer__meta">
				<div class="d-drawer__name" data-d-drawer-name></div>
				<div class="d-drawer__variant" data-d-drawer-variant></div>
				<div class="d-drawer__price" data-d-drawer-price></div>
			</div>
		</div>
		<div class="d-drawer__spacer"></div>
		<div class="d-drawer__foot">
			<div class="d-drawer__total"><span class="d-drawer__total-label">UKUPNO<span class="d-desktop-inline"> U KORPI</span> (<span class="d-drawer-count"><?php echo (int) danci_cart_count(); ?></span>)</span><span class="d-drawer-total"><?php echo esc_html( danci_price( WC()->cart ? WC()->cart->get_subtotal() : 0 ) ); ?></span></div>
			<a class="d-btn d-btn--primary" href="<?php echo esc_url( wc_get_checkout_url() ); ?>">NARUČI →</a>
			<a class="d-btn d-btn--light" href="<?php echo esc_url( wc_get_cart_url() ); ?>">POGLEDAJ KORPU</a>
			<a class="d-drawer__continue d-desktop-block" href="<?php echo esc_url( danci_products_url() ); ?>" data-d-close-link>Nastavi kupovinu</a>
		</div>
	</div>
</div>
