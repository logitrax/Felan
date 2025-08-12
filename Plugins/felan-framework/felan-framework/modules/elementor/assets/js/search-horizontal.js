(function ($) {
  "use strict";

  var HorizontalSearchHandler = function ($scope, $) {
    var search_form = $scope.find(".felan-search-horizontal");
    var filter_search = search_form.find("#search-horizontal_filter_search");
    var available = filter_search.data("key");

    filter_search
      .autocomplete({
        source: available,
        minLength: 0,
        autoFocus: false,
        focus: true,
      })
      .focus(function () {
        $(this).data("uiAutocomplete").search($(this).val());
      });

    search_form.find(".felan-clear-top-filter").on("click", function () {
      filter_search.val("");
      search_form.find(".felan-select2").val("");
      search_form.find(".felan-select2").select2("destroy");
      search_form.find(".felan-select2").select2();
      search_form.find(".input-search-location").val("");
      var list_select = search_form.find(".felan-select2");
      list_select.each(function () {
        var option = $(this).find("option");
        if (theme_vars.enable_search_box_dropdown == 1) {
          if (option.length > theme_vars.limit_search_box) {
            $(this).select2();
          } else {
            $(this).select2({
              minimumResultsForSearch: -1,
            });
          }
        } else {
          $(this).select2({
            minimumResultsForSearch: -1,
          });
        }
      });
      $(".select2.select2-container").on("click", function () {
        var options = $(this).prev().find("option");
        options.each(function () {
          var option_val = $(this).val();
          var level = $(this).attr("data-level");
          $('.select2-results li[id$="' + option_val + '"]').attr(
            "data-level",
            level
          );
        });
      });
      $(".felan-form-location .icon-arrow i").on("click", function () {
        var options = $(this)
          .closest(".felan-form-location")
          .find("select.felan-select2 option");
        options.each(function () {
          var option_val = $(this).val();
          var level = $(this).attr("data-level");
          $('.select2-results li[id$="' + option_val + '"]').attr(
            "data-level",
            level
          );
        });
      });
    });
  };

  $(window).on("elementor/frontend/init", function () {
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/felan-search-horizontal.default",
      HorizontalSearchHandler
    );
  });
})(jQuery);
