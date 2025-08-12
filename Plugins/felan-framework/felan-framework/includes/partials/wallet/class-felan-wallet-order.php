<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('Felan_Wallet_Order')) {
    /**
     * Class Felan_Wallet_Order
     */
    class Felan_Wallet_Order
    {
        /**
         * Insert wallet_order
         * @param $item_id
         * @param $user_id
         * @param $payment_for
         * @param $payment_method
         * @param int $paid
         * @param string $payment_id
         * @param string $payer_id
         * @return int|WP_Error
         */
        public function insert_wallet_order($user_id, $user_role, $wallet_price, $payment_method, $status)
        {
            $time = time();
            $wallet_order_date = date('Y-m-d', $time);
            $author_name = get_the_author_meta('display_name', $user_id);
            $random_number = rand(100, 999);

            $felan_meta = array();
            $felan_meta['wallet_order_item_price'] = $wallet_price;
            $felan_meta['wallet_order_purchase_date'] = $wallet_order_date;
            $felan_meta['wallet_order_user_id'] = $user_id;
            $felan_meta['wallet_order_author_wallet'] = $author_name;
            $felan_meta['wallet_order_payment_method'] = $payment_method;
            $posttitle = 'Wallet_Order_' . $user_id . '_' . $random_number;
            $args = array(
                'post_title'    => $posttitle,
                'post_status'    => 'publish',
                'post_type'     => 'wallet_order'
            );

            $wallet_order_id =  wp_insert_post($args);
            update_post_meta($wallet_order_id, FELAN_METABOX_PREFIX . 'wallet_order_user_id', $user_id);
            update_post_meta($wallet_order_id, FELAN_METABOX_PREFIX . 'wallet_order_author', $author_name);
            update_post_meta($wallet_order_id, FELAN_METABOX_PREFIX . 'wallet_order_author_id', $user_id);
            update_post_meta($wallet_order_id, FELAN_METABOX_PREFIX . 'wallet_order_user_role', $user_role);
            update_post_meta($wallet_order_id, FELAN_METABOX_PREFIX . 'wallet_order_price', $wallet_price);
            update_post_meta($wallet_order_id, FELAN_METABOX_PREFIX . 'wallet_order_date', $wallet_order_date);
            update_post_meta($wallet_order_id, FELAN_METABOX_PREFIX . 'wallet_order_payment_method', $payment_method);
            update_post_meta($wallet_order_id, FELAN_METABOX_PREFIX . 'wallet_order_payment_status', $status);
            update_post_meta($wallet_order_id, FELAN_METABOX_PREFIX . 'wallet_order_meta', $felan_meta);
            $update_post = array(
                'ID'         => $wallet_order_id,
            );
            wp_update_post($update_post);

            return $wallet_order_id;
        }

        /**
         * @param $payment_method
         * @return string
         */
        public static function get_wallet_order_payment_method($payment_method)
        {
            switch ($payment_method) {
                case 'paypal':
                    return esc_html__('Paypal', 'felan-framework');
                    break;
                case 'stripe':
                    return esc_html__('Stripe', 'felan-framework');
                    break;
                case 'wire-transfer':
                    return esc_html__('Wire Transfer', 'felan-framework');
                    break;
                case 'razor':
                    return esc_html__('Razor', 'felan-framework');
                    break;
                case 'woocommerce':
                    return esc_html__('Woocommerce', 'felan-framework');
                    break;
                default:
                    return '';
            }
        }

        /**
         * get_wallet_order_meta
         * @param $post_id
         * @param bool|false $field
         * @return array|bool|mixed
         */
        public function get_wallet_order_meta($post_id, $field = false)
        {
            $defaults = array(
                'wallet_order_item_id' => '',
                'wallet_order_item_price' => '',
                'wallet_order_purchase_date' => '',
                'wallet_order_user_id' => '',
                'wallet_order_payment_method' => '',
                'trans_payment_id' => '',
                'trans_payer_id' => '',
            );
            $meta = get_post_meta($post_id, FELAN_METABOX_PREFIX . 'wallet_order_meta', true);
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
    }
}
