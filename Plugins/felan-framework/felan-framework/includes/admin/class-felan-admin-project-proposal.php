<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!class_exists('Felan_Admin_Project_Proposal')) {
    /**
     * Class Felan_Admin_Project_Proposal
     */
    class Felan_Admin_Project_Proposal
    {

        /**
         * Register custom columns
         * @param $columns
         * @return array
         */
        public function register_custom_column_titles($columns)
        {
            $columns['thumb'] = esc_html__('Avatar', 'felan-framework');
            $columns['cb'] = "<input type=\"checkbox\" />";
            $columns['title'] = esc_html__('Project Title', 'felan-framework');
            $columns['status'] = esc_html__('Status', 'felan-framework');
            $columns['post_author'] = esc_html__('Name', 'felan-framework');
            $columns['price'] = esc_html__('Price', 'felan-framework');
            $columns['time'] = esc_html__('Time', 'felan-framework');
            $columns['date'] = esc_html__('Date', 'felan-framework');
            $new_columns = array();
            $custom_order = array('cb', 'thumb', 'title', 'status', 'post_author', 'price', 'time', 'date');
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
            $columns['title'] = 'title';
            $columns['status'] = 'status';
            $columns['post_author'] = 'post_author';
            $columns['price'] = 'price';
            $columns['time'] = 'time';
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

            if (isset($vars['orderby']) && 'proposal_status' == $vars['orderby']) {
                $vars = array_merge($vars, array(
                    'meta_key' => FELAN_METABOX_PREFIX . 'proposal_status',
                    'orderby' => 'meta_value_num',
                ));
            }
            return $vars;
        }
        /**
         * Display custom column for project_proposal
         * @param $column
         */
        public function display_custom_column($column)
        {
            global $post;
            $post_id = $post->ID;
            switch ($column) {
                case 'thumb':
                    $author_id = get_post_field('post_author', $post_id);
                    $freelancer_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
                    if (!empty($freelancer_avatar)) {
                        echo '<img src = " ' . $freelancer_avatar . '" alt=""/>';
                    } else {
                        echo '&ndash;';
                    }
                    break;
                case 'post_author':
                    $project_proposal_author = get_the_author();
                    echo $project_proposal_author;
                    break;
                case 'status':
                    $status = get_post_meta($post_id, FELAN_METABOX_PREFIX . 'proposal_status', true);
                    if ($status == 'inprogress') {
                        echo '<span class="label felan-label-violet">' . esc_html__('In Process', 'felan-framework') . '</span>';
                    } elseif ($status == 'transferring') {
                        echo '<span class="label felan-label-pink">' . esc_html__('Transferring', 'felan-framework') . '</span>';
                    } elseif ($status == 'canceled') {
                        echo '<span class="label felan-label-gray">' . esc_html__('Canceled', 'felan-framework') . '</span>';
                    } elseif ($status == 'completed') {
                        echo '<span class="label felan-label-blue">' . esc_html__('Completed', 'felan-framework') . '</span>';
                    } elseif ($status == 'expired') {
                        echo '<span class="label felan-label-gray">' . esc_html__('Expired', 'felan-framework') . '</span>';
                    } elseif ($status == 'refund') {
                        echo '<span class="label felan-label-gray">' . esc_html__('Refund', 'felan-framework') . '</span>';
                    } elseif ($status == 'confirming') {
                        echo '<span class="label felan-label-yellow">' . esc_html__('Confirming', 'felan-framework') . '</span>';
                    } else {
                        echo '<span class="label felan-label-yellow">' . esc_html__('Pending', 'felan-framework') . '</span>';
                    }
                    break;
                case 'price':
                    $project_currency_type = get_post_meta($post_id, FELAN_METABOX_PREFIX . 'proposal_currency_type', true);
                    $currency_position = felan_get_option('currency_position');
                    $currency_leff = $currency_right = '';
                    if ($currency_position == 'before') {
                        $currency_leff = $project_currency_type;
                    } else {
                        $currency_right = $project_currency_type;
                    }
                    $proposal_price = get_post_meta($post_id, FELAN_METABOX_PREFIX . 'proposal_price', true);
                    echo $currency_leff . $proposal_price . $currency_right;
                    break;
                case 'time':
                    $proposal_time = get_post_meta($post_id, FELAN_METABOX_PREFIX . 'proposal_time', true);
                    $proposal_time_type   = get_post_meta($post_id, FELAN_METABOX_PREFIX . 'proposal_time_type', true);
                    echo sprintf(esc_html__('%1s %2s', 'felan-framework'), $proposal_time, $proposal_time_type);
                    break;
            }
        }

        /**
         * Modify project_proposal slug
         * @param $existing_slug
         * @return string
         */
        public function modify_project_proposal_slug($existing_slug)
        {
            $project_proposal_url_slug = felan_get_option('project_proposal_url_slug');
            if ($project_proposal_url_slug) {
                return $project_proposal_url_slug;
            }
            return $existing_slug;
        }
        /**
         * filter_restrict_manage_project_proposal
         */
        public function filter_restrict_manage_project_proposal()
        {
            global $typenow;
            $post_type = 'project_proposal';

            if ($typenow == $post_type) {
                //Applicants Status
                $values = array(
                    'approved' => esc_html__('Approved', 'felan-framework'),
                    'pending' => esc_html__('Pending', 'felan-framework'),
                    'rejected' => esc_html__('Rejected', 'felan-framework'),
                );
?>
                <select name="proposal_status">
                    <option value=""><?php esc_html_e('All Status', 'felan-framework'); ?></option>
                    <?php $current_v = isset($_GET['proposal_status']) ? felan_clean(wp_unslash($_GET['proposal_status'])) : '';
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
<?php }
        }

        /**
         * project_proposal_filter
         * @param $query
         */
        public function project_proposal_filter($query)
        {
            global $pagenow;
            $post_type = 'project_proposal';
            $q_vars    = &$query->query_vars;
            $filter_arr = array();
            if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type) {
                $project_proposal_user = isset($_GET['project_proposal_user']) ? felan_clean(wp_unslash($_GET['project_proposal_user'])) : '';
                if ($project_proposal_user !== '') {
                    $user = get_user_by('login', $project_proposal_user);
                    $user_id = -1;
                    if ($user) {
                        $user_id = $user->ID;
                    }
                    $filter_arr[] = array(
                        'key' => FELAN_METABOX_PREFIX . 'project_proposal_user_id',
                        'value' => $user_id,
                        'compare' => 'IN',
                    );
                }

                $_proposal_status = isset($_GET['status']) ? felan_clean(wp_unslash($_GET['status'])) : '';

                if ($_proposal_status !== '') {
                    $proposal_status = 0;
                    if ($_proposal_status == 'paid') {
                        $proposal_status = 1;
                    }
                    $filter_arr[] = array(
                        'key' => FELAN_METABOX_PREFIX . 'project_proposal_payment_status',
                        'value' => $proposal_status,
                        'compare' => '=',
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
            if ($post->post_type == 'project_proposal') {
                unset($actions['view']);
            }
            return $actions;
        }
    }
}
