<?php

namespace Felan_Elementor;

defined('ABSPATH') || exit;

class WPML_Translatable_Nodes
{

	private static $_instance = null;

	public static function instance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function initialize()
	{
		add_action('init', [$this, 'wp_init']);
	}

	public function wp_init()
	{
		add_filter('wpml_elementor_widgets_to_translate', [$this, 'wpml_widgets_to_translate_filter']);
	}

	public function get_translatable_node()
	{
		require_once FELAN_ELEMENTOR_DIR . '/wpml/class-translate-widget-google-map.php';
		require_once FELAN_ELEMENTOR_DIR . '/wpml/class-translate-widget-list.php';
		require_once FELAN_ELEMENTOR_DIR . '/wpml/class-translate-widget-attribute-list.php';
		require_once FELAN_ELEMENTOR_DIR . '/wpml/class-translate-widget-pricing-table.php';
		require_once FELAN_ELEMENTOR_DIR . '/wpml/class-translate-widget-table.php';
		require_once FELAN_ELEMENTOR_DIR . '/wpml/class-translate-widget-modern-carousel.php';
		require_once FELAN_ELEMENTOR_DIR . '/wpml/class-translate-widget-modern-slider.php';
		require_once FELAN_ELEMENTOR_DIR . '/wpml/class-translate-widget-team-member-carousel.php';
		require_once FELAN_ELEMENTOR_DIR . '/wpml/class-translate-widget-testimonial-carousel.php';

		$widgets['felan-attribute-list'] = [
			'fields'            => [],
			'integration-class' => '\Felan_Elementor\Translate_Widget_Attribute_List',
		];

		$widgets['felan-heading'] = [
			'fields' => [
				[
					'field'       => 'title',
					'type'        => esc_html__('Modern Heading: Primary', 'felan'),
					'editor_type' => 'AREA',
				],
				'title_link' => [
					'field'       => 'url',
					'type'        => esc_html__('Modern Heading: Link', 'felan'),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'description',
					'type'        => esc_html__('Modern Heading: Description', 'felan'),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'sub_title_text',
					'type'        => esc_html__('Modern Heading: Secondary', 'felan'),
					'editor_type' => 'AREA',
				],
			],
		];

		$widgets['felan-button'] = [
			'fields' => [
				[
					'field'       => 'text',
					'type'        => esc_html__('Button: Text', 'felan'),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'badge_text',
					'type'        => esc_html__('Button: Badge', 'felan'),
					'editor_type' => 'LINE',
				],
				'link' => [
					'field'       => 'url',
					'type'        => esc_html__('Button: Link', 'felan'),
					'editor_type' => 'LINK',
				],
			],
		];

		$widgets['felan-banner'] = [
			'fields' => [
				[
					'field'       => 'title_text',
					'type'        => esc_html__('Banner: Title', 'felan'),
					'editor_type' => 'LINE',
				],
				'link' => [
					'field'       => 'url',
					'type'        => esc_html__('Banner: Link', 'felan'),
					'editor_type' => 'LINK',
				],
			],
		];

		$widgets['felan-circle-progress-chart'] = [
			'fields' => [
				[
					'field'       => 'inner_content_text',
					'type'        => esc_html__('Circle Chart: Text', 'felan'),
					'editor_type' => 'LINE',
				],
			],
		];

		$widgets['felan-flip-box'] = [
			'fields' => [
				[
					'field'       => 'title_text_a',
					'type'        => esc_html__('Flip Box: Front Title', 'felan'),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'description_text_a',
					'type'        => esc_html__('Flip Box: Front Description', 'felan'),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'title_text_b',
					'type'        => esc_html__('Flip Box: Back Title', 'felan'),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'description_text_b',
					'type'        => esc_html__('Flip Box: Back Description', 'felan'),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'button_text',
					'type'        => esc_html__('Flip Box: Button Text', 'felan'),
					'editor_type' => 'LINE',
				],
				'link' => [
					'field'       => 'url',
					'type'        => esc_html__('Flip Box: Link', 'felan'),
					'editor_type' => 'LINK',
				],
			],
		];

		$widgets['felan-google-map'] = [
			'fields'            => [],
			'integration-class' => '\Felan_Elementor\Translate_Widget_Google_Map',
		];

		$widgets['felan-icon'] = [
			'fields' => [
				'link' => [
					'field'       => 'url',
					'type'        => esc_html__('Icon: Link', 'felan'),
					'editor_type' => 'LINK',
				],
			],
		];

		$widgets['felan-icon-box'] = [
			'fields' => [
				[
					'field'       => 'title_text',
					'type'        => esc_html__('Icon Box: Title', 'felan'),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'description_text',
					'type'        => esc_html__('Icon Box: Description', 'felan'),
					'editor_type' => 'AREA',
				],
				'link'        => [
					'field'       => 'url',
					'type'        => esc_html__('Icon Box: Link', 'felan'),
					'editor_type' => 'LINK',
				],
				[
					'field'       => 'button_text',
					'type'        => esc_html__('Icon Box: Button', 'felan'),
					'editor_type' => 'LINE',
				],
				'button_link' => [
					'field'       => 'url',
					'type'        => esc_html__('Icon Box: Button Link', 'felan'),
					'editor_type' => 'LINK',
				],
			],
		];

		$widgets['felan-image-box'] = [
			'fields' => [
				[
					'field'       => 'title_text',
					'type'        => esc_html__('Image Box: Title', 'felan'),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'description_text',
					'type'        => esc_html__('Image Box: Content', 'felan'),
					'editor_type' => 'AREA',
				],
				'link' => [
					'field'       => 'url',
					'type'        => esc_html__('Image Box: Link', 'felan'),
					'editor_type' => 'LINK',
				],
				[
					'field'       => 'button_text',
					'type'        => esc_html__('Image Box: Button', 'felan'),
					'editor_type' => 'LINE',
				],
			],
		];

		$widgets['felan-list'] = [
			'fields'            => [],
			'integration-class' => '\Felan_Elementor\Translate_Widget_List',
		];

		$widgets['felan-popup-video'] = [
			'fields' => [
				[
					'field'       => 'video_text',
					'type'        => esc_html__('Popup Video: Text', 'felan'),
					'editor_type' => 'LINE',
				],
				'video_url' => [
					'field'       => 'url',
					'type'        => esc_html__('Popup Video: Link', 'felan'),
					'editor_type' => 'LINK',
				],
				[
					'field'       => 'poster_caption',
					'type'        => esc_html__('Popup Video: Caption', 'felan'),
					'editor_type' => 'AREA',
				],
			],
		];

		$widgets['felan-pricing-table'] = [
			'fields'            => [
				[
					'field'       => 'heading',
					'type'        => esc_html__('Pricing Table: Heading', 'felan'),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'sub_heading',
					'type'        => esc_html__('Pricing Table: Description', 'felan'),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'currency',
					'type'        => esc_html__('Pricing Table: Currency', 'felan'),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'price',
					'type'        => esc_html__('Pricing Table: Price', 'felan'),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'period',
					'type'        => esc_html__('Pricing Table: Period', 'felan'),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'button_text',
					'type'        => esc_html__('Pricing Table: Button', 'felan'),
					'editor_type' => 'LINE',
				],
				'button_link' => [
					'field'       => 'url',
					'type'        => esc_html__('Pricing Table: Button Link', 'felan'),
					'editor_type' => 'LINK',
				],
			],
			'integration-class' => '\Felan_Elementor\Translate_Widget_Pricing_Table',
		];

		$widgets['felan-table'] = [
			'fields'            => [],
			'integration-class' => [
				'\Felan_Elementor\Translate_Widget_Pricing_Table_Head',
				'\Felan_Elementor\Translate_Widget_Pricing_Table_Body',
			],
		];

		$widgets['felan-team-member'] = [
			'fields' => [
				[
					'field'       => 'name',
					'type'        => esc_html__('Team Member: Name', 'felan'),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'content',
					'type'        => esc_html__('Team Member: Content', 'felan'),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'position',
					'type'        => esc_html__('Team Member: Position', 'felan'),
					'editor_type' => 'LINE',
				],
				'profile' => [
					'field'       => 'url',
					'type'        => esc_html__('Team Member: Profile', 'felan'),
					'editor_type' => 'LINK',
				],
			],
		];

		$widgets['felan-modern-carousel'] = [
			'fields'            => [],
			'integration-class' => '\Felan_Elementor\Translate_Widget_Modern_Carousel',
		];

		$widgets['felan-modern-slider'] = [
			'fields'            => [],
			'integration-class' => '\Felan_Elementor\Translate_Widget_Modern_Slider',
		];

		$widgets['felan-team-member-carousel'] = [
			'fields'            => [],
			'integration-class' => '\Felan_Elementor\Translate_Widget_Team_Member_Carousel',
		];

		$widgets['felan-testimonial-carousel'] = [
			'fields'            => [],
			'integration-class' => '\Felan_Elementor\Translate_Widget_Testimonial_Carousel',
		];

		return $widgets;
	}

	public function wpml_widgets_to_translate_filter($widgets)
	{
		$felan_widgets = $this->get_translatable_node();

		foreach ($felan_widgets as $widget_name => $widget) {
			$widgets[$widget_name]               = $widget;
			$widgets[$widget_name]['conditions'] = [
				'widgetType' => $widget_name,
			];
		}

		return $widgets;
	}
}

WPML_Translatable_Nodes::instance()->initialize();
