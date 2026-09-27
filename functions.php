<?php
/**
 * Danci theme functions.
 *
 * Classic PHP templates render markup that mirrors the Claude Design mockup
 * (design/Danci Shop.dc.html); WooCommerce supplies all product, cart and
 * order data.
 */

defined( 'ABSPATH' ) || exit;

define( 'DANCI_VERSION', '1.0.0' );

/* ---------------------------------------------------------------------------
 * Setup & assets
 * ------------------------------------------------------------------------- */

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'woocommerce' );
} );

add_action( 'wp_enqueue_scripts', function () {
	// Rubik (body) from Google Fonts. The display font is self-hosted (see danci.css):
	// Lilita One has no Č/Ć/Đ, so scripts/build-font.py adds them as "Danci Display".
	wp_enqueue_style( 'danci-fonts', 'https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;600;700&display=swap', array(), null );
	wp_enqueue_style( 'danci', danci_asset( 'css/danci.css' ), array(), danci_asset_ver( 'css/danci.css' ) );
	wp_enqueue_script( 'danci', danci_asset( 'js/danci.js' ), array(), danci_asset_ver( 'js/danci.js' ), array( 'in_footer' => true ) );
	wp_localize_script( 'danci', 'DANCI', array(
		'addToCartUrl' => class_exists( 'WC_AJAX' ) ? WC_AJAX::get_endpoint( 'add_to_cart' ) : '',
	) );

	// Block styles are not used by these templates.
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'classic-theme-styles' );
	wp_dequeue_style( 'wc-blocks-style' );
}, 20 );

// WooCommerce's own stylesheets fight the design; the theme styles everything.
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

add_action( 'wp_head', function () {
	echo '<link rel="preload" href="' . esc_url( danci_asset( 'fonts/danci-display.woff' ) ) . '" as="font" type="font/woff" crossorigin>' . "\n";
	echo '<link rel="icon" href="' . esc_url( danci_asset( 'images/favicon.png' ) ) . '">' . "\n";
}, 2 );

function danci_asset( $path ) {
	return get_theme_file_uri( 'assets/' . $path );
}

function danci_asset_ver( $path ) {
	$file = get_theme_file_path( 'assets/' . $path );
	return file_exists( $file ) ? (string) filemtime( $file ) : DANCI_VERSION;
}

/* ---------------------------------------------------------------------------
 * Prices: the design shows "1.699 RSD" (dot thousands, no decimals, RSD after).
 * ------------------------------------------------------------------------- */

add_filter( 'wc_get_price_thousand_separator', fn() => '.' );
add_filter( 'wc_get_price_decimal_separator', fn() => ',' );
add_filter( 'wc_get_price_decimals', fn() => 0 );
add_filter( 'woocommerce_price_format', fn() => '%2$s&nbsp;%1$s' );
add_filter( 'woocommerce_currency_symbol', fn( $symbol, $currency ) => 'RSD' === $currency ? 'RSD' : $symbol, 10, 2 );

/** Plain-text price ("1.699 RSD") for places where wc_price() markup is unwanted. */
function danci_price( $amount ) {
	return html_entity_decode( wp_strip_all_tags( wc_price( $amount ) ), ENT_QUOTES, 'UTF-8' );
}

/** "1.699 RSD", or "od 1.699 RSD" when sizes cost different amounts. */
function danci_price_label( $product ) {
	if ( ! $product->is_type( 'variable' ) ) {
		return danci_price( $product->get_price() );
	}
	$min = $product->get_variation_price( 'min', true );
	$max = $product->get_variation_price( 'max', true );
	return ( $min < $max ? 'od ' : '' ) . danci_price( $min );
}

/* ---------------------------------------------------------------------------
 * Small markup helpers
 * ------------------------------------------------------------------------- */

/** Inline SVG icons copied from the mockup. */
function danci_icon( $name, $size = 20, $attrs = '' ) {
	$paths = array(
		'cart'  => '<circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>',
		'down'  => '<path d="M12 5v14"/><path d="m19 12-7 7-7-7"/>',
		'back'  => '<path d="m12 19-7-7 7-7"/><path d="M19 12H5"/>',
		'menu'  => '<path d="M4 6h16M4 12h16M4 18h16"/>',
		'ruler' => '<path d="M21.3 15.3a2.4 2.4 0 0 1 0 3.4l-2.6 2.6a2.4 2.4 0 0 1-3.4 0L2.7 8.7a2.41 2.41 0 0 1 0-3.4l2.6-2.6a2.41 2.41 0 0 1 3.4 0Z"/><path d="m14.5 12.5 2-2"/><path d="m11.5 9.5 2-2"/><path d="m8.5 6.5 2-2"/><path d="m17.5 15.5 2-2"/>',
		'truck' => '<path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/>',
		'cash'  => '<rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/>',
		'check' => '<path d="M20 6 9 17l-5-5"/>',
		'trash' => '<path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>',
	);
	$stroke = array( 'check' => 3.4, 'down' => 3, 'truck' => 2, 'cash' => 2, 'ruler' => 2, 'trash' => 2 );
	return sprintf(
		'<svg width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="%2$s" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" %3$s>%4$s</svg>',
		$size,
		$stroke[ $name ] ?? 2.4,
		$attrs,
		$paths[ $name ]
	);
}

function danci_logo() {
	return '<img class="d-logo" src="' . esc_url( danci_asset( 'images/logo.png' ) ) . '" alt="Danči">';
}

function danci_page_url( $slug ) {
	$page = get_page_by_path( $slug );
	return $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );
}

function danci_products_url() {
	return home_url( '/#proizvodi' );
}

/** Main navigation, as in the mockup. */
function danci_nav_items() {
	return array(
		'home'    => array( 'Početna', home_url( '/' ) ),
		'shop'    => array( 'Majice', danci_products_url() ),
		'guide'   => array( 'Vodič za veličine', danci_page_url( 'vodic-za-velicine' ) ),
		'contact' => array( 'Kontakt', danci_page_url( 'kontakt' ) ),
	);
}

function danci_current_nav() {
	if ( is_front_page() ) {
		return 'home';
	}
	if ( function_exists( 'is_woocommerce' ) && ( is_product() || is_shop() || is_product_taxonomy() ) ) {
		return 'shop';
	}
	if ( is_page( 'vodic-za-velicine' ) ) {
		return 'guide';
	}
	if ( is_page( 'kontakt' ) ) {
		return 'contact';
	}
	return '';
}

function danci_cart_count() {
	return function_exists( 'WC' ) && WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;
}

function danci_products() {
	return wc_get_products( array(
		'status'  => 'publish',
		'limit'   => -1,
		'orderby' => 'menu_order',
		'order'   => 'ASC',
	) );
}

/**
 * Home-page lifestyle tiles (design screen 01): banner image + slogan, linking
 * to the product with that slug. A tile whose product isn't published is skipped.
 */
function danci_features() {
	$tiles = array(
		array( 'koprive-danci-majica', 'Ode sve u koprive', 'images/koprive-banner.jpg' ),
		array( 'tesla-danci-majica', 'To samo malo pecne', 'images/tesla-banner.jpg' ),
	);
	$out = array();
	foreach ( $tiles as [ $slug, $slogan, $image ] ) {
		$product = get_page_by_path( $slug, OBJECT, 'product' );
		if ( $product && 'publish' === $product->post_status ) {
			$out[] = array(
				'name'   => get_the_title( $product ),
				'url'    => get_permalink( $product ),
				'slogan' => $slogan,
				'image'  => danci_asset( $image ),
			);
		}
	}
	return $out;
}

/* ---------------------------------------------------------------------------
 * Product data for the custom colour / size pickers
 * ------------------------------------------------------------------------- */

/** Swatch colours for the "Boja" attribute values; order = display order. */
function danci_color_hex() {
	return array(
		'crna' => '#111111',
		'bela' => '#FFFFFF',
	);
}

/** Kids' sizes are numbers (6–14), adults' are letters (S–L). */
function danci_is_kid_size( $label ) {
	return is_numeric( $label );
}

/**
 * Normalised data for a variable product:
 * colors (label, slug, hex, image), sizes, variations (with price), gallery.
 */
function danci_product_data( $product ) {
	$data = array(
		'id'         => $product->get_id(),
		'colorKey'   => '',
		'sizeKey'    => '',
		'colors'     => array(),
		'sizes'      => array(),
		'variations' => array(),
		'gallery'    => array(),
	);

	$image_ids = array_filter( array_merge( array( $product->get_image_id() ), $product->get_gallery_image_ids() ) );
	foreach ( array_unique( $image_ids ) as $id ) {
		$data['gallery'][] = array(
			'id'    => (int) $id,
			'large' => wp_get_attachment_image_url( $id, 'large' ),
			'thumb' => wp_get_attachment_image_url( $id, 'woocommerce_thumbnail' ),
		);
	}

	if ( ! $product->is_type( 'variable' ) ) {
		return $data;
	}

	// get_variation_attributes() keys a taxonomy attribute ("Boja") by its
	// taxonomy slug (pa_boja) and a custom attribute by its sanitized label
	// (boja); values are term slugs for the former, raw option text for the
	// latter. Match either shape so both attribute types work the same way.
	$attributes    = $product->get_variation_attributes();
	$product_attrs = $product->get_attributes();
	$colors        = array();
	$sizes         = array();
	foreach ( $attributes as $raw_name => $values ) {
		$attr    = $product_attrs[ $raw_name ] ?? $product_attrs[ sanitize_title( $raw_name ) ] ?? null;
		$is_tax  = $attr && $attr->is_taxonomy();
		$base    = $is_tax ? str_replace( 'pa_', '', $raw_name ) : sanitize_title( $raw_name );
		$options = array();
		foreach ( $values as $value ) {
			$label             = $is_tax ? ( get_term_by( 'slug', $value, $raw_name )->name ?? $value ) : $value;
			$options[ $value ] = $label; // slug/value => display label
		}
		$key = 'attribute_' . ( $is_tax ? $raw_name : sanitize_title( $raw_name ) );
		if ( 'boja' === $base ) {
			$data['colorKey'] = $key;
			$colors           = $options;
		} elseif ( ! $data['sizeKey'] ) {
			$data['sizeKey'] = $key;
			$sizes           = $options;
		}
	}

	foreach ( $product->get_available_variations( 'objects' ) as $variation ) {
		$attrs    = $variation->get_attributes();
		$image_id = $variation->get_image_id();
		$price    = wc_get_price_to_display( $variation );
		$data['variations'][] = array(
			'id'      => $variation->get_id(),
			'color'   => $data['colorKey'] ? ( $attrs[ substr( $data['colorKey'], 10 ) ] ?? '' ) : '',
			'size'    => $data['sizeKey'] ? ( $attrs[ substr( $data['sizeKey'], 10 ) ] ?? '' ) : '',
			'inStock' => $variation->is_in_stock(),
			'price'   => (float) $price,
			'priceS'  => danci_price( $price ),
			'imageId' => (int) $image_id,
			'image'   => $image_id ? wp_get_attachment_image_url( $image_id, 'large' ) : '',
			'thumb'   => $image_id ? wp_get_attachment_image_url( $image_id, 'woocommerce_thumbnail' ) : '',
		);
	}

	$hex   = danci_color_hex();
	$order = array_keys( $hex );
	if ( ! empty( $colors ) ) {
		uksort( $colors, function ( $a, $b ) use ( $colors, $order ) {
			$ia = array_search( sanitize_title( $colors[ $a ] ), $order, true );
			$ib = array_search( sanitize_title( $colors[ $b ] ), $order, true );
			return ( false === $ia ? 99 : $ia ) <=> ( false === $ib ? 99 : $ib );
		} );
		foreach ( $colors as $value => $label ) {
			$match = current( array_filter( $data['variations'], fn( $v ) => $v['color'] === (string) $value && $v['image'] ) );
			$data['colors'][] = array(
				'value'   => (string) $value, // what the form / JS state sends back
				'label'   => $label,          // what's shown to the customer
				'slug'    => sanitize_title( $label ),
				'hex'     => $hex[ sanitize_title( $label ) ] ?? '#DDDDDD',
				'image'   => $match ? $match['image'] : '',
				'thumb'   => $match ? $match['thumb'] : '',
				'imageId' => $match ? $match['imageId'] : 0,
			);
		}
	}
	if ( ! empty( $sizes ) ) {
		// Kids' numeric sizes first (6, 8, 10 …), then S, M, L, XL …
		$letter = array( 'XS', 'S', 'M', 'L', 'XL', 'XXL', '2XL', '3XL' );
		uksort( $sizes, function ( $a, $b ) use ( $sizes, $letter ) {
			$la = (string) $sizes[ $a ];
			$lb = (string) $sizes[ $b ];
			$ka = danci_is_kid_size( $la ) ? array( 0, (float) $la ) : array( 1, array_search( strtoupper( $la ), $letter, true ) );
			$kb = danci_is_kid_size( $lb ) ? array( 0, (float) $lb ) : array( 1, array_search( strtoupper( $lb ), $letter, true ) );
			return $ka <=> $kb;
		} );
		foreach ( $sizes as $value => $label ) {
			$data['sizes'][] = array(
				'value' => (string) $value,
				'label' => (string) $label,
				'kid'   => danci_is_kid_size( $label ),
			);
		}
	}

	return $data;
}

/** Colour preselected via ?boja=slug (from the home-page swatches), else the first. */
function danci_initial_color( $data ) {
	$wanted = isset( $_GET['boja'] ) ? sanitize_title( wp_unslash( $_GET['boja'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	foreach ( $data['colors'] as $i => $color ) {
		if ( $color['slug'] === $wanted ) {
			return $i;
		}
	}
	return 0;
}

/* ---------------------------------------------------------------------------
 * Cart behaviour
 * ------------------------------------------------------------------------- */

// "Kupi odmah" posts the normal add-to-cart form plus danci_buy_now=1.
add_filter( 'woocommerce_add_to_cart_redirect', function ( $url ) {
	if ( ! empty( $_REQUEST['danci_buy_now'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return wc_get_checkout_url();
	}
	return $url;
} );

// Live cart count + subtotal for the header badges and the add-to-cart drawer.
add_filter( 'woocommerce_add_to_cart_fragments', function ( $fragments ) {
	$count = danci_cart_count();
	$fragments['.d-cart-count']   = '<span class="d-cart-count">' . $count . '</span>';
	$fragments['.d-drawer-count'] = '<span class="d-drawer-count">' . $count . '</span>';
	$fragments['.d-drawer-total'] = '<span class="d-drawer-total">' . esc_html( danci_price( WC()->cart->get_subtotal() ) ) . '</span>';
	return $fragments;
} );

// Cart rows show "Crna · Veličina M".
function danci_cart_item_variant( $cart_item ) {
	$parts = array();
	foreach ( (array) ( $cart_item['variation'] ?? array() ) as $key => $value ) {
		$taxonomy = str_replace( 'attribute_', '', $key );
		$name     = wc_attribute_label( $taxonomy, $cart_item['data'] );
		$label    = taxonomy_exists( $taxonomy ) ? ( get_term_by( 'slug', $value, $taxonomy )->name ?? $value ) : $value;
		$parts[]  = 'boja' === sanitize_title( $name ) ? $label : 'Veličina ' . $label;
	}
	return implode( ' · ', $parts );
}

// Shipping (flat rate) is shown on the cart page before an address is entered.
add_filter( 'woocommerce_shipping_calculator_enable_postcode', '__return_false' );
add_filter( 'pre_option_woocommerce_shipping_cost_requires_address', fn() => 'no' );

/* ---------------------------------------------------------------------------
 * Checkout fields — the mockup's fields in its order and wording, plus
 * Država (the store only ships to Serbia, so it's fixed to Srbija).
 * ------------------------------------------------------------------------- */

function danci_checkout_field_spec() {
	return array(
		'email'      => array( 'Email', 'ime@email.com', 10, 'form-row-wide' ),
		'first_name' => array( 'Ime', 'Ime', 20, 'form-row-first' ),
		'last_name'  => array( 'Prezime', 'Prezime', 30, 'form-row-last' ),
		'country'    => array( 'Država', '', 40, 'form-row-wide' ),
		'address_1'  => array( 'Adresa', 'Ulica i broj', 50, 'form-row-wide' ),
		'city'       => array( 'Grad', 'Grad', 60, 'form-row-first' ),
		'postcode'   => array( 'Poštanski broj', '11000', 70, 'form-row-last' ),
		'phone'      => array( 'Broj telefona', '+381 6x xxx xxxx', 80, 'form-row-wide' ),
	);
}

add_filter( 'woocommerce_checkout_fields', function ( $fields ) {
	$billing = array();
	foreach ( danci_checkout_field_spec() as $key => [ $label, $placeholder, $priority, $class ] ) {
		$field                = $fields['billing'][ 'billing_' . $key ] ?? array();
		$field['label']       = $label;
		$field['placeholder'] = $placeholder;
		$field['priority']    = $priority;
		$field['required']    = true;
		$field['class']       = array( $class );
		$billing[ 'billing_' . $key ] = $field;
	}
	$billing['billing_postcode']['custom_attributes'] = array( 'inputmode' => 'numeric', 'maxlength' => '5' );
	$fields['billing'] = $billing;
	unset( $fields['order'] );
	return $fields;
}, 20 );

// address-i18n.js re-applies locale labels/placeholders/priorities on load; keep ours.
add_filter( 'woocommerce_get_country_locale_default', function ( $locale ) {
	foreach ( danci_checkout_field_spec() as $key => [ $label, $placeholder, $priority, $class ] ) {
		if ( isset( $locale[ $key ] ) ) {
			$locale[ $key ]['label']       = $label;
			$locale[ $key ]['placeholder'] = $placeholder;
			$locale[ $key ]['priority']    = $priority;
			$locale[ $key ]['class']       = array( $class, 'address-field' );
		}
	}
	return $locale;
} );

add_filter( 'woocommerce_get_country_locale', function ( $locales ) {
	foreach ( $locales as $country => $fields ) {
		foreach ( array_keys( danci_checkout_field_spec() ) as $key ) {
			unset( $locales[ $country ][ $key ]['label'], $locales[ $country ][ $key ]['placeholder'], $locales[ $country ][ $key ]['priority'], $locales[ $country ][ $key ]['class'] );
		}
	}
	return $locales;
} );

// The design's extra checks: 5-digit postcode, phone with at least 8 digits.
add_action( 'woocommerce_after_checkout_validation', function ( $data, $errors ) {
	$postcode = trim( (string) ( $data['billing_postcode'] ?? '' ) );
	if ( '' !== $postcode && ! preg_match( '/^\d{5}$/', $postcode ) ) {
		$errors->add( 'billing_postcode_validation', 'Poštanski broj ima 5 cifara.', array( 'id' => 'billing_postcode' ) );
	}
	$phone = (string) ( $data['billing_phone'] ?? '' );
	if ( '' !== trim( $phone ) && strlen( preg_replace( '/\D/', '', $phone ) ) < 8 ) {
		$errors->add( 'billing_phone_validation', 'Unesi ispravan broj telefona.', array( 'id' => 'billing_phone' ) );
	}
}, 10, 2 );

// Not in the design: coupon/login toggles above checkout, order-details table on thank-you.
add_action( 'wp', function () {
	remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );
	remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
	remove_action( 'woocommerce_thankyou', 'woocommerce_order_details_table', 10 );
} );

add_filter( 'woocommerce_enable_order_notes_field', '__return_false' );

// The site language may be English: translate the WooCommerce messages customers can see.
add_filter( 'gettext_woocommerce', function ( $translation, $text ) {
	$map = array(
		'%s is a required field.'           => '%s je obavezno polje.',
		'%s is not a valid email address.'  => '%s nije ispravna email adresa.',
		'%s is not a valid phone number.'   => '%s nije ispravan broj telefona.',
		'%s is not a valid postcode / ZIP.' => '%s nije ispravan poštanski broj.',
		'Please enter a valid email address.' => 'Unesi ispravan email.',
		'Cart updated.'                     => 'Korpa je ažurirana.',
		'%s removed.'                       => '%s je uklonjen iz korpe.',
		'Undo?'                             => 'Vrati?',
		'View cart'                         => 'Pogledaj korpu',
	);
	return $map[ $text ] ?? $translation;
}, 10, 2 );

add_filter( 'gettext_with_context_woocommerce', function ( $translation, $text, $context ) {
	return ( 'Billing %s' === $text && 'checkout-validation' === $context ) ? '%s' : $translation;
}, 10, 3 );

add_filter( 'ngettext_woocommerce', function ( $translation, $single, $plural, $number ) {
	if ( '%s has been added to your cart.' === $single ) {
		return 1 === (int) $number ? '%s je dodat u korpu.' : '%s su dodati u korpu.';
	}
	return $translation;
}, 10, 4 );

add_filter( 'woocommerce_countries', function ( $countries ) {
	$countries['RS'] = 'Srbija';
	return $countries;
} );
add_filter( 'woocommerce_cart_needs_shipping_address', '__return_false' );

// The "ZAVRŠI KUPOVINU" label, without the order total.
add_filter( 'woocommerce_order_button_text', fn() => 'ZAVRŠI KUPOVINU' );

/* ---------------------------------------------------------------------------
 * Custom order numbers: plain numbers starting at 611013, assigned once per
 * checkout order and stored on the order, so the sequence has no gaps even
 * though internal IDs skip (checkout drafts consume IDs). The internal ID
 * is untouched. Orders created outside checkout (wp-admin, REST) keep their
 * plain ID.
 * ------------------------------------------------------------------------- */

define( 'DANCI_ORDER_NUMBER_START', 611013 );

add_action( 'woocommerce_checkout_order_created', function ( $order ) {
	if ( $order->get_meta( '_danci_order_number' ) ) {
		return;
	}
	$next = max( DANCI_ORDER_NUMBER_START, (int) get_option( 'danci_next_order_number', 0 ) );
	update_option( 'danci_next_order_number', $next + 1, false );
	$order->update_meta_data( '_danci_order_number', (string) $next );
	$order->save_meta_data();
} );

add_filter( 'woocommerce_order_number', function ( $order_number, $order ) {
	return $order->get_meta( '_danci_order_number' ) ?: $order_number;
}, 10, 2 );

// Google Apps Script answers every POST with a 302; without following it,
// WooCommerce logs a failed delivery and disables the webhook after 5.
add_filter( 'woocommerce_webhook_http_args', function ( $args ) {
	$args['redirection'] = 5;
	return $args;
} );

/* ---------------------------------------------------------------------------
 * Order confirmation email ("Processing order") — Serbian copy for Danci.
 * Body markup lives in woocommerce/emails/customer-processing-order.php;
 * this sets the subject/heading/footer defaults so they're right even
 * before anyone opens WooCommerce → Settings → Emails.
 * ------------------------------------------------------------------------- */

add_filter( 'woocommerce_email_subject_customer_processing_order', function () {
	return 'Vaša porudžbina u Danči Shop-u je primljena! (#{order_number})';
} );

add_filter( 'woocommerce_email_heading_customer_processing_order', function () {
	return 'Hvala na porudžbini!';
} );

add_filter( 'woocommerce_email_footer_text', function () {
	return 'Danči Shop · dancishop.com';
} );

/* ---------------------------------------------------------------------------
 * Outgoing mail over SMTP (Gmail, for now).
 * Hostinger's PHP mail() fails for this site, so wp_mail() logs in to Gmail.
 * Address and App Password are NOT in the theme (this repo is on GitHub) —
 * they live in wp-config.php on the server as DANCI_SMTP_USER / DANCI_SMTP_PASS.
 * Without them, nothing changes.
 * ------------------------------------------------------------------------- */

add_action( 'phpmailer_init', function ( $phpmailer ) {
	if ( ! defined( 'DANCI_SMTP_USER' ) || ! defined( 'DANCI_SMTP_PASS' ) ) {
		return;
	}
	$phpmailer->isSMTP();
	$phpmailer->Host       = 'smtp.gmail.com';
	$phpmailer->Port       = 587;
	$phpmailer->SMTPSecure = 'tls';
	$phpmailer->SMTPAuth   = true;
	$phpmailer->Username   = DANCI_SMTP_USER;
	$phpmailer->Password   = DANCI_SMTP_PASS;
	$phpmailer->setFrom( DANCI_SMTP_USER, 'Danči Shop', false );
} );

if ( defined( 'DANCI_SMTP_USER' ) ) {
	add_filter( 'woocommerce_email_from_address', fn() => DANCI_SMTP_USER );
}
add_filter( 'woocommerce_email_from_name', fn() => 'Danči Shop' );
