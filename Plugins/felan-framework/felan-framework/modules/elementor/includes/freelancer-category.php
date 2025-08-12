<?php

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Plugin;

defined('ABSPATH') || exit;

Plugin::instance()->widgets_manager->register(new Widget_Freelancer_Category());

class Widget_Freelancer_Category extends Widget_Base
{

	public function get_post_type()
	{
		return 'freelancer';
	}

	public function get_name()
	{
		return 'felan-freelancer-category';
	}

	public function get_title()
	{
		return esc_html__('Freelancer Category', 'felan-framework');
	}

	public function get_icon()
	{
		return 'felan-badge eicon-preferences';
	}

	public function get_keywords()
	{
		return ['freelancer', 'category'];
	}

	public function get_style_depends()
	{
		return [FELAN_PLUGIN_PREFIX . 'freelancer-category'];
	}

	protected function register_controls()
	{
		$this->register_layout_section();
		$this->register_layout_style_section();
		$this->register_title_style_section();
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
				'layout-01' => esc_html__('Layout 01', 'felan-framework'),
				'layout-02' => esc_html__('Layout 02', 'felan-framework'),
				'layout-03' => esc_html__('Layout 03', 'felan-framework'),
			],
			'default' => 'layout-01',
		]);

		$repeater = new Repeater();

		$taxonomy_terms = get_categories(
			array(
				'taxonomy' => 'freelancer_categories',
				'orderby' => 'name',
				'order' => 'ASC',
				'hide_empty' => true,
				'parent' => 0,
			)
		);

		$categories = [];
		foreach ($taxonomy_terms as $category) {
			$categories[$category->slug] = $category->name;
		}
		$repeater->add_control(
			'category',
			[
				'label' => esc_html__('Categories', 'felan-framework'),
				'type' => Controls_Manager::SELECT,
				'options' => $categories,
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'image',
			[
				'label' => esc_html__('Choose Image', 'felan-framework'),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$repeater->add_control(
			'selected_icon',
			[
				'label' => esc_html__('Icon', 'felan-framework'),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'fa-solid',
				],
			]
		);

		$repeater->add_control(
			'icon_item_color',
			[
				'label' => esc_html__('Icon Color', 'felan-framework'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .icon-cate i' => 'color: {{VALUE}};',
				],
			]
		);

		$repeater->add_control(
			'icon_item_bg_color',
			[
				'label' => esc_html__('Icon Background Color', 'felan-framework'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .icon-cate:before' => 'background-color: {{VALUE}};',
				],
			]
		);

		$repeater->add_control(
			'item_color',
			[
				'label' => esc_html__('Color', 'felan-framework'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .cate-inner .cate-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} {{CURRENT_ITEM}} .cate-inner .icon-arrow i' => 'color: {{VALUE}};',
				],
			]
		);

		$repeater->add_control(
			'item_bg_color',
			[
				'label' => esc_html__('Background Color', 'felan-framework'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .cate-inner' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'categories_list',
			[
				'label' => '',
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'text' => esc_html__('Category #1', 'felan-framework'),
					],
					[
						'text' => esc_html__('Category #2', 'felan-framework'),
					],
					[
						'text' => esc_html__('Category #3', 'felan-framework'),
					],
				],
			]
		);

		$this->add_control(
			'show_icon',
			[
				'label' => esc_html__('Show Icon', 'felan-framework'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_arrow',
			[
				'label' => esc_html__('Show Arrow', 'felan-framework'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition'	=> array(
					'layout' => 'layout-01'
				),
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label' => esc_html__('Columns', 'felan-framework'),
				'type' => Controls_Manager::NUMBER,
				'prefix_class' => 'elementor-grid%s-',
				'min' => 1,
				'max' => 8,
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
					'{{WRAPPER}} .elementor-carousel .list-cate-item' => 'padding-left: calc({{SIZE}}{{UNIT}}/2); padding-right: calc({{SIZE}}{{UNIT}}/2)',
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
					'{{WRAPPER}} .elementor-carousel .list-cate-item' => 'padding-top: calc({{SIZE}}{{UNIT}}/2); padding-bottom: calc({{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .slick-list' => 'margin-top: calc(-{{SIZE}}{{UNIT}}/2);margin-bottom: calc(-{{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .elementor-grid' => 'grid-row-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

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

		$this->add_responsive_control('box_padding', [
			'label' => esc_html__('Padding', 'felan-framework'),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%'],
			'selectors' => [
				'{{WRAPPER}} .cate-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('layout_border_radius', [
			'label' => esc_html__('Border Radius', 'felan-framework'),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%'],
			'selectors' => [
				'{{WRAPPER}} .felan-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				'{{WRAPPER}} .cate-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'layout_border',
				'selector' => '{{WRAPPER}} .cate-inner',
			]
		);

		$this->end_controls_section();
	}

	private function register_title_style_section()
	{
		$this->start_controls_section(
			'section_title_style',
			[
				'label' => esc_html__('Title', 'felan-framework'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_spacing',
			[
				'label' => esc_html__('Spacing', 'felan-framework'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .cate-content' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__('Typography', 'felan-framework'),
				'selector' => '{{WRAPPER}} .cate-title',
			]
		);

		$this->end_controls_section();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();
?>
		<div class="elementor-grid-freelancer <?php echo $settings['layout']; ?>">
			<div class="elementor-grid">
				<?php
				foreach ($settings['categories_list'] as $categorry) {
					$item_id = $categorry['_id'];
					$item_key = 'item_' . $item_id;
					$has_icon = !empty($categorry['icon']);
					if (!$has_icon && !empty($categorry['selected_icon']['value'])) {
						$has_icon = true;
					}
					$migrated = isset($categorry['__fa4_migrated']['selected_icon']);
					$is_new = !isset($categorry['icon']) && Icons_Manager::is_migration_allowed();
					$term_link = '';
					$category_slug = $categorry['category'];

					if (!empty($category_slug)) {
						$cate = get_term_by('slug', $category_slug, 'freelancer_categories');
						if ($cate) {
							$term_name = $cate->name;
							$term_count = $cate->count;
							$term_link = get_term_link($cate, 'freelancer_categories');
							$term_des = $cate->description;
						}
						$this->add_render_attribute($item_key, 'class', array(
							'list-cate-item',
							'elementor-repeater-item-' . $item_id,
						));
				?>
						<div <?php echo $this->get_render_attribute_string($item_key); ?>>
							<div class="cate-inner">
								<?php if ($has_icon && $settings['show_icon'] == 'yes') : ?>
									<span class="icon-cate">
										<?php
										if ($is_new || $migrated) {
											Icons_Manager::render_icon($categorry['selected_icon'], ['aria-hidden' => 'true']);
										} elseif (!empty($categorry['icon'])) {
										?>
											<i <?php echo $this->get_render_attribute_string('i'); ?>></i>
										<?php
										}
										?>
									</span>
								<?php endif; ?>
								<div class="cate-content">
									<?php if (!empty($term_name)) : ?>
										<h4 class="cate-title"><?php esc_html_e($term_name); ?></h4>
									<?php endif; ?>
									<?php if ($settings['show_arrow'] == 'yes') { ?>
										<div class="icon-arrow"><i class="far fa-arrow-right"></i></div>
									<?php } ?>
									<?php if (!empty($term_count) && $settings['layout'] === 'layout-03') { ?>
										<p class="cate-count"><?php echo sprintf(esc_html__('%s freelancers', 'felan-framework'), $term_count) ?></p>
									<?php } ?>
								</div>
								<?php if ($categorry['image']['url'] && $settings['layout'] === 'layout-03') { ?>
									<span class="image-cate">
										<a href="<?php echo esc_url($term_link) ?>" class="felan-image">
											<img src="<?php echo esc_url($categorry['image']['url']); ?>" alt="">
										</a>
									</span>
								<?php } ?>
							</div>
						</div>
				<?php }
				} ?>
			</div>
		</div>
<?php
	}
}
?>