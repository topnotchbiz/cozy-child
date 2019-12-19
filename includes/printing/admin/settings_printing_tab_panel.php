<div id="printing_tab_data" class="panel woocommerce_options_panel">
	<style>
		.position_image img{
			width: 80px;
		}

		td.remove{
			width: 16px!important;
			cursor: pointer;
			font-size: 20px
		}

		td.sort {
			width: 16px!important;
			padding: 9px;
			cursor: move;
			background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAAHUlEQVQYV2O8f//+fwY8gJGgAny6QXKETRgEVgAAXxAVsa5Xr3QAAAAASUVORK5CYII=) no-repeat center;
		}
	</style>
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<td colspan="10"><h4 style="margin:0;"><?php _e('Printing Positions', 'wc_printing'); ?></h4></td>                
			</tr>             
			<tr valign="top">
				<td colspan="10">
					<table class="widefat">
						<thead>
							<tr>
								<th class="sort" style="width: 20px;">&nbsp;</th>
								<th><?php _e('Position', 'wc_printing'); ?></th>
								<th><?php _e('Size', 'wc_printing'); ?></th>
								<th class="remove" style="width: 20px;">&nbsp;</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th colspan="9" style="text-align:right">
									<a href="#" class="button button-primary add_position" data-row="<?php
									ob_start();
									include( 'html-position-fields.php' );
									$html = ob_get_clean();
									echo esc_attr($html);
									?>"><?php _e('Add Position', 'woocommerce'); ?></a>                            
								</th>
							</tr>
						</tfoot>
						<tbody id="printing_position_rows">
							<?php
							if (isset($wc_printing_opts['positions'])) {
								$positions = $wc_printing_opts['positions'];
								$index = 0;
								if (!empty($positions['label']) && is_array($positions['label'])) {
									foreach ($positions['label'] as $position) {
										$printing_size = $positions['size'][$index];
										include( 'html-position-fields.php' );
										$index++;
									}
								}
							}
							?>
						</tbody>
					</table>
				</td>                
			</tr>
			<tr valign="top">
				<td colspan="10"><h4 style="margin:0;"><?php _e('Prices', 'wc_printing'); ?></h5></td>                
			</tr>
			<?php
			$types = array(
				'embroid' => 'Embroidery',
				'screen' => 'Screen Printing',
				'digital' => 'Digital Printing'
			);
			?>

			<?php foreach ( $types as $key => $value) { ?>
				<tr valign="top">
					<td colspan="10"><h5 style="margin:0;"><?php echo $value; ?></h5></td>                
				</tr>
				<tr valign="top">
					<td colspan="10">
						<table class="widefat">
							<thead>
								<tr>
									<th class="sort" style="width: 20px;">&nbsp;</th>
									<th><?php _e('Size', 'wc_printing'); ?></th>
									<th><?php _e('Qty( From )', 'wc_printing'); ?></th>
									<th><?php _e('Qty( To )', 'wc_printing'); ?></th>
									<th><?php _e('Setup Cost', 'wc_printing'); ?>( <?php echo get_woocommerce_currency_symbol(); ?> )</th>
									<th><?php _e('Position Cost', 'wc_printing'); ?>( <?php echo get_woocommerce_currency_symbol(); ?> )</th>
									<th class="remove" style="width: 20px;">&nbsp;</th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<th colspan="9" style="text-align:right">
										<?php
										$size = null;
										$qty_from = null;
										$qty_to = null;
										$setup_cost = null;
										$pos_cost = null;
										?>
										<a href="#" class="button button-primary add_position" data-row="<?php
										ob_start();
										include( 'html-price-fields.php' );
										$html = ob_get_clean();
										echo esc_attr($html);
										?>"><?php _e('Add Price', 'woocommerce'); ?></a>                            
									</th>
								</tr>
							</tfoot>
							<tbody class="printing_price_rows">
								<?php
								if ( $wc_printing_opts && array_key_exists('prices', $wc_printing_opts) && array_key_exists($key, $wc_printing_opts['prices']) ) {
									$prices = $wc_printing_opts['prices'][$key];
									$index = 0;
									if (!empty($prices['size']) && is_array($prices['size'])) {
										foreach ($prices['size'] as $price) {
											$size = $prices['size'][$index];
											$qty_from = $prices['qty_from'][$index];
											$qty_to = $prices['qty_to'][$index];
											$setup_cost = $prices['setup_cost'][$index];
											$pos_cost = $prices['pos_cost'][$index];
											include( 'html-price-fields.php' );
											$index++;
										}
									}
								}
								?>
							</tbody>
						</table>
					</td>                
				</tr>
			<?php } ?>
		</tbody>
	</table>
</div>