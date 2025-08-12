(function ($) {
    "use strict";

    var project_dashboard = $(".project-dashboard");

    var ajax_url = felan_project_dashboard_vars.ajax_url,
        not_project = felan_project_dashboard_vars.not_project;

    $(document).ready(function () {
        $("body").on(
            "click",
            ".btn-review-project",
            function () {
                var freelancer_id = $(this).attr("freelancer-id");
                var order_id = $(this).attr("order-id");

                $('input[name="freelancer_id"]').val(freelancer_id);
                $('input[name="order_id"]').val(order_id);
            }
        );

        $(".project-number-applicant").on("click", function (e) {
            e.preventDefault();
            var applicant = $(this).attr('href');
            $(applicant).slideToggle();
            $(this).find('i').toggleClass('felan-rotate-up');
        });

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

		project_dashboard.find("input.project-proposal-search-control").keyup(
            delay(function () {
                $(".felan-proposal-pagination").find('input[name="paged"]').val(1);
                ajax_proposal_load();
            }, 1000)
        );

		project_dashboard.find("select.proposal-search-control").on("change", function () {
            $(".felan-proposal-pagination").find('input[name="paged"]').val(1);
            ajax_proposal_load();
        });

        project_dashboard.find("input.proposal-search-control").on("input", function () {
            $(".felan-proposal-pagination").find('input[name="paged"]').val(1);
            ajax_proposal_load();
        });

		function ajax_proposal_load(item_id = "", action_click = "") {
            var paged = 1;
            var height = project_dashboard.find("#project-dashboard").height();
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
            paged = $(".felan-proposal-pagination").find('input[name="paged"]').val();

            $.ajax({
                dataType: "json",
                url: ajax_url,
                data: {
                    action: "felan_filter_employer_proposal",
                    item_amount: item_amount,
                    paged: paged,
                    project_search: project_search,
                    project_status: project_status,
                    project_sort_by: project_sort_by,
                    item_id: item_id,
                    action_click: action_click,
                },
                beforeSend: function () {
                    project_dashboard
                        .find(".felan-loading-effect")
                        .addClass("loading")
                        .fadeIn();
                    project_dashboard.find("#project-dashboard").height(height);
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
                            .find("#my-project tbody")
                            .fadeOut("fast", function () {
                                project_dashboard
                                    .find("#my-project tbody")
                                    .html(data.project_html);
                                project_dashboard.find("#my-project tbody").fadeIn(300);
                            });
                        project_dashboard.find("#project-dashboard").css("height", "auto");

                        if(data.withdraw_total_price !== ''){
                            $('.site-header .role .price').text('(' + data.withdraw_total_price + ')');
                        };
                    } else {
                        project_dashboard
                            .find("#my-project tbody")
                            .html('<td><span class="not-project">' + not_project + "</span></td>");
                    }
                    project_dashboard
                        .find(".felan-loading-effect")
                        .removeClass("loading")
                        .fadeOut();

                    $('body').on("click", ".project-number-applicant", function (e) {
                        e.preventDefault();
                        var applicant = $(this).attr('href');
                        $(applicant).slideToggle();
                        $(this).find('i').toggleClass('felan-rotate-up');
                    });
                },
            });
        }

		$("body").on("click", ".felan-proposal-pagination a.page-numbers", function (e) {
            e.preventDefault();
            $(".felan-proposal-pagination li .page-numbers").removeClass("current");
            $(this).addClass("current");
            var paged = $(this).text();
            var current_page = 1;
            if (
                project_dashboard
                    .find(".felan-proposal-pagination")
                    .find('input[name="paged"]')
                    .val()
            ) {
                current_page = $(".felan-proposal-pagination").find('input[name="paged"]').val();
            }
            if ($(this).hasClass("next")) {
                paged = parseInt(current_page) + 1;
            }
            if ($(this).hasClass("prev")) {
                paged = parseInt(current_page) - 1;
            }
            project_dashboard
                .find(".felan-proposal-pagination")
                .find('input[name="paged"]')
                .val(paged);

            ajax_proposal_load();
        });

        $("body").on("click", ".project-control .btn-mark-featured", function (e) {
            e.preventDefault();
            var item_id = $(this).attr("project-id");
            ajax_load(item_id, "mark-featured");
        });

        $("body").on("click", ".project-control .btn-show", function (e) {
            e.preventDefault();
            var item_id = $(this).attr("project-id");
            ajax_load(item_id, "show");
        });

        $("body").on("click", ".project-control .btn-mark-filled", function (e) {
            e.preventDefault();
            var item_id = $(this).attr("project-id");
            ajax_load(item_id, "mark-filled");
        });

        $("body").on("click", ".project-control .btn-pause", function (e) {
            e.preventDefault();
            var item_id = $(this).attr("project-id");
            ajax_load(item_id, "pause");
        });

        $("body").on("click", ".project-control .btn-extend", function (e) {
            e.preventDefault();
            var item_id = $(this).attr("project-id");
            ajax_load(item_id, "extend");
        });

        $("body").on("click", ".felan-pagination a.page-numbers", function (e) {
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
                current_page = $(".felan-pagination").find('input[name="paged"]').val();
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

        function ajax_load(item_id = "", action_click = "") {
            var paged = 1;
            var height = project_dashboard.find("#project-dashboard").height();
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
            paged = $(".felan-pagination").find('input[name="paged"]').val();

            $.ajax({
                dataType: "json",
                url: ajax_url,
                data: {
                    action: "felan_filter_my_project",
                    item_amount: item_amount,
                    paged: paged,
                    project_search: project_search,
                    project_status: project_status,
                    project_sort_by: project_sort_by,
                    item_id: item_id,
                    action_click: action_click,
                },
                beforeSend: function () {
                    project_dashboard
                        .find(".felan-loading-effect")
                        .addClass("loading")
                        .fadeIn();
                    project_dashboard.find("#project-dashboard").height(height);
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
                            .find("#my-project tbody")
                            .fadeOut("fast", function () {
                                project_dashboard
                                    .find("#my-project tbody")
                                    .html(data.project_html);
                                project_dashboard.find("#my-project tbody").fadeIn(300);
                            });
                        project_dashboard.find("#project-dashboard").css("height", "auto");

                        if(data.withdraw_total_price !== ''){
                            $('.site-header .role .price').text('(' + data.withdraw_total_price + ')');
                        };
                    } else {
                        project_dashboard
                            .find("#my-project tbody")
                            .html('<td><span class="not-project">' + not_project + "</span></td>");
                    }
                    project_dashboard
                        .find(".felan-loading-effect")
                        .removeClass("loading")
                        .fadeOut();

                    $('body').on("click", ".project-number-applicant", function (e) {
                        e.preventDefault();
                        var applicant = $(this).attr('href');
                        $(applicant).slideToggle();
                        $(this).find('i').toggleClass('felan-rotate-up');
                    });
                },
            });
        }

        //Review
        $("body").on("click", ".btn-action-view",
            function (e) {
                e.preventDefault();
                var freelancer_id = $(this).attr('freelancer-id');

                $.ajax({
                    type: "POST",
                    url: ajax_url,
                    dataType: "json",
                    data: {
                        action: "felan_freelancer_view_review",
                        freelancer_id: freelancer_id,
                    },
                    beforeSend: function () {

                    },
                    success: function (response) {
                        if (response.html_review) {
                            $('.reviewForm .content-popup-review').html(response.html_review);
                            $('.reviewForm input[name="freelancer_id"]').val(freelancer_id);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error: " + error);
                    }
                });

            }
        );
    });
})(jQuery);
