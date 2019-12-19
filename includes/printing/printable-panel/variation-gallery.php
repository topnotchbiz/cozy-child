<div class="ndx-Variation-Gallery--slider">
	
	<?php 
	foreach ( $available_variations as $variation ) {
		$pos_images = array();

		foreach ( $positions['label'] as $label ) {
			if ( 
				isset($variation['position_images']) && 
				isset($variation['position_images'][$label]) && 
				$variation['position_images'][$label]
			) {
				$pos_thumb = wp_get_attachment_image_src( $variation['position_images'][$label], 'woocommerce_thumbnail' );
				$pos_img = wp_get_attachment_image_src( $variation['position_images'][$label], 'large' );
			} else {
				$pos_thumb = $placeholder_image;
				$pos_img = $placeholder_image;
			}

			$pos_images[$label] = array($pos_thumb, $pos_img);
		}
		?>

		<div class="ndx-Variation-slide">
			<div class="ndx-Variation-slide-img" data-pos-images="<?php echo esc_html(json_encode($pos_images)); ?>">
				<img src="<?php echo $pos_images[$positions['label'][0]][0][0]; ?>" />
			</div>
			<div class="ndx-Variation-slide-text">
				<?php echo esc_html( get_term_by( 'slug', $variation['attributes']['attribute_pa_color'], 'pa_color' )->name ); ?>
			</div>
		</div>

	<?php } ?>

</div>
