<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="edgtf-single-product-holder clearfix">

	<?php
		/**
		 * woocommerce_before_single_product hook
		 *
		 * @hooked wc_print_notices - 10
		 */
		do_action('woocommerce_before_single_product');

		if (post_password_required()) {
			echo get_the_password_form();
			return;
		}
	?>

	<div id="product-<?php the_ID(); ?>" <?php post_class('edgtf-single-product-wrapper-top'); ?>>
		
		<div class="edgtf-single-product-left">
			<?php

			/**
			 * woocommerce_before_single_product_summary hook
			 *
			 * @hooked woocommerce_show_product_images - 20
			 */
			do_action('woocommerce_before_single_product_summary');
			?>
		</div>

		<div class="edgtf-single-product-right">
			<div class="edgtf-single-product-summary">
				<div class="summary entry-summary">
					<div class="quote_wrapper">
					  <span class="quote_label">
					    <?php _e( 'Need a quote?' ); ?>
					  </span>
					  <a href="/quick-quote"><?php _e( 'Get a quick quote' ); ?></a>
					</div>

					<?php
					$product_brands = get_the_terms( get_the_ID(), 'yith_product_brand' );
					
					if ( is_array( $product_brands ) ) { ?>

						<div class="edgtf-single-product-brand">

							<?php
							foreach ( $product_brands as $term ) {
								echo sprintf( '<a href="%s">%s</a>', get_term_link( $term ), $term->name );
							}
							?>

						</div>

					<? } ?>
					</div>

					<?php
					/**
					 * woocommerce_single_product_summary hook
					 *
					 * @hooked cozy_edge_woocommerce_template_single_title - 5
					 * @hooked woocommerce_template_single_rating - 10
					 * @hooked woocommerce_template_single_price - 10
					 * @hooked woocommerce_template_single_excerpt - 20
					 * @hooked woocommerce_template_single_add_to_cart - 30
					 * @hooked woocommerce_template_single_meta - 40
					 * @hooked woocommerce_template_single_sharing - 50
					 */
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
					add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_product_sku', 6 );
					
					do_action('woocommerce_single_product_summary');
					?>

				</div>
			</div>
			<!-- .edgtf-single-product-summary -->
		</div>

		<?php
			/**
			 * Hook: woocommerce_after_single_product_summary.
			 *
			 * @hooked woocommerce_output_product_data_tabs - 10
			 * @hooked woocommerce_upsell_display - 15
			 * @hooked woocommerce_output_related_products - 20
			 */
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );

			do_action( 'woocommerce_after_single_product_summary' );
		?>
	</div><!-- #product-<?php the_ID(); ?> -->

</div><!-- .edgtf-single-product-holder -->

<?php do_action('woocommerce_after_single_product'); ?>
