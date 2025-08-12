<?php
if (!defined('ABSPATH')) {
	exit;
}

use Razorpay\Api\Api;
use Razorpay\Api\Errors;
if (!class_exists('Felan_Payment')) {
	/**
	 * Class Felan_Payment
	 */
	class Felan_Payment
	{
		protected $felan_invoice;
		protected $felan_package;
		protected $felan_trans_log;

		/**
		 * Construct
		 */
		public function __construct()
		{
			$this->felan_package = new Felan_Package();
			$this->felan_invoice = new Felan_Invoice();
			$this->felan_trans_log = new Felan_Trans_Log();

			add_action('wp_ajax_felan_razor_package_create_order', array($this, 'felan_razor_package_create_order'));
			add_action('wp_ajax_felan_razor_package_payment_verify', array($this, 'felan_razor_package_payment_verify'));

			add_action('woocommerce_new_order_item', [$this, 'felan_add_package_order_meta'], 10, 3);
			add_action('woocommerce_thankyou', [$this, 'felan_woocommerce_thankyou']);
		}

		public function felan_add_package_order_meta( $item_id, $cart_data, $item_order_id ) {
			if ( isset( $cart_data->legacy_values ) ) {
				if ( isset( $cart_data->legacy_values['felan_package_data'] ) && ! empty( $cart_data->legacy_values['felan_package_data'] ) ) {
					$felan_project_data = $cart_data->legacy_values['felan_package_data'];
					foreach( $felan_project_data as $key => $value ) {
						wc_add_order_item_meta( $item_id, 'felan_' . $key, $value);
					}
				}
			}
		}

		public function felan_woocommerce_thankyou($order_id) {
			$order    = wc_get_order( $order_id );
			$items    = $order->get_items();
			$all_meta = reset($items)->get_meta_data();

			$package_id = '';
			foreach ( $all_meta as $meta ) {
				switch ( $meta->key ) {
					case 'felan_package_id':
						$package_id = $meta->value;
						break;
				}
			}

			global $current_user, $wpdb;
			wp_get_current_user();

			$user_id        = $current_user->ID;
			$payment_method = 'Woocommerce';

			update_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_id', $package_id);
			$this->felan_package->insert_user_package($user_id, $package_id);

			// insert invoice
			$invoice_id = $this->felan_invoice->insert_invoice('Package', $package_id, $user_id, 0, $payment_method, 0);
		}

		public function felan_razor_payment_project_addons( $package_id ) {
			$payment_completed_link = felan_get_permalink( 'payment_completed' );
			?>

			<form name='razorpayform' id="felan_razor_paymentform" action="<?= $payment_completed_link ?>" method="POST">
				<input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
				<input type="hidden" name="razorpay_signature"  id="razorpay_signature" >
				<input type="hidden" name="rzp_QP_form_submit" value="1">
			</form>

			<?php
		}

		public function felan_razor_package_create_order() {
			require_once(FELAN_PLUGIN_DIR . 'includes/partials/project/razorpay-php/Razorpay.php');

			$orderID = mt_rand(0, mt_getrandmax());

			$payment_completed_link = felan_get_permalink( 'payment_completed' );
			$callback_url           = add_query_arg(
				[
					'payment_method'      => 4,
				],
				$payment_completed_link
			);

			$key_id_razor  = felan_get_option('razor_key_id');
			$key_secret    = felan_get_option('razor_key_secret');
			$currency_code = felan_get_option( 'currency_type_default', 'USD' );
			$order_id      = mt_rand( 0, mt_getrandmax() );

			$package_id    = $_REQUEST['package_id'];
			$package_price = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_price', true);
			$package_name  = get_the_title($package_id);

			$api = new Api( $key_id_razor, $key_secret );
			// Calls the helper function to create order data
			$data = $this->getOrderCreationData($orderID, $package_price);
			$api->order->create($data);
			try {
				$razorpayOrder = $api->order->create($data);
			} catch (Exception $e) {
				$razorpayArgs['error'] = 'Wordpress Error : ' . $e->getMessage();
			}
			if (isset($razorpayArgs['error']) === false) {
				$razorpayArgs = [
					'key'          => $key_id_razor,
					'name'         => get_bloginfo( 'name' ),
					// 'amount'       => $total_price,
					'currency'     => $currency_code,
					'description'  => wp_kses_post( sprintf( __('%s Package Payment', 'felan-framework' ), $package_name ) ),
					'order_id'     => $razorpayOrder['id'],
					'notes'        => [
						'quick_payment_order_id' => $order_id,
					],
					'callback_url' => $callback_url,
				];
			}


			$jsson = json_encode($razorpayArgs);
			echo $jsson;
			wp_die();
		}

		public function felan_razor_package_payment_verify() {
			$payment_completed_link = felan_get_permalink( 'payment_completed' );
			$callback_url           = add_query_arg(
				[
					'payment_method'      => 4,
					'razorpay_payment_id' => sanitize_text_field($_REQUEST['razorpay_payment_id']),
					'razorpay_order_id'   => $_REQUEST['razorpay_order_id'],
					'razorpay_signature'  => sanitize_text_field($_REQUEST['razorpay_signature']),
					'package_id'          => $_REQUEST['package_id'],
				],
				$payment_completed_link
			);

			echo $callback_url;
			wp_die();
		}

		/**
         * Creates orders API data RazorPay
         **/
        function getOrderCreationData($orderID, $amount) {
            $data = array(
                'receipt'         => $orderID,
                'amount'          => (int) round($amount * 100),
                'currency'        => felan_get_option( 'currency_type_default', 'USD' ),
                'payment_capture' => 0
            );

            return $data;
        }

		public function razor_payment_completed() {
			require_once(FELAN_PLUGIN_DIR . 'includes/partials/project/razorpay-php/Razorpay.php');

			$current_user = wp_get_current_user();
			$user_id = $current_user->ID;
			$user_email = $current_user->user_email;
			$payment_method = 'Razor';


			$key_id_razor  = felan_get_option('razor_key_id');
			$key_secret    = felan_get_option('razor_key_secret');
			$api          = new Api($key_id_razor, $key_secret);
			// $razorpayOrder = $api->order->fetch($_REQUEST['razorpay_order_id']);
			// $total_price   = $razorpayOrder->amount;
			// $total_price   = (float) ($total_price / 100);

			$attributes = $this->getPostAttributes();

            if (!empty($attributes)) {
                $success = true;

                try {
                    $api->utility->verifyPaymentSignature($attributes);
                } catch(Exception $e) {
					$success = false;
					$error = '<div class="alert alert-error" role="alert"><strong>' . esc_html__('Error!', 'felan-framework') . ' </strong> ' . $e->getMessage() . '</div>';
					echo wp_kses_post( $error );
                }

				if ( $success === true ) {
					$package_id = absint( wp_unslash( $_REQUEST['package_id'] ) );
					update_user_meta( $user_id, FELAN_METABOX_PREFIX . 'package_id', $package_id );
					$package_price = get_post_meta( $package_id, FELAN_METABOX_PREFIX . 'package_price', true );
					// if ( $payment_money != $package_price * 100 ) {
					// 	wp_die( 'No joke' );
					// 	return;
					// }
					$this->felan_package->insert_user_package( $user_id, $package_id );
					$this->felan_invoice->insert_invoice( 'Package', $package_id, $user_id, 0, $payment_method, 1, $_REQUEST['razorpay_order_id'], $user_id );
					$args = array();
					felan_send_email( $user_email, 'mail_activated_package', $args );
				} else {
					$error = '<div class="alert alert-error" role="alert">' . wp_kses_post(__('<strong>Error!</strong> Transaction failed', 'felan-framework')) . '</div>';
					echo wp_kses_post( $error );
				}

            }

		}

		protected function getPostAttributes() {
            if (isset($_REQUEST['razorpay_payment_id'])) {
                return array(
                    'razorpay_payment_id' => sanitize_text_field($_REQUEST['razorpay_payment_id']),
                    'razorpay_order_id'   => $_REQUEST['razorpay_order_id'],
                    'razorpay_signature'  => sanitize_text_field($_REQUEST['razorpay_signature'])
                );
            }

            return array();
        }

		/**
		 * Payment package by stripe
		 * @param $package_id
		 */
		public function stripe_payment_per_package($package_id)
		{
			require_once(FELAN_PLUGIN_DIR . 'includes/partials/payment/stripe-php/init.php');
			$stripe_secret_key = felan_get_option('stripe_secret_key');
			$stripe_publishable_key = felan_get_option('stripe_publishable_key');

			$current_user = wp_get_current_user();

			$user_id = $current_user->ID;
			$user_email = get_the_author_meta('user_email', $user_id);

			$stripe = array(
				"secret_key" => $stripe_secret_key,
				"publishable_key" => $stripe_publishable_key
			);

			\MyStripe\Stripe::setApiKey($stripe['secret_key']);
			$package_price = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_price', true);
			$package_name = get_the_title($package_id);
			//update_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_id', $package_id);


			$currency_code = felan_get_option('currency_type_default', 'USD');
			$package_price = $package_price * 100;
			$payment_completed_link = felan_get_permalink('payment_completed');
			$stripe_processor_link = add_query_arg(array('payment_method' => 2), $payment_completed_link);
			wp_enqueue_script('stripe-checkout');
			wp_localize_script('stripe-checkout', 'felan_stripe_vars', array(
				'felan_stripe_per_package' => array(
					'key' => $stripe_publishable_key,
					'params' => array(
						'amount' => $package_price,
						'email' => $user_email,
						'currency' => $currency_code,
						'zipCode' => true,
						'billingAddress' => true,
						'name' => esc_html__('Pay with Credit Card', 'felan-framework'),
						'description' => wp_kses_post(sprintf(__('%s Package Payment', 'felan-framework'), $package_name))
					)
				)
			));
		?>
			<form class="felan-stripe-form" action="<?php echo esc_url($stripe_processor_link) ?>" method="post" id="felan_stripe_per_package">
				<button class="felan-stripe-button" style="display: none !important;"></button>
				<input type="hidden" id="package_id" name="package_id" value="<?php echo esc_attr($package_id) ?>">
				<input type="hidden" id="payment_money" name="payment_money" value="<?php echo esc_attr($package_price) ?>">
			</form>
		<?php

		}

		private function get_paypal_access_token($url, $postArgs)
		{
			$client_id = felan_get_option('paypal_client_id');
			$secret_key = felan_get_option('paypal_client_secret_key');

			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_USERPWD, $client_id . ":" . $secret_key);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $postArgs);
			$response = curl_exec($curl);
			if (empty($response)) {
				die(curl_error($curl));
				curl_close($curl);
			} else {
				$info = curl_getinfo($curl);
				curl_close($curl);
				if ($info['http_code'] != 200 && $info['http_code'] != 201) {
					echo "Received error: " . $info['http_code'] . "\n";
					echo "Raw response:" . $response . "\n";
					die();
				}
			}
			$response = json_decode($response);
			return $response->access_token;
		}

		private function execute_paypal_request($url, $jsonData, $access_token)
		{
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array(
				'Authorization: Bearer ' . $access_token,
				'Accept: application/json',
				'Content-Type: application/json'
			));

			curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
			$response = curl_exec($curl);
			if (empty($response)) {
				die(curl_error($curl));
				curl_close($curl);
			} else {
				$info = curl_getinfo($curl);
				curl_close($curl);
				if ($info['http_code'] != 200 && $info['http_code'] != 201) {
					echo "Received error: " . $info['http_code'] . "\n";
					echo "Raw response:" . $response . "\n";
					die();
				}
			}
			$jsonResponse = json_decode($response, TRUE);
			return $jsonResponse;
		}

		/**
		 * Payment per package by Paypal
		 */
		public function paypal_payment_per_package_ajax()
		{
			check_ajax_referer('felan_payment_ajax_nonce', 'felan_security_payment');
			global $current_user;
			wp_get_current_user();
			$user_id = $current_user->ID;

			$blogInfo = esc_url(home_url());

			$package_id = $_POST['package_id'];
			$package_id = intval($package_id);
			$package_price = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_price', true);
			$package_name = get_the_title($package_id);

			if (empty($package_price) && empty($package_id)) {
				exit();
			}
			$currency = felan_get_option('currency_type_default', 'USD');
			$payment_description = $package_name . ' ' . esc_html__('Membership payment on ', 'felan-framework') . $blogInfo;
			$is_paypal_live = felan_get_option('paypal_api');
			$host = 'https://api.sandbox.paypal.com';
			if ($is_paypal_live == 'live') {
				$host = 'https://api.paypal.com';
			}
			$url = $host . '/v1/oauth2/token';
			$postArgs = 'grant_type=client_credentials';
			$access_token = $this->get_paypal_access_token($url, $postArgs);
			$url = $host . '/v1/payments/payment';
			$payment_completed_link = felan_get_permalink('payment_completed');
			$return_url = add_query_arg(array('payment_method' => 1), $payment_completed_link);
			$dash_profile_link = felan_get_permalink('dashboard');
			update_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_id', $package_id);

			$payment = array(
				'intent' => 'sale',
				"redirect_urls" => array(
					"return_url" => $return_url,
					"cancel_url" => $dash_profile_link
				),
				'payer' => array("payment_method" => "paypal"),
			);


			$payment['transactions'][0] = array(
				'amount' => array(
					'total' => $package_price,
					'currency' => $currency,
					'details' => array(
						'subtotal' => $package_price,
						'tax' => '0.00',
						'shipping' => '0.00'
					)
				),
				'description' => $payment_description
			);

			$payment['transactions'][0]['item_list']['items'][] = array(
				'quantity' => '1',
				'name' => esc_html__('Payment Package', 'felan-framework'),
				'price' => $package_price,
				'currency' => $currency,
				'sku' => $package_name . ' ' . esc_html__('Payment Package', 'felan-framework'),
			);

			$jsonEncode = json_encode($payment);
			$json_response = $this->execute_paypal_request($url, $jsonEncode, $access_token);
			$payment_approval_url = $payment_execute_url = '';
			foreach ($json_response['links'] as $link) {
				if ($link['rel'] == 'execute') {
					$payment_execute_url = $link['href'];
				} else if ($link['rel'] == 'approval_url') {
					$payment_approval_url = $link['href'];
				}
			}
			$output['payment_execute_url'] = $payment_execute_url;
			$output['access_token'] = $access_token;
			$output['package_id'] = $package_id;
			update_user_meta($user_id, FELAN_METABOX_PREFIX . 'paypal_transfer', $output);

			print $payment_approval_url;
			wp_die();
		}

		/**
		 * Payment per package by wire transfer
		 */
		public function wire_transfer_per_package_ajax()
		{
			check_ajax_referer('felan_payment_ajax_nonce', 'felan_security_payment');
			global $current_user;
			$current_user = wp_get_current_user();

			if (!is_user_logged_in()) {
				exit('No Login');
			}
			$user_id = $current_user->ID;
			$user_email = $current_user->user_email;
			$admin_email = get_bloginfo('admin_email');
			$package_id = $_POST['package_id'];
			$package_id = intval($package_id);
			$total_price = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_price', true);
			$total_price = felan_get_format_money($total_price);
			$payment_method = 'Wire_Transfer';
			update_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_id', $package_id);
			$this->felan_package->insert_user_package($user_id, $package_id);

			// insert invoice
			$invoice_id = $this->felan_invoice->insert_invoice('Package', $package_id, $user_id, 0, $payment_method, 0);
			$args = array(
				'invoice_no' => $invoice_id,
				'total_price' => $total_price
			);
			/*
             * Send email
             * */
			felan_send_email($user_email, 'mail_new_wire_transfer', $args);
			felan_send_email($admin_email, 'admin_mail_new_wire_transfer', $args);
			$payment_completed_link = felan_get_permalink('payment_completed');

			$return_link = add_query_arg(array('payment_method' => 3, 'order_id' => $invoice_id), $payment_completed_link);
			print $return_link;
			wp_die();
		}

		/**
		 * Payment per package by Woocommerce
		 */
		public function woocommerce_payment_per_package_ajax()
		{
			check_ajax_referer('felan_payment_ajax_nonce', 'felan_security_payment');
			global $current_user, $wpdb;
			wp_get_current_user();

			$user_id            = $current_user->ID;
			$package_id         = $_POST['package_id'];
			$package_title      = get_the_title($package_id);
			$package_price      = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_price', true);
			$checkout_url       = wc_get_checkout_url();
			$payment_method = 'Woocommerce';

			$query = $wpdb->prepare(
				'SELECT ID FROM ' . $wpdb->posts . '
                WHERE post_title = %s
                AND post_type = \'product\'',
				$package_title
			);
			$wpdb->query($query);

			if ($wpdb->num_rows) {
				$product_id = $wpdb->get_var($query);

				$objProduct = wc_get_product( $product_id );
				$objProduct->set_price($package_price);
				$objProduct->set_regular_price($package_price);
				$objProduct->save();
			} else {
				$objProduct         = new WC_Product();

				$objProduct->set_name($package_title);
				$objProduct->set_price($package_price);
				$objProduct->set_status("");
				$objProduct->set_catalog_visibility('hidden');
				$objProduct->set_regular_price($package_price);
				$product_id = $objProduct->save();
			}

			$cart_data = [
				'package_id' => $package_id,
			];

			global $woocommerce;
			$woocommerce->cart->empty_cart();
			$woocommerce->cart->add_to_cart($product_id, 1, '', [], ['felan_package_data' => $cart_data]);

			$total_price = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_price', true);
			$total_price = felan_get_format_money($total_price);
			update_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_id', $package_id);

			$url = add_query_arg(array(
				'package_id' => esc_attr($package_id),
			), $checkout_url);

			print $url;
			wp_die();
		}

		/**
		 * Free package
		 */
		public function free_package_ajax()
		{
			check_ajax_referer('felan_payment_ajax_nonce', 'felan_security_payment');
			global $current_user;
			$user_id = $current_user->ID;
			$current_user = wp_get_current_user();
			$used_free_package = get_user_meta($user_id, 'used_free_package', true);
			if (!is_user_logged_in()) {
				exit('No Login');
			}
			if ($used_free_package === 'yes') {
				exit('Used');
			}
			$user_id = $current_user->ID;
			$package_id = isset($_POST['package_id']) ? absint(wp_unslash($_POST['package_id'])) : 0;
			$payment_method = 'Free_Package';
			// insert invoice
			$invoice_id = $this->felan_invoice->insert_invoice('Package', $package_id, $user_id, 0, $payment_method, 1);

			$this->felan_package->insert_user_package($user_id, $package_id);
			update_user_meta($user_id, FELAN_METABOX_PREFIX . 'free_package', 'yes');
			update_user_meta($user_id, 'used_free_package', 'yes');
			$payment_completed_link = felan_get_permalink('payment_completed');
			$return_link = add_query_arg(array('payment_method' => 3, 'free_package' => $invoice_id), $payment_completed_link);
			echo esc_url_raw($return_link);
			wp_die();
		}

		/**
		 * stripe_payment_completed
		 */
		public function stripe_payment_completed()
		{
			require_once(FELAN_PLUGIN_DIR . 'includes/partials/payment/stripe-php/init.php');
			$paid_submission_type = felan_get_option('paid_submission_type');
			$current_user = wp_get_current_user();
			$user_id = $current_user->ID;
			$user_email = $current_user->user_email;
			$admin_email = get_bloginfo('admin_email');
			$currency_code = felan_get_option('currency_type_default', 'USD');
			$payment_method = 'Stripe';
			$stripe_secret_key = felan_get_option('stripe_secret_key');
			$stripe_publishable_key = felan_get_option('stripe_publishable_key');
			$stripe = array(
				"secret_key" => $stripe_secret_key,
				"publishable_key" => $stripe_publishable_key
			);
			\MyStripe\Stripe::setApiKey($stripe['secret_key']);
			$stripeEmail = '';
			if (is_email($_POST['stripeEmail'])) {
				$stripeEmail = sanitize_email(wp_unslash($_POST['stripeEmail']));
			} else {
				wp_die('None Mail');
			}

			if (isset($_POST['jobs_id']) && !is_numeric($_POST['jobs_id'])) {
				die();
			}

			if (isset($_POST['package_id']) && !is_numeric($_POST['package_id'])) {
				die();
			}

			if (isset($_POST['payment_money']) && !is_numeric($_POST['payment_money'])) {
				die();
			}

			if (isset($_POST['payment_for']) && !is_numeric($_POST['payment_for'])) {
				die();
			}
			$payment_for = 0;
			$paymentId = 0;
			if (isset($_POST['payment_for'])) {
				$payment_for = absint(wp_unslash($_POST['payment_for']));
			}
			try {
				$token = isset($_POST['stripeToken']) ? felan_clean(wp_unslash($_POST['stripeToken'])) : '';
				$payment_money = isset($_POST['payment_money']) ? absint(wp_unslash($_POST['payment_money'])) :  0;
				$customer = \MyStripe\Customer::create(array(
					"email" => $stripeEmail,
					"source" => $token
				));
				$charge = \MyStripe\Charge::create(array(
					"amount" => $payment_money,
					'customer' => $customer->id,
					"currency" => $currency_code,
				));
				$payerId = $customer->id;
				if (isset($charge->id) && (!empty($charge->id))) {
					$paymentId = $charge->id;
				}
				$payment_Status = '';
				if (isset($charge->status) && (!empty($charge->status))) {
					$payment_Status = $charge->status;
				}

				if ($payment_Status == "succeeded") {
					if ($paid_submission_type == 'per_package') {
						//Payment Stripe package
                        $package_id = absint(wp_unslash($_POST['package_id']));
                        update_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_id', $package_id);
                        $package_price = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_price', true);
                        if ($payment_money != $package_price * 100) {
                            wp_die('No joke');
                            return;
                        }
                        $this->felan_package->insert_user_package($user_id, $package_id);
                        $this->felan_invoice->insert_invoice('Package', $package_id, $user_id, 0, $payment_method, 1, $paymentId, $payerId);
						$args = array();
						felan_send_email($user_email, 'mail_activated_package', $args);
					}
				} else {
					$message = esc_html__('Transaction failed', 'felan-framework');
					if ($paid_submission_type == 'per_listing') {
						//Payment Stripe listing
						$jobs_id = absint(wp_unslash($_POST['jobs_id']));

						if ($payment_for == 3) {
							$this->felan_trans_log->insert_trans_log('Upgrade_To_Featured', $jobs_id, $user_id, 3, $payment_method, 0, $paymentId, $payerId, 0, $message);
						} else {
							if ($payment_for == 2) {
								$this->felan_trans_log->insert_trans_log('Listing_With_Featured', $jobs_id, $user_id, 2, $payment_method, 0, $paymentId, $payerId, 0, $message);
							} else {
								$this->felan_trans_log->insert_trans_log('Listing', $jobs_id, $user_id, 1, $payment_method, 0, $paymentId, $payerId, 0, $message);
							}
						}
					} else if ($paid_submission_type == 'per_package') {
						//Payment Stripe package
						$package_id = absint(wp_unslash($_POST['package_id']));
						$this->felan_trans_log->insert_trans_log('Package', $package_id, $user_id, 0, $payment_method, 0, $paymentId, $payerId, 0, $message);
					}

					$error = '<div class="alert alert-error" role="alert">' . wp_kses_post(__('<strong>Error!</strong> Transaction failed', 'felan-framework')) . '</div>';
					echo wp_kses_post($error);
				}
			} catch (Exception $e) {
				$error = '<div class="alert alert-error" role="alert"><strong>' . esc_html__('Error!', 'felan-framework') . ' </strong> ' . $e->getMessage() . '</div>';
				echo wp_kses_post($error);
			}
		}

		/**
		 * paypal_payment_completed
		 */
		public function paypal_payment_completed()
		{
			global $current_user;
			wp_get_current_user();
			$user_id = $current_user->ID;
			$user_email = $current_user->user_email;
			$admin_email = get_bloginfo('admin_email');
			$allowed_html = array();
			$payment_method = 'Paypal';
			$paid_submission_type = felan_get_option('paid_submission_type', 'no');
			try {
				if (isset($_GET['token']) && isset($_GET['PayerID'])) {
					$payerId = wp_kses(felan_clean(wp_unslash($_GET['PayerID'])), $allowed_html);
					$paymentId = wp_kses(felan_clean(wp_unslash($_GET['paymentId'])), $allowed_html);
					$transfered_data = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'paypal_transfer', true);
					if (empty($transfered_data)) {
						return;
					}
					$payment_execute_url = $transfered_data['payment_execute_url'];
					$token = $transfered_data['access_token'];

					$payment_execute = array(
						'payer_id' => $payerId
					);
					$json = json_encode($payment_execute);
					$json_response = $this->execute_paypal_request($payment_execute_url, $json, $token);
					delete_user_meta($user_id, FELAN_METABOX_PREFIX . 'paypal_transfer');
					if ($json_response['state'] == 'approved') {
						if ($paid_submission_type == 'per_package') {
							$package_id = $transfered_data['package_id'];
							$this->felan_package->insert_user_package($user_id, $package_id);
							$this->felan_invoice->insert_invoice('Package', $package_id, $user_id, 0, $payment_method, 1, $paymentId, $payerId);
							$args = array();
							felan_send_email($user_email, 'mail_activated_package', $args);
						}
					} else {
						$message = esc_html__('Transaction failed', 'felan-framework');
						if ($paid_submission_type == 'per_listing') {
							$payment_for = $transfered_data['payment_for'];
							$jobs_id = $transfered_data['jobs_id'];
							if ($payment_for == 3) {
								$this->felan_trans_log->insert_trans_log('Upgrade_To_Featured', $jobs_id, $user_id, 3, $payment_method, 0, $paymentId, $payerId, 0, $message);
							} else {
								if ($payment_for == 2) {
									$this->felan_trans_log->insert_trans_log('Listing_With_Featured', $jobs_id, $user_id, 2, $payment_method, 0, $paymentId, $payerId, 0, $message);
								} else {
									$this->felan_trans_log->insert_trans_log('Listing', $jobs_id, $user_id, 1, $payment_method, 0, $paymentId, $payerId, 0, $message);
								}
							}
						} else if ($paid_submission_type == 'per_package') {
							$package_id = $transfered_data['package_id'];
							$this->felan_trans_log->insert_trans_log('Package', $package_id, $user_id, 0, $payment_method, 0, $paymentId, $payerId, 0, $message);
						}
						$error = '<div class="alert alert-error" role="alert">' . sprintf(__('<strong>Error!</strong> Transaction failed', 'felan-framework')) . '</div>';
						print $error;
					}
				}
			} catch (Exception $e) {
				$error = '<div class="alert alert-error" role="alert"><strong>Error!</strong> ' . $e->getMessage() . '</div>';
				print $error;
			}
		}
	}
}
