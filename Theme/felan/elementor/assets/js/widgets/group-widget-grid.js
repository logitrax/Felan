(function ($) {
  "use strict";

  var FelanGridHandler = function ($scope, $) {
    var $element = $scope.find(".felan-grid-wrapper");

    $element.FelanGridLayout();
  };

  $(window).on("elementor/frontend/init", function () {
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/felan-image-gallery.default",
      FelanGridHandler
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/felan-testimonial-grid.default",
      FelanGridHandler
    );
  });
})(jQuery);
