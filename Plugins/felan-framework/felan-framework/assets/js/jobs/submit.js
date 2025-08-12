(function ($) {
  "use strict";

  var submit_form = $("#submit_jobs_form"),
    jobs_title_error = submit_form.data("titleerror"),
    jobs_des_error = submit_form.data("deserror"),
    jobs_cat_error = submit_form.data("caterror"),
    jobs_type_error = submit_form.data("typeerror"),
    jobs_skills_error = submit_form.data("skillserror"),
    jobs_rate_error = submit_form.data("rateerror"),
    jobs_minimum_price_error = submit_form.data("minimumpriceerror"),
    jobs_maximum_price_error = submit_form.data("maximumpriceerror");

  var ajax_url = felan_submit_vars.ajax_url,
    jobs_dashboard = felan_submit_vars.jobs_dashboard,
    custom_field_jobs = felan_submit_vars.custom_field_jobs;

  $(document).ready(function () {
    $.validator.setDefaults({ ignore: ":hidden:not(select)" });

    $.validator.addMethod("isCityName", function (value, element) {
      var apiUsername = "ductrung"; // Replace with your GeoNames username
      var isValid = false;

      if (value == "") {
        isValid = true;
        return isValid;
      } else {
        // Make a request to the GeoNames API
        var url =
          "https://secure.geonames.org/searchJSON?q=" +
          encodeURIComponent(value) +
          "&maxRows=1&username=" +
          apiUsername;

        $.ajax({
          url: url,
          method: "GET",
          dataType: "json",
          async: false,
          success: function (data) {
            // Check if the API response contains a city
            if (data.geonames && data.geonames.length > 0) {
              var type = data.geonames[0].fclName;
              if (type.indexOf("city") != -1) {
                isValid = true;
              }
            }
          },
          error: function () {
            console.log("An error occurred while checking the city name.");
          },
        });

        return isValid;
      }
    });

    submit_form.validate({
      ignore: [],
      rules: {
        jobs_title: {
          required: true,
        },
        jobs_categories: {
          required: true,
        },
        jobs_type: {
          required: true,
        },
        jobs_skills: {
          required: true,
        },
        jobs_des: {
          required: true,
        },
        jobs_new_location: {
          isCityName: true,
        },
        jobs_salary_minimum: {
          required: function () {
            return $('select[name="jobs_salary_show"]').val() === "range";
          },
        },
        jobs_salary_maximum: {
          required: function () {
            return $('select[name="jobs_salary_show"]').val() === "range";
          },
        },
        jobs_salary_rate: {
          required: function () {
            const salaryShowValue = $('select[name="jobs_salary_show"]').val();
            return ["starting_amount", "range", "maximum_amount"].includes(
              salaryShowValue
            );
          },
        },
        jobs_minimum_price: {
          required: function () {
            return (
              $('select[name="jobs_salary_show"]').val() === "starting_amount"
            );
          },
        },
        jobs_maximum_price: {
          required: function () {
            return (
              $('select[name="jobs_salary_show"]').val() === "maximum_amount"
            );
          },
        },
      },

      messages: {
        jobs_title: jobs_title_error,
        jobs_des: jobs_des_error,
        jobs_categories: jobs_cat_error,
        jobs_type: jobs_type_error,
        jobs_skills: jobs_skills_error,
        jobs_new_location: {
          isCityName: "Please enter a valid city name.",
        },
        jobs_salary_rate: jobs_rate_error,
        jobs_salary_minimum: jobs_minimum_price_error,
        jobs_salary_maximum: jobs_maximum_price_error,
        jobs_minimum_price: jobs_minimum_price_error,
        jobs_maximum_price: jobs_maximum_price_error,
      },
      submitHandler: function (form) {
        var submitButtonName = $(this.submitButton).attr("name");
        ajax_load(submitButtonName);
      },
      errorPlacement: function (error, element) {
        error.insertAfter(element);
      },
      invalidHandler: function () {
        if ($(".error:visible").length > 0) {
          $("html, body").animate(
            {
              scrollTop: $(".error:visible").offset().top - 100,
            },
            500
          );
        }
      },
    });

    function ajax_load(submit_button) {
      var jobs_form = submit_form.find('input[name="jobs_form"]').val(),
        jobs_action = submit_form.find('input[name="jobs_action"]').val(),
        jobs_id = submit_form.find('input[name="jobs_id"]').val(),
        jobs_title = submit_form.find('input[name="jobs_title"]').val(),
        jobs_categories = submit_form
          .find('select[name="jobs_categories"]')
          .val(),
        jobs_new_categories = submit_form
          .find('input[name="jobs_new_categories"]')
          .val(),
        jobs_type = submit_form.find('select[name="jobs_type"]').val(),
        jobs_skills = submit_form.find('select[name="jobs_skills"]').val(),
        jobs_des = tinymce.get("jobs_des").getContent(),
        jobs_career = submit_form.find('select[name="jobs_career"]').val(),
        jobs_experience = submit_form
          .find('select[name="jobs_experience"]')
          .val(),
        jobs_qualification = submit_form
          .find('select[name="jobs_qualification"]')
          .val(),
        jobs_quantity = submit_form.find('select[name="jobs_quantity"]').val(),
        jobs_gender = submit_form.find('select[name="jobs_gender"]').val(),
        jobs_days_closing = submit_form
          .find('input[name="jobs_days_closing"]')
          .val(),
        jobs_salary_show = submit_form
          .find('select[name="jobs_salary_show"]')
          .val(),
        jobs_currency_type = submit_form
          .find('select[name="jobs_currency_type"]')
          .val(),
        jobs_salary_minimum = submit_form
          .find('input[name="jobs_salary_minimum"]')
          .val(),
        jobs_salary_maximum = submit_form
          .find('input[name="jobs_salary_maximum"]')
          .val(),
        jobs_salary_rate = submit_form
          .find('select[name="jobs_salary_rate"]')
          .val(),
        jobs_minimum_price = submit_form
          .find('input[name="jobs_minimum_price"]')
          .val(),
        jobs_maximum_price = submit_form
          .find('input[name="jobs_maximum_price"]')
          .val(),
        jobs_select_apply = submit_form
          .find('select[name="jobs_select_apply"]')
          .val(),
        jobs_apply_email = submit_form
          .find('input[name="jobs_apply_email"]')
          .val(),
        jobs_apply_external = submit_form
          .find('input[name="jobs_apply_external"]')
          .val(),
        jobs_apply_call_to = submit_form
          .find('input[name="jobs_apply_call_to"]')
          .val(),
        jobs_select_company = submit_form
          .find('select[name="jobs_select_company"]')
          .val(),
        jobs_location = submit_form.find('select[name="jobs_location"]').val(),
        jobs_new_location = submit_form
          .find('input[name="jobs_new_location"]')
          .val(),
        jobs_thumbnail_url = submit_form
          .find('input[name="jobs_thumbnail_url"]')
          .val(),
        jobs_thumbnail_id = submit_form
          .find('input[name="jobs_thumbnail_id"]')
          .val(),
        felan_gallery_ids = submit_form
          .find('input[name="felan_gallery_ids[]"]')
          .map(function () {
            return $(this).val();
          })
          .get(),
        jobs_video_url = submit_form.find('input[name="jobs_video_url"]').val(),
        jobs_map_address = submit_form
          .find('input[name="felan_map_address"]')
          .val(),
        jobs_map_location = submit_form
          .find('input[name="felan_map_location"]')
          .val(),
        jobs_latitude = submit_form.find('input[name="felan_latitude"]').val(),
        jobs_longtitude = submit_form
          .find('input[name="felan_longtitude"]')
          .val(),
        company_title = submit_form.find('input[name="company_title"]').val(),
        company_email = submit_form.find('input[name="company_email"]').val(),
        company_avatar_url = submit_form
          .find('input[name="company_avatar_url"]')
          .val(),
        company_avatar_id = submit_form
          .find('input[name="company_avatar_id"]')
          .val();

      var additional = {};
      $("#jobs-submit-additional").each(function () {
        $.each(custom_field_jobs, function (index, value) {
          var val = $(".form-control[name=" + value.id + "]").val();
          if (value.type == "radio") {
            val = $("input[name=" + value.id + "]:checked").val();
          }
          if (value.type == "checkbox_list") {
            var arr_checkbox = [];
            $('input[name="' + value.id + '[]"]:checked').each(function () {
              arr_checkbox.push($(this).val());
            });
            val = arr_checkbox;
          }
          if (value.type == "image") {
            val = $("input#custom_image_id_" + value.id).val();
          }
          if (value.type == "file") {
            val = $(".drop-cv-" + value.id).data("attachment-id");
          }
          additional[value.id] = val;
        });
      });

      $.ajax({
        type: "POST",
        dataType: "json",
        url: ajax_url,
        data: {
          action: "jobs_submit_ajax",
          jobs_form: jobs_form,
          jobs_action: jobs_action,
          jobs_id: jobs_id,
          jobs_title: jobs_title,
          jobs_categories: jobs_categories,
          jobs_new_categories: jobs_new_categories,
          jobs_type: jobs_type,
          jobs_skills: jobs_skills,
          jobs_des: jobs_des,
          jobs_career: jobs_career,
          jobs_experience: jobs_experience,
          jobs_qualification: jobs_qualification,
          jobs_quantity: jobs_quantity,
          jobs_gender: jobs_gender,
          jobs_days_closing: jobs_days_closing,

          jobs_salary_show: jobs_salary_show,
          jobs_currency_type: jobs_currency_type,
          jobs_salary_minimum: jobs_salary_minimum,
          jobs_salary_maximum: jobs_salary_maximum,
          jobs_salary_rate: jobs_salary_rate,
          jobs_minimum_price: jobs_minimum_price,
          jobs_maximum_price: jobs_maximum_price,

          jobs_select_apply: jobs_select_apply,
          jobs_apply_email: jobs_apply_email,
          jobs_apply_external: jobs_apply_external,
          jobs_apply_call_to: jobs_apply_call_to,

          jobs_select_company: jobs_select_company,
          jobs_location: jobs_location,
          jobs_new_location: jobs_new_location,
          jobs_thumbnail_url: jobs_thumbnail_url,
          jobs_thumbnail_id: jobs_thumbnail_id,
          felan_gallery_ids: felan_gallery_ids,
          jobs_video_url: jobs_video_url,
          custom_field_jobs: additional,

          jobs_map_address: jobs_map_address,
          jobs_map_location: jobs_map_location,
          jobs_latitude: jobs_latitude,
          jobs_longtitude: jobs_longtitude,

          submit_button: submit_button,
          company_title: company_title,
          company_email: company_email,
          company_avatar_url: company_avatar_url,
          company_avatar_id: company_avatar_id,
        },
        beforeSend: function () {
          if (submit_button == "submit_jobs") {
            $(".btn-submit-jobs .btn-loading").fadeIn();
          } else {
            $(".btn-submit-draft .btn-loading").fadeIn();
          }
        },
        success: function (data) {
          if (submit_button == "submit_jobs") {
            $(".btn-submit-jobs .btn-loading").fadeOut();
            if (data.success === true) {
              window.location.href = jobs_dashboard;
            }
          } else {
            $(".btn-submit-draft .btn-loading").fadeOut();
          }
        },
      });
    }

    $(".ai-helper").on("click", function (e) {
      e.preventDefault();
      var _this = $(this),
        popup_name = _this.attr("data-popup");
      $("#" + popup_name).addClass("open");
    });

    if ($(window).width() > 767) {
      $(".generate-content").each(function () {
        var left = $(this).find(".left"),
          right = $(this).find(".right"),
          left_height = left.outerHeight();

        right.css("height", left_height);
      });
    }

    $(".ai-generate").on("submit", function (e) {
      e.preventDefault();

      var _this = $(this),
        wrap = $(this).closest(".ai-popup"),
        wrap_inner = wrap.find(".inner-popup"),
        keywords = _this.find('textarea[name="ai_prompt"]').val(),
        tone = _this.find('select[name="ai_tone"] option:selected').text(),
        language = _this
          .find('select[name="ai_language"] option:selected')
          .text();

      $.ajax({
        url: ajax_url,
        type: "POST",
        data: {
          action: "auto_description_generate",
          keywords: keywords,
          tone: tone,
          language: language,
        },
        beforeSend: function () {
          _this.find(".btn-loading").fadeIn();
          wrap.find(".suggestion").text("");
          _this.find(".field-notice").removeClass("error");
          _this.find(".field-notice p").text("");
          wrap.find(".generate-content").removeClass("has-suggestion");
        },
        success: function (response) {
          var response = $.parseJSON(response);
          _this.find(".btn-loading").fadeOut();
          if (response.success) {
            _this
              .find(".field-submit button .text")
              .text(felan_submit_vars.regenerate);
            wrap.find(".generate-content").addClass("has-suggestion");
            wrap.find(".suggestion").html(response.message);
            if ($(window).width() < 768) {
              $(".ai-popup .inner-popup").animate(
                {
                  scrollTop:
                    wrap.find(".suggestion").offset().top -
                    wrap_inner.offset().top -
                    40,
                },
                500
              );
            }
            $(".keep-generate").on("click", function (e) {
              e.preventDefault();
              tinymce.get("jobs_des").setContent(response.message);
              wrap.find(".generate-content").removeClass("has-suggestion");
              $(".ai-generate")[0].reset();
              _this.closest(".popup").removeClass("open");
              _this
                .find(".field-submit button .text")
                .text(felan_submit_vars.generate);
            });
          } else {
            _this.find(".field-notice p").text(response.message);
            _this.find(".field-notice").addClass("error");
          }
        },
      });
    });
  });
})(jQuery);
