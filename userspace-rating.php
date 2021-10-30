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
define( 'USP_RATING_VERSION', '1.0.0' );

define( 'USP_RATING_PRECISION', 2 );

define( 'USP_RATING_PRELOAD_DATA', true );

define( 'USP_RATING_BASE', __FILE__ );

define( 'USP_RATING_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );

define( 'USP_RATING_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );

global $wpdb;

define( 'USP_RATING_TABLE_TOTALS', $wpdb->base_prefix . 'usp_rating_totals' );
define( 'USP_RATING_TABLE_VOTES', $wpdb->base_prefix . 'usp_rating_votes' );
define( 'USP_RATING_TABLE_USERS', $wpdb->base_prefix . 'usp_rating_users' );

/**
 * Register class autoloader
 */
spl_autoload_register( function ( $class_name ) {

	/**
	 * TODO
	 *
	 * refactoring
	 */
	if ( strpos( $class_name, "USP_Rating" ) === false ) {
		return;
	}

	$folders = [ 'admin', 'core', 'query', 'object-type', 'rating-type' ];

	$fileName = "class-" . mb_strtolower( str_replace( "_", "-", $class_name ) ) . ".php";
	foreach ( $folders as $folder ) {
		if ( file_exists( USP_RATING_PATH . 'classes' . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $fileName ) ) {
			require_once USP_RATING_PATH . 'classes' . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $fileName;
			break;
		}
	}
} );

/**
 * Fires once on activate plugin UserSpace Rating
 */
function activate_usp_rating() {

	USP_Rating_Activator::activate();

}

/**
 * Fires once on uninstall plugin UserSpace Rating
 */
function uninstall_usp_rating() {

	USP_Rating_Uninstaller::uninstall();

}

register_activation_hook( __FILE__, 'activate_usp_rating' );
register_uninstall_hook( __FILE__, 'uninstall_usp_rating' );

function USP_Rating(): USP_Rating {

	return USP_Rating::get_instance();

}

function usp_rating_ajax() {

	$ajax = new USP_Rating_Ajax();

	$ajax->process();

}

/**
 * Check if UserSpace is active
 * */
if ( in_array( 'userspace/userspace.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	require_once 'functions/actions.php';
	require_once 'functions/filters.php';
	require_once 'functions/functions.php';

	/**
	 * Load UserSpace Rating
	 */
	add_action( 'usp_init', 'USP_Rating' );
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