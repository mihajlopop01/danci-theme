<?php
/**
 * Creator Goods — customer emails in Serbian, sharp product images, tracking in the "completed" mail.
 * The same file (and cg-emails/) is in every Creator Goods Woo theme; store copy (processing mail, colours,
 * footer text) stays in the theme's functions.php and woocommerce/emails/.
 *
 * Shipping: the order app (Angora) writes order meta cg_tracking and sets the order to completed in one save.
 * The completed mail below shows the tracking code, so the customer gets ONE shipping mail (no customer note).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const CG_POSTA_TRACKING_URL = 'https://www.posta.rs/cir/alati/pracenje-posiljke.aspx';

/** Customer emails rendered by cg-emails/customer.php (the theme's own processing mail is left alone). */
function cg_email_templates() {
	return array(
		'customer-completed-order'  => 'customer_completed_order',
		'customer-note'             => 'customer_note',
		'customer-on-hold-order'    => 'customer_on_hold_order',
		'customer-cancelled-order'  => 'customer_cancelled_order',
		'customer-failed-order'     => 'customer_failed_order',
		'customer-refunded-order'   => 'customer_refunded_order',
		'customer-invoice'          => 'customer_invoice',
		'customer-reset-password'   => 'customer_reset_password',
		'customer-new-account'      => 'customer_new_account',
	);
}

/** Tracking code the order app stored on the order (empty when the order was completed by hand). */
function cg_order_tracking( $order ) {
	return $order instanceof WC_Order ? trim( (string) $order->get_meta( 'cg_tracking' ) ) : '';
}

add_filter( 'wc_get_template', function ( $template, $template_name ) {
	if ( ! preg_match( '#^emails/(plain/)?([a-z-]+)\.php$#', $template_name, $m ) ) {
		return $template;
	}
	if ( ! isset( cg_email_templates()[ $m[2] ] ) ) {
		return $template;
	}
	return __DIR__ . '/cg-emails/' . ( $m[1] ? 'customer-plain.php' : 'customer.php' );
}, 20, 2 );

// Subjects and headings. Subject filters run after placeholder replacement, so the number is built here.
function cg_email_copy() {
	return array(
		'customer_completed_order' => array( 'Vaša pošiljka je na putu (#%s)', 'Vaša pošiljka je na putu' ),
		'customer_note'            => array( 'Napomena uz porudžbinu #%s', 'Napomena uz porudžbinu' ),
		'customer_on_hold_order'   => array( 'Porudžbina #%s je primljena', 'Hvala na porudžbini!' ),
		'customer_cancelled_order' => array( 'Porudžbina #%s je otkazana', 'Porudžbina je otkazana' ),
		'customer_failed_order'    => array( 'Plaćanje za porudžbinu #%s nije uspelo', 'Plaćanje nije uspelo' ),
		'customer_refunded_order'  => array( 'Povraćaj novca za porudžbinu #%s', 'Povraćaj novca' ),
		'customer_invoice'         => array( 'Porudžbina #%s', 'Detalji porudžbine' ),
		'customer_reset_password'  => array( 'Nova lozinka', 'Nova lozinka' ),
		'customer_new_account'     => array( 'Dobrodošli', 'Dobrodošli' ),
	);
}

foreach ( cg_email_copy() as $cg_id => $cg_copy ) {
	add_filter( "woocommerce_email_subject_{$cg_id}", function ( $subject, $object ) use ( $cg_copy ) {
		$number = $object instanceof WC_Order ? $object->get_order_number() : '';
		return false !== strpos( $cg_copy[0], '%s' ) ? sprintf( $cg_copy[0], $number ) : $cg_copy[0];
	}, 20, 2 );
	add_filter( "woocommerce_email_heading_{$cg_id}", fn() => $cg_copy[1], 20 );
	add_filter( "woocommerce_email_additional_content_{$cg_id}", fn() => 'Hvala što kupujete kod nas!', 20 );
}

// Product image in order emails: WooCommerce asks for a 32–48 px thumbnail and the mail scales it → blurry.
// Use the shop-size image and show it at 64 px, so it stays sharp (also on phones with 2–3× screens).
add_filter( 'woocommerce_email_order_items_args', function ( $args ) {
	$args['show_image'] = true;
	$args['image_size'] = array( 64, 64 );
	return $args;
} );

add_filter( 'woocommerce_order_item_thumbnail', function ( $image, $item ) {
	if ( ! $item instanceof WC_Order_Item_Product ) {
		return $image;
	}
	$product = $item->get_product();
	$id      = $product ? $product->get_image_id() : 0;
	if ( ! $id && $product && $product->get_parent_id() ) {
		$id = (int) get_post_thumbnail_id( $product->get_parent_id() );
	}
	$src = $id ? wp_get_attachment_image_url( $id, 'woocommerce_single' ) : '';
	if ( ! $src ) {
		return $image;
	}
	return '<img src="' . esc_url( $src ) . '" alt="" width="64" style="width:64px;height:auto;max-width:64px;border:0;border-radius:6px;vertical-align:middle;margin-right:10px;" />';
}, 20, 2 );

// Strings in WooCommerce's shared email parts (order table, addresses). Only these; everything else passes through.
add_filter( 'gettext_woocommerce', function ( $translation, $text ) {
	static $map = array(
		'Order summary'                => 'Pregled porudžbine',
		'Order #%s'                    => 'Porudžbina #%s',
		'Order #%1$s (%2$s)'           => 'Porudžbina #%1$s (%2$s)',
		'[Order #%s]'                  => '[Porudžbina #%s]',
		'[Order #%1$s] (%2$s)'         => '[Porudžbina #%1$s] (%2$s)',
		'Product'                      => 'Proizvod',
		'Quantity'                     => 'Količina',
		'Price'                        => 'Cena',
		'Subtotal:'                    => 'Međuzbir:',
		'Discount:'                    => 'Popust:',
		'Shipping:'                    => 'Dostava:',
		'Total:'                       => 'Ukupno:',
		'Refund:'                      => 'Povraćaj:',
		'Payment method:'              => 'Način plaćanja:',
		'via %s'                       => '%s',
		'Billing address'              => 'Adresa za naplatu',
		'Shipping address'             => 'Adresa za dostavu',
		'Customer details'             => 'Podaci o kupcu',
		'Note:'                        => 'Napomena:',
		'Free!'                        => 'Besplatno!',
		'Order fully refunded.'        => 'Ceo iznos je vraćen.',
		'Order details'                => 'Detalji porudžbine',
		'Cash on delivery'             => 'Plaćanje pouzećem',
		'Pay with cash upon delivery.' => 'Plaćanje pouzećem.',
	);
	return $map[ $text ] ?? $translation;
}, 20, 2 );
