<?php

class USP_Rating_Type_Likes extends USP_Rating_Type_Abstract {

  private $id;
  private $name;

  public function __construct() {

	$this->id = 'likes';
	$this->name = __( 'Likes', 'userspace-rating' );

  }

  public function get_id() {
	return $this->id;

  }

  public function get_name() {
	return $this->name;

  }

  /**
   * @param int $object_id - post_id, comment_id etc...
   * @param int $object_author - user_id
   * @param object $object_type - rating object type
   * 
   * @return string | bool - html code of rating box or false if rating for object disabled
   */
  public function get_rating_box($object_id, $object_author, $object_type) {

	$counting_type = $object_type->get_option( 'rating_' . $this->get_id() . '_overall_' . $object_type->get_id(), 0 );
	
	return 'html box of rating type Likes';

  }

  /**
   * @param $USP_Object_Type - Current rating object type
   * 
   * @return array - Array of custom options for rating type likes
   */
  public function get_custom_options($USP_Object_Type) {
	return [
		[
			'type' => 'select',
			'slug' => 'rating_likes_overall_' . $USP_Object_Type->get_id(),
			'title' => __( 'Overall rating', 'userspace-rating' ) . ' ' . $USP_Object_Type->get_name(),
			'values' => array( __( 'Sum of votes', 'userspace-rating' ), __( 'Number of votes', 'userspace-rating' ) )
		]
	];

  }

}
