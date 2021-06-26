<?php

class USP_Rating_Type_Likes extends USP_Rating_Type_Abstract {

  private $id;
  private $name;

  public function __construct() {

	$this->id = 'likes';
	$this->name = __( 'Likes', 'userspace-rating' );

  }

  public function get_id() {
	return $this->id;

  }

  public function get_name() {
	return $this->name;

  }

  public function is_valid_rating_value($rating_value, $object_type) {

	$option_rating_value = $object_type->get_option( 'rating_likes_value' );

	return $option_rating_value == $rating_value;

  }

  /**
   * @param int $object_id - post_id, comment_id etc...
   * @param int $object_author - user_id
   * @param object $object_type - rating object type
   * 
   * @return string | bool - html code of rating box or false if rating for object disabled
   */
  public function get_rating_box($object_id, $object_author, $object_type) {

	$counting_type = $object_type->get_option( 'rating_likes_overall' );
	$rating_points = $object_type->get_option( 'rating_likes_value' );

	$icons = [
		'default' => $object_type->get_option( 'rating_likes_icon_default' ),
		'voted' => $object_type->get_option( 'rating_likes_icon_voted' )
	];

	$user_vote = null;

	if ( $counting_type ) {

	  /**
	   * If counting_type == 1, $object_rating = number of votes
	   */
	  $object_rating = USP_Rating()->get_object_votes_count( $object_id, $object_type );
	} else {
	  /**
	   * If counting_type == 0, $object_rating = sum of votes
	   */
	  $object_rating = USP_Rating()->get_object_rating( $object_id, $object_type );
	}

	if ( !$object_rating ) {

	  $object_rating = 0;
	}

	$user_can_vote = get_current_user_id() && get_current_user_id() != $object_author;

	if ( $user_can_vote ) {
	  $user_vote = USP_Rating()->get_user_vote( get_current_user_id(), $object_id, $object_type );
	}

	$user_can_view_history = true;

	$html = usp_get_include_template( 'usp-rating-' . $this->get_id() . '.php', USERSPACE_RATING_PATH . 'userspace-rating.php', [
		'object_type' => $object_type,
		'object_id' => $object_id,
		'object_author' => $object_author,
		'user_can_vote' => $user_can_vote,
		'user_vote' => $user_vote,
		'object_rating' => $object_rating,
		'user_can_view_history' => $user_can_view_history,
		'rating_points' => $rating_points,
		'icons' => $icons
	] );

	return $html;

  }

  /**
   * @param $object_type - Current rating object type
   * 
   * @return array - Array of custom options for rating type likes
   */
  public function get_custom_options($object_type) {
	return [
		[
			'type' => 'number',
			'slug' => 'rating_likes_value_' . $object_type->get_id(),
			'title' => __( 'Rating value', 'userspace-rating' ),
			'default' => 1
		],
		[
			'type' => 'text',
			'slug' => 'rating_likes_icon_default_' . $object_type->get_id(),
			'title' => __( 'Default class icon', 'userspace-rating' ),
			'default' => 'fa-heart'
		],
		[
			'type' => 'text',
			'slug' => 'rating_likes_icon_voted_' . $object_type->get_id(),
			'title' => __( 'Voted class icon', 'userspace-rating' ),
			'default' => 'fa-heart-fill'
		],
		[
			'type' => 'select',
			'slug' => 'rating_likes_overall_' . $object_type->get_id(),
			'title' => __( 'Overall rating', 'userspace-rating' ) . ' ' . $object_type->get_name(),
			'values' => array( __( 'Sum of ratings', 'userspace-rating' ), __( 'Number of votes', 'userspace-rating' ) )
		]
	];

  }

}
