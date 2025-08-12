<?php

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Plugin;

defined('ABSPATH') || exit;

Plugin::instance()->widgets_manager->register(new Widget_Advanced_Archive());

class Widget_Advanced_Archive extends Widget_Base
{

    public function get_name()
    {
        return 'felan-advanced-archive';
    }

    public function get_title()
    {
        return esc_html__('Advanced Archive', 'felan-framework');
    }

    public function get_icon()
    {
        return 'felan-badge eicon-archive-posts';
    }

    public function get_keywords()
    {
        return ['jobs', 'companies', 'freelancer'];
    }

    public function get_style_depends()
    {
        return [FELAN_PLUGIN_PREFIX . 'advanced-archive'];
    }

    protected function register_controls()
    {
        $this->add_content_section();
        $this->add_sidebar_section();
    }

    private function add_content_section()
    {
        $this->start_controls_section('content_section', [
            'label' => esc_html__('Content', 'felan-framework'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $options =  array(
            'jobs' => esc_html__('Jobs', 'felan-framework'),
            'company' => esc_html__('Companies', 'felan-framework'),
            'freelancer' => esc_html__('Freelancers', 'felan-framework'),
        );

        $options['service'] = esc_html__('Services', 'felan-framework');

        $this->add_control('post_type', [
            'label' => esc_html__('Post Type', 'felan-framework'),
            'type' => Controls_Manager::SELECT,
            'options' => $options,
            'default' => 'jobs',
        ]);

        $this->add_control(
            'color_featured',
            [
                'label' => esc_html__('Color Featured ', 'felan-framework'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .felan-jobs-featured ' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .felan-freelancer-featured ' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .felan-company-featured ' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    private function add_sidebar_section()
    {
        $this->start_controls_section('sidebar_section', [
            'label' => esc_html__('Sidebar', 'felan-framework'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control(
            'show_count',
            [
                'label' => esc_html__('Show Count', 'felan-framework'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'sidebar_checkbox_style',
            [
                'label' => esc_html__('Check Box Style', 'felan-framework'),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    'square' => esc_html__('Square', 'felan-framework'),
                    'round' => esc_html__('Round', 'felan-framework'),
                ),
                'default' => 'square',
                'label_block' => true,
                'prefix_class' => 'felan-checkbox-',
            ]
        );

        $this->add_control(
            'sidebar_range',
            [
                'label' => esc_html__('Slider Range Color', 'felan-framework'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ui-slider .ui-slider-range' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} #slider-range .ui-state-default' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'sidebar_background',
            [
                'label' => esc_html__('Background Color', 'felan-framework'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .inner-content .inner-filter' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control('sidebar_space', [
            'label'     => esc_html__('Spacing', 'felan-framework'),
            'type'      => Controls_Manager::SLIDER,
            'range'     => [
                'px' => [
                    'min' => 0,
                    'max' => 200,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .inner-content .inner-filter' => 'margin-right: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('sidebar_padding', [
            'label' => esc_html__('Padding', 'felan-framework'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .inner-content .inner-filter' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_control('sidebar_border_radius', [
            'label' => esc_html__('Border Radius', 'felan-framework'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .inner-content .inner-filter' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $this->add_render_attribute('wrapper', 'class', 'felan-advanced-archive');

        if ($settings['show_count'] !== 'yes') {
            $this->add_render_attribute('wrapper', 'class', 'off-count');
        }

        if ($settings['post_type'] == 'jobs') {
            $jobs_map_postion = $map_event = '';
            $content_jobs = felan_get_option('archive_jobs_layout', 'layout-list');
            $content_jobs = !empty($_GET['layout']) ? Felan_Helper::felan_clean(wp_unslash($_GET['layout'])) : $content_jobs;
            $enable_jobs_show_map = felan_get_option('enable_jobs_show_map', 1);
            $enable_jobs_show_map = !empty($_GET['has_map']) ? Felan_Helper::felan_clean(wp_unslash($_GET['has_map'])) : $enable_jobs_show_map;

            if ($enable_jobs_show_map == 1) {
                $archive_jobs_filter = 'filter-canvas';
                $jobs_map_postion = felan_get_option('jobs_map_postion');
                $jobs_map_postion = !empty($_GET['map']) ? Felan_Helper::felan_clean(wp_unslash($_GET['map'])) : $jobs_map_postion;
                if ($jobs_map_postion == 'map-right') {
                    $map_event = 'map-event';
                }
            } else if ($content_jobs == 'layout-full') {
                $archive_jobs_filter = 'filter-canvas';
            } else {
                $archive_jobs_filter = felan_get_option('jobs_filter_sidebar_option', 'filter-left');
            };
            $archive_jobs_filter = !empty($_GET['filter']) ? Felan_Helper::felan_clean(wp_unslash($_GET['filter'])) : $archive_jobs_filter;
            $archive_classes = array('archive-layout', 'archive-jobs', $archive_jobs_filter, $map_event, $jobs_map_postion);
        } elseif ($settings['post_type'] == 'company') {
            $company_map_postion = $map_event = '';
            $content_company              = felan_get_option('archive_company_layout', 'layout-list');
            $enable_company_show_map = felan_get_option('enable_company_show_map', 1);
            $enable_company_show_map = !empty($_GET['has_map']) ? Felan_Helper::felan_clean(wp_unslash($_GET['has_map'])) : $enable_company_show_map;

            $map_event = '';
            if ($enable_company_show_map == 1) {
                $archive_company_filter = 'filter-canvas';
                $company_map_postion = felan_get_option('company_map_postion');
                $company_map_postion = !empty($_GET['map']) ? Felan_Helper::felan_clean(wp_unslash($_GET['map'])) : $company_map_postion;
                if ($company_map_postion == 'map-right') {
                    $map_event = 'map-event';
                }
            } else {
                $archive_company_filter = felan_get_option('company_filter_sidebar_option', 'filter-left');
            };
            $archive_company_filter = !empty($_GET['filter']) ? Felan_Helper::felan_clean(wp_unslash($_GET['filter'])) : $archive_company_filter;
            $content_company = !empty($_GET['layout']) ? Felan_Helper::felan_clean(wp_unslash($_GET['layout'])) : $content_company;
            $archive_classes = array('archive-layout', 'archive-company', $archive_company_filter, $map_event, $company_map_postion);
        } elseif ($settings['post_type'] == 'freelancer') {
            $map_event = $freelancer_map_postion = '';
            $content_freelancer = felan_get_option('archive_freelancer_layout', 'layout-list');
            $enable_freelancer_show_map = felan_get_option('enable_freelancer_show_map', 1);
            $enable_freelancer_show_map = !empty($_GET['has_map']) ? Felan_Helper::felan_clean(wp_unslash($_GET['has_map'])) : $enable_freelancer_show_map;

            if ($enable_freelancer_show_map == 1) {
                $archive_freelancer_filter = 'filter-canvas';
                $freelancer_map_postion = felan_get_option('freelancer_map_postion');
                $freelancer_map_postion = !empty($_GET['map']) ? Felan_Helper::felan_clean(wp_unslash($_GET['map'])) : $freelancer_map_postion;
                if ($freelancer_map_postion == 'map-right') {
                    $map_event = 'map-event';
                }
            } else {
                $archive_freelancer_filter = felan_get_option('freelancer_filter_sidebar_option', 'filter-left');
            };
            $archive_freelancer_filter = !empty($_GET['filter']) ? Felan_Helper::felan_clean(wp_unslash($_GET['filter'])) : $archive_freelancer_filter;
            $content_freelancer = !empty($_GET['layout']) ? Felan_Helper::felan_clean(wp_unslash($_GET['layout'])) : $content_freelancer;
            $archive_classes = array('archive-layout', 'archive-freelancers', $archive_freelancer_filter, $map_event, $freelancer_map_postion);
        } elseif ($settings['post_type'] == 'service') {
            $service_map_postion = $map_event = '';
            $content_service              = felan_get_option('archive_service_layout', 'layout-list');
            $enable_service_show_map = felan_get_option('enable_service_show_map', 1);
            $enable_service_show_map = !empty($_GET['has_map']) ? Felan_Helper::felan_clean(wp_unslash($_GET['has_map'])) : $enable_service_show_map;

            $map_event = '';
            if ($enable_service_show_map == 1) {
                $archive_service_filter = 'filter-canvas';
                $service_map_postion = felan_get_option('service_map_postion');
                $service_map_postion = !empty($_GET['map']) ? Felan_Helper::felan_clean(wp_unslash($_GET['map'])) : $service_map_postion;
                if ($service_map_postion == 'map-right') {
                    $map_event = 'map-event';
                }
            } else {
                $archive_service_filter = felan_get_option('service_filter_sidebar_option', 'filter-left');
            };
            $archive_service_filter = !empty($_GET['filter']) ? Felan_Helper::felan_clean(wp_unslash($_GET['filter'])) : $archive_service_filter;
            $content_service = !empty($_GET['layout']) ? Felan_Helper::felan_clean(wp_unslash($_GET['layout'])) : $content_service;
            $archive_classes = array('archive-layout', 'archive-service', $archive_service_filter, $map_event, $service_map_postion);
        }


        echo '<div ' . $this->get_render_attribute_string('wrapper') . '>';
        echo '<div class="' . join(' ', $archive_classes) . '">';
        if ($settings['post_type'] == 'jobs') {
            felan_get_template('jobs/archive/layout/layout-default.php');
        } elseif ($settings['post_type'] == 'company') {
            felan_get_template('company/archive/layout/layout-default.php');
        } elseif ($settings['post_type'] == 'freelancer') {
            felan_get_template('freelancer/archive/layout/layout-default.php');
        } elseif ($settings['post_type'] == 'service') {
            felan_get_template('service/archive/layout/layout-default.php');
        }
        echo '</div>';
        echo '</div>';
    }
}
