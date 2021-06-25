<?php

class USP_Rating_Admin_Options {

  public function __construct() {

	add_filter( 'usp_options', [ $this, 'init_options' ] );

  }

  public function init_options($options) {

	$object_types = USP_Rating()->get_object_types();

	$options->add_box( 'rating', array(
		'title' => __( 'Rating settings', 'userspace-rating' ),
		'icon' => 'fa-thumbs-up'
	) );

	foreach ( $object_types->get_all() as $object_type ) {

	  $options->box( 'rating' )->add_group( 'rating-' . $object_type->get_id(), array(
		  'title' => __( 'Rating', 'userspace-rating' ) . ' ' . $object_type->get_name()
	  ) )->add_options( $this->object_type_options( $object_type ) );
	}

	$options->box( 'rating' )->add_group( 'general', array(
		'title' => __( 'Extends options', 'userspace-rating' ),
		'extend' => true
	) )->add_options( $this->extend_options() );

	return $options;

  }

  /**
   * 
   * @param object $object_type
   * 
   * @return array - options for object type
   */
  private function object_type_options($object_type) {

	$sub_options = [];

	$sub_options[] = $this->rating_type_option( $object_type );
	$sub_options[] = $this->rating_user_option( $object_type );

	$options = array(
		array(
			'type' => 'select',
			'slug' => 'rating_' . $object_type->get_id(),
			'values' => array( __( 'Disabled', 'userspace-rating' ), __( 'Enabled', 'userspace-rating' ) ),
			'childrens' => array(
				1 => $sub_options
			)
		)
	);

	return $options;

  }

  private function rating_type_option($object_type) {

	$rating_types = USP_Rating()->get_rating_types();

	$values = [];

	$rating_type_child_options = [];

	foreach ( $rating_types->get_all() as $rating_type ) {

	  $values[ $rating_type->get_id() ] = $rating_type->get_name();

	  $child_options = $rating_type->get_custom_options( $object_type );

	  if ( $child_options ) {
		$rating_type_child_options[ $rating_type->get_id() ] = $child_options;
	  }
	}

	return array(
		'type' => 'select',
		'slug' => 'rating_type_' . $object_type->get_id(),
		'title' => __( 'Type of rating for', 'userspace-rating' ) . ' ' . $object_type->get_name(),
		'values' => $values,
		'childrens' => $rating_type_child_options
	);

  }

  private function rating_user_option($object_type) {

	$notice = '';

	$template_vars = $object_type->get_history_template_vars();

	foreach ( $template_vars as $var => $var_descr ) {
	  $notice .= "<p>{$var} - {$var_descr}</p>";
	}

	return array(
		'type' => 'select',
		'slug' => 'rating_user_' . $object_type->get_id(),
		'title' => sprintf( __( 'The influence of rating %s on the overall rating', 'userspace-rating' ), $object_type->get_name() ),
		'values' => array( __( 'No', 'userspace-rating' ), __( 'Yes', 'userspace-rating' ) ),
		'childrens' => array(
			1 => array(
				array(
					'type' => 'text',
					'slug' => 'rating_history_template_' . $object_type->get_id(),
					'title' => __( 'Template of votes list output', 'userspace-rating' ),
					'default' => $object_type->get_history_template_default(),
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
			'slug' => 'rating_delete_vote',
			'title' => __( 'Delete your vote', 'userspace-rating' ),
			'values' => array( __( 'No', 'userspace-rating' ), __( 'Yes', 'userspace-rating' ) )
		),
		array(
			'type' => 'select',
			'slug' => 'rating_tab_other',
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
