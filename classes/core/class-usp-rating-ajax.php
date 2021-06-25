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
	$object_id = (int) $params[ 'object_id' ];
	$object_type = $params[ 'object_type' ];
	$object_author = $params[ 'object_author' ];
	$rating_value = $params[ 'rating_value' ];

	$result = USP_Rating()->add_object_vote( [
		'user_id' => $user_id,
		'object_id' => $object_id,
		'object_type' => $object_type,
		'object_author' => $object_author,
		'rating_value' => $rating_value
	] );

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

  private function error($message = '') {

	wp_send_json( [ 'error' => $message ] );

  }

  private function success($message = '', $data = []) {

	wp_send_json( array_merge( [ 'success' => $message ], $data ) );

  }

}
