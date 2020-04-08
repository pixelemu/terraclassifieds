<?php
/*--------------------------------------------------------------
 Copyright (C) pixelemu.com
 License: https://www.pixelemu.com/company/license PixelEmu Proprietary Use License
 Website: https://www.pixelemu.com
 Support: info@pixelemu.com
---------------------------------------------------------------*/

if ( ! class_exists( 'TerraClassifiedsCategories' ) ) {
	
	class TerraClassifiedsCategories extends WP_Widget {
	
		function __construct() {
			$widget_ops = array(
				'classname' => 'terraclassifieds-categories',
				'description' => __( 'A list or dropdown of ad categories.', 'terraclassifieds' ),
				'customize_selective_refresh' => true,
			);
			parent::__construct( 'TerraClassifiedsCategories', __( 'TerraClassifieds Categories', 'terraclassifieds' ), $widget_ops );
		}

		function widget( $args, $instance ) {
			static $first_dropdown = true;
	
			$title = ( ! empty( $instance['title'] ) ) ? apply_filters( 'widget_title', $instance['title'] ) : false;
	
			$c = ! empty( $instance['count'] ) ? '1' : '0';
			$h = ! empty( $instance['hierarchical'] ) ? '1' : '0';
			$images = ! empty( $instance['images'] ) ? '1' : '0';
			$empty_categories = ! empty( $instance['empty_categories'] ) ? '0' : '1';
			$layout = ! empty( $instance['layout'] ) ? '1' : '0';
			$columns = ! empty( $instance['columns'] ) ? (int)$instance['columns'] : '1';
			$depth = ! empty( $instance['depth'] ) ? (int)$instance['depth'] : '0';
	
			echo $args['before_widget'];
	
			if ( $title ) {
				echo $args['before_title'] . $title . $args['after_title'];
			}
	
			$cat_args = array(
				'taxonomy'            => 'ad_category',
				'title_li'            => '',
				'show_count'   => $c,
				'hierarchical' => $h,
				'show_images'  => $images,
				'walker'=> new TerraClassifieds_Categories_Walker,
				'hide_empty' => $empty_categories,
				'depth' => $depth,
			); 

			$layout_class = ( $layout == 0 ) ? 'horizontal' : 'vertical';

			//columns only for vertical layout
			if( $layout == 1 ) {
				switch ($columns) {
					case 1:
						$columns_class = 'columns-1';
						break;
					case 2:
						$columns_class = 'columns-2';
						break;
					case 3:
						$columns_class = 'columns-3';
						break;
					case 4:
						$columns_class = 'columns-4';
						break;
					case 5:
						$columns_class = 'columns-5';
						break;
					case 6:
						$columns_class = 'columns-6';
						break;
					case 7:
						$columns_class = 'columns-7';
						break;
					case 8:
						$columns_class = 'columns-8';
						break;
					default:
						$columns_class = 'columns-1';
				}
			} else {
				$columns_class = '';
			}

			
			?>
			
			<ul class="terraclassifieds-categories-list <?php echo esc_attr($layout_class) . ' ' . esc_attr($columns_class); ?>">
				<?php wp_list_categories( apply_filters( 'widget_categories_args', $cat_args, $instance ) ); ?>
			</ul>
	<?php
			echo $args['after_widget'];
		}
	
		function form( $instance ) {
			//Defaults
			$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
			
			$title        = sanitize_text_field( $instance['title'] );
			$count        = isset( $instance['count'] ) ? (bool) $instance['count'] :false;
			$hierarchical = isset( $instance['hierarchical'] ) ? (bool) $instance['hierarchical'] : false;
			$images       = isset( $instance['images'] ) ? (bool) $instance['images'] : false;
			$empty_categories       = isset( $instance['empty_categories'] ) ? (bool) $instance['empty_categories'] : false;
			$layout       = isset( $instance['layout'] ) ? (bool) $instance['layout'] : false;
			$columns      = ! empty( $instance['columns'] ) ? (int)$instance['columns'] : '1';
			$depth      = ! empty( $instance['depth'] ) ? (int)$instance['depth'] : '0';

			?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', 'terraclassifieds' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
	
			<p>
				<label for="<?php echo $this->get_field_id('layout'); ?>"><?php _e('Layout:', 'terraclassifieds'); ?></label>
				<select name="<?php echo $this->get_field_name('layout'); ?>" id="<?php echo $this->get_field_id('layout'); ?>">
					<option value="0"<?php selected( $layout, '0' ); ?>><?php _e('Horizontal', 'terraclassifieds'); ?></option>
					<option value="1"<?php selected( $layout, '1' ); ?>><?php _e('Vertical', 'terraclassifieds'); ?></option>
				</select>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('columns'); ?>"><?php _e('Columns (only vertical layout):', 'terraclassifieds'); ?></label>
				<select name="<?php echo $this->get_field_name('columns'); ?>" id="<?php echo $this->get_field_id('columns'); ?>">
					<option value="1"<?php selected( $columns, '1' ); ?>><?php _e('1 column', 'terraclassifieds'); ?></option>
					<option value="2"<?php selected( $columns, '2' ); ?>><?php _e('2 columns', 'terraclassifieds'); ?></option>
					<option value="3"<?php selected( $columns, '3' ); ?>><?php _e('3 columns', 'terraclassifieds'); ?></option>
					<option value="4"<?php selected( $columns, '4' ); ?>><?php _e('4 columns', 'terraclassifieds'); ?></option>
					<option value="5"<?php selected( $columns, '5' ); ?>><?php _e('5 columns', 'terraclassifieds'); ?></option>
					<option value="6"<?php selected( $columns, '6' ); ?>><?php _e('6 columns', 'terraclassifieds'); ?></option>
					<option value="7"<?php selected( $columns, '7' ); ?>><?php _e('7 columns', 'terraclassifieds'); ?></option>
					<option value="8"<?php selected( $columns, '8' ); ?>><?php _e('8 columns', 'terraclassifieds'); ?></option>
				</select>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('depth'); ?>"><?php _e('Depth:', 'terraclassifieds'); ?></label>
				<select name="<?php echo $this->get_field_name('depth'); ?>" id="<?php echo $this->get_field_id('depth'); ?>">
					<option value="0"<?php selected( $depth, '0' ); ?>><?php _e('All', 'terraclassifieds'); ?></option>
					<option value="1"<?php selected( $depth, '1' ); ?>><?php _e('1', 'terraclassifieds'); ?></option>
					<option value="2"<?php selected( $depth, '2' ); ?>><?php _e('2', 'terraclassifieds'); ?></option>
					<option value="3"<?php selected( $depth, '3' ); ?>><?php _e('3', 'terraclassifieds'); ?></option>
					<option value="4"<?php selected( $depth, '4' ); ?>><?php _e('4', 'terraclassifieds'); ?></option>
					<option value="5"<?php selected( $depth, '5' ); ?>><?php _e('5', 'terraclassifieds'); ?></option>
					<option value="6"<?php selected( $depth, '6' ); ?>><?php _e('6', 'terraclassifieds'); ?></option>
					<option value="7"<?php selected( $depth, '7' ); ?>><?php _e('7', 'terraclassifieds'); ?></option>
					<option value="8"<?php selected( $depth, '8' ); ?>><?php _e('8', 'terraclassifieds'); ?></option>
					<option value="9"<?php selected( $depth, '9' ); ?>><?php _e('9', 'terraclassifieds'); ?></option>
				</select>
			</p>
				
			<p>
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>"<?php checked( $count ); ?> />
				<label for="<?php echo $this->get_field_id('count'); ?>"><?php _e( 'Show post counts', 'terraclassifieds' ); ?></label><br/>
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('hierarchical'); ?>" name="<?php echo $this->get_field_name('hierarchical'); ?>"<?php checked( $hierarchical ); ?> />
				<label for="<?php echo $this->get_field_id('hierarchical'); ?>"><?php _e( 'Show hierarchy', 'terraclassifieds' ); ?></label><br/>
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('images'); ?>" name="<?php echo $this->get_field_name('images'); ?>"<?php checked( $images ); ?> />
				<label for="<?php echo $this->get_field_id('images'); ?>"><?php _e( 'Show images', 'terraclassifieds' ); ?></label><br/>
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('empty_categories'); ?>" name="<?php echo $this->get_field_name('empty_categories'); ?>"<?php checked( $empty_categories ); ?> />
				<label for="<?php echo $this->get_field_id('empty_categories'); ?>"><?php _e( 'Show empty categories', 'terraclassifieds' ); ?></label><br/>
			</p>
			<?php
		}

		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['title']        = strip_tags( $new_instance['title'] );
			$instance['count']        = esc_attr( $new_instance['count'] );
			$instance['hierarchical'] = esc_attr( $new_instance['hierarchical'] );
			$instance['images']       = esc_attr( $new_instance['images'] );
			$instance['empty_categories']       = esc_attr( $new_instance['empty_categories'] );
			$instance['layout']       = esc_attr( $new_instance['layout'] );
			$instance['columns']      = esc_attr( $new_instance['columns'] );
			$instance['depth']       = esc_attr( $new_instance['depth'] );
			
			return $instance;
		}
	
	}

}

//https://developer.wordpress.org/reference/functions/attachment_url_to_postid/

if( ! class_exists('TerraClassifieds_Categories_Walker') ) {
	class TerraClassifieds_Categories_Walker extends Walker_Category {

		/**
		 * What the class handles.
		 *
		 * @since 2.1.0
		 * @var string
		 *
		 * @see Walker::$tree_type
		 */
		public $tree_type = 'category';

		/**
		 * Database fields to use.
		 *
		 * @since 2.1.0
		 * @var array
		 *
		 * @see Walker::$db_fields
		 * @todo Decouple this
		 */
		public $db_fields = array ('parent' => 'parent', 'id' => 'term_id');

		/**
		 * Starts the list before the elements are added.
		 *
		 * @since 2.1.0
		 *
		 * @see Walker::start_lvl()
		 *
		 * @param string $output Used to append additional content. Passed by reference.
		 * @param int    $depth  Optional. Depth of category. Used for tab indentation. Default 0.
		 * @param array  $args   Optional. An array of arguments. Will only append content if style argument
		 *                       value is 'list'. See wp_list_categories(). Default empty array.
		 */
		public function start_lvl( &$output, $depth = 0, $args = array() ) {
			if ( 'list' != $args['style'] )
				return;

			$indent = str_repeat("\t", $depth);
			$output .= "$indent<ul class='children'>\n";
		}

		/**
		 * Ends the list of after the elements are added.
		 *
		 * @since 2.1.0
		 *
		 * @see Walker::end_lvl()
		 *
		 * @param string $output Used to append additional content. Passed by reference.
		 * @param int    $depth  Optional. Depth of category. Used for tab indentation. Default 0.
		 * @param array  $args   Optional. An array of arguments. Will only append content if style argument
		 *                       value is 'list'. See wp_list_categories(). Default empty array.
		 */
		public function end_lvl( &$output, $depth = 0, $args = array() ) {
			if ( 'list' != $args['style'] )
				return;

			$indent = str_repeat("\t", $depth);
			$output .= "$indent</ul>\n";
		}

		/**
		 * Starts the element output.
		 *
		 * @since 2.1.0
		 *
		 * @see Walker::start_el()
		 *
		 * @param string $output   Used to append additional content (passed by reference).
		 * @param object $category Category data object.
		 * @param int    $depth    Optional. Depth of category in reference to parents. Default 0.
		 * @param array  $args     Optional. An array of arguments. See wp_list_categories(). Default empty array.
		 * @param int    $id       Optional. ID of the current category. Default 0.
		 */
		public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
			/** This filter is documented in wp-includes/category-template.php */
			$cat_name = apply_filters(
				'list_cats',
				esc_attr( $category->name ),
				$category
			);

			// Don't generate an element if the category name is empty.
			if ( ! $cat_name ) {
				return;
			}

			$image = get_term_meta( $category->term_id, '_tc_cat_image', true );
			$image_class = ( !empty(($image)) ) ? 'cat-has-image' : '';

			$link = '<a href="' . esc_url( get_term_link( $category ) ) . '" ';
			if ( $args['use_desc_for_title'] && ! empty( $category->description ) ) {
				/**
				 * Filters the category description for display.
				 *
				 * @since 1.2.0
				 *
				 * @param string $description Category description.
				 * @param object $category    Category object.
				 */
				$link .= 'title="' . esc_attr( strip_tags( apply_filters( 'category_description', $category->description, $category ) ) ) . '"';
			}

			$link .= '>';

			if( $args['show_images'] ) {

				if( !empty($image) ) {
					$link .= '<span class="cat-image"><img src="' . esc_url($image) .  '" alt="' . $cat_name . '"></span>';
				}

			}


			$counter = ( ! empty( $args['show_count'] ) ) ? ' (' . number_format_i18n( $category->count ) . ')' : '';

			$link .= '<span class="cat-name">'. $cat_name . $counter . '</span></a>';

			if ( ! empty( $args['feed_image'] ) || ! empty( $args['feed'] ) ) {
				$link .= ' ';

				if ( empty( $args['feed_image'] ) ) {
					$link .= '(';
				}

				$link .= '<a href="' . esc_url( get_term_feed_link( $category->term_id, $category->taxonomy, $args['feed_type'] ) ) . '"';

				if ( empty( $args['feed'] ) ) {
					$alt = ' alt="' . sprintf(__( 'Feed for all posts filed under %s' ), $cat_name ) . '"';
				} else {
					$alt = ' alt="' . $args['feed'] . '"';
					$name = $args['feed'];
					$link .= empty( $args['title'] ) ? '' : $args['title'];
				}

				$link .= '>';

				if ( empty( $args['feed_image'] ) ) {
					$link .= $name;
				} else {
					$link .= "<img src='" . $args['feed_image'] . "'$alt" . ' />';
				}
				$link .= '</a>';

				if ( empty( $args['feed_image'] ) ) {
					$link .= ')';
				}
			}

			
			if ( 'list' == $args['style'] ) {
				$output .= "\t<li";
				$css_classes = array(
					'cat-item',
					'cat-item-' . $category->term_id,
				);

				if ( ! empty( $args['current_category'] ) ) {
					// 'current_category' can be an array, so we use `get_terms()`.
					$_current_terms = get_terms( $category->taxonomy, array(
						'include' => $args['current_category'],
						'hide_empty' => false,
					) );

					foreach ( $_current_terms as $_current_term ) {
						if ( $category->term_id == $_current_term->term_id ) {
							$css_classes[] = 'current-cat';
						} elseif ( $category->term_id == $_current_term->parent ) {
							$css_classes[] = 'current-cat-parent';
						}
						while ( $_current_term->parent ) {
							if ( $category->term_id == $_current_term->parent ) {
								$css_classes[] =  'current-cat-ancestor';
								break;
							}
							$_current_term = get_term( $_current_term->parent, $category->taxonomy );
						}
					}
				}

				/**
				 * Filters the list of CSS classes to include with each category in the list.
				 *
				 * @since 4.2.0
				 *
				 * @see wp_list_categories()
				 *
				 * @param array  $css_classes An array of CSS classes to be applied to each list item.
				 * @param object $category    Category data object.
				 * @param int    $depth       Depth of page, used for padding.
				 * @param array  $args        An array of wp_list_categories() arguments.
				 */
				$css_classes = implode( ' ', apply_filters( 'category_css_class', $css_classes, $category, $depth, $args ) );

				$output .=  ' class="' . $css_classes . '"';
				$output .= ">$link\n";
			} elseif ( isset( $args['separator'] ) ) {
				$output .= "\t$link" . $args['separator'] . "\n";
			} else {
				$output .= "\t$link<br />\n";
			}
		}

		/**
		 * Ends the element output, if needed.
		 *
		 * @since 2.1.0
		 *
		 * @see Walker::end_el()
		 *
		 * @param string $output Used to append additional content (passed by reference).
		 * @param object $page   Not used.
		 * @param int    $depth  Optional. Depth of category. Not used.
		 * @param array  $args   Optional. An array of arguments. Only uses 'list' for whether should append
		 *                       to output. See wp_list_categories(). Default empty array.
		 */
		public function end_el( &$output, $page, $depth = 0, $args = array() ) {
			if ( 'list' != $args['style'] )
				return;

			$output .= "</li>\n";
		}

	}
}