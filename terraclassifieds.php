<?php
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Plugin Name: TerraClassifieds - Simple Classifieds Plugin
 * Plugin URI: https://www.pixelemu.com/wordpress-plugins/i/245-terraclassifieds
 * Description: Create a classifieds website with WordPress. To save time use dedicated theme TerraClassic available on <a href="https://www.pixelemu.com/">pixelemu.com</a>
 * Version: 1.9.1
 * Author: pixelemu.com
 * Author URI: https://pixelemu.com
 * Text Domain: terraclassifieds
 * License: http://www.pixelemu.com/license.html PixelEmu Proprietary Use License
 */

if (!class_exists('Terraclassifieds')) {
	class Terraclassifieds
	{

		static $path;
		static $url;
		static $plugin;

		function __construct()
		{

			self::$path  = plugin_dir_path(__FILE__);
			self::$url   = plugin_dir_url(__FILE__);
			self::$plugin = 'terraclassifieds';

			//$this->addFunctions();

			register_activation_hook(__FILE__, array($this, 'pluginActivation'));
			register_deactivation_hook(__FILE__, array($this, 'pluginDeactivation'));

			add_action('plugins_loaded', array($this, 'loadTextDomain'));
			add_action('init', array($this, 'cmb2'));
			add_action('cmb2_init', array($this, 'cmb2PostSearchField'));
			add_action('cmb2_init', array($this, 'cmb2Tabs'));

			$permalinks_structure = get_option('permalink_structure');
			if ($permalinks_structure == '/%postname%/') {
				add_action('init', array($this, 'hierarchicalUrls'));
			}

			add_action('init', array($this, 'addCustomPosts'));
			add_action('init', array($this, 'addFunctions'));
			add_action('init', array($this, 'addFavorites'));
			add_action('init', array($this, 'user'));

			add_action('widgets_init', array($this, 'addWidgets'));

			add_action('wp', array($this, 'addFunctionsWP')); // wp instead of init to make work methods: is_singular and get_post_type

			add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'));
			add_action('admin_enqueue_scripts', array($this, 'adminEnqueueScripts'));
			add_action('wp_enqueue_scripts', array($this, 'frontendStyles'));

			add_action('admin_enqueue_scripts', array($this, 'adminStyles'));

			include self::$path . 'inc/functions/cron-functions.php';
		}

		// get CM2 options
		public function terraclassifieds_get_option($key = '', $default = false)
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

		/**
		 * Include CMB2
		 */
		public function cmb2()
		{
			include self::$path . 'inc/cmb2.php';
		}

		/**
		 * Include CMB2 Tabs
		 */
		public function cmb2Tabs()
		{
			include self::$path . 'inc/cmb2/cmb2-tabs/plugin.php';
		}

		/**
		 * Include CMB2 Post Search Field
		 */
		public function cmb2PostSearchField()
		{
			include self::$path . 'inc/cmb2/cmb2-post-search-field/lib/init.php';
		}

		/**
		 * Include plugin functions with WP method
		 */
		public function addFunctionsWP()
		{
			include self::$path . 'inc/functions/layout.php';
		}

		/**
		 * Include plugin functions
		 */
		public function addFunctions()
		{
			include self::$path . 'inc/functions/base-functions.php';
			include self::$path . 'inc/functions/notification-functions.php';
			if (terraclassifieds_get_option('_tc_use_types', 0)) {
				include self::$path . 'inc/functions/dynamic-styles-func.php';
			}
		}

		/**
		 * Include plugin functions
		 */
		public function hierarchicalUrls()
		{
			include self::$path . 'inc/functions/hierarchical-urls.php';
		}

		/**
		 * Include custom post types
		 */
		public function addCustomPosts()
		{
			include self::$path . 'inc/custom-posts/ad-post-type.php';
			include self::$path . 'inc/functions/settings.php';
			include self::$path . 'inc/shortcodes.php';
		}

		/**
		 * Include Favorites
		 */
		public function addFavorites()
		{
			include self::$path . 'inc/favourites/favourites.php';
		}

		/**
		 * Include custom pages
		 */
		public function addCustomPages()
		{
			include self::$path . 'inc/add-classified.php';
		}

		/**
		 * Include user functions
		 */
		public function user()
		{
			include self::$path . 'inc/user/user.php';
		}

		public function userActivation()
		{
			include self::$path . 'inc/user/add-role.php';
		}

		public function userDeactivation()
		{
			include self::$path . 'inc/user/remove-role.php';
		}

		/**
		 * Plugin activation hook
		 */
		public function pluginActivation()
		{
			$this->addCustomPosts();
			$this->addCustomPages();
			$this->userActivation();

			flush_rewrite_rules();
		}

		/**
		 * Plugin deactivation hook
		 */
		public function pluginDeactivation()
		{
			$this->userDeactivation();

			flush_rewrite_rules();
		}

		/**
		 * Register widgets
		 */
		public function addWidgets()
		{
			include self::$path . 'inc/widgets/terra-latest-ads.php';
			include self::$path . 'inc/widgets/terra-categories.php';
			include self::$path . 'inc/widgets/terra-search.php';
			include self::$path . 'inc/widgets/terra-menu.php';
			register_widget('TerraClassifiedsLatestAds');
			register_widget('TerraClassifiedsCategories');
			register_widget('TerraclassifiedsSearch');
			register_widget('TerraClassifiedsMenu');
		}

		/**
		 * Add scripts and styles
		 */
		public function enqueueScripts()
		{
			if (!is_admin()) {
				//body
				if (is_singular('classified')) {
					wp_enqueue_script(self::$plugin . '-lightSlider', self::$url . 'assets/js/lightslider.min.js', array('jquery'), true);
					wp_enqueue_script(self::$plugin . '-lightGallery', self::$url . 'assets/js/lightgallery.min.js', array('jquery'), true);
				}
				wp_enqueue_script(self::$plugin . 'jquery-validate', self::$url . 'assets/js/jquery.validate.min.js', array('jquery'), true);
				wp_enqueue_script(self::$plugin . '-plugin-js', self::$url . 'assets/js/script.js', array('jquery'), true);
				wp_enqueue_script(self::$plugin . '-select2', self::$url . 'assets/js/select2.min.js', array('jquery'), true);
				wp_enqueue_script('jquery-ui-core');
				wp_enqueue_script('jquery-ui-autocomplete');
				wp_localize_script(self::$plugin . '-plugin-js', 'settings', array(
					'ajaxurl'    => admin_url('admin-ajax.php')
				));

				$imagesLimit = terraclassifieds_get_option('_tc_add_advert_images_limit', 8);
				if ($imagesLimit > 8) {
					$imagesLimit = 8;
				}
				$expire_time = terraclassifieds_get_option('_tc_advert_expire_time', 30);
				$add_advert_required_gallery = terraclassifieds_get_option('_tc_add_advert_required_gallery', false);
				$add_advert_required_location = terraclassifieds_get_option('_tc_add_advert_required_location', false);
				$add_advert_required_type = terraclassifieds_get_option('_tc_types_required', false);
				$descriptionMinimumLength = terraclassifieds_get_option('_tc_add_advert_description_minimum_length', false);
				$allowPriceZero = terraclassifieds_get_option('_tc_add_advert_allow_price_zero', false);

				$dataToBePassed = array(
					'imagesLimit'            => $imagesLimit,
					'expireTime'            => $expire_time,
					'imagesLimitMessage1' => __('You can assign max ', 'terraclassifieds'),
					'imagesLimitMessage2' => __(' images.', 'terraclassifieds'),
					'imagesLeft' => __(' images left', 'terraclassifieds'),
					'charactersLimit' => __('You have reached the limit.', 'terraclassifieds'),
					'charactersLeft' => __(' characters left.', 'terraclassifieds'),
					'descriptionMinimumCharacters' => __('Enter min', 'terraclassifieds') . ' ' . $descriptionMinimumLength . ' ' . __('characters', 'terraclassifieds') . ', ',
					'galleryRequired'            => $add_advert_required_gallery,
					'locationRequired'            => $add_advert_required_location,
					'typeRequired'            => $add_advert_required_type,
					'clearAllLocations' => __('Clear all locations', 'terraclassifieds'),
					'allLocations' => __('All locations', 'terraclassifieds'),
					'numbOfcharsDescriptionLength' => $descriptionMinimumLength,
					'cancelButtonText' => __('Cancel', 'terraclassifieds'),
					'allowPriceZero' => $allowPriceZero,
					'addAdvertSubmitButtonPublish' => __('Add advert', 'terraclassifieds'),
					'addAdvertSubmitButtonSaveChanges' => __('Save changes', 'terraclassifieds'),
					'addAdvertSubmitButtonDraft' => __('Save as draft', 'terraclassifieds'),
					'priceFilterInputFrom' => esc_html_x("From", "search price", "terraclassifieds"),
					'priceFilterInputTo' => esc_html_x("To", "search price", "terraclassifieds"),
					'registrationPasswordMinimumCharacters1' => __('Enter at least', 'terraclassifieds'),
					'registrationPasswordMinimumCharacters2' => __('characters', 'terraclassifieds'),
				);
				wp_localize_script(self::$plugin . '-plugin-js', 'php_vars', $dataToBePassed);
			}
		}

		public function adminEnqueueScripts()
		{
			if (is_admin()) {
				wp_enqueue_script(self::$plugin . '-plugin-admin-js', self::$url . 'assets/js/script-admin.js', array('jquery'), true);
			}
		}

		/**
		 * Add styles in frontend
		 */
		function frontendStyles()
		{
			if (is_singular('classified')) {
				wp_enqueue_style('lightSlider', self::$url . 'assets/css/lightslider.css', array(), true);
				wp_enqueue_style('lightGallery', self::$url . 'assets/css/lightgallery.css', array(), true);
			}
			$tc_style = terraclassifieds_get_option('_tc_style', 0);
			if ($tc_style == 0) {
				wp_enqueue_style('terraclassifieds-frontend', self::$url . 'assets/css/terraclassifieds-frontend.css', array(), true);
			}
			wp_dequeue_style('font-awesome');
			wp_dequeue_style('font-awesome-css');
			if (!(wp_style_is('all.css'))) {
				wp_enqueue_style('font-awesome-all',  self::$url . 'assets/css/font-awesome/all.css', '', '5.6.3');
			}
			if (!(wp_style_is('v4-shims.css'))) {
				wp_enqueue_style('font-awesome-v4-shims',  self::$url . 'assets/css/font-awesome/v4-shims.css', '', '5.6.3');
			}
			wp_enqueue_style('select2', self::$url . 'assets/css/select2.min.css', array(), true);
			wp_enqueue_style('terraclassifieds-grid', self::$url . 'assets/css/grid.css', array(), true);
		}

		/**
		 * Add styles in back-end
		 */
		public function adminStyles()
		{
			wp_enqueue_style('terraclassifieds-admin', self::$url . 'assets/css/terraclassifieds-admin.css', array(), true);
		}

		/**
		 * Languages
		 */
		public function loadTextDomain()
		{
			load_plugin_textdomain('terraclassifieds', false, dirname(plugin_basename(__FILE__)) . '/languages/');
		}
	}
}

new Terraclassifieds();
new CronTerraclassifieds();

// add class for new custom statuses
include_once('inc/functions/custom-post-status-archived.php');
include_once('inc/functions/custom-post-status-rejected.php');
include_once('inc/functions/notices.php');
