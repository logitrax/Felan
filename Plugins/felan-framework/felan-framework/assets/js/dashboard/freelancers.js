(function ($) {
  "use strict";

  var freelancers_dashboard = $(".freelancers-dashboard");

  var ajax_url = felan_freelancers_dashboard_vars.ajax_url,
    not_freelancers = felan_freelancers_dashboard_vars.not_freelancers;

  $(document).ready(function () {
    freelancers_dashboard
      .find(".select-pagination")
      .change(function () {
        var number = "";
        $(".select-pagination option:selected").each(function () {
          number += $(this).val() + " ";
        });
        $(this).attr("value");
      })
      .trigger("change");

    freelancers_dashboard
      .find("select.search-control")
      .on("change", function () {
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

    freelancers_dashboard.find("input.search-control").keyup(
      delay(function () {
        $(".felan-pagination").find('input[name="paged"]').val(1);
        ajax_load();
      }, 1000)
    );

    $("body").on(
      "click",
      ".freelancers-dashboard .list-action .btn-delete",
      function (e) {
        e.preventDefault();
        var items_id = $(this).attr("items-id");
        var author_id = $(this).attr("athour-id");
        var follow_company = $(this).attr("follow_company");
        ajax_load(items_id, author_id, follow_company, "delete");
      }
    );

    $("body").on(
      "click",
      ".freelancers-dashboard .felan-pagination a.page-numbers",
      function (e) {
        e.preventDefault();
        $(".felan-pagination li .page-numbers").removeClass("current");
        $(this).addClass("current");
        var paged = $(this).text();
        var current_page = 1;
        if ($(".felan-pagination").find('input[name="paged"]').val()) {
          current_page = $(".felan-pagination")
            .find('input[name="paged"]')
            .val();
        }
        if ($(this).hasClass("next")) {
          paged = parseInt(current_page) + 1;
        }
        if ($(this).hasClass("prev")) {
          paged = parseInt(current_page) - 1;
        }
        $(".felan-pagination").find('input[name="paged"]').val(paged);

        ajax_load();
      }
    );

    var paged = 1;
    $(".select-pagination").attr("data-value", paged);

    function ajax_load(
      item_id = "",
      author_id = "",
      follow_company = "",
      action_click = ""
    ) {
      var paged = 1;
      var height = freelancers_dashboard
        .find("#freelancers-dashboard")
        .height();
      var freelancers_search = freelancers_dashboard
          .find('input[name="freelancers_search"]')
          .val(),
        item_amount = freelancers_dashboard
          .find('select[name="item_amount"]')
          .val(),
        freelancers_id = freelancers_dashboard
          .find('input[name="freelancers_id"]')
          .val(),
        freelancers_sort_by = freelancers_dashboard
          .find('select[name="freelancers_sort_by"]')
          .val();
      paged = freelancers_dashboard
        .find(".felan-pagination")
        .find('input[name="paged"]')
        .val();

      $.ajax({
        dataType: "json",
        url: ajax_url,
        data: {
          action: "felan_filter_freelancers_dashboard",
          item_amount: item_amount,
          paged: paged,
          freelancers_sort_by: freelancers_sort_by,
          freelancers_search: freelancers_search,
          freelancers_id: freelancers_id,
          item_id: item_id,
          follow_company: follow_company,
          author_id: author_id,
          action_click: action_click,
        },
        beforeSend: function () {
          freelancers_dashboard
            .find(".felan-loading-effect")
            .addClass("loading")
            .fadeIn();
          freelancers_dashboard.find("#freelancers-dashboard").height(height);
        },
        success: function (data) {
          if (data.success === true) {
            var $items_pagination =
                freelancers_dashboard.find(".items-pagination"),
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

            freelancers_dashboard.find(".pagination").html(data.pagination);
            freelancers_dashboard
              .find("#freelancers-db tbody")
              .fadeOut("fast", function () {
                freelancers_dashboard
                  .find("#freelancers-db tbody")
                  .html(data.freelancers_html);
                freelancers_dashboard.find("#freelancers-db tbody").fadeIn(300);
              });
            freelancers_dashboard
              .find("#freelancers-dashboard")
              .css("height", "auto");
          } else {
            freelancers_dashboard
              .find("#freelancers-db tbody")
              .html(
                '<span class="not-freelancers">' + not_freelancers + "</span>"
              );
          }
          freelancers_dashboard
            .find(".felan-loading-effect")
            .removeClass("loading")
            .fadeOut();
        },
      });
    }
  });
})(jQuery);
