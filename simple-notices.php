<?php
/*
Plugin Name: Simple Notices Two
Plugin URI: http://pippinsplugins.com/simple-notices-plugin
Description: A simple notices plugin for WordPress updated for my own personal use.
Version: 2
Author: Jeffrey Mattson (Original: Pippen Williamson)
Author URI: https://github.com/jeffreysmattson
Contributors: mordauk
*/

if(!defined('SIMPLE_NOTICES_DIR')) define('SIMPLE_NOTICES_URL', plugin_dir_url( __FILE__ ));

include('includes/post-types.php');
include('includes/scripts.php');
include('includes/read-functions.php');

if(!is_admin()) {
	include('includes/display-functions.php');
} else {
	include('includes/metabox.php');
}
