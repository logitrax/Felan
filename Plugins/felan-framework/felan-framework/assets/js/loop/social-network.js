(function ($) {
    "use strict";
    jQuery(document).ready(function () {
        $('.add-social').on('click', function (e) {
            e.preventDefault();
            $('.errors-log').text('');
            $('.add-social').addClass('disabled');
            var clone = $('.field-social-clone').html();
            $('.add-social-list').append(clone);
            $('.add-social-list .clone-wrap').each(function (index) {
                index += 1;
                $(this).find('.number-network').html(index);
            });
        });

        $('body').on('click', '.remove-social', function (e) {
            e.preventDefault();
            $(this).parents('.clone-wrap').remove();

            $('.add-social-list .clone-wrap').each(function (index) {
                index += 1;
                $(this).find('.number-network').html(index);
            });
        });
    });
})(jQuery);
