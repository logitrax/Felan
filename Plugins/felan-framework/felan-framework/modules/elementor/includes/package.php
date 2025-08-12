<?php

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Plugin;

defined('ABSPATH') || exit;

Plugin::instance()->widgets_manager->register(new Widget_Package());

class Widget_Package extends Widget_Base
{

    public function get_name()
    {
        return 'felan-package';
    }

    public function get_title()
    {
        return esc_html__('Package', 'felan-framework');
    }

    public function get_icon()
    {
        return 'felan-badge eicon-price-table';
    }

    public function get_keywords()
    {
        return ['package'];
    }

    public function get_style_depends()
    {
        return [FELAN_PLUGIN_PREFIX . 'package'];
    }

    protected function register_controls()
    {
        $this->add_layout_section();
    }

    private function add_layout_section()
    {
        $this->start_controls_section('layout_section', [
            'label' => esc_html__('Layout', 'felan-framework'),
        ]);

        $this->add_control('for', [
            'label' => esc_html__('For', 'felan-framework'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'package' => esc_html__('Employer', 'felan-framework'),
                'freelancer_package' => esc_html__('Freelancer', 'felan-framework'),
            ],
            'default' => 'package',
        ]);

        $this->add_control('layout', [
            'label' => esc_html__('Layout', 'felan-framework'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                '01' => esc_html__('Layout 01', 'felan-framework'),
                '02' => esc_html__('Layout 02', 'felan-framework'),
                '03' => esc_html__('Layout 03', 'felan-framework'),
            ],
            'default' => '01',
            'prefix_class' => 'felan-package-layout-',
        ]);

        $employer_options = [];
        $employer_package = array(
            'post_type' => 'package',
            'posts_per_page' => -1,
            'orderby' => 'meta_value',
            'meta_key' => FELAN_METABOX_PREFIX . 'package_order_display',
            'order' => 'ASC',
            'meta_query' => array(
                array(
                    'key' => FELAN_METABOX_PREFIX . 'package_visible',
                    'value' => '1',
                    'compare' => '=',
                )
            )
        );

        $employer_package = new \WP_Query($employer_package);
        if ($employer_package->have_posts()) {
            while ($employer_package->have_posts()) : $employer_package->the_post();
                $id = get_the_id();
                $title = get_the_title($id);
                $employer_options[$id] = $title;
            endwhile;
        }
        wp_reset_postdata();

        $this->add_control('employer_title', [
            'label'       => esc_html__('Title Package', 'felan-framework'),
            'type'        => Controls_Manager::SELECT2,
            'options'     => $employer_options,
            'default'     => [],
            'label_block' => true,
            'multiple'    => true,
            'condition' => [
                'for' => 'package',
            ],
        ]);

        $freelancer_options = [];
        $freelancer_package = array(
            'post_type' => 'freelancer_package',
            'posts_per_page' => -1,
            'orderby' => 'meta_value',
            'meta_key' => FELAN_METABOX_PREFIX . 'freelancer_package_order_display',
            'order' => 'ASC',
            'meta_query' => array(
                array(
                    'key' => FELAN_METABOX_PREFIX . 'freelancer_package_visible',
                    'value' => '1',
                    'compare' => '=',
                )
            )
        );

        $freelancer_package = new \WP_Query($freelancer_package);
        if ($freelancer_package->have_posts()) {
            while ($freelancer_package->have_posts()) : $freelancer_package->the_post();
                $id = get_the_id();
                $title = get_the_title($id);
                $freelancer_options[$id] = $title;
            endwhile;
        }
        wp_reset_postdata();

        $this->add_control('freelancer_title', [
            'label'       => esc_html__('Title Package', 'felan-framework'),
            'type'        => Controls_Manager::SELECT2,
            'options'     => $freelancer_options,
            'default'     => [],
            'label_block' => true,
            'multiple'    => true,
            'condition' => [
                'for' => 'freelancer_package',
            ],
        ]);

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $this->add_render_attribute('wrapper', 'class', 'felan-package felan-package-wrap');
        if (empty($settings['employer_title']) && empty($settings['freelancer_title'])) {
            return;
        }
        global $current_user;
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
        $enable_post_type_jobs = felan_get_option('enable_post_type_jobs', '1');
        $enable_post_type_service = felan_get_option('enable_post_type_service', '1');
        $enable_post_type_project = felan_get_option('enable_post_type_project', '1');
        $field_package = array('freelancer_follow', 'download_cv', 'invite', 'send_message', 'print', 'review_and_commnent', 'info');
?>
        <div <?php echo $this->get_render_attribute_string('wrapper') ?>>
            <div class="row">
                <?php
                $user_package_id = get_the_author_meta(FELAN_METABOX_PREFIX . 'package_id', $user_id);
                $items = array();
                if ($settings['for'] === 'package') {
                    foreach ($settings['employer_title'] as $item) {
                        array_push($items, $item);
                    }
                    $args = array(
                        'post_type' => 'package',
                        'posts_per_page' => -1,
                        'orderby' => 'meta_value',
                        'meta_key' => FELAN_METABOX_PREFIX . 'package_order_display',
                        'order' => 'ASC',
                        'post__in' => $items,
                        'meta_query' => array(
                            array(
                                'key' => FELAN_METABOX_PREFIX . 'package_visible',
                                'value' => '1',
                                'compare' => '=',
                            )
                        )
                    );
                } else if ($settings['for'] === 'freelancer_package') {
                    foreach ($settings['freelancer_title'] as $item) {
                        array_push($items, $item);
                    }
                    $args = array(
                        'post_type' => 'freelancer_package',
                        'posts_per_page' => -1,
                        'orderby' => 'meta_value',
                        'meta_key' => FELAN_METABOX_PREFIX . 'freelancer_package_order_display',
                        'order' => 'ASC',
                        'post__in' => $items,
                        'meta_query' => array(
                            array(
                                'key' => FELAN_METABOX_PREFIX . 'freelancer_package_visible',
                                'value' => '1',
                                'compare' => '=',
                            )
                        )
                    );
                }
                $data = new \WP_Query($args);
                $total_records = $data->found_posts;
                while ($data->have_posts()) : $data->the_post();
                    if ($settings['for'] === 'package') {
                        $package_id = get_the_ID();
                        $package_time_unit = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_time_unit', true);
                        $package_period = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_period', true);
                        $package_num_job = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_number_job', true);
                        $package_free = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_free', true);
                        $used_free_package = get_user_meta($user_id, 'used_free_package', true);
                        if ($package_free == 1 && $used_free_package === 'yes') {
                            continue;
                        }
                        if ($package_free == 1) {
                            $package_price = 0;
                        } else {
                            $package_price = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_price', true);
                        }
                        $package_unlimited_job = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_unlimited_job', true);
                        $package_unlimited_time = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_unlimited_time', true);
                        $package_featured_job = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_unlimited_job_featured', true);
                        $package_num_featured_job = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_number_featured', true);

                        $package_num_project = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_number_project', true);
                        $package_unlimited_project = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_unlimited_project', true);
                        $package_unlimited_time = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_unlimited_time', true);
                        $package_featured_project = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_unlimited_project_featured', true);
                        $package_num_featured_project = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_number_project_featured', true);

                        $package_featured = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_featured', true);
                        $package_additional = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_additional_details', true);
                        if ($package_additional > 0) {
                            $package_additional_text = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_details_text', true);
                        }

                        if ($package_period > 1) {
                            $package_time_unit .= 's';
                        }
                        if ($package_featured == 1) {
                            $is_featured = ' active';
                        } else {
                            $is_featured = '';
                        }
                        $felan_package = new \felan_Package();
                        $get_expired_date = $felan_package->get_expired_date($package_id, $user_id);
                        $current_date = date('Y-m-d');

                        $d1 = strtotime($get_expired_date);
                        $d2 = strtotime($current_date);

                        if ($get_expired_date === 'never expires') {
                            $d1 = 999999999999999999999999;
                        }

                        if ($user_package_id == $package_id && $d1 > $d2) {
                            $is_current = 'current';
                        } else {
                            $is_current = '';
                        }
                        $payment_link = felan_get_permalink('payment');
                        $payment_process_link = add_query_arg('package_id', $package_id, $payment_link);

                        if ($package_unlimited_time == 1) {
                            $head_time_unit = esc_html__('never expires', 'felan-framework');
                        } else {
                            if ($package_period === '1') {
                                $head_time_unit = get_head_time_unit($package_time_unit);
                            } elseif ($package_period === '') {
                                $head_time_unit = '';
                            } else {
                                $head_time_unit = $package_period . get_head_time_unit($package_time_unit);
                            }
                        }

                ?>
                        <div class="col-md-4 col-sm-6">
                            <div class="felan-package-item panel panel-default <?php echo esc_attr($is_current); ?> <?php echo esc_attr($is_featured); ?>">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="felan-package-thumbnail"><?php the_post_thumbnail(); ?></div>
                                <?php endif; ?>
                                <div class="felan-package-title">
                                    <h2 class="entry-title"><?php the_title(); ?></h2>
                                    <?php if ($package_featured == 1) { ?>
                                        <span class="recommended"><?php esc_html_e('Most Popular', 'felan-framework'); ?></span>
                                    <?php } ?>
                                </div>
                                <div class="felan-package-price">
                                    <?php
                                    if ($package_price > 0) {
                                        echo felan_get_format_money($package_price, '', 2, true);
                                    } else {
                                        esc_html_e('Free', 'felan-framework');
                                    }
                                    ?>
                                    <span class="time-unit"><?php echo $head_time_unit; ?></span>
                                </div>
                                <?php if (is_user_logged_in()) : ?>
                                    <?php if ($settings['layout'] == '02' || $settings['layout'] == '03') { ?>
                                        <div class="felan-package-choose">
                                            <?php
                                            $user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);
                                            if ($user_demo == 'yes') { ?>
                                                <?php if ($user_package_id == $package_id && $d1 > $d2) { ?>
                                                    <a href="#" class="felan-button button-block btn-add-to-message" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>">
                                                        <?php esc_html_e('Package Actived', 'felan-framework'); ?></a> <?php } else { ?>
                                                    <a href="#" class="felan-button button-outline button-block btn-add-to-message" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>">
                                                        <?php esc_html_e('Get Started', 'felan-framework'); ?></a> <?php } ?>
                                            <?php } else { ?>
                                                <?php if ($user_package_id == $package_id && $d1 > $d2) { ?>
                                                    <a href="<?php echo esc_url($payment_process_link); ?>" class="felan-button button-block"><?php esc_html_e('Package Actived', 'felan-framework'); ?></a>
                                                <?php } else { ?>
                                                    <a href="<?php echo esc_url($payment_process_link); ?>" class="felan-button button-outline button-block"><?php esc_html_e('Get Started', 'felan-framework'); ?></a>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                <?php else : ?>
                                    <a href="<?php echo get_page_link(felan_get_option('sp_sign_in')); ?>" class="felan-button button-outline button-block"><?php esc_html_e('Get Started', 'felan-framework'); ?></a>
                                <?php endif; ?>
                                <ul class="list-group custom-scrollbar">
                                    <?php if (!empty($package_num_job) && $enable_post_type_jobs == '1') : ?>
                                        <li class="list-group-item">
                                            <i class="far fa-check"></i>
                                            <span class="badge">
                                                <?php if ($package_unlimited_job == 1) {
                                                    esc_html_e('Unlimited', 'felan-framework');
                                                } else {
                                                    esc_html_e($package_num_job);
                                                } ?>
                                            </span>
                                            <?php esc_html_e('job postings', 'felan-framework'); ?>
                                        </li>
                                    <?php endif; ?>

                                    <?php if (!empty($package_num_featured_job) && $enable_post_type_jobs == '1') : ?>
                                        <li class="list-group-item">
                                            <i class="far fa-check"></i>
                                            <span class="badge">
                                                <?php if ($package_featured_job == 1) {
                                                    esc_html_e('Unlimited', 'felan-framework');
                                                } else {
                                                    esc_html_e($package_num_featured_job);
                                                } ?>
                                            </span>
                                            <?php esc_html_e('featured jobs', 'felan-framework') ?>
                                        </li>
                                    <?php endif; ?>
                                    <?php if (!empty($package_num_project) && $enable_post_type_project == '1') : ?>
                                        <li class="list-group-item">
                                            <i class="far fa-check"></i>
                                            <span class="badge">
                                                <?php if ($package_unlimited_project == 1) {
                                                    esc_html_e('Unlimited', 'felan-framework');
                                                } else {
                                                    esc_html_e($package_num_project);
                                                } ?>
                                            </span>
                                            <?php esc_html_e('project postings', 'felan-framework'); ?>
                                        </li>
                                    <?php endif; ?>
                                    <?php if (!empty($package_num_featured_project) && $enable_post_type_project == '1') : ?>
                                        <li class="list-group-item">
                                            <i class="far fa-check"></i>
                                            <span class="badge">
                                                <?php if ($package_featured_project == 1) {
                                                    esc_html_e('Unlimited', 'felan-framework');
                                                } else {
                                                    esc_html_e($package_num_featured_project);
                                                } ?>
                                            </span>
                                            <?php esc_html_e('featured projects', 'felan-framework') ?>
                                        </li>
                                    <?php endif; ?>

                                    <?php foreach ($field_package as $field) :
                                        $show_option = felan_get_option('enable_company_package_' . $field);
                                        $show_field = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'show_package_company_' . $field, true);
                                        $field_unlimited = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'enable_package_' . $field . '_unlimited', true);
                                        $field_number = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'company_package_number_' . $field, true);
                                        $is_check = true;
                                        switch ($field) {
                                            case 'freelancer_follow':
                                                $name = esc_html__('freelancers follow', 'felan-framework');
                                                $is_check = false;
                                                break;
                                            case 'download_cv':
                                                $name = esc_html__('Download CV', 'felan-framework');
                                                $is_check = false;
                                                break;
                                            case 'invite':
                                                $name = esc_html__('Invite Freelancers', 'felan-framework');
                                                break;
                                            case 'send_message':
                                                $name = esc_html__('Send messages', 'felan-framework');
                                                break;
                                            case 'print':
                                                $name = esc_html__('Print freelancer profiles', 'felan-framework');
                                                break;
                                            case 'review_and_commnent':
                                                $name = esc_html__('Review and comment', 'felan-framework');
                                                break;
                                            case 'info':
                                                $name = esc_html__('View freelancer information', 'felan-framework');
                                                break;
                                        }
                                        if ($show_field == 1 && $show_option == 1) :
                                    ?>
                                            <?php if (!empty($field_number)) : ?>
                                                <li class="list-group-item">
                                                    <i class=" fas fa-check"></i>
                                                    <?php if ($is_check == true) { ?>
                                                        <span class="badge">
                                                            <?php esc_html_e($name); ?>
                                                        </span>
                                                    <?php } else { ?>
                                                        <span class="badge">
                                                            <?php if ($field_unlimited == 1) { ?>
                                                                <?php esc_html_e('Unlimited', 'felan-framework'); ?>
                                                            <?php } else { ?>
                                                                <?php echo $field_number; ?>
                                                            <?php } ?>
                                                        </span>
                                                        <?php echo $name; ?>
                                                    <?php } ?>
                                                </li>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>

                                    <?php if ($package_additional > 0) {
                                        foreach ($package_additional_text as $value) { ?>
                                            <li class="list-group-item">
                                                <i class="far fa-check"></i>
                                                <span class="badge">
                                                    <?php esc_html_e($value); ?>
                                                </span>
                                            </li>
                                    <?php }
                                    } ?>
                                </ul>
                                <?php if (is_user_logged_in()) : ?>
                                    <?php if ($settings['layout'] == '01') { ?>
                                        <div class="felan-package-choose">
                                            <?php
                                            $user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);
                                            if ($user_demo == 'yes') { ?>
                                                <?php if ($user_package_id == $package_id && $d1 > $d2) { ?>
                                                    <a href="#" class="felan-button button-block btn-add-to-message" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>">
                                                        <?php esc_html_e('Package Actived', 'felan-framework'); ?></a> <?php } else { ?>
                                                    <a href="#" class="felan-button button-outline button-block btn-add-to-message" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>">
                                                        <?php esc_html_e('Get Started', 'felan-framework'); ?></a> <?php } ?>
                                            <?php } else { ?>
                                                <?php if ($user_package_id == $package_id && $d1 > $d2) { ?>
                                                    <a href="<?php echo esc_url($payment_process_link); ?>" class="felan-button button-block"><?php esc_html_e('Package Actived', 'felan-framework'); ?></a>
                                                <?php } else { ?>
                                                    <a href="<?php echo esc_url($payment_process_link); ?>" class="felan-button button-outline button-block"><?php esc_html_e('Get Started', 'felan-framework'); ?></a>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php
                    } else if ($settings['for'] === 'freelancer_package') {
                        $freelancer_package_id = get_the_ID();
                        $freelancer_package_time_unit = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'freelancer_package_time_unit', true);
                        $freelancer_package_period = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'freelancer_package_period', true);
                        $freelancer_package_number_service = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'freelancer_package_number_service', true);
                        $freelancer_package_free = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'freelancer_package_free', true);
                        if ($freelancer_package_free == 1) {
                            $freelancer_package_price = 0;
                        } else {
                            $freelancer_package_price = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'freelancer_package_price', true);
                        }
                        $enable_package_service_unlimited = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'enable_package_service_unlimited', true);
                        $enable_package_service_unlimited_time = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'enable_package_service_unlimited_time', true);
                        $freelancer_package_featured_freelancer = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'enable_package_service_featured_unlimited', true);
                        $freelancer_package_number_service_featured = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'freelancer_package_number_service_featured', true);
                        $freelancer_package_featured = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'freelancer_package_featured', true);
                        $freelancer_package_additional = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'freelancer_package_additional_details', true);
                        if ($freelancer_package_additional > 0) {
                            $freelancer_package_additional_text = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'freelancer_package_details_text', true);
                        }

                        if ($freelancer_package_period > 1) {
                            $freelancer_package_time_unit .= 's';
                        }
                        if ($freelancer_package_featured == 1) {
                            $is_featured = ' active';
                        } else {
                            $is_featured = '';
                        }
                        $felan_freelancer_package = new \felan_freelancer_package();
                        $get_expired_date = $felan_freelancer_package->get_expired_date($freelancer_package_id, $user_id);
                        $current_date = date('Y-m-d');

                        $d1 = strtotime($get_expired_date);
                        $d2 = strtotime($current_date);

                        if ($get_expired_date === 'never expires') {
                            $d1 = 999999999999999999999999;
                        }

                        $user_freelancer_package_id = get_the_author_meta(FELAN_METABOX_PREFIX . 'freelancer_package_id', $user_id);

                        if ($user_freelancer_package_id == $freelancer_package_id && $d1 > $d2) {
                            $is_current = 'current';
                        } else {
                            $is_current = '';
                        }
                        $payment_link = felan_get_permalink('freelancer_payment');
                        if ($enable_post_type_jobs == '1') {
                            $payment_process_link = add_query_arg('candidate_package_id', $freelancer_package_id, $payment_link);
                        } else {
                            $payment_process_link = add_query_arg('freelancer_package_id', $freelancer_package_id, $payment_link);
                        }
                        $field_package = array('jobs_apply', 'project_apply', 'jobs_wishlist', 'company_follow', 'contact_company', 'info_company', 'send_message', 'review_and_commnent');

                        if ($enable_package_service_unlimited_time == 1) {
                            $head_time_unit = esc_html__('never expires', 'felan-framework');
                        } else {
                            if ($freelancer_package_period === '1') {
                                $head_time_unit = get_head_time_unit($freelancer_package_time_unit);
                            } elseif ($freelancer_package_period === '') {
                                $head_time_unit = '';
                            } else {
                                $head_time_unit = $freelancer_package_period . get_head_time_unit($freelancer_package_time_unit);
                            }
                        }

                    ?>
                        <div class="col-md-4 col-sm-6">
                            <div class="felan-package-item panel panel-default <?php echo esc_attr($is_current); ?> <?php echo esc_attr($is_featured); ?>">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="felan-package-thumbnail"><?php the_post_thumbnail(); ?></div>
                                <?php endif; ?>
                                <div class="felan-package-title">
                                    <h2 class="entry-title"><?php the_title(); ?></h2>
                                    <?php if ($freelancer_package_featured == 1) { ?>
                                        <span class="recommended"><?php esc_html_e('Most Popular', 'felan-framework'); ?></span>
                                    <?php } ?>
                                </div>
                                <div class="felan-package-price">
                                    <?php
                                    if ($freelancer_package_price > 0) {
                                        echo felan_get_format_money($freelancer_package_price, '', 2, true);
                                    } else {
                                        esc_html_e('Free', 'felan-framework');
                                    }
                                    ?>
                                    <span class="time-unit"><?php echo $head_time_unit; ?></span>
                                </div>
                                <?php if (is_user_logged_in()) : ?>
                                    <?php if ($settings['layout'] == '02' || $settings['layout'] == '03') { ?>
                                        <div class="felan-package-choose">
                                            <?php
                                            $user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);
                                            if ($user_demo == 'yes') { ?>
                                                <?php if ($user_freelancer_package_id == $freelancer_package_id && $d1 > $d2) { ?>
                                                    <a href="#" class="felan-button button-block btn-add-to-message" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>">
                                                        <?php esc_html_e('Package Actived', 'felan-framework'); ?></a> <?php } else { ?>
                                                    <a href="#" class="felan-button button-outline button-block btn-add-to-message" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>">
                                                        <?php esc_html_e('Get Started', 'felan-framework'); ?></a>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <?php if ($user_freelancer_package_id == $freelancer_package_id && $d1 > $d2) { ?>
                                                    <a href="<?php echo esc_url($payment_process_link); ?>" class="felan-button button-block"><?php esc_html_e('Package Actived', 'felan-framework'); ?></a>
                                                <?php } else { ?>
                                                    <a href="<?php echo esc_url($payment_process_link); ?>" class="felan-button button-outline button-block"><?php esc_html_e('Get Started', 'felan-framework'); ?></a>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                <?php else : ?>
                                    <a href="<?php echo get_page_link(felan_get_option('sp_sign_in')); ?>" class="felan-button button-outline button-block"><?php esc_html_e('Get Started', 'felan-framework'); ?></a>
                                <?php endif; ?>
                                <ul class="list-group custom-scrollbar">
                                    <?php if (!empty($freelancer_package_number_service) && $enable_post_type_service == '1') : ?>
                                        <li class="list-group-item">
                                            <i class="far fa-check"></i>
                                            <span class="badge">
                                                <?php if ($enable_package_service_unlimited == 1) {
                                                    esc_html_e('Unlimited', 'felan-framework');
                                                } else {
                                                    esc_html_e($freelancer_package_number_service);
                                                } ?>
                                            </span>
                                            <?php esc_html_e('service postings', 'felan-framework'); ?>
                                        </li>
                                    <?php endif; ?>
                                    <?php if (!empty($freelancer_package_number_service_featured) && $enable_post_type_service == '1') : ?>
                                        <li class="list-group-item">
                                            <i class="far fa-check"></i>
                                            <span class="badge">
                                                <?php if ($freelancer_package_featured_freelancer == 1) {
                                                    esc_html_e('Unlimited', 'felan-framework');
                                                } else {
                                                    esc_html_e($freelancer_package_number_service_featured);
                                                } ?>
                                            </span>
                                            <?php esc_html_e('featured services', 'felan-framework') ?>
                                        </li>
                                    <?php endif; ?>
                                    <?php foreach ($field_package as $field) :
                                        $show_field = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'show_package_' . $field, true);
                                        $field_number = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'freelancer_package_number_' . $field, true);
                                        $field_unlimited = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'enable_package_' . $field . '_unlimited', true);
                                        $is_check = false;

                                        if ($field == 'jobs_apply' && $enable_post_type_jobs != '1') {
                                            continue;
                                        }

                                        if ($field == 'jobs_wishlist' && $enable_post_type_jobs != '1') {
                                            continue;
                                        }

                                        if ($field == 'project_apply' && $enable_post_type_project != '1') {
                                            continue;
                                        }

                                        switch ($field) {
                                            case 'jobs_apply':
                                                $name = esc_html__('job applications', 'felan-framework');
                                                break;
                                            case 'jobs_wishlist':
                                                $name = esc_html__('jobs in wishlist', 'felan-framework');
                                                break;
                                            case 'company_follow':
                                                $name = esc_html__(' companies followed', 'felan-framework');
                                                break;
                                            case 'contact_company':
                                                $name = esc_html__('View company in jobs', 'felan-framework');
                                                $is_check = true;
                                                break;
                                            case 'info_company':
                                                $name = esc_html__('View company contact information', 'felan-framework');
                                                $is_check = true;
                                                break;
                                            case 'send_message':
                                                $name = esc_html__('Send messages', 'felan-framework');
                                                $is_check = true;
                                                break;
                                            case 'review_and_commnent':
                                                $name = esc_html__('Review and commnet', 'felan-framework');
                                                $is_check = true;
                                                break;
                                            case 'project_apply':
                                                $name = esc_html__('proposals submitted', 'felan-framework');
                                                break;
                                        }
                                        if (intval($show_field) == 1 && !empty($field_number)) : ?>
                                            <li class="list-group-item">
                                                <i class="far fa-check"></i>
                                                <?php if ($is_check == true) { ?>
                                                    <span class="badge">
                                                        <?php esc_html_e($name); ?>
                                                    </span>
                                                <?php } else { ?>
                                                    <span class="badge">
                                                        <?php if ($field_unlimited == 1) { ?>
                                                            <?php esc_html_e('Unlimited', 'felan-framework'); ?>
                                                        <?php } else { ?>
                                                            <?php echo $field_number; ?>
                                                        <?php } ?>
                                                    </span>
                                                    <?php echo $name; ?>
                                                <?php } ?>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>

                                    <?php if ($freelancer_package_additional > 0) {
                                        foreach ($freelancer_package_additional_text as $value) { ?>
                                            <?php if (!empty($value)) : ?>
                                                <li class="list-group-item">
                                                    <i class="far fa-check"></i>
                                                    <span class="badge">
                                                        <?php esc_html_e($value); ?>
                                                    </span>
                                                </li>
                                            <?php endif; ?>
                                    <?php }
                                    } ?>
                                </ul>
                                <?php if (is_user_logged_in()) : ?>
                                    <?php if ($settings['layout'] == '01') { ?>
                                        <div class="felan-package-choose">
                                            <?php
                                            $user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);
                                            if ($user_demo == 'yes') { ?>
                                                <?php if ($user_freelancer_package_id == $freelancer_package_id && $d1 > $d2) { ?>
                                                    <a href="#" class="felan-button button-block btn-add-to-message" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>">
                                                        <?php esc_html_e('Package Actived', 'felan-framework'); ?></a> <?php } else { ?>
                                                    <a href="#" class="felan-button button-outline button-block btn-add-to-message" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>">
                                                        <?php esc_html_e('Get Started', 'felan-framework'); ?></a>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <?php if ($user_freelancer_package_id == $freelancer_package_id && $d1 > $d2) { ?>
                                                    <a href="<?php echo esc_url($payment_process_link); ?>" class="felan-button button-block"><?php esc_html_e('Package Actived', 'felan-framework'); ?></a>
                                                <?php } else { ?>
                                                    <a href="<?php echo esc_url($payment_process_link); ?>" class="felan-button button-outline button-block"><?php esc_html_e('Get Started', 'felan-framework'); ?></a>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                <?php endif; ?>
                            </div>
                        </div>
                <?php
                    }
                endwhile;
                wp_reset_query(); ?>
            </div>
        </div>

<?php
    }
}
