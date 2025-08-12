(function ($) {
  "use strict";

  var FelanModernTabsHandler = function ($scope, $) {};

  $(window).on("load", function () {
    function activeTab(obj) {
      $(".felan-modern-tabs ul li").removeClass("active");
      $(obj).addClass("active");
      var id = $(obj).find("a").attr("href");
      $(".modern-tabs-item").hide();
      $(id).show();
    }
    $(".nav-modern-tabs li").click(function () {
      activeTab(this);
      return false;
    });
    setTimeout(function () {
      activeTab($(".nav-modern-tabs li:first-child"));
      $(".content-modern-tabs .modern-tabs-item:first-child").show();
    }, 1000);
  });

  $(window).on("elementor/frontend/init", function () {
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/felan-modern-tabs.default",
      FelanModernTabsHandler
    );
  });
})(jQuery);
