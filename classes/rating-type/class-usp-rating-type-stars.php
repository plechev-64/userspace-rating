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

	$rating_per_star = $object_type->get_option( 'rating_stars_value' );
	$rating_max_stars = $object_type->get_option( 'rating_stars_count' );

	$valid_values = [];

	foreach ( range( 1, $rating_max_stars ) as $star_num ) {
	  $valid_values[] = $star_num * $rating_per_star;
	}

	return in_array( $rating_value, $valid_values );

  }

  /**
   * @param int $object_id - post_id, comment_id etc...
   * @param int $object_author - user_id
   * @param object $object_type - rating object type
   * 
   * @return string | bool - html code of rating box or false if rating for object disabled
   */
  public function get_rating_box($object_id, $object_author, $object_type) {

	$rating_per_star = $object_type->get_option( 'rating_stars_value' );
	$stars_count = $object_type->get_option( 'rating_stars_count' );

	$icons = [
		'empty' => $object_type->get_option( 'rating_stars_icon_empty' ),
		'half' => $object_type->get_option( 'rating_stars_icon_half' ),
		'full' => $object_type->get_option( 'rating_stars_icon_full' )
	];

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
	  $user_vote = USP_Rating()->get_user_vote( get_current_user_id(), $object_id, $object_type );
	}

	$user_can_view_history = true;

	$average_rating = $object_rating && $object_votes_count ? round( $object_rating / $object_votes_count, 2 ) : 0;

	$html = usp_get_include_template( 'usp-rating-' . $this->get_id() . '.php', USERSPACE_RATING_PATH . 'userspace-rating.php', [
		'object_type' => $object_type,
		'object_id' => $object_id,
		'object_author' => $object_author,
		'user_can_vote' => $user_can_vote,
		'user_vote' => $user_vote,
		'object_rating' => $object_rating,
		'user_can_view_history' => $user_can_view_history,
		'stars_count' => $stars_count,
		'average_rating' => $average_rating,
		'rating_per_star' => $rating_per_star,
		'full_stars' => floor( $average_rating / $rating_per_star ),
		'half_star' => ceil( $average_rating ) - floor( $average_rating ),
		'icons' => $icons
	] );

	return $html;

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
			'type' => 'number',
			'slug' => 'rating_stars_value_' . $object_type->get_id(),
			'title' => __( 'Rating value per star', 'userspace-rating' ),
			'default' => 1
		],
		[
			'type' => 'text',
			'slug' => 'rating_stars_icon_empty_' . $object_type->get_id(),
			'title' => __( 'Class icon for empty star', 'userspace-rating' ),
			'default' => 'fa-star'
		],
		[
			'type' => 'text',
			'slug' => 'rating_stars_icon_half_' . $object_type->get_id(),
			'title' => __( 'Class icon for half star', 'userspace-rating' ),
			'default' => 'fa-star-half'
		],
		[
			'type' => 'text',
			'slug' => 'rating_stars_icon_full_' . $object_type->get_id(),
			'title' => __( 'Class icon for full star', 'userspace-rating' ),
			'default' => 'fa-star-fill'
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
