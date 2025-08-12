<?php

$section = 'color';

$default = felan_get_default_theme_options();

// Color
Felan_Kirki::add_section($section, array(
	'title'    => esc_html__('Color', 'felan'),
	'priority' => 30,
));

// Content
Felan_Kirki::add_field('theme', [
	'type'     => 'notice',
	'settings' => 'color_content',
	'label'    => esc_html__('Content', 'felan'),
	'section'  => $section,
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'color',
	'settings'  => 'text_color',
	'label'     => esc_html__('Text', 'felan'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['text_color'],
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'color',
	'settings'  => 'accent_color',
	'label'     => esc_html__('Accent', 'felan'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['accent_color'],
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'color',
	'settings'  => 'primary_color',
	'label'     => esc_html__('Primary', 'felan'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['primary_color'],
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'color',
	'settings'  => 'secondary_color',
	'label'     => esc_html__('Secondary', 'felan'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['secondary_color'],
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'color',
	'settings'  => 'border_color',
	'label'     => esc_html__('Border', 'felan'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['border_color'],
]);

// Background
Felan_Kirki::add_field('theme', [
	'type'     => 'notice',
	'settings' => 'color_bg_body',
	'label'    => esc_html__('Background', 'felan'),
	'section'  => $section,
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'color',
	'settings'  => 'body_background_color',
	'label'     => esc_html__('Body Background', 'felan'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['body_background_color'],
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'image',
	'settings'  => 'bg_body_image',
	'label'     => esc_html__('Body BG Image', 'felan'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['bg_body_image'],
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'select',
	'settings'  => 'bg_body_size',
	'label'     => esc_html__('Background Size', 'felan'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['bg_body_size'],
	'choices'   => [
		'auto'    => esc_html__('Auto', 'felan'),
		'cover'   => esc_html__('Cover', 'felan'),
		'contain' => esc_html__('Contain', 'felan'),
		'initial' => esc_html__('Initial', 'felan'),
	],
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'select',
	'settings'  => 'bg_body_repeat',
	'label'     => esc_html__('Background Repeat', 'felan'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['bg_body_repeat'],
	'choices'   => [
		'no-repeat' => esc_html__('No Repeat', 'felan'),
		'repeat'    => esc_html__('Repeat', 'felan'),
		'repeat-x'  => esc_html__('Repeat X', 'felan'),
		'repeat-y'  => esc_html__('Repeat Y', 'felan'),
	],
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'select',
	'settings'  => 'bg_body_position',
	'label'     => esc_html__('Background Position', 'felan'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['bg_body_position'],
	'choices'   => [
		'left top'      => esc_html__('Left Top', 'felan'),
		'left center'   => esc_html__('Left Center', 'felan'),
		'left bottom'   => esc_html__('Left Bottom', 'felan'),
		'right top'     => esc_html__('Right Top', 'felan'),
		'right center'  => esc_html__('Right Center', 'felan'),
		'right bottom'  => esc_html__('Right Bottom', 'felan'),
		'center top'    => esc_html__('Center Top', 'felan'),
		'center center' => esc_html__('Center Center', 'felan'),
		'center bottom' => esc_html__('Center Bottom', 'felan'),
	],
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'select',
	'settings'  => 'bg_body_attachment',
	'label'     => esc_html__('Background Attachment', 'felan'),
	'section'   => $section,
	'transport' => 'postMessage',
	'default'   => $default['bg_body_attachment'],
	'choices'   => [
		'scroll' => esc_html__('Scroll', 'felan'),
		'fixed'  => esc_html__('Fixed', 'felan'),
	],
]);
