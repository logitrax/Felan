<?php

namespace Felan_Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;

defined('ABSPATH') || exit;

class Widget_Moderm_Tabs extends Base
{

	public function get_name()
	{
		return 'felan-modern-tabs';
	}

	public function get_title()
	{
		return esc_html__('Modern Tabs', 'felan');
	}

	public function get_icon_part()
	{
		return 'eicon-product-tabs';
	}

	public function get_keywords()
	{
		return ['tabs'];
	}

	public function get_script_depends()
	{
		return [
			'felan-widget-modern-tabs',
		];
	}

	public function get_style_depends()
	{
		return ['felan-el-widget-modern-tabs'];
	}

	protected function register_controls()
	{
		$this->add_layout_section();
		$this->add_tabs_style_section();
	}

	private function add_layout_section()
	{
		$this->start_controls_section('layout_section', [
			'label' => esc_html__('Layout', 'felan'),
		]);

		$this->add_responsive_control(
			'tabs_align',
			[
				'label' => esc_html__('Alignment', 'felan'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'' => [
						'title' => esc_html__('Start', 'felan'),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'felan'),
						'icon' => 'eicon-h-align-center',
					],
					'end' => [
						'title' => esc_html__('End', 'felan'),
						'icon' => 'eicon-h-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .nav-modern-tabs' => 'justify-content:{{VALUE}}; -webkit-box-pack:{{VALUE}};-ms-flex-pack:{{VALUE}};',
				],
			]
		);

		$tabs = new \Elementor\Repeater();

		$template = $this->felan_get_page_templates('mega-menu');

		$tabs->add_control('title', [
			'label'   => esc_html__('Title Tabs', 'felan'),
			'type'    => Controls_Manager::TEXT,
		]);

		$tabs->add_control(
			'template',
			[
				'label'   => esc_html__('Choose Template', 'felan'),
				'type'    => Controls_Manager::SELECT,
				'options' => $template,
			]
		);

		$this->add_control(
			'tabs',
			[
				'label' => esc_html__('Tabs', 'felan'),
				'type'      => Controls_Manager::REPEATER,
				'fields'    => $tabs->get_controls(),
				'default' => [
					[
						'title' => esc_html__('Tabs 01', 'felan'),
					],
					[
						'title' => esc_html__('Tabs 02', 'felan'),
					],
					[
						'title' => esc_html__('Tabs 03', 'felan'),
					],
				],
				'title_field' => '{{{ title }}}',
			]
		);

		$this->end_controls_section();
	}

	private function add_tabs_style_section()
	{
		$this->start_controls_section('tabs_style_section', [
			'label' => esc_html__('Tabs', 'felan'),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'tabs_typography',
			'label'    => esc_html__('Typography', 'felan'),
			'selector' => '{{WRAPPER}} .nav-modern-tabs .nav-item a',
		]);

		$this->add_responsive_control('tabs_spacing', [
			'label'          => esc_html__('Spacing', 'felan'),
			'type'           => Controls_Manager::SLIDER,
			'size_units'     => ['px', '%'],
			'range'          => [
				'%'  => [
					'min' => 1,
					'max' => 100,
				],
				'px' => [
					'min' => 1,
					'max' => 1000,
				],
			],
			'selectors'      => [
				'{{WRAPPER}} .nav-modern-tabs li + li' => 'margin-left: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('tabs_margin_bottom', [
			'label'          => esc_html__('Margin Bottom', 'felan'),
			'type'           => Controls_Manager::SLIDER,
			'size_units'     => ['px', '%'],
			'range'          => [
				'%'  => [
					'min' => 1,
					'max' => 100,
				],
				'px' => [
					'min' => 1,
					'max' => 1000,
				],
			],
			'selectors'      => [
				'{{WRAPPER}} .nav-modern-tabs' => 'margin-bottom: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_control('tabs_color', [
			'label'     => esc_html__('Color', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .nav-modern-tabs .nav-item a' => 'color: {{VALUE}};',
			],
		]);

		$this->add_control('tabs_hover_color', [
			'label'     => esc_html__('Hover Color', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .nav-modern-tabs .nav-item a:hover' => 'color: {{VALUE}};',
				'{{WRAPPER}} .nav-modern-tabs .nav-item.active a' => 'color: {{VALUE}};',
			],
		]);

		$this->end_controls_section();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();
		$this->add_render_attribute('wrapper', 'class', 'felan-modern-tabs');
		$id_int = $this->get_id();
?>
		<div <?php $this->print_render_attribute_string('wrapper') ?>>
			<ul class="nav-modern-tabs">
				<?php foreach ($settings['tabs'] as $i => $item) : ?>
					<li class="nav-item">
						<a href="#<?php echo esc_attr($id_int . $item['_id']) ?>"><?php echo esc_html($item['title']) ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
			<div class="content-modern-tabs">
				<?php foreach ($settings['tabs'] as $i => $item) : ?>
					<?php if (!empty($item['template'])) { ?>
						<div class="modern-tabs-item" id="<?php echo esc_attr($id_int . $item['_id']) ?>">
							<?php echo Plugin::$instance->frontend->get_builder_content($item['template'], true); ?>
						</div>
					<?php } ?>
				<?php endforeach; ?>
			</div>
		</div>
<?php }

	private function felan_get_page_templates($cate = null)
	{
		$args = [
			'post_type'      => 'elementor_library',
			'posts_per_page' => -1,
		];

		if ($cate) {
			$args['tax_query'] = [
				[
					'taxonomy' => 'elementor_library_category',
					'field'    => 'slug',
					'terms'    => $cate,
				],
			];
		}

		$page_templates = get_posts($args);
		$options        = array();

		if (!empty($page_templates) && !is_wp_error($page_templates)) {
			foreach ($page_templates as $post) {
				$options[$post->ID] = $post->post_title;
			}
		}

		return $options;
	}
}
