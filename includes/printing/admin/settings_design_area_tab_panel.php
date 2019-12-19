<div id="design_area_tab_data" class="panel woocommerce_options_panel">
	<table class="form-table" style="table-layout: fixed;">
		<thead>
			<tr valign="top">
				<td>
					<h4 style="margin:0;"><?php _e('Define the Customisable Area', 'wc_printing'); ?></h4>
				</td>
			</tr>             
		</thead>
		<tbody>
		<?php
		if (isset($wc_printing_opts['positions'])) {
			$positions = $wc_printing_opts['positions'];
			
			if ( ! empty( $positions['label'] ) && is_array( $positions['label'] ) ) {
				$product = wc_get_product( $post->ID );
				$available_variations = $product->get_available_variations();

				foreach ( $positions['label'] as $position ) {
				?>
					<tr valign="top">
						<td>
							<strong style="margin-bottom: 15px; color: #000;"><?php echo $position; ?></strong>
							<div class="pos-design-area">
								<?php
								foreach ( $available_variations as $variation ) {
									$variation_pos_imgs = $variation['position_images'];

									if ( $variation_pos_imgs[$position] ) {
										?>

										<img class="jcrop-pos-img" data-position="<?php echo $position; ?>" 
											src="<?php echo wp_get_attachment_image_src( $variation_pos_imgs[$position], 'cozy_edge_square' )[0]; ?>" />

										<?php
										break;
									}
								}
								?>
								<input type="hidden" class="pos-img-bounding" 
									name="wc_design_bounding[<?php echo $position; ?>]" 
									value="<?php echo !empty($wc_design_bounding) && $wc_design_bounding[$position] ? $wc_design_bounding[$position] : ''; ?>" />
								<input type="hidden" class="pos-img-area" name="wc_design_area[<?php echo $position; ?>]" 
									value="<?php echo !empty($wc_design_area) && $wc_design_area[$position] ? $wc_design_area[$position] : ''; ?>" />
							</div>
						</td>                
					</tr>
				<?php
				}
			}
		}
		?>		
		</tbody>
	</table>
</div>