<?php

// loads the notices jQuery
function pippin_notice_js() {
	$logged_in = 'yes';
	if(!is_user_logged_in()) {
		$logged_in = 'no';
	}
	wp_enqueue_style( 'notifications', SIMPLE_NOTICES_URL . 'css/notifications.css');
	wp_enqueue_script( 'jquery-coookies', SIMPLE_NOTICES_URL . 'js/jquery.cookie.js', array( 'jquery' ) );
	wp_enqueue_script( 'notifications', SIMPLE_NOTICES_URL . 'js/notifications.js', array( 'jquery' ) );
	wp_localize_script( 'notifications', 'notices_ajax_script', array( 
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'logged_in' => $logged_in
		)
	);	
}
add_action('wp_enqueue_scripts', 'pippin_notice_js');


// loads the js for the admin.
function load_wp_admin_scripts() {
	wp_enqueue_script( 'notifications', SIMPLE_NOTICES_URL . 'js/notifications.js', array( 'jquery' ) );
}
add_action( 'admin_enqueue_scripts', 'load_wp_admin_scripts' );


/**
 * Load scripts for the expiration setting.
 */
function pw_spe_scripts() {
	wp_enqueue_style( 'jquery-ui-css', SIMPLE_NOTICES_URL . '/css/jquery-ui-fresh.min.css' );
	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_script( 'jquery-ui-slider' );
	wp_enqueue_script( 'pw-spe-expiration', SIMPLE_NOTICES_URL . '/js/edit.js' );
}
add_action( 'load-post-new.php', 'pw_spe_scripts' );
add_action( 'load-post.php', 'pw_spe_scripts' );