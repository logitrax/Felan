(function ($) {
  "use strict";

  var ajax_url = felan_template_vars.ajax_url,
    custom_field_payout = felan_payout_vars.custom_field_payout;

  $(document).ready(function () {
    $(".felan-payout-dashboard li.payout-item").each(function () {
      var value = $(this).attr("id");
      $("." + value).appendTo("#" + value + " .content");
    });

    $("#btn-submit-payout").on("click", function (e) {
      var form_payout = $(".felan-payout-dashboard"),
        payout_paypal = form_payout.find('input[name="payout_paypal"]').val(),
        payout_stripe = form_payout.find('input[name="payout_stripe"]').val(),
        payout_card_number = form_payout
          .find('input[name="payout_card_number"]')
          .val(),
        payout_card_name = form_payout
          .find('input[name="payout_card_name"]')
          .val(),
        payout_bank_transfer_name = form_payout
          .find('input[name="payout_bank_transfer_name"]')
          .val();

      var custom_field = {};
      $.each(custom_field_payout, function (index, value) {
        if (value.id) {
          var val = $("input[name=" + value.id + "]").val();
          custom_field[value.id] = val;
        }
      });

      e.preventDefault();
      $.ajax({
        type: "POST",
        dataType: "json",
        url: ajax_url,
        data: {
          action: "felan_update_payout_ajax",
          payout_paypal: payout_paypal,
          payout_stripe: payout_stripe,
          payout_card_number: payout_card_number,
          payout_card_name: payout_card_name,
          payout_bank_transfer_name: payout_bank_transfer_name,
          custom_field: custom_field,
        },
        beforeSend: function () {
          form_payout.find(".btn-loading").fadeIn();
        },
        success: function (response) {
          if (response.success) {
            window.location.reload();
          } else {
              alert(response.message);
          }
          form_payout.find(".btn-loading").fadeOut();
        },
      });
    });
  });
})(jQuery);
