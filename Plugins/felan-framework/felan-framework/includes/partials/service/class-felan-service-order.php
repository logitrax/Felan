<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('Felan_Service_Order')) {
    /**
     * Class Felan_Service_Order
     */
    class Felan_Service_Order
    {
        /**
         * Get total my service_order
         * @return int
         */
        public function get_total_my_service_order()
        {
            $args = array(
                'post_type' => 'service_order',
                'meta_query' => array(
                    array(
                        'key' => FELAN_METABOX_PREFIX . 'service_order_user_id',
                        'value' => get_current_user_id(),
                        'compare' => '='
                    )
                )
            );
            $service_orders = new WP_Query($args);
            wp_reset_postdata();
            return $service_orders->found_posts;
        }

        /**
         * Insert service_order
         * @param $item_id
         * @param $user_id
         * @param $payment_for
         * @param $payment_method
         * @param int $paid
         * @param string $payment_id
         * @param string $payer_id
         * @return int|WP_Error
         */
        public function insert_service_order($price_default,$package_des,$package_new,$package_addons,$total_money, $number_time, $time_type, $item_id, $user_id, $payment_method)
        {
            $service_order_date = current_time('Y-m-d H:i:s');
            $author_id = get_post_field('post_author', $item_id);
            $author_name = get_the_author_meta('display_name', $author_id);
            $status = 'pending';
            if(felan_get_option('enable_auto_approve_pending_service') == '1'){
                $status = 'inprogress';
            }
			$format_total_price = felan_get_format_money($total_money);

            $felan_meta = array();
            $felan_meta['service_order_item_id'] = $item_id;
            $felan_meta['service_order_item_price'] = $format_total_price;
            $felan_meta['service_order_item_price_not_format'] = $total_money;
            $felan_meta['service_order_purchase_date'] = $service_order_date;
            $felan_meta['service_order_user_id'] = $user_id;
            $felan_meta['service_order_author_service'] = $author_name;
            $felan_meta['service_order_payment_method'] = $payment_method;
            $felan_meta['service_order_time_type'] = $time_type;
            $felan_meta['service_order_number_time'] = $number_time;
            $felan_meta['service_order_price_default'] = $price_default;
            $felan_meta['service_order_package_des'] = $package_des;
            $felan_meta['service_order_package_new'] = $package_new;
            $felan_meta['service_order_package_addons'] = $package_addons;
            $posttitle = get_the_title($item_id);
            $args = array(
                'post_title'    => $posttitle,
                'post_status'    => 'publish',
                'post_type'     => 'service_order'
            );

            $service_order_id =  wp_insert_post($args);
            update_post_meta($service_order_id, FELAN_METABOX_PREFIX . 'service_order_user_id', $user_id);
            update_post_meta($service_order_id, FELAN_METABOX_PREFIX . 'service_order_author_service', $author_name);
            update_post_meta($service_order_id, FELAN_METABOX_PREFIX . 'service_order_author_id', $author_id);
            update_post_meta($service_order_id, FELAN_METABOX_PREFIX . 'service_order_item_id', $item_id);
            update_post_meta($service_order_id, FELAN_METABOX_PREFIX . 'service_order_time_type', $time_type);
            update_post_meta($service_order_id, FELAN_METABOX_PREFIX . 'service_order_number_time', $number_time);
            update_post_meta($service_order_id, FELAN_METABOX_PREFIX . 'service_order_price', $format_total_price);
            update_post_meta($service_order_id, FELAN_METABOX_PREFIX . 'service_order_price_not_format', $total_money);
            update_post_meta($service_order_id, FELAN_METABOX_PREFIX . 'service_order_date', $service_order_date);
            update_post_meta($service_order_id, FELAN_METABOX_PREFIX . 'service_order_payment_method', $payment_method);
            update_post_meta($service_order_id, FELAN_METABOX_PREFIX . 'service_order_payment_status', $status);
            update_post_meta($service_order_id, FELAN_METABOX_PREFIX . 'service_order_meta', $felan_meta);
            update_post_meta($service_order_id, FELAN_METABOX_PREFIX . 'service_order_price_default', $price_default);
            update_post_meta($service_order_id, FELAN_METABOX_PREFIX . 'service_order_des', $package_des);
            update_post_meta($service_order_id, FELAN_METABOX_PREFIX . 'service_order_new', $package_new);
            update_post_meta($service_order_id, FELAN_METABOX_PREFIX . 'service_order_addons', $package_addons);
            $update_post = array(
                'ID'         => $service_order_id,
            );
            wp_update_post($update_post);

			$user_employer        = get_post_meta($service_order_id, FELAN_METABOX_PREFIX . 'service_order_user_id', true);
			$user_employer        = get_user_by('id', $user_employer);
			$user_employer_name   = $user_employer->display_name;
			$user_freelancer      = get_post_meta($service_order_id, FELAN_METABOX_PREFIX . 'service_order_author_id', true);
			$user_freelancer      = get_user_by('id', $user_freelancer);
			$user_freelancer_name = $user_freelancer->display_name;
			$user_freelancer_mail = $user_freelancer->user_email;

			$felan_service_page_id = felan_get_option('felan_freelancer_service_page_id');
			$felan_service_page    = get_page_link($felan_service_page_id);
			$service_id            = get_post_meta($service_order_id, FELAN_METABOX_PREFIX . 'service_order_item_id', true);

			$args_mail = array(
				'employer_name'   => $user_employer_name,
				'freelancer_name' => $user_freelancer_name,
				'service_name'    => get_the_title($service_id),
				'order_url'    => $felan_service_page . '?order_id=' . $service_order_id,
			);

			$enable_post_type_service = felan_get_option('enable_post_type_service', '1');
			if($enable_post_type_service == '1') {
				felan_send_email($user_freelancer_mail, 'mail_service_employer_place_order', $args_mail);
				felan_get_data_ajax_notification($service_order_id, 'add-service-order');
			}

            return $service_order_id;
        }

        /**
         * get_service_order_meta
         * @param $post_id
         * @param bool|false $field
         * @return array|bool|mixed
         */
        public function get_service_order_meta($post_id, $field = false)
        {
            $defaults = array(
                'service_order_item_id' => '',
                'service_order_item_price' => '',
                'service_order_purchase_date' => '',
                'service_order_user_id' => '',
                'service_order_payment_method' => '',
                'trans_payment_id' => '',
                'trans_payer_id' => '',
            );
            $meta = get_post_meta($post_id, FELAN_METABOX_PREFIX . 'service_order_meta', true);
            $meta = wp_parse_args((array)$meta, $defaults);

            if ($field) {
                if (isset($meta[$field])) {
                    return $meta[$field];
                } else {
                    return false;
                }
            }
            return $meta;
        }

        /**
         * @param $payment_method
         * @return string
         */
        public static function get_service_order_payment_method($payment_method)
        {
            switch ($payment_method) {
                case 'Paypal':
                    return esc_html__('Paypal', 'felan-framework');
                    break;
                case 'Stripe':
                    return esc_html__('Stripe', 'felan-framework');
                    break;
                case 'Wire_Transfer':
                    return esc_html__('Wire Transfer', 'felan-framework');
                    break;
                case 'Woocommerce':
                    return esc_html__('Woocommerce', 'felan-framework');
                    break;
                default:
                    return '';
            }
        }
        /**
         * Print service_order
         */
        public function service_order_print_ajax()
        {
            if (!isset($_POST['service_order_id']) || !is_numeric($_POST['service_order_id'])) {
                return;
            }
            $service_order_id = absint(wp_unslash($_POST['service_order_id']));
            $isRTL = 'false';
            if (isset($_POST['isRTL'])) {
                $isRTL = $_POST['isRTL'];
            }
            felan_get_template('service_order/service_order-print.php', array('service_order_id' => intval($service_order_id), 'isRTL' => $isRTL));
            wp_die();
        }

        /**
         * Insert service service_package
         */
        public function insert_user_service_package($user_id, $service_package_id)
        {
            //Service
            $service_package_number_service = get_post_meta($service_package_id, FELAN_METABOX_PREFIX . 'service_package_number_service', true);
            $service_package_number_service_featured = get_post_meta($service_package_id, FELAN_METABOX_PREFIX . 'service_package_number_service_featured', true);
            $enable_package_service_unlimited = get_post_meta($service_package_id, FELAN_METABOX_PREFIX . 'enable_package_service_unlimited', true);

            if ($enable_package_service_unlimited == 1) {
                $service_package_number_service = 999999999999999999;
            }
            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'service_package_number_service', $service_package_number_service);
            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'service_package_number_service_featured', $service_package_number_service_featured);

            //Field
            $field_package = array('jobs_apply', 'jobs_wishlist', 'company_follow');
            foreach ($field_package as $field) {
                $show_field = get_post_meta($service_package_id, FELAN_METABOX_PREFIX . 'show_package_' . $field, true);
                $field_number = get_post_meta($service_package_id, FELAN_METABOX_PREFIX . 'service_package_number_' . $field, true);
                $field_unlimited = get_post_meta($service_package_id, FELAN_METABOX_PREFIX . 'enable_package_' . $field . '_unlimited', true);
                if (intval($show_field) == 1) {
                    if ($field_unlimited == 1) {
                        $field_number = 999999999999999999;
                    }
                    update_user_meta($user_id, FELAN_METABOX_PREFIX . 'service_package_number_' . $field, $field_number);
                }
            }

            $time = time();
            $date = date('Y-m-d H:i:s', $time);
            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'service_package_activate_date', $date);
            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'service_package_id', $service_package_id);
            $service_package_key = uniqid();
            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'service_package_key', $service_package_key);
        }
    }
}
