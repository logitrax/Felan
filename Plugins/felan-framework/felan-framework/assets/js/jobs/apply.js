(function ($) {
  "use strict";
  $(document).ready(function () {
    var ajax_url = felan_template_vars.ajax_url,
      apply_saved = felan_template_vars.apply_saved,
      not_file = felan_template_vars.not_file,
      $form_popup = $(".form-popup-apply");

    $form_popup.each(function () {
      var $btn_submit = $("#" + $(".btn-submit-apply-jobs").attr("id"));
      var $btn_popup = $(".felan-button-apply." + $(this).attr("id"));
      var apply_form = $("#" + $(this).attr("id"));
      $btn_submit.on("click", function (e) {
        e.preventDefault();
        var $this = $(this),
          emaill = apply_form.find('input[name="apply_emaill"]').val(),
          message = apply_form.find('textarea[name="apply_message"]').val(),
          phone = apply_form.find('input[name="apply_phone"]').val(),
          freelancer_id = $btn_popup.data("freelancer_id"),
          jobs_id = $btn_popup.data("jobs_id"),
          cv_url = apply_form.find('input[name="jobs_cv_url"]').val(),
          type_apply = apply_form.find('input[name="type_apply"]').val(),
          freelancer_categories = apply_form
            .find('select[name="freelancer_categories"]')
            .val(),
          freelancer_dob = apply_form
            .find('input[name="freelancer_dob"]')
            .val(),
          freelancer_current_position = apply_form
            .find('input[name="freelancer_current_position"]')
            .val(),
          freelancer_age = apply_form
            .find('select[name="freelancer_age"]')
            .val(),
          freelancer_gender = apply_form
            .find('select[name="freelancer_gender"]')
            .val(),
          freelancer_languages = apply_form
            .find('select[name="freelancer_languages"]')
            .val(),
          freelancer_qualification = apply_form
            .find('select[name="freelancer_qualification"]')
            .val(),
          freelancer_yoe = apply_form
            .find('select[name="freelancer_yoe"]')
            .val();

        $.ajax({
          type: "POST",
          url: ajax_url,
          dataType: "json",
          data: {
            action: "jobs_add_to_apply",
            jobs_id: jobs_id,
            freelancer_id: freelancer_id,
            emaill: emaill,
            phone: phone,
            message: message,
            cv_url: cv_url,
            type_apply: type_apply,

            freelancer_current_position: freelancer_current_position,
            freelancer_categories: freelancer_categories,
            freelancer_dob: freelancer_dob,
            freelancer_age: freelancer_age,
            freelancer_gender: freelancer_gender,
            freelancer_languages: freelancer_languages,
            freelancer_qualification: freelancer_qualification,
            freelancer_yoe: freelancer_yoe,
          },
          beforeSend: function () {
            $this.find(".btn-loading").fadeIn();
          },
          success: function (data) {
            if (data.success == true) {
              apply_form.find(".message_error").addClass("true");
              apply_form.find(".message_error").text(data.message);
              $(".felan-button-apply[data-jobs_id =" + jobs_id + "]").html(
                apply_saved
              );
              location.reload();
            } else {
              $(".message_error").text(data.message);
            }
            $this.find(".btn-loading").fadeOut();
          },
        });
      });
    });
  });
})(jQuery);
