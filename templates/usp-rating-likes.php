<?php

/*
 * AUTHOR: https://codeseller.ru/author/preci/
 */

/**
 * @var object $object_type
 * @var int $object_id
 * @var int $object_author
 * @var string $object_rating
 * @var int $object_votes_count
 * @var bool $user_can_vote
 * @var null|string $user_vote
 * @var string $counting_type
 */

?>

<div class="usp-rating-box usp-rating-box_type_likes">

  <div class="usp-rating-box__inner">

	<div class="usp-rating-value usp-rating-value__type_likes">0</div>

	<div class="usp-rating-vote usp-rating-vote_type_likes <?php echo $user_can_vote ? 'usp-rating-vote_can':''; ?>"><i class="uspi fa-heart"></i></div>

  </div>

</div>