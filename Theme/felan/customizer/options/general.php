<?php

/**
 * General Option
 *
 * @package Felan Theme
 * @version 1.0.0
 */

$panel = 'general';

$default = felan_get_default_theme_options();

// General
Felan_Kirki::add_panel($panel, array(
	'title'    => esc_html__('General', 'felan'),
	'priority' => 10,
));

// Site Identity
Felan_Kirki::add_section('site_identity', array(
	'title'    => esc_html__('Site Identity', 'felan'),
	'priority' => 10,
	'panel'    => $panel,
));

Felan_Kirki::add_field('theme', [
	'type'            => 'image',
	'priority'        => 80,
	'settings'        => 'logo_dark',
	'label'           => esc_html__('Logo Dark', 'felan'),
	'section'         => 'site_identity',
	'default'         => $default['logo_dark'],
]);

Felan_Kirki::add_field('theme', [
	'type'            => 'image',
	'priority'        => 80,
	'settings'        => 'logo_dark_retina',
	'label'           => esc_html__('Logo Dark Retina', 'felan'),
	'section'         => 'site_identity',
	'default'         => $default['logo_dark_retina'],
]);

Felan_Kirki::add_field('theme', [
	'type'            => 'image',
	'priority'        => 80,
	'settings'        => 'logo_light',
	'label'           => esc_html__('Logo Light', 'felan'),
	'section'         => 'site_identity',
	'default'         => $default['logo_light'],
]);

Felan_Kirki::add_field('theme', [
	'type'            => 'image',
	'priority'        => 80,
	'settings'        => 'logo_light_retina',
	'label'           => esc_html__('Logo Light Retina', 'felan'),
	'section'         => 'site_identity',
	'default'         => $default['logo_light_retina'],
]);

// Page Loading Effect
Felan_Kirki::add_section('page_loading_effect', array(
	'title'    => esc_html__('Page Loading Effect', 'felan'),
	'priority' => 10,
	'panel'    => $panel,
));

Felan_Kirki::add_field('theme', [
	'type'     => 'radio',
	'settings' => 'type_loading_effect',
	'label'    => esc_html__('Type Loading Effect', 'felan'),
	'section'  => 'page_loading_effect',
	'default'  => $default['type_loading_effect'],
	'choices'  => [
		'none'   		=> esc_html__('None', 'felan'),
		'css_animation' => esc_html__('CSS Animation', 'felan'),
		'image'  		=> esc_html__('Image', 'felan'),
	],
]);

Felan_Kirki::add_field('theme', [
	'type'     => 'radio-buttonset',
	'settings' => 'animation_loading_effect',
	'label'    => esc_html__('Animation Type', 'felan'),
	'section'  => 'page_loading_effect',
	'default'  => $default['animation_loading_effect'],
	'choices'  => [
		'css-1'  => '<span class="felan-ldef-circle felan-ldef-loading"><span></span></span>',
		'css-2'  => '<span class="felan-ldef-dual-ring felan-ldef-loading"></span>',
		'css-3'  => '<span class="felan-ldef-facebook felan-ldef-loading"><span></span><span></span><span></span></span>',
		'css-4'  => '<span class="felan-ldef-heart felan-ldef-loading"><span></span></span>',
		'css-5'  => '<span class="felan-ldef-ring felan-ldef-loading"><span></span><span></span><span></span><span></span></span>',
		'css-6'  => '<span class="felan-ldef-roller felan-ldef-loading"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></span>',
		'css-7'  => '<span class="felan-ldef-default felan-ldef-loading"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></span>',
		'css-8'  => '<span class="felan-ldef-ellipsis felan-ldef-loading"><span></span><span></span><span></span><span></span></span>',
		'css-9'  => '<span class="felan-ldef-grid felan-ldef-loading"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></span>',
		'css-10' => '<span class="felan-ldef-hourglass felan-ldef-loading"></span>',
		'css-11' => '<span class="felan-ldef-ripple felan-ldef-loading"><span></span><span></span></span>',
		'css-12' => '<span class="felan-ldef-spinner felan-ldef-loading"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></span>',
	],
]);

Felan_Kirki::add_field('theme', [
	'type'     => 'image',
	'settings' => 'image_loading_effect',
	'label'    => esc_html__('Image', 'felan'),
	'section'  => 'page_loading_effect',
	'default'  => $default['image_loading_effect'],
]);

// Page Title
Felan_Kirki::add_section('page_title', array(
	'title'    => esc_html__('Page Title', 'felan'),
	'priority' => 10,
	'panel'    => $panel,
));


Felan_Kirki::add_field('theme', [
	'type'      => 'color',
	'settings'  => 'page_title_text_color',
	'label'     => esc_html__('Text Color', 'felan'),
	'section'   => 'page_title',
	'transport' => 'postMessage',
	'default'   => $default['page_title_text_color'],
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'color',
	'settings'  => 'page_title_bg_color',
	'label'     => esc_html__('Background Color', 'felan'),
	'section'   => 'page_title',
	'transport' => 'postMessage',
	'default'   => $default['page_title_bg_color'],
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'image',
	'settings'  => 'page_title_bg_image',
	'label'     => esc_html__('Background Image', 'felan'),
	'section'   => 'page_title',
	'transport' => 'postMessage',
	'default'   => $default['page_title_bg_image'],
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'select',
	'settings'  => 'page_title_bg_size',
	'label'     => esc_html__('Background Size', 'felan'),
	'section'   => 'page_title',
	'default'   => $default['page_title_bg_size'],
	'transport' => 'postMessage',
	'choices'   => [
		'auto'    => esc_html__('Auto', 'felan'),
		'cover'   => esc_html__('Cover', 'felan'),
		'contain' => esc_html__('Contain', 'felan'),
		'initial' => esc_html__('Initial', 'felan'),
	],
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'select',
	'settings'  => 'page_title_bg_repeat',
	'label'     => esc_html__('Background Repeat', 'felan'),
	'section'   => 'page_title',
	'default'   => $default['page_title_bg_repeat'],
	'transport' => 'postMessage',
	'choices'   => [
		'no-repeat' => esc_html__('No Repeat', 'felan'),
		'repeat'    => esc_html__('Repeat', 'felan'),
		'repeat-x'  => esc_html__('Repeat X', 'felan'),
		'repeat-y'  => esc_html__('Repeat Y', 'felan'),
	],
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'select',
	'settings'  => 'page_title_bg_position',
	'label'     => esc_html__('Background Position', 'felan'),
	'section'   => 'page_title',
	'default'   => $default['page_title_bg_position'],
	'transport' => 'postMessage',
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
	'settings'  => 'page_title_bg_attachment',
	'label'     => esc_html__('Background Attachment', 'felan'),
	'section'   => 'page_title',
	'default'   => $default['page_title_bg_attachment'],
	'transport' => 'postMessage',
	'choices'   => [
		'scroll' => esc_html__('Scroll', 'felan'),
		'fixed'  => esc_html__('Fixed', 'felan'),
	],
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'slider',
	'settings'  => 'page_title_font_size',
	'label'     => esc_html__('Font Size', 'felan'),
	'section'   => 'page_title',
	'transport' => 'postMessage',
	'default'   => $default['page_title_font_size'],
	'choices'   => [
		'min'  => 12,
		'max'  => 50,
		'step' => 1,
	],
]);

Felan_Kirki::add_field('theme', [
	'type'      => 'slider',
	'settings'  => 'page_title_letter_spacing',
	'label'     => esc_html__('Letter Spacing', 'felan'),
	'section'   => 'page_title',
	'transport' => 'postMessage',
	'default'   => $default['page_title_letter_spacing'],
	'choices'   => [
		'min'  => 0,
		'max'  => 10,
		'step' => 0.5,
	],
]);
