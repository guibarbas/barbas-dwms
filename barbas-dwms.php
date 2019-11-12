<?php
/*
Plugin Name: Barbas - Default wordpress mail sender
Plugin URI: http://www.barbas.digital
Description: Simple way to change the default wordpress sender's name and email.
Version: 1.0
Author: Guilherme Souza
Author URI: https://www.barbas.digital
Licence: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: barbas-dwms
Domain Path: /languages
*/

// Prevent direct access
if (!function_exists('add_action')) {
	echo (__('Hi there, I\'m just a plugin, not much I can do when called directly.', 'barbas-dwms'));
	exit;
}

// Setup


// Includes
include('includes/barbas-functions.php');

// Hooks
add_action( 'init', 'barbas_mail_load_textdomain' );
add_action('admin_init', 'barbas_mail_sender_register');
add_action('admin_menu', 'barbas_mail_sender_menu');
add_filter('wp_mail_from', 'barbas_new_mail_from');
add_filter('wp_mail_from_name', 'barbas_new_mail_from_name');
 
// Shortcodes