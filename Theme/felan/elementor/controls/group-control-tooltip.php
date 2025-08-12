<?php

namespace Felan_Elementor;

use Elementor\Group_Control_Base;
use Elementor\Controls_Manager;

defined('ABSPATH') || exit;

/**
 * Elementor tooltip control.
 *
 * A base control for creating tooltip control.
 *
 * @since 1.0.0
 */
class Group_Control_Tooltip extends Group_Control_Base
{

	protected static $fields;

	public static function get_type()
	{
		return 'tooltip';
	}

	protected function init_fields()
	{
		$fields = [];

		$fields['skin'] = [
			'label'   => esc_html__('Tooltip Skin', 'felan'),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				''        => esc_html__('Black', 'felan'),
				'white'   => esc_html__('White', 'felan'),
				'primary' => esc_html__('Primary', 'felan'),
			],
			'default' => '',
		];

		$fields['position'] = [
			'label'   => esc_html__('Tooltip Position', 'felan'),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'top'          => esc_html__('Top', 'felan'),
				'right'        => esc_html__('Right', 'felan'),
				'bottom'       => esc_html__('Bottom', 'felan'),
				'left'         => esc_html__('Left', 'felan'),
				'top-left'     => esc_html__('Top Left', 'felan'),
				'top-right'    => esc_html__('Top Right', 'felan'),
				'bottom-left'  => esc_html__('Bottom Left', 'felan'),
				'bottom-right' => esc_html__('Bottom Right', 'felan'),
			],
			'default' => 'top',
		];

		return $fields;
	}

	protected function get_default_options()
	{
		return [
			'popover' => [
				'starter_title' => _x('Tooltip', 'Tooltip Control', 'felan'),
				'starter_name'  => 'enable',
				'starter_value' => 'yes',
				'settings'      => [
					'render_type' => 'template',
				],
			],
		];
	}
}
