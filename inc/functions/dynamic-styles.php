<?php 
define('WP_USE_THEMES', false);
require('../../../../../wp-blog-header.php');
header("Content-type: text/css");
$terraclassifieds_types = get_terms( 'ad_type', array(
									    'hide_empty' => true,
									) );
foreach($terraclassifieds_types as $terraclassifieds_type) {
	$type_text_color = get_term_meta( $terraclassifieds_type->term_id, '_tc_type_text_color', true);
	$type_background_color = get_term_meta( $terraclassifieds_type->term_id, '_tc_type_background_color', true);
	$type_border_color = get_term_meta( $terraclassifieds_type->term_id, '_tc_type_border_color', true);
	$type_border_size = get_term_meta( $terraclassifieds_type->term_id, '_tc_type_border_size', true);
	echo '
	.terraclassifieds-type-' . $terraclassifieds_type->slug . '{
		color: ' . $type_text_color . ';
		background: ' . $type_background_color . ';
		border-color: ' . $type_border_color . ';
		border-width: ' . $type_border_size . 'px;
	}
	';
}
