<?php
if (!defined('ABSPATH')) {
	exit;
}

use Razorpay\Api\Api;
use Razorpay\Api\Errors;

if (!class_exists('Felan_Service_Payment')) {
	/**
	 * Class Felan_Service_Payment
	 */
	class Felan_Service_Payment
	{
		protected $felan_order;

		/**
		 * Construct
		 */
		public function __construct()
		{
			$this->felan_order = new Felan_Service_Order();

			add_action('wp_ajax_felan_razor_service_create_order', array($this, 'felan_razor_service_create_order'));
			add_action('wp_ajax_felan_razor_service_payment_verify', array($this, 'felan_razor_service_payment_verify'));

			add_action('woocommerce_new_order_item', [$this, 'felan_add_service_order_meta'], 10, 3);
			add_action('woocommerce_thankyou', [$this, 'felan_woocommerce_thankyou']);
		}

		public function felan_add_service_order_meta($item_id, $cart_data, $item_order_id)
		{
			if (isset($cart_data->legacy_values)) {
				if (isset($cart_data->legacy_values['felan_service_data']) && ! empty($cart_data->legacy_values['felan_service_data'])) {
					$felan_service_data = $cart_data->legacy_values['felan_service_data'];
					foreach ($felan_service_data as $key => $value) {
						wc_add_order_item_meta($item_id, 'felan_service_' . $key, $value);
					}
				}
			}
		}

		public function felan_woocommerce_thankyou($order_id)
		{
			$order    = wc_get_order($order_id);
			$items    = $order->get_items();
			$all_meta = reset($items)->get_meta_data();

			$total_price = $package_time = $package_time_type = $price_default = $package_des = $package_new = $package_addons = '';
			foreach ($all_meta as $meta) {
				switch ($meta->key) {
					case 'felan_service_total_price':
						$total_price = $meta->value;
						break;
					case 'felan_service_package_time':
						$package_time = $meta->value;
						break;
					case 'felan_service_package_time_type':
						$package_time_type = $meta->value;
						break;
					case 'felan_service_price_default':
						$price_default = $meta->value;
						break;
					case 'felan_service_package_des':
						$package_des = $meta->value;
						break;
					case 'felan_service_package_addons':
						$package_addons = $meta->value;
						break;
					case 'felan_service_package_new':
						$package_new = $meta->value;
						break;
					case 'felan_service_package_time':
						$package_time = $meta->value;
						break;
				}
			}

			global $current_user;
			wp_get_current_user();
			$user_id            = $current_user->ID;
			$service_id         = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_service_id', true);
			$service_title      = get_the_title($service_id);
			$format_total_price = felan_get_format_money($total_price);
			$payment_method     = 'Woocommerce';

			do_action('felan_service_payment_completed', $order_id);
			// insert order
			$order_id = $this->felan_order->insert_service_order($price_default, $package_des, $package_new, $package_addons, $total_price, $package_time, $package_time_type, $service_id, $user_id, $payment_method);
		}

		public function felan_razor_payment_project_addons()
		{
			$payment_completed_link = felan_get_permalink('service_payment_completed');
?>

			<form name='razorpayform' id="felan_razor_paymentform" action="<?= $payment_completed_link ?>" method="POST">
				<input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
				<input type="hidden" name="razorpay_signature" id="razorpay_signature">
				<input type="hidden" name="rzp_QP_form_submit" value="1">
			</form>

		<?php
		}

		public function felan_razor_service_create_order()
		{
			require_once(FELAN_PLUGIN_DIR . 'includes/partials/project/razorpay-php/Razorpay.php');

			$orderID = mt_rand(0, mt_getrandmax());

			$payment_completed_link = felan_get_permalink('service_payment_completed');
			$callback_url           = add_query_arg(
				[
					'payment_method'      => 4,
				],
				$payment_completed_link
			);

			$key_id_razor  = felan_get_option('service_razor_key_id');
			$key_secret    = felan_get_option('service_razor_key_secret');
			$currency_code = felan_get_option('currency_type_default', 'USD');
			$order_id      = mt_rand(0, mt_getrandmax());

			$total_price = isset($_REQUEST['total_price']) ? felan_clean(wp_unslash($_REQUEST['total_price'])) : '';

			$api = new Api($key_id_razor, $key_secret);
			// Calls the helper function to create order data
			$data = $this->getOrderCreationData($orderID, $total_price);
			$api->order->create($data);
			try {
				$razorpayOrder = $api->order->create($data);
			} catch (Exception $e) {
				$razorpayArgs['error'] = 'Wordpress Error : ' . $e->getMessage();
			}
			if (isset($razorpayArgs['error']) === false) {
				$razorpayArgs = [
					'key'          => $key_id_razor,
					'name'         => get_bloginfo('name'),
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

		public function felan_razor_service_payment_verify()
		{
			$payment_completed_link = felan_get_permalink('service_payment_completed');
			$callback_url           = add_query_arg(
				[
					'payment_method'      => 4,
					'razorpay_payment_id' => sanitize_text_field($_REQUEST['razorpay_payment_id']),
					'razorpay_order_id'   => $_REQUEST['razorpay_order_id'],
					'razorpay_signature'  => sanitize_text_field($_REQUEST['razorpay_signature']),
					'package_time'        => isset($_REQUEST['package_time']) ? felan_clean(wp_unslash($_REQUEST['package_time'])) : '',
					'package_time_type'   => isset($_REQUEST['package_time_type']) ? felan_clean(wp_unslash($_REQUEST['package_time_type'])) : '',
					'price_default'       => isset($_REQUEST['price_default']) ? felan_clean(wp_unslash($_REQUEST['price_default'])) : '',
					'package_des'         => isset($_REQUEST['package_des']) ? felan_clean(wp_unslash($_REQUEST['package_des'])) : '',
					'package_new'         => isset($_REQUEST['package_new']) ? felan_clean(wp_unslash($_REQUEST['package_new'])) : '',
					'package_addons'      => isset($_REQUEST['package_addons']) ? wp_unslash($_REQUEST['package_addons']) : '',
				],
				$payment_completed_link
			);

			echo $callback_url;
			wp_die();
		}

		/**
		 * Creates orders API data RazorPay
		 **/
		function getOrderCreationData($orderID, $amount)
		{
			$data = array(
				'receipt'         => $orderID,
				'amount'          => (int) round($amount * 100),
				'currency'        => felan_get_option('currency_type_default', 'USD'),
				'payment_capture' => 0
			);

			return $data;
		}

		public function razor_payment_completed()
		{
			require_once(FELAN_PLUGIN_DIR . 'includes/partials/project/razorpay-php/Razorpay.php');

			$current_user      = wp_get_current_user();
			$user_id           = $current_user->ID;
			$user_email        = $current_user->user_email;
			$service_id        = intval(get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_service_id', true));
			$package_time      = isset($_REQUEST['package_time']) ? felan_clean(wp_unslash($_REQUEST['package_time'])) : '';
			$package_time_type = isset($_REQUEST['package_time_type']) ? felan_clean(wp_unslash($_REQUEST['package_time_type'])) : '';
			$price_default     = isset($_REQUEST['price_default']) ? felan_clean(wp_unslash($_REQUEST['price_default'])) : '';
			$package_des       = isset($_REQUEST['package_des']) ? felan_clean(wp_unslash($_REQUEST['package_des'])) : '';
			$package_new       = isset($_REQUEST['package_new']) ? felan_clean(wp_unslash($_REQUEST['package_new'])) : '';
			$package_addons    = isset($_REQUEST['package_addons']) ? wp_unslash($_REQUEST['package_addons']) : '';
			$payment_method    = 'Razor';

			$key_id_razor  = felan_get_option('service_razor_key_id');
			$key_secret    = felan_get_option('service_razor_key_secret');
			$api           = new Api($key_id_razor, $key_secret);
			$razorpayOrder = $api->order->fetch($_REQUEST['razorpay_order_id']);
			$total_price   = $razorpayOrder->amount;
			$total_price   = (float) ($total_price / 100);

			$attributes = $this->getPostAttributes();

			if (!empty($attributes)) {
				$success = true;

				try {
					$api->utility->verifyPaymentSignature($attributes);
				} catch (Exception $e) {
					$success = false;
					$error = '<div class="alert alert-error" role="alert"><strong>' . esc_html__('Error!', 'felan-framework') . ' </strong> ' . $e->getMessage() . '</div>';
					echo wp_kses_post($error);
				}

				if ($success === true) {
					// $total_price = felan_get_format_money($total_price);
					$this->felan_order->insert_user_service_package($user_id, $service_id);
					$this->felan_order->insert_service_order($price_default, $package_des, $package_new, $package_addons, $total_price, $package_time, $package_time_type, $service_id, $user_id, $payment_method);
					$args = [];
					felan_send_email($user_email, 'mail_activated_service_package', $args);
				} else {
					$error = '<div class="alert alert-error" role="alert">' . wp_kses_post(__('<strong>Error!</strong> Transaction failed', 'felan-framework')) . '</div>';
					echo wp_kses_post($error);
				}
			}
		}

		protected function getPostAttributes()
		{
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
		 * service_payment service_package by stripe
		 * @param $service_id
		 */
		public function felan_stripe_payment_service_addons($service_id, $total_price, $price_default, $service_addon, $package_time, $time_type, $package_des, $package_new)
		{
			require_once(FELAN_PLUGIN_DIR . 'includes/partials/service/stripe-php/init.php');
			$service_stripe_secret_key = felan_get_option('service_stripe_secret_key');
			$service_tripe_publishable_key = felan_get_option('service_tripe_publishable_key');

			$current_user = wp_get_current_user();

			$user_id = $current_user->ID;
			$user_email = get_the_author_meta('user_email', $user_id);

			$stripe = array(
				"secret_key" => $service_stripe_secret_key,
				"publishable_key" => $service_tripe_publishable_key
			);

			\MyStripe\Stripe::setApiKey($stripe['secret_key']);
			$service_package_name = get_the_title($service_id);
			//update_user_meta($user_id, FELAN_METABOX_PREFIX . 'service_id', $service_id);


			$currency_code = felan_get_option('currency_type_default', 'USD');
			$total_amount = intval($total_price) * 100;
			$payment_completed_link = felan_get_permalink('service_payment_completed');
			$stripe_processor_link = add_query_arg(array('payment_method' => 2), $payment_completed_link);
			wp_enqueue_script('stripe-checkout');
			wp_localize_script('stripe-checkout', 'felan_stripe_vars', array(
				'felan_stripe_service_addons' => array(
					'key' => $service_tripe_publishable_key,
					'params' => array(
						'amount' => $total_amount,
						'email' => $user_email,
						'currency' => $currency_code,
						'zipCode' => true,
						'billingAddress' => true,
						'name' => esc_html__('Pay with Credit Card', 'felan-framework'),
						'description' => wp_kses_post(sprintf(__('%s Package Service Payment', 'felan-framework'), $service_package_name))
					)
				)
			));
		?>
			<form class="felan-service-stripe-form" action="<?php echo esc_url($stripe_processor_link) ?>" method="post" id="felan_stripe_service_addons">
				<button class="felan-stripe-button" style="display: none !important;"></button>
				<input type="hidden" id="service_id" name="service_id" value="<?php echo esc_attr($service_id) ?>">
				<input type="hidden" id="total_price" name="total_price" value="<?php echo esc_attr($total_price) ?>">
				<input type="hidden" id="package_time" name="package_time" value="<?php echo esc_attr($package_time) ?>">
				<input type="hidden" id="package_time_type" name="package_time_type" value="<?php echo esc_attr($time_type) ?>">
				<input type="hidden" id="price_default" name="price_default" value="<?php echo esc_attr($price_default) ?>">
				<input type="hidden" id="package_des" name="package_des" value="<?php echo esc_attr($package_des) ?>">
				<input type="hidden" id="package_new" name="package_new" value="<?php echo esc_attr($package_new) ?>">
				<input type="hidden" id="package_addons" name="package_addons" value="<?php echo esc_attr($service_addon) ?>">
			</form>
<?php

		}

		private function get_paypal_access_token($url, $postArgs)
		{
			$client_id = felan_get_option('service_paypal_client_id');
			$secret_key = felan_get_option('service_paypal_client_secret_key');

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
		 * service_payment per package by Paypal
		 */
		public function felan_paypal_payment_service_addons()
		{
			check_ajax_referer('felan_service_payment_ajax_nonce', 'felan_service_security_payment');
			global $current_user;
			wp_get_current_user();
			$user_id = $current_user->ID;

			$blogInfo = esc_url(home_url());

			$service_id = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_service_id', true);
			$service_id = intval($service_id);
			$total_price = isset($_REQUEST['total_price']) ? felan_clean(wp_unslash($_REQUEST['total_price'])) : '';
			$service_name = get_the_title($service_id);

			if (empty($total_price) && empty($service_id)) {
				exit();
			}
			$currency = felan_get_option('currency_type_default');
			$payment_description = $service_name . ' ' . esc_html__('Membership payment on ', 'felan-framework') . $blogInfo;
			$is_paypal_live = felan_get_option('service_paypal_api');
			$host = 'https://api.sandbox.paypal.com';
			if ($is_paypal_live == 'live') {
				$host = 'https://api.paypal.com';
			}
			$url = $host . '/v1/oauth2/token';
			$postArgs = 'grant_type=client_credentials';
			$access_token = $this->get_paypal_access_token($url, $postArgs);
			$url = $host . '/v1/payments/payment';
			$payment_completed_link = felan_get_permalink('service_payment_completed');
			$return_url = add_query_arg(array('payment_method' => 1), $payment_completed_link);
			$dash_profile_link = felan_get_permalink('dashboard');
			update_user_meta($user_id, FELAN_METABOX_PREFIX . 'service_id', $service_id);

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
				'name' => esc_html__('Service Payment Package', 'felan-framework'),
				'price' => $total_price,
				'currency' => $currency,
				'sku' => $service_name . ' ' . esc_html__('Service Payment Package', 'felan-framework'),
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
			$output['service_id'] = $service_id;
			update_user_meta($user_id, FELAN_METABOX_PREFIX . 'service_paypal_transfer', $output);

			print $payment_approval_url;
			wp_die();
		}

		/**
		 * service payment by wire transfer
		 */
		public function felan_wire_transfer_service_addons()
		{
			check_ajax_referer('felan_service_payment_ajax_nonce', 'felan_service_security_payment');
			$total_price = isset($_REQUEST['total_price']) ? felan_clean(wp_unslash($_REQUEST['total_price'])) : '';
			$package_time = isset($_REQUEST['package_time']) ? felan_clean(wp_unslash($_REQUEST['package_time'])) : '';
			$package_time_type = isset($_REQUEST['package_time_type']) ? felan_clean(wp_unslash($_REQUEST['package_time_type'])) : '';
			$price_default = isset($_REQUEST['price_default']) ? felan_clean(wp_unslash($_REQUEST['price_default'])) : '';
			$package_des = isset($_REQUEST['package_des']) ? felan_clean(wp_unslash($_REQUEST['package_des'])) : '';
			$package_new = isset($_REQUEST['package_new']) ? felan_clean(wp_unslash($_REQUEST['package_new'])) : '';
			$package_addons = isset($_REQUEST['package_addons']) ? wp_unslash($_REQUEST['package_addons']) : '';

			global $current_user;
			$current_user = wp_get_current_user();

			if (!is_user_logged_in()) {
				exit('No Login');
			}
			$user_id = $current_user->ID;
			$service_id = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_service_id', true);
			// $total_price = felan_get_format_money($total_price);
			$payment_method = 'Wire_Transfer';

			//insert order
			$order_id = $this->felan_order->insert_service_order($price_default, $package_des, $package_new, $package_addons, $total_price, $package_time, $package_time_type, $service_id, $user_id, $payment_method);
			$payment_completed_link = felan_get_permalink('service_payment_completed');

			$return_link = add_query_arg(array('payment_method' => 3, 'order_id' => $order_id), $payment_completed_link);
			print $return_link;
			wp_die();
		}

		/**
		 * service_payment per package by Woocommerce
		 */
		public function felan_woocommerce_payment_service_addons()
		{
			check_ajax_referer('felan_service_payment_ajax_nonce', 'felan_service_security_payment');
			global $current_user, $wpdb;
			wp_get_current_user();
			$user_id            = $current_user->ID;
			$service_id = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_service_id', true);
			$service_title      = get_the_title($service_id);
			$total_price = isset($_REQUEST['total_price']) ? felan_clean(wp_unslash($_REQUEST['total_price'])) : '';
			$package_time = isset($_REQUEST['package_time']) ? felan_clean(wp_unslash($_REQUEST['package_time'])) : '';
			$package_time_type = isset($_REQUEST['package_time_type']) ? felan_clean(wp_unslash($_REQUEST['package_time_type'])) : '';
			$price_default = isset($_REQUEST['price_default']) ? felan_clean(wp_unslash($_REQUEST['price_default'])) : '';
			$package_des = isset($_REQUEST['package_des']) ? felan_clean(wp_unslash($_REQUEST['package_des'])) : '';
			$package_new = isset($_REQUEST['package_new']) ? felan_clean(wp_unslash($_REQUEST['package_new'])) : '';
			$package_addons = isset($_REQUEST['package_addons']) ? wp_unslash($_REQUEST['package_addons']) : '';

			$format_total_price = felan_get_format_money($total_price);
			$checkout_url       = wc_get_checkout_url();
			$payment_method = 'Woocommerce';

			$query = $wpdb->prepare(
				'SELECT ID FROM ' . $wpdb->posts . '
                WHERE post_title = %s
                AND post_type = \'product\'',
				$service_title
			);
			$wpdb->query($query);

			if ($wpdb->num_rows) {
				$product_id = $wpdb->get_var($query);
				$objProduct = wc_get_product($product_id);
				$objProduct->set_price($total_price);
				$objProduct->set_regular_price($total_price);
				$objProduct->save();
			} else {
				$objProduct         = new WC_Product();

				$objProduct->set_name($service_title);
				$objProduct->set_price($total_price);
				$objProduct->set_status("");
				$objProduct->set_catalog_visibility('hidden');
				$objProduct->set_regular_price($total_price);
				$product_id = $objProduct->save();
			}

			$cart_data = [
				'total_price'       => $total_price,
				'package_time'      => $package_time,
				'package_time_type' => $package_time_type,
				'price_default'     => $price_default,
				'package_des'       => $package_des,
				'package_new'       => $package_new,
				'package_addons'    => $package_addons,
			];

			global $woocommerce;
			$woocommerce->cart->empty_cart();
			$woocommerce->cart->add_to_cart($product_id, 1, '', [], ['felan_service_data' => $cart_data]);

			$url = add_query_arg(array(
				'service_id' => esc_attr($service_id),
			), $checkout_url);

			print $url;
			wp_die();
		}

		/**
		 * service_stripe_payment_completed
		 */
		public function stripe_payment_completed()
		{
			require_once(FELAN_PLUGIN_DIR . 'includes/partials/service/stripe-php/init.php');
			global $current_user;
			$user_id = $current_user->ID;
			$service_id = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_service_id', true);
			$service_id = intval($service_id);
			$total_price = isset($_POST['total_price']) ? felan_clean(wp_unslash($_POST['total_price'])) : 0;
			$package_time = isset($_POST['package_time']) ? felan_clean(wp_unslash($_POST['package_time'])) : '';
			$package_time_type = isset($_POST['package_time_type']) ? felan_clean(wp_unslash($_POST['package_time_type'])) : '';
			$price_default = isset($_POST['price_default']) ? felan_clean(wp_unslash($_POST['price_default'])) : '';
			$package_des = isset($_POST['package_des']) ? felan_clean(wp_unslash($_POST['package_des'])) : '';
			$package_new = isset($_POST['package_new']) ? felan_clean(wp_unslash($_POST['package_new'])) : '';
			$package_addons = isset($_POST['package_addons']) ? wp_unslash($_POST['package_addons']) : '';
			$current_user = wp_get_current_user();
			$user_id = $current_user->ID;
			$user_email = $current_user->user_email;
			$currency_code = felan_get_option('currency_type_default', 'USD');
			$payment_method = 'Stripe';
			$service_stripe_secret_key = felan_get_option('service_stripe_secret_key');
			$service_tripe_publishable_key = felan_get_option('service_tripe_publishable_key');
			$stripe = array(
				"secret_key" => $service_stripe_secret_key,
				"publishable_key" => $service_tripe_publishable_key
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
				// $currency_sign_default = felan_get_option('currency_sign_default');
				// $currency_position = felan_get_option('currency_position');
				// if ($currency_position == 'before') {
				//     $total_price = $currency_sign_default . $total_price;
				// } else {
				//     $total_price = $total_price . $currency_sign_default;
				// }

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
					$this->felan_order->insert_user_service_package($user_id, $service_id);
					$this->felan_order->insert_service_order($price_default, $package_des, $package_new, $package_addons, $total_price, $package_time, $package_time_type, $service_id, $user_id, $payment_method);

					$args = array();
					felan_send_email($user_email, 'mail_activated_service_package', $args);
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

			try {
				if (isset($_GET['token']) && isset($_GET['PayerID'])) {
					$payerId = wp_kses(felan_clean(wp_unslash($_GET['PayerID'])), $allowed_html);
					$paymentId = wp_kses(felan_clean(wp_unslash($_GET['paymentId'])), $allowed_html);
					$transfered_data = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'service_paypal_transfer', true);
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

					$total_price = $json_response['transactions'][0]['amount']['total'];
					// $total_price = felan_get_format_money($total_price);
					$package_time = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'service_package_time', true);
					$package_time_type = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'service_package_time_type', true);
					$price_default = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'service_package_price', true);
					$package_des = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'service_package_des', true);
					$package_new = json_encode(get_user_meta($user_id, FELAN_METABOX_PREFIX . 'service_package_new', true));
					$package_addons = isset($_REQUEST['package_addons']) ? wp_unslash($_REQUEST['package_addons']) : '';

					delete_user_meta($user_id, FELAN_METABOX_PREFIX . 'service_paypal_transfer');
					if ($json_response['state'] == 'approved') {
						$service_id = $transfered_data['service_id'];
						$this->felan_order->insert_user_service_package($user_id, $service_id);
						$this->felan_order->insert_service_order($price_default, $package_des, $package_new, $package_addons, $total_price, $package_time, $package_time_type, $service_id, $user_id, $payment_method);
						$args = array();
						felan_send_email($user_email, 'mail_activated_service_package', $args);
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
