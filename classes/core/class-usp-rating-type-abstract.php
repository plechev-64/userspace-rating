<?php

abstract class USP_Rating_Type_Abstract {

  abstract public function get_id();

  abstract public function get_name();

  /**
   * @param int $object_id - post_id, comment_id etc...
   * @param int $object_author - user_id
   * @param object $object_type - rating object type
   * 
   * @return string | bool - html code of rating box or false if rating for object disabled
   */
  abstract public function get_rating_box($object_id, $object_author, $object_type);

  /**
   * @param $object_type - Current rating object type
   * 
   * @return array - Array of custom options for rating type
   */
  public function get_custom_options($object_type) {
	return [];

  }

}
