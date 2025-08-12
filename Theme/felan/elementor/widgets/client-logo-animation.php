<?php

namespace Felan_Elementor;

use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Plugin;

defined('ABSPATH') || exit;

class Widget_Client_Logo_Animation extends Base
{

    public function get_name()
    {
        return 'felan-client-logo-animation';
    }

    public function get_title()
    {
        return esc_html__('Client Logo Animation', 'felan');
    }

    public function get_icon()
    {
        return 'felan-badge eicon-logo';
    }

    public function get_keywords()
    {
        return ['client-logo'];
    }

    public function get_script_depends()
    {
        return ['felan-widget-client-logo-animation'];
    }

    public function get_style_depends()
    {
        return ['felan-el-widget-client-logo-animation'];
    }

    protected function register_controls()
    {
        $this->add_layout_section();
        $this->add_layout_style_section();
    }

    private function add_layout_section()
    {
        $this->start_controls_section('layout_section', [
            'label' => esc_html__('Layout', 'felan'),
        ]);

        $this->add_control(
            'layout',
            [
                'label' => esc_html__('Layout', 'felan'),
                'type' => Controls_Manager::SELECT,
                'default' => '01',
                'options' => [
                    '01' => esc_html__('Layout 01', 'felan'),
                    '02' => esc_html__('Layout 02', 'felan'),
                ],
            ]
        );

        $this->add_control('effect', [
            'label'        => esc_html__('Effect', 'felan'),
            'type'         => Controls_Manager::SELECT,
            'options'      => [
                'rtl'  => esc_html__('Left To Right', 'felan'),
                'ltr' => esc_html__('Right To Left', 'felan'),
                'btt' => esc_html__('Bottom To Top', 'felan'),
            ],
            'default' => 'rtl',
        ]);

        $repeater = new Repeater();

        $repeater->add_control('logo', [
            'label'   => esc_html__('Logo', 'felan'),
            'type'    => Controls_Manager::MEDIA,
            'default' => [
                'url' => Utils::get_placeholder_image_src(),
            ],
        ]);

        $repeater->add_control('text', [
            'label'       => esc_html__('Text', 'felan'),
            'type'        => Controls_Manager::TEXT,
        ]);

        $repeater->add_control('link', [
            'label'         => esc_html__('Link', 'felan'),
            'type'          => Controls_Manager::URL,
            'placeholder'   => esc_html__('https://your-link.com', 'felan'),
            'show_external' => true,
            'default'       => [
                'url'         => '',
                'is_external' => true,
                'nofollow'    => true,
            ],
        ]);

        $this->add_control(
            'list',
            [
                'label' => '',
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'logo' => Utils::get_placeholder_image_src(),
                    ],
                    [
                        'logo' => Utils::get_placeholder_image_src(),
                    ],
                ],
            ]
        );

        $this->end_controls_section();
    }

    private function add_layout_style_section()
    {
        $this->start_controls_section('box_style_section', [
            'label' => esc_html__('Box', 'felan'),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
            'name'     => 'box_shadow',
            'selector' => '{{WRAPPER}} .image',
        ]);

        $this->add_control('box_color', [
            'label'     => esc_html__('Background Color', 'felan'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .image' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'box_border',
                'label' => __('Border', 'felan'),
                'selector' => '{{WRAPPER}} .image',
            ]
        );

        $this->add_responsive_control('box_border_radius', [
            'label'      => esc_html__('Border Radius', 'felan'),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors'  => [
                '{{WRAPPER}} .image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('box_padding', [
            'label'      => esc_html__('Padding', 'felan'),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors'  => [
                '{{WRAPPER}} .image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $this->add_render_attribute('wrapper', 'class', [
            'felan-client-logo-animation',
            $settings['effect'],
            'layout-' . $settings['layout']
        ]);
        ?>
        <div <?php $this->print_render_attribute_string('wrapper') ?>>
            <div class="client-logo-inner">
                <?php $this->print_client_logo_content($settings); ?>
            </div>
            <div class="client-logo-inner">
                <?php $this->print_client_logo_content($settings); ?>
            </div>
			<div class="client-logo-inner">
                <?php $this->print_client_logo_content($settings); ?>
            </div>
        </div>
        <?php }


    private function print_client_logo_content(array $settings)
    {

        if (!empty($settings['list'])) {
            foreach ($settings['list'] as $item) {
                $item_id = $item['_id'];
                $item_key = 'item_' . $item_id;
                $item_link_key = $item_key . '_link';

                if (!empty($item['link']['url'])) {
                    $this->add_link_attributes($item_link_key, $item['link']);
                }
        ?>
                <div class="felan-client-logo-item">
                    <?php if (!empty($item['link']['url'])) { ?>
                        <a <?php $this->print_render_attribute_string($item_link_key); ?>>
                        <?php } ?>
                        <div class="text">
                            <span><?php echo esc_html($item['text']); ?></span>
                        </div>
                        <div class="image">
                            <img src="<?php echo esc_url($item['logo']['url']); ?>" alt="<?php esc_attr_e('Client Logo', 'felan'); ?>">
                        </div>
                        <?php if (!empty($item['link']['url'])) { ?>
                        </a>
                    <?php } ?>
                </div>
<?php
            }
        }
    }
}
