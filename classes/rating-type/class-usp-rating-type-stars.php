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

	$data = $this->get_rating_box_data( $object_id, $object_author, $object_type );

	$stars_values = $this->get_stars_values( $object_type );
	$stars_percent = $this->get_stars_percent( $stars_values, $data[ 'rating_average' ] );

	$html = usp_get_include_template( 'usp-rating-' . $this->get_id() . '.php', USERSPACE_RATING_PATH . 'userspace-rating.php', [
		'object_type' => $object_type,
		'object_id' => $object_id,
		'object_author' => $object_author,
		'user_vote' => $data[ 'user_vote' ],
		'object_rating' => $data[ 'rating' ],
		'average_rating' => $data[ 'rating_average' ],
		'votes_count' => $data[ 'votes_count' ],
		'user_can_vote' => $data[ 'user_can_vote' ],
		'user_can_view_votes' => $data[ 'user_can_view_votes' ],
		'stars_values' => $stars_values,
		'stars_percent' => $stars_percent
	] );

	return $html;

  }

  public function get_html_from_value($rating_value, $object_type) {

	$stars_values = $this->get_stars_values( $object_type );

	$stars_percent = $this->get_stars_percent( $stars_values, $rating_value );

	$stars_html = '<div class="usp-rating-stars usps__inline usp-rating-stars_size_small">';

	foreach ( $stars_values as $star_num => $rating_value ) {

	  if ( $stars_percent[ $star_num ] == 100 ) {

		$stars_html .= '<i class="uspi fa-star-fill"></i>';
	  } else if ( $stars_percent[ $star_num ] > 0 ) {

		$stars_html .= '<i class="uspi fa-star-half-o"></i>';
	  } else {

		$stars_html .= '<i class="uspi fa-star"></i>';
	  }
	}

	$stars_html .= '</div>';

	return $stars_html;

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
