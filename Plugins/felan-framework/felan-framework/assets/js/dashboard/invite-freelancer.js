(function ($) {
  "use strict";

  var invite_freelancer = $("#tab-invite");

  var ajax_url = felan_invite_freelancer_vars.ajax_url,
    not_freelancer = felan_invite_freelancer_vars.not_freelancer;

  $(document).ready(function () {
    invite_freelancer.find("select-pagination").change(function () {
      var number = "";
      invite_freelancer
        .find(".select-pagination option:selected")
        .each(function () {
          number += $(this).val() + " ";
        });
      $(this).attr("value");
    });

    invite_freelancer.find("select.search-control").on("change", function () {
      invite_freelancer
        .find(".felan-pagination")
        .find('input[name="paged"]')
        .val(1);
      ajax_load_invite();
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

    invite_freelancer.find("input.search-control").keyup(
      delay(function () {
        invite_freelancer
          .find(".felan-pagination")
          .find('input[name="paged"]')
          .val(1);
        ajax_load_invite();
      }, 1000)
    );

    $("body").on(
      "click",
      ".felan-invite-freelancer .action-setting .btn-delete",
      function (e) {
        e.preventDefault();
        var delete_id = $(this).attr("items-id");
        ajax_load_invite(delete_id, "delete");
      }
    );

    $("body").on(
      "click",
      "#tab-invite .felan-pagination a.page-numbers",
      function (e) {
        e.preventDefault();
        invite_freelancer
          .find(".felan-pagination li .page-numbers")
          .removeClass("current");
        $(this).addClass("current");
        var paged = $(this).text();
        var current_page = 1;
        if (
          invite_freelancer
            .find(".felan-pagination")
            .find('input[name="paged"]')
            .val()
        ) {
          current_page = invite_freelancer
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
        invite_freelancer
          .find(".felan-pagination")
          .find('input[name="paged"]')
          .val(paged);

        ajax_load_invite();
      }
    );

    var paged = 1;
    invite_freelancer.find(".select-pagination").attr("data-value", paged);

    function ajax_load_invite(item_id = "", action_click = "") {
      var paged = 1;
      var height = invite_freelancer.find("#invite-freelancer").height();
      var freelancer_search = invite_freelancer
          .find('input[name="freelancer_search"]')
          .val(),
        list_jobs = $('input[name="list_jobs"]').val(),
        item_amount = invite_freelancer
          .find('select[name="item_amount"]')
          .val(),
        freelancer_sort_by = invite_freelancer
          .find('select[name="freelancer_sort_by"]')
          .val();
      paged = invite_freelancer
        .find('.felan-pagination input[name="paged"]')
        .val();

      $.ajax({
        dataType: "json",
        url: ajax_url,
        data: {
          action: "felan_filter_invite_freelancer",
          item_amount: item_amount,
          paged: paged,
          list_jobs: list_jobs,
          freelancer_search: freelancer_search,
          freelancer_sort_by: freelancer_sort_by,
          item_id: item_id,
          action_click: action_click,
        },
        beforeSend: function () {
          invite_freelancer
            .find(".felan-loading-effect")
            .addClass("loading")
            .fadeIn();
          invite_freelancer.find("#invite-freelancer").height(height);
        },
        success: function (data) {
          if (data.success === true) {
            var $items_pagination = invite_freelancer.find(".items-pagination"),
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

            invite_freelancer.find(".pagination").html(data.pagination);
            invite_freelancer
              .find("#invite-freelancer tbody")
              .fadeOut("fast", function () {
                invite_freelancer
                  .find("#invite-freelancer tbody")
                  .html(data.freelancer_html);
                invite_freelancer.find("#invite-freelancer tbody").fadeIn(300);
              });
            invite_freelancer.find("#invite-freelancer").css("height", "auto");
          } else {
            invite_freelancer
              .find("#invite-freelancer tbody")
              .html(
                '<span class="not-freelancers">' + not_freelancer + "</span>"
              );
          }
          $(".tab-invite-item span").html("(" + data.total_post + ")");
          invite_freelancer
            .find(".felan-loading-effect")
            .removeClass("loading")
            .fadeOut();
        },
      });
    }
  });
})(jQuery);
