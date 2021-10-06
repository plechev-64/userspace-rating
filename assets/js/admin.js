jQuery(document).ready(function () {

    const $ = jQuery;

    $(document).on('click', '.usp-rating__manage_button input', function () {

        let $box = $(this).closest('.usp-rating__manage');
        let user_id = $box.data('user_id');
        let new_rating = $box.find('.usp-rating__manage_val input').val();

        usp_ajax({
            data: {
                action: 'usp_rating_ajax',
                method: 'edit_user_rating',
                params: {
                    user_id,
                    new_rating
                }
            }
        });

    });

});

