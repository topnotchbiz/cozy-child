<div class="delivery-info">
	<div class="deli-row">
		<?php esc_html_e('Get it by'); ?>&nbsp;<span class="end-date"><?php esc_html_e( date( 'l jS M', strtotime( date( 'Y-m-d' ) . ' + 12 days') ) ); ?></span>
	</div>
	<div class="deli-row">
		<?php esc_html_e('Order within'); ?>&nbsp;<span class="remaining"></span>&nbsp;<?php esc_html_e('on'); ?>&nbsp;<strong class="end-day"><?php esc_html_e('Next Day'); ?></strong>&nbsp;<?php esc_html_e('delivery'); ?>
	</div>
	<a href="#"><?php esc_html_e('Delivery & Returns'); ?></a>
</div>