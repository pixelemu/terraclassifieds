<?php
/*--------------------------------------------------------------
 Copyright (C) pixelemu.com
 License: https://www.pixelemu.com/company/license PixelEmu Proprietary Use License
 Website: https://www.pixelemu.com
 Support: info@pixelemu.com
---------------------------------------------------------------*/

if ( ! class_exists( 'TerraClassifiedsMenu' ) ) {

	class TerraClassifiedsMenu extends WP_Widget {

		function __construct() {
			$widget_ops = array(
				'classname'   => 'terraclassifieds-menu',
				'description' => __( 'Show Terraclassifieds menu.', 'terraclassifieds' )
			);
			parent::__construct( 'TerraClassifiedsMenu', __( 'TerraClassifieds Menu', 'terraclassifieds' ), $widget_ops );
		}

		function widget( $args, $instance ) {

			extract( $args );

			$title = ( ! empty( $instance['title'] ) ) ? apply_filters( 'widget_title', $instance['title'] ) : false;
			$add_to_favourites    = ( ! empty( $instance['add_to_favourites'] ) ) ? true : false;

			echo $before_widget;

			if ( $title ):
				echo $before_title;
				echo $title;
				echo $after_title;
			endif; 
			
			$page_login_slug = terraclassifieds_get_option( '_tc_slug_login', 'login' );
			$page_login = get_page_link( get_page_by_path( $page_login_slug ) );
			$page_add_advert_slug = terraclassifieds_get_option( '_tc_slug_add_advert', 'add-advert' );
			$page_add_advert = get_page_link( get_page_by_path( $page_add_advert_slug ) );
			$page_my_submissions_slug = terraclassifieds_get_option( '_tc_slug_my_submissions', 'my-submissions' );
			$page_my_submissions = get_page_link( get_page_by_path( $page_my_submissions_slug ) );
			$page_ed_profile_slug = terraclassifieds_get_option( '_tc_slug_edit_profile', 'edit-profile' );
			$page_ed_profile = get_page_link( get_page_by_path( $page_ed_profile_slug ) );
			$page_favourite_ads_slug = terraclassifieds_get_option( '_tc_slug_favourite_ads', 'favourite-ads' );
			$page_favourite_ads = get_page_link( get_page_by_path( $page_favourite_ads_slug ) );
			?>

			<ul class="nav-menu terraclassifieds-usermenu">
				<li class="menu-item my-account">
					<?php if ( is_user_logged_in() ) { ?>
						<?php $current_user = wp_get_current_user(); ?>
							<span><?php echo esc_attr($current_user->user_login); ?></span>
					<?php } else { ?>
						<a href="<?php echo esc_url($page_login); ?>"><?php _e('My account', 'terraclassifieds'); ?></a>
					<?php } ?>
					<div class="nav-dropdown">
						<ul class="nav-dropdown-in main-menu">
							<li class="menu-item menu-separator">
								<span><?php _e('Classifieds', 'terraclassifieds'); ?></span>
							</li>
							<li class="menu-item">
								<a href="<?php echo esc_url($page_add_advert); ?>"><?php _e('Add new ad', 'terraclassifieds'); ?></a>
							</li>
							<li class="menu-item">
								<a href="<?php echo esc_url($page_my_submissions); ?>"><?php _e('Your adverts', 'terraclassifieds'); ?></a>
							</li>
							<li class="menu-item menu-separator">
								<span><?php _e('Settings', 'terraclassifieds'); ?></span>
							</li>
							<li class="menu-item">
								<a href="<?php echo esc_url($page_ed_profile); ?>"><?php _e('Edit profile', 'terraclassifieds'); ?></a>
							</li>
							<?php if($add_to_favourites){ ?>
								<li class="menu-item">
									<a href="<?php echo esc_url($page_favourite_ads); ?>"><?php _e('Your favorite ads', 'terraclassifieds'); ?></a>
								</li>
							<?php } ?>
							<?php if ( is_user_logged_in() ) { ?>
								<li class="menu-item">
									<a href="<?php echo wp_logout_url( get_permalink() ); ?>"><?php _e('Logout', 'terraclassifieds'); ?></a>
								</li>
							<?php } ?>
						</ul>
					</div>
				</li>
			</ul>


			<?php echo $after_widget;
		}

		function form( $instance ) {

			$instance = wp_parse_args( (array) $instance, array(
					'title'         => '',
					'add_to_favourites'    => 0,
				)
			);

			$title         = esc_attr( $instance['title'] );
			$add_to_favourites = isset( $instance['add_to_favourites'] ) ? (bool) $instance['add_to_favourites'] : false;

			?>

			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Widget Title', 'terraclassifieds' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr($title); ?>"/>
			</p>
			
			<p>
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('add_to_favourites'); ?>" name="<?php echo $this->get_field_name('add_to_favourites'); ?>"<?php checked( $add_to_favourites ); ?> />
				<label for="<?php echo $this->get_field_id('add_to_favourites'); ?>"><?php _e( 'Show add to favourites', 'terraclassifieds' ); ?></label><br/>
			</p>

		<?php }

		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;

			$instance['title']         = strip_tags( $new_instance['title'] );
			$instance['add_to_favourites']    = esc_attr( $new_instance['add_to_favourites'] );
			
			return $instance;
		}

	}
}
?>
