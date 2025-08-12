(function ($) {
    "use strict";
    $(document).ready(function () {
        var search_id =  '#' + $('.archive-search-control').attr('id');
        var available = $(search_id).data("key");
        $(search_id).autocomplete({
            source: available,
            minLength: 0,
            autoFocus: false,
            focus: true,
        }).focus(function () {
            $(this).data("uiAutocomplete").search($(this).val());
        });
    });
})(jQuery);
