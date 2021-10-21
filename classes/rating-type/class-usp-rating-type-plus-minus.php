<?php

class USP_Rating_Type_Plus_Minus extends USP_Rating_Type_Abstract {

	private $id;
	private $name;

	public function __construct() {

		$this->id   = 'plus-minus';
		$this->name = __( 'Plus / Minus', 'userspace-rating' );

	}

	public function get_id() {
		return $this->id;

	}

	public function get_name() {
		return $this->name;

	}

	public function is_valid_rating_value( $rating_value, $object_type ) {

		$option_rating_points = $object_type->get_option( 'rating_value' );

		return abs( $rating_value ) == $option_rating_points;

	}

	/**
	 * @param array $params
	 *
	 * @return string | bool - html
	 */
	public function get_vote_buttons_and_value( $params ) {

		$object_type = $params['object_type'];

		$counting_type = $object_type->get_option( 'rating_plus-minus_overall' );
		$rating_points = $object_type->get_option( 'rating_value' );

		$html = usp_get_include_template( 'usp-rating-' . $this->get_id() . '.php', USP_RATING_BASE, [
			'object_type'         => $object_type,
			'object_id'           => $params['object_id'],
			'object_author'       => $params['object_author'],
			'user_id'             => $params['user_id'],
			'user_vote'           => $params['user_vote'],
			'rating_total'        => $params['rating_total'],
			'rating_average'      => $params['rating_average'],
			'votes_count'         => $params['votes_count'],
			'user_can_vote'       => $params['user_can_vote'],
			'user_can_view_votes' => $params['user_can_view_votes'],
			'rating_points'       => $rating_points,
			'counting_type'       => $counting_type
		] );

		return $html;

	}

	public function get_html_from_value( $rating_value, $object_type ) {

		if ( $rating_value > 0 ) {
			$html = '<div class="usp-rating-plus usp-rating-plus_size_small usps__inline"><i class="uspi fa-plus"></i></div>';
		} else {
			$html = '<div class="usp-rating-minus usp-rating-minus_size_small usps__inline"><i class="uspi fa-minus"></i></div>';
		}

		return $html;

	}

	/**
	 * @param $object_type - rating object type
	 *
	 * @return array - Array of custom options for rating type plus-minus
	 */
	public function get_custom_options( $object_type ) {
		return [
			[
				'type'   => 'select',
				'slug'   => 'rating_plus-minus_overall_' . $object_type->get_id(),
				'title'  => __( 'Overall rating', 'userspace-rating' ) . ' ' . $object_type->get_name(),
				'values' => [
					__( 'Sum of ratings', 'userspace-rating' ),
					__( 'Sum of positive and negative votes', 'userspace-rating' )
				]
			]
		];

	}

}
