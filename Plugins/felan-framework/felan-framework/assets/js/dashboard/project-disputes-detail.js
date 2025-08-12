(function ($) {
    "use strict";

    var ajax_url = felan_project_disputes_detail_vars.ajax_url;

    $(document).ready(function () {
        $("body").on("click", ".btn-approve", function (e) {
            e.preventDefault();
            ajax_load("approve", $(this));
        });

        $("body").on("click", ".btn-canceled", function (e) {
            e.preventDefault();
            ajax_load("canceled", $(this));
        });

        function ajax_load(action_click = "", $button) {
            var disputes_id = $('input[name="disputes_id"]').val();
            var recipient_id = $('input[name="recipient_id"]').val();

            $.ajax({
                dataType: "json",
                url: ajax_url,
                data: {
                    action: "felan_project_disputes_detail",
                    action_click: action_click,
                    disputes_id: disputes_id,
                    recipient_id: recipient_id,
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
        var form_message = $('#felan-form-message-disputes');
        $("body").on("click", ".btn-send-message", function (e) {
            e.preventDefault();
            var message_content= form_message.find('#message_content').val();
            var recipient_id = form_message.find('#recipient_id').val();
            var disputes_id = form_message.find('#disputes_id').val();
            var attachment_id = form_message.find('#felan_drop_cv').data("attachment-id");
            var user_role = form_message.find('#user_role').val();
            var $this = $(this);

            $.ajax({
                dataType: "json",
                url: ajax_url,
                data: {
                    action: 'felan_project_disputes_message',
                    message_content: message_content,
                    recipient_id: recipient_id,
                    disputes_id: disputes_id,
                    attachment_id: attachment_id,
                    user_role: user_role,
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
