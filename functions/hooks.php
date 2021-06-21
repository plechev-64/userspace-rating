<?php

add_filter( 'the_content', 'userspace_rating_posts_display' );

function userspace_rating_posts_display($content) {

  global $post;

  $USP_Rating = USP_Rating::get_instance();

  $content .= $USP_Rating->get_rating_box( $post->ID, $post->post_author, $post->post_type );

  return $content;

}
