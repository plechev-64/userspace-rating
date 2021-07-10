<?php

/*
 * $params array - rating box params
 * 
 */

$object_type = $params[ 'object_type' ];
$rating_type = $params[ 'rating_type' ];

$classes = [ 'usp-rating-box', 'usp-rating-box_type_' . $rating_type->get_id() ];

$params[ 'user_can_vote' ] && $classes[] = 'usp-rating-box_vote_can';
$params[ 'user_can_view_votes' ] && $params[ 'votes_count' ] && $classes[] = 'usp-rating-box_history_can';

?>

<div class="<?php echo implode( " ", $classes ); ?>" data-object_type="<?php echo $object_type->get_id(); ?>" data-object_id="<?php echo $params[ 'object_id' ]; ?>" data-object_author="<?php echo $params[ 'object_author' ]; ?>">

  <div class="usp-rating-box__inner usps__inline usps__ai-center">

	<?php

	echo $rating_type->get_vote_buttons_and_value( $params );

	?>

  </div>

</div>