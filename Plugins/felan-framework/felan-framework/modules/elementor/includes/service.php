<?php

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Plugin;

defined('ABSPATH') || exit;

Plugin::instance()->widgets_manager->register(new Widget_Service());

class Widget_Service extends Widget_Base
{

	public function get_post_type()
	{
		return 'service';
	}

	public function get_name()
	{
		return 'felan-service';
	}

	public function get_title()
	{
		return esc_html__('Service', 'felan-framework');
	}

	public function get_icon()
	{
		return 'felan-badge eicon-cogs';
	}

	public function get_keywords()
	{
		return ['service', 'carousel'];
	}

	public function get_script_depends()
	{
		return [FELAN_PLUGIN_PREFIX . 'el-service'];
	}

	public function get_style_depends()
	{
		return [FELAN_PLUGIN_PREFIX . 'service'];
	}

	protected function register_controls()
	{
		$this->register_layout_section();
		$this->register_query_section();
		$this->register_slider_section();
		$this->register_tab_style_section();
		$this->register_layout_style_section();
	}

	private function register_layout_section()
	{
		$this->start_controls_section('layout_section', [
			'label' => esc_html__('Layout', 'felan-framework'),
			'tab' => Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('layout', [
			'label' => esc_html__('Layout', 'felan-framework'),
			'type' => Controls_Manager::SELECT,
			'options' => [
				'layout-list' => esc_html__('Layout List', 'felan-framework'),
				'layout-grid' => esc_html__('Layout Grid', 'felan-framework'),
				'layout-modern' => esc_html__('Layout Modern', 'felan-framework'),
			],
			'default' => 'layout-grid',
		]);

		$this->add_control(
			'image_size',
			[
				'label' => esc_html__('Image Size', 'felan-framework'),
				'type' => Controls_Manager::TEXT,
				'default' => '600x400',
				'label_block' => true,
				'placeholder' => esc_html__('Example: 200x100 (Width x Height).', 'felan-framework'),
				'pattern' => '/\d+x\d+/',
			]
		);

		$this->add_control(
			'enable_tab',
			[
				'label' => esc_html__('Enable Tab', 'felan-framework'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'title',
			[
				'label'       => esc_html__('Title', 'felan-framework'),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__('Enter title tab', 'felan-framework'),
			]
		);

		$repeater->add_control(
			'type',
			[
				'label' => esc_html__('Type', 'felan-framework'),
				'type' => Controls_Manager::SELECT,
				'default' => 'featured',
				'options' => array(
					'featured' => esc_html__('Featured', 'felan-framework'),
					'newest' => esc_html__('Newest', 'felan-framework'),
					'top_rate' => esc_html__('Top rate', 'felan-framework'),
				),
				'label_block' => true,
			]
		);

		$this->add_control(
			'service_list',
			[
				'label' => '',
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'title' => esc_html__('Featured', 'felan-framework'),
						'type' => 'featured',
					],
					[
						'title' => esc_html__('Newest', 'felan-framework'),
						'type' => 'newest',
					],
					[
						'title' => esc_html__('Top rate', 'felan-framework'),
						'type' => 'top_rate',
					],
				],
				'condition' => [
					'enable_tab' => 'yes',
				],
			]
		);

		$this->add_control(
			'enable_slider',
			[
				'label' => esc_html__('Enable Slider', 'felan-framework'),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label' => esc_html__('Columns', 'felan-framework'),
				'type' => Controls_Manager::NUMBER,
				'prefix_class' => 'elementor-grid%s-',
				'min' => 1,
				'max' => 4,
				'default' => 2,
				'required' => true,
				'device_args' => [
					Controls_Stack::RESPONSIVE_TABLET => [
						'required' => false,
					],
					Controls_Stack::RESPONSIVE_MOBILE => [
						'required' => false,
					],
				],
				'min_affected_device' => [
					Controls_Stack::RESPONSIVE_DESKTOP => Controls_Stack::RESPONSIVE_TABLET,
					Controls_Stack::RESPONSIVE_TABLET => Controls_Stack::RESPONSIVE_TABLET,
				],
				'condition' => [
					'enable_slider!' => 'yes',
				],
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label' => esc_html__('Posts Per Page', 'felan-framework'),
				'type' => Controls_Manager::NUMBER,
				'default' => 6,
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label' => __('Columns Gap', 'felan-framework'),
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
					'{{WRAPPER}} .elementor-carousel .service-item-inner' => 'padding-left: calc({{SIZE}}{{UNIT}}/2); padding-right: calc({{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .slick-list' => 'margin-left: calc(-{{SIZE}}{{UNIT}}/2);margin-right: calc(-{{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .elementor-grid' => 'grid-column-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label' => esc_html__('Rows Gap', 'felan-framework'),
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
				'frontend_available' => true,
				'selectors' => [
					'{{WRAPPER}} .elementor-carousel .service-item-inner' => 'padding-top: calc({{SIZE}}{{UNIT}}/2); padding-bottom: calc({{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .slick-list' => 'margin-top: calc(-{{SIZE}}{{UNIT}}/2);margin-bottom: calc(-{{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .elementor-grid' => 'grid-row-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_query_section()
	{
		$this->start_controls_section('query_section', [
			'label' => esc_html__('Query', 'felan-framework'),
			'tab' => Controls_Manager::TAB_CONTENT,
			'condition' => [
				'enable_tab!' => 'yes',
			],
		]);

		$this->add_control(
			'type_query',
			[
				'label' => esc_html__('Filter', 'felan-framework'),
				'type' => Controls_Manager::SELECT,
				'default' => 'orderby',
				'options' => [
					'title' => esc_html__('Title', 'felan-framework'),
					'orderby' => esc_html__('Orderby', 'felan-framework'),
					'taxonomy' => esc_html__('Taxonomy', 'felan-framework'),
				],
				'condition' => [
					'enable_tab!' => 'yes',
				],
			]
		);

		$taxonomies = array(
			"Categories" => "service-categories",
			"Skills" => "service-skills",
			"Location" => "felan_location",
			"Language" => "service-language",
		);

		foreach ($taxonomies as $label_taxonomy => $taxonomy) {
			$categories = get_terms([
				'taxonomy' => $taxonomy,
				'hide_empty' => true,
			]);

			$options = array();
			foreach ($categories as $category) {
				if (!empty($category) && $category->slug != 'uncategorized') {
					$options[$category->term_id] = $category->name;
				}
			}

			$this->add_control($taxonomy, [
				'label' => esc_html__($label_taxonomy, 'felan-framework'),
				'type' => Controls_Manager::SELECT2,
				'options' => $options,
				'default' => [],
				'label_block' => true,
				'multiple' => true,
				'condition' => [
					'type_query' => 'taxonomy',
					'enable_tab!' => 'yes',
				],
			]);
		}

		$this->add_control(
			'orderby',
			[
				'label' => esc_html__('Order By', 'felan-framework'),
				'type' => Controls_Manager::SELECT,
				'default' => 'newest',
				'options' => [
					'featured' => esc_html__('Featured', 'felan-framework'),
					'oldest' => esc_html__('Oldest', 'felan-framework'),
					'newest' => esc_html__('Newest', 'felan-framework'),
					'random' => esc_html__('Random', 'felan-framework'),
				],
				'condition' => [
					'type_query' => 'orderby',
					'enable_tab!' => 'yes',
				],
			]
		);

		$options_service = [];
		$args_service = array(
			'post_type' => $this->get_post_type(),
			'ignore_sticky_posts' => 1,
			'post_status' => 'publish',
		);

		$data_service = new \WP_Query($args_service);
		if ($data_service->have_posts()) {
			while ($data_service->have_posts()) : $data_service->the_post();
				$id = get_the_id();
				$title = get_the_title($id);
				$options_service[$id] = $title;
			endwhile;
		}
		wp_reset_postdata();

		$this->add_control('include_ids', [
			'label'       => esc_html__('Search & Select', 'felan-framework'),
			'type'        => Controls_Manager::SELECT2,
			'options'     => $options_service,
			'default'     => [],
			'label_block' => true,
			'multiple'    => true,
			'condition' => [
				'type_query' => 'title',
				'enable_tab!' => 'yes',
			],
		]);

		$this->end_controls_section();
	}

	private function register_slider_section()
	{
		$this->start_controls_section('slider_section', [
			'label' => esc_html__('Slider', 'felan-framework'),
			'tab' => Controls_Manager::TAB_CONTENT,
			'condition' => [
				'enable_slider' => 'yes',
			],
		]);

		$slides_to_show = range(1, 10);
		$slides_to_show = array_combine($slides_to_show, $slides_to_show);

		$this->add_responsive_control(
			'slides_to_show',
			[
				'label' => esc_html__('Slides to Show', 'felan-framework'),
				'type' => Controls_Manager::SELECT,
				'default' => '2',
				'options' => [
					'' => esc_html__('Default', 'felan-framework'),
				] + $slides_to_show,
			]
		);

		$this->add_responsive_control(
			'slides_to_scroll',
			[
				'label' => esc_html__('Slides to Scroll', 'felan-framework'),
				'type' => Controls_Manager::SELECT,
				'description' => esc_html__('Set how many slides are scrolled per swipe.', 'felan-framework'),
				'default' => '1',
				'options' => [
					'' => esc_html__('Default', 'felan-framework'),
				] + $slides_to_show,
			]
		);

		$this->add_control(
			'slides_number_row',
			[
				'label' => esc_html__('Number Row', 'felan-framework'),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 4,
				'default' => 1,
			]
		);

		$this->add_control(
			'navigation',
			[
				'label' => esc_html__('Navigation', 'felan-framework'),
				'type' => Controls_Manager::SELECT,
				'default' => 'both',
				'options' => [
					'both' => esc_html__('Arrows and Dots', 'felan-framework'),
					'arrows' => esc_html__('Arrows', 'felan-framework'),
					'dots' => esc_html__('Dots', 'felan-framework'),
					'none' => esc_html__('None', 'felan-framework'),
				],
			]
		);

		$this->add_control(
			'arrows_top',
			[
				'label' => esc_html__('Arrows Top', 'felan-framework'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'condition' => [
					'navigation' => array('arrows', 'both'),
				]
			]
		);

		$this->add_control(
			'arrows_top_transform_y',
			[
				'label' => esc_html__('Arrows Top Transform Y', 'felan-framework'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => -28,
				],
				'condition' => [
					'arrows_top' => 'yes',
					'navigation' => array('arrows', 'both'),
				],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .slick-arrow' => 'top: {{SIZE}}{{UNIT}};',
				],
			],
		);

		$this->add_control(
			'center_mode',
			[
				'label' => esc_html__('Center Mode', 'felan-framework'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);

		$this->add_control(
			'pause_on_hover',
			[
				'label' => esc_html__('Pause on Hover', 'felan-framework'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label' => esc_html__('Autoplay', 'felan-framework'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label' => esc_html__('Autoplay Speed', 'felan-framework'),
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
				'label' => esc_html__('Infinite Loop', 'felan-framework'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'transition',
			[
				'label' => esc_html__('Transition', 'felan-framework'),
				'type' => Controls_Manager::SELECT,
				'default' => 'slide',
				'options' => [
					'slide' => esc_html__('Slide', 'felan-framework'),
					'fade' => esc_html__('Fade', 'felan-framework'),
				],
			]
		);

		$this->add_control(
			'transition_speed',
			[
				'label' => esc_html__('Transition Speed', 'felan-framework') . ' (ms)',
				'type' => Controls_Manager::NUMBER,
				'default' => 500,
			]
		);

		$this->end_controls_section();
	}

	private function register_tab_style_section()
	{
		$this->start_controls_section(
			'section_tab_style',
			[
				'label' => esc_html__('Tab', 'felan-framework'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'type',
			[
				'label' => esc_html__('Type', 'felan-framework'),
				'type' => Controls_Manager::SELECT,
				'default' => 'fullfield',
				'options' => [
					'fullfield' => esc_html__('Fullfield', 'felan-framework'),
					'underline' => esc_html__('Underline', 'felan-framework'),
				],
			]
		);

		$this->add_responsive_control('nav_h_align', [
			'label' => esc_html__('Alignment horizontal', 'felan-framework'),
			'type' => Controls_Manager::CHOOSE,
			'options' => array(
				'flex-start' => [
					'title' => esc_html__('Left', 'felan-framework'),
					'icon' => 'eicon-h-align-left',
				],
				'center' => [
					'title' => esc_html__('Center', 'felan-framework'),
					'icon' => ' eicon-h-align-center',
				],
				'flex-end' => [
					'title' => esc_html__('Right', 'felan-framework'),
					'icon' => 'eicon-h-align-right',
				],
			),
			'default' => 'center',
			'selectors' => [
				'{{WRAPPER}} .service-nav' => 'justify-content: {{VALUE}};',
			],
		]);

		$this->add_responsive_control('nav_space', [
			'label'     => esc_html__('Spacing', 'felan-framework'),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .service-nav'       => 'margin: 0 0 {{SIZE}}{{UNIT}} 0;',
			],
		]);

		$this->end_controls_section();
	}

	private function register_layout_style_section()
	{
		$this->start_controls_section(
			'section_layout_style',
			[
				'label' => esc_html__('Layout', 'felan-framework'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'box_background',
				'label' => esc_html__('Background', 'felan-framework'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .felan-service-item',
			]
		);

		$this->add_control('box_padding', [
			'label' => esc_html__('Padding', 'felan-framework'),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%'],
			'selectors' => [
				'{{WRAPPER}} .felan-service-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_control('layout_border_radius', [
			'label' => esc_html__('Border Radius', 'felan-framework'),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%'],
			'selectors' => [
				'{{WRAPPER}} .felan-service-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'layout_border',
				'selector' => '{{WRAPPER}} .felan-service-item',
			]
		);

		$this->end_controls_section();
	}

	protected function render()
	{
		$is_rtl = is_rtl();
		$direction = $is_rtl ? 'rtl' : 'ltr';
		// $enable_rtl_mode = felan_get_option('enable_rtl_mode', 0);
		// $is_rtl          = is_rtl();
		// $direction       = $is_rtl || $enable_rtl_mode ? 'rtl' : 'ltr';

		$settings = $this->get_settings_for_display();
		$this->add_render_attribute('wrapper', 'class', 'felan-service');
		if ($settings['arrows_top'] == 'yes') {
			$this->add_render_attribute('wrapper', 'class', 'arrows-top');
		}
		$args = array(
			'posts_per_page' => $settings['posts_per_page'],
			'post_type' => 'service',
			'ignore_sticky_posts' => 1,
			'post_status' => 'publish',
		);

		//Query
		$tax_query = array();
		$meta_query = array();

		if (!empty($settings['include_ids']) && $settings['type_query'] == 'title') {
			$args['post__in'] = $settings['include_ids'];
		}

		if ($settings['type_query'] == 'orderby') {
			if (!empty($settings['orderby'])) {
				if ($settings['orderby'] == 'featured') {
					$meta_query[] = array(
						'key' => FELAN_METABOX_PREFIX . 'service_featured',
						'value' => 1,
						'type' => 'NUMERIC',
						'compare' => '=',
					);
				}
				if ($settings['orderby'] == 'oldest') {
					$args['orderby'] = array(
						'menu_order' => 'DESC',
						'date' => 'ASC',
					);
				}
				if ($settings['orderby'] == 'newest') {
					$args['orderby'] = array(
						'menu_order' => 'ASC',
						'date' => 'DESC',
					);
				}
				if ($settings['orderby'] == 'random') {
					$args['meta_key'] = '';
					$args['orderby'] = 'rand';
					$args['order'] = 'ASC';
				}
			}
		}

		$filters = array();
		if ($settings['type_query'] == 'taxonomy') {
			$taxonomies = array("service-categories", "service-skills", "felan_location", "service-language");
			foreach ($taxonomies as $taxonomy) {
				if (!empty($settings[$taxonomy])) {
					$tax_query[] = array(
						'taxonomy' => $taxonomy,
						'field' => 'term_id',
						'terms' => $settings[$taxonomy],
					);
					$filters[$taxonomy] = $settings[$taxonomy];
				}
			}
		}

		if (!empty($tax_query)) {
			$args['tax_query'] = array(
				'relation' => 'AND',
				$tax_query
			);
		}

		if (!empty($meta_query)) {
			$args['meta_query'] = array(
				'relation' => 'AND',
				$meta_query
			);
		}

		//Slider
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

		$slick_options = [
			'"slidesToShow":' . absint($settings['slides_to_show']),
			'"slidesToScroll":' . absint($settings['slides_to_scroll']),
			'"autoplaySpeed":' . (isset($settings['autoplay_speed']) ? absint($settings['autoplay_speed']) : 3000),
			'"autoplay":' . (('yes' === $settings['autoplay']) ? 'true' : 'false'),
			'"infinite":' . (('yes' === $settings['infinite']) ? 'true' : 'false'),
			'"pauseOnHover":' . (('yes' === $settings['pause_on_hover']) ? 'true' : 'false'),
			'"centerMode":' . (('yes' === $settings['center_mode']) ? 'true' : 'false'),
			'"speed":' . absint($settings['transition_speed']),
			'"arrows":' . ($show_arrows ? 'true' : 'false'),
			'"dots":' . ($show_dots ? 'true' : 'false'),
			'"rtl":' . ($is_rtl ? 'true' : 'false'),
			'"rows":' . absint($settings['slides_number_row']),
			'"responsive": [{ "breakpoint":567, "settings":{ "slidesToShow":' . $settings["slides_to_show_mobile"] . ', "slidesToScroll":' . $settings["slides_to_scroll_mobile"] . '}},{ "breakpoint":767, "settings":{ "slidesToShow": 2, "slidesToScroll": 2} }, { "breakpoint":1024, "settings":{ "slidesToShow":' . $settings["slides_to_show_tablet"] . ', "slidesToScroll":' . $settings["slides_to_scroll_tablet"] . ' } } ]',
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
		<div <?php echo $this->get_render_attribute_string('wrapper') ?>>
			<?php
			if ($settings['enable_tab'] && $settings['service_list']) {
			?>
				<div class="service-tabs">
					<ul class="service-nav <?php echo $settings['type']; ?>">
						<?php
						$i = 0;
						foreach ($settings['service_list'] as $service) {
							$i++;
						?>
							<li><a href="#st_<?php echo esc_attr__($service['type']); ?>" class="<?php if ($i == 1) {
																										echo 'active';
																									} ?>"><?php echo esc_html__($service['title']); ?></a></li>
						<?php } ?>
					</ul>
					<?php
					$j = 0;
					foreach ($settings['service_list'] as $service) {
						$j++;
						if ($service['type'] == 'featured') {
							$meta_query[] = array(
								'key' => FELAN_METABOX_PREFIX . 'service_featured',
								'value' => 1,
								'type' => 'NUMERIC',
								'compare' => '=',
							);
							$args['meta_query'] = array(
								'relation' => 'AND',
								$meta_query
							);
						} elseif ($service['type'] == 'newest') {
							$args['orderby'] = array(
								'menu_order' => 'ASC',
								'date' => 'DESC',
							);
							$args['meta_query'] = array(
								'relation' => 'AND',
							);
						} elseif ($service['type'] == 'top_rate') {
							$args['meta_query'] = array(
								'relation' => 'AND',
							);
							$args['meta_key'] = 'total_point_review';
							$args['orderby'] = 'meta_value_num';
							$args['order'] = 'DESC';
						}
						$data = new \WP_Query($args);
						$total_post = $data->found_posts;
					?>
						<div id="st_<?php echo esc_attr__($service['type']); ?>" class="service-tab-content <?php if ($j == 1) {
																												echo 'active';
																											} ?>">
							<?php if ($data->have_posts()) { ?>
								<?php if ($settings['enable_slider'] == 'yes') { ?>
									<div class="elementor-slick-slider" dir="<?php echo esc_attr($direction); ?>">
										<div <?php echo $this->get_render_attribute_string('slides'); ?>>
											<?php while ($data->have_posts()) : $data->the_post(); ?>
												<div class="service-item-inner">
													<?php felan_get_template('content-service.php', array(
														'service_layout' => 'layout-grid',
														'custom_service_image_size' => $settings['image_size'],
													)); ?>
												</div>
											<?php endwhile; ?>
										</div>
									</div>
								<?php } else { ?>
									<div class="elementor-grid-jobs">
										<div class="elementor-grid">
											<?php while ($data->have_posts()) : $data->the_post(); ?>
												<?php felan_get_template('content-service.php', array(
													'service_layout' => 'layout-grid',
													'custom_service_image_size' => $settings['image_size'],
												)); ?>
											<?php endwhile; ?>
										</div>
									</div>
								<?php } ?>
							<?php } else { ?>
								<div class="item-not-found"><?php esc_html_e('No item found', 'felan-framework'); ?></div>
							<?php } ?>
						</div>
					<?php } ?>
				</div>
			<?php
			} else {
				$data = new \WP_Query($args);
				$total_post = $data->found_posts;
			?>
				<?php if ($data->have_posts()) { ?>
					<?php if ($settings['enable_slider'] == 'yes') { ?>
						<div class="elementor-slick-slider" dir="<?php echo esc_attr($direction); ?>">
							<div <?php echo $this->get_render_attribute_string('slides'); ?>>
								<?php while ($data->have_posts()) : $data->the_post(); ?>
									<div class="service-item-inner">
										<?php felan_get_template('content-service.php', array(
											'service_layout' => $settings['layout'],
											'custom_service_image_size' => $settings['image_size'],
										)); ?>
									</div>
								<?php endwhile; ?>
							</div>
						</div>
					<?php } else { ?>
						<div class="elementor-grid-jobs">
							<div class="elementor-grid">
								<?php while ($data->have_posts()) : $data->the_post(); ?>
									<?php felan_get_template('content-service.php', array(
										'service_layout' => $settings['layout'],
										'custom_service_image_size' => $settings['image_size'],
									)); ?>
								<?php endwhile; ?>
							</div>
						</div>
					<?php } ?>
				<?php } else { ?>
					<div class="item-not-found"><?php esc_html_e('No item found', 'felan-framework'); ?></div>
				<?php } ?>
			<?php } ?>
			<input type="hidden" name="layout" value="layout-grid">
			<input type="hidden" name="item_amount" value="<?php echo $settings['posts_per_page'] ?>">
			<input type="hidden" name="include_ids" value='<?php echo json_encode($settings['include_ids']) ?>'>
			<input type="hidden" name="type_query" value="<?php echo $settings['type_query'] ?>">
			<input type="hidden" name="orderby" value="<?php echo $settings['orderby'] ?>">
		</div>
<?php }
}
