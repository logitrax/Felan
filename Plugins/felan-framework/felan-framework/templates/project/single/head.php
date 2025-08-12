<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$project_id = get_the_ID();
if (!empty($project_single_id)) {
    $project_id = $project_single_id;
}
$author_id = get_post_field('post_author', $project_id);
$author_name = get_the_author_meta('display_name', $author_id);
$company_id = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_select_company', true);
$freelancer_id = felan_id_service_to_freelancer($project_id);
$project_language = get_the_terms($project_id, 'project-language');
$project_location = get_the_terms($project_id, 'felan_location');
$project_career = get_the_terms($project_id, 'project-career');
$freelancer_current_position = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_current_position', true);
$freelancer_featured = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_featured', true);
$project_featured  = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_featured', true);
$project_categories = get_the_terms($project_id, 'project-categories');
$post_date = get_post_field('post_date', $project_id);
$human_readable_date = human_time_diff(strtotime($post_date), current_time('timestamp')) . esc_html__(' ago', 'felan-framework');
?>
<div class="container">
    <div class="project-head-details">
        <div class="head-left">
            <?php get_template_part('templates/global/breadcrumb'); ?>
            <div class="info">
                <div class="info-left">
                    <h1>
                        <?php echo get_the_title($project_id); ?>
                        <?php if ($project_featured == '1') : ?>
                            <span class="felan-label-yellow"><?php echo esc_html__('Featured', 'felan-framework'); ?></span>
                        <?php endif; ?>
                    </h1>
                    <div class="info-inner">
                        <?php if (!empty(get_the_title($company_id)) && !empty($company_id)) : ?>
                            <h4 class="title">
                                <span class="by"><?php esc_html_e('by', 'felan-framework'); ?></span>
                                <a href="<?php echo esc_url(get_permalink($company_id)); ?>">
                                    <?php echo get_the_title($company_id); ?>
                                </a>
                            </h4>
                            <?php if ($freelancer_featured == 1) : ?>
                                <span class="tooltip" data-title="<?php echo esc_attr('Featured', 'felan-framework') ?>"><i class="far fa-check"></i></span>
                            <?php endif; ?>
                        <?php else: ?>
                            <h4 class="title">
                                <span class="by"><?php esc_html_e('by', 'felan-framework'); ?></span>
                                <?php echo esc_html($author_name); ?>
                            </h4>
                        <?php endif; ?>
                        <ul class="project-meta">
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
                            <li>
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2.54961 13.4056C2.2778 13.0326 2.1419 12.8462 2.04835 12.4854C1.98388 12.2367 1.98388 11.7633 2.04835 11.5146C2.1419 11.1538 2.2778 10.9674 2.54961 10.5944C4.03902 8.55068 7.30262 5 12 5C16.6974 5 19.961 8.55068 21.4504 10.5944C21.7222 10.9674 21.8581 11.1538 21.9516 11.5146C22.0161 11.7633 22.0161 12.2367 21.9516 12.4854C21.8581 12.8462 21.7222 13.0326 21.4504 13.4056C19.961 15.4493 16.6974 19 12 19C7.30262 19 4.03902 15.4493 2.54961 13.4056Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M12 14C13.1046 14 14 13.1046 14 12C14 10.8954 13.1046 10 12 10C10.8954 10 10 10.8954 10 12C10 13.1046 10.8954 14 12 14Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <?php
									echo sprintf(
										_n( '%s view', '%s views', felan_total_view_project_details($project_id), 'felan-framework' ),
										felan_total_view_project_details($project_id)
									);
								?>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="info-right">
                    <?php felan_get_template('project/wishlist.php', array(
                        'project_id' => $project_id,
                    )); ?>
                    <div class="toggle-social">
                        <a href="#" class="jobs-share btn-share tooltip" data-title="<?php esc_attr_e('Share', 'felan-framework') ?>">
                            <i class="far fa-share-alt"></i>
                        </a>
                        <?php felan_get_template('global/social-share.php', array(
                            'post_id' => $project_id,
                        )); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>