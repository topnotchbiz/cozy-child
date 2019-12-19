<?php
$cozy_edge_variable_sidebar = cozy_edge_get_sidebar();

if( is_product_category() ) {
	$cozy_edge_variable_sidebar = get_post_meta( wc_get_page_id( 'shop' ), 'edgtf_custom_sidebar_meta', true );
} elseif ( is_tax( 'yith_product_brand' ) ) {
	$cozy_edge_variable_sidebar = get_post_meta( wc_get_page_id( 'shop' ), 'edgtf_custom_sidebar_meta', true );
}
?>
<div class="edgtf-column-inner">
	<aside class="edgtf-sidebar">
		<?php
			if (is_active_sidebar($cozy_edge_variable_sidebar)) {
				dynamic_sidebar($cozy_edge_variable_sidebar);
			}
		?>
	</aside>
</div>
