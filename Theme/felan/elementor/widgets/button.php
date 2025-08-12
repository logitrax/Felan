<?php

namespace Felan_Elementor;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use \Elementor\Group_Control_Border;
use Elementor\Icons_Manager;

defined('ABSPATH') || exit;

class Widget_Button extends Base
{

	public function get_name()
	{
		return 'felan-button';
	}

	public function get_title()
	{
		return esc_html__('Advanced Button', 'felan');
	}

	public function get_icon_part()
	{
		return 'eicon-button';
	}

	public function get_style_depends()
	{
		return ['felan-el-widget-button'];
	}

	protected function register_controls()
	{
		$this->register_button_content();
		$this->register_button_wrap_style();
		$this->register_button_icon_style();
	}

	private function register_button_content()
	{
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__('Button', 'felan')
			]
		);

		$this->add_control(
			'text',
			[
				'label' => esc_html__('Text', 'felan'),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__('Button', 'felan'),
				'placeholder' => esc_html__('Button', 'felan'),
			]
		);

		$this->add_control(
			'open_lr_form',
			[
				'label' => esc_html__('Click Form Account', 'felan'),
				'description' => esc_html__('Not logged in, click the account popup form.', 'felan'),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
			]
		);

		$this->add_control(
			'link',
			[
				'label' => esc_html__('Link', 'felan'),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__('https://your-link.com', 'felan'),
				'default' => [
					'url' => '#',
				],
			]
		);


		$this->add_control(
			'type',
			[
				'label' => esc_html__('Type', 'felan'),
				'type' => Controls_Manager::SELECT,
				'default' => 'classic',
				'separator'     => 'before',
				'options' => Widget_Utils::get_button_style(),
			]
		);

		$this->add_control(
			'shape',
			[
				'label' => esc_html__('Shape', 'felan'),
				'type' => Controls_Manager::SELECT,
				'default' => 'rounded',
				'options' => Widget_Utils::get_button_shape(),
				'condition' => [
					'type[value]!' => 'link',
				],
			]
		);

		$this->add_control(
			'size',
			[
				'label' => esc_html__('Size', 'felan'),
				'type' => Controls_Manager::SELECT,
				'default' => 'md',
				'options' =>  Widget_Utils::get_button_size(),
			]
		);

		$this->add_control(
			'icon',
			[
				'label' => esc_html__('Icon', 'felan'),
				'type' => Controls_Manager::ICONS,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'icon_align',
			[
				'label' => esc_html__('Icon Position', 'felan'),
				'type' => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left' => esc_html__('Before', 'felan'),
					'right' => esc_html__('After', 'felan'),
				],
				'condition' => [
					'icon[value]!' => '',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_button_wrap_style()
	{
		$this->start_controls_section(
			'section_wrap_style',
			[
				'label' => esc_html__('Button', 'felan'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'selector' => '{{WRAPPER}} .felan-button',
			]
		);

		$this->add_control(
			'button_gradient_background',
			[
				'label' => esc_html__('Use Gradient Background', 'felan'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Show', 'felan'),
				'label_off' => esc_html__('Hide', 'felan'),
				'return_value' => 'yes',
				'default' => '',
				'condition' => [
					'type!' => 'link',
				],
			]
		);

		$this->start_controls_tabs('tabs_button_style');

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__('Normal', 'felan'),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => esc_html__('Text Color', 'felan'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .felan-button' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_border_color',
			[
				'label' => esc_html__('Border Color', 'felan'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .felan-button' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'type!' => 'link',
				],
			]
		);

		$this->add_control(
			'button_background_color',
			[
				'label' => esc_html__('Background Color', 'felan'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .felan-button' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'type!' => ['link', 'outline'],
					'button_gradient_background' => ''
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'button_background_gradient_color',
				'types' => ['gradient', 'classic'],
				'selector' => '{{WRAPPER}} .felan-button',
				'condition' => [
					'type!' => ['link', 'outline'],
					'button_gradient_background' => 'yes'
				],
			]
		);


		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__('Hover', 'felan'),
			]
		);


		$this->add_control(
			'button_text_color_hover',
			[
				'label' => esc_html__('Text Color', 'felan'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .felan-button:hover' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_border_color_hover',
			[
				'label' => esc_html__('Border Color', 'felan'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .felan-button:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'type!' => 'link',
				],
			]
		);

		$this->add_control(
			'button_background_color_hover',
			[
				'label' => esc_html__('Background Color', 'felan'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .felan-button:hover' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'type!' => ['link'],
					'button_gradient_background' => ''
				],
			]
		);


		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'button_background_gradient_color_hover',
				'types' => ['gradient', 'classic'],
				'selector' => '{{WRAPPER}} .felan-button:hover',
				'condition' => [
					'type!' => ['link'],
					'button_gradient_background' => 'yes'
				],
			]
		);

		$this->add_control(
			'hover_animation',
			[
				'label' => esc_html__('Hover Animation', 'felan'),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();


		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'selector' => '{{WRAPPER}} .felan-button',
				'separator' => 'before',
			]
		);


		$this->add_control(
			'border_radius',
			[
				'label' => esc_html__('Border Radius', 'felan'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .felan-button' => 'border-radius: {{SIZE}}px;',
				],
			]
		);

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
				'prefix_class' => 'elementor%s-align-',
			]
		);

		$this->add_responsive_control(
			'button_width',
			[
				'label'      => esc_html__('Width', 'felan'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .felan-button' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .felan-button',
			]
		);

		$this->add_responsive_control(
			'text_padding',
			[
				'label' => esc_html__('Padding', 'felan'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .felan-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
	}

	private function register_button_icon_style()
	{
		$this->start_controls_section(
			'section_icon_style',
			[
				'label' => esc_html__('Icon', 'felan'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'icon_indent',
			[
				'label' => esc_html__('Icon Spacing', 'felan'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .button-icon-right i' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .button-icon-right svg' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .button-icon-right i' => 'margin-right: 0;',
					'{{WRAPPER}} .button-icon-right svg' => 'margin-right: 0;',
					'{{WRAPPER}} .button-icon-left i' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .button-icon-left svg' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .button-icon-left i' => 'margin-left: 0;',
					'{{WRAPPER}} .button-icon-left svg' => 'margin-left: 0;',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'      => esc_html__('Size', 'felan'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', 'rem'],
				'range'      => [
					'px' => [
						'min' => 1,
						'max' => 200,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .felan-button-icon' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->start_controls_tabs('tabs_icon_style');

		$this->start_controls_tab(
			'tab_icon_normal',
			[
				'label' => esc_html__('Normal', 'felan'),
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => esc_html__('Color', 'felan'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .felan-button-icon' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();


		$this->start_controls_tab(
			'tab_icon_hover',
			[
				'label' => esc_html__('Hover', 'felan'),
			]
		);

		$this->add_control(
			'icon_color_hover',
			[
				'label' => esc_html__('Color', 'felan'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .felan-button-icon:hover' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();

		$wrapper_classes = array(
			'felan-button',
			"button-{$settings['size']}",
			"button-{$settings['shape']}",
		);

		if ($settings['type'] !== '') {
			$wrapper_classes[] = "button-{$settings['type']}";
		}

		if ($settings['hover_animation']) {
			$wrapper_classes[] = "elementor-animation-{$settings['hover_animation']}";
		}

		if (!empty($settings['icon']) && !empty($settings['icon']['value'])) {
			$wrapper_classes[] = "button-icon-{$settings['icon_align']}";
		}

		if ($settings['open_lr_form'] == 'yes' && !is_user_logged_in()) {
			$wrapper_classes[] = 'btn-login';
		}

		$this->add_render_attribute('wrapper', 'class', $wrapper_classes);

		$this->add_inline_editing_attributes('text', 'none');

		if ($settings['open_lr_form'] == 'yes') {
			if (!is_user_logged_in()) {
				$this->add_render_attribute('wrapper', 'href', '#popup-form');
			} else {
				if (!empty($settings['link']['url'])) {
					$this->add_link_attributes('wrapper', $settings['link']);
				}
			}
		} else {
			if (!empty($settings['link']['url'])) {
				$this->add_link_attributes('wrapper', $settings['link']);
			}
		}
?>
		<?php
		if ($settings['open_lr_form'] == 'yes' && !is_user_logged_in()) {
			echo '<div class="logged-out">';
		}
		?>
		<a <?php $this->print_render_attribute_string('wrapper') ?>>
			<?php if (!empty($settings['icon']) && !empty($settings['icon']['value']) && ($settings['icon_align'] === 'left')) : ?>
				<span class="felan-button-icon"><?php Icons_Manager::render_icon($settings['icon'], ['aria-hidden' => 'true']); ?></span>
			<?php endif; ?>
			<span <?php $this->print_render_attribute_string('text') ?>><?php echo esc_html($settings['text']); ?></span>
			<?php if (!empty($settings['icon']) && !empty($settings['icon']['value']) && ($settings['icon_align'] === 'right')) : ?>
				<span class="felan-button-icon"><?php Icons_Manager::render_icon($settings['icon'], ['aria-hidden' => 'true']); ?></span>
			<?php endif; ?>
		</a>
		<?php
		if ($settings['open_lr_form'] == 'yes' && !is_user_logged_in()) {
			echo '</div>';
		}
		?>
	<?php
	}

	protected function content_template()
	{
		// @formatter:off
	?>
		<# var wrapper_classes=[ 'felan-button' , 'button-' + settings.size, 'button-' + settings.shape, ]; if (settings.type !=='' ) { wrapper_classes.push('button-' + settings.type); } if ( settings.hover_animation ) { wrapper_classes.push('elementor-animation-' + settings.hover_animation); } if ((settings.icon !=='' ) && (settings.icon.value !=='' )) { wrapper_classes.push('button-icon-' + settings.icon_align); } var iconHTML=elementor.helpers.renderIcon( view, settings.icon, { 'aria-hidden' : true }, 'i' , 'object' ); view.addRenderAttribute('wrapper', 'class' , wrapper_classes); view.addRenderAttribute('text', 'class' , 'felan-button-text' ); view.addInlineEditingAttributes( 'text' , 'none' ); #>
			<a href="{{ settings.link.url }}" {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
				<# if ((iconHTML.rendered) && (settings.icon_align==='left' )) { #>
					<span class="felan-button-icon">{{{ iconHTML.value }}}</span>
					<# } #>
						<span {{{ view.getRenderAttributeString( 'text' ) }}}>{{{ settings.text }}}</span>
						<# if ((iconHTML.rendered) && (settings.icon_align==='right' )) { #>
							<span class="felan-button-icon">{{{ iconHTML.value }}}</span>
							<# } #>
			</a>
	<?php
		// @formatter:off
	}
}
