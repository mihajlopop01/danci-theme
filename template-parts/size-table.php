<?php
/**
 * Size table (mockup screen 03). Measurements from the design; the adult
 * sizes have none yet, so they show "—".
 */
$cols = array( '6', '8', '10', '12', '14', 'S', 'M', 'L' );
$rows = array(
	array( 'A · širina', 'A', array( '6' => 34, '8' => 37, '10' => 40, '12' => 42, '14' => 45 ) ),
	array( 'B · dužina', 'B', array( '6' => 47, '8' => 52, '10' => 54, '12' => 56, '14' => 58 ) ),
);
?>
<div class="d-sizetable">
	<span class="d-sizetable__corner">CM</span>
	<?php foreach ( $cols as $c ) : ?>
		<span class="d-sizetable__head"><?php echo esc_html( $c ); ?></span>
	<?php endforeach; ?>
	<?php foreach ( $rows as [ $label, $short, $values ] ) : ?>
		<span class="d-sizetable__label"><span class="d-desktop-inline"><?php echo esc_html( $label ); ?></span><span class="d-mobile-inline"><?php echo esc_html( $short ); ?></span></span>
		<?php foreach ( $cols as $c ) : ?>
			<span><?php echo esc_html( $values[ $c ] ?? '—' ); ?></span>
		<?php endforeach; ?>
	<?php endforeach; ?>
</div>
<p class="d-sizetable__note"><span class="d-desktop-inline">A: širina ispod pazuha · B: dužina od ramena.</span><span class="d-mobile-inline">A: širina · B: dužina.</span> Moguće odstupanje ±5%.</p>
