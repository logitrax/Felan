(function ($) {
    "use strict";

    var project_dashboard = $(".felan-project-proposal");

    var ajax_url = felan_project_proposal_vars.ajax_url,
        form_view_reason = $("#form-project-view-reason"),
        not_project = felan_project_proposal_vars.not_project;

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

        $("body").on("click", ".btn-delivery", function (e) {
            e.preventDefault();
            var item_id = $(this).attr("item-id");
            ajax_load(item_id, "transferring");
        });

        $("body").on("click", ".btn-delete", function (e) {
            e.preventDefault();
            var item_id = $(this).attr("item-id");
            ajax_load(item_id, "delete");
        });

        $("body").on("click", ".btn-view-reason", function () {
            var content_refund = $(this).data("content-refund");
            form_view_reason.find(".content-refund-reason").text(content_refund);
        });

        $("body").on("click", ".btn-action-review",
            function () {
                var employer_id = $(this).attr("employer-id");
                $('input[name="company_id"]').val(employer_id);
            }
        );

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
            var height = project_dashboard
                .find("#freelancer-project-proposal")
                .height();
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
                    action: "felan_freelancer_proposal_project",
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
                    project_dashboard.find("#freelancer-project-proposal").height(height);
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
                            .find("#freelancer-project-proposal tbody")
                            .fadeOut("fast", function () {
                                project_dashboard
                                    .find("#freelancer-project-proposal tbody")
                                    .html(data.project_html);
                                project_dashboard
                                    .find("#freelancer-project-proposal tbody")
                                    .fadeIn(300);
                            });
                        project_dashboard
                            .find("#freelancer-project-proposal")
                            .css("height", "auto");
                    } else {
                        project_dashboard
                            .find("#freelancer-project-proposal tbody")
                            .html('<span class="not-project">' + not_project + "</span>");
                    }
                    project_dashboard
                        .find(".felan-loading-effect")
                        .removeClass("loading")
                        .fadeOut();
                },
            });
        }

        //Edit proposals
        $("body").on("click", ".btn-edit-proposals",
            function (e) {
                e.preventDefault();
                var project_id = $(this).data('post-current');
                var proposal_id = $(this).data("proposal-id");
                var $this = $(this);

                $.ajax({
                    type: "POST",
                    url: ajax_url,
                    dataType: "json",
                    data: {
                        action: "felan_freelancer_edit_proposals",
                        project_id: project_id,
                        proposal_id: proposal_id,
                    },
                    beforeSend: function () {},
                    success: function (response) {
                        if (response.html_form_proposals) {
                            $('#form-apply-project').html(response.html_form_proposals);
                        }
                        $('.felan-select2').select2();

                        var maximum_time = $this.data("maximum-time");
                        var author_id = $this.data("author-id");
                        var post_current = $this.data("post-current");
                        var proposal_id = $this.data("proposal-id");
                        var info_price = $this.data("info-price");
                        var info_hours = $this.data("info-hours");

                        function calculateValues() {
                            const $form_apply = $("#form-apply-project");
                            let proposalPrice = parseFloat($('#proposal_price').val()) || 0;
                            let proposalTime = parseFloat($('#proposal_time').val()) || 1;
                            let enableCommission = $('#enable_commission').val();
                            let commissionFee = enableCommission === '1' ? parseFloat($('#commission_fee').val()) || 0 : 0;
                            let amountAfterFee = proposalPrice - (proposalPrice * (commissionFee / 100));
                            let total = amountAfterFee * proposalTime;
                            let roundedProposalPrice = Math.round(proposalPrice * 100) / 100;
                            let roundedFee = Math.round(proposalPrice * (commissionFee / 100) * 100) / 100;
                            let roundedAmountAfterFee = Math.round(amountAfterFee * 100) / 100;
                            let roundedTotal = Math.round(total * 100) / 100;

                            $form_apply.find('.budget .number').text(roundedProposalPrice.toFixed(2));
                            $form_apply.find('.fee .number').text(roundedFee.toFixed(2));
                            $form_apply.find('.total-hours .number').text(roundedAmountAfterFee.toFixed(2));
                            $form_apply.find('.estimated-hours .number').text(proposalTime);
                            $form_apply.find('.total .number').text(roundedTotal.toFixed(2));
                        }

                        calculateValues();

                        $('#form-apply-project #proposal_price, #form-apply-project #proposal_time').on('input', calculateValues);
                        if (response.budget_show == 'fixed') {
                            $('#form-apply-project #proposal_time').closest('.form-group').hide();
                            $('#form-apply-project li.total-hours').hide();
                            $('#form-apply-project li.estimated-hours').hide();
                            $('#form-apply-project #proposal_fixed_time').closest('.form-group').show();
                            $('#form-apply-project #proposal_rate').show();
                        } else {
                            $('#form-apply-project #proposal_time').closest('.form-group').show();
                            $('#form-apply-project li.total-hours').show();
                            $('#form-apply-project li.estimated-hours').show();
                            $('#form-apply-project #proposal_fixed_time').closest('.form-group').hide();
                            $('#form-apply-project #proposal_rate').hide();
                        }

                        $('#form-apply-project .info-hours .number').html(info_hours);
                        $('#form-apply-project .info-budget .number').html(info_price);
                        $('input#project_maximum_time').val(maximum_time);
                        $('input#project_author_id').val(author_id);
                        $('input#project_post_current').val(post_current);
                        $('input#proposal_id').val(proposal_id);

                        $(".form-popup").each(function () {
                            var $form_popup = $(this);
                            var btn_popup = $form_popup.is("#form-apply-project") ? "#felan-apply-project" : null;
                            if (!btn_popup) return;
                            var $btn_close = $form_popup.find(".btn-close");
                            var $bg_overlay = $form_popup.find(".bg-overlay");
                            var $btn_cancel = $form_popup.find(".button-cancel");

                            function open_popup(e) {
                                e.preventDefault();
                                $form_popup.css({ opacity: "1", visibility: "unset" });
                            }

                            function close_popup(e) {
                                e.preventDefault();
                                $form_popup.css({ opacity: "0", visibility: "hidden" });
                            }

                            $("body").off("click", btn_popup).on("click", btn_popup, open_popup);
                            $bg_overlay.off("click").on("click", close_popup);
                            $btn_close.off("click").on("click", close_popup);
                            $btn_cancel.off("click").on("click", close_popup);
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error("Error: " + error);
                    }
                });

            }
        );
    });
})(jQuery);
