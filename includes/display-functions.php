<?php

function pippin_display_notice() {

	/// this displays the notification area if the user has not read it before
	global $user_ID; 
	$notice_args = array('post_type' => 'notices', 'posts_per_page' => 1);
	$notices = get_posts($notice_args);
	if($notices) :
		foreach ($notices as $notice) {
			$logged_in_only = get_post_meta($notice->ID, '_notice_for_logged_in_only', true);
			$shortcode_only = get_post_meta($notice->ID, '_display_using_shortcode_only', true);

			// This is set in the plugin "Simple Post Expiration Two"
			$expires = get_post_meta( $$notice->ID, 'pw_spe_expiration', true );
			
			// If the expiration date has passed don't show this notice.
			if( ! empty( $expires ) ) {

				// Get the current time and the post's expiration date
				$current_time = current_time( 'timestamp' );
				$expiration   = strtotime( $expires, current_time( 'timestamp' ) );

				// Determine if current time is greater than the expiration date
				if( $current_time >= $expiration ) {
					continue;
				}

			}

			if( ( ( $logged_in_only && is_user_logged_in() ) || !$logged_in_only) && !$shortcode_only) {			
				if(true) { ?>
					<div id="notification-area" class="notification-area <?php echo strtolower(get_post_meta($notice->ID, '_notice_color', true)); ?> hidden">
						<a class="remove-notice" href="#" id="remove-notice" rel="<?php echo $notice->ID; ?>"><?php _e('X', 'simple-notices'); ?></a>
						<h3><?php echo get_the_title($notice->ID); ?></h3>					
						<?php echo do_shortcode(wpautop(__($notice->post_content))); ?>
					</div>
				<?php } 
			}			
		}
	endif;
}
add_action('wp_footer', 'pippin_display_notice');

/**
 * Create a short code to show the notice.
 */
function shortcode_display_notice() {

	global $user_ID;
	
	$notice_args = array('post_type' => 'notices', 'posts_per_page' => 2);
	$notices = get_posts($notice_args);
	if($notices) :
		foreach($notices as $notice) {
			$logged_in_only = get_post_meta($notice->ID, '_notice_for_logged_in_only', true);
			$shortcode_only = get_post_meta($notice->ID, '_display_using_shortcode_only', true);

			// This is set in the plugin "Simple Post Expiration Two"
			$expires = get_post_meta( $notice->ID, 'pw_spe_expiration', true );
			
			// If the expiration date has passed don't show this notice.
			if( ! empty( $expires ) ) {

				// Get the current time and the post's expiration date
				$current_time = current_time( 'timestamp' );
				$expiration   = strtotime( $expires, current_time( 'timestamp' ) );

				// Determine if current time is greater than the expiration date
				if( $current_time >= $expiration ) {
					continue;
				}

			}

			$cookie_expiration = get_post_meta($notice->ID, '_cookie_expiration', true);
			if((($logged_in_only && is_user_logged_in() ) || !$logged_in_only) && $shortcode_only == true) {			
				if(true) { ?>
					<div id="notification-area" data-cookieExpiration="<?php echo $cookie_expiration; ?>" class="notification-area <?php echo strtolower(get_post_meta($notice->ID, '_notice_color', true)); ?> hidden">
						<a class="remove-notice" href="#" id="remove-notice" rel="<?php echo $notice->ID; ?>"><?php _e('X', 'simple-notices'); ?></a>
						<h3><?php echo get_the_title($notice->ID); ?></h3>					
						<?php echo do_shortcode(wpautop(__($notice->post_content))); ?>
					</div>
				<?php } 
			}			
		}
	endif;
}
add_shortcode( 'simple-notice-two', 'shortcode_display_notice' );