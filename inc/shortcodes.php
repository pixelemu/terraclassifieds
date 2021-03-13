<?php

/* ADD ADVERT */
function wds_handle_frontend_new_post_form_submission($cmb, $post_data = array())
{

	// If no form submission, bail
	if (empty($_POST)) {
		return false;
	}

	// check required $_POST variables and security nonce
	if (
		!isset($_POST['submit-cmb'], $_POST['object_id'], $_POST[$cmb->nonce()])
		|| !wp_verify_nonce($_POST[$cmb->nonce()], $cmb->nonce())
	) {
		return new WP_Error('security_fail', __('Security check failed.'));
	}

	if (empty($_POST['_tc_post_title'])) {
		return new WP_Error('post_data_missing', __('New post requires a title.'));
	}

	// Fetch sanitized values
	$sanitized_values = $cmb->get_sanitized_values($_POST);

	//Add ads expiration time
	$sanitized_values['_tc_expire_date'] = terraclassifieds_generate_ads_expired_time();

	// Set our post data arguments
	$post_data['post_title']   = $sanitized_values['_tc_post_title'];
	//unset( $sanitized_values['_tc_post_title'] );
	$post_data['post_content'] = $sanitized_values['_tc_post_content'];
	//unset( $sanitized_values['_tc_post_content'] );
	$ad_category_slug = $sanitized_values['_tc_category'];
	$ad_category_term = get_term_by('slug', $ad_category_slug, 'ad_category');
	$ad_category = $ad_category_term->term_id;
	//unset( $sanitized_values['_tc_category'] );
	if (!empty($sanitized_values['_tc_locations'])) {
		$ad_location_slug = $sanitized_values['_tc_locations'];
		$ad_location_term = get_term_by('slug', $ad_location_slug, 'ad_location');
		$ad_location = $ad_location_term->term_id;
	}

	$use_type = terraclassifieds_get_option_base_functions('_tc_use_types', 0);
	$type_style = terraclassifieds_get_option_base_functions('_tc_types_display_style', 0);
	if ($use_type) {
		$ad_type_slugs = $sanitized_values['_tc_types'];
		if (!empty($ad_type_slugs)) {
			if ($type_style == 'radio_buttons') {
				foreach ($ad_type_slugs as $ad_type_slug) {
					$ad_type_array[$ad_type_slug] = get_term_by('slug', $ad_type_slug, 'ad_type');
					$ad_type[] = $ad_type_array[$ad_type_slug]->term_id;
				}
			} else {
				$ad_type_term = get_term_by('slug', $ad_type_slugs, 'ad_type');
				$ad_type = $ad_type_term->name;
			}
		}
	}


	// Create the new post
	$new_submission_id = wp_insert_post($post_data, true);

	// If we hit a snag, update the user
	if (is_wp_error($new_submission_id)) {
		return $new_submission_id;
	}

	// If we hit a snag, update the user
	if (is_wp_error($new_submission_id)) {
		return $new_submission_id;
	}

	// set category
	wp_set_post_terms($new_submission_id, $ad_category, 'ad_category', false);

	// set location
	if (!empty($sanitized_values['_tc_locations'])) {
		wp_set_post_terms($new_submission_id, $ad_location, 'ad_location', false);
	}

	// set type
	if ($use_type) {
		wp_set_post_terms($new_submission_id, $ad_type, 'ad_type', false);
	}

	/**
	 * Other than post_type and post_status, we want
	 * our uploaded attachment post to have the same post-data
	 */
	unset($post_data['post_type']);
	unset($post_data['post_status']);

	// Loop through remaining (sanitized) data, and save to post-meta
	foreach ($sanitized_values as $key => $value) {
		update_post_meta($new_submission_id, $key, $value);
	}

	if (intval($post_data['post_price']) == 0){
	// Sending add free ads notofication 
		terraclassifieds_new_ad_notification($new_submission_id, $post_data, $ad_category_term);
	}

	return $new_submission_id;
}


function wds_do_frontend_form_submission_shortcode( $atts = array() ) {
    // Current user
    $user_id = get_current_user_id();
			
	if ( is_user_logged_in() ){
	    // Use ID of metabox in wds_frontend_form_register
	    $metabox_id = 'ad_options';
	
	    // since post ID will not exist yet, just need to pass it something
	    $object_id  = 'fake-oject-id';
	
	    // Get CMB2 metabox object
	    $cmb = cmb2_get_metabox( $metabox_id, $object_id );
	
	    // Get $cmb object_types
	    $post_types = $cmb->prop( 'object_types' );
		
		// get default status for new ad
		$advert_status = terraclassifieds_get_option_base_functions( '_tc_add_advert_ad_status', 0 );
		if($advert_status == 1){
			$advert_status_value = 'publish';
		} else {
			$advert_status_value = 'pending';
		}

		// change status to 'Draft' if checkbox is checked
		if( isset( $_POST["_tc_draft_status"]) ) { 
		    $advert_status_value = 'draft';
		}
		
		$charing_for_add_ads = terraclassifieds_get_option('_tc_monetizing_charging_for_adding_ads_price','free');
		$currency = terraclassifieds_get_option( '_tc_advert_currency', '$' );
		$unit_position = intval(terraclassifieds_get_option( '_tc_unit_position', 1 ));
		
		$task = !empty($_POST['task']) ? $_POST['task'] : 'form';
	    // Handle form saving (if form has been submitted)
		if ($task === 'form') {
			echo cmb2_get_metabox_form( $cmb, $object_id, array( 'save_button' => __( 'Add advert', 'terraclassifieds' ) ) );
		}elseif($task === 'save_ads') {
			$category_id = intval($_POST['_tc_id_category']);
			$ads_price_data = terraclassifieds_calculate_ads_price($category_id, true);
			$ads_title = $_POST['_tc_post_title'];
			$ads_id_category = intval($_POST['_tc_id_category']);
			$ads_category = ucfirst($_POST['_tc_category']);
			$payment_items = $ads_price_data['payment_items'];
			
			$ads_price = $ads_price_data['ads_price'];
			$ads_category_price_text = $ads_price_data['ads_category_price_text'];
			$ads_price_text = $ads_price_data['ads_price_text'];
			$vat_text = $ads_price_data['vat_text'];
			$net_text = $ads_price_data['net_text'];
			$vat_rate = $ads_price_data['vat_rate'];
			
			if ($ads_price) {
				$advert_status_value = 'pending';
			}
			
			 // Parse attributes. These shortcode attributes can be optionally overridden.
			$atts = shortcode_atts( array(
				'post_author' => $user_id ? $user_id : 1, // Current user, or admin
				'post_status' => $advert_status_value,
				'post_type'   => reset( $post_types ), // Only use first object_type in array
				'post_price_text'  => $ads_price_text,
				'post_price'  => $ads_price,
			), $atts, 'cmb-frontend-form' );
			
			$new_id = wds_handle_frontend_new_post_form_submission($cmb, $atts);
			if (is_wp_error($new_id)) { ?>
				<h3><?php echo  sprintf( __( 'There was an error in the submission: %s', 'terraclassifieds' ), '<strong>'. $new_id->get_error_message() .'</strong>' ); ?></h3>
				<?php
				// Get our form
				echo cmb2_get_metabox_form( $cmb, $object_id, array( 'save_button' => __( 'Add advert', 'terraclassifieds' ) ) );
			}else{
				if (!$ads_price) {
					// Get submitter's name
					$name = isset( $_POST['submitted_author_name'] ) && $_POST['submitted_author_name']
						? ' '. $_POST['submitted_author_name']
						: '';
		
						// Add notice of submission ?>
						<div class="terraclassifieds-message info">
							<h3>
								<?php if($advert_status_value == 'publish'){ ?>
									<?php echo sprintf( __( 'Thank you %s, your new post has been submitted and published.', 'terraclassifieds' ), esc_html( $name ) ); ?>
								<?php } else if($advert_status_value == 'pending') { ?>
									<?php echo sprintf( __( 'Thank you %s, your new post has been submitted and is pending review by a site administrator.', 'terraclassifieds' ), esc_html( $name ) ); ?>
								<?php } else { ?>
									<?php echo sprintf( __( 'Thank you %s, your new post has been saved as Draft.', 'terraclassifieds' ), esc_html( $name ) ); ?>
								<?php } ?>
							</h3>
						</div>
					<?php
					// Get our form
					echo cmb2_get_metabox_form( $cmb, $object_id, array( 'save_button' => __( 'Add advert', 'terraclassifieds' ) ) );
				}else{
					global $wpdb;
					$use_offline_payment = intval(terraclassifieds_get_option('_tc_monetizing_use_offline_payment',0)); 
					$use_paypal_payment = intval(terraclassifieds_get_option('_tc_monetizing_use_paypal_payment',0));
					$ads_id = intval($new_id);
					$user_id = intval($user_id);
					$ad_category_term = get_term_by('ID', $category_id, 'ad_category');
					$payment_time = current_time('mysql');
					$payment_hash = terraclassifieds_generate_uniqid();
					$post = get_post($ads_id);
					
					$table = $wpdb->prefix.'terraclassifieds_payments';
					$data = array('id_item' => $ads_id, 'id_user' => intval($user_id),'status' => 'pending','price' => $ads_price,'ip_address' => getUserIP(),'type'=>'Ads Category','payment_hash' => $payment_hash,'operation' => 'add');
					$format = array('%d','%d','%s','%f','%s','%s','%s','%s');
					$wpdb->insert($table,$data,$format);
					$payment_id = $wpdb->insert_id;
					
					$table_items = $wpdb->prefix.'terraclassifieds_payment_items';
					if (!empty($payment_items) && is_array($payment_items)) {
						foreach ($payment_items as $k=>$item) {
							$data = array('id_payment' => $payment_id, 'type' => $k, 'price' => $item);
							$format = array('%d','%s','%f');
							$wpdb->insert($table_items,$data,$format);
						}
					}
					
					$payment_items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_items WHERE id_payment = %d ORDER BY id DESC",$payment_id));
					
					?>
						<div>
							<a class="button" onclick="javascript:window.history.back();"><?php echo __('Back To Edit - Button','terraclassifieds'); ?></a>
						</div>
						<div class="terraclassifieds-payment-details">
							<h4 class="terraclassifieds-payment-details-head"><?php echo __('Payment Details','terraclassifieds'); ?></h4>
							<table>
								<tbody>
									<tr>
										<td><?php echo __('Advert','terraclassifieds'); ?></td>
										<td><?php echo esc_html($ads_title); ?></td>
									</tr>
									<tr>
										<td><?php echo __('Advert ID','terraclassifieds'); ?></td>
										<td><?php echo esc_html($ads_id); ?></td>
									</tr>
									<?php if (!empty($payment_items) && is_array($payment_items)) : ?>
										<?php foreach ($payment_items as $k=>$item) : ?>
											<tr>
												<td><?php echo __(ucfirst($item->type),'terraclassifieds'); ?></td>
												<td><?php echo esc_html(terraclassifieds_format_price($item->price)); ?></td>
											</tr>
										<?php endforeach; ?>
									<?php endif; ?>
									<tr>
										<td><?php echo __('Net cost','terraclassifieds'); ?></td>
										<td><?php echo esc_html($net_text); ?></td>
									</tr>
									<tr>
										<td><?php echo sprintf(__('Tax (%s%%)','terraclassifieds'),esc_html($vat_rate)); ?></td>
										<td><?php echo esc_html($vat_text); ?></td>
									</tr>
									<tr>
										<td><?php echo __('Total','terraclassifieds'); ?></td>
										<td><?php echo esc_html($ads_price_text); ?></td>
									</tr>
								</tbody>
							</table>
						</div>						
						<div class="terraclassifieds-payments-method-list">
							<h4 class="terraclassifieds-payments-method-list-head"><?php echo __('Payment Methods','terraclassifieds'); ?></h4>
							<table class="terraclassifieds-payments-method-list-table">
								<tbody>
									<?php 
										$offline_payment_image = terraclassifieds_get_option( '_tc_monetizing_offline_payment_logo', '' );
										$paypal_payment_image = terraclassifieds_get_option( '_tc_monetizing_paypal_payment_logo', '' );
									?>
									<?php if($use_offline_payment) : ?>
										<tr class="terraclassifieds-payments-method-list-item">
											<td class="terraclassifieds-payments-method-list-item-image">
												<?php if ($offline_payment_image) : ?>
													<img src="<?php echo stripslashes_deep($offline_payment_image); ?>" alt="Offline Payment Logo" title="Offline Payment Logo" class="tc_monetizing_offline_payment_logo" />
												<?php else : ?>
													&nbsp;
												<?php endif; ?>
											</td>
											<td class="terraclassifieds-payments-method-list-item-info">
												<p><?php echo __('Offline Payment','terraclassifieds'); ?></p>
												<p><?php echo __('Select this method to pay with bank transfer or other offline payment methods.','terraclassifieds'); ?></p>
											</td>
											<td class="terraclassifieds-payments-method-list-item-button">
												<form method="post" name="form_payment_offline" id="form-payment-offline">
													<?php wp_nonce_field(); ?>
													<input type="hidden" name="ads_id" value="<?php echo esc_attr($new_id); ?>" />
													<input type="hidden" name="ads_charging_price" value="<?php echo esc_attr($ads_price); ?>" />
													<input type="hidden" name="ads_id_category" value="<?php echo esc_attr($ads_id_category); ?>" />
													<input type="hidden" name="ads_user_id" value="<?php echo esc_attr($user_id); ?>" />
													<input type="hidden" id="payment-id" name="payment_id" value="<?php echo esc_attr($payment_id); ?>" />
													<input type="hidden" name="task" value="payment" />
													<input type="hidden" name="payment_method" value="offline" />
													
													<button type="button" class="button-secondary terraclassified-payment-method" name="offline_payment" id="offline-payment-button" data-method="offline" data-send-notification="1" data-ads-id="<?php echo esc_attr($ads_id); ?>" data-payment-hash="<?php echo esc_attr($payment_hash); ?>" /><span class="offline-payment-button-loader"><i class='fas fa-spinner fa-spin'></i></span><span><?php echo __('Buy Now','terraclassifieds'); ?></span></button>
												</form>
											</td>
										</tr>
									<?php endif; ?>
									<?php if($use_paypal_payment) : ?>
										<tr class="terraclassifieds-payments-method-list-item">
											<td class="terraclassifieds-payments-method-list-item-image">
												<?php if ($paypal_payment_image) : ?>
													<img src="<?php echo stripslashes_deep($paypal_payment_image); ?>" alt="PayPal Payment Logo" title="PayPal Payment Logo" class="tc_monetizing_paypal_payment_logo" />
												<?php else : ?>
													&nbsp;
												<?php endif; ?>
											</td>
											<td class="terraclassifieds-payments-method-list-item-info">
												<p><?php echo __('PayPal','terraclassifieds'); ?></p>
												<p><?php echo __('One of the most popular online payment methods enables you to pay securely with your PayPal account, VISA, MasterCard. A PayPal account is not required to pay with payment cards. Just click “Create account” on the PayPal payment page to check options.','terraclassifieds'); ?></p>
											</td>
											<td class="terraclassifieds-payments-method-list-item-button">
												<?php 
													$paypal_test_mode = terraclassifieds_get_option('_tc_monetizing_paypal_payment_test_mode',1);
													$paypal_email_id = terraclassifieds_get_option('_tc_monetizing_paypal_payment_business_email_id','');
													$paypal_return_successful_url = home_url('/').terraclassifieds_get_option('_tc_monetizing_paypal_payment_return_successful_url','paypal-payment-success');
													$paypal_return_cancel_url = home_url('/').terraclassifieds_get_option('_tc_monetizing_paypal_payment_return_cancel_url','paypal-payment-cancel');
													$paypal_return_notify_url = home_url('/').terraclassifieds_get_option('_tc_monetizing_paypal_payment_return_notify_url','paypal-payment-notify');
													$paypal_currency_code = terraclassifieds_get_option('_tc_monetizing_paypal_payment_currency_code','PLN');
													$paypalUrl = $paypal_test_mode ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';
												?>
												
												<form name="form_payment_paypal" id="form-payment-paypal" action="<?php echo $paypalUrl; ?>" method="post">
													<input type="hidden" name="business" value="<?php echo esc_attr($paypal_email_id); ?>">                                                                                                
													<input type="hidden" name="cmd" value="_xclick"> 
													<input type="hidden" id="paypal-item-name" name="item_name" value="<?php echo sprintf( __('Payment for ads: %s,Payment id: %s', 'terraclassifieds'),$post->post_title,$payment_id);?>">
													<input type="hidden" id="paypal-item-number" name="item_number" value="<?php echo esc_attr($payment_hash); ?>">
													<input type="hidden" name="amount" value="<?php echo esc_attr($ads_price); ?>">
													<input type="hidden" name="currency_code" value="<?php echo esc_attr($paypal_currency_code); ?>">
													<input type="hidden" name="return" value="<?php echo stripslashes_deep($paypal_return_successful_url); ?>">
													<input type="hidden" name="notify_url" value="<?php echo stripslashes_deep($paypal_return_notify_url); ?>">
													<input type="hidden" name="cancel_return" value="<?php echo stripslashes_deep($paypal_return_cancel_url); ?>">
													
													<button class="button-secondary terraclassified-payment-method" type="button" name="paypal_payment" id="paypal-payment-button" data-method="paypal" data-send-notification="1" data-ads-id="<?php echo esc_attr($ads_id); ?>" data-payment-hash="<?php echo esc_attr($payment_hash); ?>" /><span class="paypal-payment-button-loader"><i class='fas fa-spinner fa-spin'></i></span><span><?php echo __('Buy Now','terraclassifieds'); ?></span></button>
												</form>
											</td>
										</tr>
									<?php endif; ?>
								</tbody>
							</table>
						</div>
					<?php
				}
			}
		}elseif($task === 'payment') {
			$wpnonce = $_POST['_wpnonce'];
			if (!wp_verify_nonce($wpnonce)) {
				die( __( 'Security check', 'textdomain' ) ); 
			} else {
				global $wpdb;
				$ads_id = intval($_POST['ads_id']);
				$ads_charging_price = floatval($_POST['ads_charging_price']);
				$post = get_post($ads_id);
				$method = $_POST['payment_method'];
				$payment_id = intval($_POST['payment_id']);
				if (!$unit_position) {
						$ads_price_text = $currency.' '.terraclassifiedsPriceFormat($ads_charging_price,1);
					}else{
						$ads_price_text = terraclassifiedsPriceFormat($ads_charging_price,1).' '.$currency;
					}
				$use_offline_payment_information = terraclassifieds_get_option( '_tc_monetizing_use_offline_payment_information', __('Enter details for your bank account or other necessary information required for making a payment.','terraclassifieds'));
				if ($payment_id) {
					if ($method === 'offline') : ?>
						<div class="terraclassifieds-offline-payment-summary">
							<h4 class="terraclassifieds-offline-payment-summary-head"><?php echo __('Offline Payment','terraclassifieds'); ?></h4>
							<table>
								<tbody>
									<tr>
										<td><?php echo __('Advert','terraclassifieds'); ?></td>
										<td><?php echo esc_html($post->post_title); ?></td>
									</tr>
									<tr>
										<td><?php echo __('Price','terraclassifieds'); ?></td>
										<td><?php echo esc_html($ads_price_text); ?></td>
									</tr>
									<tr>
										<td><?php echo __('Advert ID','terraclassifieds'); ?></td>
										<td><?php echo esc_html($ads_id); ?></td>
									</tr>
									<tr>
										<td><?php echo __('Payment ID','terraclassifieds'); ?></td>
										<td><?php echo esc_html($payment_id); ?></td>
									</tr>
									<tr>
										<td colspan="2">
											<p>
												<?php echo __('Payment Information','terraclassifieds'); ?>
											</p>
											<p>
												<?php echo esc_html($use_offline_payment_information); ?>
											</p>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					<?php endif;
				}
			}
		}
	} else {
	    $page_login_slug = terraclassifieds_get_option( '_tc_slug_login', 'login' );
	    $page_login = get_page_link( get_page_by_path( $page_login_slug ) );
		echo '<div class="terraclassifieds-message info"><h3><a href="'.esc_url($page_login).'">'.__( 'Please login', 'terraclassifieds' ).'</a></h3></div>';
	}

}
add_shortcode('cmb-frontend-form', 'wds_do_frontend_form_submission_shortcode');

add_shortcode('terraclassifieds_add_item', 'terraclassifieds_add_item_body');
function terraclassifieds_add_item_body($atts)
{

	ob_start();
	echo do_shortcode('[cmb-frontend-form]');
	return ob_get_clean();
}

/* EDIT ADVERT */
function wds_handle_frontend_edit_post_form_submission($cmb, $post_data = array())
{

	// If no form submission, bail
	if (empty($_POST)) {
		return false;
	}

	// check required $_POST variables and security nonce
	if (
		!isset($_POST['submit-cmb'], $_POST['object_id'], $_POST[$cmb->nonce()])
		|| !wp_verify_nonce($_POST[$cmb->nonce()], $cmb->nonce())
	) {
		return new WP_Error('security_fail', __('Security check failed.'));
	}

	if (empty($_POST['_tc_post_title'])) {
		return new WP_Error('post_data_missing', __('New post requires a title.'));
	}

	// Fetch sanitized values
	$sanitized_values = $cmb->get_sanitized_values($_POST);

	// Set our post data arguments
	$post_data['ID']   = $_GET['object_id'];
	$post_data['post_title']   = $sanitized_values['_tc_post_title'];
	//unset( $sanitized_values['_tc_post_title'] );
	$post_data['post_content'] = $sanitized_values['_tc_post_content'];
	//unset( $sanitized_values['_tc_post_content'] );
	$ad_category_slug = $sanitized_values['_tc_category'];
	$ad_category_term = get_term_by('slug', $ad_category_slug, 'ad_category');
	$ad_category = $ad_category_term->term_id;
	$ad_location_slug = $sanitized_values['_tc_locations'];
	$ad_location_term = get_term_by('slug', $ad_location_slug, 'ad_location');
	$ad_location = $ad_location_term->term_id;

	$use_type = terraclassifieds_get_option_base_functions('_tc_use_types', 0);
	$type_style = terraclassifieds_get_option_base_functions('_tc_types_display_style', 0);
	if ($use_type && !empty($sanitized_values['_tc_types'])) {
		$ad_type_slugs = $sanitized_values['_tc_types'];
		if (!empty($ad_type_slugs)) {
			if ($type_style == 'radio_buttons') {
				foreach ($ad_type_slugs as $ad_type_slug) {
					$ad_type_array[$ad_type_slug] = get_term_by('slug', $ad_type_slug, 'ad_type');
					$ad_type[] = $ad_type_array[$ad_type_slug]->term_id;
				}
			} else {
				$ad_type_term = get_term_by('slug', $ad_type_slugs, 'ad_type');
				$ad_type = $ad_type_term->name;
			}
		}
	}

	$new_submission_id = wp_update_post($post_data, true);

	// If we hit a snag, update the user
	if (is_wp_error($new_submission_id)) {
		return $new_submission_id;
	}

	// If we hit a snag, update the user
	if (is_wp_error($new_submission_id)) {
		return $new_submission_id;
	}

	// set category
	wp_set_post_terms($new_submission_id, $ad_category, 'ad_category', false);

	// set location
	wp_set_post_terms($new_submission_id, $ad_location, 'ad_location', false);

	// set type
	if ($use_type && !empty($sanitized_values['_tc_types'])) {
		wp_set_post_terms($new_submission_id, $ad_type, 'ad_type', false);
	}

	/**
	 * Other than post_type and post_status, we want
	 * our uploaded attachment post to have the same post-data
	 */
	unset($post_data['post_type']);
	unset($post_data['post_status']);

	// Loop through remaining (sanitized) data, and save to post-meta
	foreach ($sanitized_values as $key => $value) {
		update_post_meta($new_submission_id, $key, $value);
	}

	return $new_submission_id;
}


function wds_edit_frontend_form_submission_shortcode($atts = array())
{

	if (is_user_logged_in()) {
		// Use ID of metabox in wds_frontend_form_register
		$metabox_id = 'ad_options';

		// since post ID will not exist yet, just need to pass it something
		$object_id  = $_GET['object_id'];

		// get post status
		$post_status = get_post_status($object_id);

		// Get CMB2 metabox object
		$cmb = cmb2_get_metabox($metabox_id, $object_id);

		// get default status for new ad
		$advert_status = terraclassifieds_get_option_base_functions('_tc_add_advert_ad_status', 0);
		if ($advert_status == 1) {
			$advert_status_value = 'publish';
		} else {
			$advert_status_value = 'pending';
		}

		// change status to 'Draft' if checkbox is checked
		if (isset($_POST["_tc_draft_status"])) {
			$advert_status_value = 'draft';
		}

		// Parse attributes. These shortcode attributes can be optionally overridden.
		$atts = shortcode_atts(array(

			'post_status' => $advert_status_value,
		), $atts, 'cmb-frontend-form-edit');

		// Initiate our output variable
		$output = '';

		// Handle form saving (if form has been submitted)
		$new_id = wds_handle_frontend_edit_post_form_submission($cmb, $atts);


		if ($new_id) {

			if (is_wp_error($new_id)) {

				// If there was an error with the submission, add it to our ouput.
				$output .= '<h3>' . sprintf(__('There was an error in the submission: %s', 'terraclassifieds'), '<strong>' . $new_id->get_error_message() . '</strong>') . '</h3>';
			} else {

				// Get submitter's name
				$name = isset($_POST['submitted_author_name']) && $_POST['submitted_author_name']
					? ' ' . $_POST['submitted_author_name']
					: '';

				// Add notice of submission
				$output .= '<div class="terraclassifieds-message info"><h3>' . sprintf(__('Thank you %s, your ad has been updated.', 'terraclassifieds'), esc_html($name)) . '</h3></div>';

				if ($advert_status_value == 'publish') {
					// redirect to advert after successful editing
					$ad_url = get_permalink($object_id);
					header("Location: " . $ad_url . "?edited=yes");
				}
			}
		}

		// Get our form
		$output .= cmb2_get_metabox_form($cmb, $object_id, array('save_button' => __('Save changes', 'terraclassifieds')));
	} else {
		$page_login_slug = terraclassifieds_get_option('_tc_slug_login', 'login');
		$page_login = get_page_link(get_page_by_path($page_login_slug));
		$output = '<div class="terraclassifieds-message info"><h3><a href="' . $page_login . '">' . __('Please login', 'terraclassifieds') . '</a></h3></div>';
	}

	return $output;
}
add_shortcode('cmb-frontend-form-edit', 'wds_edit_frontend_form_submission_shortcode');
add_shortcode('terraclassifieds_edit_item', 'wds_edit_frontend_form_submission_shortcode');

add_shortcode('terraclassifieds_edit_item', 'terraclassifieds_edit_item_body');
function terraclassifieds_edit_item_body($atts)
{

	ob_start();
	echo do_shortcode('[cmb-frontend-form-edit]');
	return ob_get_clean();
}

/* MY SUBMISSIONS */
add_shortcode('terraclassifieds_my_submissions', 'terraclassifieds_my_submissions_body');
function terraclassifieds_my_submissions_body($atts)
{
	global $wp;
	global $wpdb;
	ob_start(); ?>

	<div class="terraclassifieds-container terraclassifieds-archive terraclassifieds-my-submissions">
		<div class="terraclassifieds-items">
			<?php
			$task = !empty($_POST['task']) ? $_POST['task'] : 'form';
			$pay_now_payment_methods = array('paypal');
				
			$page_edit_ad_slug = terraclassifieds_get_option('_tc_slug_edit_advert', 'edit-ad');
			$page_edit_ad = get_page_link(get_page_by_path($page_edit_ad_slug));
			$current_user_id = get_current_user_id();
			$show_hits = terraclassifieds_get_option('_tc_hits', 0);
			$no_image = terraclassifieds_get_option('_tc_image_no_image', 0);
			if (!empty($no_image)) {
				$no_image_id = attachment_url_to_postid($no_image);
			}
			if ($current_user_id != 0) {
				if ($task == 'form') {
					$posts_per_page = get_option('posts_per_page');
					$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
					$terra_ads_args = array(
						'post_type'      => 'classified',
						'post_status' => array('archived', 'rejected', 'publish', 'pending', 'draft'),
						'author' => $current_user_id,
						'posts_per_page' => $posts_per_page,
						'paged' => $paged,
					);

					$terra_ads_query = new WP_Query($terra_ads_args);
					if ($terra_ads_query->have_posts()) {
						while ($terra_ads_query->have_posts()) : $terra_ads_query->the_post();
							$category_id = get_post_meta(get_the_ID(),'_tc_id_category',true);
							$ads_price = 0;
							$ads_price_data = terraclassifieds_calculate_ads_price($category_id,true);
							$ads_price = $ads_price_data['ads_price'];
							
							$price = get_post_meta(get_the_ID(), '_tc_price', true);
							$sell_type = get_post_meta(get_the_ID(), '_tc_sell_type', 'price');
							$currency = terraclassifieds_get_option('_tc_advert_currency', '$');
							$unit_position = terraclassifieds_get_option('_tc_unit_position', 1);
							$ad_time = get_the_time('U');
							$hits = get_post_meta(get_the_ID(), '_terraclassifieds_popular_posts_count', true);
							if (empty($hits)) {
								$hits = 0;
							}
							$status = get_post_status(get_the_ID());
							$expire_date = get_post_meta(get_the_ID(), '_tc_expire_date', true);
							
							if($status == 'pending') {
								$table = $wpdb->prefix.'terraclassifieds_payments';
								$payment_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id_item = %d ORDER BY id DESC LIMIT 1",get_the_ID()));
							}
							
							?>
							<article <?php post_class(); ?>>
								<div class="terraclassifieds-item terraclassifieds-clear">
									<?php
									$tcf_image_archive_size_height = terraclassifieds_get_option_base_functions('_tc_image_archive_height', '145');
									$tcf_image_archive_size_width = terraclassifieds_get_option_base_functions('_tc_image_archive_width', '170');
									$gallery = get_post_meta(get_the_ID(), '_tc_gallery', true);
									if (!empty($gallery) || !empty($no_image)) { ?>
										<div class="terraclassifieds-image" style="width: <?php echo esc_attr($tcf_image_archive_size_width) . 'px;' ?>">
											<a href="<?php echo get_permalink(get_the_ID()); ?>">
												<?php if (!empty($gallery)) {
													cmb2_output_file_list_first_image('_tc_gallery', 'tcf-archive');
												} else if (!empty($no_image)) {
													echo '<div class="terraclassifieds-gallery" style="min-height: ' . esc_attr($tcf_image_archive_size_height) . 'px;">';
													echo '<div class="terraclassifieds-gallery-element">';
													echo wp_get_attachment_image($no_image_id, 'tcf-archive');
													echo '</div>';
													echo '</div>';
												} ?>
											</a>
										</div>
									<?php } ?>

									<div class="terraclassifieds-content">

										<header class="terraclassifieds-header">
											<h2 class="terraclassifieds-title">
												<?php if ($status == 'publish') { ?>
													<a href="<?php echo get_permalink(get_the_ID()); ?>">
													<?php } ?>
													<span><?php the_title(); ?></span>
													<?php if ($status == 'publish') { ?>
													</a>
												<?php } ?>
											</h2>
										</header>

										<div class="terraclassifieds-category">
											<?php the_terms(get_the_ID(), 'ad_category') ?>
										</div>

										<div class="terraclassifieds-desc">
											<?php terraclassifieds_excerpt(10, '&hellip;', false, true); ?>
										</div>
										<div>
											ID: <?php echo get_the_ID(); ?>
										</div>
									</div>

									<div class="terraclassifieds-details">

										<?php if (!empty($price) || $sell_type == 'for_free' || $sell_type == 'exchange') { ?>
											<div class="terraclassifieds-price">
												<?php if ($sell_type == 'for_free') { ?>
													<?php echo __('For Free', 'terraclassifieds'); ?>
												<?php } else if ($sell_type == 'exchange') { ?>
													<?php echo __('Exchange', 'terraclassifieds'); ?>
												<?php } else { ?>
													<?php if (!empty($currency) && $unit_position == 0) { ?>
														<?php echo esc_attr($currency); ?>
													<?php } ?>
													<?php terraclassifiedsPriceFormat($price); ?>
													<?php if (!empty($currency) && $unit_position == 1) { ?>
														<?php echo esc_attr($currency); ?>
													<?php } ?>
												<?php } ?>
											</div>
										<?php } ?>

										<div class="terraclassifieds-date">
											<?php echo __('Added:', 'terraclassifieds'); ?> <?php echo terraclassifieds_date_ago($ad_time); ?>
										</div>

										<?php if (!empty($expire_date) && current_time('timestamp') < $expire_date) { ?>
											<div class="terraclassifieds-expiration">
												<?php echo __('Expiration:', 'terraclassifieds'); ?> <?php echo terraclassifieds_date_from_now($expire_date); ?>
											</div>
										<?php } ?>

										<div class="terraclassifieds-author">
											<a class="terraclassifieds-author-link" href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>">
												<?php echo get_the_author_meta('display_name'); ?>
											</a>
										</div>

										<?php if ($show_hits) { ?>
											<div class="terraclassifieds-hits">
												<i class="fa fa-eye" aria-hidden="true"></i> <?php echo $hits; ?>
											</div>
										<?php } ?>

										<div class="terraclassifieds-status">
											<?php if ($status == 'publish') { ?>
												<i class="fa fa-check-circle" aria-hidden="true"></i> <span><?php echo __('Published', 'terraclassifieds'); ?></span>
											<?php } else if ($status == 'rejected') { ?>
												<i class="fa fa-ban" aria-hidden="true"></i> <span><?php echo __('Rejected', 'terraclassifieds'); ?></span>
											<?php } else if ($status == 'pending') { ?>
												<i class="fa fa-dot-circle-o" aria-hidden="true"></i> <span><?php echo __('Pending', 'terraclassifieds'); ?></span>
											<?php } else if ($status == 'draft') { ?>
												<i class="fa fa-pencil-ruler" aria-hidden="true"></i> <span><?php echo __('Draft', 'terraclassifieds'); ?></span>
											<?php } else { ?>
												<i class="fa fa-dot-circle-o" aria-hidden="true"></i> <span><?php echo __('Archived', 'terraclassifieds'); ?></span>
											<?php } ?>
										</div>
										<div class="terraclassifieds-actions">
											<div class="terraclassifieds-action pay-ad">
												<?php if($status == 'pending') : ?>
													<?php if (!empty($payment_data)): ?>
														<!-- Check if payment method is chosed -->
														<?php if (!empty($payment_data->method)) : ?>
															<?php if ($payment_data->method == 'paypal') : ?>
																<?php 
																	$post = get_post($payment_data->id_item);
																	$paypal_test_mode = terraclassifieds_get_option('_tc_monetizing_paypal_payment_test_mode',1);
																	$paypal_email_id = terraclassifieds_get_option('_tc_monetizing_paypal_payment_business_email_id','');
																	$paypal_return_successful_url = home_url('/').terraclassifieds_get_option('_tc_monetizing_paypal_payment_return_successful_url','paypal-payment-success');
																	$paypal_return_cancel_url = home_url('/').terraclassifieds_get_option('_tc_monetizing_paypal_payment_return_cancel_url','paypal-payment-cancel');
																	$paypal_return_notify_url = home_url('/').terraclassifieds_get_option('_tc_monetizing_paypal_payment_return_notify_url','paypal-payment-notify');
																	$paypal_currency_code = terraclassifieds_get_option('_tc_monetizing_paypal_payment_currency_code','PLN');
																	$paypalUrl = $paypal_test_mode ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';
																?>
																
																<form name="form_payment_paypal" id="form-payment-paypal" action="<?php echo $paypalUrl; ?>" method="post">
																	<input type="hidden" name="business" value="<?php echo esc_attr($paypal_email_id); ?>">                                                                                                
																	<input type="hidden" name="cmd" value="_xclick"> 
																	<input type="hidden" id="paypal-item-name" name="item_name" value="<?php echo sprintf( __('Payment for ads: %s,Payment id: %s', 'terraclassifieds'),$post->post_title,$payment_data->id);?>">
																	<input type="hidden" id="paypal-item-number" name="item_number" value="<?php echo esc_attr($payment_data->payment_hash); ?>">
																	<input type="hidden" name="amount" value="<?php echo esc_attr($payment_data->price); ?>">
																	<input type="hidden" name="currency_code" value="<?php echo esc_attr($paypal_currency_code); ?>">
																	<input type="hidden" name="return" value="<?php echo stripslashes_deep($paypal_return_successful_url); ?>">
																	<input type="hidden" name="notify_url" value="<?php echo stripslashes_deep($paypal_return_notify_url); ?>">
																	<input type="hidden" name="cancel_return" value="<?php echo stripslashes_deep($paypal_return_cancel_url); ?>">
																	
																	<input type="submit" name="submit-pay-now-<?php echo get_the_ID(); ?>"  value="<?php echo __( 'Pay Now', 'terraclassifieds' ); ?>">
																</form>
															<?php endif; ?>
															<!-- No payment method wasen't chosed -->
														<?php else : ?>
															<form action="" method="POST" >
																<?php wp_nonce_field(); ?>
																<input type="submit" name="submit-set-payment-method-<?php echo $payment_data->id; ?>" value="<?php echo __( 'Pay now', 'terraclassifieds' ); ?>">
																<input type="hidden" name="redirect-url" value="<?php echo base64_encode(home_url($wp->request)); ?>" />
																<input type="hidden" name="task" value="set_payment_method" />
																<input type="hidden" name="send_notification" value="0" />
																<input type="hidden" name="payment_id" value="<?php echo $payment_data->id; ?>" />
																<input type="hidden" name="ads_id" value="<?php echo get_the_ID(); ?>" />
															</form>
														<?php endif; ?>
													<?php else: ?>
													<?php endif; ?>
												<?php endif; ?>
											</div>
											<div class="terraclassifieds-action remove-ad">
												<form action="" method="POST" onSubmit="if(!confirm('<?php echo __('Are you sure you want to remove the item?', 'terraclassifieds'); ?>')){return false;}">
													<input class="terraclassifieds-small-btn" type="submit" name="submit-remove-<?php echo get_the_ID(); ?>" value="<?php echo __('Remove', 'terraclassifieds'); ?>">
												</form>
												<?php terraclassifieds_remove(get_the_ID()); ?>
											</div>
											<?php if ($status != 'rejected') { ?>
												<div class="terraclassifieds-action edit-ad">
													<?php $permalinks_structure = get_option('permalink_structure');
													if (!empty($permalinks_structure)) {
														$link_join = '?';
													} else {
														$link_join = '&';
													} ?>
													<a class="button disabled terraclassifieds-edit-ad-button-disabled" title="<?php echo __('Edit is available once the ad is published', 'terraclassifieds'); ?>"><?php echo __('Edit', 'terraclassifieds'); ?></a>
													<a class="button terraclassifieds-edit-ad-button" href="<?php echo esc_url($page_edit_ad . $link_join); ?>object_id=<?php echo get_the_ID(); ?>"><?php echo __('Edit', 'terraclassifieds'); ?></a>
												</div>
												<div class="terraclassifieds-action archive-ad">
													<form action="" method="POST">
														<input type="submit" name="submit-archive-<?php echo get_the_ID(); ?>" value="<?php echo __('Archive', 'terraclassifieds'); ?>">
													</form>
													<?php
													terraclassifieds_to_archive(get_the_ID());
													terraclassifieds_sendmail_update_status(get_the_ID());
													?>
												</div>
												<div class="terraclassifieds-action renew-ad">
													<?php
														$category_id = get_post_meta(get_the_ID(),'_tc_id_category',true);
														$ads_price = 0;
														$ads_price_data = terraclassifieds_calculate_ads_price($category_id,true,true);
														echo $ads_price = $ads_price_data['ads_price'];
													?>
													<form action="" method="POST" >
														<input type="submit" name="submit-publish-<?php echo get_the_ID(); ?>" value="<?php echo __( 'Renew', 'terraclassifieds' ); ?>">
														<input type="hidden" name="redirect-url" value="<?php echo base64_encode(home_url($wp->request)); ?>" />
														<?php if ($ads_price) : ?>
															<input type="hidden" name="task" value="renew" />
															<input type="hidden" name="send_notification" value="0" />
															<input type="hidden" name="ads_id" value="<?php echo get_the_ID(); ?>" />
															<input type="hidden" name="ads_price" value="<?php echo $ads_price; ?>" />
														<?php endif; ?>
													</form>
													<?php
														terraclassifieds_to_publish(get_the_ID());
													?>
												</div>
											<?php } ?>
										</div>
									</div>
								</div>
							</article>

						<?php endwhile; ?>
						<?php terraclassifieds_pagination($terra_ads_query->max_num_pages); ?>
						<?php wp_reset_query(); ?>
					<?php } else {
						if (is_paged()) {
							$paged = 1;
							$redirect_url_prev =  esc_url_raw(get_pagenum_link($paged));
							wp_safe_redirect($redirect_url_prev);
							exit;
						} else {
							echo __('No posts found.', 'terraclassifieds');
						}
					}
				}elseif($task == 'renew') {
					$data['post_id'] = intval($_POST['ads_id']);
					$data['task'] = $task;
					terraclassifieds_frontend_payments_list($data);
				}elseif($task == 'set_payment_method') {
					$data['post_id'] = intval($_POST['ads_id']);
					$data['payment_id'] = intval($_POST['payment_id']);
					$data['task'] = $task;
					terraclassifieds_frontend_payments_list($data);
				}elseif($task == 'payment') {
					terraclassifieds_frontend_payment_details();
				}
			} else {
				$page_login_slug = terraclassifieds_get_option('_tc_slug_login', 'login');
				$page_login = get_page_link(get_page_by_path($page_login_slug));
				echo '<div class="terraclassifieds-message"><h3><a href="' . $page_login . '">' . __('Login', 'terraclassifieds') . '</a> ' . __('to see your submissions.', 'terraclassifieds') . '</h3></div>';
			} ?>
		</div>
	</div>


<?php return ob_get_clean();
}

/* REGISTRATION */

add_shortcode('terraclassifieds_registration', 'terraclassifieds_registration');
function terraclassifieds_registration($atts)
{

	ob_start(); ?>

	<?php if (is_user_logged_in()) : // logged-in 
	?>
		<div id="terraclassifieds-login-register-forgot">
			<div class="terraclassifieds-message">
				<h3><?php esc_html_e('You are already logged in.', 'terraclassifieds'); ?></h3>
			</div>
			<a class="button" href="<?php echo wp_logout_url(get_permalink()); ?>"><?php esc_html_e('Logout', 'terraclassifieds'); ?></a>
		</div>
	<?php else : // not logged-in 
	?>
		<div id="terraclassifieds-login">
			<div class="terraclassifieds-registration-content">

				<?php if (!get_option('users_can_register')) : ?>
					<p class="terraclassifieds-info"><?php esc_html_e('User registration is disabled.', 'terraclassifieds'); ?></p>
				<?php endif; ?>

				<?php if (get_option('users_can_register')) : // register form 
				?>
					<?php
					$page_login_slug = terraclassifieds_get_option('_tc_slug_login', 'login');
					$page_login = get_page_link(get_page_by_path($page_login_slug));
					$password_registration = terraclassifieds_get_option('_tc_password_registration', 0);
					$gdpr_method = terraclassifieds_get_option('_tc_gdpr_method', 0);
					$recaptcha = terraclassifieds_get_option('_tc_recaptcha', 0);
					$recaptcha_site_key = terraclassifieds_get_option('_tc_recaptcha_site_key', '');
					$recaptcha_secret_key = terraclassifieds_get_option('_tc_recaptcha_secret_key', '');
					$captcha_ok = false;
					if (isset($_POST['user_login']) && isset($_POST['user_email'])) {
						$user_name = sanitize_user($_POST['user_login']);
						$user_email = sanitize_email($_POST['user_email']);
						if ($password_registration == 1) {
							$password = sanitize_text_field($_POST['user_password']);
						}
						$user_id = username_exists($user_name);
						if (!$user_id and email_exists($user_email) == false) {
							if ($recaptcha && $recaptcha_site_key && $recaptcha_secret_key && isset($_POST['g-recaptcha-response'])) {

								$secret    = $recaptcha_secret_key;
								$recaptcha = new \ReCaptcha\ReCaptcha($secret);

								$resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

								if ($resp->isSuccess()) {
									// Your code here to handle a successful verification
									// echo json_encode(array(
									// 	'success' => true,
									// 	'message' => __("Registration complete", 'terraclassifieds')
									// ));
									$captcha_ok = true;
								} else {
									// What happens when the CAPTCHA was entered incorrectly
									// echo json_encode(array(
									// 	'success' => false,
									// 	'message' => __("The reCAPTCHA wasn't entered correctly.", 'terraclassifieds')
									// )); 
					?>
									<div class="terraclassifieds-message">
										<h3><?php esc_html_e('Captcha error.', 'terraclassifieds'); ?></h3>
									</div>
								<?php }
							}
							if (!$recaptcha || ($captcha_ok || !($recaptcha_site_key && $recaptcha_secret_key))) {
								if ($password_registration == 0) {
									$password = wp_generate_password($length = 12, $include_standard_special_chars = false);
								}
								$userdata = array(
									'user_login'  =>  $user_name,
									'user_email'  =>  $user_email,
									'user_pass'   =>  $password,
								);
								$user_id = wp_insert_user($userdata);
								if ($gdpr_method == 1) {
									GDPR::save_user_consent_on_registration($user_id);
								}
								terraclassifieds_new_user_notification($user_id, $password); ?>
								<div class="terraclassifieds-message">
									<h3><?php esc_html_e('Registration complete. Check Your inbox for username and password to', 'terraclassifieds'); ?> <a href="<?php echo esc_url($page_login); ?>"><?php echo __('login', 'terraclassifieds'); ?></a></h3>
								</div>
							<?php } ?>
						<?php } else { ?>
							<div class="terraclassifieds-message error">
								<h3><?php esc_html_e('Username or email already exists.', 'terraclassifieds'); ?></h3>
							</div>
					<?php }
					} ?>
					<div id="terraclassifieds-register">

						<form action="" id="terraclassifieds-signup-form" method="post">

							<p class="register-username">
								<label for="userName"><?php esc_html_e('Username', 'terraclassifieds') ?> *</label>
								<input size="30" type="text" id="userName" name="user_login" />
							</p>

							<p class="register-email">
								<label for="user_email"><?php esc_html_e('Email', 'terraclassifieds') ?> *</label>
								<input size="30" type="text" id="user_email" name="user_email" />
							</p>
							<?php if ($password_registration == 1) { ?>
								<p class="register-password">
									<label for="user_password"><?php esc_html_e('Password', 'terraclassifieds') ?> *</label>
									<input size="30" type="password" id="user_password" name="user_password" />
									<span class="status"></span>
								</p>
							<?php } ?>

							<div class="gdpr-checkboxes">
								<?php
								if ($gdpr_method == 0) {
									require_once(plugin_dir_path(__FILE__) . '../inc/security.php');
								} else {
									GDPR::consent_checkboxes();
								}
								?>
							</div>
							<!--<hr />-->

							<?php
							if ($recaptcha && $recaptcha_site_key && $recaptcha_secret_key) {
								echo '<div class="tcf-captcha">';
								echo '<div class="g-recaptcha" data-sitekey="' . $recaptcha_site_key . '"></div>';
								echo '<script type="text/javascript" src="https://www.google.com/recaptcha/api.js" async defer></script>';
								echo '</div>';
							}
							?>
							<p class="register-submit">
								<input type="submit" class="button" name="user-submit" value="<?php esc_html_e('Register', 'terraclassifieds'); ?>" />
								<input type="hidden" name="action" value="registration_captcha" />
							</p>

						</form>

					</div>

				<?php endif; ?>

			</div>
		</div>
	<?php endif; ?>

<?php return ob_get_clean();
}

/* LOGIN */
add_shortcode('terraclassifieds_login', 'terraclassifieds_login');
function terraclassifieds_login($atts)
{

	ob_start(); ?>
	<?php
	$page_forgot_password_slug = terraclassifieds_get_option('_tc_slug_forgot_password', 'forgot-password');
	$page_forgot_password = get_page_link(get_page_by_path($page_forgot_password_slug));
	$page_registration_slug = terraclassifieds_get_option('_tc_slug_registration', 'registration');
	$page_registration = get_page_link(get_page_by_path($page_registration_slug));
	$page_my_submissions_slug = terraclassifieds_get_option('_tc_slug_my_submissions', 'my-submissions');
	$page_my_submissions = get_page_link(get_page_by_path($page_my_submissions_slug));
	?>
	<?php if (is_user_logged_in()) : // logged-in 
	?>
		<div id="terraclassifieds-login-register-forgot">
			<div class="terraclassifieds-message">
				<h3><?php esc_html_e('You are already logged in.', 'terraclassifieds'); ?></h3>
			</div>
			<a class="button" href="<?php echo wp_logout_url(get_permalink()); ?>"><?php esc_html_e('Logout', 'terraclassifieds'); ?></a>
		</div>
	<?php else : // not logged-in 
	?>

		<?php if (isset($_GET['login']) && $_GET['login'] == 'failed') { ?>
			<div class="terraclassifieds-message">
				<h3><?php esc_html_e('Login failed. Username/password is wrong or empty.', 'terraclassifieds'); ?></h3>
			</div>
		<?php } ?>

		<div id="terraclassifieds-login">
			<?php // login form
			$args = array(
				'redirect'    => $page_my_submissions,
				'remember'    => true,
				'id_username' => 'user',
				'id_password' => 'pass',
				'id_submit'   => 'wp-submit',
				'label_username' => __('Username', 'terraclassifieds'),
			);

			wp_login_form($args);

			?>
			<a class="terraclassifieds-forgot-password-link" href="<?php echo esc_url($page_forgot_password); ?>"><?php esc_html_e('Forgot your password?', 'terraclassifieds'); ?></a>
		</div>

		<div class="terraclassifieds-register-link-outer">
			<a href="<?php echo esc_url($page_registration); ?>"><?php esc_html_e('Don\'t have an account?', 'terraclassifieds'); ?></a>
		</div>

	<?php endif; ?>

<?php return ob_get_clean();
}

/* FORGOT PASSWORD */
add_shortcode('terraclassifieds_forgot_password', 'terraclassifieds_forgot_password');
function terraclassifieds_forgot_password($atts)
{

	ob_start(); ?>

	<div class="terraclassifieds-forgot-password">

		<form action="<?php echo site_url('wp-login.php?action=lostpassword', 'login_post') ?>" id="forgot-form" method="post">

			<p class="terraclassifieds-info"><?php esc_html_e('Please enter your username or email address for your account. A verification e-mail will be sent to you and you will be able to reset your password.', 'terraclassifieds'); ?></p>

			<p class="forgot-email">
				<label for="user_login"><?php esc_html_e('Username or Email', 'terraclassifieds') ?></label>
				<input size="30" type="text" name="user_login" value="" id="user_login" />
			</p>

			<p class="forgot-submit">
				<input type="submit" class="button" name="user-submit" value="<?php esc_html_e('Submit', 'terraclassifieds'); ?>" />
				<input type="hidden" name="redirect_to" value="<?php get_permalink(); ?>" />
				<input type="hidden" name="user-cookie" value="1" />
			</p>

		</form>

	</div>

<?php return ob_get_clean();
}

/* EDIT PROFILE */
add_shortcode('terraclassifieds_edit_profile', 'terraclassifieds_edit_profile');
function terraclassifieds_edit_profile($atts)
{

	ob_start(); ?>

	<div class="terraclassifieds-edit-profile">

		<?php
		/* Get user info. */
		global $current_user, $wp_roles;
		//get_currentuserinfo(); //deprecated since 3.1

		if ( ! function_exists( 'wp_crop_image' ) ) {
			include( ABSPATH . 'wp-admin/includes/image.php' );
		}

		$error = array();
		/* If profile was saved, update profile. */
		if ('POST' == $_SERVER['REQUEST_METHOD'] && !empty($_POST['action']) && $_POST['action'] == 'update-user') {

			/* Update user password. */
			if (!empty($_POST['pass1']) && !empty($_POST['pass2'])) {
				if ($_POST['pass1'] == $_POST['pass2']) {
					wp_update_user(array('ID' => $current_user->ID, 'user_pass' => sanitize_text_field($_POST['pass1'])));
				} else {
					$error[] = __('The passwords you entered do not match.  Your password was not updated.', 'terraclassifieds');
				}
			}

			/* Update user information. */
			wp_update_user(array('ID' => $current_user->ID, 'user_url' => esc_url($_POST['url'])));
			if (!empty($_POST['user_email'])) {
				if (!is_email($_POST['user_email'])) {
					$error[] = __('The Email you entered is not valid.  please try again.', 'terraclassifieds');
				} elseif (email_exists($_POST['user_email']) && ($_POST['user_email'] != $current_user->user_email)) {
					$error[] = __('This email is already used by another user.  try a different one.', 'terraclassifieds');
				} else {
					wp_update_user(array('ID' => $current_user->ID, 'user_email' =>  sanitize_email($_POST['user_email'])));
				}
			}

			//if ( !empty( $_POST['first_name'] ) )
			update_user_meta($current_user->ID, 'first_name', sanitize_text_field($_POST['first_name']));
			//if ( !empty( $_POST['last_name'] ) )
			update_user_meta($current_user->ID, 'last_name', sanitize_text_field($_POST['last_name']));
			//if ( !empty( $_POST['description'] ) )
			update_user_meta($current_user->ID, 'description', sanitize_text_field($_POST['description']));
			//if (!empty($_POST["tc_phone"]))
			update_user_meta($current_user->ID, '_tc_phone', sanitize_text_field($_POST['tc_phone']));


			if ($_FILES['profilepicture']['name'] != "") {
				// UPLOAD IMAGE TO MEDIA LIBRARY - BEGIN

				$wordpress_upload_dir = wp_upload_dir();
				// $wordpress_upload_dir['path'] is the full server path to wp-content/uploads/2017/05, for multisite works good as well
				// $wordpress_upload_dir['url'] the absolute URL to the same folder, actually we do not need it, just to show the link to file
				$i = 1; // number of tries when the file with the same name is already exists

				$profilepicture = $_FILES['profilepicture'];
				$new_file_path = $wordpress_upload_dir['path'] . '/' . $profilepicture['name'];
				$new_file_mime = mime_content_type($profilepicture['tmp_name']);

				if (empty($profilepicture))
					die('File is not selected.');

				if ($profilepicture['error'])
					die($profilepicture['error']);

				if ($profilepicture['size'] > wp_max_upload_size())
					die('It is too large than expected.');

				if (!in_array($new_file_mime, get_allowed_mime_types()))
					die('WordPress doesn\'t allow this type of uploads.');

				while (file_exists($new_file_path)) {
					$i++;
					$new_file_path = $wordpress_upload_dir['path'] . '/' . $i . '_' . $profilepicture['name'];
				}

				// looks like everything is OK
				if (move_uploaded_file($profilepicture['tmp_name'], $new_file_path)) {


					$upload_id = wp_insert_attachment(array(
						'guid'           => $new_file_path,
						'post_mime_type' => $new_file_mime,
						'post_title'     => preg_replace('/\.[^.]+$/', '', $profilepicture['name']),
						'post_content'   => '',
						'post_status'    => 'inherit'
					), $new_file_path);


					// Generate and save the attachment metas into the database
					wp_update_attachment_metadata($upload_id, wp_generate_attachment_metadata($upload_id, $new_file_path));

					// Show the uploaded file in browser
					//wp_redirect( $wordpress_upload_dir['url'] . '/' . basename( $new_file_path ) );
					// UPLOAD IMAGE TO MEDIA LIBRARY - END
				}
				$image_src = esc_url_raw(wp_get_attachment_url($upload_id));
				update_user_meta($current_user->ID, '_tc_avatar', $image_src);
				update_user_meta($current_user->ID, '_tc_avatar_id', $upload_id);
			} else if (isset($_POST['remove-avatar'])) {
				delete_user_meta($current_user->ID, '_tc_avatar');
			}

			/* Redirect so the page will show updated info.*/
			/*I am not Author of this Code- i dont know why but it worked for me after changing below line to if ( count($error) == 0 ){ */
			if (count($error) == 0) {
				//action hook for plugins and extra fields saving
				do_action('edit_user_profile_update', $current_user->ID);
				//wp_redirect( get_permalink() );
				//exit;
				echo '<p class="terraclassifieds-message">' . __('Profile has been updated.', 'terraclassifieds') . '</p>';
			}
		} ?>
		<div class="terraclassifieds-edit-profile-in">
			<?php if (!is_user_logged_in()) : ?>
				<p class="terraclassifieds-message error">
					<?php _e('You must be logged in to edit your profile.', 'terraclassifieds'); ?>
				</p><!-- .warning -->
			<?php else : ?>
				<?php if (count($error) > 0) {
					echo '<p class="terraclassifieds-message error">' . implode("<br />", $error) . '</p>';
				} ?>
				<?php
				$phone_meta = get_user_meta($current_user->ID, '_tc_phone');
				if (empty(sizeof($phone_meta))) {
					$phone_meta_value = '';
				} else {
					$phone_meta_value = $phone_meta[0];
				}
				?>
				<form method="post" id="edituser" action="<?php the_permalink(); ?>" enctype="multipart/form-data">
					<p class="form-username">
						<label for="first_name"><?php _e('First name', 'terraclassifieds'); ?></label>
						<input class="text-input" name="first_name" type="text" id="first_name" value="<?php the_author_meta('first_name', $current_user->ID); ?>" />
					</p><!-- .form-username -->
					<p class="form-username">
						<label for="last_name"><?php _e('Last name', 'terraclassifieds'); ?></label>
						<input class="text-input" name="last_name" type="text" id="last_name" value="<?php the_author_meta('last_name', $current_user->ID); ?>" />
					</p><!-- .form-username -->
					<p class="form-email">
						<label for="user_email"><?php _e('E-mail', 'terraclassifieds'); ?></label>
						<input class="text-input" name="user_email" type="text" id="user_email" value="<?php the_author_meta('user_email', $current_user->ID); ?>" />
					</p><!-- .form-email -->
					<p class="form-url">
						<label for="url"><?php _e('Website', 'terraclassifieds'); ?></label>
						<input class="text-input" name="url" type="text" id="url" value="<?php the_author_meta('user_url', $current_user->ID); ?>" />
					</p><!-- .form-url -->
					<p class="form-phone">
						<label for="tc_phone"><?php _e('Phone', 'terraclassifieds'); ?></label>
						<input class="text-input" name="tc_phone" type="text" id="tc_phone" value="<?php echo esc_attr($phone_meta_value); ?>" />
					</p><!-- .form-phone -->
					<p class="form-password">
						<label for="pass1"><?php _e('Password', 'terraclassifieds'); ?> </label>
						<input class="text-input" name="pass1" type="password" id="pass1" />
					</p><!-- .form-password -->
					<p class="form-password">
						<label for="pass2"><?php _e('Repeat password', 'terraclassifieds'); ?></label>
						<input class="text-input" name="pass2" type="password" id="pass2" />
					</p><!-- .form-password -->
					<p class="form-textarea">
						<label for="description"><?php _e('About me', 'terraclassifieds') ?></label>
						<textarea name="description" id="description" rows="3" cols="50"><?php the_author_meta('description', $current_user->ID); ?></textarea>
					</p><!-- .form-textarea -->
					<p class="form-avatar">
						<?php $avatar_url = get_user_meta($current_user->ID, '_tc_avatar', 1); ?>
						<?php if (!empty($avatar_url)) { ?>
							<img class="terraclassifieds-user-avatar" src="<?php echo esc_url($avatar_url); ?>" alt="user-avatar" />
						<?php } ?>
						<label for="profilepicture"><?php _e('Avatar', 'terraclassifieds') ?></label>
						<input type="file" id="profilepicture" name="profilepicture">
						<br />
						<input type="checkbox" name="remove-avatar" id="remove-avatar" value="">
						<label for="remove-avatar" class="remove-avatar-label"><?php echo __('Remove avatar', 'terraclassifieds'); ?></label>
					</p>
					<p class="form-submit">
						<input name="updateuser" type="submit" id="updateuser" class="submit button" value="<?php _e('Update', 'terraclassifieds'); ?>" />
						<?php wp_nonce_field('update-user') ?>
						<input name="action" type="hidden" id="action" value="update-user" />
					</p>
				</form>
			<?php endif; ?>
		</div>
	</div>
	</div>

<?php return ob_get_clean();
}

/* FAVOURITE ADS */
add_shortcode('terraclassifieds_favourite_ads', 'terraclassifieds_favourite_ads');
function terraclassifieds_favourite_ads($atts)
{

	ob_start(); ?>
	<?php if (is_user_logged_in()) { ?>
		<?php
		$currency = terraclassifieds_get_option('_tc_advert_currency', '$');
		$unit_position = terraclassifieds_get_option('_tc_unit_position', 1);
		$text_limit = terraclassifieds_get_option('_tc_category_view_text_limit', '10');
		$add_to_favourites = terraclassifieds_get_option('_tc_category_view_add_to_favourites', 0);
		$no_image = terraclassifieds_get_option('_tc_image_no_image', 0);
		if (!empty($no_image)) {
			$no_image_id = attachment_url_to_postid($no_image);
		}
		?>
		<div class="terraclassifieds-favourite-ads">
			<div class="terraclassifieds-container terraclassifieds-archive">

				<div class="terraclassifieds-items">

					<?php $like_these_ads = get_user_option('tcf_user_like', get_current_user_id()); ?>
					<?php if (!empty($like_these_ads)) {

						$args = array(
							'post_type' => 'classified',
							'numberposts' => 999,
							'meta_key' => '_tcf_fav_count',
							'orderby' => 'meta_value',
							'order' => 'DESC',
							'post__in' => $like_these_ads
						);
						$most_loved = get_posts($args);
						foreach ($most_loved as $loved) :
							$price = get_post_meta($loved->ID, '_tc_price', true);
							$sell_type = get_post_meta($loved->ID, '_tc_sell_type', 'price');
							$ad_time = get_the_time('U', $loved->ID);
					?>

							<article class="type-classified">
								<div class="terraclassifieds-item terraclassifieds-clear">
									<?php
									$gallery = get_post_meta($loved->ID, '_tc_gallery', true);
									if (!empty($gallery) || !empty($no_image)) { ?>
										<div class="terraclassifieds-image">
											<a href="<?php echo get_permalink($loved->ID); ?>">
												<?php
												$file_list_meta_key = '_tc_gallery';
												$img_size = 'tcf-archive';
												$tcf_image_archive_size_height = terraclassifieds_get_option_base_functions('_tc_image_archive_height', '145');
												// Get the list of files
												$files = get_post_meta($loved->ID, $file_list_meta_key, 1);
												echo '<div class="terraclassifieds-gallery" style="min-height: ' . esc_attr($tcf_image_archive_size_height) . 'px;">';
												$files_counter = 0;
												// Loop through them and output an image
												foreach ((array) $files as $attachment_id => $attachment_url) {
													if ($files_counter < 1) {
														echo '<div class="terraclassifieds-gallery-element">';
														echo '<div class="terraclassifieds-gallery-element-in">';
														if ($add_to_favourites && is_user_logged_in()) { ?>
															<div class="terraclassifieds-fav">
																<?php
																$user_ID = get_current_user_id();
																// retrieve the total love count for this item
																$love_count = li_get_love_count($loved->ID);
																if (!tcf_user_has_liked_post($user_ID, $loved->ID)) {
																	echo '<span class="fav-it" data-post-id="' . get_the_ID() . '" data-user-id="' .  esc_attr($user_ID) . '">&nbsp;</span>';
																} else {
																	echo '<span class="liked" data-post-id="' . esc_attr($loved->ID) . '" data-user-id="' .  esc_attr($user_ID) . '"></span>';
																}
																?>
															</div>
												<?php }
														if (!empty($gallery)) {
															echo wp_get_attachment_image($attachment_id, $img_size);
														} else if (!empty($no_image)) {
															echo wp_get_attachment_image($no_image_id, $img_size);
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
											<h2 class="terraclassifieds-title"><a class="terraclassifieds-title-fav" href="<?php echo get_permalink($loved->ID); ?>"><?php echo get_the_title($loved->ID); ?></a></h2>
										</header>

										<div class="terraclassifieds-category">
											<?php the_terms($loved->ID, 'ad_category') ?>
										</div>

										<div class="terraclassifieds-desc">
											<?php
											$content_post = get_post($loved->ID);
											$content = $content_post->post_content;
											$shortexcerpt = wp_trim_words($content, $text_limit, $more = '… ');
											echo $shortexcerpt;
											?>
										</div>

									</div>

									<div class="terraclassifieds-details">

										<?php if (!empty($price) || $sell_type == 'for_free' || $sell_type == 'exchange') { ?>
											<div class="terraclassifieds-price">
												<?php if ($sell_type == 'for_free') { ?>
													<?php echo __('For Free', 'terraclassifieds'); ?>
												<?php } else if ($sell_type == 'exchange') { ?>
													<?php echo __('Exchange', 'terraclassifieds'); ?>
												<?php } else { ?>
													<?php if (!empty($currency) && $unit_position == 0) { ?>
														<?php echo esc_attr($currency); ?>
													<?php } ?>
													<?php terraclassifiedsPriceFormat($price); ?>
													<?php if (!empty($currency) && $unit_position == 1) { ?>
														<?php echo esc_attr($currency); ?>
													<?php } ?>
												<?php } ?>
											</div>
										<?php } ?>

										<div class="terraclassifieds-date">
											<?php echo terraclassifieds_date_ago($ad_time); ?>
										</div>

									</div>
								</div>
							</article>
						<?php endforeach; ?>
					<?php } ?>
				</div>
			</div>
		</div>
		</div>
	<?php } else { ?>
		<?php
		$page_login_slug = terraclassifieds_get_option('_tc_slug_login', 'login');
		$page_login = get_page_link(get_page_by_path($page_login_slug));
		echo '<div class="terraclassifieds-message info"><h3><a href="' . esc_url($page_login) . '">' . __('Please login', 'terraclassifieds') . '</a></h3></div>';
		?>
	<?php } ?>
<?php return ob_get_clean();
}

// List of payments for payment process
if (!function_exists('terraclassifieds_frontend_payments_list')) {
	function terraclassifieds_frontend_payments_list($data, $send_notification = 0) {
		global $wpdb;
		$post_id = intval($data['post_id']);
		$user_id = get_current_user_id();
		$category = get_post_meta($post_id,'_tc_category',true);
		$ads_id_category = get_post_meta($post_id,'_tc_id_category',true);
		$post = get_post($post_id);
		$ads_price = 0;
		$ads_category = ucfirst($category);
		$ads_id = $post_id;
		$task = $data['task'];
		
		if ($task === 'set_payment_method') {
			$payment_id = intval($data['payment_id']);
			$table = $wpdb->prefix.'terraclassifieds_payments';
			$payment_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d ORDER BY id DESC LIMIT 1",$payment_id));
			$table_items = $wpdb->prefix.'terraclassifieds_payment_items';
			$payment_items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_items WHERE id_payment = %d ORDER BY id DESC",$payment_id));
			$payment_hash = $payment_data->payment_hash;
			$ads_price_data = terraclassifieds_calculate_price($payment_data->price);
			$ads_price = $ads_price_data['price'];
			$ads_price_text = $ads_price_data['price_text'];
		}elseif($task == 'renew'){
			$ads_price_data = terraclassifieds_calculate_ads_price($ads_id_category,true,true);
			$ads_price = $ads_price_data['ads_price'];
			$payment_time = current_time('mysql');
			$payment_hash = terraclassifieds_generate_uniqid();
			$table = $wpdb->prefix.'terraclassifieds_payments';
			$data = array('id_item' => $ads_id, 'id_user' => intval($user_id),'status' => 'pending','price' => $ads_price,'ip_address' => getUserIP(),'type'=>'Ads Category','payment_hash' => $payment_hash,'operation' => $task);
			$format = array('%d','%d','%s','%f','%s','%s','%s','%s');
			$wpdb->insert($table,$data,$format);
			$payment_id = $wpdb->insert_id;
			
			$payment_items = $ads_price_data['payment_items'];
			if (!empty($payment_items) && is_array($payment_items)) {
				$table_items = $wpdb->prefix.'terraclassifieds_payment_items';
				foreach ($payment_items as $k=>$item) {
					$data = array('id_payment' => $payment_id, 'type' => $k, 'price' => $item);
					$format = array('%d','%s','%f');
					$wpdb->insert($table_items,$data,$format);
				}
			}
			
			$payment_items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_items WHERE id_payment = %d ORDER BY id DESC",$payment_id));
			$post = get_post($ads_id);
			$arg = array('ID' => $ads_id, 'post_status' => 'pending');
			wp_update_post($arg);
			
			$ads_category_price_text = $ads_price_data['ads_category_price_text'];
			$ads_price = $ads_price_data['ads_price'];
			$ads_price_text = $ads_price_data['ads_price_text'];
		}
		
		$vat_text = $ads_price_data['vat_text'];
		$vat_rate = $ads_price_data['vat_rate'];
		$net_text = $ads_price_data['net_text'];
		$use_offline_payment = intval(terraclassifieds_get_option('_tc_monetizing_use_offline_payment',0)); 
		$use_paypal_payment = intval(terraclassifieds_get_option('_tc_monetizing_use_paypal_payment',0));
		?>
			<div>
				<a class="button" onclick="javascript:window.history.back();"><?php echo __('Back To Edit - Button','terraclassifieds'); ?></a>
			</div>
			<div class="terraclassifieds-payment-details">
				<h4 class="terraclassifieds-payment-details-head"><?php echo __('Payment Details','terraclassifieds'); ?></h4>
				<table>
					<tbody>
						<tr>
							<td><?php echo __('Advert','terraclassifieds'); ?></td>
							<td><?php echo esc_html($post->post_title); ?></td>
						</tr>
						<tr>
							<td><?php echo __('Advert ID','terraclassifieds'); ?></td>
							<td><?php echo esc_html($ads_id); ?></td>
						</tr>
						<?php if (!empty($payment_items) && is_array($payment_items)) : ?>
							<?php foreach ($payment_items as $k=>$item) : ?>
								<tr>
									<td><?php echo __(ucfirst($item->type),'terraclassifieds'); ?></td>
									<td><?php echo esc_html(terraclassifieds_format_price($item->price)); ?></td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
						<tr>
							<td><?php echo __('Net cost','terraclassifieds'); ?></td>
							<td><?php echo esc_html($net_text); ?></td>
						</tr>
						<tr>
							<td><?php echo sprintf(__('Tax (%s%%)','terraclassifieds'),esc_html($vat_rate)); ?></td>
							<td><?php echo esc_html($vat_text); ?></td>
						</tr>
						<tr>
							<td><?php echo __('Total','terraclassifieds'); ?></td>
							<td><?php echo esc_html($ads_price_text); ?></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="terraclassifieds-payments-method-list">
				<h4 class="terraclassifieds-payments-method-list-head"><?php echo __('Payment Methods','terraclassifieds'); ?></h4>
				<table class="terraclassifieds-payments-method-list-table">
					<tbody>
						<?php 
							$offline_payment_image = terraclassifieds_get_option( '_tc_monetizing_offline_payment_logo', '' );
							$paypal_payment_image = terraclassifieds_get_option( '_tc_monetizing_paypal_payment_logo', '' );
						?>
						<?php if($use_offline_payment) : ?>
							<tr class="terraclassifieds-payments-method-list-item">
								<td class="terraclassifieds-payments-method-list-item-image">
									<?php if ($offline_payment_image) : ?>
										<img src="<?php echo stripslashes_deep($offline_payment_image); ?>" alt="Offline Payment Logo" title="Offline Payment Logo" class="tc_monetizing_offline_payment_logo" />
									<?php else : ?>
										&nbsp;
									<?php endif; ?>
								</td>
								<td class="terraclassifieds-payments-method-list-item-info">
									<p><?php echo __('Offline Payment','terraclassifieds'); ?></p>
									<p><?php echo __('Select this method to pay with bank transfer or other offline payment methods.','terraclassifieds'); ?></p>
								</td>
								<td class="terraclassifieds-payments-method-list-item-button">
									<form method="post" name="form_payment_offline" id="form-payment-offline">
										<?php wp_nonce_field(); ?>
										<input type="hidden" name="ads_id" value="<?php echo esc_attr($post_id); ?>" />
										<input type="hidden" name="ads_charging_price" value="<?php echo esc_attr($ads_price); ?>" />
										<input type="hidden" name="ads_id_category" value="<?php echo esc_attr($ads_id_category); ?>" />
										<input type="hidden" name="ads_user_id" value="<?php echo esc_attr($user_id); ?>" />
										<input type="hidden" id="payment-id" name="payment_id" value="<?php echo esc_attr($payment_id); ?>" />
										<input type="hidden" name="task" value="payment" />
										<input type="hidden" name="payment_method" value="offline" />
										
										<button type="button" class="button-secondary terraclassified-payment-method" name="offline_payment" id="offline-payment-button" data-method="offline" data-operation="renew" data-send-notification="<?php echo esc_attr($send_notification); ?>" data-ads-id="<?php echo esc_attr($ads_id); ?>" data-payment-hash="<?php echo esc_attr($payment_hash); ?>" /><span class="offline-payment-button-loader"><i class='fas fa-spinner fa-spin'></i></span><span><?php echo __('Buy Now','terraclassifieds'); ?></span></button>
									</form>
								</td>
							</tr>
						<?php endif; ?>
						<?php if($use_paypal_payment) : ?>
							<tr class="terraclassifieds-payments-method-list-item">
								<td class="terraclassifieds-payments-method-list-item-image">
									<?php if ($paypal_payment_image) : ?>
										<img src="<?php echo stripslashes_deep($paypal_payment_image); ?>" alt="PayPal Payment Logo" title="PayPal Payment Logo" class="tc_monetizing_paypal_payment_logo" />
									<?php else : ?>
										&nbsp;
									<?php endif; ?>
								</td>
								<td class="terraclassifieds-payments-method-list-item-info">
									<p><?php echo __('PayPal','terraclassifieds'); ?></p>
									<p><?php echo __('One of the most popular online payment methods enables you to pay securely with your PayPal account, VISA, MasterCard. A PayPal account is not required to pay with payment cards. Just click “Create account” on the PayPal payment page to check options.','terraclassifieds'); ?></p>
								</td>
								<td class="terraclassifieds-payments-method-list-item-button">
									<?php 
										$paypal_test_mode = terraclassifieds_get_option('_tc_monetizing_paypal_payment_test_mode',1);
										$paypal_email_id = terraclassifieds_get_option('_tc_monetizing_paypal_payment_business_email_id','');
										$paypal_return_successful_url = home_url('/').terraclassifieds_get_option('_tc_monetizing_paypal_payment_return_successful_url','paypal-payment-success');
										$paypal_return_cancel_url = home_url('/').terraclassifieds_get_option('_tc_monetizing_paypal_payment_return_cancel_url','paypal-payment-cancel');
										$paypal_return_notify_url = home_url('/').terraclassifieds_get_option('_tc_monetizing_paypal_payment_return_notify_url','paypal-payment-notify');
										$paypal_currency_code = terraclassifieds_get_option('_tc_monetizing_paypal_payment_currency_code','PLN');
										$paypalUrl = $paypal_test_mode ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';
									?>
									
									<form name="form_payment_paypal" id="form-payment-paypal" action="<?php echo $paypalUrl; ?>" method="post">
										<input type="hidden" name="business" value="<?php echo esc_attr($paypal_email_id); ?>">                                                                                                
										<input type="hidden" name="cmd" value="_xclick"> 
										<input type="hidden" id="paypal-item-name" name="item_name" value="<?php echo sprintf( __('Payment for ads: %s,Payment id: %s', 'terraclassifieds'),$post->post_title,$payment_id);?>">
										<input type="hidden" id="paypal-item-number" name="item_number" value="<?php echo esc_attr($payment_hash); ?>">
										<input type="hidden" name="amount" value="<?php echo esc_attr($ads_price); ?>">
										<input type="hidden" name="currency_code" value="<?php echo esc_attr($paypal_currency_code); ?>">
										<input type="hidden" name="return" value="<?php echo stripslashes_deep($paypal_return_successful_url); ?>">
										<input type="hidden" name="notify_url" value="<?php echo stripslashes_deep($paypal_return_notify_url); ?>">
										<input type="hidden" name="cancel_return" value="<?php echo stripslashes_deep($paypal_return_cancel_url); ?>">
										
										<button class="button-secondary terraclassified-payment-method" type="button" name="paypal_payment" id="paypal-payment-button" data-method="paypal" data-operation="renew" data-send-notification="<?php echo esc_attr($send_notification); ?>" data-ads-id="<?php echo esc_attr($ads_id); ?>" data-payment-hash="<?php echo esc_attr($payment_hash); ?>" /><span class="paypal-payment-button-loader"><i class='fas fa-spinner fa-spin'></i></span><span><?php echo __('Buy Now','terraclassifieds'); ?></span></button>
									</form>
								</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		<?php
	}
}

// Payment details
if (!function_exists('terraclassifieds_frontend_payment_details')) {
	function terraclassifieds_frontend_payment_details() {
		$wpnonce = $_POST['_wpnonce'];
			if (!wp_verify_nonce($wpnonce)) {
				die( __( 'Security check', 'textdomain' ) ); 
			} else {
				$ads_id = intval($_POST['ads_id']);
				$ads_charging_price = floatval($_POST['ads_charging_price']);
				$post = get_post($ads_id);
				$method = $_POST['payment_method'];
				echo $payment_id = intval($_POST['payment_id']);
				
				if (!$unit_position) {
						$ads_price_text = $currency.' '.terraclassifiedsPriceFormat($ads_charging_price,1);
					}else{
						$ads_price_text = terraclassifiedsPriceFormat($ads_charging_price,1).' '.$currency;
					}
				$use_offline_payment_information = terraclassifieds_get_option( '_tc_monetizing_use_offline_payment_information', __('Enter details for your bank account or other necessary information required for making a payment.','terraclassifieds'));
				if ($payment_id) {
					if ($method === 'offline') : ?>
						<div class="terraclassifieds-offline-payment-summary">
							<h4 class="terraclassifieds-offline-payment-summary-head"><?php echo __('Offline Payment','terraclassifieds'); ?></h4>
							<table>
								<tbody>
									<tr>
										<td><?php echo __('Advert','terraclassifieds'); ?></td>
										<td><?php echo esc_html($post->post_title); ?></td>
									</tr>
									<tr>
										<td><?php echo __('Price','terraclassifieds'); ?></td>
										<td><?php echo esc_html($ads_price_text); ?></td>
									</tr>
									<tr>
										<td><?php echo __('Advert ID','terraclassifieds'); ?></td>
										<td><?php echo esc_html($ads_id); ?></td>
									</tr>
									<tr>
										<td><?php echo __('Payment ID','terraclassifieds'); ?></td>
										<td><?php echo esc_html($payment_id); ?></td>
									</tr>
									<tr>
										<td colspan="2">
											<p>
												<?php echo __('Payment Information','terraclassifieds'); ?>
											</p>
											<p>
												<?php echo esc_html($use_offline_payment_information); ?>
											</p>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					<?php endif;
				}
			}
	}
}

// PayPal Payment Success
add_shortcode( 'terraclassifieds_paypal_payment_success', 'terraclassifieds_paypal_payment_success_body' );
function terraclassifieds_paypal_payment_success_body( $atts ){
	$my_submissions_url = home_url('/').terraclassifieds_get_option('_tc_slug_my_submissions','my-submissions');
	?>
		<p>
			<?php echo __('Your payment has been successful.','terraclassifieds'); ?>
		</p>
		<a class="" href="<?php echo stripslashes_deep($my_submissions_url); ?>" title="<?php echo esc_attr('My submissions','terraclassifieds'); ?>" alt="<?php echo esc_attr('My submissions','terraclassifieds'); ?>"><?php echo esc_attr('Go to the list of your ads.','terraclassifieds'); ?></a>
	<?php
}

// PayPal Payment Success
add_shortcode( 'terraclassifieds_paypal_payment_cancel', 'terraclassifieds_paypal_payment_cancel_body' );
function terraclassifieds_paypal_payment_cancel_body( $atts ){
	$my_submissions_url = home_url('/').terraclassifieds_get_option('_tc_slug_my_submissions','my-submissions');
	?>
		<p>
			<?php echo __('Your payment failed.','terraclassifieds'); ?>
		</p>
		<a class="" href="<?php echo stripslashes_deep($my_submissions_url); ?>" title="<?php echo esc_attr('My submissions','terraclassifieds'); ?>" alt="<?php echo __('My submissions','terraclassifieds'); ?>"><?php echo esc_attr('Go to the list of your ads and try again.','terraclassifieds'); ?></a>
	<?php
}

// PayPal Payment Success
add_shortcode( 'terraclassifieds_paypal_payment_notify', 'terraclassifieds_paypal_payment_notify_body' );
function terraclassifieds_paypal_payment_notify_body(){
	global $wpdb;
	/** Production Postback URL */
    $verify_uri = 'https://ipnpb.paypal.com/cgi-bin/webscr';
    /** Sandbox Postback URL */
    $sandbox_verify_uri = 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';

    /** Response from PayPal indicating validation was successful */
    $valid = 'VERIFIED';
    /** Response from PayPal indicating validation failed */
    $invalid = 'INVALID';
	
	if (!count($_POST)) {
		throw new Exception("Missing POST Data");
	}

	$raw_post_data = file_get_contents('php://input');
	$raw_post_array = explode('&', $raw_post_data);
	$myPost = array();
	foreach ($raw_post_array as $keyval) {
		$keyval = explode('=', $keyval);
		if (count($keyval) == 2) {
			// Since we do not want the plus in the datetime string to be encoded to a space, we manually encode it.
			if ($keyval[0] === 'payment_date') {
				if (substr_count($keyval[1], '+') === 1) {
					$keyval[1] = str_replace('+', '%2B', $keyval[1]);
				}
			}
			$myPost[$keyval[0]] = urldecode($keyval[1]);
		}
	}

	// Build the body of the verification post request, adding the _notify-validate command.
	$req = 'cmd=_notify-validate';
	$get_magic_quotes_exists = false;
	if (function_exists('get_magic_quotes_gpc')) {
		$get_magic_quotes_exists = true;
	}
	foreach ($myPost as $key => $value) {
		if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
			$value = urlencode(stripslashes($value));
		} else {
			$value = urlencode($value);
		}
		$req .= "&$key=$value";
	}
	
	// Post the data back to PayPal, using curl. Throw exceptions if errors occur.
	$paypal_test_mode = terraclassifieds_get_option('_tc_monetizing_paypal_payment_test_mode',1);
	if ($paypal_test_mode) {
		$ch = curl_init($sandbox_verify_uri);
	}
	else{
		$ch = curl_init($verify_uri);
	}
	curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
	curl_setopt($ch, CURLOPT_SSLVERSION, 6);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'User-Agent: PHP-IPN-Verification-Script',
		'Connection: Close',
	));
	$res = curl_exec($ch);
	if (!($res)) {
		$errno = curl_errno($ch);
		$errstr = curl_error($ch);
		curl_close($ch);
		throw new Exception("cURL error: [$errno] $errstr");
	}

	$info = curl_getinfo($ch);
	$http_code = $info['http_code'];
	if ($http_code != 200) {
		throw new Exception("PayPal responded with http code $http_code");
	}

	curl_close($ch);
	// Check if PayPal verifies the IPN data, and if so, return true.
	if ($res == $valid) {
		$item_number = $_POST['item_number'];
		$item_name = $_POST['item_name'];
		$payment_status = $_POST['payment_status'];
		$payment_type = $_POST['payment_type'];
		$payment_date = $_POST['payment_date'];
		$payment_time = current_time('mysql');
		$custom = $_POST['custom'];
		$table = $wpdb->prefix.'terraclassifieds_payments';
		$payment_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE payment_hash = '%s'",$item_number));
		if ($payment_status == 'Completed') {
			$txn_id = $_POST['txn_id'];
			$wpdb->update('pe_terraclassifieds_payments', array('status' => 'completed','transaction_hash' => $txn_id,'datetime' => $payment_time), array('payment_hash' => $item_number) , array('%s','%s','%s'),array('%s'));
			$arg = array('ID' => intval($payment_data->id_item), 'post_status' => 'publish');
			$expire_time_field = terraclassifieds_generate_ads_expired_time();
			update_post_meta(intval($payment_data->id_item), '_tc_expire_date', $expire_time_field);
			update_post_meta(intval($payment_data->id_item), '_tc_expire_soon_notification_done', false);
			wp_update_post($arg);
			terraclassifieds_sendmail_update_status3(intval($payment_data->id_item),$payment_status);
			terraclassifieds_sendmail_admin_payment_completed(intval($payment_data->id));
		}elseif($payment_status == 'Pending'){
			$wpdb->update('pe_terraclassifieds_payments', array('status' => 'pending','transaction_hash' => null), array('payment_hash' => $item_number), array('%s'));
			$arg = array('ID' => (int) $item_id, 'post_status' => 'pending');
			wp_update_post($arg);
			terraclassifieds_sendmail_update_status3(intval($payment_data->id_item),$payment_status);
		}else{
			$wpdb->update('pe_terraclassifieds_payments', array('status' => 'cancelled','transaction_hash' => null), array('payment_hash' => $item_number), array('%s'));
			$arg = array('ID' => (int) $item_id, 'post_status' => 'rejected');
			wp_update_post($arg);
			terraclassifieds_sendmail_update_status3(intval($payment_data->id_item),$payment_status);
		}
	} else {
		
	}
}

//Front Payments List
add_shortcode('terraclassifieds_my_payments', 'terraclassifieds_my_payments_page' );
function terraclassifieds_my_payments_page() {
	$user_id = get_current_user_id();
	
	if($user_id != 0){
		global $wpdb;
		$table = $wpdb->prefix.'terraclassifieds_payments';
		
		$posts_per_page = get_option( 'posts_per_page' );
		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
		$payments_query = "SELECT * FROM $table WHERE id_user = %d ORDER BY id DESC";
		$payments_count = count($wpdb->get_results($wpdb->prepare($payments_query,$user_id)));
		$starting_position = (($paged - 1) * $posts_per_page);
		$limit = ' LIMIT %d,%d';
		$count_pages = intval(ceil($payments_count/$posts_per_page));
		
		$payments_list = $wpdb->get_results($wpdb->prepare($payments_query.$limit,$user_id,$starting_position,$posts_per_page));
		$payment_type = array('offline'=>__('Offline payment','terraclassifieds'),'paypal'=>__('PayPal payment','terraclassifieds'));
		$payment_status = array('pending'=>__('Pending','terraclassifieds'),'completed'=>__('Completed','terraclassifieds'),'cancelled'=>__('Cancelled','terraclassifieds'));
		$currency = terraclassifieds_get_option( '_tc_advert_currency', '$' );
		$unit_position = (int) terraclassifieds_get_option( '_tc_unit_position', 1 );	
		
		if (!empty($payments_list)) {
			?>
				<table class="terraclassifieds-my-payments" id="terraclassifieds-my-payments">
					<tr>
						<th><?php echo __( 'Ads Name', 'terraclassifieds'); ?></th>
						<th><?php echo __( 'Price', 'terraclassifieds'); ?></th>
						<th><?php echo __( 'Payment Type', 'terraclassifieds'); ?></th>
						<th><?php echo __( 'Date', 'terraclassifieds'); ?></th>
						<th><?php echo __( 'Status', 'terraclassifieds'); ?></th>
					</tr>
					<?php foreach ($payments_list as $k=>$row) : ?>
						<?php $post = get_post($row->id_item); ?>
						<tr>
							<td>
								<?php echo $post->post_title; ?><br/>
								ID: <?php echo $post->ID; ?>,PID: <?php  echo $row->id;?>
							</td>
							<td>
								<?php if (!$unit_position): ?>
									<?php echo $currency; ?>
									<?php echo terraclassifiedsPriceFormat($row->price); ?>
								<?php else: ?>
									<?php echo terraclassifiedsPriceFormat($row->price); ?>
									<?php echo $currency; ?>
								<?php endif; ?>
							</td>
							<td><?php echo esc_html($payment_type[$row->method]); ?></td>
							<td><?php echo esc_html($row->datetime); ?></td>
							<td><?php echo esc_html(ucwords(__($row->status,'terraclassifieds'))); ?></td>
						</tr>
					<?php endforeach; ?>
				</table>
				
				<?php terraclassifieds_pagination($count_pages); ?>
			<?php
		}else{
			if ( is_paged() ) {
				$paged = 1;
				$redirect_url_prev =  esc_url_raw( get_pagenum_link( $paged ) );
				wp_safe_redirect( $redirect_url_prev );
				exit;
			} else {
				echo __( 'No payments found.', 'terraclassifieds' );
			}
		}
	}
	else{
		$page_login_slug = terraclassifieds_get_option( '_tc_slug_login', 'login' );
		$page_login = get_page_link( get_page_by_path( $page_login_slug ) );
		echo '<div class="terraclassifieds-message"><h3><a href="'.$page_login.'">'.__( 'Login', 'terraclassifieds' ).'</a> ' .__( 'to see your submissions.', 'terraclassifieds' ). '</h3></div>';
	}
	
	?>
	<?php
}
