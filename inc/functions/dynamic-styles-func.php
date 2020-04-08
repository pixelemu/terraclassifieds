<?php
if ( ! function_exists( 'terraclassifiedsDynamicCSS' ) ) {
	function terraclassifiedsDynamicCSS() {
		wp_enqueue_style( 'terraclassifieds-dynamic-styles', plugin_dir_url(__FILE__) . 'dynamic-styles.php');
	}
	add_action( 'wp_enqueue_scripts', 'terraclassifiedsDynamicCSS' );
}
