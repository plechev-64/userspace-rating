<?php

class USP_Rating_Type_Stars extends USP_Rating_Type_Abstract {

  private $id;
  private $name;

  public function __construct() {

	$this->id = 'stars';
	$this->name = __( 'Stars', 'userspace-rating' );

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

	return 'html box of rating type Stars';

  }

  /**
   * @param $USP_Object_Type - rating object type
   * 
   * @return array - Array of custom options for rating type stars
   */
  public function get_custom_options($USP_Object_Type) {
	return [
		[
			'type' => 'runner',
			'slug' => 'rating_stars_count_' . $USP_Object_Type->get_id(),
			'title' => __( 'Number of stars', 'userspace-rating' ),
			'value_min' => 1,
			'value_max' => 20,
			'default' => 5
		],
		[
			'type' => 'select',
			'slug' => 'rating_stars_shema_' . $USP_Object_Type->get_id(),
			'title' => __( 'Rating markup', 'userspace-rating' ),
			'values' => array(
				__( 'Disable', 'userspace-rating' ),
				__( 'Enable', 'userspace-rating' )
			),
			'notice' => __( 'If enabled, the standard markup on single pages along with the rating is displayed as <a href="http://schema.org" target="_blank">http://schema.org</a>', 'userspace-rating' )
		]
	];

  }

}
