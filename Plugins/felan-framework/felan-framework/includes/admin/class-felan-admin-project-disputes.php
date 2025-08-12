<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!class_exists('Felan_Admin_Project_Disputes')) {
    /**
     * Class Felan_Admin_Project_Disputes
     */
    class Felan_Admin_Project_Disputes
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
            $columns['employer'] = esc_html__('Employer', 'felan-framework');
            $columns['price'] = esc_html__('Price', 'felan-framework');
            $columns['status'] = esc_html__('Status', 'felan-framework');
            $new_columns = array();
            $custom_order = array('cb', 'thumb', 'title', 'employer', 'price', 'status');
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
                    'meta_key' => FELAN_METABOX_PREFIX . 'project_disputes_status',
                    'orderby' => 'meta_value_num',
                ));
            }

            return $vars;
        }

        /**
         * Display custom column for disputes
         * @param $column
         */
        public function display_custom_column($column)
        {
            global $post;
            $author_id = get_post_field('post_author', $post->ID);
            $author_name = get_the_author_meta('display_name', $author_id);
            $price = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'project_disputes_price', true);
            $currency_sign_default = felan_get_option('currency_sign_default');
            $currency_position = felan_get_option('currency_position');
            if ($currency_position == 'before') {
                $price = $price . $currency_sign_default;
            } else {
                $price = $currency_sign_default . $price;
            }
            switch ($column) {
                case 'thumb':
                    $service_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
                    if (!empty($service_avatar)) {
                        echo '<img src = " ' . $service_avatar . '" alt=""/>';
                    } else {
                        echo '&ndash;';
                    }
                    break;
                case 'employer':
                    echo esc_attr($author_name);
                    break;
                case 'price':
                    echo esc_attr($price);
                    break;
                case 'status':
                    $project_disputes_status = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'project_disputes_status', true);
                    if ($project_disputes_status == 'close') {
                        echo '<span class="label felan-label-gray">' . esc_html__('Closed', 'felan-framework') . '</span>';
                    } elseif ($project_disputes_status == 'refund') {
                        echo '<span class="label felan-label-blue">' . esc_html__('Refunded', 'felan-framework') . '</span>';
                    } else {
                        echo '<span class="label felan-label-yellow">' . esc_html__('Open', 'felan-framework') . '</span>';
                    }
                    break;
            }
        }

        /**
         * Modify disputes slug
         * @param $existing_slug
         * @return string
         */
        public function modify_disputes_slug($existing_slug)
        {
            $disputes_url_slug = felan_get_option('disputes_url_slug');
            if ($disputes_url_slug) {
                return $disputes_url_slug;
            }
            return $existing_slug;
        }

        /**
         * Filter Restrict
         */
        public function filter_restrict_manage_disputes()
        {
            global $typenow;
            $post_type = 'disputes';
            if ($typenow == $post_type) {
                //Status
                $values = array(
                    'open' => esc_html__('Open', 'felan-framework'),
                    'close' => esc_html__('Closed', 'felan-framework'),
                    'refund' => esc_html__('Eefunded', 'felan-framework'),
                );
                ?>
                <select name="project_disputes_status">
                    <option value=""><?php esc_html_e('All Status', 'felan-framework'); ?></option>
                    <?php $current_v = isset($_GET['project_disputes_status']) ? felan_clean(wp_unslash($_GET['project_disputes_status'])) : '';
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
                <?php $disputes_user = isset($_GET['disputes_user']) ? felan_clean(wp_unslash($_GET['disputes_user'])) : ''; ?>
                <input type="text" placeholder="<?php esc_attr_e('Search user id', 'felan-framework'); ?>" name="disputes_user" value="<?php echo esc_attr($disputes_user); ?>">
            <?php }
        }

        /**
         * disputes_filter
         * @param $query
         */
        public function disputes_filter($query)
        {
            global $pagenow;
            $post_type = 'disputes';
            $q_vars    = &$query->query_vars;
            $filter_arr = array();
            if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type) {
                $disputes_user = isset($_GET['disputes_user']) ? felan_clean(wp_unslash($_GET['disputes_user'])) : '';
                if ($disputes_user !== '') {
                    $filter_arr[] = array(
                        'key' => FELAN_METABOX_PREFIX . 'disputes_user_id',
                        'value' => $disputes_user,
                        'compare' => '==',
                    );
                }

                $project_disputes_status = isset($_GET['project_disputes_status']) ? felan_clean(wp_unslash($_GET['project_disputes_status'])) : '';
                if ($project_disputes_status !== '') {
                    $filter_arr[] = array(
                        'key' => FELAN_METABOX_PREFIX . 'project_disputes_status',
                        'value' => $project_disputes_status,
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
