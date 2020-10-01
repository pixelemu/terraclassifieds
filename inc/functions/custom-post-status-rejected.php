<?php
class WordPress_Custom_Status_Rejected
{
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
	protected $enable_action = false;
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
		'exclude_from_search' => false,
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
	public function __construct($args = array())
	{
		if (empty($args)) {
			return;
		}
		if (!isset($args['post_type']) || empty($args['post_type'])) {
			return;
		}
		if (!isset($args['slug']) || $args['slug'] == '') {
			return;
		}
		$this->post_type = $args['post_type'];
		$this->slug = $args['slug'];
		if (isset($args['action']) && in_array($args['action'], array(false, true, 'publish', 'update'))) {
			$this->enable_action = $args['action'];
		}
		if (!isset($args['label']) || $args['label'] == '') {
			$args['label'] = ucfirst($args['slug']);
		}
		if (!isset($args['label_count']) || $args['label_count'] == '') {
			$args['label_count'] = _n_noop($args['label'] . ' <span class="count">(%s)</span>',  $args['label'] . ' <span class="count">(%s)</span>');
		}
		unset($args['slug']);
		unset($args['post_type']);
		unset($args['action']);
		$this->settings = wp_parse_args($args, $this->defaults);
		add_action('init', array($this, 'register_status'));
		add_action('admin_footer', array($this, 'set_status'));
	}

	/**
	 * Register Post Status using WordPress API
	 */
	public function register_status()
	{
		register_post_status($this->slug, $this->settings);
	}

	public function set_status()
	{

		$set_status = apply_filters('ibenic_custom_post_status_' . $this->slug, true);

		if (!$set_status) {
			return;
		}
		global $post;

		if ($post) { // PE
			if (!in_array($post->post_type, $this->post_type)) {
				return;
			}

			$complete = '';
			$label = '';

			if ($post->post_status == $this->slug) {
				$complete = ' selected=\"selected\"';
				$label = '<span id=\"post-status-display\">' . $this->settings['label'] . '</span>';
			}

			$post_status_rejected = __('Rejected', 'terraclassifieds');
			$post_status_rejected_save = __('Save Rejected', 'terraclassifieds');
?>
			<script>
				(function($) {
					$(document).ready(function() {
						<?php if ($post->post_status == $this->slug) { ?>
							$("span#post-status-display").append("<?php echo $post_status_rejected; ?>");
							$("input#save-post").val("<?php echo $post_status_rejected_save; ?>");
						<?php } ?>

						$("a.edit-post-status").click(function() {
							$("a.save-post-status").click(function() {
								if ($("select#post_status option:selected").val() == 'rejected' || ($("select#post_status option:selected").text() == '<?php echo $post_status_rejected; ?>')) {
									$("input#save-post").val("<?php echo $post_status_rejected_save; ?>");
								}
							});
						});

						$('select#post_status').append("<option value='<?php echo esc_attr($this->slug); ?>' <?php echo esc_attr($complete); ?>><?php echo $post_status_rejected; ?></option>");
						$('.misc-pub-section label').append("<?php echo esc_attr($label); ?>");
					});
				})(jQuery);
			</script>
<?php
		} // PE
	}
}

new WordPress_Custom_Status_Rejected(array(
	'post_type' => array('classified'),
	'slug' => 'rejected',
	'label' => _x('Rejected', 'terraclassifieds'),
	'action' => 'update',
	'label_count' => _n_noop(' Rejected <span class="count">(%s)</span>', ' Rejected <span class="count">(%s)</span>'),
));
