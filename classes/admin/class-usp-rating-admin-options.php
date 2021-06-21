<?php

class USP_Rating_Admin_Options {

  public function __construct() {

	add_filter( 'usp_options', [ $this, 'init_options' ] );

  }

  public function init_options($options) {

	$USP_Rating = USP_Rating::get_instance();

	$object_types = $USP_Rating->get_object_types();

	$options->add_box( 'rating', array(
		'title' => __( 'Rating settings', 'userspace-rating' ),
		'icon' => 'fa-thumbs-up'
	) );

	foreach ( $object_types->get() as $USP_Object_Type ) {

	  $options->box( 'rating' )->add_group( 'rating-' . $USP_Object_Type->get_id(), array(
		  'title' => __( 'Rating', 'userspace-rating' ) . ' ' . $USP_Object_Type->get_name()
	  ) )->add_options( $this->object_type_options( $USP_Object_Type ) );
	}

	$options->box( 'rating' )->add_group( 'general', array(
		'title' => __( 'Extends options', 'userspace-rating' ),
		'extend' => true
	) )->add_options( $this->extend_options() );

	return $options;

  }

  /**
   * 
   * @param object $USP_Object_Type
   * 
   * @return array - options for object type
   */
  private function object_type_options($USP_Object_Type) {

	$sub_options = [];

	$sub_options[] = $this->rating_type_option( $USP_Object_Type );
	$sub_options[] = $this->rating_points_option( $USP_Object_Type );
	$sub_options[] = $this->rating_user_option( $USP_Object_Type );

	$options = array(
		array(
			'type' => 'select',
			'slug' => 'rating_' . $USP_Object_Type->get_id(),
			'values' => array( __( 'Disabled', 'userspace-rating' ), __( 'Enabled', 'userspace-rating' ) ),
			'childrens' => array(
				1 => $sub_options
			)
		)
	);

	return $options;

  }

  private function rating_type_option($USP_Object_Type) {

	$USP_Rating = USP_Rating::get_instance();

	$rating_types = $USP_Rating->get_rating_types();

	$values = [];

	$rating_type_child_options = [];

	foreach ( $rating_types->get() as $USP_Rating_Type ) {
	  $values[ $USP_Rating_Type->get_id() ] = $USP_Rating_Type->get_name();

	  $child_options = $USP_Rating_Type->get_custom_options( $USP_Object_Type );

	  if ( $child_options ) {
		$rating_type_child_options[ $USP_Rating_Type->get_id() ] = $child_options;
	  }
	}

	return array(
		'type' => 'select',
		'slug' => 'rating_type_' . $USP_Object_Type->get_id(),
		'title' => __( 'Type of rating for', 'userspace-rating' ) . ' ' . $USP_Object_Type->get_name(),
		'values' => $values,
		'childrens' => $rating_type_child_options
	);

  }

  private function rating_points_option($USP_Object_Type) {

	return array(
		'type' => 'text',
		'slug' => 'rating_points_' . $USP_Object_Type->get_id(),
		'title' => __( 'Points for ranking', 'userspace-rating' ) . ' ' . $USP_Object_Type->get_name(),
		'notice' => __( 'set how many points will be awarded for a positive or negative vote for the publication', 'userspace-rating' )
	);

  }

  private function rating_user_option($USP_Object_Type) {

	$notice = '';

	$template_vars = $USP_Object_Type->get_history_template_vars();

	foreach ( $template_vars as $var => $var_descr ) {
	  $notice .= "<p>{$var} - {$var_descr}</p>";
	}

	return array(
		'type' => 'select',
		'slug' => 'rating_user_' . $USP_Object_Type->get_id(),
		'title' => sprintf( __( 'The influence of rating %s on the overall rating', 'userspace-rating' ), $USP_Object_Type->get_name() ),
		'values' => array( __( 'No', 'userspace-rating' ), __( 'Yes', 'userspace-rating' ) ),
		'childrens' => array(
			1 => array(
				array(
					'type' => 'text',
					'slug' => 'rating_temp_' . $USP_Object_Type->get_id(),
					'title' => __( 'Template of history output in the overall ranking', 'userspace-rating' ),
					'default' => $USP_Object_Type->get_history_template_default(),
					'notice' => $notice
				)
			)
		)
	);

  }

  private function extend_options() {

	return array(
		array(
			'type' => 'select',
			'slug' => 'rating_results_can',
			'title' => __( 'View results', 'userspace-rating' ),
			'values' => array(
				0 => __( 'All users', 'userspace-rating' ),
				1 => __( 'Participants and higher', 'userspace-rating' ),
				2 => __( 'Authors and higher', 'userspace-rating' ),
				7 => __( 'Editors and higher', 'userspace-rating' ),
				10 => __( 'only Administrators', 'userspace-rating' )
			),
			'notice' => __( 'specify the user group which is allowed to view votes', 'userspace-rating' )
		),
		array(
			'type' => 'select',
			'slug' => 'rating_delete_voice',
			'title' => __( 'Delete your vote', 'userspace-rating' ),
			'values' => array( __( 'No', 'userspace-rating' ), __( 'Yes', 'userspace-rating' ) )
		),
		array(
			'type' => 'select',
			'slug' => 'rating_custom',
			'title' => __( 'Tab "Other"', 'userspace-rating' ),
			'values' => array(
				__( 'Disable', 'userspace-rating' ),
				__( 'Enable', 'userspace-rating' )
			),
			'notice' => __( 'If enabled, an additional "Other" tab will be created in the rating history, where all changes will be displayed via unregistered rating types', 'userspace-rating' )
		)
	);

  }

}
