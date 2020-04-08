<!-- TerraClassifieds - default archive -->
<?php 

get_header(); 
$currency = terraclassifieds_get_option( '_tc_advert_currency', '$' );
$unit_position = terraclassifieds_get_option( '_tc_unit_position', 1 );
$text_limit = terraclassifieds_get_option( '_tc_category_view_text_limit', '10' );
$add_to_favourites = terraclassifieds_get_option( '_tc_category_view_add_to_favourites', 0 );
$use_locations = terraclassifieds_get_option( '_tc_use_locations', 1 );
$show_location = terraclassifieds_get_option( '_tc_show_location', 1 );
$use_types = terraclassifieds_get_option( '_tc_use_types', 0 );
$use_selling_types = terraclassifieds_get_option( '_tc_use_selling_types', 1 );
$show_hits = terraclassifieds_get_option( '_tc_hits', 0 );
$show_author = terraclassifieds_get_option( '_tc_category_view_ad_author', 1 );
$no_image = terraclassifieds_get_option( '_tc_image_no_image', 0 );
if(!empty($no_image)){
    $no_image_id = attachment_url_to_postid($no_image);
}
$use_images = terraclassifieds_get_option( '_tc_image_use_images', 1 );
?>

<div class="terraclassifieds-container terraclassifieds-archive">

	<?php //if( is_search() ) { ?>
		<div class="terraclassifieds-search">
		<?php add_filter('get_search_form', 'terraclassifiedsSearchFormInit'); ?>
		<?php get_search_form(); ?>
		<?php remove_filter('get_search_form', 'terraclassifiedsSearchFormInit'); ?>
		</div>
	<?php //} ?>

	<?php if( is_tax() ) { ?>
	<div class="terraclassifieds-categories">
		<?php

			$term = get_queried_object();

			$cat_name = ( !empty($term->name) ) ? $term->name : false;
			$cat_description = ( !empty($term->description) ) ? $term->description : false;
			$cat_image = ( !empty($term->term_id) ) ? get_term_meta( $term->term_id, '_tc_cat_image', true ) : false;
			$cat_image_alt = ( !empty($term->name) ) ? $term->name : '';

			$show_cat = new stdClass();
			$show_cat->name = false;
			$show_cat->desc = false;
			$show_cat->image = false;
			$show_cat->subcat = false;
			
			$show_cat_arr = terraclassifieds_get_option( '_tc_show_cat', false );
			if(!empty($show_cat_arr)){
				$show_cat->name = ( in_array( 'name' , $show_cat_arr ) && $cat_name ) ? true : false;
				$show_cat->desc = ( in_array( 'desc' , $show_cat_arr ) && $cat_description ) ? true : false;
				$show_cat->image = ( in_array( 'image' , $show_cat_arr ) && $cat_image ) ? true : false;
				$show_cat->subcat = ( in_array( 'subcat' , $show_cat_arr ) ) ? true : false;
			}

			$childrens = '';
			if( $show_cat->subcat ) {
				$term_children = ( !empty($term->term_id) ) ? get_term_children( $term->term_id, 'ad_category' ) : false;
				if( !empty($term_children) ) {
					$parent_term_id = $term->term_id;
					$childrens .= '<ul class="subcategory-list">';
					foreach ( $term_children as $child ) {
						$term = get_term_by( 'id', $child, 'ad_category' );
						if( $parent_term_id == $term->parent ) {
							$childrens .= '<li><a href="' . get_term_link( $child, 'ad_category' ) . '">' . $term->name . '</a></li>';
						}
					}
					$childrens .= '</ul>';
				}
			}

		?>

		<?php if( $show_cat->image ) { ?>
			<div class="terraclassifieds-category-image"><img src="<?php echo esc_url($cat_image); ?>" alt="<?php echo esc_attr($cat_image_alt); ?>"></div>
		<?php } ?>

		<?php if( $show_cat->name || $show_cat->desc || !empty($childrens) ) { ?>
			<div class="terraclassifieds-category-content">
				<?php if( $show_cat->name ) { ?>
					<h1><?php echo esc_attr($cat_name) ?></h1>
				<?php } ?>

				<?php if( $show_cat->desc ) { ?>
					<div class="terraclassifieds-category-desc"><?php echo $cat_description; ?></div>
				<?php } ?>

				<?php echo $childrens; ?>
			</div>
		<?php } ?>

	</div>
	<?php } ?>

	<div class="terraclassifieds-items">
	
	<?php terraclassifieds_breadcrumbs(); ?>
		
	<?php if ( !have_posts() ) : ?>
		<?php if ( is_search() ) : ?>
			<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'terraclassifieds' ); ?></p>
		<?php else : ?>
			<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'terraclassifieds' ); ?></p>
		<?php endif; ?>
	<?php endif; ?>
	
	<?php
	global $wp_query;
	$args = array_merge( $wp_query->query_vars, array( 'post_type' => 'classified', 'post_status' => 'publish' ) );
	query_posts( $args );
    while ( have_posts() ) : the_post();
	
	$price = get_post_meta( get_the_ID(), '_tc_price', true );
	$sell_type = get_post_meta( get_the_ID(), '_tc_sell_type', 'price' );
	$ad_time = get_the_time('U');
	$hits = get_post_meta( get_the_ID(), '_terraclassifieds_popular_posts_count', true );
	if(empty($hits)){
	    $hits = 0;
	}
	?>
	
		<article <?php post_class(); ?>>
			<div class="terraclassifieds-item terraclassifieds-clear">
				<?php
				$gallery = get_post_meta( get_the_ID(), '_tc_gallery', true );
				$tcf_image_archive_size_height = terraclassifieds_get_option_base_functions( '_tc_image_archive_height', '145' );
				$tcf_image_archive_size_width = terraclassifieds_get_option_base_functions( '_tc_image_archive_width', '170' );
				if ($use_images && (!empty($gallery) || !empty($no_image))) { ?>
					<div class="terraclassifieds-image" style="width: <?php echo esc_attr($tcf_image_archive_size_width).'px;' ?>">
						<a href="<?php echo get_permalink($post->ID); ?>">
							<?php //cmb2_output_file_list_first_image( '_tc_gallery', 'tcf-archive' ); ?>
							<?php
								$file_list_meta_key = '_tc_gallery';
								$img_size = 'tcf-archive';
								// Get the list of files
								$files = get_post_meta( get_the_ID(), $file_list_meta_key, 1 );
								echo '<div class="terraclassifieds-gallery" style="min-height: '.esc_attr($tcf_image_archive_size_height).'px;">';
								$files_counter = 0;
								// Loop through them and output an image
								foreach ( (array) $files as $attachment_id => $attachment_url ) {
									if($files_counter < 1){
										echo '<div class="terraclassifieds-gallery-element">';
											echo '<div class="terraclassifieds-gallery-element-in">';
												if($add_to_favourites){ ?>
													<div class="terraclassifieds-fav">
														<?php
															// retrieve the total love count for this item
															$love_count = li_get_love_count($post->ID);
															if(!tcf_user_has_liked_post($user_ID, get_the_ID())) {
																echo '<span class="fav-it" data-post-id="' . get_the_ID() . '" data-user-id="' .  esc_attr($user_ID) . '">&nbsp;</span>';
															} else {
																echo '<span class="liked" data-post-id="' . get_the_ID() . '" data-user-id="' .  esc_attr($user_ID) . '"></span>';
															}
														?>
													</div>
												<?php }
												if (!empty($gallery)){
												    echo wp_get_attachment_image( $attachment_id, $img_size );
												} else if(!empty($no_image)) {
												    echo wp_get_attachment_image( $no_image_id, $img_size );
												}												
												
											echo '</div>';
										echo '</div>';
									}
									$files_counter++;
								}
								echo '</div>';
							?>
						</a>
					</div>
				<?php } ?>

				<div class="terraclassifieds-content">

					<header class="terraclassifieds-header">
						<?php
						  if($add_to_favourites && !$use_images){ ?>
								<div class="terraclassifieds-fav">
    								<?php
    									// retrieve the total love count for this item
    									$love_count = li_get_love_count($post->ID);
    									if(!tcf_user_has_liked_post($user_ID, get_the_ID())) {
    										echo '<span class="fav-it" data-post-id="' . get_the_ID() . '" data-user-id="' .  esc_attr($user_ID) . '">&nbsp;</span>';
    									} else {
    										echo '<span class="liked" data-post-id="' . get_the_ID() . '" data-user-id="' .  esc_attr($user_ID) . '"></span>';
    									}
    								?>
    							</div>
							<?php }
						?>
						<h2 class="terraclassifieds-title"><a href="<?php echo get_permalink($post->ID); ?>"><?php the_title(); ?></a></h2>
						<?php if($use_types && has_term( '', 'ad_type' )) { ?>
							<span class="terraclassifieds-types">
								<?php
									$terraclassifieds_types = get_the_terms( $post->ID, 'ad_type' );
									foreach($terraclassifieds_types as $terraclassifieds_type) {
										echo '<span class="terraclassifieds-type terraclassifieds-type-' . $terraclassifieds_type->slug . '">'.$terraclassifieds_type->name.'</span>';
									}
								?>
							</span>
						<?php } ?>
					</header>

					<div class="terraclassifieds-category">
						<?php the_terms($post->ID, 'ad_category') ?>
					</div>
				
					<?php if( has_term( '', 'ad_location' ) && $show_location && $use_locations) {
						$terraclassifieds_locations = get_the_term_list( $post->ID, 'ad_location' ); ?>
						<div class="terraclassifieds-location">
							<span class="terraclassifieds-value">
								<i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo $terraclassifieds_locations; ?>
							</span>
						</div>
					<?php } ?>

					<div class="terraclassifieds-desc">
						<?php terraclassifieds_excerpt($text_limit, '&hellip;', false, true); ?>
					</div>

				</div>

				<div class="terraclassifieds-details">
					
					<?php if( $use_selling_types && ((!empty($price) || $price == '0') || $sell_type == 'for_free' || $sell_type == 'exchange') ) { ?>
					<div class="terraclassifieds-price">
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

					<div class="terraclassifieds-date">
						<?php echo terraclassifieds_date_ago( $ad_time ); ?>
					</div>
					
					<?php if($show_author){ ?>
    					<div class="terraclassifieds-author">
    						<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
    							<?php echo get_the_author_meta( 'display_name' ); ?>
    						</a>
    					</div>
					<?php } ?>
					
					<?php if($show_hits){ ?>
						<div class="terraclassifieds-hits">
							<i class="fa fa-eye" aria-hidden="true"></i> <?php echo $hits; ?>
						</div>
					<?php } ?>

				</div>

			</div>
		</article>
	
	<?php endwhile; // end of the loop. ?>
	</div>
	<?php terraclassifieds_pagination(); ?>

</div>

<?php get_footer(); ?>