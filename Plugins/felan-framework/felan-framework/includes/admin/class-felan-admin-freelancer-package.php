<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!class_exists('Felan_Admin_freelancer_package')) {
    /**
     * Class Felan_Admin_freelancer_package
     */
    class Felan_Admin_freelancer_package
    {
        /**
         * Modify freelancer_package slug
         * @param $existing_slug
         * @return string
         */
        public function modify_freelancer_package_slug($existing_slug)
        {
            $freelancer_package_url_slug = felan_get_option('freelancer_package_url_slug');
            if ($freelancer_package_url_slug) {
                return $freelancer_package_url_slug;
            }
            return $existing_slug;
        }

        /**
         * Register custom column titles
         * @param $columns
         * @return array
         */
        public function register_custom_column_titles($columns)
        {
            $columns['cb'] = "<input type=\"checkbox\" />";
            $columns['title'] = esc_html__('Name', 'felan-framework');
            $columns['price'] = esc_html__('Price', 'felan-framework');
            $columns['featured'] = '<span data-tip="' .  esc_html__('Featured?', 'felan-framework') . '" class="tips dashicons dashicons-star-filled"></span>';
            $new_columns = array();
            $custom_order = array('cb', 'title', 'price', 'featured', 'date');
            foreach ($custom_order as $colname) {
                $new_columns[$colname] = $columns[$colname];
            }
            return $new_columns;
        }

        /**
         * Display custom column
         * @param $column
         */
        public function display_custom_column($column)
        {
            global $post;
            switch ($column) {
                case 'price':
                    $freelancer_package_free = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'freelancer_package_free', true);
                    if ($freelancer_package_free == 1) {
                        esc_html_e('Free', 'felan-framework');
                    } else {
                        $freelancer_package_price = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'freelancer_package_price', true);
                        if ($freelancer_package_price > 0) {
                            echo felan_get_format_money($freelancer_package_price, '', 2);
                        } else {
                            esc_html_e('Free', 'felan-framework');
                        }
                    }

                    break;
                case 'featured':
                    $featured = get_post_meta($post->ID, FELAN_METABOX_PREFIX . 'freelancer_package_featured', true);
                    if ($featured == 1) {
                        echo '<i data-tip="' .  esc_html__('Featured', 'felan-framework') . '" class="tips accent-color dashicons dashicons-star-filled"></i>';
                    } else {
                        echo '<i data-tip="' .  esc_html__('Not Featured', 'felan-framework') . '" class="tips dashicons dashicons-star-empty"></i>';
                    }
                    break;
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
            if ($post->post_type == 'freelancer_package') {
                unset($actions['view']);
            }
            return $actions;
        }
    }
}
