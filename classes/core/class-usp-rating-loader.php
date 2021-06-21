<?php

class USP_Rating_Loader {

  public function __construct() {
	
  }

  public function run() {

	$this->init_rating_types();

	$this->init_object_types();

	$this->init_admin_options();

  }

  /**
   * Initialise rating types
   */
  private function init_rating_types() {

	add_action( 'userspace_rating_types', function($USP_Rating_Types) {

	  $USP_Rating_Types->add( new USP_Rating_Type_Likes() );
	  $USP_Rating_Types->add( new USP_Rating_Type_Stars() );
	  $USP_Rating_Types->add( new USP_Rating_Type_Plus_Minus() );
	} );

  }

  /**
   * Init all default rating object types
   */
  private function init_object_types() {
	
	$this->init_object_types_posts();
	$this->init_object_types_comment();

  }

  /**
   * Initialise admin option
   */
  private function init_admin_options() {

	$admin_options = new USP_Rating_Admin_Options();

  }

  /**
   * Init rating object type for comments
   */
  private function init_object_types_comment() {

	$object_type = new USP_Rating_Object_Type_Comment();

	add_action( 'userspace_rating_object_types', function($USP_Rating_Object_Types) use ($object_type) {

	  $USP_Rating_Object_Types->add( $object_type );
	} );

  }

  /**
   * Init rating object types for all post types
   */
  private function init_object_types_posts() {

	$custom_post_types = get_post_types( array(
		'public' => true,
		'_builtin' => false
	), 'objects' );

	$default_post_types = get_post_types( array(
		'public' => true,
		'_builtin' => true
	), 'objects' );

	$post_types = array_merge( $custom_post_types, $default_post_types );

	foreach ( $post_types as $post_type ) {

	  $object_type = new USP_Rating_Object_Type_Posts( $post_type->name, $post_type->label );

	  add_action( 'userspace_rating_object_types', function($USP_Rating_Object_Types) use ($object_type) {

		$USP_Rating_Object_Types->add( $object_type );
	  } );
	}

  }

}
