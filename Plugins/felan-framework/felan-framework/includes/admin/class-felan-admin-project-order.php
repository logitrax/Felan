<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!class_exists('Felan_Admin_project_order')) {
    /**
     * Class Felan_Admin_project_order
     */
    class Felan_Admin_project_order
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
            $columns['title'] = esc_html__('Project Name', 'felan-framework');
            $columns['buyer'] = esc_html__('Buyer', 'felan-framework');
            $columns['price'] = esc_html__('Price', 'felan-framework');
            $columns['payment_method'] = esc_html__('Payment', 'felan-framework');
            $columns['status'] = esc_html__('Status', 'felan-framework');
            $columns['activate_date'] = esc_html__('Activate Date', 'felan-framework');
            $new_columns = array();
            $custom_order = array('cb', 'thumb', 'title', 'buyer', 'price', 'payment_method', 'status', 'activate_date');
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
                    'meta_key' => FELAN_METABOX_PREFIX . 'project_order_payment_status',
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
            $post_status = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'project_order_payment_status', true);
            if ($post->post_type == 'project_order') {
                if ($post_status === 'approved') {
                    $actions['pending-order'] = '<a href="' . wp_nonce_url(add_query_arg('project_order_pending', $post->ID), 'project_order_pending') . '">' . esc_html__('Pending', 'felan-framework') . '</a>';
                } elseif ($post_status === 'pending') {
                    $actions['approved-order'] = '<a href="' . wp_nonce_url(add_query_arg('project_order_approved', $post->ID), 'project_order_approved') . '">' . esc_html__('Approve', 'felan-framework') . '</a>';
                }
            }
            return $actions;
        }

        /**
         * Approve Project
         */
        public function project_order_approved()
        {
            if (!empty($_GET['project_order_approved']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'project_order_approved')) {
                $post_id = absint(felan_clean(wp_unslash($_GET['project_order_approved'])));
                $project_package_id = get_post_meta($post_id, FELAN_METABOX_PREFIX . 'project_order_item_id', true);
                update_post_meta($post_id, FELAN_METABOX_PREFIX . 'project_order_payment_status', 'approved');
                update_post_meta($project_package_id, FELAN_METABOX_PREFIX . 'proposal_status', 'inprogress');

                wp_redirect(remove_query_arg('project_order_approved', add_query_arg('project_order_approved', $post_id, admin_url('edit.php?post_type=project_order'))));
                exit;
            }
        }

        public function project_order_pending()
        {
            if (!empty($_GET['project_order_pending']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'project_order_pending')) {
                $post_id = absint(felan_clean(wp_unslash($_GET['project_order_pending'])));
                $project_package_id = get_post_meta($post_id, FELAN_METABOX_PREFIX . 'project_order_item_id', true);
                update_post_meta($post_id, FELAN_METABOX_PREFIX . 'project_order_payment_status', 'pending');
                update_post_meta($project_package_id, FELAN_METABOX_PREFIX . 'proposal_status', 'pending');

                wp_redirect(remove_query_arg('project_order_pending', add_query_arg('project_order_pending', $post_id, admin_url('edit.php?post_type=project_order'))));
                exit;
            }
        }

        /**
         * Display custom column for project_order
         * @param $column
         */
        public function display_custom_column($column)
        {
            global $post;
            $project_order_meta = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'project_order_meta', true);
            $payment_method = Felan_project_order::get_project_order_payment_method($project_order_meta['project_order_payment_method']);
            switch ($column) {
                case 'thumb':
                    $author_id = $project_order_meta['project_order_user_id'];
                    $project_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
                    if (!empty($project_avatar)) {
                        echo '<img src = " ' . $project_avatar . '" alt=""/>';
                    } else {
                        echo '&ndash;';
                    }
                    break;
                case 'buyer':
                    $user_info = get_userdata($project_order_meta['project_order_user_id']);
                    if ($user_info) {
                        echo '<a href="' . get_edit_user_link($project_order_meta['project_order_user_id']) . '">' . esc_attr($user_info->display_name) . '</a>';
                    }
                    break;
                case 'payment_method':
                    echo $payment_method;
                    if ($payment_method !== 'Woocommerce') {
                        echo '<a href="' . get_edit_user_link($project_order_meta['project_order_user_id']) . '">' . esc_html__(' (View)', 'felan-framework') . '</a>';
                    }
                    break;
                case 'price':
                    echo $project_order_meta['project_order_item_price'];
                    break;
                case 'status':
                    $project_order_payment_status = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'project_order_payment_status', true);
                    if ($project_order_payment_status == 'approved') {
                        echo '<span class="label felan-label-blue">' . esc_html__('Approved', 'felan-framework') . '</span>';
                    } else {
                        echo '<span class="label felan-label-yellow">' . esc_html__('Pending', 'felan-framework') . '</span>';
                    }
                    break;
                case 'activate_date':
                    $project_package_activate_date = $project_order_meta['project_order_purchase_date'];
                    echo $project_package_activate_date;
                    break;
            }
        }

        /**
         * Modify project_order slug
         * @param $existing_slug
         * @return string
         */
        public function modify_project_order_slug($existing_slug)
        {
            $project_order_url_slug = felan_get_option('project_order_url_slug');
            if ($project_order_url_slug) {
                return $project_order_url_slug;
            }
            return $existing_slug;
        }

        /**
         * Filter Restrict
         */
        public function filter_restrict_manage_project_order()
        {
            global $typenow;
            $post_type = 'project_order';
            if ($typenow == $post_type) {
                //Status
                $values = array(
                    'pending' => esc_html__('Pending', 'felan-framework'),
                    'approved' => esc_html__('Approve', 'felan-framework'),
                );
?>
                <select name="project_order_payment_status">
                    <option value=""><?php esc_html_e('All Status', 'felan-framework'); ?></option>
                    <?php $current_v = isset($_GET['project_order_payment_status']) ? felan_clean(wp_unslash($_GET['project_order_payment_status'])) : '';
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
                );
                ?>
                <select name="project_order_payment_method">
                    <option value=""><?php esc_html_e('All Payment', 'felan-framework'); ?></option>
                    <?php $current_v = isset($_GET['project_order_payment_method']) ? wp_unslash(felan_clean($_GET['project_order_payment_method'])) : '';
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
                <?php $project_order_user = isset($_GET['project_order_user']) ? felan_clean(wp_unslash($_GET['project_order_user'])) : ''; ?>
                <input type="text" placeholder="<?php esc_attr_e('Search user id', 'felan-framework'); ?>" name="project_order_user" value="<?php echo esc_attr($project_order_user); ?>">
<?php }
        }

        /**
         * project_order_filter
         * @param $query
         */
        public function project_order_filter($query)
        {
            global $pagenow;
            $post_type = 'project_order';
            $q_vars    = &$query->query_vars;
            $filter_arr = array();
            if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type) {
                $project_order_user = isset($_GET['project_order_user']) ? felan_clean(wp_unslash($_GET['project_order_user'])) : '';
                if ($project_order_user !== '') {
                    $filter_arr[] = array(
                        'key' => FELAN_METABOX_PREFIX . 'project_order_user_id',
                        'value' => $project_order_user,
                        'compare' => '==',
                    );
                }

                $project_order_payment_status = isset($_GET['project_order_payment_status']) ? felan_clean(wp_unslash($_GET['project_order_payment_status'])) : '';
                if ($project_order_payment_status !== '') {
                    $filter_arr[] = array(
                        'key' => FELAN_METABOX_PREFIX . 'project_order_payment_status',
                        'value' => $project_order_payment_status,
                        'compare' => '=',
                    );
                }

                $project_order_payment_method = isset($_GET['project_order_payment_method']) ? felan_clean(wp_unslash($_GET['project_order_payment_method'])) : '';
                if ($project_order_payment_method !== '') {
                    $filter_arr[] = array(
                        'key' => FELAN_METABOX_PREFIX . 'project_order_payment_method',
                        'value' => $project_order_payment_method,
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
