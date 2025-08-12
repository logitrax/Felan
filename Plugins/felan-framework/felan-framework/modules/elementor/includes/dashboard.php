<?php

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Plugin;

defined('ABSPATH') || exit;

Plugin::instance()->widgets_manager->register(new Widget_Dashboard());

class Widget_Dashboard extends Widget_Base
{
    public function get_name()
    {
        return 'felan-dashboard';
    }

    public function get_title()
    {
        return esc_html__('Dashboard', 'felan-framework');
    }

    public function get_icon()
    {
        return 'felan-badge eicon-archive';
    }

    public function get_keywords()
    {
        return ['list', 'dashboard'];
    }

    protected function register_controls()
    {
        $this->register_layout_section();
    }

    private function register_layout_section()
    {
        $this->start_controls_section('layout_section', [
            'label' => esc_html__('Layout', 'felan-framework'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control(
            'user',
            [
                'label' => esc_html__('User', 'felan-framework'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'employer' => esc_html__('Employer', 'felan-framework'),
                    'freelancer' => esc_html__('Freelancer', 'felan-framework'),
                ],
                'default' => 'employer',
            ]
        );

        $this->add_control(
            'type',
            [
                'label' => esc_html__('Type', 'felan-framework'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'jobs' => esc_html__('Jobs', 'felan-framework'),
                    'freelance' => esc_html__('Freelance', 'felan-framework'),
                ],
                'default' => 'jobs',
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $this->add_render_attribute('wrapper', 'class', array('felan-dashboard'));

        if($settings['user'] === 'employer'){
            echo do_shortcode('[felan_dashboard]');
        } else {
            echo do_shortcode('[dashboard_freelancer]');
        }
    }
}
