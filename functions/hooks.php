<?php

add_filter( 'the_content', 'userspace_rating_posts_display', 999 );

function userspace_rating_posts_display($content) {

  global $post;

  $content .= USP_Rating()->get_rating_box( $post->ID, $post->post_author, $post->post_type );

  return $content;

}

/**
 * Update total object rating after vote removed
 */
add_action( 'userspace_rating_vote_delete', 'userspace_rating_decrease_total_rating' );

function userspace_rating_decrease_total_rating($vote_data) {

  $object_type = USP_Rating()->get_object_type( $vote_data->object_type );

  if ( !$object_type ) {
	return;
  }

  $total_rating = USP_Rating()->get_object_rating( $vote_data->object_id, $vote_data->object_type );

  $total_rating_new = $total_rating - $vote_data->rating_value;

  USP_Rating_Totals_Query::update( [ 'object_id' => $vote_data->object_id, 'object_type' => $vote_data->object_type ], [ 'rating_total' => $total_rating_new ] );

}

/**
 * Update total object rating after vote added
 */
add_action( 'userspace_rating_vote_insert', 'userspace_rating_increase_total_rating' );

function userspace_rating_increase_total_rating($vote_data) {

  $object_type = USP_Rating()->get_object_type( $vote_data->object_type );

  if ( !$object_type ) {
	return;
  }

  $total_rating = USP_Rating()->get_object_rating( $vote_data->object_id, $vote_data->object_type );

  if ( is_null( $total_rating ) ) {

	USP_Rating_Totals_Query::insert( [
		'object_id' => $vote_data->object_id,
		'object_type' => $vote_data->object_type,
		'object_author' => $vote_data->object_author,
		'rating_total' => $vote_data->rating_value
	] );
  } else {

	USP_Rating_Totals_Query::update( [
		'object_id' => $vote_data->object_id,
		'object_type' => $vote_data->object_type
	],
	[
		'rating_total' => $total_rating + $vote_data->rating_value
	] );
  }

}

/**
 * Update user rating after vote removed
 */
add_action( 'userspace_rating_vote_delete', 'userspace_rating_decrease_author_rating' );

function userspace_rating_decrease_author_rating($vote_data) {

  $object_type = USP_Rating()->get_object_type( $vote_data->object_type );

  if ( !$object_type ) {
	return;
  }

  $influence_on_author = $object_type->get_option( 'rating_influence' );

  if ( !$influence_on_author ) {
	return;
  }

  if ( !$vote_data->object_author ) {
	return;
  }

  $user_rating = USP_Rating()->get_user_rating( $vote_data->object_author );

  $user_rating_new = $user_rating - $vote_data->rating_value;

  USP_Rating_Users_Query::update( [ 'user_id' => $vote_data->object_author ], [ 'rating_total' => $user_rating_new ] );

}

/**
 * Update user rating after vote added
 */
add_action( 'userspace_rating_vote_insert', 'userspace_rating_increase_author_rating' );

function userspace_rating_increase_author_rating($vote_data) {

  $object_type = USP_Rating()->get_object_type( $vote_data->object_type );

  if ( !$object_type ) {
	return;
  }

  $influence_on_author = $object_type->get_option( 'rating_influence' );

  if ( !$influence_on_author ) {
	return;
  }

  if ( !$vote_data->object_author ) {
	return;
  }

  $user_rating = USP_Rating()->get_user_rating( $vote_data->object_author );

  if ( is_null( $user_rating ) ) {

	USP_Rating_Users_Query::insert( [
		'user_id' => $vote_data->object_author,
		'rating_total' => $vote_data->rating_value
	] );
  } else {

	$user_rating_new = $user_rating + $vote_data->rating_value;

	USP_Rating_Users_Query::update( [ 'user_id' => $vote_data->object_author ], [ 'rating_total' => $user_rating_new ] );
  }

}
