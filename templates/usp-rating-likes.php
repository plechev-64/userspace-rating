<?php

/*
 * AUTHOR: https://codeseller.ru/author/preci/
 */

/**
 * @var object $object_type
 * @var int $object_id
 * @var int $object_author
 * @var int $rating_points
 * @var string $object_rating
 * @var bool $user_can_vote
 * @var null|string $user_vote
 * @var bool $user_can_view_history
 */

?>

<div class="usp-rating-box usp-rating-box_type_likes usp-rating-box_object-type_<?php echo $object_type->get_id(); ?> <?php echo $user_can_view_history ? 'usp-rating-box__history_can' : ''; ?>" data-object_type="<?php echo $object_type->get_id(); ?>" data-object_id="<?php echo $object_id; ?>" data-object_author="<?php echo $object_author; ?>">

  <div class="usp-rating-box__inner">

	<div class="usp-rating-box__value"><?php echo $object_rating; ?></div>

	<div class="usp-rating-box__vote <?php echo $user_can_vote ? 'usp-rating-box__vote_can' : ''; ?>" data-rating_value="<?php echo $rating_points; ?>"><i class="uspi fa-heart"></i></div>

  </div>

</div>