<?php

class USP_Rating_Values_Query extends USP_Query {

  function __construct($as = false) {

	$table = array(
		'name' => USERSPACE_RATING_PREF . "rating_values",
		'as' => $as ? $as : 'usp_rating_values',
		'cols' => array(
			'ID',
			'user_id',
			'object_id',
			'object_author',
			'rating_value',
			'rating_date',
			'rating_type'
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

}
