<?php

/*
  Plugin Name: UserSpace Rating
  Plugin URI: http://user-space.com/
  Description: Rating system for posts, comments, users and custom objects
  Version: 1.0.0
  Author: Plechev Andrey
  Author URI: http://user-space.com/
  Text Domain: userspace-rating
  License: GPLv2 or later (license.txt)
 */


if (!defined('USP_RATING_PATH')) {
  define('USP_RATING_PATH', trailingslashit(plugin_dir_path(__FILE__)));
}

if (!defined('USP_RATING_URL')) {
  define('USP_RATING_URL', trailingslashit(plugin_dir_url(__FILE__)));
}

if (!defined('USP_RATING_PREF')) {
  global $wpdb;

  define('USP_RATING_PREF', $wpdb->base_prefix . 'uspr_');
}

/**
 * Register class autoloader
 */
spl_autoload_register(function ($class_name) {

  $classes = ['USP_Rating_Install', 'USP_Rating_Uninstall'];

  if (in_array($class_name, $classes)) {

	$path = USP_RATING_PATH . "classes/class-" . mb_strtolower(str_replace("_", "-", $class_name));

	require_once $path;
  }
});


register_activation_hook(__FILE__, ['USP_Rating_Install', 'install']);
register_uninstall_hook(__FILE__, ['USP_Rating_Uninstall', 'uninstall']);

/**
 * Check if UserSpace is active
 * */
if (in_array('userspace/userspace.php', apply_filters('active_plugins', get_option('active_plugins')))) {

  //load
  
} else {
  add_action('admin_notices', function () {
	$url = '/wp-admin/plugin-install.php?s=UserSpace&tab=search&type=term';

	$notice = '<div class="notice notice-error">';
	$notice .= '<p>' . __('UserSpace plugin not installed!', 'userspace-rating') . '</p>';
	$notice .= sprintf(__('Go to the page %sPlugins%s - install and activate the UserSpace plugin', 'userspace-rating'), '<a href="' . $url . '">', '</a>');
	$notice .= '</div>';

	echo $notice;
  });
}