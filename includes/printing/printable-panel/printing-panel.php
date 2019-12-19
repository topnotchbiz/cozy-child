<div class="ndx-ContentCard" data-index="0">
	<div class="ndx-DesignStyle-hello">
		<h3 class="ndx-DesignStyle-teaser">Choose your style...</h3>
		<div class="ndx-DesignStyle-buttons">
			<?php
			$printing_prices = $wc_printing_opts['prices'];
			$printing_types = array(
				'embroid' => 'Embroidered Stitching',
				'screen' => '1 Colour Screen Printing',
				'digital' => 'Digital Full Colour Printing',
			);

			foreach ( $printing_prices as $key => $value ) {
				if ( isset($printing_prices[$key]['size'] ) ) {
					?>

					<div class="ndx-DesignStyle-button" data-style="<?php echo $key; ?>">
						<div class="ndx-DesignStyle-buttonImage <?php echo $key; ?>"></div>
						<div class="ndx-DesignStyle-buttonText"><?php echo $printing_types[$key]; ?></div>
					</div>
					
				<?php } ?>
			<?php } ?>
		</div>
	</div>
</div>