<?php

class USP_Rating_Object_Type_Posts extends USP_Rating_Object_Type_Abstract {

  private $id;
  private $name;

  /**
   * 
   * @param string $id - unique id
   * @param string $name - name
   */
  public function __construct($id, $name) {

	$this->id = $id;
	$this->name = $name;

  }

  public function get_id() {
	return $this->id;

  }

  public function get_name() {
	return $this->name;

  }

  public function get_history_template_custom_vars() {

	return [ '%POST%' => __( 'link to publication', 'userspace-rating' ) ];

  }

}