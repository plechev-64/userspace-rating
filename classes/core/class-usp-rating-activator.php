<?php

final class USP_Rating_Activator {

  public function __construct() {
	
  }

  public static function activate() {

	if ( !current_user_can( 'activate_plugins' ) ) {
	  return;
	}

	$charset_collate = self::char_set_collate();

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$sql_votes_table = "CREATE TABLE IF NOT EXISTS " . USERSPACE_RATING_PREF . "rating_votes (
						ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
						user_id BIGINT(20) UNSIGNED NOT NULL,
						object_id BIGINT(20) UNSIGNED NOT NULL,
						object_author BIGINT(20) UNSIGNED NOT NULL,
						object_type VARCHAR(20) NOT NULL,
						rating_value VARCHAR(10) NOT NULL,
						rating_date DATETIME NOT NULL,
						PRIMARY KEY  ID (ID),
						KEY user_id (user_id),
						KEY object_id (object_id),
						KEY object_type (object_type)
					  ) {$charset_collate};";

	$sql_totals_table = "CREATE TABLE IF NOT EXISTS " . USERSPACE_RATING_PREF . "rating_totals (
						ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
						object_id BIGINT(20) UNSIGNED NOT NULL,
						object_author BIGINT(20) UNSIGNED NOT NULL,
						object_type VARCHAR(20) NOT NULL,
						rating_total VARCHAR(10) NOT NULL,
						PRIMARY KEY  ID (ID),
						KEY object_id (object_id),
						KEY object_author (object_author),
						KEY object_type (object_type),
						KEY rating_total (rating_total)
					  ) {$charset_collate};";

	$sql_users_table = "CREATE TABLE IF NOT EXISTS " . USERSPACE_RATING_PREF . "rating_users (
						user_id BIGINT(20) UNSIGNED NOT NULL,
						rating_total VARCHAR(10) NOT NULL,
						PRIMARY KEY  id (user_id),
						KEY rating_total (rating_total)
					  ) {$charset_collate};";

	dbDelta( $sql_votes_table );
	dbDelta( $sql_totals_table );
	dbDelta( $sql_users_table );

  }

  /**
   * Set sql query Charset and Collate
   * 
   * @return void
   */
  private function char_set_collate() {

	global $wpdb;

	$charset_collate = '';

	if ( $wpdb->has_cap( 'collation' ) ) {
	  if ( !empty( $wpdb->charset ) ) {
		$charset_collate .= "DEFAULT CHARACTER SET {$wpdb->charset}";
	  }
	  if ( !empty( $wpdb->collate ) ) {
		$charset_collate .= " COLLATE {$wpdb->collate}";
	  }
	}

	return $charset_collate;

  }

}
