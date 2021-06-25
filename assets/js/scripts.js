jQuery(document).ready(function () {

  const $ = jQuery;

  $(document).on('click', '.usp-rating-box__vote', function () {

	let $box = $(this).closest('.usp-rating-box');
	
	if(!$box.hasClass('usp-rating-box_vote_can')) {
	  return;
	}

	usp_preloader_show($box);

	const object_type = $box.data('object_type');
	const object_id = $box.data('object_id');
	const object_author = $box.data('object_author');
	const rating_value = $(this).data('rating_value');

	usp_ajax({
	  data: {
		action: 'userspace_rating_ajax',
		method: 'process_vote',
		params: {
		  object_type,
		  object_id,
		  object_author,
		  rating_value
		}
	  },
	  success: resp => {
		$box.replaceWith(resp.html);
	  }
	});

  });

});

