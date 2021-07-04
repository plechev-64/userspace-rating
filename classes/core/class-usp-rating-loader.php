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

	if ( !$wp_query->posts || !USERSPACE_RATING_PRELOAD_DATA ) {
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

	$post_id_rating = [];
	$post_author_rating = [];
	$post_id_votes_count = [];
	$post_id_current_user_vote = [];

	/*
	 * Rating data for post_ids
	 */

	$totals_data = USP_Rating()->totals_query()
	->select( [ 'object_id', 'rating_total', 'object_type' ] )
	->where( [
		'object_type__in' => $post_types,
		'object_id__in' => $post_ids,
		'number' => count( $post_ids )
	] )->get_results();

	if ( $totals_data ) {

	  /*
	   * Post rating data to cache
	   */
	  foreach ( $totals_data as $object_rating_data ) {

		$post_id_rating[ $object_rating_data->object_id ] = $object_rating_data->rating_total;
	  }
	}

	/*
	 * Rating data for post_authors
	 */

	$users_data = USP_Rating()->users_query()
	->select( [ 'user_id', 'rating_total' ] )
	->where( [
		'user_id__in' => $post_authors,
		'number' => count( $post_authors )
	] )->get_results();

	if ( $users_data ) {

	  foreach ( $users_data as $user_rating_data ) {
		$post_author_rating[ $user_rating_data->user_id ] = $user_rating_data->rating_total;
	  }
	}

	/*
	 * Votes count for post_ids
	 */

	$votes_data = USP_Rating()->votes_query()
	->select_string( "COUNT(ID) as votes_count, object_id, object_type" )
	->where( [
		'object_type__in' => $post_types,
		'object_id__in' => $post_ids
	] )
	->groupby( "object_id" )
	->get_results();

	if ( $votes_data ) {

	  /*
	   * Votes data to cache
	   */
	  foreach ( $votes_data as $object_vote_data ) {

		$post_id_votes_count[ $object_vote_data->object_id ] = $object_vote_data->votes_count;
	  }
	}

	/*
	 * Current user votes for post_ids
	 */
	if ( get_current_user_id() ) {
	  $votes_data = USP_Rating()->votes_query()
	  ->select( [ 'rating_value', 'object_id', 'object_type' ] )
	  ->where( [
		  'object_type__in' => $post_types,
		  'object_id__in' => $post_ids,
		  'user_id' => get_current_user_id()
	  ] )
	  ->get_results();

	  if ( $votes_data ) {

		/*
		 * Votes data to cache
		 */
		foreach ( $votes_data as $object_vote_data ) {

		  $post_id_current_user_vote[ $object_vote_data->object_id ] = $object_vote_data->rating_value;
		}
	  }
	}

	/*
	 * Rating data to post object
	 */
	foreach ( $wp_query->posts as $post ) {

	  $post->rating = isset( $post_id_rating[ $post->ID ] ) ? $post_id_rating[ $post->ID ] : 0;
	  $post->rating_author = isset( $post_author_rating[ $post->post_author ] ) ? $post_author_rating[ $post->post_author ] : 0;
	  $post->votes_count = isset( $post_id_votes_count[ $post->ID ] ) ? $post_id_votes_count[ $post->ID ] : 0;
	  $post->user_vote = isset( $post_id_current_user_vote[ $post->ID ] ) ? $post_id_current_user_vote[ $post->ID ] : 0;

	  USP_Rating()->set_preloaded_data( $post->ID, $post->post_type, [
		  'rating' => $post->rating,
		  'rating_author' => $post->rating_author,
		  'votes_count' => $post->votes_count,
		  'user_vote' => $post->user_vote
	  ] );
	}

  }

  /**
   * load rating data for comments and posts authors after main query
   */
  public function load_comments_rating_data($comments) {

	$object_type = USP_Rating()->get_object_type( 'comment' );

	if ( !USERSPACE_RATING_PRELOAD_DATA ) {
	  return $comments;
	}

	if ( !$comments || !$object_type || !$object_type->get_option( 'rating' ) ) {
	  return $comments;
	}

	$comment_authors = [];
	$comment_ids = [];

	$comment_author_rating = [];
	$comment_id_rating = [];
	$comment_id_votes_count = [];
	$comment_id_current_user_vote = [];

	foreach ( $comments as $comment ) {
	  $comment_authors[] = $comment->user_id;
	  $comment_ids[] = $comment->comment_ID;
	}

	$comment_authors = array_unique( $comment_authors );

	if ( $comment_authors ) {

	  /*
	   * Rating data for comment_authors
	   */

	  $users_data = USP_Rating()->users_query()
	  ->select( [ 'user_id', 'rating_total' ] )
	  ->where( [
		  'user_id__in' => $comment_authors,
		  'number' => count( $comment_authors )
	  ] )->get_results();

	  if ( $users_data ) {

		foreach ( $users_data as $user_rating_data ) {

		  $comment_author_rating[ $user_rating_data->user_id ] = $user_rating_data->rating_total;
		}
	  }
	}

	/*
	 * Rating data for comment_ids
	 */

	$comments_data = USP_Rating()->totals_query()
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

		$comment_id_rating[ $object_rating_data->object_id ] = $object_rating_data->rating_total;
	  }
	}

	/*
	 * Votes count for comment_ids
	 */

	$votes_data = USP_Rating()->votes_query()
	->select_string( "COUNT(ID) as votes_count, object_id" )
	->where( [
		'object_type' => 'comment',
		'object_id__in' => $comment_ids
	] )
	->groupby( "object_id" )
	->get_results();

	if ( $votes_data ) {

	  /*
	   * Votes data to cache
	   */
	  foreach ( $votes_data as $object_vote_data ) {

		$comment_id_votes_count[ $object_vote_data->object_id ] = $object_vote_data->votes_count;
	  }
	}

	/*
	 * Current user votes for comment_ids
	 */
	if ( get_current_user_id() ) {
	  $votes_data = USP_Rating()->votes_query()
	  ->select( [ 'rating_value', 'object_id' ] )
	  ->where( [
		  'object_type' => 'comment',
		  'object_id__in' => $comment_ids,
		  'user_id' => get_current_user_id()
	  ] )
	  ->get_results();

	  if ( $votes_data ) {

		/*
		 * Votes data to cache
		 */
		foreach ( $votes_data as $object_vote_data ) {

		  $comment_id_current_user_vote[ $object_vote_data->object_id ] = $object_vote_data->rating_value;
		}
	  }
	}

	foreach ( $comments as $comment ) {
	  $comment->rating = isset( $comment_id_rating[ $comment->comment_ID ] ) ? $comment_id_rating[ $comment->comment_ID ] : 0;
	  $comment->rating_author = isset( $comment_author_rating[ $comment->user_id ] ) ? $comment_author_rating[ $comment->user_id ] : 0;
	  $comment->votes_count = isset( $comment_id_votes_count[ $comment->comment_ID ] ) ? $comment_id_votes_count[ $comment->comment_ID ] : 0;
	  $comment->user_vote = isset( $comment_id_current_user_vote[ $comment->comment_ID ] ) ? $comment_id_current_user_vote[ $comment->comment_ID ] : 0;

	  USP_Rating()->set_preloaded_data( $comment->comment_ID, 'comment', [
		  'rating' => $comment->rating,
		  'rating_author' => $comment->rating_author,
		  'votes_count' => $comment->votes_count,
		  'user_vote' => $comment->user_vote
	  ] );
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
