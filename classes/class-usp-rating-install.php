<?php

final class USP_Rating_Install {

  /**
   * @var string $charset_collate Charset / Collate 
   */
  private $charset_collate = '';

  public function __construct() {
	
  }

  public static function install() {

	if (!current_user_can('activate_plugins')) {
	  return;
	}

	$this->charSetCollate();

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$sql_values_table = "CREATE TABLE IF NOT EXISTS " . USP_RATING_PREF . "rating_values (
						ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
						user_id BIGINT(20) UNSIGNED NOT NULL,
						object_id BIGINT(20) UNSIGNED NOT NULL,
						object_author BIGINT(20) UNSIGNED NOT NULL,
						rating_value VARCHAR(5) NOT NULL,
						rating_date DATETIME NOT NULL,
						rating_type VARCHAR(20) NOT NULL,
						PRIMARY KEY  ID (ID),
						KEY user_id (user_id),
						KEY object_id (object_id),
						KEY rating_value (rating_value),
						KEY rating_type (rating_type)
					  ) {$this->charset_collate};";

	$sql_totals_table = "CREATE TABLE IF NOT EXISTS " . USP_RATING_PREF . "rating_totals (
						ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
						object_id BIGINT(20) UNSIGNED NOT NULL,
						object_author BIGINT(20) UNSIGNED NOT NULL,
						rating_total VARCHAR(10) NOT NULL,
						rating_type VARCHAR(20) NOT NULL,
						PRIMARY KEY  ID (ID),
						KEY object_id (object_id),
						KEY object_author (object_author),
						KEY rating_type (rating_type),
						KEY rating_total (rating_total)
					  ) {$this->charset_collate};";

	$sql_users_table = "CREATE TABLE IF NOT EXISTS " . USP_RATING_PREF . "rating_users (
						user_id BIGINT(20) UNSIGNED NOT NULL,
						rating_total VARCHAR(10) NOT NULL,
						PRIMARY KEY  id (user_id),
						KEY rating_total (rating_total)
					  ) {$this->charset_collate};";

	dbDelta($sql_values_table);
	dbDelta($sql_totals_table);
	dbDelta($sql_users_table);

  }

  /**
   * Set sql query Charset and Collate
   * 
   * @return void
   */
  private function charSetCollate() {

	global $wpdb;

	if ($wpdb->has_cap('collation')) {
	  if (!empty($wpdb->charset)) {
		$this->charset_collate .= "DEFAULT CHARACTER SET {$wpdb->charset}";
	  }
	  if (!empty($wpdb->collate)) {
		$this->charset_collate .= " COLLATE {$wpdb->collate}";
	  }
	}

  }

}
