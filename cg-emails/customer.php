<?php
/**
 * Creator Goods — one Serbian body for WooCommerce customer emails (see cg-emails.php).
 * Variables come from the WooCommerce email that loads this template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cg_id     = $email ? $email->id : '';
$cg_order  = isset( $order ) && $order instanceof WC_Order ? $order : null;
$cg_number = $cg_order ? $cg_order->get_order_number() : '';
$cg_name   = $cg_order ? $cg_order->get_billing_first_name() : '';
$cg_link   = 'color:inherit;font-weight:bold;text-decoration:underline;';

do_action( 'woocommerce_email_header', $email_heading, $email );
?>

<p><?php echo $cg_name ? esc_html( sprintf( 'Zdravo %s,', $cg_name ) ) : 'Zdravo,'; ?></p>

<?php
switch ( $cg_id ) {
	case 'customer_completed_order':
		$cg_tracking = cg_order_tracking( $cg_order );
		echo '<p>' . esc_html( sprintf( 'Vaša porudžbina #%s je poslata Poštom Srbije.', $cg_number ) ) . '</p>';
		if ( $cg_tracking ) {
			echo '<p style="margin:18px 0;padding:14px 16px;border:1px solid #e5e5e5;border-radius:8px;">Broj za praćenje:<br />'
				. '<strong style="font-size:18px;letter-spacing:1px;">' . esc_html( $cg_tracking ) . '</strong><br />'
				. '<a href="' . esc_url( CG_POSTA_TRACKING_URL ) . '" style="' . $cg_link . '">Pratite pošiljku na sajtu Pošte</a></p>';
		}
		break;
	case 'customer_note':
		echo '<p>' . esc_html( sprintf( 'Dodali smo napomenu uz vašu porudžbinu #%s:', $cg_number ) ) . '</p>';
		echo '<blockquote style="margin:0 0 16px;padding-left:12px;border-left:3px solid #ccc;">' . wp_kses_post( wpautop( wptexturize( make_clickable( $customer_note ) ) ) ) . '</blockquote>';
		break;
	case 'customer_on_hold_order':
		echo '<p>' . esc_html( sprintf( 'Primili smo vašu porudžbinu #%s. Čeka potvrdu plaćanja, a čim stigne krećemo sa pakovanjem.', $cg_number ) ) . '</p>';
		break;
	case 'customer_cancelled_order':
		echo '<p>' . esc_html( sprintf( 'Vaša porudžbina #%s je otkazana.', $cg_number ) ) . '</p>';
		break;
	case 'customer_failed_order':
		echo '<p>' . esc_html( sprintf( 'Plaćanje za porudžbinu #%s nije uspelo. Pokušajte ponovo ili nam pišite.', $cg_number ) ) . '</p>';
		break;
	case 'customer_refunded_order':
		echo '<p>' . esc_html( sprintf( ! empty( $partial_refund ) ? 'Deo iznosa za porudžbinu #%s je vraćen.' : 'Iznos za porudžbinu #%s je vraćen.', $cg_number ) ) . '</p>';
		break;
	case 'customer_invoice':
		echo '<p>' . esc_html( sprintf( 'Detalji vaše porudžbine #%s:', $cg_number ) ) . '</p>';
		if ( $cg_order && $cg_order->needs_payment() ) {
			echo '<p><a href="' . esc_url( $cg_order->get_checkout_payment_url() ) . '" style="' . $cg_link . '">Platite porudžbinu</a></p>';
		}
		break;
	case 'customer_reset_password':
		$cg_url = add_query_arg(
			array( 'key' => $reset_key, 'id' => $user_id, 'login' => rawurlencode( $user_login ) ),
			wc_get_endpoint_url( 'lost-password', '', wc_get_page_permalink( 'myaccount' ) )
		);
		echo '<p>' . esc_html( sprintf( 'Zatražena je nova lozinka za nalog %s. Ako to niste bili vi, samo zanemarite ovaj mejl.', $user_login ) ) . '</p>';
		echo '<p><a href="' . esc_url( $cg_url ) . '" style="' . $cg_link . '">Postavite novu lozinku</a></p>';
		break;
	case 'customer_new_account':
		echo '<p>' . esc_html( sprintf( 'Hvala što ste napravili nalog. Korisničko ime: %s.', $user_login ) ) . '</p>';
		if ( ! empty( $password_generated ) && ! empty( $set_password_url ) ) {
			echo '<p><a href="' . esc_url( $set_password_url ) . '" style="' . $cg_link . '">Postavite lozinku</a></p>';
		} else {
			echo '<p><a href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '" style="' . $cg_link . '">Moj nalog</a></p>';
		}
		break;
}

if ( $cg_order ) {
	do_action( 'woocommerce_email_order_details', $cg_order, $sent_to_admin, $plain_text, $email );
	do_action( 'woocommerce_email_order_meta', $cg_order, $sent_to_admin, $plain_text, $email );
	do_action( 'woocommerce_email_customer_details', $cg_order, $sent_to_admin, $plain_text, $email );
}

if ( ! empty( $additional_content ) ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

do_action( 'woocommerce_email_footer', $email );
