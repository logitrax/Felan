<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!class_exists('Felan_Admin_freelancer_order')) {
    /**
     * Class Felan_Admin_freelancer_order
     */
    class Felan_Admin_freelancer_order
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
            $columns['buyer'] = esc_html__('Buyer', 'felan-framework');
            $columns['name_package'] = esc_html__('Package Name', 'felan-framework');
            $columns['price'] = esc_html__('Price', 'felan-framework');
            $columns['payment_method'] = esc_html__('Payment', 'felan-framework');
            $columns['status'] = esc_html__('Status', 'felan-framework');
            $columns['activate_date'] = esc_html__('Activate Date', 'felan-framework');
            $columns['expires_date'] = esc_html__('Expires Date', 'felan-framework');
            $new_columns = array();
            $custom_order = array('cb', 'thumb', 'title', 'buyer', 'name_package', 'price', 'payment_method', 'status', 'activate_date', 'expires_date');
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
                    'meta_key' => FELAN_METABOX_PREFIX . 'freelancer_order_status',
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
            $post_status = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'freelancer_order_payment_status', true);
            if ($post->post_type == 'freelancer_order') {
                if ($post_status == 1) {
                    $actions['freelancer_order-pending'] = '<a href="' . wp_nonce_url(add_query_arg('pending_order', $post->ID), 'pending_order') . '">' . esc_html__('Pending', 'felan-framework') . '</a>';
                } else {
                    $actions['freelancer_order-active'] = '<a href="' . wp_nonce_url(add_query_arg('active_order', $post->ID), 'active_order') . '">' . esc_html__('Active', 'felan-framework') . '</a>';
                }
            }
            return $actions;
        }

        /**
         * Approve Service
         */
        public function freelancer_order_active()
        {
            if (!empty($_GET['active_order']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'active_order')) {
                $current_date = date('Y-m-d');
                $post_id = absint(felan_clean(wp_unslash($_GET['active_order'])));
                $package_user_id = get_post_meta($post_id, FELAN_METABOX_PREFIX . 'package_user_id', true);

                update_post_meta($post_id, FELAN_METABOX_PREFIX . 'freelancer_order_payment_status', 1);
                update_post_meta($post_id, FELAN_METABOX_PREFIX . 'freelancer_order_date', $current_date);
                update_user_meta($package_user_id, FELAN_METABOX_PREFIX . 'package_activate_date', $current_date);

                wp_redirect(remove_query_arg('active_order', add_query_arg('active_order', $post_id, admin_url('edit.php?post_type=freelancer_order'))));
                exit;
            }
        }

        public function freelancer_order_pending()
        {
            if (!empty($_GET['pending_order']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'pending_order')) {
                $post_id = absint(felan_clean(wp_unslash($_GET['pending_order'])));
                update_post_meta($post_id, FELAN_METABOX_PREFIX . 'freelancer_order_payment_status', 0);

                wp_redirect(remove_query_arg('pending_order', add_query_arg('pending_order', $post_id, admin_url('edit.php?post_type=freelancer_order'))));
                exit;
            }
        }

        /**
         * Display custom column for freelancer_order
         * @param $column
         */
        public function display_custom_column($column)
        {
            global $post;
            $freelancer_order_meta = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'freelancer_order_meta', true);
            switch ($column) {
                case 'thumb':
                    $author_id = $freelancer_order_meta['freelancer_order_user_id'];
                    $freelancer_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
                    if (!empty($freelancer_avatar)) {
                        echo '<img src = " ' . $freelancer_avatar . '" alt=""/>';
                    } else {
                        echo '&ndash;';
                    }
                    break;
                case 'buyer':
                    $user_info = get_userdata($freelancer_order_meta['freelancer_order_user_id']);
                    if ($user_info) {
                        esc_html_e($user_info->display_name);
                    }
                    break;
                case 'name_package':
                    $freelancer_package_id = $freelancer_order_meta['freelancer_order_item_id'];
                    $name_package = get_the_title($freelancer_package_id);
                    echo $name_package;
                    break;
                case 'payment_method':
                    echo Felan_freelancer_order::get_freelancer_order_payment_method($freelancer_order_meta['freelancer_order_payment_method']);
                    break;
                case 'price':
                    echo $freelancer_order_meta['freelancer_order_item_price'];
                    break;
                case 'status':
                    $freelancer_order_status = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'freelancer_order_payment_status', true);
                    if ($freelancer_order_status == 0) {
                        echo '<span class="label felan-label-red">' . esc_html__('Pending', 'felan-framework') . '</span>';
                    } else {
                        echo '<span class="label felan-label-blue">' . esc_html__('Active', 'felan-framework') . '</span>';
                    }
                    break;
                case 'activate_date':
                    $freelancer_package_activate_date = $freelancer_order_meta['freelancer_order_purchase_date'];
                    echo $freelancer_package_activate_date;
                    break;
                case 'expires_date':
                    $expired_time = '';
                    $freelancer_package_id = $freelancer_order_meta['freelancer_order_item_id'];
                    $freelancer_package_time_unit = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'freelancer_package_time_unit', true);
                    $freelancer_package_period = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'freelancer_package_period', true);
                    $freelancer_package_activate_date = strtotime($freelancer_order_meta['freelancer_order_purchase_date']);
                    $seconds = 0;
                    switch ($freelancer_package_time_unit) {
                        case 'Day':
                            $seconds = 60 * 60 * 24;
                            break;
                        case 'Week':
                            $seconds = 60 * 60 * 24 * 7;
                            break;
                        case 'Month':
                            $seconds = 60 * 60 * 24 * 30;
                            break;
                        case 'Year':
                            $seconds = 60 * 60 * 24 * 365;
                            break;
                    }
                    if (is_numeric($freelancer_package_activate_date) && is_numeric($seconds) && is_numeric($freelancer_package_period)) {
                        $expired_time = $freelancer_package_activate_date + ($seconds * $freelancer_package_period);
                    }
                    $enable_package_service_unlimited_time = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'enable_package_service_unlimited_time', true);
                    if ($enable_package_service_unlimited_time == 1) {
                        $expired_date = esc_html__('Never Expires');
                    } else {
                        $expired_date = date_i18n('Y-m-d', $expired_time);
                    }
                    echo $expired_date;
                    break;
            }
        }

        /**
         * Modify freelancer_order slug
         * @param $existing_slug
         * @return string
         */
        public function modify_freelancer_order_slug($existing_slug)
        {
            $freelancer_order_url_slug = felan_get_option('freelancer_order_url_slug');
            if ($freelancer_order_url_slug) {
                return $freelancer_order_url_slug;
            }
            return $existing_slug;
        }

        /**
         * Filter Restrict
         */
        public function filter_restrict_manage_freelancer_order()
        {
            global $typenow;
            $post_type = 'freelancer_order';
            if ($typenow == $post_type) {
                //Invoice Status
                $values = array(
                    '0' => esc_html__('Pending', 'felan-framework'),
                    '1' => esc_html__('Active', 'felan-framework'),
                );
?>
                <select name="freelancer_order_status">
                    <option value=""><?php esc_html_e('All Status', 'felan-framework'); ?></option>
                    <?php $current_v = isset($_GET['freelancer_order_status']) ? felan_clean(wp_unslash($_GET['freelancer_order_status'])) : '';
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
                    'Paypal' => esc_html__('Paypal', 'felan-framework'),
                    'Stripe' => esc_html__('Stripe', 'felan-framework'),
                    'Wire_Transfer' => esc_html__('Wire Transfer', 'felan-framework'),
                    'Free_Package' => esc_html__('Free Package', 'felan-framework'),
                );
                ?>
                <select name="freelancer_order_payment_method">
                    <option value=""><?php esc_html_e('All Payment', 'felan-framework'); ?></option>
                    <?php $current_v = isset($_GET['freelancer_order_payment_method']) ? wp_unslash(felan_clean($_GET['freelancer_order_payment_method'])) : '';
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
                <?php $freelancer_order_user = isset($_GET['freelancer_order_user']) ? felan_clean(wp_unslash($_GET['freelancer_order_user'])) : ''; ?>
                <input type="text" placeholder="<?php esc_attr_e('Buyer', 'felan-framework'); ?>" name="freelancer_order_user" value="<?php echo esc_attr($freelancer_order_user); ?>">
<?php }
        }

        /**
         * freelancer_order_filter
         * @param $query
         */
        public function freelancer_order_filter($query)
        {
            global $pagenow;
            $post_type = 'freelancer_order';
            $q_vars    = &$query->query_vars;
            $filter_arr = array();
            if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type) {
                $freelancer_order_user = isset($_GET['freelancer_order_user']) ? felan_clean(wp_unslash($_GET['freelancer_order_user'])) : '';
                if ($freelancer_order_user !== '') {
                    $user = get_user_by('login', $freelancer_order_user);
                    $user_id = -1;
                    if ($user) {
                        $user_id = $user->ID;
                    }
                    $filter_arr[] = array(
                        'key' => FELAN_METABOX_PREFIX . 'freelancer_order_user_id',
                        'value' => $user_id,
                        'compare' => 'IN',
                    );
                }

                $freelancer_order_status = isset($_GET['freelancer_order_status']) ? felan_clean(wp_unslash($_GET['freelancer_order_status'])) : '';
                if ($freelancer_order_status !== '') {
                    $freelancer_order_status = 0;
                    if ($freelancer_order_status == '1') {
                        $freelancer_order_status = 1;
                    }
                    $filter_arr[] = array(
                        'key' => FELAN_METABOX_PREFIX . 'freelancer_order_status',
                        'value' => $freelancer_order_status,
                        'compare' => '=',
                    );
                }

                $freelancer_order_payment_method = isset($_GET['freelancer_order_payment_method']) ? felan_clean(wp_unslash($_GET['freelancer_order_payment_method'])) : '';
                if ($freelancer_order_payment_method !== '') {
                    $filter_arr[] = array(
                        'key' => FELAN_METABOX_PREFIX . 'freelancer_order_payment_method',
                        'value' => $freelancer_order_payment_method,
                        'compare' => '=',
                    );
                }
            }
        }
    }
}
