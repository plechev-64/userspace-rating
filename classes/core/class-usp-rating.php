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
	
	$this->run();

  }

  public static function get_instance() {

	if ( is_null( self::$_instance ) ) {
	  self::$_instance = new self();
	}

	return self::$_instance;

  }

  /**
   * @return USP_Rating_Types
   */
  public function get_rating_types() {

	if ( is_null( $this->rating_types ) ) {
	  $this->rating_types = new USP_Rating_Types();
	}

	return $this->rating_types;

  }

  /**
   * @param string $id
   * 
   * @return object|false - instance of usp rating type or false
   */
  public function get_rating_type($id) {

	$rating_types = $this->get_rating_types();

	return $rating_types->get( $id );

  }

  /**
   * @return USP_Rating_Object_Types
   */
  public function get_object_types() {

	if ( is_null( $this->object_types ) ) {
	  $this->object_types = new USP_Rating_Object_Types();
	}

	return $this->object_types;

  }

  /**
   * @param string $id
   * 
   * @return object|false - instance of usp object type or false
   */
  public function get_object_type($id) {

	$object_types = $this->get_object_types();

	return $object_types->get( $id );

  }

  /**
   * Receives html code of rating box
   * 
   * @param int object_id - post_id_ comment_id, etc...
   * @param int object_author - user_id
   * @param object|string $object_type - USP_Rating_Object_Type instance or id USP_Rating_Object_Type
   * 
   * @return string - html code of rating box
   *  
   */
  public function get_rating_box($object_id, $object_author, $object_type) {

	if ( !$object_type instanceof USP_Rating_Object_Type_Abstract ) {
	  $object_type = $this->get_object_type( $object_type );
	}

	/**
	 * if rating for this object disabled
	 */
	if ( !$object_type->get_option( 'rating' ) ) {
	  return false;
	}

	/**
	 * if rating for this object_id disabled
	 */
	if ( !$object_type->is_rating_enable( $object_id ) ) {
	  return false;
	}

	$rating_type = $this->get_rating_type( $object_type->get_option( 'rating_type' ) );

	if ( !$rating_type instanceof USP_Rating_Type_Abstract ) {
	  return false;
	}

	return $rating_type->get_rating_box( $object_id, $object_author, $object_type );

  }

  /**
   * Run the loader to execute all of the hooks with WordPress.
   * 
   * @return void
   */
  private function run() {
	
	$this->loader = new USP_Rating_Loader();

	$this->loader->run();

  }

}
