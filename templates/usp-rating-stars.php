<?php

/*
 * AUTHOR: https://codeseller.ru/author/preci/
 */

/**
 * @var object $object_type
 * @var int $object_id
 * @var int $object_author
 * @var string $object_rating
 * @var bool $user_can_vote
 * @var null|string $user_vote
 * @var bool $user_can_view_history
 * @var int $stars_count
 * @var number $average_rating
 * @var number $rating_per_star
 * @var number $full_stars
 * @var number $half_star
 * @var array $icons
 */

?>

<div class="usp-rating-box usp-rating-box_type_stars <?php echo $user_can_vote ? 'usp-rating-box_vote_can' : ''; ?>" data-object_type="<?php echo $object_type->get_id(); ?>" data-object_id="<?php echo $object_id; ?>" data-object_author="<?php echo $object_author; ?>">

  <div class="usp-rating-box__inner">

	<div class="usp-rating-box__stars">

	  <?php for ( $i = 1; $i <= $stars_count; $i++ ) { ?>

  	  <div class="usp-rating-box__vote" data-rating_value="<?php echo $rating_per_star * $i; ?>">

		  <?php if ( $full_stars ) { ?>

			<?php $full_stars--; ?>

			<i class="uspi <?php echo $icons[ 'full' ]; ?>"></i>

		  <?php } else if ( $half_star ) { ?>

			<?php $half_star--; ?>

			<i class="uspi <?php echo $icons[ 'half' ]; ?>"></i>

		  <?php } else { ?>

			<i class="uspi <?php echo $icons[ 'empty' ]; ?>"></i>

		  <?php } ?>
  	  </div>

	  <?php } ?>
	</div>

	<div class="usp-rating-box__value"><?php echo $object_rating; ?></div>

  </div>

</div>