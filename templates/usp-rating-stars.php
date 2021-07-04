<?php

/**
 * @var object $object_type
 * @var int $object_id
 * @var int $object_author
 * @var number $object_rating
 * @var bool $user_can_vote
 * @var number $user_vote
 * @var bool $user_can_view_history
 * @var int $average_rating
 * @var array $stars_values
 * @var array $stars_percent
 */

?>

<div class="usp-rating-box usp-rating-box_type_stars <?php echo $user_can_vote ? 'usp-rating-box_vote_can' : ''; ?> <?php echo $user_can_view_history ? 'usp-rating-box_history_can' : ''; ?>" data-object_type="<?php echo $object_type->get_id(); ?>" data-object_id="<?php echo $object_id; ?>" data-object_author="<?php echo $object_author; ?>">

  <div class="usp-rating-box__inner">

	<div class="usp-rating-box__stars">

	  <?php foreach ( $stars_values as $star_num => $rating_value ) { ?>

  	  <div class="usp-rating-box__vote" data-rating_value="<?php echo $rating_value; ?>">

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