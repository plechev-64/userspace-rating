<?php

abstract class USP_Rating_Type_Abstract {

  abstract public function get_id();

  abstract public function get_name();

  /**
   * @param int $rating_value - value of processed user vote
   * @param object $object_type - rating object type
   * 
   * @return bool
   */
  abstract public function is_valid_rating_value($rating_value, $object_type);

  /**
   * @param int $object_id - post_id, comment_id etc...
   * @param int $object_author - user_id
   * @param object $object_type - rating object type
   * 
   * @return string - html code of rating box
   */
  abstract public function get_rating_box($object_id, $object_author, $object_type);

  /**
   * @param float $rating_value - rating value
   * @param object $object_type - rating object type
   * 
   * @return string - converted value to html
   */
  public function get_html_from_value($rating_value, $object_type) {
	return $rating_value;

  }

  /**
   * @param $object_type - Current rating object type
   * 
   * @return array - Array of custom options for rating type
   */
  public function get_custom_options($object_type) {
	return [];

  }

  /**
   * @param int $object_id - post_id, comment_id etc...
   * @param int $object_author - user_id
   * @param object $object_type - rating object type
   * 
   * @return array - base data for display rating box
   */
  public function get_rating_box_data($object_id, $object_author, $object_type) {

	/*
	 * Find rating data in preloaded cache
	 */
	$preloaded_data = USP_Rating()->get_preloaded_data( $object_id, $object_type->get_id() );

	$data = [];

	/*
	 * Object total rating
	 */
	if ( isset( $preloaded_data[ 'rating' ] ) ) {

	  $data[ 'rating' ] = $preloaded_data[ 'rating' ];
	} else {

	  $data[ 'rating' ] = USP_Rating()->get_object_rating( $object_id, $object_type );
	}

	/*
	 * Object votes count
	 */
	if ( is_null( $data[ 'rating' ] ) ) {

	  $data[ 'votes_count' ] = 0;
	} else if ( isset( $preloaded_data[ 'votes_count' ] ) ) {

	  $data[ 'votes_count' ] = $preloaded_data[ 'votes_count' ];
	} else {

	  $data[ 'votes_count' ] = USP_Rating()->get_object_votes_count( $object_id, $object_type );
	}

	/*
	 * Current user vote value
	 */
	if ( isset( $preloaded_data[ 'user_vote' ] ) ) {

	  $data[ 'user_vote' ] = $preloaded_data[ 'user_vote' ];
	} else {

	  $data[ 'user_vote' ] = USP_Rating()->get_user_vote_value( get_current_user_id(), $object_id, $object_type );
	}

	/*
	 * Only logged in and not object author can vote
	 */
	$user_can_vote = get_current_user_id() && get_current_user_id() != $object_author;

	if ( $user_can_vote && $data[ 'user_vote' ] ) {

	  $allow_delete_vote = usp_get_option( 'rating_delete_vote', 0 );

	  if ( !$allow_delete_vote ) {
		$user_can_vote = false;
	  }
	}

	$data[ 'user_can_vote' ] = $user_can_vote;

	$data[ 'user_can_view_history' ] = get_current_user_id() ? true : false;

	$data[ 'rating_average' ] = $data[ 'rating' ] && $data[ 'votes_count' ] ? round( $data[ 'rating' ] / $data[ 'votes_count' ], USERSPACE_RATING_PRECISION ) : 0;

	return $data;

  }

}
