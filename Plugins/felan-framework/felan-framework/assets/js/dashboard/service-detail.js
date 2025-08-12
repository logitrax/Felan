(function ($) {
    "use strict";

    var form_refund = $("#form-service-order-refund");
    var ajax_url = felan_service_detail_vars.ajax_url;

    $(document).ready(function () {
        $("body").on(
            "click",
            ".order-bottom .btn-action-review",
            function () {
                var service_id = $(this).attr("service-id");
                var order_id = $(this).attr("order-id");

                $('input[name="service_id"]').val(service_id);
                $('input[name="order_id"]').val(order_id);
            }
        );

        $("body").on("click", ".btn-complete", function (e) {
            e.preventDefault();
            ajax_load("completed", $(this));
        });

        $("body").on("click", ".order-status .btn-canceled", function (e) {
            e.preventDefault();
            ajax_load("canceled", $(this));
        });

        $("body").on("click", ".btn-order-refund", function (e) {
            var item_id = $(this).attr("order-id");
            form_refund.find('#btn-service-refund').attr("order-id",item_id);
        });

        $("body").on("click", "#btn-service-refund", function (e) {
            e.preventDefault();
            var tell_us = form_refund.find('input[name="reason"]:checked').val(),
                content_refund = form_refund.find('textarea[name="service_content_refund"]').val();
            ajax_load("refund",$(this), tell_us,content_refund);
        });

        function ajax_load(action_click = "", $button, tell_us = "", content_refund = "") {
            var order_id = $('input[name="order_id"]').val();
            var order_price = $('input[name="order_price"]').val();

            $.ajax({
                dataType: "json",
                url: ajax_url,
                data: {
                    action: "felan_employer_service_detail",
                    action_click: action_click,
                    content_refund: content_refund,
                    tell_us: tell_us,
                    order_price: order_price,
                    order_id: order_id,
                },
                beforeSend: function () {
                    $button.find(".btn-loading").fadeIn();
                },
                success: function (data) {
                    $button.find(".btn-loading").fadeOut();
                    if (data.success === true) {
                        window.location.reload();
                    } else {
                        form_refund.find(".felan-message-error").text(data.message);
                    }
                }
            });
        }

        //Message
        var form_message = $('#felan-form-message-order');
        $("body").on("click", ".btn-send-message", function (e) {
            e.preventDefault();
            var message_content= form_message.find('#message_content').val();
            var recipient_id = form_message.find('#recipient_id').val();
            var order_id = form_message.find('#order_id').val();
            var user_role = form_message.find('#user_role').val();
            var attachment_id = form_message.find('#felan_drop_cv').data("attachment-id");
            var $this = $(this);

            $.ajax({
                dataType: "json",
                url: ajax_url,
                data: {
                    action: 'felan_service_order_message',
                    message_content: message_content,
                    recipient_id: recipient_id,
                    order_id: order_id,
                    user_role: user_role,
                    attachment_id: attachment_id,
                },
                beforeSend: function () {
                    $this.find(".btn-loading").fadeIn();
                },
                success: function (data) {
                    $this.find(".btn-loading").fadeOut();
                    form_message.find(".message_error").html(data.message);
                    if (data.success === true) {
                        window.location.reload();
                        form_message.find(".message_error").addClass("true");
                    }
                }
            });
        });
    });
})(jQuery);
