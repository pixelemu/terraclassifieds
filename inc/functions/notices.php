<?php
/*--------------------------------------------------------------
 Copyright (C) pixelemu.com
 License: http://www.pixelemu.com/license.html PixelEmu Proprietary Use License
 Website: http://www.pixelemu.com
 Support: info@pixelemu.com
---------------------------------------------------------------*/

if ( !defined ( 'ABSPATH' ) ) {
	die();
}

// ---------------------------------------------------------------
// UPDATES
// ---------------------------------------------------------------

if ( !class_exists( 'TerraclassifiedsNotices' ) ) {

	class TerraclassifiedsNotices {
		static $instance = null;
		public static $check = true; //enable or disable checking
		public static $message = null; //theme update message
		public static $dismissed_time = false; //time when clicked on button

		function __construct() {

			//$this->clearCache(); //clear cache :)
			//update_option( 'terraclassifieds-update-dismissed', false );
			self::$dismissed_time = get_option('terraclassifieds-update-dismissed', false );
			if( true === self::$check && self::$dismissed_time == false ) {
				add_action( 'admin_init', array( $this, 'check_theme_exists' ) );
			}
		}

		/**
		 * Get instance
		 * @return object
		 */
		public static function instance() {
			if (self::$instance === null) {
				self::$instance = new TerraclassifiedsNotices();
			}
			return self::$instance;
		}

		/**
		 * Check for updates and show notice if necessary
		 */
		public function check_theme_exists() {

			//add js
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_js' ) );
			
			$images_directory = plugins_url('../assets/img/', dirname(__FILE__));
			//show notice
			$my_theme = wp_get_theme( 'pe-terraclassic' );
			if ( !$my_theme->exists() ){
				$message = get_option('terraclassifieds-update-dismissed', false );
				
				$message .= '<p style="text-align: center;"><a href="https://www.pixelemu.com/wordpress-themes/i/244-terraclassic" target="_blank"><img style="max-width: 100%;" src="' . $images_directory . 'terraclassifieds-notification.jpg" alt ="terraclassifieds-notification"/></a></p>';
				self::$message = $message;
				$this->show_update_notice();
			}
		}

		/**
		 * Clear transient and dismissed option
		 */
		public function clearCache() {
			update_option( 'terraclassifieds-update-dismissed', false );
		}

		/**
		 * Show update notice in backend
		 */
		private function show_update_notice() {
			add_action ( 'admin_notices', array( $this, 'update_notice' ) );
		}

		/**
		 * Update notice message
		 */
		public function update_notice() {
			$class = 'notice notice-info terraclassifieds-update-notice is-dismissible';
			$message = self::$message;
			printf( '<br /><div class="%1$s">%2$s</div>', $class, $message );
		}

		/**
		 * Add dismiss button script
		 */
		public function enqueue_js() {
			if( !wp_script_is('jquery', 'done') ) {
				wp_enqueue_script('jquery');
			}
			$code = "(function($) {
								$(document).ready(function() {
									$(document).on( 'click', '.terraclassifieds-update-notice .notice-dismiss', function () {
										$.ajax( ajaxurl,
											{
												type: 'POST',
												data: {
													action: 'terraclassifieds_dismissed_notice_handler'
												}
											} );
									});
								});
							})(jQuery);";
			wp_add_inline_script( 'jquery-migrate', $code );
		}

	}
	TerraclassifiedsNotices::instance();
}

add_action( 'wp_ajax_terraclassifieds_dismissed_notice_handler', 'terraclassifieds_ajax_notice_handler' );
if( !function_exists('terraclassifieds_ajax_notice_handler') ) {
	/**
	 * Update option on button click (ajax)
	 */
	function terraclassifieds_ajax_notice_handler() {
		update_option( 'terraclassifieds-update-dismissed', true );
	}
}

?>
