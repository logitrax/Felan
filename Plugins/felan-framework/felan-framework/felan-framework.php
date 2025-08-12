<?php

/**
 *  Plugin Name: Felan Framework
 *  Plugin URI: https://ricetheme.com/
 *  Description: Felan Framework.
 *  Version: 1.1.2
 *  Author: RiceTheme
 *  Author URI: https://ricetheme.com/
 *  Text Domain: felan-framework
 *
 *  @package Felan Framework
 *  @author ricetheme
 *
 **/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('Felan_Framework')) {
    class Felan_Framework
    {

        public function __construct()
        {

            $this->define_constants();
            $this->load_textdomain();

            register_deactivation_hook(__FILE__, array($this, 'felan_deactivate'));
            add_action('plugins_loaded', array($this, 'includes'));
            add_filter('upload_mimes', array($this, 'felan_svg_upload'));
            add_filter('kirki/config', array($this, 'kirki_update_url'), 10, 1);

            if (is_multisite()) {
                $blog_id = get_current_blog_id();
                $upload_path = WP_CONTENT_DIR . '/uploads/sites/' . $blog_id . '/';
            }
        }

        /**
         *  Define constant
         **/
        private function define_constants()
        {

            $theme = wp_get_theme();
            if (!empty($theme['Template'])) {
                $theme = wp_get_theme($theme['Template']);
            }
            $plugin_dir_name = dirname(__FILE__);
            $plugin_dir_name = str_replace('\\', '/', $plugin_dir_name);
            $plugin_dir_name = explode('/', $plugin_dir_name);
            $plugin_dir_name = end($plugin_dir_name);

            if (!defined('FELAN_PLUGIN_FILE')) {
                define('FELAN_PLUGIN_FILE', __FILE__);
            }

            if (!defined('FELAN_PLUGIN_NAME')) {
                define('FELAN_PLUGIN_NAME', $plugin_dir_name);
            }

            if (!defined('FELAN_PLUGIN_DIR')) {
                define('FELAN_PLUGIN_DIR', plugin_dir_path(__FILE__));
            }
            if (!defined('FELAN_PLUGIN_URL')) {
                define('FELAN_PLUGIN_URL', trailingslashit(plugins_url(FELAN_PLUGIN_NAME)));
            }

            if (!defined('FELAN_PLUGIN_PREFIX')) {
                define('FELAN_PLUGIN_PREFIX', 'felan');
            }

            if (!defined('FELAN_METABOX_PREFIX')) {
                define('FELAN_METABOX_PREFIX', 'felan-');
            }

            if (function_exists('pll_the_languages') && !defined(strtoupper(pll_current_language()) . '_' . 'FELAN_OPTIONS_NAME')) {
                define(strtoupper(pll_current_language()) . '_' . 'FELAN_OPTIONS_NAME', pll_current_language() . '_felan-framework');
            } else if (defined('ICL_SITEPRESS_VERSION')) {
                $current_language = apply_filters('wpml_current_language', NULL);

                if ($current_language) {
                    define(strtoupper($current_language) . '_' . 'FELAN_OPTIONS_NAME', $current_language . '_felan-framework');
                } else {
                    define('FELAN_OPTIONS_NAME', 'felan-framework');
                }
            } else {
                define('FELAN_OPTIONS_NAME', 'felan-framework');
            }

            if (!defined('FELAN_THEME_NAME')) {
                define('FELAN_THEME_NAME', $theme['Name']);
            }

            if (!defined('FELAN_THEME_SLUG')) {
                define('FELAN_THEME_SLUG', $theme['Template']);
            }

            if (!defined('FELAN_THEME_VERSION')) {
                define('FELAN_THEME_VERSION', $theme['Version']);
            }

            if (!defined('GLF_THEME_DIR')) {
                define('GLF_THEME_DIR', get_template_directory());
            }

            if (!defined('GLF_THEME_URL')) {
                define('GLF_THEME_URL', get_template_directory_uri());
            }

            if (!defined('GLF_THEME_SLUG')) {
                define('GLF_THEME_SLUG', $theme['Template']);
            }

            if (!defined('FELAN_PLUGIN_VER')) {
                define('FELAN_PLUGIN_VER', '1.0.0');
            }

            if (!defined('FELAN_AJAX_URL')) {
                $ajax_url = admin_url('admin-ajax.php', 'relative');
                define('FELAN_AJAX_URL', $ajax_url);
            }
        }

        public function load_textdomain()
        {
            $mofile = FELAN_PLUGIN_DIR . 'languages/' . 'felan-framework-' . get_locale() . '.mo';

            if (file_exists($mofile)) {
                load_textdomain('felan-framework', $mofile);
            }
        }

        /**
         * The code that runs during plugin deactivation.
         */
        public function felan_deactivate()
        {
            require_once FELAN_PLUGIN_DIR . 'includes/class-felan-deactivator.php';
            Felan_Deactivator::deactivate();
        }

        /**
         * Upload Svg
         */
        public function felan_svg_upload($mimes)
        {
            $mimes['svg'] = 'image/svg+xml';
            return $mimes;
        }

        /**
         *  Includes
         **/
        public function includes()
        {

            if (!class_exists('Base_Framework')) {
                add_filter('felan_base_url', 'base_url', 1);

                function base_url()
                {
                    return FELAN_PLUGIN_URL . 'includes/base/';
                }
                require_once FELAN_PLUGIN_DIR . 'includes/base/base.php';
            }

            // Core
            include_once(FELAN_PLUGIN_DIR . 'includes/class-felan-core.php');

            // Kirki
            include_once(FELAN_PLUGIN_DIR . 'includes/kirki/kirki.php');

            // Base Widget
            include_once(FELAN_PLUGIN_DIR . 'modules/widgets/base.php');

            // Base Elementor
            include_once(FELAN_PLUGIN_DIR . 'modules/elementor/base.php');
        }

        /**
         *  Kirki update url
         **/
        public function kirki_update_url($config)
        {
            $config['url_path'] = FELAN_PLUGIN_URL . '/includes/kirki/';

            return $config;
        }

        /**
         *  Fix Upload Path Multisite
         **/
        public function fix_upload_paths($data)
        {
            $data['basedir'] = $data['basedir'] . '/sites/' . get_current_blog_id();
            $data['path'] = $data['basedir'] . $data['subdir'];
            $data['baseurl'] = $data['baseurl'] . '/sites/' . get_current_blog_id();
            $data['url'] = $data['baseurl'] . $data['subdir'];

            return $data;
        }
    }

    new Felan_Framework();
}
