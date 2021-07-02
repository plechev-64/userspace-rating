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

}
