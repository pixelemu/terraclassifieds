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
			$price_for_charging_ads = '';
			$charing_for_add_ads = terraclassifieds_get_option('_tc_monetizing_charging_for_adding_ads_price','free');
			$currency = terraclassifieds_get_option( '_tc_advert_currency', '$' );
			$unit_position = (int) terraclassifieds_get_option( '_tc_unit_position', 1 );
			if ($charing_for_add_ads === 'fixed') {
				$price_for_charging_ads = floatval(terraclassifieds_get_option('_tc_monetizing_charging_for_adding_ads_price_fixed','0'));
				if (intval($price_for_charging_ads) == 0) {
					$price_for_charging_ads_text = ' ('.__('Free', 'terraclassifieds').')';
				}else{
					if (!$unit_position) {
						$price_for_charging_ads_text = ' ('.$currency.' '.terraclassifiedsPriceFormat($price_for_charging_ads,1).')';
					}else{
						$price_for_charging_ads_text = ' ('.terraclassifiedsPriceFormat($price_for_charging_ads,1).' '.$currency.')';
					}
				}
			}elseif($charing_for_add_ads === 'per_category') {
				$terms = get_terms(array('taxonomy' => $taxonomy,'hide_empty' => false, 'parent' => 0));
				if (!empty($terms) && is_array($terms)) {
					$prices_for_charging_ads = array();
					foreach($terms as $key=>$term) {
						$prices_for_charging_ads[$term->term_id] = floatval(terraclassifieds_get_option('_tc_monetizing_charging_for_adding_ads_price_per_category_charging_price_'.$term->term_id,'0'));
						if(get_term_children( $term->term_id, $taxonomy)){
							$term_children = get_term_children( $term->term_id, $taxonomy);
							if (!empty($term_children) && is_array($term_children)) {
								foreach($term_children as $child_key=>$child_item) {
									$parent_name = $term->name;
									$child_term = get_term($child_item, $taxonomy);
									$prices_for_charging_ads[$child_term->term_id] = floatval(terraclassifieds_get_option('_tc_monetizing_charging_for_adding_ads_price_per_category_charging_price_'.$child_term->term_id,'0'));
								}
							}
						}
					}
				}
			}
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
						if($charing_for_add_ads === 'per_category') {
							$price_for_charging_ads = $prices_for_charging_ads[$this->term->term_id];
							if (intval($price_for_charging_ads) == 0) {
								$price_for_charging_ads_text = ' ('.__('Free', 'terraclassifieds').')';
							}else{
								if (!$unit_position) {
									$price_for_charging_ads_text = ' ('.$currency.' '.terraclassifiedsPriceFormat($price_for_charging_ads,1).')';
								}else{
									$price_for_charging_ads_text = ' ('.terraclassifiedsPriceFormat($price_for_charging_ads,1).' '.$currency.')';
								}
							}
						}
						if (!is_admin()) {
							return sprintf( "\t" . '<li %s><span><input%s/><label for="%s">%s<span data-category="%s" data-id-category="%s" data-category-price="%s">%s</span></label></span>' . "\n", $is_parent, $this->concat_attrs( $a, array( 'label' ) ), $a['id'], $image, $a['label'], $this->term->term_id, $price_for_charging_ads, $a['label'].'<br/>'.$price_for_charging_ads_text);
						}else{
							return sprintf( "\t" . '<li %s><span><input%s/><label for="%s">%s<span data-category="%s" data-id-category="%s" data-category-price="%s">%s</span></label></span>' . "\n", $is_parent, $this->concat_attrs( $a, array( 'label' ) ), $a['id'], $image, $a['label'], $this->term->term_id, $price_for_charging_ads, $a['label'].$price_for_charging_ads_text);
						}
					} else {
						if($charing_for_add_ads === 'per_category') {
							$price_for_charging_ads = $prices_for_charging_ads[$this->term->term_id];
							if (intval($price_for_charging_ads) == 0) {
								$price_for_charging_ads_text = ' ('.__('Free', 'terraclassifieds').')';
							}else{
								if (!$unit_position) {
									$price_for_charging_ads_text = ' ('.$currency.' '.terraclassifiedsPriceFormat($price_for_charging_ads,1).')';
								}else{
									$price_for_charging_ads_text = ' ('.terraclassifiedsPriceFormat($price_for_charging_ads,1).' '.$currency.')';
								}
							}
						}
						if (!is_admin()) {
							return sprintf( "\t" . '<li %s><span><input%s/><label for="%s">%s<span data-category="%s" data-id-category="%s" data-category-price="%s">%s</span></label></span></li>' . "\n", $is_parent, $this->concat_attrs( $a, array( 'label' ) ), $a['id'], $image, $a['label'], $this->term->term_id, $price_for_charging_ads, $a['label'].'<br/>'.$price_for_charging_ads_text);
						}else{
							return sprintf( "\t" . '<li %s><span><input%s/><label for="%s">%s<span data-category="%s" data-id-category="%s" data-category-price="%s">%s</span></label></span></li>' . "\n", $is_parent, $this->concat_attrs( $a, array( 'label' ) ), $a['id'], $image, $a['label'], $this->term->term_id, $price_for_charging_ads, $a['label'].$price_for_charging_ads_text);
						}
					}
				}
			}
		} else {
			return sprintf( "\t" . '<li><input%s/> <label for="%s">%s</label></li>' . "\n", $this->concat_attrs( $a, array( 'label' ) ), $a['id'], $a['label'] );
		}
	}
}