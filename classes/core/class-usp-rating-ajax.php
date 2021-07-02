<?php

class USP_Rating_Ajax {

  public function __construct() {
	
  }

  /**
   * Process ajax request from USP_Rating
   */
  public function process() {

	$method = isset( $_POST[ 'method' ] ) ? $_POST[ 'method' ] : '';
	$params = isset( $_POST[ 'params' ] ) ? $_POST[ 'params' ] : [];

	if ( !method_exists( $this, $method ) ) {

	  $this->error( __( 'Incorrect method', 'userspace-rating' ) );
	}

	$this->$method( $params );

  }

  private function process_vote($params) {

	$user_id = get_current_user_id();
	$object_id = $params[ 'object_id' ];
	$object_type = $params[ 'object_type' ];
	$object_author = $params[ 'object_author' ];
	$rating_value = round( $params[ 'rating_value' ], USERSPACE_RATING_PRECISION );

	$vote = new USP_Rating_Vote_Process( [
		'user_id' => $user_id,
		'object_id' => $object_id,
		'object_type' => $object_type,
		'object_author' => $object_author,
		'rating_value' => $rating_value
	] );

	$result = $vote->process();

	if ( is_wp_error( $result ) ) {
	  $this->error( $result->get_error_message() );
	}

	if ( !$result ) {
	  $this->error( __( 'Unknown error', 'userspace-rating' ) );
	}

	$this->success( '', [
		'html' => USP_Rating()->get_rating_box( $object_id, $object_author, $object_type )
	] );

  }

  public function object_votes($params) {

	$object_id = $params[ 'object_id' ];
	$object_type_id = $params[ 'object_type' ];

	$object_type = USP_Rating()->get_object_type( $object_type_id );

	if ( !$object_type ) {
	  $this->error( __( 'Object type not found', 'userspace-rating' ) );
	}

	$votes = USP_Rating()->get_object_votes( $object_id, $object_type->get_id() );

	if ( !$votes ) {
	  $this->error( __( 'No votes', 'userspace-rating' ) );
	}

	$html = usp_get_include_template( 'usp-rating-votes-list.php', USERSPACE_RATING_PATH . 'userspace-rating.php', [
		'votes' => $votes,
		'object_type' => $object_type,
		'context' => 'object'
	] );

	$this->success( '', [
		'html' => $html
	] );

  }

  private function error($message = '') {

	wp_send_json( [ 'error' => $message ] );

  }

  private function success($message = '', $data = []) {

	wp_send_json( array_merge( [ 'success' => $message ], $data ) );

  }

}
