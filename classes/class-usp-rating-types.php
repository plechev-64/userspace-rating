<?php

class USP_Rating_Types {

  /**
   * Array of instances with all registered rating types
   */
  protected $types = [];

  public function __construct() {

	do_action('userspace_rating_types', $this);

  }

  /**
   * Register new rating type
   * 
   * @param object $rating_type must extends USP_Rating_Type_Abstract
   * 
   * @return bool
   */
  public function add($rating_type) {

	if ($rating_type && $rating_type instanceof USP_Rating_Type_Abstract) {

	  if ($this->get($rating_type->get_id()) === false) {

		$this->types[] = $rating_type;
	  }

	  return true;
	}

	return false;

  }

  /**
   * Get rating type instance by id
   * 
   * @param string $id
   * 
   * @return object|array|false - instance of rating type or array of all instances rating types or false
   */
  public function get($id = '') {

	if (!$id) {
	  return $this->types;
	}

	foreach ($this->types as $rating_type) {

	  if ($rating_type->get_id() == $id) {

		return $rating_type;
	  }
	}

	return false;

  }

  /**
   * Remove registered rating type by id
   * 
   * @param string $id
   * 
   * @return bool
   */
  public function remove($id) {

	if (!$id) {
	  return false;
	}

	foreach ($this->types as $key => $rating_type) {

	  if ($rating_type->get_id() == $id) {

		unset($this->types[$key]);

		return true;
	  }
	}

	return false;

  }

}
