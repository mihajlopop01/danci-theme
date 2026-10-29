<?php
/**
 * Creator Goods site tracker: anonymous page views + funnel events → Angora app (/api/track) for Analitika.
 * No personal data: random first-party session id (localStorage, 30 min idle), page type, product name, order total.
 * Loaded from functions.php. Store id: danci.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_footer', function () {
	if ( is_admin() || ! function_exists( 'is_woocommerce' ) ) {
		return;
	}
	$pt = 'other';
	$product = '';
	$value = null;
	$order_key = '';
	if ( is_front_page() ) {
		$pt = 'home';
	} elseif ( is_product() ) {
		$pt = 'product';
		$product = get_the_title();
	} elseif ( is_cart() ) {
		$pt = 'cart';
	} elseif ( is_checkout() && is_wc_endpoint_url( 'order-received' ) ) {
		$pt = 'thankyou';
		$order = wc_get_order( absint( get_query_var( 'order-received' ) ) );
		if ( $order ) {
			$value = (float) $order->get_total();
			$order_key = md5( $order->get_order_key() );
		}
	} elseif ( is_checkout() ) {
		$pt = 'checkout';
	}
	$cfg = array(
		'url'   => 'https://darkgrey-hare-758944.hostingersite.com/api/track',
		'store' => 'danci',
		'pt'    => $pt,
		'p'     => $product,
		'v'     => $value,
		'ok'    => $order_key,
	);
	?>
<script>
(function (c) {
	try {
		var now = Date.now(), ls = window.localStorage, sid = ls.getItem('cg_sid'), last = +ls.getItem('cg_sid_t') || 0;
		if (!sid || now - last > 30 * 60 * 1000) { sid = (Math.random().toString(36).slice(2) + now.toString(36)).slice(0, 24); ls.setItem('cg_sid', sid); }
		ls.setItem('cg_sid_t', String(now));
		var send = function (e, extra) {
			var b = { s: c.store, sid: sid, e: e, pt: c.pt };
			for (var k in extra || {}) b[k] = extra[k];
			var body = JSON.stringify(b);
			if (navigator.sendBeacon) navigator.sendBeacon(c.url, new Blob([body], { type: 'text/plain' }));
			else fetch(c.url, { method: 'POST', body: body, keepalive: true, mode: 'cors' });
		};
		send('page_view', c.p ? { p: c.p } : {});
		if (c.pt === 'checkout') send('checkout_start');
		if (c.pt === 'thankyou' && c.ok && !ls.getItem('cg_ord_' + c.ok)) { ls.setItem('cg_ord_' + c.ok, '1'); send('purchase', c.v !== null ? { v: c.v } : {}); }
		if (window.jQuery) jQuery(document.body).on('added_to_cart', function () { send('add_to_cart', c.p ? { p: c.p } : {}); });
		document.addEventListener('submit', function (ev) { if (ev.target && ev.target.matches && ev.target.matches('form.cart')) send('add_to_cart', c.p ? { p: c.p } : {}); }, true);
	} catch (e) {}
})(<?php echo wp_json_encode( $cfg ); ?>);
</script>
	<?php
}, 99 );
