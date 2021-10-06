<?php

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

?>


<div class="usp-rating-box__value"><?php echo $counting_type == 1 ? $votes_count : round( $rating_total, USP_RATING_PRECISION ); ?></div>

<div class="usp-rating-box__vote usp-rating-likes usps__inline usps__ai-center <?php echo $user_vote ? 'usp-rating-box__vote_voted' : ''; ?>"
     data-rating_value="<?php echo round( $rating_points, USP_RATING_PRECISION ); ?>">
    <i class="uspi <?php echo $user_vote ? 'fa-heart-fill' : 'fa-heart'; ?>"></i>
</div>