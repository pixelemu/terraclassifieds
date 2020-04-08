<?php

function terraclassifieds_get_option( $key = '', $default = false ) {
    if ( function_exists( 'cmb2_get_option' ) ) {
        // Use cmb2_get_option as it passes through some key filters.
        return cmb2_get_option( 'terraclassifieds_settings', $key, $default );
    }
    // Fallback to get_option if CMB2 is not loaded yet.
    $opts = get_option( 'terraclassifieds_settings', $default );
    $val = $default;
    if ( 'all' == $key ) {
        $val = $opts;
    } elseif ( is_array( $opts ) && array_key_exists( $key, $opts ) && false !== $opts[ $key ] ) {
        $val = $opts[ $key ];
    }
    return $val;
}

$expired_ad_behaviour = terraclassifieds_get_option( '_tc_seo_expired_ad', 0 );
if($expired_ad_behaviour == 2){
    $expired_ad_behaviour_val = true;
} else{
    $expired_ad_behaviour_val = false;
}

class WordPress_Custom_Status_Archived {
	/**
	 * Post Types for this status
	 * @var array
	 */
	protected $post_type = array();
	/**
	 * Status slug
	 * @var string
	 */
	protected $slug = '';
	/**
	 * Enable the button
	 * 'true', 'publish'	mixed		enables publishing from this status
	 * 'update'		string		enables updating from this status
	 * 'false'		boolean 	disabled/removes the button
	 * @var boolean
	 */
	protected $enable_action= false;
	/**
	 * Default definitons
	 * @var array
	 */
	protected $defaults = array(
		'label' => '',
		'public' => null,
		'protected' => null,
		'private' => null,
		'publicly_queryable' => null,
		'exclude_from_search' => true,
		'internal' => null,
		'show_in_admin_all_list' => true,
		'show_in_admin_status_list' => true,
		'label_count' => ''
		);
	/**
	 * Status settings
	 * @var array
	 */
	protected $settings = array();
	
  /**
   * Register the post status if everything that is required is set
   */
	public function __construct( $args = array() ) {
		if( empty( $args ) ) {
			return;
		}
		if( ! isset( $args['post_type'] ) || empty( $args['post_type'] ) ) {
			return;
		}
		if( ! isset( $args['slug'] ) || $args['slug'] == '' ) {
			return;
		}
		$this->post_type = $args['post_type'];
		$this->slug = $args['slug'];
		if( isset( $args['action'] ) && in_array( $args['action'], array( false, true, 'publish', 'update' ) ) ) {
			$this->enable_action = $args['action'];
		}
		if( ! isset( $args['label'] ) || $args['label'] == '' ) {
			$args['label'] = ucfirst( $args['slug'] );
		}
		if( ! isset( $args['label_count'] ) || $args['label_count'] == '' ) {
			$args['label_count'] = _n_noop( $args['label'] . ' <span class="count">(%s)</span>',  $args['label'] . ' <span class="count">(%s)</span>');
		}
		unset( $args['slug'] );
		unset( $args['post_type'] );
		unset( $args['action'] );
		$this->settings = wp_parse_args( $args, $this->defaults );
		add_action( 'init', array( $this, 'register_status' ) );
		add_action( 'admin_footer', array( $this, 'set_status' ) );
	}

	/**
	* Register Post Status using WordPress API
	*/
  	public function register_status() {
		register_post_status( $this->slug, $this->settings );
	}
	
  	public function set_status() {
  	
	  	$set_status = apply_filters( 'ibenic_custom_post_status_' . $this->slug, true );
	
		if( ! $set_status ) {
			return;
		}
		global $post;
	
		if($post){ // PE
			if( ! in_array( $post->post_type, $this->post_type ) ) {
				return;
			}
		
			$complete = '';
			$label = '';
		
			if( $post->post_status == $this->slug ) {
				$complete = ' selected=\"selected\"';
				$label = '<span id=\"post-status-display\">' . $this->settings['label'] . '</span>';
			}
			?>
			<script>
				( function($){
					$(document).ready(function(){
						<?php if( $post->post_status == $this->slug ) { ?>
							$( "span#post-status-display" ).append( "Archived" );
							$( "input#save-post" ).val("Save Archived");
						<?php } ?>
						
						$( "a.edit-post-status" ).click(function() {
							$( "a.save-post-status" ).click(function() {
								if($( "select#post_status option:selected" ).val() == 'archived' || ($( "select#post_status option:selected").text()  == 'Archived')){
								  $( "input#save-post" ).val("Save Archived");
								 }
							});
						});
						
						$('select#post_status').append( "<option value='<?php echo esc_attr($this->slug); ?>' <?php echo esc_attr($complete); ?>><?php echo esc_attr($this->settings['label']); ?></option>");
						$('.misc-pub-section label').append( "<?php echo esc_attr($label); ?>");
					});
				})( jQuery );
			</script>
		<?php
		} // PE
	}
}
new WordPress_Custom_Status_Archived( array(
	'post_type' => array( 'classified' ),
	'slug' => 'archived',
    'public' => $expired_ad_behaviour_val,
	'label' => _x( 'Archived', 'terraclassifieds' ),
	'action' => 'update',
	'label_count' => _n_noop( ' Archived <span class="count">(%s)</span>', ' Archived <span class="count">(%s)</span>' ),
));