(function ($) {
    "use strict";

    const portfolioMasonry = $(".archive-post.layout-masonry").eq(0);
    portfolioMasonry.isotope({
        itemSelector: "article.type-post",
        percentPosition: true,
        transitionDuration: 1500,
        masonry: {
            columnWidth: "article.type-post",
            gutter: 30,
        },
    });
})(jQuery);


