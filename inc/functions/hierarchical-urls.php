<?php
function hierarchical_urls_init() {
    new Hierarchical_Urls();
}
add_action( 'init', 'hierarchical_urls_init', 11 );

class Hierarchical_Urls {

    function __construct() {
    
		//flush_rewrite_rules();
        add_filter( 'post_type_link', array( $this, 'post_link' ), 10, 3 );
        add_filter( 'term_link', array( $this, 'term_link' ), 10, 3 );

        if ( ! is_admin() ) {
            add_filter( 'rewrite_rules_array', array( $this, 'rewrite_rules_array' ) );
            //add_filter( 'classified_rewrite_rules', array( $this, 'rewrite_rules_array' ) );
            add_action( 'wp_loaded', array( $this, 'flush_rules' ) );

        }

        //add_action( 'wp_loaded', array( $this, 'debug_rules' ) );

    }

    function debug_rules() {
        global $wp_query, $wp_rewrite;
        echo '<pre>';
        //print_r( $wp_query );
        print_r( $wp_rewrite->rewrite_rules() );
        echo '</pre>';
    }

    function flush_rules() {
        if ( ! $rules = get_option( 'rewrite_rules' ))
            $rules = array();
        $plugin_rules = $this->get_rules() + $rules;
        foreach ( array_keys( $plugin_rules ) as $r ) {
            if ( empty( $rules[$r] ) ) {
                global $wp_rewrite;
                $wp_rewrite->flush_rules();
                break;
            }
        }
    }

    function post_link( $permalink, $post, $leavename ) {
    	
		$slug_archive = terraclassifieds_get_option( '_tc_slug_archive', 'classifieds' );
		$slug_single = terraclassifieds_get_option( '_tc_slug_single', 'classified' );
		$categories = array_reverse( wp_get_post_terms( $post->ID, 'ad_category' ) );
		
		if ( empty( $categories[0] ) ) return $permalink;

		$category = $categories[0];
		$path = array( $category->slug );
		while ( ( $category = get_term( $category->parent ) )
			&& ( ! empty( $category->term_id ) ) ) {
			$path[] = $category->slug;
		}
		$path[] = $slug_single;
        
		$path = array_reverse( $path );
		$path[] = $post->post_name;
		return home_url( implode( '/', $path ) );
    }

    function term_link ( $permalink, $term, $leavename ) {
		$slug_archive = terraclassifieds_get_option( '_tc_slug_archive', 'classifieds' );
		$slug_single = terraclassifieds_get_option( '_tc_slug_single', 'classified' );
		if( $term->taxonomy != 'ad_category' ) return $permalink;

		$path = array( $term->slug );

		$term = get_term( $term->parent, 'ad_category' );

		while ( $term && ! empty( $term->parent ) ) {
			$path[] = $term->slug;
			$term = get_term( $term->parent, 'ad_category' );
		}

		if ( $term && !empty($term->slug) ) {
			$path[] = $term->slug;
		}

        // dj custom
		$path[] = $slug_archive;
		$path = array_reverse( $path );

		return home_url( implode( '/', $path ) );
    }

    function rewrite_rules_array( $rules ) {
        return $this->get_rules() + $rules;
    }

    function get_entities() {
        $entities = array();
        $types = get_post_types( array( 'public' => true ) );
        foreach ( $types as $t ) {
            $post_type = get_post_type_object( $t );
            $entities[] = array(
                'post_type' => $post_type,
                'taxonomies' => get_object_taxonomies( $post_type->name, 'objects' )
            );
        }
        return $entities;
    }

    function get_rules_tree( $args = false ) {
		$slug_archive = terraclassifieds_get_option( '_tc_slug_archive', 'classifieds' );
		$slug_single = terraclassifieds_get_option( '_tc_slug_single', 'classified' );
        // dj custom
		$home_path = $slug_archive;
		
        $defaults = array(
            'post_type' => 'classified',
            'taxonomy_name' => 'ad_category',
            'parent' => 0,
            'path' => array()
        );
        $args = wp_parse_args( $args, $defaults );

        // dj custom
        $terms = get_terms(
            $args['taxonomy_name'],
            array(
                'parent' => $args['parent'], 
                'hide_empty' => false
            )
        );
        // $terms = get_terms( $args['taxonomy_name'], array( 'parent' => $args['parent'] ) );
        
        if ( empty( $terms ) )
            return array();
        
        $rules = array();
        foreach( $terms as $term ) {

            $term_path = $args['path'];
            $term_path_match = $args['path'];

            $term_path[] = $term->slug;
            $term_path_match[] = '(' . $term->slug . ')';

            $term_path_str = implode( '/', $term_path );
            $term_path_match_str = implode( '/', $term_path_match );

            $feed_sufix = 'feed/?(rss|rss2|atom)?/?$';
            $page_sufix = 'page/?([0-9]{1,})?/?$';

			//if(!($args['parent'] == 0)){
			// Posts
            // dj custom
			$rules[$slug_single . '/' . $term_path_str . '/([^/]+)/?$'] = //$rules[$home_path . '/' . $term_path_str . '/([^/]+)/?$'] =
				'index.php?classified=$matches[1]';
			//}

			// Term
			$rules[$home_path . '/' . $term_path_match_str . '/?$'] =
				'index.php?ad_category=$matches[1]';
			// Feeds
			$rules[$home_path . '/' . $term_path_match_str . '/' . $feed_sufix] =
				'index.php?ad_category=$matches[1]&feed=atom';
			// Pagination
			$rules[$home_path . '/' . $term_path_match_str . '/' . $page_sufix] =
				'index.php?ad_category=$matches[1]&paged=$matches[2]';

            $subrules = $this->get_rules_tree( array(
                'parent' => $term->term_id,
                'path' => $term_path
            ) );

            if ( $subrules )
                $rules = array_merge( $rules, $subrules );

        }
        return $rules;
    }

    function get_rules() {

        $rules = array();

        foreach ( $this->get_entities() as $e ) {

            // Post Type
            $rules[$e['post_type']->name . '/?$'] =
                'index.php?post_type=' . $e['post_type']->name;

            foreach( $e['taxonomies'] as $t ) {
                $args = array(
                    'post_type' => $e['post_type']->name,
                    'taxonomy_name' => $t->name
                );
                $rules = array_merge( $rules, $this->get_rules_tree( $args ) );
            }

        }

        return $rules;
    }

}

?>