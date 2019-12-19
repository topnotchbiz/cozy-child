<?php

class YWBR_Widget extends WP_Widget {
	// class constructor
	public function __construct() {
		$widget_ops = array( 
			'classname' => 'ywbr_widget woocommerce',
			'description' => 'YITH Brand widget',
		);

		parent::__construct( 'ywbr_widget', 'YITH Brands Widget', $widget_ops );
	}
	
	// output the widget content on the front-end
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		$bargs = array(
			'taxonomy' => 'yith_product_brand',
			'hide_empty' => false
		);

		$terms = get_terms( $bargs );

		if ( !empty($terms)) {

			echo '<ul class="product-brands">';

			foreach ( $terms as $term ) {
				echo '<li class="term-item">';
				echo '<a href="' . get_term_link($term->term_id) . '">' . $term->name . '</a>';
				echo '<span class="count">(' . $term->count . ')</span>';
				echo '</li>';
			}

			echo '<ul>';
		}

		echo $args['after_widget'];
	}

	// output the option form field in admin Widgets screen
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Title', 'text_domain' );
		?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
					<?php esc_attr_e( 'Title:', 'text_domain' ); ?>
				</label> 
			
				<input 
					class="widefat" 
					id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" 
					name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" 
					type="text" 
					value="<?php echo esc_attr( $title ); ?>">
			</p>
		<?php
	}

	// save options
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}
}

// register My_Widget
add_action( 'widgets_init', function(){
	register_widget( 'YWBR_Widget' );
});