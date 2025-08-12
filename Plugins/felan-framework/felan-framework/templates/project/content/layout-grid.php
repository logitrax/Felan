<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
$user_id = $current_user->ID;
$project_id = get_the_ID();
if (!empty($projects_id)) {
    $project_id = $projects_id;
}
$enable_project_des = felan_get_option('enable_project_show_des');
$currency_sign_default = felan_get_option('currency_sign_default');
$currency_position = felan_get_option('currency_position');
$enable_project_review = felan_get_option('enable_single_project_review', '1');
$enable_project_single_popup = felan_get_option('enable_project_single_popup', '0');
$enable_project_single_popup = !empty($_GET['has_popup']) ? Felan_Helper::felan_clean(wp_unslash($_GET['has_popup'])) : $enable_project_single_popup;

$author_id = get_post_field('post_author', $project_id);
$company_id = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_select_company', true);
$company_logo = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_logo', true);
$project_featured  = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_featured', true);
$project_categories = get_the_terms($project_id, 'project-categories');
$project_time_type =  get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_time_type', true);
$post_date = get_post_field('post_date', $project_id);
$human_readable_date = human_time_diff(strtotime($post_date), current_time('timestamp')) . esc_html__(' ago', 'felan-framework');
$project_item_class[] = 'felan-project-item';
if (!empty($layout)) {
    $project_item_class[] = $layout;
}
if ($project_featured == 1) {
    $project_item_class[] = 'felan-project-featured';
}
$project_item_class[] = 'project-' . $project_id;

if (has_post_thumbnail($project_id)) {
    $thumbnail_url = get_the_post_thumbnail_url($project_id);
} else {
    $thumbnail_url = FELAN_PLUGIN_URL . 'assets/images/no-image.jpg';
}

if (!empty($custom_project_image_size) && preg_match('/^\d+x\d+$/', $custom_project_image_size) == 1) {
    $image_sizes          = explode('x', $custom_project_image_size);
    $width                = $image_sizes[0];
    $height               = $image_sizes[1];
    if ($thumbnail_url) {
        $image_crop = felan_image_resize_url($thumbnail_url, $width, $height);
        if (!is_wp_error($image_crop)) {
            $thumbnail_url = $image_crop['url'];
        } else {
            error_log($image_crop->get_error_message());
            $thumbnail_url = '';
        }
    }
}
$project_currency_type = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_currency_type', true);

$freelancer_paid_submission_type = felan_get_option('freelancer_paid_submission_type');
$check_package = felan_get_field_check_freelancer_package('project_apply');
$freelancer_package_number_project_apply = intval(get_user_meta($user_id, FELAN_METABOX_PREFIX . 'freelancer_package_number_project_apply', true));
$projects_budget_show = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_budget_show', true);
$has_project_proposal = felan_has_project_proposal($project_id);
$class_fixed = '';
if($projects_budget_show == 'fixed'){
    $class_fixed = 'fixed';
}

$enable_post_type_jobs = felan_get_option('enable_post_type_jobs','1');
$enable_post_type_service = felan_get_option('enable_post_type_service','1');
$enable_post_type_project = felan_get_option('enable_post_type_project','1');
?>
<div class="<?php echo join(' ', $project_item_class); ?>">
    <div class="project-thumbnail">
        <a href="<?php echo esc_url(get_the_permalink($project_id)); ?>">
            <img src="<?php echo $thumbnail_url; ?>" alt="<?php echo get_the_title($project_id); ?>">
        </a>
        <?php if ($project_featured == 1) : ?>
            <span class="featured">
                <?php echo esc_html__('Featured', 'felan-framework') ?>
            </span>
        <?php endif; ?>
        <div class="project-status-inner">
            <?php felan_get_template('project/wishlist.php', array(
                'project_id' => $project_id,
            )); ?>
        </div>
    </div>
    <div class="project-content">
        <?php if (!empty($company_id)) : ?>
            <div class="project-author">
                <?php if (!empty($company_logo)) : ?>
                    <img class="image-company" src="<?php echo esc_attr($company_logo['url']) ?>" alt="" />
                <?php endif; ?>
                <?php if (!empty(get_the_title($company_id))) : ?>
                    <a href="<?php echo esc_url(get_permalink($company_id)); ?>">
                        <?php echo get_the_title($company_id); ?>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty(get_the_title($project_id))) : ?>
            <h2 class="project-title <?php if (empty($company_logo)) : ?>logo-empty<?php endif; ?>">
                <a href="<?php echo get_the_permalink($project_id); ?>"><?php echo get_the_title($project_id); ?></a>
            </h2>
        <?php endif; ?>
        <div class="project-meta">
            <ul>
                <?php if (is_array($project_categories)) : ?>
                    <li>
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.23529 15.1458L2.76141 9.67189C2.4841 9.39458 2.32612 9.01991 2.32116 8.62777L2.25006 3.01128C2.24471 2.58868 2.58867 2.24471 3.01128 2.25006L8.62777 2.32116C9.0199 2.32612 9.39458 2.4841 9.67189 2.76141L15.1458 8.23529C15.6506 8.74013 16.0394 9.70424 15.4632 10.2804L10.2804 15.4632C9.70424 16.0394 8.74012 15.6506 8.23529 15.1458Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M6.01422 5.66412L5.48389 5.13379" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <?php foreach ($project_categories as $categories) { ?>
                            <a href="<?php echo get_term_link($categories->term_id, 'project-categories'); ?>"><?php echo $categories->name; ?></a>
                        <?php } ?>
                    </li>
                <?php endif; ?>
                <li>
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 4.5V9H12.375" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <circle cx="9" cy="9" r="6.75" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <?php echo esc_html($human_readable_date); ?>
                </li>
                <li>
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15.75 14.8124C15.75 13.245 14.4978 11.0115 12.75 10.5173M11.25 14.8125C11.25 12.8243 9.23528 10.3125 6.75 10.3125C4.26472 10.3125 2.25 12.8243 2.25 14.8125" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <circle cx="6.75" cy="5.4375" r="2.25" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M11.25 7.6875C12.4926 7.6875 13.5 6.68014 13.5 5.4375C13.5 4.19486 12.4926 3.1875 11.25 3.1875" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <?php echo sprintf(esc_html__('%s sent proposal', 'felan-framework'), felan_post_count_applicant_project($project_id)) ?>
                </li>
            </ul>
        </div>
        <div class="project-info">
            <div class="project-info-left">
                <p class="budget-show">
                    <?php if($projects_budget_show == 'hourly') : ?>
                        <?php echo esc_html__('Hourly Rate', 'felan-framework'); ?>
                    <?php else: ?>
                        <?php echo esc_html__('Fixed Price', 'felan-framework'); ?>
                    <?php endif; ?>
                </p>
                <div class="price-inner">
                    <?php echo felan_get_budget_project($project_id); ?>
                </div>
            </div>
            <div class="project-proposal">
                <?php if (is_user_logged_in() && in_array('felan_user_freelancer', (array)$current_user->roles)) { ?>
                    <?php if ($user_id == $author_id) { ?>
                        <a href="#" class="felan-button button-outline btn-add-to-message"
                            data-text="<?php echo esc_attr('This feature is not available for the same user ID', 'felan-framework') ?>">
                            <?php esc_html_e('Send proposals', 'felan-framework') ?>
                        </a>
                    <?php } else { ?>
                        <?php if ($check_package == -1 || $check_package == 0 || ($freelancer_paid_submission_type == 'freelancer_per_package' && $freelancer_package_number_project_apply < 1)) { ?>
                            <a href="<?php echo get_permalink(felan_get_option('felan_freelancer_package_page_id')); ?>" class="felan-button button-outline">
                                <?php esc_html_e('Renew Package', 'felan-framework') ?>
                            </a>
                        <?php } else { ?>
                            <?php if($has_project_proposal == '1') { ?>
                                <button class="felan-button button-outline button-disbale">
                                    <?php esc_html_e('Proposal Submitted', 'felan-framework') ?>
                                </button>
                            <?php } else { ?>
                                <a href="#form-apply-project" class="felan-button button-outline btn-apply-project <?php echo esc_attr($class_fixed); ?>" id="felan-apply-project"
                                   data-post-current="<?php echo intval($project_id) ?>"
                                   data-currency-type="<?php echo $project_currency_type; ?>"
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
                        <a href="#popup-form" class="felan-button btn-login btn-login-freelancer button-outline button-block btn-apply-project"
                           data-jobs = "<?php echo $enable_post_type_jobs;?>"
                           data-service = "<?php echo $enable_post_type_service;?>"
                           data-project = "<?php echo $enable_post_type_project;?>">
                            <?php esc_html_e('Send proposals', 'felan-framework') ?>
                        </a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php if ($enable_project_single_popup === '1' && is_archive()) { ?>
        <a class=" felan-link-item btn-single-settings" data-post-id="<?php echo esc_attr($project_id) ?>" data-post-type="project" href="#"></a>
    <?php } ?>
</div>