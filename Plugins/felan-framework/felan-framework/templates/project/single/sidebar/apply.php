<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
$user_id = $current_user->ID;
$project_id = get_the_ID();
if (!empty($project_single_id)) {
    $project_id = $project_single_id;
}
$author_id = get_post_field('post_author', $project_id);
$classes = array();
$enable_sticky_sidebar_type = felan_get_option('enable_sticky_project_sidebar_type');
$project_time_type =  get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_time_type', true);
if ($enable_sticky_sidebar_type) {
    $classes[] = 'has-sticky';
}
$freelancer_paid_submission_type = felan_get_option('freelancer_paid_submission_type');
$check_package = felan_get_field_check_freelancer_package('project_apply');
$freelancer_package_number_project_apply = intval(get_user_meta($user_id, FELAN_METABOX_PREFIX . 'freelancer_package_number_project_apply', true));
$projects_budget_show = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_budget_show', true);
$project_career = get_the_terms($project_id, 'project-career');
$project_language = get_the_terms($project_id, 'project-language');
$class_fixed = '';
if($projects_budget_show == 'fixed'){
    $class_fixed = 'fixed';
}
$has_project_proposal = felan_has_project_proposal($project_id);
$enable_post_type_jobs = felan_get_option('enable_post_type_jobs','1');
$enable_post_type_service = felan_get_option('enable_post_type_service','1');
$enable_post_type_project = felan_get_option('enable_post_type_project','1');
?>
<div class="project-info-sidebar block-archive-sidebar <?php echo implode(" ", $classes); ?>">
    <div class="project-info-warpper">
        <div class="price-inner">
            <p class="budget-show">
                <?php if($projects_budget_show == 'hourly') : ?>
                    <?php echo esc_html__('Hourly Rate', 'felan-framework'); ?>
                <?php else: ?>
                    <?php echo esc_html__('Fixed Price', 'felan-framework'); ?>
                <?php endif; ?>
            </p>
            <?php echo felan_get_budget_project($project_id); ?>
        </div>
        <ul class="info">
            <li class="list-item">
                <span class="icon">
                   <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 4.5V9H12.375" stroke="#333333" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="9" cy="9" r="6.75" stroke="#333333" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <?php if($projects_budget_show == 'hourly') : ?>
                        <?php echo esc_html__('Estimated maximum hours', 'felan-framework'); ?>
                    <?php else: ?>
                        <?php echo esc_html__('Estimated maximum time', 'felan-framework'); ?>
                    <?php endif; ?>
                </span>
                <span class="value"><?php echo esc_html(felan_project_maximum_time($project_id)); ?></span>
            </li>
            <?php if (!empty($project_career)) : ?>
                <li class="list-item">
                    <span class="icon">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6.231 15.75H11.769C14.5518 15.75 15.0503 14.6632 15.1956 13.3402L15.7148 7.9402C15.9017 6.2932 15.4172 4.94995 12.4613 4.94995H5.53875C2.58284 4.94995 2.09826 6.2932 2.28517 7.9402L2.80436 13.3402C2.94973 14.6632 3.44815 15.75 6.231 15.75Z" stroke="#333333" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6.23047 4.95V4.41C6.23047 3.21525 6.23047 2.25 8.44567 2.25H9.55327C11.7685 2.25 11.7685 3.21525 11.7685 4.41V4.95" stroke="#333333" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M7.35946 9.99839C5.63341 9.80994 3.93944 9.22113 2.42578 8.25" stroke="black" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M10.6758 9.99839C12.4018 9.80994 14.0958 9.22113 15.6095 8.25" stroke="black" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="9" cy="10.125" r="1.5" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <?php echo esc_html__('Career Level', 'felan-framework'); ?>
                    </span>
                    <span class="value">
                        <?php foreach ($project_career as $career) { ?>
                            <span class="cate"><?php echo $career->name; ?></span>
                        <?php } ?>
                    </span>
                </li>
            <?php endif; ?>
            <?php if (!empty($project_language)) : ?>
                <li class="list-item">
                    <span class="icon">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 15.75C12.7279 15.75 15.75 12.7279 15.75 9C15.75 5.27208 12.7279 2.25 9 2.25M9 15.75C5.27208 15.75 2.25 12.7279 2.25 9C2.25 5.27208 5.27208 2.25 9 2.25M9 15.75C11.0711 15.75 11.956 11.8777 11.956 9C11.956 6.12228 11.0711 2.25 9 2.25M9 15.75C6.92893 15.75 6.04421 11.8777 6.04421 9C6.04421 6.1223 6.92893 2.25 9 2.25M2.62491 6.74998C7.60409 6.74998 10.3957 6.74998 15.3749 6.74998M2.62491 11.25C7.60409 11.25 10.3957 11.25 15.3749 11.25" stroke="#333333" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <?php echo esc_html__('Language', 'felan-framework'); ?>
                    </span>
                    <span class="value">
                    <?php foreach ($project_language as $language) { ?>
                        <span class="cate"><?php echo $language->name; ?></span>
                    <?php } ?>
                    </span>
                </li>
            <?php endif; ?>
        </ul>
        <?php if (is_user_logged_in() && in_array('felan_user_freelancer', (array)$current_user->roles)) { ?>
            <?php if ($user_id == $author_id) { ?>
                <a href="#" class="felan-button button-block btn-add-to-message"
                    data-text="<?php echo esc_attr('This feature is not available for the same user ID', 'felan-framework') ?>">
                    <?php esc_html_e('Send proposals', 'felan-framework') ?>
                </a>
            <?php } else { ?>
                <?php if ($check_package == -1 || $check_package == 0 || ($freelancer_paid_submission_type == 'freelancer_per_package' && $freelancer_package_number_project_apply < 1)) { ?>
                    <a href="<?php echo get_permalink(felan_get_option('felan_freelancer_package_page_id')); ?>" class="felan-button button-block">
                        <?php esc_html_e('Renew Package', 'felan-framework') ?>
                    </a>
                <?php } else { ?>
                    <?php if($has_project_proposal == '1') { ?>
                        <button class="felan-button button-block button-disbale">
                            <?php esc_html_e('Proposal Submitted', 'felan-framework') ?>
                        </button>
                    <?php } else { ?>
                        <a href="#form-apply-project" class="felan-button button-block btn-apply-project <?php echo esc_attr($class_fixed); ?>" id="felan-apply-project"
                           data-post-current="<?php echo intval($project_id) ?>"
                           data-maximum-time="<?php echo esc_attr(felan_project_maximum_time($project_id)); ?>"
                           data-author-id="<?php echo intval($user_id) ?>"
                           data-info-price='<?php echo felan_get_budget_project($project_id); ?>'
                           data-info-hours="<?php echo esc_attr(felan_project_maximum_time($project_id)); ?>">
                            <?php esc_html_e('Send proposals', 'felan-framework') ?>
                        </a>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
        <?php } else { ?>
            <div class="logged-out">
                <a href="#popup-form" class="felan-button btn-login btn-login-freelancer button-block btn-apply-project"
                   data-jobs = "<?php echo $enable_post_type_jobs;?>"
                   data-service = "<?php echo $enable_post_type_service;?>"
                   data-project = "<?php echo $enable_post_type_project;?>">
                    <?php esc_html_e('Send proposals', 'felan-framework') ?>
                </a>
            </div>
        <?php } ?>
    </div>
</div>