<?php
/**
 * Creator Goods — plain-text version of cg-emails/customer.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cg_id     = $email ? $email->id : '';
$cg_order  = isset( $order ) && $order instanceof WC_Order ? $order : null;
$cg_number = $cg_order ? $cg_order->get_order_number() : '';
$cg_name   = $cg_order ? $cg_order->get_billing_first_name() : '';

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
echo esc_html( wp_strip_all_tags( $email_heading ) ) . "\n";
echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo ( $cg_name ? sprintf( 'Zdravo %s,', esc_html( $cg_name ) ) : 'Zdravo,' ) . "\n\n";

switch ( $cg_id ) {
	case 'customer_completed_order':
		echo sprintf( 'Vaša porudžbina #%s je poslata Poštom Srbije.', esc_html( $cg_number ) ) . "\n\n";
		$cg_tracking = cg_order_tracking( $cg_order );
		if ( $cg_tracking ) {
			echo 'Broj za praćenje: ' . esc_html( $cg_tracking ) . "\n";
			echo 'Pratite pošiljku: ' . esc_url( CG_POSTA_TRACKING_URL ) . "\n\n";
		}
		break;
	case 'customer_note':
		echo sprintf( 'Dodali smo napomenu uz vašu porudžbinu #%s:', esc_html( $cg_number ) ) . "\n\n";
		echo "----------\n\n" . wptexturize( $customer_note ) . "\n\n----------\n\n";
		break;
	case 'customer_on_hold_order':
		echo sprintf( 'Primili smo vašu porudžbinu #%s. Čeka potvrdu plaćanja, a čim stigne krećemo sa pakovanjem.', esc_html( $cg_number ) ) . "\n\n";
		break;
	case 'customer_cancelled_order':
		echo sprintf( 'Vaša porudžbina #%s je otkazana.', esc_html( $cg_number ) ) . "\n\n";
		break;
	case 'customer_failed_order':
		echo sprintf( 'Plaćanje za porudžbinu #%s nije uspelo. Pokušajte ponovo ili nam pišite.', esc_html( $cg_number ) ) . "\n\n";
		break;
	case 'customer_refunded_order':
		echo sprintf( ! empty( $partial_refund ) ? 'Deo iznosa za porudžbinu #%s je vraćen.' : 'Iznos za porudžbinu #%s je vraćen.', esc_html( $cg_number ) ) . "\n\n";
		break;
	case 'customer_invoice':
		echo sprintf( 'Detalji vaše porudžbine #%s:', esc_html( $cg_number ) ) . "\n\n";
		if ( $cg_order && $cg_order->needs_payment() ) {
			echo 'Platite porudžbinu: ' . esc_url( $cg_order->get_checkout_payment_url() ) . "\n\n";
		}
		break;
	case 'customer_reset_password':
		echo sprintf( 'Zatražena je nova lozinka za nalog %s. Ako to niste bili vi, samo zanemarite ovaj mejl.', esc_html( $user_login ) ) . "\n\n";
		echo 'Postavite novu lozinku: ' . esc_url( add_query_arg( array( 'key' => $reset_key, 'id' => $user_id, 'login' => rawurlencode( $user_login ) ), wc_get_endpoint_url( 'lost-password', '', wc_get_page_permalink( 'myaccount' ) ) ) ) . "\n\n";
		break;
	case 'customer_new_account':
		echo sprintf( 'Hvala što ste napravili nalog. Korisničko ime: %s.', esc_html( $user_login ) ) . "\n\n";
		if ( ! empty( $password_generated ) && ! empty( $set_password_url ) ) {
			echo 'Postavite lozinku: ' . esc_url( $set_password_url ) . "\n\n";
		}
		break;
}

if ( $cg_order ) {
	do_action( 'woocommerce_email_order_details', $cg_order, $sent_to_admin, $plain_text, $email );
	echo "\n----------------------------------------\n\n";
	do_action( 'woocommerce_email_order_meta', $cg_order, $sent_to_admin, $plain_text, $email );
	do_action( 'woocommerce_email_customer_details', $cg_order, $sent_to_admin, $plain_text, $email );
	echo "\n\n----------------------------------------\n\n";
}

if ( ! empty( $additional_content ) ) {
	echo esc_html( wp_strip_all_tags( wptexturize( $additional_content ) ) ) . "\n\n";
}

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
