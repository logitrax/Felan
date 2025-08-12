<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$enable_post_type_jobs = felan_get_option('enable_post_type_jobs','1');
$enable_post_type_service = felan_get_option('enable_post_type_service','1');
$enable_post_type_project = felan_get_option('enable_post_type_project','1');
$freelancer_paid_submission_type = felan_get_option('freelancer_paid_submission_type');
if ($freelancer_paid_submission_type !== 'freelancer_per_package') {
    echo felan_get_template_html('global/access-denied.php', array('type' => 'free_submit'));
    return;
}

?>
<div class="felan-package-wrap">
    <div class="felan-heading">
        <h2 class="entry-title"><?php esc_html_e('Try a Freelancer Package', 'felan-framework') ?></h2>
        <div class="choose-package">
            <h4><?php esc_html_e('Choose Package', 'felan-framework') ?></h4>
            <p><?php esc_html_e('Designed to maximize your freelancer success and earnings!', 'felan-framework') ?></p>
        </div>
    </div>
    <div class="row">
        <?php
        $user_freelancer_package_id = get_the_author_meta(FELAN_METABOX_PREFIX . 'freelancer_package_id', $user_id);
        $args = array(
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
        $data = new WP_Query($args);
        $total_records = $data->found_posts;
        if ($total_records == 4) {
            $css_class = 'col-md-4 col-sm-6';
        } else if ($total_records == 3) {
            $css_class = 'col-md-4 col-sm-6';
        } else if ($total_records == 2) {
            $css_class = 'col-md-4 col-sm-6';
        } else if ($total_records == 1) {
            $css_class = 'col-md-4 col-sm-12';
        } else {
            $css_class = 'col-md-3 col-sm-6';
        }
        while ($data->have_posts()) : $data->the_post();
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
            $felan_freelancer_package = new felan_freelancer_package();
            $get_expired_date = $felan_freelancer_package->get_expired_date($freelancer_package_id, $user_id);
            $current_date = date('Y-m-d');

            $d1 = strtotime($get_expired_date);
            $d2 = strtotime($current_date);

            if ($get_expired_date === 'never expires') {
                $d1 = 999999999999999999999999;
            }

            if ($user_freelancer_package_id == $freelancer_package_id && $d1 > $d2) {
                $is_current = 'current';
            } else {
                $is_current = '';
            }
            $payment_link = felan_get_permalink('freelancer_payment');

            if($enable_post_type_jobs == '1'){
                $payment_process_link = add_query_arg('candidate_package_id', $freelancer_package_id, $payment_link);
            } else {
                $payment_process_link = add_query_arg('freelancer_package_id', $freelancer_package_id, $payment_link);
            }

            $field_package = array('jobs_apply','project_apply', 'jobs_wishlist', 'company_follow', 'contact_company', 'info_company', 'send_message', 'review_and_commnent');

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
            <div class="<?php echo esc_attr($css_class); ?>">
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
                                    $name = esc_html__('companies followed', 'felan-framework');
                                    break;
                                case 'contact_company':
                                    $name = esc_html__('View company in jobs', 'felan-framework');
                                    $is_check = true;
                                    break;
                                case 'info_company':
                                    $name = esc_html__('View information company', 'felan-framework');
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
                </div>
            </div>
        <?php endwhile; ?>
        <?php wp_reset_postdata(); ?>
    </div>
</div>