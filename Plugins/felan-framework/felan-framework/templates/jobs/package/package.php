<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$enable_post_type_jobs = felan_get_option('enable_post_type_jobs','1');
$enable_post_type_project = felan_get_option('enable_post_type_project','1');
$paid_submission_type = felan_get_option('paid_submission_type', 'no');
if ($paid_submission_type != 'per_package') {
    echo felan_get_template_html('global/access-denied.php', array('type' => 'free_submit'));
    return;
}

?>
<div class="felan-package-wrap">
    <div class="felan-heading">
        <h2 class="entry-title"><?php esc_html_e('Try a Employer Package', 'felan-framework') ?></h2>
        <div class="choose-package">
            <h4><?php esc_html_e('Choose Package', 'felan-framework') ?></h4>
            <p><?php esc_html_e('Choose a Package to Submit Jobs and Projects', 'felan-framework') ?></p>
        </div>
    </div>
    <div class="row">
        <?php
        $user_package_id = get_the_author_meta(FELAN_METABOX_PREFIX . 'package_id', $user_id);
        $args = array(
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
            $package_id = get_the_ID();
            $package_time_unit = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_time_unit', true);
            $package_period = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_period', true);
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

            $package_num_job = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_number_job', true);
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
            $felan_package = new felan_Package();
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
            $field_package = array('freelancer_follow', 'download_cv', 'invite', 'send_message', 'print', 'review_and_commnent', 'info');

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
            <div class="<?php echo esc_attr($css_class); ?>">
                <div class="felan-package-item panel panel-default <?php echo esc_attr($is_current); ?> <?php echo esc_attr($is_featured); ?>">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="felan-package-thumbnail"><?php the_post_thumbnail(); ?></div>
                    <?php endif; ?>
                    <div class="felan-package-title">
                        <h2 class="entry-title">
                            <?php the_title(); ?>
                        </h2>
                        <?php if ($package_featured == 1) { ?>
                            <span class="recommended"><?php esc_html_e('Most Popular', 'felan-framework'); ?></span>
                        <?php } ?>
                    </div>
                    <div class="felan-package-price">
                        <span>
                            <?php
                            if ($package_price > 0) {
                                echo felan_get_format_money($package_price, '', 2, true);
                            } else {
                                esc_html_e('Free', 'felan-framework');
                            }
                            ?>
                            <span class="time-unit"><?php echo $head_time_unit; ?></span>
                    </div>
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
                                    $name = esc_html__('Send Messages', 'felan-framework');
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
                    <div class="felan-package-choose">
                        <?php
                        $user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);
                        if ($user_demo == 'yes') { ?>
                            <?php if ($user_package_id == $package_id && $d1 > $d2) { ?>
                                <a href="#" class="felan-button button-block btn-add-to-message" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>">
                                    <?php esc_html_e('Package Actived', 'felan-framework'); ?></a> <?php } else { ?>
                                <a href="#" class="felan-button button-outline button-block btn-add-to-message" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>">
                                    <?php esc_html_e('Get Started', 'felan-framework'); ?></a>
                            <?php } ?>
                        <?php } else { ?>
                            <?php if ($user_package_id == $package_id && $d1 > $d2) { ?>

                                <?php $args_invoice = array(
                                    'post_type'           => 'invoice',
                                    'posts_per_page'      => 1,
                                    'author'              => $user_id,
                                );
                                $data_invoice = new WP_Query($args_invoice);
                                if (!empty($data_invoice->post)) {
                                    $invoice_id = $data_invoice->post->ID;
                                    $invoice_status = get_post_meta($invoice_id, FELAN_METABOX_PREFIX . 'invoice_payment_status', true);
                                    if ($invoice_status == 0) { ?>
                                        <a href="<?php echo esc_url($payment_process_link); ?>" class="felan-button button-block"><?php esc_html_e('Package Pending', 'felan-framework'); ?></a>
                                    <?php } else { ?>
                                        <a href="<?php echo esc_url($payment_process_link); ?>" class="felan-button button-block"><?php esc_html_e('Package Actived', 'felan-framework'); ?></a>
                                <?php }
                                }
                                ?>

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