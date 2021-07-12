<?php

/**
 * Get user total rating
 * 
 * @param int $user_id
 * 
 * @return float|null - total user rating or null if record in db not exist
 */
function usp_get_user_rating($user_id) {

  return USP_Rating()->get_user_rating( $user_id );

}

/**
 * Get vote from user on object
 * 
 * @param int $user_id
 * @param int $object_id
 * @param string $object_type_id
 * 
 * @return object|null - vote data or null if record in db not exist
 */
function usp_get_user_vote($user_id, $object_id, $object_type_id) {

  return USP_Rating()->get_user_vote( $user_id, $object_id, $object_type_id );

}

/**
 * Get value of vote from user on object
 * 
 * @param int $user_id
 * @param int $object_id
 * @param string $object_type_id
 * 
 * @return float|null - vote value or null if record in db not exist
 */
function usp_get_user_vote_value($user_id, $object_id, $object_type_id) {

  return USP_Rating()->get_user_vote_value( $user_id, $object_id, $object_type_id );

}

/**
 * Get object total rating
 * 
 * @param int $object_id
 * @param string $object_type
 * 
 * @return float|null - total object rating or null if record in db not exist
 */
function usp_get_object_rating($object_id, $object_type) {

  return USP_Rating()->get_object_rating( $object_id, $object_type );

}

/**
 * Get all votes on object
 * 
 * @param int $object_id
 * @param string $object_type_id
 * 
 * @return array - array of objects votes for object
 */
function usp_get_object_votes($object_id, $object_type_id) {

  return USP_Rating()->get_object_votes( $object_id, $object_type_id );

}

/**
 * Get count votes on object
 * 
 * @param int $object_id
 * @param string $object_type_id
 * 
 * @return int - number of votes on object
 */
function usp_get_object_votes_count($object_id, $object_type_id) {

  return USP_Rating()->get_object_votes_count( $object_id, $object_type_id );

}

/**
 * Insert new vote on object
 * 
 * @param array $args
 * 	$args['user_id'] (required)
 * 	$args['object_id'] (required)
 * 	$args['object_author'] (required)
 * 	$args['object_type'] (required)
 * 	$args['rating_value'] (required)
 * 	$args['rating_date'] (required) mysql format
 * 
 * @return bool - result of inserting vote
 */
function usp_insert_vote($args) {

  $result = USP_Rating_Votes_Query::insert( $args );

  if ( $result === 1 ) {

	$vote_data = usp_get_user_vote( $args[ 'user_id' ], $args[ 'object_id' ], $args[ 'object_type' ] );

	do_action( 'usp_rating_vote_insert', $vote_data );

	return true;
  }

  return false;

}

/**
 * Delete vote on object
 * 
 * @param array $args
 * 	$args['user_id'] (required)
 * 	$args['object_id'] (required)
 * 	$args['object_type'] (required)
 * 
 * @return bool|WP_Error - result of deleting vote
 */
function usp_delete_vote($args) {

  $vote_data = usp_get_user_vote( $args[ 'user_id' ], $args[ 'object_id' ], $args[ 'object_type' ] );

  if ( !$vote_data || !is_object( $vote_data ) ) {
	return false;
  }

  $result = USP_Rating_Votes_Query::delete( $args );

  if ( $result === 1 ) {

	do_action( 'usp_rating_vote_delete', $vote_data );

	return true;
  }

  return false;

}

/**
 * Process new vote on object
 * 
 * if vote from user on object not exist - insert vote
 * if vote exist and rating_value != new rating_value - update vote
 * if vote exist and rating_value == new rating_value - delete vote
 * 
 * @param array $args
 * 	$args['user_id'] (required)
 * 	$args['object_id'] (required)
 * 	$args['object_author'] (required)
 * 	$args['object_type'] (required)
 * 	$args['rating_value'] (required)
 * 	$args['rating_date'] mysql format. Default current_time('mysql')
 * 
 * @return bool|WP_Error - result of processing vote
 */
function usp_process_vote($args) {

  $vote_process = new USP_Rating_Vote_Process( $args );

  return $vote_process->process();

}

/**
 * Update object total rating
 * 
 * @param array $args
 * 	$args['object_id'] int (required)
 *  $args['object_author'] int (required)
 * 	$args['object_type'] string (required)
 * 	$args['rating_value'] float (required) - value of increase / decrease rating
 * 
 * @return bool
 */
function usp_update_total_rating($args) {

  $total_rating = usp_get_object_rating( $args[ 'object_id' ], $args[ 'object_type' ] );

  if ( is_null( $total_rating ) ) {

	$total_rating = $args[ 'rating_value' ];

	$result = USP_Rating_Totals_Query::insert( [
		'object_id' => $args[ 'object_id' ],
		'object_type' => $args[ 'object_type' ],
		'object_author' => $args[ 'object_author' ],
		'rating_total' => $total_rating
	] );
  } else {

	$total_rating += $args[ 'rating_value' ];

	$result = USP_Rating_Totals_Query::update( [
		'object_id' => $args[ 'object_id' ],
		'object_type' => $args[ 'object_type' ],
		'object_author' => $args[ 'object_author' ]
	],
	[
		'rating_total' => $total_rating
	] );
  }

  if ( $result === 1 ) {

	do_action( 'usp_rating_update_total_rating', $args, $total_rating );
  }

  return $result;

}

/**
 * Update user rating
 * 
 * @param array $args
 * 	$args['user_id'] int (required)
 * 	$args['rating_value'] float (required) - value of increase / decrease rating
 * 
 * @return bool
 */
function usp_update_user_rating($args) {

  $user_rating = usp_get_user_rating( $args[ 'user_id' ] );

  if ( is_null( $user_rating ) ) {

	$user_rating = $args[ 'rating_value' ];

	$result = USP_Rating_Users_Query::insert( [
		'user_id' => $args[ 'user_id' ],
		'rating_total' => $user_rating
	] );
  } else {

	$user_rating += $args[ 'rating_value' ];

	$result = USP_Rating_Users_Query::update( [
		'user_id' => $args[ 'user_id' ]
	],
	[
		'rating_total' => $user_rating
	] );
  }

  if ( $result === 1 ) {

	do_action( 'usp_rating_update_user_rating', $args, $user_rating );
  }

  return $result;

}
