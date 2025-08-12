var FREELANCER = FREELANCER || {};
(function ($) {
  "use strict";

  FREELANCER = {
    init: function () {
      this.tab_freelancer();
      this.social_freelancer();
      this.login_notice();
    },

    tab_freelancer: function () {
      function tabfreelancer(obj) {
        $(".jobs-freelancer-sidebar ul li").removeClass("active");
        $(obj).addClass("active");
        var id = $(obj).find("a").attr("href");
        $(".tab-info-freelancer").hide();
        $(id).show();
      }
      $(".tab-freelancer li").click(function () {
        tabfreelancer(this);
        return false;
      });
      tabfreelancer($(".jobs-freelancer-sidebar ul li:first-child"));
    },

    social_freelancer: function () {
      $("body").on(
        "click",
        "#freelancer-submit-social .soical-remove-inner",
        function () {
          var wrap = $(this).closest(".clone-wrap");
          $(wrap).find(".field-wrap").slideToggle();
        }
      );
    },

    login_notice: function () {
      var notice = $(".btn-login.notice-employer").data("notice");
      if ($(".btn-login").hasClass("notice-employer")) {
        $("#popup-form .notice").html(
          '<i class="fal fa-exclamation-circle"></i>' + notice
        );
      }
    },
  };
  $(document).ready(function () {
    FREELANCER.init();
  });
})(jQuery);
