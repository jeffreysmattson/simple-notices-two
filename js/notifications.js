jQuery(document).ready( function($) {

	// notication is present
	if ($("#notification-area").length && notices_ajax_script.logged_in == 'no') {
		var notice_id = $('#notification-area #remove-notice').attr('rel');
		if(true /*!$.cookie('notice-' + notice_id)*/) {
			$('#notification-area').show();
		}
	}
	
	$(".remove-notice").on('click', function() {
		$this = $(this);
		var notice_id = $this.attr('rel');
		
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