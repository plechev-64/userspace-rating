<?php

class USP_Rating_Type_Plus_Minus extends USP_Rating_Type_Abstract {

  private $id;
  private $name;

  public function __construct() {

	$this->id = 'plus-minus';
	$this->name = __( 'Plus / Minus', 'userspace-rating' );

  }

  public function get_id() {
	return $this->id;

  }

  public function get_name() {
	return $this->name;

  }

  public function is_valid_rating_value($rating_value, $object_type) {

	$option_rating_points = $object_type->get_option( 'rating_points' );

	return $option_rating_points == $option_rating_points;

  }

  /**
   * @param int $object_id - post_id, comment_id etc...
   * @param int $object_author - user_id
   * @param object $object_type - rating object type
   * 
   * @return string | bool - html code of rating box or false if rating for object disabled
   */
  public function get_rating_box($object_id, $object_author, $object_type) {

	/**
	 * Rating counting type 0 - sum of votes / 1 - Number of positive and negative votes 
	 */
	$counting_type = $object_type->get_option( 'rating_' . $this->get_id() . '_overall_' . $object_type->get_id(), 0 );





	return 'html box of rating type Plus/Minus';

  }

  /**
   * @param $object_type - rating object type
   * 
   * @return array - Array of custom options for rating type plus-minus
   */
  public function get_custom_options($object_type) {
	return [
		[
			'type' => 'select',
			'slug' => 'rating_plus-minus_overall_' . $object_type->get_id(),
			'title' => __( 'Overall rating', 'userspace-rating' ) . ' ' . $object_type->get_name(),
			'values' => array( __( 'Sum of votes', 'userspace-rating' ), __( 'Number of positive and negative votes', 'userspace-rating' ) )
		]
	];

  }

}
