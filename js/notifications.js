jQuery(document).ready( function($) {

	// notification is present
	if ($("#notification-area").length) {
		
		$('.notification-area').each(function(){
			$this = $(this);
			notice_id = $this.find('.remove-notice').attr('rel');
			
			if(!$.cookie('notice-' + notice_id)) {
				$this.show();
			} else{
				$this.hide();
			}
		});
	}
	
	$(".remove-notice").on('click', function() {
		$this = $(this);
		var notice_id = $this.attr('rel');
		var expirationMinutes = $this.data("cookieExpiration");
		
		// Set cookie for people not logged in.
		var date = new Date();
 		var minutes = expirationMinutes;
 		date.setTime(date.getTime() + (minutes * 60 * 1000));

		$.cookie('notice-' + notice_id, 'yes', { expires: date });

		if(notices_ajax_script.logged_in == 'no') {
			// store a cookie so notice is not shown again
			$.cookie('notice-' + notice_id, 'yes', { expires: 1 });
		}
		
		var data = {
			action: 'mark_notice_as_read',
			notice_read: notice_id
		};
		$.post(notices_ajax_script.ajaxurl, data, function(response) {
			$this.parent('#notification-area').slideUp('fast');
		});
		return false;
	});
	
});