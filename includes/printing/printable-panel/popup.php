<div id="added-to-cart-popup" class="ndx-Popup" style="display: none;">
	<div class="prod-Design">
		<div class="prod-Design-content">
			<div class="prod-Msg">
	  		<?php esc_html_e('Success! You\'ve added this item to your bag.'); ?>
	  	</div>
			<div class="prod-Item clearfix">
				<div class="prod-Item-canvasContainer"></div>
				<div class="prod-Item-details">
					<?php the_title( '<h4 class="prod-ItemName">', '</h4>' ); ?>
					<div class="prod-Item-details-content">
						<div class="prod-Item-details-label"><?php esc_html_e('Brand'); ?></div>
						<div class="prod-Item-details-item">
							<?php echo get_the_term_list( $product->get_id(), 'yith_product_brand', '', ',', '' ); ?>
						</div>
						<div class="prod-Item-details-label"><?php esc_html_e('Decoration'); ?></div>
						<div class="prod-Item-details-decos"></div>
						<div class="prod-Item-details-label"><?php esc_html_e('Quantities'); ?></div>
						<div class="prod-Item-details-items"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="prod-Item-buttons">
			<a href="<?php echo wc_get_checkout_url(); ?>" class="edgtf-btn edgtf-btn-solid edgtf-btn-small"><?php esc_html_e('Checkout'); ?></a>
			<a href="javascript:;" class="edgtf-btn edgtf-btn-outline edgtf-btn-small edgtf-btn-close"><?php esc_html_e('Continue'); ?></a>
		</div>
	</div>
</div>
