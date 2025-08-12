var FELAN_SERVICE_STRIPE = FELAN_SERVICE_STRIPE || {};
(function ($) {
  "use strict";

  FELAN_SERVICE_STRIPE = {
    init: function () {
      this.setupForm();
    },

    setupForm: function () {
      var self = this,
        $form = $(".felan-freelancer-stripe-form");
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
    FELAN_SERVICE_STRIPE.init();

    if (typeof felan_payment_vars !== "undefined") {
      var ajax_url = felan_payment_vars.ajax_url;
      var processing_text = felan_payment_vars.processing_text;

      $("#felan_payment_freelancer_package").on("click", function (event) {
        var payment_method = $(
          "input[name='felan_freelancer_payment_method']:checked"
        ).val();
        var freelancer_package_id = $(
          "input[name='felan_freelancer_package_id']"
        ).val();
        if (payment_method == "paypal") {
          felan_freelancer_paypal_payment_per_package(freelancer_package_id);
        } else if (payment_method == "stripe") {
          $("#felan_stripe_freelancer_per_package button").trigger("click");
        } else if (payment_method == "wire_transfer") {
          felan_freelancer_wire_transfer_per_package(freelancer_package_id);
        } else if (payment_method == "woocheckout") {
          felan_freelancer_woocommerce_payment_per_package(
            freelancer_package_id
          );
        } else if (payment_method == "razor") {
			felan_razor_payment_package_addons(freelancer_package_id);
		}
      });

      var felan_freelancer_paypal_payment_per_package = function (
        freelancer_package_id
      ) {
        $.ajax({
          type: "POST",
          url: ajax_url,
          data: {
            action: "felan_freelancer_paypal_payment_per_package_ajax",
            freelancer_package_id: freelancer_package_id,
            felan_freelancer_security_payment: $(
              "#felan_freelancer_security_payment"
            ).val(),
          },
          beforeSend: function () {
            $("#felan_payment_freelancer_package").append(
              '<div class="felan-loading-effect"><span class="felan-dual-ring"></span></div>'
            );
          },
          success: function (data) {
            window.location.href = data;
          },
        });
      };

      var felan_stripe_freelancer_per_package = function (
        freelancer_package_id
      ) {
        $.ajax({
          type: "POST",
          url: ajax_url,
          data: {
            action: "felan_freelancer_paypal_payment_per_package_ajax",
            freelancer_package_id: freelancer_package_id,
            felan_freelancer_security_payment: $(
              "#felan_freelancer_security_payment"
            ).val(),
          },
          beforeSend: function () {
            $("#felan_payment_freelancer_package").append(
              '<div class="felan-loading-effect"><span class="felan-dual-ring"></span></div>'
            );
          },
          success: function (data) {
            window.location.href = data;
          },
        });
      };

      var felan_freelancer_wire_transfer_per_package = function (
        freelancer_package_id
      ) {
        $.ajax({
          type: "POST",
          url: ajax_url,
          data: {
            action: "felan_freelancer_wire_transfer_per_package_ajax",
            freelancer_package_id: freelancer_package_id,
            felan_freelancer_security_payment: $(
              "#felan_freelancer_security_payment"
            ).val(),
          },
          beforeSend: function () {
            $("#felan_payment_freelancer_package").append(
              '<div class="felan-loading-effect"><span class="felan-dual-ring"></span></div>'
            );
          },
          success: function (data) {
            window.location.href = data;
          },
        });
      };

      $("#felan_free_freelancer_package").on("click", function () {
        var freelancer_package_id = $(
          "input[name='felan_freelancer_package_id']"
        ).val();
        $.ajax({
          type: "POST",
          url: ajax_url,
          data: {
            action: "felan_freelancer_free_package_ajax",
            freelancer_package_id: freelancer_package_id,
            felan_freelancer_security_payment: $(
              "#felan_freelancer_security_payment"
            ).val(),
          },
          beforeSend: function () {
            $("#felan_payment_freelancer_package").append(
              '<div class="felan-loading-effect"><span class="felan-dual-ring"></span></div>'
            );
          },
          success: function (data) {
            window.location.href = data;
          },
        });
      });

      var felan_freelancer_woocommerce_payment_per_package = function (
        freelancer_package_id
      ) {
        $.ajax({
          type: "POST",
          url: ajax_url,
          data: {
            action: "felan_freelancer_woocommerce_payment_per_package_ajax",
            freelancer_package_id: freelancer_package_id,
            felan_freelancer_security_payment: $(
              "#felan_freelancer_security_payment"
            ).val(),
          },
          beforeSend: function () {
            $("#felan_payment_freelancer_package").append(
              '<div class="felan-loading-effect"><span class="felan-dual-ring"></span></div>'
            );
          },
          success: function (data) {
            window.location.href = data;
          },
        });
      };

	  	// var data = felan_payment_razor_vars;
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

		function felan_razor_payment_package_addons (freelancer_package_id) {
			$.ajax({
				type: "POST",
				url: ajax_url,
				data: {
					action: "freelancer_razor_package_create_order",
					freelancer_package_id: freelancer_package_id,
				},
				beforeSend: function () {
					$("#felan_payment_project").append(
						'<div class="felan-loading-effect"><span class="felan-dual-ring"></span></div>'
					);
				},
				success: function (order) {
					order = JSON.parse( order );
					// Payment was closed without handler getting called
					order.modal = {
						ondismiss: function() {
							setDisabled('felan_payment_project', false);
						},
					};

					order.handler = function(payment) {
						document.getElementById('razorpay_payment_id').value =
							payment.razorpay_payment_id;
						document.getElementById('razorpay_signature').value =
							payment.razorpay_signature;
						// document.razorpayform.submit();

						$.ajax({
							url: ajax_url,
							data: {
								action: "freelancer_razor_package_payment_verify",
								freelancer_package_id: $( 'input[name="felan_freelancer_package_id"]' ).val(),
								razorpay_payment_id: $( '#razorpay_payment_id' ).val(),
								razorpay_order_id: order.order_id,
								razorpay_signature: $( '#razorpay_signature' ).val(),
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
