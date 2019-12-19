</div>

<?php
global $product;
$wc_printing_opts = get_post_meta($product->get_id(), 'wc_printing_opts', true);
$wc_design_bounding = get_post_meta($product->get_id(), 'wc_design_bounding', true);
$wc_design_area = get_post_meta($product->get_id(), 'wc_design_area', true);

if ( !$wc_printing_opts ) {
	return;
}

$positions = $wc_printing_opts['positions'];

unset( $wc_printing_opts['positions']['image'] );

$available_variations = $product->get_available_variations();
$price = $available_variations[0]['display_price'];
$placeholder_image = wp_get_attachment_image_src( get_option( 'woocommerce_placeholder_image', 0 ), 'large' );

if ( !isset($positions) ) return;

$post_thumbnail_id = $product->get_image_id();

if ( $product->get_image_id() ) {
	$thumb_html = wp_get_attachment_image( $post_thumbnail_id );
} else {
	$thumb_html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_html__( 'Awaiting product image', 'cozy' ) );
}
?>

<div class="ndx-App-holder clearfix" 
	data-printing-opts="<?php echo esc_html( json_encode( $wc_printing_opts ) ); ?>" 
	data-display-price="<?php echo $price; ?>">
	<div class="ndx-Container ndx-Container--relative ndx-App">
		<div class="ndx-Content-main" style="position: relative;">
			<div class="ndx-App-menuContainer ndx-Content-menu--floating">
				<div class="ndx-label4 ndx-VerticalToolbar">
					<?php include 'vertical-toolbar-styles.php'; ?>
					<?php include 'vertical-toolbar.php'; ?>
				</div>
				<div class="ndx-Container ndx-Container--relative ndx-PanelContainer">
					<div class="ndx-Panel">
						<?php
						include 'printing-panel.php';
						include 'welcome-panel.php';
						include 'text-panel.php';
						include 'upload-panel.php';
						?>
					</div>
				</div>
			</div>
			<div class="ndx-CanvasContainer">
				<div class="ndx-Canvas" style="position: relative;">
					<div class="ndx-Canvas-zoomContainer">
						<div class="ndx-Product">
							<?php foreach ($positions['label'] as $key => $value) { ?>
								<div class="ndx-Product-position <?php echo $key == 0 ? 'ndx-Product-position--active' : ''; ?>">
									<?php
									if ( 
										isset($available_variations[0]['position_images']) && 
										isset($available_variations[0]['position_images'][$value]) && 
										$available_variations[0]['position_images'][$value] 
									) {
										$pos_img = wp_get_attachment_image_src( $available_variations[0]['position_images'][$value], 'large' );
									} else {
										$pos_img = $placeholder_image;
									}
									?>

									<img 
										src="<?php echo $pos_img[0]; ?>" 
										width="<?php echo $pos_img[1]; ?>" 
										height="<?php echo $pos_img[2]; ?>" 
										class="ndx-Product-photo ndx-Product-photo-main"
									/>
									<div class="ndx-Design-printableArea">
										<span class="ndx-Design-posLabel"></span>
										<div class="content-inner"
											<?php echo $wc_design_area && !empty($wc_design_area) && $wc_design_area[$value] ? 'data-design-area="' . $wc_design_area[$value] . '"' : ''; ?>
											<?php echo $wc_design_bounding && !empty($wc_design_bounding) && $wc_design_area[$value] ? 'data-design-bounding="' . $wc_design_bounding[$value] . '"' : ''; ?>
										></div>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
				<div>
					<div class="ndx-CanvasContainer-toolbar" style="opacity: 1;">
						<div class="ndx-CanvasToolbar u-clickPassThrough">
							<div class="ndx-CanvasToolbar-changeViews">
								<?php foreach ($positions['label'] as $key => $value) { ?>
									<div class="ndx-Menu ndx-CanvasToolbar-menu <?php echo $key == 0 ? 'ndx-CanvasToolbar-menu--active' : ''; ?>">
										<div class="ndx-MenuButton isActionable ndx-CanvasToolbar-changeViewButton ndx-label4" data-pos-label="<?php echo $value; ?>">
											<div class="ndx-CanvasContainer-changeView">
												<div class="ndx-Canvas" style="position: relative;">
													<div class="ndx-Canvas-zoomContainer">
														<div class="ndx-Product">
															<?php
															if (
																isset($available_variations[0]['position_images']) && 
																isset($available_variations[0]['position_images'][$value]) && 
																$available_variations[0]['position_images'][$value] 
															) {
																$pos_img = wp_get_attachment_image_src( $available_variations[0]['position_images'][$value], 'large' );
															} else {
																$pos_img = $placeholder_image;
															}
															?>

															<img 
																src="<?php echo $pos_img[0]; ?>" 
																width="<?php echo $pos_img[1]; ?>" 
																height="<?php echo $pos_img[2]; ?>" 
																class="ndx-Product-photo ndx-Product-photo-main"
															/>
														</div>
													</div>
													<div class="resize-triggers">
														<div class="expand-trigger">
															<div style="width: 41px; height: 45px;"></div>
														</div>
														<div class="contract-trigger"></div>
													</div>
												</div>
											</div>
											<div class="ndx-MenuButton-text"><?php echo $positions['label'][$key]; ?></div>
										</div>
									</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="ndx-Sidebar-container" style="display: none;">
				<div class="ndx-Sidebar">
					<div class="ndx-Panel">
						<div class="ndx-ContentCard" data-index="4">
							<?php include 'design-panel.php'; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="ndx-Variation-Gallery">
		<?php include 'variation-gallery.php'; ?>
	</div>
	<div class="ndx-Add-to-Cart-holder">
		<input class="edgtf-btn edgtf-btn-outline btn-cancel-design" type="button" value="<?php echo esc_html('Back to Product'); ?>">

		<div class="prod-Cart-info">
			<div class="prod-Design-costs"></div>
			<input class="edgtf-btn edgtf-btn-solid btn-get-price" type="button" value="<?php echo esc_html('Get Price'); ?>">
    	<button class="edgtf-btn edgtf-btn-solid btn-add-to-cart ajax_add_to_cart" data-product_id="<?php echo $product->get_id(); ?>" style="display: none;">
    		<?php esc_html_e('Add to Cart'); ?>
    	</button>
    </div>
	</div>

	<?php include 'popup.php'; ?>