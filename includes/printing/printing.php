<?php
/**
 * WooCommerce Printing Addon
 * Description: add custom logo and its position.
*/

load_plugin_textdomain( 'wc_printing', false, dirname( plugin_basename( __FILE__ ) ) . '/' );

define('PRINTING_ROOT', get_stylesheet_directory_uri() . '/includes/printing/');
define('PRINTING_ROOT_DIR', dirname(__FILE__) . '/');

class WoocommercePrintingLogo {
	private static $upload_dir;
	private static $upload_dir_url;
	public $wc_printing_opts = null;

	function __construct() {
		self::$upload_dir = WP_CONTENT_DIR . '/printing_logos';

		if(!is_dir(self::$upload_dir)){
			mkdir(self::$upload_dir);
		}

		self::$upload_dir_url = WP_CONTENT_URL . '/printing_logos';


		add_action( 'init', array( &$this, 'init') );

		if ( ! wp_next_scheduled ( 'clear_logos_event' ) ) {
			wp_schedule_event( time(), 'monthly', 'clear_logos_event' );
		}

		add_action( 'clear_logos_event', array( &$this, 'clear_unused_logos' ) );		
	}

	public function init() {
		add_action( 'woocommerce_checkout_create_order_line_item', array( &$this, 'add_order_item_meta'), 10, 4 );

		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ) );
		add_filter( 'woocommerce_product_data_tabs', array( &$this, 'printing_tab' ), 99 );
		add_action( 'woocommerce_product_data_panels', array( &$this, 'printing_tab_panel' ) );
		
		add_action( 'woocommerce_product_after_variable_attributes', array( &$this, 'gallery_admin_html' ), 11, 3 );
		add_action( 'woocommerce_save_product_variation', array( &$this, 'save_variation_gallery' ), 10, 2 );
		// add_action( 'admin_footer', array( $this, 'admin_template_js' ) );

		add_action( 'save_post', array( &$this, 'save_product_printing_data' ), 11, 2 );

		add_filter( 'woocommerce_available_variation', array( &$this, 'custom_available_variation_hook'), 11, 3 );

		add_filter( 'manage_shop_order_posts_columns', array( &$this, 'set_custom_edit_post_columns' ), 99, 1 );
		add_action( 'manage_shop_order_posts_custom_column' , array( &$this, 'custom_cpost_column' ), 99, 2 );
		add_filter( 'manage_edit-pa_color_columns', array( &$this, 'add_pa_color_swatch_columns' ), 99, 1 );
		add_filter( 'manage_pa_color_custom_column', array( &$this, 'add_swatch_column_content' ), 10, 3 );

		add_action( 'woocommerce_single_product_summary', array( &$this, 'printing_details' ), 12 );
		add_action( 'woocommerce_single_product_summary', array( &$this, 'woocommerce_template_single_customisations' ), 7 );

		add_action( 'woocommerce_after_single_product', array( &$this, 'product_design_holder' ), 19 );
		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );

		add_action( 'wp_ajax_add_logo', array( &$this, 'upload_logo' ) );
		add_action( 'wp_ajax_nopriv_add_logo', array( &$this, 'upload_logo' ) );
		add_action( 'wp_ajax_upload_print', array( &$this, 'upload_print' ) );
		add_action( 'wp_ajax_nopriv_upload_print', array( &$this, 'upload_print' ) );

		add_action( 'wp_ajax_prints_to_cart', array( &$this, 'prints_to_cart' ) );
		add_action( 'wp_ajax_nopriv_prints_to_cart', array( &$this, 'prints_to_cart' ) );

		add_filter( 'woocommerce_add_cart_item_data', array( &$this, 'add_cart_item_data' ), 10, 1 );
		add_filter( 'woocommerce_add_cart_item', array( &$this, 'add_cart_item' ), 10, 1 );
		add_filter( 'woocommerce_get_cart_item_from_session', array( &$this, 'get_cart_item_from_session' ), 10, 2 );
		add_filter( 'woocommerce_product_variation_title_include_attributes', '__return_false' );
		add_filter( 'woocommerce_get_item_data', array( &$this, 'add_sku_brand_item_data' ), 11, 2 );

		add_action('woocommerce_before_order_itemmeta', array( &$this, 'add_brands_to_order_item' ), 10, 3);
		add_action('woocommerce_after_order_itemmeta', array( &$this, 'add_printings_to_order_item' ), 10, 3);

		add_action( 'woocommerce_order_item_meta_start', array( &$this, 'add_brand_to_order_item_meta' ), 11, 3 );

		add_filter( 'wpi_get_invoice_columns', array( &$this, 'add_deco_label_to_invoice_table' ), 99, 2 );
		add_filter( 'wpi_get_invoice_columns_data_row', array( &$this, 'add_decoration_to_invoice_table' ), 99, 4 );
	}

	public function clear_unused_logos() {
		global $wpdb;

		$results = $wpdb->get_results("SELECT meta_value FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE meta_key LIKE 'printing_logos'", ARRAY_A);

		$using_printings = array();

		foreach ( $results as $result ) {
			$meta_value = $result['meta_value'];

			$printing_logos = maybe_unserialize($meta_value);

			foreach ( $printing_logos as $pos => $logos ) {
				$using_printings[] = str_replace( content_url(), WP_CONTENT_DIR, $logos['detail'] );

				foreach ( $logos['img'] as $img) {
					$using_printings[] = str_replace( content_url(), WP_CONTENT_DIR, $img['url'] );
				}
			}  
		}

		$results = $wpdb->get_results("SELECT session_value FROM {$wpdb->prefix}woocommerce_sessions WHERE session_value LIKE '%" . content_url() . "/printing_logos/%';", ARRAY_A);

		foreach ( $results as $result ) {
			$meta_value = $result['session_value'];

			$session_data = maybe_unserialize($meta_value);
			$cart_items = maybe_unserialize( $session_data['cart'] );

			foreach ( $cart_items as $key => $cart_item ) {
				$printing_logos = $cart_item['printing_logos'];

				foreach ( $printing_logos as $pos => $logos ) {
					$using_printings[] = str_replace( content_url(), WP_CONTENT_DIR, $logos['detail'] );

					foreach ( $logos['img'] as $img) {
						$using_printings[] = str_replace( content_url(), WP_CONTENT_DIR, $img['url'] );
					}
				}
			}
		}

		error_log(json_encode($using_printings));

		$expire_date = strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-10 days" );

		$results = array();
		$total_array = array();

		$this->scan_dir_for_clear( WP_CONTENT_DIR . '/printing_logos', $using_printings, $expire_date );
	}

	private function scan_dir_for_clear( $dir, $using_printings, $expire_date ) {
		$files = scandir( $dir );

		foreach( $files as $value ) {
			$path = realpath( $dir . DIRECTORY_SEPARATOR . $value );
      
      if ( !is_dir( $path ) ) {
        if ( in_array( $path, $using_printings ) == false ) {
					$modified_date = filemtime( $path );
					
					if ( $modified_date < $expire_date ) {
						unlink( $path );
					}
				}
      } elseif ( $value != "." && $value != ".." ) {
        $this->scan_dir_for_clear( $path, $using_printings, $expire_date );
      }
		}
	}

	public function printing_tab( $tabs ) {
		$tabs['printing'] = array(
			'label' => __('Printing', 'woocommerce'),
			'target' => 'printing_tab_data',
			'class' => array( 'show_if_variable' ),
		);

		$tabs['design_area'] = array(
			'label' => __('Design Area', 'woocommerce'),
			'target' => 'design_area_tab_data',
		);

		return $tabs;
	}

	public function printing_tab_panel(){
		global $post;

		$wc_printing_opts = get_post_meta($post->ID, 'wc_printing_opts', true);
		$wc_design_area = get_post_meta($post->ID, 'wc_design_area', true);
		$wc_design_bounding = get_post_meta($post->ID, 'wc_design_bounding', true);

		include_once PRINTING_ROOT_DIR . 'admin/settings_printing_tab_panel.php';
		include_once PRINTING_ROOT_DIR . 'admin/settings_design_area_tab_panel.php';
	}

	public function save_product_printing_data($post_id, $post){

		if ($post->post_type == 'product') {
			$wc_printing_opts = array();

			if ( isset($_POST['positions']) ) {
				$wc_printing_opts['positions'] = $_POST['positions'];
			}

			if ( isset($_POST['prices']) ) {
				$wc_printing_opts['prices'] = $_POST['prices'];
			}

			if ( !empty($wc_printing_opts) ) {
				update_post_meta($post_id, 'wc_printing_opts', $wc_printing_opts);
			}

			if ( isset($_POST['wc_design_area']) && !empty($_POST['wc_design_area']) ) {
				update_post_meta($post_id, 'wc_design_area', $_POST['wc_design_area']);
				update_post_meta($post_id, 'wc_design_bounding', $_POST['wc_design_bounding']);
			}
		}
	}

	public function custom_available_variation_hook( $array, $product, $variation ) {
		$pos_images = get_post_meta( $variation->get_id(), 'wc_printing_images', true );

		if ( $pos_images ) {
			$array['position_images'] = $pos_images;
		}

		return $array;
	}

	public function gallery_admin_html( $loop, $variation_data, $variation ) {
		global $post;

		$wc_printing_opts = get_post_meta($post->ID, 'wc_printing_opts', true);

		if( isset( $wc_printing_opts['positions'] ) && $wc_printing_opts['positions'] ) {
			$labels = $wc_printing_opts['positions']['label'];
			$variation_id   = absint( $variation->ID );
			$gallery_images = get_post_meta( $variation_id, 'wc_printing_images', true );
			?>

			<div class="form-row form-row-full wc-printing-images-wrapper">
				<table class="wc-printing-images">
					<tbody>
						<tr>
							<?php foreach ( $labels as $label ) { ?>
								<td>
									<input type="hidden" 
										name="wc_printing_images[<?php echo $variation_id ?>][<?php echo $label; ?>]" 
										value="<?php echo $gallery_images && isset($gallery_images[$label]) ? $gallery_images[$label] : ''; ?>">
									<div class="image-wrapper">
										<div class="image">
											<img src="<?php echo $gallery_images && isset($gallery_images[$label]) ? wp_get_attachment_image_src( $gallery_images[$label] )[0] : wc_placeholder_img_src('thumbnail'); ?>">
										</div>
										<div class="label"><?php echo $label; ?></div>
										<a href="#" class="delete remove-position-image">
											<span class="dashicons dashicons-dismiss"></span>
										</a>
									</div>
								</td>
							<?php } ?>
						</tr>
					</tbody>
				</table>
			</div>

			<?php
		}
	}

	public function save_variation_gallery( $variation_id, $i ) {
		if ( isset( $_POST[ 'wc_printing_images' ] ) && isset( $_POST[ 'wc_printing_images' ][ $variation_id ] ) ) {
			update_post_meta( $variation_id, 'wc_printing_images', $_POST[ 'wc_printing_images' ][ $variation_id ] );
		} else {
			delete_post_meta( $variation_id, 'wc_printing_images' );
		}
	}

	public function admin_enqueue_scripts(){
		wp_enqueue_style( 'admin-printing',  PRINTING_ROOT . 'assets/css/admin.css' );
		
		$screen = get_current_screen();
		if( $screen->id == 'product' ){
			wp_enqueue_script( 'Jcrop',  PRINTING_ROOT . 'assets/js/jquery.Jcrop.js', array( 'jquery', 'jquery-ui-sortable' ) );
			wp_enqueue_script( 'logo-pricing',  PRINTING_ROOT . 'assets/js/admin.js', array( 'Jcrop' ) );
		}

		if( $screen->id == 'shop_order' ){
			wp_enqueue_script( 'printing_order_js',  PRINTING_ROOT . 'assets/js/admin_order.js', array( 'jquery' ) );
		}
	}

	public function enqueue_scripts(){
		if ( $this->is_printable() ) {
			wp_enqueue_script('jquery-ui');
			wp_enqueue_script('jquery-ui-mouse');
			wp_enqueue_script('jquery-ui-sortable');
			wp_enqueue_script('jquery-ui-draggable');
			wp_enqueue_script('jquery-ui-resizable');
			wp_enqueue_script('jquery-rotatable', PRINTING_ROOT . 'assets/js/jquery.ui.rotatable.js', null, '1.0', TRUE);
			wp_enqueue_script('jquery-ui-slider');
			wp_enqueue_script('dropzone', PRINTING_ROOT . 'assets/js/dropzone.js');
			wp_enqueue_script('printing-addon', PRINTING_ROOT . 'assets/js/add-ons.js', 'jquery-ui', null, true);
			wp_enqueue_script('canvg', PRINTING_ROOT . 'assets/js/canvg.js');
			wp_enqueue_script('text-design', PRINTING_ROOT . 'assets/js/text-design.js', 'printing-addon', null, true);
			wp_enqueue_script('customize', PRINTING_ROOT . 'assets/js/customize.js');
			wp_enqueue_script('spectrum', PRINTING_ROOT . 'assets/js/spectrum.js');
			wp_enqueue_style('jquery-ui');
			wp_enqueue_style('spectrum', PRINTING_ROOT . 'assets/css/spectrum.css');
			wp_enqueue_style('spectrum', PRINTING_ROOT . 'assets/css/spectrum.css');
			wp_enqueue_style('text-design', PRINTING_ROOT . 'assets/css/text-design.css');
			wp_enqueue_style('add-logo', PRINTING_ROOT . 'assets/css/custom.css');
		}
	}

	public function upload_logo(){
		if(isset($_FILES)){
			$target_dir = self::$upload_dir . '/' . date("Y");

			if(!is_dir($target_dir)){
				mkdir($target_dir);
			}

			$target_dir = self::$upload_dir . '/' . date("Y") . '/' . date("m") . '/';

			if(!is_dir($target_dir)){
				mkdir($target_dir);
			}
			
			$target_dir_url = self::$upload_dir_url . '/' . date("Y") . '/' . date("m") . '/';

			$file_name = preg_replace( '`[^a-z0-9-_.]`i', '', basename($_FILES["file"]["name"]) );

			$path_parts = pathinfo( $target_dir . $file_name );
			$file_type = $path_parts['extension'];
			$name_of_file = $path_parts['filename']; // filename without extension
			
			if( in_array( strtolower( $file_type ), array('jpg', 'jpeg', 'png', 'bmp', 'gif', 'pdf' ) ) ) {
				$file_counter = 1; 

				// loop until an available filename is found 
				while ( file_exists( $target_dir . $file_name ) ) {
					 $file_name = $name_of_file . '_' . $file_counter++ . '.' . $file_type; 
				}

				if ( move_uploaded_file( $_FILES["file"]["tmp_name"], $target_dir . $file_name ) ) {
					echo json_encode(
						array('code' => 'OK', 'url' => $target_dir_url . $file_name, 'name' => $file_name, 'file_type' => strtolower($file_type))
					);
				} else {
					echo json_encode(
						array('code'=>'BAD', 'msg' => __('there was an error uploading your file.', 'wc_printing_logo'))
					);
				}
			}else{
				echo json_encode(
					array('code'=>'BAD', 'msg' => __('Not Allowed File Type.', 'wc_printing_logo'))
				);
			}
		}
		exit;
	}

	public function upload_print() {
		if(isset($_POST['dataUrl'])) {
			$target_dir = self::$upload_dir . '/' . date("Y");

			if(!is_dir($target_dir)){
				mkdir($target_dir);
			}

			$target_dir = self::$upload_dir . '/' . date("Y") . '/' . date("m") . '/';

			if(!is_dir($target_dir)){
				mkdir($target_dir);
			}
			
			$target_dir_url = self::$upload_dir_url . '/' . date("Y") . '/' . date("m") . '/';
			$file_name = preg_replace( '`[^a-z0-9-_.]`i', '-', $_POST['name'] );

			$path_parts = pathinfo( $target_dir . $file_name );
			$file_type = $path_parts['extension'];
			$name_of_file = $path_parts['filename'];
			
			$file_counter = 1; 

			while ( file_exists( $target_dir . $file_name ) ) {
				 $file_name = $name_of_file . '_' . $file_counter++ . '.' . $file_type;
			}

			$ifp = fopen( $target_dir . $file_name, 'wb' ); 
			$data = explode( ',', $_POST['dataUrl'] );
			fwrite( $ifp, base64_decode( $data[ 1 ] ) );
			fclose( $ifp );

			$return = array( 'url' => $target_dir_url . $file_name, 'pos' => $_POST['pos'] );

			if ( isset($_POST['type']) ) {
				$return['type'] = $_POST['type'];
			}

			die( json_encode( $return ) );
		} else {
			header('HTTP/1.1 500 Internal Server');
			die( json_encode(array('message' => 'ERROR')) );
		}
	}

	public function prints_to_cart() {

		$product_id = $_POST['product_id'];	
		
		$product_status = get_post_status($product_id);

		if ( 'publish' !== $product_status ) {
			$data = array(
				'error' => true,
				'message' => 'Product is not published yet.'
			);

			echo wp_send_json($data);
		}

		$items = $_POST['order_info'];
		$uploads = $_POST['uploads'];
		
		$adding_to_cart = wc_get_product( $product_id );
		$attributes = $adding_to_cart->get_attributes();
		$variation = wc_get_product( $variation_id );

		$all_added_to_cart = false;

		$added_count = 0;
		$failed_count = 0;

		$success_message = '';
		$error_message = '';

		foreach ( $items as $item ) {
			$qty = $item['qty'];

			if ( $qty) {
				$variation_id = $item['variation_id'];
				$all_variations_set = true;

				$variations = array();

				if ( empty( $variation_id ) ) {
					$failed_count++;
					continue;
				}

				// Verify all attributes
				foreach ( $attributes as $attribute ) {
					if ( !$attribute['is_variation'] ) {
						continue;
					}

					$taxonomy = 'attribute_' . sanitize_title( $attribute['name'] );

					if ( isset( $item[$taxonomy] ) ) {

						// Get value from post data
						// Don't use wc_clean as it destroys sanitized characters
						$value = sanitize_title( trim( stripslashes( $item[$taxonomy] ) ) );

						// Get valid value from variation
						$valid_value = $variation->variation_data[$taxonomy];

						// Allow if valid
						if ( $valid_value == '' || $valid_value == $value ) {
							if ( $attribute['is_taxonomy'] ) {
								$variations[$taxonomy] = $value;
							} else {
								// For custom attributes, get the name from the slug
								$options = array_map( 'trim', explode( WC_DELIMITER, $attribute['value'] ) );
								foreach ( $options as $option ) {
									if ( sanitize_title( $option ) == $value ) {
										$value = $option;
										break;
									}
								}
								$variations[$taxonomy] = $value;
							}
							continue;
						}
					}

					$all_variations_set = false;
				}

				if ( $all_variations_set ) {
					// Add to cart validation
					$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $qty, $variation_id, $variations );

					if ( $passed_validation ) {
						$added = WC()->cart->add_to_cart( $product_id, $qty, $variation_id, $variations );
						$all_added_to_cart &= $added;
					}
				} else {
					$failed_count++;
					continue;
				}
				
				$all_added_to_cart &= $added;

				if ( $added ) {
					$added_count++;
				} else {
					$failed_count++;
				}
			}
		}

		if ( $failed_count ) {
			$data = array(
				'error' => true,
				'product_id' => $product_id,
				'variation_id' => $variation_id,
				'msg' => sprintf( __( 'Unable to add %s to the cart.', 'wc_printing_opts' ), $failed_count )
			);

			wp_send_json($data);
		}

		if ( !$added_count && !$failed_count ) {
			$data = array(
				'error' => true,
				'product_id' => $product_id,
				'variation_id' => $variation_id,
				'msg' => sprintf( __( 'No product quantities entered.', 'wc_printing_opts' ), $failed_count )
			);

			wp_send_json($data);
		}

		WC_AJAX :: get_refreshed_fragments();

		wp_die();
	}

	public function add_cart_item_data( $cart_item_data ){

		if ( isset( $_POST['action'] ) && $_POST['action'] == 'prints_to_cart' ) {
			$cart_item_data['printing_logos'] = $_POST['uploads'];
			$cart_item_data['printing_comment'] = $_POST['comment'];
		}

		return $cart_item_data;
	}

	public function get_cart_item_from_session( $cart_item, $values ) {
		if ( isset( $cart_item['printing_logos'] ) ) {
			$this->add_cart_item($cart_item);
		}

		return $cart_item;
	}

	public function add_sku_brand_item_data( $item_data, $cart_item ) {
		$new_data = array();

		$product = $cart_item['data'];

		$sku = $product->get_sku();

		$new_data[] = array(
			'key' => 'SKU',
			'value' => $sku
		);

		$terms = get_the_terms( $cart_item['product_id'], 'yith_product_brand' );

		$termarr = array();

		if ( is_array( $terms ) ) {
			foreach ( $terms as $term ) {
				$termarr[] = '<a href="' . get_term_link( $term ) . '">' . $term->name . '</a>';
			}
		}
		
		$new_data[] = array(
			'key' => 'Brand',
			'value' => implode(', ', $termarr)
		);

		return array_merge( $new_data, $item_data );
	}

	public function add_cart_item( $cart_item ) {
		if ( isset( $cart_item['printing_logos'] ) ) {
			$product_id = $cart_item['product_id'];

			$wc_printing_opts = get_post_meta($product_id, 'wc_printing_opts', true);

			$cart = WC()->session->get( 'cart', null );

			$total_qty = 0;

			if ( is_array( $cart ) ) {
				foreach ( $cart as $key => $values ) {
					if ( $values['product_id'] == $product_id ) {
						$total_qty += $values['quantity'];
					}
				}
			}

			$adjust_price = 0;

			foreach ( $cart_item['printing_logos'] as $pos => $logos ) {
				$types = array();

				foreach ( $logos['img'] as $logo ) {
					$type = $logo['type'];

					if ( ! in_array( $type, $types ) ) {
						$types[] = $type;
						$ind = array_search( $pos, $wc_printing_opts['positions']['label'] );
						$size = $wc_printing_opts['positions']['size'][$ind];

						$this->wc_printing_opts = $wc_printing_opts;
						$logo_price = $this->get_logo_price( $size, $total_qty, $type );

						$adjust_price += $logo_price[0] + $logo_price[1];
					}
				}
			}

			$cart_item['data']->set_price( $cart_item['data']->get_price() + $adjust_price );
		}

		return $cart_item;
	}
	
	public function add_order_item_meta( $item, $cart_item_key, $values, $order ) {
		if ( empty( $values['printing_logos'] ) ) {
			return;
		}

		$item->add_meta_data( 'printing_logos', $values['printing_logos'] );
		$item->add_meta_data( 'Comment', $values['printing_comment'] );
	}

	public function add_brands_to_order_item( $item_id, $item, $_product ) {
		if ( ! $_product ) return;

		$terms = get_the_terms( $_product->get_parent_id(), 'yith_product_brand' );

		$termarr = array();

		if ( is_array( $terms ) ) {
			foreach ( $terms as $term ) {
				$termarr[] = $term->name;
			}
		}
		?>

		<div class="view">
			<table class="display_meta" cellspacing="0">
				<tbody>
					<tr>
						<th>Brand:</th>
						<td><p><?php echo implode(', ', $termarr); ?></p></td>
					</tr>
				</tbody>
			</table>
		</div>

		<?php
	}

	public function add_printings_to_order_item( $item_id, $item, $_product ) {
		$printing_logos = wc_get_order_item_meta($item_id, 'printing_logos');

		if ( isset( $printing_logos ) && $printing_logos ) {
			$wc_printing_opts = get_post_meta($_product->get_id(), 'wc_printing_opts', true);

			$printing_types = array(
				'embroid' => 'Embroidered Stitching',
				'screen' => '1 Colour Screen Printing',
				'digital' => 'Digital Full Colour'
			);
			?>

			<table class="printing-items" style="margin-bottom: 10px;">
				<tbody>
					<?php
					foreach ( $printing_logos as $pos => $logos ) {
						$types = array();
						$pos_str = '<strong>' . $pos . '</strong>: ';
						$printings = array();

						foreach ( $logos['img'] as $logo ) {
							$type = $logo['type'];

							if ( ! in_array( $type, $types ) ) {
								$types[] = $type;
								$pos_str .= $printing_types[$type] . ', ';
							}

							$printings[] = $logo['url'];
						}

						$pos_str = rtrim( $pos_str, ', ' );
						?>

						<tr>
							<th colspan="2" style="font-size: 15px;color: #000;background: transparent;border: 0;font-weight: 700;padding: 0;">
								<?php echo $pos; ?>
							</th>
						</tr>
						<tr>
							<td class="printing-item-detail" style="padding: 3px;border: 0;text-align: center;">
								<a href="<?php echo $logos['detail']; ?>" download="<?php echo basename( $logos['detail'] ); ?>" target="_blank">
									<img src="<?php echo $logos['detail']; ?>" style="width: 180px;display: block;">
								</a>
								<div class="printing-position"><?php echo $pos_str; ?></div>
							</td>
							<td class="printing-item-print" style="padding: 3px;border: 0;">
								<?php foreach ( $printings as $key => $printing ) : ?>
									<a href="<?php echo $printing; ?>" target="_blank" download="<?php echo basename( $printing ); ?>" style="display: block;margin-bottom: 3px;">
										<img src="<?php echo $printing; ?>" style="width: 80px;height: auto;display: block;" />
									</a>
								<?php endforeach; ?>
							</td>
						</tr>							
						
					<?php } ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="2" style="padding: 0;border: 0;text-align: center;">
							<a class="button button-primary btn-download-prints" href="javascript:;" style="margin-top: 10px;">
								<?php echo esc_html( 'DOWNLOAD IMAGES' ); ?>
							</a>
						</td>
					</tr>
				</tfoot>
			</table>
			<?php
		}		
	}

	public function add_brand_to_order_item_meta( $item_id, $item, $order ) {
		if ( $item->get_type() == 'line_item' ) {
			$product = $item->get_product_id();
		} else {
			$product = $item->get_parent_id();
		}
		
		$sku = wc_get_product( $product )->get_sku();

		$terms = get_the_terms( $product, 'yith_product_brand' );

		$termarr = array();

		if ( is_array( $terms ) ) {
			foreach ( $terms as $term ) {
				$termarr[] = '<a href="' . get_term_link( $term ) . '">' . $term->name . '</a>';
			}
		}
		
		echo '<ul class="wc-item-meta">';
		echo '<li><strong class="wc-item-meta-label" style="float: left; margin-right: .25em; clear: both">SKU:</strong> ' . $sku . '</li>';
		echo '<li><strong class="wc-item-meta-label" style="float: left; margin-right: .25em; clear: both">Brand:</strong> ' . implode(', ', $termarr) . '</li>';
		echo '</ul>';
	}

	public function add_deco_label_to_invoice_table( $columns, $invoice ) {
		return array_merge( array('deco'=> ''), $columns );
	}

	public function add_decoration_to_invoice_table( $row, $item_id, $item, $invoice ) {
		$printing_logos = wc_get_order_item_meta( $item->get_id(), 'printing_logos' );

		ob_start();

		if ( $printing_logos ) {
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

				<a href="<?php echo $logos['detail']; ?>" target="_blank" style="width:100px;">
					<img src="<?php echo $logos['detail']; ?>" style="width:100px;border:1px solid #ddd;">
					<div class="logo-position"><?php echo $pos_str; ?></div>
				</a>
				<br/>

				<?php
			}
		} else {
			echo wc_get_product($item->get_variation_id())->get_image();
		}
		
		$deco = ob_get_contents();
		ob_end_clean();

		return array_merge( array( 'deco' => $deco ), $row );
	}

	public function set_custom_edit_post_columns($columns) {
		$index = 0;

		foreach ( $columns as $key => $column) {
			$index++;

			if ( $key == 'order_total' ) {
				break;
			}
		}

		$columns = array_slice( $columns, 0, $index, true ) + 
							array( 'wc_printing_logos_actions' => '' ) + 
							array_slice( $columns, $index, count($columns) - $index, true );

		return $columns;
	}
	
	public function custom_cpost_column( $column, $post_id ) {
		if ( $column == 'wc_printing_logos_actions' ) {
			$has_wcpl = false;
			$has_blank = false;

			$order = wc_get_order( $post_id );
			$items = $order->get_items();

			foreach ( $items as $key => $item ) {
				$printing_logos = wc_get_order_item_meta( $item->get_id(), 'printing_logos' );

				if ( isset($printing_logos) && $printing_logos ) {
					$has_wcpl = true;
				} else {
					$has_blank = true;
				}
			}

			if ( $has_wcpl ) {
				echo '<span style="display: inline-block;background: rgb(15,249,222);color: #fff;padding: 7px;line-height: 15px;margin-bottom: 5px;float: right;">Customisation</span>';
			}

			if ( $has_blank ) {
				echo '<span style="display: inline-block;background: rgb(151,151,151);color: #fff;padding: 7px;line-height: 15px;float: right;">Blank Items</span>';
			}
		}
	}

	public function add_pa_color_swatch_columns( $columns ) {
		$columns['swatch'] = 'Swatch';
		return $columns;
	}

	public function add_swatch_column_content( $content, $column_name, $term_id ) {
		if ( 'swatch' == $column_name ) {
			$colour_term = get_term_by( 'id', $term_id, 'pa_color' );

			$colours = array();

			while ( get_field( 'colour_' . ( count( $colours ) + 1 ), $colour_term ) ) {
				$colours[] = get_field( 'colour_' . ( count( $colours ) + 1 ), $colour_term );
			}

			$cnt = count ( $colours );
			$swatch_html = '';

			foreach ( $colours as $key => $colour ) {
				$swatch_html .= '<span style="background-color: ' . $colour . '; width: ' . ( 100 / $cnt ) . '%;"></span>';
			}

			$content .= '<span class="colour_holder"><span class="colours_' . $cnt . '">';
			$content .= $swatch_html;
			$content .='</span></span>';
		}

		return $content;
	}

	public function get_logo_price( $size, $qty, $printing_type = 'embroid' ) {
		global $product;

		if ( $product ) {
			$this->wc_printing_opts = get_post_meta( $product->get_id(), 'wc_printing_opts', true );
		}

		if ( $this->wc_printing_opts ) {
			$logo_pricing = $this->wc_printing_opts['prices'][$printing_type];

			$pos_cost = 0;
			$setup_cost = 0;

			if ( is_array($logo_pricing['size']) && count($logo_pricing['size']) ){
				$applied_min = $applied_max = $price_index = 0;

				for ( $i = 0; $i < count($logo_pricing['size']); $i++ ) {
					$from =$logo_pricing['qty_from'][$i];
					$to = $logo_pricing['qty_to'][$i];

					if ( ($size == $logo_pricing['size'][$i]) && ($qty >= $from) && ($to === '' || $to >= $qty) ) {
						$applied_min = $from;
						$applied_max = $to;
						$price_index = $i;

						break;
					}
				}

				$pos_cost = floatval($logo_pricing['pos_cost'][$price_index]);
				$setup_cost = floatval($logo_pricing['setup_cost'][$price_index]);
				$qty_from = intval($logo_pricing['qty_from'][$price_index]);

				if(!isset($qty_from) || $qty_from==0) {
					error_log($product->get_id() . " has error zero division");
					error_log("from index 0 used on line 698 in printing.php");
					$qty_from = $logo_pricing['from'][0];
				}

				$calc_cost = round( $setup_cost / $qty_from, 2 );
			}
			
			return array($calc_cost, $pos_cost);
		}
	}

	public function get_min_price( $parent ) {
		$children = $parent->get_children( $parent );
		$min_price = INF;
	
		if ( count($children) ) {
			foreach( $children as $child ) {
				$product = wc_get_product( $child );
				if ( $min_price > wc_get_price_including_tax( $product ) ) {
					$min_price = wc_get_price_including_tax( $product );
				}
			}
		}

		return $min_price;
	}

	public function printing_details() {
		global $product;
		

		if ( ! $this->is_printable() ) {
			return;
		}

		$wc_printing_opts = get_post_meta( $product->get_id(), 'wc_printing_opts', true );
		?>
		
		<div class="design-buttons-holder">			
			<input class="edgtf-btn edgtf-btn-solid btn-add-logo" type="button" value="<?php esc_html_e( 'Design Now', 'wc_printing_logo' ); ?>" />
		</div>

		<?php
		$printing_prices = $wc_printing_opts['prices'];
		$printing_types = array(
			'embroid' => 'Embroidered Stitching',
			'screen' => '1 Colour Screen Printing',
			'digital' => 'Digital Full Colour Printing',
		);
		?>

		<div class="embroidered_buttons clearfix">
			<?php foreach ( $printing_prices as $key => $value ) { ?>
				<?php if ( isset($printing_prices[$key]['size'] ) ) { ?>
					<span class="embroidered_price_btn close"><?php echo $printing_types[$key]; ?></span>
				<?php } ?>
			<?php } ?>
		</div>

		<div class="embroidered_price_details">
			<?php
			foreach ( $printing_prices as $key => $values ) {

				if ( ! isset($values['size'] ) ) {
					continue;
				}

				$printing_price = array( 'label' => array(), 'price' => array() );
				$min_qty = 0;

				foreach ( $values['qty_from'] as $qty_from) {
					if ( $min_qty == 0 || $min_qty > $qty_from ) {
						$min_qty = $qty_from;
					}
				}

				$min_price = $this->get_min_price( $product );

				for ( $i = 0; $i < count( $values['size'] ); $i++ ) {
					$from = $values['qty_from'][$i];

					if ( $from > 0 ) {
						$price_label = $from;

						if( $values['qty_to'][$i] > 0 ) {
							$price_label .= " - ".  $values['qty_to'][$i];
						} else {
							$price_label .= "+";
						}

						$this->wc_printing_opts = $wc_printing_opts;
						$logo_price = $this->get_logo_price( $values['size'][$i], $from, $key );

						$price_by_qty = $min_price + $logo_price[0] + $logo_price[1];

						if ( !in_array( $price_label, $printing_price['label'] ) ) {
							array_push( $printing_price['label'], $price_label );
							array_push( $printing_price['price'], $price_by_qty );
						} else {
							$label_key = array_search($price_label, $printing_price['label']);
							$printing_price['price'][$label_key] = min($printing_price['price'][$label_key], $price_by_qty);
						}
					}
				}
				?>
				<div class="embroidered_price <?php if (!isset($wc_printing_opts['price']) || count($wc_printing_opts['price'])==0) { echo 'close'; } ?>">
					<table>
						<thead>
							<tr>
								<th colspan="<?php echo count($printing_price['label']);?>">
									<?php esc_html_e('Instant Live Quote') ; ?>:
									<span class="tooltiptext">Min qty <?php echo $min_qty; ?></span>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<?php 
								foreach($printing_price['label'] as $label){
									echo '<td>'.$label.'</td>';
								}
								?>
							</tr>
							<tr>
								<?php 
								foreach($printing_price['price'] as $price){
									echo '<td>'.wc_price($price).'<br /><span class="total_each" >TOTAL (EACH)</span></td>';
								}
								?>
							</tr>
						</tbody>
					</table>
				</div>
				<?php
			}          
			?>
		</div>

		<div class="product-custom-info">
			<?php wc_get_template( 'single-product/delivery-info.php' ); ?>

			<div class="size_info_wrapper">	
				<span class="size_info">
					<?php esc_html_e( 'Will this fit me?', 'cozy' ); ?>
					<span class="size_info_link"><?php esc_html_e( 'View size info:', 'cozy' ); ?></span>
				</span>
				<a href="#size-info-popup" class="edgtf-btn edgtf-btn-solid btn-sizeguide" data-rel="prettyPhoto[inline]"><?php esc_html_e( 'SIZE GUIDE', 'cozy' ); ?></a>
				<div id="size-info-popup">
					<div class="popup-content">
						<?php the_content(); ?>
					</div>
				</div>
			</div>
		</div>

		<?php
	}

	public function woocommerce_template_single_customisations() {
		global $product;
		

		if ( ! $this->is_printable() ) {
			return;
		}

		$wc_printing_opts = get_post_meta( $product->get_id(), 'wc_printing_opts', true );
		$printing_prices = $wc_printing_opts['prices'];

		$printing_types = array(
			'screen' => 'Print',
			'digital' => 'Digital',
			'embroid' => 'Embroidery'
		);


		$ava_prints = 'Customisations available: ';

		foreach ( $printing_prices as $key => $value ) {
			if ( isset($printing_prices[$key]['size'] ) ) {
				$ava_prints .= '<span class="' . $key . '"></span> ' . $printing_types[$key] . ' ';
			}
		}

		$ava_prints .= '<a href="#">How do I customise this item?</a>';
		?>
		
		<p class="available-prints"><?php echo sprintf( $ava_prints ); ?></p>

		<?php
	}

	public function product_design_holder() {
		if ( $this->is_printable() ) {
			include PRINTING_ROOT_DIR . 'printable-panel/main.php';
		}
	}

	public function is_printable() {
		global $product;
		
		if ( ! is_product() || ! $product ) {
			return false;
		}

		$wc_printing_opts = get_post_meta( $product->get_id(), 'wc_printing_opts', true );

		return isset( $wc_printing_opts ) && 
			isset( $wc_printing_opts['positions'] ) && 
			isset( $wc_printing_opts['prices'] ) && 
			( 
				! empty( $wc_printing_opts['prices']['embroid']['size'] ) || 
				! empty( $wc_printing_opts['prices']['screen']['size'] ) || 
				! empty( $wc_printing_opts['prices']['digital']['size'] ) 
			);
	}
}

global $wcpl;
$wcpl = new WoocommercePrintingLogo();	
