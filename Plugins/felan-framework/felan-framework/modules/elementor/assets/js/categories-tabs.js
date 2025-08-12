(function ($) {
  "use strict";

  var FelanCategoriesTabsHandler = function ($scope, $) {
    var $nav = $scope.find(".nav-categories-tabs"),
      $content = $scope.find(".categories-tabs-item");
    $nav.find(".nav-item a").on("click", function (e) {
      e.preventDefault();
      var id = $(this).attr("href");
      $nav.find(".nav-item").removeClass("active");
      $(this).parent().addClass("active");

      $content.removeClass("active");
      $(id).addClass("active");
    });
  };

  $(window).on("elementor/frontend/init", function () {
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/felan-categories-tabs.default",
      FelanCategoriesTabsHandler
    );
  });
})(jQuery);
