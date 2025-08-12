<?php

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Utils;

defined('ABSPATH') || exit;

Plugin::instance()->widgets_manager->register(new Widget_Search_Horizontal());

class Widget_Search_Horizontal extends Widget_Base
{

    public function get_name()
    {
        return 'felan-search-horizontal';
    }

    public function get_title()
    {
        return esc_html__('Search Horizontal PostTypes', 'felan-framework');
    }

    public function get_icon()
    {
        return 'felan-badge eicon-search';
    }

    public function get_keywords()
    {
        return ['jobs', 'companies', 'freelancer', 'search'];
    }

    public function get_script_depends()
    {
        return [FELAN_PLUGIN_PREFIX . 'search-horizontal', FELAN_PLUGIN_PREFIX . 'search-location', 'jquery-ui-autocomplete'];
    }

    public function get_style_depends()
    {
        return [FELAN_PLUGIN_PREFIX . 'search-horizontal'];
    }

    protected function register_controls()
    {
        $this->add_layout_section();
        $this->add_layout_jobs_section();
        $this->add_layout_companies_section();
        $this->add_layout_freelancers_section();
        $this->add_layout_service_section();
        $this->add_layout_style_section();
    }

    private function add_layout_section()
    {
        $this->start_controls_section('layout_section', [
            'label' => esc_html__('Layout', 'felan-framework'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('layout', [
            'label' => esc_html__('Layout', 'felan-framework'),
            'type' => Controls_Manager::SELECT,
            'options' => [

                '01' => esc_html__('Layout 01', 'felan-framework'),
            ],
            'default' => '01',
            'prefix_class' => 'felan-search-horizontal-layout-',
        ]);

        $this->add_control('post_type', [
            'label' => esc_html__('Post Type', 'felan-framework'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'jobs' => esc_html__('Jobs', 'felan-framework'),
                'company' => esc_html__('Companies', 'felan-framework'),
                'freelancer' => esc_html__('Freelancers', 'felan-framework'),
                'service' => esc_html__('Service', 'felan-framework'),
            ],
            'default' => 'jobs',
        ]);

        $this->add_control(
            'show_popular',
            [
                'label' => esc_html__('Show Popular', 'felan-framework'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $taxonomy_terms = get_categories(
            array(
                'taxonomy' => 'freelancer_skills',
                'hide_empty' => false,
                'orderby' => 'name',
                'order' => 'ASC'
            )
        );

        $categories = [];
        foreach ($taxonomy_terms as $term) {
            $categories[$term->term_id] = $term->name;
        }
        $this->add_control(
            'choose_options',
            [
                'label' => esc_html__('Choose Options', 'felan-framework'),
                'type' => Controls_Manager::SELECT2,
                'options' => $categories,
                'label_block' => true,
                'multiple'    => true,
                'condition' => [
                    'show_popular' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_arrow',
            [
                'label' => esc_html__('Show Arrow', 'felan-framework'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
            ]
        );

        $this->add_control(
            'show_clear',
            [
                'label' => esc_html__('Show Clear', 'felan-framework'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
            ]
        );

        $this->add_control(
            'show_redirect',
            [
                'label' => esc_html__('Show ajax page redirect', 'felan-framework'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
            ]
        );

        $this->add_control('link_redirect', [
            'label'     => esc_html__('Link', 'felan-framework'),
            'type'      => Controls_Manager::URL,
            'dynamic'   => [
                'active' => true,
            ],
            'default'   => [
                'url' => '',
            ],
            'condition' => [
                'show_redirect' => 'yes',
            ],
        ]);

        $this->end_controls_section();
    }

    private function add_layout_jobs_section()
    {
        $this->start_controls_section('layout_jobs', [
            'label' => esc_html__('Jobs', 'felan-framework'),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => [
                'post_type' => 'jobs',
            ],
        ]);

        $taxonomies_jobs = array(
            "Categories" => "jobs-categories",
            "Skills" => "jobs-skills",
            "Type" => "jobs-type",
            "Location" => "jobs-location",
            "Career" => "jobs-career",
            "Experience" => "jobs-experience",
        );

        foreach ($taxonomies_jobs as $label_jobs => $jobs) {
            $this->add_control(
                'show_' . $jobs,
                [
                    'label' => esc_html__('Show ' . $label_jobs, 'felan-framework'),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => '',
                ]
            );
            $this->add_control('icon_' . $jobs, [
                'label' => esc_html__('Icon ' . $label_jobs, 'felan-framework'),
                'type' => Controls_Manager::ICONS,
                'default' => [],
                'condition' => [
                    'show_' . $jobs => 'yes',
                ],
            ]);
        };

        $this->end_controls_section();
    }

    private function add_layout_companies_section()
    {
        $this->start_controls_section('layout_company', [
            'label' => esc_html__('Companies', 'felan-framework'),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => [
                'post_type' => 'company',
            ],
        ]);

        $taxonomies_company  = array(
            "Categories" => "company-categories",
            "Location" => "company-location",
            "Size" => "company-size",
        );

        foreach ($taxonomies_company as $label_company => $company) {
            $this->add_control(
                'show_' . $company,
                [
                    'label' => esc_html__('Show ' . $label_company, 'felan-framework'),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => '',
                ]
            );
            $this->add_control('icon_' . $company, [
                'label' => esc_html__('Icon ' . $label_company, 'felan-framework'),
                'type' => Controls_Manager::ICONS,
                'default' => [],
                'condition' => [
                    'show_' . $company => 'yes',
                ],
            ]);
        };

        $this->end_controls_section();
    }

    private function add_layout_freelancers_section()
    {
        $this->start_controls_section('layout_freelancer', [
            'label' => esc_html__('Freelancers', 'felan-framework'),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => [
                'post_type' => 'freelancer',
            ],
        ]);

        $taxonomies_freelancer = array(
            "Categories" => "freelancer_categories",
            "Ages" => "freelancer_ages",
            "Languages" => "freelancer_languages",
            "Qualification" => "freelancer_qualification",
            "Yoe" => "freelancer_yoe",
            "Education" => "freelancer_education_levels",
            "Skills" => "freelancer_skills",
            "Locations" => "freelancer_locations",
        );

        foreach ($taxonomies_freelancer as $label_freelancer => $freelancer) {
            $this->add_control(
                'show_' . $freelancer,
                [
                    'label' => esc_html__('Show ' . $label_freelancer, 'felan-framework'),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => '',
                ]
            );
            $this->add_control('icon_' . $freelancer, [
                'label' => esc_html__('Icon ' . $label_freelancer, 'felan-framework'),
                'type' => Controls_Manager::ICONS,
                'default' => [],
                'condition' => [
                    'show_' . $freelancer => 'yes',
                ],
            ]);
        };

        $this->end_controls_section();
    }

    private function add_layout_service_section()
    {
        $this->start_controls_section('layout_service', [
            'label' => esc_html__('Service', 'felan-framework'),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => [
                'post_type' => 'service',
            ],
        ]);

        $taxonomies_service = array(
            "Categories" => "service-categories",
            "Skills" => "service-skills",
            "Location" => "service-location",
            "Language" => "service-language",
        );

        foreach ($taxonomies_service as $label_service => $service) {
            $this->add_control(
                'show_' . $service,
                [
                    'label' => esc_html__('Show ' . $label_service, 'felan-framework'),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => '',
                ]
            );
            $this->add_control('icon_' . $service, [
                'label' => esc_html__('Icon ' . $label_service, 'felan-framework'),
                'type' => Controls_Manager::ICONS,
                'default' => [],
                'condition' => [
                    'show_' . $service => 'yes',
                ],
            ]);
        };

        $this->end_controls_section();
    }

    private function add_layout_style_section()
    {
        $this->start_controls_section('layout_style_section', [
            'label' => esc_html__('Layout', 'felan-framework'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control(
            'box_max-width',
            [
                'label' => esc_html__('Max Width', 'felan-framework'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .search-horizontal-inner' => 'max-width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control('text_align', [
            'label' => esc_html__('Alignment', 'felan-framework'),
            'type' => Controls_Manager::CHOOSE,
            'options' => array(
                'left' => [
                    'title' => esc_html__('Left', 'felan-framework'),
                    'icon' => 'eicon-text-align-left',
                ],
                'center' => [
                    'title' => esc_html__('Center', 'felan-framework'),
                    'icon' => 'eicon-text-align-center',
                ],
                'right' => [
                    'title' => esc_html__('Right', 'felan-framework'),
                    'icon' => 'eicon-text-align-right',
                ],
            ),
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .felan-search-horizontal' => 'text-align: {{VALUE}};',
            ],
        ]);

        $this->add_control(
            'text_color',
            [
                'label' => esc_html__('Text Color', 'felan-framework'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .popular-categories span' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => esc_html__('Categories Color', 'felan-framework'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .list-category a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'border_color',
            [
                'label' => esc_html__('Border Color', 'felan-framework'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .search-horizontal-inner' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_bg_color',
            [
                'label' => esc_html__('Button Background Color', 'felan-framework'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .btn-search-horizontal' => 'background-color: {{VALUE}};border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {

        $settings = $this->get_settings_for_display();
        $has_arrow = '';
        if ($settings['show_arrow'] == 'yes') {
            $has_arrow = 'has-arrow';
        }
        $this->add_render_attribute('wrapper', 'class', array(
            'felan-search-horizontal',
            $has_arrow,
        ));
        if ($settings['post_type'] == 'jobs') {
            $taxonomy_key = 'jobs-skills';
            $search_placeholder = esc_attr__('Jobs title or keywords', 'felan-framework');
            $taxonomies_field = array(
                esc_html__('Locations', 'felan-framework') => "jobs-location",
                esc_html__('Categories', 'felan-framework') => "jobs-categories",
                esc_html__('Skills', 'felan-framework') => "jobs-skills",
                esc_html__('Type', 'felan-framework') => "jobs-type",
                esc_html__('Career', 'felan-framework') => "jobs-career",
                esc_html__('Experience', 'felan-framework') => "jobs-experience",
            );
        } elseif ($settings['post_type'] == 'company') {
            $taxonomy_key = 'company-categories';
            $search_placeholder = esc_attr__('Company title or keywords', 'felan-framework');
            $taxonomies_field  = array(
                esc_html__('Locations', 'felan-framework') => "company-location",
                esc_html__('Categories', 'felan-framework') => "company-categories",
                esc_html__('Size', 'felan-framework') => "company-size",
            );
        } elseif ($settings['post_type'] == 'freelancer') {
            $taxonomy_key = 'freelancer_skills';
            $search_placeholder = esc_attr__('Freelancer title or keywords', 'felan-framework');
            $taxonomies_field = array(
                esc_html__('Locations', 'felan-framework') => "freelancer_locations",
                esc_html__('Categories', 'felan-framework') => "freelancer_categories",
                esc_html__('Ages', 'felan-framework') => "freelancer_ages",
                esc_html__('Languages', 'felan-framework') => "freelancer_languages",
                esc_html__('Qualification', 'felan-framework') => "freelancer_qualification",
                esc_html__('Yoe', 'felan-framework') => "freelancer_yoe",
                esc_html__('Education', 'felan-framework') => "freelancer_education_levels",
                esc_html__('Skills', 'felan-framework') => "freelancer_skills",
            );
        } elseif ($settings['post_type'] == 'service') {
            $taxonomy_key = 'service-skills';
            $search_placeholder = esc_attr__('Service title...', 'felan-framework');
            $taxonomies_field  = array(
                esc_html__('Location', 'felan-framework') => "service-location",
                esc_html__('Categories', 'felan-framework') => "service-categories",
                esc_html__('Skills', 'felan-framework') => "service-skills",
                esc_html__('Language', 'felan-framework') => "service-language",
            );
        }
        if ($settings['show_redirect'] == 'yes' && !empty($settings['link_redirect']['url'])) {
            $link_redirect = $settings['link_redirect']['url'] . '/';
        } else {
            $link_redirect = get_site_url();
        }
        $enable_search_location_radius = felan_get_option('enable_' . $settings['post_type'] . '_search_location_radius');
        $felan_distance_unit = felan_get_option('felan_distance_unit', 'km');
?>
        <div <?php echo $this->get_render_attribute_string('wrapper') ?>>
            <form action="<?php echo esc_url($link_redirect); ?>" method="get" class="form-search-horizontal">
                <div class="search-horizontal-inner">
                    <?php $key_name = array();
                    $taxonomy_post_type = get_categories(
                        array(
                            'taxonomy' => $taxonomy_key,
                            'orderby' => 'name',
                            'order' => 'ASC',
                            'hide_empty' => false,
                            'number' => 88,
                            'parent' => 0
                        )
                    );
                    if (!empty($taxonomy_post_type)) {
                        foreach ($taxonomy_post_type as $term) {
                            $key_name[] = $term->name;
                        }
                    }
                    $post_type_keyword = json_encode($key_name);
                    $id = apply_filters('felan/search-control/id', 'search-horizontal_filter_search');
                    ?>
                    <div class="form-group">
                        <input class="search-horizontal-control" data-key='<?php echo $post_type_keyword ?>' id="<?php echo esc_attr($id); ?>" type="text" name="s" placeholder="<?php echo $search_placeholder; ?>" autocomplete="off">
                        <span class="btn-filter-search"><i class="far fa-search"></i></span>
                    </div>

                    <?php foreach ($taxonomies_field as $label_field => $field) {
                        if ($settings['show_' . $field]) {
                            if ($field == 'jobs-location' || $field == 'company-location' || $field == 'freelancer_locations' || $field == 'service-location') { ?>
                                <div class="form-group felan-form-location">
                                    <input class="input-search-location" type="text" name="<?php echo $field ?>" placeholder="<?php esc_attr_e('All Cities', 'felan-framework') ?>" autocomplete="off">
                                    <?php do_action('felan_search_horizontal_after_location'); ?>
                                    <select class="felan-select2">
                                        <?php felan_get_taxonomy('felan_location', true, false); ?>
                                    </select>
                                    <span class="icon-location">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g clip-path="url(#clip0_8969_23265)">
                                                <path d="M13 1L13.001 4.062C14.7632 4.28479 16.4013 5.08743 17.6572 6.34351C18.9131 7.5996 19.7155 9.23775 19.938 11H23V13L19.938 13.001C19.7153 14.7631 18.9128 16.401 17.6569 17.6569C16.401 18.9128 14.7631 19.7153 13.001 19.938L13 23H11V19.938C9.23775 19.7155 7.5996 18.9131 6.34351 17.6572C5.08743 16.4013 4.28479 14.7632 4.062 13.001L1 13V11H4.062C4.28459 9.23761 5.08713 7.59934 6.34324 6.34324C7.59934 5.08713 9.23761 4.28459 11 4.062V1H13ZM12 6C10.4087 6 8.88258 6.63214 7.75736 7.75736C6.63214 8.88258 6 10.4087 6 12C6 13.5913 6.63214 15.1174 7.75736 16.2426C8.88258 17.3679 10.4087 18 12 18C13.5913 18 15.1174 17.3679 16.2426 16.2426C17.3679 15.1174 18 13.5913 18 12C18 10.4087 17.3679 8.88258 16.2426 7.75736C15.1174 6.63214 13.5913 6 12 6ZM12 10C12.5304 10 13.0391 10.2107 13.4142 10.5858C13.7893 10.9609 14 11.4696 14 12C14 12.5304 13.7893 13.0391 13.4142 13.4142C13.0391 13.7893 12.5304 14 12 14C11.4696 14 10.9609 13.7893 10.5858 13.4142C10.2107 13.0391 10 12.5304 10 12C10 11.4696 10.2107 10.9609 10.5858 10.5858C10.9609 10.2107 11.4696 10 12 10Z" fill="#999999" />
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_8969_23265">
                                                    <rect width="24" height="24" fill="white" />
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </span>
                                    <span class="icon-arrow">
                                        <i class="far fa-angle-down"></i>
                                    </span>
                                    <?php if ($enable_search_location_radius == 1) { ?>
                                        <span class="radius">
                                            <span class="labels"><?php esc_html_e('Radius:', 'felan-framework') ?></span>
                                            <input type="number" name="<?php echo $settings['post_type'] ?>_number_radius" value="25" placeholder="0" />
                                            <span class="distance"><?php echo esc_html($felan_distance_unit); ?></span>
                                        </span>
                                    <?php } ?>
                                    </span>
                                </div>
                            <?php } else { ?>
                                <div class="form-group">
                                    <?php Icons_Manager::render_icon($settings['icon_' . $field], ['aria-hidden' => 'true']); ?>
                                    <select name="<?php echo $field ?>" class="felan-select2">
                                        <option value=""><?php echo sprintf(esc_html__('All %s', 'felan-framework'), $label_field) ?></option>
                                        <?php felan_get_taxonomy($field, true, false); ?>
                                    </select>
                                </div>
                            <?php } ?>
                    <?php }
                    } ?>

                    <div class="form-group">
                        <?php if ($settings['show_clear'] == 'yes') { ?>
                            <span class="felan-clear-top-filter"><?php esc_html_e('Clear', 'felan-framework') ?></span>
                        <?php } ?>
                        <button type="submit" class="btn-search-horizontal felan-button">
                            <?php esc_html_e('Search', 'felan-framework') ?>
                        </button>
                    </div>
                </div>
                <?php if ($settings['show_redirect'] !== 'yes') { ?>
                    <input type="hidden" name="post_type" class="post-type" value="<?php echo $settings['post_type'] ?>">
                <?php } ?>
            </form>

            <?php if ($settings['show_popular'] == 'yes') { ?>
                <div class="popular-categories">
                    <span><?php esc_html_e('Popular Searches: ', 'felan-framework'); ?></span>
                    <ul class="list-category">
                        <?php
                        if ($settings['choose_options']) {
                            foreach ($settings['choose_options'] as $key => $value) {
                                $term = get_term($value);
                                if ($term) {
                                    $term_link = get_term_link($term);
                        ?>
                                    <li>
                                        <a href="<?php echo esc_url($term_link); ?>"><?php esc_html_e($term->name); ?></a>
                                    </li>
                                    <?php
                                }
                            }
                        } else {
                            $taxonomy_terms = get_categories(
                                array(
                                    'taxonomy' => $taxonomy_key,
                                    'order' => 'DESC',
                                    'orderby' => 'rand',
                                    'hide_empty' => false,
                                )
                            );
                            shuffle($taxonomy_terms);

                            if (!empty($taxonomy_terms)) {
                                foreach ($taxonomy_terms as $index => $term) {
                                    if ($index < 2) {
                                        $term_link = get_term_link($term);
                                    ?>
                                        <li>
                                            <a href="<?php echo esc_url($term_link); ?>"><?php esc_html_e($term->name); ?></a>
                                        </li>
                        <?php
                                    }
                                }
                            }
                        }
                        ?>
                    </ul>
                </div>
            <?php } ?>
        </div>
<?php }
}
