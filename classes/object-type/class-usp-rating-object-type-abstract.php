<?php

abstract class USP_Rating_Object_Type_Abstract {

  /**
   * @return string - unique id of object type
   */
  abstract public function get_id();

  /**
   * @return string - name of object type
   */
  abstract public function get_name();

  /**
   * @param int $object_id
   * 
   * @return int - user_id
   */
  abstract public function get_object_author($object_id);

  /**
   * @param int $object_id
   * 
   * @return string - url for object_id
   */
  abstract public function get_object_url($object_id);

  /**
   * @param int $object_id
   * 
   * @return string - name for object_id (post_title, comment...)
   */
  abstract public function get_object_name($object_id);

  /**
   * @param int $object_id
   * 
   * @return bool - valid or not object_id
   */
  abstract public function is_valid_object_id($object_id);

  /**
   * @return string - vote template for object type
   */
  public function get_vote_template() {

	$default = '%DATE% %USER% %VALUE%';

	return $this->get_option( 'rating_vote_template', $default );

  }

  /**
   * @return array - all aviable vars in template for object type (['%var%' => 'description'])
   */
  public function get_vote_template_vars() {

	$vars = [
		'%DATE%' => __( 'Voting date', 'userspace-rating' ),
		'%USER%' => __( 'User who voted', 'userspace-rating' ),
		'%VALUE%' => __( 'Vote value', 'userspace-rating' ),
		'%OBJECT%' => __( 'Voting object', 'userspace-rating' )
	];

	return $vars;

  }

  /**
   * Replace vars in vote template
   * 
   * @param object $vote
   * 
   * @return string
   */
  public function convert_vote_to_template($vote) {

	$template = $this->get_vote_template();

	return $this->convert_vote_template_vars( $vote, $template );

  }

  /**
   * Replace vars in template
   * 
   * @param object $vote
   * @param string $template
   * 
   * @return string
   */
  public function convert_vote_template_vars($vote, $template) {

	return preg_replace_callback_array(
	[
		'/(%DATE%)/m' => function ($match) use ($vote) {

		  return '<time>' . date( "Y-m-d", strtotime( $vote->rating_date ) ) . '</time>';
		},
		'/(%USER%)/m' => function ($match) use ($vote) {

		  $userdata = get_userdata( $vote->user_id );
		  $user_url = get_author_posts_url( $vote->user_id );

		  return "<a href='{$user_url}'>{$userdata->display_name}</a>";
		},
		'/(%VALUE%)/m' => function ($match) use ($vote) {

		  $rating_type = USP_Rating()->get_rating_type( $this->get_option( 'rating_type' ) );

		  if ( !$rating_type ) {
			return $vote->rating_value;
		  }

		  return $rating_type->get_html_from_value( $vote->rating_value, $this );
		},
		'/(%OBJECT%)/m' => function ($match) use ($vote) {

		  $object_name = $this->get_object_name( $vote->object_id );
		  $object_url = $this->get_object_url( $vote->object_id );

		  return "<a href='{$object_url}'>{$object_name}</a>";
		}
	],
	$template
	);

  }

  /**
   * @param string $option_name
   * @param mixed $default - default value
   * 
   * @return mixed - value of option for current object type
   */
  public function get_option($option_name = '', $default = '') {

	$option_value = usp_get_option( $option_name . '_' . $this->get_id(), null );

	return is_null( $option_value ) ? $default : $option_value;

  }

  /**
   * Enable or disable rating for object_id
   * 
   * @param int $object_id
   * 
   * @return bool
   */
  public function is_object_rating_enable($object_id) {

	return true;

  }

  /**
   * Must be return false if this object type not display and uses only for manipulate rating
   * 
   * 
   * @return bool
   */
  public function is_hidden() {
	return false;

  }

}
