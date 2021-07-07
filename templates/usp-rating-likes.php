<?php

/**
 * @var object $object_type
 * @var int $object_id
 * @var int $object_author
 * @var number $user_vote
 * @var number $object_rating
 * @var number $average_points
 * @var number $votes_count
 * @var bool $user_can_vote
 * @var bool $user_can_view_votes
 * @var number $rating_points
 */
$classes = [ 'usp-rating-box', 'usp-rating-box_type_likes' ];

$user_can_vote && $classes[] = 'usp-rating-box_vote_can';
$user_can_view_votes && $votes_count && $classes[] = 'usp-rating-box_history_can';

?>

<div class="<?php echo implode( " ", $classes ); ?>" data-object_type="<?php echo $object_type->get_id(); ?>" data-object_id="<?php echo $object_id; ?>" data-object_author="<?php echo $object_author; ?>">

  <div class="usp-rating-box__inner usps__inline usps__ai-center">

	<div class="usp-rating-box__value"><?php echo $object_rating; ?></div>

	<div class="usp-rating-box__vote usp-rating-likes usps__inline usps__ai-center <?php echo $user_vote ? 'usp-rating-box__vote_voted' : ''; ?>" data-rating_value="<?php echo $rating_points; ?>">
	  <i class="uspi <?php echo $user_vote ? 'fa-heart-fill' : 'fa-heart'; ?>"></i>
	</div>

  </div>

</div>