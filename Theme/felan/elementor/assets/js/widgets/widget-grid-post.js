(function ($) {
    "use strict";

    var $body = $("body");

    var FelanGridDataHandler = function ($scope, $) {
        var $element = $scope.find(".felan-grid-wrapper");
        $element.FelanGridLayout();

        handlerOverlayHuge($element);

        handlerOverlayMovement($element);
    };

    function handlerOverlayMovement($element) {
        $element.on("mousemove", ".post-wrapper", function (e) {
            var offset = $(this).offset();
            var x = e.pageX - offset.left;
            var y = e.pageY - offset.top;

            var mover = $(this).find(".post-overlay");

            var moverW = mover.width() / 2;
            var moverH = mover.height() / 2;

            x -= moverW;
            y -= moverH;

            var finalX = parseInt(x);
            var finalY = parseInt(y);

            mover.css(
                "transform",
                "translate3d(" + finalX + "px," + finalY + "px,0px)"
            );
        });
    }

    function handlerOverlayHuge($element) {
        $element.on("mouseenter", ".grid-item", function () {
            $element.addClass("on");
        });

        $element.on("mouseleave", ".grid-item", function () {
            $element.removeClass("on");
        });
    }

    $(window).on("elementor/frontend/init", function () {
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/felan-blog.default",
            FelanGridDataHandler
        );
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/felan-product.default",
            FelanGridDataHandler
        );
    });

})(jQuery);
