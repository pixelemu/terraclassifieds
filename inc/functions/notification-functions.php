<?php
// contact form - send email
if ( ! function_exists( 'terraclassifieds_sendmail' ) ) {
	function terraclassifieds_sendmail( $to, $subject = '',  $username, $email, $user_message = '', $classified_id, $author_id ) {
		
		if(is_user_logged_in()){
			$logged_in_user = wp_get_current_user();
			$username = $logged_in_user->user_login;
			$email = $logged_in_user->user_email;
		}
		
		$headers = array("Content-Type: text/html; charset=UTF-8", "Reply-To: " .$email);
		$advert_link_subject = get_permalink($classified_id);
		$advert_link = '<a href="' . get_permalink($classified_id) . '">' . get_permalink($classified_id) . '</a>';
		$user = get_userdata($author_id);
		$advert_author_login = $user->user_login;

		$email_template_contact_form_subject = terraclassifieds_get_option_base_functions( '_tc_email_template_contact_form_subject', '' );
		$email_template_contact_form_message = terraclassifieds_get_option_base_functions( '_tc_email_template_contact_form_message', '' );
	
		if( empty($to) || empty($user_message) ){
			add_action( 'terraclassifieds_notice', 'terraclassifieds_notice_send_error' );
			return;
		}

		if( empty($subject) ) {
			$subject = $email_template_contact_form_subject;
			$subject = str_replace("[[user_name]]", $advert_author_login, $subject);
			$subject = str_replace("[[contact_author_name]]", $username, $subject);
			$subject = str_replace("[[contact_author_email]]", $email, $subject);
			$subject = str_replace("[[advert_title_link]]", $advert_link_subject, $subject);
			$subject = str_replace("[[contact_message]]", $user_message, $subject);
		}
		
		$message = $email_template_contact_form_message;
		$message = str_replace("[[user_name]]", $advert_author_login, $message);
		$message = str_replace("[[contact_author_name]]", $username, $message);
		$message = str_replace("[[contact_author_email]]", $email, $message);
		$message = str_replace("[[advert_title_link]]", $advert_link, $message);
		$message = str_replace("[[contact_message]]", $user_message, $message);

		$sent = wp_mail($to, $subject, $message, $headers);
		if( $sent ) {
			add_action( 'terraclassifieds_notice', 'terraclassifieds_notice_send_success' );
		} else {
			add_action( 'terraclassifieds_notice', 'terraclassifieds_notice_send_error' );
		}
	}
}

function terraclassifieds_contact_form() {
	if( isset($_POST['tc_form_action']) ) {
	    $to = base64_decode(strtr($_POST['tc_to'], '-_', '+/'));
	    $to = ( isset($to) ) ? sanitize_email($to) : false;
		$author_id = ( isset($_POST['tc_classified_author_id']) ) ? sanitize_text_field($_POST['tc_classified_author_id']) : false;
		$classified_id = ( isset($_POST['tc_classified_id']) ) ? sanitize_text_field($_POST['tc_classified_id']) : false;

		$username = '';
		$email = '';
		$user_message = '';
		
		if( isset($_POST['tc_username']) ) {
			$username = sanitize_text_field($_POST['tc_username']) . "\n\n";
		}
		if( isset($_POST['tc_email']) ) {
			$email = sanitize_email($_POST['tc_email']) . "\n\n";
		}
		if( isset($_POST['tc_message']) ) {
			$user_message = sanitize_text_field($_POST['tc_message']) . "\n\n";
		}

		$subject = '';

		terraclassifieds_sendmail( $to, $subject, $username, $email, $user_message, $classified_id, $author_id );
	}
}
terraclassifieds_contact_form();

// abuse form - send email
if ( ! function_exists( 'terraclassifieds_sendmail_abuse' ) ) {
	function terraclassifieds_sendmail_abuse( $to, $subject = '',  $username, $email, $user_message = '', $classified_id, $author_id ) {

		if(is_user_logged_in()){
			$logged_in_user = wp_get_current_user();
			$username = $logged_in_user->user_login;
			$email = $logged_in_user->user_email;
		}
		
		$headers = array("Content-Type: text/html; charset=UTF-8");
		$email_notification_administrators = terraclassifieds_get_option_base_functions( '_tc_email_notifications_administrators', '' );
		if(!empty($email_notification_administrators)){
		    $email_notification_administrators_array = explode(",", $email_notification_administrators);
		}
		foreach($email_notification_administrators_array as $email_notification_administrators_item){
		    $headers[] = 'Cc: '.$email_notification_administrators_item;
		}
		
		$advert_link_subject = get_permalink($classified_id);
		$advert_link = '<a href="' . get_permalink($classified_id) . '">' . get_permalink($classified_id) . '</a>';
		//$user = get_userdata($author_id);
		//$advert_author_login = $user->user_login;
		
		$email_template_abuse_form_subject = terraclassifieds_get_option_base_functions( '_tc_email_template_abuse_form_subject', '' );
		$email_template_abuse_form_message = terraclassifieds_get_option_base_functions( '_tc_email_template_abuse_form_message', '' );
	
		if( empty($to) || empty($user_message) ){
			add_action( 'terraclassifieds_notice', 'terraclassifieds_notice_send_error' );
			return;
		}

		if( empty($subject) ) {
			$subject = $email_template_abuse_form_subject;
			$subject = str_replace("[[abuse_author_name]]", $username, $subject);
			$subject = str_replace("[[abuse_author_email]]", $email, $subject);
			$subject = str_replace("[[advert_title_link]]", $advert_link_subject, $subject);
			$subject = str_replace("[[abuse_message]]", $user_message, $subject);
		}
		
		$message = $email_template_abuse_form_message;
		$message = str_replace("[[abuse_author_name]]", $username, $message);
		$message = str_replace("[[abuse_author_email]]", $email, $message);
		$message = str_replace("[[advert_title_link]]", $advert_link, $message);
		$message = str_replace("[[abuse_message]]", $user_message, $message);

		$sent = wp_mail($to, $subject, $message, $headers);
		if( $sent ) {
			add_action( 'terraclassifieds_notice', 'terraclassifieds_notice_send_success' );
		} else {
			add_action( 'terraclassifieds_notice', 'terraclassifieds_notice_send_error' );
		}
	}
}

function terraclassifieds_contact_form_abuse() {
	if( isset($_POST['tc_form_action_abuse']) ) {
	    
	    $to = get_option('admin_email');

		$author_id = ( isset($_POST['tc_classified_author_id']) ) ? sanitize_text_field($_POST['tc_classified_author_id']) : false;
		$classified_id = ( isset($_POST['tc_classified_id']) ) ? sanitize_text_field($_POST['tc_classified_id']) : false;

		$username = '';
		$email = '';
		$user_message = '';
		
		if( isset($_POST['tc_username_abuse']) ) {
			$username = sanitize_text_field($_POST['tc_username_abuse']) . "\n\n";
		}
		if( isset($_POST['tc_email_abuse']) ) {
			$email = sanitize_email($_POST['tc_email_abuse']) . "\n\n";
		}
		if( isset($_POST['tc_message_abuse']) ) {
			$user_message = sanitize_text_field($_POST['tc_message_abuse']) . "\n\n";
		}

		$subject = '';

		terraclassifieds_sendmail_abuse( $to, $subject, $username, $email, $user_message, $classified_id, $author_id );
	}
}
terraclassifieds_contact_form_abuse();

// email notifications - registration
if ( !function_exists('terraclassifieds_new_user_notification') ) :
	/**
	 * Pluggable - Email login credentials to a newly-registered user
	 *
	 * A new user registration notification is also sent to admin email.
	 *
	 * @since 2.0.0
	 *
	 * @param int    $user_id        User ID.
	 * @param string $plaintext_pass Optional. The user's plaintext password. Default empty.
	 */
	function terraclassifieds_new_user_notification($user_id, $plaintext_pass = ''){
	
	    $user = get_userdata($user_id);
		$headers = array("Content-Type: text/html; charset=UTF-8");
		$email_notification_administrators = terraclassifieds_get_option_base_functions( '_tc_email_notifications_administrators', '' );
		if(!empty($email_notification_administrators)){
			$email_notification_administrators_array = explode(",", $email_notification_administrators);
			foreach($email_notification_administrators_array as $email_notification_administrators_item){
				$headers[] = 'Cc: '.$email_notification_administrators_item;
			}
		}

		
	    // The blogname option is escaped with esc_html on the way into the database in sanitize_option
	    // we want to reverse this for the plain text arena of emails.
	    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
		
		$gdpr_method = terraclassifieds_get_option( '_tc_gdpr_method', 0 );
		$terms_and_conditions = terraclassifieds_get_option( '_tc_terms_and_conditions', 0 );
		$terms_and_conditions_page = terraclassifieds_get_option( '_tc_terms_and_conditions_page', 0 );
		$privacy_policy = terraclassifieds_get_option( '_tc_privacy_policy', 0 );
		$privacy_policy_page = terraclassifieds_get_option( '_tc_privacy_policy_page', 0 );
		$gdpr = terraclassifieds_get_option( '_tc_gdpr', 0 );
		$gdpr_information = terraclassifieds_get_option( '_tc_gdpr_information', 0 );
		
		$email_template_registration_administrator_subject = terraclassifieds_get_option( '_tc_email_template_registration_administrator_subject', '' );
		$email_template_registration_administrator_message = terraclassifieds_get_option( '_tc_email_template_registration_administrator_message', '' );
		$email_template_registration_user_subject = terraclassifieds_get_option( '_tc_email_template_registration_user_subject', '' );
		$email_template_registration_user_message = terraclassifieds_get_option( '_tc_email_template_registration_user_message', '' );
	    
	    $subject = $email_template_registration_administrator_subject;
		$subject = str_replace("[[website_name]]", $blogname, $subject);
		$subject = str_replace("[[user_login]]", $user->user_login, $subject);
		$subject = str_replace("[[user_email]]", $user->user_email, $subject);
	
		$message = $email_template_registration_administrator_message;
		$message = str_replace("[[website_name]]", $blogname, $message);
		$message = str_replace("[[user_login]]", $user->user_login, $message);
		$message = str_replace("[[user_email]]", $user->user_email, $message);
		
		if($gdpr_method == 0){
			if($terms_and_conditions != 0 || $privacy_policy != 0 || $gdpr != 0){
				$message .= __('Expressed consents:', 'terraclassifieds') . "<br />";
			}
			if($terms_and_conditions != 0){
				$message .= __( 'I agree to the', 'terraclassifieds' ) . ' <a href="' . get_page_link($terms_and_conditions_page) . '" target="_blank">' . __( 'Terms and conditions', 'terraclassifieds' ) . '</a>' . '<br />';
			}
			if($privacy_policy != 0){
				$message .= __( 'I agree to the', 'terraclassifieds' ) . ' <a href="' . get_page_link($privacy_policy_page) . '" target="_blank">' . __( 'Privacy Policy', 'terraclassifieds' ) . '</a>' . '<br />';
			}
			if($gdpr != 0){
				$message .= $gdpr_information;
			}
		}
		
	    @wp_mail(get_option('admin_email'), $subject, $message, $headers);
	
	    if ( empty($plaintext_pass) )
	        return;
		
		if(!empty(terraclassifieds_get_option( '_tc_email_template_reply_to_email_address', '' ))){
			$reply_to_email_address = terraclassifieds_get_option( '_tc_email_template_reply_to_email_address', '' );
		} else {
			$reply_to_email_address = get_option( 'admin_email' );
		}
		$headers = array("Content-Type: text/html; charset=UTF-8", "Reply-To: " . $reply_to_email_address);
	    $subject = $email_template_registration_user_subject;
		$subject = str_replace("[[website_name]]", $blogname, $subject);
		$subject = str_replace("[[user_login]]", $user->user_login, $subject);
		$subject = str_replace("[[user_passwordl]]", $plaintext_pass, $subject);
		
		$message = $email_template_registration_user_message;
		$message = str_replace("[[website_name]]", $blogname, $message);
		$message = str_replace("[[user_login]]", $user->user_login, $message);
		$message = str_replace("[[user_passwordl]]", $plaintext_pass, $message);
	
		if($gdpr_method == 0){
			if($terms_and_conditions != 0 || $privacy_policy != 0 || $gdpr != 0){
				$message .= __('Expressed consents:', 'terraclassifieds') . "<br />";
			}
			if($terms_and_conditions != 0){
				$message .= __( 'I agree to the', 'terraclassifieds' ) . ' <a href="' . get_page_link($terms_and_conditions_page) . '" target="_blank">' . __( 'Terms and conditions', 'terraclassifieds' ) . '</a>' . '<br />';
			}
			if($privacy_policy != 0){
				$message .= __( 'I agree to the', 'terraclassifieds' ) . ' <a href="' . get_page_link($privacy_policy_page) . '" target="_blank">' . __( 'Privacy Policy', 'terraclassifieds' ) . '</a>' . '<br />';
			}
			if($gdpr != 0){
				$message .= $gdpr_information;
			}
		}
	
	    wp_mail($user->user_email, $subject, $message, $headers);
	
	}
endif;

// email notifications - add new advert
if ( !function_exists('terraclassifieds_new_ad_notification') ) :
function terraclassifieds_new_ad_notification($new_submission_id, $post_data, $ad_category_term){
    
    // send notification if no Draft is selected
    if( !isset( $_POST["_tc_draft_status"]) ) {
        $user = get_userdata($post_data['post_author']);
        $advert_author_login = $user->user_login;
        $advert_author_email = $user->user_email;
        $advert_status = terraclassifieds_get_option( '_tc_add_advert_ad_status', 0 );
        if($advert_status == 1){
            $advert_status_value = __( 'published', 'terraclassifieds' );
        } else {
            $advert_status_value = __( 'pending', 'terraclassifieds' );
        }
        $advert_link_subject = get_permalink($new_submission_id);
        $advert_link = '<a href="' . get_permalink($new_submission_id) . '">' . get_permalink($new_submission_id) . '</a>';
        $advert_content = $post_data['post_content'];
        $advert_category = $ad_category_term->name;
        $headers = array("Content-Type: text/html; charset=UTF-8");
        $email_notification_administrators = terraclassifieds_get_option_base_functions( '_tc_email_notifications_administrators', '' );
        if(!empty($email_notification_administrators)){
            $email_notification_administrators_array = explode(",", $email_notification_administrators);
            foreach($email_notification_administrators_array as $email_notification_administrators_item){
                $headers[] = 'Cc: '.$email_notification_administrators_item;
            }
        }
        
        $email_template_new_advert_administrator_subject = terraclassifieds_get_option( '_tc_email_template_new_advert_administrator_subject', '' );
        $email_template_new_advert_administrator_message = terraclassifieds_get_option( '_tc_email_template_new_advert_administrator_message', '' );
        $email_template_new_advert_user_subject = terraclassifieds_get_option( '_tc_email_template_new_advert_user_subject', '' );
        $email_template_new_advert_user_message = terraclassifieds_get_option( '_tc_email_template_new_advert_user_message', '' );
        
        $subject = $email_template_new_advert_administrator_subject;
        if($advert_status == 1){
            $subject = str_replace("[[advert_title_link]]", $advert_link_subject, $subject);
        }
        $subject = str_replace("[[advert_category]]", $advert_category, $subject);
        $subject = str_replace("[[advert_status]]", $advert_status_value, $subject);
        $subject = str_replace("[[advert_desc]]", $advert_content, $subject);
        $subject = str_replace("[[advert_author_login]]", $advert_author_login, $subject);
        $subject = str_replace("[[advert_author_email]]", $advert_author_email, $subject);
        
        $message = $email_template_new_advert_administrator_message;
        if($advert_status == 1){
            $message = str_replace("[[advert_title_link]]", $advert_link, $message);
        }
        $message = str_replace("[[advert_category]]", $advert_category, $message);
        $message = str_replace("[[advert_status]]", $advert_status_value, $message);
        $message = str_replace("[[advert_desc]]", $advert_content, $message);
        $message = str_replace("[[advert_author_login]]", $advert_author_login, $message);
        $message = str_replace("[[advert_author_email]]", $advert_author_email, $message);
        
        @wp_mail(get_option('admin_email'), $subject, $message, $headers);
        
        if(!empty(terraclassifieds_get_option( '_tc_email_template_reply_to_email_address', '' ))){
            $reply_to_email_address = terraclassifieds_get_option( '_tc_email_template_reply_to_email_address', '' );
        } else {
            $reply_to_email_address = get_option( 'admin_email' );
        }
        $headers = array("Content-Type: text/html; charset=UTF-8", "Reply-To: " . $reply_to_email_address);
        
        $subject = $email_template_new_advert_user_subject;
        if($advert_status == 1){
            $subject = str_replace("[[advert_title_link]]", $advert_link_subject, $subject);
        }
        $subject = str_replace("[[advert_category]]", $advert_category, $subject);
        $subject = str_replace("[[advert_status]]", $advert_status_value, $subject);
        $subject = str_replace("[[advert_desc]]", $advert_content, $subject);
        
        $message = $email_template_new_advert_user_message;
        if($advert_status == 1){
            $message = str_replace("[[advert_title_link]]", $advert_link, $message);
        }
        $message = str_replace("[[advert_category]]", $advert_category, $message);
        $message = str_replace("[[advert_status]]", $advert_status_value, $message);
        $message = str_replace("[[advert_desc]]", $advert_content, $message);
        
        wp_mail($user->user_email, $subject, $message, $headers);
    }
    
}
endif;

// custom checkbox in classified edit view for auhor notification
function terraclassifieds_add_publish_meta_options($post_obj) {
  global $post;
  if ( ($post->post_type == 'classified') && (get_post_status( $post->ID ) == 'pending' )){
		$value = get_post_meta($post_obj->ID, 'checkbox_status_change_notification', true); // If saving value to post_meta
	    echo  '<div class="misc-pub-section misc-pub-section-last">';
	    	echo '<label><input type="checkbox"' . (!empty($value) ? ' checked="checked" ' : null) . ' value="1" name="checkbox_status_change_notification" />Send email to author about publish status</label>';
	    echo '</div>';
  }
}
// add the extra options to the 'Publish' box
add_action('post_submitbox_misc_actions', 'terraclassifieds_add_publish_meta_options');

// send email to author when classified changes status - publish in backend when checkbox is ticked
if ( ! function_exists( 'terraclassifieds_sendmail_update_status' ) ) {
	function terraclassifieds_sendmail_update_status($pid = '') {
		if(isset($_POST['checkbox_status_change_notification']) && $_POST['checkbox_status_change_notification'] == 1) {
			
			/*$advert_status = '';
			if((isset($_POST['checkbox_status_change_notification']) && $_POST['checkbox_status_change_notification'] == 1) || isset($_POST["submit-publish-$pid"])){
				$advert_status = 'publish';
			} else if(isset($_POST["submit-archive-$pid"])){
				$advert_status = 'archived';
			}*/

			$email_template_change_status_subject = terraclassifieds_get_option( '_tc_email_template_change_status_subject', '' );
			$email_template_change_status_message = terraclassifieds_get_option( '_tc_email_template_change_status_message', '' );
	
			$classified_id = get_the_ID();
			$advert_status = get_post_status( $classified_id );
			if(!empty(terraclassifieds_get_option( '_tc_email_template_reply_to_email_address', '' ))){
				$reply_to_email_address = terraclassifieds_get_option( '_tc_email_template_reply_to_email_address', '' );
			} else {
				$reply_to_email_address = get_option( 'admin_email' );
			}
			$headers = array("Content-Type: text/html; charset=UTF-8", "Reply-To: " . $reply_to_email_address);
			$advert_link_subject = get_permalink($classified_id);
			$advert_link = '<a href="' . get_permalink($classified_id) . '">' . get_permalink($classified_id) . '</a>';
			
		    $author_id = get_post_field( 'post_author', $classified_id );
			$author_email = get_the_author_meta( 'user_email', $author_id );

		    $subject = $email_template_change_status_subject;
			$subject = str_replace("[[advert_title_link]]", $advert_link_subject, $subject);
			$subject = str_replace("[[advert_status]]", $advert_status, $subject);
		
			$message = $email_template_change_status_message;
			$message = str_replace("[[advert_title_link]]", $advert_link, $message);
			$message = str_replace("[[advert_status]]", $advert_status, $message);

			$sent = wp_mail($author_email, $subject, $message, $headers);
		}
	}
	add_action( 'pending_to_publish', 'terraclassifieds_sendmail_update_status' );
}

// send email to author when classified changes status - pending_to_rejected, publish_to_rejected, rejected_to_publish, publish_to_archived, archived_to_publish
if ( ! function_exists( 'terraclassifieds_sendmail_update_status2' ) ) {
	function terraclassifieds_sendmail_update_status2() {
			$email_template_change_status_subject = terraclassifieds_get_option_base_functions( '_tc_email_template_change_status_subject', '' );
			$email_template_change_status_message = terraclassifieds_get_option_base_functions( '_tc_email_template_change_status_message', '' );
	
			$classified_id = get_the_ID();
			$advert_status = get_post_status( $classified_id );
			if(!empty(terraclassifieds_get_option_base_functions( '_tc_email_template_reply_to_email_address', '' ))){
				$reply_to_email_address = terraclassifieds_get_option_base_functions( '_tc_email_template_reply_to_email_address', '' );
			} else {
				$reply_to_email_address = get_option( 'admin_email' );
			}
			$headers = array("Content-Type: text/html; charset=UTF-8", "Reply-To: " . $reply_to_email_address);
			$advert_link_subject = get_permalink($classified_id);
			$advert_link = '<a href="' . get_permalink($classified_id) . '">' . get_permalink($classified_id) . '</a>';
			
		    $author_id = get_post_field( 'post_author', $classified_id );
			$author_email = get_the_author_meta( 'user_email', $author_id );

		    $subject = $email_template_change_status_subject;
			$subject = str_replace("[[advert_title_link]]", $advert_link_subject, $subject);
			$subject = str_replace("[[advert_status]]", $advert_status, $subject);
		
			$message = $email_template_change_status_message;
			$message = str_replace("[[advert_title_link]]", $advert_link, $message);
			$message = str_replace("[[advert_status]]", $advert_status, $message);

			$sent = wp_mail($author_email, $subject, $message, $headers);
	}
	add_action( 'pending_to_rejected', 'terraclassifieds_sendmail_update_status2' );
	add_action( 'publish_to_rejected', 'terraclassifieds_sendmail_update_status2' );
	add_action( 'rejected_to_publish', 'terraclassifieds_sendmail_update_status2' );
	add_action( 'publish_to_archived', 'terraclassifieds_sendmail_update_status2' );
	add_action( 'archived_to_publish', 'terraclassifieds_sendmail_update_status2' );
}
