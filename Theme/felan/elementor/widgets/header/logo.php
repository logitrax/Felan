<?php

namespace Felan_Elementor;

defined('ABSPATH') || exit;

class Widget_Site_Logo extends Base
{

    public function get_name()
    {
        return 'felan-site-logo';
    }

    public function get_title()
    {
        return esc_html__('Site Logo', 'felan');
    }

    public function get_icon_part()
    {
        return 'eicon-site-logo';
    }

    public function get_keywords()
    {
        return ['logo'];
    }

    protected function register_controls()
    {
        $this->add_content_section();
    }

    private function add_content_section()
    {
        $this->start_controls_section('content_section', [
            'label' => esc_html__('Content', 'felan'),
        ]);

        $this->add_responsive_control('logo_width', [
            'label' => esc_html__('Logo Width', 'felan'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 1000,
                ]
            ],
            'size_units' => ['px'],
            'default' => [
                'unit' => 'px',
                'size' => 120,
            ],
            'tablet_default' => [
                'unit' => 'px',
                'size' => 80,
            ],
            'mobile_default' => [
                'unit' => 'px',
                'size' => 80,
            ],
            'selectors' => [
                '{{WRAPPER}} .site-logo img' => 'width: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();
    }

    protected function render()
    {
        $id = get_the_ID();
        $header_style = '';
        if (!empty($id)) {
            $header_style = get_post_meta($id, 'felan-header_style', true);
        }
        if ($header_style == 'light') {
            $header_logo = \Felan_Templates::site_logo('light');
        } else {
            $header_logo = \Felan_Templates::site_logo('dark');
        }
        echo wp_kses_post($header_logo);
    }
}
