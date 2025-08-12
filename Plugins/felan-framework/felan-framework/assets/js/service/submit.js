(function ($) {
    "use strict";

    var ajax_url = felan_submit_vars.ajax_url,
        service_dashboard = felan_submit_vars.service_dashboard,
        submit_form = $("#submit_service_form"),
        service_title_error = submit_form.data("titleerror"),
        service_des_error = submit_form.data("deserror"),
        service_language_error = submit_form.data("languageerror"),
        service_price_error = submit_form.data("priceerror"),
        service_delivery_time_error = submit_form.data("deliverytimeerror"),
        service_revisions_error = submit_form.data("revisionserror"),
        service_cat_error = submit_form.data("caterror");

    $(document).ready(function () {
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

        submit_form.find(".btn-more.service-fields").on("click", function () {
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
                submit_form.find('select[name="service_quantity"]').val()
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

        //Submit
        $.validator.setDefaults({ignore: ":hidden:not(select)"});
        submit_form.validate({
            ignore: [],
            rules: {
                service_title: {
                    required: true,
                },
                service_categories: {
                    required: true,
                },
                service_des: {
                    required: true,
                },
                service_language: {
                    required: true,
                },
                service_basic_revisions: {
                    required: true,
                },
                service_basic_price: {
                    required: true,
                },
                service_standard_price: {
                    required: true,
                },
                service_premium_price: {
                    required: true,
                },
            },

            messages: {
                service_title: service_title_error,
                service_des: service_des_error,
                service_language: service_language_error,
                service_categories: service_cat_error,
                service_basic_price: "Basic price is required.",
                service_standard_price: "Standard price is required.",
                service_premium_price: "Premium price is required.",
                service_basic_time: service_delivery_time_error,
                service_standard_time: service_delivery_time_error,
                service_premium_time: service_delivery_time_error,
                service_basic_revisions: service_revisions_error,
                service_standard_revisions: service_revisions_error,
                service_premium_revisions: service_revisions_error,
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

        //Pricing Quantity
        const selectQuantity = submit_form.find('select[name="service_quantity"]');
        const selectQuantityVal = submit_form
            .find('select[name="service_quantity"]')
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

        function updateValidationRules(service_quantity) {
            const basicPriceField = submit_form.find('input[name="service_basic_price"]');
            const standardPriceField = submit_form.find('input[name="service_standard_price"]');
            const premiumPriceField = submit_form.find('input[name="service_premium_price"]');

            if (service_quantity === 1 && basicPriceField.length) {
                basicPriceField.rules("add", {
                    required: true,
                    messages: {
                        required: "Basic price is required."
                    }
                });
                standardPriceField.rules("remove");
                premiumPriceField.rules("remove");
            }
            if (service_quantity === 2 && standardPriceField.length) {
                basicPriceField.rules("add", {
                    required: true,
                    messages: {
                        required: "Basic price is required."
                    }
                });
                standardPriceField.rules("add", {
                    required: true,
                    messages: {
                        required: "Standard price is required."
                    }
                });
                premiumPriceField.rules("remove");
            }
            if (service_quantity === 3 && premiumPriceField.length) {
                basicPriceField.rules("add", {
                    required: true,
                    messages: {
                        required: "Basic price is required."
                    }
                });
                standardPriceField.rules("add", {
                    required: true,
                    messages: {
                        required: "Standard price is required."
                    }
                });
                premiumPriceField.rules("add", {
                    required: true,
                    messages: {
                        required: "Premium price is required."
                    }
                });
            }
            if (submit_form.data('validator')) {
                submit_form.valid();
            }
        }

        selectQuantity.change(function () {
            const quantity = parseInt($(this).val());
            totalQuantity(quantity);
            updateValidationRules(quantity);
        });

        totalQuantity(selectQuantityVal);

        function ajax_load() {
            var service_form = submit_form.find('input[name="service_form"]').val(),
                service_id = submit_form.find('input[name="service_id"]').val(),
                service_title = submit_form.find('input[name="service_title"]').val(),
                service_categories = submit_form
                    .find('select[name="service_categories"]')
                    .val(),
                service_skills = submit_form
                    .find('select[name="service_skills"]')
                    .val(),
                service_des = tinymce.get("service_des").getContent(),
                service_language = submit_form
                    .find('select[name="service_language"]')
                    .val(),
                service_location = submit_form
                    .find('select[name="service_location"]')
                    .val(),
                service_map_address = submit_form
                    .find('input[name="felan_map_address"]')
                    .val(),
                service_map_location = submit_form
                    .find('input[name="felan_map_location"]')
                    .val(),
                service_latitude = submit_form
                    .find('input[name="felan_latitude"]')
                    .val(),
                service_longtitude = submit_form
                    .find('input[name="felan_longtitude"]')
                    .val(),
                service_thumbnail_url = submit_form
                    .find('input[name="service_thumbnail_url"]')
                    .val(),
                service_thumbnail_id = submit_form
                    .find('input[name="service_thumbnail_id"]')
                    .val(),
                felan_gallery_ids = submit_form
                    .find('input[name="felan_gallery_ids[]"]')
                    .map(function () {
                        return $(this).val();
                    })
                    .get(),
                service_video_url = submit_form
                    .find('input[name="service_video_url"]')
                    .val(),
                service_currency = submit_form
                    .find('select[name="service_currency"]')
                    .val(),
                service_time = submit_form.find('select[name="service_time"]').val(),
                service_quantity = submit_form
                    .find('select[name="service_quantity"]')
                    .val(),
                service_basic_des = submit_form
                    .find('textarea[name="service_basic_des"]')
                    .val(),
                service_standard_des = submit_form
                    .find('textarea[name="service_standard_des"]')
                    .val(),
                service_premium_des = submit_form
                    .find('textarea[name="service_premium_des"]')
                    .val(),
                service_basic_price = submit_form
                    .find('input[name="service_basic_price"]')
                    .val(),
                service_standard_price = submit_form
                    .find('input[name="service_standard_price"]')
                    .val(),
                service_premium_price = submit_form
                    .find('input[name="service_premium_price"]')
                    .val(),
                service_basic_time = submit_form
                    .find('input[name="service_basic_time"]')
                    .val(),
                service_standard_time = submit_form
                    .find('input[name="service_standard_time"]')
                    .val(),
                service_premium_time = submit_form
                    .find('input[name="service_premium_time"]')
                    .val(),
                service_basic_revisions = submit_form
                    .find('select[name="service_basic_revisions"]')
                    .val(),
                service_standard_revisions = submit_form
                    .find('select[name="service_standard_revisions"]')
                    .val(),
                service_premium_revisions = submit_form
                    .find('select[name="service_premium_revisions"]')
                    .val(),
                service_basic_number_revisions = submit_form
                    .find('input[name="service_basic_number_revisions"]')
                    .val(),
                service_standard_number_revisions = submit_form
                    .find('input[name="service_standard_number_revisions"]')
                    .val(),
                service_premium_number_revisions = submit_form
                    .find('input[name="service_premium_number_revisions"]')
                    .val(),
                service_package_title = submit_form
                    .find('input[name="service_package_title[]"]')
                    .map(function () {
                        return $(this).val();
                    })
                    .get(),
                service_package_basic = submit_form
                    .find('input[name="service_package_basic[]"]')
                    .map(function () {
                        return this.checked ? "basic" : "off";
                    })
                    .get(),
                service_package_standard = submit_form
                    .find('input[name="service_package_standard[]"]')
                    .map(function () {
                        return this.checked ? "standard" : "off";
                    })
                    .get(),
                service_package_premium = submit_form
                    .find('input[name="service_package_premium[]"]')
                    .map(function () {
                        return this.checked ? "premium" : "off";
                    })
                    .get(),
                service_custom_title = submit_form
                    .find('input[name="service_custom_title[]"]')
                    .map(function () {
                        return $(this).val();
                    })
                    .get(),
                service_custom_basic = submit_form
                    .find('input[name="service_custom_basic[]"]')
                    .map(function () {
                        return this.checked ? "basic" : "off";
                    })
                    .get(),
                service_custom_standard = submit_form
                    .find('input[name="service_custom_standard[]"]')
                    .map(function () {
                        return this.checked ? "standard" : "off";
                    })
                    .get(),
                service_custom_premium = submit_form
                    .find('input[name="service_custom_premium[]"]')
                    .map(function () {
                        return this.checked ? "premium" : "off";
                    })
                    .get(),
                service_addons_title = submit_form
                    .find('input[name="service_addons_title[]"]')
                    .map(function () {
                        return $(this).val();
                    })
                    .get(),
                service_addons_price = submit_form
                    .find('input[name="service_addons_price[]"]')
                    .map(function () {
                        return $(this).val();
                    })
                    .get(),
                service_addons_time = submit_form
                    .find('input[name="service_addons_time[]"]')
                    .map(function () {
                        return $(this).val();
                    })
                    .get(),
                service_faq_title = submit_form
                    .find('input[name="service_faq_title[]"]')
                    .map(function () {
                        return $(this).val();
                    })
                    .get(),
                service_faq_description = submit_form
                    .find('textarea[name="service_faq_description[]"]')
                    .map(function () {
                        return $(this).val();
                    })
                    .get();

            $.ajax({
                dataType: "json",
                url: ajax_url,
                data: {
                    action: "service_submit_ajax",
                    service_form: service_form,
                    service_id: service_id,
                    service_title: service_title,
                    service_categories: service_categories,
                    service_skills: service_skills,
                    service_des: service_des,
                    service_language: service_language,

                    service_currency: service_currency,
                    service_time: service_time,
                    service_quantity: service_quantity,
                    service_basic_des: service_basic_des,
                    service_standard_des: service_standard_des,
                    service_premium_des: service_premium_des,
                    service_basic_price: service_basic_price,
                    service_standard_price: service_standard_price,
                    service_premium_price: service_premium_price,
                    service_basic_time: service_basic_time,
                    service_standard_time: service_standard_time,
                    service_premium_time: service_premium_time,
                    service_basic_revisions: service_basic_revisions,
                    service_standard_revisions: service_standard_revisions,
                    service_premium_revisions: service_premium_revisions,
                    service_basic_number_revisions: service_basic_number_revisions,
                    service_standard_number_revisions: service_standard_number_revisions,
                    service_premium_number_revisions: service_premium_number_revisions,

                    service_location: service_location,
                    service_map_address: service_map_address,
                    service_map_location: service_map_location,
                    service_latitude: service_latitude,
                    service_longtitude: service_longtitude,

                    service_thumbnail_url: service_thumbnail_url,
                    service_thumbnail_id: service_thumbnail_id,
                    felan_gallery_ids: felan_gallery_ids,
                    service_video_url: service_video_url,

                    service_package_title: service_package_title,
                    service_package_basic: service_package_basic,
                    service_package_standard: service_package_standard,
                    service_package_premium: service_package_premium,

                    service_custom_title: service_custom_title,
                    service_custom_basic: service_custom_basic,
                    service_custom_standard: service_custom_standard,
                    service_custom_premium: service_custom_premium,

                    service_addons_title: service_addons_title,
                    service_addons_price: service_addons_price,
                    service_addons_time: service_addons_time,

                    service_faq_title: service_faq_title,
                    service_faq_description: service_faq_description,
                },
                beforeSend: function () {
                    $(".btn-submit-service .btn-loading").fadeIn();
                },
                success: function (data) {
                    $(".btn-submit-service .btn-loading").fadeOut();
                    if (data.success === true) {
                        window.location.href = service_dashboard;
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
					tinymce.get("service_des").setContent(response.message);
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
