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
	abstract public function is_valid_rating_value( $rating_value, $object_type );

	/**
	 * Display vote buttons and object rating value
	 *
	 * @param array $params - rating box params
	 *
	 * @return string - html code of rating box
	 */
	abstract public function get_vote_buttons_and_value( $params );

	/**
	 * @param float $rating_value - rating value
	 * @param object $object_type - rating object type
	 *
	 * @return string - converted value to html
	 */
	public function get_html_from_value( $rating_value, $object_type ) {
		return $rating_value;

	}

	/**
	 * @param $object_type - Current rating object type
	 *
	 * @return array - Array of custom options for rating type
	 */
	public function get_custom_options( $object_type ) {
		return [];

	}

	/**
	 * If the rating type is for public objects - return true
	 *
	 * @return bool
	 */
	public function is_public() {
		return true;

	}

}
