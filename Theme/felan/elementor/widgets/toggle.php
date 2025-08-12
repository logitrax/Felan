<?php

namespace Felan_Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Utils;

defined('ABSPATH') || exit;

class Widget_Content_Toggle extends Base
{

	public function get_name()
	{
		return 'felan-content-toggle';
	}

	public function get_title()
	{
		return esc_html__('Content Toggle', 'felan');
	}

	public function get_icon_part()
	{
		return 'eicon-dual-button';
	}

	public function get_keywords()
	{
		return ['modern', 'toggle'];
	}

	public function get_style_depends()
	{
		return ['felan-el-widget-toggle'];
	}

	protected function register_controls()
	{
		$this->add_primary_section();

		$this->add_sercondary_section();

		$this->add_toggle_style_section();

		$this->add_discount_style_section();
	}

	private function add_primary_section()
	{
		$this->start_controls_section('primary_section', [
			'label' => esc_html__('Primary', 'felan'),
		]);

		$this->add_control('primary_label', [
			'label'   => esc_html__('Label', 'felan'),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__('Monthly', 'felan'),
		]);

		$this->add_control('primary_style', [
			'label'        => esc_html__('Content Type', 'felan'),
			'type'         => Controls_Manager::SELECT,
			'options'      => [
				'image' => esc_html__('Image', 'felan'),
				'content' => esc_html__('Content', 'felan'),
				'template' => esc_html__('Saved Templates', 'felan'),
			],
			'default'      => 'content',
			'prefix_class' => 'felan-pricing-style-',
		]);

		$this->add_control('primary_image', [
			'label' => esc_html__('Image', 'felan'),
			'type'  => Controls_Manager::MEDIA,
			'default' => [
				'url' => Utils::get_placeholder_image_src(),
			],
			'condition' => [
				'primary_style' => 'image',
			],
		]);

		$this->add_group_control(Group_Control_Image_Size::get_type(), [
			'name'      => 'primary_image',
			'default'   => 'full',
			'separator' => 'none',
			'condition' => [
				'primary_style' => 'image',
			],
		]);

		$this->add_control('primary_content', [
			'label'   => esc_html__('Content', 'felan'),
			'type'    => Controls_Manager::WYSIWYG,
			'condition' => [
				'primary_style' => 'content',
			],
		]);

		$this->add_control('primary_saved_templates', [
			'label'        	=> esc_html__('Saved Templates', 'felan'),
			'type'         	=> Controls_Manager::SELECT,
			'options'   	=> Widget_Utils::saved_templates(),
			'default'      	=> '',
			'condition' 	=> [
				'primary_style' => 'template',
			],
		]);

		$this->end_controls_section();
	}

	private function add_sercondary_section()
	{
		$this->start_controls_section('sercondary_section', [
			'label' => esc_html__('Sercondary', 'felan'),
		]);

		$this->add_control('sercondary_label', [
			'label'   => esc_html__('Label', 'felan'),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__('Anually', 'felan'),
		]);

		$this->add_control('sercondary_style', [
			'label'        => esc_html__('Content Type', 'felan'),
			'type'         => Controls_Manager::SELECT,
			'options'      => [
				'image' => esc_html__('Image', 'felan'),
				'content' => esc_html__('Content', 'felan'),
				'template' => esc_html__('Saved Templates', 'felan'),
			],
			'default'      => 'content',
			'prefix_class' => 'felan-pricing-style-',
		]);

		$this->add_control('sercondary_image', [
			'label' => esc_html__('Image', 'felan'),
			'type'  => Controls_Manager::MEDIA,
			'condition' => [
				'sercondary_style' => 'image',
			],
		]);

		$this->add_group_control(Group_Control_Image_Size::get_type(), [
			'name'      => 'sercondary_image',
			'default'   => 'full',
			'separator' => 'none',
			'condition' => [
				'sercondary_style' => 'image',
			],
		]);

		$this->add_control('sercondary_content', [
			'label'   => esc_html__('Content', 'felan'),
			'type'    => Controls_Manager::WYSIWYG,
			'condition' => [
				'sercondary_style' => 'content',
			],
		]);

		$this->add_control('sercondary_saved_templates', [
			'label'        	=> esc_html__('Saved Templates', 'felan'),
			'type'         	=> Controls_Manager::SELECT,
			'options'   	=> Widget_Utils::saved_templates(),
			'default'      	=> '',
			'condition' 	=> [
				'sercondary_style' => 'template',
			],
		]);

		$this->end_controls_section();
	}

	private function add_toggle_style_section()
	{
		$this->start_controls_section('toggle_style_section', [
			'label'     => esc_html__('Toggle', 'felan'),
			'tab'       => Controls_Manager::TAB_STYLE,
		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'toggle_typography',
			'label'    => esc_html__('Typography', 'felan'),
			'selector' => '{{WRAPPER}} .toggle-wrap .switch',
		]);

		$this->add_control('toggle_width', [
			'label'      => esc_html__('Width', 'felan'),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => ['%', 'px'],
			'range'      => [
				'%'  => [
					'max'  => 100,
					'step' => 1,
				],
				'px' => [
					'max'  => 200,
					'step' => 1,
				],
			],
			'selectors'  => [
				'{{WRAPPER}} .toggle-wrap .switch-label' => 'width: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_control('toggle_height', [
			'label'      => esc_html__('Height', 'felan'),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => ['%', 'px'],
			'range'      => [
				'%'  => [
					'max'  => 100,
					'step' => 1,
				],
				'px' => [
					'max'  => 200,
					'step' => 1,
				],
			],
			'selectors'  => [
				'{{WRAPPER}} .toggle-wrap .switch-label' => 'height: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_control('toggle_radius', [
			'label'      => esc_html__('Border Radius', 'felan'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%'],
			'selectors'  => [
				'{{WRAPPER}} .toggle-wrap .switch-label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_control('toggle_wp_bg_color', [
			'label'     => esc_html__('Background Color', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .toggle-wrap .switch-label' => 'background-color: {{VALUE}};',
			],
		]);

		$this->add_control('toggle_wp_border_color', [
			'label'     => esc_html__('Border Color', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .toggle-wrap .switch-label' => 'border-color: {{VALUE}};',
			],
		]);

		$this->add_control('toggle_skin_heading', [
			'label' => esc_html__('Skin', 'felan'),
			'type'  => Controls_Manager::HEADING,
		]);

		$this->start_controls_tabs('toggle_skin_tabs');

		$this->start_controls_tab('toggle_skin_normal_tab', [
			'label' => esc_html__('Normal', 'felan'),
		]);

		$this->add_control('toggle_border_color', [
			'label'     => esc_html__('Border', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .toggle-wrap .switch' => 'border-color: {{VALUE}};',
			],
		]);

		$this->add_control('toggle_bg_color', [
			'label'     => esc_html__('Background Border', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .toggle-wrap .switch' => 'background-color: {{VALUE}};',
			],
		]);

		$this->add_control('toggle_color', [
			'label'     => esc_html__('Color', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .toggle-wrap .switch' => 'background-color: {{VALUE}};',
			],
		]);

		$this->end_controls_tab();

		$this->start_controls_tab('toggle_skin_active_tab', [
			'label' => esc_html__('Active', 'felan'),
		]);

		$this->add_control('toggle_active_border_color', [
			'label'     => esc_html__('Border', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .toggle-wrap .switch.active' => 'border-color: {{VALUE}};',
			],
		]);

		$this->add_control('toggle_active_bg_color', [
			'label'     => esc_html__('Background Border', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .toggle-wrap .switch.active' => 'background-color: {{VALUE}};',
			],
		]);

		$this->add_control('toggle_active_color', [
			'label'     => esc_html__('Color', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .toggle-wrap .switch.active' => 'background-color: {{VALUE}};',
			],
		]);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	private function add_discount_style_section()
	{
		$this->start_controls_section('discount_style_section', [
			'label'     => esc_html__('Discount', 'felan'),
			'tab'       => Controls_Manager::TAB_STYLE,
		]);

		$this->add_control('discount_color', [
			'label'     => esc_html__('Color', 'felan'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .toggle-wrap .discount' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'discount_label',
			'global' => ['default' =>  Global_Typography::TYPOGRAPHY_PRIMARY],
			'selector' => '{{WRAPPER}} .toggle-wrap .discount .discount-text',
		]);

		$this->end_controls_section();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('wrapper', 'class', 'felan-pricing-plan');

		$this->add_render_attribute('heading', 'class', 'title');
?>
		<div <?php $this->print_attributes_string('wrapper'); ?>>
			<div class="inner">

				<div class="felan-pricing-plan-header">
					<div class="toggle-wrap">
						<div class="switch-label">
							<?php if (!empty($settings['primary_label'])) : ?>
								<span class="switch active"><?php echo esc_html($settings['primary_label']); ?></span>
							<?php endif; ?>
							<?php if (!empty($settings['sercondary_label'])) : ?>
								<span class="switch"><?php echo esc_html($settings['sercondary_label']); ?></span>
							<?php endif; ?>
						</div>
					</div>
				</div>
				<div class="felan-pricing-plan-main">
					<div class="pricing-plan-item pricing-plan-primary active">

						<?php if ($settings['primary_style'] === 'image' && $settings['primary_image']) : ?>
							<div class="primary-image">
								<?php echo \Felan_Image::get_elementor_attachment([
									'settings' 	=> $settings,
									'image_key'	=> 'primary_image',
								]); ?>
							</div>
						<?php endif; ?>

						<?php if ($settings['primary_style'] === 'content' && $settings['primary_content']) : ?>
							<div class="primary-content">
								<?php echo '' . $this->parse_text_editor($settings['primary_content']); ?>
							</div>
						<?php endif; ?>

						<?php if ($settings['primary_style'] === 'template' && $settings['primary_saved_templates']) : ?>
							<div class="primary-template">
								<?php
                                $template_primary_id = isset($settings['primary_saved_templates']) ? intval($settings['primary_saved_templates']) : 0;
                                if ($template_primary_id) {
                                    echo Widget_Init::felan_template_elementor(['id' => $template_primary_id]);
                                }
								 ?>
							</div>
						<?php endif; ?>

					</div>

					<div class="pricing-plan-item pricing-plan-sercondary">

						<?php if ($settings['sercondary_style'] === 'image' && $settings['sercondary_image']) : ?>
							<div class="sercondary-image">
								<?php echo \Felan_Image::get_elementor_attachment([
									'settings' 	=> $settings,
									'image_key'	=> 'sercondary_image',
								]); ?>
							</div>
						<?php endif; ?>

						<?php if ($settings['sercondary_style'] === 'content' && $settings['sercondary_content']) : ?>
							<div class="sercondary-content">
								<?php echo '' . $this->parse_text_editor($settings['sercondary_content']); ?>
							</div>
						<?php endif; ?>

						<?php if ($settings['sercondary_style'] === 'template' && $settings['sercondary_saved_templates']) : ?>
							<div class="sercondary-template">
                                <?php
                                $template_sercondary_id = isset($settings['sercondary_saved_templates']) ? intval($settings['sercondary_saved_templates']) : 0;
                                if ($template_sercondary_id) {
                                    echo Widget_Init::felan_template_elementor(['id' => $template_sercondary_id]);
                                }
                                ?>
							</div>
						<?php endif; ?>

					</div>
				</div>
			</div>
		</div>
<?php
	}
}
