<?php

final class USP_Rating_Uninstaller {

  public function __construct() {
	
  }

  public static function uninstall() {

	if ( !current_user_can( 'activate_plugins' ) || !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	  return;
	}

	global $wpdb;

	$tables = [
		USERSPACE_RATING_PREF . 'rating_values',
		USERSPACE_RATING_PREF . 'rating_totals',
		USERSPACE_RATING_PREF . 'rating_users'
	];

	$wpdb->query( "DROP TABLE IF EXISTS `" . implode( '`, `', $tables ) . "`" );

  }

}
