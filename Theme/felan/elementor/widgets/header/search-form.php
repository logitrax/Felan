<?php

namespace Felan_Elementor;

defined('ABSPATH') || exit;

class Widget_Search_Form extends Base
{

    public function get_name()
    {
        return 'felan-search-form';
    }

    public function get_title()
    {
        return esc_html__('Search Form', 'felan');
    }

    public function get_icon_part()
    {
        return 'eicon-site-search';
    }

    public function get_keywords()
    {
        return ['search', 'form'];
    }

    protected function register_controls()
    {
        $this->add_content_section();
        $this->add_content_style_section();
    }

    private function add_content_section()
    {
        $this->start_controls_section('content_section', [
            'label' => esc_html__('Content', 'felan'),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('search_by_post_type', [
            'label'   => esc_html__('Post Type', 'felan'),
            'type'    => \Elementor\Controls_Manager::SELECT2,
            'multiple' => true,
            'options' => [
                'jobs' => esc_html__('Jobs', 'felan'),
                'company' => esc_html__('Companies', 'felan'),
                'freelancer' => esc_html__('Freelancers', 'felan'),
                'service' => esc_html__('Services', 'felan'),
                'project' => esc_html__('Projects', 'felan'),
            ],
            'default' => 'jobs',
            'label_block' => true,
        ]);

        $this->add_control('search_result_per_page', [
            'label'   => esc_html__('Number of Results', 'felan'),
            'type'    => \Elementor\Controls_Manager::NUMBER,
            'step'    => 1,
            'min'     => 1,
            'max'     => 20,
            'default' => 10,
        ]);

        $this->end_controls_section();
    }

    private function add_content_style_section()
    {
        $this->start_controls_section('content_style_section', [
            'label' => esc_html__('Content', 'felan'),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('max_width', [
            'label' => esc_html__('Max Width', 'felan'),
            'type'  => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => [
                'px' => [
                    'min' => 100,
                    'max' => 1000,
                ],
            ],
            'default' => [
                'unit' => 'px',
                'size' => 500,
            ],
            'tablet_default' => [
                'unit' => 'px',
                'size' => 500,
            ],
            'mobile_default' => [
                'unit' => 'px',
                'size' => 500,
            ],
            'selectors' => [
                '{{WRAPPER}} .site-search' => 'max-width: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $search_by_post_type = $settings['search_by_post_type'];
        $search_result_per_page = $settings['search_result_per_page'];
        if (empty($search_by_post_type)) {
            return;
        }

        switch ($search_by_post_type[0]) {
            case 'jobs':
                $current_label = esc_html__('Jobs', 'felan');
                break;
            case 'company':
                $current_label = esc_html__('Companies', 'felan');
                break;
            case 'freelancer':
                $current_label = esc_html__('Freelancers', 'felan');
                break;
            case 'service':
                $current_label = esc_html__('Services', 'felan');
                break;
            case 'project':
                $current_label = esc_html__('Projects', 'felan');
                break;
        }
?>
        <div class="site-search">
            <form action="<?php echo esc_url(home_url("/")); ?>" method="get" class="site-search-form" data-per-page="<?php echo esc_attr($search_result_per_page); ?>">
                <div class="search-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" aria-hidden="true" viewBox="0 0 24 24" role="img">
                        <path vector-effect="non-scaling-stroke" stroke="var(--icon-color, #001e00)" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="1.5" d="M10.688 18.377a7.688 7.688 0 100-15.377 7.688 7.688 0 000 15.377zm5.428-2.261L21 21"></path>
                    </svg>
                </div>
                <input type="text" class="search-input" name="s" autocomplete="off" placeholder="<?php echo esc_attr__('Search', 'felan'); ?>">
                <input type="hidden" name="post_type" value="<?php echo esc_attr($search_by_post_type[0]); ?>">
                <div class="reset-search">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 9.00002L9 15M8.99997 9L14.9999 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>

                <div class="list-post-type">
                    <div class="post-type active"><span><?php echo esc_html($current_label); ?></span><i class="far fa-chevron-down"></i></div>
                    <ul>
                        <?php
                        $i = 0;
                        foreach ($search_by_post_type as $key => $value) {
                            switch ($value) {
                                case 'jobs':
                                    $label = esc_html__('Jobs', 'felan');
                                    break;
                                case 'company':
                                    $label = esc_html__('Companies', 'felan');
                                    break;
                                case 'freelancer':
                                    $label = esc_html__('Freelancers', 'felan');
                                    break;
                                case 'service':
                                    $label = esc_html__('Services', 'felan');
                                    break;
                                case 'project':
                                    $label = esc_html__('Projects', 'felan');
                                    break;
                            }
                            if ($i == 0) {
                                $class = 'active';
                            } else {
                                $class = '';
                            }
                            echo '<li>';
                            echo '<a href="#" data-post-type="' . esc_attr($value) . '" data-post-type-label="' . esc_html($label) . '" class="' . $class . '" >';
                            if ($value == 'jobs') {
                        ?>
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.308 21H15.692C19.4025 21 20.067 19.551 20.2609 17.787L20.9531 10.587C21.2023 8.39098 20.5562 6.59998 16.615 6.59998H7.385C3.44378 6.59998 2.79768 8.39098 3.04689 10.587L3.73914 17.787C3.93297 19.551 4.59753 21 8.308 21Z" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M8.30811 6.6V5.88C8.30811 4.287 8.30811 3 11.2617 3H12.7385C15.6921 3 15.6921 4.287 15.6921 5.88V6.6" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M9.81164 13.3312C7.51024 13.0799 5.25161 12.2948 3.2334 11" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M14.2334 13.3312C16.5348 13.0799 18.7934 12.2948 20.8116 11" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                    <circle cx="12" cy="13.5" r="2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            <?php
                            } elseif ($value == 'company') {
                            ?>
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 8.94059V18M17 8.94059V18M7 8.94059V18M12.4472 3.10627L20.2111 7.01386C21.155 7.48888 20.8192 8.92079 19.7639 8.92079H4.23607C3.18084 8.92079 2.84503 7.48889 3.78885 7.01386L11.5528 3.10627C11.8343 2.96458 12.1657 2.96458 12.4472 3.10627ZM19.5 21H4.50001C3.67158 21 3 20.3284 3 19.5C3 18.6716 3.67158 18 4.50001 18H19.5C20.3284 18 21 18.6716 21 19.5C21 20.3284 20.3284 21 19.5 21Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            <?php
                            } elseif ($value == 'freelancer') {
                            ?>
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M21 19.7499C21 17.66 19.3304 14.682 17 14.023M15 19.75C15 17.099 12.3137 13.75 9 13.75C5.68629 13.75 3 17.099 3 19.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <circle cx="9" cy="7.25" r="3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M15 10.25C16.6569 10.25 18 8.90685 18 7.25C18 5.59315 16.6569 4.25 15 4.25" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            <?php
                            } elseif ($value == 'service') {
                            ?>
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9.47796 3H7.25C6.00736 3 5 4.00736 5 5.25V18.75C5 19.9926 6.00736 21 7.25 21H16.25C17.4926 21 18.5 19.9926 18.5 18.75V12M9.47796 3C10.7206 3 11.75 4.00736 11.75 5.25V7.5C11.75 8.74264 12.7574 9.75 14 9.75H16.25C17.4926 9.75 18.5 10.7574 18.5 12M9.47796 3C13.1679 3 18.5 8.3597 18.5 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M9 16.5H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M9 13.5H13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            <?php
                            } else {
                            ?>
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.9999 20.25C4.9999 20.6642 5.33568 21 5.7499 21H16.4019C17.565 21 17.9999 20.6348 17.9999 19.4V17.9549M4.9999 20.25C4.9999 19.0074 6.00726 18 7.2499 18H17.4019C17.6281 18 17.8267 17.9862 17.9999 17.9549M4.9999 20.25V6.2002C4.9999 5.06408 4.92789 3.81097 6.09169 3.21799C6.51952 3 7.07999 3 8.20009 3H17.4001C18.6353 3 18.9999 3.43658 18.9999 4.6001V16.4001C18.9999 17.3948 18.7176 17.8251 17.9999 17.9549" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                        <?php
                            }
                            echo esc_html($label);
                            echo '</a>';
                            echo '</li>';
                            $i++;
                        }
                        ?>
                    </ul>
                </div>

            </form>
            <div class="search-result"></div>
        </div>
<?php
    }
}
