<?php

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Plugin;

defined('ABSPATH') || exit;

Plugin::instance()->widgets_manager->register(new Widget_Category_Carousel());

class Widget_Category_Carousel extends Widget_Base
{

    public function get_name()
    {
        return 'felan-category-carousel';
    }

    public function get_title()
    {
        return esc_html__('Category Carousel', 'felan-framework');
    }

    public function get_icon()
    {
        return 'felan-badge eicon-slider-push';
    }

    public function get_keywords()
    {
        return ['category', 'carousel'];
    }

    public function get_style_depends()
    {
        return [FELAN_PLUGIN_PREFIX . 'category-carousel'];
    }

    protected function register_controls()
    {
        $this->register_layout_section();
        $this->register_content_jobs_section();
        $this->register_content_company_section();
        $this->register_content_project_section();
        $this->register_content_freelancer_section();
        $this->register_content_service_section();
        $this->register_slider_section();
        $this->register_layout_section_style();
    }

    private function register_layout_section()
    {
        $this->start_controls_section('layout_section', [
            'label' => esc_html__('Layout', 'felan-framework'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control(
            'post_type',
            [
                'label' => esc_html__('Post Type', 'felan-framework'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'jobs' => esc_html__('Jobs', 'felan-framework'),
                    'company' => esc_html__('Company', 'felan-framework'),
                    'project' => esc_html__('Project', 'felan-framework'),
                    'freelancer' => esc_html__('Freelancer', 'felan-framework'),
                    'service' => esc_html__('Service', 'felan-framework'),
                ],
                'default' => 'jobs',
            ]
        );

        $this->end_controls_section();
    }

    private function register_content_jobs_section()
    {
        $this->start_controls_section('content_jobs_section', [
            'label' => esc_html__('Content', 'felan-framework'),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => [
                'post_type' => 'jobs',
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
            $categories[$category->term_id] = $category->name;
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
            'categories_jobs_list',
            [
                'label' => '',
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
            ]
        );

        $this->end_controls_section();
    }

    private function register_content_company_section()
    {
        $this->start_controls_section('content_company_section', [
            'label' => esc_html__('Content', 'felan-framework'),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => [
                'post_type' => 'company',
            ],
        ]);

        $repeater = new Repeater();

        $taxonomy_terms = get_categories(
            array(
                'taxonomy' => 'company-categories',
                'orderby' => 'name',
                'order' => 'ASC',
                'hide_empty' => false,
            )
        );

        $categories = [];
        foreach ($taxonomy_terms as $category) {
            $categories[$category->term_id] = $category->name;
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
            'categories_company_list',
            [
                'label' => '',
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
            ]
        );

        $this->end_controls_section();
    }

    private function register_content_project_section()
    {
        $this->start_controls_section('content_project_section', [
            'label' => esc_html__('Content', 'felan-framework'),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => [
                'post_type' => 'project',
            ],
        ]);

        $repeater = new Repeater();

        $taxonomy_terms = get_categories(
            array(
                'taxonomy' => 'project-categories',
                'orderby' => 'name',
                'order' => 'ASC',
                'hide_empty' => false,
            )
        );

        $categories = [];
        foreach ($taxonomy_terms as $category) {
            $categories[$category->term_id] = $category->name;
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
            'categories_project_list',
            [
                'label' => '',
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
            ]
        );

        $this->end_controls_section();
    }

    private function register_content_freelancer_section()
    {
        $this->start_controls_section('content_freelancer_section', [
            'label' => esc_html__('Content', 'felan-framework'),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => [
                'post_type' => 'freelancer',
            ],
        ]);

        $repeater = new Repeater();

        $taxonomy_terms = get_categories(
            array(
                'taxonomy' => 'freelancer_categories',
                'orderby' => 'name',
                'order' => 'ASC',
                'hide_empty' => false,
            )
        );

        $categories = [];
        foreach ($taxonomy_terms as $category) {
            $categories[$category->term_id] = $category->name;
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
            'categories_freelancer_list',
            [
                'label' => '',
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
            ]
        );

        $this->end_controls_section();
    }

    private function register_content_service_section()
    {
        $this->start_controls_section('content_service_section', [
            'label' => esc_html__('Content', 'felan-framework'),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => [
                'post_type' => 'service',
            ],
        ]);

        $repeater = new Repeater();

        $taxonomy_terms = get_categories(
            array(
                'taxonomy' => 'service-categories',
                'orderby' => 'name',
                'order' => 'ASC',
                'hide_empty' => false,
            )
        );

        $categories = [];
        foreach ($taxonomy_terms as $category) {
            $categories[$category->term_id] = $category->name;
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
            'categories_service_list',
            [
                'label' => '',
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
            ]
        );

        $this->end_controls_section();
    }

    private function register_slider_section()
    {
        $this->start_controls_section('slider_section', [
            'label' => esc_html__('Slider', 'felan-framework'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_responsive_control(
            'slides_to_show',
            [
                'label' => esc_html__('Slides to Show', 'felan-framework'),
                'type' => Controls_Manager::SELECT,
                'default' => '5',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                ]
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
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                ]
            ]
        );

        $this->add_control(
            'navigation',
            [
                'label' => esc_html__('Navigation', 'felan-framework'),
                'type' => Controls_Manager::SELECT,
                'default' => 'arrows',
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
                'default' => '',
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

    private function register_layout_section_style()
    {
        $this->start_controls_section('layout_section_style', [
            'label' => esc_html__('Layout', 'felan-framework'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control('item_padding', [
            'label' => esc_html__('Item Padding', 'felan-framework'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px'],
            'selectors' => [
                '{{WRAPPER}} .category-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('border_radius', [
            'label' => esc_html__('Border Radius', 'felan-framework'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .category-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_control(
            'border_color',
            [
                'label'  => esc_html__('Border Color', 'felan-framework'),
                'type'   => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .category-item' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'label'    => esc_html__('Typography', 'felan-framework'),
                'selector' => '{{WRAPPER}} .category-title',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label'  => esc_html__('Title Color', 'felan-framework'),
                'type'   => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .category-title' => 'color: {{VALUE}};',
                ],
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

?>

        <div class="elementor-slick-slider category-carousel" dir="<?php echo esc_attr($direction); ?>">
            <div <?php echo $this->get_render_attribute_string('slides'); ?>>
                <?php
                if ($settings['categories_' . $settings['post_type'] . '_list']) {
                    foreach ($settings['categories_' . $settings['post_type'] . '_list'] as $item) {
                        $category_id = $item['category'];
                        $category_image = $item['image'];
                        switch ($settings['post_type']) {
                            case 'jobs':
                                $taxonomy = 'jobs-categories';
                                break;

                            case 'service':
                                $taxonomy = 'service-categories';
                                break;

                            case 'freelancer':
                                $taxonomy = 'freelancer_categories';
                                break;

                            case 'project':
                                $taxonomy = 'project-categories';
                                break;

                            case 'company':
                                $taxonomy = 'company-categories';
                                break;

                            default:
                                $taxonomy = 'jobs-categories';
                                break;
                        }
                        $term = get_term($category_id, $taxonomy);
                        if (!is_wp_error($term) && !empty($term)) {
                            $term_url = get_term_link($term->term_id, $taxonomy);
                            echo '<div class="elementor-slick-item">';
                            echo '<div class="category-item">';
                            echo '<div class="category-content">';
                            echo '<img class="category-icon" src="' . esc_url($category_image['url']) . '">';
                            echo '<h4 class="category-title"><a href="' . esc_url($term_url) . '">' . esc_html($term->name) . '</a></h4>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    }
                }
                ?>
            </div>
    <?php }
}
