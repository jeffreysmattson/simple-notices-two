<?php

/**
 * Check if the notice has been read.
 * 
 * @param  int 		$post_id
 * @param  int 		$user_id
 * @return boolean  
 */
function pippin_check_notice_is_read($post_id, $user_id) {
	
	$read_notices = get_user_meta($user_id, 'pippin_notice_ids', true);
	if($read_notices && is_array($read_notices)) {
		if(in_array($post_id, $read_notices)) {
			return true;
		}
	}
	
	// if not closed
	return false;
	
}

/**
 * Update the user meta with post ID
 * 
 * @param  int 	$post_id
 * @return void
 */
function pippin_notice_add_to_usermeta($post_id) {
	global $user_ID;
	$read_notices = get_user_meta($user_ID, 'pippin_notice_ids', true);
	$read_notices[] = $post_id;
	
	update_user_meta($user_ID, 'pippin_notice_ids', $read_notices);
}

/**
 * Mark notice as read
 *
 * @return void
 */
function pippin_notice_mark_as_read() {
	if(isset($_POST['notice_read'])) {
		$notice_id = intval($_POST['notice_read']);
		$marked_as_read = pippin_notice_add_to_usermeta($notice_id);
	}
	die();
}
add_action('wp_ajax_mark_notice_as_read', 'pippin_notice_mark_as_read');

/**
 * Determines if a post is expired.
 *
 * @access public
 * @since 2.0
 * @return bool
 */
function l7w_spe_is_expired( $post_id = 0 ) {

	$expires = get_post_meta( $post_id, '_pw_spe_expiration', true );

	if( ! empty( $expires ) ) {

		// Get the current time and the post's expiration date
		$current_time = current_time( 'timestamp' );
		$expiration   = strtotime( $expires, current_time( 'timestamp' ) );

		// Determine if current time is greater than the expiration date
		if( $current_time >= $expiration ) {

			return true;

		}

	}

	return false;

}

/**
 * Filters the post titles
 *
 * @access public
 * @since 2.0
 * @return void
 */
function l7w_spe_filter_title( $title = '', $post_id = 0 ) {

	if( l7w_spe_is_expired( $post_id ) ) {

		// Post is expired so attach the prefix
		$prefix = get_option( 'pw_spe_prefix', 'Expired:');

		$title  = $prefix . '&nbsp;' . $title;

	}
	return $title;

}
add_filter( 'the_title', 'l7w_spe_filter_title', 100, 2 );