(function ($) {
  "use strict";

  var SwiperHandler = function ($scope, $) {
    var $element = $scope.find(".felan-slider-widget");

    $element.FelanSwiper();
  };

  var SwiperLinkedHandler = function ($scope, $) {
    var $element = $scope.find(".felan-slider-widget");

    if ($scope.hasClass("felan-swiper-linked-yes")) {
      var thumbsSlider = $element.filter(".felan-thumbs-swiper").FelanSwiper();
      var mainSlider = $element.filter(".felan-main-swiper").FelanSwiper({
        thumbs: {
          swiper: thumbsSlider,
        },
      });
    } else {
      $element.FelanSwiper();
    }
  };

  $(window).on("elementor/frontend/init", function () {
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/felan-image-carousel.default",
      SwiperHandler
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/felan-modern-carousel.default",
      SwiperHandler
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/felan-modern-slider.default",
      SwiperHandler
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/felan-freelancer-carousel.default",
      SwiperHandler
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/felan-team-member-carousel.default",
      SwiperHandler
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/felan-testimonial.default",
      SwiperLinkedHandler
    );
  });
})(jQuery);
