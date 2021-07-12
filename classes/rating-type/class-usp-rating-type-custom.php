<?php

class USP_Rating_Type_Custom extends USP_Rating_Type_Abstract {

  private $id;
  private $name;

  public function __construct() {

	$this->id = 'custom';
	$this->name = __( 'Custom', 'userspace-rating' );

  }

  public function get_id() {
	return $this->id;

  }

  public function get_name() {
	return $this->name;

  }

  public function is_valid_rating_value($rating_value, $object_type) {

	return is_numeric( $rating_value );

  }

  /**
   * @param array $params
   * 
   * @return string | bool - html
   */
  public function get_vote_buttons_and_value($params) {

	return '';

  }

  public function get_html_from_value($rating_value, $object_type) {

	$html = '<div class="usp-rating-custom usps__inline">' . $rating_value . '</div>';

	return $html;

  }

  /**
   * @param $object_type - Current rating object type
   * 
   * @return array - Array of custom options for rating type likes
   */
  public function get_custom_options($object_type) {
	return [];

  }

  public function is_public() {
	return false;

  }

}
