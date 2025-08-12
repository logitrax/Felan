var DASHBOARD = DASHBOARD || {};
(function ($) {
  "use strict";

  DASHBOARD = {
    init: function () {
      this.closebtn();
      this.opennav();
      this.icon_setting();
      this.scrollToElement();
      this.scroll_top();
      this.tabs();
      this.tabs_active();
      this.tabs_change_heading();
      this.tabs_popup();
      this.form_popup();
      this.svg();
      this.search_canvas();
      this.select_tabs();
      this.about_jobs();
      this.service_conver_price();
      this.toggle_password();
      this.about_company();
      this.check_company();
      this.service_total_price();
      this.service_submit_tab();
    },

    service_submit_tab: function () {
      const serviceSubmit = $("ul.service-submit-tab");
      serviceSubmit.find(".tab-item a").on("click", function () {
        var href = $(this).attr("href");
        if (href === "#tab-pricing") {
          $("#submit_service_form .submit-service-header").css(
            "max-width",
            "100%"
          );
        } else {
          $("#submit_service_form .submit-service-header").css(
            "max-width",
            "700px"
          );
        }
      });
    },

    service_total_price: function () {
      const packageAddons = $(".package-addons"),
        packageTotal = $(".package-bottom").find(".price .number"),
        packageDeliveryTime = $(".package-bottom").find(".delivery-time .time"),
        startDeliveryTime = packageDeliveryTime.data("delivery-time"),
        startPrice = $(".package-content .price").attr("data-start-price");

      packageAddons.find('input[type="checkbox"]').click(function () {
        let priceTotal = parseInt(startPrice);
        let deliveryTime = startDeliveryTime;

        packageAddons.find("input:checkbox:checked").each(function () {
          priceTotal += parseInt($(this).val());
          deliveryTime += parseInt($(this).data("delivery-time"));
        });

        packageTotal.text(priceTotal);
        packageDeliveryTime.text(deliveryTime);
        $(".package-service").find('input[name="total_price"]').val(priceTotal);
        $("#felan_stripe_service_addons").find('input[name="total_price"]').val(priceTotal);
        $(".package-service")
          .find('input[name="package_time"]')
          .val(deliveryTime);
      });
    },

    svg: function () {
      var $navItems = $(".list-nav-dashboard .nav-item");
      var $activeNavItem = $(".list-nav-dashboard .nav-item.active");
      var $colorAccent = $(".felan-nav-dashboard").data("accent");

      $navItems.each(function () {
        $(this)
          .find("object.felan-svg")
          .contents()
          .find("path")
          .attr("stroke", "white");
      });

      $activeNavItem
        .find("object.felan-svg")
        .contents()
        .find("path")
        .attr("stroke", $colorAccent);

      $navItems.on("mouseover", function () {
        $(this)
          .find("object.felan-svg")
          .contents()
          .find("path")
          .attr("stroke", $colorAccent);
      });

      $navItems.on("mouseout", function () {
        $(this)
          .find("object.felan-svg")
          .contents()
          .find("path")
          .attr("stroke", "white");
        $activeNavItem
          .find("object.felan-svg")
          .contents()
          .find("path")
          .attr("stroke", $colorAccent);
      });
    },

    closebtn: function () {
      var $nav = $(".nav-dashboard-wapper");
      var $close = $nav.find(".closebtn");
      $close.click(function () {
        $(".nav-dashboard-wapper").toggleClass("close");
        if ($close.find("i").hasClass("fas fa-arrow-right")) {
          $close.find("i").removeAttr("class", "fas fa-arrow-right");
          $(this).find("i").attr("class", "fas fa-arrow-left");
          $nav.css({ width: "260px", "overflow-y": "auto" });
          $nav.find(".nav-profile-strength").css("display", "block");
          $nav.find(".nav-item a").removeClass("tooltip");

          if ($("body").hasClass("rtl")) {
            $(".page-dashboard #felan-content-dashboard").css(
              "padding-right",
              "290px"
            );
          } else {
            $(".page-dashboard #felan-content-dashboard").css(
              "padding-left",
              "290px"
            );
          }
        } else {
          $close.find("i").removeAttr("class", "fas fa-arrow-left");
          $(this).find("i").attr("class", "fas fa-arrow-right");
          $nav.css({ width: "55px", "overflow-y": "unset" });
          $nav.find(".nav-profile-strength").css("display", "none");
          $nav.find(".nav-item a").addClass("tooltip");

          if ($("body").hasClass("rtl")) {
            $nav.find(".list-nav-dashboard").css("margin-right", "0");
            $(".page-dashboard #felan-content-dashboard").css(
              "padding-right",
              "85px"
            );
          } else {
            $nav.find(".list-nav-dashboard").css("margin-left", "0");
            $(".page-dashboard #felan-content-dashboard").css(
              "padding-left",
              "85px"
            );
          }
        }
      });
    },

    search_canvas: function () {
      var search_id =
        "#" + $(".form-search-canvas .jobs-search-canvas").attr("id");
      var available = $(search_id).data("key");

      if (window.matchMedia("(max-width: 1199PX)").matches) {
        $(search_id)
          .autocomplete({
            source: available,
            minLength: 0,
            autoFocus: false,
            focus: true,
          })
          .focus(function () {
            $(this).data("uiAutocomplete").search($(this).val());
          });
      }
    },

    opennav: function () {
      var dashboard = $(".nav-dashboard-inner");
      dashboard.find(".icon-nav-mobie").click(function () {
        dashboard.toggleClass("open-nav");
        if (dashboard.hasClass("open-nav")) {
          $(this).css("left", "260px");
        } else {
          $(this).css("left", "0");
        }
      });
      dashboard.find(".bg-overlay").click(function () {
        dashboard.removeClass("open-nav");
        dashboard.find(".icon-nav-mobie").css("left", "0");
      });
    },

    icon_setting: function () {
      var icon_setting = ".action-setting .icon-setting";
      $("body").on("click", icon_setting, function (e) {
        var action = $(this).closest(".action-setting");
        var dropdown = $(action).find(".action-dropdown");
        e.preventDefault();
        $(dropdown).toggleClass("show");
        $(".action-setting .action-dropdown").not(dropdown).removeClass("show");
        $(action).css("z-index", "2");
        $(".action-setting").not(action).css("z-index", "1");
      });
    },

    scrollToElement: function () {
      var ele = $("#company-review-details");
      var hash = location.hash.replace("#", "");
      $(window).load(function () {
        if (hash == "company-review-details") {
          $("html, body").animate({ scrollTop: ele.offset().top }, 1000);
        }
      });
    },

    scroll_top: function () {
      var scrollHeader = 190;
      var submit = $(".felan-submit-header");
      $(window).scroll(function () {
        var scroll = getCurrentScroll();
        if (scroll >= scrollHeader) {
          submit.addClass("scroll");
        } else {
          submit.removeClass("scroll");
        }
      });

      function getCurrentScroll() {
        return window.pageYOffset;
      }
    },

    toggle_password: function () {
      $(".felan-toggle-password").click(function () {
        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $($(this).attr("toggle"));
        if (input.attr("type") == "password") {
          input.attr("type", "text");
        } else {
          input.attr("type", "password");
        }
      });
    },

    tabs: function () {
      function tab_dashboard(obj) {
        $(".tab-dashboard ul li").removeClass("active");
        $(obj).addClass("active");
        var id = $(obj).find("a").attr("href");
        $(".tab-info").hide();
        $(id).show();
      }

      $(".tab-list li").click(function () {
        tab_dashboard(this);
        return false;
      });

      tab_dashboard($(".tab-list li:first-child"));
    },

    tabs_active: function () {
      function tab_active(obj) {
        $(".tab-dashboard-active ul li").removeClass("active");
        $(".tab-info-active").removeClass("active");
        $(obj).addClass("active");
        var id = $(obj).find("a").attr("href");
        $(".tab-info-active").hide();
        $(id).show();
      }

      $(".tab-list-active li").click(function () {
        tab_active(this);
        return false;
      });
    },

    tabs_change_heading: function () {
      var $my_freelancer = $(".my-freelancer");
      $my_freelancer.find(".tab-list li").click(function () {
        var $tab_active = $(this).find("a");
        var $text = $tab_active.data("text");

        $my_freelancer.find(".entry-title h4").text($text);
      });
    },

    tabs_popup: function () {
      function tab_popup(obj) {
        $(".tab-popup-warpper ul li").removeClass("active");
        $(obj).addClass("active");
        var id = $(obj).find("a").attr("href");
        $(id).show();
      }

      $(".tab-list-popup li").click(function () {
        $(".tab-popup").hide();
        tab_popup(this);
        return false;
      });
    },

    form_popup: function () {
      $(".form-popup").each(function () {
        if ($(this).is("#form-invite-popup")) {
          var form_popup = "#form-invite-popup";
          var btn_popup = "#btn-invite-freelancer";
        } else if ($(this).is("#form-messages-popup")) {
          var form_popup = "#form-messages-popup";
          var btn_popup = "#felan-add-messages";
        } else if ($(this).is("#form-messages-applicants")) {
          var form_popup = "#form-messages-applicants";
          var btn_popup = "#btn-mees-applicants";
        } else if ($(this).is("#form-setting-deactive")) {
          var form_popup = "#form-setting-deactive";
          var btn_popup = "#btn-setting-deactive";
        } else if ($(this).is("#form-freelancer-user-package")) {
          var form_popup = "#form-freelancer-user-package";
          var btn_popup = "#action-user-package";
        } else if ($(this).is("#form-service-order-refund")) {
          var form_popup = "#form-service-order-refund";
          var btn_popup = ".btn-order-refund";
        } else if ($(this).is("#form-project-order-refund")) {
          var form_popup = "#form-project-order-refund";
          var btn_popup = ".btn-order-refund";
        } else if ($(this).is("#form-service-view-reason")) {
          var form_popup = "#form-service-view-reason";
          var btn_popup = ".btn-view-reason";
        } else if ($(this).is("#form-project-view-reason")) {
          var form_popup = "#form-project-view-reason";
          var btn_popup = ".btn-view-reason";
        } else if ($(this).is("#form-freelancer-withdraw")) {
          var form_popup = "#form-freelancer-withdraw";
          var btn_popup = "#btn-freelancer-withdraw";
        } else if ($(this).is("#form-employer-withdraw")) {
          var form_popup = "#form-employer-withdraw";
          var btn_popup = "#btn-employer-withdraw";
        } else if ($(this).is("#form-project-withdraw")) {
          var form_popup = "#form-project-withdraw";
          var btn_popup = "#btn-project-withdraw";
        } else if ($(this).is("#form-employer-user-package")) {
          var form_popup = "#form-employer-user-package";
          var btn_popup = "#action-employer-user-package";
        } else if ($(this).is("#form-apply-project")) {
          var form_popup = "#form-apply-project";
          var btn_popup = "#felan-apply-project";
        } else if ($(this).is("#form-popup-review")) {
          var form_popup = "#form-popup-review";
          var btn_popup = ".btn-action-review";
        } else if ($(this).is("#form-popup-view")) {
          var form_popup = "#form-popup-view";
          var btn_popup = ".btn-action-view";
        } else if ($(this).is("#form-wallet-topup")) {
          var form_popup = "#form-wallet-topup";
          var btn_popup = "#btn-wallet-topup";
        } else {
          var form_popup = "#felan-form-setting-meetings";
          var btn_popup = "#btn-meeting-settings";
        }

        var $btn_close = $(form_popup).find(".btn-close");
        var $bg_overlay = $(form_popup).find(".bg-overlay");
        var $btn_cancel = $(form_popup).find(".button-cancel");

        function open_popup(e) {
          e.preventDefault();
          $(form_popup).css({ opacity: "1", visibility: "unset" });
        }

        function close_popup(e) {
          e.preventDefault();
          $(form_popup).css({ opacity: "0", visibility: "hidden" });
        }

        $("body").on("click", btn_popup, open_popup);
        $bg_overlay.click(close_popup);
        $btn_close.click(close_popup);
        $btn_cancel.click(close_popup);
      });
    },

    select_tabs: function () {
      $(".felan-section-salary-select").hide();
      $("#select-salary-pay").change(function () {
        $(".felan-section-salary-select").hide();
        $("#" + $(this).val()).show();
        if ($(this).val() == "agree") {
          $("#jobs_rate").hide();
        } else {
          $("#jobs_rate").show();
        }
      });
      $("#" + $("#select-salary-pay option[selected]").val()).show();

      //Apply
      $(".felan-section-apply-select").hide();
      $("#select-apply-type").change(function () {
        $(".felan-section-apply-select").hide();
        $("#" + $(this).val()).show();
      });
      $("#" + $("#select-apply-type").val()).show();
    },

    about_jobs: function () {
      var $form_general = $("#jobs-submit-general"),
        $title = $form_general.find('input[name="jobs_title"]'),
        $cate = $form_general.find('select[name="jobs_categories"]'),
        $type = $form_general.find('select[name="jobs_type"]'),
        $about = $(".about-jobs-dashboard"),
        $text_title = $about.find(".title-about"),
        $text_cate = $about.find(".cate-about"),
        $label_type = $about.find(".label-warpper .label-type-inner"),
        $label_location = $about.find(".label-warpper .label-location-inner"),
        $logo = $about.find(".img-company"),
        $title_company = $about.find(".name-company");

      $title
        .keyup(function () {
          var value = $(this).val();
          var data_title = $text_title.data("title");
          if (value == "") {
            $text_title.text(data_title);
          } else {
            $text_title.text(value);
          }
        })
        .trigger("keyup");

      $cate
        .change(function () {
          var value = "";
          var data_cate = $text_cate.data("cate");
          $(this)
            .find("option:selected")
            .each(function () {
              value += $(this).text() + " ";
            });
          if ($(this).val() == "") {
            $text_cate.text(data_cate);
          } else {
            $text_cate.text(value);
          }
        })
        .trigger("change");

      $type
        .change(function () {
          var value = "",
            html = "";
          $(this)
            .find("option:selected")
            .each(function () {
              value = $(this).text() + " ";
              html += '<div class="label label-type">' + value + "</div>";
            });
          $label_type.html(html);
        })
        .trigger("change");

      var $location = $('select[name="jobs_location"]');
      $location
        .change(function () {
          var value = "";
          $(this)
            .find("option:selected")
            .each(function () {
              value =
                '<div class="label label-location"><i class="fas fa-map-marker-alt"></i>' +
                $(this).text() +
                "</div>";
            });
          if ($(this).val() !== "") {
            $label_location.html(value);
          }
        })
        .trigger("change");

      //company
      var $company = $('select[name="jobs_select_company"]');
      $company
        .change(function () {
          var company_url = "",
            company_title = "",
            data_name = $title_company.data("name");
          $(this)
            .find("option:selected")
            .each(function () {
              company_url = $(this).data("url");
              company_title = $(this).text();
            });

          if (company_url == "" || company_url == "new_company") {
            $logo.html('<i class="far fa-camera"></i>');
          } else {
            $logo.html('<img src ="' + company_url + '" alt=""/>');
          }
          if ($(this).val() !== "" && $(this).val() !== "new_company") {
            $title_company.text(company_title);
          } else {
            $title_company.text(data_name);
          }
        })
        .trigger("change");

      //salary
      var $form_salary = $("#jobs-submit-salary"),
        $salary_currency = $form_salary.find(
          'select[name="jobs_currency_type"]'
        ),
        $salary_show = $form_salary.find('select[name="jobs_salary_show"]'),
        $salary_rate = $form_salary.find('select[name="jobs_salary_rate"]'),
        $salary_minimum = $form_salary.find(
          'input[name="jobs_salary_minimum"]'
        ),
        $salary_maximum = $form_salary.find(
          'input[name="jobs_salary_maximum"]'
        ),
        $maximum_price = $form_salary.find('input[name="jobs_maximum_price"]'),
        $minimum_price = $form_salary.find('input[name="jobs_minimum_price"]'),
        $label_price = $about.find(".label-price");

      $salary_show
        .change(function () {
          var html = "",
            salary_show = $salary_show.find("option:selected").val(),
            text_min = $label_price.data("text-min"),
            text_max = $label_price.data("text-max"),
            text_agree = $label_price.data("text-agree"),
            salary_rate = $salary_rate.find("option:selected").val(),
            salary_currency = $salary_currency.find("option:selected").val();

          if (salary_show == "range") {
            var salary_minimum = $salary_minimum
              .val()
              .toString()
              .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            var salary_maximum = $salary_maximum
              .val()
              .toString()
              .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            $maximum_price.val("");
            $minimum_price.val("");
            html =
              '<i class="fas fa-money-bill-alt"></i><span class="salary-currency">' +
              salary_currency +
              '</span><span class="salary-minimum">' +
              salary_minimum +
              '</span> - <span class="salary-currency">' +
              salary_currency +
              '</span><span class="salary-maximum">' +
              salary_maximum +
              '</span> / <span class="salary-rate">' +
              salary_rate +
              "</span>";
          }
          if (salary_show == "maximum_amount") {
            var maximum_price = $maximum_price
              .val()
              .toString()
              .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            $salary_minimum.val("");
            $salary_maximum.val("");
            $minimum_price.val("");
            html =
              '<i class="fas fa-money-bill-alt"></i>' +
              text_max +
              '<span class="salary-currency">' +
              salary_currency +
              '</span><span class="price-maximum">' +
              maximum_price +
              '</span></span> / <span class="salary-rate">' +
              salary_rate +
              "</span>";
          }
          if (salary_show == "starting_amount") {
            var minimum_price = $minimum_price
              .val()
              .toString()
              .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            $salary_minimum.val("");
            $salary_maximum.val("");
            $maximum_price.val("");
            html =
              '<i class="fas fa-money-bill-alt"></i>' +
              text_min +
              '<span class="salary-currency">' +
              salary_currency +
              '</span><span class="price-minimum">' +
              minimum_price +
              '</span></span> / <span class="salary-rate">' +
              salary_rate +
              "</span>";
          }
          if (salary_show == "agree") {
            $salary_minimum.val("");
            $salary_maximum.val("");
            $maximum_price.val("");
            $minimum_price.val("");
            html = text_agree;
          }
          $label_price.html(html);
        })
        .trigger("change");

      $salary_currency
        .change(function () {
          var salary_currency = $salary_currency.find("option:selected").val();
          $(".salary-currency").html(salary_currency);
        })
        .trigger("change");

      $salary_rate
        .change(function () {
          var salary_rate = $salary_rate.find("option:selected").val();
          $(".salary-rate").html(salary_rate);
        })
        .trigger("change");

      $salary_minimum
        .keyup(function () {
          var salary_minimum = $salary_minimum
            .val()
            .toString()
            .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
          $(".salary-minimum").html(salary_minimum);
        })
        .trigger("keyup");

      $salary_maximum
        .keyup(function () {
          var salary_maximum = $salary_maximum
            .val()
            .toString()
            .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
          $(".salary-maximum").html(salary_maximum);
        })
        .trigger("keyup");

      $maximum_price
        .keyup(function () {
          var maximum_price = $maximum_price
            .val()
            .toString()
            .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
          $(".price-maximum").html(maximum_price);
        })
        .trigger("keyup");

      $minimum_price
        .keyup(function () {
          var minimum_price = $minimum_price
            .val()
            .toString()
            .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
          $(".price-minimum").html(minimum_price);
        })
        .trigger("keyup");
    },

    about_company: function () {
      var $form_general = $("#company-submit-general"),
        $title = $form_general.find('input[name="company_title"]'),
        $company_avatar_url = $form_general.find("input.avatar_url"),
        $location = $('select[name="company_location"]'),
        $about = $(".about-company-dashboard"),
        $text_title = $about.find(".title-about"),
        $text_des = $about.find(".des-about"),
        $text_location = $about.find(".location-about");

      $title
        .keyup(function () {
          var value = $(this).val();
          var data_title = $text_title.data("title");
          if (value == "") {
            $text_title.text(data_title);
          } else {
            $text_title.text(value);
          }
        })
        .trigger("keyup");

      $location
        .change(function () {
          var value = "";
          var data_location = $text_location.data("location");
          $(this)
            .find("option:selected")
            .each(function () {
              value += $(this).text() + " ";
            });
          if ($(this).val() == "") {
            $text_location.text(data_location);
          } else {
            $text_location.text(value);
          }
        })
        .trigger("change");

      if (typeof tinyMCE !== "undefined") {
        if ($("#wp-company_des-wrap").hasClass("tmce-active")) {
          tinyMCE.get("company_des").on("keyup", function () {
            var value = tinyMCE
              .get("company_des")
              .getContent({ format: "text" });
            if (value == "") {
              $text_des.text("");
            } else {
              $text_des.text(value);
            }
          });
        }
      }
    },

    check_company: function () {
      var $form_company = $("#company-submit-general"),
        $company_website = $form_company.find('input[name="company_website"]'),
        $company_phone = $form_company.find('input[name="company_phone"]'),
        $company_location = $(
          '#company-submit-location select[name="company_location"]'
        ),
        $about = $(".about-company-dashboard"),
        $check_webs = $about.find(".check-webs"),
        $webs_verified = $check_webs.data("verified"),
        $webs_not_verified = $check_webs.data("not-verified"),
        $check_phone = $about.find(".check-phone"),
        $phone_verified = $check_phone.data("verified"),
        $phone_not_verified = $check_phone.data("not-verified"),
        $check_location = $about.find(".check-location"),
        $location_verified = $check_location.data("verified"),
        $location_not_verified = $check_location.data("not-verified");

      $company_website
        .keyup(function () {
          var value_website = $(this).val();
          if (value_website !== "") {
            $check_webs.html('<i class="fas fa-check"></i>' + $webs_verified);
            $check_webs.addClass("active");
          } else {
            $check_webs.html(
              '<i class="fas fa-check"></i>' + $webs_not_verified
            );
            $check_webs.removeClass("active");
          }
          if (
            $check_webs.hasClass("active") &&
            $check_phone.hasClass("active") &&
            $check_location.hasClass("active")
          ) {
            $about.find(".felan-check-company").addClass("active");
          } else {
            $about.find(".felan-check-company").removeClass("active");
          }
        })
        .trigger("keyup");

      $company_phone
        .keyup(function () {
          var value_website = $(this).val();
          if (value_website !== "") {
            $check_phone.html('<i class="fas fa-check"></i>' + $phone_verified);
            $check_phone.addClass("active");
          } else {
            $check_phone.html(
              '<i class="fas fa-check"></i>' + $phone_not_verified
            );
            $check_phone.removeClass("active");
          }
          if (
            $check_webs.hasClass("active") &&
            $check_phone.hasClass("active") &&
            $check_location.hasClass("active")
          ) {
            $about.find(".felan-check-company").addClass("active");
          } else {
            $about.find(".felan-check-company").removeClass("active");
          }
        })
        .trigger("keyup");

      $company_location
        .change(function () {
          var value_location = $(this).val();
          if (value_location !== "") {
            $check_location.html(
              '<i class="fas fa-check"></i>' + $location_verified
            );
            $check_location.addClass("active");
          } else {
            $check_location.html(
              '<i class="fas fa-check"></i>' + $location_not_verified
            );
            $check_location.removeClass("active");
          }
          if (
            $check_webs.hasClass("active") &&
            $check_phone.hasClass("active") &&
            $check_location.hasClass("active")
          ) {
            $about.find(".felan-check-company").addClass("active");
          } else {
            $about.find(".felan-check-company").removeClass("active");
          }
        })
        .trigger("change");
    },

    service_conver_price: function () {
      var $form_service = $("#submit_service_form"),
        $service_price = $form_service.find('input[name="service_price"]'),
        $price_received = $form_service.find('input[name="price_received"]'),
        $percentage_price = $price_received.data("price-received");
      $service_price
        .keyup(function () {
          var $price =
            (parseInt($service_price.val()) *
              (100 - parseInt($percentage_price))) /
            100;
          $price_received.val(parseInt($price));
        })
        .trigger("keyup");
    },
  };

  $(document).ready(function () {
    DASHBOARD.init();
  });
})(jQuery);
