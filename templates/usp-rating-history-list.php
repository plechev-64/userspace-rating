<?php

/**
 * @var array $votes
 */

?>

<div class="usp-rating-history">

    <div class="usp-rating-history__list">

		<?php foreach ( $votes as $vote ) { ?>

			<?php

			$object_type = USP_Rating()->get_object_type( $vote->object_type );

			$vote_html = $object_type->convert_vote_to_template( $vote );

			?>

            <div class="usp-rating-history__vote">
				<?php echo $vote_html; ?>
            </div>

		<?php } ?>

    </div>

</div>