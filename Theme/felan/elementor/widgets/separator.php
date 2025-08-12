<?php

namespace Felan_Elementor;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

defined('ABSPATH') || exit;

class Widget_Separator extends Base
{

	public function get_name()
	{
		return 'felan-separator';
	}

	public function get_title()
	{
		return esc_html__('Separator', 'felan');
	}

	public function get_icon_part()
	{
		return 'eicon-divider';
	}

	public function get_keywords()
	{
		return ['divider', 'hr', 'line', 'border', 'separator'];
	}

	public function get_style_depends()
	{
		return ['felan-el-widget-separator'];
	}

	protected function register_controls()
	{
		$this->start_controls_section('separator_section', [
			'label' => esc_html__('Separator', 'felan'),
		]);

		$this->add_control('style', [
			'label'   => esc_html__('Style', 'felan'),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'vertical-line'   => esc_html__('Vertical Line', 'felan'),
				'horizontal-line' => esc_html__('Horizontal Line', 'felan'),
			],
			'default' => 'vertical-line',
		]);

		$this->add_control('play_animate', [
			'label'        => esc_html__('Play Animate', 'felan'),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => '1',
			'condition'    => [
				'style' => array('vertical-line'),
			],
		]);

		$this->add_control('color', [
			'label'     => esc_html__('Color', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
            'global' => ['default' =>  Global_Colors::COLOR_PRIMARY],
			'selectors' => [
				'{{WRAPPER}} .felan-separator .inner' => 'color: {{VALUE}};',
			],
		]);

		$this->add_responsive_control('align', [
			'label'        => esc_html__('Alignment', 'felan'),
			'type'         => Controls_Manager::CHOOSE,
			'options'      => Widget_Utils::get_control_options_horizontal_alignment(),
			'prefix_class' => 'elementor%s-align-',
			'default'      => 'center',
		]);

		$this->add_responsive_control('width', [
			'label'      => esc_html__('Width', 'felan'),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => ['%', 'px'],
			'range'      => [
				'px' => [
					'max' => 1000,
				],
			],
			'selectors'  => [
				'{{WRAPPER}} .felan-separator .inner' => 'width: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('height', [
			'label'      => esc_html__('Height', 'felan'),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => ['%', 'px'],
			'range'      => [
				'px' => [
					'max' => 1000,
				],
			],
			'selectors'  => [
				'{{WRAPPER}} .felan-separator .inner' => 'height: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->end_controls_section();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('wrapper', 'class', 'felan-separator');
		$this->add_render_attribute('wrapper', 'class', $settings['style']);

		if ($settings['play_animate'] === '1') {
			$this->add_render_attribute('wrapper', 'class', 'play-animate');
		}
?>
		<div <?php $this->print_attributes_string('wrapper'); ?>>
			<div class="inner"></div>
		</div>
	<?php
	}

	protected function content_template()
	{
		// @formatter:off
	?>
		<# view.addRenderAttribute( 'wrapper' , 'class' , 'felan-separator' ); view.addRenderAttribute( 'wrapper' , 'class' , settings.style ); if( settings.play_animate==='1' ) { view.addRenderAttribute( 'wrapper' , 'class' , 'play-animate' ); } #>
			<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
				<div class="inner"></div>
			</div>
	<?php
		// @formatter:off
	}
}
