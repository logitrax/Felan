<?php

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Plugin;

defined('ABSPATH') || exit;

Plugin::instance()->widgets_manager->register(new Widget_Jobs_Category());

class Widget_Jobs_Category extends Widget_Base
{

    const QUERY_CONTROL_ID = 'query';
    const QUERY_OBJECT_POST = 'post';

    public function get_post_type()
    {
        return 'jobs';
    }

    public function get_name()
    {
        return 'felan-jobs-category';
    }

    public function get_title()
    {
        return esc_html__('Jobs Category', 'felan-framework');
    }

    public function get_icon()
    {
        return 'felan-badge eicon-folder-o';
    }

    public function get_keywords()
    {
        return ['jobs', 'category', 'carousel'];
    }

    public function get_style_depends()
    {
        return [FELAN_PLUGIN_PREFIX . 'jobs-category'];
    }

    protected function register_controls()
    {
        $this->register_layout_section();
        $this->register_slider_section();
        $this->register_layout_style_section();
        $this->register_title_style_section();
        $this->register_icon_style_section();
        $this->register_count_style_section();
    }

    private function register_layout_section()
    {
        $this->start_controls_section('layout_section', [
            'label' => esc_html__('Layout', 'felan-framework'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control(
            'layout',
            [
                'label' => esc_html__('Layout', 'felan-framework'),
                'type' => Controls_Manager::SELECT,
                'default' => '01',
                'options' => [
                    '01' => esc_html__('01', 'felan-framework'),
                    '02' => esc_html__('02', 'felan-framework'),
                    '03' => esc_html__('03', 'felan-framework'),
                ],
                'prefix_class' => 'felan-layout-',
            ]
        );

        $this->add_control('position', [
            'label'        => esc_html__('Position', 'felan-framework'),
            'type'         => Controls_Manager::CHOOSE,
            'default'      => 'top',
            'options'      => [
                'left'  => [
                    'title' => esc_html__('Left', 'felan-framework'),
                    'icon'  => 'eicon-h-align-left',
                ],
                'top'   => [
                    'title' => esc_html__('Top', 'felan-framework'),
                    'icon'  => 'eicon-v-align-top',
                ],
                'right' => [
                    'title' => esc_html__('Right', 'felan-framework'),
                    'icon'  => 'eicon-h-align-right',
                ],
            ],
            'prefix_class' => 'elementor-position-',
            'condition' => [
                'layout' => '01',
            ],
        ]);

        $repeater = new Repeater();

        $taxonomy_terms = get_categories(
            array(
                'taxonomy' => 'jobs-categories',
                'orderby' => 'name',
                'order' => 'ASC',
                'hide_empty' => false,
            )
        );

        $categories = [];
        foreach ($taxonomy_terms as $category) {
            $categories[$category->slug] = $category->name;
        }
        $repeater->add_control(
            'category',
            [
                'label' => esc_html__('Categories', 'felan-framework'),
                'type' => Controls_Manager::SELECT,
                'options' => $categories,
                'label_block' => true,
            ]
        );

        $repeater->add_control('image', [
            'label'   => esc_html__('Choose Image', 'felan-framework'),
            'type'    => Controls_Manager::MEDIA,
        ]);

        $this->add_control(
            'categories_list',
            [
                'label' => '',
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'text' => esc_html__('Category #1', 'felan-framework'),
                    ],
                    [
                        'text' => esc_html__('Category #2', 'felan-framework'),
                    ],
                    [
                        'text' => esc_html__('Category #3', 'felan-framework'),
                    ],
                ],
                //                'title_field' => '{{{ category }}}',
            ]
        );

        $this->add_control(
            'show_icon',
            [
                'label' => esc_html__('Show Icon', 'felan-framework'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_count',
            [
                'label' => esc_html__('Show Count', 'felan-framework'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_description',
            [
                'label' => esc_html__('Show Description', 'felan-framework'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'condition' => [
                    'layout' => '01',
                ],
            ]
        );

        $this->add_control(
            'show_list_cate',
            [
                'label' => esc_html__('Show List Categories', 'felan-framework'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'condition' => [
                    'show_slider!' => 'yes',
                    'layout' => '01',
                ],
                'separator'  => 'before',
            ]
        );

        $this->add_control('text_cate', [
            'label'       => esc_html__('Text', 'felan-framework'),
            'type'        => Controls_Manager::TEXT,
            'default'     => esc_html__('View all categories', 'felan-framework'),
            'condition' => [
                'show_list_cate!' => '',
                'show_slider!' => 'yes',
                'layout' => '01',
            ],
        ]);

        $this->add_control('link_cate', [
            'label'     => esc_html__('Link', 'felan-framework'),
            'type'      => Controls_Manager::URL,
            'dynamic'   => [
                'active' => true,
            ],
            'default'   => [
                'url' => '',
            ],
            'condition' => [
                'show_list_cate!' => '',
                'show_slider!' => 'yes',
                'layout' => '01',
            ],
        ]);

        $this->add_control(
            'show_slider',
            [
                'label' => esc_html__('Show Slider', 'felan-framework'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'separator'  => 'before',
                'condition' => [
                    'layout' => '01',
                ],
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label' => esc_html__('Columns', 'felan-framework'),
                'type' => Controls_Manager::NUMBER,
                'prefix_class' => 'elementor-grid%s-',
                'min' => 1,
                'max' => 8,
                'default' => 2,
                'required' => true,
                'device_args' => [
                    Controls_Stack::RESPONSIVE_TABLET => [
                        'required' => false,
                    ],
                    Controls_Stack::RESPONSIVE_MOBILE => [
                        'required' => false,
                    ],
                ],
                'min_affected_device' => [
                    Controls_Stack::RESPONSIVE_DESKTOP => Controls_Stack::RESPONSIVE_TABLET,
                    Controls_Stack::RESPONSIVE_TABLET => Controls_Stack::RESPONSIVE_TABLET,
                ],
                'condition' => [
                    'show_slider!' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'column_gap',
            [
                'label' => __('Columns Gap', 'felan-framework'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 30,
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-carousel .list-cate-item' => 'padding-left: calc({{SIZE}}{{UNIT}}/2); padding-right: calc({{SIZE}}{{UNIT}}/2)',
                    '{{WRAPPER}} .slick-list' => 'margin-left: calc(-{{SIZE}}{{UNIT}}/2);margin-right: calc(-{{SIZE}}{{UNIT}}/2)',
                    '{{WRAPPER}} .elementor-grid' => 'grid-column-gap: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'row_gap',
            [
                'label' => esc_html__('Rows Gap', 'felan-framework'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 30,
                ],
                'frontend_available' => true,
                'selectors' => [
                    '{{WRAPPER}} .elementor-carousel .list-cate-item' => 'padding-top: calc({{SIZE}}{{UNIT}}/2); padding-bottom: calc({{SIZE}}{{UNIT}}/2)',
                    '{{WRAPPER}} .slick-list' => 'margin-top: calc(-{{SIZE}}{{UNIT}}/2);margin-bottom: calc(-{{SIZE}}{{UNIT}}/2)',
                    '{{WRAPPER}} .elementor-grid' => 'grid-row-gap: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->end_controls_section();
    }

    private function register_slider_section()
    {
        $this->start_controls_section('slider_section', [
            'label' => esc_html__('Slider', 'felan-framework'),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => [
                'show_slider' => 'yes',
            ],
        ]);

        $slides_to_show = range(1, 10);
        $slides_to_show = array_combine($slides_to_show, $slides_to_show);

        $this->add_responsive_control(
            'slides_to_show',
            [
                'label' => esc_html__('Slides to Show', 'felan-framework'),
                'type' => Controls_Manager::SELECT,
                'default' => '2',
                'options' => [
                    '' => esc_html__('Default', 'felan-framework'),
                ] + $slides_to_show,
            ]
        );

        $this->add_responsive_control(
            'slides_to_scroll',
            [
                'label' => esc_html__('Slides to Scroll', 'felan-framework'),
                'type' => Controls_Manager::SELECT,
                'description' => esc_html__('Set how many slides are scrolled per swipe.', 'felan-framework'),
                'default' => '1',
                'options' => [
                    '' => esc_html__('Default', 'felan-framework'),
                ] + $slides_to_show,
            ]
        );

        $this->add_control(
            'slides_number_row',
            [
                'label' => esc_html__('Number Row', 'felan-framework'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 4,
                'default' => 1,
            ]
        );

        $this->add_control(
            'navigation',
            [
                'label' => esc_html__('Navigation', 'felan-framework'),
                'type' => Controls_Manager::SELECT,
                'default' => 'both',
                'options' => [
                    'both' => esc_html__('Arrows and Dots', 'felan-framework'),
                    'arrows' => esc_html__('Arrows', 'felan-framework'),
                    'dots' => esc_html__('Dots', 'felan-framework'),
                    'none' => esc_html__('None', 'felan-framework'),
                ],
            ]
        );

        $this->add_control(
            'center_mode',
            [
                'label' => esc_html__('Center Mode', 'felan-framework'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'pause_on_hover',
            [
                'label' => esc_html__('Pause on Hover', 'felan-framework'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label' => esc_html__('Autoplay', 'felan-framework'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'autoplay_speed',
            [
                'label' => esc_html__('Autoplay Speed', 'felan-framework'),
                'type' => Controls_Manager::NUMBER,
                'default' => 5000,
                'condition' => [
                    'autoplay' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-slide-bg' => 'animation-duration: calc({{VALUE}}ms*1.2); transition-duration: calc({{VALUE}}ms)',
                ],
            ]
        );

        $this->add_control(
            'infinite',
            [
                'label' => esc_html__('Infinite Loop', 'felan-framework'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'transition',
            [
                'label' => esc_html__('Transition', 'felan-framework'),
                'type' => Controls_Manager::SELECT,
                'default' => 'slide',
                'options' => [
                    'slide' => esc_html__('Slide', 'felan-framework'),
                    'fade' => esc_html__('Fade', 'felan-framework'),
                ],
            ]
        );

        $this->add_control(
            'transition_speed',
            [
                'label' => esc_html__('Transition Speed', 'felan-framework') . ' (ms)',
                'type' => Controls_Manager::NUMBER,
                'default' => 500,
            ]
        );

        $this->end_controls_section();
    }

    private function register_layout_style_section()
    {
        $this->start_controls_section(
            'section_layout_style',
            [
                'label' => esc_html__('Layout', 'felan-framework'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control('content_text_align', [
            'label'        => esc_html__('Text Align', 'felan-framework'),
            'type'         => Controls_Manager::CHOOSE,
            'default'      => 'left',
            'options'      => array(
                'left'   => [
                    'title' => esc_html__('Left', 'felan-framework'),
                    'icon'  => 'eicon-text-align-left',
                ],
                'center' => [
                    'title' => esc_html__('Center', 'felan-framework'),
                    'icon'  => 'eicon-text-align-center',
                ],
                'right'  => [
                    'title' => esc_html__('Right', 'felan-framework'),
                    'icon'  => 'eicon-text-align-right',
                ],
            ),
            'selectors'    => [
                '{{WRAPPER}} .list-cate-item' => 'text-align: {{VALUE}};',
            ],
        ]);

        $this->add_control(
            'overlay_color',
            [
                'label' => esc_html__('Overlay Color', 'felan-framework'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .has-image .cate-inner:before' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'box_background',
                'label' => esc_html__('Background', 'felan-framework'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .cate-inner',
            ]
        );

        $this->add_responsive_control('box_padding', [
            'label' => esc_html__('Padding', 'felan-framework'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .cate-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('layout_border_radius', [
            'label' => esc_html__('Border Radius', 'felan-framework'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .felan-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .cate-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'layout_border',
                'selector' => '{{WRAPPER}} .cate-inner',
            ]
        );

        $this->end_controls_section();
    }

    private function register_title_style_section()
    {
        $this->start_controls_section(
            'section_title_style',
            [
                'label' => esc_html__('Title', 'felan-framework'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_spacing',
            [
                'label' => esc_html__('Spacing', 'felan-framework'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .cate-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label'  => esc_html__('Text Color', 'felan-framework'),
                'type'   => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cate-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'label'    => esc_html__('Typography', 'felan-framework'),
                'selector' => '{{WRAPPER}} .cate-title',
            ]
        );

        $this->end_controls_section();
    }

    private function register_icon_style_section()
    {
        $this->start_controls_section(
            'section_icon_style',
            [
                'label' => esc_html__('Icon', 'felan-framework'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_icon' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'icon_spacing',
            [
                'label' => esc_html__('Spacing', 'felan-framework'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}.elementor-position-top .icon-cate' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}}.elementor-position-left .icon-cate' => 'margin-right: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}}.elementor-position-right .icon-cate' => 'margin-left: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_control(
            'icon_size',
            [
                'label' => esc_html__('Size', 'felan-framework'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .icon-cate' => 'font-size: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_control(
            'icon_width',
            [
                'label' => esc_html__('Width', 'felan-framework'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .icon-cate' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label'  => esc_html__('Text Color', 'felan-framework'),
                'type'   => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .icon-cate' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_bg_color',
            [
                'label'  => esc_html__('Background Color', 'felan-framework'),
                'type'   => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .icon-cate' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    private function register_count_style_section()
    {
        $this->start_controls_section(
            'section_count_style',
            [
                'label' => esc_html__('Count', 'felan-framework'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_count' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'count_color',
            [
                'label'  => esc_html__('Text Color', 'felan-framework'),
                'type'   => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cate-count' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'count_typography',
                'label'    => esc_html__('Typography', 'felan-framework'),
                'selector' => '{{WRAPPER}} .cate-count',
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $is_rtl = is_rtl();
        $direction = $is_rtl ? 'rtl' : 'ltr';
        $settings = $this->get_settings_for_display();

        //Slider
        $show_dots = (in_array($settings['navigation'], ['dots', 'both']));
        $show_arrows = (in_array($settings['navigation'], ['arrows', 'both']));

        if (empty($settings['slides_to_show_tablet'])) : $settings['slides_to_show_tablet'] = $settings['slides_to_show'];
        endif;
        if (empty($settings['slides_to_show_mobile'])) : $settings['slides_to_show_mobile'] = $settings['slides_to_show'];
        endif;
        if (empty($settings['slides_to_scroll_tablet'])) : $settings['slides_to_scroll_tablet'] = $settings['slides_to_scroll'];
        endif;
        if (empty($settings['slides_to_scroll_mobile'])) : $settings['slides_to_scroll_mobile'] = $settings['slides_to_scroll'];
        endif;

        $slick_options = [
            '"slidesToShow":' . absint($settings['slides_to_show']),
            '"slidesToScroll":' . absint($settings['slides_to_scroll']),
            '"autoplaySpeed":' . (isset($settings['autoplay_speed']) ? absint($settings['autoplay_speed']) : 3000),
            '"autoplay":' . (('yes' === $settings['autoplay']) ? 'true' : 'false'),
            '"infinite":' . (('yes' === $settings['infinite']) ? 'true' : 'false'),
            '"pauseOnHover":' . (('yes' === $settings['pause_on_hover']) ? 'true' : 'false'),
            '"centerMode":' . (('yes' === $settings['center_mode']) ? 'true' : 'false'),
            '"speed":' . absint($settings['transition_speed']),
            '"arrows":' . ($show_arrows ? 'true' : 'false'),
            '"dots":' . ($show_dots ? 'true' : 'false'),
            '"rtl":' . ($is_rtl ? 'true' : 'false'),
            '"rows":' . absint($settings['slides_number_row']),
            '"responsive": [{ "breakpoint":567, "settings":{ "slidesToShow":' . $settings["slides_to_show_mobile"] . ', "slidesToScroll":' . $settings["slides_to_scroll_mobile"] . '}},{ "breakpoint":767, "settings":{ "slidesToShow": 2, "slidesToScroll": 2} }, { "breakpoint":1024, "settings":{ "slidesToShow":' . $settings["slides_to_show_tablet"] . ', "slidesToScroll":' . $settings["slides_to_scroll_tablet"] . ' } } ]',
        ];
        $slick_data = '{' . implode(', ', $slick_options) . '}';

        if ('fade' === $settings['transition']) {
            $slick_options['fade'] = true;
        }

        $carousel_classes = ['elementor-carousel'];
        $this->add_render_attribute('slides', [
            'class' => $carousel_classes,
            'data-slider_options' => $slick_data,
        ]);

        $link_cate = '#';
        if (!empty($settings['link_cate']['url'])) {
            $link_cate = $settings['link_cate']['url'];
        }

?>
        <?php if ($settings['show_slider'] == 'yes') { ?>
            <div class="elementor-slick-slider" dir="<?php echo esc_attr($direction); ?>">
                <div <?php echo $this->get_render_attribute_string('slides'); ?>>
                <?php } else { ?>
                    <div class="elementor-grid-jobs" dir="<?php echo esc_attr($direction); ?>">
                        <div class="elementor-grid">
                        <?php } ?>
                        <?php foreach ($settings['categories_list'] as $categorry) {
                            $item_id = $categorry['_id'];
                            $item_key = 'item_' . $item_id;

                            $category_slug = $categorry['category'];
                            if (!empty($category_slug)) {
                                $cate = get_term_by('slug', $category_slug, 'jobs-categories');
                                if ($cate) {
                                    $term_name = $cate->name;
                                    $term_count = $cate->count;
                                    $term_link = get_term_link($cate, 'jobs-categories');
                                    $term_des = $cate->description;
                                }
                                $this->add_render_attribute($item_key, 'class', array(
                                    'list-cate-item',
                                    'elementor-repeater-item-' . $item_id,
                                ));
                        ?>
                                <div <?php echo $this->get_render_attribute_string($item_key); ?>>
                                    <div class="cate-inner">
                                        <div class="cate-content">
                                            <?php if (!empty($term_name)) : ?>
                                                <h4 class="cate-title"><a href="<?php echo esc_url($term_link); ?>"><?php esc_html_e($term_name); ?></a></h4>
                                            <?php endif; ?>
                                            <?php if (!empty($term_count) && $settings['show_count'] == 'yes') : ?>
                                                <p class="cate-count"><?php echo sprintf(esc_html__('%s jobs', 'felan-framework'), $term_count) ?></p>
                                            <?php endif; ?>
                                            <?php
                                            if ($settings['show_description'] == 'yes') { ?>
                                                <div class="cate-des"><?php esc_html_e($term_des); ?></div>
                                            <?php } ?>
                                        </div>
                                        <?php if (!empty($categorry['image']['url'])) { ?>
                                            <div class="felan-image image">
                                                <a href="<?php echo esc_url($term_link); ?>">
                                                    <?php echo \Felan_Image::get_elementor_attachment([
                                                        'settings' => $categorry,
                                                    ]); ?>
                                                </a>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                        <?php }
                        } ?>
                        <?php if ($settings['show_list_cate'] !== '' && !empty($settings['text_cate'] && $settings['show_slider'] !== 'yes')) { ?>
                            <?php if ($settings['text_style'] === 'yes') :  ?>
                        </div>
                        <div class="list-cate-item text-style">
                            <div class="cate-inner view-cate">
                                <a href="<?php echo $link_cate; ?>" class="felan-button button-border-bottom">
                                    <?php esc_html_e($settings['text_cate']) ?>
                                </a>
                            </div>
                        </div>
                    <?php else : ?>
                        <div class="list-cate-item <?php if ($settings['text_style'] === 'yes') {
                                                        echo ' text-style';
                                                    }  ?>">
                            <div class="cate-inner view-cate">
                                <a href="<?php echo $link_cate; ?>" class="felan-button button-border-bottom">
                                    <?php esc_html_e($settings['text_cate']) ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php } else { ?>
                </div>
            <?php } ?>
            </div>
    <?php }
}
