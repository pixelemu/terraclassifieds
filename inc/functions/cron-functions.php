<?php
if ( ! class_exists( 'CronTerraclassifieds' ) ) {
	class CronTerraclassifieds {
		
		function __construct() {
			register_activation_hook(realpath(__DIR__ . '/../../terraclassifieds.php'), array($this,'terraclassifieds_cron_change_status_func'));
			register_activation_hook(realpath(__DIR__ . '/../../terraclassifieds.php'), array($this,'terraclassifieds_cron_archive_ads_soon_notification_func'));
			register_deactivation_hook(realpath(__DIR__ . '/../../terraclassifieds.php'), array($this,'terraclassifieds_cron_change_status_func_deactivate'));
			register_deactivation_hook(realpath(__DIR__ . '/../../terraclassifieds.php'), array($this,'terraclassifieds_cron_archive_ads_soon_notification_func_deactivate'));
			add_filter( 'cron_schedules', array($this,'terraclassifieds_custom_cron' ));
			add_action('terraclassifieds_cron_archive_ads_soon_notification', array($this,'terraclassifieds_archive_ads_soon_notification'));
			add_action('terraclassifieds_cron_change_status', array($this,'terraclassifieds_archive_ads'));
		}
		
		// custom cron duration
		public function terraclassifieds_custom_cron( $schedules ) {
		 
		    $schedules['every_15_seconds'] = array( // test value for cron - just 15 seconds
		            'interval'  => 5,
		            'display'   => __( 'Every 15 seconds', 'terraclasifieds' )
		    );
		     
		    $schedules['every_3611_seconds'] = array( // custom cron value - a little bit more than 1 hour to prevent conflict between 2 actions trigerred at the same time
		            'interval'  => 3611,
		            'display'   => __( 'Every 3611 seconds', 'terraclasifieds' )
		    );
			
		    return $schedules;
		}
		
		// archive old ads
		public function terraclassifieds_archive_ads() {
			$expire_time = terraclassifieds_get_option( '_tc_advert_expire_time', 30 );
			$args = array(
			    'post_type' => 'classified',
			    'posts_per_page' => -1,
			    'meta_query' => array(
			        array(
			            'key' => '_tc_expire_date',
			            'value' => current_time('timestamp'),
			            'compare' => '<'
			        )
			    ),
			    'meta_type' => 'text_date_timestamp',
			);
			$posts = new WP_Query( $args );
			// The Loop
			if ( $posts>have_posts() ) {
			
			    while ( $posts->have_posts() ) {
			        $posts->the_post();
			        if(get_post_meta( get_the_ID(), '_tc_expire_date', true ) != ''){
				        update_post_meta( get_the_ID(), '_tc_expire_date', '' ); // clear expire date
				        $post = array( 'ID' => get_the_ID(), 'post_status' => 'archived' );
				        wp_update_post($post); // change post status
				        update_post_meta( get_the_ID(), '_tc_expire_soon_notification_done', false );
			        }
			    }
			
			}
			/* Restore original Post Data */
			wp_reset_postdata();
		}
		
		// add wp cron job
		public function terraclassifieds_cron_change_status_func() {
		    if (! wp_next_scheduled ( 'terraclassifieds_cron_change_status' )) {
				wp_schedule_event(time(), 'every_3611_seconds', 'terraclassifieds_cron_change_status');
		    }
		}
		
		// remove wp cron job
		public function terraclassifieds_cron_change_status_func_deactivate() {
			wp_clear_scheduled_hook('terraclassifieds_cron_change_status');
		}
		
		// notification when ad will expired soon
		public function terraclassifieds_archive_ads_soon_notification() {
			$email_template_expiration_notification_number_of_days = terraclassifieds_get_option( '_tc_advert_expire_time_before_notification', 3 );
			$date = new DateTime( get_the_date('m/j/Y') );
			$date->modify( "+".$email_template_expiration_notification_number_of_days." day" );
			$date = $date->format('U');
	
			$args = array(
			    'post_type' => 'classified',
			    'posts_per_page' => -1,
			    'meta_query' => array(
			        array(
			            'key' => '_tc_expire_date',
			            'value' => $date,
			            'compare' => '<'
			        )
			    ),
			    'meta_type' => 'text_date_timestamp',
			);
			$posts = new WP_Query( $args );
			// The Loop
			if ( $posts->have_posts() ) {
			
			    while ( $posts->have_posts() ) {
			        $posts->the_post();
					$expire_notification_done = get_post_meta( get_the_ID(), '_tc_expire_soon_notification_done', false);
					if(!$expire_notification_done){
						$this->terraclassifieds_sendmail_when_advert_archived_soon(get_the_ID()); // send notification
						update_post_meta( get_the_ID(), '_tc_expire_soon_notification_done', true );
					}
			    }
			}
			/* Restore original Post Data */
			wp_reset_postdata();
		}
		
		// send email with info that advert will be archived soon
		private function terraclassifieds_sendmail_when_advert_archived_soon($pid = '') {
			$email_template_expiration_notification_number_of_days = terraclassifieds_get_option( '_tc_advert_expire_time_before_notification', 3 );
			$email_template_expiration_notification_subject = terraclassifieds_get_option( '_tc_email_template_expiration_notification_subject', '' );
			$email_template_expiration_notification_message = terraclassifieds_get_option( '_tc_email_template_expiration_notification_message', '' );
	
			$classified_id = get_the_ID();
			if(!empty(terraclassifieds_get_option( '_tc_email_template_reply_to_email_address', '' ))){
				$reply_to_email_address = terraclassifieds_get_option( '_tc_email_template_reply_to_email_address', '' );
			} else {
				$reply_to_email_address = get_option( 'admin_email' );
			}
			
			$headers = array("Content-Type: text/html; charset=UTF-8", "Reply-To: " . $reply_to_email_address);
			$advert_link = get_permalink($classified_id);
			
		    $author_email = get_the_author_meta('user_email');

		    $subject = $email_template_expiration_notification_subject;
			$subject = str_replace("[[advert_title_link]]", $advert_link, $subject);
			$subject = str_replace("[[advert_expire_days]]", $email_template_expiration_notification_number_of_days, $subject);
		
			$message = $email_template_expiration_notification_message;
			$message = str_replace("[[advert_title_link]]", $advert_link, $message);
			$message = str_replace("[[advert_expire_days]]", $email_template_expiration_notification_number_of_days, $message);

			$sent = wp_mail($author_email, $subject, $message, $headers);
		}
		
		// add wp cron job
		public function terraclassifieds_cron_archive_ads_soon_notification_func() {
		    if (! wp_next_scheduled ( 'terraclassifieds_cron_archive_ads_soon_notification' )) {
				wp_schedule_event(time(), 'hourly', 'terraclassifieds_cron_archive_ads_soon_notification');
		    }
		}
		
		// remove wp cron job
		public function terraclassifieds_cron_archive_ads_soon_notification_func_deactivate() {
			wp_clear_scheduled_hook('terraclassifieds_cron_archive_ads_soon_notification');
		}

		/*public function isa_test_cron_job_send_mail() {
		    $to = 'test@gmail.com';
		    $subject = 'Test my 3-minute cron job';
		    $message = 'If you received this message, it means that your 3-minute cron job has worked! :) ';
		 
		    wp_mail( $to, $subject, $message );
		 
		}*/
	}
}