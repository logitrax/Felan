var NOTIFICATION = NOTIFICATION || {};
(function ($) {
  "use strict";
  var ajax_url = felan_template_vars.ajax_url,
    notification = $(".felan-notification"),
    icon = notification.find(".icon-noti"),
    content = notification.find(".content-noti");

  NOTIFICATION = {
    init: function () {
      this.content_active();
      this.refresh_notification();
    },

    content_active: function () {
      icon.on("click", function (e) {
        e.preventDefault();
        content.toggleClass("active");
      });
    },

    refresh_notification: function () {
      $("body").on("click", ".felan-notification .btn-delete", function (e) {
        e.preventDefault();
        var noti_id = $(this).data("noti-id");
        ajax_load(noti_id, "delete");
      });

      $("body").on("click", ".felan-notification .noti-clear", function (e) {
        e.preventDefault();
        ajax_load("", "clear");
      });

      $("body").on("click", ".felan-notification .noti-refresh", function (e) {
        e.preventDefault();
        ajax_load();
      });

      function close_noti() {
        notification.find(".close-noti").on("click", function (e) {
          e.preventDefault();
          content.removeClass("active");
        });
      }
      close_noti();

      function ajax_load(noti_id = "", action_click = "") {
        $.ajax({
          type: "POST",
          url: ajax_url,
          dataType: "json",
          data: {
            action: "felan_refresh_notification",
            noti_id: noti_id,
            action_click: action_click,
          },
          beforeSend: function () {
            $('.content-noti .noti-refresh .fa-sync').addClass('fa-spin');
          },
          success: function (data) {
            if (data.success == true) {
              icon.find("span").text(data.count);
              content.html(data.noti_content);
              close_noti();
            }
            $('.content-noti .noti-refresh .fa-sync').removeClass('fa-spin');
          },
        });
      }
    },
  };

  $(document).ready(function () {
    NOTIFICATION.init();
  });
})(jQuery);
