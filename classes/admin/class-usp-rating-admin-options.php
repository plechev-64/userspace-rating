<?php

class USP_Rating_Admin_Options {

	public function __construct() {

		add_filter( 'usp_options', [ $this, 'init_options' ] );

	}

	public function init_options( $options_manager ) {

		$object_types = USP_Rating()->get_object_types();

		$options_manager->add_box( 'rating', [
			'title' => __( 'Rating settings', 'userspace-rating' ),
			'icon'  => 'fa-thumbs-up'
		] );

		foreach ( $object_types->get_all() as $object_type ) {

			if ( $object_type->is_public() ) {
				$options = $this->build_object_type_options( $object_type );
			} else {
				$options = [];
			}

			$filtered_options = $object_type->filter_options( $options );

			if ( ! $filtered_options ) {
				continue;
			}

			$options_manager->box( 'rating' )->add_group( 'rating-' . $object_type->get_id(), [
				'title' => __( 'Rating', 'userspace-rating' ) . ' ' . $object_type->get_name()
			] )->add_options( $filtered_options );
		}

		$options_manager->box( 'rating' )->add_group( 'general', [
			'title'  => __( 'Extends options', 'userspace-rating' ),
			'extend' => true
		] )->add_options( $this->extend_options() );

		return $options_manager;

	}

	/**
	 *
	 * @param object $object_type
	 *
	 * @return array - options for object type
	 */
	private function build_object_type_options( $object_type ) {

		$sub_options = [];

		$sub_options[] = $this->rating_value_option( $object_type );
		$sub_options[] = $this->rating_type_option( $object_type );
		$sub_options[] = $this->rating_influence_option( $object_type );

		return [
			[
				'type'      => 'select',
				'slug'      => 'rating_' . $object_type->get_id(),
				'values'    => [
					0 => __( 'Disabled', 'userspace-rating' ),
					1 => __( 'Enabled', 'userspace-rating' )
				],
				'default'   => 0,
				'childrens' => [
					1 => $sub_options
				]
			]
		];

	}

	private function rating_type_option( $object_type ) {

		$rating_types = USP_Rating()->get_rating_types();

		$values = [];

		$rating_type_child_options = [];

		foreach ( $rating_types->get_all() as $rating_type ) {

			if ( ! $rating_type->is_public() ) {
				continue;
			}

			$values[ $rating_type->get_id() ] = $rating_type->get_name();

			$child_options = $rating_type->get_custom_options( $object_type );

			if ( $child_options ) {
				$rating_type_child_options[ $rating_type->get_id() ] = $child_options;
			}
		}

		return [
			'type'        => 'select',
			'slug'        => 'rating_type_' . $object_type->get_id(),
			'empty-first' => __( 'Not selected', 'userspace-rating' ),
			'title'       => __( 'Type of rating for', 'userspace-rating' ) . ' ' . $object_type->get_name(),
			'values'      => $values,
			'childrens'   => $rating_type_child_options
		];

	}

	private function rating_value_option( $object_type ) {

		return [
			'type'    => 'text',
			'slug'    => 'rating_value_' . $object_type->get_id(),
			'title'   => __( 'Rating value', 'userspace-rating' ),
			'default' => 1
		];

	}

	private function rating_influence_option( $object_type ) {

		$notice = '';

		$template_vars = $object_type->get_vote_template_vars();

		foreach ( $template_vars as $var => $var_descr ) {
			$notice .= "<p>" . esc_html( $var ) . " - " . esc_html( $var_descr ) . "</p>";
		}

		return [
			'type'      => 'select',
			'slug'      => 'rating_influence_' . $object_type->get_id(),
			'title'     => sprintf( __( 'The influence of rating %s on the overall rating of users', 'userspace-rating' ), $object_type->get_name() ),
			'values'    => [
				0 => __( 'No', 'userspace-rating' ),
				1 => __( 'Yes', 'userspace-rating' )
			],
			'childrens' => [
				1 => [
					[
						'type'    => 'text',
						'slug'    => 'rating_vote_template_' . $object_type->get_id(),
						'title'   => __( 'Template of votes list output', 'userspace-rating' ),
						'default' => $object_type->get_vote_template(),
						'notice'  => $notice
					]
				]
			]
		];

	}

	private function extend_options() {

		return [
			[
				'type'   => 'select',
				'slug'   => 'rating_delete_vote',
				'title'  => __( 'Allow delete or change vote', 'userspace-rating' ),
				'values' => [ __( 'No', 'userspace-rating' ), __( 'Yes', 'userspace-rating' ) ]
			]
		];

	}

}
