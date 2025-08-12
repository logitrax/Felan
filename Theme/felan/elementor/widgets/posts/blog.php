<?php

namespace Felan_Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Core\Base\Document;
use ElementorPro\Plugin;

defined('ABSPATH') || exit;

class Widget_Blog extends Posts_Base
{

	public function get_name()
	{
		return 'felan-blog';
	}

	public function get_title()
	{
		return esc_html__('Blog', 'felan');
	}

	public function get_icon_part()
	{
		return 'eicon-post-list';
	}

	public function get_keywords()
	{
		return ['blog', 'post'];
	}

	protected function get_post_type()
	{
		return 'post';
	}

	protected function get_post_category()
	{
		return 'category';
	}

	public function get_style_depends()
	{
		return ['felan-el-widget-blogs'];
	}

	public function is_reload_preview_required()
	{
		return false;
	}

	protected function register_controls()
	{
		$this->add_layout_section();

		$this->add_banners_section();

		$this->add_grid_section();

		$this->add_pagination_section();

		$this->add_content_style_section();

		$this->add_thumbnail_style_section();

		$this->add_caption_style_section();

		$this->add_overlay_style_section();

		$this->add_pagination_style_section();

		parent::register_controls();
	}

	private function add_layout_section()
	{
		$this->start_controls_section('layout_section', [
			'label' => esc_html__('Layout', 'felan'),
		]);

		$this->add_control('layout', [
			'label'   => esc_html__('Layout', 'felan'),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'grid'    => esc_html__('Grid', 'felan'),
				'list-01'    => esc_html__('List', 'felan'),
			],
			'default' => 'grid',
		]);

		$this->add_control('hover_effect', [
			'label'        => esc_html__('Hover Effect', 'felan'),
			'type'         => Controls_Manager::SELECT,
			'options'      => [
				''         => esc_html__('None', 'felan'),
				'zoom-in'  => esc_html__('Zoom In', 'felan'),
				'zoom-out' => esc_html__('Zoom Out', 'felan'),
			],
			'default'      => '',
			'prefix_class' => 'felan-animation-',
		]);

		$this->add_control('big_first', [
			'label'        => esc_html__('Big First', 'felan'),
			'type'         => Controls_Manager::SWITCHER,
			'default'      => '0',
			'return_value' => '1',
			'condition'    => [
				'layout' => 'list-02',
			],
		]);

		$this->add_caption_popover();

		$this->add_overlay_popover();

		$this->end_controls_section();
	}

	private function add_banners_section()
	{
		$this->start_controls_section('banners_section', [
			'label'     => esc_html__('Banners', 'felan'),
			'condition' => [
				'layout' => ['masonry'],
			],
		]);

		$banner_repeater = new Repeater();

		$banner_repeater->add_control('position', [
			'label' => esc_html__('After Post Number', 'felan'),
			'type'  => Controls_Manager::NUMBER,
			'step'  => 1,
		]);

		$this->add_control('banners', [
			'label'         => esc_html__('Banners', 'felan'),
			'type'          => Controls_Manager::REPEATER,
			'fields'        => $banner_repeater->get_controls(),
			'prevent_empty' => false,
		]);

		$this->end_controls_section();
	}

	private function add_caption_popover()
	{
		$this->add_control('show_caption', [
			'label'        => esc_html__('Caption', 'felan'),
			'type'         => Controls_Manager::POPOVER_TOGGLE,
			'label_off'    => esc_html__('Default', 'felan'),
			'label_on'     => esc_html__('Custom', 'felan'),
			'return_value' => 'yes',
			'condition'    => [
				'layout!' => ['zigzag'],
			],
		]);

		$this->start_popover();

		$this->add_control('caption_style', [
			'label'   => esc_html__('Style', 'felan'),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'01' => '01',
				'02' => '02',
				'03' => '03',
			],
			'default' => '01',
		]);

		$this->add_control('show_caption_category', [
			'label'     => esc_html__('Category', 'felan'),
			'type'      => Controls_Manager::SWITCHER,
			'label_on'  => esc_html__('Show', 'felan'),
			'label_off' => esc_html__('Hide', 'felan'),
			'default'   => 'yes',
			'separator' => 'before',
		]);

		$this->add_control('show_caption_excerpt', [
			'label'     => esc_html__('Excerpt', 'felan'),
			'type'      => Controls_Manager::SWITCHER,
			'label_on'  => esc_html__('Show', 'felan'),
			'label_off' => esc_html__('Hide', 'felan'),
			'default'   => 'yes',
			'separator' => 'before',
		]);

		$this->add_control('excerpt_length', [
			'label'     => esc_html__('Excerpt Length', 'felan'),
			'type'      => Controls_Manager::NUMBER,
			'min'       => 5,
			'condition' => [
				'show_caption_excerpt' => 'yes',
			],
		]);

		$this->add_control('show_caption_read_more', [
			'label'     => esc_html__('Read More', 'felan'),
			'type'      => Controls_Manager::SWITCHER,
			'label_on'  => esc_html__('Show', 'felan'),
			'label_off' => esc_html__('Hide', 'felan'),
			'default'   => 'yes',
			'separator' => 'before',
		]);

		$this->add_control('read_more_text', [
			'label'     => esc_html__('Read More Text', 'felan'),
			'type'      => Controls_Manager::TEXT,
			'default'   => esc_html__('Read More', 'felan'),
			'condition' => [
				'show_caption_read_more' => 'yes',
			],
		]);

		$this->add_control('show_caption_share', [
			'label'     => esc_html__('Share', 'felan'),
			'type'      => Controls_Manager::SWITCHER,
			'label_on'  => esc_html__('Show', 'felan'),
			'label_off' => esc_html__('Hide', 'felan'),
		]);

		$this->add_control('show_caption_meta', [
			'label'       => esc_html__('Meta', 'felan'),
			'label_block' => true,
			'type'        => Controls_Manager::SELECT2,
			'multiple'    => true,
			'default'     => [
				'date',
				'views',
			],
			'options'     => [
				'author'   => esc_html__('Author', 'felan'),
				'date'     => esc_html__('Date', 'felan'),
				'views'    => esc_html__('View Count', 'felan'),
				'comments' => esc_html__('Comment Count', 'felan'),
			],
		]);

		$this->end_popover();
	}

	private function add_overlay_popover()
	{
		$this->add_control('show_overlay', [
			'label'        => esc_html__('Overlay', 'felan'),
			'type'         => Controls_Manager::POPOVER_TOGGLE,
			'label_off'    => esc_html__('Default', 'felan'),
			'label_on'     => esc_html__('Custom', 'felan'),
			'return_value' => 'yes',
			'condition'    => [
				'layout!' => [
					'zigzag',
					'list-03',
					'one-left-featured',
				],
			],
		]);

		$this->start_popover();

		$this->add_control('overlay_style', [
			'label'   => esc_html__('Style', 'felan'),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'float'    => esc_html__('Float', 'felan'),
				'float-02' => esc_html__('Float-02', 'felan'),
			],
			'default' => 'float',
		]);

		$this->add_control('show_overlay_category', [
			'label'     => esc_html__('Category', 'felan'),
			'type'      => Controls_Manager::SWITCHER,
			'label_on'  => esc_html__('Show', 'felan'),
			'label_off' => esc_html__('Hide', 'felan'),
			'default'   => 'yes',
			'separator' => 'before',
		]);

		$this->add_control('show_overlay_title', [
			'label'     => esc_html__('Title', 'felan'),
			'type'      => Controls_Manager::SWITCHER,
			'label_on'  => esc_html__('Show', 'felan'),
			'label_off' => esc_html__('Hide', 'felan'),
			'separator' => 'before',
		]);

		$overlay_meta_repeater = new Repeater();

		$overlay_meta_repeater->add_control('meta', [
			'label'       => esc_html__('Select Meta', 'felan'),
			'label_block' => true,
			'type'        => Controls_Manager::SELECT,
			'default'     => 'date',
			'options'     => [
				'author'   => esc_html__('Author', 'felan'),
				'date'     => esc_html__('Date', 'felan'),
				'comments' => esc_html__('Comments', 'felan'),
			],
		]);

		$this->add_control('overlay_meta_data', [
			'label'         => esc_html__('Meta Data', 'felan'),
			'type'          => Controls_Manager::REPEATER,
			'fields'        => $overlay_meta_repeater->get_controls(),
			'title_field'   => '{{{ meta }}}',
			'classes'       => 'felan-control-repeater-title-capitalize',
			'prevent_empty' => false,
		]);

		$this->end_popover();
	}

	private function add_grid_section()
	{
		$this->start_controls_section('grid_options_section', [
			'label'     => esc_html__('Grid Options', 'felan'),
		]);

		$this->add_responsive_control('grid_columns', [
			'label'          => esc_html__('Columns', 'felan'),
			'type'           => Controls_Manager::NUMBER,
			'min'            => 1,
			'max'            => 12,
			'step'           => 1,
			'default'        => 3,
			'prefix_class'   => 'elementor-grid%s-',
		]);

		$this->add_responsive_control('grid_column_gutter', [
			'label'     => esc_html__('Column Spacing', 'felan'),
			'type'      => Controls_Manager::NUMBER,
			'min'       => 0,
			'max'       => 200,
			'step'      => 1,
			'default'   => 30,
			'selectors' => [
				'{{WRAPPER}}' => '--grid-column-gap: {{VALUE}}px;',
			],
		]);

		$this->add_responsive_control('grid_row_gutter', [
			'label'     => esc_html__('Row Gutter', 'felan'),
			'type'      => Controls_Manager::NUMBER,
			'min'       => 0,
			'max'       => 200,
			'step'      => 1,
			'default'   => 30,
			'selectors' => [
				'{{WRAPPER}}' => '--grid-row-gap: {{VALUE}}px;',
			],
		]);

		$this->end_controls_section();
	}


	private function add_content_style_section()
	{
		$this->start_controls_section('content_style_section', [
			'label' => esc_html__('Content', 'felan'),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_responsive_control(
			'content_padding',
			[
				'label' => __('Padding', 'felan'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .grid-item' => 'padding: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'content_background',
			[
				'label' => __('Background Color', 'felan'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .grid-item' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'content_hover_color',
			[
				'label' => __('Hover Color', 'felan'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .grid-item:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .grid-item:hover .post-categories' => 'color: {{VALUE}}',
					'{{WRAPPER}} .grid-item:hover a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'content_hover_background',
			[
				'label' => __('Hover Background Color', 'felan'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .grid-item:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	private function add_thumbnail_style_section()
	{
		$this->start_controls_section('thumbnail_style_section', [
			'label' => esc_html__('Thumbnail', 'felan'),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_control('thumbnail_border_radius', [
			'label'     => esc_html__('Border Radius', 'felan'),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .post-thumbnail' => 'border-radius: {{SIZE}}{{UNIT}};overflow: hidden',
			],
		]);

		$this->start_controls_tabs('thumbnail_effects_tabs');

		$this->start_controls_tab('thumbnail_effects_normal_tab', [
			'label' => esc_html__('Normal', 'felan'),
		]);

		$this->add_group_control(Group_Control_Box_Shadow::get_type(), [
			'name'     => 'thumbnail_box_shadow',
			'selector' => '{{WRAPPER}} .post-thumbnail',
		]);

		$this->add_group_control(Group_Control_Css_Filter::get_type(), [
			'name'     => 'css_filters',
			'selector' => '{{WRAPPER}} .post-thumbnail img',
		]);

		$this->add_control('thumbnail_opacity', [
			'label'     => esc_html__('Opacity', 'felan'),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'max'  => 1,
					'min'  => 0.10,
					'step' => 0.01,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .post-thumbnail img' => 'opacity: {{SIZE}};',
			],
		]);

		$this->add_control('thumbnail_width', [
			'label'     => esc_html__('Width', 'felan'),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'%' => [
					'max'  => 100,
					'min'  => 0,
					'step' => 1,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .post-thumbnail-wrapper' => 'width: {{SIZE}}%;object-fit: cover;',
				'{{WRAPPER}} .post-caption' => 'width: calc( 100% - {{SIZE}}% );',
			],
			'condition' => [
				'layout' => ['list-02'],
			],
		]);

		$this->add_control('thumbnail_height', [
			'label'     => esc_html__('Height', 'felan'),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'max'  => 1000,
					'min'  => 100,
					'step' => 1,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .post-thumbnail img' => 'height: {{SIZE}}px;object-fit: cover;',
			],
		]);

		$this->end_controls_tab();

		$this->start_controls_tab('thumbnail_effects_hover_tab', [
			'label' => esc_html__('Hover', 'felan'),
		]);

		$this->add_group_control(Group_Control_Box_Shadow::get_type(), [
			'name'     => 'thumbnail_box_shadow_hover',
			'selector' => '{{WRAPPER}} .felan-box:hover .post-thumbnail',
		]);

		$this->add_group_control(Group_Control_Css_Filter::get_type(), [
			'name'     => 'css_filters_hover',
			'selector' => '{{WRAPPER}} .felan-box:hover .post-thumbnail img',
		]);

		$this->add_control('thumbnail_opacity_hover', [
			'label'     => esc_html__('Opacity', 'felan'),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'max'  => 1,
					'min'  => 0.10,
					'step' => 0.01,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .felan-box:hover .post-thumbnail img' => 'opacity: {{SIZE}};',
			],
		]);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	private function add_caption_style_section()
	{
		$this->start_controls_section('caption_style_section', [
			'label'      => esc_html__('Caption', 'felan'),
			'tab'        => Controls_Manager::TAB_STYLE,
			'conditions' => [
				'relation' => 'or',
				'terms'    => [
					[
						'name'     => 'show_caption',
						'operator' => '===',
						'value'    => 'yes',
					],
					[
						'name'     => 'layout',
						'operator' => '===',
						'value'    => 'list-03',
					],
				],
			],
		]);

		$this->add_responsive_control('text_align', [
			'label'     => esc_html__('Text Align', 'felan'),
			'type'      => Controls_Manager::CHOOSE,
			'options'   => Widget_Utils::get_control_options_text_align(),
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .post-wrapper' => 'text-align: {{VALUE}};',
			],
		]);

		$this->add_responsive_control('caption_margin', [
			'label'      => esc_html__('Margin', 'felan'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors'  => [
				'{{WRAPPER}} .post-caption' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('caption_padding', [
			'label'      => esc_html__('Padding', 'felan'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors'  => [
				'{{WRAPPER}} .post-caption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		// Meta style.
		$this->add_control('caption_meta_heading', [
			'label'     => esc_html__('Meta', 'felan'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
			'condition' => [
				'show_caption_meta!' => '',
			],
		]);

		$this->add_responsive_control('caption_meta_margin', [
			'label'      => esc_html__('Margin', 'felan'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors'  => [
				'{{WRAPPER}} .post-meta' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
			'condition'  => [
				'show_caption_meta!' => '',
			],
		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'      => 'caption_meta_typography',
			'label'     => esc_html__('Typography', 'felan'),
			'selector'  => '{{WRAPPER}} .post-meta',
			'condition' => [
				'show_caption_meta!' => '',
			],
		]);

		$this->start_controls_tabs('caption_meta_style_tabs', [
			'condition' => [
				'show_caption_meta!' => '',
			],
		]);

		$this->start_controls_tab('caption_meta_style_normal_tab', [
			'label' => esc_html__('Normal', 'felan'),
		]);

		$this->add_control('caption_meta_text_color', [
			'label'     => esc_html__('Text Color', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .post-meta' => 'color: {{VALUE}};',
			],
		]);

		$this->add_control('caption_meta_link_color', [
			'label'     => esc_html__('Link Color', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .post-meta a' => 'color: {{VALUE}};',
			],
		]);

		$this->end_controls_tab();

		$this->start_controls_tab('caption_meta_style_hover_tab', [
			'label' => esc_html__('Hover', 'felan'),
		]);

		$this->add_control('caption_meta_link_hover_color', [
			'label'     => esc_html__('Link Color', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .post-meta a:hover' => 'color: {{VALUE}};',
			],
		]);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		// Category style.
		$this->add_control('caption_category_heading', [
			'label'     => esc_html__('Category', 'felan'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
			'condition' => [
				'show_caption_category' => 'yes',
			],
		]);

		$this->add_responsive_control('caption_category_margin', [
			'label'      => esc_html__('Margin', 'felan'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors'  => [
				'{{WRAPPER}} .post-categories' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
			'condition'  => [
				'show_caption_category' => 'yes',
			],
		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'      => 'caption_category_typography',
			'label'     => esc_html__('Typography', 'felan'),
			'selector'  => '{{WRAPPER}} .post-categories',
			'condition' => [
				'show_caption_category' => 'yes',
			],
		]);

		$this->start_controls_tabs('caption_category_style_tabs', [
			'condition' => [
				'show_caption_category' => 'yes',
			],
		]);

		$this->start_controls_tab('caption_category_style_normal_tab', [
			'label' => esc_html__('Normal', 'felan'),
		]);

		$this->add_control('caption_category_color', [
			'label'     => esc_html__('Color', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .post-categories' => 'color: {{VALUE}};',
			],
		]);

		$this->end_controls_tab();

		$this->start_controls_tab('caption_category_style_hover_tab', [
			'label' => esc_html__('Hover', 'felan'),
		]);

		$this->add_control('caption_category_hover_color', [
			'label'     => esc_html__('Color', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .post-categories a:hover' => 'color: {{VALUE}};',
			],
		]);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		// Title style.
		$this->add_control('caption_title_heading', [
			'label'     => esc_html__('Title', 'felan'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		]);

		$this->add_responsive_control('caption_title_margin', [
			'label'      => esc_html__('Margin', 'felan'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors'  => [
				'{{WRAPPER}} .post-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'caption_title_typography',
			'label'    => esc_html__('Typography', 'felan'),
			'selector' => '{{WRAPPER}} .post-title',
		]);

		$this->start_controls_tabs('caption_title_style_tabs');

		$this->start_controls_tab('caption_title_style_normal_tab', [
			'label' => esc_html__('Normal', 'felan'),
		]);

		$this->add_control('caption_title_color', [
			'label'     => esc_html__('Color', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .post-title a' => 'color: {{VALUE}};',
			],
		]);

		$this->end_controls_tab();

		$this->start_controls_tab('caption_title_style_hover_tab', [
			'label' => esc_html__('Hover', 'felan'),
		]);

		$this->add_control('caption_title_hover_color', [
			'label'     => esc_html__('Color', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .post-title a:hover' => 'color: {{VALUE}};',
			],
		]);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		// Excerpt style.
		$this->add_control('caption_excerpt_heading', [
			'label'     => esc_html__('Excerpt', 'felan'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
			'condition' => [
				'show_caption_excerpt' => 'yes',
			],
		]);

		$this->add_responsive_control('caption_excerpt_margin', [
			'label'      => esc_html__('Margin', 'felan'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors'  => [
				'{{WRAPPER}} .post-excerpt' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
			'condition'  => [
				'show_caption_excerpt' => 'yes',
			],
		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'      => 'caption_excerpt_typography',
			'label'     => esc_html__('Typography', 'felan'),
			'selector'  => '{{WRAPPER}} .post-excerpt',
			'condition' => [
				'show_caption_excerpt' => 'yes',
			],
		]);

		$this->add_control('caption_excerpt_color', [
			'label'     => esc_html__('Color', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .post-excerpt' => 'color: {{VALUE}};',
			],
			'condition' => [
				'show_caption_excerpt' => 'yes',
			],
		]);

		// Read more style.
		$this->add_control('caption_read_more_heading', [
			'label'     => esc_html__('Read More', 'felan'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
			'condition' => [
				'show_caption_read_more' => 'yes',
			],
		]);

		$this->add_responsive_control('caption_read_more_margin', [
			'label'      => esc_html__('Margin', 'felan'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors'  => [
				'{{WRAPPER}} .post-read-more' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
			'condition'  => [
				'show_caption_read_more' => 'yes',
			],
		]);

		$this->start_controls_tabs('read_more_style_tabs', [
			'condition' => [
				'show_caption_read_more' => 'yes',
			],
		]);

		$this->start_controls_tab('read_more_style_normal_tab', [
			'label' => esc_html__('Normal', 'felan'),
		]);

		$this->add_control('button_text_color', [
			'label'     => esc_html__('Text Color', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .felan-button' => 'color: {{VALUE}};',
			],
		]);

		$this->add_control('button_background_color', [
			'label'     => esc_html__('Background Color', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .felan-button:before' => 'background-color: {{VALUE}};',
			],
		]);

		$this->add_control('button_border_color', [
			'label'     => esc_html__('Border Color', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .felan-button' => 'border-color: {{VALUE}};',
			],
		]);

		$this->end_controls_tab();

		$this->start_controls_tab('read_more_style_hover_tab', [
			'label' => esc_html__('Hover', 'felan'),
		]);

		$this->add_control('button_hover_text_color', [
			'label'     => esc_html__('Text Color', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .felan-button:hover' => 'color: {{VALUE}};',
			],
		]);

		$this->add_control('button_hover_background_color', [
			'label'     => esc_html__('Background Color', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .felan-button:after' => 'background-color: {{VALUE}};',
			],
		]);

		$this->add_control('button_hover_border_color', [
			'label'     => esc_html__('Border Color', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .felan-button:hover' => 'border-color: {{VALUE}};',
			],
		]);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	private function add_overlay_style_section()
	{
		$this->start_controls_section('overlay_style_section', [
			'label'     => esc_html__('Overlay', 'felan'),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => [
				'show_overlay' => 'yes',
			],
		]);

		$this->add_control('overlay_category_heading', [
			'label'     => esc_html__('Category', 'felan'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'overlay_category_typography',
			'label'    => esc_html__('Typography', 'felan'),
			'selector' => '{{WRAPPER}} .post-overlay-categories',
		]);

		$this->start_controls_tabs('overlay_category_style_tabs');

		$this->start_controls_tab('overlay_category_style_normal_tab', [
			'label' => esc_html__('Normal', 'felan'),
		]);

		$this->add_control('overlay_category_color', [
			'label'     => esc_html__('Color', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .post-overlay-categories' => 'color: {{VALUE}};',
			],
		]);

		$this->add_control('overlay_category_bg_color', [
			'label'     => esc_html__('Background', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .post-overlay-categories a' => 'background-color: {{VALUE}};',
			],
		]);

		$this->end_controls_tab();

		$this->start_controls_tab('overlay_category_style_hover_tab', [
			'label' => esc_html__('Hover', 'felan'),
		]);

		$this->add_control('overlay_category_hover_color', [
			'label'     => esc_html__('Color', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .post-overlay-categories a:hover' => 'color: {{VALUE}};',
			],
		]);

		$this->add_control('overlay_category_hover_bg_color', [
			'label'     => esc_html__('Background', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .post-overlay-categories a:hover' => 'background-color: {{VALUE}};',
			],
		]);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();
		$this->query_posts();
		/**
		 * @var $query \WP_Query
		 */
		$query     = $this->get_query();
		$post_type = $this->get_post_type();

		$this->add_render_attribute('wrapper', 'class', [
			'felan-grid-wrapper felan-blog',
			'felan-blog-' . $settings['layout'],
		]);

		if (!empty($settings['show_caption']) && 'yes' === $settings['show_caption']) {
			$this->add_render_attribute('wrapper', 'class', 'felan-blog-caption-style-' . $settings['caption_style']);
		}

		if (!empty($settings['show_overlay']) && 'yes' === $settings['show_overlay']) {
			$this->add_render_attribute('wrapper', 'class', 'felan-blog-overlay-style-' . $settings['overlay_style']);
		}

		$this->add_render_attribute('content-wrapper', 'class', 'elementor-grid');

		if ('current_query' === $settings['query_source']) {
			$this->add_render_attribute('wrapper', 'data-query-main', '1');
		}

		if (!empty($settings['pagination_type']) && $query->found_posts > $settings['query_number']) {
			$this->add_render_attribute('wrapper', 'data-pagination', $settings['pagination_type']);
		}

		if (!empty($settings['pagination_custom_button_id'])) {
			$this->add_render_attribute('wrapper', 'data-pagination-custom-button-id', $settings['pagination_custom_button_id']);
		}
?>


		<div <?php $this->print_attributes_string('wrapper'); ?>>
			<?php
			if ($query->have_posts()) : ?>

				<?php
				$felan_grid_query['source']        = $settings['query_source'];
				$felan_grid_query['action']        = "{$post_type}_infinite_load";
				$felan_grid_query['max_num_pages'] = $query->max_num_pages;
				$felan_grid_query['found_posts']   = $query->found_posts;
				$felan_grid_query['count']         = $query->post_count;
				$felan_grid_query['query_vars']    = $this->get_query_args();
				$felan_grid_query['settings']      = $settings;
				$felan_grid_query                  = htmlspecialchars(wp_json_encode($felan_grid_query));
				?>
				<input type="hidden" class="felan-query-input" <?php echo 'value="' . $felan_grid_query . '"'; ?> />

				<div <?php $this->print_attributes_string('content-wrapper'); ?>>

					<?php

					set_query_var('felan_query', $query);
					set_query_var('settings', $settings);
					get_template_part('templates/loop/widgets/blog/style', $settings['layout']);
					?>
				</div>

				<?php $this->print_pagination($query, $settings); ?>

				<?php wp_reset_postdata(); ?>
			<?php endif; ?>
		</div>
<?php
	}
}
