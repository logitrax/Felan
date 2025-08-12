(function ($) {
  "use strict";

  var FelanFlipBoxHandler = function ($scope, $) {
    var $element = $scope.find(".felan-flip-box");

    $element.imagesLoaded(function () {
      calMaxHeight();
    });

    $(window).on("resize", calMaxHeight);

    function calMaxHeight() {
      var frontSideHeight = $element
        .children(".front-side")
        .find(".layer-content")
        .outerHeight();
      var backSideHeight = $element
        .children(".back-side")
        .find(".layer-content")
        .outerHeight();
      var maxHeight = Math.max(frontSideHeight, backSideHeight);

      $element.height(maxHeight);
    }
  };

  $(window).on("elementor/frontend/init", function () {
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/felan-flip-box.default",
      FelanFlipBoxHandler
    );
  });
})(jQuery);
