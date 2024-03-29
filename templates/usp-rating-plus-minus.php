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
 * @var int $counting_type - 1 - display value as sum positive and negative votes, 0 - display value as rating_total
 */
if ( $counting_type == 1 ) {
	$display_rating = round( ( $rating_total - $rating_points * $votes_count ) / $rating_points, USP_RATING_PRECISION );
} else {
	$display_rating = round( $rating_total, USP_RATING_PRECISION );
}

$rating_value_minus = - 1 * round( $rating_points, USP_RATING_PRECISION );
$rating_value_plus  = round( $rating_points, USP_RATING_PRECISION );

?>


<div class="usp-rating-box__vote usp-rating-minus usps__inline usps__ai-center <?php echo $user_vote && $user_vote == - $rating_points ? 'usp-rating-box__vote_voted' : ''; ?>"
     data-rating_value="<?php echo esc_attr( $rating_value_minus ); ?>">
    <i class="uspi fa-minus"></i>
</div>

<div class="usp-rating-box__value"><?php echo esc_html( $display_rating ); ?></div>

<div class="usp-rating-box__vote usp-rating-plus usps__inline usps__ai-center <?php echo $user_vote && $user_vote == $rating_points ? 'usp-rating-box__vote_voted' : ''; ?>"
     data-rating_value="<?php echo esc_attr( $rating_value_plus ); ?>">
    <i class="uspi fa-plus"></i>
</div>
