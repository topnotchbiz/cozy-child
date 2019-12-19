<?php
/**
 * Single Product Price
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $wcpl;

if ( $wcpl->is_printable() ) :
	
	$wc_printing_opts = get_post_meta( $product->get_id(), 'wc_printing_opts', true );

	$printing_prices = $wc_printing_opts['prices'];
	
	$prices = $product->get_variation_prices( true );
	$min_price = current( $prices['price'] );
	$min_printing_price = INF;

	foreach ( $printing_prices as $key => $values ) {

		if ( ! isset($values['size'] ) ) {
			continue;
		}

		$size = '';
		$min_qty = INF;

		foreach ( $values['qty_from'] as $qty_from) {
			if ( $min_qty > $qty_from ) {
				$min_qty = $qty_from;
			}
		}

		for ( $i = 0; $i < count( $values['size'] ); $i++ ) {
			if ( $size  !== $values['size'][$i] ) {
				$size = $values['size'][$i];
			}

			$logo_price = $wcpl->get_logo_price( $size, $min_qty, $key );
			
			$price_by_qty = $min_price + $logo_price[0] + $logo_price[1];

			$min_printing_price = min( $price_by_qty, $min_printing_price );
		}
	}

	?>

	<div class="printing_price_holder">
		<?php echo sprintf( 'Customise from <span class="printing_price">%s each</span>', wc_price( $min_printing_price ) ); ?>
	</div>

<?php else : ?>

	<p class="price"><?php echo $product->get_price_html(); ?></p>

<?php endif; ?>
