<?php

class USP_Rating_Loader {

  public function __construct() {
	
  }

  public function run() {

	$this->init_rating_types();

	$this->init_object_types();

	if ( is_admin() ) {
	  $this->init_admin_options();
	}

	add_action( 'usp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

	usp_ajax_action( 'userspace_rating_ajax' );

	add_action( 'wp', [ $this, 'load_posts_rating_data' ] );
	add_filter( 'comments_array', [ $this, 'load_comments_rating_data' ] );

  }

  /**
   * Register scripts & styles
   */
  public function enqueue_scripts() {

	if ( is_user_logged_in() ) {
	  usp_enqueue_script( 'userspace-rating', USERSPACE_RATING_URL . 'assets/js/scripts.js' );
	}

	usp_enqueue_style( 'userspace-rating', USERSPACE_RATING_URL . 'assets/css/style.css' );

  }

  /**
   * load rating data for posts and posts authors after main query
   */
  public function load_posts_rating_data() {

	global $wp_query;

	if ( !$wp_query->posts ) {
	  return;
	}

	$post_types = [];
	$post_ids = [];
	$post_authors = [];

	foreach ( $wp_query->posts as $post ) {

	  $object_type = USP_Rating()->get_object_type( $post->post_type );

	  if ( !$object_type || !$object_type->get_option( 'rating' ) ) {
		continue;
	  }

	  $post_types[] = $post->post_type;
	  $post_ids[] = $post->ID;
	  $post_authors[] = $post->post_author;
	}

	if ( !$post_ids ) {
	  return;
	}

	$post_types = array_unique( $post_types );
	$post_authors = array_unique( $post_authors );

	/*
	 * Rating data for post_ids
	 */

	$totals_query = USP_Rating()->totals_query();

	$totals_data = $totals_query
	->select( [ 'object_id', 'rating_total', 'object_type' ] )
	->where( [
		'object_type__in' => $post_types,
		'object_id__in' => $post_ids,
		'number' => count( $post_ids )
	] )->get_results();

	if ( $totals_data ) {

	  /*
	   * Rating data to cache
	   */
	  foreach ( $totals_data as $object_rating_data ) {
		USP_Rating()->set_cache( $object_rating_data->object_type, $object_rating_data->object_id, [
			'rating_total' => $object_rating_data->rating_total
		] );
	  }
	}

	/*
	 * Rating data for post_authors
	 */

	$users_query = USP_Rating()->users_query();

	$users_data = $users_query
	->select( [ 'user_id', 'rating_total' ] )
	->where( [
		'user_id__in' => $post_authors,
		'number' => count( $post_authors )
	] )->get_results();

	if ( $users_data ) {

	  /*
	   * Rating data to cache
	   */
	  foreach ( $users_data as $user_rating_data ) {
		USP_Rating()->set_cache( 'user', $user_rating_data->user_id, [
			'rating_total' => $user_rating_data->rating_total
		] );
	  }
	}

  }

  /**
   * load rating data for comments and posts authors after main query
   */
  public function load_comments_rating_data($comments) {

	$object_type = USP_Rating()->get_object_type( 'comment' );

	if ( !$comments || !$object_type || !$object_type->get_option( 'rating' ) ) {
	  return $comments;
	}

	$comment_authors = [];
	$comment_ids = [];

	foreach ( $comments as $comment ) {
	  $comment_authors[] = $comment->user_id;
	  $comment_ids[] = $comment->comment_ID;
	}

	$comment_authors = array_unique( $comment_authors );

	if ( $comment_authors ) {

	  /*
	   * Rating data for comment_authors
	   */

	  $users_query = USP_Rating()->users_query();

	  $users_data = $users_query
	  ->select( [ 'user_id', 'rating_total' ] )
	  ->where( [
		  'user_id__in' => $comment_authors,
		  'number' => count( $comment_authors )
	  ] )->get_results();

	  if ( $users_data ) {
		/*
		 * Rating data to cache
		 */
		foreach ( $users_data as $user_rating_data ) {
		  USP_Rating()->set_cache( 'user', $user_rating_data->user_id, [
			  'rating_total' => $user_rating_data->rating_total
		  ] );
		}
	  }
	}

	/*
	 * Rating data for comment_ids
	 */

	$totals_query = USP_Rating()->totals_query();

	$comments_data = $totals_query
	->select( [ 'object_id', 'rating_total' ] )
	->where( [
		'object_type' => 'comment',
		'object_id__in' => $comment_ids,
		'number' => count( $comment_ids )
	] )->get_results();

	if ( $comments_data ) {

	  /*
	   * Rating data to cache
	   */
	  foreach ( $comments_data as $object_rating_data ) {
		USP_Rating()->set_cache( 'comment', $object_rating_data->object_id, [
			'rating_total' => $object_rating_data->rating_total
		] );
	  }
	}

	return $comments;

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
