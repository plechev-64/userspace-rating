<?php

defined( 'ABSPATH' ) || exit;

/**
 * @var object $object_type
 * @var int $object_id
 * @var int $object_author
 * @var int $user_id
 * @var number $user_vote
 * @var number $rating_total
 * @var number $rating_average
 * @var number $votes_count
 * @var bool $user_can_vote
 * @var bool $user_can_view_votes
 * @var number $rating_points
 * @var int $counting_type - 1 - display value as votes_count, 0 - display value as rating_total
 */

if ( $counting_type == 1 ) {
	$display_rating = $votes_count;
} else {
	$display_rating = round( $rating_total, USP_RATING_PRECISION );
}

$rating_value = round( $rating_points, USP_RATING_PRECISION );

?>


<div class="usp-rating-box__value"><?php echo esc_html( $display_rating ); ?></div>

<div class="usp-rating-box__vote usp-rating-likes usps__inline usps__ai-center <?php echo $user_vote ? 'usp-rating-box__vote_voted' : ''; ?>"
     data-rating_value="<?php echo esc_attr( $rating_value ); ?>">
    <i class="uspi <?php echo $user_vote ? 'fa-heart-fill' : 'fa-heart'; ?>"></i>
</div>