(function ($) {
    "use strict";
    $(document).ready(function () {
        var ajax_url = felan_company_review_vars.ajax_url;

        $("body").on("click", ".btn-action-review",
            function (e) {
                e.preventDefault();
                var company_id = $(this).attr('company-id');

                $.ajax({
                    type: "POST",
                    url: ajax_url,
                    dataType: "json",
                    data: {
                        action: "felan_company_write_a_review",
                        company_id: company_id,
                    },
                    beforeSend: function () {

                    },
                    success: function (response) {
                        if (response.html_review) {
                            $('.reviewForm .content-popup-review').html(response.html_review);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error: " + error);
                    }
                });

            }
        );
    });
})(jQuery);
