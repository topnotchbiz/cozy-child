<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCBR' ) ) {
	exit;
} // Exit if accessed directly
?>

<?php if( $product_has_brands ): ?>

	<?php if ( ! empty( $title ) ): ?>
		<h3><?php echo $title ?></h3>
	<?php endif; ?>

	<?php if( ! isset( $content_to_show ) || ( $content_to_show == 'both' || $content_to_show == 'name' ) ): ?>
		<span class="yith-wcbr-brands">
			<?php echo $brands_label ?>
			<span itemprop="brand" ><?php echo get_the_term_list( $product_id, $brands_taxonomy, $before_term_list, $term_list_sep, $after_term_list ); ?></span>
		</span>
	<?php endif; ?>

<?php endif; ?>