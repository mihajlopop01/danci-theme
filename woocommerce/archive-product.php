<?php
/**
 * Shop / category archive: not in the design, so it reuses the home
 * page's "Sve majice" section.
 */
defined( 'ABSPATH' ) || exit;

get_header( null, array( 'variant' => 'default' ) );
?>
<main class="d-shop">
	<?php get_template_part( 'template-parts/product-grid' ); ?>
</main>
<?php
get_footer( null, array( 'variant' => 'full' ) );
