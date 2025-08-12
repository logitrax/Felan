(function ($) {
    "use strict";

    var Widget_Reload_Carousel = function ($scope, $) {
        var carousel_elem = $scope.find(".elementor-carousel").eq(0);
        var $slider = $scope.find('.elementor-slick-slider');
        var $progressBar = $slider.find('.progress');
        var $progressBarLabel = $slider.find('.slider__label');

        if (carousel_elem.length > 0) {
            var settings = carousel_elem.data("slider_options");
            if (settings["isslick"] == "false") {
                alert(settings["isslick"]);
                carousel_elem.unslick();
            } else {
                carousel_elem.not(".slick-initialized").slick(settings);
            }
        }

        $slider.on('beforeChange', function(event, slick, currentSlide, nextSlide) {
            var calc = ( (nextSlide) / (slick.slideCount-1) ) * 100;
            $progressBar
                .css('background-size', calc + '% 100%')
                .attr('aria-valuenow', calc );

            $progressBarLabel.text( calc + '% completed' );
        });
    };

    $(window).on("elementor/frontend/init", function () {
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/felan-testimonial.default",
            Widget_Reload_Carousel
        );
    });
})(jQuery);
