<?php

final class USP_Rating_Uninstaller {

  public function __construct() {
	
  }

  public static function uninstall() {
	
	if ( !current_user_can( 'activate_plugins' ) ) {
	  return;
	}

	global $wpdb;

	$tables = [
		USERSPACE_RATING_PREF . 'rating_votes',
		USERSPACE_RATING_PREF . 'rating_totals',
		USERSPACE_RATING_PREF . 'rating_users'
	];

	$wpdb->query( "DROP TABLE IF EXISTS `" . implode( '`, `', $tables ) . "`" );

  }

}
