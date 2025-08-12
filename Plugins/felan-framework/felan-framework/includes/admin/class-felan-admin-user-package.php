<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!class_exists('Felan_User_Package_Admin')) {
    /**
     * Class Felan_User_Package_Admin
     */
    class Felan_User_Package_Admin
    {
        /**
         * Register custom columns
         * @param $columns
         * @return array
         */
        public function register_custom_column_titles($columns)
        {
            $columns['cb'] = "<input type=\"checkbox\" />";
            $columns['title'] =  esc_html__('ID', 'felan-framework');
            $columns['user_id'] = esc_html__('Name', 'felan-framework');
            $columns['package'] = esc_html__('Package', 'felan-framework');
            $columns['num_job'] = esc_html__('Number Jobs', 'felan-framework');
            $columns['num_featured'] = esc_html__('Number Featured', 'felan-framework');
            $columns['activate_date'] = esc_html__('Activate Date', 'felan-framework');
            $columns['expire_date'] = esc_html__('Expires Date', 'felan-framework');
            $new_columns = array();
            $custom_order = array('cb', 'title', 'user_id', 'package', 'num_job', 'num_featured', 'activate_date', 'expire_date');
            foreach ($custom_order as $colname) {
                $new_columns[$colname] = $columns[$colname];
            }
            return $new_columns;
        }

        /**
         * Display custom column for agent package
         * @param $column
         */
        public function display_custom_column($column)
        {
            global $post;
            $postID = $post->ID;
            $package_user_id = get_post_meta($postID, FELAN_METABOX_PREFIX . 'package_user_id', true);
            $package_id = get_user_meta($package_user_id, FELAN_METABOX_PREFIX . 'package_id', true);
            $package_unlimited_job = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_unlimited_job', true);
            $package_unlimited_featured_job = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_unlimited_job_featured', true);
            $package_num_job = get_user_meta($package_user_id, FELAN_METABOX_PREFIX . 'package_number_job', true);
            $package_num_featured_job = get_user_meta($package_user_id, FELAN_METABOX_PREFIX . 'package_number_featured', true);
            $package_activate_date = get_user_meta($package_user_id, FELAN_METABOX_PREFIX . 'package_activate_date', true);
            $package_name = get_the_title($package_id);
            $user_info = get_userdata($package_user_id);
            $felan_package = new Felan_Package();
            $expired_date = $felan_package->get_expired_date($package_id, $package_user_id);

            switch ($column) {
                case 'user_id':
                    if ($user_info) {
                        echo esc_attr($user_info->display_name);
                    }
                    break;
                case 'package':
                    echo esc_attr($package_name);
                    break;

                case 'num_job':
                    if ($package_unlimited_job == 1) {
                        esc_html_e('Unlimited', 'felan-framework');
                    } else {
                        echo esc_attr($package_num_job);
                    }
                    break;

                case 'num_featured':
                    if ($package_unlimited_featured_job == 1) {
                        esc_html_e('Unlimited', 'felan-framework');
                    } else {
                        echo esc_attr($package_num_featured_job);
                    }
                    break;

                case 'activate_date':
                    echo esc_attr($package_activate_date);
                    break;

                case 'expire_date':
                    echo esc_attr($expired_date);
                    break;
            }
        }
        /**
         * Modify agent package slug
         * @param $existing_slug
         * @return string
         */
        public function modify_user_package_slug($existing_slug)
        {
            $user_package_url_slug = felan_get_option('user_package_url_slug');
            if ($user_package_url_slug) {
                return $user_package_url_slug;
            }
            return $existing_slug;
        }

        /**
         * filter_restrict_manage_user_package
         */
        public function filter_restrict_manage_user_package()
        {
            global $typenow;
            $post_type = 'user_package';
            if ($typenow == $post_type) { ?>
                <input type="text" placeholder="<?php esc_html_e('Buyer', 'felan-framework'); ?>" name="package_user" value="<?php echo (isset($_GET['package_user']) ? felan_clean(wp_unslash($_GET['package_user'])) : ''); ?>">
<?php }
        }

        /**
         * user_package_filter
         * @param $query
         */
        public function user_package_filter($query)
        {
            global $pagenow;
            $post_type = 'user_package';
            $q_vars    = &$query->query_vars;
            $filter_arr = array();
            if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type) {
                if (isset($_GET['package_user']) && $_GET['package_user'] != '') {
                    $user = get_user_by('login', felan_clean(wp_unslash($_GET['package_user'])));
                    $user_id = -1;
                    if ($user) {
                        $user_id = $user->ID;
                    }
                    $filter_arr[] = array(
                        'key' => FELAN_METABOX_PREFIX . 'package_user_id',
                        'value' =>  $user_id,
                        'compare' => 'IN',
                    );
                }
                if (!empty($filter_arr)) {
                    $q_vars['meta_query'] = $filter_arr;
                }
            }
        }

        /**
         * @param $actions
         * @param $post
         * @return mixed
         */
        public function modify_list_row_actions($actions, $post)
        {
            // Check for your post type.
            if ($post->post_type == 'user_package') {
                unset($actions['view']);
            }
            return $actions;
        }

        // define the delete_post callback 
        public function action_delete_post()
        {
            global $post;
            $postID = isset($post->ID) ? $post->ID : '';
            if ('user_package' == get_post_type($postID)) {
                $package_user_id = get_post_meta($postID, FELAN_METABOX_PREFIX . 'package_user_id', true);
                update_user_meta($package_user_id, FELAN_METABOX_PREFIX . 'package_id', '');
                update_user_meta($package_user_id, FELAN_METABOX_PREFIX . 'paypal_transfer', '');
                update_user_meta($package_user_id, FELAN_METABOX_PREFIX . 'free_package', '');
            }
        }
    }
}
