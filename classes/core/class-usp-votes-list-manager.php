<?php

class USP_Votes_List_Manager extends USP_Content_Manager {

  public $object_author;

  function __construct($args = array()) {

	$this->init_custom_prop( 'object_author', isset( $args[ 'object_author' ] ) ? $args[ 'object_author' ] : null  );

	parent::
	__construct( array(
		'number' => 10,
		'is_ajax' => 1,
	) );

  }

  function get_query() {

	return USP_Rating()->votes_query()
	->select( [] )
	->where( [
		'object_author' => $this->object_author,
		'object_type' => $this->get_request_data_value( 'object_type' )
	] )
	->orderby(
	$this->get_request_data_value( 'orderby', 'rating_date' ),
	$this->get_request_data_value( 'order', 'DESC' )
	);

  }

  function get_data_content() {

	$content .= '<div class="manager-content">';

	if ( !$this->data ) {
	  $content .= $this->get_no_result_notice();
	} else {

	  $content .= usp_get_include_template( 'usp-rating-votes-list.php', USERSPACE_RATING_PATH . 'userspace-rating.php', [
		  'votes' => $this->data,
		  'context' => 'tab'
	  ] );
	}
	$content .= '</div>';

	return $content;

  }

  function get_search_fields() {

	$object_types = USP_Rating()->get_object_types()->get_all();

	$object_types_list = ['' => 'Все'];

	foreach ( $object_types as $object_type ) {
	  $object_types_list[ $object_type->get_id() ] = $object_type->get_name();
	}

	return array(
		array(
			'type' => 'select',
			'slug' => 'object_type',
			'title' => __( 'Тип объекта' ),
			'values' => $object_types_list,
			'value' => $this->get_request_data_value( 'object_type' )
		),
		array(
			'type' => 'select',
			'slug' => 'orderby',
			'title' => __( 'Сортировка по' ),
			'values' => [
				'rating_date' => __( 'Дате оценки', 'wp-recall' ),
				'rating_value' => __( 'Значению оценки', 'wp-recall' )
			],
			'value' => $this->get_request_data_value( 'orderby', 'rating_date' ),
		),
		array(
			'type' => 'radio',
			'slug' => 'order',
			'title' => __( 'Направление сортировки' ),
			'values' => [
				'DESC' => __( 'По убыванию' ),
				'ASC' => __( 'По возрастанию' )
			],
			'value' => $this->get_request_data_value( 'order', 'DESC' ),
		)
	);

  }

}
