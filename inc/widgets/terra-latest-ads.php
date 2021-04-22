<?php
/*--------------------------------------------------------------
 Copyright (C) pixelemu.com
 License: https://www.pixelemu.com/company/license PixelEmu Proprietary Use License
 Website: https://www.pixelemu.com
 Support: info@pixelemu.com
---------------------------------------------------------------*/

if ( ! class_exists( 'TerraClassifiedsLatestAds' ) ) {

	class TerraClassifiedsLatestAds extends WP_Widget {

		function __construct() {
			$widget_ops = array(
				'classname'   => 'terraclassifieds-latest-ads',
				'description' => __( 'Display Latest Ads.', 'terraclassifieds' )
			);
			parent::__construct( 'TerraClassifiedsLatestAds', __( 'TerraClassifieds Latest Ads', 'terraclassifieds' ), $widget_ops );
		}

		function widget( $args, $instance ) {

			extract( $args );

			$title = ( ! empty( $instance['title'] ) ) ? apply_filters( 'widget_title', $instance['title'] ) : false;

			$total_to_show = ( ! empty( $instance['total_to_show'] ) ) ? esc_attr( $instance['total_to_show'] ) : 3;
			$desc_limit    = ( isset( $instance['desc_limit'] ) && is_numeric( $instance['desc_limit'] ) ) ? esc_attr( $instance['desc_limit'] ) : 'full';
			$order         = ( ! empty( $instance['order'] ) ) ? esc_attr( $instance['order'] ) : 'desc';
			$imgsize       = ( ! empty( $instance['imgsize'] ) ) ? esc_attr( $instance['imgsize'] ) : 'thumbnail';
			$show_price    = ( ! empty( $instance['show_price'] ) ) ? true : false;
			$show_types    = ( ! empty( $instance['show_types'] ) ) ? true : false;
			$add_to_favourites    = ( ! empty( $instance['add_to_favourites'] ) ) ? true : false;
			$columns       = ( ! empty( $instance['columns'] ) ) ? (int)$instance['columns'] : '1';
			
			//sort
			if ( $order == 'alpha' ) {
				$orderby  = 'name';
				$ordering = 'asc';
			} else {
				$orderby  = 'id';
				$ordering = $order;
			}

			$terra_ads_args = array(
				'post_type'      => 'classified',
				'orderby'        => $orderby,
				'order'          => $ordering,
				'posts_per_page' => $total_to_show,
			    'post_status' => 'publish'
			);

			$terra_ads_query = new WP_Query( $terra_ads_args );

			echo $before_widget;

			if ( $title ):
				echo $before_title;
				echo $title;
				echo $after_title;
			endif;

			$count = 0;

			switch ($columns) {
				case 1:
					$columns_class = 'columns-1';
					break;
				case 2:
					$columns_class = 'columns-2';
					break;
				case 3:
					$columns_class = 'columns-3';
					break;
				case 4:
					$columns_class = 'columns-4';
					break;
				case 5:
					$columns_class = 'columns-5';
					break;
				case 6:
					$columns_class = 'columns-6';
					break;
				case 7:
					$columns_class = 'columns-7';
					break;
				case 8:
					$columns_class = 'columns-8';
					break;
				default:
					$columns_class = 'columns-1';
			}

			if ( $terra_ads_query->have_posts() ): ?>

				<div class="terraclassifieds-latest-ads">

					<ul class="terraclassifieds-latest-ads-list <?php echo esc_attr($columns_class); ?>">
						<?php
						$currency = terraclassifieds_get_option( '_tc_advert_currency', '$' );
						$unit_position = terraclassifieds_get_option( '_tc_unit_position', 1 );
						$use_types = terraclassifieds_get_option( '_tc_use_types', 0 );
						$use_selling_types = terraclassifieds_get_option( '_tc_use_selling_types', 1 );
						$no_image = terraclassifieds_get_option( '_tc_image_no_image', 0 );
						if(!empty($no_image)){
						    $no_image_id = attachment_url_to_postid($no_image);
						}
						$use_images = terraclassifieds_get_option( '_tc_image_use_images', 1 );
						
						while ( $terra_ads_query->have_posts() ): $terra_ads_query->the_post();

							$count ++;

							$price = get_post_meta( get_the_ID(), '_tc_price', true );
							$sell_type = get_post_meta( get_the_ID(), '_tc_sell_type', 'price' );

							?>
							<li class="terra-<?php echo esc_attr($count); ?> terraclassifieds-ad">

								<div class="terraclassifieds-ad-in terraclassifieds-clear">
									<?php
									$gallery = get_post_meta( get_the_ID(), '_tc_gallery', true );
									if ($use_images && (!empty($gallery) || !empty($no_image))) {

										echo '<div class="terraclassifieds-image"><a href="' . get_the_permalink() .'">'; ?>
											<?php if($add_to_favourites){ ?>
												<div class="terraclassifieds-fav">
													<?php
														$user_ID = get_current_user_id();
														// retrieve the total love count for this item
														$love_count = li_get_love_count(get_the_ID());
														$fav_redirect = ( !is_user_logged_in() ) ? ' data-redirect="' . terraclassifieds_get_login_url() . '"' : '';
														if(!tcf_user_has_liked_post($user_ID, get_the_ID())) {
															echo '<span class="fav-it" data-post-id="' . get_the_ID() . '" data-user-id="' .  esc_attr($user_ID) . '"' . $fav_redirect . '>&nbsp;</span>';
														} else {
															echo '<span class="liked" data-post-id="' . get_the_ID() . '" data-user-id="' .  esc_attr($user_ID) . '"></span>';
														}
													?>
												</div>
											<?php }
											if(!empty($gallery)){
											    cmb2_output_file_list_first_image( '_tc_gallery', $imgsize );
											} else if(!empty($no_image)){
											    echo wp_get_attachment_image( $no_image_id, $imgsize );
											}
											
										echo '</a></div>';

									} ?>
									

									<div class="terraclassifieds-desc">

										<h3 class="terraclassifieds-title">
											<a href="<?php the_permalink(); ?>">
												<?php the_title(); ?>
											</a>
										</h3>
											
										<?php if($use_types && $show_types && has_term( '', 'ad_type' )) { ?>
											<span class="terraclassifieds-types">
												<?php
													$terraclassifieds_types = get_the_terms( get_the_ID(), 'ad_type' );
													foreach($terraclassifieds_types as $terraclassifieds_type) {
														echo '<span class="terraclassifieds-type terraclassifieds-type-' . $terraclassifieds_type->slug . '">'.$terraclassifieds_type->name.'</span>';
													}
												?>
											</span>
										<?php } ?>
									
										<?php if ( $desc_limit ) { ?>
											<div class="terraclassifieds-text"><?php terraclassifieds_excerpt( $desc_limit, '&hellip;', false, true ); ?></div>
										<?php } ?>

										<?php if($use_selling_types && $show_price && ( (!empty($price) || $price == '0') || $sell_type == 'for_free' || $sell_type == 'exchange' )) { ?>
										<div class="terraclassifieds-price">
											<span class="terraclassifieds-label"><?php _e('Price:', 'terraclassifieds'); ?></span>
											<?php if($sell_type == 'for_free'){ ?>
												<?php echo __( 'For Free', 'terraclassifieds' ); ?>
											<?php } else if($sell_type == 'exchange'){ ?>
												<?php echo __( 'Exchange', 'terraclassifieds' ); ?>
											<?php } else { ?>
												<?php if(!empty($currency) && $unit_position == 0){ ?>
													<?php echo esc_attr($currency); ?>
												<?php } ?>
												<?php terraclassifiedsPriceFormat($price); ?>
												<?php if(!empty($currency) && $unit_position == 1){ ?>
													<?php echo esc_attr($currency); ?>
												<?php } ?>
											<?php } ?>
										</div>
										<?php } ?>

									</div>

								</div>

							</li>

						<?php endwhile; ?>

					</ul>

				</div>

				<?php wp_reset_query();
			else: ?>
				<div class="terraclassifieds-alert"><?php _e( 'No ads.', 'terraclassifieds' ) ?></div>
			<?php endif;


			echo $after_widget;
		}

		function form( $instance ) {

			$instance = wp_parse_args( (array) $instance, array(
					'title'         => '',
					'total_to_show' => 3,
					'desc_limit'    => 50,
					'order'         => 'desc',
					'imgsize'       => 'thumbnail',
					'show_price'    => 0,
					'show_types'    => 0,
					'add_to_favourites'    => 0,
					'columns'       => 1,
				)
			);

			$title         = esc_attr( $instance['title'] );
			$total_to_show = esc_attr( $instance['total_to_show'] );
			$desc_limit    = isset( $instance['desc_limit'] ) ? (int) $instance['desc_limit'] : '0';
			$order         = esc_attr( $instance['order'] );
			$show_price    = isset( $instance['show_price'] ) ? (bool) $instance['show_price'] : false;
			$show_types    = isset( $instance['show_types'] ) ? (bool) $instance['show_types'] : false;
			$add_to_favourites = isset( $instance['add_to_favourites'] ) ? (bool) $instance['add_to_favourites'] : false;
			$columns       = esc_attr( $instance['columns'] );

			?>

			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Widget Title', 'terraclassifieds' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr($title); ?>"/>
			</p>
			
			<p>
				<label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php _e( 'Order', 'terraclassifieds' ); ?></label>
				<select class="widefat" id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>" style="width:100%;">
					<option value='desc'<?php echo ( $order == 'desc' ) ? 'selected' : ''; ?>><?php _e( 'Latest first', 'terraclassifieds' ); ?></option>
					<option value='asc'<?php echo ( $order == 'asc' ) ? 'selected' : ''; ?>><?php _e( 'Oldest first', 'terraclassifieds' ); ?></option>
					<option value='alpha'<?php echo ( $order == 'alpha' ) ? 'selected' : ''; ?>><?php _e( 'Alphabetical', 'terraclassifieds' ); ?></option>
				</select>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'total_to_show' ); ?>"><?php _e( 'Number of ads to show', 'terraclassifieds' ); ?></label>
				<input id="<?php echo $this->get_field_id( 'total_to_show' ); ?>" name="<?php echo $this->get_field_name( 'total_to_show' ); ?>" type="text" value="<?php echo esc_attr($total_to_show); ?>" size="3"/>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'desc_limit' ); ?>"><?php _e( 'Description limit <small>(in words, 0 to disable)</small>', 'terraclassifieds' ); ?></label>
				<input id="<?php echo $this->get_field_id( 'desc_limit' ); ?>" name="<?php echo $this->get_field_name( 'desc_limit' ); ?>" type="text" value="<?php echo esc_attr($desc_limit); ?>" size="3"/>
			</p>

			<p class="terraclassifieds-panels terraclassifieds-panels-img-size">
				<label for="<?php echo $this->get_field_id( 'imgsize' ); ?>"><?php _e( 'Thumbnail Size:', 'terraclassifieds' ); ?></label>
				<select name="<?php echo $this->get_field_name( 'imgsize' ); ?>" id="<?php echo $this->get_field_id( 'imgsize' ); ?>">
					<?php
					//get all available post sizes (except custom created)
					$image_sizes = get_intermediate_image_sizes();
					foreach ( $image_sizes as $size_name ):
						?>
						<option value="<?php echo esc_attr($size_name); ?>"<?php selected( $instance['imgsize'], $size_name ); ?>><?php echo esc_attr($size_name); ?></option>
						<?php
					endforeach;
					?>
				</select>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('columns'); ?>"><?php _e('Columns :', 'terraclassifieds'); ?></label>
				<select name="<?php echo $this->get_field_name('columns'); ?>" id="<?php echo $this->get_field_id('columns'); ?>">
					<option value="1"<?php selected( $columns, '1' ); ?>><?php _e('1 column', 'terraclassifieds'); ?></option>
					<option value="2"<?php selected( $columns, '2' ); ?>><?php _e('2 columns', 'terraclassifieds'); ?></option>
					<option value="3"<?php selected( $columns, '3' ); ?>><?php _e('3 columns', 'terraclassifieds'); ?></option>
					<option value="4"<?php selected( $columns, '4' ); ?>><?php _e('4 columns', 'terraclassifieds'); ?></option>
					<option value="5"<?php selected( $columns, '5' ); ?>><?php _e('5 columns', 'terraclassifieds'); ?></option>
					<option value="6"<?php selected( $columns, '6' ); ?>><?php _e('6 columns', 'terraclassifieds'); ?></option>
					<option value="7"<?php selected( $columns, '7' ); ?>><?php _e('7 columns', 'terraclassifieds'); ?></option>
					<option value="8"<?php selected( $columns, '8' ); ?>><?php _e('8 columns', 'terraclassifieds'); ?></option>
				</select>
			</p>

			<p>
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('show_price'); ?>" name="<?php echo $this->get_field_name('show_price'); ?>"<?php checked( $show_price ); ?> />
				<label for="<?php echo $this->get_field_id('show_price'); ?>"><?php _e( 'Show price', 'terraclassifieds' ); ?></label><br/>
			</p>
			
			<p>
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('show_types'); ?>" name="<?php echo $this->get_field_name('show_types'); ?>"<?php checked( $show_types ); ?> />
				<label for="<?php echo $this->get_field_id('show_types'); ?>"><?php _e( 'Show types', 'terraclassifieds' ); ?></label><br/>
			</p>
			
			<p>
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('add_to_favourites'); ?>" name="<?php echo $this->get_field_name('add_to_favourites'); ?>"<?php checked( $add_to_favourites ); ?> />
				<label for="<?php echo $this->get_field_id('add_to_favourites'); ?>"><?php _e( 'Show add to favourites', 'terraclassifieds' ); ?></label><br/>
			</p>

		<?php }

		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;

			$instance['title']         = strip_tags( $new_instance['title'] );
			$instance['total_to_show'] = esc_attr( $new_instance['total_to_show'] );
			$instance['desc_limit']    = esc_attr( $new_instance['desc_limit'] );
			$instance['order']         = esc_attr( $new_instance['order'] );
			$instance['imgsize']       = esc_attr( $new_instance['imgsize'] );
			$instance['show_price']    = esc_attr( $new_instance['show_price'] );
			$instance['show_types']    = esc_attr( $new_instance['show_types'] );
			$instance['add_to_favourites']    = esc_attr( $new_instance['add_to_favourites'] );
			$instance['columns']       = esc_attr( $new_instance['columns'] );

			return $instance;
		}

	}
}
?>
