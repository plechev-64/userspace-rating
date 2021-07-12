<?php

class USP_Rating_Users_Query extends USP_Query {

  function __construct($as = false) {

	$table = array(
		'name' => USP_RATING_TABLE_USERS,
		'as' => $as ? $as : 'usp_rating_users',
		'cols' => array(
			'user_id',
			'rating_total'
		)
	);

	parent::__construct( $table );

  }

  static function insert($data) {

	global $wpdb;

	return $wpdb->insert( USP_RATING_TABLE_USERS, $data );

  }

  static function update($where, $data) {

	global $wpdb;

	return $wpdb->update( USP_RATING_TABLE_USERS, $data, $where );

  }

  static function delete($where) {

	global $wpdb;

	return $wpdb->delete( USP_RATING_TABLE_USERS, $where );

  }

}
