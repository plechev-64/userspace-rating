<?php

class USP_Rating_Object_Type_User extends USP_Rating_Object_Type_Abstract {

  private $id;
  private $name;

  public function __construct() {

	$this->id = 'user';
	$this->name = __( 'User', 'userspace-rating' );

  }

  public function get_id() {
	return $this->id;

  }

  public function get_name() {
	return $this->name;

  }

  public function get_object_author($object_id) {

	return $object_id;

  }

  public function get_object_name($object_id) {

	$userdata = get_userdata( $object_id );

	return $userdata->display_name;

  }

  public function get_object_url($object_id) {

	return get_author_posts_url( $object_id );

  }

  public function is_valid_object_id($object_id) {

	return (get_userdata( $object_id )) ? true : false;

  }

  public function is_hidden() {

	return true;

  }

}
