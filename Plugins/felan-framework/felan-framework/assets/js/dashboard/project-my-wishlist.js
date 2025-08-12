(function ($) {
  "use strict";

  var project_my_wishlist = $(".felan-project-my-wishlist");

  var ajax_url = felan_project_my_wishlist_vars.ajax_url,
    not_project = felan_project_my_wishlist_vars.not_project;

  $(document).ready(function () {
    project_my_wishlist.find("select-pagination").change(function () {
      var number = "";
      project_my_wishlist
        .find(".select-pagination option:selected")
        .each(function () {
          number += $(this).val() + " ";
        });
      $(this).attr("value");
    });

    project_my_wishlist.find("select.search-control").on("change", function () {
      project_my_wishlist
        .find(".felan-pagination")
        .find('input[name="paged"]')
        .val(1);
      ajax_load_wishlist();
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

    project_my_wishlist.find("input.project-search-control").keyup(
      delay(function () {
        project_my_wishlist
          .find(".felan-pagination")
          .find('input[name="paged"]')
          .val(1);
        ajax_load_wishlist();
      }, 1000)
    );

    $("body").on(
      "click",
      ".felan-project-my-wishlist .project-control .btn-delete",
      function (e) {
        e.preventDefault();
        var delete_id = $(this).attr("project-id");
        ajax_load_wishlist(delete_id, "delete");
      }
    );

    $("body").on(
      "click",
      ".felan-project-my-wishlist .felan-pagination a.page-numbers",
      function (e) {
        e.preventDefault();
        project_my_wishlist
          .find(".felan-pagination li .page-numbers")
          .removeClass("current");
        $(this).addClass("current");
        var paged = $(this).text();
        var current_page = 1;
        if (
          project_my_wishlist
            .find(".felan-pagination")
            .find('input[name="paged"]')
            .val()
        ) {
          current_page = project_my_wishlist
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
        project_my_wishlist
          .find(".felan-pagination")
          .find('input[name="paged"]')
          .val(paged);

        ajax_load_wishlist();
      }
    );

    var paged = 1;
    project_my_wishlist.find(".select-pagination").attr("data-value", paged);

    function ajax_load_wishlist(item_id = "", action_click = "") {
      var paged = 1;
      var height = project_my_wishlist.find("#project-my-wishlist").height();
      var project_search = project_my_wishlist
          .find('input[name="project_search"]')
          .val(),
        item_amount = project_my_wishlist
          .find('select[name="item_amount"]')
          .val(),
        project_sort_by = project_my_wishlist
          .find('select[name="project_sort_by"]')
          .val();
      paged = project_my_wishlist
        .find('.felan-pagination input[name="paged"]')
        .val();

      $.ajax({
        dataType: "json",
        url: ajax_url,
        data: {
          action: "felan_filter_project_my_wishlist",
          item_amount: item_amount,
          paged: paged,
          project_search: project_search,
          project_sort_by: project_sort_by,
          item_id: item_id,
          action_click: action_click,
        },
        beforeSend: function () {
          project_my_wishlist
            .find(".felan-loading-effect")
            .addClass("loading")
            .fadeIn();
          project_my_wishlist.find("#project-my-wishlist").height(height);
        },
        success: function (data) {
          if (data.success === true) {
            var $items_pagination =
                project_my_wishlist.find(".items-pagination"),
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

            project_my_wishlist.find(".pagination").html(data.pagination);
            project_my_wishlist
              .find("#project-my-wishlist tbody")
              .fadeOut("fast", function () {
                project_my_wishlist
                  .find("#project-my-wishlist tbody")
                  .html(data.project_html);
                project_my_wishlist
                  .find("#project-my-wishlist tbody")
                  .fadeIn(300);
              });
            project_my_wishlist
              .find("#project-my-wishlist")
              .css("height", "auto");
          } else {
            project_my_wishlist
              .find("#project-my-wishlist tbody")
              .html('<span class="not-project">' + not_project + "</span>");
          }
          $(".tab-wishlist-item span").html("(" + data.total_post + ")");
          project_my_wishlist
            .find(".felan-loading-effect")
            .removeClass("loading")
            .fadeOut();
        },
      });
    }
  });
})(jQuery);
