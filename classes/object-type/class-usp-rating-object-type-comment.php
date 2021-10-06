<?php

class USP_Rating_Object_Type_Comment extends USP_Rating_Object_Type_Abstract {

	private $id;
	private $name;

	public function __construct() {

		$this->id   = 'comment';
		$this->name = __( 'Comments', 'userspace-rating' );

	}

	public function get_id() {
		return $this->id;

	}

	public function get_name() {
		return $this->name;

	}

	public function get_object_author( $object_id ) {

		return get_comment( $object_id )->user_id;

	}

	public function get_object_name( $object_id ) {

		return __( 'comment', 'userspace-rating' );

	}

	public function get_object_url( $object_id ) {

		return get_comment_link( $object_id );

	}

	public function is_valid_object_id( $object_id ) {

		return ( get_comment( $object_id ) ) ? true : false;

	}

}
