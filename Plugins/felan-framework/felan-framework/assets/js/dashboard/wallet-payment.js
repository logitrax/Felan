var FELAN_STRIPE = FELAN_STRIPE || {};
(function ($) {
    "use strict";

    FELAN_STRIPE = {
        setupForm: function () {
            var self = this,
                $form = $(".felan-wallet-stripe-form");
            if ($form.length === 0) return;
            var formId = $form.attr("id");
            // Set formData array index of the current form ID to match the localized data passed over for form settings.
            var formData = felan_stripe_vars[formId];
            // Variable to hold the Stripe configuration.
            var stripeHandler = null;
            var $submitBtn = $form.find(".felan-stripe-button");

            if ($submitBtn.length) {
                stripeHandler = StripeCheckout.configure({
                    // Key param MUST be sent hfelan instead of stripeHandler.open().
                    key: formData.key,
                    locale: "auto",
                    token: function (token, args) {
                        $("<input>")
                            .attr({
                                type: "hidden",
                                name: "stripeToken",
                                value: token.id,
                            })
                            .appendTo($form);

                        $("<input>")
                            .attr({
                                type: "hidden",
                                name: "stripeTokenType",
                                value: token.type,
                            })
                            .appendTo($form);

                        if (token.email) {
                            $("<input>")
                                .attr({
                                    type: "hidden",
                                    name: "stripeEmail",
                                    value: token.email,
                                })
                                .appendTo($form);
                        }
                        $form.submit();
                    },
                });

                $submitBtn.on("click", function (event) {
                    event.preventDefault();
                    stripeHandler.open(formData.params);
                });
            }

            // Close Checkout on page navigation:
            window.addEventListener("popstate", function () {
                if (stripeHandler != null) {
                    stripeHandler.close();
                }
            });
        },
    };

    $(document).ready(function () {
        //Click payment method
        const walletTopup = $('.felan-wallet-topup');
        let firstPayment = walletTopup.find('.payment-inner .title:first');

        firstPayment.addClass('active');
        walletTopup.find('input[name="payment_method"]').val(firstPayment.data('payment'));
        walletTopup.find('.payment-inner .title').on('click', function() {
            let selectedPayment = $(this).data('payment');

            walletTopup.find('.payment-inner .title').removeClass('active');

            $(this).addClass('active');
            walletTopup.find('input[name="payment_method"]').val(selectedPayment);
        });

        if (typeof felan_payment_vars !== "undefined") {
            var ajax_url = felan_template_vars.ajax_url;

            $(document).on("click", "#felan_payment_wallet", function () {
                var wallet_topup = $(".felan-wallet-topup");
                var payment_method = wallet_topup.find("input[name='payment_method']").val();
                var wallet_price = wallet_topup.find("input[name='wallet_price']").val();
                var user_role = wallet_topup.find("input[name='user_role']").val();
                var security_payment = $("#felan_wallet_security_payment").val();

                if (payment_method == "paypal") {
                    felan_paypal_payment_wallet_addons(user_role,wallet_price,security_payment);
                } else if (payment_method == "stripe") {
                    felan_stripe_wallet_addons(user_role,wallet_price,security_payment);
                } else if (payment_method == "wire_transfer") {
                    felan_wire_transfer_wallet_addons(user_role,wallet_price,security_payment);
                } else if (payment_method == "woocheckout") {
                    felan_woocommerce_payment_wallet_addons(user_role,wallet_price,security_payment);
                } else if (payment_method == "razor") {
                    felan_razor_payment_wallet_addons(user_role,wallet_price,security_payment);
                }
            });

            var felan_stripe_wallet_addons = function (user_role,wallet_price,security_payment) {
                var $stripe_form = $('.felan-wallet-stripe-form');
                var $form_popup = $('#form-wallet-topup');
                $form_popup.find(".felan-message-error").text("");

                $.ajax({
                    type: "POST",
                    url: ajax_url,
                    data: {
                        action: "felan_stripe_payment_wallet_addons",
                        user_role: user_role,
                        wallet_price: wallet_price,
                        felan_wallet_security_payment: security_payment,
                    },
                    beforeSend: function () {
                        $("#felan_payment_wallet").append(
                            '<div class="felan-loading-effect"><span class="felan-dual-ring"></span></div>'
                        );
                    },
                    success: function (data) {
                        if (data.success && data.data.script) {
                            $("body").append(data.data.script);
                            FELAN_STRIPE.setupForm();
                            $("#felan_stripe_wallet_addons button").trigger("click");
                            $stripe_form.find('input[name="stripe_wallet_price"]').val(data.data.wallet_price);
                            $stripe_form.find('input[name="stripe_user_role"]').val(data.data.user_role);
                            $form_popup.css({opacity: "0", visibility: "hidden"});
                        } else {
                            $form_popup.find(".felan-message-error").text(data.message);
                        }
                        $("#felan_payment_wallet .felan-loading-effect").remove();
                    },
                });
            };

            var felan_paypal_payment_wallet_addons = function (user_role,wallet_price,security_payment){
                $('#form-wallet-topup').find(".felan-message-error").text("");
                $.ajax({
                    type: "POST",
                    url: ajax_url,
                    data: {
                        action: "felan_paypal_payment_wallet_addons",
                        user_role: user_role,
                        wallet_price: wallet_price,
                        felan_wallet_security_payment: security_payment,
                    },
                    beforeSend: function () {
                        $("#felan_payment_wallet").append(
                            '<div class="felan-loading-effect"><span class="felan-dual-ring"></span></div>'
                        );
                    },
                    success: function (data) {
                        if (data.success) {
                            window.location.href = data.redirect_url;
                        } else {
                            $('#form-wallet-topup').find(".felan-message-error").text(data.message);
                        }
                        $("#felan_payment_wallet").find(".felan-loading-effect").remove();
                    },
                });
            };

            var felan_wire_transfer_wallet_addons = function (user_role,wallet_price,security_payment) {
                $('#form-wallet-topup').find(".felan-message-error").text("");
                $.ajax({
                    type: "POST",
                    url: ajax_url,
                    data: {
                        action: "felan_wire_transfer_wallet_addons",
                        user_role: user_role,
                        wallet_price: wallet_price,
                        felan_wallet_security_payment: security_payment,
                    },
                    beforeSend: function () {
                        $("#felan_payment_wallet").append(
                            '<div class="felan-loading-effect"><span class="felan-dual-ring"></span></div>'
                        );
                    },
                    success: function (data) {
                        if (data.success) {
                            window.location.href = data.redirect_url;
                        } else {
                            $('#form-wallet-topup').find(".felan-message-error").text(data.message);
                        }
                        $("#felan_payment_wallet").find(".felan-loading-effect").remove();
                    },
                });
            };

            var felan_woocommerce_payment_wallet_addons = function (user_role,wallet_price,security_payment) {
                $('#form-wallet-topup').find(".felan-message-error").text("");
                $.ajax({
                    type: "POST",
                    url: ajax_url,
                    data: {
                        action: "felan_woocommerce_payment_wallet_addons",
                        user_role: user_role,
                        wallet_price: wallet_price,
                        felan_wallet_security_payment: security_payment,
                    },
                    beforeSend: function () {
                        $("#felan_payment_wallet").append(
                            '<div class="felan-loading-effect"><span class="felan-dual-ring"></span></div>'
                        );
                    },
                    success: function (data) {
                        if (data.success) {
                            window.location.href = data.redirect_url;
                        } else {
                            $('#form-wallet-topup').find(".felan-message-error").text(data.message);
                        }
                        $("#felan_payment_wallet").find(".felan-loading-effect").remove();
                    },
                });
            };

            var setDisabled = function(id, state) {
                if (typeof state === 'undefined') {
                    state = true;
                }
                var elem = document.getElementById(id);
                if (state === false) {
                    elem.removeAttribute('disabled');
                } else {
                    elem.setAttribute('disabled', state);
                }
            };

            function felan_razor_payment_wallet_addons (user_role,wallet_price,security_payment) {
                var $form_popup = $('#form-wallet-topup');
                $form_popup.find(".felan-message-error").text("");

                $.ajax({
                    type: "POST",
                    url: ajax_url,
                    data: {
                        action: "felan_razor_wallet_create_order",
                        user_role: user_role,
                        wallet_price: wallet_price,
                        felan_wallet_security_payment: security_payment,
                    },
                    beforeSend: function () {
                        $("#felan_payment_wallet").append(
                            '<div class="felan-loading-effect"><span class="felan-dual-ring"></span></div>'
                        );
                    },
                    success: function (order) {
                        console.log(order.data);
                        if (order.success) {
                            order = order.data;
                            // Payment was closed without handler getting called
                            order.modal = {
                                ondismiss: function() {
                                    setDisabled('felan_payment_wallet', false);
                                },
                            };

                            order.handler = function(payment) {
                                console.log('payment', payment);
                                document.getElementById('razorpay_payment_id').value =
                                    payment.razorpay_payment_id;
                                document.getElementById('razorpay_signature').value =
                                    payment.razorpay_signature;
                                // document.razorpayform.submit();

                                var form_data = $('#felan_razor_paymentform').serializeArray();
                                $.ajax({
                                    url: ajax_url,
                                    data: {
                                        action: "felan_razor_wallet_verify",
                                        razorpay_payment_id: $( '#razorpay_payment_id' ).val(),
                                        razorpay_order_id: order.order_id,
                                        razorpay_signature: $( '#razorpay_signature' ).val(),
                                        user_role: user_role,
                                    },
                                    type: 'POST',
                                    success: function(response){
                                        if (response) {
                                            window.location.href = response
                                        }
                                    }
                                });
                            };
                            openCheckout(order);
                            $form_popup.css({opacity: "0", visibility: "hidden"});
                        } else {
                            $form_popup.find(".felan-message-error").text(order.message);
                        }
                        $("#felan_payment_wallet .felan-loading-effect").remove();
                    },
                });
            }

            // global method
            function openCheckout(order) {
                var razorpayCheckout = new Razorpay(order);
                razorpayCheckout.open();
            }
        }
    });
})(jQuery);
