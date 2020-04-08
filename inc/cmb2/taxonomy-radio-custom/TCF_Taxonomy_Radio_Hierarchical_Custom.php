<?php
/**
 * Handles 'taxonomy_radio_custom' custom field type.
 */
class TCF_Taxonomy_Radio_Hierarchical_Custom extends CMB2_Type_Taxonomy_Radio_Hierarchical {
	protected $term;
	protected $saved_term;
	/**
	 * Build children hierarchy.
	 *
	 * @param  object       $parent_term The parent term object.
	 * @param  array|string $saved       Array of terms set to the object, or single term slug.
	 *
	 * @return string                    List of terms.
	 */
	protected function build_children( $parent_term, $saved ) {
		if ( empty( $parent_term->term_id ) ) {
			return '';
		}
		$this->parent = $parent_term->term_id;
		$terms   = $this->get_terms();
		$options = '';
		if ( ! empty( $terms ) && is_array( $terms ) ) {
			// DJ - BEGIN
			$options = '<ul class="cmb2-indented-hierarchy">';
			$options .= $this->loop_terms( $terms, $saved );
			$options .= '</ul>';
			// DJ - END
		}
		return $options;
	}
	protected function list_term_input( $term, $saved_term ) {
		$this->term = $term;
		$this->saved_term = $saved_term;
		return parent::list_term_input( $term, $saved_term );
	}
	public function list_input( $args = array(), $i ) {
		if ( empty( $this->term ) ) {
			return parent::list_input( $args, $i );
		}
		$a = $this->parse_args( 'list_input', array(
			'type'  => 'radio',
			'class' => 'cmb2-option',
			'name'  => $this->_name(),
			'id'    => $this->_id( $i ),
			'value' => $this->field->escaped_value(),
			'label' => '',
		), $args );
		if($a['name'] == '_tc_category'){
			$taxonomy = 'ad_category';
			$terms = get_terms($taxonomy, array("hide_empty" => false)); // Get all terms of a taxonomy
			foreach ( $terms as $term ) {
				$cat = get_term_by('slug', $a['value'], $taxonomy);
				$image_url = '';
				$image = '';
				$is_parent = '';
				if(!empty($cat)){
					$cat_id = $cat->term_id;
					$level1 = $cat->parent;
					$image_url = get_term_meta( $cat_id, '_tc_cat_image', true );
					if(!empty($image_url) && $level1 == 0){
						$image = '<img src="'.$image_url.'" alt="'.$a['label'].'" />';
					} else {
						$image = '';
					}
					
					if(get_term_children( $cat_id, $taxonomy )){
						$is_parent = 'class="parent"';
						return sprintf( "\t" . '<li %s><span><input%s/><label for="%s">%s<span>%s</span></label></span>' . "\n", $is_parent, $this->concat_attrs( $a, array( 'label' ) ), $a['id'], $image, $a['label']);
					} else {
						return sprintf( "\t" . '<li %s><span><input%s/><label for="%s">%s<span>%s</span></label></span></li>' . "\n", $is_parent, $this->concat_attrs( $a, array( 'label' ) ), $a['id'], $image, $a['label']);
					}
				}
			}
		} else {
			return sprintf( "\t" . '<li><input%s/> <label for="%s">%s</label></li>' . "\n", $this->concat_attrs( $a, array( 'label' ) ), $a['id'], $a['label'] );
		}
	}
}