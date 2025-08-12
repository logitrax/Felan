<?php

/**
 * Define constants
 */
$felan_theme = wp_get_theme();

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

if (!empty($felan_theme['Template'])) {
	$felan_theme = wp_get_theme($felan_theme['Template']);
}

if (!defined('FELAN_THEME_NAME')) {
	define('FELAN_THEME_NAME', $felan_theme['Name']);
}

if (!defined('FELAN_THEME_SLUG')) {
	define('FELAN_THEME_SLUG', $felan_theme['Template']);
}

if (!defined('FELAN_THEME_VER')) {
	define('FELAN_THEME_VER', $felan_theme['Version']);
}

if (!defined('FELAN_THEME_DIR')) {
	define('FELAN_THEME_DIR', trailingslashit(get_template_directory()));
}

if (!defined('FELAN_THEME_URI')) {
	define('FELAN_THEME_URI', get_template_directory_uri());
}

if (!defined('FELAN_THEME_PREFIX')) {
	define('FELAN_THEME_PREFIX', 'felan_');
}

if (!defined('FELAN_METABOX_PREFIX')) {
	define('FELAN_METABOX_PREFIX', 'felan-');
}

if (!defined('FELAN_CUSTOMIZER_DIR')) {
	define('FELAN_CUSTOMIZER_DIR', FELAN_THEME_DIR . '/customizer');
}

if (!defined('FELAN_IMAGES')) {
	define('FELAN_IMAGES', FELAN_THEME_URI . '/assets/images/');
}

define('FELAN_ELEMENTOR_DIR', get_template_directory() . DS . 'elementor');
define('FELAN_ELEMENTOR_URI', get_template_directory_uri() . '/elementor');
define('FELAN_ELEMENTOR_ASSETS', get_template_directory_uri() . '/elementor/assets');

/**
 * Load Theme Class.
 *
 */
foreach (glob(get_template_directory() . '/includes/*.php') as $theme_class) {
	require_once($theme_class);
}

require_once FELAN_ELEMENTOR_DIR . '/class-entry.php';

function felan_load_elementor_options()
{
	update_option('elementor_disable_typography_schemes', 'yes');
}

add_action('after_switch_theme', 'felan_load_elementor_options');

add_filter('wp_mail_smtp_core_wp_mail_function_incorrect_location_notice', '__return_false');

function add_chevron_to_menu_items($items, $args)
{
	if ($args->theme_location == 'primary' || $args->theme_location == 'main_menu' || $args->theme_location == 'mobile_menu') {
		foreach ($items as &$item) {
			if (in_array('menu-item-has-children', $item->classes) || in_array('page_item_has_children', $item->classes)) {
				$item->title .= '<span class="chevron"><i class="far fa-chevron-down"></i></span>';
			}
		}
	}
	return $items;
}
add_filter('wp_nav_menu_objects', 'add_chevron_to_menu_items', 10, 2);

/**
 * Init the theme
 *
 */
new Felan_Init();
