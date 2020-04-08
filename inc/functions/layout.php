<?php 
// add own layouts
if ( ! is_admin() ) {
	if( is_singular('classified') ){
		terraclassifieds_add_own_single_layout();
	} else if ( is_archive() && (is_post_type_archive('classified') || get_post_type() == 'classified') ) {
		terraclassifieds_add_own_archive_layout();
	}
}

// change layout for single ad view
function terraclassifieds_add_own_single_layout() {
	$tc_style = terraclassifieds_get_option( '_tc_layout', 0 );
	// load default style
	if( $tc_style == 0 ) {
		add_filter('template_include', 'terraclassifieds_template_single');
	// load from theme if exists
	} else if ( current_theme_supports( 'terraclassifieds' ) && file_exists(get_stylesheet_directory().'/terraclassifieds/single.php') ){
		add_filter('template_include', 'terraclassifieds_template_from_theme_single');
	} else {
		// else use theme default page
		// remove some default stuff
		add_filter( 'the_title', 'terraclassifieds_remove_page_title' );
		add_filter( 'the_content', 'terraclassifieds_replace_content' );
		add_filter( 'post_thumbnail_html', 'terraclassifieds_remove_featured_image');
	}
}

// change layout for archive view
function terraclassifieds_add_own_archive_layout() {
	$tc_style = terraclassifieds_get_option( '_tc_layout', 0 );
	// load default style
	if( $tc_style == 0 ) {
		add_filter('template_include', 'terraclassifieds_template_archive');
	// load from theme if exists
	} else if ( current_theme_supports( 'terraclassifieds' ) && file_exists(get_stylesheet_directory().'/terraclassifieds/archive.php') ){
		add_filter('template_include', 'terraclassifieds_template_from_theme_archive');
	}
	// else use theme default page
}

// replace whole page for supported themes by default template - single
function terraclassifieds_template_single( $template ){
	$template = plugin_dir_path( __FILE__ ).'../../templates/single.php';
	return $template;
}

// replace whole page for supported themes by theme's template - single
function terraclassifieds_template_from_theme_single( $template ){
	$template = get_stylesheet_directory().'/terraclassifieds/single.php';
	return $template;
}

// replace whole page for supported themes by default template - archive
function terraclassifieds_template_archive( $template ){
	$template = plugin_dir_path( __FILE__ ).'../../templates/archive.php';
	return $template;
}

// replace whole page for supported themes by theme's template - archive
function terraclassifieds_template_from_theme_archive( $template ){
	$template = get_stylesheet_directory().'/terraclassifieds/archive.php';
	return $template;
}

// replace the_content() for unsupported themes
function terraclassifieds_replace_content() {
	$content = include dirname( __FILE__ ) . '/../../templates/default_post.php'; 
}

// remove title for unsupported themes
function terraclassifieds_remove_page_title( $title ) {
	if(in_the_loop()){
		$title="";
	}
	return $title;
}

// remove featured image for unsupported themes
function terraclassifieds_remove_featured_image() {
	return '';
}

// own layout for author view
add_filter( 'template_include', 'terraclassifieds_author' );
function terraclassifieds_author( $template ) {

    $file = 'author.php';
    if ( is_author() ) {
            $template = untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/../../templates/' . $file;
    } 

    return $template;
}
?>