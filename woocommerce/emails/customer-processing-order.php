<?php
/**
 * Customer processing order email — Danci, Serbian copy.
 * Overrides woocommerce/templates/emails/customer-processing-order.php.
 *
 * @package WooCommerce\Templates\Emails
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_email_header', $email_heading, $email );
?>

<p>
<?php
if ( $order->get_billing_first_name() ) {
	printf( 'Zdravo %s,', esc_html( $order->get_billing_first_name() ) );
} else {
	echo 'Zdravo,';
}
?>
</p>

<?php /* translators: %s: Order number */ ?>
<p><?php printf( 'Hvala na porudžbini u Danči Shop-u! Porudžbina #%s je primljena i uskoro kreće na pakovanje.', esc_html( $order->get_order_number() ) ); ?></p>
<p>Evo pregleda šta si poručio/la:</p>

<?php
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

do_action( 'woocommerce_email_footer', $email );
