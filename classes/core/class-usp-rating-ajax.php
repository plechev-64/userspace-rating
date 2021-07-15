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
	$rating_value = round( $params[ 'rating_value' ], USP_RATING_PRECISION );

	$result = usp_process_vote( [
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

	$html = usp_get_include_template( 'usp-rating-votes-list.php', USP_RATING_BASE, [
		'votes' => $votes
	] );

	$this->success( '', [
		'html' => $html
	] );

  }

  public function edit_user_rating($params) {

	if ( !current_user_can( 'administrator' ) ) {
	  $this->error( __( 'You cannot do this', 'userspace-rating' ) );
	}

	$user_id = $params[ 'user_id' ];
	$new_rating = $params[ 'new_rating' ];

	if ( !$user_id ) {
	  $this->error( __( 'Incorrect user_id', 'userspace-rating' ) );
	}

	if ( !is_numeric( $new_rating ) ) {
	  $this->error( __( 'Incorrect rating value', 'userspace-rating' ) );
	}
	
	$current_rating = usp_get_user_rating($user_id);
	
	if(is_null($current_rating)) {
	  $current_rating = 0;
	}
	
	$rating_delta = $new_rating - $current_rating;
	
	if(!$rating_delta) {
	  $this->error(__('Rating not changed', 'userspace-rating'));
	}
	
	$result = usp_insert_vote([
		'user_id' => get_current_user_id(),
		'object_id' => $user_id,
		'object_author' => $user_id,
		'object_type' => 'custom',
		'rating_value' => $rating_delta,
		'rating_date' => current_time('mysql')
	]);
	
	if(!$result) {
	  $this->error(__('Rating not changed', 'userspace-rating'));
	}
	
	$this->success(__('Rating changed', 'userspace-rating'));

  }

  private function error($message = '') {

	wp_send_json( [ 'error' => $message ] );

  }

  private function success($message = '', $data = []) {

	wp_send_json( array_merge( [ 'success' => $message ], $data ) );

  }

}
