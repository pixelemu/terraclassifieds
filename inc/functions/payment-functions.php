<?php
	if (!function_exists('terraclassified_payment_list_get_column_header')) {
		function terraclassified_payment_list_get_column_header() {
			$columns = array('id' => __('ID','terraclassifieds'), 'type' => __('Type','terraclassifieds'), 'post_title' => __('Name','terraclassifieds'), 'price' => __('Price','terraclassifieds'), 'method' => __('Payment Type','terraclassifieds'), 'user' => __('User','terraclassifieds'), 'ip_address' => __('IP Address','terraclassifieds'), 'datetime' => __('Date','terraclassifieds'), 'status' => __('Status','terraclassifieds'));
			$current_url = set_url_scheme('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
			if (isset($_GET['orderby'])) {
				$current_orderby = trim($_GET['orderby']);
			} else {
				$current_orderby = 'id';
			}

			if (isset($_GET['order']) && 'desc' === $_GET['order']) {
				$current_order = 'desc';
			} else {
				$current_order = 'asc';
			}
			?>
			<td id="cb" class="manage-column column-cb check-column">
				<label class="screen-reader-text" for="cb-select-all-1"><?php esc_html_e('Select All','terraclassifieds'); ?></label>
				<input id="cb-select-all-1" type="checkbox">
			</td>
			<?php foreach($columns as $key=>$field) : ?>
				<?php if ($current_orderby == $key) : ?>
					<?php $sorted ='sorted'; ?>
					<?php if ($current_order == 'desc') {
						$order = 'asc';
					}else{
						$order = 'desc';
					}; ?>
				<?php else: ?>
					<?php $sorted ='sortable'; ?>
					<?php $order = 'desc'; ?>
				<?php endif; ?>
				
				<th scope="col" id="<?php echo $field; ?>" class="manage-column column-<?php echo $field; ?> <?php echo $sorted; ?> <?php echo $order; ?>">
					<a href="<?php echo esc_url(add_query_arg(array('orderby'=>$key,'order'=>$order), $current_url)); ?>">
						<span><?php echo $field; ?></span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
			<?php endforeach; ?>
			<?php
		}
	}
	if (!function_exists('terraclassified_payment_list_get_pagination_links')) {
		function terraclassified_payment_list_get_pagination_links($count_items,$items_on_page = 20) {
			$pagenum = isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 1;
			$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
			$current_url = remove_query_arg( 'paged', $current_url );
			$pagination = '';
			$count_pages = intval(ceil($count_items/$items_on_page));
			$one_page = ($count_pages === 1) ? ' one-page' : '';
			
			$pagination .= sprintf('<div class="tablenav-pages %s">',$one_page);
			$pagination .= '<span class="displaying-num">'.$count_items.'&nbsp;items</span>';
			
			if ($count_pages > 1) {
				$disable_first = false;
				$disable_last  = false;
				$disable_prev  = false;
				$disable_next  = false;
				
				if ($pagenum === 1) {
					$disable_first = true;
					$disable_prev  = true;
				}
				
				if ($pagenum === 2) {
					$disable_first = true;
				}
				
				if ($pagenum === $count_pages) {
					$disable_last = true;
					$disable_next = true;
				}
				
				if ($pagenum === $count_pages-1) {
					$disable_last = true;
				}
				
				$pagination .= '<span class="pagination-links">';
				if ($pagenum === 1) {
					$pagination .= '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&laquo;</span> ';
					$pagination .= '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&lsaquo;</span>';
				}else{
					$pagination .= sprintf("<a class='first-page button' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a> ",
						esc_url( remove_query_arg( 'paged', $current_url ) ),
						__('First page'),
						'&laquo;'
					);
					$pagination .= sprintf(
						"<a class='prev-page button' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
						esc_url( add_query_arg( 'paged', max( 1, $pagenum - 1 ), $current_url ) ),
						__('Previous page'),
						'&lsaquo;'
					);
				}
				
				$pagination .= ' <span class="paging-input">';
				$pagination .= '<span class="total-pages">';
				$pagination .= sprintf(/* translators: 1: Current page, 2: Total pages. */_x( '%1$s of %2$s', 'paging' ),
				sprintf("%s<input class='current-page' id='current-page-selector' type='text' name='paged' value='%s' size='%d' aria-describedby='table-paging' /><span class='tablenav-paging-text'>",
				'<label for="current-page-selector" class="screen-reader-text">' . __( 'Current Page' ) . '</label>',$pagenum,strlen($count_pages)), 
					sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $count_pages)));
				$pagination .= '</span></span> ';
				
				if ($pagenum === $count_pages) {
					$pagination .= '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&rsaquo;</span> ';
					$pagination .= '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&raquo;</span>';
				}else{
					$pagination .= sprintf(
						"<a class='next-page button' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a> ",
						esc_url( add_query_arg( 'paged', min( $count_pages, $pagenum + 1 ), $current_url ) ),
						__('Next page'),
						'&rsaquo;'
					);
					$pagination .= sprintf(
						"<a class='last-page button' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
						esc_url( add_query_arg( 'paged', $count_pages, $current_url ) ),
						__('Last page'),
						'&raquo;'
					);
				}
				$pagination .= '</span>';
			}
			
			$pagination .= '</div>';
			
			return $pagination;
		}
	}
	
	if (!function_exists('payments_list')) {
		function payments_list() {
			global $wpdb;
			
			$terms = get_terms(array('taxonomy' => 'ad_category','hide_empty' => false,'parent'   => 0));
			
			$default_payment_items_on_page = 20;
			$filter_type = '';
			$filter_status = '';
			$table_payments = $wpdb->prefix.'terraclassifieds_payments';
			$table_users = $wpdb->prefix.'users';
			$table_posts = $wpdb->prefix.'posts';
			
			$payment_items_on_page = array(100=>100,50=>50,20=>20,10=>10,5=>5,1=>1);
			$payment_status = array('pending'=>__('Pending','terraclassifieds'),'completed'=>__('Completed','terraclassifieds'),'cancelled'=>__('Cancelled','terraclassifieds'));
			$payment_type = array('offline'=>__('Offline payment','terraclassifieds'),'paypal'=>__('PayPal payment','terraclassifieds'));
			
			$removable_query_args = wp_removable_query_args();
			$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
			
			if (isset($_GET['orderby'])) {
				$current_orderby = trim($_GET['orderby']);
			} else {
				$current_orderby = 'id';
			}

			if (isset($_GET['order']) && 'desc' === $_GET['order']) {
				$current_order = 'desc';
			} else {
				$current_order = 'asc';
			}
			
			if (isset($_REQUEST['apply_action']) && !empty($_REQUEST['apply_action'])) {
				if (isset($_REQUEST['change_status'])) {
					$change_status = $_REQUEST['change_status'];
					if (isset($_REQUEST['item'])) {
						$item = $_REQUEST['item'];
						if (is_array($item)) {
							foreach($item as $item_key=>$item_val) {
								$wpdb->update($table_payments, array('status' => $change_status), array('id' => $item_val));
								$item_id = $wpdb->get_var($wpdb->prepare("SELECT `id_item` FROM $table_payments WHERE id=%d",$item_val));
								if ($change_status === 'pending') {
									$arg = array('ID' => $item_id, 'post_status' => 'pending');
									wp_update_post($arg);
									terraclassifieds_sendmail_update_status3($item_id,$payment_status[$change_status]);
								}elseif($change_status === 'completed') {
									$arg = array('ID' => $item_id, 'post_status' => 'publish');
									wp_update_post($arg);
									terraclassifieds_sendmail_update_status3($item_id,$payment_status[$change_status]);
								}else{
									$arg = array('ID' => $item_id, 'post_status' => 'rejected');
									wp_update_post($arg);
									terraclassifieds_sendmail_update_status3($item_id,$payment_status[$change_status]);
								}
							}
						}
						wp_safe_redirect(wp_get_referer());
					}
				}
			}
			
			if (isset($_REQUEST['filter_action']) && !empty($_REQUEST['filter_action'])) {
				if (isset($_REQUEST['filter_type']) && (in_array($_REQUEST['filter_type'],array_keys($payment_type)))) {
					$filter_type = $_REQUEST['filter_type'];
					$select_type = $wpdb->prepare(' payment.method = %s', $filter_type);
				}
				
				if (isset($_REQUEST['filter_status']) && (in_array($_REQUEST['filter_status'],array_keys($payment_status)))) {
					$filter_status = $_REQUEST['filter_status'];
					$select_status = $wpdb->prepare(' payment.status = %s', $filter_status);
				}
				
				if (isset($_REQUEST['limit'])) {
					$default_payment_items_on_page = intval($_REQUEST['limit']);
				}
			}
			
			if (isset($_REQUEST['s']) && (!empty($_REQUEST['s']))) {
				$search_word_org = trim(strip_tags($_REQUEST['s']));
				$select_word = $wpdb->prepare(' item.post_title LIKE %s', '%'.$wpdb->esc_like($search_word_org).'%');
			}
			
			$select_query = "SELECT payment.*,user.user_nicename,user.user_email,user.id as user_id,item.post_title from $table_payments as payment LEFT JOIN $table_users as user on (payment.id_user = user.id) LEFT JOIN $table_posts as item ON (payment.id_item = item.id)";
			$where = '';
			
			if (isset($search_word_org)) {
				$where = " WHERE $select_word";
			}
			
			if (isset($select_type)) {
				if (!empty($where)) {
					$where .= " AND $select_type";
				}else{
					$where = " WHERE $select_type";
				}
			}
			
			if (isset($select_status)) {
				if (!empty($where)) {
					$where .= " AND $select_status";
				}else{
					$where = " WHERE $select_status";
				}
			}
			
			$pagenum = isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 1;
			
			$count_payments = count($wpdb->get_results($select_query.$where));
			$order = " ORDER BY ".$current_orderby." ".$current_order;
			$starting_position = (($pagenum - 1) * $default_payment_items_on_page);
			$limit = ' LIMIT '.$starting_position.','.$default_payment_items_on_page;
			
			$payment_lists = $wpdb->get_results($select_query.$where.$order.$limit);
			
			$currency = terraclassifieds_get_option( '_tc_advert_currency', '$' );
			$unit_position = (int) terraclassifieds_get_option( '_tc_unit_position', 1 );
			
			?>
				<div class="wrap">
					<form method="get" id="payment-list" action="admin.php">
						<input type="hidden" name="page" value="payments-list" />
						<h2><?php esc_html_e('Payments list','terraclassifieds'); ?></h2>
						<hr class="wp-header-end">
						<p class="payment-list-search-box search-box">								
							<label class="screen-reader-text" for="payment-search-input"><?php esc_html_e('Search', 'terraclassifieds' ); ?></label>
							<input type="search" id="payment-search-input" name="s" value="<?php echo !empty($search_word_org)? $search_word_org : ''; ?>">
							<input type="submit" id="search-submit" class="button" value="<?php esc_html_e('Search', 'terraclassifieds' ); ?>">
						</p>
						<div class="tablenav top">						
							<div class="alignleft actions bulkactions">
								<select name="change_status" id="terraclassifieds-payment-change-status">
									<option value="-1"><?php esc_html_e( ' - Change Status - ', 'terraclassifieds' ); ?></option>
									<?php  foreach ($payment_status as $k=>$status) : ?>
										<option <?php echo $k === $item->status ? 'selected': '';  ?> value="<?php echo esc_attr($k); ?>"><?php echo esc_html_e( $status, 'terraclassifieds' ); ?></option>
									<?php endforeach; ?>
								</select>
								<input type="submit" id="apply" class="button" name="apply_action" value="<?php esc_html_e('Apply', 'terraclassifieds' ); ?>">
							</div>
							<div class="alignleft actions bulkactions">
								<select name="filter_type" id="terraclassifieds-payment-type">
									<option value="-1"><?php esc_html_e( ' - Select Type - ', 'terraclassifieds' ); ?></option>
									<?php  foreach ($payment_type as $k=>$type) : ?>
										<option <?php echo $k === $filter_type ? 'selected': '';  ?> value="<?php echo esc_attr($k); ?>"><?php echo esc_html_e( $type, 'terraclassifieds' ); ?></option>
									<?php endforeach; ?>
								</select>
								<select name="filter_status" id="terraclassifieds-payment-status">
									<option value="-1"><?php esc_html_e( ' - Select Status - ', 'terraclassifieds' ); ?></option>
									<?php  foreach ($payment_status as $k=>$status) : ?>
										<option <?php echo $k === $filter_status ? 'selected': '';  ?> value="<?php echo esc_attr($k); ?>"><?php echo esc_html_e( $status, 'terraclassifieds' ); ?></option>
									<?php endforeach; ?>
								</select>
								<select name="limit" id="terraclassifieds-payment-items-on-page">
									<?php foreach($payment_items_on_page as $k=>$v) : ?>
										<option <?php echo intval($k) === intval($default_payment_items_on_page) ? 'selected': '';  ?> value="<?php echo $k; ?>"><?php echo $v; ?></option>
									<?php endforeach; ?>
								</select>
								<input type="submit" id="filter" class="button" name="filter_action" value="<?php esc_html_e('Filter', 'terraclassifieds' ); ?>">
							</div>
							<?php echo terraclassified_payment_list_get_pagination_links($count_payments,$default_payment_items_on_page); ?>
						</div>
					
						<table class="wp-list-table widefat fixed striped posts">
							<thead>
								<tr>
									<?php terraclassified_payment_list_get_column_header(); ?>
								</tr>
							</thead>
							<tbody>
								<?php if(!empty($payment_lists)): ?>
									<?php foreach($payment_lists as $k=>$item): ?>
										<tr>
											<th scope="row" class="check-column">
												<label class="screen-reader-text" for="cb-select-<?php _e($item->id); ?>">
													<?php echo esc_html(ucwords($item->post_title)); ?>
												</label>
												<input id="cb-select-<?php _e($item->id); ?>" type="checkbox" name="item[]" value="<?php _e($item->id); ?>">
												<div class="locked-indicator">
													<span class="locked-indicator-icon" aria-hidden="true"></span>
													<span class="screen-reader-text">
														<?php echo esc_html(ucwords($item->post_title)); ?>
													</span>
												</div>
											</th>
											<td>
												<?php echo esc_html($item->id); ?>
											</td>
											<td>
												<?php echo esc_html(ucwords($item->type)); ?>
											</td>
											<td>
												<a target="_blank" href="post.php?post=<?php echo esc_html($item->id_item); ?>&action=edit" title="<?php echo esc_html(ucwords($item->post_title)); ?>" alt="<?php echo esc_html(ucwords($item->post_title)); ?>"><?php echo esc_html(ucwords($item->post_title)); ?></a>
											</td>
											<td>
												<?php if (!$unit_position): ?>
													<?php echo $currency; ?>
													<?php echo terraclassifiedsPriceFormat($item->price); ?>
												<?php else: ?>
													<?php echo terraclassifiedsPriceFormat($item->price); ?>
													<?php echo $currency; ?>
												<?php endif; ?>
											</td>
											<td>
												<?php echo esc_html($payment_type[$item->method]); ?>
											</td>
											<td>
												<?php echo esc_html(ucwords($item->user_nicename)); ?><br/>
												<a href="mailto:<?php echo esc_html(ucwords($item->user_email)); ?>" title="" alt="" ><?php echo esc_html(ucwords($item->user_email)); ?></a><br/>
												(ID: <?php echo esc_html($item->user_id); ?>)
											</td>
											<td>
												<?php echo esc_html($item->ip_address); ?>
											</td>
											<td>
												<?php echo esc_html($item->datetime); ?>
											</td>
											<td>
												<?php echo esc_html(ucwords($item->status)); ?>
											</td>
										</tr>
									<?php endforeach; ?>
								<?php else: ?>
								
								<?php endif; ?>
							</tbody>
							<tfoot>
								<tr>
									<?php terraclassified_payment_list_get_column_header(); ?>
								</tr>
							</tfoot>
						<table>
					</form>
				</div>
			<?php
		}
	}
