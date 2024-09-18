<?php
/**
 * Display user rating
 */
add_action( 'usp_user_stats', 'usp_user_stats_rating', 22, 2 );
function usp_user_stats_rating( User $user, $custom_data = [] ) {

	if ( ! in_array( 'rating', $custom_data ) || ! is_numeric( $user->rating ) ) {
		return;
	}

	$title = __( 'Rating', 'userspace' );
	$count = round( $user->rating, USP_RATING_PRECISION );
	$icon  = 'fa-star';
	$class = 'usp-meta__rating';

	echo usp_user_get_stat_item( $title, $count, $icon, $class );
}

/**
 * Filter users manager query
 */
add_filter( 'usp_users_query', 'usp_rating_add_users_query_data', 10, 2 );
function usp_rating_add_users_query_data( QueryBuilder $query, USP_Users_Manager $manager ) {

	$is_rating = in_array( 'rating', $manager->get_param( 'custom_data' ) );

	if ( ! $is_rating ) {
		return $query;
	}

	$rating_query = ( new USP_Rating_Users_Query( 'usp_rating' ) )
		->select( [ 'rating_total', 'user_id' ] )
		->limit( - 1 );

	$rating_sql = $rating_query->get_sql();

	$query->select_string( 'IFNULL(usp_rating.rating_total, 0) as rating' );
	$query->join_string( "LEFT JOIN ({$rating_sql}) as usp_rating ON users.ID = usp_rating.user_id" );

	return $query;
}

/**
 * Filter users manager search fields
 */
add_filter( 'usp_users_search_fields', 'usp_rating_add_users_search_field', 10, 2 );
function usp_rating_add_users_search_field( array $search_fields, USP_Users_Manager $manager ) {

	$is_rating = in_array( 'rating', $manager->get_param( 'custom_data' ) );

	if ( ! $is_rating ) {
		return $search_fields;
	}

	foreach ( $search_fields as $k => $field ) {
		if ( $field['slug'] == 'orderby' ) {
			$search_fields[ $k ]['values']['rating'] = __( 'Rating', 'userspace-rating' );
		}
	}

	return $search_fields;

}
