<?php

class USP_Rating_Votes_Query extends USP_Query {

  function __construct($as = false) {

	$table = array(
		'name' => USERSPACE_RATING_PREF . "rating_votes",
		'as' => $as ? $as : 'usp_rating_votes',
		'cols' => array(
			'ID',
			'user_id',
			'object_id',
			'object_author',
			'object_type',
			'rating_value',
			'rating_date'
		)
	);

	parent::__construct( $table );

  }

  function get_sum_values($args) {

	$this->query[ 'select' ] = array(
		"SUM(" . $this->query[ 'table' ][ 'as' ] . ".rating_value)"
	);

	return $this->get_var( $args );

  }

  static function insert($data) {

	global $wpdb;

	return $wpdb->insert( USERSPACE_RATING_PREF . "rating_votes", $data );

  }

  static function update($where, $data) {

	global $wpdb;

	return $wpdb->update( USERSPACE_RATING_PREF . "rating_votes", $data, $where );

  }

  static function delete($where) {

	global $wpdb;

	return $wpdb->delete( USERSPACE_RATING_PREF . "rating_votes", $where );

  }

}
