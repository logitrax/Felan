<?php
if (!defined('ABSPATH')) {
	exit;
}

use Razorpay\Api\Api;
use Razorpay\Api\Errors;
if (!class_exists('Felan_Project_Payment')) {
	/**
	 * Class Felan_Project_Payment
	 */
	class Felan_Project_Payment
	{
		protected $felan_order;

		/**
		 * Construct
		 */
		public function __construct()
		{
			$this->felan_order = new Felan_Project_Order();
			add_action('wp_ajax_felan_razor_payment_create_order', array($this, 'felan_razor_payment_create_order'));
			add_action('wp_ajax_felan_razor_payment_verify', array($this, 'felan_razor_payment_verify'));

			add_action('woocommerce_new_order_item', [$this, 'felan_add_project_order_meta'], 10, 3);
			add_action('woocommerce_thankyou', [$this, 'felan_woocommerce_thankyou']);
		}

		public function felan_add_project_order_meta( $item_id, $cart_data, $item_order_id ) {
			if ( isset( $cart_data->legacy_values ) ) {
				if ( isset( $cart_data->legacy_values['felan_project_data'] ) && ! empty( $cart_data->legacy_values['felan_project_data'] ) ) {
					$felan_project_data = $cart_data->legacy_values['felan_project_data'];
					foreach( $felan_project_data as $key => $value ) {
						wc_add_order_item_meta( $item_id, 'felan_project_' . $key, $value);
					}
				}
			}
		}

		public function felan_woocommerce_thankyou($order_id) {
			$order    = wc_get_order( $order_id );
			$items    = $order->get_items();
			$all_meta = reset($items)->get_meta_data();

			$total_price = '';
			foreach ( $all_meta as $meta ) {
				switch ( $meta->key ) {
					case 'felan_project_total_price':
						$total_price = $meta->value;
						break;
				}
			}

			global $current_user, $wpdb;
			wp_get_current_user();
			$user_id            = $current_user->ID;
			$project_id = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_project_id', true);
			$total_price = felan_get_format_money($total_price);
			$payment_method = 'Woocommerce';

			// insert order
            $proposal_id = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_proposal_id', true);
            update_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_status', 'inprogress');
			$order_id = $this->felan_order->insert_project_order($total_price, $project_id, $user_id, $payment_method);
		}

		/**
		 * project_payment project_package by stripe
		 * @param $project_id
		 */
		public function felan_stripe_payment_project_addons($project_id, $total_price)
		{
			require_once(FELAN_PLUGIN_DIR . 'includes/partials/project/stripe-php/init.php');
			$project_stripe_secret_key = felan_get_option('project_stripe_secret_key');
			$project_tripe_publishable_key = felan_get_option('project_tripe_publishable_key');

			$current_user = wp_get_current_user();

			$user_id = $current_user->ID;
			$user_email = get_the_author_meta('user_email', $user_id);

			$stripe = array(
				"secret_key" => $project_stripe_secret_key,
				"publishable_key" => $project_tripe_publishable_key
			);

			\MyStripe\Stripe::setApiKey($stripe['secret_key']);
			$project_package_name = get_the_title($project_id);
			//update_user_meta($user_id, FELAN_METABOX_PREFIX . 'project_id', $project_id);


			$currency_code = felan_get_option('currency_type_default', 'USD');
			$total_amount = intval($total_price) * 100;
			$payment_completed_link = felan_get_permalink('project_payment_completed');
			$stripe_processor_link = add_query_arg(array('payment_method' => 2), $payment_completed_link);
			wp_enqueue_script('stripe-checkout');
			wp_localize_script('stripe-checkout', 'felan_stripe_vars', array(
				'felan_stripe_project_addons' => array(
					'key' => $project_tripe_publishable_key,
					'params' => array(
						'amount' => $total_amount,
						'email' => $user_email,
						'currency' => $currency_code,
						'zipCode' => true,
						'billingAddress' => true,
						'name' => esc_html__('Pay with Credit Card', 'felan-framework'),
						'description' => wp_kses_post(sprintf(__('%s Package Project Payment', 'felan-framework'), $project_package_name))
					)
				)
			));
			?>
			<form class="felan-project-stripe-form" action="<?php echo esc_url($stripe_processor_link) ?>" method="post" id="felan_stripe_project_addons">
				<button class="felan-stripe-button" style="display: none !important;"></button>
				<input type="hidden" id="project_id" name="project_id" value="<?php echo esc_attr($project_id) ?>">
				<input type="hidden" id="total_price" name="total_price" value="<?php echo esc_attr($total_price) ?>">
			</form>
			<?php

		}

		public function felan_razor_payment_project_addons( $project_id ) {
			$payment_completed_link = felan_get_permalink( 'project_payment_completed' );
			?>

			<form name='razorpayform' id="felan_razor_paymentform" action="<?= $payment_completed_link ?>" method="POST">
				<input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
				<input type="hidden" name="razorpay_signature"  id="razorpay_signature" >
				<input type="hidden" name="rzp_QP_form_submit" value="1">
			</form>

			<?php
		}

		public function felan_razor_payment_create_order() {
			if ( empty( $_POST['felan_project_security_payment'] ) ) {
				return;
			}
			require_once(FELAN_PLUGIN_DIR . 'includes/partials/project/razorpay-php/Razorpay.php');

			$orderID = mt_rand(0, mt_getrandmax());

			$payment_completed_link = felan_get_permalink( 'project_payment_completed' );
			$callback_url           = add_query_arg(
				[
					'payment_method'      => 4,
				],
				$payment_completed_link
			);

			$key_id_razor  = felan_get_option('project_razor_key_id');
			$key_secret    = felan_get_option('project_razor_key_secret');
			$currency_code = felan_get_option( 'currency_type_default', 'USD' );
			$order_id      = mt_rand( 0, mt_getrandmax() );

			$total_price = isset($_REQUEST['total_price']) ? felan_clean(wp_unslash($_REQUEST['total_price'])) : '';

			$api = new Api( $key_id_razor, $key_secret );
			// Calls the helper function to create order data
			$data = $this->getOrderCreationData($orderID, $total_price);
			$api->order->create($data);
			try {
				$razorpayOrder = $api->order->create($data);
			} catch (Exception $e) {
				$razorpayArgs['error'] = 'Wordpress Error : ' . $e->getMessage();
			}
			if (isset($razorpayArgs['error']) === false) {
				// Stores the data as a cached variable temporarily
				// $_SESSION['rzp_QP_order_id'] = $razorpayOrder['id'];
				// $_SESSION['rzp_QP_amount']   = $total_price;
				$razorpayArgs = [
					'key'          => $key_id_razor,
					'name'         => get_bloginfo( 'name' ),
					// 'amount'       => $total_price,
					'currency'     => $currency_code,
					'description'  => '',
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

		public function felan_razor_payment_verify() {
			$payment_completed_link = felan_get_permalink( 'project_payment_completed' );
			$callback_url           = add_query_arg(
				[
					'payment_method'      => 4,
					'razorpay_payment_id' => sanitize_text_field($_REQUEST['razorpay_payment_id']),
					'razorpay_order_id'   => $_REQUEST['razorpay_order_id'],
					'razorpay_signature'  => sanitize_text_field($_REQUEST['razorpay_signature'])
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

			$current_user   = wp_get_current_user();
			$user_id        = $current_user->ID;
			$user_email     = $current_user->user_email;
			$project_id     = intval( get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_project_id', true) );
			$payment_method = 'Razor';

			$key_id_razor  = felan_get_option('project_razor_key_id');
			$key_secret    = felan_get_option('project_razor_key_secret');
			$api           = new Api($key_id_razor, $key_secret);
			$razorpayOrder = $api->order->fetch($_REQUEST['razorpay_order_id']);
			$total_price   = $razorpayOrder->amount;
			$total_price   = (float) ($total_price / 100);

			$attributes = $this->getPostAttributes();

            if (!empty($attributes)) {
                $success = true;

                try {
                    $api->utility->verifyPaymentSignature($attributes);
                } catch(Exception $e) {
					$success = false;
					$error = '<div class="alert alert-error" role="alert"><strong>' . esc_html__('Error!', 'felan-framework') . ' </strong> ' . $e->getMessage() . '</div>';
					echo wp_kses_post($error);
                }

				if ($success === true) {
					//project_payment Stripe project_package
					$total_price = felan_get_format_money($total_price);
                    $proposal_id = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_proposal_id', true);
                    update_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_status', 'inprogress');
					$this->felan_order->insert_user_project_package($user_id, $project_id);
					$this->felan_order->insert_project_order($total_price, $project_id, $user_id, $payment_method, 'pending');
					$args = array();
					felan_send_email($user_email, 'mail_activated_project_package', $args);
				} else {
					$error = '<div class="alert alert-error" role="alert">' . wp_kses_post(__('<strong>Error!</strong> Transaction failed', 'felan-framework')) . '</div>';
					echo wp_kses_post($error);
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

		private function get_paypal_access_token($url, $postArgs)
		{
			$client_id = felan_get_option('project_paypal_client_id');
			$secret_key = felan_get_option('project_paypal_client_secret_key');

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
		 * project_payment per package by Paypal
		 */
		public function felan_paypal_payment_project_addons()
		{
			check_ajax_referer('felan_project_payment_ajax_nonce', 'felan_project_security_payment');
			global $current_user;
			wp_get_current_user();
			$user_id = $current_user->ID;

			$blogInfo = esc_url(home_url());

			$project_id = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_project_id', true);
			$project_id = intval($project_id);
			$total_price = isset($_REQUEST['total_price']) ? felan_clean(wp_unslash($_REQUEST['total_price'])) : '';
			$project_name = get_the_title($project_id);

			if (empty($total_price) && empty($project_id)) {
				exit();
			}
			$currency = felan_get_option('currency_type_default');
			$payment_description = $project_name . ' ' . esc_html__('Membership payment on ', 'felan-framework') . $blogInfo;
			$is_paypal_live = felan_get_option('project_paypal_api');
			$host = 'https://api.sandbox.paypal.com';
			if ($is_paypal_live == 'live') {
				$host = 'https://api.paypal.com';
			}
			$url = $host . '/v1/oauth2/token';
			$postArgs = 'grant_type=client_credentials';
			$access_token = $this->get_paypal_access_token($url, $postArgs);
			$url = $host . '/v1/payments/payment';
			$payment_completed_link = felan_get_permalink('project_payment_completed');
			$return_url = add_query_arg(array('payment_method' => 1), $payment_completed_link);
			$dash_profile_link = felan_get_permalink('dashboard');
			update_user_meta($user_id, FELAN_METABOX_PREFIX . 'project_id', $project_id);

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
					'total' => $total_price,
					'currency' => $currency,
					'details' => array(
						'subtotal' => $total_price,
						'tax' => '0.00',
						'shipping' => '0.00'
					)
				),
				'description' => $payment_description
			);

			$payment['transactions'][0]['item_list']['items'][] = array(
				'quantity' => '1',
				'name' => esc_html__('Project Payment Package', 'felan-framework'),
				'price' => $total_price,
				'currency' => $currency,
				'sku' => $project_name . ' ' . esc_html__('Project Payment Package', 'felan-framework'),
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
			$output['project_id'] = $project_id;
			update_user_meta($user_id, FELAN_METABOX_PREFIX . 'project_paypal_transfer', $output);

			print $payment_approval_url;
			wp_die();
		}

		/**
		 * project payment by wire transfer
		 */
		public function felan_wire_transfer_project_addons()
		{
			check_ajax_referer('felan_project_payment_ajax_nonce', 'felan_project_security_payment');
			$total_price = isset($_REQUEST['total_price']) ? felan_clean(wp_unslash($_REQUEST['total_price'])) : '';

			global $current_user;
			$current_user = wp_get_current_user();

			if (!is_user_logged_in()) {
				exit('No Login');
			}
			$user_id = $current_user->ID;
			$project_id = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_project_id', true);
			$total_price = felan_get_format_money($total_price);
			$payment_method = 'Wire_Transfer';

			//insert order
            $proposal_id = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_proposal_id', true);
            update_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_status', 'inprogress');

            $order_id = $this->felan_order->insert_project_order($total_price, $project_id, $user_id, $payment_method);
			$payment_completed_link = felan_get_permalink('project_payment_completed');

			$return_link = add_query_arg(array('payment_method' => 3, 'order_id' => $order_id), $payment_completed_link);
			print $return_link;
			wp_die();
		}

		/**
		 * project_payment per package by Woocommerce
		 */
		public function felan_woocommerce_payment_project_addons()
		{
			check_ajax_referer('felan_project_payment_ajax_nonce', 'felan_project_security_payment');
			global $current_user, $wpdb;
			wp_get_current_user();
			$user_id            = $current_user->ID;
			$project_id = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_project_id', true);
			$project_title      = get_the_title($project_id);
			$total_price = isset($_REQUEST['total_price']) ? felan_clean(wp_unslash($_REQUEST['total_price'])) : '';
			$total_price = felan_get_format_money($total_price);
			$checkout_url       = wc_get_checkout_url();
			$payment_method = 'Woocommerce';

			$query = $wpdb->prepare(
				'SELECT ID FROM ' . $wpdb->posts . '
                WHERE post_title = %s
                AND post_type = \'product\'',
				$project_title
			);
			$wpdb->query($query);

			if ($wpdb->num_rows) {
				$product_id = $wpdb->get_var($query);
				$objProduct = wc_get_product( $product_id );
				$objProduct->set_price($total_price);
				$objProduct->set_regular_price($total_price);
				$objProduct->save();
			} else {
				$objProduct         = new WC_Product();

				$objProduct->set_name($project_title);
				$objProduct->set_price($total_price);
				$objProduct->set_status("");
				$objProduct->set_catalog_visibility('hidden');
				$objProduct->set_regular_price($total_price);
				$product_id = $objProduct->save();
			}

			$cart_data = [
				'total_price' => $total_price,
			];

			global $woocommerce;
			$woocommerce->cart->empty_cart();
			$woocommerce->cart->add_to_cart($product_id, 1, '', [], ['felan_project_data' => $cart_data]);

			$url = add_query_arg(array(
				'project_id' => esc_attr($project_id),
			), $checkout_url);

			print $url;
			wp_die();
		}

		/**
		 * project_stripe_payment_completed
		 */
		public function stripe_payment_completed()
		{
			require_once(FELAN_PLUGIN_DIR . 'includes/partials/project/stripe-php/init.php');
			global $current_user;
			$user_id = $current_user->ID;
			$project_id = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_project_id', true);
			$project_id = intval($project_id);
			$total_price = isset($_REQUEST['total_price']) ? felan_clean(wp_unslash($_REQUEST['total_price'])) : '';
			$current_user = wp_get_current_user();
			$user_id = $current_user->ID;
			$user_email = $current_user->user_email;
			$currency_code = felan_get_option('currency_type_default', 'USD');
			$payment_method = 'Stripe';
			$project_stripe_secret_key = felan_get_option('project_stripe_secret_key');
			$project_tripe_publishable_key = felan_get_option('project_tripe_publishable_key');
			$stripe = array(
				"secret_key" => $project_stripe_secret_key,
				"publishable_key" => $project_tripe_publishable_key
			);
			\MyStripe\Stripe::setApiKey($stripe['secret_key']);
			$stripeEmail = '';
			if (is_email($_POST['stripeEmail'])) {
				$stripeEmail = sanitize_email(wp_unslash($_POST['stripeEmail']));
			} else {
				wp_die('None Mail');
			}

			$paymentId = 0;
			try {
				$token = isset($_POST['stripeToken']) ? felan_clean(wp_unslash($_POST['stripeToken'])) : '';
				$customer = \MyStripe\Customer::create(array(
					"email" => $stripeEmail,
					"source" => $token
				));
				$total_amount = intval($total_price) * 100;
				$charge = \MyStripe\Charge::create(array(
					"amount" => $total_amount,
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
					//project_payment Stripe project_package
					$total_price = felan_get_format_money($total_price);
                    $proposal_id = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_proposal_id', true);
                    update_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_status', 'inprogress');
					$this->felan_order->insert_user_project_package($user_id, $project_id);
					$this->felan_order->insert_project_order($total_price, $project_id, $user_id, $payment_method, 'pending');
					$args = array();
					felan_send_email($user_email, 'mail_activated_project_package', $args);
				} else {
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
			$allowed_html = array();
			$payment_method = 'Paypal';
			$total_price = isset($_REQUEST['total_price']) ? felan_clean(wp_unslash($_REQUEST['total_price'])) : '';
			$total_price = felan_get_format_money($total_price);
			try {
				if (isset($_GET['token']) && isset($_GET['PayerID'])) {
					$payerId = wp_kses(felan_clean(wp_unslash($_GET['PayerID'])), $allowed_html);
					$paymentId = wp_kses(felan_clean(wp_unslash($_GET['paymentId'])), $allowed_html);
					$transfered_data = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'project_paypal_transfer', true);
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
					delete_user_meta($user_id, FELAN_METABOX_PREFIX . 'project_paypal_transfer');
					if ($json_response['state'] == 'approved') {
						$project_id = $transfered_data['project_id'];
                        $proposal_id = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_proposal_id', true);
                        update_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_status', 'inprogress');
						$this->felan_order->insert_user_project_package($user_id, $project_id);
						$this->felan_order->insert_project_order($total_price, $project_id, $user_id, $payment_method, 'pending');
						$args = array();
						felan_send_email($user_email, 'mail_activated_project_package', $args);
					} else {
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
