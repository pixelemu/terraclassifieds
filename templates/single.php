<!-- TerraClassifieds - default single -->
<?php 

get_header();

$currency = terraclassifieds_get_option( '_tc_advert_currency', '$' );
$unit_position = terraclassifieds_get_option( '_tc_unit_position', 1 );
$add_to_favourites = terraclassifieds_get_option( '_tc_ad_view_add_to_favourites', 0 );
$use_locations = terraclassifieds_get_option( '_tc_use_locations', 1 );
$show_location = terraclassifieds_get_option( '_tc_show_location', 1 );
$use_address = terraclassifieds_get_option( '_tc_location_address', 0 );
$use_post_code = terraclassifieds_get_option( '_tc_location_post_code', 0 );
$use_types = terraclassifieds_get_option( '_tc_use_types', 0 );
$use_selling_types = terraclassifieds_get_option( '_tc_use_selling_types', 1 );
$show_contact_form = terraclassifieds_get_option( '_tc_ad_view_contact_form', 1 );
$show_report_abuse = terraclassifieds_get_option( '_tc_ad_view_report_abuse', 1 );
$show_phone_number = terraclassifieds_get_option( '_tc_ad_view_phone_number', 1 );
$show_website_url = terraclassifieds_get_option( '_tc_ad_view_website_url', 1 );
$show_ad_author = terraclassifieds_get_option( '_tc_ad_view_ad_author', 1 );
$show_hits = terraclassifieds_get_option( '_tc_hits', 0 );
$hits = get_post_meta( get_the_ID(), '_terraclassifieds_popular_posts_count', true );
$no_image = terraclassifieds_get_option( '_tc_image_no_image', 0 );
if(!empty($no_image)){
    $no_image_id = attachment_url_to_postid($no_image);
}
$use_images = terraclassifieds_get_option( '_tc_image_use_images', 1 );

$category = get_the_terms( $post->ID, 'ad_category' );
$category_url = '';
$category_name = '';
if(!empty($category)){
    $category_name = $category['0']->name;
    $category_slug = $category['0']->slug;
    $category_url = '&ad_category=' . $category_slug;
}

$location = get_the_terms( $post->ID, 'ad_location' );
$location_url = '';
$location_name = '';
if(!empty($location)){
    $location_name = ', ' . $location['0']->name;
    $location_slug = $location['0']->slug;
    $location_url = '&ad_location=' . $location_slug;
}

if (isset($_GET['edited'])) {
    $edited_advert = true;
} else {
    $edited_advert = false;
}

$page_edit_ad_slug = terraclassifieds_get_option( '_tc_slug_edit_advert', 'edit-ad' );
$page_edit_ad = get_page_link( get_page_by_path( $page_edit_ad_slug ) );
$permalinks_structure = get_option('permalink_structure');
if(!empty($permalinks_structure)){
    $link_join = '?';
} else {
    $link_join = '&';
}
?>
<div class="terraclassifieds-container terraclassifieds-single">

	<?php do_action('terraclassifieds_notice'); ?>

	<?php while ( have_posts() ) : the_post(); ?>
		<?php
			$price = get_post_meta( get_the_ID(), '_tc_price', true );
			$negotiable = get_post_meta( get_the_ID(), '_tc_negotiable', false );
			$sell_type = get_post_meta( get_the_ID(), '_tc_sell_type', 'price' );
			$email = get_post_meta( get_the_ID(), '_tc_email', true );
			$autor_email = get_the_author_meta("user_email");
			$autor_id = get_the_author_meta("ID");
			$post_id = get_the_ID();
			$gallery = get_post_meta( get_the_ID(), '_tc_gallery', true );
			$user_id = get_the_author_meta( 'ID' );
			$avatar_url = get_user_meta( $user_id, '_tc_avatar', 1 );
			$location_address = get_post_meta( get_the_ID(), '_tc_location_address', true );
			$location_post_code = get_post_meta( get_the_ID(), '_tc_location_post_code', true );
		?>
		<article <?php post_class(); ?>>

			<div class="terraclassifieds-top">

				<div class="terraclassifieds-content">
				
					<div class="terraclassifieds-message info archived-messsage">
						<?php _e('This item is not available. Browse the similar products in category:','terraclassifieds'); ?> <a href="<?php echo get_site_url(); ?>?s=<?php echo $category_url; ?><?php echo $location_url; ?>&post_type=classified"><?php echo $category_name . $location_name; ?></a>
					</div>
					
					<?php if($edited_advert){ ?>
    					<div class="terraclassifieds-message info just-edited-advert">
    						<?php _e('Your ad has been updated','terraclassifieds'); ?>			
    					</div>
					<?php } ?>

					<?php terraclassifieds_breadcrumbs(); ?>
					
					<?php if ($use_images && (!empty($gallery) || !empty($no_image))) { ?>
						<div class="terraclassifieds-image">
							<?php if($add_to_favourites){ ?>
								<div class="terraclassifieds-fav">
									<?php
										// retrieve the total love count for this item
										$love_count = li_get_love_count($post->ID);
										$fav_redirect = ( !is_user_logged_in() ) ? ' data-redirect="' . terraclassifieds_get_login_url() . '"' : '';
	
										if(!tcf_user_has_liked_post($user_ID, get_the_ID())) {
											echo '<span class="fav-it" data-post-id="' . get_the_ID() . '" data-user-id="' .  esc_attr($user_ID) . '"' . $fav_redirect . '>&nbsp;</span>';
										} else {
											echo '<span class="liked" data-post-id="' . get_the_ID() . '" data-user-id="' .  esc_attr($user_ID) . '"></span>';
										}
									?>
								</div>
							<?php } ?>
							<?php if (!empty($gallery)){
							    cmb2_output_file_list_first_image( '_tc_gallery', 'tcf-single' );
							} else if(!empty($no_image)) {
							    echo wp_get_attachment_image( $no_image_id, 'tcf-single' );
							} ?>
						</div>
					<?php } ?>

					<div class="terraclassifieds-content-in">
												
						<header class="terraclassifieds-page-header">
							<?php if($add_to_favourites && !$use_images){ ?>
								<div class="terraclassifieds-fav">
									<?php
										// retrieve the total love count for this item
										$love_count = li_get_love_count($post->ID);
										$fav_redirect = ( !is_user_logged_in() ) ? ' data-redirect="' . terraclassifieds_get_login_url() . '"' : '';
	
										if(!tcf_user_has_liked_post($user_ID, get_the_ID())) {

											echo '<span class="fav-it" data-post-id="' . get_the_ID() . '" data-user-id="' .  esc_attr($user_ID) . '"' . $fav_redirect . '>&nbsp;</span>';
										} else {
											echo '<span class="liked" data-post-id="' . get_the_ID() . '" data-user-id="' .  esc_attr($user_ID) . '"></span>';
										}
									?>
								</div>
							<?php } ?>
							<h1 class="terraclassifieds-main-title"><?php the_title(); ?></h1>
							<?php if($edited_advert){ ?>
								<a class="terraclassifieds-small-btn edit-btn" href="<?php echo esc_url($page_edit_ad.$link_join); ?>object_id=<?php echo get_the_ID(); ?>"><?php echo __( 'Edit', 'terraclassifieds' ); ?></a>	
							<?php } ?>
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
							<div class="terraclassifieds-single-additional">
    							<div class="terraclassifieds-publish-time"><span class="terraclassifieds-label"><?php _e('Added','terraclassifieds') ?></span> <?php the_date(); ?></div>
    							<?php if($show_hits){ ?>
    								<div class="terraclassifieds-single-separator">|</div>
    								<div class="terraclassifieds-hits"><span class="terraclassifieds-label"><?php _e('Hits:','terraclassifieds') ?></span> <?php echo $hits; ?></div>
    							<?php } ?>
    							<div class="terraclassifieds-single-separator">|</div>
    							<div class="terraclassifieds-ad-id"><span class="terraclassifieds-label"><?php _e('Advert ID:','terraclassifieds') ?></span> <?php echo $post->ID; ?></div>
							</div>

						</header>

						<div class="terraclassifieds-desc">
							<?php the_content(); ?>
						</div>
						<?php // echo gallery ?>
						<?php 
						if($use_images && !empty($gallery)){
    						if(!empty($gallery)){
    							unset($gallery[key($gallery)]); // remove first element from array
    						}
    						$files_number = count($gallery);
				        ?>
						<?php if(!empty($gallery)){ ?>
							<div class="lightSliderOuter number-images-<?php echo esc_attr($files_number); ?>">
								<ul id="vertical" class="lightSlider">
										<?php foreach ( $gallery as $key => $value ) {
											$thumb_url = wp_get_attachment_image_src($key,'thumbnail', true);
											$large_url = wp_get_attachment_image_src($key,'tcf-single', true); ?>
												<li data-thumb="<?php echo esc_url($thumb_url[0]); ?>" data-src="<?php echo esc_url($large_url[0]); ?>">
													<?php echo wp_get_attachment_image( $key, 'tcf-single' ); ?>
												</li>
										<?php } ?>
								</ul>
							</div>
						<?php } ?>
						<?php } ?>
					</div>
				</div>
				
				<div class="terraclassifieds-details">

					<?php if( $use_selling_types && ((!empty($price) || $price == '0') || $sell_type == 'for_free' || $sell_type == 'exchange') ) { ?>
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
						<?php if(!empty(sizeof($negotiable))){ ?>
							<div class="terraclassifieds-negotiable-price">
								<?php _e('negotiable','terraclassifieds');?>
							</div>
						<?php } ?>
					</div>
					<?php } ?>
					
					<?php if($show_contact_form){ ?>
					<div class="terraclassifieds-contact-form contact-form">
						<?php $contact_redirect = ( $show_contact_form == '2' && !is_user_logged_in() ) ? ' data-redirect="' . terraclassifieds_get_login_url() . '"' : ''; ?>
						<button class="terraclassifieds-btn terraclassifieds-contact-advertiser"<?php echo $contact_redirect; ?>><?php _e('Contact this advertiser', 'terraclassifieds') ?></button>
						<?php if( $show_contact_form == '1' || $show_contact_form == '2' && is_user_logged_in() ) { ?>
						<form action="<?php the_permalink(); ?>" id="terraclassifieds-contact-form"  method="post">
							
							<?php if ( !is_user_logged_in() ) { ?>
							<p class="terraclassifieds-form-username">
								<label for="terraclassifieds-username"><?php _e('Your name','terraclassifieds');?></label>
								<input type="text" id="terraclassifieds-username" name="tc_username" class="terraclassifieds-input" />
							</p>

							<p class="terraclassifieds-form-email">
								<label for="terraclassifieds-useremail"><?php _e('Your email','terraclassifieds');?></label>
								<input type="text" id="terraclassifieds-useremail" name="tc_email" class="terraclassifieds-input" />
							</p>
							<?php } ?>

							<p class="terraclassifieds-form-message">
								<label for="terraclassifieds-message"><?php _e('Message','terraclassifieds');?></label>
								<textarea id="terraclassifieds-message" name="tc_message" class="terraclassifieds-input"></textarea>
							</p>
							
							<?php if ( !is_user_logged_in() ) { ?>
								<div class="gdpr-checkboxes">
									<?php require_once(WP_PLUGIN_DIR . '/terraclassifieds/inc/security.php'); ?>
								 </div>
							 <?php } ?>
							 
							<p class="terraclassifieds-form-submit">
								<input type="hidden" name="tc_to" value="<?php echo rtrim(strtr(base64_encode($autor_email), '+/', '-_'), '='); ?>">
								<input type="hidden" name="tc_classified_author_id" value="<?php echo esc_attr($autor_id); ?>">
								<input type="hidden" name="tc_classified_id" value="<?php echo esc_attr($post_id); ?>">
								<input type="hidden" name="tc_form_action" value="1">
								<input type="submit" class="terraclassifieds-btn" name="user-submit" value="<?php _e('Send message','terraclassifieds');?>" />
							</p>

						</form>
						<?php } ?>
					</div>
					<?php } ?>
					
					<?php
					if(!empty(get_the_author_meta("_tc_phone")) && $show_phone_number){ ?>
							<div class="terraclassifieds-phone">
								<span class="terraclassifieds-label"><?php _e('Phone:', 'terraclassifieds'); ?></span>
								<span class="terraclassifieds-value">
									<?php 
									$phone_number = get_the_author_meta("_tc_phone");
									if( $show_phone_number == '2' && !is_user_logged_in() ) {
										echo substr($phone_number, 0, 3) . '&mldr;';
									} else {
										echo $phone_number;
									}
									?>
								</span>
								<?php $phone_redirect = ( $show_phone_number == '2' && !is_user_logged_in() ) ? ' data-redirect="' . terraclassifieds_get_login_url() . '"' : ''; ?>
								<a href="#" class="terraclassifieds-phone-more"<?php echo $phone_redirect; ?>><?php _e('show', 'terraclassifieds'); ?></a>

							</div>
						<?php }
					?>
					
					<?php if( has_term( '', 'ad_location' ) && $show_location && $use_locations) {
						$terraclassifieds_locations = get_the_terms( $post->ID, 'ad_location' );
						$terraclassifieds_locations_splitted = join(', ', wp_list_pluck($terraclassifieds_locations, 'name')); ?>
						<div class="terraclassifieds-location">
							<span class="terraclassifieds-label"><?php _e('Location:', 'terraclassifieds'); ?></span>
							<span class="terraclassifieds-value">
								<i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo $terraclassifieds_locations_splitted; ?>
								
								<?php if($use_address && !empty($location_address)){ ?>
									<?php echo '<br />' . $location_address; ?>
								<?php } ?>
								
								<?php if($use_post_code && !empty($location_post_code)){ ?>
									<?php echo '<br />' . $location_post_code; ?>
								<?php } ?>

							</span>
						</div>
					<?php } ?>
					
					<?php if(!empty(get_the_author_meta("user_url")) && $show_website_url){ ?>
						<div class="terraclassifieds-website">
							
							<span class="terraclassifieds-label"><?php _e('Website:', 'terraclassifieds'); ?></span>

							<span class="terraclassifieds-value">
									<?php 
									$url_address = get_the_author_meta("user_url");
									if( $show_website_url == '2' && !is_user_logged_in() ) { ?>
										<span>
										<?php echo substr($url_address, 0, 3) . '&mldr;'; ?>
										</span>
									<?php } else { ?>
										<a href="<?php echo $url_address; ?>" target="_blank">
										<?php echo $url_address; ?>
										</a>
									<?php }
									?>
								</span>
								<?php if ( $show_website_url == '2' && !is_user_logged_in() ) { ?>
								<a href="#" class="terraclassifieds-website-more" data-redirect="<?php echo terraclassifieds_get_login_url(); ?>"><?php _e('show', 'terraclassifieds'); ?></a>
								<?php } ?>

						</div>
					<?php } ?>

					<?php if($show_ad_author){ ?>
					<div class="terraclassifieds-author">
						<span class="terraclassifieds-label"><?php _e('Created by:', 'terraclassifieds'); ?></span> 
						<div class="terraclassifieds-author-in">
							<div class="terraclassifieds-author-avatar">
								<?php if(!empty($avatar_url)){ ?>
									<img class="terraclassifieds-user-avatar" src="<?php echo esc_url($avatar_url); ?>" alt="user-avatar" />
								<?php } else { ?>
									<img class="terraclassifieds-user-avatar" src="<?php echo plugins_url() . '/terraclassifieds/assets/img/default-avatar.jpg'; ?>" alt="user-avatar" />
								<?php } ?>
							</div>
							
							<?php $author_redirect = ( $show_ad_author == '2' && !is_user_logged_in() ) ? ' data-redirect="' . terraclassifieds_get_login_url() . '"' : ''; 
								$author_url = ( $show_ad_author == '2' && !is_user_logged_in() ) ? '#' : esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) );
							?>
							<a class="terraclassifieds-author-name" href="<?php echo $author_url;?>"<?php echo $author_redirect; ?>>
								<?php if((get_the_author_meta( 'user_firstname' )) || (get_the_author_meta( 'user_lastname' ))){ ?>
									<?php _e('See all ads by', 'terraclassifieds'); ?> <?php echo get_the_author_meta( 'user_firstname' ).' '.get_the_author_meta( 'user_lastname' ); ?>
								<?php } else { ?>
									<?php _e('No name', 'terraclassifieds'); ?>
								<?php } ?>
							</a>

						</div>
					</div>
					<?php } ?>
					
					<?php if($show_report_abuse){ ?>
					<div class="terraclassifieds-contact-form abuse-form">
						<?php $abuse_redirect = ( $show_report_abuse == '2' && !is_user_logged_in() ) ? ' data-redirect="' . terraclassifieds_get_login_url() . '"' : ''; ?>
						<button class="terraclassifieds-btn terraclassifieds-contact-advertiser"<?php echo $abuse_redirect; ?>><?php _e('Report abuse', 'terraclassifieds') ?></button>
						<?php if( $show_report_abuse == '1' || $show_report_abuse == '2' && is_user_logged_in() ) { ?>
						<form action="<?php the_permalink(); ?>" id="terraclassifieds-abuse-form"  method="post">
							
							<?php if ( !is_user_logged_in() ) { ?>
							<p class="terraclassifieds-form-username">
								<label for="terraclassifieds-username"><?php _e('Your name','terraclassifieds');?></label>
								<input type="text" id="terraclassifieds-username" name="tc_username_abuse" class="terraclassifieds-input" />
							</p>

							<p class="terraclassifieds-form-email">
								<label for="terraclassifieds-useremail"><?php _e('Your email','terraclassifieds');?></label>
								<input type="text" id="terraclassifieds-useremail" name="tc_email_abuse" class="terraclassifieds-input" />
							</p>
							<?php } ?>

							<p class="terraclassifieds-form-message">
								<label for="terraclassifieds-message"><?php _e('Message','terraclassifieds');?></label>
								<textarea id="terraclassifieds-message" name="tc_message_abuse" class="terraclassifieds-input"></textarea>
							</p>

							<?php if ( !is_user_logged_in() ) { ?>
								<div class="gdpr-checkboxes">
									<?php require_once(WP_PLUGIN_DIR . '/terraclassifieds/inc/security-abuse-form.php'); ?>
								 </div>
							 <?php } ?>
							 
							<p class="terraclassifieds-form-submit">
								<input type="hidden" name="tc_classified_author_id" value="<?php echo esc_attr($autor_id); ?>">
								<input type="hidden" name="tc_classified_id" value="<?php echo esc_attr($post_id); ?>">
								<input type="hidden" name="tc_form_action_abuse" value="1">
								<input type="submit" class="terraclassifieds-btn" name="user-submit" value="<?php _e('Send message','terraclassifieds');?>" />
							</p>

						</form>
						<?php } ?>
					</div>
					<?php } ?>
				</div>
			</div>
		</article>
	
	<?php endwhile; // end of the loop. ?>
</div>
<?php get_footer(); ?>