var MESSAGES = MESSAGES || {};
(function ($) {
  "use strict";
  var ajax_url = felan_template_vars.ajax_url,
    form_popup = $("#form-apply-project");

  MESSAGES = {
    init: function () {
        $("body").off("click", "#btn-send-proposal").on("click", "#btn-send-proposal", function (e) {
        e.preventDefault();
        var $this = $(this),
          proposal_price = form_popup
            .find('input[name="proposal_price"]')
            .val(),

          proposal_price_fee = form_popup.find("li.fee .number").text(),
          proposal_total_price = form_popup.find("li.total .number").text(),
          proposal_total_hous = form_popup.find("li.total-hours .number").text(),
          proposal_estimated_hours = form_popup.find("li.estimated-hours .number").text(),
          proposal_time = form_popup.find('input[name="proposal_time"]').val(),
          proposal_fixed_time = form_popup.find('input[name="proposal_fixed_time"]').val(),
          proposal_rate = form_popup.find('select[name="proposal_rate"]').val(),
          content_message = form_popup
            .find('textarea[name="content_message"]')
            .val(),
           proposal_maximum_time = $("input#project_maximum_time").val(),
           creator_message = $("input#project_author_id").val(),
           proposal_id = $("input#proposal_id").val(),
           recipient_message = $("input#project_post_current").val();

        $.ajax({
          type: "POST",
          url: ajax_url,
          dataType: "json",
          data: {
            action: "felan_send_proposal_project",
            proposal_price: proposal_price,
            proposal_price_fee: proposal_price_fee,
            proposal_total_price: proposal_total_price,
            proposal_total_hous: proposal_total_hous,
            proposal_estimated_hours: proposal_estimated_hours,
            proposal_time: proposal_time,
            proposal_fixed_time: proposal_fixed_time,
            proposal_rate: proposal_rate,
            proposal_maximum_time: proposal_maximum_time,
            content_message: content_message,
            proposal_id: proposal_id,
            creator_message: creator_message,
            recipient_message: recipient_message,
          },
          beforeSend: function () {
            $this.find(".btn-loading").fadeIn();
          },
          success: function (data) {
            if (data.success == true) {
              form_popup.find(".felan-message-error").addClass("true");
                if ($('#form-apply-project .project-popup').hasClass('update-proposal')) {
                    $('#form-apply-project .project-popup.update-proposal').html(data.update_proposal);
                } else {
                    $('#form-apply-project .project-popup').html(data.thank_proposals);
                }
              $('body').on("click", ".bg-overlay, .btn-close", function(e) {
                  e.preventDefault();
                  location.reload();
              });
            }
            form_popup.find(".felan-message-error").text(data.message);
            $this.find(".btn-loading").fadeOut();
          },
        });
      });
    },
  };

  $(document).ready(function () {
    MESSAGES.init();
  });
})(jQuery);
