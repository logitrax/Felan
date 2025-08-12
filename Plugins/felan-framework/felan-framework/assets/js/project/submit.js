(function ($) {
    "use strict";

    var ajax_url = felan_submit_vars.ajax_url,
        project_dashboard = felan_submit_vars.project_dashboard,
        submit_form = $("#submit_project_form"),
        project_title_error = submit_form.data("titleerror"),
        project_des_error = submit_form.data("deserror"),
        project_career_error = submit_form.data("careererror"),
        project_language_error = submit_form.data("languageerror"),
        project_cat_error = submit_form.data("caterror"),
        custom_field_project = felan_submit_vars.custom_field_project;

    $(document).ready(function () {
        //Budget
        function toggleFields(selectedValue) {
            var isFixed = selectedValue === 'fixed';
            var isHourly = selectedValue === 'hourly';

            $('#project_maximum_hours').closest('.form-group').toggle(isHourly);
            $('#project_value_rate').closest('.form-group').toggle(isFixed);
            $('#projects_rate').toggle(isFixed);
        }

        $('#select-budget-show').change(function () {
            toggleFields($(this).val());
        });
        toggleFields($('#select-budget-show').val());

        //More Section
        var $rowActive = submit_form.find(
            ".felan-addons-warpper > .row:first-child"
        );
        $rowActive.find(".group-title i").removeClass("delete-group");

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

            template.data("size", groupSiblings.size());
        });

        submit_form.find(".btn-more.project-fields").on("click", function () {
            var template = $(this).siblings("template");
            var html = $(template.html().trim());
            var row = $(this).closest(".felan-addons-warpper").find(".row");
            var innerContainer = $(this)
                .closest(".felan-addons-warpper")
                .find(".felan-addons-inner");
            var index = parseInt(row.length) + 1;

            html.find(".group-title h6 span").text(index);
            innerContainer.append(html);
            template.data("size", index);
        });

        submit_form.on("click", ".group-title", function () {
            if (!$(this).hasClass("up")) {
                $(this).addClass("up");
            } else {
                $(this).removeClass("up");
            }
        });

        //Package
        submit_form.find(".btn-more.package-fields").on("click", function () {
            var template = $(this).siblings("template");
            var html = $(template.html().trim());
            var innerContainer = $(this).closest(".table-responsive").find("tbody");
            const quantity = parseInt(
                submit_form.find('select[name="project_quantity"]').val()
            );

            if (quantity == 1) {
                html.find(".field-standard").hide();
                html.find(".field-premium").hide();
            } else if (quantity == 2) {
                html.find(".field-premium").hide();
            }

            innerContainer.append(html);
        });

        submit_form.on("click", ".table-package i.delete-group", function () {
            $(this).closest("tr").remove();
        });

        //Pricing Quantity
        const selectQuantity = submit_form.find('select[name="project_quantity"]');
        const selectQuantityVal = submit_form
            .find('select[name="project_quantity"]')
            .val();

        function totalQuantity(quantity) {
            if (quantity == 1) {
                submit_form.find(".field-standard").hide();
                submit_form.find(".field-premium").hide();
            }
            if (quantity == 2) {
                submit_form.find(".field-standard").show();
                submit_form.find(".field-premium").hide();
            }

            if (quantity == 3) {
                submit_form.find(".field-standard").show();
                submit_form.find(".field-premium").show();
            }
        }

        selectQuantity.change(function () {
            const quantity = parseInt($(this).val());
            totalQuantity(quantity);
        });
        totalQuantity(selectQuantityVal);

        //Custom Number Revisions
        const revisions = submit_form.find("tr.number-revisions .felan-select2");

        function handleRevisionSelection(selectedSelect, selectedOption) {
            if (selectedOption == "custom") {
                selectedSelect
                    .closest(".filed-revisions")
                    .find('input[type="number"]')
                    .show();
            } else {
                selectedSelect
                    .closest(".filed-revisions")
                    .find('input[type="number"]')
                    .hide();
            }
        }

        revisions.on("change", function () {
            handleRevisionSelection($(this), $(this).val());
        });

        submit_form.find(".number-revisions .filed-revisions").each(function () {
            const revisions = $(this).find(".felan-select2");
            const selectedOption = revisions.val();

            handleRevisionSelection(revisions, selectedOption);
        });

        //Min Max
        var timeout;
        $('#project_budget_minimum, #project_budget_maximum').on('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                var minPrice = parseFloat($('#project_budget_minimum').val());
                var maxPrice = parseFloat($('#project_budget_maximum').val());

                if (minPrice >= maxPrice && !isNaN(minPrice) && !isNaN(maxPrice)) {
                    alert('Minimum Price cannot be greater than Maximum Price.');
                    $('#project_budget_minimum').val(maxPrice - 1);
                }
            }, 1000);
        });

        //Submit
        $.validator.setDefaults({ignore: ":hidden:not(select)"});
        submit_form.validate({
            ignore: [],
            rules: {
                project_title: {
                    required: true,
                },
                project_categories: {
                    required: true,
                },
                project_des: {
                    required: true,
                },
                project_career: {
                    required: true,
                },
                project_language: {
                    required: true,
                },
            },
            messages: {
                project_title: project_title_error,
                project_des: project_des_error,
                project_career: project_career_error,
                project_language: project_language_error,
                project_categories: project_cat_error,
            },
            submitHandler: function (form) {
                ajax_load();
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

        function ajax_load() {
            var project_form = submit_form.find('input[name="project_form"]').val(),
                project_id = submit_form.find('input[name="project_id"]').val(),
                project_title = submit_form.find('input[name="project_title"]').val(),
                project_categories = submit_form
                    .find('select[name="project_categories"]')
                    .val(),
                project_skills = submit_form
                    .find('select[name="project_skills"]')
                    .val(),
                project_des = tinymce.get("project_des").getContent(),
                project_language = submit_form
                    .find('select[name="project_language"]')
                    .val(),
                project_career = submit_form
                    .find('select[name="project_career"]')
                    .val(),
                project_location = submit_form
                    .find('select[name="project_location"]')
                    .val(),
                project_map_address = submit_form
                    .find('input[name="felan_map_address"]')
                    .val(),
                project_map_location = submit_form
                    .find('input[name="felan_map_location"]')
                    .val(),
                project_latitude = submit_form
                    .find('input[name="felan_latitude"]')
                    .val(),
                project_longtitude = submit_form
                    .find('input[name="felan_longtitude"]')
                    .val(),
                project_thumbnail_url = submit_form
                    .find('input[name="project_thumbnail_url"]')
                    .val(),
                project_thumbnail_id = submit_form
                    .find('input[name="project_thumbnail_id"]')
                    .val(),
                felan_gallery_ids = submit_form
                    .find('input[name="felan_gallery_ids[]"]')
                    .map(function () {
                        return $(this).val();
                    })
                    .get(),
                project_video_url = submit_form
                    .find('input[name="project_video_url"]')
                    .val(),
                project_budget_show = submit_form
                    .find('select[name="project_budget_show"]')
                    .val(),
                project_budget_minimum = submit_form
                    .find('input[name="project_budget_minimum"]')
                    .val(),
                project_budget_maximum = submit_form
                    .find('input[name="project_budget_maximum"]')
                    .val(),
                project_value_rate = submit_form
                    .find('input[name="project_value_rate"]')
                    .val(),
                project_budget_rate = submit_form
                    .find('select[name="project_budget_rate"]')
                    .val(),
                project_maximum_hours = submit_form
                    .find('input[name="project_maximum_hours"]')
                    .val(),
                project_select_company = submit_form
                    .find('select[name="project_select_company"]')
                    .val(),
                project_faq_title = submit_form
                    .find('input[name="project_faq_title[]"]')
                    .map(function () {
                        return $(this).val();
                    })
                    .get(),
                project_faq_description = submit_form
                    .find('textarea[name="project_faq_description[]"]')
                    .map(function () {
                        return $(this).val();
                    })
                    .get(),
                company_title = submit_form.find('input[name="company_title"]').val(),
                company_email = submit_form.find('input[name="company_email"]').val(),
                company_avatar_url = submit_form.find('input[name="company_avatar_url"]').val(),
                company_avatar_id = submit_form.find('input[name="company_avatar_id"]').val();

            var additional = {};
            $("#project-submit-additional").each(function () {
                $.each(custom_field_project, function (index, value) {
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
                    action: "project_submit_ajax",
                    project_form: project_form,
                    project_id: project_id,
                    project_title: project_title,
                    project_categories: project_categories,
                    project_skills: project_skills,
                    project_des: project_des,
                    project_language: project_language,
                    project_career: project_career,

                    project_location: project_location,
                    project_map_address: project_map_address,
                    project_map_location: project_map_location,
                    project_latitude: project_latitude,
                    project_longtitude: project_longtitude,

                    project_thumbnail_url: project_thumbnail_url,
                    project_thumbnail_id: project_thumbnail_id,
                    felan_gallery_ids: felan_gallery_ids,
                    project_video_url: project_video_url,

                    project_budget_show: project_budget_show,
                    project_budget_minimum: project_budget_minimum,
                    project_budget_maximum: project_budget_maximum,
                    project_value_rate: project_value_rate,
                    project_budget_rate: project_budget_rate,
                    project_maximum_hours: project_maximum_hours,

                    project_select_company: project_select_company,

                    project_faq_title: project_faq_title,
                    project_faq_description: project_faq_description,

                    custom_field_project: additional,
                    company_title: company_title,
                    company_email: company_email,
                    company_avatar_url: company_avatar_url,
                    company_avatar_id: company_avatar_id,
                },
                beforeSend: function () {
                    $(".btn-submit-project .btn-loading").fadeIn();
                },
                success: function (data) {
                    $(".btn-submit-project .btn-loading").fadeOut();
                    if (data.success === true) {
                        window.location.href = project_dashboard;
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
					tinymce.get("project_des").setContent(response.message);
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
