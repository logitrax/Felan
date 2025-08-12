<?php
if (!defined('ABSPATH')) {
    exit;
}

use Razorpay\Api\Api;
use Razorpay\Api\Errors;
if (!class_exists('Felan_Wallet_Payment')) {
    /**
     * Class Felan_Wallet_Payment
     */
    class Felan_Wallet_Payment
    {
        protected $felan_order;

        /**
         * Construct
         */
        public function __construct()
        {
            $this->felan_order = new Felan_Wallet_Order();
            add_action('wp_ajax_felan_razor_wallet_create_order', array($this, 'felan_razor_wallet_create_order'));
            add_action('wp_ajax_felan_razor_wallet_verify', array($this, 'felan_razor_wallet_verify'));

            add_action('woocommerce_new_order_item', [$this, 'felan_add_wallet_order_meta'], 10, 3);
            add_action('woocommerce_thankyou', [$this, 'felan_woocommerce_thankyou']);
        }

        public function felan_add_wallet_order_meta( $item_id, $cart_data, $item_order_id ) {
            if ( isset( $cart_data->legacy_values ) ) {
                if ( isset( $cart_data->legacy_values['felan_wallet_data'] ) && ! empty( $cart_data->legacy_values['felan_wallet_data'] ) ) {
                    $felan_wallet_data = $cart_data->legacy_values['felan_wallet_data'];
                    foreach( $felan_wallet_data as $key => $value ) {
                        wc_add_order_item_meta( $item_id, 'felan_wallet_' . $key, $value);
                    }
                }
            }
        }

        public function felan_woocommerce_thankyou($order_id) {
            $order    = wc_get_order( $order_id );
            $items    = $order->get_items();
            $all_meta = reset($items)->get_meta_data();

            $wallet_price = $user_role = '';
            foreach ( $all_meta as $meta ) {
                switch ( $meta->key ) {
                    case 'felan_wallet_wallet_price':
                        $wallet_price = $meta->value;
                        break;
                    case 'felan_wallet_user_role':
                        $user_role = $meta->value;
                        break;
                }
            }
            
            global $current_user;
            wp_get_current_user();
            $user_id  = $current_user->ID;

            // insert order
            $this->felan_order->insert_wallet_order($user_id, $user_role, $wallet_price, 'woocommerce', 'approve');
            felan_update_withdraw_total_price($wallet_price, $user_role);
        }

        /**
         * Wallet_Payment wallet_package by stripe
         * @param $wallet_id
         */
        public function felan_stripe_form_payment_wallet()
        {
            $payment_completed_link = felan_get_permalink('thank_you');
            $stripe_processor_link = add_query_arg(array('payment_method' => 'stripe'), $payment_completed_link);
            wp_enqueue_script('stripe-checkout');
            ?>
            <form class="felan-wallet-stripe-form" action="<?php echo esc_url($stripe_processor_link) ?>" method="post" id="felan_stripe_wallet_addons">
                <button class="felan-stripe-button" style="display: none !important;"></button>
                <input type="hidden" id="stripe_wallet_price" name="stripe_wallet_price" value="">
                <input type="hidden" id="stripe_user_role" name="stripe_user_role" value="">
            </form>
            <?php
        }

        /**
         * wallet_payment per package by Stripe
         */
        public function felan_stripe_payment_wallet_addons()
        {
            check_ajax_referer('felan_wallet_payment_ajax_nonce', 'felan_wallet_security_payment');
            $wallet_price = isset($_REQUEST['wallet_price']) ? felan_clean(wp_unslash($_REQUEST['wallet_price'])) : '';
            $user_role = isset($_REQUEST['user_role']) ? felan_clean(wp_unslash($_REQUEST['user_role'])) : '';

            if (empty($wallet_price)) {
                wp_send_json(array('success' => false, 'message' => esc_html('Please enter the amount to add to the wallet.','felan-framework')));
            }

            require_once(FELAN_PLUGIN_DIR . 'includes/partials/payment/stripe-php/init.php');
            $wallet_stripe_secret_key = felan_get_option('wallet_stripe_secret_key');
            $wallet_stripe_publishable_key = felan_get_option('wallet_stripe_publishable_key');

            $current_user = wp_get_current_user();
            $user_id = $current_user->ID;
            $user_email = get_the_author_meta('user_email', $user_id);

            $stripe = array(
                "secret_key" => $wallet_stripe_secret_key,
                "publishable_key" => $wallet_stripe_publishable_key
            );

            \MyStripe\Stripe::setApiKey($stripe['secret_key']);

            $currency_code = felan_get_option('currency_type_default', 'USD');
            $stripe_amount = intval($wallet_price) * 100;

            $localize_script = '
            <script type="text/javascript">
            var felan_stripe_vars = ' . json_encode(array(
                    'felan_stripe_wallet_addons' => array(
                        'key' => $wallet_stripe_publishable_key,
                        'params' => array(
                            'amount' => $stripe_amount,
                            'email' => $user_email,
                            'currency' => $currency_code,
                            'zipCode' => true,
                            'billingAddress' => true,
                            'name' => esc_html__('Pay with Credit Card', 'felan-framework'),
                            'description' => esc_html__('Package Wallet Payment', 'felan-framework'),
                        ),
                    ),
                )) . ';
            </script>';

            wp_send_json_success(array(
                'script' => $localize_script,
                'wallet_price' => $wallet_price,
                'user_role' => $user_role,
            ));

            wp_die();
        }

        public function felan_razor_payment_wallet_addons() {
            $payment_completed_link = felan_get_permalink( 'thank_you' );
            ?>

            <form name='razorpayform' id="felan_razor_paymentform" action="<?php echo esc_url($payment_completed_link); ?>" method="POST">
                <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
                <input type="hidden" name="razorpay_signature"  id="razorpay_signature" >
                <input type="hidden" name="rzp_QP_form_submit" value="1">
            </form>

            <?php
        }

        public function felan_razor_wallet_create_order() {
            check_ajax_referer('felan_wallet_payment_ajax_nonce', 'felan_wallet_security_payment');
            if ( empty( $_POST['felan_wallet_security_payment'] ) ) {
                return;
            }
            require_once(FELAN_PLUGIN_DIR . 'includes/partials/project/razorpay-php/Razorpay.php');

            $orderID = mt_rand(0, mt_getrandmax());
            $wallet_price = isset($_REQUEST['wallet_price']) ? felan_clean(wp_unslash($_REQUEST['wallet_price'])) : '';
            $user_role = isset($_REQUEST['user_role']) ? felan_clean(wp_unslash($_REQUEST['user_role'])) : '';
            $payment_completed_link = felan_get_permalink( 'thank_you' );
            $callback_url           = add_query_arg(['payment_method'      => 'razor',],$payment_completed_link);

            if (empty($wallet_price)) {
                wp_send_json(array('success' => false, 'message' => esc_html('Please enter the amount to add to the wallet.','felan-framework')));
            }

            $key_id_razor  = felan_get_option('wallet_razor_key_id');
            $key_secret    = felan_get_option('wallet_razor_key_secret');
            $currency_code = felan_get_option( 'currency_type_default', 'USD' );
            $order_id      = mt_rand( 0, mt_getrandmax() );

            $api = new Api( $key_id_razor, $key_secret );
            // Calls the helper function to create order data
            $data = $this->getOrderCreationData($orderID, $wallet_price);
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

            wp_send_json(array('success' => true, 'data' => $razorpayArgs));
        }

        public function felan_razor_wallet_verify() {
            $payment_completed_link = felan_get_permalink( 'thank_you' );
            $callback_url           = add_query_arg(
                [
                    'payment_method'      => 'razor',
                    'razorpay_payment_id' => sanitize_text_field($_REQUEST['razorpay_payment_id']),
                    'razorpay_order_id'   => $_REQUEST['razorpay_order_id'],
                    'razorpay_signature'  => sanitize_text_field($_REQUEST['razorpay_signature']),
                    'user_role'  => sanitize_text_field($_REQUEST['user_role'])
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

            $key_id_razor  = felan_get_option('wallet_razor_key_id');
            $key_secret    = felan_get_option('wallet_razor_key_secret');
            $api           = new Api($key_id_razor, $key_secret);
            $razorpayOrder = $api->order->fetch($_REQUEST['razorpay_order_id']);
            $total_price   = $razorpayOrder->amount;
            $total_price   = (float) ($total_price / 100);
            $user_role = sanitize_text_field($_GET['user_role']);
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
                    //wallet_payment Stripe wallet_package
                    $this->felan_order->insert_wallet_order($user_id, $user_role, $total_price, 'razor', 'approve');
                    felan_update_withdraw_total_price($total_price, $user_role);

                    $args = array();
                    felan_send_email($user_email, 'mail_activated_wallet_package', $args);
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
            $client_id = felan_get_option('wallet_paypal_client_id');
            $secret_key = felan_get_option('wallet_paypal_client_secret_key');

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
         * wallet_payment per package by Paypal
         */
        public function felan_paypal_payment_wallet_addons()
        {
            check_ajax_referer('felan_wallet_payment_ajax_nonce', 'felan_wallet_security_payment');
            $wallet_price = isset($_REQUEST['wallet_price']) ? felan_clean(wp_unslash($_REQUEST['wallet_price'])) : '';
            $user_role = isset($_REQUEST['user_role']) ? felan_clean(wp_unslash($_REQUEST['user_role'])) : '';

            if (empty($wallet_price)) {
                wp_send_json(array('success' => false, 'message' => esc_html('Please enter the amount to add to the wallet.','felan-framework')));
            }

            global $current_user;
            wp_get_current_user();
            $user_id = $current_user->ID;
            $blogInfo = esc_url(home_url());
            $currency = felan_get_option('currency_type_default');
            $payment_description = esc_html__('Membership payment on ', 'felan-framework') . $blogInfo;
            $is_paypal_live = felan_get_option('wallet_paypal_api');
            $host = 'https://api.sandbox.paypal.com';
            if ($is_paypal_live == 'live') {
                $host = 'https://api.paypal.com';
            }
            $url = $host . '/v1/oauth2/token';
            $postArgs = 'grant_type=client_credentials';
            $access_token = $this->get_paypal_access_token($url, $postArgs);
            $url = $host . '/v1/payments/payment';
            $payment_completed_link = felan_get_permalink('thank_you');
            $return_url = add_query_arg(array('payment_method' => 'paypal'), $payment_completed_link);
            $dash_profile_link = felan_get_permalink('dashboard');

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
                    'total' => $wallet_price,
                    'currency' => $currency,
                    'details' => array(
                        'subtotal' => $wallet_price,
                        'tax' => '0.00',
                        'shipping' => '0.00'
                    )
                ),
                'description' => $payment_description
            );

            $payment['transactions'][0]['item_list']['items'][] = array(
                'quantity' => '1',
                'name' => esc_html__('Wallet Payment Package', 'felan-framework'),
                'price' => $wallet_price,
                'currency' => $currency,
                'sku' => esc_html__('Wallet Payment Package', 'felan-framework'),
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
            $output['wallet_price'] = $wallet_price;
            $output['user_role'] = $user_role;
            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'wallet_paypal_transfer', $output);

            wp_send_json(array('success' => true, 'redirect_url' => $payment_approval_url));
        }

        /**
         * wallet payment by wire transfer
         */
        public function felan_wire_transfer_wallet_addons()
        {
            check_ajax_referer('felan_wallet_payment_ajax_nonce', 'felan_wallet_security_payment');
            $wallet_price = isset($_REQUEST['wallet_price']) ? felan_clean(wp_unslash($_REQUEST['wallet_price'])) : '';
            $user_role = isset($_REQUEST['user_role']) ? felan_clean(wp_unslash($_REQUEST['user_role'])) : '';

            if (empty($wallet_price)) {
                wp_send_json(array('success' => false, 'message' => esc_html('Please enter the amount to add to the wallet.','felan-framework')));
            }

            global $current_user;
            $user_id = $current_user->ID;
            $payment_method = 'wire-transfer';
            $enable_admin_approval_package = felan_get_option('enable_admin_approval_package','1');

            if($enable_admin_approval_package == '1'){
                $status = 'pending';
            } else {
                $status = 'approve';
                felan_update_withdraw_total_price($wallet_price, $user_role);
            }

            $order_id = $this->felan_order->insert_wallet_order($user_id, $user_role, $wallet_price, $payment_method, $status);
            $payment_completed_link = felan_get_permalink('thank_you');

            $return_link = add_query_arg(array('payment_method' => $payment_method, 'order_id' => $order_id), $payment_completed_link);

            wp_send_json(array('success' => true, 'redirect_url' => $return_link));
        }

        /**
         * wallet_payment per package by Woocommerce
         */
        public function felan_woocommerce_payment_wallet_addons()
        {
            check_ajax_referer('felan_wallet_payment_ajax_nonce', 'felan_wallet_security_payment');
            global $current_user, $wpdb;
            wp_get_current_user();
            $user_id            = $current_user->ID;
            $wallet_title      = 'Wallet-'. $user_id;
            $user_role = isset($_REQUEST['user_role']) ? felan_clean(wp_unslash($_REQUEST['user_role'])) : '';
            $wallet_price = isset($_REQUEST['wallet_price']) ? felan_clean(wp_unslash($_REQUEST['wallet_price'])) : '';
            $wallet_price_format = felan_get_format_money($wallet_price);
            $checkout_url       = wc_get_checkout_url();

            if (empty($wallet_price)) {
                wp_send_json(array('success' => false, 'message' => esc_html('Please enter the amount to add to the wallet.','felan-framework')));
            }

            $random_id = wp_rand(1000, 9999);
            $wallet_title_with_random = $wallet_title . '-' . $random_id;
            $query = $wpdb->prepare(
                'SELECT ID FROM ' . $wpdb->posts . '
                WHERE post_title = %s
                AND post_type = \'product\'',
                $wallet_title_with_random
            );
            $wpdb->query($query);

            if ($wpdb->num_rows) {
                $product_id = $wpdb->get_var($query);
            } else {
                $objProduct         = new WC_Product();

                $objProduct->set_name($wallet_title);
                $objProduct->set_price($wallet_price_format);
                $objProduct->set_status("");
                $objProduct->set_catalog_visibility('hidden');
                $objProduct->set_regular_price($wallet_price_format);
                $product_id = $objProduct->save();
            }

            $cart_data = [
                'wallet_price'  => $wallet_price,
                'user_role'     => $user_role,
            ];

            global $woocommerce;
            $woocommerce->cart->empty_cart();
            $woocommerce->cart->add_to_cart($product_id, 1, '', [], ['felan_wallet_data' => $cart_data]);

            wp_send_json(array('success' => true, 'redirect_url' => $checkout_url));
        }

        /**
         * wallet_stripe_payment_completed
         */
        public function stripe_payment_completed()
        {
            require_once(FELAN_PLUGIN_DIR . 'includes/partials/payment/stripe-php/init.php');
            global $current_user;
            $user_id = $current_user->ID;
            $user_email = $current_user->user_email;
            $currency_code = felan_get_option('currency_type_default', 'USD');
            $wallet_stripe_secret_key = felan_get_option('wallet_stripe_secret_key');
            $wallet_stripe_publishable_key = felan_get_option('wallet_stripe_publishable_key');
            $stripe = array(
                "secret_key" => $wallet_stripe_secret_key,
                "publishable_key" => $wallet_stripe_publishable_key
            );
            \MyStripe\Stripe::setApiKey($stripe['secret_key']);
            $stripeEmail = '';
            if (isset($_POST['stripeEmail']) && is_email($_POST['stripeEmail'])) {
                $stripeEmail = sanitize_email(wp_unslash($_POST['stripeEmail']));
            }

            $paymentId = 0;
            try {
                $token = isset($_POST['stripeToken']) ? felan_clean(wp_unslash($_POST['stripeToken'])) : '';
                $wallet_price = isset($_POST['stripe_wallet_price']) ? wp_unslash($_POST['stripe_wallet_price']) :  0;
                $user_role = isset($_POST['stripe_user_role']) ? wp_unslash($_POST['stripe_user_role']) :  '';
                $stripe_amount = intval($wallet_price) * 100;

                $customer = \MyStripe\Customer::create(array(
                    "email" => $stripeEmail,
                    "source" => $token
                ));

                $charge = \MyStripe\Charge::create(array(
                    "amount" => $stripe_amount,
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
                    $this->felan_order->insert_wallet_order($user_id, $user_role, $wallet_price, 'stripe', 'approve');
                    felan_update_withdraw_total_price($wallet_price, $user_role);
                    $args = array();
                    felan_send_email($user_email, 'mail_activated_wallet_package', $args);

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
            $payment_method = 'paypal';
            try {
                if (isset($_GET['token']) && isset($_GET['PayerID'])) {
                    $payerId = wp_kses(felan_clean(wp_unslash($_GET['PayerID'])), $allowed_html);
                    $transfered_data = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'wallet_paypal_transfer', true);
                    if (empty($transfered_data)) {
                        return;
                    }
                    $payment_execute_url = $transfered_data['payment_execute_url'];
                    $token = $transfered_data['access_token'];
                    $wallet_price = $transfered_data['wallet_price'];
                    $user_role = $transfered_data['user_role'];

                    $payment_execute = array(
                        'payer_id' => $payerId
                    );
                    $json = json_encode($payment_execute);
                    $json_response = $this->execute_paypal_request($payment_execute_url, $json, $token);
                    delete_user_meta($user_id, FELAN_METABOX_PREFIX . 'wallet_paypal_transfer');
                    if ($json_response['state'] == 'approved') {
                        $this->felan_order->insert_wallet_order($user_id, $user_role, $wallet_price, $payment_method, 'approve');
                        felan_update_withdraw_total_price($wallet_price, $user_role);
                        $args = array();
                        felan_send_email($user_email, 'mail_activated_wallet_package', $args);
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