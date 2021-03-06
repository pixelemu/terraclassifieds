<?php

// ensure is_plugin_active() exists (not on frontend)
if (!function_exists('is_plugin_active')) {
	include_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

// return array with all pages
/*if ( ! function_exists( 'pageArray' ) ) {
	function pageArray(){
		$args = array('post_type' => 'page', 'posts_per_page' => -1);
		$loop = new WP_Query($args);
		if($loop->have_posts()) {
		    while($loop->have_posts()) : $loop->the_post();
		        $varID = get_the_id();
		        $varName = get_the_title();
		        $pageArray[$varID]=$varName;
		    endwhile;
		    return $pageArray;
		}
	}
}*/

if (!function_exists('terraclassifieds_taxonomy_children')) {
	function terraclassifieds_taxonomy_children($parent_id = 0,$level = 0,$item_path = null) {
		$catArray = array();
		$category_path_separator = ' - ';
		$terms = get_terms(array('taxonomy' => 'ad_category','hide_empty' => false, 'parent' => $parent_id));
		if($terms) {
			foreach($terms as $key=>$term) {
				$term->cat_level = $level+1;
				if (empty($item_path)) {
					$term->category_path = $term->name;
				}else{
					$term->category_path = $item_path.$category_path_separator.$term->name;
				}
				$catArray[$term->term_id] = $term;
				if ($term->parent === $parent_id) {
					$childrens = terraclassifieds_taxonomy_children($term->term_id,$term->cat_level,$term->category_path);
					if($childrens) {
						if (is_array($childrens)) {
							foreach ($childrens as $child_key=>$child) {
								$catArray[$child->term_id] = $child;
							}
						}
					}
				}
				unset($term);
			}
		}
		return $catArray;
	}
}

if (!function_exists('terraclassifieds_register_fields')) {
	add_action('cmb2_admin_init', 'terraclassifieds_register_fields');
	function terraclassifieds_register_fields()
	{

		// get slugs for pages
		$page_add_advert_slug = terraclassifieds_get_option('_tc_slug_add_advert', 'add-advert');
		$page_edit_advert_slug = terraclassifieds_get_option('_tc_slug_edit_advert', 'edit-ad');
		$page_edit_profile_slug = terraclassifieds_get_option('_tc_slug_edit_profile', 'edit-profile');
		$page_favourite_ads_slug = terraclassifieds_get_option('_tc_slug_favourite_ads', 'favourite-ads');
		$page_forgot_password_slug = terraclassifieds_get_option('_tc_slug_forgot_password', 'forgot-password');
		$page_login_slug = terraclassifieds_get_option('_tc_slug_login', 'login');
		$page_registration_slug = terraclassifieds_get_option('_tc_slug_registration', 'registration');
		$page_my_submissions_slug = terraclassifieds_get_option('_tc_slug_my_submissions', 'my-submissions');

		// get pages IDs
		$page_add_advert_id = terraclassifieds_get_option('_tc_add_advert_page_id');
		$page_edit_advert_id = terraclassifieds_get_option('_tc_edit_advert_page_id');
		$page_edit_profile_id = terraclassifieds_get_option('_tc_edit_profile_page_id');
		$page_favourite_ads_id = terraclassifieds_get_option('_tc_favourite_ads_page_id');
		$page_forgot_password_id = terraclassifieds_get_option('_tc_forgot_password_page_id');
		$page_login_id = terraclassifieds_get_option('_tc_login_page_id');
		$page_registration_id = terraclassifieds_get_option('_tc_registration_page_id');
		$page_my_submissions_id = terraclassifieds_get_option('_tc_my_submissions_page_id');

		// default pages IDs
		$page_add_advert_default_id = url_to_postid(site_url($page_add_advert_slug));
		$page_edit_advert_default_id = url_to_postid(site_url($page_edit_advert_slug));
		$page_edit_profile_default_id = url_to_postid(site_url($page_edit_profile_slug));
		$page_favourite_ads_default_id = url_to_postid(site_url($page_favourite_ads_slug));
		$page_forgot_password_default_id = url_to_postid(site_url($page_forgot_password_slug));
		$page_login_default_id = url_to_postid(site_url($page_login_slug));
		$page_registration_default_id = url_to_postid(site_url($page_registration_slug));
		$page_my_submissions_default_id = url_to_postid(site_url($page_my_submissions_slug));

		// messages
		$missing_page_description = '<br /><span class="terraclassifieds-post-tile-more-info">' . __('The corresponded page was removed or its slug was changed via editing a page.<br />Do not leave this field empty, assign the correct page for this view.', 'terraclassifieds') . '</span>';
		$missing_page_but_conditional_description_part1 = '<br /><span class="terraclassifieds-post-tile-more-info">' . __('For security reason, your website uses the following page: ', 'terraclassifieds') . '<strong>';
		$missing_page_but_conditional_description_part2 = '</strong>' .  __('<br />Do not leave this field empty, assign the correct page for this view.', 'terraclassifieds') . '</span>';

		$missing_page_but_conditional_description_add_advert =  $missing_page_but_conditional_description_part1 . get_the_title($page_add_advert_default_id) . $missing_page_but_conditional_description_part2;
		$missing_page_but_conditional_description_edit_advert =  $missing_page_but_conditional_description_part1 . get_the_title($page_edit_advert_default_id) . $missing_page_but_conditional_description_part2;
		$missing_page_but_conditional_description_edit_profile =  $missing_page_but_conditional_description_part1 . get_the_title($page_edit_profile_default_id) . $missing_page_but_conditional_description_part2;
		$missing_page_but_conditional_description_favourite_ads =  $missing_page_but_conditional_description_part1 . get_the_title($page_favourite_ads_default_id) . $missing_page_but_conditional_description_part2;
		$missing_page_but_conditional_description_forgot_password =  $missing_page_but_conditional_description_part1 . get_the_title($page_forgot_password_default_id) . $missing_page_but_conditional_description_part2;
		$missing_page_but_conditional_description_login =  $missing_page_but_conditional_description_part1 . get_the_title($page_login_default_id) . $missing_page_but_conditional_description_part2;
		$missing_page_but_conditional_description_registration =  $missing_page_but_conditional_description_part1 . get_the_title($page_registration_default_id) . $missing_page_but_conditional_description_part2;
		$missing_page_but_conditional_description_my_submissions =  $missing_page_but_conditional_description_part1 . get_the_title($page_my_submissions_default_id) . $missing_page_but_conditional_description_part2;

		// check for permalinks structire
		if (get_option('permalink_structure') == '/%postname%/') {
			$permalinks_postname = true;
		} else {
			$permalinks_postname = false;
		}
		// errors for Add advert
		if ($permalinks_postname && terraclassifieds_post_by_slug($page_add_advert_slug, 'page') && ($page_add_advert_id != $page_add_advert_default_id)) {
			$page_add_advert_more = $missing_page_but_conditional_description_add_advert;
		} else if ($permalinks_postname && !terraclassifieds_post_by_slug($page_add_advert_slug, 'page')) {
			$page_add_advert_more = $missing_page_description;
		} else {
			$page_add_advert_more = '';
		}

		// errors for Edit advert
		if ($permalinks_postname && terraclassifieds_post_by_slug($page_edit_advert_slug, 'page') && ($page_edit_advert_id != $page_edit_advert_default_id)) {
			$page_edit_advert_more = $missing_page_but_conditional_description_edit_advert;
		} else if ($permalinks_postname && !terraclassifieds_post_by_slug($page_edit_advert_slug, 'page')) {
			$page_edit_advert_more = $missing_page_description;
		} else {
			$page_edit_advert_more = '';
		}

		// errors for Edit profile
		if ($permalinks_postname && terraclassifieds_post_by_slug($page_edit_profile_slug, 'page') && ($page_edit_profile_id != $page_edit_profile_default_id)) {
			$page_edit_profile_more = $missing_page_but_conditional_description_edit_profile;
		} else if ($permalinks_postname && !terraclassifieds_post_by_slug($page_edit_profile_slug, 'page')) {
			$page_edit_profile_more = $missing_page_description;
		} else {
			$page_edit_profile_more = '';
		}

		// errors for Favourite ads
		if ($permalinks_postname && terraclassifieds_post_by_slug($page_favourite_ads_slug, 'page') && ($page_favourite_ads_id != $page_favourite_ads_default_id)) {
			$page_favourite_ads_more = $missing_page_but_conditional_description_favourite_ads;
		} else if ($permalinks_postname && !terraclassifieds_post_by_slug($page_favourite_ads_slug, 'page')) {
			$page_favourite_ads_more = $missing_page_description;
		} else {
			$page_favourite_ads_more = '';
		}

		// errors for Forgot password
		if ($permalinks_postname && terraclassifieds_post_by_slug($page_forgot_password_slug, 'page') && ($page_forgot_password_id != $page_forgot_password_default_id)) {
			$page_forgot_password_more = $missing_page_but_conditional_description_forgot_password;
		} else if ($permalinks_postname && !terraclassifieds_post_by_slug($page_forgot_password_slug, 'page')) {
			$page_forgot_password_more = $missing_page_description;
		} else {
			$page_forgot_password_more = '';
		}

		// errors for Login
		if ($permalinks_postname && terraclassifieds_post_by_slug($page_login_slug, 'page') && ($page_login_id != $page_login_default_id)) {
			$page_login_more = $missing_page_but_conditional_description_login;
		} else if ($permalinks_postname && !terraclassifieds_post_by_slug($page_login_slug, 'page')) {
			$page_login_more = $missing_page_description;
		} else {
			$page_login_more = '';
		}

		// errors for Registration
		if ($permalinks_postname && terraclassifieds_post_by_slug($page_registration_slug, 'page') && ($page_registration_id != $page_registration_default_id)) {
			$page_registration_more = $missing_page_but_conditional_description_registration;
		} else if ($permalinks_postname && !terraclassifieds_post_by_slug($page_registration_slug, 'page')) {
			$page_registration_more = $missing_page_description;
		} else {
			$page_registration_more = '';
		}

		// errors for My submissions
		if ($permalinks_postname && terraclassifieds_post_by_slug($page_my_submissions_slug, 'page') && ($page_my_submissions_id != $page_my_submissions_default_id)) {
			$page_my_submissions_more = $missing_page_but_conditional_description_my_submissions;
		} else if ($permalinks_postname && !terraclassifieds_post_by_slug($page_my_submissions_slug, 'page')) {
			$page_my_submissions_more = $missing_page_description;
		} else {
			$page_my_submissions_more = '';
		}

		$box_options = array(
			'id'          => 'tc_settings_layout',
			'title'       => __('Settings', 'terraclassifieds'),
			'show_names'  => true,
			'object_type' => 'options-page',
			'show_on'     => array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array('terraclassifieds_settings')
			),
			'parent_slug'     => 'edit.php?post_type=classified'
		);

		if (is_plugin_active('gdpr/gdpr.php')) {
			$gdpr_plugin = __('Remember to add consents in GDPR plugin -> Settings.', 'terraclassifieds');
		} else {
			$gdpr_plugin =  __('You need to install and activate ' . '<a href="https://wordpress.org/plugins/gdpr/">GDPR plugin</a>', 'terraclassifieds');
		}


		// Setup meta box
		$cmb = new_cmb2_box($box_options);

		// setting tabs
		$tabs_setting = array(
			'config' => $box_options,
			'layout' => 'vertical', // Default : horizontal
			'tabs'   => array()
		);

		$tabs_setting['tabs'][] = array(
			'id'     => 'tc_general',
			'title'  => __('General', 'terraclassifieds'),
			'fields' => array(

				array(
					'name' => __('Currency / price settings', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_general_currenct_price_settings',
				),

				array(
					'name'    => __('Currency', 'terraclassifieds'),
					'id'      => '_tc_advert_currency',
					'type'    => 'text',
					'default' => '$',
				),

				array(
					'name'             => __('Unit position', 'terraclassifieds'),
					'id'               => '_tc_unit_position',
					'type'             => 'radio_inline',
					'show_option_none' => false,
					'options'          => array(
						'0' => __('Before price', 'terraclassifieds'),
						'1' => __('After price', 'terraclassifieds'),

					),
					'default' => '1',
				),

				array(
					'name'             => __('Thousand separator', 'terraclassifieds'),
					'id'               => '_tc_price_thousand_separator',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'none' => __('None', 'terraclassifieds'),
						'space' => __('Space', 'terraclassifieds'),
						'comma' => __('Comma', 'terraclassifieds'),
						'dot' => __('Dot', 'terraclassifieds'),
					),
					'default' => 'comma',
				),

				array(
					'name'             => __('Decimal separator', 'terraclassifieds'),
					'id'               => '_tc_price_decimal_separator',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'comma' => __('Comma', 'terraclassifieds'),
						'dot' => __('Dot', 'terraclassifieds'),
					),
					'default' => 'dot',
				),

				array(
					'name'             => __('Decimal points', 'terraclassifieds'),
					'id'               => '_tc_price_decimal_points',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'0' => __('0', 'terraclassifieds'),
						'1' => __('1', 'terraclassifieds'),
						'2' => __('2', 'terraclassifieds'),
						'3' => __('3', 'terraclassifieds'),
						'4' => __('4', 'terraclassifieds'),
					),
					'default' => '2',
				),

				array(
					'name'    => __('Allow "0" price', 'terraclassifieds'),
					'id'      => '_tc_add_advert_allow_price_zero',
					'type'    => 'radio_inline',
					'default' => '0',
					'options'          => array(
						'0' => __('No', 'terraclassifieds'),
						'1'   => __('Yes', 'terraclassifieds'),
					),
					'desc' => __('Select if the user can add price equal zero.', 'terraclassifieds'),
				),

				array(
					'name' => __('Author page', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_general_author_page',
				),

				array(
					'name'             => __('Username', 'terraclassifieds'),
					'id'               => '_tc_author_page_username',
					'type'             => 'radio_inline',
					'show_option_none' => false,
					'options'          => array(
						'0' => __('Hide', 'terraclassifieds'),
						'1' => __('Show', 'terraclassifieds'),

					),
					'default' => '0',
				),

				array(
					'name' => __('Other settings', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_general_other_settings',
				),

				array(
					'name'             => __('Hits', 'terraclassifieds'),
					'id'               => '_tc_hits',
					'type'             => 'radio_inline',
					'show_option_none' => false,
					'options'          => array(
						'0' => __('Hide', 'terraclassifieds'),
						'1' => __('Show', 'terraclassifieds'),

					),
					'default' => '0',
					'desc' => __('If you set "Show", number of ad\'s hits will be visible on advert list and advert view', 'terraclassifieds'),
				),

				array(
					'name'             => __('Date order', 'terraclassifieds'),
					'id'               => '_tc_date_order',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'1' => __('Number - Period - Ago', 'terraclassifieds'),
						'2' => __('Period - Number - Ago', 'terraclassifieds'),
						'3' => __('Ago - Number - Period', 'terraclassifieds'),
						'4' => __('Number - Ago - Period', 'terraclassifieds'),
						'5' => __('Period - Ago - Number', 'terraclassifieds'),
						'6' => __('Ago - Period - Number', 'terraclassifieds'),
					),
					'default' => '1',
					'desc' => __('Select the most suitable date ordering for your language. The approximate time of adding an ad on the ads list.', 'terraclassifieds'),
				),

			)
		);

		$tabs_setting['tabs'][] = array(
			'id'     => 'tc_categories',
			'title'  => __('Category view', 'terraclassifieds'),
			'fields' => array(

				array(
					'name' => __('General', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_category_view_general_settings',
				),

				array(
					'name'    => __('Show category on the archive page', 'terraclassifieds'),
					'id'      => '_tc_show_cat',
					'type'    => 'multicheck',
					'options' => array(
						'none'    => __('None', 'terraclassifieds'),
						'name'    => __('Name', 'terraclassifieds'),
						'desc'    => __('Description', 'terraclassifieds'),
						'image'   => __('Image', 'terraclassifieds'),
						'subcat'  => __('Subcategories', 'terraclassifieds'),
					),
					'select_all_button' => false,
				),

				array(
					'name'    => __('Items per page', 'terraclassifieds'),
					'id'      => '_tc_category_view_items_per_page',
					'type'    => 'text',
					'default' => '4',
				),

				array(
					'name'    => __('Text limit (number of words)', 'terraclassifieds'),
					'id'      => '_tc_category_view_text_limit',
					'type'    => 'text',
					'default' => '10',
				),

				array(
					'name'    => __('Add to favourites', 'terraclassifieds'),
					'id'      => '_tc_category_view_add_to_favourites',
					'type'    => 'radio_inline',
					'default' => '0',
					'options'          => array(
						'0' => __('Hide', 'terraclassifieds'),
						'1'   => __('Show', 'terraclassifieds'),
					),
				),

				array(
					'name'    => __('Ad\'s author', 'terraclassifieds'),
					'id'      => '_tc_category_view_ad_author',
					'type'    => 'radio_inline',
					'default' => '1',
					'options'          => array(
						'0' => __('Hide', 'terraclassifieds'),
						'1'   => __('Show', 'terraclassifieds'),
					),
				),

				array(
					'name' => __('Search form', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_category_view_search_title'
				),

				array(
					'name'             => __('Search form', 'terraclassifieds'),
					'id'               => '_tc_archive_search',
					'type'             => 'radio_inline',
					'options'          => array(
						'0' => __('No', 'terraclassifieds'),
						'1' => __('Yes', 'terraclassifieds'),

					),
					'default' => '1',
				),

				array(
					'name'             => __('Search input size', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_search_size',
				),

				array(
					'name'             => __('Large Desktop ( ≥ 1200px )', 'terraclassifieds'),
					'id'               => '_tc_search_size1',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'12' => __('100% - col-lg-12', 'terraclassifieds'),
						'11' => __('91% - col-lg-11', 'terraclassifieds'),
						'10' => __('83% - col-lg-10', 'terraclassifieds'),
						'9' => __('75% - col-lg-9', 'terraclassifieds'),
						'8' => __('66% - col-lg-8', 'terraclassifieds'),
						'7' => __('58% - col-lg-7', 'terraclassifieds'),
						'6' => __('50% - col-lg-6', 'terraclassifieds'),
						'5' => __('41% - col-lg-5', 'terraclassifieds'),
						'4' => __('33% - col-lg-4', 'terraclassifieds'),
						'3' => __('25% - col-lg-3', 'terraclassifieds'),
						'2' => __('16% - col-lg-2', 'terraclassifieds'),
						'1' => __('8% - col-lg-1', 'terraclassifieds'),
					),
					'default' => '4',
				),

				array(
					'name'             => __('Desktop ( ≥ 992px )', 'terraclassifieds'),
					'id'               => '_tc_search_size2',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'12' => __('100% - col-md-12', 'terraclassifieds'),
						'11' => __('91% - col-md-11', 'terraclassifieds'),
						'10' => __('83% - col-md-10', 'terraclassifieds'),
						'9' => __('75% - col-md-9', 'terraclassifieds'),
						'8' => __('66% - col-md-8', 'terraclassifieds'),
						'7' => __('58% - col-md-7', 'terraclassifieds'),
						'6' => __('50% - col-md-6', 'terraclassifieds'),
						'5' => __('41% - col-md-5', 'terraclassifieds'),
						'4' => __('33% - col-md-4', 'terraclassifieds'),
						'3' => __('25% - col-md-3', 'terraclassifieds'),
						'2' => __('16% - col-md-2', 'terraclassifieds'),
						'1' => __('8% - col-md-1', 'terraclassifieds'),
					),
					'default' => '4',
				),

				array(
					'name'             => __('Tablet ( ≥ 768px )', 'terraclassifieds'),
					'id'               => '_tc_search_size3',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'12' => __('100% - col-sm-12', 'terraclassifieds'),
						'11' => __('91% - col-sm-11', 'terraclassifieds'),
						'10' => __('83% - col-sm-10', 'terraclassifieds'),
						'9' => __('75% - col-sm-9', 'terraclassifieds'),
						'8' => __('66% - col-sm-8', 'terraclassifieds'),
						'7' => __('58% - col-sm-7', 'terraclassifieds'),
						'6' => __('50% - col-sm-6', 'terraclassifieds'),
						'5' => __('41% - col-sm-5', 'terraclassifieds'),
						'4' => __('33% - col-sm-4', 'terraclassifieds'),
						'3' => __('25% - col-sm-3', 'terraclassifieds'),
						'2' => __('16% - col-sm-2', 'terraclassifieds'),
						'1' => __('8% - col-sm-1', 'terraclassifieds'),
					),
					'default' => '12',
				),

				array(
					'name'             => __('Phone ( < 768px )', 'terraclassifieds'),
					'id'               => '_tc_search_size4',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'12' => __('100% - col-xs-12', 'terraclassifieds'),
						'11' => __('91% - col-xs-11', 'terraclassifieds'),
						'10' => __('83% - col-xs-10', 'terraclassifieds'),
						'9' => __('75% - col-xs-9', 'terraclassifieds'),
						'8' => __('66% - col-xs-8', 'terraclassifieds'),
						'7' => __('58% - col-xs-7', 'terraclassifieds'),
						'6' => __('50% - col-xs-6', 'terraclassifieds'),
						'5' => __('41% - col-xs-5', 'terraclassifieds'),
						'4' => __('33% - col-xs-4', 'terraclassifieds'),
						'3' => __('25% - col-xs-3', 'terraclassifieds'),
						'2' => __('16% - col-xs-2', 'terraclassifieds'),
						'1' => __('8% - col-xs-1', 'terraclassifieds'),
					),
					'default' => '12',
				),

				array(
					'name'             => __('Show location filter', 'terraclassifieds'),
					'id'               => '_tc_archive_search_location_show',
					'type'             => 'radio_inline',
					'options'          => array(
						'0' => __('No', 'terraclassifieds'),
						'1' => __('Yes', 'terraclassifieds'),

					),
					'default' => '1',
				),

				array(
					'name'             => __('Location field type', 'terraclassifieds'),
					'id'               => '_tc_archive_search_location_kind',
					'type'             => 'radio_inline',
					'options'          => array(
						'0' => __('List', 'terraclassifieds'),
						'1' => __('Inputbox', 'terraclassifieds'),

					),
					'default' => '0',
				),

				array(
					'name'             => __('Location hierarchical', 'terraclassifieds'),
					'id'               => '_tc_archive_search_location_hierarchical',
					'type'             => 'radio_inline',
					'options'          => array(
						'0' => __('No', 'terraclassifieds'),
						'1' => __('Yes', 'terraclassifieds'),
					),
					'default' => '0',
					'desc' => __('Available only for location field type - <strong>list</strong>. Disabling hierarchical can be useful (quicker) when you have a lot of nested locations.', 'terraclassifieds'),
				),

				array(
					'name'             => __('Location field size', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_location_size',
				),

				array(
					'name'             => __('Large Desktop ( ≥ 1200px )', 'terraclassifieds'),
					'id'               => '_tc_location_size1',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'12' => __('100% - col-lg-12', 'terraclassifieds'),
						'11' => __('91% - col-lg-11', 'terraclassifieds'),
						'10' => __('83% - col-lg-10', 'terraclassifieds'),
						'9' => __('75% - col-lg-9', 'terraclassifieds'),
						'8' => __('66% - col-lg-8', 'terraclassifieds'),
						'7' => __('58% - col-lg-7', 'terraclassifieds'),
						'6' => __('50% - col-lg-6', 'terraclassifieds'),
						'5' => __('41% - col-lg-5', 'terraclassifieds'),
						'4' => __('33% - col-lg-4', 'terraclassifieds'),
						'3' => __('25% - col-lg-3', 'terraclassifieds'),
						'2' => __('16% - col-lg-2', 'terraclassifieds'),
						'1' => __('8% - col-lg-1', 'terraclassifieds'),
					),
					'default' => '4',
				),

				array(
					'name'             => __('Desktop ( ≥ 992px )', 'terraclassifieds'),
					'id'               => '_tc_location_size2',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'12' => __('100% - col-md-12', 'terraclassifieds'),
						'11' => __('91% - col-md-11', 'terraclassifieds'),
						'10' => __('83% - col-md-10', 'terraclassifieds'),
						'9' => __('75% - col-md-9', 'terraclassifieds'),
						'8' => __('66% - col-md-8', 'terraclassifieds'),
						'7' => __('58% - col-md-7', 'terraclassifieds'),
						'6' => __('50% - col-md-6', 'terraclassifieds'),
						'5' => __('41% - col-md-5', 'terraclassifieds'),
						'4' => __('33% - col-md-4', 'terraclassifieds'),
						'3' => __('25% - col-md-3', 'terraclassifieds'),
						'2' => __('16% - col-md-2', 'terraclassifieds'),
						'1' => __('8% - col-md-1', 'terraclassifieds'),
					),
					'default' => '4',
				),

				array(
					'name'             => __('Tablet ( ≥ 768px )', 'terraclassifieds'),
					'id'               => '_tc_location_size3',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'12' => __('100% - col-sm-12', 'terraclassifieds'),
						'11' => __('91% - col-sm-11', 'terraclassifieds'),
						'10' => __('83% - col-sm-10', 'terraclassifieds'),
						'9' => __('75% - col-sm-9', 'terraclassifieds'),
						'8' => __('66% - col-sm-8', 'terraclassifieds'),
						'7' => __('58% - col-sm-7', 'terraclassifieds'),
						'6' => __('50% - col-sm-6', 'terraclassifieds'),
						'5' => __('41% - col-sm-5', 'terraclassifieds'),
						'4' => __('33% - col-sm-4', 'terraclassifieds'),
						'3' => __('25% - col-sm-3', 'terraclassifieds'),
						'2' => __('16% - col-sm-2', 'terraclassifieds'),
						'1' => __('8% - col-sm-1', 'terraclassifieds'),
					),
					'default' => '12',
				),

				array(
					'name'             => __('Phone ( < 768px )', 'terraclassifieds'),
					'id'               => '_tc_location_size4',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'12' => __('100% - col-xs-12', 'terraclassifieds'),
						'11' => __('91% - col-xs-11', 'terraclassifieds'),
						'10' => __('83% - col-xs-10', 'terraclassifieds'),
						'9' => __('75% - col-xs-9', 'terraclassifieds'),
						'8' => __('66% - col-xs-8', 'terraclassifieds'),
						'7' => __('58% - col-xs-7', 'terraclassifieds'),
						'6' => __('50% - col-xs-6', 'terraclassifieds'),
						'5' => __('41% - col-xs-5', 'terraclassifieds'),
						'4' => __('33% - col-xs-4', 'terraclassifieds'),
						'3' => __('25% - col-xs-3', 'terraclassifieds'),
						'2' => __('16% - col-xs-2', 'terraclassifieds'),
						'1' => __('8% - col-xs-1', 'terraclassifieds'),
					),
					'default' => '12',
				),

				array(
					'name'             => __('Show category filter', 'terraclassifieds'),
					'id'               => '_tc_archive_search_category_show',
					'type'             => 'radio_inline',
					'options'          => array(
						'0' => __('No', 'terraclassifieds'),
						'1' => __('Yes', 'terraclassifieds'),

					),
					'default' => '1',
				),

				array(
					'name'             => __('Display only first level categories', 'terraclassifieds'),
					'id'               => '_tc_archive_search_category_only_first_level',
					'type'             => 'radio_inline',
					'options'          => array(
						'0' => __('No', 'terraclassifieds'),
						'1' => __('Yes', 'terraclassifieds'),

					),
					'default' => '0',
				),

				array(
					'name'             => __('Category field size', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_category_size',
				),

				array(
					'name'             => __('Large Desktop ( ≥ 1200px )', 'terraclassifieds'),
					'id'               => '_tc_category_size1',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'12' => __('100% - col-lg-12', 'terraclassifieds'),
						'11' => __('91% - col-lg-11', 'terraclassifieds'),
						'10' => __('83% - col-lg-10', 'terraclassifieds'),
						'9' => __('75% - col-lg-9', 'terraclassifieds'),
						'8' => __('66% - col-lg-8', 'terraclassifieds'),
						'7' => __('58% - col-lg-7', 'terraclassifieds'),
						'6' => __('50% - col-lg-6', 'terraclassifieds'),
						'5' => __('41% - col-lg-5', 'terraclassifieds'),
						'4' => __('33% - col-lg-4', 'terraclassifieds'),
						'3' => __('25% - col-lg-3', 'terraclassifieds'),
						'2' => __('16% - col-lg-2', 'terraclassifieds'),
						'1' => __('8% - col-lg-1', 'terraclassifieds'),
					),
					'default' => '4',
				),

				array(
					'name'             => __('Desktop ( ≥ 992px )', 'terraclassifieds'),
					'id'               => '_tc_category_size2',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'12' => __('100% - col-md-12', 'terraclassifieds'),
						'11' => __('91% - col-md-11', 'terraclassifieds'),
						'10' => __('83% - col-md-10', 'terraclassifieds'),
						'9' => __('75% - col-md-9', 'terraclassifieds'),
						'8' => __('66% - col-md-8', 'terraclassifieds'),
						'7' => __('58% - col-md-7', 'terraclassifieds'),
						'6' => __('50% - col-md-6', 'terraclassifieds'),
						'5' => __('41% - col-md-5', 'terraclassifieds'),
						'4' => __('33% - col-md-4', 'terraclassifieds'),
						'3' => __('25% - col-md-3', 'terraclassifieds'),
						'2' => __('16% - col-md-2', 'terraclassifieds'),
						'1' => __('8% - col-md-1', 'terraclassifieds'),
					),
					'default' => '4',
				),

				array(
					'name'             => __('Tablet ( ≥ 768px )', 'terraclassifieds'),
					'id'               => '_tc_category_size3',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'12' => __('100% - col-sm-12', 'terraclassifieds'),
						'11' => __('91% - col-sm-11', 'terraclassifieds'),
						'10' => __('83% - col-sm-10', 'terraclassifieds'),
						'9' => __('75% - col-sm-9', 'terraclassifieds'),
						'8' => __('66% - col-sm-8', 'terraclassifieds'),
						'7' => __('58% - col-sm-7', 'terraclassifieds'),
						'6' => __('50% - col-sm-6', 'terraclassifieds'),
						'5' => __('41% - col-sm-5', 'terraclassifieds'),
						'4' => __('33% - col-sm-4', 'terraclassifieds'),
						'3' => __('25% - col-sm-3', 'terraclassifieds'),
						'2' => __('16% - col-sm-2', 'terraclassifieds'),
						'1' => __('8% - col-sm-1', 'terraclassifieds'),
					),
					'default' => '12',
				),

				array(
					'name'             => __('Phone ( < 768px )', 'terraclassifieds'),
					'id'               => '_tc_category_size4',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'12' => __('100% - col-xs-12', 'terraclassifieds'),
						'11' => __('91% - col-xs-11', 'terraclassifieds'),
						'10' => __('83% - col-xs-10', 'terraclassifieds'),
						'9' => __('75% - col-xs-9', 'terraclassifieds'),
						'8' => __('66% - col-xs-8', 'terraclassifieds'),
						'7' => __('58% - col-xs-7', 'terraclassifieds'),
						'6' => __('50% - col-xs-6', 'terraclassifieds'),
						'5' => __('41% - col-xs-5', 'terraclassifieds'),
						'4' => __('33% - col-xs-4', 'terraclassifieds'),
						'3' => __('25% - col-xs-3', 'terraclassifieds'),
						'2' => __('16% - col-xs-2', 'terraclassifieds'),
						'1' => __('8% - col-xs-1', 'terraclassifieds'),
					),
					'default' => '12',
				),

				array(
					'name'             => __('Use types filter', 'terraclassifieds'),
					'id'               => '_tc_archive_search_use_type_filter',
					'type'             => 'radio_inline',
					'options'          => array(
						'0' => __('No', 'terraclassifieds'),
						'1' => __('Yes', 'terraclassifieds'),

					),
					'default' => '0',
				),

				array(
					'name'             => __('Use selling type filter', 'terraclassifieds'),
					'id'               => '_tc_archive_search_use_selling_type_filter',
					'type'             => 'radio_inline',
					'options'          => array(
						'0' => __('No', 'terraclassifieds'),
						'1' => __('Yes', 'terraclassifieds'),

					),
					'default' => '0',
				),

				array(
					'name'             => __('Use price filter', 'terraclassifieds'),
					'id'               => '_tc_archive_search_use_price_filter',
					'type'             => 'radio_inline',
					'options'          => array(
						'0' => __('No', 'terraclassifieds'),
						'1' => __('Yes', 'terraclassifieds'),

					),
					'default' => '0',
				),

				array(
					'name'             => __('Price field size', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_price_size',
				),

				array(
					'name'             => __('Large Desktop ( ≥ 1200px )', 'terraclassifieds'),
					'id'               => '_tc_price_size1',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'12' => __('100% - col-lg-12', 'terraclassifieds'),
						'11' => __('91% - col-lg-11', 'terraclassifieds'),
						'10' => __('83% - col-lg-10', 'terraclassifieds'),
						'9' => __('75% - col-lg-9', 'terraclassifieds'),
						'8' => __('66% - col-lg-8', 'terraclassifieds'),
						'7' => __('58% - col-lg-7', 'terraclassifieds'),
						'6' => __('50% - col-lg-6', 'terraclassifieds'),
						'5' => __('41% - col-lg-5', 'terraclassifieds'),
						'4' => __('33% - col-lg-4', 'terraclassifieds'),
						'3' => __('25% - col-lg-3', 'terraclassifieds'),
						'2' => __('16% - col-lg-2', 'terraclassifieds'),
						'1' => __('8% - col-lg-1', 'terraclassifieds'),
					),
					'default' => '6',
				),

				array(
					'name'             => __('Desktop ( ≥ 992px )', 'terraclassifieds'),
					'id'               => '_tc_price_size2',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'12' => __('100% - col-md-12', 'terraclassifieds'),
						'11' => __('91% - col-md-11', 'terraclassifieds'),
						'10' => __('83% - col-md-10', 'terraclassifieds'),
						'9' => __('75% - col-md-9', 'terraclassifieds'),
						'8' => __('66% - col-md-8', 'terraclassifieds'),
						'7' => __('58% - col-md-7', 'terraclassifieds'),
						'6' => __('50% - col-md-6', 'terraclassifieds'),
						'5' => __('41% - col-md-5', 'terraclassifieds'),
						'4' => __('33% - col-md-4', 'terraclassifieds'),
						'3' => __('25% - col-md-3', 'terraclassifieds'),
						'2' => __('16% - col-md-2', 'terraclassifieds'),
						'1' => __('8% - col-md-1', 'terraclassifieds'),
					),
					'default' => '6',
				),

				array(
					'name'             => __('Tablet ( ≥ 768px )', 'terraclassifieds'),
					'id'               => '_tc_price_size3',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'12' => __('100% - col-sm-12', 'terraclassifieds'),
						'11' => __('91% - col-sm-11', 'terraclassifieds'),
						'10' => __('83% - col-sm-10', 'terraclassifieds'),
						'9' => __('75% - col-sm-9', 'terraclassifieds'),
						'8' => __('66% - col-sm-8', 'terraclassifieds'),
						'7' => __('58% - col-sm-7', 'terraclassifieds'),
						'6' => __('50% - col-sm-6', 'terraclassifieds'),
						'5' => __('41% - col-sm-5', 'terraclassifieds'),
						'4' => __('33% - col-sm-4', 'terraclassifieds'),
						'3' => __('25% - col-sm-3', 'terraclassifieds'),
						'2' => __('16% - col-sm-2', 'terraclassifieds'),
						'1' => __('8% - col-sm-1', 'terraclassifieds'),
					),
					'default' => '6',
				),

				array(
					'name'             => __('Phone ( < 768px )', 'terraclassifieds'),
					'id'               => '_tc_price_size4',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'12' => __('100% - col-xs-12', 'terraclassifieds'),
						'11' => __('91% - col-xs-11', 'terraclassifieds'),
						'10' => __('83% - col-xs-10', 'terraclassifieds'),
						'9' => __('75% - col-xs-9', 'terraclassifieds'),
						'8' => __('66% - col-xs-8', 'terraclassifieds'),
						'7' => __('58% - col-xs-7', 'terraclassifieds'),
						'6' => __('50% - col-xs-6', 'terraclassifieds'),
						'5' => __('41% - col-xs-5', 'terraclassifieds'),
						'4' => __('33% - col-xs-4', 'terraclassifieds'),
						'3' => __('25% - col-xs-3', 'terraclassifieds'),
						'2' => __('16% - col-xs-2', 'terraclassifieds'),
						'1' => __('8% - col-xs-1', 'terraclassifieds'),
					),
					'default' => '12',
				),

			)
		);

		$tabs_setting['tabs'][] = array(
			'id'     => 'tc_ad_view',
			'title'  => __('Advert view', 'terraclassifieds'),
			'fields' => array(

				array(
					'name'    => __('Add to favourites', 'terraclassifieds'),
					'id'      => '_tc_ad_view_add_to_favourites',
					'type'    => 'radio_inline',
					'default' => '0',
					'options'          => array(
						'0' => __('Hide', 'terraclassifieds'),
						'1'   => __('Show', 'terraclassifieds'),
					),
				),

				array(
					'name'    => __('Contact form', 'terraclassifieds'),
					'id'      => '_tc_ad_view_contact_form',
					'type'    => 'radio_inline',
					'default' => '1',
					'options'          => array(
						'0' => __('Hide', 'terraclassifieds'),
						'1'   => __('Show', 'terraclassifieds'),
					),
				),

				array(
					'name'    => __('Report abuse', 'terraclassifieds'),
					'id'      => '_tc_ad_view_report_abuse',
					'type'    => 'radio_inline',
					'default' => '1',
					'options'          => array(
						'0' => __('Hide', 'terraclassifieds'),
						'1'   => __('Show', 'terraclassifieds'),
					),
				),

				array(
					'name'    => __('Phone number', 'terraclassifieds'),
					'id'      => '_tc_ad_view_phone_number',
					'type'    => 'radio_inline',
					'default' => '1',
					'options'          => array(
						'0' => __('Hide', 'terraclassifieds'),
						'1'   => __('Show', 'terraclassifieds'),
					),
					'desc' => __('Phone number is taken from user profile.', 'terraclassifieds'),
				),

				array(
					'name'    => __('Website URL', 'terraclassifieds'),
					'id'      => '_tc_ad_view_website_url',
					'type'    => 'radio_inline',
					'default' => '1',
					'options'          => array(
						'0' => __('Hide', 'terraclassifieds'),
						'1'   => __('Show', 'terraclassifieds'),
					),
				),

				array(
					'name'    => __('Ad\'s author', 'terraclassifieds'),
					'id'      => '_tc_ad_view_ad_author',
					'type'    => 'radio_inline',
					'default' => '1',
					'options'          => array(
						'0' => __('Hide', 'terraclassifieds'),
						'1'   => __('Show', 'terraclassifieds'),
					),
				),

			)
		);

		$tabs_setting['tabs'][] = array(
			'id'     => 'tc_adding_advert',
			'title'  => __('Adding advert view', 'terraclassifieds'),
			'fields' => array(

				array(
					'name'    => __('Number of images limit (max. 8)', 'terraclassifieds'),
					'id'      => '_tc_add_advert_images_limit',
					'type'    => 'text',
					'default' => '8',
					'attributes' => array(
						'type' => 'number',
					),
				),

				array(
					'name'    => __('Title characters limit (0 for unlimited)', 'terraclassifieds'),
					'id'      => '_tc_add_advert_title_characters_limit',
					'type'    => 'text',
					'default' => '0',
					'attributes' => array(
						'type' => 'number',
					),
				),

				array(
					'name'             => __('Description characters limit', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_description_characters_limit_label',
				),

				array(
					'name'    => __('Min', 'terraclassifieds'),
					'id'      => '_tc_add_advert_description_minimum_length',
					'type'    => 'text',
					'default' => '1',
					'attributes' => array(
						'type' => 'number',
						'min' => 1,
					),
				),

				array(
					'name'    => __('Max (0 for unlimited)', 'terraclassifieds'),
					'id'      => '_tc_add_advert_description_characters_limit',
					'type'    => 'text',
					'default' => '0',
					'attributes' => array(
						'type' => 'number',
					),
				),

				array(
					'name'    => __('The number of columns for subcategories list', 'terraclassifieds'),
					'id'      => '_tc_add_advert_subcategories_columns',
					'type'    => 'text',
					'default' => '4',
					'attributes' => array(
						'type' => 'number',
					),
				),

				array(
					'name'    => __('The number of days after which the ads will be archived.', 'terraclassifieds'),
					'id'      => '_tc_advert_expire_time',
					'type'    => 'text',
					'default' => '30',
					'attributes' => array(
						'type' => 'number',
					),
				),

				array(
					'name'    => __('The number of days before sending a notification to classified\'s author.', 'terraclassifieds'),
					'id'      => '_tc_advert_expire_time_before_notification',
					'type'    => 'text',
					'default' => '3',
					'attributes' => array(
						'type' => 'number',
					),
				),

				array(
					'name'    => __('Use selling types', 'terraclassifieds'),
					'id'      => '_tc_use_selling_types',
					'type'    => 'radio_inline',
					'default' => '1',
					'options'          => array(
						'0' => __('No', 'terraclassifieds'),
						'1'   => __('Yes', 'terraclassifieds'),
					),
					'desc' => __('If disabled, all selling types ( including price ) will be not visible on the single, archive and add advert views.', 'terraclassifieds'),
				),

				array(
					'name'    => __('Selling types', 'terraclassifieds'),
					'id'      => '_tc_selling_types',
					'type'    => 'multicheck',
					'options' => array(
						'price'    => __('Price', 'terraclassifieds'),
						'for-free'    => __('For free', 'terraclassifieds'),
						'exchange'    => __('Exchange', 'terraclassifieds'),
						'nothing'    => __('Nothing', 'terraclassifieds'),
					),
					'select_all_button' => false,
					'default'          => array('price', 'for-free', 'exchange'),
					'desc' => __('At least one option must be selected.', 'terraclassifieds'),
				),

				array(
					'name'    => __('Ad status', 'terraclassifieds'),
					'id'      => '_tc_add_advert_ad_status',
					'type'    => 'radio_inline',
					'default' => '0',
					'options'          => array(
						'0' => __('Pending', 'terraclassifieds'),
						'1'   => __('Published', 'terraclassifieds'),
					),
					'desc' => __('Select if status of newly created ad should be <strong>pending</strong> or <strong>published</strong>.', 'terraclassifieds'),
				),

				array(
					'name'    => __('Ad images required', 'terraclassifieds'),
					'id'      => '_tc_add_advert_required_gallery',
					'type'    => 'radio_inline',
					'default' => '0',
					'options'          => array(
						'0' => __('No', 'terraclassifieds'),
						'1'   => __('Yes', 'terraclassifieds'),
					),
					'desc' => __('Select if adding at least one ad\'s image should be required.', 'terraclassifieds'),
				),

				array(
					'name'    => __('Ad location required', 'terraclassifieds'),
					'id'      => '_tc_add_advert_required_location',
					'type'    => 'radio_inline',
					'default' => '1',
					'options'          => array(
						'0' => __('No', 'terraclassifieds'),
						'1'   => __('Yes', 'terraclassifieds'),
					),
					'desc' => __('Select if "Ads location" should be required. Locations must be enabled first.', 'terraclassifieds'),
				),

				array(
					'name'             => __('Ad type required', 'terraclassifieds'),
					'id'               => '_tc_types_required',
					'type'             => 'radio_inline',
					'options'          => array(
						'0' => __('No', 'terraclassifieds'),
						'1' => __('Yes', 'terraclassifieds'),

					),
					'default' => '0',
					'desc' => __('Select if "Ads Types" should be required. Types must be enabled first.', 'terraclassifieds'),
				),

			)
		);

		$tabs_setting['tabs'][] = array(
			'id'     => 'tc_locations',
			'title'  => __('Locations', 'terraclassifieds'),
			'fields' => array(

				array(
					'name'             => __('Use locations', 'terraclassifieds'),
					'id'               => '_tc_use_locations',
					'type'             => 'radio_inline',
					'options'          => array(
						'0' => __('No', 'terraclassifieds'),
						'1' => __('Yes', 'terraclassifieds'),

					),
					'default' => '1',
					'desc' => __('Remember to create locations in TerraClassifieds -> Locations.<br />If No, all location relations will be disabled including Adding advert view.', 'terraclassifieds'),
				),

				array(
					'name'             => __('Show location', 'terraclassifieds'),
					'id'               => '_tc_show_location',
					'type'             => 'radio_inline',
					'options'          => array(
						'0' => __('No', 'terraclassifieds'),
						'1' => __('Yes', 'terraclassifieds'),

					),
					'default' => '1',
					'desc' => __('Show locations on category view and single advert view.', 'terraclassifieds'),
					'classes' => 'location-fields',
				),

				array(
					'name'             => __('Address field', 'terraclassifieds'),
					'id'               => '_tc_location_address',
					'type'             => 'radio_inline',
					'options'          => array(
						'0' => __('No', 'terraclassifieds'),
						'1' => __('Yes', 'terraclassifieds'),

					),
					'default' => '0',
					'desc' => __('Use the filed on Adding advert view and Advert view.', 'terraclassifieds'),
					'classes' => 'location-fields',
				),

				array(
					'name'             => __('Post / ZIP code field', 'terraclassifieds'),
					'id'               => '_tc_location_post_code',
					'type'             => 'radio_inline',
					'options'          => array(
						'0' => __('No', 'terraclassifieds'),
						'1' => __('Yes', 'terraclassifieds'),

					),
					'default' => '0',
					'desc' => __('Use the filed on Adding advert view and Advert view.', 'terraclassifieds'),
					'classes' => 'location-fields',
				),

			)
		);

		$tabs_setting['tabs'][] = array(
			'id'     => 'tc_types',
			'title'  => __('Types', 'terraclassifieds'),
			'fields' => array(

				array(
					'name'             => __('Use "ads types"', 'terraclassifieds'),
					'id'               => '_tc_use_types',
					'type'             => 'radio_inline',
					'options'          => array(
						'0' => __('No', 'terraclassifieds'),
						'1' => __('Yes', 'terraclassifieds'),

					),
					'default' => '0',
					'desc' => __('Remember to create types in TerraClassifieds -> Types.<br />If No, all types relations will be disabled including Adding advert view', 'terraclassifieds'),
				),

				array(
					'name'             => __('"Types" display style', 'terraclassifieds'),
					'id'               => '_tc_types_display_style',
					'type'             => 'radio_inline',
					'options'          => array(
						'selectlist' => __('Selectlist', 'terraclassifieds'),
						'radio_buttons' => __('Checkboxes', 'terraclassifieds'),

					),
					'default' => 'selectlist',
					'desc' => __('Style for displaying "Types" on advert edit mode and advert adding page. Selectlist or checkboxes with preview of types<br /><strong>NOTE</strong>: Selectlist lets you choose only one type per classified.', 'terraclassifieds'),
				),

			)
		);

		$tabs_setting['tabs'][] = array(
			'id'     => 'tc_image_processing',
			'title'  => __('Image processing', 'terraclassifieds'),
			'fields' => array(

				array(
					'name'    => __('Use images', 'terraclassifieds'),
					'id'      => '_tc_image_use_images',
					'type'    => 'radio_inline',
					'default' => '1',
					'options'          => array(
						'0' => __('No', 'terraclassifieds'),
						'1'   => __('Yes', 'terraclassifieds'),
					),
					'desc' => __('If No, the option to add advert images is blocked and no advert images displayed.', 'terraclassifieds'),
				),

				array(
					'name' => __('Image sizes for classifieds archive view', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_image_archive_title'
				),

				array(
					'name'    => __('Image width (px)', 'terraclassifieds'),
					'id'      => '_tc_image_archive_width',
					'type'    => 'text',
					'default' => '170',
				),

				array(
					'name'    => __('Image height (px)', 'terraclassifieds'),
					'id'      => '_tc_image_archive_height',
					'type'    => 'text',
					'default' => '145',
				),

				array(
					'name' => __('Image sizes for single post view', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_image_single_title'
				),

				array(
					'name'    => __('Image width (px)', 'terraclassifieds'),
					'id'      => '_tc_image_single_width',
					'type'    => 'text',
					'default' => '980',
				),

				array(
					'name'    => __('Image height (px)', 'terraclassifieds'),
					'id'      => '_tc_image_single_height',
					'type'    => 'text',
					'default' => '735',
				),

				array(
					'name' => __('Other settings', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_image_other_settings'
				),

				array(
					'name'    => __('No image', 'terraclassifieds'),
					'desc' => __('Set the image that will be used once the user does not upload any images for an advert.', 'terraclassifieds'),
					'id'      => '_tc_image_no_image',
					'type'    => 'file',
					// Optional:
					'options' => array(
						'url' => false, // Hide the text input for the url
					),
					'text'    => array(
						'add_upload_file_text' => __('Select image', 'terraclassifieds'),
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
					'preview_size' => 'full',
				),

			)
		);

		$tabs_setting['tabs'][] = array(
			'id'     => 'tc_style',
			'title'  => __('Style', 'terraclassifieds'),
			'fields' => array(

				array(
					'name'             => __('Layout', 'terraclassifieds'),
					'id'               => '_tc_layout',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'0' => __('Default', 'terraclassifieds'),
						'1' => __('Override from theme', 'terraclassifieds'),

					),
					'default' => '0',
				),

				array(
					'name'             => __('CSS Style', 'terraclassifieds'),
					'id'               => '_tc_style',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'0' => __('Default', 'terraclassifieds'),
						'1' => __('Override from theme', 'terraclassifieds'),

					),
					'default' => '0',
					'desc' => __('Read important details about the <a href="https://terraclassifieds.pixelemu.com/settings-and-options/172-style-settings" target="_blank">layout and CSS style settings</a>', 'terraclassifieds'),
				),

			)
		);

		$tabs_setting['tabs'][] = array(
			'id'     => 'tc_security',
			'title'  => __('Security', 'terraclassifieds'),
			'fields' => array(

				array(
					'name' => __('GDPR', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_gdpr_title'
				),

				array(
					'name'             => __('GDPR method', 'terraclassifieds'),
					'id'               => '_tc_gdpr_method',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'0' => __('Simple', 'terraclassifieds'),
						'1' => __('Extended', 'terraclassifieds'),
					),
					'default' => '0',
				),

				array(
					'name'             => '',
					'id'               => '_tc_gdpr_plugin',
					'desc' => $gdpr_plugin,
					'type'             => 'title',
				),

				array(
					'name'             => __('Terms and conditions', 'terraclassifieds'),
					'id'               => '_tc_terms_and_conditions',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'0' => __('No', 'terraclassifieds'),
						'1' => __('Open in new window', 'terraclassifieds'),
						'2' => __('Open in modal', 'terraclassifieds'),
					),
					'default' => '0',
				),

				array(
					'name'             => __('Terms and conditions page', 'terraclassifieds'),
					'id'               => '_tc_terms_and_conditions_page',
					'type'             => 'post_search_text',
					'post_type'   => array('page'),
					'select_type' => 'radio',
					'select_behavior' => 'replace',
				),

				array(
					'name'             => __('Privacy policy', 'terraclassifieds'),
					'id'               => '_tc_privacy_policy',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'0' => __('No', 'terraclassifieds'),
						'1' => __('Open in new window', 'terraclassifieds'),
						'2' => __('Open in modal', 'terraclassifieds'),
					),
					'default' => '0',
				),

				array(
					'name'             => __('Privacy policy page', 'terraclassifieds'),
					'id'               => '_tc_privacy_policy_page',
					'type'             => 'post_search_text',
					'post_type'   => array('page'),
					'select_type' => 'radio',
					'select_behavior' => 'replace',
				),

				array(
					'name'             => __('Data protection agreement (GDPR)', 'terraclassifieds'),
					'id'               => '_tc_gdpr',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'0' => __('No', 'terraclassifieds'),
						'1' => __('Yes', 'terraclassifieds'),
					),
					'default' => '0',
				),

				array(
					'name'             => __('Agreement information (GDPR)', 'terraclassifieds'),
					'id'               => '_tc_gdpr_information',
					'type'             => 'textarea',
					'default' => __('GDPR Information', 'terraclassifieds'),
				),

				array(
					'name' => __('Password for registration', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_password_registration_title'
				),

				array(
					'name'             => __('Password for registration', 'terraclassifieds'),
					'id'               => '_tc_password_registration',
					'type'             => 'radio',
					'show_option_none' => false,
					'options'          => array(
						'0' => __('Automatically generated password', 'terraclassifieds'),
						'1' => __('Let the user choose a password during registration', 'terraclassifieds'),
					),
					'default' => '0',
				),

				array(
					'name' => __('Captcha', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_captcha'
				),

				array(
					'name'             => __('Google reCaptcha API', 'terraclassifieds'),
					'id'               => '_tc_recaptcha',
					'type'             => 'radio_inline',
					'options'          => array(
						'0' => __('No', 'terraclassifieds'),
						'1' => __('Yes', 'terraclassifieds'),

					),
					'default' => '0',
					'desc' => __('Enable/disable loading Google reCaptcha API (required for spam protection on the "Registration" page)', 'terraclassifieds'),
				),

				array(
					'name'    => __('reCAPTCHA Site key', 'terraclassifieds'),
					'id'      => '_tc_recaptcha_site_key',
					'type'    => 'text',
					'default' => '',
				),
				array(
					'name'    => __('reCAPTCHA Secret key', 'terraclassifieds'),
					'id'      => '_tc_recaptcha_secret_key',
					'type'    => 'text',
					'default' => '',
				),
			)
		);

		$tabs_setting['tabs'][] = array(
			'id'     => 'tc_email_templates',
			'title'  => __('Email templates', 'terraclassifieds'),
			'fields' => array(

				array(
					'name' => __('Reply-to e-mail address', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_email_template_reply_to_email_address_title',
				),

				array(
					'name'             => __('E-mail address', 'terraclassifieds'),
					'id'               => '_tc_email_template_reply_to_email_address',
					'type'             => 'text_email',
					'desc' => __('Add email address that will be used as "reply-to" for all user notification messages (except contact form).<br /><strong>NOTE:</strong> This is not the email address used for sending system messages. If your Wordpress uses PHP mail<br /> (the most common case) emails will be sent from the address: wordpress@yourdomain.', 'terraclassifieds'),
				),

				// Registration
				array(
					'name' => __('Registration - administrator notification', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_email_template_registration_administrator_title',
				),

				array(
					'name'             => __('Registration - administrator (subject)', 'terraclassifieds'),
					'id'               => '_tc_email_template_registration_administrator_subject',
					'type'             => 'text',
					'default' => __('[[[website_name]]] New User Registration', 'terraclassifieds'),
				),

				array(
					'name'             => __('Registration - administrator (message)', 'terraclassifieds'),
					'id'               => '_tc_email_template_registration_administrator_message',
					'type'             => 'textarea_code',
					'default' => __('
	<p>New user registration on your site [[website_name]]:</p>
	<p>Username: [[user_login]]</p>
	<p>E-mail: [[user_email]]</p>', 'terraclassifieds'),
				),

				array(
					'name' => __('Available tags', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_email_template_registration_administrator_af',
					'desc' => __('[[website_name]] - Name of your website<br />[[user_login]] - New user login name<br />[[user_email]] - New user email', 'terraclassifieds'),
				),

				array(
					'name' => __('Registration - user notification', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_email_template_registration_user_title',
				),

				array(
					'name'             => __('Registration - user (subject)', 'terraclassifieds'),
					'id'               => '_tc_email_template_registration_user_subject',
					'type'             => 'text',
					'default' => __('[[[website_name]]] Your username and password', 'terraclassifieds'),
				),

				array(
					'name'             => __('Registration - user (message)', 'terraclassifieds'),
					'id'               => '_tc_email_template_registration_user_message',
					'type'             => 'textarea_code',
					'default' => __('
	<p>Welcome on site [[website_name]]:</p>
	<p>Your username: [[user_login]]</p>
	<p>Your password: [[user_passwordl]]</p>', 'terraclassifieds'),
				),

				array(
					'name' => __('Available tags', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_email_template_registration_user_af',
					'desc' => __('[[website_name]] - Name of your website<br />[[user_login]] - New user login name<br />[[user_passwordl]] - New user password', 'terraclassifieds'),
				),

				// New advert
				array(
					'name' => __('New advert - administrator notification', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_email_template_new_advert_administrator_title',
				),

				array(
					'name'             => __('New advert - administrator (subject)', 'terraclassifieds'),
					'id'               => '_tc_email_template_new_advert_administrator_subject',
					'type'             => 'text',
					'default' => __('New advert', 'terraclassifieds'),
				),

				array(
					'name'             => __('New advert - administrator (message)', 'terraclassifieds'),
					'id'               => '_tc_email_template_new_advert_administrator_message',
					'type'             => 'textarea_code',
					'default' => __('
	<p>Hello,</p>
	<p>New advert: [[advert_title_link]]</p>
	<p>Category: [[advert_category]]</p>
	<p>Author login: [[advert_author_login]]</p>
	<p>Author email: [[advert_author_email]]</p>
	<p>Description: [[advert_desc]]</p>', 'terraclassifieds'),
				),

				array(
					'name' => __('Available tags', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_email_template_new_advert_administrator_af',
					'desc' => __('[[advert_title_link]] - Linked advert title (shortcode is available only if option <strong>Ad status</strong> is set to <strong>Publish</strong> on cart <strong>Adding advert view</strong>)<br />[[advert_category]] - Advert category<br />[[advert_status]] - Advert status<br />[[advert_desc]] - Advert description<br />[[advert_author_login]] - Advert author login<br />[[advert_author_email]] - Advert author email', 'terraclassifieds'),
				),

				array(
					'name' => __('New advert - user notification', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_email_template_new_advert_user_title',
				),

				array(
					'name'             => __('New advert - user (subject)', 'terraclassifieds'),
					'id'               => '_tc_email_template_new_advert_user_subject',
					'type'             => 'text',
					'default' => __('New advert', 'terraclassifieds'),
				),

				array(
					'name'             => __('New advert - user (message)', 'terraclassifieds'),
					'id'               => '_tc_email_template_new_advert_user_message',
					'type'             => 'textarea_code',
					'default' => __('
	<p>Hello,</p>
	<p>Your new advert is waiting for the administrator review.</p>
	<p>Category: [[advert_category]]</p>
	<p></p><p>Status: [[advert_status]]</p>
	<p>Description: [[advert_desc]]</p>', 'terraclassifieds'),
				),

				array(
					'name' => __('Available tags', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_email_template_new_advert_user_af',
					'desc' => __('[[advert_category]] - Advert category<br />[[advert_status]] - Advert status<br />[[advert_desc]] - Advert description', 'terraclassifieds'),
				),

				// Contact Form in advert view
				array(
					'name' => __('Contact form - user notification', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_email_template_contact_form_user_title',
				),

				array(
					'name'             => __('Contact form in advert (subject)', 'terraclassifieds'),
					'id'               => '_tc_email_template_contact_form_subject',
					'type'             => 'text',
					'default' => __('Your advertisement enquiry', 'terraclassifieds'),
				),

				array(
					'name'             => __('Contact form in advert (message)', 'terraclassifieds'),
					'id'               => '_tc_email_template_contact_form_message',
					'type'             => 'textarea_code',
					'default' => __('
	<p>Hello [[user_name]],</p>
	<p>Message from:</p>
	<p>User name: [[contact_author_name]]</p>
	<p>User email: [[contact_author_email]]</p>
	<p>Your advertisement [[advert_title_link]] enquiry:</p>
	<p></p>
	[[contact_message]]', 'terraclassifieds'),
				),

				array(
					'name' => __('Available tags', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_email_template_contact_form_af',
					'desc' => __('[[user_name]] - Advert author login<br />[[contact_author_name]] - Author name<br />[[contact_author_email]] - Author email<br />[[advert_title_link]] - Linked advert title (shortcode is available only if option <strong>Ad status</strong> is set to <strong>Publish</strong> on cart <strong>Adding advert view</strong>)<br />[[contact_message]] - Author message', 'terraclassifieds'),
				),

				// Abuse Form in advert view
				array(
					'name' => __('Abuse form - user notification', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_email_template_abuse_form_user_title',
				),

				array(
					'name'             => __('Abuse form in theadvert (subject)', 'terraclassifieds'),
					'id'               => '_tc_email_template_abuse_form_subject',
					'type'             => 'text',
					'default' => __('Abuse Report', 'terraclassifieds'),
				),

				array(
					'name'             => __('Abuse form in the advert (message)', 'terraclassifieds'),
					'id'               => '_tc_email_template_abuse_form_message',
					'type'             => 'textarea_code',
					'default' => __('
	<p>Hello,</p>
	<p> </p>
	<p>Message from:</p>
	<p>User name: [[abuse_author_name]]</p>
	<p>User email: [[abuse_author_email]]</p>
	<p></p>
	<p>Advert: [[advert_title_link]]</p>
	<p></p>
	<p>Abuse reason:<br />
	[[abuse_message]]</p>', 'terraclassifieds'),
				),

				array(
					'name' => __('Available tags', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_email_template_abuse_form_af',
					'desc' => __('[[user_name]] - Advert author login<br />[[contact_author_name]] - Author name<br />[[contact_author_email]] - Author email<br />[[advert_title_link]] - Linked advert title (shortcode is available only if option <strong>Ad status</strong> is set to <strong>Publish</strong> on cart <strong>Adding advert view</strong>)<br />[[contact_message]] - Author message', 'terraclassifieds'),
				),

				// Change status of advert
				array(
					'name' => __('Change status - user notification', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_email_template_change_status_user_title',
				),

				array(
					'name'             => __('Change status (subject)', 'terraclassifieds'),
					'id'               => '_tc_email_template_change_status_subject',
					'type'             => 'text',
					'default' => __('Advert status change', 'terraclassifieds'),
				),

				array(
					'name'             => __('Change status (message)', 'terraclassifieds'),
					'id'               => '_tc_email_template_change_status_message',
					'type'             => 'textarea_code',
					'default' => __('
	<p>Hello,</p>
	<p> </p>
	<p>Status change of your advert [[advert_title_link]].</p>
	<p> </p>
	<p>New status [[advert_status]].</p>
	<p> </p>', 'terraclassifieds'),
				),

				array(
					'name' => __('Available tags', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_email_template_change_status_af',
					'desc' => __('[[advert_title_link]] - Linked advert title (shortcode is available only if option <strong>Ad status</strong> is set to <strong>Publish</strong> on cart <strong>Adding advert view</strong>)<br />[[advert_status]] - Advert status', 'terraclassifieds'),
				),

				// Advert - expiration notification
				array(
					'name' => __('Expiration - user notification', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_email_template_expiration_user_title',
				),

				array(
					'name'             => __('Advert - expiration notification (subject)', 'terraclassifieds'),
					'id'               => '_tc_email_template_expiration_notification_subject',
					'type'             => 'text',
					'default' => __('Advert expire notification', 'terraclassifieds'),
				),

				array(
					'name'             => __('Advert - expiration notification (message)', 'terraclassifieds'),
					'id'               => '_tc_email_template_expiration_notification_message',
					'type'             => 'textarea_code',
					'default' => __('
	<p>Hello,</p>
	<p> </p>
	<p>Your advert [[advert_title_link]] will expire in [[advert_expire_days]] days.</p>
	<p> </p>
	<p>You can renew it on your adverts list.</p>
	<p> </p>', 'terraclassifieds'),
				),

				array(
					'name' => __('Available tags', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_email_template_expiration_notification_af',
					'desc' => __('[[advert_title_link]] - Linked advert title (shortcode is available only if option <strong>Ad status</strong> is set to <strong>Publish</strong> on cart <strong>Adding advert view</strong>)<br />[[advert_expire_days]] - Number of days to advert expiration', 'terraclassifieds'),
				),

				array(
					'name'             => __('E-mail notifications for administrators', 'terraclassifieds'),
					'id'               => '_tc_email_notifications_administrators',
					'type'             => 'textarea_code',
					'desc' => __('By default, all notifications are sent to Administration Email Address from General Settings.<br />You may add additional recipients separating addresses with comma. The message is sent as CC.', 'terraclassifieds'),
				),
			)
		);

		$tabs_setting['tabs'][] = array(
			'id'     => 'tc_seo',
			'title'  => __('SEO', 'terraclassifieds'),
			'fields' => array(

				array(
					'name' => __('Pages', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_image_seo_pages_title',
					'desc' => __('TerraClassifieds will use the default built-in pages unless you change them. If you change the default page for any of the following views, then remember to place a related shortcode in the content.', 'terraclassifieds'),
				),

				array(
					'name'        => __('Page for "Add advert"', 'terraclassifieds'),
					'id'          => '_tc_add_advert_page_id',
					'type'        => 'post_search_text',
					'post_type'   => array('page'),
					'select_type' => 'radio',
					'select_behavior' => 'replace',
					'default' => $page_add_advert_default_id,
					'desc' => __('Shortcode for this page: <strong>[terraclassifieds_add_item]</strong>', 'terraclassifieds') . $page_add_advert_more
				),

				array(
					'name'        => __('Page for "Edit advert"', 'terraclassifieds'),
					'id'          => '_tc_edit_advert_page_id',
					'type'        => 'post_search_text',
					'post_type'   => array('page'),
					'select_type' => 'radio',
					'select_behavior' => 'replace',
					'default' => $page_edit_advert_default_id,
					'desc' => __('Shortcode for this page: <strong>[terraclassifieds_edit_item]</strong>', 'terraclassifieds') . $page_edit_advert_more,
				),

				array(
					'name'        => __('Page for "Edit profile"', 'terraclassifieds'),
					'id'          => '_tc_edit_profile_page_id',
					'type'        => 'post_search_text',
					'post_type'   => array('page'),
					'select_type' => 'radio',
					'select_behavior' => 'replace',
					'default' => $page_edit_profile_default_id,
					'desc' => __('Shortcode for this page: <strong>[terraclassifieds_edit_profile]</strong>', 'terraclassifieds') . $page_edit_profile_more,
				),

				array(
					'name'        => __('Page for "Favourite ads"', 'terraclassifieds'),
					'id'          => '_tc_favourite_ads_page_id',
					'type'        => 'post_search_text',
					'post_type'   => array('page'),
					'select_type' => 'radio',
					'select_behavior' => 'replace',
					'default' => $page_favourite_ads_default_id,
					'desc' => __('Shortcode for this page: <strong>[terraclassifieds_favourite_ads]</strong>', 'terraclassifieds') . $page_favourite_ads_more,
				),

				array(
					'name'        => __('Page for "Forgot password"', 'terraclassifieds'),
					'id'          => '_tc_forgot_password_page_id',
					'type'        => 'post_search_text',
					'post_type'   => array('page'),
					'select_type' => 'radio',
					'select_behavior' => 'replace',
					'default' => $page_forgot_password_default_id,
					'desc' => __('Shortcode for this page: <strong>[terraclassifieds_forgot_password]</strong>', 'terraclassifieds') . $page_forgot_password_more,
				),

				array(
					'name'        => __('Page for "Login"', 'terraclassifieds'),
					'id'          => '_tc_login_page_id',
					'type'        => 'post_search_text',
					'post_type'   => array('page'),
					'select_type' => 'radio',
					'select_behavior' => 'replace',
					'default' => $page_login_default_id,
					'desc' => __('Shortcode for this page: <strong>[terraclassifieds_login]</strong>', 'terraclassifieds') . $page_login_more,
				),

				array(
					'name'        => __('Page for "Registration"', 'terraclassifieds'),
					'id'          => '_tc_registration_page_id',
					'type'        => 'post_search_text',
					'post_type'   => array('page'),
					'select_type' => 'radio',
					'select_behavior' => 'replace',
					'default' => $page_registration_default_id,
					'desc' => __('Shortcode for this page: <strong>[terraclassifieds_registration]</strong>', 'terraclassifieds') . $page_registration_more,
				),

				array(
					'name'        => __('Page for "My submissions"', 'terraclassifieds'),
					'id'          => '_tc_my_submissions_page_id',
					'type'        => 'post_search_text',
					'post_type'   => array('page'),
					'select_type' => 'radio',
					'select_behavior' => 'replace',
					'default' => $page_my_submissions_default_id,
					'desc' => __('Shortcode for this page: <strong>[terraclassifieds_my_submissions]</strong>', 'terraclassifieds') . $page_my_submissions_more,
				),

				array(
					'name' => __('Slugs', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_image_seo_slugs_title',
					'desc' => __('TerraClassifieds will use the default slugs for pages. Remember that slugs need to be unique.<br />NOTE: You need to select <strong>Post name</strong> in Settings -> Permalinks.', 'terraclassifieds'),
				),

				array(
					'name'     => __('Slug for "Add advert"', 'terraclassifieds'),
					'id'      => '_tc_slug_add_advert',
					'type'    => 'text',
					'default' => 'add-advert',
					'attributes'  => array(
						'required'    => 'required',
					),
				),

				array(
					'name'     => __('Slug for "Edit advert"', 'terraclassifieds'),
					'id'      => '_tc_slug_edit_advert',
					'type'    => 'text',
					'default' => 'edit-ad',
					'attributes'  => array(
						'required'    => 'required',
					),
				),

				array(
					'name'     => __('Slug for "Edit profile"', 'terraclassifieds'),
					'id'      => '_tc_slug_edit_profile',
					'type'    => 'text',
					'default' => 'edit-profile',
					'attributes'  => array(
						'required'    => 'required',
					),
				),

				array(
					'name'     => __('Slug for "Favourite ads"', 'terraclassifieds'),
					'id'      => '_tc_slug_favourite_ads',
					'type'    => 'text',
					'default' => 'favourite-ads',
					'attributes'  => array(
						'required'    => 'required',
					),
				),

				array(
					'name'     => __('Slug for "Forgot Password"', 'terraclassifieds'),
					'id'      => '_tc_slug_forgot_password',
					'type'    => 'text',
					'default' => 'forgot-password',
					'attributes'  => array(
						'required'    => 'required',
					),
				),

				array(
					'name'     => __('Slug for "Login"', 'terraclassifieds'),
					'id'      => '_tc_slug_login',
					'type'    => 'text',
					'default' => 'login',
					'attributes'  => array(
						'required'    => 'required',
					),
				),

				array(
					'name'     => __('Slug for "Registration"', 'terraclassifieds'),
					'id'      => '_tc_slug_registration',
					'type'    => 'text',
					'default' => 'registration',
					'attributes'  => array(
						'required'    => 'required',
					),
				),

				array(
					'name'     => __('Slug for "My submissions"', 'terraclassifieds'),
					'id'      => '_tc_slug_my_submissions',
					'type'    => 'text',
					'default' => 'my-submissions',
					'attributes'  => array(
						'required'    => 'required',
					),
				),

				array(
					'name'    => __('Slug for archive', 'terraclassifieds'),
					'id'      => '_tc_slug_archive',
					'type'    => 'text',
					'default' => 'classifieds',
				),

				array(
					'name'     => __('Slug for single classified', 'terraclassifieds'),
					'id'      => '_tc_slug_single',
					'type'    => 'text',
					'default' => 'classified',
				),

				array(
					'name' => __('Other settings', 'terraclassifieds'),
					'type' => 'title',
					'id'   => '_tc_image_seo_other_settings_title'
				),

				array(
					'name'    => __('Meta description text limit', 'terraclassifieds'),
					'id'      => '_tc_meta_description_text_limit',
					'type'    => 'text',
					'default' => '158',
					'desc' => __('Meta description content is taken by default from the ad\'s description.', 'terraclassifieds'),
				),

				array(
					'name'             => __('Archived ad', 'terraclassifieds'),
					'id'               => '_tc_seo_expired_ad',
					'type'             => 'select',
					'show_option_none' => false,
					'options'          => array(
						'0' => __('404 page', 'terraclassifieds'),
						'1' => __('redirect to URL', 'terraclassifieds'),
						'2' => __('deactivate the page', 'terraclassifieds'),
						'3' => __('rediret to the parent category ads list', 'terraclassifieds'),
					),
					'default' => '2',
					'desc' => __('Select the action once somebody clicks on an expired (archived) ad.', 'terraclassifieds'),
				),

				array(
					'name'    => __('Archived ad - redirect URL', 'terraclassifieds'),
					'id'      => '_tc_seo_expired_ad_redirect_url',
					'type'    => 'text_url',
					'default' => 'https://pixelemu.com',
				),

			)
		);

		$tabs_setting['tabs'][] = array(
			'id'     => 'tc_documentation',
			'title'  => __('Documentation', 'terraclassifieds'),
			'fields' => array(

				array(
					'name' => '',
					'type' => 'title',
					'id'   => '_tc_documentation_1',
					'desc' => '<a href="http://terraclassifieds.pixelemu.com/" target="_blank">Documentation and tutorials</a>',
				),

				array(
					'name' => '',
					'type' => 'title',
					'id'   => '_tc_documentation_2',
					'desc' => '<a href="https://www.pixelemu.com/my-account/submit-ticket" target="_blank">Report a bug & get help or have an idea?</a>',
				),

				array(
					'name' => '',
					'type' => 'title',
					'id'   => '_tc_documentation_3',
					'desc' => '<a href="https://www.pixelemu.com/changelogs/wordpress-plugins/terraclassifieds" target="_blank">TerraClassifieds changelog</a>',
				),

				array(
					'name' => '',
					'type' => 'title',
					'id'   => '_tc_documentation_4',
					'desc' => '<a href="https://www.pixelemu.com/wordpress-plugins/i/245-terraclassifieds" target="_blank">Download TerraClassifieds</a>',
				),

				array(
					'name' => '',
					'type' => 'title',
					'id'   => '_tc_documentation_4',
					'desc' => '<a href="https://www.pixelemu.com/wordpress-themes/i/244-terraclassic" target="_blank">Download Free theme for TerraClassifieds</a>',
				),

			)
		);

		$cmb->add_field(array(
			'id'   => '__tabs',
			'type' => 'tabs',
			'tabs' => $tabs_setting
		));
	}
}
