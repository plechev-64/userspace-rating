<?php

/*
  Plugin Name: UserSpace Rating
  Plugin URI: http://user-space.com/
  Description: Rating system for posts, comments, users and custom objects
  Version: 1.0.0
  Author: Preci1
  Author URI: http://user-space.com/
  Text Domain: userspace-rating
  License: GPLv2 or later (license.txt)
 */


/**
 * Currently plugin version.
 */
define( 'USERSPACE_RATING_VERSION', '1.0.0' );

if ( !defined( 'USERSPACE_RATING_PATH' ) ) {
  define( 'USERSPACE_RATING_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
}

if ( !defined( 'USERSPACE_RATING_URL' ) ) {
  define( 'USERSPACE_RATING_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
}

if ( !defined( 'USERSPACE_RATING_PREF' ) ) {
  global $wpdb;

  define( 'USERSPACE_RATING_PREF', $wpdb->base_prefix . 'uspr_' );
}

/**
 * Register class autoloader
 */
spl_autoload_register( function ($class_name) {

  $path = USERSPACE_RATING_PATH . "classes/class-" . mb_strtolower( str_replace( "_", "-", $class_name ) ) . ".php";

  if ( file_exists( $path ) ) {
	require_once $path;
  }
} );

require_once 'functions/hooks.php';

/**
 * Fires once on activate plugin UserSpace Rating
 */
function activate_userspace_rating() {

  USP_Rating_Activator::activate();

}

/**
 * Fires once on uninstall plugin UserSpace Rating
 */
function uninstall_userspace_rating() {

  USP_Rating_Uninstaller::uninstall();

}

register_activation_hook( __FILE__, 'activate_userspace_rating' );
register_uninstall_hook( __FILE__, 'uninstall_userspace_rating' );

/**
 * Check if UserSpace is active
 * */
if ( in_array( 'userspace/userspace.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

  add_action( 'init', function() {

	$USP_Rating = USP_Rating::get_instance();
	$USP_Rating->run();
  } );
} else {

  add_action( 'admin_notices', function () {

	$url = '/wp-admin/plugin-install.php?s=UserSpace&tab=search&type=term';

	$notice = '<div class="notice notice-error">';
	$notice .= '<p>' . __( 'UserSpace plugin not installed!', 'userspace-rating' ) . '</p>';
	$notice .= sprintf( __( 'Go to the page %sPlugins%s - install and activate the UserSpace plugin', 'userspace-rating' ), '<a href="' . $url . '">', '</a>' );
	$notice .= '</div>';

	echo $notice;
  } );
}
