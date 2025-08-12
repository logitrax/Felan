(function ($) {
  "use strict";

  var FelanPaginationHandler = function ($scope, $) {
    var $element = $scope.find(".felan-jobs");

    var pagination = $element.find(".felan-pagination li .page-numbers");

    var ajax_url = felan_template_vars.ajax_url;

    $("body").on("click", ".felan-pagination a.page-numbers", function (e) {
      e.preventDefault();
      $element
        .find(".felan-pagination li .page-numbers")
        .removeClass("current");
      $(this).addClass("current");
      var paged = $(this).text();
      var current_page = 1;
      if ($element.find('input[name="paged"]').val()) {
        current_page = $(".felan-pagination").find('input[name="paged"]').val();
      }
      if ($(this).hasClass("next")) {
        paged = parseInt(current_page) + 1;
      }
      if ($(this).hasClass("prev")) {
        paged = parseInt(current_page) - 1;
      }
      $element.find(".felan-pagination").find('input[name="paged"]').val(paged);

      ajax_load();
    });

    function ajax_load() {
      var paged = 1;
      var layout = $element.find('input[name="layout"]').val();
      var type_pagination = $element
        .find(".felan-pagination")
        .attr("data-type");
      var item_amount = $element.find('input[name="item_amount"]').val();
      var include_ids = $element.find('input[name="include_ids"]').val();
      var type_query = $element.find('input[name="type_query"]').val();
      var orderby = $element.find('input[name="orderby"]').val();
      var settings = $element.find('input[name="settings"]').val();
      paged = $element
        .find(".felan-pagination")
        .find('input[name="paged"]')
        .val();

      var jobs_categories = [];
      var jobs_skills = [];
      var jobs_type = [];
      var jobs_location = [];
      var jobs_career = [];
      var jobs_experience = [];

      $("input[name='jobs-categories']:checked").each(function () {
        jobs_categories.push($(this).val());
      });
      $("input[name='jobs-skills']:checked").each(function () {
        jobs_skills.push($(this).val());
      });
      $("input[name='jobs-type']:checked").each(function () {
        jobs_type.push($(this).val());
      });
      $("input[name='jobs-location']:checked").each(function () {
        jobs_location.push($(this).val());
      });
      $("input[name='jobs-career']:checked").each(function () {
        jobs_career.push($(this).val());
      });
      $("input[name='jobs-experience']:checked").each(function () {
        jobs_experience.push($(this).val());
      });
      $.ajax({
        dataType: "json",
        url: ajax_url,
        data: {
          action: "felan_el_jobs_pagination_ajax",
          layout: layout,
          item_amount: item_amount,
          type_pagination: type_pagination,
          include_ids: include_ids,
          type_query: type_query,
          orderby: orderby,
          settings: settings,
          jobs_categories: jobs_categories,
          jobs_skills: jobs_skills,
          jobs_type: jobs_type,
          jobs_location: jobs_location,
          jobs_career: jobs_career,
          jobs_experience: jobs_experience,
          paged: paged,
        },
        beforeSend: function () {
          $element.find(".felan-jobs-item").addClass("skeleton-loading");
          if (type_pagination == "loadmore") {
            $element.find(".btn-loading").fadeIn();
          }
        },
        success: function (data) {
          $element.find(".pagination").html(data.pagination);
          $element.find(".felan-jobs-item").removeClass("skeleton-loading");

          if (type_pagination == "number") {
            $element.find(".elementor-grid").html(data.jobs_html);
          } else {
            $element.find(".elementor-grid").append(data.jobs_html);
            $element.find(".btn-loading").fadeOut();
            if (data.hidden_pagination) {
              $element.find(".felan-pagination .pagination").html("");
            }
          }
        },
      });
    }
  };

  $(window).on("elementor/frontend/init", function () {
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/felan-jobs.default",
      FelanPaginationHandler
    );
  });
})(jQuery);
