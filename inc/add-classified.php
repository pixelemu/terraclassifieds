<?php

// get post by slug
function terraclassifieds_post_by_slug( $slug, $post_type = 'post', $unique = true ){
	$args=array(
		'name' => $slug,
		'post_type' => $post_type,
		'post_status' => 'publish',
		'posts_per_page' => 1
	);
	$my_posts = get_posts( $args );
	if( $my_posts ) {
		if( $unique ){
			return $my_posts[ 0 ];
		}else{
			return $my_posts;
		}
	}
	return false;
}

// add page with add advert shortcode
function terraclassifieds_add_item_page() {

	$post_slug = terraclassifieds_get_option( '_tc_slug_add_advert', 'add-advert' );

	if( terraclassifieds_post_by_slug( $post_slug, 'page') ) {
		return;
	}

    // Create post object
    $my_post = array(
      'post_title'    => wp_strip_all_tags( 'Terraclassifieds - Add advert' ),
      'post_content'  => '[terraclassifieds_add_item]',
      'post_status'   => 'publish',
      'post_author'   => 1,
	  'post_type'     => 'page',
	  'post_name'     => $post_slug,
    );

    // Insert the post into the database
	wp_insert_post( $my_post );

	return true;
}
terraclassifieds_add_item_page();

// add page with my submissions shortcode
function terraclassifieds_my_submissions_page() {

	$post_slug = terraclassifieds_get_option( '_tc_slug_my_submissions', 'my-submissions' );

	if( terraclassifieds_post_by_slug( $post_slug, 'page') ) {
		return;
	}

    // Create post object
    $my_post = array(
      'post_title'    => wp_strip_all_tags( 'Terraclassifieds - My submissions' ),
      'post_content'  => '[terraclassifieds_my_submissions]',
      'post_status'   => 'publish',
      'post_author'   => 1,
	  'post_type'     => 'page',
	  'post_name'     => $post_slug,
    );

    // Insert the post into the database
	wp_insert_post( $my_post );

	return true;
}
terraclassifieds_my_submissions_page();

// add page with edit ad shortcode
function terraclassifieds_edit_page() {

    $post_slug = terraclassifieds_get_option( '_tc_slug_edit_advert', 'edit-ad' );

	if( terraclassifieds_post_by_slug( $post_slug, 'page') ) {
		return;
	}

    // Create post object
    $my_post = array(
      'post_title'    => wp_strip_all_tags( 'Terraclassifieds - Edit ad' ),
      'post_content'  => '[cmb-frontend-form-edit]',
      'post_status'   => 'publish',
      'post_author'   => 1,
	  'post_type'     => 'page',
	  'post_name'     => $post_slug,
    );

    // Insert the post into the database
	wp_insert_post( $my_post );

	return true;
}
terraclassifieds_edit_page();

// add page with registration
function terraclassifieds_registration_page() {

    $post_slug = terraclassifieds_get_option( '_tc_slug_registration', 'registration' );

	if( terraclassifieds_post_by_slug( $post_slug, 'page') ) {
		return;
	}

    // Create post object
    $my_post = array(
      'post_title'    => wp_strip_all_tags( 'Terraclassifieds - Registration' ),
      'post_content'  => '[terraclassifieds_registration]',
      'post_status'   => 'publish',
      'post_author'   => 1,
	  'post_type'     => 'page',
	  'post_name'     => $post_slug,
    );

    // Insert the post into the database
	wp_insert_post( $my_post );

	return true;
}
terraclassifieds_registration_page();

// add page with login
function terraclassifieds_login_page() {

    $post_slug = terraclassifieds_get_option( '_tc_slug_login', 'login' );

	if( terraclassifieds_post_by_slug( $post_slug, 'page') ) {
		return;
	}

    // Create post object
    $my_post = array(
      'post_title'    => wp_strip_all_tags( 'Terraclassifieds - Login' ),
      'post_content'  => '[terraclassifieds_login]',
      'post_status'   => 'publish',
      'post_author'   => 1,
	  'post_type'     => 'page',
	  'post_name'     => $post_slug,
    );

    // Insert the post into the database
	wp_insert_post( $my_post );

	return true;
}
terraclassifieds_login_page();

// add page with forgot password
function terraclassifieds_forgot_password_page() {

    $post_slug = terraclassifieds_get_option( '_tc_slug_forgot_password', 'forgot-password' );

	if( terraclassifieds_post_by_slug( $post_slug, 'page') ) {
		return;
	}

    // Create post object
    $my_post = array(
      'post_title'    => wp_strip_all_tags( 'Terraclassifieds - Forgot Password' ),
      'post_content'  => '[terraclassifieds_forgot_password]',
      'post_status'   => 'publish',
      'post_author'   => 1,
	  'post_type'     => 'page',
	  'post_name'     => $post_slug,
    );

    // Insert the post into the database
	wp_insert_post( $my_post );

	return true;
}
terraclassifieds_forgot_password_page();

// edit profile
function terraclassifieds_edit_profile_page() {

    $post_slug = terraclassifieds_get_option( '_tc_slug_edit_profile', 'edit-profile' );

	if( terraclassifieds_post_by_slug( $post_slug, 'page') ) {
		return;
	}

    // Create post object
    $my_post = array(
      'post_title'    => wp_strip_all_tags( 'Terraclassifieds - Edit Profile' ),
      'post_content'  => '[terraclassifieds_edit_profile]',
      'post_status'   => 'publish',
      'post_author'   => 1,
	  'post_type'     => 'page',
	  'post_name'     => $post_slug,
    );

    // Insert the post into the database
	wp_insert_post( $my_post );

	return true;
}
terraclassifieds_edit_profile_page();

// favourites ads
function terraclassifieds_favourite_ads_page() {

    $post_slug = terraclassifieds_get_option( '_tc_slug_favourite_ads', 'favourite-ads' );

	if( terraclassifieds_post_by_slug( $post_slug, 'page') ) {
		return;
	}

    // Create post object
    $my_post = array(
      'post_title'    => wp_strip_all_tags( 'Terraclassifieds - Favourite Ads' ),
      'post_content'  => '[terraclassifieds_favourite_ads]',
      'post_status'   => 'publish',
      'post_author'   => 1,
	  'post_type'     => 'page',
	  'post_name'     => $post_slug,
    );

    // Insert the post into the database
	wp_insert_post( $my_post );

	return true;
}
terraclassifieds_favourite_ads_page();

//PayPal Payment Success Page
function terraclassifieds_paypal_payment_success_page() {
	$post_slug = terraclassifieds_get_option( '_tc_monetizing_paypal_payment_return_successful_url', 'paypal-payment-success' );

	if( terraclassifieds_post_by_slug( $post_slug, 'page') ) {
		return;
	}

    // Create post object
    $my_post = array(
      'post_title'    => wp_strip_all_tags( 'Terraclassifieds - PayPal Payment Success' ),
      'post_content'  => '[terraclassifieds_paypal_payment_success]',
      'post_status'   => 'publish',
      'post_author'   => 1,
	  'post_type'     => 'page',
	  'post_name'     => $post_slug,
    );

    // Insert the post into the database
	wp_insert_post( $my_post );

	return true;
}

terraclassifieds_paypal_payment_success_page();

//PayPal Payment Cancel Page
function terraclassifieds_paypal_payment_cancel_page() {
	$post_slug = terraclassifieds_get_option( '_tc_monetizing_paypal_payment_return_cancel_url', 'paypal-payment-cancel' );

	if( terraclassifieds_post_by_slug( $post_slug, 'page') ) {
		return;
	}

    // Create post object
    $my_post = array(
      'post_title'    => wp_strip_all_tags( 'Terraclassifieds - PayPal Payment Cancel' ),
      'post_content'  => '[terraclassifieds_paypal_payment_cancel]',
      'post_status'   => 'publish',
      'post_author'   => 1,
	  'post_type'     => 'page',
	  'post_name'     => $post_slug,
    );

    // Insert the post into the database
	wp_insert_post( $my_post );

	return true;
}

terraclassifieds_paypal_payment_cancel_page();


//PayPal Payment Notify Page
function terraclassifieds_paypal_payment_notify_page() {
	$post_slug = terraclassifieds_get_option( '_tc_monetizing_paypal_payment_return_notify_url', 'paypal-payment-notify' );

	if( terraclassifieds_post_by_slug( $post_slug, 'page') ) {
		return;
	}

    // Create post object
    $my_post = array(
      'post_title'    => wp_strip_all_tags( 'Terraclassifieds - PayPal Payment Notify' ),
      'post_content'  => '[terraclassifieds_paypal_payment_notify]',
      'post_status'   => 'publish',
      'post_author'   => 1,
	  'post_type'     => 'page',
	  'post_name'     => $post_slug,
    );

    // Insert the post into the database
	wp_insert_post( $my_post );

	return true;
}

terraclassifieds_paypal_payment_notify_page();

//Payments List Page
function terraclassifieds_payments_list_page() {
	$post_slug = terraclassifieds_get_option( '_tc_slug_my_payments', 'my-payments' );

	if( terraclassifieds_post_by_slug( $post_slug, 'page') ) {
		return;
	}

    // Create post object
    $my_post = array(
      'post_title'    => wp_strip_all_tags( 'Terraclassifieds - My payments' ),
      'post_content'  => '[terraclassifieds_my_payments]',
      'post_status'   => 'publish',
      'post_author'   => 1,
	  'post_type'     => 'page',
	  'post_name'     => $post_slug,
    );

    // Insert the post into the database
	wp_insert_post( $my_post );
	return true;
}
terraclassifieds_payments_list_page();