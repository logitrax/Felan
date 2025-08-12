(function ($) {
  "use strict";

  var FelanModernMenuHandler = function ($scope, $) {
    $scope
      .find(
        ".desktop-menu li.menu-item-has-children > a .menu-item-wrap > .menu-item-title"
      )
      .append(
        '<span class="chevron"><i class="far fa-chevron-down"></i></span>'
      );
    $scope
      .find(".mobile-menu li.menu-item-has-children > a")
      .append(
        '<span class="chevron"><i class="far fa-chevron-down"></i></span>'
      );
  };

  $(window).on("elementor/frontend/init", function () {
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/felan-modern-menu.default",
      FelanModernMenuHandler
    );
  });
})(jQuery);
