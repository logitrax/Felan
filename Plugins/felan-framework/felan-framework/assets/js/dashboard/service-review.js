(function ($) {
    "use strict";
    $(document).ready(function () {
        var ajax_url = felan_service_review_vars.ajax_url;

        $("body").on("click", ".btn-action-review",
            function (e) {
                e.preventDefault();
                var service_id = $(this).attr('service-id');

                $.ajax({
                    type: "POST",
                    url: ajax_url,
                    dataType: "json",
                    data: {
                        action: "felan_service_write_a_review",
                        service_id: service_id,
                    },
                    beforeSend: function () {

                    },
                    success: function (response) {
                        if (response.html_review) {
                            $('.reviewForm .content-popup-review').html(response.html_review);
                            $('.reviewForm input[name="service_id"]').val(service_id);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error: " + error);
                    }
                });

            }
        );

        $("body").on("click", ".btn-action-view",
            function (e) {
                e.preventDefault();
                var service_id = $(this).attr('service-id');

                $.ajax({
                    type: "POST",
                    url: ajax_url,
                    dataType: "json",
                    data: {
                        action: "felan_service_view_review",
                        service_id: service_id,
                    },
                    beforeSend: function () {

                    },
                    success: function (response) {
                        if (response.html_review) {
                            $('.reviewForm .content-popup-review').html(response.html_review);
                            $('.reviewForm input[name="service_id"]').val(service_id);
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
