<?php

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Plugin;

defined('ABSPATH') || exit;

Plugin::instance()->widgets_manager->register(new Widget_Freelancer_Box());

class Widget_Freelancer_Box extends Widget_Base
{

    const QUERY_CONTROL_ID = 'query';
    const QUERY_OBJECT_POST = 'post';

    public function get_post_type()
    {
        return 'freelancer';
    }

    public function get_name()
    {
        return 'felan-freelancer-box';
    }

    public function get_title()
    {
        return esc_html__('Freelancer Box', 'felan-framework');
    }

    public function get_icon()
    {
        return 'felan-badge eicon-preferences';
    }

    public function get_keywords()
    {
        return ['freelancer', 'carousel'];
    }

    public function get_style_depends()
    {
        return [FELAN_PLUGIN_PREFIX . 'freelancer-box'];
    }

    protected function register_controls()
    {
        $this->register_layout_section();
        $this->register_query_section();
        $this->register_slider_section();
        $this->register_layout_style_section();
    }

    private function register_layout_section()
    {
        $this->start_controls_section('layout_section', [
            'label' => esc_html__('Layout', 'felan-framework'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control(
            'enable_slider',
            [
                'label' => esc_html__('Enable Slider', 'felan-framework'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label' => esc_html__('Columns', 'felan-framework'),
                'type' => Controls_Manager::NUMBER,
                'prefix_class' => 'elementor-grid%s-',
                'min' => 1,
                'max' => 4,
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
                    'enable_slider!' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label' => esc_html__('Posts Per Page', 'felan-framework'),
                'type' => Controls_Manager::NUMBER,
                'default' => 6,
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
                    '{{WRAPPER}} .elementor-carousel .felan-freelancer-item' => 'padding-left: calc({{SIZE}}{{UNIT}}/2); padding-right: calc({{SIZE}}{{UNIT}}/2)',
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
                    '{{WRAPPER}} .elementor-carousel .felan-freelancer-item' => 'padding-top: calc({{SIZE}}{{UNIT}}/2); padding-bottom: calc({{SIZE}}{{UNIT}}/2)',
                    '{{WRAPPER}} .slick-list' => 'margin-top: calc(-{{SIZE}}{{UNIT}}/2);margin-bottom: calc(-{{SIZE}}{{UNIT}}/2)',
                    '{{WRAPPER}} .elementor-grid' => 'grid-row-gap: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->end_controls_section();
    }

    private function register_query_section()
    {
        $this->start_controls_section('query_section', [
            'label' => esc_html__('Query', 'felan-framework'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control(
            'type_query',
            [
                'label' => esc_html__('Filter', 'felan-framework'),
                'type' => Controls_Manager::SELECT,
                'default' => 'orderby',
                'options' => [
                    'title' => esc_html__('Title', 'felan-framework'),
                    'orderby' => esc_html__('Orderby', 'felan-framework'),
                    'taxonomy' => esc_html__('Taxonomy', 'felan-framework'),
                ],
            ]
        );

        $taxonomies = array(
            "Categories" => "freelancer_categories",
            "Experience Level" => "freelancer_yoe",
            "Qualification" => "freelancer_qualification",
            "Ages" => "freelancer_ages",
            "Skills" => "freelancer_skills",
            "Languages" => "freelancer_languages",
            "Gender" => "freelancer_gender",
        );

        foreach ($taxonomies as $label_taxonomy => $taxonomy) {
            $categories = get_terms([
                'taxonomy' => $taxonomy,
                'hide_empty' => true,
            ]);

            $options = array();
            foreach ($categories as $category) {
                if (!empty($category) && $category->slug != 'uncategorized') {
                    $options[$category->term_id] = $category->name;
                }
            }

            $this->add_control($taxonomy, [
                'label' => esc_html__($label_taxonomy, 'felan-framework'),
                'type' => Controls_Manager::SELECT2,
                'options' => $options,
                'default' => [],
                'label_block' => true,
                'multiple' => true,
                'condition' => [
                    'type_query' => 'taxonomy',
                ],
            ]);
        }

        $this->add_control(
            'orderby',
            [
                'label' => esc_html__('Order By', 'felan-framework'),
                'type' => Controls_Manager::SELECT,
                'default' => 'newest',
                'options' => [
                    'featured' => esc_html__('Featured', 'felan-framework'),
                    'oldest' => esc_html__('Oldest', 'felan-framework'),
                    'newest' => esc_html__('Newest', 'felan-framework'),
                    'random' => esc_html__('Random', 'felan-framework'),
                ],
                'condition' => [
                    'type_query' => 'orderby',
                ],
            ]
        );

        $options_freelancer = [];
        $args_freelancer = array(
            'post_type' => $this->get_post_type(),
            'ignore_sticky_posts' => 1,
            'post_status' => 'publish',
        );

        $data_freelancer = new \WP_Query($args_freelancer);
        if ($data_freelancer->have_posts()) {
            while ($data_freelancer->have_posts()) : $data_freelancer->the_post();
                $id = get_the_id();
                $title = get_the_title($id);
                $options_freelancer[$id] = $title;
            endwhile;
        }
        wp_reset_postdata();

        $this->add_control('include_ids', [
            'label' => esc_html__('Search & Select', 'felan-framework'),
            'type' => Controls_Manager::SELECT2,
            'options' => $options_freelancer,
            'default' => [],
            'label_block' => true,
            'multiple' => true,
            'condition' => [
                'type_query' => 'title',
            ],
        ]);

        $this->end_controls_section();
    }

    private function register_slider_section()
    {
        $this->start_controls_section('slider_section', [
            'label' => esc_html__('Slider', 'felan-framework'),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => [
                'enable_slider' => 'yes',
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

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'box_background',
                'label' => esc_html__('Background', 'felan-framework'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .felan-freelancer-item',
            ]
        );

        $this->add_control('box_padding', [
            'label' => esc_html__('Padding', 'felan-framework'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .felan-freelancer-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_control('layout_border_radius', [
            'label' => esc_html__('Border Radius', 'felan-framework'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .felan-freelancer-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'layout_border',
                'selector' => '{{WRAPPER}} .felan-freelancer-item',
            ]
        );

        $this->add_control(
            'heading_title',
            [
                'label' => __('Title', 'felan-framework'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Text Color', 'felan-framework'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .freelancers-title a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name'     => 'title',
            'selector' => '{{WRAPPER}} .freelancers-title',
        ]);

        $this->add_control(
            'heading_position',
            [
                'label' => __('Position', 'felan-framework'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'position_color',
            [
                'label' => esc_html__('Text Color', 'felan-framework'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .freelancer-current-position' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name'     => 'position',
            'selector' => '{{WRAPPER}} .freelancer-current-position',
        ]);

        $this->end_controls_section();
    }

    protected function render()
    {
        $is_rtl = is_rtl();
        $direction = $is_rtl ? 'rtl' : 'ltr';
        $settings = $this->get_settings_for_display();
        $this->add_render_attribute('wrapper', 'class', 'felan-freelancer');
        $args = array(
            'posts_per_page' => $settings['posts_per_page'],
            'post_type' => 'freelancer',
            'ignore_sticky_posts' => 1,
            'post_status' => 'publish',
        );

        //Query
        $tax_query = array(
            array(
                'key' => 'felan-enable_freelancer_package_expires',
                'value' => 0,
                'compare' => '=='
            )
        );
        $meta_query = array();

        if (!empty($settings['include_ids']) && $settings['type_query'] == 'title') {
            $args['post__in'] = $settings['include_ids'];
        }

        if ($settings['type_query'] == 'orderby') {
            if (!empty($settings['orderby'])) {
                if ($settings['orderby'] == 'featured') {
                    $meta_query[] = array(
                        'key' => FELAN_METABOX_PREFIX . 'freelancer_featured',
                        'value' => 1,
                        'type' => 'NUMERIC',
                        'compare' => '=',
                    );
                }
                if ($settings['orderby'] == 'oldest') {
                    $args['orderby'] = array(
                        'menu_order' => 'DESC',
                        'date' => 'ASC',
                    );
                }
                if ($settings['orderby'] == 'newest') {
                    $args['orderby'] = array(
                        'menu_order' => 'ASC',
                        'date' => 'DESC',
                    );
                }
                if ($settings['orderby'] == 'random') {
                    $args['meta_key'] = '';
                    $args['orderby'] = 'rand';
                    $args['order'] = 'ASC';
                }
            }
        }

        $filters = array();
        if ($settings['type_query'] == 'taxonomy') {
            $taxonomies = array("freelancer_categories", "freelancer_yoe", "freelancer_qualification", "freelancer_ages", "freelancer_skills", "freelancer_languages", "freelancer_gender");
            foreach ($taxonomies as $taxonomy) {
                if (!empty($settings[$taxonomy])) {
                    $tax_query[] = array(
                        'taxonomy' => $taxonomy,
                        'field' => 'term_id',
                        'terms' => $settings[$taxonomy],
                    );
                    $filters[$taxonomy] = $settings[$taxonomy];
                }
            }
        }

        if (!empty($tax_query)) {
            $args['tax_query'] = array(
                'relation' => 'AND',
                $tax_query
            );
        }

        if (!empty($meta_query)) {
            $args['meta_query'] = array(
                'relation' => 'AND',
                $meta_query
            );
        }

        $data = new \WP_Query($args);
        $total_post = $data->found_posts;

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
?>
        <div <?php echo $this->get_render_attribute_string('wrapper') ?>>
            <?php if ($data->have_posts()) { ?>
                <?php if ($settings['enable_slider'] == 'yes') { ?>
                    <div class="elementor-slick-slider" dir="<?php echo esc_attr($direction); ?>">
                        <div <?php echo $this->get_render_attribute_string('slides'); ?>>
                            <?php while ($data->have_posts()) : $data->the_post();
                                $this->print_freelancer_content();
                            endwhile; ?>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="elementor-grid-freelancer" dir="<?php echo esc_attr($direction); ?>">
                        <div class="elementor-grid">
                            <?php while ($data->have_posts()) : $data->the_post();
                                $this->print_freelancer_content();
                            endwhile; ?>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="item-not-found"><?php esc_html_e('No item found', 'felan-framework'); ?></div>
            <?php } ?>
            <input type="hidden" name="item_amount" value="<?php echo $settings['posts_per_page'] ?>">
            <input type="hidden" name="include_ids" value='<?php echo json_encode($settings['include_ids']) ?>'>
            <input type="hidden" name="type_query" value="<?php echo $settings['type_query'] ?>">
            <input type="hidden" name="orderby" value="<?php echo $settings['orderby'] ?>">
        </div>
    <?php }

    private function print_freelancer_content()
    { ?>
        <div class="felan-freelancer-item">
            <div class="freelancer-item-inner">
                <?php
                $freelancer_id = get_the_ID();
                $author_id = get_post_field('post_author', $freelancer_id);
                $freelancer_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
                $freelancer_featured = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_featured', true);
                $freelancer_current_position = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_current_position', true);
                ?>
                <a class="freelancer-img" href="<?php echo get_the_permalink($freelancer_id); ?>">
                    <?php if (!empty($freelancer_avatar)) : ?>
                        <img class="freelancer-avatar" src="<?php echo esc_attr($freelancer_avatar) ?>" alt="" />
                    <?php else : ?>
                        <div class="freelancer-avatar"><i class="far fa-camera"></i></div>
                    <?php endif; ?>
                </a>
                <div class="content">
                    <?php if (!empty(get_the_title($freelancer_id))) : ?>
                        <h2 class="freelancers-title">
                            <a href="<?php echo get_the_permalink($freelancer_id); ?>"><?php echo get_the_title($freelancer_id); ?></a>
                            <?php if ($freelancer_featured == 1) : ?>
                                <span class="tooltip" data-title="<?php echo esc_attr('Featured', 'felan-framework') ?>"><i class="far fa-check"></i></span>
                            <?php endif; ?>
                        </h2>
                    <?php endif; ?>
                    <?php if (!empty($freelancer_current_position)) { ?>
                        <div class="freelancer-current-position">
                            <?php esc_html_e($freelancer_current_position); ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
<?php }
}
