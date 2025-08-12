var PRINT = PRINT || {};
(function ($) {
  "use strict";

  PRINT = {
    init: function () {
      var ajax_url = felan_template_vars.ajax_url;
      $("body").on("click", "#btn-print-freelancer", function (e) {
        var freelancer_id = $(this).data("freelancer-id"),
          freelancer_print_window = window.open(
            "",
            freelancer_print_window,
            "scrollbars=0,menubar=0,resizable=1,width=991 ,height=800"
          );
        $.ajax({
          type: "POST",
          url: ajax_url,
          data: {
            action: "felan_freelancer_print_ajax",
            freelancer_id: freelancer_id,
            isRTL: $("body").hasClass("rtl") ? "true" : "false",
          },
          success: function (html) {
            freelancer_print_window.document.write(html);
            freelancer_print_window.document.close();
            freelancer_print_window.focus();
          },
        });
      });
    },
  };
  $(document).ready(function () {
    PRINT.init();
  });
})(jQuery);
