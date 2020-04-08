<?php
/*--------------------------------------------------------------
 Copyright (C) pixelemu.com
 License: http://www.pixelemu.com/license.html PixelEmu Proprietary Use License
 Website: http://www.pixelemu.com
 Support: info@pixelemu.com
---------------------------------------------------------------*/

/* Ad post type */
$labels = array(
	'name'               => __( 'TerraClassifieds', 'terraclassifieds' ),
	'all_items'          => __( 'Ads', 'terraclassifieds' ),
	'singular_name'      => __( 'Ad', 'terraclassifieds' ),
	'add_new'            => __( 'Add New', 'terraclassifieds' ),
	'add_new_item'       => __( 'Add New Ad', 'terraclassifieds' ),
	'edit_item'          => __( 'Edit Ad', 'terraclassifieds' ),
	'new_item'           => __( 'New Ad', 'terraclassifieds' ),
	'view_item'          => __( 'View Ad', 'terraclassifieds' ),
	'search_items'       => __( 'Search Ad', 'terraclassifieds' ),
	'not_found'          => __( 'No Ad found', 'terraclassifieds' ),
	'not_found_in_trash' => __( 'No Ad found in Trash', 'terraclassifieds' ),
	'parent_item_colon'  => '',
);

$args = array(
	'labels'              => $labels,
	'public'              => true,
	'exclude_from_search' => false,
	'publicly_queryable'  => true,
	'show_ui'             => true,
	'query_var'           => true,
	'hierarchical'        => true,
	'has_archive'         => true,
	'menu_position'       => 5,
	'menu_icon'           => '',
	'show_in_nav_menus'   => true,
	'supports'            => array( 'title', 'editor', 'author' ),
	'rewrite'             => array( 'slug' => 'classified' ),
	'rewrite' => true,
	//'capability_type'     => array('classified','classifieds'),
	//'map_meta_cap'        => true,
);

register_post_type( 'classified', $args );

/* Ads Categories */
$ads_category_labels = array(
	'name'                       => __('Ads Categories', 'terraclassifieds'), 
	'singular_name'              => __('Category', 'terraclassifieds'), 
	'search_items'               => __('Search Categories', 'terraclassifieds'), 
	'popular_items'              => __('Popular Categories', 'terraclassifieds'), 
	'all_items'                  => __('All Categories', 'terraclassifieds'), 
	'parent_item'                => __('Parent Category', 'terraclassifieds'), 
	'parent_item_colon'          => __('Parent Category:', 'terraclassifieds'), 
	'edit_item'                  => __('Edit Category', 'terraclassifieds'), 
	'update_item'                => __('Update Category', 'terraclassifieds'), 
	'add_new_item'               => __('Add New Category', 'terraclassifieds'), 
	'new_item_name'              => __('New Category Name', 'terraclassifieds'), 
	'separate_items_with_commas' => __('Separate Categories with commas', 'terraclassifieds'), 
	'add_or_remove_items'        => __('Add or remove Categories', 'terraclassifieds'), 
	'choose_from_most_used'      => __('Choose from the most used Categories', 'terraclassifieds'), 
	'menu_name'                  => __('Categories', 'terraclassifieds'),
);

register_taxonomy(
	'ad_category',
	array('classified'),
	array(
		'hierarchical'      => true,
		'labels'            => $ads_category_labels, 
		'show_ui'           => true,
		'show_in_nav_menus' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'ad_category' ),
		'rewrite' => true,
	)
);

/* Edit Default Columns, added Category and Date Column */
if (!function_exists('terra_classified_columns')) {
	function terra_ad_columns($columns) {

		$columns = array(
			'cb'       => '<input type="checkbox" />', 
			'title'    => __('Ad', 'terraclassifieds'), 
			'category' => __('Ad Category', 'terraclassifieds'), 
			'date'     => __('Date', 'terraclassifieds')
		);

		return $columns;
	}

}
add_filter('manage_edit-classified_columns', 'terra_ad_columns');

/* Value for Category Column */
if (!function_exists('terra_ad_category_column_value')) {
	function terra_ad_category_column_value($column) {
		global $post;
		switch ($column) {
			case 'category' :
				echo get_the_term_list($post -> ID, 'ad_category', '', ', ', '');
				break;
		}
	}

}
add_action('manage_classified_posts_custom_column', 'terra_ad_category_column_value');

// callback function for CMB2 ad_category
function cmb2_get_term_options( $field ) {
	$args = $field->args( 'get_terms_args' );
	$args = is_array( $args ) ? $args : array();

	$args = wp_parse_args( $args, array( 'taxonomy' => 'ad_category' ) );

	$taxonomy = $args['taxonomy'];

	$terms = (array) cmb2_utils()->wp_at_least( '4.5.0' )
		? get_terms( $args )
		: get_terms( $taxonomy, $args );

	// Initate an empty array
	$term_options = array();
	if ( ! empty( $terms ) ) {
		foreach ( $terms as $term ) {
			$term_options[ $term->term_id ] = $term->name;
		}
	}

	return $term_options;
}

// add special span after galllery field
function terra_gallery_after( $field_args, $field ) {
	echo '<span class="tcf-gallery-after-label">'.__( 'Upload images and raise your chances for selling.', 'terraclassifieds' ).'</span>';
}

function override_category_field_callback( $field_args, $field ) {

	// If field is requesting to not be shown on the front-end
	if ( ! is_admin() && ! $field->args( 'on_front' ) ) {
		return;
	}

	// If field is requesting to be conditionally shown
	if ( ! $field->should_show() ) {
		return;
	}

	$field->peform_param_callback( 'before_row' );

	// Remove the cmb-row class
	printf( '<div class="cmb-row %s">', $field->row_classes() );

	if ( ! $field->args( 'show_names' ) ) {
	
		// If the field is NOT going to show a label output this
		echo '<div class="cmb-td custom-label-class">';
		$field->peform_param_callback( 'label_cb' );
	
	} else {

		// Otherwise output something different
		if ( $field->get_param_callback_result( 'label_cb', false ) ) {
			echo '<div class="cmb-th">', esc_attr($field->peform_param_callback( 'label_cb' )), '</div>';
		}
		echo '<div class="cmb-td"><span class="tcf-categories-breadcrumb"></span><span class="tcf-add-category-done btn button">'.__( 'Use selected category', 'terraclassifieds' ).'</span><span class="tcf-change-category-button btn button">'.__( 'Change category', 'terraclassifieds' ).'</span><span class="tcf-add-category-button btn button">'.__( 'Choose  category', 'terraclassifieds' ).'</span>';
	}

	$field->peform_param_callback( 'before' );
	
	// The next two lines are key. This is what actually renders the input field
	$field_type = new CMB2_Types( $field );
	$field_type->render();

	$field->peform_param_callback( 'after' );

	echo '</div>';
	echo '</div>';

	$field->peform_param_callback( 'after_row' );

    // For chaining
	return $field;
}

/* Ads Locations */
$ads_location_labels = array(
	'name'                       => __('Ads Locations', 'terraclassifieds'), 
	'singular_name'              => __('Location', 'terraclassifieds'), 
	'search_items'               => __('Search Locations', 'terraclassifieds'), 
	'popular_items'              => __('Popular Locations', 'terraclassifieds'), 
	'all_items'                  => __('All Locations', 'terraclassifieds'), 
	'parent_item'                => __('Parent Location', 'terraclassifieds'), 
	'parent_item_colon'          => __('Parent Location:', 'terraclassifieds'), 
	'edit_item'                  => __('Edit Location', 'terraclassifieds'), 
	'update_item'                => __('Update Location', 'terraclassifieds'), 
	'add_new_item'               => __('Add New Location', 'terraclassifieds'), 
	'new_item_name'              => __('New Location Name', 'terraclassifieds'), 
	'separate_items_with_commas' => __('Separate Locations with commas', 'terraclassifieds'), 
	'add_or_remove_items'        => __('Add or remove Locations', 'terraclassifieds'), 
	'choose_from_most_used'      => __('Choose from the most used Locations', 'terraclassifieds'), 
	'menu_name'                  => __('Locations', 'terraclassifieds'),
);

register_taxonomy(
	'ad_location',
	array('classified'),
	array(
		'hierarchical'      => true,
		'labels'            => $ads_location_labels, 
		'show_ui'           => true,
		'show_in_nav_menus' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'location' ),
	)
);

/* Ads Types */
$ads_type_labels = array(
	'name'                       => __('Ads types', 'terraclassifieds'), 
	'singular_name'              => __('Type', 'terraclassifieds'), 
	'search_items'               => __('Search Types', 'terraclassifieds'), 
	'popular_items'              => __('Popular Types', 'terraclassifieds'), 
	'all_items'                  => __('All Types', 'terraclassifieds'), 
	'parent_item'                => __('Parent Type', 'terraclassifieds'), 
	'parent_item_colon'          => __('Parent Type:', 'terraclassifieds'), 
	'edit_item'                  => __('Edit Type', 'terraclassifieds'), 
	'update_item'                => __('Update Type', 'terraclassifieds'), 
	'add_new_item'               => __('Add New Type', 'terraclassifieds'), 
	'new_item_name'              => __('New Type Name', 'terraclassifieds'), 
	'separate_items_with_commas' => __('Separate Types with commas', 'terraclassifieds'), 
	'add_or_remove_items'        => __('Add or remove Types', 'terraclassifieds'), 
	'choose_from_most_used'      => __('Choose from the most used Types', 'terraclassifieds'), 
	'menu_name'                  => __('Types', 'terraclassifieds'),
);

register_taxonomy(
	'ad_type',
	array('classified'),
	array(
		'hierarchical'      => false,
		'labels'            => $ads_type_labels, 
		'show_ui'           => true,
		'show_in_nav_menus' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'type' ),
	)
);

/* Custom fields for ad */
add_action( 'cmb2_init', 'terra_custom_fields' );
if (!function_exists('terra_custom_fields')) {
	function terra_custom_fields() {

		require_once dirname( __FILE__ ) . '/../cmb2/taxonomy-radio-custom/taxonomy-radio-custom-field-type.php'; //add taxonomy-radio-custom field type
	    $prefix = '_tc_';
		$title_limit = terraclassifieds_get_option( '_tc_add_advert_title_characters_limit', 0 );
		if($title_limit == 0){
			$title_limit = 524288;
		}
		$desc_limit = terraclassifieds_get_option( '_tc_add_advert_description_characters_limit', 0 );
		if($desc_limit == 0){
			$desc_limit = 9999999999;
		}
		
		$use_type = terraclassifieds_get_option( '_tc_use_types', 0 );
		
		$type_type = terraclassifieds_get_option( '_tc_types_display_style', 0 );
		if($type_type == 'selectlist'){
			$type_type = 'taxonomy_select';
		} else {
			$type_type = 'taxonomy_multicheck';
		}
		
		$use_locations = terraclassifieds_get_option( '_tc_use_locations', 1 );
		$use_location_address = terraclassifieds_get_option( '_tc_location_address', 0 );
		$use_location_post_code = terraclassifieds_get_option( '_tc_location_post_code', 0 );
		
		$use_selling_types = terraclassifieds_get_option( '_tc_use_selling_types', 1 );
		
		$use_images = terraclassifieds_get_option( '_tc_image_use_images', 1 );
		if($use_images){
		    $gallery_field_class = '';
		} else {
		    $gallery_field_class = 'hidden';
		}
		
		$descriptionMinimumLength = terraclassifieds_get_option( '_tc_add_advert_description_minimum_length', false );
		
		$currency = terraclassifieds_get_option( '_tc_advert_currency', false );
		
		$sellingTypes = terraclassifieds_get_option( '_tc_selling_types', array('price', 'for-free', 'exchange', 'nothing') );
		if($sellingTypes == array('price', 'for-free', 'exchange', 'nothing')){
		    $selling_type_options = array(
		        'price' => __( 'Price in', 'terraclassifieds' ) . ' ' . $currency,
		        'for_free'   => __( 'For Free', 'terraclassifieds' ),
		        'exchange'     => __( 'Exchange', 'terraclassifieds' ),
		        'nothing'     => __( 'Nothing', 'terraclassifieds' ),
		    );
		} else if($sellingTypes == array('price', 'for-free', 'exchange')){
		    $selling_type_options = array(
		        'price' => __( 'Price in', 'terraclassifieds' ) . ' ' . $currency,
		        'for_free'   => __( 'For Free', 'terraclassifieds' ),
		        'exchange'     => __( 'Exchange', 'terraclassifieds' ),
		    );
		} else if($sellingTypes == array('price', 'for-free', 'nothing')){
		    $selling_type_options = array(
		        'price' => __( 'Price in', 'terraclassifieds' ) . ' ' . $currency,
		        'for_free'   => __( 'For Free', 'terraclassifieds' ),
		        'nothing'     => __( 'Nothing', 'terraclassifieds' ),
		    );
		} else if($sellingTypes == array('price', 'exchange', 'nothing')){
		    $selling_type_options = array(
		        'price' => __( 'Price in', 'terraclassifieds' ) . ' ' . $currency,
		        'exchange'     => __( 'Exchange', 'terraclassifieds' ),
		        'nothing'     => __( 'Nothing', 'terraclassifieds' ),
		    );
		} else if($sellingTypes == array('for-free', 'exchange', 'nothing')){
		    $selling_type_options = array(
		        'for_free'   => __( 'For Free', 'terraclassifieds' ),
		        'exchange'     => __( 'Exchange', 'terraclassifieds' ),
		        'nothing'     => __( 'Nothing', 'terraclassifieds' ),
		    );
		} else if($sellingTypes == array('price', 'for-free')){
		    $selling_type_options = array(
		        'price' => __( 'Price in', 'terraclassifieds' ) . ' ' . $currency,
		        'for_free'   => __( 'For Free', 'terraclassifieds' ),
		    );
		} else if($sellingTypes == array('price', 'exchange')){
		    $selling_type_options = array(
		        'price' => __( 'Price in', 'terraclassifieds' ) . ' ' . $currency,
		        'exchange'     => __( 'Exchange', 'terraclassifieds' ),
		    );
		} else if($sellingTypes == array('for-free', 'exchange')){
		    $selling_type_options = array(
		        'for_free'   => __( 'For Free', 'terraclassifieds' ),
		        'exchange'     => __( 'Exchange', 'terraclassifieds' ),
		    );
		} else if($sellingTypes == array('for-free', 'nothing')){
		    $selling_type_options = array(
		        'for_free'   => __( 'For Free', 'terraclassifieds' ),
		        'nothing'     => __( 'Nothing', 'terraclassifieds' ),
		    );
		} else if($sellingTypes == array('price', 'nothing')){
		    $selling_type_options = array(
		        'price' => __( 'Price in', 'terraclassifieds' ) . ' ' . $currency,
		        'nothing'     => __( 'Nothing', 'terraclassifieds' ),
		    );
		} else if($sellingTypes == array('exchange', 'nothing')){
		    $selling_type_options = array(
		        'exchange'     => __( 'Exchange', 'terraclassifieds' ),
		        'nothing'     => __( 'Nothing', 'terraclassifieds' ),
		    );
		} else if($sellingTypes == array('price')){
		    $selling_type_options = array(
		        'price' => __( 'Price in', 'terraclassifieds' ) . ' ' . $currency,
		    );
		} else if($sellingTypes == array('for-free')){
		    $selling_type_options = array(
		        'for_free'   => __( 'For Free', 'terraclassifieds' ),
		    );
		} else if($sellingTypes == array('exchange')){
		    $selling_type_options = array(
		        'exchange'     => __( 'Exchange', 'terraclassifieds' ),
		    );
		} else if($sellingTypes == array('nothing')){
		    $selling_type_options = array(
		        'nothing'     => __( 'Nothing', 'terraclassifieds' ),
		    );
		}


		/* ADS */

	    $cmb = new_cmb2_box( array(
	        'id'            => 'ad_options',
	        'title'         => __( 'Additional fields', 'terraclassifieds' ),
	        'object_types'  => array( 'classified' ), // Post type
	        'context'       => 'normal',
	        'priority'      => 'high',
	        'show_names'    => true, // Show field names on the left
	        'closed'        => false, // Keep the metabox closed by default
	    ) );
		
		$cmb->add_field( array(
			'name' => __( 'Fill in all required fields.', 'terraclassifieds' ),
			'type' => 'title',
			'id'   => $prefix . 'validate_message',
		) );
		
		$cmb->add_field( array(
		    'name' => __( 'Description is too short.', 'terraclassifieds' ),
		    'type' => 'title',
		    'id'   => $prefix . 'too_short_description',
		) );

	    $cmb->add_field( array(
	        'name'    => __( 'Title *', 'terraclassifieds' ),
	        'desc' =>  __( 'title - change me', 'terraclassifieds' ),
	        'id'   => $prefix . 'post_title',
	        'type'    => 'text',
	        'attributes' => array(
	            'maxlength' => $title_limit,
	        ),
	    ) );
	
	    $cmb->add_field( array(
	        'name'    => __( 'Description *', 'terraclassifieds' ),
	        'desc' =>  __( 'desc - change me', 'terraclassifieds' ),
	        'id'   => $prefix . 'post_content',
	        'type'    => 'textarea',
	        'options' => array(
	            'textarea_rows' => 12,
	            'media_buttons' => false,
	        ),
	        'attributes' => array(
	            'maxlength' => $desc_limit,
	            'minlength' => $descriptionMinimumLength,
	        ),
	    ) );
		
		$cmb->add_field( array(
			'name'       => __( 'Category *', 'terraclassifieds' ), 
			'id'   => $prefix . 'category',
			'type'           => 'taxonomy_radio_custom',
			'taxonomy'       => 'ad_category', // Enter Taxonomy Slug
			'render_row_cb' => 'override_category_field_callback'
		) );
		
		if($use_type){
			$cmb->add_field( array(
				'name'           => __( 'Type', 'terraclassifieds' ), 
				'id'   => $prefix . 'types',
				'taxonomy'       => 'ad_type',
				'type'           => $type_type,
				'remove_default' => 'true',
				'select_all_button' => false,
			) );
		}
		
		// sell type
		if($use_selling_types){
		    $cmb->add_field( array(
		        'name'             => __( 'Selling type', 'terraclassifieds' ),
		        'id'   => $prefix . 'sell_type',
		        'type'             => 'radio',
		        'show_option_none' => false,
		        'options'          => $selling_type_options,
		        'default' => 'price',
		    ) );
		}
	
		// Price
		if($use_selling_types){
    		$cmb->add_field( array(
    		    'name' => __( 'Price *', 'terraclassifieds' ),
    		    'desc' =>  __( 'Enter price for ad.', 'terraclassifieds' ),
    		    'id'   => $prefix . 'price',
    		    'type' => 'text'
    		) );
		}
		
		// negotiable price
		$cmb->add_field( array(
			'name' => __( 'Negotiable', 'terraclassifieds' ),
			'id'   => $prefix . 'negotiable',
			'desc' => __( 'negotiable', 'terraclassifieds' ),
			'type' => 'checkbox',
		) );

		// gallery
	    $cmb->add_field( array(
	        'name' => __( 'Gallery', 'terraclassifieds' ),
	        'desc' => __( 'gallery - change me', 'terraclassifieds' ),
	        'before_field' => 'terra_gallery_after',
	        'id'   => $prefix . 'gallery',
	        'type' => 'file_list',
	        'preview_size' => array( 100, 100 ), // Default: array( 50, 50 )
	        'query_args' => array( 'type' => 'image' ), // Only images attachment
	        'repeatable'      => false,
	        'classes' => $gallery_field_class,
	        'text' => array(
	            'add_upload_files_text' => __( 'Add or upload files', 'terraclassifieds' ),
	        ),
	    ) );
		
	    // location
	    if($use_locations){
	        $cmb->add_field( array(
	            'name'           => __( 'Location', 'terraclassifieds' ),
	            'id'   => $prefix . 'locations',
	            'taxonomy'       => 'ad_location',
	            'type'           => 'taxonomy_radio_custom',
	            'remove_default' => 'true',
	            'show_option_none' => false,
	        ) );
	    }
	    
	    if($use_location_address){
	        $cmb->add_field( array(
	            'name'    => __( 'Address', 'terraclassifieds' ),
	            'id'   => $prefix . 'location_address',
	            'type'    => 'text',
	        ) );
	    }

	    if($use_location_post_code){
	        $cmb->add_field( array(
	            'name'    => __( 'Post / ZIP code', 'terraclassifieds' ),
	            'id'   => $prefix . 'location_post_code',
	            'type'    => 'text',
	        ) );
	    }

		$cmb->add_field( array(
			'name' => __( 'Expire date', 'terraclassifieds' ),
			'id'   => $prefix . 'expire_date',
			'type' => 'text_date_timestamp',
			// 'timezone_meta_key' => 'wiki_test_timezone',
			// 'date_format' => 'l jS \of F Y',
		) );
		
		// expire notificaton was sended
		$cmb->add_field( array(
			'name' => 'Expire soon notifcation was sended',
			'id'   => $prefix . 'expire_soon_notification_done',
			'type' => 'checkbox',
		) );
		
		$cmb->add_field( array(
		    'name' => __( '<span>Draft</span> (you can save part of your advert before publishing)', 'terraclassifieds' ),
		    'id'   => $prefix . 'draft_status',
		    'type' => 'checkbox',
		) );

		/* CATEGORIES */

		$cmb_term = new_cmb2_box( array(
			'id'               => 'ad_category_options',
			'title'            => esc_html__( 'Category options', 'terraclassifieds' ), // Doesn't output for term boxes
			'object_types'     => array( 'term' ), // Tells CMB2 to use term_meta vs post_meta
			'taxonomies'       => array( 'ad_category' ), // Tells CMB2 which taxonomies should have these fields
		) );
	
		$cmb_term->add_field( array(
			'name'    => __( 'Category image', 'terraclassifieds' ),
			'id'      => $prefix . 'cat_image',
			'type'    => 'file',
			// Optional:
			'options' => array(
				'url' => false, // Hide the text input for the url
			),
			'text'    => array(
				'add_upload_file_text' => __('Add image', 'terraclassifieds') // Change upload button text. Default: "Add or Upload File"
			),
			// query_args are passed to wp.media's library query.
			'query_args' => array(
				// Or only allow gif, jpg, or png images
				'type' => array(
					'image/gif',
					'image/jpeg',
					'image/png',
				),
			),
			'preview_size' => 'thumbnail', // Image size to use when previewing in the admin.
		) );
		
		/* TYPES */

		$cmb_term = new_cmb2_box( array(
			'id'               => 'ad_type_options',
			'title'            => esc_html__( 'Type options', 'terraclassifieds' ), // Doesn't output for term boxes
			'object_types'     => array( 'term' ), // Tells CMB2 to use term_meta vs post_meta
			'taxonomies'       => array( 'ad_type' ), // Tells CMB2 which taxonomies should have these fields
		) );
	
		$cmb_term->add_field( array(
			'name'    => __( 'Text color', 'terraclassifieds' ),
			'id'      => $prefix . 'type_text_color',
			'type'    => 'colorpicker',
			'default' => '#000000',
		) );
		
		$cmb_term->add_field( array(
			'name'    => __( 'Background color', 'terraclassifieds' ),
			'id'      => $prefix . 'type_background_color',
			'type'    => 'colorpicker',
			'default' => '#ffffff',
		) );
		
		$cmb_term->add_field( array(
			'name'    => __( 'Border color', 'terraclassifieds' ),
			'id'      => $prefix . 'type_border_color',
			'type'    => 'colorpicker',
			'default' => '#ffffff',
		) );
		
		$cmb_term->add_field( array(
			'name'    => __( 'Border size (px)', 'terraclassifieds' ),
			'id'      => $prefix . 'type_border_size',
			'type'    => 'text',
			'default' => 1,
			'attributes' => array(
					'type' => 'number',
					'min' => '0',
					'step' => '1',
			),
		) );
		
		$cmb_term->add_field( array(
			'name'    => __( 'Use in search', 'terraclassifieds' ),
			'id'      => $prefix . 'type_use_in_search',
			'type'    => 'radio',
			'default' => 'yes',
			'options'          => array(
				'yes' => __( 'Yes', 'terraclassifieds' ),
				'no'   => __( 'No', 'terraclassifieds' ),
			),
		) );
		
	}
}
