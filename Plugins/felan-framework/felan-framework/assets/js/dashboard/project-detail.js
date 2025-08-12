(function ($) {
    "use strict";

    var form_refund = $("#form-project-order-refund");
    var ajax_url = felan_project_detail_vars.ajax_url;
    var payment_url = felan_project_detail_vars.payment_url;

    $(document).ready(function () {

        $("body").on(
            "click",
            ".btn-action-review",
            function () {
                var freelancer_id = $(this).attr("freelancer-id");
                var order_id = $(this).attr("order-id");

                $('input[name="freelancer_id"]').val(freelancer_id);
                $('input[name="order_id"]').val(order_id);
            }
        );

        $("body").on("click", ".btn-approve-proposal", function (e) {
            e.preventDefault();
            ajax_load("inprogress", $(this));
        });

        $("body").on("click", ".btn-reject-proposal", function (e) {
            e.preventDefault();
            ajax_load("reject", $(this));
        });

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
            form_refund.find('#btn-project-refund').attr("order-id",item_id);
        });

        $("body").on("click", "#btn-project-refund", function (e) {
            e.preventDefault();
            var tell_us = form_refund.find('input[name="reason"]:checked').val(),
                content_refund = form_refund.find('textarea[name="project_content_refund"]').val();
            ajax_load("refund",$(this), tell_us,content_refund);
        });

        function ajax_load(action_click = "", $button, tell_us = "", content_refund = "") {
            var order_id = $('input[name="order_id"]').val();
            var project_id = $('input[name="project_id"]').val();
            var proposal_price = $('input[name="proposal_price"]').val();
            var projects_budget_show = $('input[name="projects_budget_show"]').val();
            var proposal_time = $('input[name="proposal_time"]').val();
            var proposal_fixed_time = $('input[name="proposal_fixed_time"]').val();
            var proposal_rate = $('input[name="proposal_rate"]').val();

            $.ajax({
                dataType: "json",
                url: ajax_url,
                data: {
                    action: "felan_employer_project_detail",
                    action_click: action_click,
                    content_refund: content_refund,
                    tell_us: tell_us,
                    project_id: project_id,
                    order_id: order_id,
                    proposal_price: proposal_price,
                    projects_budget_show: projects_budget_show,
                    proposal_time: proposal_time,
                    proposal_fixed_time: proposal_fixed_time,
                    proposal_rate: proposal_rate,
                },
                beforeSend: function () {
                    $button.find(".btn-loading").fadeIn();
                },
                success: function (data) {
                    $button.find(".btn-loading").fadeOut();
                    if (data.success === true) {
                        if(action_click == 'inprogress'){
                            window.location.href = payment_url;
                        } else {
                            window.location.reload();
                        }
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
                    action: 'felan_project_order_message',
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
