var ISF = ISF || {};

(function ($) {
  "use strict";

  var ajax_url = felan_template_vars.ajax_url;

  /******************* Refresh after live preview *********************/

  var Widget_Reload_Carousel = function ($scope, $) {
    var carousel_elem = $scope.find(".elementor-carousel");
    carousel_elem.each(function () {
      var settings = carousel_elem.data("slider_options");
      if (settings["isslick"] == "false") {
        alert(settings["isslick"]);
        carousel_elem.unslick();
      } else {
        carousel_elem.not(".slick-initialized").slick(settings);
      }
    });
  };

  var Widget_Job_Alerts = function ($scope, $) {
    var form = $scope.find(".job-alerts-form");
    form.on("submit", function (e) {
      e.preventDefault();
      var name = $(this).find('input[name="name"]').val();
      var email = $(this).find('input[name="email"]').val();
      var skills = $(this).find('select[name="skills"]').val();
      var location = $(this).find('select[name="location"]').val();
      var category = $(this).find('select[name="category"]').val();
      var experience = $(this).find('select[name="experience"]').val();
      var types = $(this).find('select[name="types"]').val();
      var frequency = $(this).find('select[name="frequency"]').val();

      $.ajax({
        type: "post",
        url: ajax_url,
        dataType: "json",
        data: {
          name: name,
          email: email,
          skills: skills,
          location: location,
          category: category,
          experience: experience,
          types: types,
          frequency: frequency,
          action: "felan_job_alerts_action",
        },
        beforeSend: function () {
          form
            .find(".notice")
            .text("")
            .removeClass("warning")
            .removeClass("success");
          form.find(".btn-loading").fadeIn();
          $(".job-alerts-notice").remove();
        },
        success: function (data) {
          form.find(".btn-loading").fadeOut();
          form
            .find(".notice")
            .removeClass("warning")
            .removeClass("success")
            .addClass(data.class);
          form.find(".notice").text("").text(data.message);
        },
        error: function () {
          form.find(".btn-loading").fadeOut();
        },
      });
    });
  };

  $(window).on("elementor/frontend/init", function () {
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/felan-companies.default",
      Widget_Reload_Carousel
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/felan-jobs.default",
      Widget_Reload_Carousel
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/felan-service.default",
      Widget_Reload_Carousel
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/felan-project.default",
      Widget_Reload_Carousel
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/felan-freelancers.default",
      Widget_Reload_Carousel
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/felan-service-category.default",
      Widget_Reload_Carousel
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/felan-jobs-category.default",
      Widget_Reload_Carousel
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/felan-category-carousel.default",
      Widget_Reload_Carousel
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/felan-companies-category.default",
      Widget_Reload_Carousel
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/felan-jobs-location.default",
      Widget_Reload_Carousel
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/felan-freelancer-box.default",
      Widget_Reload_Carousel
    );
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/felan-job-alerts.default",
      Widget_Job_Alerts
    );
  });
})(jQuery);
