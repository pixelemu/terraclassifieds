<!-- TerraClassifieds - default archive -->
<?php 

get_header(); 
$currency = terraclassifieds_get_option( '_tc_advert_currency', '$' );
$unit_position = terraclassifieds_get_option( '_tc_unit_position', 1 );
$user_id = get_the_author_meta( 'ID' );
$avatar_url = get_user_meta( $user_id, '_tc_avatar', 1 );
$tcf_image_archive_size_width = terraclassifieds_get_option_base_functions( '_tc_image_archive_width', '170' );
$use_types = terraclassifieds_get_option( '_tc_use_types', 0 );
$show_username = terraclassifieds_get_option( '_tc_author_page_username', 0 );
$show_hits = terraclassifieds_get_option( '_tc_hits', 0 );
$no_image = terraclassifieds_get_option( '_tc_image_no_image', 0 );
if(!empty($no_image)){
    $no_image_id = attachment_url_to_postid($no_image);
}
?>

<div class="terraclassifieds-container terraclassifieds-archive">

	<div class="terraclassifieds-author-info clearfix">
		
		<div class="terraclassifieds-author-info-avatar">
			<?php if(!empty($avatar_url)){ ?>
				<img class="terraclassifieds-user-avatar" src="<?php echo esc_url($avatar_url); ?>" alt="user-avatar" />
			<?php } else { ?>
				<img class="terraclassifieds-user-avatar" src="<?php echo plugins_url('assets/img/default-avatar.jpg', dirname(__FILE__)); ?>" alt="user-avatar" />
			<?php } ?>
		</div>
		
		<?php if($show_username){ ?>
		<div class="terraclassifieds-author-info-user-login">
			<?php echo get_the_author_meta( 'user_login' ); ?>
		</div>
		<?php } ?>
		
		<?php if(get_the_author_meta( 'first_name' ) || get_the_author_meta( 'last_name' )){ ?>
			<div class="terraclassifieds-author-info-name">
				<?php echo get_the_author_meta( 'first_name' ); ?> <?php echo get_the_author_meta( 'last_name' ); ?>
			</div>
		<?php } ?>
		
		<?php if(get_the_author_meta( 'url' )){ ?>
			<div class="terraclassifieds-author-info-website">
				<a href="<?php echo get_the_author_meta( 'url' ); ?>" target="_blank"><?php _e('website', 'terraclassifieds'); ?></a>
			</div>
		<?php } ?>
		
		<?php if(get_the_author_meta( 'description' )){ ?>
			<div class="terraclassifieds-author-info-biographical-info">
				<?php echo get_the_author_meta( 'description' ); ?>
			</div>
		<?php } ?>
		
		<?php if(get_the_author_meta( '_tc_phone' )){ ?>
			<div class="terraclassifieds-author-info-phone">
				<?php _e('Phone', 'terraclassifieds'); ?>: <?php echo get_the_author_meta( '_tc_phone' ); ?>
			</div>
		<?php } ?>
		
	</div>

	<div class="terraclassifieds-items">
	<?php 
	$posts_per_page = get_option( 'posts_per_page' );
	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	$terra_ads_args = array(
		'post_type'      => 'classified',
		'author' => get_the_author_meta( 'ID' ),
	    'posts_per_page' => $posts_per_page,
	    'paged' => $paged,
	);

	$terra_ads_query = new WP_Query( $terra_ads_args );
	while ( $terra_ads_query->have_posts() ): $terra_ads_query->the_post();
	
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
				if (!empty($gallery) || !empty($no_image)) { ?>
					<div class="terraclassifieds-image" style="width: <?php echo esc_attr($tcf_image_archive_size_width).'px;' ?>">
						<a href="<?php echo get_permalink($post->ID); ?>">
							<?php if (!empty($gallery)){
							    cmb2_output_file_list_first_image( '_tc_gallery', 'tcf-archive' );
							} else if(!empty($no_image)) {
							    echo wp_get_attachment_image( $no_image_id, 'tcf-archive' );
							} ?>
						</a>
					</div>
				<?php } ?>

				<div class="terraclassifieds-content">

					<header class="terraclassifieds-header">
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

					<div class="terraclassifieds-desc">
						<?php terraclassifieds_excerpt(10, '&hellip;', false, true); ?>
					</div>

				</div>

				<div class="terraclassifieds-details">
					
					<?php if( !empty($price) || $sell_type == 'for_free' || $sell_type == 'exchange') { ?>
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

					<div class="terraclassifieds-author">
						<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
							<?php echo get_the_author_meta( 'display_name' ); ?>
						</a>
					</div>
					
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
	<?php terraclassifieds_pagination($terra_ads_query->max_num_pages); ?>

</div>

<?php get_footer(); ?>