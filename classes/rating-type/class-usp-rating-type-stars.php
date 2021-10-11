<?php

class USP_Rating_Type_Stars extends USP_Rating_Type_Abstract {

	private $id;
	private $name;

	public function __construct() {

		$this->id   = 'stars';
		$this->name = __( 'Stars', 'userspace-rating' );

	}

	public function get_id() {
		return $this->id;

	}

	public function get_name() {
		return $this->name;

	}

	public function is_valid_rating_value( $rating_value, $object_type ) {

		$stars_values = $this->get_stars_values( $object_type );

		return in_array( $rating_value, $stars_values );

	}

	/**
	 * @param array $params
	 *
	 * @return string | bool - html
	 */
	public function get_vote_buttons_and_value( $params ) {

		$object_type = $params['object_type'];

		$stars_values  = $this->get_stars_values( $object_type );
		$stars_percent = $this->get_stars_percent( $stars_values, $params['rating_average'] );

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
			'stars_values'        => $stars_values,
			'stars_percent'       => $stars_percent
		] );

		return $html;

	}

	public function get_html_from_value( $rating_value, $object_type ) {

		$stars_values = $this->get_stars_values( $object_type );

		$stars_percent = $this->get_stars_percent( $stars_values, $rating_value );

		$stars_html = '<div class="usp-rating-stars usps__inline usp-rating-stars_size_small">';

		foreach ( $stars_values as $star_num => $rating_value ) {

			if ( $stars_percent[ $star_num ] == 100 ) {

				$stars_html .= '<i class="uspi fa-star-fill"></i>';
			} else if ( $stars_percent[ $star_num ] > 0 ) {

				$stars_html .= '<i class="uspi fa-star-half-o"></i>';
			} else {

				$stars_html .= '<i class="uspi fa-star"></i>';
			}
		}

		$stars_html .= '</div>';

		return $stars_html;

	}

	private function get_stars_percent( $stars_values, $total_rating ) {

		$stars = array_fill( 1, count( $stars_values ), 0 );

		foreach ( $stars_values as $star_num => $star_value ) {

			if ( $total_rating >= $star_value ) {
				$stars[ $star_num ] = 100;
				continue;
			}

			$val = $star_num - 1;

			$star_percent = ( $val > 0 ) ? round( ( ( $total_rating - $stars_values[ $val ] ) / $stars_values[1] ) * 100 ) : '0';

			$stars[ $star_num ] = $star_percent;

			break;
		}

		return $stars;

	}

	private function get_stars_values( $object_type ) {

		$stars_count = $object_type->get_option( 'rating_stars_count' );
		$max_rating  = $object_type->get_option( 'rating_value' );

		$single_star_rating = round( $max_rating / $stars_count, USP_RATING_PRECISION );
		$last_star_rating   = round( $max_rating, USP_RATING_PRECISION );

		$stars = [];

		foreach ( range( 1, $stars_count ) as $star_num ) {

			if ( $stars_count == $star_num ) {
				$value = $last_star_rating;
			} else {

				$value = round( $single_star_rating * $star_num, USP_RATING_PRECISION );
			}

			$stars[ $star_num ] = $value;
		}

		return $stars;

	}

	/**
	 * @param object $object_type - rating object type
	 *
	 * @return array - Array of custom options for rating type stars
	 */
	public function get_custom_options( $object_type ) {

		return [
			[
				'type'      => 'runner',
				'slug'      => 'rating_stars_count_' . $object_type->get_id(),
				'title'     => __( 'Number of stars', 'userspace-rating' ),
				'value_min' => 1,
				'value_max' => 20,
				'default'   => 5
			],
			[
				'type'   => 'select',
				'slug'   => 'rating_stars_schema_' . $object_type->get_id(),
				'title'  => __( 'Rating markup', 'userspace-rating' ),
				'values' => [
					__( 'Disable', 'userspace-rating' ),
					__( 'Enable', 'userspace-rating' )
				],
				'notice' => __( 'If enabled, the standard markup on single pages along with the rating is displayed as <a href="http://schema.org" target="_blank">http://schema.org</a>', 'userspace-rating' )
			]
		];

	}

}
