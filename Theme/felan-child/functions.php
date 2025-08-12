<?php
defined('ABSPATH') || exit;

/**
 * Theme functions and definitions.
 */
function felan_child_enqueue_styles()
{

	wp_enqueue_style(
		'felan-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		array('felan-style'),
		wp_get_theme()->get('Version')
	);
}
add_action('wp_enqueue_scripts', 'felan_child_enqueue_styles');

/**
 * Enqueue child scripts
 */
add_action('wp_enqueue_scripts', 'felan_child_enqueue_scripts');
if (!function_exists('felan_child_enqueue_scripts')) {

	function felan_child_enqueue_scripts()
	{
		wp_enqueue_script(
			'felan-child-script',
			trailingslashit(get_stylesheet_directory_uri()) . 'script.js',
			array('jquery'),
			time(),
			true
		);
	}
}
