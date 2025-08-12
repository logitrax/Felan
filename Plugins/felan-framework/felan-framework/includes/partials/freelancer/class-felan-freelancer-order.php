<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('Felan_Freelancer_Order')) {
    /**
     * Class Felan_Freelancer_Order
     */
    class Felan_Freelancer_Order
    {
        /**
         * Get total my freelancer_order
         * @return int
         */
        public function get_total_my_freelancer_order()
        {
            $args = array(
                'post_type' => 'freelancer_order',
                'meta_query' => array(
                    array(
                        'key' => FELAN_METABOX_PREFIX . 'freelancer_order_user_id',
                        'value' => get_current_user_id(),
                        'compare' => '='
                    )
                )
            );
            $freelancer_orders = new WP_Query($args);
            wp_reset_postdata();
            return $freelancer_orders->found_posts;
        }

        /**
         * Insert freelancer_order
         * @param $payment_type
         * @param $item_id
         * @param $user_id
         * @param $payment_for
         * @param $payment_method
         * @param int $paid
         * @param string $payment_id
         * @param string $payer_id
         * @return int|WP_Error
         */
        public function insert_freelancer_order($payment_type, $item_id, $user_id, $payment_for, $payment_method, $paid = 0, $payment_id = '', $payer_id = '')
        {
            $package_free = get_post_meta($item_id, FELAN_METABOX_PREFIX . 'freelancer_package_free', true);
            if ($package_free == 1) {
                $total_money = 0;
            } else {
                $total_money = get_post_meta($item_id, FELAN_METABOX_PREFIX . 'freelancer_package_price', true);
            }
            $time = time();
            $freelancer_order_date = date('Y-m-d', $time);

            $felan_meta = array();
            $felan_meta['freelancer_order_item_id'] = $item_id;
            $felan_meta['freelancer_order_item_price'] = $total_money;
            $felan_meta['freelancer_order_purchase_date'] = $freelancer_order_date;
            $felan_meta['freelancer_order_user_id'] = $user_id;
            $felan_meta['freelancer_order_payment_method'] = $payment_method;
            $felan_meta['trans_payment_id'] = $payment_id;
            $felan_meta['trans_payer_id'] = $payer_id;
            $posttitle = 'Order_' . $payment_method . '_' . $total_money . $user_id;
            $args = array(
                'post_title'    => $posttitle,
                'post_status'    => 'publish',
                'post_type'     => 'freelancer_order'
            );

            $rw = felan_get_page_by_title($posttitle, 'freelancer_order');
            $freelancer_order_payment_status = get_post_meta($rw->ID, FELAN_METABOX_PREFIX . 'freelancer_order_payment_status', true);

            $enable_admin_approval_package = felan_get_option('enable_admin_approval_package','1');
            $felan_freelancer_package = new felan_freelancer_package();
            $felan_freelancer_package->insert_user_freelancer_package($user_id, $item_id);

            if (empty($rw->ID) || ($rw->ID && $freelancer_order_payment_status == '1')) {
                $freelancer_order_id =  wp_insert_post($args);
                update_post_meta($freelancer_order_id, FELAN_METABOX_PREFIX . 'freelancer_order_user_id', $user_id);
                update_post_meta($freelancer_order_id, FELAN_METABOX_PREFIX . 'freelancer_order_item_id', $item_id);
                update_post_meta($freelancer_order_id, FELAN_METABOX_PREFIX . 'freelancer_order_price', $total_money);
                update_post_meta($freelancer_order_id, FELAN_METABOX_PREFIX . 'freelancer_order_date', $freelancer_order_date);
                update_post_meta($freelancer_order_id, FELAN_METABOX_PREFIX . 'freelancer_order_payment_method', $payment_method);

                if($enable_admin_approval_package == '1'){
                    update_post_meta($freelancer_order_id, FELAN_METABOX_PREFIX . 'freelancer_order_payment_status', $paid);
                } else {
                    update_post_meta($freelancer_order_id, FELAN_METABOX_PREFIX . 'freelancer_order_payment_status', 1);
                }

                update_post_meta($freelancer_order_id, FELAN_METABOX_PREFIX . 'trans_payment_id', $payment_id);
                update_post_meta($freelancer_order_id, FELAN_METABOX_PREFIX . 'trans_payer_id', $payer_id);

                update_post_meta($freelancer_order_id, FELAN_METABOX_PREFIX . 'freelancer_order_meta', $felan_meta);
                $update_post = array(
                    'ID'         => $freelancer_order_id,
                );
                wp_update_post($update_post);
            } else {
                $freelancer_order_id = $rw->ID;
            }
            return $freelancer_order_id;
        }

        /**
         * get_freelancer_order_meta
         * @param $post_id
         * @param bool|false $field
         * @return array|bool|mixed
         */
        public function get_freelancer_order_meta($post_id, $field = false)
        {
            $defaults = array(
                'freelancer_order_item_id' => '',
                'freelancer_order_item_price' => '',
                'freelancer_order_purchase_date' => '',
                'freelancer_order_user_id' => '',
                'freelancer_order_payment_method' => '',
                'trans_payment_id' => '',
                'trans_payer_id' => '',
            );
            $meta = get_post_meta($post_id, FELAN_METABOX_PREFIX . 'freelancer_order_meta', true);
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
        public static function get_freelancer_order_payment_method($payment_method)
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
                case 'Free_Package':
                    return esc_html__('Free Package', 'felan-framework');
                    break;
                case 'Woocommerce':
                    return esc_html__('Woocommerce', 'felan-framework');
                    break;
                default:
                    return '';
            }
        }
        /**
         * Print freelancer_order
         */
        public function freelancer_order_print_ajax()
        {
            if (!isset($_POST['freelancer_order_id']) || !is_numeric($_POST['freelancer_order_id'])) {
                return;
            }
            $freelancer_order_id = absint(wp_unslash($_POST['freelancer_order_id']));
            $isRTL = 'false';
            if (isset($_POST['isRTL'])) {
                $isRTL = $_POST['isRTL'];
            }
            felan_get_template('freelancer_order/freelancer_order-print.php', array('freelancer_order_id' => intval($freelancer_order_id), 'isRTL' => $isRTL));
            wp_die();
        }
    }
}
