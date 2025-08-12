<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!class_exists('Felan_Admin_service_order')) {
    /**
     * Class Felan_Admin_service_order
     */
    class Felan_Admin_service_order
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
            $columns['title'] = esc_html__('Service Name', 'felan-framework');
            $columns['buyer'] = esc_html__('Buyer', 'felan-framework');
            $columns['price'] = esc_html__('Price', 'felan-framework');
            $columns['status'] = esc_html__('Status', 'felan-framework');
            $columns['activate_date'] = esc_html__('Activate Date', 'felan-framework');
            $new_columns = array();
            $custom_order = array('cb', 'thumb', 'title', 'buyer', 'price', 'status', 'activate_date');
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
                    'meta_key' => FELAN_METABOX_PREFIX . 'service_order_payment_status',
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
            $post_status = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'service_order_payment_status', true);
            if ($post->post_type == 'service_order') {
                if ($post_status === 'inprogress') {
                    $actions['pending-order'] = '<a href="' . wp_nonce_url(add_query_arg('pending_order', $post->ID), 'pending_order') . '">' . esc_html__('Pending', 'felan-framework') . '</a>';
                } elseif ($post_status === 'pending') {
                    $actions['inprogress-order'] = '<a href="' . wp_nonce_url(add_query_arg('inprogress_order', $post->ID), 'inprogress_order') . '">' . esc_html__('Active', 'felan-framework') . '</a>';
                }
            }
            return $actions;
        }

        /**
         * Approve Service
         */
        public function service_order_inprogress()
        {
            if (!empty($_GET['inprogress_order']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'inprogress_order')) {
                $post_id = absint(felan_clean(wp_unslash($_GET['inprogress_order'])));
                update_post_meta($post_id, FELAN_METABOX_PREFIX . 'service_order_payment_status', 'inprogress');

                wp_redirect(remove_query_arg('inprogress_order', add_query_arg('inprogress_order', $post_id, admin_url('edit.php?post_type=service_order'))));
                exit;
            }
        }

        public function service_order_pending()
        {
            if (!empty($_GET['pending_order']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'pending_order')) {
                $post_id = absint(felan_clean(wp_unslash($_GET['pending_order'])));
                update_post_meta($post_id, FELAN_METABOX_PREFIX . 'service_order_payment_status', 'pending');

                wp_redirect(remove_query_arg('pending_order', add_query_arg('pending_order', $post_id, admin_url('edit.php?post_type=service_order'))));
                exit;
            }
        }

        /**
         * Display custom column for service_order
         * @param $column
         */
        public function display_custom_column($column)
        {
            global $post;
            $service_order_meta = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'service_order_meta', true);
            switch ($column) {
                case 'thumb':
                    $author_id = $service_order_meta['service_order_user_id'];
                    $service_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
                    if (!empty($service_avatar)) {
                        echo '<img src = " ' . $service_avatar . '" alt=""/>';
                    } else {
                        echo '&ndash;';
                    }
                    break;
                case 'buyer':
                    $user_info = get_userdata($service_order_meta['service_order_user_id']);
                    if ($user_info) {
                        echo '<a href="' . get_edit_user_link($service_order_meta['service_order_user_id']) . '">' . esc_attr($user_info->display_name) . '</a>';
                    }
                    break;
                case 'price':
                    echo $service_order_meta['service_order_item_price'];
                    break;
                case 'status':
                    $service_order_payment_status = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'service_order_payment_status', true);
                    if ($service_order_payment_status == 'inprogress') {
                        echo '<span class="label felan-label-violet">' . esc_html__('In Process', 'felan-framework') . '</span>';
                    } elseif ($service_order_payment_status == 'transferring') {
                        echo '<span class="label felan-label-pink">' . esc_html__('Transferring', 'felan-framework') . '</span>';
                    } elseif ($service_order_payment_status == 'canceled') {
                        echo '<span class="label felan-label-gray">' . esc_html__('Canceled', 'felan-framework') . '</span>';
                    } elseif ($service_order_payment_status == 'completed') {
                        echo '<span class="label felan-label-blue">' . esc_html__('Completed', 'felan-framework') . '</span>';
                    } elseif ($service_order_payment_status == 'expired') {
                        echo '<span class="label felan-label-gray">' . esc_html__('Expired', 'felan-framework') . '</span>';
                    } elseif ($service_order_payment_status == 'refund') {
                        echo '<span class="label felan-label-gray">' . esc_html__('Refund', 'felan-framework') . '</span>';
                    } else {
                        echo '<span class="label felan-label-yellow">' . esc_html__('Pending', 'felan-framework') . '</span>';
                    }
                    break;
                case 'activate_date':
                    $service_package_activate_date = $service_order_meta['service_order_purchase_date'];
                    echo $service_package_activate_date;
                    break;
            }
        }

        /**
         * Modify service_order slug
         * @param $existing_slug
         * @return string
         */
        public function modify_service_order_slug($existing_slug)
        {
            $service_order_url_slug = felan_get_option('service_order_url_slug');
            if ($service_order_url_slug) {
                return $service_order_url_slug;
            }
            return $existing_slug;
        }

        /**
         * Filter Restrict
         */
        public function filter_restrict_manage_service_order()
        {
            global $typenow;
            $post_type = 'service_order';
            if ($typenow == $post_type) {
                //Status
                $values = array(
                    'pending' => esc_html__('Pending', 'felan-framework'),
                    'inprogress' => esc_html__('In Process', 'felan-framework'),
                    'transferring' => esc_html__('Transferring', 'felan-framework'),
                    'canceled' => esc_html__('Canceled', 'felan-framework'),
                    'completed' => esc_html__('Completed', 'felan-framework'),
                    'expired' => esc_html__('Expired', 'felan-framework'),
                    'refund' => esc_html__('Refund', 'felan-framework'),
                );
?>
                <select name="service_order_payment_status">
                    <option value=""><?php esc_html_e('All Status', 'felan-framework'); ?></option>
                    <?php $current_v = isset($_GET['service_order_payment_status']) ? felan_clean(wp_unslash($_GET['service_order_payment_status'])) : '';
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
                <?php $service_order_user = isset($_GET['service_order_user']) ? felan_clean(wp_unslash($_GET['service_order_user'])) : ''; ?>
                <input type="text" placeholder="<?php esc_attr_e('Search user id', 'felan-framework'); ?>" name="service_order_user" value="<?php echo esc_attr($service_order_user); ?>">
<?php }
        }

        /**
         * service_order_filter
         * @param $query
         */
        public function service_order_filter($query)
        {
            global $pagenow;
            $post_type = 'service_order';
            $q_vars    = &$query->query_vars;
            $filter_arr = array();
            if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type) {
                $service_order_user = isset($_GET['service_order_user']) ? felan_clean(wp_unslash($_GET['service_order_user'])) : '';
                if ($service_order_user !== '') {
                    $filter_arr[] = array(
                        'key' => FELAN_METABOX_PREFIX . 'service_order_user_id',
                        'value' => $service_order_user,
                        'compare' => '==',
                    );
                }

                $service_order_payment_status = isset($_GET['service_order_payment_status']) ? felan_clean(wp_unslash($_GET['service_order_payment_status'])) : '';
                if ($service_order_payment_status !== '') {
                    $filter_arr[] = array(
                        'key' => FELAN_METABOX_PREFIX . 'service_order_payment_status',
                        'value' => $service_order_payment_status,
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
