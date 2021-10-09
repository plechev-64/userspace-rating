<?php

class USP_Rating_Box {

	/**
	 * Rating type instance
	 *
	 * @var object $rating_type
	 */
	private $rating_type;

	/**
	 * Object type instance
	 *
	 * @var object $object_type
	 */
	private $object_type;

	/**
	 * Object author ID
	 *
	 * @var int $object_author
	 */
	private $object_author;

	/**
	 * Object ID
	 *
	 * @var int $object_id
	 */
	private $object_id;

	/**
	 * Object total rating
	 *
	 * @var float $rating_total
	 */
	private $rating_total;

	/**
	 * Object rating average
	 *
	 * @var float $rating_average
	 */
	private $rating_average;

	/**
	 * Object votes count
	 *
	 * @var int $votes_count
	 */
	private $votes_count;

	/**
	 * Current user
	 *
	 * @var int $user_id ;
	 */
	private $user_id;

	/**
	 * Current user vote value on object
	 *
	 * @var float $user_vote ;
	 */
	private $user_vote;

	/**
	 * Can current user vote this object
	 *
	 * @var boolean $user_can_vote
	 */
	private $user_can_vote;

	/**
	 * Can current user view votes list on this object
	 *
	 * @var boolean $user_can_view_votes
	 */
	private $user_can_view_votes;

	/**
	 * @param array $args
	 */
	public function __construct( $args ) {

		$this->set_params( $args );

	}

	/**
	 * Get rating box HTML
	 *
	 * @return string
	 */
	public function get_box() {

		if ( ! $this->init() ) {
			return '';
		}

		$html = usp_get_include_template( 'usp-rating-box.php', USP_RATING_BASE, [
			'params' => $this->get_params()
		] );

		return $html;

	}

	/**
	 * Init rating box params
	 *
	 * @return bool
	 */
	public function init() {


		if ( ! $this->object_type instanceof USP_Rating_Object_Type_Abstract ) {
			return false;
		}


		if ( ! $this->rating_type instanceof USP_Rating_Type_Abstract ) {

			$rating_type = USP_Rating()->get_rating_type( $this->object_type->get_option( 'rating_type' ) );

			if ( ! $rating_type ) {
				return false;
			}

			$this->rating_type = $rating_type;
		}


		if ( ! isset( $this->object_id ) ) {
			return false;
		}


		if ( ! isset( $this->object_author ) ) {
			$this->object_author = $this->object_type->get_object_author( $this->object_id );
		}


		if ( ! isset( $this->user_id ) ) {
			$this->user_id = get_current_user_id();
		}


		if ( ! isset( $this->rating_total ) ) {
			$this->rating_total = usp_get_object_rating( $this->object_id, $this->object_type->get_id() );
		}


		if ( ! isset( $this->votes_count ) ) {
			$this->votes_count = usp_get_object_votes_count( $this->object_id, $this->object_type->get_id() );
		}


		if ( ! isset( $this->rating_average ) ) {

			if ( ! $this->rating_total || ! $this->votes_count ) {
				$this->rating_average = 0;
			} else {
				$this->rating_average = round( $this->rating_total / $this->votes_count, USP_RATING_PRECISION );
			}
		}


		if ( ! isset( $this->user_vote ) ) {
			if ( $this->user_id ) {
				$this->user_vote = usp_get_user_vote_value( $this->user_id, $this->object_id, $this->object_type->get_id() );
			} else {
				$this->user_vote = 0;
			}
		}

		if ( ! isset( $this->user_can_view_votes ) ) {
			$this->user_can_view_votes = true;
		}

		$this->init_user_can_vote();

		$this->user_can_view_votes = apply_filters( 'usp_rating_user_can_view_votes', $this->user_can_view_votes, $this->user_id, $this->get_params() );

		return true;

	}

	public function get_params() {

		return get_object_vars( $this );

	}

	public function get_param( $key ) {

		return $this->$key ?? null;

	}

	private function init_user_can_vote() {

		if ( ! $this->user_id || $this->object_author == $this->user_id ) {
			$this->user_can_vote = false;

			return;
		}

		if ( ! isset( $this->user_can_vote ) ) {

			if ( $this->user_vote ) {

				$allow_delete_vote = usp_get_option( 'rating_delete_vote', 0 );

				$this->user_can_vote = (bool) $allow_delete_vote;
			} else {

				$this->user_can_vote = true;
			}
		}

		$this->user_can_vote = apply_filters( 'usp_rating_user_can_vote', $this->user_can_vote, $this->user_id, $this->get_params() );

	}

	/**
	 * Set rating box params
	 *
	 * @param array $params
	 *
	 * @return void
	 */
	private function set_params( $params ) {

		foreach ( get_class_vars( __CLASS__ ) as $name => $value ) {

			if ( isset( $params[ $name ] ) ) {
				$this->$name = $params[ $name ];
			}
		}

	}

}
