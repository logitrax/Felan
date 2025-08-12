(function ($) {
    "use strict";

    var disputes_dashboard = $(".felan-disputes"),
        ajax_url = felan_disputes_vars.ajax_url,
        not_disputes = felan_disputes_vars.not_disputes;

    $(document).ready(function () {
        disputes_dashboard
            .find(".select-pagination")
            .change(function () {
                var number = "";
                $(".select-pagination option:selected").each(function () {
                    number += $(this).val() + " ";
                });
                $(this).attr("value");
            })
            .trigger("change");

        disputes_dashboard.find("select.search-control").on("change", function () {
            $(".felan-pagination").find('input[name="paged"]').val(1);
            ajax_load();
        });

        disputes_dashboard.find("input.search-control").on("input", function () {
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

        disputes_dashboard.find("input.disputes-search-control").keyup(
            delay(function () {
                $(".felan-pagination").find('input[name="paged"]').val(1);
                ajax_load();
            }, 1000)
        );

        $("body").on("click", ".felan-disputes .felan-pagination a.page-numbers", function (e) {
            e.preventDefault();
            $(".felan-pagination li .page-numbers").removeClass("current");
            $(this).addClass("current");
            var paged = $(this).text();
            var current_page = 1;
            if (
                disputes_dashboard
                    .find(".felan-pagination")
                    .find('input[name="paged"]')
                    .val()
            ) {
                current_page = disputes_dashboard
                    .find(".felan-pagination").find('input[name="paged"]').val();
            }
            if ($(this).hasClass("next")) {
                paged = parseInt(current_page) + 1;
            }
            if ($(this).hasClass("prev")) {
                paged = parseInt(current_page) - 1;
            }
            disputes_dashboard
                .find(".felan-pagination")
                .find('input[name="paged"]')
                .val(paged);

            ajax_load();
        });

        var paged = 1;
        disputes_dashboard.find(".select-pagination").attr("data-value", paged);

        function ajax_load() {
            var paged = 1;
            var height = disputes_dashboard.find("#disputes").height();
            var disputes_search = disputes_dashboard
                    .find('input[name="disputes_search"]')
                    .val(),
                disputes_status = disputes_dashboard
                    .find('select[name="disputes_status"]')
                    .val(),
                item_amount = disputes_dashboard
                    .find('select[name="item_amount"]')
                    .val(),
                disputes_sort_by = disputes_dashboard
                    .find('select[name="disputes_sort_by"]')
                    .val();
            paged = disputes_dashboard
                .find(".felan-pagination").find('input[name="paged"]').val();

            $.ajax({
                dataType: "json",
                url: ajax_url,
                data: {
                    action: "felan_employer_disputes",
                    item_amount: item_amount,
                    paged: paged,
                    disputes_search: disputes_search,
                    disputes_status: disputes_status,
                    disputes_sort_by: disputes_sort_by,
                },
                beforeSend: function () {
                    disputes_dashboard
                        .find(".felan-loading-effect")
                        .addClass("loading")
                        .fadeIn();
                },
                success: function (data) {
                    if (data.success === true) {
                        var $items_pagination = disputes_dashboard.find(".items-pagination"),
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

                        disputes_dashboard.find(".pagination").html(data.pagination);
                        disputes_dashboard
                            .find("#disputes tbody")
                            .fadeOut("fast", function () {
                                disputes_dashboard
                                    .find("#disputes tbody")
                                    .html(data.disputes_html);
                                disputes_dashboard.find("#disputes tbody").fadeIn(300);
                            });
                        disputes_dashboard.find("#disputes").css("height", "auto");
                    } else {
                        disputes_dashboard
                            .find("#disputes tbody")
                            .html('<span class="not-disputes">' + not_disputes + "</span>");
                    }
                    disputes_dashboard
                        .find(".felan-loading-effect")
                        .removeClass("loading")
                        .fadeOut();
                },
            });
        }
    });
})(jQuery);
