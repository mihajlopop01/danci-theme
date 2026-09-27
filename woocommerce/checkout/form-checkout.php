<?php
/**
 * Checkout (mockup screen 06).
 *
 * Left: "Podaci za dostavu" card with the billing fields (see
 * danci_checkout_field_spec()) and the payment method. Right: #order_review =
 * summary card (review-order.php) + "ZAVRŠI KUPOVINU" (payment.php). On mobile
 * the summary moves to the top via CSS order.
 *
 * @var WC_Checkout $checkout
 */
defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_checkout_form', $checkout );

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}
?>
<form name="checkout" method="post" class="checkout woocommerce-checkout d-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="Plaćanje">

	<div class="d-checkout__details" id="customer_details">
		<h1 class="d-checkout__title">Podaci za dostavu</h1>
		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
		<div class="woocommerce-billing-fields">
			<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>
			<div class="woocommerce-billing-fields__field-wrapper d-fields">
				<?php
				foreach ( $checkout->get_checkout_fields( 'billing' ) as $key => $field ) {
					woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
				}
				?>
			</div>
			<?php do_action( 'woocommerce_after_checkout_billing_form', $checkout ); ?>
		</div>
		<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

		<div class="d-paymethod">
			<div class="d-label">NAČIN PLAĆANJA</div>
			<div class="d-cod">
				<span class="d-cod__radio"><span></span></span>
				<div class="d-cod__text"><b>Plaćanje pouzećem</b><span><span class="d-desktop-inline">Plaćaš kuriru prilikom dostave · </span>Post Express 1–3 radna dana</span></div>
			</div>
		</div>
	</div>

	<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
	<div id="order_review" class="woocommerce-checkout-review-order d-checkout__side">
		<?php do_action( 'woocommerce_checkout_order_review' ); ?>
	</div>
	<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

</form>
<?php
do_action( 'woocommerce_after_checkout_form', $checkout );
