<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!class_exists('Felan_Admin_freelancer_withdraw')) {
    /**
     * Class Felan_Admin_freelancer_withdraw
     */
    class Felan_Admin_freelancer_withdraw
    {
        /**
         * Register custom columns
         * @param $columns
         * @return array
         */
        public function register_custom_column_titles($columns)
        {
            $columns['cb'] = "<input type=\"checkbox\" />";
            $columns['thumb'] = esc_html__('Avatar', 'felan-framework');
            $columns['title'] = esc_html__('Title', 'felan-framework');
            $columns['price'] = esc_html__('Price', 'felan-framework');
            $columns['payment_method'] = esc_html__('Payment', 'felan-framework');
            $columns['status'] = esc_html__('Status', 'felan-framework');
            $columns['request_date'] = esc_html__('Request Date', 'felan-framework');
            $columns['process_date'] = esc_html__('Process Date', 'felan-framework');
            $new_columns = array();
            $custom_order = array('cb', 'thumb', 'title', 'price', 'payment_method', 'status', 'request_date', 'process_date');
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
                    'meta_key' => FELAN_METABOX_PREFIX . 'freelancer_withdraw_status',
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
            $post_status = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'freelancer_withdraw_status', true);
            $post_price = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'freelancer_withdraw_price', true);
            $total_price = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'freelancer_withdraw_total_price', true);
            if ($post->post_type == 'freelancer_withdraw') {
                if ($post_price > $total_price) {
                    $actions['ex-withdraw'] = '<span>' . esc_html__('Not enough balance', 'felan-framework') . '</span>';
                } else {
                    if ($post_status === 'pending') {
                        $actions['completed-withdraw'] = '<a href="' . wp_nonce_url(add_query_arg('completed_withdraw', $post->ID), 'completed_withdraw') . '">' . esc_html__('Completed', 'felan-framework') . '</a>';
                        $actions['canceled-withdraw'] = '<a href="' . wp_nonce_url(add_query_arg('canceled_withdraw', $post->ID), 'canceled_withdraw') . '">' . esc_html__('Canceled', 'felan-framework') . '</a>';
                    } elseif ($post_status === 'completed') {
                        $actions['pending-withdraw'] = '<a href="' . wp_nonce_url(add_query_arg('pending_withdraw', $post->ID), 'pending_withdraw') . '">' . esc_html__('Pending', 'felan-framework') . '</a>';
                    }
                }
            }
            return $actions;
        }

        public function freelancer_withdraw_active()
        {
            if (!empty($_GET['completed_withdraw']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'completed_withdraw')) {
                $post_id = absint(felan_clean(wp_unslash($_GET['completed_withdraw'])));
                $author_id = get_post_meta($post_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_user_id', true);
                $total_price = get_user_meta($author_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_total_price', true);
                $post_price = get_post_meta($post_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_price', true);
                $current_date = date('Y-m-d');

                update_post_meta($post_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_status', 'completed');
                update_post_meta($post_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_process_date', $current_date);
                if ($total_price >= $post_price) {
					$enable_freelancer_withdrawal_fee = get_post_meta($post_id, FELAN_METABOX_PREFIX . 'freelancer_enable_freelancer_withdrawal_fee', true);
					$freelancer_number_withdrawal_fee = get_post_meta($post_id, FELAN_METABOX_PREFIX . 'freelancer_withdrawal_fee', true);
					if ($enable_freelancer_withdrawal_fee == '1' || !empty($freelancer_number_withdrawal_fee) || $freelancer_number_withdrawal_fee !== '0') {
						$post_price = intval($post_price) + intval($freelancer_number_withdrawal_fee);
					}
                    $price = $total_price - $post_price;
                    update_user_meta($author_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_total_price', $price);
                }

                wp_redirect(remove_query_arg('completed_withdraw', add_query_arg('completed_withdraw', $post_id, admin_url('edit.php?post_type=freelancer_withdraw'))));
                exit;
            }
        }

        public function freelancer_withdraw_pending()
        {
            if (!empty($_GET['pending_withdraw']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'pending_withdraw')) {
                $post_id = absint(felan_clean(wp_unslash($_GET['pending_withdraw'])));
                update_post_meta($post_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_status', 'pending');

                wp_redirect(remove_query_arg('pending_withdraw', add_query_arg('pending_withdraw', $post_id, admin_url('edit.php?post_type=freelancer_withdraw'))));
                exit;
            }
        }

        public function freelancer_withdraw_canceled()
        {
            if (!empty($_GET['canceled_withdraw']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'canceled_withdraw')) {
                $post_id = absint(felan_clean(wp_unslash($_GET['canceled_withdraw'])));
                $current_date = date('Y-m-d');
                update_post_meta($post_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_status', 'canceled');
                update_post_meta($post_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_process_date', $current_date);

                wp_redirect(remove_query_arg('canceled_withdraw', add_query_arg('canceled_withdraw', $post_id, admin_url('edit.php?post_type=freelancer_withdraw'))));
                exit;
            }
        }

        /**
         * Display custom column for freelancer_withdraw
         * @param $column
         */
        public function display_custom_column($column)
        {
            global $post;
            $author_id = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'freelancer_withdraw_user_id', true);
            $payment_method = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'freelancer_withdraw_payment_method', true);
            $payment_method = str_replace(['-', '_'], ' ', $payment_method);
            $price = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'freelancer_withdraw_price', true);
            $currency_position = felan_get_option('currency_position');
            $currency_sign_default = felan_get_option('currency_sign_default');
            if ($currency_position == 'before') {
                $price = $currency_sign_default . $price;
            } else {
                $price = $price . $currency_sign_default;
            }
            $service_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
            $request_date = get_the_date('Y-m-d');
            $process_date = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'freelancer_withdraw_process_date', true);
            if (empty($process_date)) {
                $process_date = '...';
            }
            switch ($column) {
                case 'thumb':
                    if (!empty($service_avatar)) {
                        echo '<img src = " ' . $service_avatar . '" alt=""/>';
                    } else {
                        echo '&ndash;';
                    }
                    break;
                case 'price':
                    echo $price;
                    break;
                case 'payment_method':
                    echo $payment_method;
                    echo '<a href="' . get_edit_user_link($author_id) . '">' . esc_html__(' (View)', 'felan-framework') . '</a>';
                    break;
                case 'status':
                    $freelancer_withdraw_status = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'freelancer_withdraw_status', true);
                    if ($freelancer_withdraw_status == 'completed') {
                        echo '<span class="label felan-label-blue">' . esc_html__('Completed', 'felan-framework') . '</span>';
                    } elseif ($freelancer_withdraw_status == 'canceled') {
                        echo '<span class="label felan-label-gray">' . esc_html__('Canceled', 'felan-framework') . '</span>';
                    } else {
                        echo '<span class="label felan-label-yellow">' . esc_html__('Pending', 'felan-framework') . '</span>';
                    }
                    break;
                case 'request_date':
                    echo $request_date;
                    break;
                case 'process_date':
                    echo $process_date;
                    break;
            }
        }

        /**
         * Modify freelancer_withdraw slug
         * @param $existing_slug
         * @return string
         */
        public function modify_freelancer_withdraw_slug($existing_slug)
        {
            $freelancer_withdraw_url_slug = felan_get_option('freelancer_withdraw_url_slug');
            if ($freelancer_withdraw_url_slug) {
                return $freelancer_withdraw_url_slug;
            }
            return $existing_slug;
        }

        /**
         * Filter Restrict
         */
        public function filter_restrict_manage_freelancer_withdraw()
        {
            global $typenow;
            $post_type = 'freelancer_withdraw';
            if ($typenow == $post_type) {
                //Status
                $values = array(
                    'completed' => esc_html__('Completed', 'felan-framework'),
                    'pending' => esc_html__('Pending', 'felan-framework'),
                    'canceled' => esc_html__('Canceled', 'felan-framework'),
                );
?>
                <select name="freelancer_withdraw_status">
                    <option value=""><?php esc_html_e('All Status', 'felan-framework'); ?></option>
                    <?php $current_v = isset($_GET['freelancer_withdraw_status']) ? felan_clean(wp_unslash($_GET['freelancer_withdraw_status'])) : '';
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
                    'wire_transfer' => esc_html__('Wire Transfer', 'felan-framework'),
                );
                ?>
                <select name="freelancer_withdraw_payment_method">
                    <option value=""><?php esc_html_e('All Payment', 'felan-framework'); ?></option>
                    <?php $current_v = isset($_GET['freelancer_withdraw_payment_method']) ? wp_unslash(felan_clean($_GET['freelancer_withdraw_payment_method'])) : '';
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
                <?php $freelancer_withdraw_user = isset($_GET['freelancer_withdraw_user']) ? felan_clean(wp_unslash($_GET['freelancer_withdraw_user'])) : ''; ?>
                <input type="text" placeholder="<?php esc_attr_e('Search user id', 'felan-framework'); ?>" name="freelancer_withdraw_user" value="<?php echo esc_attr($freelancer_withdraw_user); ?>">
<?php }
        }

        /**
         * freelancer_withdraw_filter
         * @param $query
         */
        public function freelancer_withdraw_filter($query)
        {
            global $pagenow;
            $post_type = 'freelancer_withdraw';
            $q_vars    = &$query->query_vars;
            $filter_arr = array();
            if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type) {
                $freelancer_withdraw_user = isset($_GET['freelancer_withdraw_user']) ? felan_clean(wp_unslash($_GET['freelancer_withdraw_user'])) : '';
                if ($freelancer_withdraw_user !== '') {
                    $filter_arr[] = array(
                        'key' => FELAN_METABOX_PREFIX . 'freelancer_withdraw_user_id',
                        'value' => $freelancer_withdraw_user,
                        'compare' => '=',
                    );
                }

                $freelancer_withdraw_status = isset($_GET['freelancer_withdraw_status']) ? felan_clean(wp_unslash($_GET['freelancer_withdraw_status'])) : '';
                if ($freelancer_withdraw_status !== '') {
                    $filter_arr[] = array(
                        'key' => FELAN_METABOX_PREFIX . 'freelancer_withdraw_status',
                        'value' => $freelancer_withdraw_status,
                        'compare' => '=',
                    );
                }

                $freelancer_withdraw_payment_method = isset($_GET['freelancer_withdraw_payment_method']) ? felan_clean(wp_unslash($_GET['freelancer_withdraw_payment_method'])) : '';
                if ($freelancer_withdraw_payment_method !== '') {
                    $filter_arr[] = array(
                        'key' => FELAN_METABOX_PREFIX . 'freelancer_withdraw_payment_method',
                        'value' => $freelancer_withdraw_payment_method,
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
