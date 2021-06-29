<?php

/**
 * @var array $votes
 */

?>

<div class="usp-rating-votes">

  <div class="usp-rating-votes__list">

	<?php foreach ( $votes as $vote ) { ?>

	  <?php

	  $userdata = get_userdata( $vote->user_id );

	  if ( $userdata ) {
		$username = '<a href="' . get_author_posts_url( $vote->user_id ) . '">' . $userdata->display_name . '</a>';
	  } else {
		$username = __( 'Unknown', 'userspace-rating' );
	  }

	  $date = date( "Y-m-d", strtotime( $vote->rating_date ) );

	  $value = $vote->rating_value;

	  ?>

  	<div class="usp-rating-votes__vote">
  	  <div class="usp-rating-votes__vote_date">
		  <?php echo $date; ?>
  	  </div>
  	  <div class="usp-rating-votes__vote_user">
		  <?php echo $username; ?>
  	  </div>
  	  <div class="usp-rating-votes__vote_value">
		  <?php echo $value; ?>
  	  </div>
  	</div>

	<?php } ?>

  </div>

</div>