<?php

class USP_Rating_Users_Query extends USP_Query {

  private $db;

  function __construct($as = false) {

	global $wpdb;

	$this->db = $wpdb;

	$table = array(
		'name' => USERSPACE_RATING_PREF . "rating_users",
		'as' => $as ? $as : 'usp_rating_users',
		'cols' => array(
			'user_id',
			'rating_total'
		)
	);

	parent::__construct( $table );

  }

  function insert($data) {

	return $this->db->insert( $this->query[ 'table' ][ 'name' ], $data );

  }

  function update($where, $data) {

	return $this->db->update( $this->query[ 'table' ][ 'name' ], $data, $where );

  }

  function delete($where) {

	return $this->db->delete( $this->query[ 'table' ][ 'name' ], $where );

  }

}
