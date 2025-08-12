(function ($) {
	"use strict";

	var FelanClientLogoAnimationHandler = function ($scope, $) {
		$(".client-logo-inner").each(function () {
			var item = $(this).find(".lagi-client-logo-item");
			if (item.length > 0) {
				var bodyWidth = $("body").width();
                if (bodyWidth < 768) {
                    bodyWidth = 1000;
				}
				$(this).css("min-width", bodyWidth);
			}
		});
	};

	$(window).on("elementor/frontend/init", function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/lagi-client-logo-animation.default",
			FelanClientLogoAnimationHandler
		);
	});
})(jQuery);
