<?php
/**
 * Customer processing order email (plain text) — Danci, Serbian copy.
 * Overrides woocommerce/templates/emails/plain/customer-processing-order.php.
 *
 * @package WooCommerce\Templates\Emails\Plain
 */

defined( 'ABSPATH' ) || exit;

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
echo esc_html( wp_strip_all_tags( $email_heading ) );
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

if ( $order->get_billing_first_name() ) {
	echo sprintf( 'Zdravo %s,', esc_html( $order->get_billing_first_name() ) ) . "\n\n";
} else {
	echo "Zdravo,\n\n";
}

/* translators: %s: Order number */
echo sprintf( 'Hvala na porudžbini u Danči Shop-u! Porudžbina #%s je primljena i uskoro kreće na pakovanje.', esc_html( $order->get_order_number() ) ) . "\n\n";
echo "Evo pregleda šta si poručio/la:\n\n";

do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n----------------------------------------\n\n";

do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n\n----------------------------------------\n\n";

if ( $additional_content ) {
	echo esc_html( wp_strip_all_tags( wptexturize( $additional_content ) ) );
	echo "\n\n----------------------------------------\n\n";
}

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
