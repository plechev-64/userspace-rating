<?php

defined( 'ABSPATH' ) || exit;

/**
 * @var array $votes
 */

?>

<div class="usp-rating-history">

    <div class="usp-rating-history__list">

		<?php foreach ( $votes as $vote ) { ?>

			<?php

			$object_type = USP_Rating()->get_object_type( $vote->object_type );

			?>

            <div class="usp-rating-history__vote">
				<?php echo $object_type->convert_vote_to_template( $vote ); ?>
            </div>

		<?php } ?>

    </div>

</div>