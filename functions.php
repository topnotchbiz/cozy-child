<?php

/*** Child Theme Function  ***/

function cozy_edge_child_enqueue_style() {

	$parent_style = 'cozy_edge_default_style';

	wp_enqueue_style('cozy_edge_child_style', get_stylesheet_directory_uri() . '/style.css', array($parent_style));   
	wp_enqueue_style('magnific-popup', get_stylesheet_directory_uri() . '/assets/css/magnific-popup.css', array($parent_style));   
	wp_enqueue_script('cozy_edge_child_script', get_stylesheet_directory_uri() . '/assets/js/custom.js', array('jquery'), null, true);
	wp_enqueue_script('magnific-popup', get_stylesheet_directory_uri() . '/assets/js/jquery.magnific-popup.min.js', array('jquery'), null, true);
}
add_action( 'wp_enqueue_scripts', 'cozy_edge_child_enqueue_style', 11 );

function woocommerce_before_single_product_summary_hooks() {
	// remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);
	add_action( 'woocommerce_before_single_product_summary', 'woocommerce_template_single_excerpt', 21 );
}
add_action( 'woocommerce_before_single_product_summary', 'woocommerce_before_single_product_summary_hooks', 2);

function woocommerce_single_product_summary_hooks() {
	// remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
}
add_action( 'woocommerce_single_product_summary', 'woocommerce_single_product_summary_hooks', 2 );

function woocommerce_after_add_to_cart_button_hooks() {
	wc_get_template( 'single-product/delivery-info.php' );
}
add_action( 'woocommerce_after_add_to_cart_form', 'woocommerce_after_add_to_cart_button_hooks' );

function woocommerce_template_single_product_sku() {
	wc_get_template( 'single-product/product-sku.php' );
}

function change_admin_email_subject( $subject, $order ) {
	global $woocommerce;

	$subject = str_replace( '{customer_name}', $order->get_formatted_billing_full_name(), $subject );

	return $subject;
}
add_filter('woocommerce_email_subject_new_order', 'change_admin_email_subject', 1, 2);

function customize_producdt_price( $return, $price ) {
	return $return . ' inc GST';
}
add_filter( 'wc_price', 'customize_producdt_price', 99, 2 );

function add_gst_tax_to_email_total( $total_rows, $order, $tax_display ) {
	$new = array();
  
  foreach ($total_rows as $k => $row) {
    if ( $k === 'order_total' ) {
      $new['tax'] = array(
      	'label' => __( 'GST 10%:', 'cozy' ),
				'value' => str_replace( 'inc GST', '', wc_price( $order->get_total( '' ) / 10 )),
      );
    }

    $new[$k] = $row;
  }	

	return $new;
}
add_filter( 'woocommerce_get_order_item_totals', 'add_gst_tax_to_email_total', 99, 3 );

function add_gst_tax_to_invoice_total( $total_rows, $invoice ) {
	$new = array();
  
  foreach ($total_rows as $k => $row) {
    if ( $k === 'order_total' ) {
      $new['tax'] = array(
      	'label' => __( 'GST 10%:', 'cozy' ),
				'value' => str_replace( 'inc GST', '', wc_price( $invoice->order->get_total( '' ) / 10 )),
      );
    }

    $new[$k] = $row;
  }	

	return $new;
}
add_filter( 'wpi_get_invoice_total_rows', 'add_gst_tax_to_invoice_total', 99, 2 );

include_once get_stylesheet_directory() . '/includes/printing/printing.php';
include_once get_stylesheet_directory() . '/includes/widgets/brand-widget.php';