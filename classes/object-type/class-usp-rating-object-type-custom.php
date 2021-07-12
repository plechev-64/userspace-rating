<?php

class USP_Rating_Object_Type_Custom extends USP_Rating_Object_Type_Abstract {

  private $id;
  private $name;

  public function __construct() {

	$this->id = 'custom';
	$this->name = __( 'Custom', 'userspace-rating' );

  }

  public function get_id() {
	return $this->id;

  }

  public function get_name() {
	return $this->name;

  }

  public function get_object_author($object_id) {

	return 0;

  }

  public function get_object_name($object_id) {

	return '';

  }

  public function get_object_url($object_id) {

	return '';

  }

  public function is_valid_object_id($object_id) {

	return (get_userdata( $object_id )) ? true : false;

  }

  public function convert_vote_to_template($vote, $template = '') {

	$result = sprintf( __( 'Rating change: %s', 'userspace-rating' ), $vote->rating_value );

	return $result;

  }

  public function is_public() {
	return false;

  }

  public function filter_options($options) {

	return $options;

  }

}
