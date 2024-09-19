<?php

class USP_Rating_Votes_List_Manager extends ContentManager {

	private $_required_params = [
		'orderby'  => 'rating_date',
		'order'    => 'DESC',
		'pagenavi' => 1,
		'search'   => 1
	];

	private $_custom_params = [
		'object_type' => '_all'
	];

	public function __construct( array $args = [] ) {

		$this->set_custom_default_params( $this->_custom_params );

		parent::__construct( array_merge( $this->_required_params, $args ) );

	}

	function get_query() {

		$filter_by_type = $this->get_param( 'object_type' );

		if ( $filter_by_type !== '_all' ) {
			$where = [
				'object_author' => $this->get_param( 'object_author' ),
				'object_type'   => $filter_by_type
			];
		} else {

			$object_types = USP_Rating()->get_object_types()->get_all();

			$object_type__in = [];

			foreach ( $object_types as $object_type ) {

				$object_type__in[] = $object_type->get_id();
			}

			$where = [
				'object_author'   => $this->get_param( 'object_author' ),
				'object_type__in' => $object_type__in
			];
		}

		return USP_Rating()->votes_query()
		                   ->select( [] )
		                   ->where( $where );

	}

	function get_search_fields() {

		$object_types = USP_Rating()->get_object_types()->get_all();

		$object_types_list = [ '_all' => __( 'All', 'userspace-rating' ) ];

		foreach ( $object_types as $object_type ) {
			$object_types_list[ $object_type->get_id() ] = $object_type->get_name();
		}

		return [
			[
				'type'   => 'select',
				'slug'   => 'object_type',
				'title'  => __( 'Object type', 'userspace-rating' ),
				'values' => $object_types_list,
				'value'  => $this->get_param( 'object_type' )
			],
			[
				'type'   => 'select',
				'slug'   => 'orderby',
				'title'  => __( 'Sort by', 'userspace-rating' ),
				'values' => [
					'rating_date'  => __( 'Vote date', 'userspace-rating' ),
					'rating_value' => __( 'Vote value', 'userspace-rating' )
				],
				'value'  => $this->get_param( 'orderby' ),
			],
			[
				'type'   => 'radio',
				'slug'   => 'order',
				'title'  => __( 'Sorting direction', 'userspace-rating' ),
				'values' => [
					'DESC' => __( 'Descending', 'userspace-rating' ),
					'ASC'  => __( 'Ascending', 'userspace-rating' )
				],
				'value'  => $this->get_param( 'order' ),
			]
		];

	}

	function get_items_content() {

		$items = $this->get_items();

		$content = '<div class="usp-content-manager-content">';

		if ( ! $items ) {
			$content .= $this->get_no_result_notice();
		} else {

			$content .= usp_get_include_template( 'usp-rating-history-list.php', USP_RATING_BASE, [
				'votes' => $items
			] );
		}
		$content .= '</div>';

		return $content;

	}

}
