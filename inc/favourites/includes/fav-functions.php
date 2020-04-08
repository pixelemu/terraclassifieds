<?php

// check whether a user has loved an item
function tcf_user_has_liked_post($user_id, $post_id) {

	// get all item IDs the user has loved
	$loved = get_user_option('tcf_user_like', $user_id);
	if(is_array($loved) && in_array($post_id, $loved)) {
		return true; // user has loved post
	}
	return false; // user has not loved post
}

// adds the loved ID to the users meta so they can't love it again
function tcf_store_liked_id_for_user($user_id, $post_id) {
	$loved = get_user_option('tcf_user_like', $user_id);
	if(is_array($loved)) {
		$loved[] = $post_id;
	} else {
		$loved = array($post_id);
	}
	update_user_option($user_id, 'tcf_user_like', $loved);
}

// increments a love count
function tcf_mark_post_as_liked($post_id, $user_id) {

	// retrieve the love count for $post_id	
	$love_count = get_post_meta($post_id, '_tcf_fav_count', true);
	if($love_count)
		$love_count = $love_count + 1;
	else
		$love_count = 1;
	
	if(update_post_meta($post_id, '_tcf_fav_count', $love_count)) {	
		// store this post as loved for $user_id	
		tcf_store_liked_id_for_user($user_id, $post_id);
		return true;
	}
	return false;
}

// returns a love count for a post
function li_get_love_count($post_id) {
	$love_count = get_post_meta($post_id, '_tcf_fav_count', true);
	if($love_count)
		return $love_count;
	return 0;
}

// processes the ajax request
function tcf_process_like() {
	if ( isset( $_POST['item_id'] ) && wp_verify_nonce($_POST['like_it_nonce'], 'love-it-nonce') ) {
		if(tcf_mark_post_as_liked($_POST['item_id'], $_POST['user_id'])) {
			echo 'liked';
		} else {
			echo 'failed';
		}
	}
	die();
}
add_action('wp_ajax_like_it', 'tcf_process_like');

function tcfDeleteElement($element, &$array){
    $index = array_search($element, $array);
    if($index !== false){
        unset($array[$index]);
    }
}

function tcf_store_unlike_id_for_user() {
	$loved = get_user_option('tcf_user_like', $user_id);
	$post_id = sanitize_text_field($_POST['item_id']);
	$user_id = sanitize_text_field($_POST['user_id']);
	tcfDeleteElement($post_id, $loved);
	update_user_option( $user_id, 'tcf_user_like', $loved );
}

add_action('wp_ajax_unlike_it', 'tcf_store_unlike_id_for_user');