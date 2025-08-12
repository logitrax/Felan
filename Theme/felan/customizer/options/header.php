<?php

$section = 'header';

$default = felan_get_default_theme_options();

// Header
Felan_Kirki::add_section($section, array(
	'title'    => esc_html__('Header', 'felan'),
	'priority' => 50,
));

Felan_Kirki::add_field('theme', [
	'type'     => 'notice',
	'settings' => 'header_customize',
	'label'    => esc_html__('Header Customize', 'felan'),
	'section'  => $section,
]);

Felan_Kirki::add_field('theme', [
	'type'     => 'select',
	'settings' => 'header_type',
	'label'    => esc_html__('Header Main Type', 'felan'),
	'section'  => $section,
	'default'  => $default['header_type'],
	'choices'  => felan_get_header_elementor(false),
]);

Felan_Kirki::add_field('theme', [
    'type'     => 'select',
    'settings' => 'header_dashboard_type',
    'label'    => esc_html__('Header Dashboard Type', 'felan'),
    'section'  => $section,
    'default'  => $default['header_dashboard_type'],
    'choices'  => felan_get_header_elementor(true),
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'color',
	'settings'  => 'header_background',
	'label'     => esc_html__('Background Color', 'felan'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['header_background'],
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'color',
	'settings'  => 'header_sticky_background',
	'label'     => esc_html__('Background Color Header Sticky', 'felan'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['header_sticky_background'],
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'toggle',
	'settings'  => 'top_bar_enable',
	'label'     => esc_html__('Top Bar', 'felan'),
	'section'   => $section,
	'default'   => $default['top_bar_enable'],
]);


Felan_Kirki::add_field('theme', [
	'type'      => 'toggle',
	'settings'  => 'sticky_header',
	'label'     => esc_html__('Enable Sticky', 'felan'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['sticky_header'],
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'toggle',
	'settings'  => 'float_header',
	'label'     => esc_html__('Enable Float', 'felan'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['float_header'],
]);


Felan_Kirki::add_field('theme', [
	'type'      => 'toggle',
	'settings'  => 'show_canvas_menu',
	'label'     => esc_html__('Show Canvas Menu', 'felan'),
	'section'   => $section,
	'default'   => $default['show_canvas_menu'],
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'toggle',
	'settings'  => 'show_main_menu',
	'label'     => esc_html__('Show Main Menu', 'felan'),
	'section'   => $section,
	'default'   => $default['show_main_menu'],
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'toggle',
	'settings'  => 'show_login',
	'label'     => esc_html__('Show Sign In', 'felan'),
	'section'   => $section,
	'default'   => $default['show_login'],
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'toggle',
	'settings'  => 'show_register',
	'label'     => esc_html__('Show Sign Up', 'felan'),
	'section'   => $section,
	'default'   => $default['show_register'],
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'toggle',
	'settings'  => 'show_icon_noti',
	'label'     => esc_html__('Show Notification', 'felan'),
	'section'   => $section,
	'default'   => $default['show_icon_noti'],
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'toggle',
	'settings'  => 'show_search_icon',
	'label'     => esc_html__('Show Search (Mobile)', 'felan'),
	'section'   => $section,
	'default'   => $default['show_search_icon'],
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'toggle',
	'settings'  => 'show_add_project_button',
	'label'     => esc_html__('Show Add Project/Update Profile', 'felan'),
	'section'   => $section,
	'default'   => $default['show_add_project_button'],
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'toggle',
	'settings'  => 'show_button',
	'label'     => esc_html__('Show Button', 'felan'),
	'section'   => $section,
	'default'   => $default['show_button'],
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'text',
	'settings'  => 'button_text',
	'label'     => esc_html__('Button Text', 'felan'),
	'section'   => $section,
	'default'   => $default['button_text'],
	'active_callback' => [
		[
			'setting'  => 'show_button',
			'operator' => '==',
			'value'    => '1',
		]
	]
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'text',
	'settings'  => 'button_link',
	'label'     => esc_html__('Button Link', 'felan'),
	'section'   => $section,
	'default'   => $default['button_link'],
	'active_callback' => [
		[
			'setting'  => 'show_button',
			'operator' => '==',
			'value'    => '1',
		]
	]
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'color',
	'settings'  => 'button_background_color',
	'label'     => esc_html__('Button Background Color', 'felan'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['button_background_color'],
	'active_callback' => [
		[
			'setting'  => 'show_button',
			'operator' => '==',
			'value'    => '1',
		]
	]
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'color',
	'settings'  => 'button_text_color',
	'label'     => esc_html__('Button Text Color', 'felan'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['button_text_color'],
	'active_callback' => [
		[
			'setting'  => 'show_button',
			'operator' => '==',
			'value'    => '1',
		]
	]
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'toggle',
	'settings'  => 'show_categories',
	'label'     => esc_html__('Show Categories', 'felan'),
	'section'   => $section,
	'default'   => $default['show_categories'],
]);

Felan_Kirki::add_field('theme', [
	'type'     => 'select',
	'settings' => 'post_type_categories',
	'label'    => esc_html__('Post Type', 'felan'),
	'section'  => $section,
	'default'  => $default['header_type'],
	'choices'  => array(
		'jobs' => esc_html('Jobs', 'felan'),
		'company' => esc_html('Companies', 'felan'),
		'freelancer' => esc_html('Freelancer', 'felan'),
		'service' => esc_html('Service', 'felan'),
		'project' => esc_html('Project', 'felan'),
	),
	'active_callback' => [
		[
			'setting'  => 'show_categories',
			'operator' => '==',
			'value'    => '1',
		],
	],
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'toggle',
	'settings'  => 'show_search_form',
	'label'     => esc_html__('Show Search Form', 'felan'),
	'section'   => $section,
	'default'   => $default['show_search_form'],
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'slider',
	'settings'  => 'search_result_per_page',
	'label'     => esc_html__('Search Result Per Page', 'felan'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['search_result_per_page'],
	'choices'   => [
		'min'  => 1,
		'max'  => 20,
		'step' => 1,
	],
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'slider',
	'settings'  => 'logo_width',
	'label'     => esc_html__('Logo Width', 'felan'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['logo_width'],
	'choices'   => [
		'min'  => 0,
		'max'  => 500,
		'step' => 1,
	],
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'slider',
	'settings'  => 'header_padding_top',
	'label'     => esc_html__('Padding Top', 'felan'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['header_padding_top'],
	'choices'   => [
		'min'  => 0,
		'max'  => 200,
		'step' => 1,
	],
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'slider',
	'settings'  => 'header_padding_bottom',
	'label'     => esc_html__('Padding Bottom', 'felan'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['header_padding_bottom'],
	'choices'   => [
		'min'  => 0,
		'max'  => 500,
		'step' => 1,
	],
]);
