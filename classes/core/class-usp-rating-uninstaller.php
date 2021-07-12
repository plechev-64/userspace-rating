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
		USP_RATING_TABLE_TOTALS,
		USP_RATING_TABLE_VOTES,
		USP_RATING_TABLE_USERS
	];

	$wpdb->query( "DROP TABLE IF EXISTS `" . implode( '`, `', $tables ) . "`" );

  }

}
