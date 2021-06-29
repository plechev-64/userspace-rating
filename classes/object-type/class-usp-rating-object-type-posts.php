<?php

class USP_Rating_Object_Type_Posts extends USP_Rating_Object_Type_Abstract {

  private $id;
  private $name;

  /**
   * 
   * @param string $id - unique id
   * @param string $name - name
   */
  public function __construct($id, $name) {

	$this->id = $id;
	$this->name = $name;

  }

  public function get_id() {
	return $this->id;

  }

  public function get_name() {
	return $this->name;

  }

  public function get_object_author($object_id) {

	return get_post_field( 'post_author', $object_id );

  }

  public function is_valid_object_id($object_id) {

	return (get_post( $object_id )) ? true : false;

  }

  public function get_history_template_custom_vars() {

	return [ '%POST%' => __( 'link to publication', 'userspace-rating' ) ];

  }

  public function replace_custom_vars($template, $vote) {

	return preg_replace_callback_array(
	[
		'/(%POST%)/m' => function ($match) use ($vote) {
		  $post_name = get_post_field( 'post_title', $vote->object_id );
		  $permalink = get_post_permalink( $vote->object_id );
		  return "<a href='{$permalink}'>{$post_name}</a>";
		}
	],
	$template
	);

  }

}
