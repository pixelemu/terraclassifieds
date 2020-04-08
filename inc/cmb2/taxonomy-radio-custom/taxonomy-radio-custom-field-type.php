<?php
/**
 * Custom field type to add term images to the output of the taxonomy radio field.
 * See https://wordpress.org/support/topic/disable-cmb2-on-some-view/
 */
function terraclassifieds_taxonomy_radio_custom_field_type_class_name() {
	$class_name = 'TCF_Taxonomy_Radio_Hierarchical_Custom';
	require_once dirname( __FILE__ ) . '/'. $class_name .'.php';
	return $class_name;
}
function terraclassifieds_taxonomy_radio_custom_field_type_display_class_name() {
	$class_name = 'TCF_Taxonomy_Radio_Custom_Display';
	require_once dirname( __FILE__ ) . '/'. $class_name .'.php';
	return $class_name;
}
function terraclassifieds_add_taxonomy_radio_custom( $types ) {
	$types[] = 'taxonomy_radio_custom';
	return $types;
}
function terraclassifieds_taxonomy_radio_custom_sanitize( $check, $meta_value, $object_id, $field_args, $sanitize_object ) {
	$sanitized_value = $sanitize_object->taxonomy();
	return $sanitize_object->_is_empty_array( $sanitized_value ) ? '' : $sanitized_value;
}
function cmb2_init_taxonomy_radio_custom_field_type() {
	add_filter( 'cmb2_render_class_taxonomy_radio_custom', 'terraclassifieds_taxonomy_radio_custom_field_type_class_name' );
	add_filter( 'cmb2_display_class_taxonomy_radio_custom', 'terraclassifieds_taxonomy_radio_custom_field_type_display_class_name' );
	add_filter( 'cmb2_all_or_nothing_types', 'terraclassifieds_add_taxonomy_radio_custom' );
	add_filter( 'cmb2_non_repeatable_fields', 'terraclassifieds_add_taxonomy_radio_custom' );
	/**
	 * The following snippet is required for allowing the taxonomy_radio_custom field
	 * to save to the term fields.
	 */
	add_filter( 'cmb2_sanitize_taxonomy_radio_custom', 'terraclassifieds_taxonomy_radio_custom_sanitize', 10, 5 );
}
add_action( 'cmb2_init', 'cmb2_init_taxonomy_radio_custom_field_type', 100 );