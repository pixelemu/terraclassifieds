<?php
/*--------------------------------------------------------------
 Copyright (C) pixelemu.com
 License: https://www.pixelemu.com/company/license PixelEmu Proprietary Use License
 Website: https://www.pixelemu.com
 Support: info@pixelemu.com
---------------------------------------------------------------*/

if ( ! class_exists( 'TerraclassifiedsSearch' ) ) {
	
	class TerraclassifiedsSearch extends WP_Widget {
	
		/**
		 * Constructor.
		 */
		function __construct() {
			$widget_ops = array(
				'classname'   => 'terraclassifieds-search',
				'description' => __( 'Search for Terraclassifieds ads.', 'terraclassifieds' )
			);
			parent::__construct( 'TerraClassifiedsSearch', __( 'TerraClassifieds Search', 'terraclassifieds' ), $widget_ops );
		}
	
		function widget( $args, $instance ) {
			
			extract( $args );
	
			$title = ( ! empty( $instance['title'] ) ) ? apply_filters( 'widget_title', $instance['title'] ) : false;
			$location    = ( ! empty( $instance['location'] ) ) ? true : false;
			$location_option    = ( ! empty( $instance['location_option'] ) ) ? true : false;
			$location_list_hierarchical    = ( ! empty( $instance['location_list_hierarchical'] ) ) ? true : false;

			if ( $title ):
				echo $before_title;
				echo $title;
				echo $after_title;
			endif;
			
			echo $before_widget; 
			
			?>

			<?php echo terraclassifiedsSearchForm( $instance ); ?>
			
			<?php echo $after_widget;
	
		}

		function form( $instance ) {
	
			$instance = wp_parse_args( (array) $instance, array(
					'title'           => '',
					'category_filter' => 0,
					'only_first_level' => 1,
					'location'    => 0,
					'location_option'    => 0,
					'location_list_hierarchical'    => 0,
					'type'    => 0,
                    'price'    => 0,
					'search_large_size'    => 4,
					'search_desktop_size'    => 4,
					'search_tablet_size'    => 12,
					'search_phone_size'    => 12,
					'location_large_size'    => 4,
					'location_desktop_size'    => 4,
					'location_tablet_size'    => 12,
					'location_phone_size'    => 12,
					'category_large_size'    => 4,
					'category_desktop_size'    => 4,
					'category_tablet_size'    => 12,
					'category_phone_size'    => 12,
    			    'price_large_size'    => 6,
    			    'price_desktop_size'    => 6,
    			    'price_tablet_size'    => 6,
    			    'price_phone_size'    => 12,
				)
			);
			$title           = esc_attr( $instance['title'] );
			$category_filter = esc_attr( $instance['category_filter'] );
			$only_first_level = esc_attr( $instance['only_first_level'] );
			$location = isset( $instance['location'] ) ? (bool) $instance['location'] : false;
			$location_option = isset( $instance['location_option'] ) ? (bool) $instance['location_option'] : false;
			$location_list_hierarchical = isset( $instance['location_list_hierarchical'] ) ? (bool) $instance['location_list_hierarchical'] : false;
			$type = isset( $instance['type'] ) ? (bool) $instance['type'] : false;
			$price = isset( $instance['price'] ) ? (bool) $instance['price'] : false;
			$search_large_size = esc_attr( $instance['search_large_size'] );
			$search_desktop_size = esc_attr( $instance['search_desktop_size'] );
			$search_tablet_size = esc_attr( $instance['search_tablet_size'] );
			$search_phone_size = esc_attr( $instance['search_phone_size'] );
			$location_large_size = esc_attr( $instance['location_large_size'] );
			$location_desktop_size = esc_attr( $instance['location_desktop_size'] );
			$location_tablet_size = esc_attr( $instance['location_tablet_size'] );
			$location_phone_size = esc_attr( $instance['location_phone_size'] );
			$category_large_size = esc_attr( $instance['category_large_size'] );
			$category_desktop_size = esc_attr( $instance['category_desktop_size'] );
			$category_tablet_size = esc_attr( $instance['category_tablet_size'] );
			$category_phone_size = esc_attr( $instance['category_phone_size'] );
			$price_large_size = esc_attr( $instance['price_large_size'] );
			$price_desktop_size = esc_attr( $instance['price_desktop_size'] );
			$price_tablet_size = esc_attr( $instance['price_tablet_size'] );
			$price_phone_size = esc_attr( $instance['price_phone_size'] );
			?>
	
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Widget Title', 'terraclassifieds' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr($title); ?>"/>
			</p>

			<p>
				<label for="<?php echo $this -> get_field_id('category_filter'); ?>"><?php _e('Category filter', 'terraclassifieds'); ?></label>
				<select class="widefat" id="<?php echo $this -> get_field_id('category_filter'); ?>" name="<?php echo $this -> get_field_name('category_filter'); ?>" style="width:100%;">
					<option value='0'<?php echo($category_filter == '0') ? 'selected' : ''; ?>><?php _e('Hide', 'terraclassifieds'); ?></option>
					<option value='1'<?php echo($category_filter == '1') ? 'selected' : ''; ?>><?php _e('Show', 'terraclassifieds'); ?></option>
				</select>
			</p>
			
			<p>
				<label for="<?php echo $this -> get_field_id('only_first_level'); ?>"><?php _e('Display only first level categories', 'terraclassifieds'); ?></label>
				<select class="widefat" id="<?php echo $this -> get_field_id('only_first_level'); ?>" name="<?php echo $this -> get_field_name('only_first_level'); ?>" style="width:100%;">
					<option value='0'<?php echo($only_first_level == '0') ? 'selected' : ''; ?>><?php _e('No', 'terraclassifieds'); ?></option>
					<option value='1'<?php echo($only_first_level == '1') ? 'selected' : ''; ?>><?php _e('Yes', 'terraclassifieds'); ?></option>
				</select>
			</p>
			
			<p>
				<label for="<?php echo $this -> get_field_id('location'); ?>"><?php _e('Location filter', 'terraclassifieds'); ?></label>
				<select class="widefat" id="<?php echo $this -> get_field_id('location'); ?>" name="<?php echo $this -> get_field_name('location'); ?>" style="width:100%;">
					<option value='0'<?php echo($location == '0') ? 'selected' : ''; ?>><?php _e('Hide', 'terraclassifieds'); ?></option>
					<option value='1'<?php echo($location == '1') ? 'selected' : ''; ?>><?php _e('Show', 'terraclassifieds'); ?></option>
				</select>
			</p>
			
			<p>
				<label for="<?php echo $this -> get_field_id('location_option'); ?>"><?php _e('Location option', 'terraclassifieds'); ?></label>
				<select class="widefat" id="<?php echo $this -> get_field_id('location_option'); ?>" name="<?php echo $this -> get_field_name('location_option'); ?>" style="width:100%;">
					<option value='0'<?php echo($location_option == '0') ? 'selected' : ''; ?>><?php _e('List', 'terraclassifieds'); ?></option>
					<option value='1'<?php echo($location_option == '1') ? 'selected' : ''; ?>><?php _e('Inputbox', 'terraclassifieds'); ?></option>
				</select>
			</p>
			
			<p>
				<label for="<?php echo $this -> get_field_id('location_list_hierarchical'); ?>"><?php _e('Location hierarchical (for list option only)', 'terraclassifieds'); ?></label>
				<select class="widefat" id="<?php echo $this -> get_field_id('location_list_hierarchical'); ?>" name="<?php echo $this -> get_field_name('location_list_hierarchical'); ?>" style="width:100%;">
					<option value='0'<?php echo($location_list_hierarchical == '0') ? 'selected' : ''; ?>><?php _e('No', 'terraclassifieds'); ?></option>
					<option value='1'<?php echo($location_list_hierarchical == '1') ? 'selected' : ''; ?>><?php _e('Yes', 'terraclassifieds'); ?></option>
				</select>
			</p>
			
			<p>
				<label for="<?php echo $this -> get_field_id('type'); ?>"><?php _e('Type filter', 'terraclassifieds'); ?></label>
				<select class="widefat" id="<?php echo $this -> get_field_id('type'); ?>" name="<?php echo $this -> get_field_name('type'); ?>" style="width:100%;">
					<option value='0'<?php echo($type == '0') ? 'selected' : ''; ?>><?php _e('Hide', 'terraclassifieds'); ?></option>
					<option value='1'<?php echo($type == '1') ? 'selected' : ''; ?>><?php _e('Show', 'terraclassifieds'); ?></option>
				</select>
			</p>
			
			<p>
				<label for="<?php echo $this -> get_field_id('price'); ?>"><?php _e('Price filter', 'terraclassifieds'); ?></label>
				<select class="widefat" id="<?php echo $this -> get_field_id('price'); ?>" name="<?php echo $this -> get_field_name('price'); ?>" style="width:100%;">
					<option value='0'<?php echo($price == '0') ? 'selected' : ''; ?>><?php _e('Hide', 'terraclassifieds'); ?></option>
					<option value='1'<?php echo($price == '1') ? 'selected' : ''; ?>><?php _e('Show', 'terraclassifieds'); ?></option>
				</select>
			</p>
					
			<p class="terraclassifieds-search-label"><?php _e('Search field size', 'terraclassifieds'); ?></p>
			
			<p class="terraclassifieds-screen-size">
				<label for="<?php echo $this -> get_field_id('search_large_size'); ?>"><?php _e('Large Desktop ( ≥ 1200px )', 'terraclassifieds'); ?></label>
				<select class="widefat" id="<?php echo $this -> get_field_id('search_large_size'); ?>" name="<?php echo $this -> get_field_name('search_large_size'); ?>" style="width:100%;">
					<option value='12'<?php echo($search_large_size == '12') ? 'selected' : ''; ?>><?php _e('100% - col-lg-12', 'terraclassifieds'); ?></option>
					<option value='11'<?php echo($search_large_size == '11') ? 'selected' : ''; ?>><?php _e('91% - col-lg-11', 'terraclassifieds'); ?></option>
					<option value='10'<?php echo($search_large_size == '10') ? 'selected' : ''; ?>><?php _e('83% - col-lg-10', 'terraclassifieds'); ?></option>
					<option value='9'<?php echo($search_large_size == '9') ? 'selected' : ''; ?>><?php _e('75% - col-lg-9', 'terraclassifieds'); ?></option>
					<option value='8'<?php echo($search_large_size == '8') ? 'selected' : ''; ?>><?php _e('66% - col-lg-8', 'terraclassifieds'); ?></option>
					<option value='7'<?php echo($search_large_size == '7') ? 'selected' : ''; ?>><?php _e('58% - col-lg-7', 'terraclassifieds'); ?></option>
					<option value='6'<?php echo($search_large_size == '6') ? 'selected' : ''; ?>><?php _e('50% - col-lg-6', 'terraclassifieds'); ?></option>
					<option value='5'<?php echo($search_large_size == '5') ? 'selected' : ''; ?>><?php _e('41% - col-lg-5', 'terraclassifieds'); ?></option>
					<option value='4'<?php echo($search_large_size == '4') ? 'selected' : ''; ?>><?php _e('33% - col-lg-4', 'terraclassifieds'); ?></option>
					<option value='3'<?php echo($search_large_size == '3') ? 'selected' : ''; ?>><?php _e('25% - col-lg-3', 'terraclassifieds'); ?></option>
					<option value='2'<?php echo($search_large_size == '2') ? 'selected' : ''; ?>><?php _e('16% - col-lg-2', 'terraclassifieds'); ?></option>
					<option value='1'<?php echo($search_large_size == '1') ? 'selected' : ''; ?>><?php _e('8% - col-lg-1', 'terraclassifieds'); ?></option>
				</select>
			</p>
			
			<p class="terraclassifieds-screen-size">
				<label for="<?php echo $this -> get_field_id('search_desktop_size'); ?>"><?php _e('Desktop ( ≥ 992px )', 'terraclassifieds'); ?></label>
				<select class="widefat" id="<?php echo $this -> get_field_id('search_desktop_size'); ?>" name="<?php echo $this -> get_field_name('search_desktop_size'); ?>" style="width:100%;">
					<option value='12'<?php echo($search_desktop_size == '12') ? 'selected' : ''; ?>><?php _e('100% - col-md-12', 'terraclassifieds'); ?></option>
					<option value='11'<?php echo($search_desktop_size == '11') ? 'selected' : ''; ?>><?php _e('91% - col-md-11', 'terraclassifieds'); ?></option>
					<option value='10'<?php echo($search_desktop_size == '10') ? 'selected' : ''; ?>><?php _e('83% - col-md-10', 'terraclassifieds'); ?></option>
					<option value='9'<?php echo($search_desktop_size == '9') ? 'selected' : ''; ?>><?php _e('75% - col-md-9', 'terraclassifieds'); ?></option>
					<option value='8'<?php echo($search_desktop_size == '8') ? 'selected' : ''; ?>><?php _e('66% - col-md-8', 'terraclassifieds'); ?></option>
					<option value='7'<?php echo($search_desktop_size == '7') ? 'selected' : ''; ?>><?php _e('58% - col-md-7', 'terraclassifieds'); ?></option>
					<option value='6'<?php echo($search_desktop_size == '6') ? 'selected' : ''; ?>><?php _e('50% - col-md-6', 'terraclassifieds'); ?></option>
					<option value='5'<?php echo($search_desktop_size == '5') ? 'selected' : ''; ?>><?php _e('41% - col-md-5', 'terraclassifieds'); ?></option>
					<option value='4'<?php echo($search_desktop_size == '4') ? 'selected' : ''; ?>><?php _e('33% - col-md-4', 'terraclassifieds'); ?></option>
					<option value='3'<?php echo($search_desktop_size == '3') ? 'selected' : ''; ?>><?php _e('25% - col-md-3', 'terraclassifieds'); ?></option>
					<option value='2'<?php echo($search_desktop_size == '2') ? 'selected' : ''; ?>><?php _e('16% - col-md-2', 'terraclassifieds'); ?></option>
					<option value='1'<?php echo($search_desktop_size == '1') ? 'selected' : ''; ?>><?php _e('8% - col-md-1', 'terraclassifieds'); ?></option>
				</select>
			</p>
			
			<p class="terraclassifieds-screen-size">
				<label for="<?php echo $this -> get_field_id('search_tablet_size'); ?>"><?php _e('Tablet ( ≥ 768px )', 'terraclassifieds'); ?></label>
				<select class="widefat" id="<?php echo $this -> get_field_id('search_tablet_size'); ?>" name="<?php echo $this -> get_field_name('search_tablet_size'); ?>" style="width:100%;">
					<option value='12'<?php echo($search_tablet_size == '12') ? 'selected' : ''; ?>><?php _e('100% - col-sm-12', 'terraclassifieds'); ?></option>
					<option value='11'<?php echo($search_tablet_size == '11') ? 'selected' : ''; ?>><?php _e('91% - col-sm-11', 'terraclassifieds'); ?></option>
					<option value='10'<?php echo($search_tablet_size == '10') ? 'selected' : ''; ?>><?php _e('83% - col-sm-10', 'terraclassifieds'); ?></option>
					<option value='9'<?php echo($search_tablet_size == '9') ? 'selected' : ''; ?>><?php _e('75% - col-sm-9', 'terraclassifieds'); ?></option>
					<option value='8'<?php echo($search_tablet_size == '8') ? 'selected' : ''; ?>><?php _e('66% - col-sm-8', 'terraclassifieds'); ?></option>
					<option value='7'<?php echo($search_tablet_size == '7') ? 'selected' : ''; ?>><?php _e('58% - col-sm-7', 'terraclassifieds'); ?></option>
					<option value='6'<?php echo($search_tablet_size == '6') ? 'selected' : ''; ?>><?php _e('50% - col-sm-6', 'terraclassifieds'); ?></option>
					<option value='5'<?php echo($search_tablet_size == '5') ? 'selected' : ''; ?>><?php _e('41% - col-sm-5', 'terraclassifieds'); ?></option>
					<option value='4'<?php echo($search_tablet_size == '4') ? 'selected' : ''; ?>><?php _e('33% - col-sm-4', 'terraclassifieds'); ?></option>
					<option value='3'<?php echo($search_tablet_size == '3') ? 'selected' : ''; ?>><?php _e('25% - col-sm-3', 'terraclassifieds'); ?></option>
					<option value='2'<?php echo($search_tablet_size == '2') ? 'selected' : ''; ?>><?php _e('16% - col-sm-2', 'terraclassifieds'); ?></option>
					<option value='1'<?php echo($search_tablet_size == '1') ? 'selected' : ''; ?>><?php _e('8% - col-sm-1', 'terraclassifieds'); ?></option>
				</select>
			</p>
			
			<p class="terraclassifieds-screen-size">
				<label for="<?php echo $this -> get_field_id('search_phone_size'); ?>"><?php _e('Phone ( < 768px )', 'terraclassifieds'); ?></label>
				<select class="widefat" id="<?php echo $this -> get_field_id('search_phone_size'); ?>" name="<?php echo $this -> get_field_name('search_phone_size'); ?>" style="width:100%;">
					<option value='12'<?php echo($search_phone_size == '12') ? 'selected' : ''; ?>><?php _e('100% - col-xs-12', 'terraclassifieds'); ?></option>
					<option value='11'<?php echo($search_phone_size == '11') ? 'selected' : ''; ?>><?php _e('91% - col-xs-11', 'terraclassifieds'); ?></option>
					<option value='10'<?php echo($search_phone_size == '10') ? 'selected' : ''; ?>><?php _e('83% - col-xs-10', 'terraclassifieds'); ?></option>
					<option value='9'<?php echo($search_phone_size == '9') ? 'selected' : ''; ?>><?php _e('75% - col-xs-9', 'terraclassifieds'); ?></option>
					<option value='8'<?php echo($search_phone_size == '8') ? 'selected' : ''; ?>><?php _e('66% - col-xs-8', 'terraclassifieds'); ?></option>
					<option value='7'<?php echo($search_phone_size == '7') ? 'selected' : ''; ?>><?php _e('58% - col-xs-7', 'terraclassifieds'); ?></option>
					<option value='6'<?php echo($search_phone_size == '6') ? 'selected' : ''; ?>><?php _e('50% - col-xs-6', 'terraclassifieds'); ?></option>
					<option value='5'<?php echo($search_phone_size == '5') ? 'selected' : ''; ?>><?php _e('41% - col-xs-5', 'terraclassifieds'); ?></option>
					<option value='4'<?php echo($search_phone_size == '4') ? 'selected' : ''; ?>><?php _e('33% - col-xs-4', 'terraclassifieds'); ?></option>
					<option value='3'<?php echo($search_phone_size == '3') ? 'selected' : ''; ?>><?php _e('25% - col-xs-3', 'terraclassifieds'); ?></option>
					<option value='2'<?php echo($search_phone_size == '2') ? 'selected' : ''; ?>><?php _e('16% - col-xs-2', 'terraclassifieds'); ?></option>
					<option value='1'<?php echo($search_phone_size == '1') ? 'selected' : ''; ?>><?php _e('8% - col-xs-1', 'terraclassifieds'); ?></option>
				</select>
			</p>
			
			<p class="terraclassifieds-search-label"><?php _e('Location field size', 'terraclassifieds'); ?></p>
			
			<p class="terraclassifieds-screen-size">
				<label for="<?php echo $this -> get_field_id('location_large_size'); ?>"><?php _e('Large Desktop ( ≥ 1200px )', 'terraclassifieds'); ?></label>
				<select class="widefat" id="<?php echo $this -> get_field_id('location_large_size'); ?>" name="<?php echo $this -> get_field_name('location_large_size'); ?>" style="width:100%;">
					<option value='12'<?php echo($location_large_size == '12') ? 'selected' : ''; ?>><?php _e('100% - col-lg-12', 'terraclassifieds'); ?></option>
					<option value='11'<?php echo($location_large_size == '11') ? 'selected' : ''; ?>><?php _e('91% - col-lg-11', 'terraclassifieds'); ?></option>
					<option value='10'<?php echo($location_large_size == '10') ? 'selected' : ''; ?>><?php _e('83% - col-lg-10', 'terraclassifieds'); ?></option>
					<option value='9'<?php echo($location_large_size == '9') ? 'selected' : ''; ?>><?php _e('75% - col-lg-9', 'terraclassifieds'); ?></option>
					<option value='8'<?php echo($location_large_size == '8') ? 'selected' : ''; ?>><?php _e('66% - col-lg-8', 'terraclassifieds'); ?></option>
					<option value='7'<?php echo($location_large_size == '7') ? 'selected' : ''; ?>><?php _e('58% - col-lg-7', 'terraclassifieds'); ?></option>
					<option value='6'<?php echo($location_large_size == '6') ? 'selected' : ''; ?>><?php _e('50% - col-lg-6', 'terraclassifieds'); ?></option>
					<option value='5'<?php echo($location_large_size == '5') ? 'selected' : ''; ?>><?php _e('41% - col-lg-5', 'terraclassifieds'); ?></option>
					<option value='4'<?php echo($location_large_size == '4') ? 'selected' : ''; ?>><?php _e('33% - col-lg-4', 'terraclassifieds'); ?></option>
					<option value='3'<?php echo($location_large_size == '3') ? 'selected' : ''; ?>><?php _e('25% - col-lg-3', 'terraclassifieds'); ?></option>
					<option value='2'<?php echo($location_large_size == '2') ? 'selected' : ''; ?>><?php _e('16% - col-lg-2', 'terraclassifieds'); ?></option>
					<option value='1'<?php echo($location_large_size == '1') ? 'selected' : ''; ?>><?php _e('8% - col-lg-1', 'terraclassifieds'); ?></option>
				</select>
			</p>
			
			<p class="terraclassifieds-screen-size">
				<label for="<?php echo $this -> get_field_id('location_desktop_size'); ?>"><?php _e('Desktop ( ≥ 992px )', 'terraclassifieds'); ?></label>
				<select class="widefat" id="<?php echo $this -> get_field_id('location_desktop_size'); ?>" name="<?php echo $this -> get_field_name('location_desktop_size'); ?>" style="width:100%;">
					<option value='12'<?php echo($location_desktop_size == '12') ? 'selected' : ''; ?>><?php _e('100% - col-md-12', 'terraclassifieds'); ?></option>
					<option value='11'<?php echo($location_desktop_size == '11') ? 'selected' : ''; ?>><?php _e('91% - col-md-11', 'terraclassifieds'); ?></option>
					<option value='10'<?php echo($location_desktop_size == '10') ? 'selected' : ''; ?>><?php _e('83% - col-md-10', 'terraclassifieds'); ?></option>
					<option value='9'<?php echo($location_desktop_size == '9') ? 'selected' : ''; ?>><?php _e('75% - col-md-9', 'terraclassifieds'); ?></option>
					<option value='8'<?php echo($location_desktop_size == '8') ? 'selected' : ''; ?>><?php _e('66% - col-md-8', 'terraclassifieds'); ?></option>
					<option value='7'<?php echo($location_desktop_size == '7') ? 'selected' : ''; ?>><?php _e('58% - col-md-7', 'terraclassifieds'); ?></option>
					<option value='6'<?php echo($location_desktop_size == '6') ? 'selected' : ''; ?>><?php _e('50% - col-md-6', 'terraclassifieds'); ?></option>
					<option value='5'<?php echo($location_desktop_size == '5') ? 'selected' : ''; ?>><?php _e('41% - col-md-5', 'terraclassifieds'); ?></option>
					<option value='4'<?php echo($location_desktop_size == '4') ? 'selected' : ''; ?>><?php _e('33% - col-md-4', 'terraclassifieds'); ?></option>
					<option value='3'<?php echo($location_desktop_size == '3') ? 'selected' : ''; ?>><?php _e('25% - col-md-3', 'terraclassifieds'); ?></option>
					<option value='2'<?php echo($location_desktop_size == '2') ? 'selected' : ''; ?>><?php _e('16% - col-md-2', 'terraclassifieds'); ?></option>
					<option value='1'<?php echo($location_desktop_size == '1') ? 'selected' : ''; ?>><?php _e('8% - col-md-1', 'terraclassifieds'); ?></option>
				</select>
			</p>
			
			<p class="terraclassifieds-screen-size">
				<label for="<?php echo $this -> get_field_id('location_tablet_size'); ?>"><?php _e('Tablet ( ≥ 768px )', 'terraclassifieds'); ?></label>
				<select class="widefat" id="<?php echo $this -> get_field_id('location_tablet_size'); ?>" name="<?php echo $this -> get_field_name('location_tablet_size'); ?>" style="width:100%;">
					<option value='12'<?php echo($location_tablet_size == '12') ? 'selected' : ''; ?>><?php _e('100% - col-sm-12', 'terraclassifieds'); ?></option>
					<option value='11'<?php echo($location_tablet_size == '11') ? 'selected' : ''; ?>><?php _e('91% - col-sm-11', 'terraclassifieds'); ?></option>
					<option value='10'<?php echo($location_tablet_size == '10') ? 'selected' : ''; ?>><?php _e('83% - col-sm-10', 'terraclassifieds'); ?></option>
					<option value='9'<?php echo($location_tablet_size == '9') ? 'selected' : ''; ?>><?php _e('75% - col-sm-9', 'terraclassifieds'); ?></option>
					<option value='8'<?php echo($location_tablet_size == '8') ? 'selected' : ''; ?>><?php _e('66% - col-sm-8', 'terraclassifieds'); ?></option>
					<option value='7'<?php echo($location_tablet_size == '7') ? 'selected' : ''; ?>><?php _e('58% - col-sm-7', 'terraclassifieds'); ?></option>
					<option value='6'<?php echo($location_tablet_size == '6') ? 'selected' : ''; ?>><?php _e('50% - col-sm-6', 'terraclassifieds'); ?></option>
					<option value='5'<?php echo($location_tablet_size == '5') ? 'selected' : ''; ?>><?php _e('41% - col-sm-5', 'terraclassifieds'); ?></option>
					<option value='4'<?php echo($location_tablet_size == '4') ? 'selected' : ''; ?>><?php _e('33% - col-sm-4', 'terraclassifieds'); ?></option>
					<option value='3'<?php echo($location_tablet_size == '3') ? 'selected' : ''; ?>><?php _e('25% - col-sm-3', 'terraclassifieds'); ?></option>
					<option value='2'<?php echo($location_tablet_size == '2') ? 'selected' : ''; ?>><?php _e('16% - col-sm-2', 'terraclassifieds'); ?></option>
					<option value='1'<?php echo($location_tablet_size == '1') ? 'selected' : ''; ?>><?php _e('8% - col-sm-1', 'terraclassifieds'); ?></option>
				</select>
			</p>
			
			<p class="terraclassifieds-screen-size">
				<label for="<?php echo $this -> get_field_id('location_phone_size'); ?>"><?php _e('Phone ( < 768px )', 'terraclassifieds'); ?></label>
				<select class="widefat" id="<?php echo $this -> get_field_id('location_phone_size'); ?>" name="<?php echo $this -> get_field_name('location_phone_size'); ?>" style="width:100%;">
					<option value='12'<?php echo($location_phone_size == '12') ? 'selected' : ''; ?>><?php _e('100% - col-xs-12', 'terraclassifieds'); ?></option>
					<option value='11'<?php echo($location_phone_size == '11') ? 'selected' : ''; ?>><?php _e('91% - col-xs-11', 'terraclassifieds'); ?></option>
					<option value='10'<?php echo($location_phone_size == '10') ? 'selected' : ''; ?>><?php _e('83% - col-xs-10', 'terraclassifieds'); ?></option>
					<option value='9'<?php echo($location_phone_size == '9') ? 'selected' : ''; ?>><?php _e('75% - col-xs-9', 'terraclassifieds'); ?></option>
					<option value='8'<?php echo($location_phone_size == '8') ? 'selected' : ''; ?>><?php _e('66% - col-xs-8', 'terraclassifieds'); ?></option>
					<option value='7'<?php echo($location_phone_size == '7') ? 'selected' : ''; ?>><?php _e('58% - col-xs-7', 'terraclassifieds'); ?></option>
					<option value='6'<?php echo($location_phone_size == '6') ? 'selected' : ''; ?>><?php _e('50% - col-xs-6', 'terraclassifieds'); ?></option>
					<option value='5'<?php echo($location_phone_size == '5') ? 'selected' : ''; ?>><?php _e('41% - col-xs-5', 'terraclassifieds'); ?></option>
					<option value='4'<?php echo($location_phone_size == '4') ? 'selected' : ''; ?>><?php _e('33% - col-xs-4', 'terraclassifieds'); ?></option>
					<option value='3'<?php echo($location_phone_size == '3') ? 'selected' : ''; ?>><?php _e('25% - col-xs-3', 'terraclassifieds'); ?></option>
					<option value='2'<?php echo($location_phone_size == '2') ? 'selected' : ''; ?>><?php _e('16% - col-xs-2', 'terraclassifieds'); ?></option>
					<option value='1'<?php echo($location_phone_size == '1') ? 'selected' : ''; ?>><?php _e('8% - col-xs-1', 'terraclassifieds'); ?></option>
				</select>
			</p>
			
			<p class="terraclassifieds-search-label"><?php _e('Category field size', 'terraclassifieds'); ?></p>
			
			<p class="terraclassifieds-screen-size">
				<label for="<?php echo $this -> get_field_id('category_large_size'); ?>"><?php _e('Large Desktop ( ≥ 1200px )', 'terraclassifieds'); ?></label>
				<select class="widefat" id="<?php echo $this -> get_field_id('category_large_size'); ?>" name="<?php echo $this -> get_field_name('category_large_size'); ?>" style="width:100%;">
					<option value='12'<?php echo($category_large_size == '12') ? 'selected' : ''; ?>><?php _e('100% - col-lg-12', 'terraclassifieds'); ?></option>
					<option value='11'<?php echo($category_large_size == '11') ? 'selected' : ''; ?>><?php _e('91% - col-lg-11', 'terraclassifieds'); ?></option>
					<option value='10'<?php echo($category_large_size == '10') ? 'selected' : ''; ?>><?php _e('83% - col-lg-10', 'terraclassifieds'); ?></option>
					<option value='9'<?php echo($category_large_size == '9') ? 'selected' : ''; ?>><?php _e('75% - col-lg-9', 'terraclassifieds'); ?></option>
					<option value='8'<?php echo($category_large_size == '8') ? 'selected' : ''; ?>><?php _e('66% - col-lg-8', 'terraclassifieds'); ?></option>
					<option value='7'<?php echo($category_large_size == '7') ? 'selected' : ''; ?>><?php _e('58% - col-lg-7', 'terraclassifieds'); ?></option>
					<option value='6'<?php echo($category_large_size == '6') ? 'selected' : ''; ?>><?php _e('50% - col-lg-6', 'terraclassifieds'); ?></option>
					<option value='5'<?php echo($category_large_size == '5') ? 'selected' : ''; ?>><?php _e('41% - col-lg-5', 'terraclassifieds'); ?></option>
					<option value='4'<?php echo($category_large_size == '4') ? 'selected' : ''; ?>><?php _e('33% - col-lg-4', 'terraclassifieds'); ?></option>
					<option value='3'<?php echo($category_large_size == '3') ? 'selected' : ''; ?>><?php _e('25% - col-lg-3', 'terraclassifieds'); ?></option>
					<option value='2'<?php echo($category_large_size == '2') ? 'selected' : ''; ?>><?php _e('16% - col-lg-2', 'terraclassifieds'); ?></option>
					<option value='1'<?php echo($category_large_size == '1') ? 'selected' : ''; ?>><?php _e('8% - col-lg-1', 'terraclassifieds'); ?></option>
				</select>
			</p>
			
			<p class="terraclassifieds-screen-size">
				<label for="<?php echo $this -> get_field_id('category_desktop_size'); ?>"><?php _e('Desktop ( ≥ 992px )', 'terraclassifieds'); ?></label>
				<select class="widefat" id="<?php echo $this -> get_field_id('category_desktop_size'); ?>" name="<?php echo $this -> get_field_name('category_desktop_size'); ?>" style="width:100%;">
					<option value='12'<?php echo($category_desktop_size == '12') ? 'selected' : ''; ?>><?php _e('100% - col-md-12', 'terraclassifieds'); ?></option>
					<option value='11'<?php echo($category_desktop_size == '11') ? 'selected' : ''; ?>><?php _e('91% - col-md-11', 'terraclassifieds'); ?></option>
					<option value='10'<?php echo($category_desktop_size == '10') ? 'selected' : ''; ?>><?php _e('83% - col-md-10', 'terraclassifieds'); ?></option>
					<option value='9'<?php echo($category_desktop_size == '9') ? 'selected' : ''; ?>><?php _e('75% - col-md-9', 'terraclassifieds'); ?></option>
					<option value='8'<?php echo($category_desktop_size == '8') ? 'selected' : ''; ?>><?php _e('66% - col-md-8', 'terraclassifieds'); ?></option>
					<option value='7'<?php echo($category_desktop_size == '7') ? 'selected' : ''; ?>><?php _e('58% - col-md-7', 'terraclassifieds'); ?></option>
					<option value='6'<?php echo($category_desktop_size == '6') ? 'selected' : ''; ?>><?php _e('50% - col-md-6', 'terraclassifieds'); ?></option>
					<option value='5'<?php echo($category_desktop_size == '5') ? 'selected' : ''; ?>><?php _e('41% - col-md-5', 'terraclassifieds'); ?></option>
					<option value='4'<?php echo($category_desktop_size == '4') ? 'selected' : ''; ?>><?php _e('33% - col-md-4', 'terraclassifieds'); ?></option>
					<option value='3'<?php echo($category_desktop_size == '3') ? 'selected' : ''; ?>><?php _e('25% - col-md-3', 'terraclassifieds'); ?></option>
					<option value='2'<?php echo($category_desktop_size == '2') ? 'selected' : ''; ?>><?php _e('16% - col-md-2', 'terraclassifieds'); ?></option>
					<option value='1'<?php echo($category_desktop_size == '1') ? 'selected' : ''; ?>><?php _e('8% - col-md-1', 'terraclassifieds'); ?></option>
				</select>
			</p>
			
			<p class="terraclassifieds-screen-size">
				<label for="<?php echo $this -> get_field_id('category_tablet_size'); ?>"><?php _e('Tablet ( ≥ 768px )', 'terraclassifieds'); ?></label>
				<select class="widefat" id="<?php echo $this -> get_field_id('category_tablet_size'); ?>" name="<?php echo $this -> get_field_name('category_tablet_size'); ?>" style="width:100%;">
					<option value='12'<?php echo($category_tablet_size == '12') ? 'selected' : ''; ?>><?php _e('100% - col-sm-12', 'terraclassifieds'); ?></option>
					<option value='11'<?php echo($category_tablet_size == '11') ? 'selected' : ''; ?>><?php _e('91% - col-sm-11', 'terraclassifieds'); ?></option>
					<option value='10'<?php echo($category_tablet_size == '10') ? 'selected' : ''; ?>><?php _e('83% - col-sm-10', 'terraclassifieds'); ?></option>
					<option value='9'<?php echo($category_tablet_size == '9') ? 'selected' : ''; ?>><?php _e('75% - col-sm-9', 'terraclassifieds'); ?></option>
					<option value='8'<?php echo($category_tablet_size == '8') ? 'selected' : ''; ?>><?php _e('66% - col-sm-8', 'terraclassifieds'); ?></option>
					<option value='7'<?php echo($category_tablet_size == '7') ? 'selected' : ''; ?>><?php _e('58% - col-sm-7', 'terraclassifieds'); ?></option>
					<option value='6'<?php echo($category_tablet_size == '6') ? 'selected' : ''; ?>><?php _e('50% - col-sm-6', 'terraclassifieds'); ?></option>
					<option value='5'<?php echo($category_tablet_size == '5') ? 'selected' : ''; ?>><?php _e('41% - col-sm-5', 'terraclassifieds'); ?></option>
					<option value='4'<?php echo($category_tablet_size == '4') ? 'selected' : ''; ?>><?php _e('33% - col-sm-4', 'terraclassifieds'); ?></option>
					<option value='3'<?php echo($category_tablet_size == '3') ? 'selected' : ''; ?>><?php _e('25% - col-sm-3', 'terraclassifieds'); ?></option>
					<option value='2'<?php echo($category_tablet_size == '2') ? 'selected' : ''; ?>><?php _e('16% - col-sm-2', 'terraclassifieds'); ?></option>
					<option value='1'<?php echo($category_tablet_size == '1') ? 'selected' : ''; ?>><?php _e('8% - col-sm-1', 'terraclassifieds'); ?></option>
				</select>
			</p>
			
			<p class="terraclassifieds-screen-size">
				<label for="<?php echo $this -> get_field_id('category_phone_size'); ?>"><?php _e('Phone ( < 768px )', 'terraclassifieds'); ?></label>
				<select class="widefat" id="<?php echo $this -> get_field_id('category_phone_size'); ?>" name="<?php echo $this -> get_field_name('category_phone_size'); ?>" style="width:100%;">
					<option value='12'<?php echo($category_phone_size == '12') ? 'selected' : ''; ?>><?php _e('100% - col-xs-12', 'terraclassifieds'); ?></option>
					<option value='11'<?php echo($category_phone_size == '11') ? 'selected' : ''; ?>><?php _e('91% - col-xs-11', 'terraclassifieds'); ?></option>
					<option value='10'<?php echo($category_phone_size == '10') ? 'selected' : ''; ?>><?php _e('83% - col-xs-10', 'terraclassifieds'); ?></option>
					<option value='9'<?php echo($category_phone_size == '9') ? 'selected' : ''; ?>><?php _e('75% - col-xs-9', 'terraclassifieds'); ?></option>
					<option value='8'<?php echo($category_phone_size == '8') ? 'selected' : ''; ?>><?php _e('66% - col-xs-8', 'terraclassifieds'); ?></option>
					<option value='7'<?php echo($category_phone_size == '7') ? 'selected' : ''; ?>><?php _e('58% - col-xs-7', 'terraclassifieds'); ?></option>
					<option value='6'<?php echo($category_phone_size == '6') ? 'selected' : ''; ?>><?php _e('50% - col-xs-6', 'terraclassifieds'); ?></option>
					<option value='5'<?php echo($category_phone_size == '5') ? 'selected' : ''; ?>><?php _e('41% - col-xs-5', 'terraclassifieds'); ?></option>
					<option value='4'<?php echo($category_phone_size == '4') ? 'selected' : ''; ?>><?php _e('33% - col-xs-4', 'terraclassifieds'); ?></option>
					<option value='3'<?php echo($category_phone_size == '3') ? 'selected' : ''; ?>><?php _e('25% - col-xs-3', 'terraclassifieds'); ?></option>
					<option value='2'<?php echo($category_phone_size == '2') ? 'selected' : ''; ?>><?php _e('16% - col-xs-2', 'terraclassifieds'); ?></option>
					<option value='1'<?php echo($category_phone_size == '1') ? 'selected' : ''; ?>><?php _e('8% - col-xs-1', 'terraclassifieds'); ?></option>
				</select>
			</p>
			
			<p class="terraclassifieds-search-label"><?php _e('Price field size', 'terraclassifieds'); ?></p>
			
			<p class="terraclassifieds-screen-size">
				<label for="<?php echo $this -> get_field_id('price_large_size'); ?>"><?php _e('Large Desktop ( ≥ 1200px )', 'terraclassifieds'); ?></label>
				<select class="widefat" id="<?php echo $this -> get_field_id('price_large_size'); ?>" name="<?php echo $this -> get_field_name('price_large_size'); ?>" style="width:100%;">
					<option value='12'<?php echo($price_large_size == '12') ? 'selected' : ''; ?>><?php _e('100% - col-lg-12', 'terraclassifieds'); ?></option>
					<option value='11'<?php echo($price_large_size == '11') ? 'selected' : ''; ?>><?php _e('91% - col-lg-11', 'terraclassifieds'); ?></option>
					<option value='10'<?php echo($price_large_size == '10') ? 'selected' : ''; ?>><?php _e('83% - col-lg-10', 'terraclassifieds'); ?></option>
					<option value='9'<?php echo($price_large_size == '9') ? 'selected' : ''; ?>><?php _e('75% - col-lg-9', 'terraclassifieds'); ?></option>
					<option value='8'<?php echo($price_large_size == '8') ? 'selected' : ''; ?>><?php _e('66% - col-lg-8', 'terraclassifieds'); ?></option>
					<option value='7'<?php echo($price_large_size == '7') ? 'selected' : ''; ?>><?php _e('58% - col-lg-7', 'terraclassifieds'); ?></option>
					<option value='6'<?php echo($price_large_size == '6') ? 'selected' : ''; ?>><?php _e('50% - col-lg-6', 'terraclassifieds'); ?></option>
					<option value='5'<?php echo($price_large_size == '5') ? 'selected' : ''; ?>><?php _e('41% - col-lg-5', 'terraclassifieds'); ?></option>
					<option value='4'<?php echo($price_large_size == '4') ? 'selected' : ''; ?>><?php _e('33% - col-lg-4', 'terraclassifieds'); ?></option>
					<option value='3'<?php echo($price_large_size == '3') ? 'selected' : ''; ?>><?php _e('25% - col-lg-3', 'terraclassifieds'); ?></option>
					<option value='2'<?php echo($price_large_size == '2') ? 'selected' : ''; ?>><?php _e('16% - col-lg-2', 'terraclassifieds'); ?></option>
					<option value='1'<?php echo($price_large_size == '1') ? 'selected' : ''; ?>><?php _e('8% - col-lg-1', 'terraclassifieds'); ?></option>
				</select>
			</p>
			
			<p class="terraclassifieds-screen-size">
				<label for="<?php echo $this -> get_field_id('price_desktop_size'); ?>"><?php _e('Desktop ( ≥ 992px )', 'terraclassifieds'); ?></label>
				<select class="widefat" id="<?php echo $this -> get_field_id('price_desktop_size'); ?>" name="<?php echo $this -> get_field_name('price_desktop_size'); ?>" style="width:100%;">
					<option value='12'<?php echo($price_desktop_size == '12') ? 'selected' : ''; ?>><?php _e('100% - col-md-12', 'terraclassifieds'); ?></option>
					<option value='11'<?php echo($price_desktop_size == '11') ? 'selected' : ''; ?>><?php _e('91% - col-md-11', 'terraclassifieds'); ?></option>
					<option value='10'<?php echo($price_desktop_size == '10') ? 'selected' : ''; ?>><?php _e('83% - col-md-10', 'terraclassifieds'); ?></option>
					<option value='9'<?php echo($price_desktop_size == '9') ? 'selected' : ''; ?>><?php _e('75% - col-md-9', 'terraclassifieds'); ?></option>
					<option value='8'<?php echo($price_desktop_size == '8') ? 'selected' : ''; ?>><?php _e('66% - col-md-8', 'terraclassifieds'); ?></option>
					<option value='7'<?php echo($price_desktop_size == '7') ? 'selected' : ''; ?>><?php _e('58% - col-md-7', 'terraclassifieds'); ?></option>
					<option value='6'<?php echo($price_desktop_size == '6') ? 'selected' : ''; ?>><?php _e('50% - col-md-6', 'terraclassifieds'); ?></option>
					<option value='5'<?php echo($price_desktop_size == '5') ? 'selected' : ''; ?>><?php _e('41% - col-md-5', 'terraclassifieds'); ?></option>
					<option value='4'<?php echo($price_desktop_size == '4') ? 'selected' : ''; ?>><?php _e('33% - col-md-4', 'terraclassifieds'); ?></option>
					<option value='3'<?php echo($price_desktop_size == '3') ? 'selected' : ''; ?>><?php _e('25% - col-md-3', 'terraclassifieds'); ?></option>
					<option value='2'<?php echo($price_desktop_size == '2') ? 'selected' : ''; ?>><?php _e('16% - col-md-2', 'terraclassifieds'); ?></option>
					<option value='1'<?php echo($price_desktop_size == '1') ? 'selected' : ''; ?>><?php _e('8% - col-md-1', 'terraclassifieds'); ?></option>
				</select>
			</p>
			
			<p class="terraclassifieds-screen-size">
				<label for="<?php echo $this -> get_field_id('price_tablet_size'); ?>"><?php _e('Tablet ( ≥ 768px )', 'terraclassifieds'); ?></label>
				<select class="widefat" id="<?php echo $this -> get_field_id('price_tablet_size'); ?>" name="<?php echo $this -> get_field_name('price_tablet_size'); ?>" style="width:100%;">
					<option value='12'<?php echo($price_tablet_size == '12') ? 'selected' : ''; ?>><?php _e('100% - col-sm-12', 'terraclassifieds'); ?></option>
					<option value='11'<?php echo($price_tablet_size == '11') ? 'selected' : ''; ?>><?php _e('91% - col-sm-11', 'terraclassifieds'); ?></option>
					<option value='10'<?php echo($price_tablet_size == '10') ? 'selected' : ''; ?>><?php _e('83% - col-sm-10', 'terraclassifieds'); ?></option>
					<option value='9'<?php echo($price_tablet_size == '9') ? 'selected' : ''; ?>><?php _e('75% - col-sm-9', 'terraclassifieds'); ?></option>
					<option value='8'<?php echo($price_tablet_size == '8') ? 'selected' : ''; ?>><?php _e('66% - col-sm-8', 'terraclassifieds'); ?></option>
					<option value='7'<?php echo($price_tablet_size == '7') ? 'selected' : ''; ?>><?php _e('58% - col-sm-7', 'terraclassifieds'); ?></option>
					<option value='6'<?php echo($price_tablet_size == '6') ? 'selected' : ''; ?>><?php _e('50% - col-sm-6', 'terraclassifieds'); ?></option>
					<option value='5'<?php echo($price_tablet_size == '5') ? 'selected' : ''; ?>><?php _e('41% - col-sm-5', 'terraclassifieds'); ?></option>
					<option value='4'<?php echo($price_tablet_size == '4') ? 'selected' : ''; ?>><?php _e('33% - col-sm-4', 'terraclassifieds'); ?></option>
					<option value='3'<?php echo($price_tablet_size == '3') ? 'selected' : ''; ?>><?php _e('25% - col-sm-3', 'terraclassifieds'); ?></option>
					<option value='2'<?php echo($price_tablet_size == '2') ? 'selected' : ''; ?>><?php _e('16% - col-sm-2', 'terraclassifieds'); ?></option>
					<option value='1'<?php echo($price_tablet_size == '1') ? 'selected' : ''; ?>><?php _e('8% - col-sm-1', 'terraclassifieds'); ?></option>
				</select>
			</p>
			
			<p class="terraclassifieds-screen-size">
				<label for="<?php echo $this -> get_field_id('price_phone_size'); ?>"><?php _e('Phone ( < 768px )', 'terraclassifieds'); ?></label>
				<select class="widefat" id="<?php echo $this -> get_field_id('price_phone_size'); ?>" name="<?php echo $this -> get_field_name('price_phone_size'); ?>" style="width:100%;">
					<option value='12'<?php echo($price_phone_size == '12') ? 'selected' : ''; ?>><?php _e('100% - col-xs-12', 'terraclassifieds'); ?></option>
					<option value='11'<?php echo($price_phone_size == '11') ? 'selected' : ''; ?>><?php _e('91% - col-xs-11', 'terraclassifieds'); ?></option>
					<option value='10'<?php echo($price_phone_size == '10') ? 'selected' : ''; ?>><?php _e('83% - col-xs-10', 'terraclassifieds'); ?></option>
					<option value='9'<?php echo($price_phone_size == '9') ? 'selected' : ''; ?>><?php _e('75% - col-xs-9', 'terraclassifieds'); ?></option>
					<option value='8'<?php echo($price_phone_size == '8') ? 'selected' : ''; ?>><?php _e('66% - col-xs-8', 'terraclassifieds'); ?></option>
					<option value='7'<?php echo($price_phone_size == '7') ? 'selected' : ''; ?>><?php _e('58% - col-xs-7', 'terraclassifieds'); ?></option>
					<option value='6'<?php echo($price_phone_size == '6') ? 'selected' : ''; ?>><?php _e('50% - col-xs-6', 'terraclassifieds'); ?></option>
					<option value='5'<?php echo($price_phone_size == '5') ? 'selected' : ''; ?>><?php _e('41% - col-xs-5', 'terraclassifieds'); ?></option>
					<option value='4'<?php echo($price_phone_size == '4') ? 'selected' : ''; ?>><?php _e('33% - col-xs-4', 'terraclassifieds'); ?></option>
					<option value='3'<?php echo($price_phone_size == '3') ? 'selected' : ''; ?>><?php _e('25% - col-xs-3', 'terraclassifieds'); ?></option>
					<option value='2'<?php echo($price_phone_size == '2') ? 'selected' : ''; ?>><?php _e('16% - col-xs-2', 'terraclassifieds'); ?></option>
					<option value='1'<?php echo($price_phone_size == '1') ? 'selected' : ''; ?>><?php _e('8% - col-xs-1', 'terraclassifieds'); ?></option>
				</select>
			</p>
	
		<?php }
	
		function update( $new_instance, $old_instance ) {
			
			$instance = $old_instance;
			$instance['title']           = strip_tags( $new_instance['title'] );
			$instance['category_filter'] = strip_tags( $new_instance['category_filter'] );
			$instance['only_first_level'] = strip_tags( $new_instance['only_first_level'] );
			$instance['location']    = esc_attr( $new_instance['location'] );
			$instance['type']    = esc_attr( $new_instance['type'] );
			$instance['price']    = esc_attr( $new_instance['price'] );
			$instance['location_option']    = esc_attr( $new_instance['location_option'] );
			$instance['location_list_hierarchical']    = esc_attr( $new_instance['location_list_hierarchical'] );
			$instance['search_large_size']    = esc_attr( $new_instance['search_large_size'] );
			$instance['search_desktop_size']    = esc_attr( $new_instance['search_desktop_size'] );
			$instance['search_tablet_size']    = esc_attr( $new_instance['search_tablet_size'] );
			$instance['search_phone_size']    = esc_attr( $new_instance['search_phone_size'] );
			$instance['location_large_size']    = esc_attr( $new_instance['location_large_size'] );
			$instance['location_desktop_size']    = esc_attr( $new_instance['location_desktop_size'] );
			$instance['location_tablet_size']    = esc_attr( $new_instance['location_tablet_size'] );
			$instance['location_phone_size']    = esc_attr( $new_instance['location_phone_size'] );
			$instance['category_large_size']    = esc_attr( $new_instance['category_large_size'] );
			$instance['category_desktop_size']    = esc_attr( $new_instance['category_desktop_size'] );
			$instance['category_tablet_size']    = esc_attr( $new_instance['category_tablet_size'] );
			$instance['category_phone_size']    = esc_attr( $new_instance['category_phone_size'] );
			$instance['price_large_size']    = esc_attr( $new_instance['price_large_size'] );
			$instance['price_desktop_size']    = esc_attr( $new_instance['price_desktop_size'] );
			$instance['price_tablet_size']    = esc_attr( $new_instance['price_tablet_size'] );
			$instance['price_phone_size']    = esc_attr( $new_instance['price_phone_size'] );
			return $instance;
			
		}
	
	}
	


}