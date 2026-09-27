<?php
/**
 * Product page (mockup screen 02) and the size-guide popup (03).
 *
 * Colour/size buttons fill a normal WooCommerce add-to-cart form
 * (add-to-cart + variation_id + attribute_* + quantity), so it also works
 * without JavaScript; danci.js adds the AJAX add + drawer on top.
 */
defined( 'ABSPATH' ) || exit;

get_header( null, array( 'variant' => 'default', 'back' => danci_products_url() ) );

while ( have_posts() ) :
	the_post();
	global $product;
	$product = wc_get_product( get_the_ID() );
	$data    = danci_product_data( $product );
	$ci      = danci_initial_color( $data );
	$color   = $data['colors'][ $ci ] ?? null;
	$price_s = danci_price_label( $product );
	$kids    = array_values( array_filter( $data['sizes'], fn( $s ) => $s['kid'] ) );
	$adults  = array_values( array_filter( $data['sizes'], fn( $s ) => ! $s['kid'] ) );

	// Main image: the selected colour's variation image, else the first gallery image.
	$main_id  = $color && $color['imageId'] ? $color['imageId'] : ( $data['gallery'][0]['id'] ?? 0 );
	$main_src = $color && $color['image'] ? $color['image'] : ( $data['gallery'][0]['large'] ?? wc_placeholder_img_src( 'large' ) );

	$others = array_filter( danci_products(), fn( $p ) => $p->get_id() !== $product->get_id() );

	$size_buttons = function ( $sizes ) {
		foreach ( $sizes as $s ) {
			printf(
				'<button type="button" class="d-size" aria-pressed="false" data-d-size data-value="%1$s" data-label="%2$s">%3$s</button>',
				esc_attr( $s['value'] ),
				esc_attr( $s['label'] ),
				esc_html( $s['label'] )
			);
		}
	};
	?>
	<main class="d-product">
		<div class="d-crumbs"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Početna</a><span>/</span><a href="<?php echo esc_url( danci_products_url() ); ?>">Majice</a><span>/</span><span class="is-current"><?php the_title(); ?></span></div>

		<?php woocommerce_output_all_notices(); ?>

		<div class="d-product__grid">
			<div class="d-gallery">
				<?php if ( count( $data['gallery'] ) > 1 ) : ?>
					<div class="d-gallery__thumbs">
						<?php foreach ( $data['gallery'] as $img ) : ?>
							<button type="button" class="d-thumb<?php echo $img['id'] === $main_id ? ' is-active' : ''; ?>" data-d-thumb data-id="<?php echo (int) $img['id']; ?>" data-large="<?php echo esc_url( $img['large'] ); ?>" aria-label="Slika proizvoda">
								<img src="<?php echo esc_url( $img['thumb'] ); ?>" alt="" loading="lazy">
							</button>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<div class="d-gallery__main"><img src="<?php echo esc_url( $main_src ); ?>" alt="<?php the_title_attribute(); ?>" data-d-main-img></div>
			</div>

			<form class="d-buy" method="post" action="<?php echo esc_url( $product->get_permalink() ); ?>" data-d-buy
				data-variations="<?php echo esc_attr( wp_json_encode( $data['variations'] ) ); ?>"
				data-name="<?php echo esc_attr( $product->get_name() ); ?>"
				data-price="<?php echo esc_attr( $price_s ); ?>">
				<div class="d-buy__title">
					<span class="d-tag">#DANČISQUAD</span>
					<h1><?php the_title(); ?></h1>
					<div class="d-buy__price" data-d-price><?php echo esc_html( $price_s ); ?></div>
				</div>

				<?php if ( count( $data['colors'] ) > 1 ) : ?>
					<div class="d-buy__group">
						<div class="d-label">BOJA: <span data-d-color-label><?php echo esc_html( $color['label'] ); ?></span></div>
						<div class="d-colors">
							<?php foreach ( $data['colors'] as $i => $c ) : ?>
								<button type="button" class="d-color<?php echo $i === $ci ? ' is-selected' : ''; ?>" aria-pressed="<?php echo $i === $ci ? 'true' : 'false'; ?>"
									data-d-color data-value="<?php echo esc_attr( $c['value'] ); ?>" data-label="<?php echo esc_attr( $c['label'] ); ?>" data-image="<?php echo esc_url( $c['image'] ); ?>" data-image-id="<?php echo (int) $c['imageId']; ?>">
									<span class="d-color__dot" style="background:<?php echo esc_attr( $c['hex'] ); ?>"></span><?php echo esc_html( $c['label'] ); ?>
								</button>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( $data['sizes'] ) : ?>
					<div class="d-buy__group">
						<div class="d-buy__size-head">
							<div class="d-label">VELIČINA: <span data-d-size-label>—</span></div>
							<button type="button" class="d-guide-link" data-d-guide-open><?php echo danci_icon( 'ruler', 16 ); ?>VODIČ ZA VELIČINE</button>
						</div>
						<div class="d-sizes" data-d-sizes>
							<?php if ( $kids && $adults ) : ?>
								<span class="d-sizes__group">DECA</span>
								<div class="d-sizes__row"><?php $size_buttons( $kids ); ?></div>
								<span class="d-sizes__group">ODRASLI</span>
								<div class="d-sizes__row"><?php $size_buttons( $adults ); ?></div>
							<?php else : ?>
								<div class="d-sizes__row d-sizes__row--full"><?php $size_buttons( $data['sizes'] ); ?></div>
							<?php endif; ?>
						</div>
						<div class="d-buy__error" data-d-error hidden>Izaberi veličinu pre dodavanja u korpu.</div>
					</div>
				<?php endif; ?>

				<div class="d-buy__actions">
					<div class="d-qty">
						<button type="button" aria-label="Manje" data-d-qty="-1">−</button>
						<span data-d-qty-value>1</span>
						<button type="button" aria-label="Više" data-d-qty="1">+</button>
					</div>
					<button type="submit" class="d-btn d-btn--primary d-buy__add d-desktop-flex" data-d-add>DODAJ U KORPU</button>
					<button type="submit" class="d-btn d-btn--light d-buy__now d-mobile-flex" name="danci_buy_now" value="1">KUPI ODMAH →</button>
				</div>
				<button type="submit" class="d-btn d-btn--light d-buy__now d-desktop-flex" name="danci_buy_now" value="1">KUPI ODMAH →</button>

				<div class="d-info">
					<div><?php echo danci_icon( 'truck', 20 ); ?>Post Express · 1–3 radna dana · samo Srbija</div>
					<div><?php echo danci_icon( 'cash', 20 ); ?>Plaćanje pouzećem</div>
				</div>

				<?php if ( $product->get_description() ) : ?>
					<div class="d-buy__desc"><?php the_content(); ?></div>
				<?php endif; ?>

				<input type="hidden" name="add-to-cart" value="<?php echo (int) $product->get_id(); ?>">
				<input type="hidden" name="product_id" value="<?php echo (int) $product->get_id(); ?>">
				<input type="hidden" name="variation_id" value="" data-d-variation>
				<input type="hidden" name="quantity" value="1" data-d-qty-input>
				<?php if ( $data['colorKey'] ) : ?>
					<input type="hidden" name="<?php echo esc_attr( $data['colorKey'] ); ?>" value="<?php echo esc_attr( $color['value'] ?? '' ); ?>" data-d-color-input>
				<?php endif; ?>
				<?php if ( $data['sizeKey'] ) : ?>
					<input type="hidden" name="<?php echo esc_attr( $data['sizeKey'] ); ?>" value="" data-d-size-input>
				<?php endif; ?>

				<div class="d-sticky-buy">
					<div class="d-sticky-buy__info">
						<span class="d-sticky-buy__price" data-d-price><?php echo esc_html( $price_s ); ?></span>
						<span class="d-sticky-buy__variant"><?php if ( $color ) : ?><span data-d-color-label><?php echo esc_html( $color['label'] ); ?></span> · <?php endif; ?><span data-d-size-label>—</span></span>
					</div>
					<button type="submit" class="d-btn d-btn--cyan" data-d-add>DODAJ U KORPU</button>
				</div>
			</form>
		</div>

		<?php if ( $others ) : ?>
			<section class="d-others">
				<h2 class="d-others__title">Pogledaj i ostale<span class="d-desktop-inline"> majice</span></h2>
				<div class="d-others__grid">
					<?php foreach ( $others as $o ) :
						$od  = danci_product_data( $o );
						$img = ( $od['colors'][0]['thumb'] ?? '' ) ?: ( $od['gallery'][0]['thumb'] ?? wc_placeholder_img_src() );
						?>
						<a class="d-other" href="<?php echo esc_url( $o->get_permalink() ); ?>">
							<img src="<?php echo esc_url( $img ); ?>" alt="" loading="lazy">
							<div class="d-other__meta">
								<div class="d-other__name"><?php echo esc_html( $o->get_name() ); ?></div>
								<div class="d-other__price"><?php echo esc_html( danci_price_label( $o ) ); ?></div>
							</div>
							<span class="d-other__arrow">→</span>
						</a>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endif; ?>
	</main>

	<?php /* Size guide: centred modal on desktop, bottom sheet on mobile. */ ?>
	<div class="d-overlay d-guide" hidden data-d-guide>
		<div class="d-guide__box" role="dialog" aria-modal="true" aria-labelledby="d-guide-title">
			<div class="d-sheet-handle"></div>
			<div class="d-dialog-head">
				<div class="d-dialog-title" id="d-guide-title">Vodič za veličine</div>
				<button type="button" class="d-close" aria-label="Zatvori" data-d-close>✕</button>
			</div>
			<?php get_template_part( 'template-parts/size-table' ); ?>
		</div>
	</div>
	<?php
endwhile;

get_footer( null, array( 'variant' => 'full' ) );
