var DOWNLOADCV = DOWNLOADCV || {};
(function ($) {
  "use strict";

  DOWNLOADCV = {
    init: function () {
      var ajax_url = felan_template_vars.ajax_url;
      var package_expires = felan_template_vars.package_expires;
      $("body").on("click", "#btn-download-cv-freelancer", function () {
        $.ajax({
          type: "POST",
          url: ajax_url,
          data: {
            action: "felan_freelancer_download_cv",
          },
          dataType: "json",
          success: function (data) {
            if (data.message) {
              alert(package_expires);
              window.location.reload();
            }
          },
        });
      });
    },
  };
  $(document).ready(function () {
    DOWNLOADCV.init();
  });
})(jQuery);
