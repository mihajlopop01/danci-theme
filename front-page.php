<?php
/**
 * Home (mockup screen 01): banner, headline band, lifestyle tiles, product grid.
 */
get_header( null, array( 'variant' => 'home' ) );
$features = danci_features();
?>
<main class="d-home">
	<img class="d-hero" src="<?php echo esc_url( danci_asset( 'images/banner.jpg' ) ); ?>" alt="Danči" fetchpriority="high">

	<section class="d-band">
		<div class="d-band__text">
			<h1 class="d-band__title">NOVE DANČI MAJICE SU U PRODAJI</h1>
			<p class="d-band__sub">Požurite, količine su ograničene.</p>
		</div>
		<a class="d-btn d-btn--cyan d-band__cta" href="#proizvodi">POGLEDAJ MAJICE <?php echo danci_icon( 'down', 22 ); ?></a>
	</section>

	<?php if ( $features ) : ?>
		<section class="d-features">
			<?php foreach ( $features as $f ) : ?>
				<a class="d-feature" href="<?php echo esc_url( $f['url'] ); ?>">
					<img src="<?php echo esc_url( $f['image'] ); ?>" alt="<?php echo esc_attr( $f['name'] ); ?>" loading="lazy">
					<div class="d-feature__row">
						<div>
							<div class="d-feature__slogan">„<?php echo esc_html( $f['slogan'] ); ?>”</div>
							<div class="d-feature__name"><?php echo esc_html( $f['name'] ); ?></div>
						</div>
						<span class="d-feature__cta"><span class="d-desktop-inline">POGLEDAJ MAJICU </span>→</span>
					</div>
				</a>
			<?php endforeach; ?>
		</section>
	<?php endif; ?>

	<?php get_template_part( 'template-parts/product-grid' ); ?>
</main>
<?php
get_footer( null, array( 'variant' => 'full' ) );
