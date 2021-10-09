<?php

final class USP_Rating_Vote_Process {

	private $user_id;
	private $object_id;
	private $object_author;
	private $object_type;
	private $rating_value;
	private $rating_date;

	function __construct( $vote_args ) {

		if ( is_object( $vote_args ) ) {
			$vote_args = (array) $vote_args;
		}

		$this->user_id       = $vote_args['user_id'] ?? null;
		$this->object_id     = $vote_args['object_id'] ?? null;
		$this->object_author = $vote_args['object_author'] ?? null;
		$this->object_type   = $vote_args['object_type'] ?? null;
		$this->rating_value  = $vote_args['rating_value'] ?? null;
		$this->rating_date   = $vote_args['rating_date'] ?? current_time( 'mysql' );

	}

	function process() {

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

		if ( ! $object_type ) {

			$error_message = __( "Incorrect object type", 'userspace-rating' );

			return new WP_Error( 'userspace-rating', $error_message );
		}

		/*
		 * if rating for this object_type disabled
		 */
		if ( ! $object_type->get_option( 'rating' ) ) {

			$error_message = sprintf( __( "Rating for object type %s is disabled", 'userspace-rating' ), $object_type->get_id() );

			return new WP_Error( 'userspace-rating', $error_message );
		}

		/*
		 * if Incorrect object_id
		 */
		if ( ! $object_type->is_valid_object_id( $this->object_id ) ) {

			$error_message = sprintf( __( "Incorrect object id %s", 'userspace-rating' ), $this->object_id );

			return new WP_Error( 'userspace-rating', $error_message );
		}

		/*
		 * if rating for this object_id disabled
		 */
		if ( ! $object_type->is_object_rating_enable( $this->object_id ) ) {

			$error_message = sprintf( __( "Rating for object id %s is disabled", 'userspace-rating' ), $this->object_id );

			return new WP_Error( 'userspace-rating', $error_message );
		}

		/*
		 * if object_author incorrect
		 */
		if ( $object_type->get_object_author( $this->object_id ) != $this->object_author ) {

			$error_message = sprintf( __( "Incorrect object author %s", 'userspace-rating' ), $this->object_author );

			return new WP_Error( 'userspace-rating', $error_message );
		}

		$rating_type = USP_Rating()->get_rating_type( $object_type->get_option( 'rating_type' ) );

		/*
		 * if rating_type for this object_type not exist or incorrect
		 */
		if ( ! $rating_type ) {

			$error_message = sprintf( __( "Incorrect rating type", 'userspace-rating' ), $object_type->get_id() );

			return new WP_Error( 'userspace-rating', $error_message );
		}

		/*
		 * if rating value incorrect
		 */
		if ( ! $rating_type->is_valid_rating_value( $this->rating_value, $object_type ) ) {

			$error_message = __( "Incorrect vote value", 'userspace-rating' );

			return new WP_Error( 'userspace-rating', $error_message );
		}

		$rating_box = new USP_Rating_Box( [
			'object_type' => $object_type,
			'object_id'   => $this->object_id,
			'user_id'     => $this->user_id
		] );

		$rating_box->init();

		if ( ! $rating_box->get_param( 'user_can_vote' ) ) {

			$error_message = __( "You can't vote on this object", 'userspace-rating' );

			return new WP_Error( 'userspace-rating', $error_message );
		}

		return true;

	}

	private function get_params() {

		return get_object_vars( $this );

	}

	private function insert() {

		$validate = $this->validate();

		if ( is_wp_error( $validate ) ) {
			return $validate;
		}

		$result = usp_insert_vote( [
			'user_id'       => $this->user_id,
			'object_id'     => $this->object_id,
			'object_author' => $this->object_author,
			'object_type'   => $this->object_type,
			'rating_value'  => $this->rating_value,
			'rating_date'   => $this->rating_date
		] );

		return $result ? $result : new WP_Error( 'userspace-rating', __( 'Error on insert vote', 'userspace-rating' ) );

	}

	private function update() {

		$remove_result = $this->delete();

		if ( $remove_result !== true ) {
			return $remove_result;
		}

		$insert_result = $this->insert();

		return $insert_result;

	}

	private function delete() {

		$validate = $this->validate();

		if ( is_wp_error( $validate ) ) {
			return $validate;
		}

		$result = usp_delete_vote( [
			'user_id'     => $this->user_id,
			'object_id'   => $this->object_id,
			'object_type' => $this->object_type
		] );

		return $result ? $result : new WP_Error( 'userspace-rating', __( 'Error on delete vote', 'userspace-rating' ) );

	}

}
