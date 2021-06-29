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
   * @return bool - valid or not object_id
   */
  abstract public function is_valid_object_id($object_id);

  /**
   * @return string - default history template for object type
   */
  public function get_history_template_default() {

	return '%DATE% %USER% ' . __( 'has voted', 'userspace-rating' ) . ': %VALUE%';

  }

  /**
   * @return array - all aviable vars in template for object type (['%var%' => 'description'])
   */
  public function get_history_template_vars() {

	$default_vars = [
		'%DATE%' => __( 'Voting date', 'userspace-rating' ),
		'%USER%' => __( 'User who voted', 'userspace-rating' ),
		'%VALUE%' => __( 'Vote value', 'userspace-rating' )
	];

	$custom_vars = $this->get_history_template_custom_vars();

	return array_merge( $default_vars, $custom_vars );

  }

  /**
   * @return array - custom vars in template for object type (['%var%' => 'description'])
   */
  public function get_history_template_custom_vars() {

	return [];

  }

  /**
   * Replace custom vars in history template
   * 
   * @param string $template
   * @param object $vote
   * 
   * @return string
   */
  public function convert_history_template($template, $vote) {

	$result = $this->replace_default_vars( $template, $vote );

	return $this->replace_custom_vars( $result, $vote );

  }

  /**
   * Replace default vars in template
   * 
   * @param string $template
   * @param object $vote
   * 
   * @return string
   */
  public function replace_default_vars($template, $vote) {

	return preg_replace_callback_array(
	[
		'/(%DATE%)/m' => function ($match) use ($vote) {
		  return date( "Y-m-d", strtotime( $vote->rating_date ) );
		},
		'/(%USER%)/m' => function ($match) use ($vote) {
		  $userdata = get_userdata( $vote->user_id );
		  $user_url = get_author_posts_url( $vote->user_id );
		  return "<a href='{$user_url}'>{$userdata->display_name}</a>";
		},
		'/(%VALUE%)/m' => function ($match) use ($vote) {
		  return $vote->rating_value;
		}
	],
	$template
	);

  }

  /**
   * Replace custom vars in template
   * 
   * @param string $template
   * @param object $vote
   * 
   * @return string
   */
  public function replace_custom_vars($template, $vote) {

	return $template;

  }

  /**
   * @param string $option_name
   * @param string|int|bool $default - default value
   * 
   * @return string|int|array|bool - value of option for current object type
   */
  public function get_option($option_name = '', $default = '') {

	$option_value = usp_get_option( $option_name, null );

	if ( is_null( $option_value ) ) {

	  $option_value = usp_get_option( $option_name . '_' . $this->get_id(), null );
	}

	return is_null( $option_value ) ? $default : $option_value;

  }

  /**
   * Enable or disable rating for object_id
   * 
   * Run if rating enable in options.
   * 
   * @param int $object_id
   * 
   * @return bool
   */
  public function is_rating_enable($object_id) {

	return true;

  }

}
