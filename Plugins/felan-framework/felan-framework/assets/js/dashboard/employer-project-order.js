(function ($) {
    "use strict";

    var project_dashboard = $(".felan-project-order"),
        ajax_url = felan_project_order_vars.ajax_url,
        not_project = felan_project_order_vars.not_project;

    $(document).ready(function () {
        project_dashboard
            .find(".select-pagination")
            .change(function () {
                var number = "";
                $(".select-pagination option:selected").each(function () {
                    number += $(this).val() + " ";
                });
                $(this).attr("value");
            })
            .trigger("change");

        project_dashboard.find("select.search-control").on("change", function () {
            $(".felan-pagination").find('input[name="paged"]').val(1);
            ajax_load();
        });

        project_dashboard.find("input.search-control").on("input", function () {
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

        project_dashboard.find("input.project-search-control").keyup(
            delay(function () {
                $(".felan-pagination").find('input[name="paged"]').val(1);
                ajax_load();
            }, 1000)
        );

        $("body").on(
            "click",
            ".btn-action-review",
            function () {
                var freelancer_id = $(this).attr("freelancer-id");
                $('input[name="freelancer_id"]').val(freelancer_id);
            }
        );

        $("body").on("click", ".felan-project-order .felan-pagination a.page-numbers", function (e) {
            e.preventDefault();
            $(".felan-pagination li .page-numbers").removeClass("current");
            $(this).addClass("current");
            var paged = $(this).text();
            var current_page = 1;
            if (
                project_dashboard
                    .find(".felan-pagination")
                    .find('input[name="paged"]')
                    .val()
            ) {
                current_page = project_dashboard
                    .find(".felan-pagination").find('input[name="paged"]').val();
            }
            if ($(this).hasClass("next")) {
                paged = parseInt(current_page) + 1;
            }
            if ($(this).hasClass("prev")) {
                paged = parseInt(current_page) - 1;
            }
            project_dashboard
                .find(".felan-pagination")
                .find('input[name="paged"]')
                .val(paged);

            ajax_load();
        });

        var paged = 1;
        project_dashboard.find(".select-pagination").attr("data-value", paged);

        function ajax_load(item_id = "",
                           action_click = "",
                           content_refund = "",
                           project_payment = "") {
            var paged = 1;
            var height = project_dashboard.find("#project-order").height();
            var project_search = project_dashboard
                    .find('input[name="project_search"]')
                    .val(),
                project_status = project_dashboard
                    .find('select[name="project_status"]')
                    .val(),
                item_amount = project_dashboard
                    .find('select[name="item_amount"]')
                    .val(),
                project_sort_by = project_dashboard
                    .find('select[name="project_sort_by"]')
                    .val();
            paged = project_dashboard
                .find(".felan-pagination").find('input[name="paged"]').val();

            $.ajax({
                dataType: "json",
                url: ajax_url,
                data: {
                    action: "felan_employer_order_project",
                    item_amount: item_amount,
                    paged: paged,
                    project_search: project_search,
                    project_status: project_status,
                    project_sort_by: project_sort_by,
                    item_id: item_id,
                    content_refund: content_refund,
                    project_payment: project_payment,
                    action_click: action_click,
                },
                beforeSend: function () {
                    if (action_click !== "refund") {
                        project_dashboard
                            .find(".felan-loading-effect")
                            .addClass("loading")
                            .fadeIn();
                        project_dashboard.find("#project-order").height(height);
                    }
                },
                success: function (data) {
                    if (data.success === true) {
                        var $items_pagination = project_dashboard.find(".items-pagination"),
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

                        project_dashboard.find(".pagination").html(data.pagination);
                        project_dashboard
                            .find("#project-order tbody")
                            .fadeOut("fast", function () {
                                project_dashboard
                                    .find("#project-order tbody")
                                    .html(data.project_html);
                                project_dashboard.find("#project-order tbody").fadeIn(300);
                            });
                        project_dashboard.find("#project-order").css("height", "auto");
                    } else {
                        project_dashboard
                            .find("#project-order tbody")
                            .html('<span class="not-project">' + not_project + "</span>");
                    }
                    project_dashboard
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
