(function ($) {
  "use strict";

  var project_applicants = $(".applicants-dashboard"),
    form_view_reason = $("#form-project-view-reason"),
    form_refund = $("#form-project-order-refund");

  var ajax_url = felan_project_applicants_vars.ajax_url,
    not_applicants = felan_project_applicants_vars.not_applicants,
    payment_url = felan_project_applicants_vars.payment_url;

  $(document).ready(function () {
    $("body").on("click", "#btn-mees-applicants", function () {
      var item_id = $(this).attr("data-id");
      $("#form-messages-applicants .content-mess").text($(this).data("mess"));
      $(this).find(".fa-facebook-messenger").addClass("active");
      $(".btn-realy-mess").attr("data-id", $(this).data("id")),
        $(".btn-realy-mess").attr("data-apply", $(this).data("apply")),
        $(".btn-realy-mess").attr("data-mess", $(this).data("mess")),
        $(".btn-realy-mess").attr(
          "data-project-id",
          $(this).data("project-id")
        );
      read_mess_ajax_load(item_id);
      return false;
    });

    function read_mess_ajax_load(item_id = "") {
      $.ajax({
        dataType: "json",
        url: ajax_url,
        data: {
          action: "felan_read_mess_ajax_load",
          item_id: item_id,
        },
        beforeSend: function () {},
        success: function (data) {},
      });
    }

    $("body").on("click", ".btn-realy-mess", function (e) {
      e.preventDefault();
      var item_id = $(this).attr("data-id"),
        title = $(this).attr("data-apply"),
        content = $(this).attr("data-mess"),
        project_id = $(this).attr("data-project-id");

      realy_mess_ajax_load(item_id, title, content, project_id);
    });

    function realy_mess_ajax_load(
      item_id = "",
      title = "",
      content = "",
      project_id = ""
    ) {
      var link_mess = $('input[name="link_mess"]').val();

      $.ajax({
        dataType: "json",
        url: ajax_url,
        data: {
          action: "felan_realy_mess_project_ajax_load",
          item_id: item_id,
          title: title,
          content: content,
          project_id: project_id,
        },
        beforeSend: function () {
          $(".btn-realy-mess").find(".btn-loading").fadeIn();
        },
        success: function (data) {
          $(".btn-realy-mess").find(".btn-loading").fadeOut();
          if (data.success === true) {
            window.location.href = link_mess;
          }
        },
      });
    }

    project_applicants
      .find(".select-pagination")
      .change(function () {
        var number = "";
        $(".select-pagination option:selected").each(function () {
          number += $(this).val() + " ";
        });
        $(this).attr("value");
      })
      .trigger("change");

    project_applicants.find("select.search-control").on("change", function () {
      $(".felan-pagination").find('input[name="paged"]').val(1);
      ajax_load();
    });

    function delay(callback, ms) {
      var timer = 0;
      return function () {
        var context = this,
          args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function () {
          callback.apply(context, args);
        }, ms || 0);
      };
    }

    project_applicants.find("input.search-control").keyup(
      delay(function () {
        $(".felan-pagination").find('input[name="paged"]').val(1);
        ajax_load();
      }, 1000)
    );

    $("body").on("click", ".btn-accept-pay", function (e) {
      var $this = $(this),
        project_id = $this.data("id"),
        project_price = $this.data("price"),
        project_time = $this.data("time"),
        project_time_type = $this.data("time-type");

      e.preventDefault();
      $.ajax({
        type: "post",
        url: ajax_url,
        dataType: "json",
        data: {
          action: "felan_project_package",
          project_id: project_id,
          project_price: project_price,
          project_time: project_time,
          project_time_type: project_time_type,
        },
        beforeSend: function () {
          $this.find(".btn-loading").fadeIn();
          project_applicants
            .find(".felan-loading-effect")
            .addClass("loading")
            .fadeIn();
        },
        success: function (data) {
          ajax_load();
          if (data.success == true) {
            window.location.href = payment_url;
          }
          project_applicants
            .find(".felan-loading-effect")
            .addClass("loading")
            .fadeOut();
          $this.find(".btn-loading").fadeOut();
        },
      });
    });

    $("body").on("click", ".applicants-control .btn-completed", function (e) {
      e.preventDefault();
      var item_id = $(this).attr("order-id");
      ajax_load(item_id, "completed");
    });

    $("body").on(
      "click",
      ".applicants-control .btn-order-refund",
      function (e) {
        var item_id = $(this).attr("order-id");
        form_refund.find("#btn-project-refund").attr("order-id", item_id);
      }
    );

    $("body").on("click", "#btn-project-refund", function (e) {
      e.preventDefault();
      var item_id = $(this).attr("order-id"),
        content_refund = form_refund
          .find('textarea[name="project_content_refund"]')
          .val(),
        project_payment = form_refund
          .find('select[name="project_payment"]')
          .val();
      ajax_load(item_id, "refund", content_refund, project_payment);
    });

    $("body").on("click", ".applicants-control .btn-view-reason", function () {
      var content_refund = $(this).data("content-refund");
      form_view_reason.find(".content-refund-reason").text(content_refund);
    });

    $("body").on(
      "click",
      ".applicants-control .btn-action-review",
      function () {
        var freelancer_id = $(this).attr("freelancer-id");
        $('input[name="freelancer_id"]').val(freelancer_id);
      }
    );

    $("body").on("click", ".felan-pagination a.page-numbers", function (e) {
      e.preventDefault();
      project_applicants
        .find(".felan-pagination li .page-numbers")
        .removeClass("current");
      $(this).addClass("current");
      var paged = $(this).text();
      var current_page = 1;
      if (
        project_applicants
          .find(".felan-pagination")
          .find('input[name="paged"]')
          .val()
      ) {
        current_page = $(".felan-pagination").find('input[name="paged"]').val();
      }
      if ($(this).hasClass("next")) {
        paged = parseInt(current_page) + 1;
      }
      if ($(this).hasClass("prev")) {
        paged = parseInt(current_page) - 1;
      }
      project_applicants
        .find(".felan-pagination")
        .find('input[name="paged"]')
        .val(paged);

      ajax_load();
    });

    var paged = 1;
    project_applicants.find(".select-pagination").attr("data-value", paged);

    function ajax_load(
      item_id = "",
      action_click = "",
      content_refund = "",
      service_payment = ""
    ) {
      var paged = 1,
        height = project_applicants.find("#applicants-dashboard").height(),
        project_search = project_applicants
          .find('input[name="applicants_search"]')
          .val(),
        item_amount = project_applicants
          .find('select[name="item_amount"]')
          .val(),
        project_sort_by = project_applicants
          .find('select[name="applicants_sort_by"]')
          .val();
      paged = $(".felan-pagination").find('input[name="paged"]').val();

      $.ajax({
        dataType: "json",
        url: ajax_url,
        data: {
          action: "felan_filter_project_applicants",
          item_amount: item_amount,
          paged: paged,
          project_search: project_search,
          project_sort_by: project_sort_by,
          content_refund: content_refund,
          service_payment: service_payment,
          item_id: item_id,
          action_click: action_click,
        },
        beforeSend: function () {
          if (action_click !== "refund") {
            project_applicants
              .find(".felan-loading-effect")
              .addClass("loading")
              .fadeIn();
            project_applicants.find("#applicants-dashboard").height(height);
          }
        },
        success: function (data) {
          if (data.success === true) {
            var $items_pagination =
                project_applicants.find(".items-pagination"),
              select_item = $items_pagination
                .find('select[name="item_amount"] option:selected')
                .val(),
              max_number = data.total_post,
              value_first = select_item * paged + 1 - select_item,
              value_last = select_item * paged;
            if (max_number < value_first) {
              value_first = select_item * (paged - 1) + 1;
            }
            if (max_number < value_last) {
              value_last = max_number;
            }
            $(".num-first").text(value_first);
            $(".num-last").text(value_last);

            if (max_number > select_item) {
              $items_pagination.closest(".pagination-dashboard").show();
              $items_pagination.find(".num-total").html(data.total_post);
            } else {
              $items_pagination.closest(".pagination-dashboard").hide();
            }

            project_applicants.find(".pagination").html(data.pagination);
            project_applicants
              .find("#my-applicants tbody")
              .fadeOut("fast", function () {
                project_applicants
                  .find("#my-applicants tbody")
                  .html(data.project_html);
                project_applicants.find("#my-applicants tbody").fadeIn(300);
              });
            project_applicants
              .find("#applicants-dashboard")
              .css("height", "auto");
          } else {
            project_applicants
              .find("#my-applicants tbody")
              .html(
                '<span class="not-applicants">' + not_applicants + "</span>"
              );
          }
          project_applicants
            .find(".felan-loading-effect")
            .removeClass("loading")
            .fadeOut();

          if (action_click === "refund") {
            window.location.reload();
          }
        },
      });
    }
  });
})(jQuery);
