<?php
global $product;
$post_thumbnail_id = $product->get_image_id();

if ( $product->get_image_id() ) {
	$html = wp_get_attachment_image( $post_thumbnail_id );
} else {
	$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_html__( 'Awaiting product image', 'cozy' ) );
}
?>

<div class="ndx-Menu ndx-title3 ndx-NavHeader">
	<h5 class="ndx-NavHeader-title"><?php echo __('Price'); ?></h5>
	<div class="ndx-MenuButton isActionable ndx-NavHeader-close"></div>
</div>
<div class="prod-Design">
	<div class="prod-Item clearfix">
		<div class="prod-Item-canvasContainer">
			<?php echo $html; ?>
		</div>
		<div class="prod-Item-details">
			<?php the_title( '<h4 class="prod-ItemName">', '</h4>' ); ?>
			<div class="prod-Item-details-content">
				<div class="prod-Item-details-label"><?php esc_html_e( 'SKU' ); ?></div>
				<div class="prod-Item-details-item">
					<?php echo $product->get_sku(); ?>
				</div>
				<div class="prod-Item-details-label"><?php esc_html_e( 'Brand' ); ?></div>
				<div class="prod-Item-details-item">
					<?php echo get_the_term_list( $product->get_id(), 'yith_product_brand', '', ',', '' ); ?>
				</div>
				<div class="prod-Item-details-label">Decoration</div>
				<div class="prod-Item-details-decos"></div>
			</div>
		</div>
	</div>

	<div class="prod-Variation-Cnt">
		<span class="tooltip is-Active"><?php esc_html_e( 'Enter qty & sizes' ); ?></span>
		<?php $size_terms = wc_get_product_terms( $product->get_id(), 'pa_size' ); ?>

		<table>
			<tbody>
				<?php
				foreach ( $available_variations as $variation ) {
					$attributes = $variation['attributes'];

					if ( isset( $attributes['attribute_pa_color'] ) ) {
						$colour_val = $attributes['attribute_pa_color'];
						$colour_term = get_term_by( 'slug', $colour_val, 'pa_color' );
						?>

						<tr class="variation_term">
							<th>
								<?php
								$colours = array();

								while ( get_field( 'colour_' . ( count( $colours ) + 1 ), $colour_term ) ) {
									$colours[] = get_field( 'colour_' . ( count( $colours ) + 1 ), $colour_term );
								}

								$cnt = count ( $colours );
								$swatch_html = '';

								foreach ( $colours as $key => $colour ) {
									$swatch_html .= '<span style="background-color: ' . $colour . '; width: ' . ( 100 / $cnt ) . '%;"></span>';
								}
								?>
								<span class="colour_holder">
									<span class="<?php echo $colour_val; ?> colours_<?php echo $cnt; ?>">
										<?php echo $swatch_html; ?>
									</span>
								</span>
							</th>
							<th class="colour_name"><?php echo $colour_term->name; ?></th>
							
							<td>
								<div class="variation_term_holder clearfix">
									<?php foreach ( $size_terms as $term ) { ?>
										<div class="variation_single_term">
											<label>
												<span class="variation_term_name"><?php echo $term->name; ?></span>
												<input type="number" class="variation_term_qty"
													data-variation_id="<?php echo $variation['variation_id']; ?>"
													data-attribute_pa_color="<?php echo $colour_term->slug; ?>"
													data-attribute_pa_size="<?php echo $term->slug; ?>"
											</label>
										</div>
									<?php } ?>
								</div>
							</td>


						</tr>

						<?php
					}
				}
				?>
			</tbody>
		</table>
	</div>

	<div class="prod-Design-comments">
    <label>Add here any additional comments.</label>
    <textarea></textarea>
	</div>
</div>

