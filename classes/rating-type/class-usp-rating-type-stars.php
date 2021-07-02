<?php

class USP_Rating_Type_Stars extends USP_Rating_Type_Abstract {

  private $id;
  private $name;

  public function __construct() {

	$this->id = 'stars';
	$this->name = __( 'Stars', 'userspace-rating' );

  }

  public function get_id() {
	return $this->id;

  }

  public function get_name() {
	return $this->name;

  }

  public function is_valid_rating_value($rating_value, $object_type) {

	$stars_values = $this->get_stars_values( $object_type );

	return in_array( $rating_value, $stars_values );

  }

  /**
   * @param int $object_id - post_id, comment_id etc...
   * @param int $object_author - user_id
   * @param object $object_type - rating object type
   * 
   * @return string | bool - html code of rating box or false if rating for object disabled
   */
  public function get_rating_box($object_id, $object_author, $object_type) {

	$stars_values = $this->get_stars_values( $object_type );

	$object_rating = USP_Rating()->get_object_rating( $object_id, $object_type );

	if ( !$object_rating ) {

	  $object_rating = 0;
	}

	if ( $object_rating ) {
	  $object_votes_count = USP_Rating()->get_object_votes_count( $object_id, $object_type );
	} else {
	  $object_votes_count = 0;
	}

	$user_vote = null;

	$user_can_vote = get_current_user_id() && get_current_user_id() != $object_author;

	if ( $user_can_vote && $object_rating ) {
	  $user_vote = USP_Rating()->get_user_vote_value( get_current_user_id(), $object_id, $object_type );
	}

	$user_can_view_history = true;

	$average_rating = $object_rating && $object_votes_count ? round( $object_rating / $object_votes_count, USERSPACE_RATING_PRECISION ) : 0;

	$stars_percent = $this->get_stars_percent( $stars_values, $average_rating );

	$html = usp_get_include_template( 'usp-rating-' . $this->get_id() . '.php', USERSPACE_RATING_PATH . 'userspace-rating.php', [
		'object_type' => $object_type,
		'object_id' => $object_id,
		'object_author' => $object_author,
		'user_can_vote' => $user_can_vote,
		'user_vote' => $user_vote,
		'object_rating' => $object_rating,
		'user_can_view_history' => $user_can_view_history,
		'average_rating' => $average_rating,
		'stars_values' => $stars_values,
		'stars_percent' => $stars_percent
	] );

	return $html;

  }

  public function get_html_from_value($rating_value, $object_type) {

	$stars_values = $this->get_stars_values( $object_type );

	$stars_percent = $this->get_stars_percent( $stars_values, $rating_value );

	$html = usp_get_include_template( 'usp-rating-' . $this->get_id() . '.php', USERSPACE_RATING_PATH . 'userspace-rating.php', [
		'object_type' => $object_type,
		'object_id' => 0,
		'object_author' => 0,
		'user_can_vote' => false,
		'user_vote' => $rating_value,
		'object_rating' => $rating_value,
		'user_can_view_history' => false,
		'average_rating' => $rating_value,
		'stars_values' => $stars_values,
		'stars_percent' => $stars_percent
	] );

	return $html;

  }

  private function get_stars_percent($stars_values, $total_rating) {

	$stars = array_fill( 1, count( $stars_values ), 0 );

	foreach ( $stars_values as $star_num => $star_value ) {

	  if ( $total_rating >= $star_value ) {
		$stars[ $star_num ] = 100;
		continue;
	  }

	  $star_percent = round( (($total_rating - $stars_values[ $star_num - 1 ]) / $stars_values[ 1 ]) * 100 );

	  $stars[ $star_num ] = $star_percent;

	  break;
	}

	return $stars;

  }

  private function get_stars_values($object_type) {

	$stars_count = $object_type->get_option( 'rating_stars_count' );
	$max_rating = $object_type->get_option( 'rating_value' );

	$single_star_rating = round( $max_rating / $stars_count, USERSPACE_RATING_PRECISION );
	$last_star_rating = round( $max_rating, USERSPACE_RATING_PRECISION );

	$stars = [];

	foreach ( range( 1, $stars_count ) as $star_num ) {

	  if ( $stars_count == $star_num ) {
		$value = $last_star_rating;
	  } else {

		$value = round( $single_star_rating * $star_num, USERSPACE_RATING_PRECISION );
	  }

	  $stars[ $star_num ] = $value;
	}

	return $stars;

  }

  /**
   * @param $object_type - rating object type
   * 
   * @return array - Array of custom options for rating type stars
   */
  public function get_custom_options($object_type) {

	return [
		[
			'type' => 'runner',
			'slug' => 'rating_stars_count_' . $object_type->get_id(),
			'title' => __( 'Number of stars', 'userspace-rating' ),
			'value_min' => 1,
			'value_max' => 20,
			'default' => 5
		],
		[
			'type' => 'select',
			'slug' => 'rating_stars_shema_' . $object_type->get_id(),
			'title' => __( 'Rating markup', 'userspace-rating' ),
			'values' => array(
				__( 'Disable', 'userspace-rating' ),
				__( 'Enable', 'userspace-rating' )
			),
			'notice' => __( 'If enabled, the standard markup on single pages along with the rating is displayed as <a href="http://schema.org" target="_blank">http://schema.org</a>', 'userspace-rating' )
		]
	];

  }

}
