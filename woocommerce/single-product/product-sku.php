<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;
?>

<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>

	<span class="edgtf-single-product-sku sku_wrapper"><?php esc_html_e( 'Product code:', 'cozy' ); ?> <span class="sku" itemprop="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : esc_html_e( 'N/A', 'cozy' ); ?></span></span>

<?php endif; ?>