"use strict";

jQuery(document).ready(function ($) {
  /**
   *  Declarations go here
   */

  var submit_form = $("#freelancer-profile-form");
  var profile_strength = $(".freelancer-profile-strength");
  var profile_dashboard = $(".freelancer-profile-dashboard");
  var ajax_url = felan_freelancer_vars.ajax_url;
  var text_present = felan_freelancer_vars.text_present;
  var custom_field_freelancer = felan_freelancer_vars.custom_field_freelancer;
  var date_format = felan_template_vars.date_format;

  var tabTemplate = profile_dashboard.find(".tab-item.repeater");
  if (tabTemplate.length > 0) {
    $.each(tabTemplate, function () {
      var val = $(this).find("a").attr("href").replace("#tab-", "");
      var $index = 1,
        tabID = submit_form.find("#tab-" + val),
        btn_more = tabID.find(".btn-more.profile-fields"),
        item = tabID.find(".felan-freelancer-warpper .row").length,
        template = $(tabID.find("template").html().trim());

      if (item == 0) {
        template.find(".group-title h6 span").text($index);
        template
          .find(".project-upload")
          .attr("id", "project-uploader_" + $index);
        template
          .find(".project-uploaded-list")
          .attr("id", "project-uploaded-list_" + $index);
        template
          .find(".errors-log")
          .attr("id", "felan_project_errors_log_" + $index);
        template
          .find(".uploaded-container")
          .attr("id", "uploaded-container_" + $index);
        template.find(".uploaded-main").attr("id", "uploader-main_" + $index);
        template.insertBefore(btn_more);
      }
    });
  }

  var $rowActive = submit_form.find(
    ".felan-freelancer-warpper > .row:first-child"
  );
  $rowActive.find(".group-title i").removeClass("delete-group");
  $rowActive.find("input").addClass("point-mark");
  $rowActive.find("textarea").addClass("point-mark");
  $rowActive.find(".project-uploaded-list input").removeClass("point-mark");
  $rowActive.find("#project-uploaded-list_1").addClass("point-mark");

  var $profile = $("#freelancer-profile");
  var $fieldPoint = submit_form.find(".point-mark");

  function tabMarkPoint() {
    var textTab = [
      "info",
      "awards",
      "projects",
      "skills",
      "experience",
      "education",
    ];
    $.each(textTab, function (index, val) {
      var tabID = submit_form.find("#tab-" + val),
        checkStrength = profile_strength.find("#profile-check-" + val),
        textHasCheck = checkStrength.data("has-check"),
        textNotCheck = checkStrength.data("not-check"),
        textCheck = checkStrength.find("span"),
        tabLength = tabID.find(".point-mark").length,
        tabLengthActive = tabID.find(".point-mark.point-active").length;

      if (tabLength == tabLengthActive) {
        checkStrength.addClass("check");
        textCheck.text(textHasCheck);
      } else {
        checkStrength.removeClass("check");
        textCheck.text(textNotCheck);
      }
    });
  }

  function markPoint() {
    $fieldPoint.each(function () {
      if ($(this).val() !== "") {
        $(this).addClass("point-active");
      } else {
        $(this).removeClass("point-active");
      }
    });

    var pointActive = submit_form.find(".point-mark.point-active").length - 1;
    var pointAll = submit_form.find(".point-mark").length - 1;

    var mediaGallery = submit_form.find(
      "#felan_gallery_thumbs .media-thumb-wrap"
    ).length;
    var uploadCv = submit_form
      .find("#felan_drop_cv")
      .attr("data-attachment-id");
    var avatar = submit_form
      .find('input[name="author_avatar_image_url"]')
      .val();
    var coverImage = submit_form.find(
      'input[name="freelancer_cover_image_url"]'
    ).length;
    var select2 = submit_form
      .find(".select2-selection__choice")
      .attr("data-select2-id");
    var select2Multiple = $(".select2-multiple").find(
      "ul.select2-selection__rendered li"
    ).length;
    var projectUploaded = submit_form.find("#project-uploaded-list_1");

    if (mediaGallery > 0) {
      pointActive = pointActive + 1;
    } else {
      pointActive = pointActive;
    }

    if (avatar !== "") {
      pointActive = pointActive + 1;
    } else {
      pointActive = pointActive;
    }

    if (coverImage > 0) {
      pointActive = pointActive + 1;
    } else {
      pointActive = pointActive;
    }

    if (uploadCv !== "") {
      pointActive = pointActive + 1;
    } else {
      pointActive = pointActive;
    }

    projectUploaded.addClass("point-mark");
    if (projectUploaded.find(".media-thumb").length > 0) {
      projectUploaded.addClass("point-active");
      pointActive = pointActive + 1;
    } else {
      projectUploaded.removeClass("point-active");
      pointActive = pointActive;
    }

    pointAll = pointAll + 5;

    if (typeof select2 === "undefined") {
      pointActive = pointActive;
      $(".felan-select2").removeClass("point-active");
    } else {
      pointActive = pointActive + 1;
      $(".felan-select2").addClass("point-active");
    }

    if (select2Multiple > 1) {
      pointActive = pointActive + 1;
      $(".select2-multiple").addClass("point-active");
    } else {
      pointActive = pointActive;
      $(".select2-multiple").removeClass("point-active");
    }

    if (pointAll > 0) {
      var percent = Math.round((pointActive / pointAll) * 100);
    } else {
      var percent = 0;
    }

    $profile.find(".profile-strength").css("--pct", percent);
    $profile.find(".profile-strength h1 span:first-child").text(percent);
    submit_form.attr("data-pointactive", pointActive);
    submit_form.attr("data-pointall", pointAll);
    submit_form.find('input[name="freelancer_profile_strength"]').val(percent);

    $(".profile-strength.left-sidebar").css("--pct", percent);
    $(".profile-strength.left-sidebar")
      .find(".title")
      .find("span:nth-child(2)")
      .text(percent);

    tabMarkPoint();
  }

  markPoint();
  $fieldPoint.change(function () {
    markPoint();
  });

  if (typeof tinyMCE !== "undefined") {
    if ($("#wp-freelancer_des-wrap").hasClass("tmce-active")) {
      tinyMCE.get("freelancer_des").on("change", function () {
        var value = tinyMCE
          .get("freelancer_des")
          .getContent({ format: "text" })
          .trim().length;
        $("#wp-freelancer_des-wrap").addClass("point-mark");
        if (value > 0) {
          $("#wp-freelancer_des-wrap").addClass("point-active");
        } else {
          $("#wp-freelancer_des-wrap").removeClass("point-active");
        }
        markPoint();
      });
    }
  }

  submit_form.closest("#wrapper").css("overflow", "inherit");

  //  Edit Freelancer Profile
  function ajax_submit() {
    var freelancer_social_data = {};
    $(".freelancer-social-input").each(function () {
      var fieldName = $(this).attr("name");
      var fieldValue = $(this).val();
      freelancer_social_data[fieldName] = fieldValue;
    });

    var freelancer_id = submit_form.find('input[name="freelancer_id"]').val(),
      freelancer_first_name = submit_form
        .find('input[name="freelancer_first_name"]')
        .val(),
      freelancer_last_name = submit_form
        .find('input[name="freelancer_last_name"]')
        .val(),
      freelancer_email = submit_form
        .find('input[name="freelancer_email"]')
        .val(),
      freelancer_phone = submit_form
        .find('input[name="freelancer_phone"]')
        .val(),
      freelancer_phone_code = submit_form
        .find('select[name="prefix_code"]')
        .val(),
      freelancer_current_position = submit_form
        .find('input[name="freelancer_current_position"]')
        .val(),
      freelancer_categories = submit_form
        .find('select[name="freelancer_categories"]')
        .val(),
      freelancer_des = tinymce.get("freelancer_des").getContent(),
      freelancer_dob = submit_form.find('input[name="freelancer_dob"]').val(),
      freelancer_age = submit_form.find('select[name="freelancer_age"]').val(),
      freelancer_gender = submit_form
        .find('select[name="freelancer_gender"]')
        .val(),
      freelancer_languages = submit_form
        .find('select[name="freelancer_languages"]')
        .val(),
      freelancer_qualification = submit_form
        .find('select[name="freelancer_qualification"]')
        .val(),
      freelancer_yoe = submit_form.find('select[name="freelancer_yoe"]').val(),
      freelancer_salary_type = submit_form
        .find('select[name="freelancer_salary_type"]')
        .val(),
      freelancer_offer_salary = submit_form
        .find('input[name="freelancer_offer_salary"]')
        .val(),
      freelancer_currency_type = submit_form
        .find('select[name="freelancer_currency_type"]')
        .val(),
      freelancer_education_title = submit_form
        .find('input[name="freelancer_education_title[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      freelancer_education_level = submit_form
        .find('input[name="freelancer_education_level[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      freelancer_education_from = submit_form
        .find('input[name="freelancer_education_from[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      freelancer_education_to = submit_form
        .find('input[name="freelancer_education_to[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      freelancer_education_description = submit_form
        .find('textarea[name="freelancer_education_description[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      freelancer_experience_job = submit_form
        .find('input[name="freelancer_experience_job[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      freelancer_experience_company = submit_form
        .find('input[name="freelancer_experience_company[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      freelancer_experience_from = submit_form
        .find('input[name="freelancer_experience_from[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      freelancer_experience_to = submit_form
        .find('input[name="freelancer_experience_to[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      freelancer_experience_description = submit_form
        .find('textarea[name="freelancer_experience_description[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      freelancer_skills = submit_form
        .find('select[name="freelancer_skills"]')
        .val(),
      freelancer_project_title = submit_form
        .find('input[name="freelancer_project_title[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      freelancer_project_link = submit_form
        .find('input[name="freelancer_project_link[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      freelancer_project_description = submit_form
        .find('textarea[name="freelancer_project_description[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      freelancer_project_image_id = submit_form
        .find('input[name="freelancer_project_image_id[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      freelancer_project_image_url = submit_form
        .find('input[name="freelancer_project_image_url[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      freelancer_award_title = submit_form
        .find('input[name="freelancer_award_title[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      freelancer_award_date = submit_form
        .find('input[name="freelancer_award_date[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      freelancer_award_description = submit_form
        .find('textarea[name="freelancer_award_description[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      freelancer_cover_image_id = submit_form
        .find('input[name="freelancer_cover_image_id"]')
        .val(),
      freelancer_cover_image_url = submit_form
        .find('input[name="freelancer_cover_image_url"]')
        .val(),
      author_avatar_image_id = submit_form
        .find('input[name="author_avatar_image_id"]')
        .val(),
      author_avatar_image_url = submit_form
        .find('input[name="author_avatar_image_url"]')
        .val(),
      freelancer_video_url = submit_form
        .find('input[name="freelancer_video_url"]')
        .val(),
      freelancer_resume = submit_form
        .find("#felan_drop_cv")
        .attr("data-attachment-id"),
      freelancer_twitter = submit_form
        .find('input[name="freelancer_twitter"]')
        .val(),
      freelancer_linkedin = submit_form
        .find('input[name="freelancer_linkedin"]')
        .val(),
      freelancer_facebook = submit_form
        .find('input[name="freelancer_facebook"]')
        .val(),
      freelancer_instagram = submit_form
        .find('input[name="freelancer_instagram"]')
        .val(),
      social_data = social_data,
      freelancer_social_name = submit_form
        .find('input[name="freelancer_social_name[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      freelancer_social_url = submit_form
        .find('input[name="freelancer_social_url[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      freelancer_location = submit_form
        .find('select[name="freelancer_location"]')
        .val(),
      freelancer_map_address = submit_form
        .find('input[name="felan_map_address"]')
        .val(),
      freelancer_map_location = submit_form
        .find('input[name="felan_map_location"]')
        .val(),
      freelancer_latitude = submit_form
        .find('input[name="felan_latitude"]')
        .val(),
      freelancer_longtitude = submit_form
        .find('input[name="felan_longtitude"]')
        .val(),
      felan_gallery_ids = submit_form
        .find('input[name="felan_gallery_ids[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      freelancer_profile_strength = submit_form
        .find('input[name="freelancer_profile_strength"]')
        .val();

    var additional = {};
    submit_form.find(".block-from").each(function () {
      $.each(custom_field_freelancer, function (index, value) {
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
            val = $(".drop-cv-" + value.id).data('attachment-id');
        }
        additional[value.id] = val;
      });
    });

    $.ajax({
      dataType: "json",
      url: ajax_url,
      data: {
        action: "freelancer_submit_ajax",
        freelancer_id: freelancer_id,

        freelancer_first_name: freelancer_first_name,
        freelancer_last_name: freelancer_last_name,
        freelancer_email: freelancer_email,
        freelancer_phone: freelancer_phone,
        freelancer_phone_code: freelancer_phone_code,
        freelancer_current_position: freelancer_current_position,
        freelancer_categories: freelancer_categories,
        freelancer_des: freelancer_des,
        freelancer_dob: freelancer_dob,
        freelancer_age: freelancer_age,
        freelancer_gender: freelancer_gender,
        freelancer_languages: freelancer_languages,
        freelancer_qualification: freelancer_qualification,
        freelancer_yoe: freelancer_yoe,
        freelancer_offer_salary: freelancer_offer_salary,
        freelancer_salary_type: freelancer_salary_type,
        freelancer_currency_type: freelancer_currency_type,

        freelancer_education_title: freelancer_education_title,
        freelancer_education_level: freelancer_education_level,
        freelancer_education_from: freelancer_education_from,
        freelancer_education_to: freelancer_education_to,
        freelancer_education_description: freelancer_education_description,

        freelancer_experience_job: freelancer_experience_job,
        freelancer_experience_company: freelancer_experience_company,
        freelancer_experience_from: freelancer_experience_from,
        freelancer_experience_to: freelancer_experience_to,
        freelancer_experience_description: freelancer_experience_description,

        freelancer_skills: freelancer_skills,

        freelancer_project_title: freelancer_project_title,
        freelancer_project_link: freelancer_project_link,
        freelancer_project_description: freelancer_project_description,
        freelancer_project_image_id: freelancer_project_image_id,
        freelancer_project_image_url: freelancer_project_image_url,

        freelancer_award_title: freelancer_award_title,
        freelancer_award_date: freelancer_award_date,
        freelancer_award_description: freelancer_award_description,

        freelancer_cover_image_id: freelancer_cover_image_id,
        freelancer_cover_image_url: freelancer_cover_image_url,
        author_avatar_image_id: author_avatar_image_id,
        author_avatar_image_url: author_avatar_image_url,
        felan_gallery_ids: felan_gallery_ids,
        freelancer_video_url: freelancer_video_url,

        freelancer_resume: freelancer_resume,

        freelancer_twitter: freelancer_twitter,
        freelancer_linkedin: freelancer_linkedin,
        freelancer_facebook: freelancer_facebook,
        freelancer_instagram: freelancer_instagram,
        freelancer_social_data: freelancer_social_data,
        freelancer_social_name: freelancer_social_name,
        freelancer_social_url: freelancer_social_url,

        freelancer_location: freelancer_location,
        freelancer_map_address: freelancer_map_address,
        freelancer_map_location: freelancer_map_location,
        freelancer_latitude: freelancer_latitude,
        freelancer_longtitude: freelancer_longtitude,

        freelancer_profile_strength: freelancer_profile_strength,

        custom_field_freelancer: additional,
      },
      beforeSend: function () {
        $(".btn-update-profile .btn-loading").fadeIn();
      },
      success: function (data) {
        $(".btn-update-profile .btn-loading").fadeOut();
        if (data.success === true) {
          window.location.reload();
        }
      },
    });
  }

  // Extend Date object with a function to add days
  //
  Date.prototype.addDays = function (days) {
    this.setDate(this.getDate() + parseInt(days));
    return this;
  };

  // Stored current tab in Local Storage
  //
  function setTabLocalStorage(value) {
    var current_page = $("#main div:first-child").attr("id");

    localStorage.setItem(
      "session_felan_tab_dashboard" + "_" + current_page,
      value
    );
  }

  // Retrive stored tab in Local Storage
  //
  function getTabLocalStorage() {
    var current_page = $("#main div:first-child").attr("id");

    return localStorage.getItem(
      "session_felan_tab_dashboard" + "_" + current_page
    );
  }

  // Present
  function present_education_to(row) {
    return function () {
      if ($(this).is(":checked")) {
        row.find(".present-to .datepicker").remove();
        if (row.find('.present-to input[type="text"]').length < 1) {
          row
            .find(".present-to")
            .append(
              '<input class="text-present" disabled type="text" name="freelancer_education_to[]" value="' +
                text_present +
                '">'
            );
        }
      } else {
        row.find(".present-to .text-present").remove();
        if (row.find('.present-to input[type="text"]').length < 1) {
          row
            .find(".present-to")
            .append(
              '<input type="text" class="datepicker" placeholder="' +
                date_format +
                '" name="freelancer_education_to[]">'
            );
        }
      }
    };
  }

  function present_experience_to(row) {
    return function () {
      if ($(this).is(":checked")) {
        row.find(".present-to .datepicker").remove();
        if (row.find('.present-to input[type="text"]').length < 1) {
          row
            .find(".present-to")
            .append(
              '<input class="text-present" disabled type="text" name="freelancer_experience_to[]" value="' +
                text_present +
                '">'
            );
        }
      } else {
        row.find(".present-to .text-present").remove();
        if (row.find('.present-to input[type="text"]').length < 1) {
          row
            .find(".present-to")
            .append(
              '<input type="text" class="datepicker" placeholder="' +
                date_format +
                '" name="freelancer_experience_to[]" value="">'
            );
        }
      }
    };
  }

  // Freelancer Profile Switch tab
  //
  function switchToTab(obj) {
    $(".tab-dashboard ul li").removeClass("active");
    $(obj).addClass("active");
    var id = $(obj).find("a").attr("href");
    $(".tab-info").hide();
    $(id).show();
    $(".tab-info").removeClass("active");
    $(id).addClass("active");

    if ($("#tab-education.active .row").length > 0) {
      $("#tab-education.active .row").each(function () {
        var row = $(this);
        row
          .find('input[type="checkbox"]')
          .on("change", present_education_to(row));
      });
    }

    if ($("#tab-experience.active .row").length > 0) {
      $("#tab-experience.active .row").each(function () {
        var row = $(this);
        row
          .find('input[type="checkbox"]')
          .on("change", present_experience_to(row));
      });
    }
  }

  // Onready check and load the stored tab
  //
  function showSavedTab() {
    var tabDefault = $("#freelancer-profile .tab-list li:first-child");
    var idStored = getTabLocalStorage();

    if (idStored !== null) {
      tabDefault = $(".tab-list").find(`li a[href="${idStored}"]`).parent();
    }

    switchToTab(tabDefault);
  }

  // Oncheck check and load the stored tab
  //
  function removeAllChecked() {
    var checkedBoxes = $('input[name="freelancer_cover_image_id"]:checked');
    checkedBoxes.prop("checked", false);
  }

  // Make Validator to check array Input fields
  // https://github.com/jquery-validation/jquery-validation/issues/1226
  //
  $.validator.prototype.checkForm = function () {
    this.prepareForm();
    for (
      var i = 0, elements = (this.currentElements = this.elements());
      elements[i];
      i++
    ) {
      if (
        this.findByName(elements[i].name).length != undefined &&
        this.findByName(elements[i].name).length > 1
      ) {
        for (
          var cnt = 0;
          cnt < this.findByName(elements[i].name).length;
          cnt++
        ) {
          this.check(this.findByName(elements[i].name)[cnt]);
        }
      } else {
        this.check(elements[i]);
      }
    }
    return this.valid();
  };

  // Set Attribute and Property for Element
  //
  function setAttrAndProp(input, attr = {}, prop = {}) {
    if (!(input instanceof jQuery)) {
      return false;
    }

    $.each(attr, function (attrName, attrVal) {
      input.attr(attrName, attrVal);
    });

    $.each(prop, function (propName, propVal) {
      input.attr(propName, propVal);
    });
  }

  // Find Input Date in the same group
  //
  function findRelatedInputDate(input, nameToFind) {
    if (!(input instanceof jQuery)) {
      return false;
    }
    var relatedInput = input
      .closest(".row")
      .find(`input[name="${nameToFind}"]`);
    return relatedInput;
  }

  function setRelatedInputDateTo(input) {
    if (!(input instanceof jQuery)) {
      return false;
    }

    var nameWithFrom = input.attr("name");
    var nameWithTo = nameWithFrom.replace("from", "to");

    var relatedInput = findRelatedInputDate(input, nameWithTo);

    if (relatedInput == false) {
      return false;
    }

    var fromDate = new Date(input.val());
    if (fromDate !== "") {
      var minDate = fromDate.addDays(1).toISOString().split("T")[0];
    }

    var attrs = {
      min: minDate,
    };

    var props = {
      required: true,
    };

    setAttrAndProp(relatedInput, attrs, props);
  }

  function setRelatedInputDateFrom(input) {
    if (!(input instanceof jQuery)) {
      return false;
    }

    var nameWithTo = input.attr("name");
    var nameWithFrom = nameWithTo.replace("to", "from");

    var relatedInput = findRelatedInputDate(input, nameWithFrom);

    if (relatedInput == false) {
      return false;
    }

    var toDate = new Date(input.val());
    var maxDate = toDate.addDays(-1).toISOString().split("T")[0];

    var attrs = {
      max: maxDate,
    };

    var props = {
      required: true,
    };

    setAttrAndProp(relatedInput, attrs, props);
  }

  // Validate Single Input
  //
  function validateSingleInput(input) {
    if (!(input instanceof jQuery)) {
      return false;
    }

    submit_form.validate().element(input);

    if (input.hasClass("error")) {
      input.focus();
      return false;
    }

    return true;
  }

  // Ajax Delete attachment
  function ajaxDeleteAttachment(clickedEl, $type, $none) {
    var $this = $(clickedEl),
      icon_delete = $this,
      thumbnail = $this.closest(".media-thumb-wrap"),
      freelancer_id = $this.data("freelancer-id"),
      attachment_id = $this.data("attachment-id");

    icon_delete.html('<i class="fal fa-spinner fa-spin large"></i>');

    $.ajax({
      type: "post",
      url: ajax_url,
      dataType: "json",
      data: {
        action: "remove_freelancer_attachment_ajax",
        freelancer_id: freelancer_id,
        attachment_id: attachment_id,
        type: $type,
        removeNonce: $none,
      },
      success: function (response) {
        if (response.success) {
          thumbnail.remove();
        }
        icon_delete.html('<i class="fal fa-spinner fa-spin large"></i>');
      },
      error: function () {
        icon_delete.html('<i class="far fa-trash-alt large"></i>');
      },
    });
  }

  //  Event: Remove an add-more Group
  //
  submit_form.on("click", "i.delete-group", function () {
    var groupToRemove = $(this).closest(".group-title").closest(".row");
    var groupSiblings = groupToRemove.siblings(".row");
    var template = groupToRemove.siblings("template");

    groupToRemove.remove();

    $.each(groupSiblings, function renumberGroups(index) {
      $(this)
        .find(".group-title h6 span")
        .text(index + 1);
    });

    // Update total number of groups
    template.data("size", groupSiblings.size());
  });

  //  Event: Hide/Show A Group
  //
  submit_form.on("click", ".group-title", function () {
    if (!$(this).hasClass("up")) {
      $(this).addClass("up");
    } else {
      $(this).removeClass("up");
    }
  });

  // Validate Form and Submit
  //
  $.validator.setDefaults({ ignore: ":hidden:not(select)" });

  submit_form.validate({
    ignore: [],
    rules: {},
    messages: {},

    submitHandler: function (form) {
      ajax_submit();
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


  var submit_verify = $("#freelancer-verify-form");
  submit_verify.validate({
    ignore: [],
    rules: {},
    messages: {},

    submitHandler: function (form) {
      ajax_verify_submit();
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


  //  User verify ajax
  function ajax_verify_submit() {
	const verify_type = document.getElementById('verify-type').value;
    var user_id = submit_verify.find('input[name="user_id"]').val(),
		front_image_id = submit_verify.find('input[name="author_avatar_image_id"]').val(),
		front_image_url = submit_verify.find('input[name="author_avatar_image_url"]').val(),
      	back_image_id = submit_verify.find('input[name="freelancer_cover_image_id"]').val(),
      	back_image_url = submit_verify.find('input[name="freelancer_cover_image_url"]').val();

    $.ajax({
      dataType: "json",
      url: ajax_url,
      data: {
        action: "user_verify_ajax",
        user_id: user_id,
        front_image_id: front_image_id,
        front_image_url: front_image_url,
        back_image_id: back_image_id,
        back_image_url: back_image_url,
		verify_type: verify_type
      },
      beforeSend: function () {
        $(".btn-update-profile .btn-loading").fadeIn();
      },
      success: function (data) {
        $(".btn-update-profile .btn-loading").fadeOut();
        if (data.success === true) {
          window.location.reload();
        }
      },
    });
  }

  // Event: onblur DateInput "from", set DateInput "to" minDate
  //

  submit_form.on("blur", 'input[type="date"][name*="from"]', function () {
    var isValid = validateSingleInput($(this));

    if (!isValid) {
      return false;
    }

    setRelatedInputDateTo($(this));
  });

  // Event: onblur DateInput "from", set DateInput "to" minDate
  //
  submit_form.on("blur", 'input[type="date"][name*="to"]', function () {
    var isValid = validateSingleInput($(this));

    if (!isValid) {
      return false;
    }
  });

  function formatDateForDisplay(date) {
    const options = { month: "long", day: "numeric", year: "numeric" };
    return new Date(date).toLocaleDateString("en-US", options);
  }

  function formatDatPpicker() {
    if (date_format === "F j, Y") {
      $(".datepicker").datepicker({
        dateFormat: "mm/dd/yy",
        onSelect: function (dateText, inst) {
          const formattedDate = formatDateForDisplay(dateText);
          $(this).val(formattedDate);
        },
      });
    } else if (date_format === "Y-m-d") {
      $(".datepicker").datepicker({
        dateFormat: "yy-mm-dd",
      });
    } else if (date_format === "d/m/Y") {
      $(".datepicker").datepicker({
        dateFormat: "d/m/yy",
      });
    } else {
      $(".datepicker").datepicker({
        dateFormat: "mm/dd/yy",
      });
    }
  }

  formatDatPpicker();
  showSavedTab();

  // Event: onclick tab Profile Form
  $("#freelancer-profile .tab-list li").click(function () {
    setTabLocalStorage($(this).find("a").attr("href"));
    switchToTab(this);
  });

  //More
  $(".btn-more.profile-fields").on("click", function () {
    var template = $(this).siblings("template");
    var html = $(template.html().trim());
    var index = parseInt(template.data("size")) + 1;

    html.find(".group-title h6 span").text(index);
    html.find(".project-upload").attr("id", "project-uploader_" + index);
    html
      .find(".project-uploaded-list")
      .attr("id", "project-uploaded-list_" + index);
    html.find(".errors-log").attr("id", "felan_project_errors_log_" + index);
    html.find(".uploaded-container").attr("id", "uploaded-container_" + index);
    html.find(".uploaded-main").attr("id", "uploader-main_" + index);

    html.insertBefore($(this));
    tab_projects_each(index);
    formatDatPpicker();

    template.data("size", index);

    $("#tab-education .row").each(function () {
      var row = $(this);
      row
        .find('input[type="checkbox"]')
        .on("change", present_education_to(row));
    });

    $("#tab-experience .row").each(function () {
      var row = $(this);
      row
        .find('input[type="checkbox"]')
        .on("change", present_experience_to(row));
    });
  });

  // Event: Oncheck Cover Image
  //
  $('input[name="freelancer_cover_image_id"]').click(function () {
    var is_checked = false;
    if ($(this).is(":checked")) {
      is_checked = true;
    }

    removeAllChecked();

    $(this).prop("checked", is_checked);
  });

  // Event: OnClick Project Upload
  //
  function tab_projects_each($index) {
    $("#tab-projects .row").each(function ($index) {
      var $index = $index + 1;

      var upload_nonce = $("#tab-projects").data("nonce");
      var cv_title = $("#tab-projects").data("title");
      var cv_type = $("#tab-projects").data("type");
      var cv_size = $("#tab-projects").data("file-size");
      var uploader = "uploader_" + $index;

      uploader = new plupload.Uploader({
        browse_button: "uploader-main_" + $index,
        file_data_name: "freelancer_upload_file",
        container: "uploaded-container_" + $index,
        drop_element: "uploaded-container_" + $index,
        max_file_count: 1,
        url:
          ajax_url +
          "?action=upload_freelancer_attachment_ajax&nonce=" +
          upload_nonce,
        filters: {
          mime_types: [
            {
              title: cv_title,
              extensions: cv_type,
            },
          ],
          max_file_size: cv_size,
          prevent_duplicates: true,
        },
      });

      uploader.init();

      function configProjectUploader(uploader) {
        var options = {
          filters: {
            mime_types: [
              {
                title: cv_title,
                extensions: cv_type,
              },
            ],
            max_file_size: cv_size,
            prevent_duplicates: true,
          },
        };

        uploader.setOption(options);

        uploader.bind("FilesAdded", function (up, files) {
          var freelancerThumb = "";
          plupload.each(files, function (file) {
            freelancerThumb +=
              '<li class="card-preview-item" id="holder-' + file.id + '"></li>';
          });

          document.getElementById(
            "project-uploaded-list_" + $index
          ).innerHTML += freelancerThumb;
          up.refresh();
          uploader.start();
        });

        uploader.bind("UploadProgress", function (up, file) {
          var project_btn = "project-uploader_" + $index;
          document.getElementById(project_btn).innerHTML =
            '<span><i class="fal fa-spinner fa-spin large"></i></span>';
        });

        uploader.bind("Error", function (up, err) {
          document.getElementById(
            "felan_project_errors_log_" + $index
          ).innerHTML += "Error: " + err.message + "<br/>";
        });

        uploader.bind("FileUploaded", function (up, file, ajax_response) {
          var response = $.parseJSON(ajax_response.response);
          if (response.success) {
            var $html = $($("#project-single-image").html().trim());
            var $project_uploaded = $("#project-uploaded-list_" + $index);
            var $project_btn = $("#project-uploader_" + $index);

            $html.find("img").attr("src", response.url);
            $html.find("a").attr("data-attachment-id", response.attachment_id);

            $project_uploaded
              .find("input.freelancer_project_image_id")
              .val(response.attachment_id);
            $project_uploaded
              .find("input.freelancer_project_image_url")
              .val(response.url);

            $("#holder-" + file.id).html($html);
            $("#freelancer-profile-form").find(".point-mark").change();
            $project_btn.text("");
          }
        });
      }

      function triggerUploaderButton(uploader) {
        $(uploader.settings.browse_button).trigger("click");
      }

      var icon_delete =
        "#project-uploaded-list_" + $index + " .icon-project-delete";
      $("body").on("click", icon_delete, function (e) {
        e.preventDefault();
        var $this = $(this);
        var $none = $this.closest("#tab-projects").data("nonce");
        var $type = $this.closest("#tab-projects").data("type");
        var $project_uploaded = $this.closest(".project-uploaded-list");
        var $project_btn = $project_uploaded.siblings(".project-upload");
        var $text_uploaded = $this.closest("#tab-projects").data("uploaded");

        $project_uploaded
          .find('input[name="freelancer_project_image_id[]"]')
          .val("");
        $project_uploaded
          .find('input[name="freelancer_project_image_url[]"]')
          .val("");
        ajaxDeleteAttachment($(this), $type, $none);
        $("#freelancer-profile-form").find(".point-mark").change();

        $project_btn.html($text_uploaded);
      });

      $(this)
        .find(".browse.project-upload")
        .on("click", function () {
          configProjectUploader(uploader);

          triggerUploaderButton(uploader);
        });
    });
  }

  tab_projects_each();
});
