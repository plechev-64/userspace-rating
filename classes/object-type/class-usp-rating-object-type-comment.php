<?php

class USP_Rating_Object_Type_Comment extends USP_Rating_Object_Type_Abstract {

  private $id;
  private $name;

  public function __construct() {

	$this->id = 'comment';
	$this->name = __( 'Comments', 'userspace-rating' );

  }

  public function get_id() {
	return $this->id;

  }

  public function get_name() {
	return $this->name;

  }

  public function is_valid_object_id($object_id) {

	return (get_comment( $object_id )) ? true : false;

  }

  public function get_history_template_custom_vars() {

	return [ '%COMMENT%' => __( 'link to comment', 'userspace-rating' ) ];

  }

}
