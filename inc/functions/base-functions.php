<?php

// get CM2 options
if (!function_exists('terraclassifieds_get_option_base_functions')) {
	function terraclassifieds_get_option_base_functions($key = '', $default = false)
	{
		if (function_exists('cmb2_get_option')) {
			// Use cmb2_get_option as it passes through some key filters.
			return cmb2_get_option('terraclassifieds_settings', $key, $default);
		}
		// Fallback to get_option if CMB2 is not loaded yet.
		$opts = get_option('terraclassifieds_settings', $default);
		$val = $default;
		if ('all' == $key) {
			$val = $opts;
		} elseif (is_array($opts) && array_key_exists($key, $opts) && false !== $opts[$key]) {
			$val = $opts[$key];
		}
		return $val;
	}
}

// custom images size
$tcf_image_archive_size_width = terraclassifieds_get_option_base_functions('_tc_image_archive_width', '170');
$tcf_image_archive_size_height = terraclassifieds_get_option_base_functions('_tc_image_archive_height', '145');
$tcf_image_single_size_width = terraclassifieds_get_option_base_functions('_tc_image_single_width', '980');
$tcf_image_single_size_height = terraclassifieds_get_option_base_functions('_tc_image_single_height', '735');
add_image_size('tcf-archive', $tcf_image_archive_size_width, $tcf_image_archive_size_height);
add_image_size('tcf-single', $tcf_image_single_size_width, $tcf_image_single_size_height);

// custom excerpt function
if (!function_exists('terraclassifieds_excerpt')) {
	function terraclassifieds_excerpt($len = 55, $trim = '&hellip;', $readmore = false, $ignore = false)
	{
		global $post;

		if (strpos($post->post_content, '<!--more-->') && $len > 0 && $ignore == false) { //if more quicktag in post
			if ($readmore === true) {
				echo '<!-- with readmore -->';
				the_content(); //with readmore
			} else {
				echo '<!-- no readmore -->';
				the_content(''); //no readmore
			}
		} elseif ($len == 'full') {
			echo '<!-- full content -->';
			the_content('');
		} elseif ($len == 0) {
			echo '<!-- excerpt 0 -->';
		} else { //prepare excerpt depends on limit
			echo '<!-- excerpt ' . esc_attr($len) . ' -->';
			remove_filter('the_content', 'wpautop');
			$excerpt = preg_replace('/\s+/', ' ', get_the_content()); // remove whitespaces
			$limit     = $len + 1;
			$excerpt   = explode(' ', strip_tags($excerpt), $limit);
			$num_words = count($excerpt);
			$trim      = ' ' . $trim;

			if ($num_words >= $len) {
				$last_item = array_pop($excerpt);
			} else {
				$trim = '';
			}

			$excerpt = implode(' ', $excerpt);

			$excerpt = rtrim($excerpt, ',');
			$excerpt = rtrim($excerpt, '.');

			$excerpt .= $trim;

			if ($readmore === true) { //with readmore
				$excerpt .= pe_read_more_link();
			}
			echo apply_filters('the_content', $excerpt);
		}
	}
}

// Equipment Category Dropdown
class Walker_SlugValueCategoryDropdown extends Walker_CategoryDropdown
{
	function start_el(&$output, $category, $depth = 0, $args = array(), $id = 0)
	{
		$pad = str_repeat('-', $depth * 1);

		$cat_name = apply_filters('list_cats', $category->name, $category);
		$output .= "\t<option class=\"level-$depth\" value=\"" . $category->slug . "\"";
		if ($category->slug == $args['selected'])
			$output .= ' selected="selected"';
		$output .= '>';
		$output .= $pad . ' ' . $cat_name;
		if ($args['show_count'])
			$output .= '&nbsp;&nbsp;(' . $category->count . ')';
		$output .= "</option>\n";
	}
}

// Equipment Location Dropdown
class Walker_SlugValueLocationDropdown extends Walker_CategoryDropdown
{
	function start_el(&$output, $category, $depth = 0, $args = array(), $id = 0)
	{
		$pad = str_repeat('-', $depth * 1);

		$cat_name = apply_filters('list_cats', $category->name, $category);
		$output .= "\t<option class=\"level-$depth\" value=\"" . $category->slug . "\"";
		if ($category->slug == $args['selected'])
			$output .= ' selected="selected"';
		$output .= '>';
		$output .= $pad . ' ' . $cat_name;
		if ($args['show_count'])
			$output .= '&nbsp;&nbsp;(' . $category->count . ')';
		$output .= "</option>\n";
	}
}

//prepare search form
if (!function_exists('terraclassifiedsSearchForm')) {
	function terraclassifiedsSearchForm($instance)
	{

		$category_filter = (!empty($instance['category_filter']) && $instance['category_filter'] == 1) ? true : false;
		$only_first_level = (!empty($instance['only_first_level']) && $instance['only_first_level'] == 1) ? true : false;
		if ($only_first_level) {
			$depth = 1;
		} else {
			$depth = 0;
		}

		$use_locations = terraclassifieds_get_option('_tc_use_locations', 1);
		$location = (!empty($instance['location']) && $instance['location'] == 1) ? true : false;

		$search_word_get = '';
		if (isset($_GET['s'])) {
			$search_word_get = $_GET['s'];
		}

		$location_option = (!empty($instance['location_option']) && $instance['location_option'] == 1) ? true : false;
		$location_list_hierarchical = (!empty($instance['location_list_hierarchical']) && $instance['location_list_hierarchical'] == 1) ? true : false;

		$type_show = terraclassifieds_get_option_base_functions('_tc_use_types', '0');
		$type = (!empty($instance['type']) && $instance['type'] == 1) ? true : false;

		$price_filter_show = (!empty($instance['price']) && $instance['price'] == 1) ? true : false;

		$more_enabled = '';
		if (($type && $type_show) || $price_filter_show) {
			$more_enabled = 'terraclassifieds-more-fields';
		}

		$search_size1 = esc_attr($instance['search_large_size']);
		$search_size2 = esc_attr($instance['search_desktop_size']);
		$search_size3 = esc_attr($instance['search_tablet_size']);
		$search_size4 = esc_attr($instance['search_phone_size']);
		$location_size1 = esc_attr($instance['location_large_size']);
		$location_size2 = esc_attr($instance['location_desktop_size']);
		$location_size3 = esc_attr($instance['location_tablet_size']);
		$location_size4 = esc_attr($instance['location_phone_size']);
		$category_size1 = esc_attr($instance['category_large_size']);
		$category_size2 = esc_attr($instance['category_desktop_size']);
		$category_size3 = esc_attr($instance['category_tablet_size']);
		$category_size4 = esc_attr($instance['category_phone_size']);
		$price_size1 = esc_attr($instance['price_large_size']);
		$price_size2 = esc_attr($instance['price_desktop_size']);
		$price_size3 = esc_attr($instance['price_tablet_size']);
		$price_size4 = esc_attr($instance['price_phone_size']);

		$form = '<form action="' . esc_url(home_url('/')) . '" id="terraclassifieds-search-form" method="get" class="terraclassifieds-search-form ' . $more_enabled . '">
			<div class="terraclassifieds-search-fields">
			<div class="row">
			<div class="terraclassifieds-search-word col-lg-' . $search_size1 . ' col-md-' . $search_size2 . ' col-sm-' . $search_size3 . ' col-xs-' . $search_size4 . '">
			<label class="screen-reader-text" for="terraclassifieds-product-search-field">' . __("Search for:", "terraclassifieds") . '</label>
			<input type="search" id="terraclassifieds-product-search-field" class="terraclassifieds-product-search-field" placeholder="' . esc_attr__("Search products&hellip;", "terraclassifieds") . '" value="' . $search_word_get . '" name="s" />
			</div>';

		if ($category_filter) {

			$category_get = '';
			if (isset($_GET['ad_category'])) {
				$category_get = $_GET['ad_category'];
			}

			$dropdown_args = array(
				'taxonomy'      => 'ad_category',
				'id'                 => 'ad_category',
				'name'                 => 'ad_category',
				'depth'              => $depth,
				'show_option_none'  => esc_html__('All categories', 'terraclassifieds'),
				'option_none_value' => 0,
				'show_count'        => 1,
				'orderby'       => 'name',
				'hierarchical'      => true,
				'echo'          => 0,
				'walker'            => new Walker_SlugValueCategoryDropdown,
				'value_field' => 'slug',
				'selected' => $category_get,
			);

			$form .= '<div class="terraclassifieds-search-category col-lg-' . $category_size1 . ' col-md-' . $category_size2 . ' col-sm-' . $category_size3 . ' col-xs-' . $category_size4 . '">' . wp_dropdown_categories($dropdown_args) . '</div>';
		}

		if ($location && $use_locations) {

			$form .= '<div class="terraclassifieds-search-location col-lg-' . $location_size1 . ' col-md-' . $location_size2 . ' col-sm-' . $location_size3 . ' col-xs-' . $location_size4 . '">';

			if ($location_option) {
				$location_get = '';
				if (isset($_GET['ad_location'])) {
					$location_get = $_GET['ad_location'];
				}
				$form .= wp_nonce_field('terraclassifiedsLocationsAjax', 'location_nonce');
				$form .=  '<input type="text" id="terraclassifieds-product-search-location-field" class="terraclassifieds-product-search-field location-tax-input ui-autocomplete-input" data-taxonomy="ad_location" placeholder="' . esc_attr__("Enter location&hellip;", "terraclassifieds") . '" value="' . $location_get . '" name="ad_location" /><span class="terraclassifieds-location-more-characters">' . esc_attr__("Please enter minimum 3 characters.", "terraclassifieds") . '</span><span class="terraclassifieds-location-no-location">' . esc_attr__("Can't find the location.", "terraclassifieds") . '</span>';
			} else {

				$location_get = '';
				if (isset($_GET['ad_location'])) {
					$location_get = $_GET['ad_location'];
				}

				$location_dropdown_args = array(
					'taxonomy'      => 'ad_location',
					'id'                 => 'ad_location',
					'name'                 => 'ad_location',
					'show_option_none'  => esc_html__('All locations', 'terraclassifieds'),
					'option_none_value' => 0,
					'show_count'        => 1,
					'orderby'       => 'name',
					'hierarchical'      => $location_list_hierarchical,
					'echo'          => 0,
					'walker'            => new Walker_SlugValueLocationDropdown,
					'value_field' => 'slug',
					'selected' => $location_get,
				);

				$form .= wp_dropdown_categories($location_dropdown_args);
			}
			$form .= '</div>';
		}
		$form .= '</div>';

		if (($type && $type_show) || $price_filter_show) {

			$form .= '</div>';

			$form .= '<div class="terraclassifieds-search-more-wrapper clearfix">';

			$form .= '<span class="terraclassifieds-more">' . esc_attr_x("More", "search more", "terraclassifieds") . '</span>';

			$form .= '<div class="terraclassifieds-search-type">';

			if ($type && $type_show) {
				$type_get = '';
				if (isset($_GET['ad_type'])) {
					$type_get = $_GET['ad_type'];
				}

				$array_excluded_terms = array();
				$terraclassifieds_types = get_terms('ad_type', array(
					'hide_empty' => true,
				));
				foreach ($terraclassifieds_types as $terraclassifieds_type) {
					$type_use_in_search = get_term_meta($terraclassifieds_type->term_id, '_tc_type_use_in_search', true);
					if ($type_use_in_search != 'yes') {
						array_push($array_excluded_terms, $terraclassifieds_type->term_id);
					}
				}

				$type_dropdown_args = array(
					'taxonomy'      => 'ad_type',
					'id'                 => 'ad_type',
					'name'                 => 'ad_type',
					'show_option_none'  => false,
					'option_none_value' => 0,
					'show_count'        => 0,
					'orderby'       => 'name',
					'hierarchical'      => true,
					'echo'          => 0,
					'value_field' => 'slug',
					'selected' => $type_get,
					'exclude'            => $array_excluded_terms,
				);

				$form .= wp_dropdown_categories($type_dropdown_args);
			}

			// selling type - BEGIN
			$selling_type_filter = terraclassifieds_get_option_base_functions('_tc_archive_search_use_selling_type_filter', '0');
			if ($selling_type_filter) {
				$sellingTypes = terraclassifieds_get_option('_tc_selling_types', array('price', 'for-free', 'exchange', 'nothing'));
				if (in_array('price', $sellingTypes) || in_array('for-free', $sellingTypes) || in_array('exchange', $sellingTypes)) {
					$form .= '<div class="terraclassifieds-search-filter-selling-type">';
					$form .= '<label for="sell_type">' . __('Selling Type:', 'terraclassifieds') . '</label>';
					$form .= '<select name="sell_type" id="sell_type">';
					$form .= '<option value="0">' . __('Select', 'terraclassifieds') . '</option>';

					if (in_array('price', $sellingTypes)) {
						$form .= '<option value="price">' . __('Price', 'terraclassifieds') . '</option>';
					}

					if (in_array('for-free', $sellingTypes)) {
						$form .= '<option value="for_free">' . __('For free', 'terraclassifieds') . '</option>';
					}

					if (in_array('exchange', $sellingTypes)) {
						$form .= '<option value="exchange">' . __('Exchange', 'terraclassifieds') . '</option>';
					}

					$form .= '</select>';
					$form .= '</div>';
				}
			}


			// selling type - END


			// price filter - BEGIN
			if ($price_filter_show) {
				$price_min = '';
				if (isset($_GET['price-min'])) {
					$price_min = $_GET['price-min'];
				}
				$price_max = '';
				if (isset($_GET['price-max'])) {
					$price_max = $_GET['price-max'];
				}

				$form .= '<div class="terraclassifieds-search-filter-price">';
				$form .= '<div class="row">';

				$form .= '<label class="terraclassifieds-search-filter-price-label col-lg-12 col-md-12 col-sm-12 col-xs-12">' . esc_html_x("Price", "search price", "terraclassifieds") . '</label>';
				$form .= '<div class="terraclassifieds-search-filter-price-field col-lg-' . $price_size1 . ' col-md-' . $price_size2 . ' col-sm-' . $price_size3 . ' col-xs-' . $price_size4 . '">';
				$form .= '<input type="number" placeholder="' . esc_html_x("From", "search price", "terraclassifieds") . '" name="price-min" min="0" value="' . $price_min . '">';
				$form .= '</div>';

				$form .= '<div class="terraclassifieds-search-filter-price-field col-lg-' . $price_size1 . ' col-md-' . $price_size2 . ' col-sm-' . $price_size3 . ' col-xs-' . $price_size4 . '">';
				$form .= '<input type="number" placeholder="' . esc_html_x("To", "search price", "terraclassifieds") . '" name="price-max" min="0" value="' . $price_max . '">';
				$form .= '</div>';

				$form .= '</div>';
				$form .= '</div>';
			}
			// price filter - END

			$form .= '</div>';

			$form .= '</div>';
		}

		if (!(($type && $type_show) || $price_filter_show)) {
			$form .= '</div>';
		}


		if (($type && $type_show) || $price_filter_show) {
			$form .= '<div class="terraclassifieds-clear-area">
				<div class="terraclassifieds-clear-button">';
			$form .= '<i class="fa fa-close"></i>' . esc_attr_x("Clear", "search more", "terraclassifieds");
			$form .= '</div>
			</div>';
		}

		$form .= '<button type="submit" class="terraclassifieds-btn" value="' . esc_attr_x("Search", "submit button", "terraclassifieds") . '">' . esc_html_x("Search", "submit button", "terraclassifieds") . '</button>';

		$form .= '<input type="hidden" name="post_type" value="classified" />
		</form>';
		return $form;
	}
}

// custom search form on search results page and category view
if (!function_exists('terraclassifiedsSearchFormInit')) {
	function terraclassifiedsSearchFormInit($form = false)
	{
		$search_form_show = terraclassifieds_get_option_base_functions('_tc_archive_search', '1');
		$category_show = terraclassifieds_get_option_base_functions('_tc_archive_search_category_show', '1');
		$category_only_first_level = terraclassifieds_get_option_base_functions('_tc_archive_search_category_only_first_level', '0');
		$location_show = terraclassifieds_get_option_base_functions('_tc_archive_search_location_show', '1');
		$type_show = terraclassifieds_get_option_base_functions('_tc_use_types', '0');
		$type_filter = terraclassifieds_get_option_base_functions('_tc_archive_search_use_type_filter', '0');
		$price_filter_show = terraclassifieds_get_option_base_functions('_tc_archive_search_use_price_filter', '0');
		$location_kind = terraclassifieds_get_option_base_functions('_tc_archive_search_location_kind', '0');
		$location_list_hierarchical = terraclassifieds_get_option_base_functions('_tc_archive_search_location_hierarchical', '1');
		$search_size1 = terraclassifieds_get_option_base_functions('_tc_search_size1', '4');
		$search_size2 = terraclassifieds_get_option_base_functions('_tc_search_size2', '4');
		$search_size3 = terraclassifieds_get_option_base_functions('_tc_search_size3', '12');
		$search_size4 = terraclassifieds_get_option_base_functions('_tc_search_size4', '12');
		$location_size1 = terraclassifieds_get_option_base_functions('_tc_location_size1', '4');
		$location_size2 = terraclassifieds_get_option_base_functions('_tc_location_size2', '4');
		$location_size3 = terraclassifieds_get_option_base_functions('_tc_location_size3', '12');
		$location_size4 = terraclassifieds_get_option_base_functions('_tc_location_size4', '12');
		$category_size1 = terraclassifieds_get_option_base_functions('_tc_category_size1', '4');
		$category_size2 = terraclassifieds_get_option_base_functions('_tc_category_size2', '4');
		$category_size3 = terraclassifieds_get_option_base_functions('_tc_category_size3', '12');
		$category_size4 = terraclassifieds_get_option_base_functions('_tc_category_size4', '12');
		$price_size1 = terraclassifieds_get_option_base_functions('_tc_price_size1', '6');
		$price_size2 = terraclassifieds_get_option_base_functions('_tc_price_size2', '6');
		$price_size3 = terraclassifieds_get_option_base_functions('_tc_price_size3', '6');
		$price_size4 = terraclassifieds_get_option_base_functions('_tc_price_size4', '12');
		$type_search = 0;
		if ($type_show && $type_filter) {
			$type_search = 1;
		}
		$instance = array(
			'category_filter' => $category_show,
			'only_first_level' => $category_only_first_level,
			'location' => $location_show,
			'location_option' => $location_kind,
			'location_list_hierarchical' => $location_list_hierarchical,
			'type' => $type_search,
			'price' => $price_filter_show,
			'search_large_size'    => $search_size1,
			'search_desktop_size'    => $search_size2,
			'search_tablet_size'    => $search_size3,
			'search_phone_size'    => $search_size4,
			'location_large_size'    => $location_size1,
			'location_desktop_size'    => $location_size2,
			'location_tablet_size'    => $location_size3,
			'location_phone_size'    => $location_size4,
			'category_large_size'    => $category_size1,
			'category_desktop_size'    => $category_size2,
			'category_tablet_size'    => $category_size3,
			'category_phone_size'    => $category_size4,
			'price_large_size'    => $price_size1,
			'price_desktop_size'    => $price_size2,
			'price_tablet_size'    => $price_size3,
			'price_phone_size'    => $price_size4,
		);
		if ($search_form_show) {
			return terraclassifiedsSearchForm($instance);
		} else {
			return false;
		}
	}
}

// add adverts to global search
if (!is_admin()) {
	add_filter('pre_get_posts', 'terraclassifieds_search_widget', 9999);
	function terraclassifieds_search_widget($query)
	{
		if (is_search() && $query->is_search && $query->is_main_query()) {
			$current = (array) get_query_var('post_type', 'post');
			$cpt = array('classified');
			$query->set('post_type', array_merge($current, $cpt));
		}
		return $query;
	}
}


// add custom post type "classified" to search in ad search results view
if (!is_admin() && !function_exists('terraclassifiedsSearch')) {
	add_filter('pre_get_posts', 'terraclassifiedsSearch', 9999);
	function terraclassifiedsSearch($query)
	{
		if (isset($_GET['post_type']) && $_GET['post_type'] == 'classified') {
			if (is_search() && $query->is_search && $query->is_main_query()) {
				$query->set('post_type', array('classified'));
			}
			return $query;
		}
	}
}

// add price range filter
if (!function_exists('terraclassifiedsSearchFilterPrice')) {
	add_action('pre_get_posts', 'terraclassifiedsSearchFilterPrice', 9999);
	function terraclassifiedsSearchFilterPrice($query)
	{
		if (is_search() && $query->is_search() && $query->is_main_query() && (!empty($_GET['price-min']) || !empty($_GET['price-max']))) {

			//Collect user input from $_GET
			$user_input_min_value = $_GET['price-min'];
			$user_input_max_value = $_GET['price-max'];

			//$meta_query = [];
			//$meta_query = $query->get('meta_query');

			if ($_GET['price-min'] && $_GET['price-max']) {
				$meta_query = array(
					array(
						'key' => '_tc_price',
						'value' => array($user_input_min_value, $user_input_max_value),
						'type' => 'NUMERIC',
						'compare' => 'BETWEEN'
					)
				);
			} else if ($_GET['price-min']) {
				$meta_query = array(
					array(
						'key' => '_tc_price',
						'value' => $user_input_min_value,
						'type' => 'NUMERIC',
						'compare' => '>='
					)
				);
			} else if ($_GET['price-max']) {
				$meta_query = array(
					array(
						'key' => '_tc_price',
						'value' => $user_input_max_value,
						'type' => 'NUMERIC',
						'compare' => '<='
					)
				);
			}

			$query->set('meta_query', $meta_query);
			//var_dump($query);

		}
	}
}

// add selling type filter
if (!function_exists('terraclassifiedsSearchFilterSellingType')) {
	add_action('pre_get_posts', 'terraclassifiedsSearchFilterSellingType', 9999);
	function terraclassifiedsSearchFilterSellingType($query)
	{
		if (is_search() && $query->is_search() && $query->is_main_query() && !empty($_GET['sell_type'])) {

			//Collect user input from $_GET
			$user_input_selling_type = $_GET['sell_type'];

			if ($_GET['sell_type'] && $_GET['sell_type'] != '0') {
				$meta_query = array(
					array(
						'key' => '_tc_sell_type',
						'value' => $user_input_selling_type,
						'type' => 'CHAR',
						'compare' => '=',
					)
				);
				$query->set('meta_query', $meta_query);
			}
		}
	}
}

// add Custom Post Type "classified" to author archive view
if (!function_exists('terraclassifiedsAuthorArchive')) {
	add_action('pre_get_posts', 'terraclassifiedsAuthorArchive');
	function terraclassifiedsAuthorArchive($query)
	{
		if (is_author()) {
			$query->set('post_type', array('classified', 'post'));
		}
		remove_action('pre_get_posts', 'terraclassifiedsAuthorArchive');
	}
}

if (!function_exists('terraclassifieds_date_ago')) {
	function terraclassifieds_date_ago($time)
	{

		$date_order = terraclassifieds_get_option('_tc_date_order', 1);

		$period = array(
			__("second", 'terraclassifieds'),
			__("minute", 'terraclassifieds'),
			__("hour", 'terraclassifieds'),
			__("day", 'terraclassifieds'),
			__("week", 'terraclassifieds'),
			__("month", 'terraclassifieds'),
			__("year", 'terraclassifieds'),
			__("decade", 'terraclassifieds')
		);
		$periods = array(
			__("seconds", 'terraclassifieds'),
			__("minutes", 'terraclassifieds'),
			__("hours", 'terraclassifieds'),
			__("days", 'terraclassifieds'),
			__("weeks", 'terraclassifieds'),
			__("months", 'terraclassifieds'),
			__("years", 'terraclassifieds'),
			__("decades", 'terraclassifieds')
		);
		$lengths = array("60", "60", "24", "7", "4.35", "12", "10");
		$ago = __('ago', 'terraclassifieds');

		$now = current_time('U');
		$difference     = $now - $time;

		for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++) {
			$difference /= $lengths[$j];
		}

		$difference = round($difference);
		$period = ($difference == 1) ? $period[$j] : $periods[$j];

		if ($date_order == 1) {
			return "$difference $period $ago";
		} else if ($date_order == 2) {
			return "$period $difference $ago";
		} else if ($date_order == 3) {
			return "$ago $difference $period";
		} else if ($date_order == 4) {
			return "$difference $ago $period";
		} else if ($date_order == 5) {
			return "$period $ago $difference";
		} else if ($date_order == 6) {
			return "$ago $period $difference";
		}
	}
}

if (!function_exists('terraclassifieds_date_from_now')) {
	function terraclassifieds_date_from_now($time)
	{
		$period = array(
			__("second", 'terraclassifieds'),
			__("minute", 'terraclassifieds'),
			__("hour", 'terraclassifieds'),
			__("day", 'terraclassifieds'),
			__("week", 'terraclassifieds'),
			__("month", 'terraclassifieds'),
			__("year", 'terraclassifieds'),
			__("decade", 'terraclassifieds')
		);
		$periods = array(
			__("seconds", 'terraclassifieds'),
			__("minutes", 'terraclassifieds'),
			__("hours", 'terraclassifieds'),
			__("days", 'terraclassifieds'),
			__("weeks", 'terraclassifieds'),
			__("months", 'terraclassifieds'),
			__("years", 'terraclassifieds'),
			__("decades", 'terraclassifieds')
		);
		$lengths = array("60", "60", "24", "7", "4.35", "12", "10");
		$from_now = __('from now', 'terraclassifieds');

		$now = time();
		$difference     = $time - $now;

		for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++) {
			$difference /= $lengths[$j];
		}

		$difference = round($difference);
		$period = ($difference == 1) ? $period[$j] : $periods[$j];

		return "$difference $period $from_now";
	}
}

if (!function_exists('terraclassifieds_pagination')) {
	/**
	 * Modify default pagination
	 *
	 * @param integer $pages Number of pages in listing
	 */
	function terraclassifieds_pagination($pages = false)
	{
		global $paged;

		if (empty($paged)) {
			$paged = 1;
		}

		$prev      = $paged - 1;
		$next      = $paged + 1;
		$range     = 1; // only change it to show more links
		$showitems = 3;

		if ($pages === false) {
			global $wp_query;
			$pages = $wp_query->max_num_pages;
			if (!$pages) {
				$pages = 1;
			}
		}

		if ($pages != 1) {

			echo '<nav class="terraclassifieds-pagination"><ul class="terraclassifieds-pagination-list">';
			echo ($paged > 1) ? '<span class="prev"><a href="' . esc_url(get_pagenum_link($prev)) . '">' . esc_html__('Prev', 'terraclassifieds') . '</a></span>' : '';

			for ($i = 1; $i <= $pages; $i++) {
				echo ($paged == $i) ? '<span class="active"><span>' . esc_attr($i) . '</span></span> ' : '<span><a href="' . esc_url(get_pagenum_link($i)) . '">' . esc_attr($i) . '</a></span>';
			}
			echo ($paged < $pages) ? '<span class="next"><a href=' . esc_url(get_pagenum_link($next)) . '>' . esc_html__('Next', 'terraclassifieds') . '</a></span>' : '';
			echo '</ul></nav>';
		}
	}
}

///notices
if (!function_exists('terraclassifieds_notice_send_success')) {
	function terraclassifieds_notice_send_success()
	{
		terraclassifieds_display_notice("success", __('Message was sent successfully.', 'terraclassifieds'));
	}
}
if (!function_exists('terraclassifieds_notice_send_error')) {
	function terraclassifieds_notice_send_error()
	{
		terraclassifieds_display_notice("error", __('Message could not be sent.', 'terraclassifieds'));
	}
}
if (!function_exists('terraclassifieds_display_notice')) {
	function terraclassifieds_display_notice($status, $message)
	{
		echo '<div class="terraclassifieds-notice ' . sanitize_html_class($status) . '">' . $message . '</div>';
	}
}

if (!function_exists('terraclassifieds_save_ad')) {
	function terraclassifieds_save_ad()
	{

		if ('POST' == $_SERVER['REQUEST_METHOD'] && !empty($_POST['tc_form_action']) && $_POST['tc_form_action'] == 'add_item') {

			$taxonomy = array(
				'ad_category' => array(
					$_POST['cat']
				)
			);

			// Add the content of the form to $post as an array
			$post = array(
				'post_title'	=> sanitize_text_field($_POST['title']),
				'post_content'	=> sanitize_text_field($_POST['description']),
				'post_status'	=> 'publish',
				'post_author'   => get_current_user_id(),
				'post_type'	=> 'classified',
				'tax_input'    => $taxonomy,
			);

			$insert = wp_insert_post($post);
			if (is_wp_error($insert)) {
				echo $insert->get_error_message();
			} else {
				wp_redirect(home_url());
			}
		}
	}
	add_action('wp_loaded', 'terraclassifieds_save_ad');
}


// echo only first image
if (!function_exists('cmb2_output_file_list_first_image')) {
	function cmb2_output_file_list_first_image($file_list_meta_key, $img_size = 'medium')
	{
		$tcf_image_archive_size_height = terraclassifieds_get_option_base_functions('_tc_image_archive_height', '145');
		// Get the list of files
		$files = get_post_meta(get_the_ID(), $file_list_meta_key, 1);
		echo '<div class="terraclassifieds-gallery" style="min-height: ' . esc_attr($tcf_image_archive_size_height) . 'px;">';
		$files_counter = 0;
		// Loop through them and output an image
		foreach ((array) $files as $attachment_id => $attachment_url) {
			if ($files_counter < 1) {
				echo '<div class="terraclassifieds-gallery-element">';
				echo wp_get_attachment_image($attachment_id, $img_size);
				echo '</div>';
			}
			$files_counter++;
		}
		echo '</div>';
	}
}

// update ad status - to archive
if (!function_exists('terraclassifieds_to_archive')) {
	function terraclassifieds_to_archive($pid)
	{
		if (isset($_POST["submit-archive-$pid"])) {
			wp_update_post(array('ID' => $pid, 'post_status'   =>  'archived')); ?>
			<script>
				(function($) {
					$(document).ready(function() {
						$('.post-<?php echo esc_attr($pid); ?> .terraclassifieds-status span').closest('.type-classified').removeClass('status-publish');
						$('.post-<?php echo esc_attr($pid); ?> .terraclassifieds-status span').closest('.type-classified').addClass('status-archived');
						$('.post-<?php echo esc_attr($pid); ?> .terraclassifieds-status span').html("<?php echo __('Archived', 'terraclassifieds'); ?>");
						$('.post-<?php echo esc_attr($pid); ?> .terraclassifieds-status i').replaceWith('<i class="fa fa-dot-circle-o" aria-hidden="true"></i>');
					});
				})(jQuery);
			</script>
		<?php }
	}
}

// update ad status - to publish
if (!function_exists('terraclassifieds_to_publish')) {
	function terraclassifieds_to_publish($pid) {
		if (isset($_POST["submit-publish-$pid"])) {
			wp_update_post(array('ID' => $pid, 'post_status'   =>  'publish'));
			$expire_time_field = terraclassifieds_generate_ads_expired_time();
			update_post_meta($pid, '_tc_expire_date', $expire_time_field);
			update_post_meta(get_the_ID(), '_tc_expire_soon_notification_done', false);
			
			// refresh page after post updating
			if (isset($_POST['redirect-url'])) {
				header("Location: " . base64_decode($_POST['redirect-url']));
			} else if (isset($_SERVER['SCRIPT_URI'])) {
				header("Location: " . $_SERVER['SCRIPT_URI']);
			} else {
				header("refresh: 0;");
			}
			?>
			<script>
			(function($) {
				$(document).ready(function() {
					$('.post-<?php echo esc_attr($pid); ?> .terraclassifieds-status span').closest('.type-classified').removeClass('status-archived');
					$('.post-<?php echo esc_attr($pid); ?> .terraclassifieds-status span').closest('.type-classified').addClass('status-publish');
					$('.post-<?php echo esc_attr($pid); ?> .terraclassifieds-status span').html("<?php echo __('Published', 'terraclassifieds'); ?>");
					$('.post-<?php echo esc_attr($pid); ?> .terraclassifieds-status i').replaceWith('<i class="fa fa-check-circle" aria-hidden="true"></i>');
				});
			})(jQuery);
			</script>
	<?php }
	}
}

// Function to generate expired time for ads
if (!function_exists('terraclassifieds_generate_ads_expired_time')) {
	function terraclassifieds_generate_ads_expired_time() {
		$expire_time = intval(terraclassifieds_get_option('_tc_advert_expire_time', 30));
		$today = current_time('mysql');
		$expire_date = new DateTime($today);
		$expire_date->modify('+'.$expire_time.' day');
		$expire_time_field = $expire_date->format('U');
		
		return $expire_time_field;
	}
}

// update ad status - remove
if (!function_exists('terraclassifieds_remove')) {
	function terraclassifieds_remove($pid)
	{
		if (isset($_POST["submit-remove-$pid"])) {
			wp_delete_post($pid, true); ?>
			?>
			<script>
				(function($) {
					$(document).ready(function() {
						//$('.post-<?php echo esc_attr($pid); ?>').remove();
						location.reload(true);
					});
				})(jQuery);
			</script>
<?php }
	}
}

// add class if admin is logged in
if (!function_exists('terraclassifieds_body_class_admin')) {
	add_filter('body_class', 'terraclassifieds_body_class_admin');
	function terraclassifieds_body_class_admin($classes)
	{
		$admin_class = '';
		if (current_user_can('administrator')) {
			$admin_class = 'admin-on-board';
		}
		return array_merge($classes, array($admin_class));
	}
}

// breadcrumbs
if (!function_exists('terraclassifieds_breadcrumbs')) {
	function terraclassifieds_breadcrumbs($args = array())
	{
		global $post;
		$html = '<div class="terraclassifieds-breadcrumb">';
		$html .= '<span class="terraclassifieds-you-are-here">' . esc_html__('You are here', 'terraclassifieds') . ': </span>';
		$html .= '<span class="item-home"><a class="bread-link bread-home" href="' . get_home_url() . '"><span>' . esc_html__('Home', 'terraclassifieds') . '</span></a></span>';
		$html .= '<span class="terraclassifieds-breadcrumb-separator"> <i class="fa fa-angle-right" aria-hidden="true"></i> </span>';

		if (is_singular('classified')) {
			$last_category = wp_get_post_terms($post->ID, 'ad_category');
			if (!empty($last_category)) {
				$last_category_id = $last_category[0]->term_id;
				$html .= get_term_parents_list(
					$last_category_id,
					'ad_category',
					array(
						'separator' => '<span class="terraclassifieds-breadcrumb-separator"> <i class="fa fa-angle-right" aria-hidden="true"></i> </span>',
					)
				);
			}
			$html .= '<span> ' . esc_attr(get_the_title()) . '</span>';
		}
		if (is_tax('ad_category')) {
			$this_category_id = get_queried_object()->term_id;
			$html .= get_term_parents_list(
				$this_category_id,
				'ad_category',
				array(
					'separator' => '<span class="terraclassifieds-breadcrumb-separator"> <i class="fa fa-angle-right" aria-hidden="true"></i> </span>',
				)
			);
		}
		$html .= '</div>';
		echo wp_kses_post($html);
	}
}

// login - stop redirect to wp-login.php after entering a wrong password
if (!function_exists('terraclassifieds_custom_login_failed')) {
	add_action('wp_login_failed', 'terraclassifieds_custom_login_failed');
	function terraclassifieds_custom_login_failed($username)
	{
		$referrer = wp_get_referer();
		$page_login_slug = terraclassifieds_get_option('_tc_slug_login', 'login');
		$page_login = get_page_by_path($page_login_slug);
		if ($page_login) {
			$page_id =  $page_login->ID;
		}
		if ($referrer && !strstr($referrer, 'wp-login') && !strstr($referrer, 'wp-admin') && (strstr($referrer, 'login') || strstr($referrer, "?page_id=" . $page_id))) {
			wp_redirect(add_query_arg('login', 'failed', $referrer));
			exit;
		}
	}
}

// login - stop redirect to wp-login.php when login or password is empty
if (!function_exists('terraclassifieds_custom_authenticate_username_password')) {
	function terraclassifieds_custom_authenticate_username_password()
	{
		// check for permalinks structire
		if (get_option('permalink_structure') == '/%postname%/') {
			$permalinks_postname = true;
		} else {
			$permalinks_postname = false;
		}
		$page_login_slug = terraclassifieds_get_option('_tc_slug_login', 'login');
		$page_login = get_page_link(get_page_by_path($page_login_slug));
		if ($permalinks_postname) {
			$connector = '?';
		} else {
			$connector = '&';
		}
		return header('Location: ' . $page_login . $connector . 'login=failed');
		exit;
	}
	add_filter('login_errors', 'terraclassifieds_custom_authenticate_username_password');
}

// override posts per page for category
if (!function_exists('terraclassifieds_posts_per_page')) {
	add_action('pre_get_posts',  'terraclassifieds_posts_per_page');
	function terraclassifieds_posts_per_page($query)
	{
		$items_per_page = terraclassifieds_get_option('_tc_category_view_items_per_page', '4');
		//echo '<pre>';
		//print_r($query);
		//echo '</pre>';
		if (!is_page() && !is_tax('ad_location')) {
			if (!is_admin() && ($query->is_archive() && !empty($query->query['ad_category']) || $query->is_search() && !empty($query->query['post_type']) && $query->query['post_type'] == "classified")) {
				$query->set('posts_per_page', $items_per_page);
			}
		}
	}
}

// add subcategories class to BODY for add advert view
if (!function_exists('terraclassifieds_add_advert_body_class')) {
	add_filter('body_class', 'terraclassifieds_add_advert_body_class');
	function terraclassifieds_add_advert_body_class($classes)
	{
		$subcategories_columns_number = terraclassifieds_get_option('_tc_add_advert_subcategories_columns', 4);
		$add_advert_page_id = terraclassifieds_get_option('_tc_slug_add_advert', 'add-advert');
		if (is_page($add_advert_page_id)) {
			$classes[] = 'subcategories-' . $subcategories_columns_number;
		} else {
			$classes[] = '';
		}
		return $classes;
	}
}


// save title and description into _tc_post_title and _tc_post_content fields when create ad in the backend
if (!function_exists('terraclassifieds_save_fake_title_desc')) {
	function terraclassifieds_save_fake_title_desc($post_id, $post, $update)
	{

		$post_type = get_post_type($post_id);

		// If this isn't a 'classified' post, don't update it.
		if ("classified" != $post_type && is_admin()) return;

		// - Update the post's metadata.
		if (isset($_POST['post_title'])) {
			update_post_meta($post_id, '_tc_post_title', $_POST['post_title']);
		}
		if (isset($_POST['content'])) {
			update_post_meta($post_id, '_tc_post_content', $_POST['content']);
		}
	}
	add_action('save_post', 'terraclassifieds_save_fake_title_desc', 12, 3);
}

// ajax search for location search as an inputbox
add_action('wp_ajax_nopriv_terraclassifiedsLocationsAjax', 'terraclassifiedsLocationsAjax');
add_action('wp_ajax_terraclassifiedsLocationsAjax', 'terraclassifiedsLocationsAjax');

if (!function_exists('terraclassifiedsLocationsAjax')) {
	function terraclassifiedsLocationsAjax()
	{

		if (check_ajax_referer('terraclassifiedsLocationsAjax', 'nonce', false) == false) {
			wp_send_json_error();
		}


		if (!empty($_POST['name']) && !empty($_POST['taxonomy'])) {

			$terms = get_terms(array(
				'taxonomy'     => $_POST['taxonomy'],
				'name__like'   => $_POST['name'],
				'hide_empty'   => true,
				'hierarchical' => false
			));

			$results = [];
			foreach ($terms as $term) {
				$item = new stdClass();
				$item->value = $term->name;
				$item->id = $term->term_id;
				$results[] = $item;
			}

			wp_send_json_success($results);
		}
	}
}

// meta description for single ad and ad category
if (!function_exists('terraclassifiedsMetaDescription')) {
	function terraclassifiedsMetaDescription()
	{
		global $post;
		$meta_description_limit = terraclassifieds_get_option_base_functions('_tc_meta_description_text_limit', '158');
		if (is_singular('classified')) {
			$des_post = strip_tags($post->post_content);
			$des_post = strip_shortcodes($post->post_content);
			$des_post = str_replace(array("\n", "\r", "\t"), ' ', $des_post);
			$des_post = mb_substr($des_post, 0, $meta_description_limit, 'utf8');
			echo '<meta name="description" content="' . $des_post . '" />' . "\n";
		}
		if (is_tax('ad_category')) {
			$category_description = strip_tags(category_description());
			$desc_cat = strip_shortcodes($category_description);
			$desc_cat = str_replace(array("\n", "\r", "\t"), ' ', $desc_cat);
			$desc_cat = mb_substr($desc_cat, 0, $meta_description_limit, 'utf8');
			echo '<meta name="description" content="' . $desc_cat . '" />' . "\n";
		}
	}
	add_action('wp_head', 'terraclassifiedsMetaDescription');
}

// Set/check session
if (!function_exists('terraclassifiedsSession')) {
	add_action('init', 'terraclassifiedsSession', 1);
	function terraclassifiedsSession()
	{
		$hits = terraclassifieds_get_option_base_functions('_tc_hits', '0');
		if (!session_id() && $hits) {
			session_start();
		}
	}
}

// count hits of posts
if (!function_exists('terraclassifiedsTrackPopularPosts')) {
	function terraclassifiedsTrackPopularPosts()
	{
		$hits = terraclassifieds_get_option_base_functions('_tc_hits', '0');
		// Only run the process for single posts, pages and post types
		if (is_singular('classified') && $hits) {
			global $post;
			$custom_field = '_terraclassifieds_popular_posts_count';
			// Only track a one view per post for a single visitor session to avoid duplications
			if (!isset($_SESSION["terraclassifieds-popular-posts-count-{$post->ID}"])) {
				// Update view count
				$view_count = get_post_meta($post->ID, $custom_field, true);
				$stored_count = (isset($view_count) && !empty($view_count)) ? (intval($view_count) + 1) : 1;
				$update_meta = update_post_meta($post->ID, $custom_field, $stored_count);
				// Check for errors
				if (is_wp_error($update_meta))
					error_log($update_meta->get_error_message(), 0);
				// Store session in "viewed" state
				$_SESSION["terraclassifieds-popular-posts-count-{$post->ID}"] = 1;
			}
			// uncomment these 3 lines to show views of post (right after <body> tag)
			/*echo '<p style="color: red; text-align: center; margin: 0; background: #fff; z-index: 9999; position: relative; border: 1px solid #000; padding: 1em;">';
             echo get_post_meta( $post->ID, $custom_field, true );
             echo ' views of this post</p>';*/
		}
	}
	add_action('wp_head', 'terraclassifiedsTrackPopularPosts');
}

// format price
if (!function_exists('terraclassifiedsPriceFormat')) {
	function terraclassifiedsPriceFormat($price)
	{
		$price_thousand_separator = terraclassifieds_get_option('_tc_price_thousand_separator', 'none');
		if ($price_thousand_separator == 'none') {
			$price_thousand_separator_val = '';
		} else if ($price_thousand_separator == 'space') {
			$price_thousand_separator_val = ' ';
		} else if ($price_thousand_separator == 'comma') {
			$price_thousand_separator_val = ',';
		} else if ($price_thousand_separator == 'dot') {
			$price_thousand_separator_val = '.';
		}
		$price_decimal_separator = terraclassifieds_get_option('_tc_price_decimal_separator', 'dot');
		if ($price_decimal_separator == 'comma') {
			$price_decimal_separator_val = ',';
		} else if ($price_decimal_separator == 'dot') {
			$price_decimal_separator_val = '.';
		}
		$price_decimal_points = terraclassifieds_get_option('_tc_price_decimal_points', 0);

		$price = str_replace(",", ".", $price);
		$price = (float) $price; // make a float from string

		echo esc_attr(number_format($price, $price_decimal_points, $price_decimal_separator_val, $price_thousand_separator_val));
	}
}

if (!function_exists('terraclassifiedsRedirectArchivedAdvert')) {
	function terraclassifiedsRedirectArchivedAdvert()
	{
		//check for 404
		$expired_ad_behaviour = terraclassifieds_get_option('_tc_seo_expired_ad', 0);
		$expired_ad_redirect_url = terraclassifieds_get_option('_tc_seo_expired_ad_redirect_url', 0);
		if (is_404() && $expired_ad_behaviour == 1) {
			global $wp_query;
			if (isset($wp_query->query['post_type']) && $wp_query->query['post_type'] == 'classified') {
				wp_redirect($expired_ad_redirect_url);
				exit();
			}
		} else {
			return;
		}
	}
	add_action('template_redirect', 'terraclassifiedsRedirectArchivedAdvert');
}

if (!function_exists('terraclassifiedsRedirectArchivedAdvertCategory')) {
	function terraclassifiedsRedirectArchivedAdvertCategory()
	{
		//check for 404
		global $wp_query;
		if (!isset($wp_query->query['ad_category']) && !isset($wp_query->query['ad_location']) && isset($wp_query->query['post_type']) && $wp_query->query['post_type'] == 'classified') {
			$post_id = preg_replace('/\D/', '', $wp_query->request);
			$post_id = substr($post_id, 2);
			$category = get_the_terms($post_id, 'ad_category');
			$category_slug = $category['0']->slug;
			$category_url = '';
			if (!empty($category)) {
				$category_url = get_site_url() . '?s=&ad_category=' . $category_slug;
			}
			$expired_ad_behaviour = terraclassifieds_get_option('_tc_seo_expired_ad', 0);
			if (is_404() && $expired_ad_behaviour == 3) {
				wp_redirect($category_url);
				exit();
			} else {
				return;
			}
		}
	}
	add_action('template_redirect', 'terraclassifiedsRedirectArchivedAdvertCategory');
}

// change slugs for Terraclassifieds pages when save Terraclassifieds settings
if (!function_exists('terraclassifiedsSaveSettingsAction')) {
	add_action('cmb2_save_options-page_fields_tc_settings_layout', 'terraclassifiedsSaveSettingsAction', 10, 3);
	function terraclassifiedsSaveSettingsAction(string $object_id, array $updated, CMB2 $cmb)
	{

		// add advert
		$page_add_advert_id = terraclassifieds_get_option('_tc_add_advert_page_id', false);
		$page_add_advert_slug = terraclassifieds_get_option('_tc_slug_add_advert', 'add-advert');
		$add_advert_page = array(
			'ID'           => $page_add_advert_id,
			'post_name' => $page_add_advert_slug,
		);
		if ($page_add_advert_id && $page_add_advert_slug) {
			$page_add_advert_already_exists = get_page_by_path($page_add_advert_slug);
			if ($page_add_advert_already_exists) {
				$page_add_advert_already_exists_id =  $page_add_advert_already_exists->ID;
				$add_advert_page_already_exists_array = array(
					'ID'           => $page_add_advert_already_exists_id,
					'post_name' => $page_add_advert_slug . '-copy',
				);
				wp_update_post($add_advert_page_already_exists_array);
			}
			wp_update_post($add_advert_page);
		}

		// edit advert
		$page_edit_advert_id = terraclassifieds_get_option('_tc_edit_advert_page_id', false);
		$page_edit_advert_slug = terraclassifieds_get_option('_tc_slug_edit_advert', 'edit-advert');
		$edit_advert_page = array(
			'ID'           => $page_edit_advert_id,
			'post_name' => $page_edit_advert_slug,
		);

		if ($page_edit_advert_id && $page_edit_advert_slug) {
			$page_edit_advert_already_exists = get_page_by_path($page_edit_advert_slug);
			if ($page_edit_advert_already_exists) {
				$page_edit_advert_already_exists_id =  $page_edit_advert_already_exists->ID;
				$edit_advert_page_already_exists_array = array(
					'ID'           => $page_edit_advert_already_exists_id,
					'post_name' => $page_edit_advert_slug . '-copy',
				);
				wp_update_post($edit_advert_page_already_exists_array);
			}
			wp_update_post($edit_advert_page);
		}

		// edit profile
		$page_edit_profile_id = terraclassifieds_get_option('_tc_edit_profile_page_id', false);
		$page_edit_profile_slug = terraclassifieds_get_option('_tc_slug_edit_profile', 'edit-profile');
		$edit_profile_page = array(
			'ID'           => $page_edit_profile_id,
			'post_name' => $page_edit_profile_slug,
		);

		if ($page_edit_profile_id && $page_edit_profile_slug) {
			$page_edit_profile_already_exists = get_page_by_path($page_edit_profile_slug);
			if ($page_edit_profile_already_exists) {
				$profile_edit_profile_already_exists_id =  $page_edit_profile_already_exists->ID;
				$page_edit_profile_already_exists_array = array(
					'ID'           => $profile_edit_profile_already_exists_id,
					'post_name' => $page_edit_profile_slug . '-copy',
				);
				wp_update_post($page_edit_profile_already_exists_array);
			}
			wp_update_post($edit_profile_page);
		}

		// favourite ads
		$page_favourite_ads_id = terraclassifieds_get_option('_tc_favourite_ads_page_id', false);
		$page_favourite_ads_slug = terraclassifieds_get_option('_tc_slug_favourite_ads', 'favourite-ads');
		$favourite_ads_page = array(
			'ID'           => $page_favourite_ads_id,
			'post_name' => $page_favourite_ads_slug,
		);

		if ($page_favourite_ads_id && $page_favourite_ads_slug) {
			$page_favourite_ads_already_exists = get_page_by_path($page_favourite_ads_slug);
			if ($page_favourite_ads_already_exists) {
				$page_favourite_ads_already_exists_id =  $page_favourite_ads_already_exists->ID;
				$page_favourite_ads_already_exists_array = array(
					'ID'           => $page_favourite_ads_already_exists_id,
					'post_name' => $page_favourite_ads_slug . '-copy',
				);
				wp_update_post($page_favourite_ads_already_exists_array);
			}
			wp_update_post($favourite_ads_page);
		}

		// forgot password
		$page_forgot_password_id = terraclassifieds_get_option('_tc_forgot_password_page_id', false);
		$page_forgot_password_slug = terraclassifieds_get_option('_tc_slug_forgot_password', 'forgot-password');
		$forgot_password_page = array(
			'ID'           => $page_forgot_password_id,
			'post_name' => $page_forgot_password_slug,
		);

		if ($page_forgot_password_id && $page_forgot_password_slug) {
			$page_forgot_password_already_exists = get_page_by_path($page_forgot_password_slug);
			if ($page_forgot_password_already_exists) {
				$page_forgot_password_already_exists_id =  $page_forgot_password_already_exists->ID;
				$page_forgot_password_already_exists_array = array(
					'ID'           => $page_forgot_password_already_exists_id,
					'post_name' => $page_forgot_password_slug . '-copy',
				);
				wp_update_post($page_forgot_password_already_exists_array);
			}
			wp_update_post($forgot_password_page);
		}

		// login
		$page_login_id = terraclassifieds_get_option('_tc_login_page_id', false);
		$page_login_slug = terraclassifieds_get_option('_tc_slug_login', 'login');
		$login_page = array(
			'ID'           => $page_login_id,
			'post_name' => $page_login_slug,
		);

		if ($page_login_id && $page_login_slug) {
			$page_login_already_exists = get_page_by_path($page_login_slug);
			if ($page_login_already_exists) {
				$page_login_already_exists_id =  $page_login_already_exists->ID;
				$page_login_already_exists_array = array(
					'ID'           => $page_login_already_exists_id,
					'post_name' => $page_login_slug . '-copy',
				);
				wp_update_post($page_login_already_exists_array);
			}
			wp_update_post($login_page);
		}

		// registration
		$page_registration_id = terraclassifieds_get_option('_tc_registration_page_id', false);
		$page_registration_slug = terraclassifieds_get_option('_tc_slug_registration', 'registration');
		$registration_page = array(
			'ID'           => $page_registration_id,
			'post_name' => $page_registration_slug,
		);

		if ($page_registration_id && $page_registration_slug) {
			$page_registration_already_exists = get_page_by_path($page_registration_slug);
			if ($page_registration_already_exists) {
				$page_registration_already_exists_id =  $page_registration_already_exists->ID;
				$page_registration_already_exists_array = array(
					'ID'           => $page_registration_already_exists_id,
					'post_name' => $page_registration_slug . '-copy',
				);
				wp_update_post($page_registration_already_exists_array);
			}
			wp_update_post($registration_page);
		}

		// my submissions
		$page_my_submissions_id = terraclassifieds_get_option('_tc_my_submissions_page_id', false);
		$page_my_submissions_slug = terraclassifieds_get_option('_tc_slug_my_submissions', 'my-submissions');
		$my_submissions_page = array(
			'ID'           => $page_my_submissions_id,
			'post_name' => $page_my_submissions_slug,
		);

		if ($page_my_submissions_id && $page_my_submissions_slug) {
			$page_my_submissions_already_exists = get_page_by_path($page_my_submissions_slug);
			if ($page_my_submissions_already_exists) {
				$page_my_submissions_already_exists_id =  $page_my_submissions_already_exists->ID;
				$page_my_submissions_already_exists_array = array(
					'ID'           => $page_my_submissions_already_exists_id,
					'post_name' => $page_my_submissions_slug . '-copy',
				);
				wp_update_post($page_my_submissions_already_exists_array);
			}
			wp_update_post($my_submissions_page);
		}
	}
}

// get post by slug
function terraclassifieds_post_by_slug($slug, $post_type = 'post', $unique = true)
{
	$args = array(
		'name' => $slug,
		'post_type' => $post_type,
		'post_status' => 'publish',
		'posts_per_page' => 1
	);
	$my_posts = get_posts($args);
	if ($my_posts) {
		if ($unique) {
			return $my_posts[0];
		} else {
			return $my_posts;
		}
	}
	return false;
}

// notice about theme options in Appearance -> Themes
if (!function_exists('terraclassifiedsAdminNoticePages')) {
	function terraclassifiedsAdminNoticePages()
	{

		$terraclassifiedsNoticeText = '';

		$page_add_advert_slug = terraclassifieds_get_option('_tc_slug_add_advert', 'add-advert');
		$page_edit_advert_slug = terraclassifieds_get_option('_tc_slug_edit_advert', 'edit-ad');
		$page_edit_profile_slug = terraclassifieds_get_option('_tc_slug_edit_profile', 'edit-profile');
		$page_favourite_ads_slug = terraclassifieds_get_option('_tc_slug_favourite_ads', 'favourite-ads');
		$page_forgot_password_slug = terraclassifieds_get_option('_tc_slug_forgot_password', 'forgot-password');
		$page_login_slug = terraclassifieds_get_option('_tc_slug_login', 'login');
		$page_registration_slug = terraclassifieds_get_option('_tc_slug_registration', 'registration');
		$page_my_submissions_slug = terraclassifieds_get_option('_tc_slug_my_submissions', 'my-submissions');

		if (!terraclassifieds_post_by_slug($page_add_advert_slug, 'page')) {
			$terraclassifiedsNoticeText .= __('<strong>Add advert</strong> page is missing or its slug was changed via editing a page.', 'terraclassifieds') . '<br />';
		}
		if (!terraclassifieds_post_by_slug($page_edit_advert_slug, 'page')) {
			$terraclassifiedsNoticeText .= __('<strong>Edit advert</strong> page is missing or its slug was changed via editing a page.', 'terraclassifieds') . '<br />';
		}
		if (!terraclassifieds_post_by_slug($page_edit_profile_slug, 'page')) {
			$terraclassifiedsNoticeText .= __('<strong>Edit profile</strong> page is missing or its slug was changed via editing a page.', 'terraclassifieds') . '<br />';
		}
		if (!terraclassifieds_post_by_slug($page_favourite_ads_slug, 'page')) {
			$terraclassifiedsNoticeText .= __('<strong>Favourite ads</strong> page is missing or its slug was changed via editing a page.', 'terraclassifieds') . '<br />';
		}
		if (!terraclassifieds_post_by_slug($page_forgot_password_slug, 'page')) {
			$terraclassifiedsNoticeText .= __('<strong>Forgot password</strong> page is missing or its slug was changed via editing a page.', 'terraclassifieds') . '<br />';
		}
		if (!terraclassifieds_post_by_slug($page_login_slug, 'page')) {
			$terraclassifiedsNoticeText .= __('<strong>Login</strong> page is missing or its slug was changed via editing a page.', 'terraclassifieds') . '<br />';
		}
		if (!terraclassifieds_post_by_slug($page_registration_slug, 'page')) {
			$terraclassifiedsNoticeText .= __('<strong>Registration</strong> page is missing or its slug was changed via editing a page.', 'terraclassifieds') . '<br />';
		}
		if (!terraclassifieds_post_by_slug($page_my_submissions_slug, 'page')) {
			$terraclassifiedsNoticeText .= __('<strong>My submissions</strong> page is missing or its slug was changed via editing a page.', 'terraclassifieds') . '<br />';
		}

		if ($terraclassifiedsNoticeText != '') {
			$terraclassifiedsNoticeText .= __('If you changed a slug via editing a page,  it will not work correctly. Instead, use <strong>Slugs</strong> settings for each page (view) in <strong>SEO</strong> and assign the correct page for each view.', 'terraclassifieds') . '<br />' .
				__('If you removed the default page, it can be recreated by following steps of deactivating and activating the TerraClassifieds plugin.', 'terraclassifieds');
		}

		if (isset($_GET['page'])) {
			global $pagenow;
			if ($terraclassifiedsNoticeText != '' && in_array($pagenow, array('edit.php')) && ($_GET['page'] == 'terraclassifieds_settings')) {
				echo '<div class="error notice-info is-dismissible pe-notice">';
				echo  '<p>';
				echo $terraclassifiedsNoticeText;
				echo '</p>';
				echo '</div>';
			}
		}
	}
	add_action('admin_notices', 'terraclassifiedsAdminNoticePages', 9999);
}

function terraclassifieds_get_login_url() {
	$page_slug = terraclassifieds_get_option('_tc_slug_login');
	if( !empty($page_slug) ) {
		$page_url = get_page_link(get_page_by_path($page_slug));
	} else {
		$page_url = wp_login_url();
	}
	
	return esc_url($page_url);
}

function terraclassifieds_get_edit_profile_url() {
	$page_slug = terraclassifieds_get_option('_tc_slug_edit_profile');
	if( !empty($page_slug) ) {
		$page_url = get_page_link(get_page_by_path($page_slug));
	} else {
		$page_url = get_edit_user_link();
	}
	
	return esc_url($page_url);
}