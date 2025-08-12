<?php

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Plugin;

defined('ABSPATH') || exit;

Plugin::instance()->widgets_manager->register(new Widget_Categories_List());

class Widget_Categories_List extends Widget_Base
{
    public function get_name()
    {
        return 'felan-categories-list';
    }

    public function get_title()
    {
        return esc_html__('Categories List', 'felan-framework');
    }

    public function get_icon()
    {
        return 'felan-badge eicon-archive';
    }

    public function get_keywords()
    {
        return ['list', 'category', 'categories'];
    }

    public function get_style_depends()
    {
        return [FELAN_PLUGIN_PREFIX . 'categories-list'];
    }

    protected function register_controls()
    {
        $this->register_layout_section();
        $this->register_job_categories_section();
        $this->register_company_categories_section();
        $this->register_freelancer_categories_section();
        $this->register_service_categories_section();
        $this->register_project_categories_section();
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
                ],
                'prefix_class' => 'felan-layout-',
            ]
        );

        $this->add_control('cate', [
            'label' => esc_html__('Type Categories', 'felan-framework'),
            'type' => Controls_Manager::SELECT,
            'options' =>  array(
                'jobs-categories' => esc_html__('Jobs Categories', 'felan-framework'),
                'company-categories' => esc_html__('Company Categories', 'felan-framework'),
                'freelancer_categories' => esc_html__('Freelancer Categories', 'felan-framework'),
                'service-categories' => esc_html__('Service Categories', 'felan-framework'),
                'project-categories' => esc_html__('Project Categories', 'felan-framework'),
            ),
            'default' => 'jobs-categories',
        ]);

        $this->end_controls_section();
    }

    private function register_job_categories_section()
    {
        $this->start_controls_section('categories_job_section', [
            'label' => esc_html__('Jobs Categories', 'felan-framework'),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => [
                'cate' => 'jobs-categories',
            ],
        ]);

        $repeater = new Repeater();

        $taxonomy_terms = get_categories(
            array(
                'taxonomy' => 'jobs-categories',
                'orderby' => 'name',
                'order' => 'ASC',
                'hide_empty' => 0,
                'parent' => 0,
            )
        );

        $categories = $category_slug = [];
        foreach ($taxonomy_terms as $category) {
            $categories[$category->slug] = $category->name;
        }

        $repeater->add_control(
            'jobs_selected_icon',
            [
                'label' => esc_html__('Icon', 'felan-framework'),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
                'default' => [
                    'value' => 'fas fa-star',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $repeater->add_control(
            'jobs_category',
            [
                'label' => esc_html__('Categories', 'felan-framework'),
                'type' => Controls_Manager::SELECT,
                'options' => $categories,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'jobs_categories_list',
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
            ]
        );

        $this->end_controls_section();
    }

    private function register_company_categories_section()
    {
        $this->start_controls_section('categories_company_section', [
            'label' => esc_html__('Company Categories', 'felan-framework'),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => [
                'cate' => 'company-categories',
            ],
        ]);

        $repeater = new Repeater();

        $taxonomy_terms = get_categories(
            array(
                'taxonomy' => 'company-categories',
                'orderby' => 'name',
                'order' => 'ASC',
                'hide_empty' => 0,
                'parent' => 0,
            )
        );

        $categories = $category_slug = [];
        foreach ($taxonomy_terms as $category) {
            $categories[$category->slug] = $category->name;
        }

        $repeater->add_control(
            'company_selected_icon',
            [
                'label' => esc_html__('Icon', 'felan-framework'),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
                'default' => [
                    'value' => 'fas fa-star',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $repeater->add_control(
            'company_category',
            [
                'label' => esc_html__('Categories', 'felan-framework'),
                'type' => Controls_Manager::SELECT,
                'options' => $categories,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'company_categories_list',
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
            ]
        );

        $this->end_controls_section();
    }

    private function register_freelancer_categories_section()
    {
        $this->start_controls_section('categories_freelancer_section', [
            'label' => esc_html__('Freelancer Categories', 'felan-framework'),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => [
                'cate' => 'freelancer_categories',
            ],
        ]);

        $repeater = new Repeater();

        $taxonomy_terms = get_categories(
            array(
                'taxonomy' => 'freelancer_categories',
                'orderby' => 'name',
                'order' => 'ASC',
                'hide_empty' => 0,
                'parent' => 0,
            )
        );

        $categories = $category_slug = [];
        foreach ($taxonomy_terms as $category) {
            $categories[$category->slug] = $category->name;
        }

        $repeater->add_control(
            'freelancer_selected_icon',
            [
                'label' => esc_html__('Icon', 'felan-framework'),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
                'default' => [
                    'value' => 'fas fa-star',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $repeater->add_control(
            'freelancer_category',
            [
                'label' => esc_html__('Categories', 'felan-framework'),
                'type' => Controls_Manager::SELECT,
                'options' => $categories,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'freelancer_categories_list',
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
            ]
        );

        $this->end_controls_section();
    }

    private function register_service_categories_section()
    {
        $this->start_controls_section('categories_service_section', [
            'label' => esc_html__('Service Categories', 'felan-framework'),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => [
                'cate' => 'service-categories',
            ],
        ]);

        $repeater = new Repeater();

        $taxonomy_terms = get_categories(
            array(
                'taxonomy' => 'service-categories',
                'orderby' => 'name',
                'order' => 'ASC',
                'hide_empty' => 0,
                'parent' => 0,
            )
        );

        $categories = $category_slug = [];
        foreach ($taxonomy_terms as $category) {
            $categories[$category->slug] = $category->name;
        }

        $repeater->add_control(
            'service_selected_icon',
            [
                'label' => esc_html__('Icon', 'felan-framework'),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
                'default' => [
                    'value' => 'fas fa-star',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $repeater->add_control(
            'service_category',
            [
                'label' => esc_html__('Categories', 'felan-framework'),
                'type' => Controls_Manager::SELECT,
                'options' => $categories,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'service_categories_list',
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
            ]
        );

        $this->end_controls_section();
    }

    private function register_project_categories_section()
    {
        $this->start_controls_section('categories_project_section', [
            'label' => esc_html__('Project Categories', 'felan-framework'),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => [
                'cate' => 'project-categories',
            ],
        ]);

        $repeater = new Repeater();

        $taxonomy_terms = get_categories(
            array(
                'taxonomy' => 'project-categories',
                'orderby' => 'name',
                'order' => 'ASC',
                'hide_empty' => 0,
                'parent' => 0,
            )
        );

        $categories = $category_slug = [];
        foreach ($taxonomy_terms as $category) {
            $categories[$category->slug] = $category->name;
        }

        $repeater->add_control(
            'project_selected_icon',
            [
                'label' => esc_html__('Icon', 'felan-framework'),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
                'default' => [
                    'value' => 'fas fa-star',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $repeater->add_control(
            'project_category',
            [
                'label' => esc_html__('Categories', 'felan-framework'),
                'type' => Controls_Manager::SELECT,
                'options' => $categories,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'project_categories_list',
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
            ]
        );

        $this->end_controls_section();
    }


    protected function render()
    {

        $settings = $this->get_settings_for_display();
        $this->add_render_attribute('wrapper', 'class', array('felan-categories-list'));

        $taxonomy = $settings['cate'];
        if ($taxonomy == 'company-categories') {
            $post_type= 'company';
        } elseif ($taxonomy == 'freelancer_categories') {
            $post_type= 'freelancer';
        } elseif ($taxonomy == 'service-categories') {
            $post_type= 'service';
        } elseif ($taxonomy == 'project-categories') {
            $post_type= 'project';
        } else {
            $post_type= 'jobs';
        }

        $args = array(
            'taxonomy' => $taxonomy,
            'hide_empty' => 0,
            'number' => 6,
            'parent' => 0,
        );

        $categories = get_categories($args); ?>

        <div <?php echo $this->get_render_attribute_string('wrapper') ?>>
             <?php foreach ($settings[$post_type . '_categories_list'] as $category_item) {
                $migrated = isset($category_item['__fa4_migrated']['selected_icon']);
                $is_new = !isset($category_item['icon']) && Icons_Manager::is_migration_allowed();
                $item_id = $category_item['_id'];
                $item_key = 'item_' . $item_id;
                $category_slug = $category_item[$post_type . '_category'];
                $cate = get_term_by('slug', $category_slug, $taxonomy);

                $this->add_render_attribute($item_key, 'class', array(
                    'list-cate-item',
                    'elementor-repeater-item-' . $item_id,
                ));
                if ($cate) {
                    $cate_id = $cate->term_id; ?>
                    <?php if (!empty($categories)) : ?>
                        <div <?php echo $this->get_render_attribute_string($item_key); ?>>
                            <ul class="list-all-categories">
                                <?php
                                foreach ($categories as $category) {
                                    if ($category->term_id === $cate_id) {
                                        echo '<li>';
                                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">';
										if ($is_new || $migrated) {
											Icons_Manager::render_icon($category_item[$post_type . '_selected_icon'], ['aria-hidden' => 'true']);
										} elseif (!empty($category_item['icon'])) {
                                            echo '<i ' . $this->get_render_attribute_string('i') . '></i>';
                                        }
                                        echo esc_html($category->name);
										echo '</a>';
                                        $this->display_subcategories($category->term_id, $taxonomy);
                                        echo '</li>';
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <?php
                }
            } ?>
        </div>
    <?php }

    private function display_subcategories($parent_id, $taxonomy) {
        $subcategories = get_categories(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => 0,
            'parent' => $parent_id,
        ));

        if (!empty($subcategories)) {
            echo '<ul class="sub-all-categories">';
            foreach ($subcategories as $subcategory) {
                echo '<li>';
                echo '<a href="' . esc_url(get_category_link($subcategory->term_id)) . '">' . esc_html($subcategory->name) . '</a>';
                $this->display_subcategories($subcategory->term_id, $taxonomy);
                echo '</li>';
            }
            echo '</ul>';
        }
    }
}
