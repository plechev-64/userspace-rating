<?php

class USP_Rating_Users_Query extends USP_Query {

  function __construct($as = false) {

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

}
