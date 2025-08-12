<?php

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;

defined('ABSPATH') || exit;

Plugin::instance()->widgets_manager->register(new Widget_Categories_Tabs());

class Widget_Categories_Tabs extends Widget_Base
{

    public function get_name()
    {
        return 'felan-categories-tabs';
    }

    public function get_title()
    {
        return esc_html__('Categories Tabs', 'felan-framwork');
    }

    public function get_icon()
    {
        return 'felan-badge eicon-table-of-contents';
    }

    public function get_keywords()
    {
        return ['tabs'];
    }

    public function get_script_depends()
    {
        return [FELAN_PLUGIN_PREFIX . 'categories-tabs'];
    }

    public function get_style_depends()
    {
        return [FELAN_PLUGIN_PREFIX . 'categories-tabs'];
    }

    protected function register_controls()
    {
        $this->add_layout_section();
    }

    private function add_layout_section()
    {
        $data = [
            'jobs-categories' => esc_html__('Jobs Categories', 'felan-framwork'),
            'jobs-skills' => esc_html__('Jobs Skills', 'felan-framwork'),
            'jobs-type' => esc_html__('Jobs Type', 'felan-framwork'),
            'jobs-career' => esc_html__('Jobs Career', 'felan-framwork'),
            'jobs-experience' => esc_html__('Jobs Experience', 'felan-framwork'),
            'jobs-qualification' => esc_html__('Jobs Qualification', 'felan-framwork'),
            'service-categories' => esc_html__('Service Categories', 'felan-framwork'),
            'service-skills' => esc_html__('Service Skills', 'felan-framwork'),
            'service-language' => esc_html__('Service Language', 'felan-framwork'),
            'freelancer_categories' => esc_html__('Freelancer Categories', 'felan-framwork'),
            'freelancer_ages' => esc_html__('Freelancer Ages', 'felan-framwork'),
            'freelancer_languages' => esc_html__('Freelancer Languages', 'felan-framwork'),
            'freelancer_qualification' => esc_html__('Freelancer Qualification', 'felan-framwork'),
            'freelancer_yoe' => esc_html__('Freelancer Years of Experience', 'felan-framwork'),
            'freelancer_education_levels' => esc_html__('Freelancer Education Levels', 'felan-framwork'),
            'freelancer_skills' => esc_html__('Freelancer Skills', 'felan-framwork'),
            'freelancer_gender' => esc_html__('Freelancer Gender', 'felan-framwork'),
            'project-categories' => esc_html__('Project Categories', 'felan-framwork'),
            'project-skills' => esc_html__('Project Skills', 'felan-framwork'),
            'project-language' => esc_html__('Project Language', 'felan-framwork'),
            'project-career' => esc_html__('Project Career', 'felan-framwork'),
            'company-categories' => esc_html__('Company Categories', 'felan-framwork'),
            'company-size' => esc_html__('Company Size', 'felan-framwork'),
        ];

        $this->start_controls_section('layout_section', [
            'label' => esc_html__('Layout', 'felan-framwork'),
        ]);

        $this->add_control('posts_per_page', [
            'label' => esc_html__('Posts Per Page', 'felan-framwork'),
            'type'  => Controls_Manager::NUMBER,
            'step'  => 1,
            'min'   => 1,
            'default' => 5,
        ]);

        $this->add_control(
            'enable_view_more',
            [
                'label' => esc_html__('Enable View More', 'felan-framework'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
            ]
        );

        $this->add_control(
            'nav_layout',
            [
                'label'   => esc_html__('Nav Layout', 'felan-framwork'),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    'border-line' => esc_html__('Border Line', 'felan-framwork'),
                    'underline' => esc_html__('Underline', 'felan-framwork'),
                    'vertical' => esc_html__('Vertical', 'felan-framwork'),
                ],
                'default' => 'border-line',
            ]
        );

        $term_args = array();

        foreach ($data as $key => $value) {
            $terms = get_terms([
                'taxonomy' => $key,
                'hide_empty' => false,
            ]);

            if (!empty($terms) && !is_wp_error($terms)) {
                foreach ($terms as $term) {
                    $term_args[$term->slug] = $value . ': ' . esc_html($term->name);
                }
            }
        }

        $tabs = new \Elementor\Repeater();

        $tabs->add_control('custom_title', [
            'label'   => esc_html__('Custom title', 'felan-framwork'),
            'type'    => Controls_Manager::TEXT,
        ]);

        $tabs->add_control(
            'term',
            [
                'label'   => esc_html__('Term', 'felan-framwork'),
                'type'    => Controls_Manager::SELECT2,
                'options' => $term_args,
                'label_block' => true,
            ]
        );

        $tabs->add_control(
            'layout',
            [
                'label'   => esc_html__('Layout', 'felan-framwork'),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    'layout-list'    => esc_html__('List', 'felan-framwork'),
                    'layout-grid'    => esc_html__('Grid', 'felan-framwork'),
                ],
                'default' => 'layout-list',
            ]
        );

        $tabs->add_control(
            'column',
            [
                'label'   => esc_html__('Column', 'felan-framwork'),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    '01'    => esc_html__('01', 'felan-framwork'),
                    '02'    => esc_html__('02', 'felan-framwork'),
                    '03'    => esc_html__('03', 'felan-framwork'),
                ],
                'default' => '02',
            ]
        );

        $tabs->add_control('icon', [
            'label' => esc_html__('Icon', 'felan-framework'),
            'type' => Controls_Manager::ICONS,
        ]);

        $this->add_control(
            'tabs',
            [
                'label' => esc_html__('Tabs', 'felan-framwork'),
                'type'      => Controls_Manager::REPEATER,
                'fields'    => $tabs->get_controls(),
                'default' => [
                    [
                        'custom_title' => esc_html__('Tabs 01', 'felan-framwork'),
                    ],
                    [
                        'custom_title' => esc_html__('Tabs 02', 'felan-framwork'),
                    ],
                    [
                        'custom_title' => esc_html__('Tabs 03', 'felan-framwork'),
                    ],
                ],
                'title_field' => '{{{ custom_title }}}',
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $this->add_render_attribute('wrapper', 'class', array(
            'felan-categories-tabs',
            'layout-' . $settings['nav_layout'],
        ));
?>
        <div <?php $this->print_render_attribute_string('wrapper') ?>>
            <div class="nav-categories-tabs <?php echo esc_html($settings['nav_layout']); ?>">
                <ul>
                    <?php
                    foreach ($settings['tabs'] as $i => $item) :
                        $taxonomy = $this->get_taxonomy_name_by_term_slug($item['term']);
                        $term = get_term_by('slug', $item['term'], $taxonomy);
                        if (!empty($term)) {
                    ?>

                            <li class="nav-item <?php if ($i == 0) echo 'active' ?>">
                                <a href="#<?php echo esc_attr($term->slug) ?>">
                                    <?php if (!empty($item['icon'] && $settings['nav_layout'] == 'vertical')) { ?>
                                        <span class="icon">
                                            <?php Icons_Manager::render_icon($item['icon'], ['aria-hidden' => 'true']); ?>
                                        </span>
                                    <?php } ?>
                                    <?php if (!empty($item['custom_title'])) echo esc_html($item['custom_title']);
                                    else echo esc_html($term->name); ?>
                                </a>
                            </li>
                        <?php } ?>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="content-categories-tabs">
                <?php
                foreach ($settings['tabs'] as $i => $item) :
                    if ($item['term']) {
                        $taxonomy = $this->get_taxonomy_name_by_term_slug($item['term']);
                        $term = get_term_by('slug', $item['term'], $taxonomy);
                        if (!empty($term)) {
                ?>
                            <div class="categories-tabs-item <?php if ($i == 0) echo 'active' ?>" id="<?php echo esc_attr($term->slug) ?>">
                                <?php
                                $args = array(
                                    'posts_per_page' => $settings['posts_per_page'],
                                    'post_status' => 'publish',
                                    'ignore_sticky_posts' => 1,
                                    'tax_query' => array(
                                        array(
                                            'taxonomy' => $term->taxonomy,
                                            'terms' => $term->term_id,
                                            'field' => 'term_id',
                                        )
                                    ),
                                );
                                if (strpos($term->taxonomy, 'jobs') === 0) {
                                    $args['post_type'] = 'jobs';
                                    $the_query = new \WP_Query($args);
                                    if ($the_query->have_posts()) {
                                        echo '<div class="categories-tabs-item-inner column-' . esc_html($item['column']) . '">';
                                        while ($the_query->have_posts()) {
                                            $the_query->the_post();

                                ?>
                                            <div class="jobs-item-inner <?php echo esc_attr($item['layout']) ?>">
                                                <?php felan_get_template('content-jobs.php', array(
                                                    'jobs_layout' => $item['layout'],
                                                )); ?>
                                            </div>
                                        <?php
                                        }
                                        wp_reset_postdata();
                                        echo '</div>';
                                        if ($settings['enable_view_more']) {
                                            echo '<div class="btn-readmore felan-button button-border-bottom"><a href="' .  esc_url(get_term_link($term->term_id)) . '">' . esc_html__('Explore All', 'felan-framework') . ' ' . esc_html($term->name) . '</a></div>';
                                        }
                                    } else {
                                        echo '<div class="item-not-found">' . esc_html__('No item found', 'felan-framework') . '</div>';
                                    }
                                } elseif (strpos($term->taxonomy, 'service') === 0) {
                                    $args['post_type'] = 'service';
                                    $the_query = new \WP_Query($args);
                                    if ($the_query->have_posts()) {
                                        echo '<div class="categories-tabs-item-inner column-' . esc_attr($item['column']) . '">';
                                        while ($the_query->have_posts()) {
                                            $the_query->the_post();

                                        ?>
                                            <div class="service-item-inner">
                                                <?php felan_get_template('content-service.php', array(
                                                    'service_layout' => $item['layout'],
                                                )); ?>
                                            </div>
                                        <?php
                                        }
                                        wp_reset_postdata();
                                        echo '</div>';
                                        if ($settings['enable_view_more']) {
                                            echo '<div class="btn-readmore felan-button button-border-bottom"><a href="' . esc_url(get_term_link($term->term_id)) . '">' . esc_html__('Explore All', 'felan-framework') . ' ' . esc_html($term->name) . '</a></div>';
                                        }
                                    } else {
                                        echo '<div class="item-not-found">' . esc_html__('No item found', 'felan-framework') . '</div>';
                                    }
                                } elseif (strpos($term->taxonomy, 'freelancer') === 0) {
                                    $args['post_type'] = 'freelancer';
                                    $the_query = new \WP_Query($args);
                                    if ($the_query->have_posts()) {
                                        echo '<div class="categories-tabs-item-inner column-' . esc_attr($item['column']) . '">';
                                        while ($the_query->have_posts()) {
                                            $the_query->the_post();
                                        ?>
                                            <div class="freelancer-item-inner">
                                                <?php felan_get_template('content-freelancer.php', array(
                                                    'freelancer_layout' => $item['layout'],
                                                )); ?>
                                            </div>
                                        <?php
                                        }
                                        wp_reset_postdata();
                                        echo '</div>';
                                        if ($settings['enable_view_more']) {
                                            echo '<div class="btn-readmore felan-button button-border-bottom"><a href="' . esc_url(get_term_link($term->term_id)) . '">' . esc_html__('Explore All', 'felan-framework') . ' ' . esc_html($term->name) . '</a></div>';
                                        }
                                    } else {
                                        echo '<div class="item-not-found">' . esc_html__('No item found', 'felan-framework') . '</div>';
                                    }
                                } elseif (strpos($term->taxonomy, 'project') === 0) {
                                    $args['post_type'] = 'project';
                                    $the_query = new \WP_Query($args);
                                    if ($the_query->have_posts()) {
                                        echo '<div class="categories-tabs-item-inner column-' . esc_attr($item['column']) . '">';
                                        while ($the_query->have_posts()) {
                                            $the_query->the_post();
                                        ?>
                                            <div class="project-item-inner">
                                                <?php felan_get_template('content-project.php', array(
                                                    'project_layout' => $item['layout'],
                                                )); ?>
                                            </div>
                                        <?php
                                        }
                                        wp_reset_postdata();
                                        echo '</div>';
                                        if ($settings['enable_view_more']) {
                                            echo '<div class="btn-readmore felan-button button-border-bottom"><a href="' . esc_url(get_term_link($term->term_id)) . '">' . esc_html__('Explore All', 'felan-framework') . ' ' . esc_html($term->name) . '</a></div>';
                                        }
                                    } else {
                                        echo '<div class="item-not-found">' . esc_html__('No item found', 'felan-framework') . '</div>';
                                    }
                                } elseif (strpos($term->taxonomy, 'company') === 0) {
                                    $args['post_type'] = 'company';
                                    $the_query = new \WP_Query($args);
                                    if ($the_query->have_posts()) {
                                        echo '<div class="categories-tabs-item-inner column-' . esc_attr($item['column']) . '">';
                                        while ($the_query->have_posts()) {
                                            $the_query->the_post();
                                        ?>
                                            <div class="company-item-inner">
                                                <?php felan_get_template('content-company.php', array(
                                                    'company_layout' => $item['layout'],
                                                )); ?>
                                            </div>
                                <?php
                                        }
                                        wp_reset_postdata();
                                        echo '</div>';
                                        if ($settings['enable_view_more']) {
                                            echo '<div class="btn-readmore felan-button button-border-bottom"><a href="' . esc_url(get_term_link($term->term_id)) . '">' . esc_html__('Explore All', 'felan-framework') . ' ' . esc_html($term->name) . '</a></div>';
                                        }
                                    } else {
                                        echo '<div class="item-not-found">' . esc_html__('No item found', 'felan-framework') . '</div>';
                                    }
                                }
                                ?>
                            </div>
                <?php }
                    }
                endforeach; ?>
            </div>
        </div>
<?php }

    protected function get_taxonomy_name_by_term_slug($term_slug)
    {
        // Get all registered taxonomies
        $taxonomies = get_taxonomies();

        foreach ($taxonomies as $taxonomy) {
            // Try to get the term by slug in the current taxonomy
            $term = get_term_by('slug', $term_slug, $taxonomy);

            if ($term && !is_wp_error($term)) {
                // Return the taxonomy name if the term is found
                return $taxonomy;
            }
        }

        // If no term is found, return false
        return false;
    }
}
