<?php

function tcf_fav_front_end_js() {
	wp_enqueue_script('favourites', LI_BASE_URL . '/includes/js/favourites.js', array( 'jquery' ) );
	wp_localize_script( 'favourites', 'fav_it_vars', 
		array( 
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce('love-it-nonce'),
			'already_loved_message' => __('You have already added this item to favourites.', 'terraclassifieds'),
			'already_unloved_message' => __('You have already removed this item from favourites.', 'terraclassifieds'),
			'error_message' => __('Sorry, there was a problem processing your request.', 'terraclassifieds'),
			'login_required' => __('Log in to your account to add to favourites.', 'terraclassifieds'),
			'is_user_logged_in' => is_user_logged_in()
		) 
	);	
}
add_action('wp_enqueue_scripts', 'tcf_fav_front_end_js');