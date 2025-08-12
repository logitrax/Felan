<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!class_exists('Felan_Admin_Wallet_Order')) {
    /**
     * Class Felan_Admin_Wallet_Order
     */
    class Felan_Admin_Wallet_Order
    {
        /**
         * Register custom columns
         * @param $columns
         * @return array
         */
        public function register_custom_column_titles($columns)
        {
            $columns['cb'] = "<input type=\"checkbox\" />";
            $columns['title'] = esc_html__('Order Title', 'felan-framework');
            $columns['buyer'] = esc_html__('Buyer', 'felan-framework');
            $columns['role'] = esc_html__('User Role', 'felan-framework');
            $columns['price'] = esc_html__('Price', 'felan-framework');
            $columns['payment_method'] = esc_html__('Payment', 'felan-framework');
            $columns['status'] = esc_html__('Status', 'felan-framework');
            $columns['activate_date'] = esc_html__('Activate Date', 'felan-framework');
            $new_columns = array();
            $custom_order = array('cb', 'title', 'buyer', 'role', 'price', 'payment_method', 'status', 'activate_date');
            foreach ($custom_order as $colname) {
                $new_columns[$colname] = $columns[$colname];
            }
            return $new_columns;
        }

        /**
         * sortable_columns
         * @param $columns
         * @return mixed
         */
        public function sortable_columns($columns)
        {
            $columns['status'] = 'status';
            $columns['payment_method'] = 'payment_method';
            $columns['title'] = 'title';

            $columns['date'] = 'date';
            return $columns;
        }

        /**
         * @param $vars
         * @return array
         */
        public function column_orderby($vars)
        {
            if (!is_admin())
                return $vars;

            if (isset($vars['orderby']) && 'status' == $vars['orderby']) {
                $vars = array_merge($vars, array(
                    'meta_key' => FELAN_METABOX_PREFIX . 'wallet_order_payment_status',
                    'orderby' => 'meta_value_num',
                ));
            }

            return $vars;
        }

        /**
         * @param $actions
         * @param $post
         * @return mixed
         */
        public function modify_list_row_actions($actions, $post)
        {
            // Check for your post type.
            $post_status = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'wallet_order_payment_status', true);
            if ($post->post_type == 'wallet_order') {
                if ($post_status === 'approve') {
                    $actions['pending-order'] = '<a href="' . wp_nonce_url(add_query_arg('wallet_order_pending', $post->ID), 'wallet_order_pending') . '">' . esc_html__('Pending', 'felan-framework') . '</a>';
                } elseif ($post_status === 'pending') {
                    $actions['approve-order'] = '<a href="' . wp_nonce_url(add_query_arg('wallet_order_approve', $post->ID), 'wallet_order_approve') . '">' . esc_html__('Approve', 'felan-framework') . '</a>';
                }
            }
            return $actions;
        }

        /**
         * Approve wallet
         */
        public function order_approve()
        {
            if (!empty($_GET['wallet_order_approve']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'wallet_order_approve')) {
                $post_id = absint(felan_clean(wp_unslash($_GET['wallet_order_approve'])));
                $wallet_package_id = get_post_meta($post_id, FELAN_METABOX_PREFIX . 'wallet_order_item_id', true);
                update_post_meta($post_id, FELAN_METABOX_PREFIX . 'wallet_order_payment_status', 'approve');
                update_post_meta($wallet_package_id, FELAN_METABOX_PREFIX . 'proposal_status', 'inprogress');

                $wallet_order_meta = get_post_meta($post_id, FELAN_METABOX_PREFIX . 'wallet_order_meta', true);
                $price_order = $wallet_order_meta['wallet_order_item_price'];
                $user_role = get_post_meta($post_id, FELAN_METABOX_PREFIX . 'wallet_order_user_role', true);
                $user_id = get_post_field('post_author', $post_id);

                if($user_role == 'Employer'){
                    $withdraw_price = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'employer_withdraw_total_price', true);
                } else {
                    $withdraw_price = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_total_price', true);
                }

                if (empty($withdraw_price)) {
                    $withdraw_price = 0;
                }

                $withdraw_price_new = intval($withdraw_price) + intval($price_order);
                if($user_role == 'Employer'){
                    update_user_meta($user_id, FELAN_METABOX_PREFIX . 'employer_withdraw_total_price', $withdraw_price_new);
                } else {
                    update_user_meta($user_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_total_price', $withdraw_price_new);
                }

                wp_redirect(remove_query_arg('wallet_order_approve', add_query_arg('wallet_order_approve', $post_id, admin_url('edit.php?post_type=wallet_order'))));
                exit;
            }
        }

        public function order_pending()
        {
            if (!empty($_GET['wallet_order_pending']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'wallet_order_pending')) {
                $post_id = absint(felan_clean(wp_unslash($_GET['wallet_order_pending'])));
                $wallet_package_id = get_post_meta($post_id, FELAN_METABOX_PREFIX . 'wallet_order_item_id', true);
                update_post_meta($post_id, FELAN_METABOX_PREFIX . 'wallet_order_payment_status', 'pending');
                update_post_meta($wallet_package_id, FELAN_METABOX_PREFIX . 'proposal_status', 'pending');

                wp_redirect(remove_query_arg('wallet_order_pending', add_query_arg('wallet_order_pending', $post_id, admin_url('edit.php?post_type=wallet_order'))));
                exit;
            }
        }

        /**
         * Display custom column for wallet_order
         * @param $column
         */
        public function display_custom_column($column)
        {
            global $post;
            $currency_sign_default = felan_get_option('currency_sign_default');
            $currency_position = felan_get_option('currency_position');
            $wallet_order_meta = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'wallet_order_meta', true);
            $payment_method = Felan_wallet_order::get_wallet_order_payment_method($wallet_order_meta['wallet_order_payment_method']);
            $user_role = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'wallet_order_user_role', true);

            $price_order = $wallet_order_meta['wallet_order_item_price'];
            if ($currency_position == 'before') {
                $price_order = $currency_sign_default . $price_order;
            } else {
                $price_order = $price_order . $currency_sign_default;
            }

            switch ($column) {
                case 'buyer':
                    $user_info = get_userdata($wallet_order_meta['wallet_order_user_id']);
                    if ($user_info) {
                        echo '<a href="' . get_edit_user_link($wallet_order_meta['wallet_order_user_id']) . '">' . esc_attr($user_info->display_name) . '</a>';
                    }
                    break;
                case 'role':
                    echo $user_role;
                    break;
                case 'payment_method':
                    echo $payment_method;
                    break;
                case 'price':
                    echo $price_order;
                    break;
                case 'status':
                    $wallet_order_payment_status = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'wallet_order_payment_status', true);
                    if ($wallet_order_payment_status == 'approve') {
                        echo '<span class="label felan-label-blue">' . esc_html__('Approved', 'felan-framework') . '</span>';
                    } else {
                        echo '<span class="label felan-label-yellow">' . esc_html__('Pending', 'felan-framework') . '</span>';
                    }
                    break;
                case 'activate_date':
                    $wallet_package_activate_date = $wallet_order_meta['wallet_order_purchase_date'];
                    echo $wallet_package_activate_date;
                    break;
            }
        }

        /**
         * Modify wallet_order slug
         * @param $existing_slug
         * @return string
         */
        public function modify_order_slug($existing_slug)
        {
            $wallet_order_url_slug = felan_get_option('wallet_order_url_slug');
            if ($wallet_order_url_slug) {
                return $wallet_order_url_slug;
            }
            return $existing_slug;
        }

        /**
         * Filter Restrict
         */
        public function filter_restrict_manage_wallet_order()
        {
            global $typenow;
            $post_type = 'wallet_order';
            if ($typenow == $post_type) {
                //Status
                $values = array(
                    'pending' => esc_html__('Pending', 'felan-framework'),
                    'approve' => esc_html__('Approve', 'felan-framework'),
                );
                ?>
                <select name="wallet_order_payment_status">
                    <option value=""><?php esc_html_e('All Status', 'felan-framework'); ?></option>
                    <?php $current_v = isset($_GET['wallet_order_payment_status']) ? felan_clean(wp_unslash($_GET['wallet_order_payment_status'])) : '';
                    foreach ($values as $value => $label) {
                        printf(
                            '<option value="%s"%s>%s</option>',
                            $value,
                            $value == $current_v ? ' selected="selected"' : '',
                            $label
                        );
                    }
                    ?>
                </select>
                <?php
                //Payment method
                $values = array(
                    'paypal' => esc_html__('Paypal', 'felan-framework'),
                    'stripe' => esc_html__('Stripe', 'felan-framework'),
                    'wire_Transfer' => esc_html__('Wire Transfer', 'felan-framework'),
                    'razor' => esc_html__('Razor', 'felan-framework'),
                );
                ?>
                <select name="wallet_order_payment_method">
                    <option value=""><?php esc_html_e('All Payment', 'felan-framework'); ?></option>
                    <?php $current_v = isset($_GET['wallet_order_payment_method']) ? wp_unslash(felan_clean($_GET['wallet_order_payment_method'])) : '';
                    foreach ($values as $value => $label) {
                        printf(
                            '<option value="%s"%s>%s</option>',
                            $value,
                            $value == $current_v ? ' selected="selected"' : '',
                            $label
                        );
                    }
                    ?>
                </select>

                <?php
                //User Role
                $enable_post_type_jobs = felan_get_option('enable_post_type_jobs','1');
                $user_role = array(
                    'Employer' => esc_html__('Employer', 'felan-framework'),
                );
                if($enable_post_type_jobs == '1'){
                    $user_role['Candidate'] = esc_html__('Candidate', 'felan-framework');
                } else {
                    $user_role['Freelancer'] = esc_html__('Freelancer', 'felan-framework');
                }
                ?>
                <select name="wallet_order_urer_role">
                    <option value=""><?php esc_html_e('User Role', 'felan-framework'); ?></option>
                    <?php $current_role = isset($_GET['wallet_order_urer_role']) ? wp_unslash(felan_clean($_GET['wallet_order_urer_role'])) : '';
                    foreach ($user_role as $value => $label) {
                        printf(
                            '<option value="%s"%s>%s</option>',
                            $value,
                            $value == $current_role ? ' selected="selected"' : '',
                            $label
                        );
                    }
                    ?>
                </select>
            <?php }
        }

        /**
         * wallet_order_filter
         * @param $query
         */
        public function order_filter($query)
        {
            global $pagenow;
            $post_type = 'wallet_order';
            $q_vars    = &$query->query_vars;
            $filter_arr = array();
            if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type) {
                $wallet_order_urer_role = isset($_GET['wallet_order_urer_role']) ? felan_clean(wp_unslash($_GET['wallet_order_urer_role'])) : '';
                if ($wallet_order_urer_role !== '') {
                    $filter_arr[] = array(
                        'key' => FELAN_METABOX_PREFIX . 'wallet_order_user_role',
                        'value' => $wallet_order_urer_role,
                        'compare' => '=',
                    );
                }

                $wallet_order_payment_status = isset($_GET['wallet_order_payment_status']) ? felan_clean(wp_unslash($_GET['wallet_order_payment_status'])) : '';
                if ($wallet_order_payment_status !== '') {
                    $filter_arr[] = array(
                        'key' => FELAN_METABOX_PREFIX . 'wallet_order_payment_status',
                        'value' => $wallet_order_payment_status,
                        'compare' => '=',
                    );
                }

                $wallet_order_payment_method = isset($_GET['wallet_order_payment_method']) ? felan_clean(wp_unslash($_GET['wallet_order_payment_method'])) : '';
                if ($wallet_order_payment_method !== '') {
                    $filter_arr[] = array(
                        'key' => FELAN_METABOX_PREFIX . 'wallet_order_payment_method',
                        'value' => $wallet_order_payment_method,
                        'compare' => '=',
                    );
                }

                if (!empty($filter_arr)) {
                    $q_vars['meta_query'] = $filter_arr;
                }
            }
        }
    }
}
