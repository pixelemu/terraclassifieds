<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// add a new role
add_role('terra_user',
	'Terraclassifieds user',
	array(
		'read'          => true,
		'edit_posts'    => false,
		'delete_posts'  => false,
		'publish_posts' => false,
		'upload_files'  => true,
	)
);

// add new capabilities
add_action('admin_init','terra_add_role_caps', 999);
function terra_add_role_caps() {

	$roles = array('administrator');
	
	// Loop through each role and assign capabilities
	foreach($roles as $the_role) {

		$role = get_role($the_role);

		//$role->add_cap( 'read' );
		$role->add_cap( 'read_classified' );
		$role->add_cap( 'read_private_classifieds' );
		$role->add_cap( 'edit_classified' );
		$role->add_cap( 'edit_classifieds' );
		$role->add_cap( 'edit_others_classifieds' );
		$role->add_cap( 'edit_published_classifieds' );
		$role->add_cap( 'publish_classifieds' );
		$role->add_cap( 'delete_others_classifieds' );
		$role->add_cap( 'delete_private_classifieds' );
		$role->add_cap( 'delete_published_classifieds' );
		$role->add_cap( 'edit_others_pages' );
		$role->add_cap( 'edit_published_pages' );
	}
	
	$roles2 = array('terra_user');
	
	// Loop through each role and assign capabilities
	foreach($roles2 as $the_role2) {

		$role2 = get_role($the_role2);
		
		$role2->remove_cap( 'read_classified' );
		$role2->remove_cap( 'read_private_classifieds' );
		$role2->remove_cap( 'edit_classified' );
		$role2->remove_cap( 'edit_classifieds' );
		$role2->remove_cap( 'edit_others_classifieds' );
		$role2->remove_cap( 'edit_published_classifieds' );
		$role2->remove_cap( 'publish_classifieds' );
		$role2->remove_cap( 'delete_others_classifieds' );
		$role2->remove_cap( 'delete_private_classifieds' );
		$role2->remove_cap( 'delete_published_classifieds' );
		$role2->add_cap( 'edit_others_pages' );
		$role2->add_cap( 'edit_published_pages' );
	}
}

// allow upload featured image for terra_user
add_action('admin_init', 'allow_contributor_uploads');
function allow_contributor_uploads() {
    $contributor = get_role('terra_user');
    $contributor->add_cap('upload_files');
}

//language variables needed in registration and login forms
if( !function_exists( 'terra_lang' ) ) {
	function  terra_lang() {
		$terra_lang = array();
		$terra_lang['TERRA_LOG'] = __('Please enter a valid username or email', 'terraclassifieds');
		$terra_lang['TERRA_PWD'] = __('Please enter a valid password', 'terraclassifieds');
		$terra_lang['TERRA_USER_LOGIN'] = __('Please enter a valid username', 'terraclassifieds');
		$terra_lang['TERRA_USER_EMAIL'] = __('Please enter valid email address', 'terraclassifieds');
		$terra_lang['TERRA_PHONE'] = __('Please enter valid phone numer', 'terraclassifieds');
		$terra_lang['TERRA_WEBSITE'] = __('Please enter valid URL', 'terraclassifieds');
		return $terra_lang;
	}
}

// PROFILE
add_action( 'cmb2_admin_init', 'terra_register_member_metabox' );
if ( ! function_exists( 'terra_register_member_metabox' ) ) {
	function terra_register_member_metabox() {

		// Start with an underscore to hide fields from custom fields list
		$prefix = '_tc_';

		/* REGISTRATION */

		$cmb_user = new_cmb2_box( array(
			'id'               => $prefix . 'edit',
			'title'            => esc_html__( 'User Profile', 'terraclassifieds' ),
			'object_types'     => array( 'user' ),
			'show_names'       => true,
			'new_user_section' => 'add-new-user',
		) );

		$cmb_user->add_field( array(
			'name'       => esc_html__( 'Phone', 'terraclassifieds' ),
			'id'         => $prefix. 'phone',
			'type'       => 'text',
		) );
		
		$cmb_user->add_field( array(
			'name'       => esc_html__( 'Terraclassifieds Avatar', 'terraclassifieds' ),
			'id'         => $prefix. 'avatar',
			'type'       => 'file',
			'options' => array(
				'url' => false, // Hide the text input for the url
			),
			'query_args' => array(
				 'type' => array(
				 	'image/gif',
				 	'image/jpeg',
				 	'image/png',
				 ),
			),
		) );

	}
}

//save additional profile fields on registration and change user's role
add_action( 'user_register', 'terra_user_register' );
if (!function_exists('terra_user_register')) {
	function terra_user_register( $user_id ) {
		
		if ( ! empty( $_POST['phone'] ) ) {
			update_user_meta( $user_id, '_tc_phone', sanitize_text_field( $_POST['phone'] ) );
		}
		if ( ! empty( $_POST['user_url'] ) ) {
			wp_update_user( array( 'ID' => $user_id, 'user_url' => esc_url( $_POST['user_url'] ) ) );
		}

		//hidden
		if ( ! empty( $_POST['registration_url'] ) ) { //set referrer URL for bid
			update_user_meta( $user_id, 'registration_url', esc_url( $_POST['registration_url'] ) );
		}
		
		// remove default role and add the new one
		$u = new WP_User( $user_id );
		// Remove role
		$u->remove_role( 'subscriber' );
		// Add role
		$u->add_role( 'terra_user' );
	}
}

// Limit media library access only for own files (admin see all files)
add_filter( 'ajax_query_attachments_args', 'show_current_user_attachments', 10, 1 );
function show_current_user_attachments( $query = array() ) {
    $user_id = get_current_user_id();  
    if( !current_user_can('manage_options') ) {
        $query['author'] = $user_id;
    }
    return $query;
}

// disable admin bar for subscriber and terraclassifieds user
add_action('wp', 'terraclassifieds_disable_admin_bar');
function terraclassifieds_disable_admin_bar() {
    if (current_user_can('subscriber') || current_user_can('terra_user')) {
        show_admin_bar(false);
    }
}
?>