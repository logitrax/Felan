<?php
defined('ABSPATH') || exit;

if (!class_exists('Felan_Footer')) {

    class Felan_Footer
    {

        protected static $instance = null;

        public static function instance()
        {
            if (null === self::$instance) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        public function initialize()
        {
            add_action('init', array($this, 'register_footer'));
            add_post_type_support('felan_footer', 'elementor');
            add_action('wp_footer', array($this, 'render_back_to_top'));
        }

        /**
         * Register Footer Post Type
         */
        function register_footer()
        {
            $labels = array(
                'name' => __('Footer', 'felan-framework'),
                'singular_name' => __('Footer', 'felan-framework'),
                'add_new' => __('Add New', 'felan-framework'),
                'add_new_item' => __('Add New', 'felan-framework'),
                'edit_item' => __('Edit Footer', 'felan-framework'),
                'new_item' => __('Add New Footer', 'felan-framework'),
                'view_item' => __('View Footer', 'felan-framework'),
                'search_items' => __('Search Footer', 'felan-framework'),
                'not_found' => __('No items found', 'felan-framework'),
                'not_found_in_trash' => __('No items found in trash', 'felan-framework'),
            );

            $args = array(
                'menu_icon' => 'dashicons-arrow-down-alt',
                'label' => esc_html__('Footer', 'felan-framework'),
                'description' => esc_html__('Footer', 'felan-framework'),
                'labels' => $labels,
                'supports' => array(
                    'title',
                    'editor',
                    'revisions',
                ),
                'hierarchical' => false,
                'public' => true,
                'menu_position' => 15,
                'show_in_admin_bar' => true,
                'show_in_nav_menus' => true,
                'can_export' => true,
                'has_archive' => false,
                'exclude_from_search' => true,
                'publicly_queryable' => false,
                'rewrite' => false,
                'capability_type' => 'page',
                'publicly_queryable' => true, // Enable TRUE for Elementor Editing
            );
            register_post_type('felan_footer', $args);
        }

        public function render_back_to_top()
        {
            if (felan_get_option('enable_back_top') != '1') {
                return;
            }
?>
            <div id="back-to-top" class="back-to-top">
                <a href="#" class="back-top">
                    <i class="far fa-chevron-up"></i>
                </a>
            </div>
<?php
        }
    }

    Felan_Footer::instance()->initialize();
}
