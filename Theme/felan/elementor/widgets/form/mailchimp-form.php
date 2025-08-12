<?php

namespace Felan_Elementor;

use Elementor\Controls_Manager;

defined('ABSPATH') || exit;

class Widget_Mailchimp_Form extends Form_Base
{

	public function get_name()
	{
		return 'felan-mailchimp-form';
	}

	public function get_title()
	{
		return esc_html__('Mailchimp Form', 'felan');
	}

	public function get_keywords()
	{
		return ['mailchimp', 'form', 'subscribe'];
	}

	public function get_style_depends()
	{
		return ['felan-el-widget-mailchimp-form'];
	}

	protected function register_controls()
	{
		$this->add_content_section();
		$this->add_content_style_section();
	}

	private function add_content_section()
	{
		$this->start_controls_section('content_section', [
			'label' => esc_html__('Layout', 'felan'),
		]);

		$this->add_responsive_control(
			'align',
			[
				'label' => esc_html__('Alignment', 'felan'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left'    => [
						'title' => esc_html__('Left', 'felan'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'felan'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'felan'),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .mc4wp-form-fields' => 'justify-content:{{VALUE}}; -webkit-box-pack:{{VALUE}};-ms-flex-pack:{{VALUE}};',
				],
			]
		);

		$this->add_control('form_id', [
			'label'       => esc_html__('Form Id', 'felan'),
			'description' => esc_html__('Input the id of form. Leave blank to show default form.', 'felan'),
			'type'        => Controls_Manager::TEXT,
		]);

		$this->add_control('style', [
			'label'        => esc_html__('Style', 'felan'),
			'type'         => Controls_Manager::SELECT,
			'options'      => [
				'01' => '01',
				'02' => '02',
			],
			'default'      => '01',
			'prefix_class' => 'felan-mailchimp-form-style-',
		]);

		$this->end_controls_section();
	}

	private function add_content_style_section()
	{

		$this->start_controls_section('content_style_section', [
			'label' => esc_html__('Button Style', 'felan'),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->start_controls_tabs('button_style_tabs');

		$this->start_controls_tab('button_style_normal_tab', [
			'label' => esc_html__('Normal', 'felan'),
		]);

		$this->add_control(
			'button_background_color',
			[
				'label' => esc_html__('Background Color', 'felan'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} input[type=submit]' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_border_color',
			[
				'label' => esc_html__('Border Color', 'felan'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} input[type=submit]' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_color',
			[
				'label' => esc_html__('Color', 'felan'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} input[type=submit]' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('button_style_hover_tab', [
			'label' => esc_html__('Hover', 'felan'),
		]);

		$this->add_control(
			'button_hover_background_color',
			[
				'label' => esc_html__('Background Color', 'felan'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} input[type=submit]:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label' => esc_html__('Border Color', 'felan'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} input[type=submit]:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_hover_color',
			[
				'label' => esc_html__('Color', 'felan'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} input[type=submit]:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();
		$form_id  = !empty($settings['form_id']) ? $settings['form_id'] : '';


		if ('' === $form_id && function_exists('mc4wp_get_forms')) {
			$mc_forms = mc4wp_get_forms();
			if (count($mc_forms) > 0) {
				$form_id = $mc_forms[0]->ID;
			}
		}

		$this->add_render_attribute('box', 'class', 'felan-mailchimp-form');
?>
		<?php if (function_exists('mc4wp_show_form') && $form_id !== '') : ?>
			<div <?php $this->print_render_attribute_string('box') ?>>
				<?php mc4wp_show_form($form_id); ?>
			</div>
		<?php endif; ?>
<?php
	}
}
