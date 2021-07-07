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
 * @var array $stars_values
 * @var array $stars_percent
 */
$classes = [ 'usp-rating-box', 'usp-rating-box_type_stars' ];

$user_can_vote && $classes[] = 'usp-rating-box_vote_can';
$user_can_view_votes && $votes_count && $classes[] = 'usp-rating-box_history_can';

?>

<div class="<?php echo implode( " ", $classes ); ?>" data-object_type="<?php echo $object_type->get_id(); ?>" data-object_id="<?php echo $object_id; ?>" data-object_author="<?php echo $object_author; ?>">

  <div class="usp-rating-box__inner usps__inline usps__ai-center">

	<div class="usp-rating-stars usps__inline">

	  <?php foreach ( $stars_values as $star_num => $rating_value ) { ?>

  	  <div class="usp-rating-box__vote usps__inline usps__ai-center" data-rating_value="<?php echo $rating_value; ?>">

		  <?php if ( $stars_percent[ $star_num ] == 100 ) { ?>

			<i class="uspi fa-star-fill"></i>

		  <?php } else if ( $stars_percent[ $star_num ] > 0 ) { ?>

			<i class="uspi fa-star-half-o"></i>

		  <?php } else { ?>

			<i class="uspi fa-star"></i>

		  <?php } ?>
  	  </div>

	  <?php } ?>
	</div>

	<div class="usp-rating-box__value"><?php echo $average_rating; ?></div>

  </div>

</div>