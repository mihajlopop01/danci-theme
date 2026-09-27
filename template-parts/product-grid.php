<?php
/**
 * "Sve majice" section (mockup screen 01): product cards with colour
 * swatches, size chips and a quick "Dodaj u korpu" (danci.js adds via AJAX
 * and opens the drawer). Used by the front page and the shop archive.
 */
$products = danci_products();
?>
<section class="d-products" id="proizvodi">
	<div class="d-products__head">
		<h2 class="d-products__title">Sve majice</h2>
		<p class="d-products__sub">Dečje veličine 6–14 · odrasli S–L</p>
	</div>
	<div class="d-products__grid">
		<?php foreach ( $products as $product ) :
			$data  = danci_product_data( $product );
			$url   = $product->get_permalink();
			$first = $data['colors'][0] ?? null;
			$image = $first && $first['image'] ? $first['image'] : ( $data['gallery'][0]['large'] ?? wc_placeholder_img_src( 'large' ) );
			$link  = $first && count( $data['colors'] ) > 1 ? add_query_arg( 'boja', $first['slug'], $url ) : $url;
			?>
			<div class="d-card" data-d-card
				data-variations="<?php echo esc_attr( wp_json_encode( $data['variations'] ) ); ?>"
				data-name="<?php echo esc_attr( $product->get_name() ); ?>"
				data-color="<?php echo esc_attr( $first['value'] ?? '' ); ?>"
				data-color-label="<?php echo esc_attr( $first['label'] ?? '' ); ?>"
				data-price="<?php echo esc_attr( danci_price_label( $product ) ); ?>">
				<a class="d-card__media" href="<?php echo esc_url( $link ); ?>" data-d-card-link tabindex="-1">
					<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" data-d-card-img loading="lazy">
				</a>
				<div class="d-card__info">
					<div class="d-card__titles">
						<a class="d-card__name" href="<?php echo esc_url( $link ); ?>" data-d-card-link><?php echo esc_html( $product->get_name() ); ?></a>
						<div class="d-card__price" data-d-card-price><?php echo esc_html( danci_price_label( $product ) ); ?></div>
					</div>

					<?php if ( count( $data['colors'] ) > 1 ) : ?>
						<div class="d-card__colors">
							<span class="d-card__label">BOJA: <span data-d-card-color><?php echo esc_html( $first['label'] ); ?></span></span>
							<?php foreach ( $data['colors'] as $i => $color ) : ?>
								<button type="button" class="d-swatch<?php echo 0 === $i ? ' is-selected' : ''; ?>" aria-label="<?php echo esc_attr( $color['label'] ); ?>" aria-pressed="<?php echo 0 === $i ? 'true' : 'false'; ?>"
									data-d-swatch data-value="<?php echo esc_attr( $color['value'] ); ?>" data-label="<?php echo esc_attr( $color['label'] ); ?>" data-image="<?php echo esc_url( $color['image'] ); ?>" data-href="<?php echo esc_url( add_query_arg( 'boja', $color['slug'], $url ) ); ?>">
									<span style="background:<?php echo esc_attr( $color['hex'] ); ?>"></span>
								</button>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<?php if ( $data['sizes'] ) : ?>
						<div class="d-card__sizes-head"><span class="d-card__label">VELIČINA</span><span class="d-card__error" data-d-card-error hidden>Izaberi veličinu</span></div>
						<div class="d-card__sizes" data-d-card-sizes>
							<?php foreach ( $data['sizes'] as $s ) : ?>
								<button type="button" class="d-chip" aria-pressed="false" data-d-card-size data-value="<?php echo esc_attr( $s['value'] ); ?>" data-label="<?php echo esc_attr( $s['label'] ); ?>"><?php echo esc_html( $s['label'] ); ?></button>
							<?php endforeach; ?>
						</div>
						<a class="d-btn d-btn--primary d-card__add" href="<?php echo esc_url( $link ); ?>" data-d-card-add>DODAJ U KORPU</a>
					<?php else : ?>
						<a class="d-btn d-btn--light d-card__add" href="<?php echo esc_url( $link ); ?>">IZABERI VELIČINU →</a>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>
