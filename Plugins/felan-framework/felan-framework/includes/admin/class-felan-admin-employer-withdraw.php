<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!class_exists('Felan_Admin_employer_withdraw')) {
    /**
     * Class Felan_Admin_employer_withdraw
     */
    class Felan_Admin_employer_withdraw
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
                    'meta_key' => FELAN_METABOX_PREFIX . 'employer_withdraw_status',
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
            $post_status = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'employer_withdraw_status', true);
            $post_price = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'employer_withdraw_price', true);
            $total_price = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'employer_withdraw_total_price', true);
            if ($post->post_type == 'employer_withdraw') {
                if ($post_price > $total_price) {
                    $actions['ex-withdraw'] = '<span>' . esc_html__('Not enough balance', 'felan-framework') . '</span>';
                } else {
                    if ($post_status === 'pending') {
                        $actions['completed-withdraw'] = '<a href="' . wp_nonce_url(add_query_arg('employer_completed_withdraw', $post->ID), 'employer_completed_withdraw') . '">' . esc_html__('Completed', 'felan-framework') . '</a>';
                        $actions['canceled-withdraw'] = '<a href="' . wp_nonce_url(add_query_arg('employer_canceled_withdraw', $post->ID), 'employer_canceled_withdraw') . '">' . esc_html__('Canceled', 'felan-framework') . '</a>';
                    } elseif ($post_status === 'completed') {
                        $actions['pending-withdraw'] = '<a href="' . wp_nonce_url(add_query_arg('employer_pending_withdraw', $post->ID), 'employer_pending_withdraw') . '">' . esc_html__('Pending', 'felan-framework') . '</a>';
                    }
                }
            }
            return $actions;
        }

        public function employer_withdraw_active()
        {
            if (!empty($_GET['employer_completed_withdraw']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'employer_completed_withdraw')) {
                $post_id = absint(felan_clean(wp_unslash($_GET['employer_completed_withdraw'])));
                $author_id = get_post_meta($post_id, FELAN_METABOX_PREFIX . 'employer_withdraw_user_id', true);
                $total_price = get_user_meta($author_id, FELAN_METABOX_PREFIX . 'employer_withdraw_total_price', true);
                $post_price = get_post_meta($post_id, FELAN_METABOX_PREFIX . 'employer_withdraw_price', true);
                $current_date = date('Y-m-d');

                update_post_meta($post_id, FELAN_METABOX_PREFIX . 'employer_withdraw_status', 'completed');
                update_post_meta($post_id, FELAN_METABOX_PREFIX . 'employer_withdraw_process_date', $current_date);
                if ($total_price >= $post_price) {
                    $price = $total_price - $post_price;
                    update_user_meta($author_id, FELAN_METABOX_PREFIX . 'employer_withdraw_total_price', $price);
                }

                wp_redirect(remove_query_arg('employer_completed_withdraw', add_query_arg('employer_completed_withdraw', $post_id, admin_url('edit.php?post_type=employer_withdraw'))));
                exit;
            }
        }

        public function employer_withdraw_pending()
        {
            if (!empty($_GET['employer_pending_withdraw']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'employer_pending_withdraw')) {
                $post_id = absint(felan_clean(wp_unslash($_GET['employer_pending_withdraw'])));
                update_post_meta($post_id, FELAN_METABOX_PREFIX . 'employer_withdraw_status', 'pending');

                wp_redirect(remove_query_arg('employer_pending_withdraw', add_query_arg('employer_pending_withdraw', $post_id, admin_url('edit.php?post_type=employer_withdraw'))));
                exit;
            }
        }

        public function employer_withdraw_canceled()
        {
            if (!empty($_GET['employer_canceled_withdraw']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'employer_canceled_withdraw')) {
                $post_id = absint(felan_clean(wp_unslash($_GET['employer_canceled_withdraw'])));
                $current_date = date('Y-m-d');
                update_post_meta($post_id, FELAN_METABOX_PREFIX . 'employer_withdraw_status', 'canceled');
                update_post_meta($post_id, FELAN_METABOX_PREFIX . 'employer_withdraw_process_date', $current_date);

                wp_redirect(remove_query_arg('employer_canceled_withdraw', add_query_arg('employer_canceled_withdraw', $post_id, admin_url('edit.php?post_type=employer_withdraw'))));
                exit;
            }
        }

        /**
         * Display custom column for employer_withdraw
         * @param $column
         */
        public function display_custom_column($column)
        {
            global $post;
            $author_id = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'employer_withdraw_user_id', true);
            $payment_method = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'employer_withdraw_payment_method', true);
            $payment_method = str_replace(['-', '_'], ' ', $payment_method);
            $price = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'employer_withdraw_price', true);
            $currency_position = felan_get_option('currency_position');
            $currency_sign_default = felan_get_option('currency_sign_default');
            if ($currency_position == 'before') {
                $price = $currency_sign_default . $price;
            } else {
                $price = $price . $currency_sign_default;
            }
            $service_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
            $request_date = get_the_date('Y-m-d');
            $process_date = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'employer_withdraw_process_date', true);
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
                    $employer_withdraw_status = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'employer_withdraw_status', true);
                    if ($employer_withdraw_status == 'completed') {
                        echo '<span class="label felan-label-blue">' . esc_html__('Completed', 'felan-framework') . '</span>';
                    } elseif ($employer_withdraw_status == 'canceled') {
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
         * Modify employer_withdraw slug
         * @param $existing_slug
         * @return string
         */
        public function modify_employer_withdraw_slug($existing_slug)
        {
            $employer_withdraw_url_slug = felan_get_option('employer_withdraw_url_slug');
            if ($employer_withdraw_url_slug) {
                return $employer_withdraw_url_slug;
            }
            return $existing_slug;
        }

        /**
         * Filter Restrict
         */
        public function filter_restrict_manage_employer_withdraw()
        {
            global $typenow;
            $post_type = 'employer_withdraw';
            if ($typenow == $post_type) {
                //Status
                $values = array(
                    'completed' => esc_html__('Completed', 'felan-framework'),
                    'pending' => esc_html__('Pending', 'felan-framework'),
                    'canceled' => esc_html__('Canceled', 'felan-framework'),
                );
?>
                <select name="employer_withdraw_status">
                    <option value=""><?php esc_html_e('All Status', 'felan-framework'); ?></option>
                    <?php $current_v = isset($_GET['employer_withdraw_status']) ? felan_clean(wp_unslash($_GET['employer_withdraw_status'])) : '';
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
                <select name="employer_withdraw_payment_method">
                    <option value=""><?php esc_html_e('All Payment', 'felan-framework'); ?></option>
                    <?php $current_v = isset($_GET['employer_withdraw_payment_method']) ? wp_unslash(felan_clean($_GET['employer_withdraw_payment_method'])) : '';
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
                <?php $employer_withdraw_user = isset($_GET['employer_withdraw_user']) ? felan_clean(wp_unslash($_GET['employer_withdraw_user'])) : ''; ?>
                <input type="text" placeholder="<?php esc_attr_e('Search user id', 'felan-framework'); ?>" name="employer_withdraw_user" value="<?php echo esc_attr($employer_withdraw_user); ?>">
<?php }
        }

        /**
         * employer_withdraw_filter
         * @param $query
         */
        public function employer_withdraw_filter($query)
        {
            global $pagenow;
            $post_type = 'employer_withdraw';
            $q_vars    = &$query->query_vars;
            $filter_arr = array();
            if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type) {
                $employer_withdraw_user = isset($_GET['employer_withdraw_user']) ? felan_clean(wp_unslash($_GET['employer_withdraw_user'])) : '';
                if ($employer_withdraw_user !== '') {
                    $filter_arr[] = array(
                        'key' => FELAN_METABOX_PREFIX . 'employer_withdraw_user_id',
                        'value' => $employer_withdraw_user,
                        'compare' => '=',
                    );
                }

                $employer_withdraw_status = isset($_GET['employer_withdraw_status']) ? felan_clean(wp_unslash($_GET['employer_withdraw_status'])) : '';
                if ($employer_withdraw_status !== '') {
                    $filter_arr[] = array(
                        'key' => FELAN_METABOX_PREFIX . 'employer_withdraw_status',
                        'value' => $employer_withdraw_status,
                        'compare' => '=',
                    );
                }

                $employer_withdraw_payment_method = isset($_GET['employer_withdraw_payment_method']) ? felan_clean(wp_unslash($_GET['employer_withdraw_payment_method'])) : '';
                if ($employer_withdraw_payment_method !== '') {
                    $filter_arr[] = array(
                        'key' => FELAN_METABOX_PREFIX . 'employer_withdraw_payment_method',
                        'value' => $employer_withdraw_payment_method,
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
