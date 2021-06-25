<?php

add_filter( 'the_content', 'userspace_rating_posts_display' );

function userspace_rating_posts_display($content) {

  global $post;

  $content .= USP_Rating()->get_rating_box( $post->ID, $post->post_author, $post->post_type );

  return $content;

}

add_action( 'userspace_rating_remove_vote', 'userspace_rating_decrease_total_rating' );

function userspace_rating_decrease_total_rating($vote_data) {

  $object_type = USP_Rating()->get_object_type( $vote_data->object_type );

  if ( !$object_type instanceof USP_Rating_Object_Type_Abstract ) {
	return;
  }

  $total_rating = USP_Rating()->get_object_rating( $vote_data->object_id, $vote_data->object_type );

  $total_rating_new = $total_rating - $vote_data->rating_value;

  USP_Rating_Totals_Query::update( [ 'object_id' => $vote_data->object_id, 'object_type' => $vote_data->object_type ], [ 'rating_total' => $total_rating_new ] );

}

add_action( 'userspace_rating_insert_vote', 'userspace_rating_increase_total_rating' );

function userspace_rating_increase_total_rating($vote_data) {

  $object_type = USP_Rating()->get_object_type( $vote_data->object_type );

  if ( !$object_type instanceof USP_Rating_Object_Type_Abstract ) {
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
