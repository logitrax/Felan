<?php

$section = 'top-bar';

$default = felan_get_default_theme_options();

// Top Bar
Felan_Kirki::add_section($section, array(
	'title'    => esc_html__('Top Bar', 'felan'),
	'priority' => 50,
));

Felan_Kirki::add_field('theme', [
	'type'     => 'notice',
	'settings' => 'top_bar_customize',
	'label'    => esc_html__('Top Bar Customize', 'felan'),
	'section'  => $section,
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'image',
	'settings'  => 'top_bar_ringbell',
	'label'     => esc_html__('Icon ring bell', 'felan'),
	'section'   => $section,
	'default'   => $default['top_bar_ringbell'],
]);

Felan_Kirki::add_field('theme', [
	'type'     => 'text',
	'settings' => 'top_bar_text',
	'label'    => esc_html__('Text Left', 'felan'),
	'section'  => $section,
	'default'  => $default['top_bar_text'],
]);

Felan_Kirki::add_field('theme', [
	'type'     => 'text',
	'settings' => 'top_bar_link',
	'label'    => esc_html__('Link', 'felan'),
	'section'  => $section,
	'default'  => $default['top_bar_link'],
]);

Felan_Kirki::add_field('theme', [
	'type'     => 'text',
	'settings' => 'top_bar_phone',
	'label'    => esc_html__('Phone', 'felan'),
	'section'  => $section,
	'default'  => $default['top_bar_phone'],
]);

Felan_Kirki::add_field('theme', [
	'type'     => 'text',
	'settings' => 'top_bar_email',
	'label'    => esc_html__('Email', 'felan'),
	'section'  => $section,
	'default'  => $default['top_bar_email'],
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'color',
	'settings'  => 'top_bar_color',
	'label'     => esc_html__('Color', 'felan'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['top_bar_color'],
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'color',
	'settings'  => 'top_bar_bg_color',
	'label'     => esc_html__('Background Color', 'felan'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['top_bar_bg_color'],
]);
