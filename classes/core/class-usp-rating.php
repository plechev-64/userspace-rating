<?php

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
   * @return \USP_Rating_Types
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
   * @return \USP_Rating_Object_Types
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
   * @param object|string $object_type - object type instance or object type id
   * 
   * @return string - html code of rating box
   */
  public function get_rating_box($object_id, $object_author, $object_type) {

	if ( !$object_type instanceof USP_Rating_Object_Type_Abstract ) {
	  $object_type = $this->get_object_type( $object_type );
	}

	/*
	 * if rating for this object disabled
	 */
	if ( !$object_type->get_option( 'rating' ) ) {
	  return false;
	}

	/*
	 * if rating for this object_id disabled
	 */
	if ( !$object_type->is_object_rating_enable( $object_id ) ) {
	  return false;
	}

	$rating_type = $this->get_rating_type( $object_type->get_option( 'rating_type' ) );

	if ( !$rating_type instanceof USP_Rating_Type_Abstract ) {
	  return false;
	}

	return $rating_type->get_rating_box( $object_id, $object_author, $object_type );

  }

  /**
   * 
   * @return \USP_Rating_Totals_Query
   */
  public function totals_query() {
	return new USP_Rating_Totals_Query();

  }

  /**
   * 
   * @return \USP_Rating_Votes_Query
   */
  public function votes_query() {
	return new USP_Rating_Votes_Query();

  }

  /**
   * 
   * @return \USP_Rating_Users_Query
   */
  public function users_query() {
	return new USP_Rating_Users_Query();

  }

  /**
   * @param int $user_id
   * @param int $object_id
   * @param object|string $object_type_id
   * 
   * @return string - $user_id vote value for $object_id
   */
  public function get_user_vote_value($user_id, $object_id, $object_type_id) {

	if ( $object_type_id instanceof USP_Rating_Object_Type_Abstract ) {
	  $object_type_id = $object_type_id->get_id();
	}

	$query = $this->votes_query();

	return $query->select( [ 'rating_value' ] )
	->where( [ 'user_id' => $user_id, 'object_id' => $object_id, 'object_type' => $object_type_id ] )
	->get_var();

  }

  /**
   * @param int $user_id
   * @param int $object_id
   * @param object|string $object_type_id
   * 
   * @return object|null - vote data
   */
  public function get_user_vote($user_id, $object_id, $object_type_id) {

	if ( $object_type_id instanceof USP_Rating_Object_Type_Abstract ) {
	  $object_type_id = $object_type_id->get_id();
	}

	$query = $this->votes_query();

	return $query->select( [] )
	->where( [ 'user_id' => $user_id, 'object_id' => $object_id, 'object_type' => $object_type_id ] )
	->get_row();

  }

  /**
   * @param int $object_id
   * @param object|string $object_type_id
   * 
   * @return string|null - total rating of $object_id
   */
  public function get_object_rating($object_id, $object_type_id) {

	if ( $object_type_id instanceof USP_Rating_Object_Type_Abstract ) {
	  $object_type_id = $object_type_id->get_id();
	}

	if ( USERSPACE_RATING_CACHE ) {

	  $object_rating = $this->get_cache( $object_type_id, $object_id, 'rating_total' );

	  if ( $object_rating !== false ) {
		return $object_rating;
	  }
	}

	$query = $this->totals_query();

	return $query->select( [ 'rating_total' ] )
	->where( [ 'object_id' => $object_id, 'object_type' => $object_type_id ] )
	->get_var();

  }

  /**
   * @param int $object_id
   * @param object|string $object_type_id
   * 
   * @return array - array of all votes for object
   */
  public function get_object_votes($object_id, $object_type_id) {

	if ( $object_type_id instanceof USP_Rating_Object_Type_Abstract ) {
	  $object_type_id = $object_type_id->get_id();
	}

	$query = $this->votes_query();

	return $query->select( [] )
	->where( [ 'object_id' => $object_id, 'object_type' => $object_type_id ] )
	->get_results();

  }

  /**
   * @param int $object_id
   * @param object|string $object_type_id
   * 
   * @return int - votes count for $object_id
   */
  public function get_object_votes_count($object_id, $object_type_id) {

	if ( $object_type_id instanceof USP_Rating_Object_Type_Abstract ) {
	  $object_type_id = $object_type_id->get_id();
	}

	$query = $this->votes_query();

	return $query->select( [ 'ID' ] )
	->where( [ 'object_id' => $object_id, 'object_type' => $object_type_id ] )
	->get_count();

  }

  /**
   * @param int $user_id
   * 
   * @return int|null - total rating of $user_id
   */
  public function get_user_rating($user_id) {

	if ( USERSPACE_RATING_CACHE ) {

	  $user_rating = $this->get_cache( 'user', $user_id, 'rating_total' );

	  if ( $user_rating !== false ) {
		return $user_rating;
	  }
	}

	$query = $this->users_query();

	return $query->select( [ 'rating_total' ] )
	->where( [ 'user_id' => $user_id ] )
	->get_var();

  }

  /**
   * Add/Update cached rating data
   * 
   * @param string $cache_slug
   * @param int $object_id
   * @param array $data
   * 
   * @return bool
   */
  public function set_cache($cache_slug, $object_id, $data) {

	if ( !USERSPACE_RATING_CACHE ) {
	  return false;
	}

	$cache_key = $cache_slug . '_' . $object_id;

	$cache = wp_cache_get( $cache_key, 'userspace_rating' );

	$old_data = $cache === false ? [] : $cache;

	$to_cache = array_merge( $old_data, $data );

	return wp_cache_set( $cache_key, $to_cache, 'userspace_rating', USERSPACE_RATING_CACHE_TIME );

  }

  /**
   * Get rating data from cache
   * 
   * @param string $cache_slug
   * @param int $object_id
   * @param string|false $data_key
   * 
   * @return array|string|bool - cache value
   */
  public function get_cache($cache_slug, $object_id, $data_key = false) {

	if ( !USERSPACE_RATING_CACHE ) {
	  return false;
	}

	$cache_key = $cache_slug . '_' . $object_id;

	$cache = wp_cache_get( $cache_key, 'userspace_rating' );
	
	if ( $cache === false ) {
	  return false;
	}

	if ( !$data_key ) {
	  return $cache;
	}

	if ( isset( $cache[ $data_key ] ) ) {
	  return $cache[ $data_key ];
	}

	return false;

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
