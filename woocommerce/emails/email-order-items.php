<?php
/**
 * Email Order Items
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-order-items.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;

$text_align = is_rtl() ? 'right' : 'left';

foreach ( $items as $item_id => $item ) :
	$product       = $item->get_product();
	$sku           = '';
	$purchase_note = '';
	$image         = '';

	if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
		continue;
	}

	if ( is_object( $product ) ) {
		$sku           = $product->get_sku();
		$purchase_note = $product->get_purchase_note();
		$image         = $product->get_image( $image_size );
	}

	?>
	<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
		<td style="text-align:center; vertical-align:middle; border: 1px solid #eee; width: 180px;">
			<?php
			$printing_logos = wc_get_order_item_meta($item_id, 'printing_logos');

			if ( isset( $printing_logos ) ) {
				$printing_types = array(
					'embroid' => 'Embroidered Stitching',
					'screen' => '1 Colour Screen Printing',
					'digital' => 'Digital Full Colour'
				);

				foreach ( $printing_logos as $pos => $logos ) {
					$types = array();
					$pos_str = '<strong>' . $pos . '</strong>: ';

					foreach ( $logos['img'] as $logo ) {
						$type = $logo['type'];

						if ( ! in_array( $type, $types ) ) {
							$types[] = $type;
							$pos_str .= $printing_types[$type] . ', ';
						}
					}

					$pos_str = rtrim( $pos_str, ', ' );
					?>

					<a href="<?php echo $logos['detail']; ?>" target="_blank" style="display: block;">
						<img width="180" class="logo-img" src="<?php echo $logos['detail']; ?>">
					</a>
					<span class="logo-position" style="display: block;"><?php echo $pos_str; ?></span>

				<?php } ?>

			<?php } ?>
		</td>
		<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">
		<?php

		// Show title/image etc.
		if ( $show_image ) {
			echo wp_kses_post( apply_filters( 'woocommerce_order_item_thumbnail', $image, $item ) );
		}

		$prod_id = $product->get_id();

		if ( $product->get_type() == 'variation' ) {
			$prod_id = $product->get_parent_id();
		}

		// Product name.
		echo '<a href="' . get_permalink( $prod_id ) . '" style="color: inherit;font: inherit;">' . wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) ) . '</a>';

		// SKU.
		if ( $show_sku && $sku ) {
			echo wp_kses_post( ' (#' . $sku . ')' );
		}

		// allow other plugins to add additional product information here.
		do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, $plain_text );

		wc_display_item_meta( $item, array(
			'label_before' => '<strong class="wc-item-meta-label" style="float: left; margin-right: .25em; clear: both">',
		) );

		// allow other plugins to add additional product information here.
		do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, $plain_text );

		?>
		</td>
		<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
			<?php echo wp_kses_post( apply_filters( 'woocommerce_email_order_item_quantity', $item->get_quantity(), $item ) ); ?>
		</td>
		<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
			<?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?>
		</td>
	</tr>
	<?php

	if ( $show_purchase_note && $purchase_note ) {
		?>
		<tr>
			<td colspan="3" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
				<?php
				echo wp_kses_post( wpautop( do_shortcode( $purchase_note ) ) );
				?>
			</td>
		</tr>
		<?php
	}
	?>

<?php endforeach; ?>
