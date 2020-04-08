<?php
$terms_and_conditions = terraclassifieds_get_option( '_tc_terms_and_conditions', 0 );
$terms_and_conditions_page = terraclassifieds_get_option( '_tc_terms_and_conditions_page', 0 );
$privacy_policy = terraclassifieds_get_option( '_tc_privacy_policy', 0 );
$privacy_policy_page = terraclassifieds_get_option( '_tc_privacy_policy_page', 0 );
$gdpr = terraclassifieds_get_option( '_tc_gdpr', 0 );
$gdpr_information = terraclassifieds_get_option( '_tc_gdpr_information', 0 );

if($terms_and_conditions != 0 && !empty($terms_and_conditions_page)){ ?>
<p class="agreement-terms-and-conditions">
	<input type="checkbox" name="tc_terms_and_conditions_abuse" id="tc_terms_and_conditions_abuse" value="0" class="inputbox">
	<label class="label_terms_abuse" for="tc_terms_and_conditions_abuse" id="tc_terms_and_conditions_abuse-lbl"><?php esc_html_e( 'I agree to the', 'terraclassifieds' ) ?>
		<?php if($terms_and_conditions == 1){ ?>
			<a href="<?php echo get_page_link($terms_and_conditions_page); ?>" target="_blank"><?php esc_html_e( 'Terms and conditions', 'terraclassifieds' ) ?></a> *
		<?php } else if($terms_and_conditions == 2){ ?>
			<?php
				$terms_and_conditions_page_array = get_post( $terms_and_conditions_page );
				$terms_and_conditions_page_content = apply_filters('the_content', $terms_and_conditions_page_array->post_content); 
			?>
			<a id="tcf-modal-terms-and-conditions-abuse" href="#"><?php esc_html_e( 'Terms and conditions', 'terraclassifieds' ) ?></a> *
		<?php } ?>
	</label>
</p>
<?php }
if($terms_and_conditions == 2){ ?>
	<div id="terms-and-conditions-modal-abuse" class="modal">
		<div class="modal-content">
		  <div class="modal-header">
		    <span class="tcf-modal-terms-and-conditions-close-abuse close">&times;</span>
		  </div>
		  <div class="modal-body">
			<?php echo $terms_and_conditions_page_content; ?>
		  </div>
		</div>
	</div>
<?php }

if($privacy_policy != 0 && !empty($privacy_policy_page)){ ?>
<p class="agreement-privacy-policy">
	<input type="checkbox" name="tc_privacy_policy_abuse" id="tc_privacy_policy_abuse" value="0" class="inputbox">
	<label class="label_privacy_policy_abuse" for="tc_privacy_policy_abuse" id="tc_privacy_policy_abuse-lbl"><?php esc_html_e( 'I agree to the', 'terraclassifieds' ) ?>
		<?php if($privacy_policy == 1){ ?>
			<a href="<?php echo get_page_link($privacy_policy_page); ?>" target="_blank"><?php esc_html_e( 'Privacy Policy', 'terraclassifieds' ) ?></a> *
		<?php } else if($privacy_policy == 2){ ?>
			<?php
				$privacy_policy_page_array = get_post( $privacy_policy_page );
				$privacy_policy_page_content = apply_filters('the_content', $privacy_policy_page_array->post_content); 
			?>
			<a id="tcf-modal-privacy-policy-abuse" href="#"><?php esc_html_e( 'Privacy Policy', 'terraclassifieds' ) ?></a> *
		<?php } ?>
	</label>
</p>
<?php }
if($privacy_policy == 2){ ?>
	<div id="privacy-policy-modal-abuse" class="modal">
		<div class="modal-content">
		  <div class="modal-header">
		    <span class="tcf-modal-privacy-policy-close-abuse close">&times;</span>
		  </div>
		  <div class="modal-body">
			<?php echo $privacy_policy_page_content; ?>
		  </div>
		</div>
	</div>
<?php }

if($gdpr != 0 && !empty($gdpr_information)){ ?>
<p class="agreement-gdpr">
	<input type="checkbox" name="tc_gdpr_abuse" id="tc_gdpr_abuse" value="0" class="inputbox">
	<label class="label_gdpr_abuse" for="tc_gdpr_abuse" id="tc_gdpr_abuse-lbl"><?php echo esc_html($gdpr_information); ?> *</label>
</p>
<?php } 
?>