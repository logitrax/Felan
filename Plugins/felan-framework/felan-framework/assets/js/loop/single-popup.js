(function ($) {
  "use strict";

  $(document).ready(function () {
    var ajax_url = felan_ajax_single_popup.ajax_url;
    var form_single_popup = $("#felan-form-single");
    var btn_single_popup = ".felan-link-item.btn-single-settings";
    var $bg_single_overlay = form_single_popup.find(".bg-overlay");

    //Tabs
    function tabs_single(obj) {
      var $tabGroup = $(obj).closest(".tab-single");
      $tabGroup.find("ul li").removeClass("active");
      $(obj).addClass("active");

      var id = $(obj).find("a").attr("href");
      $tabGroup.parent().find(".tab-single-info").hide();
      $tabGroup.parent().find(id).show();
    }

    $(".tab-single").each(function () {
      var $group = $(this);
      $group.find(".tab-single-list li").click(function () {
        tabs_single(this);
        return false;
      });
      tabs_single($group.find(".tab-single-list li:first-child"));
    });

    //Form Popup
    function open_single_popup(e) {
      e.preventDefault();
      $("body").css("overflow", "hidden");
      form_single_popup.addClass("active");
    }

    function close_single_popup(e) {
      e.preventDefault();
      $("body").css("overflow", "unset");
      form_single_popup.removeClass("active");
    }

    $bg_single_overlay.click(function (e) {
      close_single_popup(e);
    });

    $("body").on("click", btn_single_popup, function (e) {
      e.preventDefault();
      var post_id = $(this).data("post-id");
      var post_type = $(this).data("post-type");

      $.ajax({
        dataType: "json",
        url: ajax_url,
        data: {
          action: "felan_ajax_single_popup",
          post_id: post_id,
          post_type: post_type,
        },
        beforeSend: function () {
          $(".area-freelancers .felan-freelancers-item").addClass(
            "skeleton-loading"
          );
          $(".area-company .felan-company-item").addClass("skeleton-loading");
          $(".area-jobs .felan-jobs-item").addClass("skeleton-loading");
        },
        success: function (data) {
          if (data.success === true) {
            open_single_popup(e);
            form_single_popup.find(".single-inner-popup").html(data.popup_html);
            $(".area-freelancers .felan-freelancers-item").removeClass(
              "skeleton-loading"
            );
            $(".area-company .felan-company-item").removeClass(
              "skeleton-loading"
            );
            $(".area-jobs .felan-jobs-item").removeClass("skeleton-loading");

            $(".tab-single-list li").click(function () {
              tabs_single(this);
              return false;
            });
            tabs_single($(".tab-single-list li:first-child"));
            GLF.element.slick_carousel();
            $(".felan-light-gallery").lightGallery({
              thumbnail: true,
              selector: ".lgbox",
            });
            $(".btn-single-close").click(function (e) {
              close_single_popup(e);
            });

            COMPANY.tab_company();

            if (data.post_type === "freelancer") {
              FREELANCER_REVIEW.submit_review();
            } else if (data.post_type === "company") {
              COMPANY_REVIEW.submit_review();
            } else if (data.post_type === "service") {
              SERVICE_REVIEW.submit_review();
              SERVICE.submit_addons();
            } else if (data.post_type === "jobs") {
              var $form_popup = $(".form-popup-apply");
              $form_popup.each(function () {
                var $form_popup = $(".form-popup-apply");
                var $btn_close = $form_popup.find(".btn-close");
                var $bg_overlay = $form_popup.find(".bg-overlay");
                var $btn_cancel = $form_popup.find(".button-cancel");
                var $form_popup_id = $("#" + $(this).attr("id"));
                var $btn_popup = $(".felan-button-apply." + $(this).attr("id"));

                function open_popup(e) {
                  e.preventDefault();
                  $form_popup_id.css({ opacity: "1", visibility: "unset" });
                }

                function close_popup(e) {
                  e.preventDefault();
                  $form_popup_id.css({ opacity: "0", visibility: "hidden" });
                }
                $btn_popup.on("click", open_popup);
                $bg_overlay.on("click", close_popup);
                $btn_close.on("click", close_popup);
                $btn_cancel.on("click", close_popup);
              });
              $(".felan-select2").select2();
            }

            $(".service-faq-details").on("click", ".faq-header", function (e) {
              e.preventDefault();
              $(this).parent().find(".faq-content").slideToggle();
            });
            $(".project-faq-details").on("click", ".faq-header", function (e) {
              e.preventDefault();
              $(this).parent().find(".faq-content").slideToggle();
            });

            $(".toggle-social").on("click", ".btn-share", function (e) {
              e.preventDefault();
              $(this).parent().toggleClass("active");
              $(this).parent().find(".social-share").slideToggle(300);
            });
          }
        },
      });
    });
  });
})(jQuery);
