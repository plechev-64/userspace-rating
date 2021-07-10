<?php

class USP_Rating_Totals_Query extends USP_Query {

  function __construct($as = false) {

	$table = array(
		'name' => USERSPACE_RATING_TABLE_TOTALS,
		'as' => $as ? $as : 'usp_rating_totals',
		'cols' => array(
			'ID',
			'object_id',
			'object_author',
			'object_type',
			'rating_total'
		)
	);

	parent::__construct( $table );

  }

  static function insert($data) {

	global $wpdb;

	return $wpdb->insert( USERSPACE_RATING_TABLE_TOTALS, $data );

  }

  static function update($where, $data) {

	global $wpdb;

	return $wpdb->update( USERSPACE_RATING_TABLE_TOTALS, $data, $where );

  }

  static function delete($where) {

	global $wpdb;

	return $wpdb->delete( USERSPACE_RATING_TABLE_TOTALS, $where );

  }

}
