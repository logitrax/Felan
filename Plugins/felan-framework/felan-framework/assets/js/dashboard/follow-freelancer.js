(function ($) {
  "use strict";

  var follow_freelancer = $(".felan-follow-freelancer");

  var ajax_url = felan_follow_freelancer_vars.ajax_url,
    not_freelancer = felan_follow_freelancer_vars.not_freelancer;

  $(document).ready(function () {
    follow_freelancer.find("select-pagination").change(function () {
      var number = "";
      follow_freelancer
        .find(".select-pagination option:selected")
        .each(function () {
          number += $(this).val() + " ";
        });
      $(this).attr("value");
    });

    follow_freelancer.find("select.search-control").on("change", function () {
      follow_freelancer
        .find(".felan-pagination")
        .find('input[name="paged"]')
        .val(1);
      ajax_load_follow();
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

    follow_freelancer.find("input.search-control").keyup(
      delay(function () {
        follow_freelancer
          .find(".felan-pagination")
          .find('input[name="paged"]')
          .val(1);
        ajax_load_follow();
      }, 1000)
    );

    $("body").on(
      "click",
      ".felan-follow-freelancer .action-setting .btn-delete",
      function (e) {
        e.preventDefault();
        var delete_id = $(this).attr("items-id");
        ajax_load_follow(delete_id, "delete");
      }
    );

    $("body").on(
      "click",
      ".felan-follow-freelancer .felan-pagination a.page-numbers",
      function (e) {
        e.preventDefault();
        follow_freelancer
          .find(".felan-pagination li .page-numbers")
          .removeClass("current");
        $(this).addClass("current");
        var paged = $(this).text();
        var current_page = 1;
        if (
          follow_freelancer
            .find(".felan-pagination")
            .find('input[name="paged"]')
            .val()
        ) {
          current_page = follow_freelancer
            .find(".felan-pagination")
            .find('input[name="paged"]')
            .val();
        }
        if ($(this).hasClass("next")) {
          paged = parseInt(current_page) + 1;
        }
        if ($(this).hasClass("prev")) {
          paged = parseInt(current_page) - 1;
        }
        follow_freelancer
          .find(".felan-pagination")
          .find('input[name="paged"]')
          .val(paged);

        ajax_load_follow();
      }
    );

    var paged = 1;
    follow_freelancer.find(".select-pagination").attr("data-value", paged);

    function ajax_load_follow(item_id = "", action_click = "") {
      var paged = 1;
      var height = follow_freelancer.find("#follow-freelancer").height();
      var freelancer_search = follow_freelancer
          .find('input[name="freelancer_search"]')
          .val(),
        item_amount = follow_freelancer
          .find('select[name="item_amount"]')
          .val(),
        freelancer_sort_by = follow_freelancer
          .find('select[name="freelancer_sort_by"]')
          .val();
      paged = follow_freelancer
        .find('.felan-pagination input[name="paged"]')
        .val();

      $.ajax({
        dataType: "json",
        url: ajax_url,
        data: {
          action: "felan_filter_follow_freelancer",
          item_amount: item_amount,
          paged: paged,
          freelancer_search: freelancer_search,
          freelancer_sort_by: freelancer_sort_by,
          item_id: item_id,
          action_click: action_click,
        },
        beforeSend: function () {
          follow_freelancer
            .find(".felan-loading-effect")
            .addClass("loading")
            .fadeIn();
          follow_freelancer.find("#follow-freelancer").height(height);
        },
        success: function (data) {
          if (data.success === true) {
            var $items_pagination = follow_freelancer.find(".items-pagination"),
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
            $items_pagination.find(".num-first").text(value_first);
            $items_pagination.find(".num-last").text(value_last);

            if (max_number > select_item) {
              $items_pagination.closest(".pagination-dashboard").show();
              $items_pagination.find(".num-total").html(data.total_post);
            } else {
              $items_pagination.closest(".pagination-dashboard").hide();
            }

            follow_freelancer.find(".pagination").html(data.pagination);
            follow_freelancer
              .find("#follow-freelancer tbody")
              .fadeOut("fast", function () {
                follow_freelancer
                  .find("#follow-freelancer tbody")
                  .html(data.freelancer_html);
                follow_freelancer.find("#follow-freelancer tbody").fadeIn(300);
              });
            follow_freelancer.find("#follow-freelancer").css("height", "auto");
          } else {
            follow_freelancer
              .find("#follow-freelancer tbody")
              .html(
                '<span class="not-freelancers">' + not_freelancer + "</span>"
              );
          }
          $(".tab-follow-item span").html("(" + data.total_post + ")");
          follow_freelancer
            .find(".felan-loading-effect")
            .removeClass("loading")
            .fadeOut();
        },
      });
    }
  });
})(jQuery);
