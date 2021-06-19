<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://user-space.com
 * @since      1.0.0
 */
class USP_Rating {

  private $loader = null;
  private $rating_types = null;
  private $object_types = null;
  private static $_instance = null;

  protected function __construct() {

	$this->init_loader();

  }

  /**
   * Run the loader to execute all of the hooks with WordPress.
   * 
   * @return void
   */
  public function run() {

	$this->loader->run();

  }

  public static function get_instance() {

	if (is_null(self::$_instance)) {
	  self::$_instance = new self();
	}

	return self::$_instance;

  }

  /**
   * 
   * @return USP_Rating_Types
   */
  public function get_rating_types() {

	if (is_null($this->rating_types)) {
	  $this->rating_types = new USP_Rating_Types();
	}

	return $this->rating_types->get();

  }

  /**
   * 
   * @param string $id
   * 
   * @return object|false - instance of usp rating type or false
   */
  public function get_rating_type($id) {

	$rating_types = $this->get_rating_types();

	return $rating_types->get($id);

  }

  /**
   * 
   * @return USP_Rating_Object_Types
   */
  public function get_object_types() {

	if (is_null($this->object_types)) {
	  $this->object_types = new USP_Rating_Object_Types();
	}

	return $this->object_types->get();

  }

  /**
   * 
   * @param string $id
   * 
   * @return object|false - instance of usp object type or false
   */
  public function get_object_type($id) {

	$object_types = $this->get_object_types();

	return $object_types->get($id);

  }

  /**
   * Load the required dependencies for this plugin.
   *
   * Create an instance of the loader which will be used to register the hooks
   * with WordPress.
   * 
   * @return void
   */
  private function init_loader() {

	$this->loader = new USP_Rating_Loader();

  }

}
