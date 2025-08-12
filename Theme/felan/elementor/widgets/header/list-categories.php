<?php

namespace Felan_Elementor;

defined('ABSPATH') || exit;

class Widget_List_Categories extends Base
{

    public function get_name()
    {
        return 'felan-list-categories';
    }

    public function get_title()
    {
        return esc_html__('List Categories', 'felan');
    }

    public function get_icon_part()
    {
        return 'eicon-form-vertical';
    }

    public function get_keywords()
    {
        return ['category'];
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

        $this->add_control('post_type', [
            'label'   => esc_html__('Post Type', 'felan'),
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'jobs' => esc_html__('Jobs', 'felan'),
                'company' => esc_html__('Company', 'felan'),
                'freelancer' => esc_html__('Freelancer', 'felan'),
                'service' => esc_html__('Service', 'felan'),
                'project' => esc_html__('Project', 'felan'),
            ],
            'default' => 'jobs',
            'label_block' => true,
        ]);

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $post_type_categories = $settings['post_type'];

        if ($post_type_categories == 'company') {
            $taxonomy = 'company-categories';
        } elseif ($post_type_categories == 'freelancer') {
            $taxonomy = 'freelancer_categories';
        } elseif ($post_type_categories == 'service') {
            $taxonomy = 'service-categories';
        } elseif ($post_type_categories == 'project') {
            $taxonomy = 'project-categories';
        } else {
            $taxonomy = 'jobs-categories';
        }

        $args = array(
            'taxonomy' => $taxonomy,
            'hide_empty' => 0,
            'number' => 6,
            'parent' => 0,
        );

        $categories = get_categories($args);

        if (!empty($categories)) {
            echo '<div class="felan-list-categories">';
            echo '<ul class="list-categories">';
            foreach ($categories as $category) {
                echo '<li>';
                echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a>';
                $this->display_subcategories($category->term_id, $taxonomy);
                echo '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }
    }

    protected function display_subcategories($parent_id, $taxonomy)
    {
        $subcategories = get_categories(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => 0,
            'parent' => $parent_id,
        ));

        if (!empty($subcategories)) {
            echo '<ul class="sub-categories">';
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
