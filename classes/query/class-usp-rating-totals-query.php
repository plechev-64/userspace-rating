<?php

class USP_Rating_Totals_Query extends USP_Query {

  function __construct($as = false) {

	$table = array(
		'name' => USERSPACE_RATING_PREF . "rating_totals",
		'as' => $as ? $as : 'usp_rating_totals',
		'cols' => array(
			'ID',
			'object_id',
			'object_author',
			'rating_total',
			'rating_type'
		)
	);

	parent::__construct( $table );

  }

}
