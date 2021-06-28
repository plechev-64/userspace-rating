jQuery(document).ready(function () {

  const $ = jQuery;

  $(document).on('click', '.usp-rating-box_vote_can .usp-rating-box__vote', function () {

	let $box = $(this).closest('.usp-rating-box');

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

  $(document).on('click', '.usp-rating-box_history_can .usp-rating-box__value', function () {

	let $box = $(this).closest('.usp-rating-box');

	usp_preloader_show($box);

	const object_type = $box.data('object_type');
	const object_id = $box.data('object_id');

	usp_ajax({
	  data: {
		action: 'userspace_rating_ajax',
		method: 'object_votes',
		params: {
		  object_type,
		  object_id
		}
	  },
	  success: resp => {
		display_votes(resp.html, $box);
	  }
	});

  });

  function display_votes(votes_html, $container) {

	let $votes_html = $(votes_html);

	$('body').append($votes_html);

	let $value = $container.find('.usp-rating-box__value');

	let value_pos = $value.offset();

	let votes_left = value_pos.left - $votes_html.outerWidth() + $value.outerWidth();

	let votes_css = {
	  left: (votes_left - $votes_html.outerWidth() < 10) ? 10 : votes_left,
	  top: value_pos.top + $container.outerHeight(),
	  opacity: 1
	};


	$votes_html.css(votes_css);

	init_outside_click_listener();

  }

  function init_outside_click_listener() {

	$(document).on('mousedown touchstart', process_outside_click);

  }

  function destroy_outside_click_listener() {

	$(document).off('mousedown touchstart', process_outside_click);

  }

  function process_outside_click(e) {

	if (!$(e.target).closest('.usp-rating-votes').length) {

	  destroy_outside_click_listener();

	  $('.usp-rating-votes').css({opacity: 0});

	  setTimeout(() => {
		$('.usp-rating-votes').remove();
	  }, 200);
	}
  }

});

