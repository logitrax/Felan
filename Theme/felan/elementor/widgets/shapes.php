<?php

namespace Felan_Elementor;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

defined('ABSPATH') || exit;

class Widget_Shapes extends Base
{

	public function get_name()
	{
		return 'felan-shapes';
	}

	public function get_title()
	{
		return esc_html__('Modern Shapes', 'felan');
	}

	public function get_icon_part()
	{
		return 'eicon-favorite';
	}

	public function get_keywords()
	{
		return ['shapes'];
	}

	protected function register_controls()
	{
		$this->add_content_section();
	}

	public function get_style_depends()
	{
		return ['felan-el-widget-shapes'];
	}

	private function add_content_section()
	{
		$this->start_controls_section('content_section', [
			'label' => esc_html__('Shape', 'felan'),
		]);

		$this->add_control('type', [
			'label'        => esc_html__('Type', 'felan'),
			'type'         => Controls_Manager::SELECT,
			'options'      => [
				'circle'        => esc_html__('Circle', 'felan'),
				'border-circle' => esc_html__('Border Circle', 'felan'),
				'distortion'    => esc_html__('Distortion', 'felan'),
			],
			'default'      => 'circle',
			'prefix_class' => 'felan-shape-',
		]);

		$this->add_responsive_control('shape_size', [
			'label'     => esc_html__('Size', 'felan'),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'min' => 5,
					'max' => 500,
				],
			],
			'default'   => [
				'unit' => 'px',
				'size' => 50,
			],
			'selectors' => [
				'{{WRAPPER}} .shape' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}} svg'    => 'width: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('shape_border_size', [
			'label'     => esc_html__('Border', 'felan'),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'min' => 1,
					'max' => 50,
				],
			],
			'default'   => [
				'unit' => 'px',
				'size' => 3,
			],
			'selectors' => [
				'{{WRAPPER}} .shape' => 'border-width: {{SIZE}}{{UNIT}};',
			],
			'condition' => [
				'type' => ['border-circle'],
			],
		]);

		$this->add_control('shape_color', [
			'label'     => esc_html__('Color', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .shape'                => 'color: {{VALUE}};',
				'{{WRAPPER}} .elementor-shape-fill' => 'fill: {{VALUE}};',
			],
            'global' => ['default' =>  Global_Colors::COLOR_PRIMARY],
		]);

		$this->end_controls_section();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('box', 'class', 'felan-shape');
?>
		<div <?php $this->print_render_attribute_string('box') ?>>
			<?php if ('distortion' === $settings['type']) : ?>
				<?php echo \Felan_Helper::get_file_contents(FELAN_THEME_DIR . '/assets/shape/' . $settings['type'] . '.svg'); ?>
			<?php else : ?>
				<div class="shape"></div>
			<?php endif; ?>
		</div>
<?php
	}
}
