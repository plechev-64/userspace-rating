<?php

class USP_Rating_Votes_Query extends QueryBuilder {

	function __construct( $as = false ) {

		$table = array(
			'name' => USP_RATING_TABLE_VOTES,
			'as'   => $as ? $as : 'usp_rating_votes',
			'cols' => array(
				'ID',
				'user_id',
				'object_id',
				'object_author',
				'object_type',
				'rating_value',
				'rating_date'
			)
		);

		parent::__construct( $table );

	}

	static function insert( $data, $unused_param = '' ) {

		global $wpdb;

		return $wpdb->insert( USP_RATING_TABLE_VOTES, $data );

	}

	static function update( $where, $data ) {

		global $wpdb;

		return $wpdb->update( USP_RATING_TABLE_VOTES, $data, $where );

	}

	static function delete( $where ) {

		global $wpdb;

		return $wpdb->delete( USP_RATING_TABLE_VOTES, $where );

	}

}
