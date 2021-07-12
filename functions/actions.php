<?php

if ( !is_admin() ) {
  add_filter( 'the_content', 'usp_rating_posts_display', 999 );
}

function usp_rating_posts_display($content) {

  global $post;

  $rating_data = isset( $post->rating_data ) ? $post->rating_data : [];

  $content .= USP_Rating()->get_rating_box( $post->ID, $post->post_author, $post->post_type, $rating_data );

  return $content;

}

if ( !is_admin() ) {
  add_filter( 'comment_text', 'usp_rating_comment_display', 999 );
}

function usp_rating_comment_display($content) {

  global $comment;

  $rating_data = isset( $comment->rating_data ) ? $comment->rating_data : [];

  $content .= USP_Rating()->get_rating_box( $comment->comment_ID, $comment->user_id, 'comment', $rating_data );

  return $content;

}

/**
 * Update total object rating after vote removed
 */
add_action( 'usp_rating_vote_delete', 'usp_rating_decrease_total_rating' );

function usp_rating_decrease_total_rating($vote_data) {

  usp_update_total_rating( [
	  'object_id' => $vote_data->object_id,
	  'object_type' => $vote_data->object_type,
	  'object_author' => $vote_data->object_author,
	  'rating_value' => $vote_data->rating_value * -1
  ] );

}

/**
 * Update total object rating after vote added
 */
add_action( 'usp_rating_vote_insert', 'usp_rating_increase_total_rating' );

function usp_rating_increase_total_rating($vote_data) {

  usp_update_total_rating( [
	  'object_id' => $vote_data->object_id,
	  'object_type' => $vote_data->object_type,
	  'object_author' => $vote_data->object_author,
	  'rating_value' => $vote_data->rating_value
  ] );

}

/**
 * Update user rating after object total rating updated
 */
add_action( 'usp_rating_update_total_rating', 'usp_rating_update_object_author_rating' );

function usp_rating_update_object_author_rating($args) {

  if ( !$args[ 'object_author' ] ) {
	return;
  }

  $object_type = USP_Rating()->get_object_type( $args[ 'object_type' ] );

  if ( !$object_type ) {
	return;
  }

  if ( $object_type->is_public() ) {
	$influence_on_author = $object_type->get_option( 'rating_influence' );

	if ( !$influence_on_author ) {
	  return;
	}
  }

  $pre = apply_filters( 'usp_rating_pre_update_object_author_rating', null, $args );

  if ( !is_null( $pre ) ) {
	return;
  }

  usp_update_user_rating( [
	  'user_id' => $args[ 'object_author' ],
	  'rating_value' => $args[ 'rating_value' ]
  ] );

}

/**
 * Register profile tabs
 */
add_action( 'init', 'usp_rating_profile_tabs', 10 );

function usp_rating_profile_tabs() {

  $tab_data = array(
	  'id' => 'rating',
	  'name' => __( 'Rating', 'userspace-rating' ),
	  'supports' => array( 'ajax' ),
	  'public' => 1,
	  'icon' => 'fa-comments',
	  'output' => 'counters',
	  'counter' => usp_get_user_rating( 88546 ),
	  'content' => [
		  [
			  'id' => 'rating',
			  'name' => __( 'Rating history', 'userspace-rating' ),
			  'title' => __( 'Rating history', 'userspace-rating' ),
			  'callback' => [
				  'name' => 'usp_rating_profile_tab_content'
			  ]
		  ]
	  ]
  );

  usp_tab( $tab_data );

}

function usp_rating_profile_tab_content($master_lk) {

  global $usp_office;

  USP()->use_module( 'content-manager' );

  $manager = new USP_Rating_Votes_List_Manager( [
	  'object_author' => $usp_office
  ] );

  $content = $manager->get_manager();

  return $content;

}
