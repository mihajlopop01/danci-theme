<?php
/**
 * Document head + site header.
 *
 * $args['variant']:
 *   home     – announcement bar + navy header (front page)
 *   default  – navy header (product, cart, pages)
 *   checkout – navy header with steps + "Nazad u korpu"
 *   bare     – nothing (thank-you screen)
 * $args['back']: on mobile, show a back arrow to this URL instead of the menu button.
 * $args['cart']: false hides the mobile cart button (cart page).
 */
$variant = $args['variant'] ?? 'default';
$back    = $args['back'] ?? '';
$current = danci_current_nav();
$cart    = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '#';
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class( 'd-variant-' . $variant ); ?>>
<?php wp_body_open(); ?>

<?php if ( 'home' === $variant ) : ?>
	<div class="d-announce">Dostava samo za Srbiju. Uskoro dostava za ceo Balkan.</div>
<?php endif; ?>

<?php if ( in_array( $variant, array( 'home', 'default' ), true ) ) : ?>
	<header class="d-header">
		<?php if ( $back ) : ?>
			<a class="d-header__icon d-mobile-only" href="<?php echo esc_url( $back ); ?>" aria-label="Nazad"><?php echo danci_icon( 'back', 22 ); ?></a>
		<?php else : ?>
			<button class="d-header__icon d-mobile-only" type="button" aria-label="Meni" aria-expanded="false" data-d-menu-toggle><?php echo danci_icon( 'menu', 24 ); ?></button>
		<?php endif; ?>
		<a class="d-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo danci_logo(); ?></a>
		<nav class="d-header__nav" aria-label="Glavni meni">
			<?php foreach ( danci_nav_items() as $key => [ $label, $url ] ) : ?>
				<a href="<?php echo esc_url( $url ); ?>"<?php echo $key === $current ? ' class="is-active" aria-current="page"' : ''; ?>><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</nav>
		<?php if ( $args['cart'] ?? true ) : ?>
			<a class="d-cart-pill" href="<?php echo esc_url( $cart ); ?>" aria-label="Korpa">
				<?php echo danci_icon( 'cart', 20 ); ?><span class="d-cart-pill__label">KORPA</span>
				<span class="d-cart-pill__badge"><span class="d-cart-count"><?php echo (int) danci_cart_count(); ?></span></span>
			</a>
		<?php else : ?>
			<a class="d-cart-pill d-desktop-flex" href="<?php echo esc_url( $cart ); ?>" aria-label="Korpa">
				<?php echo danci_icon( 'cart', 20 ); ?><span class="d-cart-pill__label">KORPA</span>
				<span class="d-cart-pill__badge"><span class="d-cart-count"><?php echo (int) danci_cart_count(); ?></span></span>
			</a>
			<span class="d-header__spacer d-mobile-only"></span>
		<?php endif; ?>

		<?php if ( ! $back ) : ?>
			<div class="d-mobile-menu" hidden data-d-menu>
				<?php foreach ( danci_nav_items() as $key => [ $label, $url ] ) : ?>
					<a href="<?php echo esc_url( $url ); ?>"<?php echo $key === $current ? ' class="is-active"' : ''; ?>><?php echo esc_html( $label ); ?></a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</header>
<?php elseif ( 'checkout' === $variant ) : ?>
	<header class="d-header d-header--checkout">
		<a class="d-header__icon d-mobile-only" href="<?php echo esc_url( $cart ); ?>" aria-label="Nazad u korpu"><?php echo danci_icon( 'back', 22 ); ?></a>
		<a class="d-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo danci_logo(); ?></a>
		<div class="d-steps"><span>Korpa</span><span>→</span><span class="is-active">Podaci za dostavu</span><span>→</span><span>Potvrda</span></div>
		<a class="d-header__back" href="<?php echo esc_url( $cart ); ?>"><?php echo danci_icon( 'back', 18 ); ?>Nazad u korpu</a>
		<span class="d-header__spacer d-mobile-only"></span>
	</header>
<?php endif; ?>
