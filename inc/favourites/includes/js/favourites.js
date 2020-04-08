jQuery(document).ready( function($) {
	
	$('.fav-it').on('click', function() {
		
		if(fav_it_vars.is_user_logged_in == 1) { // check if user is logged in
			var $this = $(this);
			var post_id = $this.data('post-id');
			var user_id = $this.data('user-id');
			if($this.hasClass('liked')) {
				//alert(fav_it_vars.already_loved_message);
				//return false;
				var post_data = {
					action: 'unlike_it',
					item_id: post_id,
					user_id: user_id,
					like_it_nonce: fav_it_vars.nonce
				};
				$.post(fav_it_vars.ajaxurl, post_data, function(response) {
					$this.removeClass('liked');
					$this.addClass('fav-it');
					//$this.addClass('removed-moment-ago');
				});
			} else {
				var post_data = {
					action: 'like_it',
					item_id: post_id,
					user_id: user_id,
					like_it_nonce: fav_it_vars.nonce
				};
				$.post(fav_it_vars.ajaxurl, post_data, function(response) {
					if(response == 'liked') {
						$this.addClass('liked');
						$this.removeClass('fav-it');
						var count_wrap = $this.next();
						var count = count_wrap.text();
						count_wrap.text(parseInt(count) + 1);		
					} else {
						alert(fav_it_vars.error_message);
					}
				});
			}
			return false;
			
		} else {
			
			alert(fav_it_vars.login_required);
			return false;
		}
	});	
	
	$('.liked').on('click', function() {
		var $this = $(this);
		var post_id = $this.data('post-id');
		var user_id = $this.data('user-id');
		if($this.hasClass('fav-it')) {
			//alert(fav_it_vars.already_unloved_message);
			//return false;
			var post_data = {
				action: 'like_it',
				item_id: post_id,
				user_id: user_id,
				like_it_nonce: fav_it_vars.nonce
			};
			$.post(fav_it_vars.ajaxurl, post_data, function(response) {
				if(response == 'liked') {
					$this.addClass('liked');
					$this.removeClass('fav-it');
					//$this.removeClass('removed-moment-ago');
					var count_wrap = $this.next();
					var count = count_wrap.text();
					count_wrap.text(parseInt(count) + 1);		
				} else {
					alert(fav_it_vars.error_message);
				}
			});
		} else {
			var post_data = {
				action: 'unlike_it',
				item_id: post_id,
				user_id: user_id,
				like_it_nonce: fav_it_vars.nonce
			};
			$.post(fav_it_vars.ajaxurl, post_data, function(response) {
				$this.removeClass('liked');
				$this.addClass('fav-it');
				//$this.addClass('removed-moment-ago');
			});
		}
		return false;
	});
});