<?php

namespace Felan_Elementor;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;

defined('ABSPATH') || exit;

class Widget_Testimonial_Carousel extends Base
{

	private $current_slide = null;
	private $current_key   = null;
	private $slider_looped_slides = 4;

	public function get_name()
	{
		return 'felan-testimonial';
	}

	public function get_title()
	{
		return esc_html__('Testimonial Carousel', 'felan');
	}

	public function get_icon_part()
	{
		return 'eicon-testimonial-carousel';
	}

	public function get_keywords()
	{
		return ['testimonial', 'carousel'];
	}

	public function get_style_depends()
	{
		return ['felan-el-widget-testimonial-carousel'];
	}

	public function get_script_depends()
	{
		return ['carousel', 'felan-widget-testimonial-carousel'];
	}

	protected function register_controls()
	{
		$this->add_layout_section();

		$this->register_content();

		$this->add_slider_section();

		$this->add_box_style_section();

		$this->add_content_style_section();

		$this->add_image_style_section();
	}

	private function add_layout_section()
	{
		$this->start_controls_section('layout_section', [
			'label' => esc_html__('Layout', 'felan'),
		]);

		$this->add_control('style', [
			'label'        => esc_html__('Style', 'felan'),
			'type'         => Controls_Manager::SELECT,
			'default'      => '',
			'options'      => [
				''   => esc_html__('None', 'felan'),
				'01' => esc_html__('01', 'felan'),
				'02' => esc_html__('02', 'felan'),
				'03' => esc_html__('03', 'felan'),
				'04' => esc_html__('04', 'felan'),
			],
			'render_type'  => 'template',
			'prefix_class' => 'felan-testimonial-style-',
		]);

		$this->add_control('layout', [
			'label'        => esc_html__('Layout', 'felan'),
			'type'         => Controls_Manager::SELECT,
			'default'      => 'image-stacked',
			'options'      => [
				'image-inline'  => esc_html__('Image Inline', 'felan'),
				'image-stacked' => esc_html__('Image Stacked', 'felan'),
				'image-top'     => esc_html__('Image Top Overlap', 'felan'),
				'image-top-02'  => esc_html__('Image Top', 'felan'),
				'image-bottom'     => esc_html__('Image Bottom', 'felan'),
				'image-above'   => esc_html__('Image Above', 'felan'),
				'image-left'    => esc_html__('Image Left', 'felan'),
			],
			'render_type'  => 'template',
			'prefix_class' => 'layout-',
			'condition'    => [
				'style!' => '04',
			],
		]);

		$this->add_control('image_position', [
			'label'        => esc_html__('Info Position', 'felan'),
			'type'         => Controls_Manager::CHOOSE,
			'label_block'  => false,
			'default'      => 'below',
			'options'      => [
				'above'  => [
					'title' => esc_html__('Above', 'felan'),
					'icon'  => 'eicon-v-align-top',
				],
				'below'  => [
					'title' => esc_html__('Below', 'felan'),
					'icon'  => 'eicon-v-align-bottom',
				],
				'bottom' => [
					'title' => esc_html__('Bottom', 'felan'),
					'icon'  => 'eicon-v-align-stretch',
				],
			],
			'render_type'  => 'template',
			'prefix_class' => 'image-position-',
			'condition'    => [
				'layout' => [
					'image-inline',
					'image-stacked',
				],
			],
		]);

		$this->add_control('cite_layout', [
			'label'        => esc_html__('Cite Layout', 'felan'),
			'label_block'  => false,
			'type'         => Controls_Manager::CHOOSE,
			'default'      => 'block',
			'options'      => [
				'block'  => [
					'title' => esc_html__('Default', 'felan'),
					'icon'  => 'eicon-editor-list-ul',
				],
				'inline' => [
					'title' => esc_html__('Inline', 'felan'),
					'icon'  => 'eicon-ellipsis-h',
				],
			],
			'prefix_class' => 'felan-testimonial-cite-layout-',
		]);

		$this->add_control(
			'enable_quote',
			[
				'label' => esc_html__('Enable Quote', 'felan'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label' => __('Columns Gap', 'felan'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-carousel .testimonial-inner' => 'padding-left: calc({{SIZE}}{{UNIT}}/2); padding-right: calc({{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .slick-list' => 'margin-left: calc(-{{SIZE}}{{UNIT}}/2);margin-right: calc(-{{SIZE}}{{UNIT}}/2)',
				],
			]
		);

		$this->end_controls_section();
	}

	private function add_slider_section()
	{
		$this->start_controls_section('slider_section', [
			'label' => esc_html__('Settings', 'felan'),
			'tab' => Controls_Manager::TAB_CONTENT,
		]);

		$slides_to_show = range(1, 10);
		$slides_to_show = array_combine($slides_to_show, $slides_to_show);

		$this->add_responsive_control(
			'slides_to_show',
			[
				'label' => esc_html__('Slides to Show', 'felan'),
				'type' => Controls_Manager::SELECT,
				'default' => '2',
				'options' => [
					'' => esc_html__('Default', 'felan'),
				] + $slides_to_show,
			]
		);

		$this->add_responsive_control(
			'slides_to_scroll',
			[
				'label' => esc_html__('Slides to Scroll', 'felan'),
				'type' => Controls_Manager::SELECT,
				'description' => esc_html__('Set how many slides are scrolled per swipe.', 'felan'),
				'default' => '1',
				'options' => [
					'' => esc_html__('Default', 'felan'),
				] + $slides_to_show,
			]
		);

		$this->add_control(
			'navigation',
			[
				'label' => esc_html__('Navigation', 'felan'),
				'type' => Controls_Manager::SELECT,
				'default' => 'both',
				'options' => [
					'both' => esc_html__('Arrows and Dots', 'felan'),
					'arrows' => esc_html__('Arrows', 'felan'),
					'dots' => esc_html__('Dots', 'felan'),
					'none' => esc_html__('None', 'felan'),
				],
			]
		);

		$this->add_control('dots_alignment', [
			'label'     => esc_html__('Dots Alignment', 'felan'),
			'type'      => Controls_Manager::CHOOSE,
			'options'   => Widget_Utils::get_control_options_text_align(),
			'selectors' => [
				'{{WRAPPER}} .slick-dots' => 'text-align: {{VALUE}}',
			],
			'condition'    => [
				'navigation!' => [
					'arrows',
					'none',
				],
			],
		]);

		$this->add_control(
			'center_mode',
			[
				'label' => esc_html__('Center Mode', 'felan'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);

		$this->add_responsive_control(
			'center_padding',
			[
				'label' => esc_html__('Center Padding', 'felan'),
				'type' => Controls_Manager::NUMBER,
				'default' => 100,
				'condition'    => [
					'center_mode' => 'yes',
				]
			]
		);

		$this->add_control(
			'pause_on_hover',
			[
				'label' => esc_html__('Pause on Hover', 'felan'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'scroll_bar',
			[
				'label' => esc_html__('Scroll Bar', 'felan'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label' => esc_html__('Autoplay', 'felan'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label' => esc_html__('Autoplay Speed', 'felan'),
				'type' => Controls_Manager::NUMBER,
				'default' => 5000,
				'condition' => [
					'autoplay' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .slick-slide-bg' => 'animation-duration: calc({{VALUE}}ms*1.2); transition-duration: calc({{VALUE}}ms)',
				],
			]
		);

		$this->add_control(
			'infinite',
			[
				'label' => esc_html__('Infinite Loop', 'felan'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'transition',
			[
				'label' => esc_html__('Transition', 'felan'),
				'type' => Controls_Manager::SELECT,
				'default' => 'slide',
				'options' => [
					'slide' => esc_html__('Slide', 'felan'),
					'fade' => esc_html__('Fade', 'felan'),
				],
			]
		);

		$this->add_control(
			'transition_speed',
			[
				'label' => esc_html__('Transition Speed', 'felan') . ' (ms)',
				'type' => Controls_Manager::NUMBER,
				'default' => 500,
			]
		);

		$this->end_controls_section();
	}

	private function register_content()
	{
		$this->start_controls_section('slides_section', [
			'label' => esc_html__('Slides', 'felan'),
		]);

		$repeater = new Repeater();

		$this->add_repeater_controls($repeater);

		$this->add_control('slides', [
			'label'     => esc_html__('Slides', 'felan'),
			'type'      => Controls_Manager::REPEATER,
			'fields'    => $repeater->get_controls(),
			'default'   => $this->get_repeater_defaults(),
			'separator' => 'after',
		]);

		$this->add_group_control(Group_Control_Image_Size::get_type(), [
			'name'    => 'image_size',
			'default' => 'full',
		]);

		$this->end_controls_section();
	}

	private function add_box_style_section()
	{
		$this->start_controls_section('box_style_section', [
			'label' => esc_html__('Box', 'felan'),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_responsive_control('box_alignment', [
			'label'     => esc_html__('Alignment', 'felan'),
			'type'      => Controls_Manager::CHOOSE,
			'options'   => Widget_Utils::get_control_options_text_align(),
			'selectors' => [
				'{{WRAPPER}} .content-wrap' => 'text-align: {{VALUE}}',
			],
		]);

		$this->add_responsive_control('box_max_width', [
			'label'      => esc_html__('Max Width', 'felan'),
			'type'       => Controls_Manager::SLIDER,
			'default'    => [
				'unit' => 'px',
			],
			'size_units' => ['px', '%'],
			'range'      => [
				'%'  => [
					'min' => 1,
					'max' => 100,
				],
				'px' => [
					'min' => 1,
					'max' => 1600,
				],
			],
			'selectors'  => [
				'{{WRAPPER}} .testimonial-item' => 'width: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_group_control(Group_Control_Background::get_type(), [
			'name'     => 'box',
			'selector' => '{{WRAPPER}} .testimonial-item',
		]);

		$this->add_responsive_control('box_padding', [
			'label'      => esc_html__('Padding', 'felan'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors'  => [
				'{{WRAPPER}} .testimonial-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_control(
			'border_style',
			[
				'label' => __('Style', 'felan'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' => __('None', 'felan'),
					'solid' => __('Solid', 'felan'),
					'double' => __('Double', 'felan'),
					'dotted' => __('Dotted', 'felan'),
					'dashed' => __('Dashed', 'felan'),
				],
				'default' => 'none',
				'selectors' => [
					'{{WRAPPER}} .testimonial-item' => 'border-style: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'border_width',
			[
				'label' => __('Border Width', 'felan'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .testimonial-item' => 'border-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'border_color',
			[
				'label' => __('Border Color', 'felan'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .testimonial-item' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'toggle_border_radius',
			[
				'label' => __('Border Radius', 'felan'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .testimonial-item' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	private function add_content_style_section()
	{
		$this->start_controls_section('content_style_section', [
			'label' => esc_html__('Content', 'felan'),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_responsive_control('content_max_width', [
			'label'      => esc_html__('Max Width', 'felan'),
			'type'       => Controls_Manager::SLIDER,
			'default'    => [
				'unit' => 'px',
			],
			'size_units' => ['px', '%'],
			'range'      => [
				'%'  => [
					'min' => 1,
					'max' => 100,
				],
				'px' => [
					'min' => 1,
					'max' => 1600,
				],
			],
			'selectors'  => [
				'{{WRAPPER}} .content-wrap' => 'width: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('content_alignment', [
			'label'                => esc_html__('Alignment', 'felan'),
			'type'                 => Controls_Manager::CHOOSE,
			'options'              => Widget_Utils::get_control_options_horizontal_alignment(),
			'selectors_dictionary' => [
				'left'  => 'flex-start',
				'right' => 'flex-end',
			],
			'selectors'            => [
				'{{WRAPPER}} .testimonial-main-content' => 'justify-content: {{VALUE}}',
			],
		]);

		$this->add_control('content_text_align', [
			'label'        => esc_html__('Text Align', 'felan'),
			'label_block'  => false,
			'type'         => Controls_Manager::CHOOSE,
			'default'      => 'center',
			'options'      => Widget_Utils::get_control_options_text_align(),
			'prefix_class' => 'align-',
			//'render_type'  => 'template',
			'selectors'    => [
				'{{WRAPPER}} .content-wrap' => 'text-align: {{VALUE}};',
			],
		]);

		$this->add_control('title_heading', [
			'label'     => esc_html__('Title', 'felan'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		]);

		$this->add_control('title_color', [
			'label'     => esc_html__('Color', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .title' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'title_typography',
			'label'    => esc_html__('Typography', 'felan'),
			'selector' => '{{WRAPPER}} .title',
		]);

		$this->add_responsive_control('title_margin', [
			'label'      => esc_html__('Margin', 'felan'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors'  => [
				'{{WRAPPER}} .title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_control('text_heading', [
			'label'     => esc_html__('Text', 'felan'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		]);

		$this->add_control('text_color', [
			'label'     => esc_html__('Color', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .text' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'text_typography',
			'label'    => esc_html__('Typography', 'felan'),
			'selector' => '{{WRAPPER}} .text',
		]);

		$this->add_control('name_heading', [
			'label'     => esc_html__('Name', 'felan'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		]);

		$this->add_control('name_color', [
			'label'     => esc_html__('Color', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .name' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'name_typography',
			'label'    => esc_html__('Typography', 'felan'),
			'selector' => '{{WRAPPER}} .name',
		]);

		$this->add_control('position_heading', [
			'label'     => esc_html__('Position', 'felan'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		]);

		$this->add_control('position_color', [
			'label'     => esc_html__('Color', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .position' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'position_typography',
			'label'    => esc_html__('Typography', 'felan'),
			'selector' => '{{WRAPPER}} .position',
		]);

		$this->end_controls_section();
	}

	private function add_image_style_section()
	{
		$this->start_controls_section('image_style_section', [
			'label' => esc_html__('Image', 'felan'),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_responsive_control('image_spacing', [
			'label'     => esc_html__('Spacing', 'felan'),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'min' => 0,
					'max' => 500,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .info' => 'padding-top: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}}.layout-image-bottom .image' => 'padding-top: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control(
			'image_size',
			[
				'label'     => esc_html__('Size', 'felan'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 30,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .image img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_repeater_controls($repeater)
	{

		$repeater->add_control('title', [
			'label'       => esc_html__('Title', 'felan'),
			'label_block' => true,
			'type'        => Controls_Manager::TEXT,
		]);

		$repeater->add_control('content', [
			'label' => esc_html__('Content', 'felan'),
			'type'  => Controls_Manager::TEXTAREA,
		]);

		$repeater->add_control('image', [
			'label' => esc_html__('Avatar', 'felan'),
			'type'  => Controls_Manager::MEDIA,
		]);

		$repeater->add_control('image_logo', [
			'label' => esc_html__('Logo', 'felan'),
			'type'  => Controls_Manager::MEDIA,
		]);

		$repeater->add_control('name', [
			'label'   => esc_html__('Name', 'felan'),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__('John Doe', 'felan'),
		]);

		$repeater->add_control('position', [
			'label'   => esc_html__('Position', 'felan'),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__('CEO', 'felan'),
		]);

		$repeater->add_control('rating', [
			'label' => esc_html__('Rating', 'felan'),
			'type'  => Controls_Manager::NUMBER,
			'min'   => 0,
			'max'   => 5,
			'step'  => 0.1,
		]);
	}

	protected function get_repeater_defaults()
	{
		$placeholder_image_src = Utils::get_placeholder_image_src();

		return [
			[
				'content'  => esc_html__('I am slide content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'felan'),
				'name'     => esc_html__('John Doe', 'felan'),
				'position' => esc_html__('Web Design', 'felan'),
				'image'    => ['url' => $placeholder_image_src],
			],
			[
				'content'  => esc_html__('I am slide content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'felan'),
				'name'     => esc_html__('John Doe', 'felan'),
				'position' => esc_html__('Web Design', 'felan'),
				'image'    => ['url' => $placeholder_image_src],
			],
			[
				'content'  => esc_html__('I am slide content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'felan'),
				'name'     => esc_html__('John Doe', 'felan'),
				'position' => esc_html__('Web Design', 'felan'),
				'image'    => ['url' => $placeholder_image_src],
			],
		];
	}

	private function get_testimonial_rating_template($rating = 5)
	{
		$full_stars = intval($rating);
		$template   = '';

		$template .= str_repeat('<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
<rect width="20" height="20" fill="#219653"/>
<path d="M10 13.6761L13.0417 12.8616L14.3125 17L10 13.6761ZM17 8.32704H11.6458L10 3L8.35417 8.32704H3L7.33334 11.6289L5.6875 16.956L10.0208 13.6541L12.6875 11.6289L17 8.32704Z" fill="white"/>
</svg>', $full_stars);

		$half_star = floatval($rating) - $full_stars;

		if ($half_star != 0) {
			$template .= '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
<rect width="20" height="20" fill="url(#paint0_linear_1949_24854)"/>
<path d="M10 13.6761L13.0417 12.8616L14.3125 17L10 13.6761ZM17 8.32704H11.6458L10 3L8.35417 8.32704H3L7.33334 11.6289L5.6875 16.956L10.0208 13.6541L12.6875 11.6289L17 8.32704Z" fill="white"/>
<defs>
<linearGradient x1="0" y1="10" x2="20" y2="10" gradientUnits="userSpaceOnUse">
<stop offset="0" stop-color="#219653"/>
<stop offset="0.5" stop-color="#219653"/>
<stop offset="0.5001" stop-color="#219653" stop-opacity="0"/>
<stop offset="0.5002" stop-color="#D9D9D9"/>
<stop offset="0.9998" stop-color="#D9D9D9"/>
<stop offset="0.9999" stop-color="#219653" stop-opacity="0"/>
<stop offset="1" stop-color="#D9D9D9"/>
</linearGradient>
</defs>
</svg>';
		}

		$empty_stars = intval(5 - $rating);
		$template    .= str_repeat('<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
<rect width="20" height="20" fill="#D9D9D9"/>
<path d="M10 13.6761L13.0417 12.8616L14.3125 17L10 13.6761ZM17 8.32704H11.6458L10 3L8.35417 8.32704H3L7.33334 11.6289L5.6875 16.956L10.0208 13.6541L12.6875 11.6289L17 8.32704Z" fill="white"/>
</svg>', $empty_stars);

		return '<div class="testimonial-rating">' . $template . '</div>';
	}

	private function print_testimonial_cite()
	{
		$slide = $this->get_current_slide();
		$settings = $this->get_settings_for_display();

		if (empty($slide['name']) && empty($slide['position'])) {
			return;
		}

		$html = '<div class="cite">';

		if (!empty($slide['rating']) && 'image-inline' !== $settings['layout']) :
			$html .= $this->get_testimonial_rating_template($slide['rating']);
		endif;

		if (!empty($slide['name'])) {
			$html .= '<h4 class="name">' . $slide['name'] . '</h4>';
		}
		if (!empty($slide['position'])) {
			$html .= '<span class="position">' . $slide['position'] . '</span>';
		}
		$html .= '</div>';

		echo '' . $html;
	}

	private function print_testimonial_avatar()
	{
		$slide = $this->get_current_slide();

		if (empty($slide['image']['url'])) {
			return;
		}
?>
		<div class="image">
			<?php echo \Felan_Image::get_elementor_attachment([
				'settings'       => $slide,
				'image_size_key' => 'image_size',
			]); ?>
		</div>
	<?php
	}

	private function print_testimonial_info()
	{

		$settings = $this->get_settings_for_display();
		$slide = $this->get_current_slide();
	?>
		<div class="info">

			<?php
			if ($settings['style'] == '03') {
				if (!empty($slide['image_logo']['url'])) {
					$logo = $slide['image_logo']['url'];
				} else {
					$logo =  FELAN_THEME_URI . '/assets/images/testimonial-icon.svg';
				} ?>
				<div class="testimonial-logo">
					<img src="<?php echo esc_url($logo); ?>" alt="" />
				</div>
			<?php } ?>

			<?php if (!in_array($settings['layout'], ['image-top', 'image-top-02', 'image-left'], true)) : ?>
				<?php $this->print_testimonial_avatar(); ?>
			<?php endif; ?>

			<?php $this->print_testimonial_cite(); ?>
		</div>
	<?php
	}

	private function print_testimonial_main_content()
	{
		$settings = $this->get_settings_for_display();
	?>

		<?php if ($settings['enable_quote'] === 'yes' && $settings['layout'] !== 'image-left') : ?>
			<span class="box-post"></span>
			<div class="quote">
				<svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
					<g>
						<path d="M0 18H11.25C11.25 24.939 6.939 29.25 0 29.25V33.75C9.423 33.75 15.75 27.423 15.75 18V2.25H0V18ZM20.25 2.25V18H31.5C31.5 24.939 27.189 29.25 20.25 29.25V33.75C29.673 33.75 36 27.423 36 18V2.25H20.25Z" fill="#1F72F2" />
					</g>
					<defs>
						<clipPath>
							<rect width="36" height="36" fill="white" />
						</clipPath>
					</defs>
				</svg>
			</div>
		<?php endif; ?>

		<?php if ($settings['style'] == '01' || $settings['style'] == '02') {
			if (!empty($slide['image_logo']['url'])) {
				$logo = $slide['image_logo']['url'];
			} else {
				$logo =  FELAN_THEME_URI . '/assets/images/testimonial-icon.png';
			} ?>
			<div class="testimonial-logo">
				<img src="<?php echo esc_url($logo); ?>" alt="" />
			</div>
		<?php } ?>

		<div class="testimonial-main-content">
			<div class="content-wrap">
				<?php if ($settings['enable_quote'] === 'yes' && $settings['layout'] == 'image-left') : ?>
					<div class="quote">
						<svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
							<g>
								<path d="M0 18H11.25C11.25 24.939 6.939 29.25 0 29.25V33.75C9.423 33.75 15.75 27.423 15.75 18V2.25H0V18ZM20.25 2.25V18H31.5C31.5 24.939 27.189 29.25 20.25 29.25V33.75C29.673 33.75 36 27.423 36 18V2.25H20.25Z" fill="#1F72F2" />
							</g>
							<defs>
								<clipPath>
									<rect width="36" height="36" fill="white" />
								</clipPath>
							</defs>
						</svg>
					</div>
				<?php endif; ?>

				<?php if ('image-above' === $settings['layout'] && $settings['style'] != "04") : ?>
					<?php $this->print_layout_image_above(); ?>
				<?php else : ?>
					<?php $this->print_layout(); ?>
				<?php endif; ?>
			</div>
		</div>
	<?php
	}

	protected function get_current_slide()
	{
		return $this->current_slide;
	}

	protected function get_current_key()
	{
		return $this->current_key;
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();
		$is_rtl = is_rtl();
		$direction = $is_rtl ? 'rtl' : 'ltr';
		$show_dots = (in_array($settings['navigation'], ['dots', 'both']));
		$show_arrows = (in_array($settings['navigation'], ['arrows', 'both']));

		if (empty($settings['slides_to_show_tablet'])) : $settings['slides_to_show_tablet'] = $settings['slides_to_show'];
		endif;
		if (empty($settings['slides_to_show_mobile'])) : $settings['slides_to_show_mobile'] = $settings['slides_to_show'];
		endif;
		if (empty($settings['slides_to_scroll_tablet'])) : $settings['slides_to_scroll_tablet'] = $settings['slides_to_scroll'];
		endif;
		if (empty($settings['slides_to_scroll_mobile'])) : $settings['slides_to_scroll_mobile'] = $settings['slides_to_scroll'];
		endif;
		if (empty($settings['center_padding_tablet'])) : $settings['center_padding_tablet'] = $settings['center_padding'];
		endif;
		if (empty($settings['center_padding_mobile'])) : $settings['center_padding_mobile'] = $settings['center_padding'];
		endif;

		$slick_options = [
			'"slidesToShow":' . absint($settings['slides_to_show']),
			'"slidesToScroll":' . absint($settings['slides_to_scroll']),
			'"autoplaySpeed":' . (isset($settings['autoplay_speed']) ? absint($settings['autoplay_speed']) : 3000),
			'"autoplay":' . (('yes' === $settings['autoplay']) ? 'true' : 'false'),
			'"infinite":' . (('yes' === $settings['infinite']) ? 'true' : 'false'),
			'"pauseOnHover":' . (('yes' === $settings['pause_on_hover']) ? 'true' : 'false'),
			'"centerMode":' . (('yes' === $settings['center_mode']) ? 'true' : 'false'),
			'"centerPadding":' . '"' . absint($settings['center_padding']) . 'px"',
			'"speed":' . absint($settings['transition_speed']),
			'"arrows":' . ($show_arrows ? 'true' : 'false'),
			'"dots":' . ($show_dots ? 'true' : 'false'),
			'"rtl":' . ($is_rtl ? 'true' : 'false'),
			'"responsive": [{ "breakpoint":567, "settings":{ "slidesToShow":' . $settings["slides_to_show_mobile"] . ', "slidesToScroll":' . $settings["slides_to_scroll_mobile"] . ', "centerPadding":"' . $settings["center_padding_mobile"] . 'px"}},{ "breakpoint":767, "settings":{ "slidesToShow": 2, "slidesToScroll": 2} }, { "breakpoint":1024, "settings":{ "slidesToShow":' . $settings["slides_to_show_tablet"] . ', "slidesToScroll":' . $settings["slides_to_scroll_tablet"] . ', "centerPadding":"' . $settings["center_padding_tablet"] . 'px"} } ]',
		];
		$slick_data = '{' . implode(', ', $slick_options) . '}';

		if ('fade' === $settings['transition']) {
			$slick_options['fade'] = true;
		}

		$carousel_classes = ['elementor-carousel'];
		$this->add_render_attribute('slides', [
			'class' => $carousel_classes,
			'data-slider_options' => $slick_data,
		]);
	?>
		<div class="elementor-slick-slider" dir="<?php echo esc_attr($direction); ?>">
			<div <?php $this->print_render_attribute_string('slides'); ?>>

				<?php foreach ($settings['slides'] as $slide) :
					$item_id = $slide['_id'];
					$item_key = 'item_' . $item_id;

					$this->current_key   = $item_key;
					$this->current_slide = $slide;

					$this->add_render_attribute($item_key, [
						'class' => [
							'swiper-slide',
							'elementor-repeater-item-' . $item_id,
						],
					]);
				?>
					<div class="testimonial-inner">
						<span class="box-post"></span>
						<?php if ($settings['enable_quote'] === 'yes' && $settings['layout'] !== 'image-left') : ?>
							<div class="quote">
								<svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
									<g>
										<path d="M0 18H11.25C11.25 24.939 6.939 29.25 0 29.25V33.75C9.423 33.75 15.75 27.423 15.75 18V2.25H0V18ZM20.25 2.25V18H31.5C31.5 24.939 27.189 29.25 20.25 29.25V33.75C29.673 33.75 36 27.423 36 18V2.25H20.25Z" fill="#1F72F2" />
									</g>
									<defs>
										<clipPath>
											<rect width="36" height="36" fill="white" />
										</clipPath>
									</defs>
								</svg>
							</div>
						<?php endif; ?>
						<?php $this->print_slide(); ?>
					</div>
				<?php endforeach; ?>
			</div>
			<?php if ($settings['scroll_bar'] === 'yes') : ?>
				<div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100">
					<span class="slider__label sr-only"></span>
				</div>
			<?php endif; ?>
		</div>
	<?php }

	protected function print_slide()
	{
		$settings = $this->get_settings_for_display();
		$item_key = $this->get_current_key();
		$this->add_render_attribute($item_key . '-testimonial', [
			'class' => 'testimonial-item',
		]);
	?>
		<div <?php $this->print_attributes_string($item_key . '-testimonial'); ?>>

			<?php if (in_array($settings['layout'], ['image-top', 'image-left'], true) && $settings['style'] != '04') : ?>
				<?php $this->print_testimonial_avatar(); ?>
			<?php endif; ?>

			<?php $this->print_testimonial_main_content(); ?>
		</div>
	<?php
	}

	private function print_layout_image_above()
	{
		$slide = $this->get_current_slide();
	?>
		<?php if ($slide['content']) : ?>
			<div class="content">
				<div class="text">
					<?php echo wp_kses($slide['content'], 'felan-default'); ?>
				</div>
			</div>
		<?php endif; ?>

		<?php $this->print_testimonial_cite(); ?>

	<?php
	}

	private function print_layout()
	{
		$slide    = $this->get_current_slide();
		$settings = $this->get_settings_for_display();
	?>
		<?php if ('image-top-02' === $settings['layout'] && $settings['style'] != '04') : ?>
			<?php $this->print_testimonial_avatar(); ?>
		<?php endif; ?>

		<?php if ('above' === $settings['image_position']) : ?>
			<?php $this->print_testimonial_info(); ?>
		<?php endif; ?>

		<?php if ($settings['layout'] == 'image-bottom' || $settings['style'] == '04') :
			$this->print_testimonial_cite();
		endif; ?>

		<?php if ($slide['content']) : ?>
			<div class="content">
				<?php if (!empty($slide['title'])) : ?>
					<h4 class="title"><?php echo esc_html($slide['title']); ?></h4>
				<?php endif; ?>

				<?php
				if (!empty($slide['rating']) && 'image-inline' === $settings['layout']) :
					echo wp_kses_post($this->get_testimonial_rating_template($slide['rating']));
				endif;
				?>

				<div class="text">
					<?php echo wp_kses($slide['content'], 'felan-default'); ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ($settings['layout'] == 'image-bottom') :
			$this->print_testimonial_avatar();
		endif; ?>

		<?php if (in_array($settings['image_position'], array(
			'below',
			'bottom',
		), true) || in_array($settings['layout'], array(
			'image-top',
			'image-top-02',
			'image-left',
		), true)) : ?>
			<?php $this->print_testimonial_info(); ?>
		<?php endif; ?>

	<?php
	}

	/**
	 * Print Avatar Thumbs Slider
	 */
	protected function after_slider()
	{
		$settings = $this->get_active_settings();

		if ('image-above' !== $settings['layout']) {
			return;
		}

		$this->add_render_attribute('_wrapper', 'class', 'felan-swiper-linked-yes');

		$testimonial_thumbs_template = '';

		foreach ($settings['slides'] as $slide) :
			if ($slide['image']['url']) :
				$testimonial_thumbs_template .= '<div class="swiper-slide"><div class="post-thumbnail"><div class="image">' . \Felan_Image::get_elementor_attachment([
					'settings'       => $slide,
					'image_size_key' => 'image_size',
				]) . '</div></div></div>';
			endif;
		endforeach;

	?>
		<div class="felan-swiper felan-slider-widget felan-testimonial-pagination style-01 felan-thumbs-swiper" data-lg-items="3" data-lg-gutter="30" data-slide-to-clicked-slide="1" data-centered="1" data-loop="1" data-looped-slides="<?php echo esc_attr($this->slider_looped_slides); ?>">
			<div class="swiper-inner">
				<div class="swiper-container">
					<div class="swiper-wrapper">
						<?php echo '' . $testimonial_thumbs_template; ?>
					</div>
				</div>
			</div>
		</div>
<?php
	}
}
