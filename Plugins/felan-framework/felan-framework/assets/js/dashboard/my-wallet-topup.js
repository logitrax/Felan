var WALLET_TOPUP = WALLET_TOPUP || {};
(function ($) {
    "use strict";
    var ajax_url = felan_wallet_topup_vars.ajax_url,
        not_wallet = felan_wallet_topup_vars.not_wallet,
        wallet_topup = $("#tab-topup");

    WALLET_TOPUP = {
        init: function () {
            this.wallet_topup();
        },

        wallet_topup: function () {
            wallet_topup
                .find(".select-pagination")
                .change(function () {
                    var number = "";
                    $(".select-pagination option:selected").each(function () {
                        number += $(this).val() + " ";
                    });
                    $(this).attr("value");
                })
                .trigger("change");

            wallet_topup.find("select.search-control").on("change", function () {
                $(".felan-pagination").find('input[name="paged"]').val(1);
                ajax_load();
            });

            wallet_topup.find("input.search-control").on("input", function () {
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

            $("body").on("click", "#tab-topup .felan-pagination a.page-numbers", function (e) {
                e.preventDefault();
                $("#tab-topup .felan-pagination li .page-numbers").removeClass("current");
                $(this).addClass("current");
                var paged = $(this).text();
                var current_page = 1;
                if (
                    wallet_topup.find(".felan-pagination").find('input[name="paged"]').val()
                ) {
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
                wallet_topup
                    .find(".felan-pagination")
                    .find('input[name="paged"]')
                    .val(paged);

                ajax_load();
            });

            var paged = 1;
            wallet_topup.find(".select-pagination").attr("data-value", paged);

            function ajax_load() {
                var paged = 1;
                var height = wallet_topup.find("#wallet-topup").height();
                var wallet_status = wallet_topup
                        .find('select[name="wallet_status"]')
                        .val(),
                    wallet_method = wallet_topup.find('select[name="wallet_method"]').val(),
                    item_amount = wallet_topup.find('select[name="item_amount"]').val(),
                    wallet_sort_by = wallet_topup
                        .find('select[name="wallet_sort_by"]')
                        .val();
                paged = wallet_topup.find(".felan-pagination").find('input[name="paged"]').val();

                $.ajax({
                    dataType: "json",
                    url: ajax_url,
                    data: {
                        action: "felan_my_wallet_topup",
                        item_amount: item_amount,
                        paged: paged,
                        wallet_status: wallet_status,
                        wallet_method: wallet_method,
                        wallet_sort_by: wallet_sort_by,
                    },
                    beforeSend: function () {
                        wallet_topup
                            .find(".felan-loading-effect")
                            .addClass("loading")
                            .fadeIn();
                        wallet_topup.find("#wallet-topup").height(height);
                    },
                    success: function (data) {
                        if (data.success === true) {
                            var $items_pagination = wallet_topup.find(".items-pagination"),
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
                            wallet_topup.find(".num-first").text(value_first);
                            wallet_topup.find(".num-last").text(value_last);

                            if (max_number > select_item) {
                                $items_pagination.closest(".pagination-dashboard").show();
                                $items_pagination.find(".num-total").html(data.total_post);
                            } else {
                                $items_pagination.closest(".pagination-dashboard").hide();
                            }

                            wallet_topup.find(".pagination").html(data.pagination);
                            wallet_topup.find("#wallet-topup tbody").fadeOut("fast", function () {
                                wallet_topup.find("#wallet-topup tbody").html(data.wallet_html);
                                wallet_topup.find("#wallet-topup tbody").fadeIn(300);
                            });
                            wallet_topup.find("#wallet-topup").css("height", "auto");
                        } else {
                            wallet_topup
                                .find("#wallet-topup tbody")
                                .html('<span class="not-service">' + not_wallet + "</span>");
                        }
                        wallet_topup
                            .find(".felan-loading-effect")
                            .removeClass("loading")
                            .fadeOut();
                    },
                });
            }
        },
    };

    $(document).ready(function () {
        WALLET_TOPUP.init();
    });
})(jQuery);
