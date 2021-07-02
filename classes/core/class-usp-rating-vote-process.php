<?php

final class USP_Rating_Vote_Process {

  private $user_id;
  private $object_id;
  private $object_author;
  private $object_type;
  private $rating_value;
  private $rating_date;

  public function __construct($vote_args) {

	$this->user_id = isset( $vote_args[ 'user_id' ] ) ? $vote_args[ 'user_id' ] : null;
	$this->object_id = isset( $vote_args[ 'object_id' ] ) ? $vote_args[ 'object_id' ] : null;
	$this->object_author = isset( $vote_args[ 'object_author' ] ) ? $vote_args[ 'object_author' ] : null;
	$this->object_type = isset( $vote_args[ 'object_type' ] ) ? $vote_args[ 'object_type' ] : null;
	$this->rating_value = isset( $vote_args[ 'rating_value' ] ) ? $vote_args[ 'rating_value' ] : null;
	$this->rating_date = isset( $vote_args[ 'rating_date' ] ) ? $vote_args[ 'rating_date' ] : current_time( 'mysql' );

	if ( $this->object_type instanceof USP_Rating_Object_Type_Abstract ) {
	  $this->object_type = $this->object_type->get_id();
	}

  }

  public function process() {

	$validate = $this->validate();

	if ( is_wp_error( $validate ) ) {
	  return $validate;
	}

	$db_rating_value = USP_Rating()->get_user_vote_value( $this->user_id, $this->object_id, $this->object_type );

	if ( $db_rating_value == $this->rating_value ) {

	  return $this->delete();
	}

	if ( $db_rating_value ) {

	  return $this->update();
	}

	return $this->insert();

  }

  private function validate() {

	$object_type = USP_Rating()->get_object_type( $this->object_type );

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
	if ( !$object_type->is_object_rating_enable( $this->object_id ) ) {

	  $error_message = sprintf( __( "Rating for object_id %s disabled", 'userspace-rating' ), $this->object_id );

	  return new WP_Error( 'userspace-rating', $error_message );
	}

	/**
	 * if Incorrect object_id
	 */
	if ( !$object_type->is_valid_object_id( $this->object_id ) ) {

	  $error_message = sprintf( __( "Incorrect object_id %s", 'userspace-rating' ), $this->object_id );

	  return new WP_Error( 'userspace-rating', $error_message );
	}

	/**
	 * if object_author incorrect
	 */
	if ( $object_type->get_object_author( $this->object_id ) != $this->object_author ) {

	  $error_message = sprintf( __( "Incorrect object_author %s", 'userspace-rating' ), $this->object_author );

	  return new WP_Error( 'userspace-rating', $error_message );
	}

	$rating_type = USP_Rating()->get_rating_type( $object_type->get_option( 'rating_type' ) );

	/**
	 * if rating_type for this object_type not exist or incorrect
	 */
	if ( !$rating_type instanceof USP_Rating_Type_Abstract ) {

	  $error_message = sprintf( __( "Incorrect rating type for object_type %s", 'userspace-rating' ), $object_type->get_id() );

	  return new WP_Error( 'userspace-rating', $error_message );
	}

	/**
	 * if rating value incorrect
	 */
	if ( !$rating_type->is_valid_rating_value( $this->rating_value, $object_type ) ) {

	  $error_message = __( "rating_value incorrect", 'userspace-rating' );

	  return new WP_Error( 'userspace-rating', $error_message );
	}

	$valid_vote = apply_filters( 'userspace_rating_vote_validate', true, $this->get_params() );

	if ( is_wp_error( $valid_vote ) ) {
	  return $valid_vote;
	}

	return true;

  }

  private function get_params() {

	return (object) get_object_vars( $this );

  }

  private function insert() {

	$result = USP_Rating_Votes_Query::insert( [
		'user_id' => $this->user_id,
		'object_id' => $this->object_id,
		'object_author' => $this->object_author,
		'object_type' => $this->object_type,
		'rating_value' => $this->rating_value,
		'rating_date' => $this->rating_date
	] );

	if ( $result === 1 ) {

	  $vote_data = USP_Rating()->get_user_vote( $this->user_id, $this->object_id, $this->object_type );

	  do_action( 'userspace_rating_vote_insert', $vote_data );

	  return $result;
	}

	return new WP_Error( 'userspace-rating', __( 'Error on insert vote', 'userspace-rating' ) );

  }

  private function update() {

	$remove_result = $this->delete();

	if ( $remove_result !== 1 ) {
	  return $remove_result;
	}

	$insert_result = $this->insert();

	return $insert_result;

  }

  private function delete() {

	$vote_data = USP_Rating()->get_user_vote( $this->user_id, $this->object_id, $this->object_type );

	$result = USP_Rating_Votes_Query::delete( [
		'user_id' => $this->user_id,
		'object_id' => $this->object_id,
		'object_type' => $this->object_type
	] );

	if ( $result === 1 ) {
	  do_action( 'userspace_rating_vote_delete', $vote_data );

	  return $result;
	}

	return new WP_Error( 'userspace-rating', __( 'Error on delete vote', 'userspace-rating' ) );

  }

}
