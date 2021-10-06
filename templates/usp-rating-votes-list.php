<?php

/**
 * @var array $votes
 */

?>

<div class="usp-rating-votes">

    <div class="usp-rating-votes__list">

		<?php foreach ( $votes as $vote ) { ?>

			<?php

			$object_type = USP_Rating()->get_object_type( $vote->object_type );

			$vote_html = $object_type->convert_vote_to_template( $vote );

			?>

            <div class="usp-rating-votes__vote">
				<?php echo $vote_html; ?>
            </div>

		<?php } ?>

    </div>

</div>