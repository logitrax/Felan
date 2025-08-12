<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('Felan_freelancer_package')) {
    /**
     * Class Felan_freelancer_package
     */
    class Felan_freelancer_package
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
         * Insert service freelancer_package
         * @param $user_id
         * @param $freelancer_package_id
         */
        public function insert_user_freelancer_package($user_id, $freelancer_package_id)
        {
            //Service
            $freelancer_package_number_service = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'freelancer_package_number_service', true);
            $freelancer_package_number_service_featured = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'freelancer_package_number_service_featured', true);
            $enable_package_service_unlimited = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'enable_package_service_unlimited', true);

            if ($enable_package_service_unlimited == 1) {
                $freelancer_package_number_service = 999999999999999999;
            }
            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'freelancer_package_number_service', $freelancer_package_number_service);
            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'freelancer_package_number_service_featured', $freelancer_package_number_service_featured);

            //Field
            $field_package = array('jobs_apply', 'project_apply', 'jobs_wishlist', 'company_follow');
            foreach ($field_package as $field) {
                $show_field = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'show_package_' . $field, true);
                $field_number = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'freelancer_package_number_' . $field, true);
                $field_unlimited = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'enable_package_' . $field . '_unlimited', true);
                if (intval($show_field) == 1) {
                    if ($field_unlimited == 1) {
                        $field_number = 999999999999999999;
                    }
                    update_user_meta($user_id, FELAN_METABOX_PREFIX . 'freelancer_package_number_' . $field, $field_number);
                }
            }

            $time = time();
            $date = date('Y-m-d H:i:s', $time);
            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'freelancer_package_activate_date', $date);
            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'freelancer_package_id', $freelancer_package_id);
            $freelancer_package_key = uniqid();
            update_user_meta($user_id, FELAN_METABOX_PREFIX . 'freelancer_package_key', $freelancer_package_key);
        }

        public function get_expired_date($freelancer_package_id, $freelancer_package_user_id)
        {
            $enable_package_service_unlimited_time = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'enable_package_service_unlimited_time', true);
            if ($enable_package_service_unlimited_time == 1) {
                $expired_date = esc_html__('Never Expires');
            } else {
                $expired_date = $this->get_expired_time($freelancer_package_id, $freelancer_package_user_id);
                $expired_date = date_i18n('Y-m-d', $expired_date);
            }
            return $expired_date;
        }

        public function get_expired_time($freelancer_package_id, $freelancer_package_user_id)
        {
            $expired_time = '';
            $freelancer_package_time_unit = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'freelancer_package_time_unit', true);
            $freelancer_package_period = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'freelancer_package_period', true);
            $freelancer_package_activate_date = strtotime(get_user_meta($freelancer_package_user_id, FELAN_METABOX_PREFIX . 'freelancer_package_activate_date', true));
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
            return $expired_time;
        }

        public function user_freelancer_package_available($user_id)
        {
            $freelancer_paid_submission_type      = felan_get_option('freelancer_paid_submission_type');
            if ($freelancer_paid_submission_type == 'freelancer_per_package') {
                $freelancer_package_id = get_the_author_meta(FELAN_METABOX_PREFIX . 'freelancer_package_id', $user_id);
                if (empty($freelancer_package_id)) {
                    return 0;
                } else {
                    $enable_package_service_unlimited_time = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'enable_package_service_unlimited_time', true);
                    if ($enable_package_service_unlimited_time == 0) {
                        $expired_date = strtotime($this->get_expired_date($freelancer_package_id, $user_id));
                        $current_date = strtotime(date('Y-m-d'));
                        if ($current_date >= $expired_date) {
                            return -1;
                        }
                    }
                }
            }
            return 1;
        }

        public function get_service_expired($user_id)
        {
            $check_freelancer_package = $this->user_freelancer_package_available($user_id);
            $args_expired = array(
                'post_type'           => 'service',
                'post_status'         => 'pause',
                'posts_per_page'      => -1,
                'author'              => $user_id,
            );
            $data_expired = new WP_Query($args_expired);
            if ($data_expired->have_posts()) {
                while ($data_expired->have_posts()) : $data_expired->the_post();
                    $service_id =  get_the_ID();
                endwhile;
            }
        }
    }
}
