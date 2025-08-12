<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('Felan_Project_Order')) {
    /**
     * Class Felan_Project_Order
     */
    class Felan_Project_Order
    {
        /**
         * Get total my project_order
         * @return int
         */
        public function get_total_my_project_order()
        {
            $args = array(
                'post_type' => 'project_order',
                'meta_query' => array(
                    array(
                        'key' => FELAN_METABOX_PREFIX . 'project_order_user_id',
                        'value' => get_current_user_id(),
                        'compare' => '='
                    )
                )
            );
            $project_orders = new WP_Query($args);
            wp_reset_postdata();
            return $project_orders->found_posts;
        }

        /**
         * Insert project_order
         * @param $item_id
         * @param $user_id
         * @param $payment_for
         * @param $payment_method
         * @param int $paid
         * @param string $payment_id
         * @param string $payer_id
         * @return int|WP_Error
         */
        public function insert_project_order($total_money, $item_id, $user_id, $payment_method, $status = 'pending')
        {

            $time = time();
            $project_order_date = date('Y-m-d', $time);
            $author_id = get_post_field('post_author', $item_id);
            $author_name = get_the_author_meta('display_name', $author_id);
            $time_type = get_post_meta($item_id, FELAN_METABOX_PREFIX . 'project_time_type', true);
            $number_time = get_post_meta($item_id, FELAN_METABOX_PREFIX . 'project_number_time', true);

            $felan_meta = array();
            $felan_meta['project_order_item_id'] = $item_id;
            $felan_meta['project_order_item_price'] = $total_money;
            $felan_meta['project_order_purchase_date'] = $project_order_date;
            $felan_meta['project_order_user_id'] = $user_id;
            $felan_meta['project_order_author_project'] = $author_name;
            $felan_meta['project_order_payment_method'] = $payment_method;
            $felan_meta['project_order_time_type'] = $time_type;
            $felan_meta['project_order_number_time'] = $number_time;
            $posttitle = get_the_title($item_id);
            $args = array(
                'post_title'    => $posttitle,
                'post_status'    => 'publish',
                'post_type'     => 'project_order'
            );

            $project_order_id =  wp_insert_post($args);
            update_post_meta($project_order_id, FELAN_METABOX_PREFIX . 'project_order_user_id', $user_id);
            update_post_meta($project_order_id, FELAN_METABOX_PREFIX . 'project_order_author_project', $author_name);
            update_post_meta($project_order_id, FELAN_METABOX_PREFIX . 'project_order_author_id', $author_id);
            update_post_meta($project_order_id, FELAN_METABOX_PREFIX . 'project_order_item_id', $item_id);
            update_post_meta($project_order_id, FELAN_METABOX_PREFIX . 'project_order_time_type', $time_type);
            update_post_meta($project_order_id, FELAN_METABOX_PREFIX . 'project_order_number_time', $number_time);
            update_post_meta($project_order_id, FELAN_METABOX_PREFIX . 'project_order_price', $total_money);
            update_post_meta($project_order_id, FELAN_METABOX_PREFIX . 'project_order_date', $project_order_date);
            update_post_meta($project_order_id, FELAN_METABOX_PREFIX . 'project_order_payment_method', $payment_method);
            update_post_meta($project_order_id, FELAN_METABOX_PREFIX . 'project_order_payment_status', $status);
            update_post_meta($project_order_id, FELAN_METABOX_PREFIX . 'project_order_meta', $felan_meta);
            $update_post = array(
                'ID'         => $project_order_id,
            );
            wp_update_post($update_post);

            return $project_order_id;
        }

        /**
         * get_project_order_meta
         * @param $post_id
         * @param bool|false $field
         * @return array|bool|mixed
         */
        public function get_project_order_meta($post_id, $field = false)
        {
            $defaults = array(
                'project_order_item_id' => '',
                'project_order_item_price' => '',
                'project_order_purchase_date' => '',
                'project_order_user_id' => '',
                'project_order_payment_method' => '',
                'trans_payment_id' => '',
                'trans_payer_id' => '',
            );
            $meta = get_post_meta($post_id, FELAN_METABOX_PREFIX . 'project_order_meta', true);
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
        public static function get_project_order_payment_method($payment_method)
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
         * Print project_order
         */
        public function project_order_print_ajax()
        {
            if (!isset($_POST['project_order_id']) || !is_numeric($_POST['project_order_id'])) {
                return;
            }
            $project_order_id = absint(wp_unslash($_POST['project_order_id']));
            $isRTL = 'false';
            if (isset($_POST['isRTL'])) {
                $isRTL = $_POST['isRTL'];
            }
            felan_get_template('project_order/project_order-print.php', array('project_order_id' => intval($project_order_id), 'isRTL' => $isRTL));
            wp_die();
        }

        /**
         * Insert project project_package
         */
        public function insert_user_project_package($user_id, $project_package_id)
        {
            //Project
            $project_package_number_project = get_post_meta($project_package_id, FELAN_METABOX_PREFIX . 'project_package_number_project', true);
            $project_package_number_project_featured = get_post_meta($project_package_id, FELAN_METABOX_PREFIX . 'project_package_number_project_featured', true);
            $enable_package_project_unlimited = get_post_meta($project_package_id, FELAN_METABOX_PREFIX . 'enable_package_project_unlimited', true);

            if ($enable_package_project_unlimited == 1) {
                $project_package_number_project = 999999999999999999;
            }
            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'project_package_number_project', $project_package_number_project);
            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'project_package_number_project_featured', $project_package_number_project_featured);

            //Field
            $field_package = array('jobs_apply', 'jobs_wishlist', 'company_follow');
            foreach ($field_package as $field) {
                $show_field = get_post_meta($project_package_id, FELAN_METABOX_PREFIX . 'show_package_' . $field, true);
                $field_number = get_post_meta($project_package_id, FELAN_METABOX_PREFIX . 'project_package_number_' . $field, true);
                $field_unlimited = get_post_meta($project_package_id, FELAN_METABOX_PREFIX . 'enable_package_' . $field . '_unlimited', true);
                if (intval($show_field) == 1) {
                    if ($field_unlimited == 1) {
                        $field_number = 999999999999999999;
                    }
                    update_user_meta($user_id, FELAN_METABOX_PREFIX . 'project_package_number_' . $field, $field_number);
                }
            }

            $time = time();
            $date = date('Y-m-d H:i:s', $time);
            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'project_package_activate_date', $date);
            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'project_package_id', $project_package_id);
            $project_package_key = uniqid();
            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'project_package_key', $project_package_key);
        }
    }
}
