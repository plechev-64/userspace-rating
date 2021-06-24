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
   * @param object $object_type
   * 
   * @return string - $user_id vote value for $object_id
   */
  public function get_user_vote($user_id, $object_id, $object_type) {

	if ( $object_type instanceof USP_Rating_Object_Type_Abstract ) {
	  $object_type = $object_type->get_id();
	}

	$query = $this->votes_query();

	return $query->select( [ 'rating_value' ] )
	->where( [ 'user_id' => $user_id, 'object_id' => $object_id, 'object_type' => $object_type ] )
	->get_var();

  }

  /**
   * @param int $object_id
   * @param object|string $object_type
   * 
   * @return string - total rating of $object_id
   */
  public function get_object_rating($object_id, $object_type) {

	if ( $object_type instanceof USP_Rating_Object_Type_Abstract ) {
	  $object_type = $object_type->get_id();
	}
	
	$query = $this->totals_query();

	return $query->select( [ 'rating_total' ] )
	->where( [ 'object_id' => $object_id, 'object_type' => $object_type ] )
	->get_var();

  }

  /**
   * @param int $object_id
   * @param object|string $object_type
   * 
   * @return array - array of all votes for object
   */
  public function get_object_votes($object_id, $object_type) {

	if ( $object_type instanceof USP_Rating_Object_Type_Abstract ) {
	  $object_type = $object_type->get_id();
	}

	$query = $this->votes_query();

	return $query->select( [] )
	->where( [ 'object_id' => $object_id, 'object_type' => $object_type ] )
	->get_results();

  }

  /**
   * @param int $object_id
   * @param object|string $object_type
   * 
   * @return string - votes count for $object_id
   */
  public function get_object_votes_count($object_id, $object_type) {

	if ( $object_type instanceof USP_Rating_Object_Type_Abstract ) {
	  $object_type = $object_type->get_id();
	}

	$query = $this->votes_query();

	return $query->select( [ 'ID' ] )
	->where( [ 'object_id' => $object_id, 'object_type' => $object_type ] )
	->get_count();

  }

  /**
   * Insert / update user vote
   * 
   * array['user_id'] - int (required)
   * array['object_id'] - int (required)
   * array['object_author'] - int (required)
   * array['object_type'] - object|string (required)
   * array['rating_value'] - numeric value (required)
   * array['rating_date'] - vote date
   * 
   * @param array $_args
   * 
   * @return bool|WP_Error
   */
  public function add_object_vote($_args) {

	$args = $this->prepare_vote_args( $_args );
	$is_valid_vote_args = $this->is_valid_vote_args( $args );

	if ( is_wp_error( $is_valid_vote_args ) ) {
	  return $is_valid_vote_args;
	}

	$object_type = $args[ 'object_type' ];

	$rating_type = $this->get_rating_type( $object_type->get_option( 'rating_type' ) );

	/**
	 * if rating_type for this object_type not exist or incorrect
	 */
	if ( !$rating_type instanceof USP_Rating_Type_Abstract ) {

	  $error_message = sprintf( __( "Rating type for object_type %s not found", 'userspace-rating' ), $object_type->get_id() );

	  return new WP_Error( 'userspace-rating', $error_message );
	}

	/**
	 * if rating value incorrect
	 */
	if ( !$rating_type->is_valid_rating_value( $args[ 'rating_value' ], $object_type ) ) {

	  $error_message = __( "rating_value incorrect", 'userspace-rating' );

	  return new WP_Error( 'userspace-rating', $error_message );
	}


	$prev_rating_value = $this->get_user_vote( $args[ 'user_id' ], $args[ 'object_id' ], $object_type );

	$args[ 'object_type' ] = $args[ 'object_type' ]->get_id();

	if ( !is_null( $prev_rating_value ) ) {

	  if ( $prev_rating_value == $args[ 'rating_value' ] ) {
		/**
		 * Remove vote
		 */
		return $this->remove_vote( $args );
	  } else {
		/**
		 * Update vote
		 */
		return $this->update_vote( $args );
	  }
	}

	/**
	 * Insert vote
	 */
	return $this->insert_vote( $args );

  }

  /**
   * Insert user vote
   * 
   * Use $this->add_object_vote
   * 
   * @param array $args
   * 
   * @return bool
   */
  private function insert_vote($args) {

	$result = USP_Rating_Votes_Query::insert( $args );

	if ( $result === 1 ) {
	  do_action( 'userspace_rating_insert_vote', $args );
	}

	return $result;

  }

  /**
   * Update vote for object
   * 
   * Use $this->add_object_vote
   * 
   * @param array $args
   * 
   * @return bool|WP_Error
   */
  private function update_vote($args) {

	$remove_result = $this->remove_vote( $args );

	if ( $remove_result !== 1 ) {
	  return $remove_result;
	}

	$insert_result = $this->insert_vote( $args );

	return $insert_result;

  }

  /**
   * Remove vote for object
   * 
   * array['user_id'] - int (required)
   * array['object_id'] - int (required)
   * array['object_type'] - object|string (required)
   * 
   * @param array $args
   * 
   * @return bool|WP_Error
   */
  public function remove_vote($args) {

	$user_id = $args[ 'user_id' ];
	$object_id = $args[ 'object_id' ];
	$object_type = $args[ 'object_type' ];

	if ( !$object_type instanceof USP_Rating_Object_Type_Abstract ) {

	  $object_type = $this->get_object_type( $object_type );
	}

	if ( !$object_type instanceof USP_Rating_Object_Type_Abstract ) {

	  $error_message = __( "Incorrect object_type", 'userspace-rating' );

	  return new WP_Error( 'userspace-rating', $error_message );
	}

	$result = USP_Rating_Votes_Query::delete( [
		'user_id' => (int) $user_id,
		'object_id' => (int) $object_id,
		'object_type' => $object_type->get_id()
	] );

	if ( $result === 1 ) {
	  do_action( 'userspace_rating_remove_vote', $args );
	}

	return $result;

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

  private function prepare_vote_args($args) {

	$prepared_args = wp_parse_args( $args, [
		'user_id' => 0,
		'object_id' => 0,
		'object_author' => 0,
		'object_type' => '',
		'rating_value' => 0,
		'rating_date' => current_time( 'mysql' )
	] );

	if ( !$prepared_args[ 'object_type' ] instanceof USP_Rating_Object_Type_Abstract ) {
	  $prepared_args[ 'object_type' ] = $this->get_object_type( $prepared_args[ 'object_type' ] );
	}

	return $prepared_args;

  }

  private function is_valid_vote_args($args) {

	$object_type = $args[ 'object_type' ];

	if ( !$object_type instanceof USP_Rating_Object_Type_Abstract ) {

	  $error_message = __( "Incorrect object_type", 'userspace-rating' );

	  return new WP_Error( 'userspace-rating', $error_message );
	}

	/**
	 * if rating for this object_type disabled
	 */
	if ( !$object_type->get_option( 'rating' ) ) {

	  $error_message = sprintf( __( "Rating for object_type %s disabled", 'userspace-rating' ), $object_type->get_id() );

	  return new WP_Error( 'userspace-rating', $error_message );
	}

	/**
	 * if rating for this object_id disabled
	 */
	if ( !$object_type->is_rating_enable( $args[ 'object_id' ] ) ) {

	  $error_message = sprintf( __( "Rating for object_id %s disabled", 'userspace-rating' ), $args[ 'object_id' ] );

	  return new WP_Error( 'userspace-rating', $error_message );
	}

	/**
	 * if Incorrect object_id
	 */
	if ( !$object_type->is_valid_object_id( $args[ 'object_id' ] ) ) {

	  $error_message = sprintf( __( "Incorrect object_id %s", 'userspace-rating' ), $args[ 'object_id' ] );

	  return new WP_Error( 'userspace-rating', $error_message );
	}

	/**
	 * if object_author incorrect
	 */
	if ( $object_type->get_object_author( $args[ 'object_id' ] ) != $args[ 'object_author' ] ) {

	  $error_message = sprintf( __( "Incorrect object_author %s", 'userspace-rating' ), $args[ 'object_author' ] );

	  return new WP_Error( 'userspace-rating', $error_message );
	}

	/**
	 * if user_id cannot vote for this object_id
	 */
	if ( !$object_type->user_can_vote( $args[ 'user_id' ], $args[ 'object_id' ], $args[ 'object_author' ] ) ) {

	  $error_message = __( "You cannot vote for this object", 'userspace-rating' );

	  return new WP_Error( 'userspace-rating', $error_message );
	}

	return true;

  }

}
