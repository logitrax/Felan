var FELAN_STRIPE = FELAN_STRIPE || {};
(function ($) {
  "use strict";

  FELAN_STRIPE = {
    init: function () {
      this.setupForm();
    },

    setupForm: function () {
      var self = this,
        $form = $(".felan-stripe-form");
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
    FELAN_STRIPE.init();

    var show_loading = function ($text) {
      if ($text == "undefined" || $text == "" || $text == null) {
        $text = loading_text;
      }
      var template = wp.template("felan-processing-template");
      $("body").append(template({ ico: "fa fa-spinner fa-spin", text: $text }));
    };

    if (typeof felan_payment_vars !== "undefined") {
      var ajax_url = felan_payment_vars.ajax_url;
      var processing_text = felan_payment_vars.processing_text;

      $("#felan_payment_package").on("click", function (event) {
        var payment_method = $(
          "input[name='felan_payment_method']:checked"
        ).val();
        var package_id = $("input[name='felan_package_id']").val();
        if (payment_method == "paypal") {
          felan_paypal_payment_per_package(package_id);
        } else if (payment_method == "stripe") {
          $("#felan_stripe_per_package button").trigger("click");
        } else if (payment_method == "wire_transfer") {
          felan_wire_transfer_per_package(package_id);
        } else if (payment_method == "woocheckout") {
          felan_woocommerce_payment_per_package(package_id);
        } else if (payment_method == "razor") {
			felan_razor_payment_package_addons(package_id);
		}
      });

      var felan_paypal_payment_per_package = function (package_id) {
        $.ajax({
          type: "POST",
          url: ajax_url,
          data: {
            action: "felan_paypal_payment_per_package_ajax",
            package_id: package_id,
            felan_security_payment: $("#felan_security_payment").val(),
          },
          beforeSend: function () {
            $("#felan_payment_package").append(
              '<div class="felan-loading-effect"><span class="felan-dual-ring"></span></div>'
            );
          },
          success: function (data) {
            window.location.href = data;
          },
        });
      };

      var felan_wire_transfer_per_package = function (package_id) {
        $.ajax({
          type: "POST",
          url: ajax_url,
          data: {
            action: "felan_wire_transfer_per_package_ajax",
            package_id: package_id,
            felan_security_payment: $("#felan_security_payment").val(),
          },
          beforeSend: function () {
            $("#felan_payment_package").append(
              '<div class="felan-loading-effect"><span class="felan-dual-ring"></span></div>'
            );
          },
          success: function (data) {
            window.location.href = data;
          },
        });
      };

      $("#felan_free_package").on("click", function () {
        var package_id = $("input[name='felan_package_id']").val();
        $.ajax({
          type: "POST",
          url: ajax_url,
          data: {
            action: "felan_free_package_ajax",
            package_id: package_id,
            felan_security_payment: $("#felan_security_payment").val(),
          },
          beforeSend: function () {
            $("#felan_free_package").append(
              '<div class="felan-loading-effect"><span class="felan-dual-ring"></span></div>'
            );
          },
          success: function (data) {
            window.location.href = data;
          },
        });
      });

      var felan_woocommerce_payment_per_package = function (package_id) {
        $.ajax({
          type: "POST",
          url: ajax_url,
          data: {
            action: "felan_woocommerce_payment_per_package_ajax",
            package_id: package_id,
            felan_security_payment: $("#felan_security_payment").val(),
          },
          beforeSend: function () {
            $("#felan_payment_package").append(
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

		function felan_razor_payment_package_addons (package_id) {
			$.ajax({
				type: "POST",
				url: ajax_url,
				data: {
					action: "felan_razor_package_create_order",
					package_id: package_id,
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
								action: "felan_razor_package_payment_verify",
								package_id: $( 'input[name="felan_package_id"]' ).val(),
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
