<?php

/**
 * @var array $votes
 * @var object $object_type
 * @var string $template
 */

?>

<div class="usp-rating-history">

  <div class="usp-rating-history__list">

	<?php foreach ( $votes as $vote ) { ?>

	  <?php

	  $html = $object_type->convert_history_template( $template, $vote );

	  ?>

  	<div class="usp-rating-history__vote">
		<?php echo $html; ?>
  	</div>

	<?php } ?>

  </div>

</div>