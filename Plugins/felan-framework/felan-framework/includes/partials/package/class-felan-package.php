<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('Felan_Package')) {
    /**
     * Class Felan_Package
     */
    class Felan_Package
    {
        /**
         * get_time_unit
         * @param $time_unit
         * @return null|string
         */
        public static function get_time_unit($time_unit)
        {
            if ($time_unit == 'Day') {
                return esc_html__('day', 'felan-framework');
            } else if ($time_unit == 'Day') {
                return esc_html__('days', 'felan-framework');
            } else if ($time_unit == 'Week') {
                return esc_html__('week', 'felan-framework');
            } else if ($time_unit == 'Weeks') {
                return esc_html__('weeks', 'felan-framework');
            } else if ($time_unit == 'Month') {
                return esc_html__('month', 'felan-framework');
            } else if ($time_unit == 'Months') {
                return esc_html__('months', 'felan-framework');
            } else if ($time_unit == 'Year') {
                return esc_html__('year', 'felan-framework');
            } else if ($time_unit == 'Years') {
                return esc_html__('years', 'felan-framework');
            }
            return null;
        }

        /**
         * Insert agent package
         * @param $user_id
         * @param $package_id
         */
        public function insert_user_package($user_id, $package_id)
        {
            $args = array(
                'post_type' => 'user_package',
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => FELAN_METABOX_PREFIX . 'package_user_id',
                        'value' => $user_id,
                        'compare' => '='
                    )
                ),
            );
            $user_package = new WP_Query($args);
            wp_reset_postdata();
            $existed_post = $user_package->found_posts;

            if ($existed_post < 1) {
                $args = array(
                    'post_title' => '#' . $user_id,
                    'post_type' => 'user_package',
                    'post_status' => 'publish'
                );
                $post_id = wp_insert_post($args);
                update_post_meta($post_id, FELAN_METABOX_PREFIX . 'package_user_id', $user_id);
            }
            $package_number_project = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_number_project', true);
            $package_number_project_featured = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_number_project_featured', true);
            $package_unlimited_project = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_unlimited_project', true);

            $package_number_job = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_number_job', true);
            $package_number_featured = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_number_featured', true);
            $package_unlimited_job = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_unlimited_job', true);

            $package_number_follow = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'company_package_number_freelancer_follow', true);
            $package_follow_unlimited = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'enable_package_freelancer_follow_unlimited', true);
            $package_number_download_cv = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'company_package_number_download_cv', true);
            $package_download_cv_unlimited = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'enable_package_download_cv_unlimited', true);

            if ($package_follow_unlimited == 1) {
                $package_number_follow = 999999999999999999;
            }

            if ($package_unlimited_job == 1) {
                $package_number_job = 999999999999999999;
            }

            if ($package_unlimited_project == 1) {
                $package_number_project = 999999999999999999;
            }

            if ($package_download_cv_unlimited == 1) {
                $package_number_download_cv = 999999999999999999;
            }

            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_number_job', $package_number_job);
            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_number_featured', $package_number_featured);
            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_number_project', $package_number_project);
            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_number_project_featured', $package_number_project_featured);
            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_number_freelancer_follow', $package_number_follow);
            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_number_download_cv', $package_number_download_cv);

            do_action('felan_ajax_field_package_jobs', $user_id, $package_id);

            $time = time();
            $date = date('Y-m-d H:i:s', $time);
            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_activate_date', $date);
            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_id', $package_id);
            $package_key = uniqid();
            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_key', $package_key);

            $user = get_user_by('id', $user_id);
            $user_email = $user->user_email;
            $package_args = array();
            $package_args['website_url'] = get_option('siteurl');
            $package_args['website_name'] = get_option('blogname');
            $package_args['your_name'] = $user->user_login;

            // felan_send_email($user_email, 'mail_activated_package', $package_args);
        }

        public function get_expired_date($package_id, $package_user_id)
        {
            $package_unlimited_time = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_unlimited_time', true);
            if ($package_unlimited_time == 1) {
                $expired_date = esc_html__('Never Expires');
            } else {
                $expired_date = $this->get_expired_time($package_id, $package_user_id);
                $expired_date = date_i18n('Y-m-d', $expired_date);
            }
            return $expired_date;
        }

        public function get_expired_time($package_id, $package_user_id)
        {
            $expired_time = '';
            $package_time_unit = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_time_unit', true);
            $package_period = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_period', true);
            $package_activate_date = strtotime(get_user_meta($package_user_id, FELAN_METABOX_PREFIX . 'package_activate_date', true));
            $seconds = 0;
            switch ($package_time_unit) {
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
            if (is_numeric($package_activate_date) && is_numeric($seconds) && is_numeric($package_period)) {
                $expired_time = $package_activate_date + ($seconds * $package_period);
            }
            return $expired_time;
        }
    }
}
