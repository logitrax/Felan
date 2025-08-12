var GLF = GLF || {};

(function ($) {
    "use strict";

    GLF.element = {
        init: function () {
            GLF.element.click_outside();
            GLF.element.payment_method();
            GLF.element.select2();
            GLF.element.sticky_element();
            GLF.element.click_to_demo();
            GLF.element.toggle_panel();
            GLF.element.toggle_payout();
            GLF.element.toggle_faq();
            GLF.element.toggle_social();
            GLF.element.toggle_content();
            GLF.element.nav_scroll();
            GLF.element.filter_toggle();
            GLF.element.slick_carousel();
            GLF.element.click_target_blank();
            GLF.element.back_top_top();
            GLF.element.search_canvas();
            GLF.element.switch_account();
            GLF.element.review_scroll_top();
            GLF.element.calculate_total_apply_project();
            GLF.element.create_new_company();
            GLF.element.login_user();
            GLF.element.jobs_salary();

            GLF.element.click_outside(".input-field", ".focus-result");
            GLF.element.click_outside(".location-field", ".focus-result");
            GLF.element.click_outside(".type-field", ".focus-result");

            $(".toggle-select").on("click", ".toggle-show", function () {
                $(this).closest(".toggle-select").find(".toggle-list").slideToggle();
            });
            GLF.element.click_outside(".toggle-select", ".toggle-list", "slide");
        },

        jobs_salary: function () {
            let timer;
            $('#jobs_salary_minimum, #jobs_salary_maximum').on('input', function () {
                clearTimeout(timer);

                timer = setTimeout(function () {
                    const min = parseFloat($('#jobs_salary_minimum').val());
                    const max = parseFloat($('#jobs_salary_maximum').val());

                    if (!isNaN(min) && !isNaN(max)) {
                        if (max <= min) {
                            alert('The Maximum value must be greater than the Minimum value.');
                            $('#jobs_salary_maximum').val('');
                        }
                    }
                }, 1000);
            });
        },

        login_user: function () {
            $("body").on('click', '.btn-login.btn-login-freelancer', function (e) {
                var $this = $(this);
                var popup_form = $('#popup-form .head-popup');
                var $html_noti = '<p class="notice"><i class="fal fa-exclamation-circle"></i>';

                $html_noti += 'You must Sign in as a Freelancer.';
                $html_noti += '</p>';

                if (!popup_form.find('.notice').length) {
                    popup_form.append($html_noti);
                }
            });
        },

        create_new_company: function () {
            var initialValue = $('select[name="jobs_select_company"],select[name="project_select_company"]').val();
            if (initialValue === "new_company") {
                $('.new-company-form').slideDown();
            } else {
                $('.new-company-form').slideUp();
            }

            $('select[name="jobs_select_company"],select[name="project_select_company"]').on('select2:select', function (e) {
                var selectedValue = $(this).val();
                if (selectedValue === "new_company") {
                    $('.new-company-form').slideDown();
                } else {
                    $('.new-company-form').slideUp();
                }
            });
        },

        calculate_total_apply_project: function () {
            $("body").on('click', '.btn-apply-project', function (e) {
                var maximum_time = $(this).data("maximum-time");
                var author_id = $(this).data("author-id");
                var post_current = $(this).data("post-current");
                var proposal_id = $(this).data("proposal-id");
                var info_price = $(this).data("info-price");
                var info_hours = $(this).data("info-hours");

                $('#form-apply-project').find('textarea[name="content_message"]').val('');
                $('#form-apply-project').find('.budget .number').val('');
                $('#form-apply-project').find('.price .text').val('');
                $('#form-apply-project #proposal_price').val('');
                $('#form-apply-project #proposal_time').val('');

                $('#form-apply-project #proposal_price, #form-apply-project #proposal_time').on('input', function () {
                    const $form_apply = $("#form-apply-project");
                    let proposalPrice = parseFloat($('#proposal_price').val()) || 0;
                    let proposalTime = parseFloat($('#proposal_time').val()) || 1;
                    let enableCommission = $('#enable_commission').val();
                    let commissionFee = enableCommission === '1' ? parseFloat($('#commission_fee').val()) || 0 : 0;
                    let amountAfterFee = proposalPrice - (proposalPrice * (commissionFee / 100));
                    let total = amountAfterFee * proposalTime;
                    let roundedProposalPrice = Math.round(proposalPrice * 100) / 100;
                    let roundedFee = Math.round(proposalPrice * (commissionFee / 100) * 100) / 100;
                    let roundedAmountAfterFee = Math.round(amountAfterFee * 100) / 100;
                    let roundedTotal = Math.round(total * 100) / 100;

                    $form_apply.find('.budget .number').text(roundedProposalPrice.toFixed(2));
                    $form_apply.find('.fee .number').text(roundedFee.toFixed(2));
                    $form_apply.find('.total-hours .number').text(roundedAmountAfterFee.toFixed(2));
                    $form_apply.find('.estimated-hours .number').text(proposalTime);
                    $form_apply.find('.total .number').text(roundedTotal.toFixed(2));
                });


                e.preventDefault();
                if ($(this).hasClass('fixed')) {
                    $('#form-apply-project #proposal_time').closest('.form-group').hide();
                    $('#form-apply-project li.total-hours').hide();
                    $('#form-apply-project li.estimated-hours').hide();
                    $('#form-apply-project #proposal_fixed_time').closest('.form-group').show();
                    $('#form-apply-project #proposal_rate').show();
                } else {
                    $('#form-apply-project #proposal_time').closest('.form-group').show();
                    $('#form-apply-project li.total-hours').show();
                    $('#form-apply-project li.estimated-hours').show();
                    $('#form-apply-project #proposal_fixed_time').closest('.form-group').hide();
                    $('#form-apply-project #proposal_rate').hide();
                }

                $('#form-apply-project .info-hours .number').html(info_hours);
                $('#form-apply-project .info-budget .number').html(info_price);
                $('input#project_maximum_time').val(maximum_time);
                $('input#project_author_id').val(author_id);
                $('input#project_post_current').val(post_current);
                $('input#proposal_id').val(proposal_id);
            });
        },

        review_scroll_top: function () {
            $('a.review-count').on('click', function(event) {
                event.preventDefault();
                var target = $(this).attr('href');

                $('html, body').animate({
                    scrollTop: $(target).offset().top
                }, 1000);
            });
        },

        switch_account: function () {
            $(".felan-switch-account").on('click', function() {
                var ajax_url = felan_template_vars.ajax_url;
                var new_role = $(this).data('new-role');
                var redirect = $(this).data('redirect');
                console.log(redirect);

                $.ajax({
                    url: ajax_url,
                    type: 'POST',
                    dataType: "json",
                    data: {
                        action: "felan_switch_account_ajax",
                        new_role: new_role,
                    },
                    beforeSend: function () {
                        $(".felan-switch-account .image i")
                            .addClass("fa-spin");
                    },
                    success: function(data) {
                        if (data.success === true) {
                            window.location.href = redirect;
                            $(".felan-switch-account .image i")
                                .removeClass("fa-spin");
                        }
                    },
                    error: function() {
                        alert('Failed to switch account.');
                    }
                });
            });
        },

        search_canvas: function () {
            $(".form-search-canvas select[name='post_type']").on("change", function (e) {
                e.preventDefault();
                var ajax_url = felan_template_vars.ajax_url;
                var post_type = $(this).val();

                $.ajax({
                    type: "post",
                    url: ajax_url,
                    dataType: "json",
                    data: {
                        action: "felan_canvas_search_ajax",
                        post_type: post_type,
                    },
                    beforeSend: function () {
                        $(".form-search-canvas .form-group.location")
                            .addClass("loading");
                    },
                    success: function (data) {
                        if (data.success === true) {
                            $(".form-search-canvas .form-group.location").html(data.taxonomy_html);
                            $(".form-search-canvas .form-group.location")
                                .removeClass("loading");
                            $('.felan-select2').select2();
                        }
                    },
                });
            });
        },

        scroll_to: function (element) {
            var offset = $(element).offset().top;
            $("html, body").animate(
                {
                    scrollTop: offset - 100,
                },
                500
            );
        },

        click_to_demo: function () {
            $(".menu a").on("click", function (e) {
                var id = $(this).attr("href");
                if (id == "#demo") {
                    e.preventDefault();
                    scroll_to(id);
                }
            });
        },

        counter_showing_number: function () {
            const containerWidth = $(".freelancer-skills").width();
            const labelHeight = $(".label-skills").outerHeight(true);
            const labelWidth = $(".label-skills").outerWidth(true);
            const totalItems = $(".label-skills").length;
            let itemsNotDisplayed = 0;

            //$('.freelancer-skills').css('height', labelHeight);

            $(".label-skills").each(function () {
                const itemPosition = $(this).position().left + $(this).outerWidth(true);
                if (itemPosition > containerWidth) {
                    itemsNotDisplayed++;
                    $(this).hide();
                }
            });

            if (itemsNotDisplayed > 0) {
                $(".counter").html(`<a id="moreButton">+ ${itemsNotDisplayed}</a>`);
            }

            $("#moreButton").click(function () {
                $(".label-skills:hidden").show();
                itemsNotDisplayed = 0;

                if (itemsNotDisplayed <= itemsPerRow) {
                    $("#moreButton").hide();
                } else {
                    $("#moreButton").html(`+ ${itemsNotDisplayed}`);
                }
            });
        },

        click_outside: function (element, child, type) {
            $(document).on("click", function (event) {
                var $this = $(element);
                if ($this !== event.target && !$this.has(event.target).length) {
                    if (type) {
                        if (child) {
                            $this.find(child).slideUp();
                        } else {
                            $this.slideUp();
                        }
                    } else {
                        if (child) {
                            $this.find(child).hide();
                        } else {
                            $this.hide();
                        }
                    }
                }
            });
        },

        payment_method: function () {
            $(".felan-payment-method-wrap .radio").on("click", function () {
                $(".felan-payment-method-wrap .radio").removeClass("active");
                $(this).addClass("active");
            });
        },

        select2: function () {
            var select2 = "";

            $(".felan-select2").each(function () {
                var option = $(this).find("option");
                if (theme_vars.enable_search_box_dropdown == 1) {
                    if (option.length > theme_vars.limit_search_box) {
                        select2 = $(this).select2();
                    } else {
                        select2 = $(this).select2({
                            minimumResultsForSearch: -1,
                        });
                    }
                } else {
                    select2 = $(this).select2({
                        minimumResultsForSearch: -1,
                    });
                }
            });

            if ($(".elementor-editor-active").length) {
                elementorFrontend.hooks.addAction(
                    "frontend/element_ready/widget",
                    function ($scope) {
                        $scope.find(".felan-select2").select2();
                    }
                );
            }

            $(".felan-select2.prefix-code").each(function () {
                var group = $(this).closest(".tel-group");
                var rendered = $(this).find("option:selected").val();
                group
                    .find(".select2-selection__rendered")
                    .removeClass(function (index, className) {
                        var classNames = className.split(" ");
                        return classNames
                            .filter(function (name) {
                                return name !== "select2-selection__rendered";
                            })
                            .join(" ");
                    })
                    .addClass(rendered);
            });

            var codeFirst = $(".prefix-code")
                .find("option:selected")
                .attr("data-dial-code");
            var valFirst = $(".tel-group").find('input[type="tel"]').val();
            if (valFirst == "") {
                $(".tel-group").find('input[type="tel"]').val(codeFirst);
            }
            $(".felan-select2.prefix-code").on("select2:select", function () {
                var group = $(this).closest(".tel-group");
                var rendered = $(this).find("option:selected").val();
                var code = $(this).find("option:selected").attr("data-dial-code");
                group
                    .find(".select2-selection__rendered")
                    .removeClass(function (index, className) {
                        var classNames = className.split(" ");
                        return classNames
                            .filter(function (name) {
                                return name !== "select2-selection__rendered";
                            })
                            .join(" ");
                    })
                    .addClass(rendered);
                group.find('input[type="tel"]').val(code);
            });

            $(".select2.select2-container").on("click", function () {
                var options = $(this).prev().find("option");
                options.each(function () {
                    var option_val = $(this).val();
                    var level = $(this).attr("data-level");
                    $('.select2-results li[id$="' + option_val + '"]').attr("data-level", level);
                });

                if ($(this).hasClass("select2-hidden-accessible")) {
                    $(this).select2('destroy').select2();
                }
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
        },

        sticky_element: function () {
            var offset = "";
            if ($(".ricetheme-sticky").length > 0) {
                offset = $(".ricetheme-sticky").offset().top;
            }
            var has_wpadminbar = $("#wpadminbar").length;
            var height_sticky = $(".ricetheme-sticky").height();
            var wpadminbar = 0;
            var lastScroll = 0;
            if (has_wpadminbar > 0) {
                wpadminbar = $("#wpadminbar").height();
                $(".ricetheme-sticky").addClass("has-wpadminbar");
            }

            var lastScrollTop = 0;
            $(window).scroll(function (event) {
                var st = $(this).scrollTop();
                if (st < lastScrollTop) {
                    $(".ricetheme-sticky").addClass("on");
                } else {
                    $(".ricetheme-sticky").removeClass("on");
                }

                if (st < height_sticky + wpadminbar) {
                    $(".ricetheme-sticky").removeClass("on");
                }
                lastScrollTop = st;
            });

            $(".block-archive-sidebar").each(function () {
                var _this = $(this);
                if (_this.hasClass("has-sticky")) {
                    _this.removeClass("has-sticky");
                    _this.parents(".widget-area-init").addClass("has-sticky");
                }
            });
        },

        toggle_panel: function () {
            $(".block-panel").on("click", ".block-tab", function () {
                var parent = $(this).closest(".block-panel");
                if (parent.hasClass("active")) {
                    parent.removeClass("active");
                    parent.find(".block-content").slideUp(300);
                } else {
                    $(".entry-property-element .block-panel").removeClass("active");
                    $(".entry-property-element .block-panel .block-content").slideUp(300);
                    parent.addClass("active");
                    parent.find(".block-content").slideDown(300);
                }
            });
        },

        toggle_payout: function () {
            $(".felan-payout-dashboard").on(
                "click",
                ".payout-item .title",
                function (e) {
                    e.preventDefault();
                    $(this).toggleClass("active");
                    $(this).parent().find(".content").slideToggle();
                }
            );
        },

        toggle_faq: function () {
            $(".service-faq-details").on("click", ".faq-header", function (e) {
                e.preventDefault();
                $(this).parent().find(".faq-content").slideToggle();
            });
            $(".project-faq-details").on("click", ".faq-header", function (e) {
                e.preventDefault();
                $(this).parent().find(".faq-content").slideToggle();
            });
        },

        toggle_social: function () {
            $(".toggle-social").on("click", ".btn-share", function (e) {
                e.preventDefault();
                $(this).parent().toggleClass("active");
                $(this).parent().find(".social-share").slideToggle(300);
            });
        },

        toggle_content: function () {
            var h_desc = $(
                ".single-jobs .jobs-content .inner-content .entry-visibility"
            ).height();
            if (h_desc > 130) {
                $(".single-jobs .jobs-content").addClass("on");
            }

            $(".show-more").on("click", function (e) {
                e.preventDefault();
                $(this).parents(".jobs-area").addClass("active");
            });

            $(".hide-all").on("click", function (e) {
                e.preventDefault();
                $(this).parents(".jobs-area").removeClass("active");
            });

            $(".open-toggle").on("click", function (e) {
                e.preventDefault();
                $(this).parent().toggleClass("active");
            });

            $(document).on("click", function (event) {
                var $this = $(".form-toggle");
                if ($this !== event.target && !$this.has(event.target).length) {
                    $this.removeClass("active");
                }
            });

            $("body").on("click", ".area-booking .minus", function (e) {
                var input = $(this)
                    .parents(".product-quantity")
                    .find(".input-text.qty");
                var name = $(this)
                    .parents(".product-quantity")
                    .find(".input-text.qty")
                    .attr("name");
                var val = parseInt(input.val()) - 1;
                if (input.val() > 0) input.attr("value", val);
                $(this)
                    .parents(".area-booking")
                    .find(".open-toggle")
                    .addClass("active");
                if (val > 0) {
                    $(this)
                        .parents(".area-booking")
                        .find("." + name + " span")
                        .text(parseInt(val));
                } else {
                    $(this)
                        .parents(".area-booking")
                        .find("." + name + " span")
                        .text(0);
                }
            });
        },

        nav_scroll: function () {
            $('.nav-scroll a[href^="#"]').on("click", function (event) {
                event.preventDefault();
                var target = $(this.getAttribute("href"));
                var has_wpadminbar = 0;
                if ($("#wpadminbar").height()) {
                    has_wpadminbar = $("#wpadminbar").height();
                }
                if (target.length) {
                    if ($(window).width() > 767) {
                        var top = target.offset().top - 15 - has_wpadminbar;
                    } else {
                        var top = target.offset().top - 15 - has_wpadminbar;
                    }
                    $("html, body").stop().animate(
                        {
                            scrollTop: top,
                        },
                        500
                    );
                }

                $(".nav-scroll li").removeClass("active");
                $(this).parent().addClass("active");
            });

            $(window).scroll(function () {
                var scrollDistance = $(window).scrollTop();

                // Assign active class to nav links while scolling
                $(".group-field").each(function (i) {
                    if ($(this).offset().top <= scrollDistance + 50) {
                        var href = $(this).attr("id"),
                            id = "#" + href;
                        $(".nav-scroll a").parent().removeClass("active");
                        $(".nav-scroll a").each(function () {
                            var attr = $(this).attr("href");
                            // For some browsers, `attr` is undefined; for others, `attr` is false. Check for both.
                            if (attr == id) {
                                // Element has this attribute
                                $(this).parent().addClass("active");
                            }
                        });
                    }
                });
            });
        },

        filter_toggle: function () {
            $(".btn-canvas-filter").on("click", function (event) {
                event.preventDefault();
                $("body").css("overflow", "hidden");
                $("body").addClass("open-popup");
                $(this).toggleClass("active");
                $(".archive-filter").toggleClass("open-canvas");
            });

            $(".archive-filter").on(
                "click",
                ".btn-close,.bg-overlay,.show-result .felan-button",
                function (e) {
                    e.preventDefault();
                    $("body").css("overflow", "inherit");
                    $("body").removeClass("open-popup");
                    $(this).parents(".archive-filter").removeClass("open-canvas");
                    $(".btn-canvas-filter").removeClass("active");
                }
            );
        },

        slick_carousel: function () {
            var rtl = false;
            if ($("body").hasClass("rtl")) {
                rtl = true;
            }
            $(".felan-slick-carousel").each(function () {
                var slider = $(this);
                var defaults = {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: true,
                    prevArrow:
                        '<div class="gl-prev slick-arrow"><i class="far fa-angle-left large"></i></div>',
                    nextArrow:
                        '<div class="gl-next slick-arrow"><i class="far fa-angle-right large"></i></div>',
                    dots: false,
                    fade: false,
                    infinite: false,
                    centerMode: false,
                    adaptiveHeight: true,
                    pauseOnFocus: true,
                    pauseOnHover: true,
                    swipe: true,
                    draggable: true,
                    rtl: rtl,
                    autoplay: false,
                    autoplaySpeed: 250,
                    speed: 250,
                };

                if (slider.hasClass("slick-nav")) {
                    defaults["prevArrow"] =
                        '<div class="gl-prev"><i class="far fa-angle-left large"></i></div>';
                    defaults["nextArrow"] =
                        '<div class="gl-next"><i class="far fa-angle-right large"></i></div>';
                }

                var config = $.extend({}, defaults, slider.data("slick"));
                // Initialize Slider
                slider.slick(config);
            });
        },

        click_target_blank: function () {
            var $layout = $(".post-type-archive .archive-layout .is-popup");
            $layout
                .find(".felan-link-item:not(.btn-single-settings)")
                .prop("target", "_blank");
        },

        back_top_top: function () {
            $(window).scroll(function () {
                if ($(window).scrollTop() > 500) {
                    $("#back-to-top").addClass("is-active");
                } else {
                    $("#back-to-top").removeClass("is-active");
                }
            });
            $("#back-to-top").on("click", function (e) {
                e.preventDefault();
                $("html, body").animate(
                    {
                        scrollTop: 0,
                    },
                    500
                );
            });
        },
    };

    GLF.onReady = {
        init: function () {
            GLF.element.init();
        },
    };

    GLF.onLoad = {
        init: function () {
        },
    };

    GLF.onResize = {
        init: function () {
            // Resize Window
        },
    };

    $(document).ready(GLF.onReady.init);
    $(window).resize(GLF.onResize.init);
    $(window).load(GLF.onLoad.init);
})(jQuery);
