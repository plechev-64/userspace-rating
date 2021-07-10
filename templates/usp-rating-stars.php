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
 * @var array $stars_values
 * @var array $stars_percent
 */

?>

<div class="usp-rating-stars usps__inline">

  <?php foreach ( $stars_values as $star_num => $rating_value ) { ?>

    <div class="usp-rating-box__vote usps__inline usps__ai-center <?php echo $user_vote == $rating_value ? 'usp-rating-box__vote_voted' : ''; ?>" data-rating_value="<?php echo $rating_value; ?>">

  	<i class="uspi fa-star usp-rating-stars__icon"><i class="uspi fa-star-fill usp-rating-stars__icon_in" style="width: <?php echo $stars_percent[ $star_num ]; ?>%;"></i></i>
    </div>

  <?php } ?>
</div>

<div class="usp-rating-box__value"><?php echo $rating_average; ?></div>