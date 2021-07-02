<?php

/**
 * @var array $votes
 * @var object $object_type
 * @var string $context
 */
?>

<div class="usp-rating-votes usp-rating-votes_context_<?php echo $context; ?>">

  <div class="usp-rating-votes__list">

	<?php foreach ( $votes as $vote ) { ?>

	  <?php

	  $vote_html = $object_type->convert_vote_to_template( $vote );

	  ?>

  	<div class="usp-rating-votes__vote">
		<?php echo $vote_html; ?>
  	</div>

	<?php } ?>

  </div>

</div>